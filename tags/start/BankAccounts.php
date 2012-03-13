<?php
$title = "Bank Accounts Maintenance";

$PageSecurity = 10;

include("includes/session.inc");
include("includes/header.inc");

?>

<P>

<?php

if (isset($_GET['SelectedBankAccount'])) {
	$SelectedBankAccount=$_GET['SelectedBankAccount'];
} elseif (isset($_POST['SelectedBankAccount'])) {
	$SelectedBankAccount=$_POST['SelectedBankAccount'];
}


if ($_POST['submit']) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible


	if (strlen($_POST['BankAccountName']) >50) {
		$InputError = 1;
		echo "The bank account name must be fifty characters or less long";
	} elseif (strlen($_POST['BankAccountNumber']) >50) {
		$InputError = 1;
		echo "The bank account number must be fifty characters or less long";
	}  elseif (strlen($_POST['BankAddress']) >50) {
		$InputError = 1;
		echo "The bank address must be fifty characters or less long";
	}



	if (isset($SelectedBankAccount) AND $InputError !=1) {

		$sql = "UPDATE BankAccounts SET BankAccountName='" . $_POST['BankAccountName'] . "', BankAccountNumber='" . $_POST['BankAccountNumber'] . "', BankAddress='" . $_POST['BankAddress'] . "' WHERE AccountCode = '" . $SelectedBankAccount . "'";
		$msg = "The bank account details have been updated.";
	} elseif ($InputError !=1) {

	/*Selectedbank account is null cos no item selected on first time round so must be adding a    record must be submitting new entries in the new bank account form */

		$sql = "INSERT INTO BankAccounts (AccountCode, BankAccountName, BankAccountNumber, BankAddress) VALUES ('" . $_POST['AccountCode'] . "', '" . $_POST['BankAccountName'] . "', '" . $_POST['BankAccountNumber'] . "', '" . $_POST['BankAddress'] . "')";
		$msg = "The new bank account has been entered.";
	}

	//run the SQL from either of the above possibilites
	$result = DB_query($sql,$db);
	if (DB_error_no($db) !=0) {
		echo "The bank account could not be inserted or modified because - " . DB_error_msg($db);
		if ($debug==1){
		     echo "The SQL used to insert/modify the bank account details was:<BR>$sql";
		}
		exit;
	} else {
		echo "<P>$msg<P>";
	}


} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'BankTrans'

	$sql= "SELECT COUNT(*) FROM BankTrans WHERE BankTrans.BankAct='$SelectedBankAccount'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		echo "Cannot delete this bank account because transactions have been created using this account.";
		echo "<br> There are " . $myrow[0] . " transactions with this bank account code";

	}
	if (!$CancelDelete) {
		$sql="DELETE FROM BankAccounts WHERE AccountCode='$SelectedBankAccount'";
		$result = DB_query($sql,$db);
		echo "Bank account deleted ! <p>";
	} //end if Delete bank account
}

/* Always show the list of accounts */

$sql = "SELECT BankAccounts.AccountCode, ChartMaster.AccountName, BankAccountName, BankAccountNumber, BankAddress FROM BankAccounts, ChartMaster WHERE BankAccounts.AccountCode = ChartMaster.AccountCode";
$result = DB_query($sql,$db);
if (DB_error_no($db) !=0) {
	echo "The bank accounts set up could not be retreived because - " . DB_error_msg($db);
	if ($debug==1){
	     echo "The SQL used to retrieve the bank account details was:<BR>$sql";
	}
	     exit;
}


echo "<CENTER><table>\n";
echo "<tr><td class='tableheader'>GL Account</td><td class='tableheader'>Account Name</td><td class='tableheader'>Account Number</td><td class='tableheader'>Bank Address</td></tr>";

$k=0; //row colour counter
while ($myrow = DB_fetch_row($result)) {
if ($k==1){
	echo "<tr bgcolor='#CCCCCC'>";
	$k=0;
} else {
	echo "<tr bgcolor='#EEEEEE'>";
	$k++;
}

printf("<td>%s<BR><FONT SIZE=2>%s</FONT></td><td>%s</td><td>%s</td><td>%s</td><td><a href=\"%s?SelectedBankAccount=%s\">Edit</td><td><a href=\"%s?SelectedBankAccount=%s&delete=1\">Delete</td></tr>", $myrow[0], $myrow[1], $myrow[2], $myrow[3], $myrow[4], $_SERVER['PHP_SELF'], $myrow[0], $_SERVER['PHP_SELF'], $myrow[0]);

}
//END WHILE LIST LOOP


?>
</CENTER></table>
<p>

<?php

echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . ">";

if (isset($SelectedBankAccount) AND !isset($_GET['delete'])) {
	//editing an existing bank account  - not deleting

	$sql = "SELECT AccountCode, BankAccountName, BankAccountNumber, BankAddress FROM BankAccounts WHERE BankAccounts.AccountCode='$SelectedBankAccount'";

	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);

	$_POST['AccountCode'] = $myrow["AccountCode"];
	$_POST['BankAccountName']  = $myrow["BankAccountName"];
	$_POST['BankAccountNumber'] = $myrow["BankAccountNumber"];
	$_POST['BankAddress'] = $myrow["BankAddress"];

	echo "<INPUT TYPE=HIDDEN NAME=SelectedBankAccount VALUE=" . $SelectedBankAccount . ">";
	echo "<INPUT TYPE=HIDDEN NAME=AccountCode VALUE=" . $_POST['AccountCode'] . ">";
	echo "<CENTER><TABLE> <TR><TD>Bank Account GL Code:</TD><TD>";
	echo $_POST['AccountCode'] . "</TD></TR>";
} else { //end of if $Selectedbank account only do the else when a new record is being entered
	echo "<CENTER><TABLE><TR><TD>Bank Account GL Code:</TD><TD><Select name='AccountCode'>";

	$sql = "SELECT AccountCode, AccountName FROM ChartMaster, AccountGroups WHERE ChartMaster.Group_ = AccountGroups.GroupName AND AccountGroups.PandL = 0 ORDER BY AccountCode";

	$result = DB_query($sql,$db);
	while ($myrow = DB_fetch_array($result)) {
		if ($myrow["AccountCode"]==$_POST['AccountCode']) {
			echo "<OPTION SELECTED VALUE=";
		} else {
			echo "<OPTION VALUE=";
		}
		echo $myrow["AccountCode"] . ">" . $myrow["AccountName"];

	} //end while loop

	echo "</SELECT></TD></TR>";
}
?>

<TR><TD>Bank Account Name:</TD>
<TD><input type="Text" name="BankAccountName" value="<?php echo $_POST['BankAccountName']; ?>" SIZE=40 MAXLENGTH=50></TD></TR>
<TR><TD>Bank Account Number:</TD>
<TD><input type="Text" name="BankAccountNumber" value="<?php echo $_POST['BankAccountNumber']; ?>" SIZE=40 MAXLENGTH=50></TD></TR>
<TR><TD>Bank Address:</TD>
<TD><input type="Text" name="BankAddress" value="<?php echo $_POST['BankAddress']; ?>" SIZE=40 MAXLENGTH=50></TD></TR>
</TABLE>

<CENTER><input type="Submit" name="submit" value="Enter Information">

</FORM>

<?php
	include("includes/footer.inc");
?>