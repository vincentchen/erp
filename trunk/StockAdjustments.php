<?php

/* $Id$*/

include('includes/DefineStockAdjustment.php');
include('includes/DefineSerialItems.php');
include('includes/session.inc');
$title = _('Stock Adjustments');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['NewAdjustment'])){
	unset($_SESSION['Adjustment']);
	$_SESSION['Adjustment'] = new StockAdjustment();
}

if (!isset($_SESSION['Adjustment'])){
	$_SESSION['Adjustment'] = new StockAdjustment();
}

$NewAdjustment = false;

if (isset($_GET['StockID'])){
	$StockID = trim(mb_strtoupper($_GET['StockID']));
	$_SESSION['Adjustment']->StockID = trim(mb_strtoupper($StockID));
	$result = DB_query("SELECT description, 
							controlled, 
							serialised, 
							decimalplaces 
						FROM stockmaster 
						WHERE stockid='" . $_SESSION['Adjustment']->StockID . "'",$db);
	$myrow = DB_fetch_array($result);
	$_SESSION['Adjustment']->ItemDescription = $myrow['description'];
	$_SESSION['Adjustment']->Controlled = $myrow['controlled'];
	$_SESSION['Adjustment']->Serialised = $myrow['serialised'];
	$_SESSION['Adjustment']->DecimalPlaces = $myrow['decimalplaces'];
	$_SESSION['Adjustment']->SerialItems = array();
	if (!isset($_SESSION['Adjustment']->Quantity) OR !is_numeric($_SESSION['Adjustment']->Quantity)){
		$_SESSION['Adjustment']->Quantity=0;
	}
	$NewAdjustment = true;
} elseif (isset($_POST['StockID'])){
	if(isset($_POST['StockID']) 
		AND $_POST['StockID'] != $_SESSION['Adjustment']->StockID){
			
		$NewAdjustment = true;
		$_SESSION['Adjustment']->StockID = trim(mb_strtoupper($_POST['StockID']));
		$StockID = trim(mb_strtoupper($_POST['StockID']));
	}
	$_SESSION['Adjustment']->tag = $_POST['tag'];
	$_SESSION['Adjustment']->Narrative = $_POST['Narrative'];
	$_SESSION['Adjustment']->StockLocation = $_POST['StockLocation'];
	if ($_POST['Quantity']=='' OR !is_numeric(filter_number_format($_POST['Quantity']))){
		$_POST['Quantity']=0;
	}
	$_SESSION['Adjustment']->Quantity = filter_number_format($_POST['Quantity']);
}

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/supplier.png" title="' .
	_('Inventory Adjustment') . '" alt="" />' . ' ' . _('Inventory Adjustment') . '</p>';

if (isset($_POST['CheckCode'])) {

	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/magnifier.png" title="' . _('Dispatch') .
		'" alt="" />' . ' ' . _('Select Item to Adjust') . '</p>';

	if (mb_strlen($_POST['StockText'])>0) {
		$sql="SELECT stockid, 
					description 
				FROM stockmaster 
				WHERE description " . LIKE . " '%" . $_POST['StockText'] ."%'";
	} else {
		$sql="SELECT stockid, 
					description 
				FROM stockmaster 
				WHERE stockid " . LIKE  . " '%" . $_POST['StockCode'] ."%'";
	}
	$ErrMsg=_('The stock information cannot be retrieved because');
	$DbgMsg=_('The SQL to get the stock description was');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	echo '<table class="selection">
			<tr>
				<th>'._('Stock Code').'</th>
				<th>'._('Stock Description').'</th>
			</tr>';
	while ($myrow = DB_fetch_row($result)) {
		echo '<tr>
				<td>'.$myrow[0].'</td>
				<td>'.$myrow[1].'</td>
				<td><a href="StockAdjustments.php?StockID='.$myrow[0].'&Description='.$myrow[1].'">'._('Adjust').'</a>
			</tr>';
	}
	echo '</table>';
	include('includes/footer.inc');
	exit;
}

if (isset($_POST['EnterAdjustment']) AND $_POST['EnterAdjustment']!= ''){

	$InputError = false; /*Start by hoping for the best */
	$result = DB_query("SELECT * FROM stockmaster WHERE stockid='" . $_SESSION['Adjustment']->StockID . "'",$db);
	$myrow = DB_fetch_row($result);
	if (DB_num_rows($result)==0) {
		prnMsg( _('The entered item code does not exist'),'error');
		$InputError = true;
	} elseif (!is_numeric($_SESSION['Adjustment']->Quantity)){
		prnMsg( _('The quantity entered must be numeric'),'error');
		$InputError = true;
	} elseif ($_SESSION['Adjustment']->Quantity==0){
		prnMsg( _('The quantity entered cannot be zero') . '. ' . _('There would be no adjustment to make'),'error');
		$InputError = true;
	} elseif ($_SESSION['Adjustment']->Controlled==1 AND count($_SESSION['Adjustment']->SerialItems)==0) {
		prnMsg( _('The item entered is a controlled item that requires the detail of the serial numbers or batch references to be adjusted to be entered'),'error');
		$InputError = true;
	}

	if ($_SESSION['ProhibitNegativeStock']==1){
		$SQL = "SELECT quantity FROM locstock
				WHERE stockid='" . $_SESSION['Adjustment']->StockID . "'
				AND loccode='" . $_SESSION['Adjustment']->StockLocation . "'";
		$CheckNegResult=DB_query($SQL,$db);
		$CheckNegRow = DB_fetch_array($CheckNegResult);
		if ($CheckNegRow['quantity']+$_SESSION['Adjustment']->Quantity <0){
			$InputError=true;
			prnMsg(_('The system parameters are set to prohibit negative stocks. Processing this stock adjustment would result in negative stock at this location. This adjustment will not be processed.'),'error');
		}
	}

	if (!$InputError) {

/*All inputs must be sensible so make the stock movement records and update the locations stocks */

		$AdjustmentNumber = GetNextTransNo(17,$db);
		$PeriodNo = GetPeriod (Date($_SESSION['DefaultDateFormat']), $db);
		$SQLAdjustmentDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));

		$Result = DB_Txn_Begin($db);

		// Need to get the current location quantity will need it later for the stock movement
		$SQL="SELECT locstock.quantity
			FROM locstock
			WHERE locstock.stockid='" . $_SESSION['Adjustment']->StockID . "'
			AND loccode= '" . $_SESSION['Adjustment']->StockLocation . "'";
		$Result = DB_query($SQL, $db);
		if (DB_num_rows($Result)==1){
			$LocQtyRow = DB_fetch_row($Result);
			$QtyOnHandPrior = $LocQtyRow[0];
		} else {
			// There must actually be some error this should never happen
			$QtyOnHandPrior = 0;
		}

		$SQL = "INSERT INTO stockmoves (stockid,
										type,
										transno,
										loccode,
										trandate,
										prd,
										reference,
										qty,
										newqoh)
									VALUES (
										'" . $_SESSION['Adjustment']->StockID . "',
										17,
										'" . $AdjustmentNumber . "',
										'" . $_SESSION['Adjustment']->StockLocation . "',
										'" . $SQLAdjustmentDate . "',
										'" . $PeriodNo . "',
										'" . DB_escape_string($_SESSION['Adjustment']->Narrative) ."',
										'" . $_SESSION['Adjustment']->Quantity . "',
										'" . ($QtyOnHandPrior + $_SESSION['Adjustment']->Quantity) . "'
									)";


		$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
		$DbgMsg =  _('The following SQL to insert the stock movement record was used');
		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

