<?php
/* $Revision: 1.2 $ */

$PageSecurity = 11;

include('includes/session.inc');

$title = _('Fixed Asset Category Maintenance');

include('includes/header.inc');

if (isset($_GET['SelectedCategory'])){
	$SelectedCategory = strtoupper($_GET['SelectedCategory']);
} else if (isset($_POST['SelectedCategory'])){
	$SelectedCategory = strtoupper($_POST['SelectedCategory']);
}

if (isset($_GET['DeleteProperty'])){

	$ErrMsg = _('Could not delete the property') . ' ' . $_GET['DeleteProperty'] . ' ' . _('because');
	$sql = "DELETE FROM stockitemproperties WHERE stkcatpropid=" . $_GET['DeleteProperty'];
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "DELETE FROM stockcatproperties WHERE stkcatpropid=" . $_GET['DeleteProperty'];
	$result = DB_query($sql,$db,$ErrMsg);
	prnMsg(_('Deleted the property') . ' ' . $_GET['DeleteProperty'],'success');
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	$_POST['CategoryID'] = strtoupper($_POST['CategoryID']);

	if (strlen($_POST['CategoryID']) > 6) {
		$InputError = 1;
		prnMsg(_('The Fixed Asset Category code must be six characters or less long'),'error');
	} elseif (strlen($_POST['CategoryID'])==0) {
		$InputError = 1;
		prnMsg(_('The Fixed Asset Category code must be at least 1 character but less than six characters long'),'error');
	} elseif (strlen($_POST['CategoryDescription']) >20) {
		$InputError = 1;
		prnMsg(_('The Fixed Asset Category description must be twenty characters or less long'),'error');
	}

	if ($SelectedCategory AND $InputError !=1) {

		/*SelectedCategory could also exist if submit had not been clicked this code
		would not run in this case cos submit is false of course  see the
		delete code below*/

		$sql = "UPDATE stockcategory SET 
					stocktype = '" . $_POST['StockType'] . "',
					categorydescription = '" . $_POST['CategoryDescription'] . "',
					stockact = " . $_POST['StockAct'] . ",
					adjglact = " . $_POST['AdjGLAct'] . ",
					materialuseagevarac = " . $_POST['MaterialUseageVarAc'] . ",
					wipact = " . $_POST['WIPAct'] . "
				WHERE
					categoryid = '$SelectedCategory'";
		$ErrMsg = _('Could not update the fixed asset category') . $_POST['CategoryDescription'] . _('because');
		$result = DB_query($sql,$db,$ErrMsg);

/*		for ($i=0;$i<=$_POST['PropertyCounter'];$i++){

			if (isset($_POST['PropReqSO' .$i]) and $_POST['PropReqSO' .$i] == true){
				$_POST['PropReqSO' .$i] =1;
			} else {
					$_POST['PropReqSO' .$i] =0;
			}
			if ($_POST['PropID' .$i] =='NewProperty' AND strlen($_POST['PropLabel'.$i])>0){
				$sql = "INSERT INTO stockcatproperties (categoryid,
							label,
							controltype,
							defaultvalue,
							reqatsalesorder)
						VALUES ('" . $SelectedCategory . "',
							'" . $_POST['PropLabel' . $i] . "',
							" . $_POST['PropControlType' . $i] . ",
							'" . $_POST['PropDefault' .$i] . "',
							" . $_POST['PropReqSO' .$i] . ')';
				$ErrMsg = _('Could not insert a new category property for') . $_POST['PropLabel' . $i];
				$result = DB_query($sql,$db,$ErrMsg);
			} elseif ($_POST['PropID' .$i] !='NewProperty') { //we could be amending existing properties
				$sql = "UPDATE stockcatproperties SET label ='" . $_POST['PropLabel' . $i] . "',
							controltype = " . $_POST['PropControlType' . $i] . ",
							defaultvalue = '"	. $_POST['PropDefault' .$i] . "',
							reqatsalesorder = " . $_POST['PropReqSO' .$i] . "
						WHERE stkcatpropid =" . $_POST['PropID' .$i];
				$ErrMsg = _('Updated the asset category property for') . ' ' . $_POST['PropLabel' . $i];
				$result = DB_query($sql,$db,$ErrMsg);
			}

		} //end of loop round properties
*/
		prnMsg(_('Updated the fixed asset category record for') . ' ' . $_POST['CategoryDescription'],'success');

	} elseif ($InputError !=1) {

	/*Selected category is null cos no item selected on first time round so must be adding a	record must be submitting new entries in the new asset category form */

		$sql = "INSERT INTO stockcategory (categoryid,
                                       stocktype,
                                       categorydescription,
                                       stockact,
                                       adjglact,
                                       materialuseagevarac,
                                       wipact)
                                       VALUES (
                                       '" . $_POST['CategoryID'] . "',
                                       '" . $_POST['StockType'] . "',
                                       '" . $_POST['CategoryDescription'] . "',
                                       " . $_POST['StockAct'] . ",
                                       " . $_POST['AdjGLAct'] . ",
                                       " . $_POST['MaterialUseageVarAc'] . ",
                                       " . $_POST['WIPAct'] . ")";
        $ErrMsg = _('Could not insert the new asset category') . $_POST['CategoryDescription'] . _('because');
        $result = DB_query($sql,$db,$ErrMsg);
		prnMsg(_('A new asset category record has been added for') . ' ' . $_POST['CategoryDescription'],'success');
		$sql="INSERT INTO stockcatproperties 
				VALUES(
					NULL, 
					'".$_POST['CategoryID']."', 
					'"._('Depreciation Type')."',
					'1',
					'"._('Straight Line').","._('Reducing Balance')."',
					'0')";
		$result=DB_query($sql,$db);
		$sql="INSERT INTO stockcatproperties 
				VALUES(
					NULL, 
					'".$_POST['CategoryID']."', 
					'"._('Annual Depreciation Percentage')."',
					'0',
					'5',
					'0')";
		$result=DB_query($sql,$db);
		$sql="INSERT INTO stockcatproperties 
				VALUES(
					NULL, 
					'".$_POST['CategoryID']."', 
					'"._('Annual Internal Depreciation Percentage')."',
					'0',
					'5',
					'0')";
		$result=DB_query($sql,$db);
	}
	//run the SQL from either of the above possibilites

	unset($_POST['StockType']);
	unset($_POST['CategoryDescription']);
	unset($_POST['StockAct']);
	unset($_POST['AdjGLAct']);
	unset($_POST['PurchPriceVarAct']);
	unset($_POST['MaterialUseageVarAc']);
	unset($_POST['WIPAct']);


} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'StockMaster'

	$sql= "SELECT COUNT(*) FROM stockmaster WHERE stockmaster.categoryid='$SelectedCategory'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		prnMsg(_('Cannot delete this asset category because stock items have been created using this asset category') .
			'<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('items referring to this asset category code'),'warn');

	} else {
		$sql = "SELECT COUNT(*) FROM salesglpostings WHERE stkcat='$SelectedCategory'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			prnMsg(_('Cannot delete this asset category because it is used by the sales') . ' - ' . _('GL posting interface') . '. ' . _('Delete any records in the Sales GL Interface set up using this asset category first'),'warn');
		} else {
			$sql = "SELECT COUNT(*) FROM cogsglpostings WHERE stkcat='$SelectedCategory'";
			$result = DB_query($sql,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]>0) {
				prnMsg(_('Cannot delete this asset category because it is used by the cost of sales') . ' - ' . _('GL posting interface') . '. ' . _('Delete any records in the Cost of Sales GL Interface set up using this asset category first'),'warn');
			} else {
				$sql="DELETE FROM stockcategory WHERE categoryid='$SelectedCategory'";
				$result = DB_query($sql,$db);
				prnMsg(_('The asset category') . ' ' . $SelectedCategory . ' ' . _('has been deleted') . ' !','success');
				unset ($SelectedCategory);
			}
		}
	} //end if asset category used in debtor transactions
}

