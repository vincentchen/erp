<?php
/* $Revision: 1.5 $ */
include('includes/DefineStockAdjustment.php');
include('includes/DefineSerialItems.php');

$PageSecurity = 11;
include('includes/session.inc');
$title = _('Stock Adjustments');

include('includes/header.inc');
include('includes/DateFunctions.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['NewAdjustment'])){
     unset($_SESSION['Adjustment']);
     $_SESSION['Adjustment'] = new StockAdjustment;
}

if (!isset($_SESSION['Adjustment'])){
     $_SESSION['Adjustment'] = new StockAdjustment;
}

$NewAdjustment = false;

if (isset($_GET['StockID'])){
	$_SESSION['Adjustment']->StockID =$_GET['StockID'];
	$NewAdjustment = true;
} elseif (isset($_POST['StockID'])){
	if ($_POST['StockID'] != $_SESSION['Adjustment']->StockID){
		$NewAdjustment = true;
		$_SESSION['Adjustment']->StockID =$_POST['StockID'];
	}
	$_SESSION['Adjustment']->Narrative = $_POST['Narrative'];
	$_SESSION['Adjustment']->StockLocation = $_POST['StockLocation'];
	if ($_POST['Quantity']=='' or !is_numeric($_POST['Quantity'])){
		$_POST['Quantity']=0;
	}
	$_SESSION['Adjustment']->Quantity = $_POST['Quantity'];
}

if ($NewAdjustment){

	$sql ="SELECT Description,
				Units,
				MBflag,
				Materialcost+Labourcost+Overheadcost AS StandardCost,
				Controlled,
				Serialised,
				DecimalPlaces
			FROM StockMaster
			WHERE StockID='" . $_SESSION['Adjustment']->StockID . "'";
	$ErrMsg = _('Unable to Load StockMaster info for part'). ':' . $_SESSION['Adjustment']->StockID;
	$result = DB_query($sql, $db, $ErrMsg);
	$myrow = DB_fetch_row($result);

	if (DB_num_rows($result)==0){
                prnMsg( _('Unable to locate Stock Code').' '.$_SESSION['Adjustment']->StockID, 'error' );
	} elseif (DB_num_rows($result)>0){

		$_SESSION['Adjustment']->ItemDescription = $myrow[0];
		$_SESSION['Adjustment']->PartUnit = $myrow[1];
		$_SESSION['Adjustment']->StandardCost = $myrow[3];
		$_SESSION['Adjustment']->Controlled = $myrow[4];
		$_SESSION['Adjustment']->Serialised = $myrow[5];
		$_SESSION['Adjustment']->DecimalPlaces = $myrow[6];
		$_SESSION['Adjustment']->SerialItems = array();
		$_SESSION['Adjustment']->Quantity =0;

		if ($myrow[2]=='D' OR $myrow[2]=='A' OR $myrow[2]=='K'){
			echo '<P>' . _('The part entered is either or a dummy part or an assembly/kit-set part. These parts are not physical parts and no stock holding is maintained for them. Stock adjustments are therefore not possible.').'<HR>';
			echo '<A HREF="'. $rootpath .'/StockAdjustments.php?' . SID .'">'. _('Enter another adjustment'). '</A>';
			unset ($_SESSION['Adjustment']);
			include ('includes/footer.inc');
			exit;
		}
	}
}

