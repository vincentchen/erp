<?php
/* $Revision: 1.11 $ */
$PageSecurity = 3;

include('includes/session.inc');
$title = _('Customer Branches');
include('includes/header.inc');

if (isset($_GET['DebtorNo'])) {
	$DebtorNo = strtoupper($_GET['DebtorNo']);
}elseif (isset($_POST['DebtorNo'])){
	$DebtorNo = strtoupper($_POST['DebtorNo']);
}

if (!isset($DebtorNo)) {
	prnMsg(_('This page must be called with the debtor code of the customer for whom you wish to edit the branches for').'. <BR>'._('When the pages is called from within the system this will always be the case').' <BR>'._('Select a customer first then select the link to add/edit/delete branches'),'warn');
	include('includes/footer.inc');
	exit;
}


if (isset($_GET['SelectedBranch'])){
	$SelectedBranch = strtoupper($_GET['SelectedBranch']);
}elseif (isset($_POST['SelectedBranch'])){
	$SelectedBranch = strtoupper($_POST['SelectedBranch']);
}

?>

<?php
if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	$_POST['BranchCode'] = strtoupper($_POST['BranchCode']);

	if (strstr($_POST['BranchCode'],"'") OR strstr($_POST['BranchCode'],'"') OR strstr($_POST['BranchCode'],'&')) {
		$InputError = 1;
		prnMsg(_('The Branch code cannot contain any of the following characters')." -  & \'",'error');
	} elseif (strlen($_POST['BranchCode'])==0) {
		$InputError = 1;
		prnMsg(_('The Branch code must be at least one character long'),'error');
	} elseif (!Is_integer((int) $_POST['FwdDate'])) {
		$InputError = 1;
		prnMsg(_('The date after which invoices are charged to the following month is expected to be a number and a recognised number has not been entered'),'error');
	} elseif ($_POST['FwdDate'] >30) {
		$InputError = 1;
		prnMsg(_('The date (in the month) after which invoices are charged to the following month should be a number less than 31'),'error');
	} elseif (!Is_integer((int) $_POST['EstDeliveryDays'])) {
		$InputError = 1;
		prnMsg(_('The estimated delivery days is expected to be a number and a recognised number has not been entered'),'error');
	} elseif ($_POST['EstDeliveryDays'] >60) {
		$InputError = 1;
		prnMsg(_('The estimated delivery days should be a number of days less than 60') . '. ' . _('A package can be delivered by seafreight anywhere in the world normally in less than 60 days'),'error');
	}

	if (!isset($_POST['EstDeliveryDays']) OR !is_numeric($_POST['EstDeliveryDays'])){
		$_POST['EstDeliveryDays']=1;
	}
	if (!isset($_POST['FwdDate']) OR !is_numeric($_POST['FwdDate'])){
		$_POST['FwdDate']=0;
	}


	if (isset($SelectedBranch) AND $InputError !=1) {

		/*SelectedBranch could also exist if submit had not been clicked this code would not run in this case cos submit is false of course see the 	delete code below*/

		$sql = "UPDATE CustBranch SET BrName = '" . $_POST['BrName'] . "',
						BrAddress1 = '" . $_POST['BrAddress1'] . "',
						BrAddress2 = '" . $_POST['BrAddress2'] . "',
						BrAddress3 = '" . $_POST['BrAddress3'] . "',
						BrAddress4 = '" . $_POST['BrAddress4'] . "',
						PhoneNo='" . $_POST['PhoneNo'] . "',
						FaxNo='" . $_POST['FaxNo'] . "',
						FwdDate= " . $_POST['FwdDate'] . ",
						ContactName='" . $_POST['ContactName'] . "',
						Salesman= '" . $_POST['Salesman'] . "',
						Area='" . $_POST['Area'] . "',
						EstDeliveryDays =" . $_POST['EstDeliveryDays'] . ",
						Email='" . $_POST['Email'] . "',
						TaxAuthority=" . $_POST['TaxAuthority'] . ",
						DefaultLocation='" . $_POST['DefaultLocation'] . "',
						BrPostAddr1 = '" . $_POST['BrPostAddr1'] . "',
						BrPostAddr2 = '" . $_POST['BrPostAddr2'] . "',
						BrPostAddr3 = '" . $_POST['BrPostAddr3'] . "',
						BrPostAddr4 = '" . $_POST['BrPostAddr4'] . "',
						DisableTrans=" . $_POST['DisableTrans'] . ",
						DefaultShipVia=" . $_POST['DefaultShipVia'] . ",
						CustBranchCode='" . $_POST['CustBranchCode'] ."'
					WHERE BranchCode = '$SelectedBranch' AND DebtorNo='$DebtorNo'";

		$msg = $_POST['BrName'] . ' '._('branch  has been updated.');

	} elseif ($InputError !=1) {

	/*Selected branch is null cos no item selected on first time round so must be adding a	record must be submitting new entries in the new Customer Branches form */

		$sql = "INSERT INTO CustBranch (BranchCode,
						DebtorNo,
						BrName,
						BrAddress1,
						BrAddress2,
						BrAddress3,
						BrAddress4,
						EstDeliveryDays,
						FwdDate,
						Salesman,
						PhoneNo,
						FaxNo,
						ContactName,
						Area,
						Email,
						TaxAuthority,
						DefaultLocation,
						BrPostAddr1,
						BrPostAddr2,
						BrPostAddr3,
						BrPostAddr4,
						DisableTrans,
						DefaultShipVia,
						CustBranchCode)
				VALUES ('" . $_POST['BranchCode'] . "',
					'" . $DebtorNo . "',
					'" . $_POST['BrName'] . "',
					'" . $_POST['BrAddress1'] . "',
					'" . $_POST['BrAddress2'] . "',
					'" . $_POST['BrAddress3'] . "',
					'" . $_POST['BrAddress4'] . "',
					" . $_POST['EstDeliveryDays'] . ",
					" . $_POST['FwdDate'] . ",
					'" . $_POST['Salesman'] . "',
					'" . $_POST['PhoneNo'] . "',
					'" . $_POST['FaxNo'] . "',
					'" . $_POST['ContactName'] . "',
					'" . $_POST['Area'] . "',
					'" . $_POST['Email'] . "',
					" . $_POST['TaxAuthority'] . ",
					'" . $_POST['DefaultLocation'] . "',
					'" . $_POST['BrPostAddr1'] . "',
					'" . $_POST['BrPostAddr2'] . "',
					'" . $_POST['BrPostAddr3'] . "',
					'" . $_POST['BrPostAddr4'] . "',
					" . $_POST['DisableTrans'] . ",
					" . $_POST['DefaultShipVia'] . ",
					'" . $_POST['CustBranchCode'] ."')";

		$msg = _('Customer branch').' '. $_POST['BrName'] . ' '._('has been added');
	}
	//run the SQL from either of the above possibilites

	$ErrMsg = _('The branch record could not be inserted or updated because');
	$result = DB_query($sql,$db, $ErrMsg);

	if (DB_error_no($db) ==0) {
		prnMsg($msg,'success');
		unset($_POST['BranchCode']);
		unset($_POST['BrName']);
		unset($_POST['BrAddress1']);
		unset($_POST['BrAddress2']);
		unset($_POST['BrAddress3']);
		unset($_POST['BrAddress4']);
		unset($_POST['EstDeliveryDays']);
		unset($_POST['FwdDate']);
		unset($_POST['Salesman']);
		unset($_POST['PhoneNo']);
		unset($_POST['FaxNo']);
		unset($_POST['ContactName']);
		unset($_POST['Area']);
		unset($_POST['Email']);
		unset($_POST['TaxAuthority']);
		unset($_POST['DefaultLocation']);
		unset($_POST['DisableTrans']);
		unset($_POST['BrPostAddr1']);
		unset($_POST['BrPostAddr2']);
		unset($_POST['BrPostAddr3']);
		unset($_POST['BrPostAddr4']);
		unset($_POST['DefaultShipVia']);
		unset($_POST['CustBranchCode']);
		unset($SelectedBranch);
	}


} elseif ($_GET['delete']) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorTrans'

	$sql= "SELECT COUNT(*) FROM DebtorTrans WHERE DebtorTrans.BranchCode='$SelectedBranch' AND DebtorNo = '$DebtorNo'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		prnMsg(_('Cannot delete this branch because customer transactions have been created to this branch') . '<BR>' .
			 _('There are').' ' . $myrow[0] . ' '._('transactions with this Branch Code'),'error');

	} else {
		$sql= "SELECT COUNT(*) FROM SalesAnalysis WHERE SalesAnalysis.CustBranch='$SelectedBranch' AND Cust = '$DebtorNo'";

		$result = DB_query($sql,$db);

		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			prnMsg(_('Cannot delete this branch because sales analysis records exist for it'),'error');
			echo '<BR>'._('There are').' ' . $myrow[0] . ' '._('sales analysis records with this Branch Code/customer');

		} else {

			$sql= "SELECT COUNT(*) FROM SalesOrders WHERE SalesOrders.BranchCode='$SelectedBranch' AND SalesOrders.DebtorNo = '$DebtorNo'";
			$result = DB_query($sql,$db);

			$myrow = DB_fetch_row($result);
			if ($myrow[0]>0) {
				prnMsg(_('Cannot delete this branch because sales orders exist for it') . '. ' . _('Purge old sales orders first'),'warn');
				echo '<BR>'._('There are').' ' . $myrow[0] . ' '._('sales orders for this Branch/customer');
			} else {
				// Sherifoz 22.06.03 Check if there are any users that refer to this branch code
				$sql= "SELECT COUNT(*) FROM WWW_Users WHERE WWW_Users.BranchCode='$SelectedBranch' AND WWW_Users.CustomerID = '$DebtorNo'";

				$result = DB_query($sql,$db);
				$myrow = DB_fetch_row($result);

				if ($myrow[0]>0) {
					prnMsg(_('Cannot delete this branch because users exist that refer to it') . '. ' . _('Purge old users first'),'warn');
				    echo '<BR>'._('There are').' ' . $myrow[0] . ' '._('users referring to this Branch/customer');
				} else {

					$sql="DELETE FROM CustBranch WHERE BranchCode='" . $SelectedBranch . "' AND DebtorNo='" . $DebtorNo . "'";
					$ErrMsg = _('The branch record could not be deleted') . ' - ' . _('the SQL server returned the following message');
    					$result = DB_query($sql,$db,$ErrMsg);
					if (DB_error_no($db)==0){
						prnMsg(_('Branch Deleted'),'success');
					}
				}
			}
		}
	} //end ifs to test if the branch can be deleted

}

