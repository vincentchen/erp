<?php
/* $Revision: 1.5 $ */
$PageSecurity=15;

include('includes/session.inc');
$title = _('User Maintenance');
include('includes/header.inc');
include('includes/DateFunctions.inc');


if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (strlen($_POST['UserID'])<4){
		$InputError = 1;
		prnMsg(_('The user ID entered must be at least 4 characters long'),'error');
	} elseif (strlen($_POST['Password'])<5){
		$InputError = 1;
		prnMsg(_('The password entered must be at least 5 characters long'),'error');
	} elseif (strstr($_POST['Password'],$_POST['UserID'])!= False){
		$InputError = 1;
		prnMsg(_('The password cannot contain the user id'),'error');
	} elseif ((strlen($_POST['Cust'])>0) AND (strlen($_POST['BranchCode'])==0)) {
		$InputError = 1;
		prnMsg(_('If you enter a Customer Code you must also enter a Branch Code valid for this Customer'),'error');
	}

	if ((strlen($_POST['BranchCode'])>0) AND ($InputError !=1)) {
		// check that the entered branch is valid for the customer code
		$sql = "SELECT CustBranch.DebtorNo
				FROM CustBranch
				WHERE CustBranch.DebtorNo='" . $_POST['Cust'] . "'
				AND CustBranch.BranchCode='" . $_POST['BranchCode'] . "'";

		$ErrMsg = _('The check on validity of the customer code and branch failed  because');
		$DbgMsg = _('The SQL that was used to check the customer code and branch was');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

		if (DB_num_rows($result)==0){
			prnMsg(_('The entered Branch Code is not valid for the entered Customer Code'),'error');
			$InputError = 1;
		}
	}

	/* Make a comma seperated list of modules allowed ready to update the database*/
	$i=0;
	$ModulesAllowed = '';
	while ($i < count($ModuleList)){
		$FormVbl = "Module_" . $i;
		$ModulesAllowed .= $_POST[($FormVbl)] . ',';
		$i++;
	}
	$_POST['ModulesAllowed']= $ModulesAllowed;


	if ($SelectedUser AND $InputError !=1) {

/*SelectedUser could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/

		if (!isset($_POST['Cust']) OR $_POST['Cust']==NULL OR $_POST['Cust']==''){
			$_POST['Cust']='';
			$_POST['BranchCode']='';
		}

		$sql = "UPDATE WWW_Users SET RealName='" . $_POST['RealName'] . "',
						CustomerID='" . $_POST['Cust'] ."',
						Phone='" . $_POST['Phone'] ."',
						Email='" . $_POST['Email'] ."',
						Password='" . $_POST['Password'] . "',
						BranchCode='" . $_POST['BranchCode'] . "',
						PageSize='" . $_POST['PageSize'] . "',
						FullAccess=" . $_POST['Access'] . ",
						DefaultLocation='" . $_POST['DefaultLocation'] ."',
						ModulesAllowed='" . $ModulesAllowed . "',
						Blocked=" . $_POST['Blocked'] . "
					WHERE UserID = '$SelectedUser'";

		$msg = _('The selected user record has been updated');
	} elseif ($InputError !=1) {

		$sql = "INSERT INTO WWW_Users (UserID,
						RealName,
						CustomerID,
						BranchCode,
						Password,
						Phone,
						Email,
						PageSize,
						FullAccess,
						DefaultLocation,
						ModulesAllowed)
					VALUES ('" . $_POST['UserID'] . "',
						'" . $_POST['RealName'] ."',
						'" . $_POST['Cust'] ."',
						'" . $_POST['BranchCode'] ."',
						'" . $_POST['Password'] ."',
						'" . $_POST['Phone'] . "',
						'" . $_POST['Email'] ."',
						'" . $_POST['PageSize'] ."',
						" . $_POST['Access'] . ",
						'" . $_POST['DefaultLocation'] ."',
						'" . $ModulesAllowed . "')";
		$msg = _('A new user record has been inserted');
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

		unset($_POST['UserID']);
		unset($_POST['RealName']);
		unset($_POST['Cust']);
		unset($_POST['BranchCode']);
		unset($_POST['Phone']);
		unset($_POST['Email']);
		unset($_POST['Password']);
		unset($_POST['PageSize']);
		unset($_POST['Access']);
		unset($_POST['DefaultLocation']);
		unset($_POST['ModulesAllowed']);
		unset($_POST['Blocked']);
		unset($SelectedUser);
	}

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$sql="DELETE FROM WWW_Users WHERE UserID='$SelectedUser'";
	$ErrMsg = _('The User could not be deleted because');;
	$result = DB_query($sql,$db,$ErrMsg);

	prnMsg(_('User Deleted'),'info');
	unset($SelectedUser);
}

if (!isset($SelectedUser)) {

/* If its the first time the page has been displayed with no parameters then none of the above are true and the list of Users will be displayed with links to delete or edit each. These will call the same page again and allow update/input or deletion of the records*/

	$sql = "SELECT UserID,
			RealName,
			Phone,
			Email,
			CustomerID,
			BranchCode,
			LastVisitDate,
			FullAccess,
			PageSize
		FROM WWW_Users";
	$result = DB_query($sql,$db);

	echo '<CENTER><table border=1>';
	echo "<tr><td class='tableheader'>" . _('User Login') . "</td>
		<td class='tableheader'>" . _('Full Name') . "</td>
		<td class='tableheader'>" . _('Telephone') . "</td>
		<td class='tableheader'>" . _('Email') . "</td>
		<td class='tableheader'>" . _('Customer Code') . "</td>
		<td class='tableheader'>" . _('Branch Code') . "</td>
		<td class='tableheader'>" . _('Last Visit') . "</td>
		<td class='tableheader'>" . _('Security Group') ."</td>
		<td class='tableheader'>" . _('Report Size') ."</td>
	</tr>";

	$k=0; //row colour counter

	while ($myrow = DB_fetch_row($result)) {
		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}

		$LastVisitDate = ConvertSQLDate($myrow[6]);

		/*The SecurityHeadings array is defined in config.php */

		printf("<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td><a href=\"%sSelectedUser=%s\">" . _('Edit') . "</a></td>
			<td><a href=\"%sSelectedUser=%s&delete=1\">" . _('Delete') . "</a></td>
			</tr>",
			$myrow[0],
			$myrow[1],
			$myrow[2],
			$myrow[3],
			$myrow[4],
			$myrow[5],
			$LastVisitDate,
			$SecurityHeadings[($myrow[7])],
			$myrow[8],
			$_SERVER['PHP_SELF']  . "?" . SID,
			$myrow[0],
			$_SERVER['PHP_SELF'] . "?" . SID,
			$myrow[0]);

	} //END WHILE LIST LOOP
	echo '</TABLE></CENTER>';
} //end of ifs and buts!


