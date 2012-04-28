<?php

/* $Id$*/

include('includes/session.inc');
$title = _('Work Order Entry');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

echo '<p class="page_title_text">
		<img src="'.$rootpath.'/css/'.$theme.'/images/transactions.png" title="' . _('Search') . '" alt="" />' . ' ' . $title.'
	</p>';

if (isset($_GET['ReqDate'])){
	$ReqDate = $_GET['ReqDate'];
} else {
	$ReqDate=Date('Y-m-d');
}
if (isset($_GET['loccode'])){
	$LocCode = $_GET['loccode'];
} else {
	$LocCode=$_SESSION['UserStockLocation'];
}

// check for new or modify condition
if (isset($_REQUEST['WO']) and $_REQUEST['WO']!=''){
	// modify
	$_POST['WO'] = $_REQUEST['WO'];
	$EditingExisting = true;
} else {
	// new
	$_POST['WO'] = GetNextTransNo(40,$db);
	$sql = "INSERT INTO workorders (wo,
							 loccode,
							 requiredby,
							 startdate)
			 VALUES ('" . $_POST['WO'] . "',
					'" . $LocCode . "',
					'" . $ReqDate . "',
					'" . Date('Y-m-d'). "')";
	$InsWOResult = DB_query($sql,$db);
}


if (isset($_GET['NewItem'])){
	$NewItem = $_GET['NewItem'];
}
if (isset($_GET['ReqQty'])){
	$ReqQty = $_GET['ReqQty'];
}
if (!isset($_POST['StockLocation'])){
	if (isset($LocCode)){
		$_POST['StockLocation']=$LocCode;
	} elseif (isset($_SESSION['UserStockLocation'])){
		$_POST['StockLocation']=$_SESSION['UserStockLocation'];
	}
}


if (isset($_POST['Search'])){

	If ($_POST['Keywords'] AND $_POST['StockCode']) {
		prnMsg(_('Stock description keywords have been used in preference to the Stock code extract entered'),'warn');
	}
	If (mb_strlen($_POST['Keywords'])>0) {
			//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.description " . LIKE . " '$SearchString'
					AND stockmaster.discontinued=0
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		}

	} elseif (mb_strlen($_POST['StockCode'])>0){

		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		$SearchString = '%' . $_POST['StockCode'] . '%';

		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued=0
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		}
	} else {
		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		  }
	}

	$SQL .= " LIMIT " . $_SESSION['DisplayRecordsMax'];

	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL used to get the part selection was');
	$SearchResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

	if (DB_num_rows($SearchResult)==0 ){
		prnMsg (_('There are no products available meeting the criteria specified'),'info');

		if ($debug==1){
			prnMsg(_('The SQL statement used was') . ':<br />' . $SQL,'info');
		}
	}
	if (DB_num_rows($SearchResult)==1){
		$myrow=DB_fetch_array($SearchResult);
		$NewItem = $myrow['stockid'];
		DB_data_seek($SearchResult,0);
	}

} //end of if search