if (!isset($SelectedCategory)) {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedCategory will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of asset categorys will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

	$sql = "SELECT * FROM stockcategory WHERE stocktype='".'A'."'";
	$result = DB_query($sql,$db);

	echo "<br><table border=1>\n";
	echo '<tr><th>' . _('Cat Code') . '</th>
            <th>' . _('Description') . '</th>
            <th>' . _('Type') . '</th>
            <th>' . _('Asset GL') . '</th>
            <th>' . _('P & L Depn GL') . '</th>
            <th>' . _('Sale of Asset account') . '</th>
            <th>' . _('BS Depn GL') . '</th></tr>';

	$k=0; //row colour counter

	while ($myrow = DB_fetch_row($result)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
		printf("<td>%s</td>
            		<td>%s</td>
            		<td>%s</td>
            		<td align=right>%s</td>
            		<td align=right>%s</td>
            		<td align=right>%s</td>
            		<td align=right>%s</td>
            		<td><a href=\"%sSelectedCategory=%s\">" . _('Edit') . "</td>
            		<td><a href=\"%sSelectedCategory=%s&delete=yes\" onclick=\"return confirm('" . _('Are you sure you wish to delete this asset category? Additional checks will be performed before actual deletion to ensure data integrity is not compromised.') . "');\">" . _('Delete') . "</td>
            		</tr>",
            		$myrow[0],
            		$myrow[1],
            		$myrow[2],
            		$myrow[3],
            		$myrow[4],
//            		$myrow[5],
            		$myrow[6],
            		$myrow[7],
            		$_SERVER['PHP_SELF'] . '?' . SID,
            		$myrow[0],
            		$_SERVER['PHP_SELF'] . '?' . SID,
            		$myrow[0]);
	}
	//END WHILE LIST LOOP
	echo '</table>';
}