if (isset($SelectedUser)) {
	echo "<Center><a href='" . $_SERVER['PHP_SELF'] ."?" . SID . "'>" . _('Review Existing Users') . '</a></Center>';
}

echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . "?" . SID . ">";

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT UserID,
			RealName,
			Phone,
			Email,
			CustomerID,
			Password,
			BranchCode,
			PageSize,
			FullAccess,
			DefaultLocation,
			ModulesAllowed,
			Blocked
		FROM WWW_Users
		WHERE UserID='" . $SelectedUser . "'";

	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);

	$_POST['UserID'] = $myrow['UserID'];
	$_POST['RealName'] = $myrow['RealName'];
	$_POST['Phone'] = $myrow['Phone'];
	$_POST['Email'] = $myrow['Email'];
	$_POST['Cust']	= $myrow['CustomerID'];
	$_POST['Password'] = $myrow['Password'];
	$_POST['BranchCode']  = $myrow['BranchCode'];
	$_POST['PageSize'] = $myrow['PageSize'];
	$_POST['Access'] = $myrow['FullAccess'];
	$_POST['DefaultLocation'] = $myrow['DefaultLocation'];
	$_POST['ModulesAllowed'] = $myrow['ModulesAllowed'];
	$_POST['Blocked'] = $myrow['Blocked'];

	echo "<INPUT TYPE=HIDDEN NAME='SelectedUser' VALUE='" . $SelectedUser . "'>";
	echo "<INPUT TYPE=HIDDEN NAME='UserID' VALUE='" . $_POST['UserID'] . "'>";
	echo "<INPUT TYPE=HIDDEN NAME='ModulesAllowed' VALUE='" . $_POST['ModulesAllowed'] . "'>";

	echo '<CENTER><TABLE> <TR><TD>' . _('User code') . ':</TD><TD>';
	echo $_POST['UserID'] . '</TD></TR>';

} else { //end of if $SelectedUser only do the else when a new record is being entered

	echo '<CENTER><TABLE><TR><TD>' . _('User code') . ":</TD><TD><input type='Text' name='UserID' SIZE=22 MAXLENGTH=20 Value='" . $_POST['UserID'] . "'></TD></TR>";
}

echo '<TR><TD>' . _('Password') . ":</TD>
	<TD><INPUT TYPE='Password' name='Password' SIZE=22 MAXLENGTH=20 VALUE='" . $_POST['Password'] . "'></TR>";
echo '<TR><TD>' . _('User Name') . ":</TD>
	<TD><INPUT TYPE='text' name='RealName' VALUE='" . $_POST['RealName'] . "' SIZE=36 MAXLENGTH=35></TD></TR>";
echo '<TR><TD>' . _('Telephone No') . ":</TD>
	<TD><INPUT TYPE='Text' name='Phone' VALUE='" . $_POST['Phone'] . "' SIZE=32 MAXLENGTH=30></TD></TR>";
echo '<TR><TD>' . _('Email Address') .":</TD>
	<TD><INPUT TYPE='Text' name='Email' VALUE='" . $_POST['Email'] ."' SIZE=32 MAXLENGTH=55></TD></TR>";