if (isset($NewItem) AND isset($_POST['WO'])){

	  $InputError=false;
	  $CheckItemResult = DB_query("SELECT mbflag,
											eoq,
											controlled
									FROM stockmaster
									WHERE stockid='" . $NewItem . "'",
											$db);
	if (DB_num_rows($CheckItemResult)==1){
  		$CheckItemRow = DB_fetch_array($CheckItemResult);
		if ($CheckItemRow['controlled']==1 AND $_SESSION['DefineControlledOnWOEntry']==1){ //need to add serial nos or batches to determine quantity
			$EOQ = 0;
		} else {
			if (!isset($ReqQty)) {
				$ReqQty=$CheckItemRow['eoq'];
			}
			$EOQ = $ReqQty;
		}
  		if ($CheckItemRow['mbflag']!='M'){
  			prnMsg(_('The item selected cannot be added to a work order because it is not a manufactured item'),'warn');
  			$InputError=true;
  		}
	} else {
		prnMsg(_('The item selected cannot be found in the database'),'error');
		$InputError = true;
	}
	 $CheckItemResult = DB_query("SELECT stockid
									FROM woitems
									WHERE stockid='" . $NewItem . "'
									AND wo='" .$_POST['WO'] . "'",
									$db);
	if (DB_num_rows($CheckItemResult)==1){
		prnMsg(_('This item is already on the work order and cannot be added again'),'warn');
		$InputError=true;
	}


	if ($InputError==false){
		$CostResult = DB_query("SELECT SUM((materialcost+labourcost+overheadcost)*bom.quantity) AS cost
									FROM stockmaster INNER JOIN bom
									ON stockmaster.stockid=bom.component
									WHERE bom.parent='" . $NewItem . "'
									AND bom.loccode='" . $_POST['StockLocation'] . "'",
							 $db);
		$CostRow = DB_fetch_row($CostResult);
		if (is_null($CostRow[0]) OR $CostRow[0]==0){
			$Cost =0;
			prnMsg(_('The cost of this item as accumulated from the sum of the component costs is nil. This could be because there is no bill of material set up ... you may wish to double check this'),'warn');
		} else {
			$Cost = $CostRow[0];
		}
		if (!isset($EOQ)){
			$EOQ=1;
		}
	
		$Result = DB_Txn_Begin($db);
	
		// insert parent item info
		$sql = "INSERT INTO woitems (wo,
									 stockid,
									 qtyreqd,
									 stdcost)
				 VALUES ( '" . $_POST['WO'] . "',
							'" . $NewItem . "',
							'" . $EOQ . "',
							'" . $Cost . "'
							)";
		$ErrMsg = _('The work order item could not be added');
		$result = DB_query($sql,$db,$ErrMsg);
	
		//Recursively insert real component requirements - see includes/SQL_CommonFunctions.in for function WoRealRequirements
		WoRealRequirements($db, $_POST['WO'], $_POST['StockLocation'], $NewItem);
	
		$result = DB_Txn_Commit($db);
	
		unset($NewItem);
	} //end if there were no input errors
} //adding a new item to the work order