if (!isset($SelectedBranch)){

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedBranch will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters then none of the above are true and the list of branches will be displayed with links to delete or edit each. These will call the same page again and allow update/input or deletion of the records*/

	$sql = "SELECT DebtorsMaster.Name,
			CustBranch.BranchCode,
			BrName,
			Salesman.SalesmanName,
			Areas.AreaDescription,
			ContactName,
			PhoneNo,
			FaxNo,
			Email,
			TaxAuthority,
			CustBranch.BranchCode
		FROM CustBranch,
			DebtorsMaster,
			Areas,
			Salesman
		WHERE CustBranch.DebtorNo=DebtorsMaster.DebtorNo
		AND CustBranch.Area=Areas.AreaCode
		AND CustBranch.Salesman=Salesman.SalesmanCode
		AND CustBranch.DebtorNo = '$DebtorNo'";

	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);

	if ($myrow) {
		echo '<BR><B>'._('Branches Defined for'). ' '. $DebtorNo . ' - ' . $myrow[0] . '</B>';
		echo '<table border=1>';

		echo "<tr><td class='tableheader'>"._('Code')."</td>
			<td class='tableheader'>"._('Name')."</td>
			<td class='tableheader'>"._('Contact')."</td>
			<td class='tableheader'>"._('Salesman')."</td>
			<td class='tableheader'>"._('Area')."</td>
			<td class='tableheader'>"._('Phone No')."</td>
			<td class='tableheader'>"._('Fax No')."</td>
			<td class='tableheader'>"._('Email')."</td>
			<td class='tableheader'>"._('Tax Auth')."</td></tr>";

		do {
			printf("<tr><td><font size=2>%s</td>
					<td><font size=2>%s</td>
					<td><font size=2>%s</font></td>
					<td><font size=2>%s</font></td>
					<td><font size=2>%s</font></td>
					<td><font size=2>%s</font></td>
					<td><font size=2>%s</font></td>
					<td><font size=2><a href=\"Mailto:%s\">%s</a></font></td>
					<td><font size=2>%s</font></td>
					<td><font size=2><a href=\"%s?DebtorNo=%s&SelectedBranch=%s\">%s</font></td>
					<td><font size=2><a href=\"%s?DebtorNo=%s&SelectedBranch=%s&delete=yes\">%s</font></td></tr>",
					$myrow[10],
					$myrow[2],
					$myrow[5],
					$myrow[3],
					$myrow[4],
					$myrow[6],
					$myrow[7],
					$myrow[8],
					$myrow[8],
					$myrow[9],
					$_SERVER['PHP_SELF'],
					$DebtorNo,
					$myrow[1],
					_('Edit'),
					$_SERVER['PHP_SELF'],
					$DebtorNo,
					$myrow[1],
					_('Delete'));

		} while ($myrow = DB_fetch_row($result));
		//END WHILE LIST LOOP
		echo '</table>';
	} else {
		$sql = "SELECT DebtorsMaster.Name,
				Address1,
				Address2,
				Address3,
				Address4
			FROM DebtorsMaster
			WHERE DebtorNo = '$DebtorNo'";

		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		echo '<B>'._('Branches Defined for').' - '.$myrow[0].'</B>';
		$_POST['BranchCode'] = substr($DebtorNo,0,10);
		$_POST['BrName'] = $myrow[0];
		$_POST['BrAddress1'] = $myrow[1];
		$_POST['BrAddress2'] = $myrow[2];
		$_POST['BrAddress3'] = $myrow[3];
		$_POST['BrAddress4'] = $myrow[4];
		unset($myrow);
	}


}