/*Get the ID of the StockMove... */
		$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

/*Insert the StockSerialMovements and update the StockSerialItems  for controlled items*/

		if ($_SESSION['Adjustment']->Controlled ==1){
			foreach($_SESSION['Adjustment']->SerialItems as $Item){
			/*We need to add or update the StockSerialItem record and
			The StockSerialMoves as well */

				/*First need to check if the serial items already exists or not */
				$SQL = "SELECT COUNT(*)
						FROM stockserialitems
						WHERE stockid='" . $_SESSION['Adjustment']->StockID . "'
						AND loccode='" . $_SESSION['Adjustment']->StockLocation . "'
						AND serialno='" . $Item->BundleRef . "'";
				$ErrMsg = _('Unable to determine if the serial item exists');
				$Result = DB_query($SQL,$db,$ErrMsg);
				$SerialItemExistsRow = DB_fetch_row($Result);

				if ($SerialItemExistsRow[0]==1){

					$SQL = "UPDATE stockserialitems SET quantity= quantity + " . $Item->BundleQty . "
							WHERE stockid='" . $_SESSION['Adjustment']->StockID . "'
							AND loccode='" . $_SESSION['Adjustment']->StockLocation . "'
							AND serialno='" . $Item->BundleRef . "'";

					$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg =  _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
				} else {
					/*Need to insert a new serial item record */
					$SQL = "INSERT INTO stockserialitems (stockid,
														loccode,
														serialno,
														qualitytext,
														quantity)
											VALUES ('" . $_SESSION['Adjustment']->StockID . "',
											'" . $_SESSION['Adjustment']->StockLocation . "',
											'" . $Item->BundleRef . "',
											'',
											'" . $Item->BundleQty . "'
											)";

					$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg =  _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
				}


				/* now insert the serial stock movement */

				$SQL = "INSERT INTO stockserialmoves (stockmoveno,
													stockid,
													serialno,
													moveqty)
										VALUES ('" . $StkMoveNo . "',
											'" . $_SESSION['Adjustment']->StockID . "',
											'" . $Item->BundleRef . "',
											'" . $Item->BundleQty . "')";
				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
				$DbgMsg =  _('The following SQL to insert the serial stock movement records was used');
				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

			}/* foreach controlled item in the serialitems array */
		} /*end if the adjustment item is a controlled item */



		$SQL = "UPDATE locstock SET quantity = quantity + '" . $_SESSION['Adjustment']->Quantity . "'
				WHERE stockid='" . $_SESSION['Adjustment']->StockID . "'
				AND loccode='" . $_SESSION['Adjustment']->StockLocation . "'";

		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
		$DbgMsg = _('The following SQL to update the stock record was used');

		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

		if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $_SESSION['Adjustment']->StandardCost > 0){

			$StockGLCodes = GetStockGLCode($_SESSION['Adjustment']->StockID,$db);

			$SQL = "INSERT INTO gltrans (type,
										typeno,
										trandate,
										periodno,
										account,
										amount,
										narrative,
										tag)
								VALUES (17,
									'" .$AdjustmentNumber . "',
									'" . $SQLAdjustmentDate . "',
									'" . $PeriodNo . "',
									'" .  $StockGLCodes['adjglact'] . "',
									'" . $_SESSION['Adjustment']->StandardCost * -($_SESSION['Adjustment']->Quantity) . "',
									'" . $_SESSION['Adjustment']->StockID . " x " . $_SESSION['Adjustment']->Quantity . " @ " .
										$_SESSION['Adjustment']->StandardCost . " " . $_SESSION['Adjustment']->Narrative . "',
									'" . $_SESSION['Adjustment']->tag . "'
									)";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);

			$SQL = "INSERT INTO gltrans (type,
										typeno,
										trandate,
										periodno,
										account,
										amount,
										narrative,
										tag)
								VALUES (17,
									'" .$AdjustmentNumber . "',
									'" . $SQLAdjustmentDate . "',
									'" . $PeriodNo . "',
									'" .  $StockGLCodes['stockact'] . "',
									'" . $_SESSION['Adjustment']->StandardCost * $_SESSION['Adjustment']->Quantity . "',
									'" . $_SESSION['Adjustment']->StockID . " x " . $_SESSION['Adjustment']->Quantity . " @ " . $_SESSION['Adjustment']->StandardCost . " " . $_SESSION['Adjustment']->Narrative . "',
									'" . $_SESSION['Adjustment']->tag . "'
									)";

			$Errmsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);
		}

		$Result = DB_Txn_Commit($db);

		$ConfirmationText = _('A stock adjustment for'). ' ' . $_SESSION['Adjustment']->StockID . ' -  ' . $_SESSION['Adjustment']->ItemDescription . ' '._('has been created from location').' ' . $_SESSION['Adjustment']->StockLocation .' '. _('for a quantity of') . ' ' . locale_number_format($_SESSION['Adjustment']->Quantity,$_SESSION['Adjustment']->DecimalPlaces) ;
		prnMsg( $ConfirmationText,'success');

		if ($_SESSION['InventoryManagerEmail']!=''){
			$ConfirmationText = $ConfirmationText . ' ' . _('by user') . ' ' . $_SESSION['UserID'] . ' ' . _('at') . ' ' . Date('Y-m-d H:i:s');
			$EmailSubject = _('Stock adjustment for'). ' ' . $_SESSION['Adjustment']->StockID;
			mail($_SESSION['InventoryManagerEmail'],$EmailSubject,$ConfirmationText);
		}
		
		unset ($_SESSION['Adjustment']);
	} /* end if there was no input error */

}/* end if the user hit enter the adjustment */