if (isset($_POST['EnterAdjustment']) && $_POST['EnterAdjustment']!= ''){

	$InputError = false; /*Start by hoping for the best */
	$result = DB_query("SELECT * FROM StockMaster WHERE StockID='" . $_SESSION['Adjustment']->StockID . "'",$db);
	$myrow = DB_fetch_row($result);
	if (DB_num_rows($result)==0) {
		echo '<P>'. _('The entered item code does not exist').'.';
		$InputError = true;
	} elseif (!is_numeric($_SESSION['Adjustment']->Quantity)){
		echo '<P>'. _('The quantity entered must be numeric'). '.';
		$InputError = true;
	} elseif ($_SESSION['Adjustment']->Quantity==0){
		echo '<P>'. _('The quantity entered cannot be zero! There would be no adjustment to make'). '.';
		$InputError = true;
	} elseif ($_SESSION['Adjustment']->Controlled==1 AND count($_SESSION['Adjustment']->SerialItems)==0) {
		echo '<P>'. _('The item entered is a controlled item that requires the detail of the serial numbers (or batch references) to be adjusted, to be entered').'.';
		$InputError = true;
	}

	if (!$InputError) {

/*All inputs must be sensible so make the stock movement records and update the locations stocks */

		$AdjustmentNumber = GetNextTransNo(17,$db);
		$PeriodNo = GetPeriod (Date($DefaultDateFormat), $db);
		$SQLAdjustmentDate = FormatDateForSQL(Date($DefaultDateFormat));
		$CompanyRecord = ReadInCompanyRecord($db);


		$SQL = 'BEGIN';
		$Result = DB_query($SQL,$db);

		// Need to get the current location quantity will need it later for the stock movement
		$SQL="SELECT LocStock.Quantity
			FROM LocStock
			WHERE LocStock.StockID='" . $_SESSION['Adjustment']->StockID . "'
			AND LocCode= '" . $_SESSION['Adjustment']->StockLocation . "'";
		$Result = DB_query($SQL, $db);
		if (DB_num_rows($Result)==1){
			$LocQtyRow = DB_fetch_row($Result);
			$QtyOnHandPrior = $LocQtyRow[0];
		} else {
			// There must actually be some error this should never happen
			$QtyOnHandPrior = 0;
		}

		$SQL = "INSERT INTO StockMoves (
				StockID,
				Type,
				TransNo,
				LocCode,
				TranDate,
				Prd,
				Reference,
				Qty,
				NewQOH)
			VALUES (
				'" . $_SESSION['Adjustment']->StockID . "',
				17,
				" . $AdjustmentNumber . ",
				'" . $_SESSION['Adjustment']->StockLocation . "',
				'" . $SQLAdjustmentDate . "',
				" . $PeriodNo . ",
				'" . $_SESSION['Adjustment']->Narrative ."',
				" . $_SESSION['Adjustment']->Quantity . ",
				" . ($QtyOnHandPrior + $_SESSION['Adjustment']->Quantity) . "
			)";


		$ErrMsg =  _('CRITICAL ERROR! NOTE DOWN THIS ERROR AND SEEK ASSISTANCE: The stock movement record cannot be inserted because');
		$DbgMsg =  _('The following SQL to insert the stock movement record was used');
		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);


/*Get the ID of the StockMove... */
		$StkMoveNo = DB_Last_Insert_ID($db);