if (isset($_POST['submit'])) { //The update button has been clicked

	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') .'">' . _('Enter a new work order') . '</a>
			<br />
			<a href="' . $rootpath . '/SelectWorkOrder.php">' . _('Select an existing work order') . '</a>
			<br />
			<a href="'. $rootpath . '/WorkOrderCosting.php?WO=' .  $_REQUEST['WO'] . '">' . _('Go to Costing'). '</a>
		</div>';

	$Input_Error = false; //hope for the best
	 for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
	   	if (!is_numeric(filter_number_format($_POST['OutputQty'.$i]))){
		   	prnMsg(_('The quantity entered must be numeric'),'error');
			$Input_Error = true;
		} elseif (filter_number_format($_POST['OutputQty'.$i])<=0){
			prnMsg(_('The quantity entered must be a positive number greater than zero'),'error');
			$Input_Error = true;
		}
	 }
	 if (!Is_Date($_POST['RequiredBy'])){
		prnMsg(_('The required by date entered is in an invalid format'),'error');
		$Input_Error = true;
	 }

	if ($Input_Error == false) {

		$SQL_ReqDate = FormatDateForSQL($_POST['RequiredBy']);
		$QtyRecd=0;

		for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
				$QtyRecd += filter_number_format($_POST['RecdQty'.$i]);
		}
		unset($sql);

		if ($QtyRecd==0){ //can only change factory location if Qty Recd is 0
				$sql[] = "UPDATE workorders SET requiredby='" . $SQL_ReqDate . "',
												startdate='" . FormatDateForSQL($_POST['StartDate']) . "',
												loccode='" . $_POST['StockLocation'] . "'
							WHERE wo='" . $_POST['WO'] . "'";
		} else {
				prnMsg(_('The factory where this work order is made can only be updated if the quantity received on all output items is 0'),'warn');
				$sql[] = "UPDATE workorders SET requiredby='" . $SQL_ReqDate . "',
												startdate='" . FormatDateForSQL($_POST['StartDate']) . "'
							WHERE wo='" . $_POST['WO'] . "'";
		}

		for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
			if (!isset($_POST['NextLotSNRef'.$i])) {
				$_POST['NextLotSNRef'.$i]='';
			}
			if (isset($_POST['QtyRecd'.$i]) AND $_POST['QtyRecd'.$i]>filter_number_format($_POST['OutputQty'.$i])){
				$_POST['OutputQty'.$i]=$_POST['QtyRecd'.$i]; //OutputQty must be >= Qty already reced
			}
			if ($_POST['RecdQty'.$i]==0 AND (!isset($_POST['HasWOSerialNos'.$i]) OR $_POST['HasWOSerialNos'.$i]==false)){
				/* can only change location cost if QtyRecd=0 */
				$CostResult = DB_query("SELECT SUM((materialcost+labourcost+overheadcost)*bom.quantity) AS cost
											FROM stockmaster INNER JOIN bom
											ON stockmaster.stockid=bom.component
											WHERE bom.parent='" . $_POST['OutputItem'.$i] . "'
											AND bom.loccode='" . $_POST['StockLocation'] . "'",
									 $db);
				$CostRow = DB_fetch_row($CostResult);
				if (is_null($CostRow[0])){
					$Cost =0;
					prnMsg(_('The cost of this item as accumulated from the sum of the component costs is nil. This could be because there is no bill of material set up ... you may wish to double check this'),'warn');
				} else {
					$Cost = $CostRow[0];
				}
				$sql[] = "UPDATE woitems SET qtyreqd =  '". filter_number_format($_POST['OutputQty' . $i]) . "',
												 nextlotsnref = '". $_POST['NextLotSNRef'.$i] ."',
												 stdcost ='" . $Cost . "'
								  WHERE wo='" . $_POST['WO'] . "'
								  AND stockid='" . $_POST['OutputItem'.$i] . "'";
  			} elseif (isset($_POST['HasWOSerialNos'.$i]) and $_POST['HasWOSerialNos'.$i]==false) {
				$sql[] = "UPDATE woitems SET qtyreqd =  '". filter_number_format($_POST['OutputQty' . $i]) . "',
												 nextlotsnref = '". $_POST['NextLotSNRef'.$i] ."'
								  WHERE wo='" . $_POST['WO'] . "'
								  AND stockid='" . $_POST['OutputItem'.$i] . "'";
			}
		}

		//run the SQL from either of the above possibilites
		$ErrMsg = _('The work order could not be added/updated');
		foreach ($sql as $sql_stmt){
		//	echo '<br />' . $sql_stmt;
			$result = DB_query($sql_stmt,$db,$ErrMsg);
		}

		prnMsg(_('The work order has been updated'),'success');

		for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
			unset($_POST['OutputItem'.$i]);
			unset($_POST['OutputQty'.$i]);
			unset($_POST['QtyRecd'.$i]);
			unset($_POST['NetLotSNRef'.$i]);
			unset($_POST['HasWOSerialNos'.$i]);
		}
	}
} elseif (isset($_POST['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete=false; //always assume the best

	// can't delete it there are open work issues
	$HasTransResult = DB_query("SELECT * FROM stockmoves
								WHERE (stockmoves.type= 26 OR stockmoves.type=28)
								AND reference " . LIKE  . " '%" . $_POST['WO'] . "%'",$db);
	if (DB_num_rows($HasTransResult)>0){
		prnMsg(_('This work order cannot be deleted because it has issues or receipts related to it'),'error');
		$CancelDelete=true;
	}

	if ($CancelDelete==false) { //ie all tests proved ok to delete
		DB_Txn_Begin($db);
		$ErrMsg = _('Unable to delete the work order because');
		$DbgMsg = _('The SQL that failed to delete the work order was');
		//delete the worequirements
		$sql = "DELETE FROM worequirements WHERE wo='" . $_POST['WO'] . "'";
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
		//delete the items on the work order
		$sql = "DELETE FROM woitems WHERE wo='" . $_POST['WO'] . "'";
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
		//delete the controlled items defined in wip
		$sql="DELETE FROM woserialnos WHERE wo='" . $_POST['WO'] . "'";
		$ErrMsg=_('The work order serial numbers could not be deleted');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
		// delete the actual work order
		$sql="DELETE FROM workorders WHERE wo='" . $_POST['WO'] . "'";
		$ErrMsg=_('The work order could not be deleted');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);

		DB_Txn_Commit($db);
		prnMsg(_('The work order has been deleted'),'success');


		echo '<p><a href="' . $rootpath . '/SelectWorkOrder.php">' . _('Select an existing outstanding work order') . '</a></p>';
		unset($_POST['WO']);
		for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
			 unset($_POST['OutputItem'.$i]);
			 unset($_POST['OutputQty'.$i]);
			 unset($_POST['QtyRecd'.$i]);
			 unset($_POST['NetLotSNRef'.$i]);
			 unset($_POST['HasWOSerialNos'.$i]);
		}
		include('includes/footer.inc');
		exit;
	}
}

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<input type="hidden" name="WO" value="' .$_POST['WO'] . '" />';

echo '<br /><table class="selection">';

$sql="SELECT workorders.loccode,
			 requiredby,
			 startdate,
			 costissued,
			 closed
		FROM workorders INNER JOIN locations
		ON workorders.loccode=locations.loccode
		WHERE workorders.wo='" . $_POST['WO'] . "'";

$WOResult = DB_query($sql,$db);
if (DB_num_rows($WOResult)==1){
	$myrow = DB_fetch_array($WOResult);
	$_POST['StartDate'] = ConvertSQLDate($myrow['startdate']);
	$_POST['CostIssued'] = $myrow['costissued'];
	$_POST['Closed'] = $myrow['closed'];
	$_POST['RequiredBy'] = ConvertSQLDate($myrow['requiredby']);
	$_POST['StockLocation'] = $myrow['loccode'];
	$ErrMsg =_('Could not get the work order items');
	$WOItemsResult = DB_query("SELECT woitems.stockid,
									stockmaster.description,
									qtyreqd,
									qtyrecd,
									stdcost,
									nextlotsnref,
									controlled,
									serialised,
									nextserialno,
									decimalplaces
								FROM woitems INNER JOIN stockmaster
								ON woitems.stockid=stockmaster.stockid
								WHERE wo='" .$_POST['WO'] . "'",$db,$ErrMsg);

	$NumberOfOutputs=DB_num_rows($WOItemsResult);
	$i=1;
	while ($WOItem=DB_fetch_array($WOItemsResult)){
				$_POST['OutputItem' . $i]=$WOItem['stockid'];
				$_POST['OutputItemDesc'.$i]=$WOItem['description'];
				$_POST['OutputQty' . $i]= $WOItem['qtyreqd'];
		  		$_POST['RecdQty' .$i] =$WOItem['qtyrecd'];
		  		$_POST['DecimalPlaces' . $i] = $WOItem['decimalplaces'];
		  		if ($WOItem['serialised']==1 AND $WOItem['nextserialno']>0){
		  		   $_POST['NextLotSNRef' .$i]=$WOItem['nextserialno'];
		  		} else {
				   $_POST['NextLotSNRef' .$i]=$WOItem['nextlotsnref'];
				}
		  		$_POST['Controlled'.$i] =$WOItem['controlled'];
		  		$_POST['Serialised'.$i] =$WOItem['serialised'];
		  		$HasWOSerialNosResult = DB_query("SELECT * FROM woserialnos WHERE wo='" . $_POST['WO'] . "'",$db);
		  		if (DB_num_rows($HasWOSerialNosResult)>0){
		  		   $_POST['HasWOSerialNos']=true;
		  		} else {
				   $_POST['HasWOSerialNos']=false;
				}
		  		$i++;
	}
}

echo '<tr>
		<td class="label">' . _('Work Order Reference') . ':</td>
		<td>' . $_POST['WO'] . '</td>
	</tr>';
echo '<tr>
		<td class="label">' . _('Factory Location') .':</td>
		<td><select name="StockLocation" onchange="ReloadForm(form1.submit)">';
$LocResult = DB_query("SELECT loccode,locationname FROM locations",$db);
while ($LocRow = DB_fetch_array($LocResult)){
	if ($_POST['StockLocation']==$LocRow['loccode']){
		echo '<option selected="selected" value="' . $LocRow['loccode'] .'">' . $LocRow['locationname'] . '</option>';
	} else {
		echo '<option value="' . $LocRow['loccode'] .'">' . $LocRow['locationname'] . '</option>';
	}
}
echo '</select></td>
	</tr>';
if (!isset($_POST['StartDate'])){
	$_POST['StartDate'] = Date($_SESSION['DefaultDateFormat']);
}

echo '<tr>
		<td class="label">' . _('Start Date') . ':</td>
		<td><input type="text" name="StartDate" size="12" maxlength="12" value="' . $_POST['StartDate'] . '" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" /></td>
	</tr>';

if (!isset($_POST['RequiredBy'])){
	$_POST['RequiredBy'] = Date($_SESSION['DefaultDateFormat']);
}

echo '<tr>
		<td class="label">' . _('Required By') . ':</td>
		<td><input type="text" name="RequiredBy"  size="12" maxlength="12" value="' . $_POST['RequiredBy'] . '" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" /></td>
	</tr>';

if (isset($WOResult)){
	echo '<tr>
			<td class="label">' . _('Accumulated Costs') . ':</td>
			<td class="number">' . locale_number_format($myrow['costissued'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
		</tr>';
}
echo '</table>
		<br /><table class="selection">';
echo '<tr>
		<th>' . _('Output Item') . '</th>
		<th>' . _('Qty Required') . '</th>
		<th>' . _('Qty Received') . '</th>
		<th>' . _('Balance Remaining') . '</th>
		<th>' . _('Next Lot/SN Ref') . '</th>
        <th>&nbsp;</th>
	</tr>';
$j=0;

if (isset($NumberOfOutputs)){
	for ($i=1;$i<=$NumberOfOutputs;$i++){
		if ($j==1) {
			echo '<tr class="OddTableRows">';
			$j=0;
		} else {
			echo '<tr class="EvenTableRows">';
			$j++;
		}
		echo '<td><input type="hidden" name="OutputItem' . $i . '" value="' . $_POST['OutputItem' .$i] . '" />' . $_POST['OutputItem' . $i] . ' - ' . $_POST['OutputItemDesc' .$i] . '</td>';
		if ($_POST['Controlled'.$i]==1 AND $_SESSION['DefineControlledOnWOEntry']==1){
			echo '<td class="number">' . locale_number_format($_POST['OutputQty' . $i],$_POST['DecimalPlaces' . $i]);
			echo '<input type="hidden" name="OutputQty' . $i .'" value="' . locale_number_format($_POST['OutputQty' . $i],$_POST['DecimalPlaces' . $i]) . '" /></td>';
		} else {
		  	echo'<td><input type="text" class="number" name="OutputQty' . $i . '" value="' . locale_number_format($_POST['OutputQty' . $i],$_POST['DecimalPlaces' . $i]) . '" size="10" maxlength="10" /></td>';
		}
		 echo '<td class="number"><input type="hidden" name="RecdQty' . $i . '" value="' . $_POST['RecdQty' .$i] . '" />' . $_POST['RecdQty' .$i] .'</td>
		  		<td class="number">' . locale_number_format(($_POST['OutputQty' . $i] - $_POST['RecdQty' .$i]),$_POST['DecimalPlaces' . $i]) . '</td>';
		if ($_POST['Controlled'.$i]==1){
			echo '<td><input type="text" name="NextLotSNRef' .$i . '" value="' . $_POST['NextLotSNRef'.$i] . '" /></td>';
			if ($_SESSION['DefineControlledOnWOEntry']==1){
				if ($_POST['Serialised' . $i]==1){
					$LotOrSN = _('S/Ns');
				} else {
					$LotOrSN = _('Batches');
				}
                echo '<td><a href="' . $rootpath . '/WOSerialNos.php?WO=' . $_POST['WO'] . '&amp;StockID=' . $_POST['OutputItem' .$i] . '&amp;Description=' . $_POST['OutputItemDesc' .$i] . '&amp;Serialised=' . $_POST['Serialised' .$i] . '&amp;NextSerialNo=' . $_POST['NextLotSNRef' .$i] . '">' . $LotOrSN . '</a></td>';
			}
		}
		echo '<td>';
		wikiLink('WorkOrder', $_POST['WO'] . $_POST['OutputItem' .$i]);
		if (isset($_POST['Controlled' . $i])) {
			echo '<input type="hidden" name="Controlled' . $i .'" value="' . $_POST['Controlled' . $i] . '" />';
		}
		if (isset( $_POST['Serialised' . $i])) {
			echo '<input type="hidden" name="Serialised' . $i .'" value="' . $_POST['Serialised' . $i] . '" />';
		}
		if (isset($_POST['HasWOSerialNos' . $i])) {
			echo '<input type="hidden" name="HasWOSerialNos' . $i .'" value="' . $_POST['HasWOSerialNos' . $i] . '" />';
		}
        echo '</td>
            </tr>';
	}
	echo '<tr><td><input type="hidden" name="NumberOfOutputs" value="' . ($i -1).'" /></td></tr>';
}
echo '</table>';

echo '<div class="centre">
		<br />
		<input type="submit" name="submit" value="' . _('Update') . '" />
		<br />
		<br />
		<input type="submit" name="delete" value="' . _('Delete This Work Order') . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');" />
		<br />
	</div>';

$SQL="SELECT categoryid,
			categorydescription
		FROM stockcategory
		WHERE stocktype='F' OR stocktype='D'
		ORDER BY categorydescription";
	$result1 = DB_query($SQL,$db);

echo '<table class="selection">
		<tr>
			<td>' . _('Select a stock category') . ':<select name="StockCat">';

if (!isset($_POST['StockCat'])){
	echo '<option selected="selected" value="All">' . _('All') . '</option>';
	$_POST['StockCat'] ='All';
} else {
	echo '<option value="All">' . _('All') . '</option>';
}

while ($myrow1 = DB_fetch_array($result1)) {

	if ($_POST['StockCat']==$myrow1['categoryid']){
		echo '<option selected="selected" value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	} else {
		echo '<option value="'. $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	}
}
?>

</select></td>
<td><?php echo _('Enter text extracts in the'); ?> <b><?php echo _('description'); ?></b>:</td>
<td><input type="text" name="Keywords" size="20" maxlength="25" value="<?php if (isset($_POST['Keywords'])) echo $_POST['Keywords']; ?>" /></td>
</tr>
<tr><td></td>
	<td><b><?php echo _('OR'); ?> </b><?php echo _('Enter extract of the'); ?> <b><?php echo _('Stock Code'); ?></b>:</td>
	<td><input type="text" name="StockCode" size="15" maxlength="18" value="<?php if (isset($_POST['StockCode'])) echo $_POST['StockCode']; ?>" /></td>
</tr>
</table>
<br /><div class="centre"><input type="submit" name="Search" value="<?php echo _('Search Now'); ?>" />

<?php

echo '</div>';

if (isset($SearchResult)) {

	if (DB_num_rows($SearchResult)>1){

		echo '<table cellpadding="2" class="selection">';
		$TableHeader = '<tr>
							<th>' . _('Code') . '</th>
				   			<th>' . _('Description') . '</th>
				   			<th>' . _('Units') . '</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
				   		</tr>';
		echo $TableHeader;
		$j = 1;
		$k=0; //row colour counter
		$ItemCodes = array();
		for ($i=1;$i<=$NumberOfOutputs;$i++){
			$ItemCodes[] =$_POST['OutputItem'.$i];
		}

		while ($myrow=DB_fetch_array($SearchResult)) {

			if (!in_array($myrow['stockid'],$ItemCodes)){
				if (function_exists('imagecreatefrompng') ){
					$ImageSource = '<img src="GetStockImage.php?automake=1&amp;textcolor=FFFFFF&amp;bgcolor=CCCCCC&amp;StockID=' . urlencode($myrow['stockid']). '&amp;text=&amp;width=64&amp;height=64" alt="" />';
				} else {
					if(file_exists($_SERVER['DOCUMENT_ROOT'] . $rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg')) {
						$ImageSource = '<img src="' .$_SERVER['DOCUMENT_ROOT'] . $rootpath .  '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg" alt="" />';
					} else {
						$ImageSource = _('No Image');
					}
				}

				if ($k==1){
					echo '<tr class="EvenTableRows">';
					$k=0;
				} else {
					echo '<tr class="OddTableRows">';
					$k=1;
				}

				printf('<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td><a href="%s">' . _('Add to Work Order') . '</a></td>
						</tr>',
						$myrow['stockid'],
						$myrow['description'],
						$myrow['units'],
						$ImageSource,
						htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?WO=' . $_POST['WO'] . '&amp;NewItem=' . $myrow['stockid'].'&amp;Line='.$i);

				$j++;
				If ($j == 25){
					$j=1;
					echo $TableHeader;
				} //end of page full new headings if
			} //end if not already on work order
		}//end of while loop
	} //end if more than 1 row to show
	echo '</table>';

}#end if SearchResults to show


if (!isset($_GET['NewItem']) or $_GET['NewItem']=='') {
	echo '<script  type="text/javascript">defaultControl(document.forms[0].StockCode);</script>';
} else {
	echo '<script  type="text/javascript">defaultControl(document.forms[0].OutputQty"'.$_GET['Line'].'");</script>';
}

echo '</div>
      </form>';
include('includes/footer.inc');
?>