//end of ifs and buts!

?>

<p>
<?php
if (isset($SelectedCategory)) {  ?>
	<div class='centre'><a href="<?php echo $_SERVER['PHP_SELF'] . '?' . SID;?>"><?php echo _('Show All Stock Categories'); ?></a></div>
<?php } ?>

<p>

<?php

if (! isset($_GET['delete'])) {

	echo '<form name="CategoryForm" method="post" action="' . $_SERVER['PHP_SELF'] . '?' . SID . '">';

	if (isset($SelectedCategory)) {
		//editing an existing asset category
		if (!isset($_POST['UpdateTypes'])) {
			$sql = "SELECT categoryid,
						stocktype,
						categorydescription,
						stockact,
						adjglact,
						purchpricevaract,
						materialuseagevarac,
						wipact
					FROM stockcategory
					WHERE categoryid='" . $SelectedCategory . "'";

			$result = DB_query($sql, $db);
			$myrow = DB_fetch_array($result);

			$_POST['CategoryID'] = $myrow['categoryid'];
			$_POST['StockType']  = $myrow['stocktype'];
			$_POST['CategoryDescription']  = $myrow['categorydescription'];
			$_POST['StockAct']  = $myrow['stockact'];
			$_POST['AdjGLAct']  = $myrow['adjglact'];
			$_POST['PurchPriceVarAct']  = $myrow['purchpricevaract'];
			$_POST['MaterialUseageVarAc']  = $myrow['materialuseagevarac'];
			$_POST['WIPAct']  = $myrow['wipact'];
		}
		echo '<input type=hidden name="SelectedCategory" value="' . $SelectedCategory . '">';
		echo '<input type=hidden name="CategoryID" value="' . $_POST['CategoryID'] . '">';
		echo '<table><tr><td>' . _('Asset Category Code') . ':</td><td>' . $_POST['CategoryID'] . '</td></tr>';

	} else { //end of if $SelectedCategory only do the else when a new record is being entered
		if (!isset($_POST['CategoryID'])) {
			$_POST['CategoryID'] = '';
		}
		echo '<table><tr><td>' . _('Asset Category Code') . ':</td>
                             <td><input type="Text" name="CategoryID" size=7 maxlength=6 value="' . $_POST['CategoryID'] . '"></td></tr>';
	}

	//SQL to poulate account selection boxes
	$sql = "SELECT accountcode,
                 accountname
                 FROM chartmaster,
                      accountgroups
                 WHERE chartmaster.group_=accountgroups.groupname and
                       accountgroups.pandl=0
                 ORDER BY accountcode";

	$BSAccountsResult = DB_query($sql,$db);

	$sql = "SELECT accountcode,
                 accountname
                 FROM chartmaster,
                      accountgroups
                 WHERE chartmaster.group_=accountgroups.groupname and
                       accountgroups.pandl!=0
                 ORDER BY accountcode";

	$PnLAccountsResult = DB_query($sql,$db);

	if (!isset($_POST['CategoryDescription'])) {
		$_POST['CategoryDescription'] = '';
	}
	
	echo '<tr><td>' . _('Category Description') . ':</td>
            <td><input type="Text" name="CategoryDescription" size=22 maxlength=20 value="' . $_POST['CategoryDescription'] . '"></td></tr>';


/*	echo '<tr><td>' . _('Stock Type') . ':</td>
            <td><select name="StockType" onChange="ReloadForm(CategoryForm.UpdateTypes)" >';
		if (isset($_POST['StockType']) and $_POST['StockType']=='F') {
			echo '<option selected value="F">' . _('Finished Goods');
		} else {
			echo '<option value="F">' . _('Finished Goods');
		}
		if (isset($_POST['StockType']) and $_POST['StockType']=='M') {
			echo '<option selected value="M">' . _('Raw Materials');
		} else {
			echo '<option value="M">' . _('Raw Materials');
		}
		if (isset($_POST['StockType']) and $_POST['StockType']=='D') {
			echo '<option selected value="D">' . _('Dummy Item - (No Movements)');
		} else {
			echo '<option value="D">' . _('Dummy Item - (No Movements)');
		}
		if (isset($_POST['StockType']) and $_POST['StockType']=='L') {
			echo '<option selected value="L">' . _('Labour');
		} else {
			echo '<option value="L">' . _('Labour');
		}

	echo '</select></td></tr>';
*/
	echo '<input type=hidden name="StockType" value="A">';
	echo '<input type="submit" name="UpdateTypes" style="visibility:hidden;width:1px" value="Not Seen">';
	echo '<tr><td>' . _('Fixed Asset GL Code');

	echo ':</td><td><select name="StockAct">';
	
	while ($myrow = DB_fetch_array($BSAccountsResult)){
	
		if (isset($_POST['StockAct']) and $myrow['accountcode']==$_POST['StockAct']) {
			echo '<option selected value=';
		} else {
			echo '<option value=';
		}
		echo $myrow['accountcode'] . '>' . $myrow['accountname'] . ' ('.$myrow['accountcode'].')';
	} //end while loop
	DB_data_seek($PnLAccountsResult,0);
	DB_data_seek($BSAccountsResult,0);
	echo '</select></td></tr>';

	echo '<tr><td>' . _('Balance Sheet Depreciation GL Code') . ':</td><td><select name="WIPAct">';

	while ($myrow = DB_fetch_array($BSAccountsResult)) {
	
		if (isset($_POST['WIPAct']) and $myrow['accountcode']==$_POST['WIPAct']) {
			echo '<option selected value=';
		} else {
			echo '<option value=';
		}
		echo $myrow['accountcode'] . '>' . $myrow['accountname'] . ' ('.$myrow['accountcode'].')';

	} //end while loop
	echo '</select></td></tr>';
	DB_data_seek($BSAccountsResult,0);

	echo '<tr><td>' . _('Profit and Loss Depreciation GL Code') . ':</td>
            <td><select name="AdjGLAct">';

	while ($myrow = DB_fetch_array($PnLAccountsResult)) {
		if (isset($_POST['AdjGLAct']) and $myrow['accountcode']==$_POST['AdjGLAct']) {
			echo '<option selected value=';
		} else {
			echo '<option value=';
		}
		echo $myrow['accountcode'] . '>' . $myrow['accountname'] . ' ('.$myrow['accountcode'].')';

	} //end while loop
	DB_data_seek($PnLAccountsResult,0);
	echo '</select></td></tr>';
	
/*	echo '<tr><td>' . _('Price Variance GL Code') . ':</td>
            <td><select name="PurchPriceVarAct">';

	while ($myrow = DB_fetch_array($PnLAccountsResult)) {
		if (isset($_POST['PurchPriceVarAct']) and $myrow['accountcode']==$_POST['PurchPriceVarAct']) {
			echo '<option selected value=';
		} else {
			echo '<option value=';
		}
		echo $myrow['accountcode'] . '>' . $myrow['accountname'] . ' ('.$myrow['accountcode'].')';

	} //end while loop
	DB_data_seek($PnLAccountsResult,0);

	echo '</select></td></tr><tr><td>'; */
	echo '<tr><td>';
	if (isset($_POST['StockType']) and $_POST['StockType']=='L') {
		echo  _('Other Capitalized Goods P/L Code');
	} else {
		echo  _('Sale of Asset account');
	}
	echo ':</td><td><select name="MaterialUseageVarAc">';

	while ($myrow = DB_fetch_array($PnLAccountsResult)) {
		if (isset($_POST['MaterialUseageVarAc']) and $myrow['accountcode']==$_POST['MaterialUseageVarAc']) {
			echo '<option selected value=';
		} else {
			echo '<option value=';
		}
		echo $myrow['accountcode'] . '>' . $myrow['accountname'] . ' ('.$myrow['accountcode'].')';

	} //end while loop
	DB_free_result($PnLAccountsResult);
	echo '</select></td></tr></table><br>';
	
	$sql='SELECT COUNT(categoryid)
			FROM stockcatproperties
			WHERE categoryid="dpntyp"';
	$result=DB_query($sql,$db);
	$row=DB_fetch_array($result);
	if ($row['categoryid']==0) {
	}
	echo '<table>';


	echo '<div class="centre"><input type="Submit" name="submit" value="' . _('Enter Information') . '"></div>';

	echo '</form>';

} //end if record deleted no point displaying form to add record


include('includes/footer.inc');
?>