echo '<form action="'. $_SERVER['PHP_SELF'] . '" method=post>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (!isset($_SESSION['Adjustment'])) {
	$StockID='';
	$Controlled= 0;
	$Quantity = 0;
	$DecimalPlaces =2;
} else {
	$StockID = $_SESSION['Adjustment']->StockID;
	$Controlled = $_SESSION['Adjustment']->Controlled;
	$Quantity = $_SESSION['Adjustment']->Quantity;
	
	$sql="SELECT materialcost, 
				labourcost, 
				overheadcost, 
				units, 
				decimalplaces 
			FROM stockmaster 
			WHERE stockid='".$StockID."'";
			
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_array($result);
	$_SESSION['Adjustment']->PartUnit=$myrow['units'];
	$_SESSION['Adjustment']->StandardCost=$myrow['materialcost']+$myrow['labourcost']+$myrow['overheadcost'];
	$DecimalPlaces = $myrow['decimalplaces'];
}
echo '<br /><table class=selection>';
echo '<tr><th colspan=4><font color=blue size=3>'._('Adjustment Details').'</font></th></tr>';
if (!isset($_GET['Description'])) {
	$_GET['Description']='';
}
echo '<tr><td>'. _('Stock Code'). ':</td><td>';
if (isset($StockID)) {
	echo '<input type="text" name="StockID" size=21 value="' . $StockID . '" maxlength=20></td></tr>';
} else {
	echo '<input type="text" name="StockID" size=21 value="" maxlength=20></td></tr>';
}
echo '<tr>
		<td>'. _('Partial Description'). ':</td>
		<td><input type="text" name="StockText" size="21" value="' . $_GET['Description'] .'" />&nbsp; &nbsp;'._('Partial Stock Code'). ':</td>
		<td>';