echo '<TR><TD>' . _('Access Level') . ":</TD><TD><SELECT NAME='Access'>";


for ($i=0;$i<count($SecurityHeadings);$i++){
	if ($i== (int)$_POST["Access"]){
		echo "<OPTION SELECTED VALUE=" . $i . ">" . $SecurityHeadings[$i];
	} else {
		echo "<OPTION VALUE=" . $i . ">" . $SecurityHeadings[$i];
	}
}

echo '</SELECT></TD></TR>';

echo '<TR><TD>' . _('Default Location') . ":</TD>
	<TD><SELECT name='DefaultLocation'>";

$sql = "SELECT LocCode, LocationName FROM Locations";
$result = DB_query($sql,$db);

while ($myrow=DB_fetch_array($result)){

	if ($myrow["LocCode"] == $_POST['DefaultLocation']){

		echo "<OPTION SELECTED Value='" . $myrow["LocCode"] . "'>" . $myrow["LocationName"];

	} else {
		echo "<OPTION Value='" . $myrow["LocCode"] . "'>" . $myrow["LocationName"];

	}

}

echo '</SELECT></TD></TR>';

echo '<TR><TD>' . _('Customer Code') . ":</TD>
	<TD><INPUT TYPE='Text' name='Cust' SIZE=10 MAXLENGTH=8 VALUE='" . $_POST['Cust'] . "'></TD></TR>";

echo '<TR><TD>' . _('Branch Code') . ":</TD>
	<TD><INPUT TYPE='Text' name='BranchCode' SIZE=10 MAXLENGTH=8 VALUE='" . $_POST['BranchCode'] ."'></TD></TR>";


echo '<TR><TD>' . _('Reports Page Size') .":</TD>
	<TD><SELECT name='PageSize'>";

if($_POST['PageSize']=="A4"){
	echo "<OPTION SELECTED Value='A4'>" . _('A4');
} else {
	echo "<OPTION Value='A4'>A4";
}

if($_POST['PageSize']=="A3"){
	echo "<OPTION SELECTED Value='A3'>" . _('A3');
} else {
	echo "<OPTION Value='A3'>A3";
}

if($_POST['PageSize']=="A3_landscape"){
	echo "<OPTION SELECTED Value='A3_landscape'>" . _('A3') . ' ' . _('landscape');
} else {
	echo "<OPTION Value='A3_landscape'>" . _('A3') . ' ' . _('landscape');
}

if($_POST['PageSize']=="letter"){
	echo "<OPTION SELECTED Value='letter'>" . _('Letter');
} else {
	echo "<OPTION Value='letter'>" . _('Letter');
}

if($_POST['PageSize']=="letter_landscape"){
	echo "<OPTION SELECTED Value='letter_landscape'>" . _('Letter') . ' ' . _('landscape');
} else {
	echo "<OPTION Value='letter_landscape'>" . _('Letter') . ' ' . _('landscape');
}

if($_POST['PageSize']=="legal"){
	echo "<OPTION SELECTED Value='legal'>" . _('Legal');
} else {
	echo "<OPTION Value='legal'>" . _('Legal');
}
if($_POST['PageSize']=="legal_landscape"){
	echo "<OPTION SELECTED Value='legal_landscape'>" . _('Legal') . ' ' . _('landscape');
} else {
	echo "<OPTION Value='legal_landscape'>" . _('Legal') . ' ' . _('landscape');
}

echo "</SELECT></TD></TR>";


/*Make an array out of the comma seperated list of modules allowed*/
$ModulesAllowed = explode(",",$_POST['ModulesAllowed']);

/*Module List is in config.php */
$i=0;
foreach($ModuleList as $ModuleName){

	echo '<TR><TD>' . _('Display') . ' ' . $ModuleName . ' ' . _('options') . ":</TD><TD><SELECT name='Module_" . $i . "'>";
	if ($ModulesAllowed[$i]==0){
		echo '<OPTION SELECTED VALUE=0>' . _('No');
		echo '<OPTION VALUE=1>' . _('Yes');
	} else {
	 	echo '<OPTION SELECTED VALUE=1>' . _('Yes');
		echo '<OPTION VALUE=0>' . _('No');
	}
	echo '</SELECT></TD></TR>';
	$i++;
}

echo '<TR><TD>' . _('Account Status') . ":</TD><TD><SELECT name='Blocked'>";
if ($_POST['Blocked']==0){
	echo '<OPTION SELECTED VALUE=0>' . _('Open');
	echo '<OPTION VALUE=1>' . _('Blocked');
} else {
 	echo '<OPTION SELECTED VALUE=1>' . _('Blocked');
	echo '<OPTION VALUE=0>' . _('Open');
}
echo '</SELECT></TD></TR>';


echo "</TABLE>
	<CENTER><input type='Submit' name='submit' value='" . _('Enter Information') . "'></CENTER></FORM>";

include("includes/footer.inc");

?>