//end of ifs and buts!

if (isset($SelectedBranch)) {
	echo '<Center><a href=' . $_SERVER['PHP_SELF'] . '?' . SID . 'DebtorNo=' . $DebtorNo. '>' . _('Show all branches defined for'). ' '. $DebtorNo . '</a></Center>';
}
echo '<BR>';

if (! isset($_GET['delete'])) {

	echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] .'?' . SID . '>';

	if (isset($SelectedBranch)) {
		//editing an existing branch

		$sql = "SELECT BranchCode,
				BrName,
				BrAddress1,
				BrAddress2,
				BrAddress3,
				BrAddress4,
				EstDeliveryDays,
				FwdDate,
				Salesman,
				Area,
				PhoneNo,
				FaxNo,
				ContactName,
				Email,
				TaxAuthority,
				DefaultLocation,
				BrPostAddr1,
				BrPostAddr2,
				BrPostAddr3,
				BrPostAddr4,
				DisableTrans,
				DefaultShipVia,
				CustBranchCode
			FROM CustBranch
			WHERE BranchCode='$SelectedBranch'
			AND DebtorNo='$DebtorNo'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['BranchCode'] = $myrow['BranchCode'];
		$_POST['BrName']  = $myrow['BrName'];
		$_POST['BrAddress1']  = $myrow['BrAddress1'];
		$_POST['BrAddress2']  = $myrow['BrAddress2'];
		$_POST['BrAddress3']  = $myrow['BrAddress3'];
		$_POST['BrAddress4']  = $myrow['BrAddress4'];
		$_POST['BrPostAddr1']  = $myrow['BrPostAddr1'];
		$_POST['BrPostAddr2']  = $myrow['BrPostAddr2'];
		$_POST['BrPostAddr3']  = $myrow['BrPostAddr3'];
		$_POST['BrPostAddr4']  = $myrow['BrPostAddr4'];
		$_POST['EstDeliveryDays']  = $myrow['EstDeliveryDays'];
		$_POST['FwdDate'] =$myrow['FwdDate'];
		$_POST['ContactName'] = $myrow['ContactName'];
		$_POST['Salesman'] =$myrow['Salesman'];
		$_POST['Area'] =$myrow['Area'];
		$_POST['PhoneNo'] =$myrow['PhoneNo'];
		$_POST['FaxNo'] =$myrow['FaxNo'];
		$_POST['Email'] =$myrow['Email'];
		$_POST['TaxAuthority'] = $myrow['TaxAuthority'];
		$_POST['DisableTrans'] = $myrow['DisableTrans'];
		$_POST['DefaultLocation'] = $myrow['DefaultLocation'];
		$_POST['DefaultShipVia'] = $myrow['DefaultShipVia'];
		$_POST['CustBranchCode'] = $myrow['CustBranchCode'];

		echo "<INPUT TYPE=HIDDEN NAME='SelectedBranch' VALUE=" . $SelectedBranch . '>';
		echo "<INPUT TYPE=HIDDEN NAME='BranchCode'  VALUE=" . $_POST['BranchCode'] . '>';
		echo "<CENTER><TABLE> <TR><TD>"._('Branch Code').':</TD><TD>';
		echo $_POST['BranchCode'] . '</TD></TR>';

	} else { //end of if $SelectedBranch only do the else when a new record is being entered

		echo '<CENTER><TABLE><TR><TD>'._('Branch Code').":</TD>
				<TD><input type='Text' name='BranchCode' SIZE=12 MAXLENGTH=10 value=" . $_POST['BranchCode'] . '></TD></TR>';
	}

	//SQL to poulate account selection boxes
	$sql = "SELECT SalesmanName, SalesmanCode FROM Salesman";

	$result = DB_query($sql,$db);

	if (DB_num_rows($result)==0){
		echo '</TABLE>';
		prnMsg(_('There are no sales people defined as yet') . ' - ' . _('customer branches must be allocated to a sales person') . '. ' . _('Please use the link below to define at least one sales person'),'error');
		echo "<BR><A HREF='$rootpath/SalesPeople.php?" . SID . "'>"._('Define Sales People').'</A>';
		include('includes/footer.inc');
		exit;
	}

	echo '<input type=HIDDEN name="DebtorNo" value="'. $DebtorNo . '">';


	echo '<TR><TD>'._('Branch Name').':</TD>';
	echo '<TD><input type="Text" name="BrName" SIZE=41 MAXLENGTH=40 value="'. $_POST['BrName'].'"></TD></TR>';
	echo '<TR><TD>'._('Contact').':</TD>';
	echo '<TD><input type="Text" name="ContactName" SIZE=41 MAXLENGTH=40 value="'. $_POST['ContactName'].'"></TD></TR>';
	echo '<TR><TD>'._('Street Address 1').':</TD>';
	echo '<TD><input type="Text" name="BrAddress1" SIZE=41 MAXLENGTH=40 value="'. $_POST['BrAddress1'].'"></TD></TR>';
	echo '<TR><TD>'._('Street Address 2').':</TD>';
	echo '<TD><input type="Text" name="BrAddress2" SIZE=41 MAXLENGTH=40 value="'. $_POST['BrAddress2'].'"></TD></TR>';
	echo '<TR><TD>'._('Street Address 3').':</TD>';
	echo '<TD><input type="Text" name="BrAddress3" SIZE=41 MAXLENGTH=40 value="'. $_POST['BrAddress3'].'"></TD></TR>';
	echo '<TR><TD>'._('Street Address 4').':</TD>';
	echo '<TD><input type="Text" name="BrAddress4" SIZE=31 MAXLENGTH=30 value="'. $_POST['BrAddress4'].'"></TD></TR>';

	echo '<TR><TD>'._('Delivery Days').':</TD>';
	echo '<TD><input type="Text" name="EstDeliveryDays" SIZE=4 MAXLENGTH=2 value='. $_POST['EstDeliveryDays'].'></TD></TR>';
	echo '<TR><TD>'._('Forward Date After (day in month)').':</TD>';
	echo '<TD><input type="Text" name="FwdDate" SIZE=4 MAXLENGTH=2 value='. $_POST['FwdDate'].'></TD></TR>';

	echo '<TR><TD>'._('Salesperson').':</TD>';
	echo '<TD><SELECT name="Salesman">';

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['SalesmanCode']==$_POST['Salesman']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['SalesmanCode'] . '>' . $myrow['SalesmanName'];

	} //end while loop

	echo '</SELECT></TD></TR>';

	DB_data_seek($result,0);

	$sql = 'SELECT AreaCode, AreaDescription FROM Areas';
	$result = DB_query($sql,$db);
	if (DB_num_rows($result)==0){
		echo '</TABLE>';
		prnMsg(_('There are no areas defined as yet') . ' - ' . _('customer branches must be allocated to an area') . '. ' . _('Please use the link below to define at least one sales area'),'error');
		echo "<BR><A HREF='$rootpath/Areas.php?" . SID . "'>"._('Define Sales Areas').'</A>';
		include('includes/footer.inc');
		exit;
	}

	echo '<TR><TD>'._('Sales Area').':</TD>';
	echo '<TD><SELECT name="Area">';
	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['AreaCode']==$_POST['Area']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['AreaCode'] . '>' . $myrow['AreaDescription'];

	} //end while loop


	echo '</SELECT></TD></TR>';
	DB_data_seek($result,0);

	$sql = 'SELECT LocCode, LocationName FROM Locations';
	$result = DB_query($sql,$db);

	if (DB_num_rows($result)==0){
		echo '</TABLE>';
		prnMsg(_('There are no stock locations defined as yet') . ' - ' . _('customer branches must refer to a default location where stock is normally drawn from') . '. ' . _('Please use the link below to define at least one stock location'),'error');
		echo "<BR><A HREF='$rootpath/Locations.php?" . SID . "'>"._('Define Stock Locations').'</A>';
		include('includes/footer.inc');
		exit;
	}

	echo '<TR><TD>'._('Draw Stock From').':</TD>';
	echo '<TD><SELECT name="DefaultLocation">';

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['LocCode']==$_POST['DefaultLocation']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['LocCode'] . '>' . $myrow['LocationName'];

	} //end while loop

	echo '</SELECT></TD></TR>';
	echo '<TR><TD>'._('Phone Number').':</TD>';
	echo '<TD><input type="Text" name="PhoneNo" SIZE=22 MAXLENGTH=20 value="'. $_POST['PhoneNo'].'"></TD></TR>';

	echo '<TR><TD>'._('Fax Number').':</TD>';
	echo '<TD><input type="Text" name="FaxNo" SIZE=22 MAXLENGTH=20 value="'. $_POST['FaxNo'].'"></TD></TR>';


	echo '<TR><TD><a href="Mailto:'. $_POST['Email'].'">'._('Email').':</a></TD>';
	echo '<TD><input type="Text" name="Email" SIZE=56 MAXLENGTH=55 value="'. $_POST['Email'].'"></TD></TR>';

	echo '<TR><TD>'._('Tax Authority').':</TD>';
	echo '<TD><SELECT name="TaxAuthority">';

	DB_data_seek($result,0);

	$sql = 'SELECT TaxID, Description FROM TaxAuthorities';
	$result = DB_query($sql,$db);

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['TaxID']==$_POST['TaxAuthority']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['TaxID'] . '>' . $myrow['Description'];

	} //end while loop

	echo '</SELECT></TD></TR>';
	echo '<TR><TD>'._('Disable transactions on this branch').":</TD><TD><SELECT NAME='DisableTrans'>";
	if ($_POST['DisableTrans']==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Enabled');
		echo '<OPTION VALUE=1>' . _('Disabled');
	} else {
		echo '<OPTION SELECTED VALUE=1>' . _('Disabled');
		echo '<OPTION VALUE=0>' . _('Enabled');
	}

	echo '	</SELECT></TD></TR>';

	echo '<TR><TD>'._('Default freight company').":</TD><TD><SELECT name='DefaultShipVia'>";
	$SQL = 'SELECT Shipper_ID, ShipperName FROM Shippers';
	$ShipperResults = DB_query($SQL,$db);
	while ($myrow=DB_fetch_array($ShipperResults)){
		if ($myrow['Shipper_ID']==$_POST['DefaultShipVia']){
			echo '<OPTION SELECTED VALUE=' . $myrow['Shipper_ID'] . '>' . $myrow['ShipperName'];
		}else {
			echo '<OPTION VALUE=' . $myrow['Shipper_ID'] . '>' . $myrow['ShipperName'];
		}
	}

	echo '</SELECT></TD></TR>';

	echo '<TR><TD>'._('Postal Address 1').':</TD>';
	echo '<TD><input type="Text" name="BrPostAddr1" SIZE=41 MAXLENGTH=40 value="'. $_POST['BrPostAddr1'].'"></TD></TR>';
	echo '<TR><TD>'._('Postal Address 2').':</TD>';
	echo '<TD><input type="Text" name="BrPostAddr2" SIZE=41 MAXLENGTH=40 value="'. $_POST['BrPostAddr2'].'"></TD></TR>';
	echo '<TR><TD>'._('Postal Address 3').':</TD>';
	echo '<TD><input type="Text" name="BrPostAddr3" SIZE=31 MAXLENGTH=30 value="'. $_POST['BrPostAddr3'].'"></TD></TR>';
	echo '<TR><TD>'._('Postal Address 4').':</TD>';
	echo '<TD><input type="Text" name="BrPostAddr4" SIZE=21 MAXLENGTH=20 value="'. $_POST['BrPostAddr4'].'"></TD></TR>';
	echo '<TR><TD>'._('Customers Internal Branch Code (EDI)').':</TD>';
	echo '<TD><input type="Text" name="CustBranchCode" SIZE=31 MAXLENGTH=30 value="'. $_POST['CustBranchCode'].'"></TD></TR>';
	echo '</TABLE>';

	echo '<CENTER><input type="Submit" name="submit" value='._('Enter Information').'>';

	echo '</FORM>';

} //end if record deleted no point displaying form to add record

include('includes/footer.inc');
?>