/*Insert the StockSerialMovements and update the StockSerialItems  for controlled items*/

		if ($_SESSION['Adjustment']->Controlled ==1){
			foreach($_SESSION['Adjustment']->SerialItems as $Item){
			/*We need to add or update the StockSerialItem record and
			The StockSerialMoves as well */

				/*First need to check if the serial items already exists or not */
				$SQL = "SELECT Count(*)
					FROM StockSerialItems
					WHERE
					StockID='" . $_SESSION['Adjustment']->StockID . "'
					AND LocCode='" . $_SESSION['Adjustment']->StockLocation . "'
					AND SerialNo='" . $Item->BundleRef . "'";
				$ErrMsg = '<BR>'._('Unable to determine if the serial item exists');
				$Result = DB_query($SQL,$db,$ErrMsg);
				$SerialItemExistsRow = DB_fetch_row($Result);

				if ($SerialItemExistsRow[0]==1){

					$SQL = "UPDATE StockSerialItems SET
						Quantity= Quantity + " . $Item->BundleQty . "
						WHERE
						StockID='" . $_SESSION['Adjustment']->StockID . "'
						AND LocCode='" . $_SESSION['Adjustment']->StockLocation . "'
						AND SerialNo='" . $Item->BundleRef . "'";

					$ErrMsg =  _('CRITICAL ERROR! NOTE DOWN THIS ERROR AND SEEK ASSISTANCE: The serial stock item record could not be updated because');
					$DbgMsg =  _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
				} else {
					/*Need to insert a new serial item record */
					$SQL = "INSERT INTO StockSerialItems (StockID,
									LocCode,
									SerialNo,
									Quantity)
						VALUES ('" . $_SESSION['Adjustment']->StockID . "',
						'" . $_SESSION['Adjustment']->StockLocation . "',
						'" . $Item->BundleRef . "',
						" . $Item->BundleQty . ")";

					$ErrMsg =  _('CRITICAL ERROR! NOTE DOWN THIS ERROR AND SEEK ASSISTANCE: The serial stock item record could not be updated because');
					$DbgMsg =  _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
				}


				/* now insert the serial stock movement */

				$SQL = "INSERT INTO StockSerialMoves (StockMoveNo, StockID, SerialNo, MoveQty) VALUES (" . $StkMoveNo . ", '" . $_SESSION['Adjustment']->StockID . "', '" . $Item->BundleRef . "', " . $Item->BundleQty . ")";
				$ErrMsg =  _('CRITICAL ERROR! NOTE DOWN THIS ERROR AND SEEK ASSISTANCE: The serial stock movement record could not be inserted because');
				$DbgMsg =  _('The following SQL to insert the serial stock movement records was used');
				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

			}/* foreach controlled item in the serialitems array */
		} /*end if the adjustment item is a controlled item */



		$SQL = "UPDATE LocStock SET Quantity = Quantity + " . $_SESSION['Adjustment']->Quantity . " WHERE StockID='" . $_SESSION['Adjustment']->StockID . "' AND LocCode='" . $_SESSION['Adjustment']->StockLocation . "'";

		$ErrMsg = _('CRITICAL ERROR! NOTE DOWN THIS ERROR AND SEEK ASSISTANCE: The location stock record could not be updated because');
		$DbgMsg = _('The following SQL to update the stock record was used');

		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

		if ($CompanyRecord['GLLink_Stock']==1 AND $StandardCost > 0){

			$StockGLCodes = GetStockGLCode($StockID,$db);

			$SQL = "INSERT INTO GLTrans (Type, TypeNo, TranDate, PeriodNo, Account, Amount, Narrative) VALUES (17," .$AdjustmentNumber . ", '" . $SQLAdjustmentDate . "', " . $PeriodNo . ", " .  $StockGLCodes['AdjGLAct'] . ", " . $StandardCost * -($_SESSION['Adjustment']->Quantity) . ", '" . $_SESSION['Adjustment']->StockID . " x " . $_SESSION['Adjustment']->Quantity . " @ " . $StandardCost . " - " . $_SESSION['Adjustment']->Narrative . "')";

			$ErrMsg = _('CRITICAL ERROR! NOTE DOWN THIS ERROR AND SEEK ASSISTANCE: The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);

			$SQL = "INSERT INTO GLTrans (Type, TypeNo, TranDate, PeriodNo, Account, Amount, Narrative) VALUES (17," .$AdjustmentNumber . ", '" . $SQLAdjustmentDate . "', " . $PeriodNo . ", " .  $StockGLCodes['StockAct'] . ", " . $StandardCost * $_SESSION['Adjustment']->Quantity . ", '" . $_SESSION['Adjustment']->StockID . " x " . $_SESSION['Adjustment']->Quantity . " @ " . $StandardCost . " - " . $_SESSION['Adjustment']->Narrative . "')";

			$Errmsg = _('CRITICAL ERROR! NOTE DOWN THIS ERROR AND SEEK ASSISTANCE: The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);
		}

		$Result = DB_query('Commit',$db);

		echo '<P>'. _('A stock Adjustment for'). ' ' . $_SESSION['Adjustment']->StockID . ' -  ' . $_SESSION['Adjustment']->ItemDescription . ' '._('has been created from location').' ' . $_SESSION['Adjustment']->StockLocation .' '.('for a quantity of').' ' . $_SESSION['Adjustment']->Quantity;
		unset ($_SESSION['Adjustment']);
	} /* end if there was no input error */

}/* end if the user hit enter the adjustment */


echo '<FORM ACTION="'. $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';


echo '<CENTER><TABLE><TR><TD>'. _('Stock Code'). ':</TD><TD><input type=text name="StockID" size=21 value="' . $_SESSION['Adjustment']->StockID . '" maxlength=20> <INPUT TYPE=SUBMIT NAME="CheckCode" VALUE="'._('Check Part').'"></TD></TR>';