if (isset($StockID)) {
	echo '<input type="text" name="StockCode" size=21 value="' . $StockID .'" maxlength="20" />';
} else {
	echo '<input type="text" name="StockCode" size=21 value="" maxlength="20" />';
}
echo '</td>
		<td><input type="submit" name="CheckCode" value="'._('Check Part').'"></td>
	</tr>';
if (isset($_SESSION['Adjustment']) AND mb_strlen($_SESSION['Adjustment']->ItemDescription)>1){
	echo '<tr>
			<td colspan="3"><font color="blue" size="3">' . $_SESSION['Adjustment']->ItemDescription . ' ('._('In Units of').' ' . $_SESSION['Adjustment']->PartUnit . ' ) - ' . _('Unit Cost').' = ' . locale_number_format($_SESSION['Adjustment']->StandardCost,4) . '</font></td>
		</tr>';
}

echo '<tr><td>'. _('Adjustment to Stock At Location').':</td>
		<td><select name="StockLocation"> ';

$sql = "SELECT loccode, locationname FROM locations";
$resultStkLocs = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_SESSION['Adjustment']->StockLocation)){
		if ($myrow['loccode'] == $_SESSION['Adjustment']->StockLocation){
			 echo '<option selected value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		} else {
			 echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
		 echo '<option selected value="' . $myrow['loccode'] . '">' . $myrow['locationname']  . '</option>';
		 $_POST['StockLocation']=$myrow['loccode'];
	} else {
		 echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
	}
}

echo '</select></td></tr>';
if (isset($_SESSION['Adjustment']) AND !isset($_SESSION['Adjustment']->Narrative)) {
	$_SESSION['Adjustment']->Narrative = '';
	$Narrative ='';
} else {
	$Narrative ='';
}

echo '<tr>
		<td>'. _('Comments On Why').':</td>
		<td><input type="text" name="Narrative" size="32" maxlength="30" value="' . $Narrative . '" /></td>
	</tr>';

echo '<tr><td>'._('Adjustment Quantity').':</td>';

echo '<td>';
if ($Controlled==1){
		if ($_SESSION['Adjustment']->StockLocation == ''){
			$_SESSION['Adjustment']->StockLocation = $_SESSION['UserStockLocation'];
		}
		echo '<input type="hidden" name="Quantity" value="' . $_SESSION['Adjustment']->Quantity . '" />
				'.locale_number_format($_SESSION['Adjustment']->Quantity,$DecimalPlaces) .' &nbsp; &nbsp; &nbsp; &nbsp;
				[<a href="'.$rootpath.'/StockAdjustmentsControlled.php?AdjType=REMOVE">'._('Remove').'</a>]
				[<a href="'.$rootpath.'/StockAdjustmentsControlled.php?AdjType=ADD">'._('Add').'</a>]';
} else {
	echo '<input type="text" class="number" name="Quantity" size="12" maxlength="12" value="' . locale_number_format($Quantity,$DecimalPlaces) . '" />';
}
echo '</td></tr>';
	//Select the tag
echo '<tr>
		<td>'._('Select Tag').'</td>
		<td><select name="tag">';

$SQL = "SELECT tagref,
				tagdescription
		FROM tags
		ORDER BY tagref";

$result=DB_query($SQL,$db);
echo '<option value=0>0 - ' . _('None') . '</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_SESSION['Adjustment']->tag) AND $_SESSION['Adjustment']->tag==$myrow['tagref']){
		echo '<option selected value=' . $myrow['tagref'] . '>' . $myrow['tagref'].' - ' .$myrow['tagdescription'] . '</option>';
	} else {
		echo '<option value=' . $myrow['tagref'] . '>' . $myrow['tagref'].' - ' .$myrow['tagdescription']. '</option>';
	}
}
echo '</select></td>';
// End select tag

echo '</table>
	<div class="centre">
	<br />
	<input type="submit" name="EnterAdjustment" value="'. _('Enter Stock Adjustment'). '">
	<br />';

if (!isset($_POST['StockLocation'])) {
	$_POST['StockLocation']='';
}

echo '<br />
	<a href="'. $rootpath. '/StockStatus.php?StockID='. $StockID . '">'._('Show Stock Status').'</a>';
echo '<br />
	<a href="'.$rootpath.'/StockMovements.php?StockID=' . $StockID . '">'._('Show Movements').'</a>';
echo '<br />
	<a href="'.$rootpath.'/StockUsage.php?StockID=' . $StockID . '&StockLocation=' . $_POST['StockLocation'] . '">'._('Show Stock Usage').'</a>';
echo '<br />
	<a href="'.$rootpath.'/SelectSalesOrder.php?SelectedStockItem='. $StockID .'&StockLocation=' . $_POST['StockLocation'] . '">'. _('Search Outstanding Sales Orders').'</a>';
echo '<br />
	<a href="'.$rootpath.'/SelectCompletedOrder.php?SelectedStockItem=' . $StockID .'">'._('Search Completed Sales Orders').'</a>';

echo '</div></form>';
include('includes/footer.inc');
?>