if (strlen($_SESSION['Adjustment']->ItemDescription)>1){
	echo '<TR><TD COLSPAN=3><FONT COLOR=BLUE SIZE=3>' . $_SESSION['Adjustment']->ItemDescription . ' ('._('In Units of').' ' . $_SESSION['Adjustment']->PartUnit . ' ) - ' . _('Unit Cost').' = ' . $_SESSION['Adjustment']->StandardCost . '</FONT></TD></TR>';
}

echo '<TR><TD>'. _('Adjustment to Stock At Location').':</TD><TD><SELECT name="StockLocation"> ';

$sql = 'SELECT LocCode, LocationName FROM Locations';
$resultStkLocs = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_SESSION['Adjustment']->StockLocation)){
		if ($myrow['LocCode'] == $_SESSION['Adjustment']->StockLocation){
		     echo '<OPTION SELECTED Value="' . $myrow['LocCode'] . '">' . $myrow['LocationName'];
		} else {
		     echo '<OPTION Value="' . $myrow['LocCode'] . '">' . $myrow['LocationName'];
		}
	} elseif ($myrow['LocCode']==$_SESSION['UserStockLocation']){
		 echo '<OPTION SELECTED Value="' . $myrow['LocCode'] . '">' . $myrow['LocationName'];
		 $_POST['StockLocation']=$myrow['LocCode'];
	} else {
		 echo '<OPTION Value="' . $myrow['LocCode'] . '">' . $myrow['LocationName'];
	}
}

echo '</SELECT></TD></TR>';

echo '<TR><TD>'. _('Comments On Why').':</TD><TD><input type=text name="Narrative" size=32 maxlength=30 value="' . $_SESSION['Adjustment']->Narrative . '"></TD></TR>';

echo '<TR><TD>'._('Adjustment Quantity').':</TD>';

echo '<TD';
if ($_SESSION['Adjustment']->Controlled==1){
		if ($_SESSION['Adjustment']->StockLocation != ''){
			echo '<INPUT TYPE="HIDDEN" NAME="Quantity" Value="' . $_SESSION['Adjustment']->Quantity . '">
				'.$_SESSION['Adjustment']->Quantity.' &nbsp; &nbsp; &nbsp; &nbsp;
				[<A HREF="'.$rootpath.'/StockAdjustmentsControlled.php?AdjType=REMOVE&' . SID . '">'._('Remove').'</A>]
				[<A HREF="'.$rootpath.'/StockAdjustmentsControlled.php?AdjType=ADD&' . SID . '">'._('Add').'</A>]';
		} else {
			prnMsg( _('Please Select a Location and Press "Enter Stock Adjustment" below to enter Controlled Items') . '.', '','&nbsp;');
		}
} else {
	echo '<INPUT TYPE=TEXT NAME="Quantity" SIZE=12 MAXLENGTH=12 Value="' . $_SESSION['Adjustment']->Quantity . '">';
}
echo '</TD></TR>';

echo '</TABLE><BR><INPUT TYPE=SUBMIT NAME="EnterAdjustment" VALUE="'. _('Enter Stock Adjustment'). '">';
echo '<HR>';


echo '<A HREF="'. $rootpath. '/StockStatus.php?' . SID . 'StockID='. $_SESSION['Adjustment']->StockID . '">'._('Show Stock Status').'</A>';
echo '<BR><A HREF="'.$rootpath.'/StockMovements.php?' . SID . 'StockID=' . $_SESSION['Adjustment']->StockID . '">'._('Show Movements').'</A>';
echo '<BR><A HREF="'.$rootpath.'/StockUsage.php?' . SID . 'StockID=' . $_SESSION['Adjustment']->StockID . '&StockLocation=' . $_POST['StockLocation'] . '">'._('Show Stock Usage').'</A>';
echo '<BR><A HREF="'.$rootpath.'/SelectSalesOrder.php?' . SID . 'SelectedStockItem='. $_SESSION['Adjustment']->StockID .'&StockLocation=' . $_POST['StockLocation'] . '">'. _('Search Outstanding Sales Orders').'</A>';
echo '<BR><A HREF="'.$rootpath.'/SelectCompletedOrder.php?' . SID . 'SelectedStockItem=' . $_SESSION['Adjustment']->StockID .'">'._('Search Completed Sales Orders').'</A>';

echo '</form>';
include('includes/footer.inc');

?>