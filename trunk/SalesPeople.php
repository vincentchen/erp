<?php
$title = "Sales People Maintenance";

$PageSecurity = 3;

include("includes/session.inc");
include("includes/header.inc");

if (isset($_GET['SelectedSaleperson'])){
	$SelectedSaleperson =strtoupper($_GET['SelectedSaleperson']);
} elseif(isset($_POST['SelectedSaleperson'])){
	$SelectedSaleperson =strtoupper($_POST['SelectedSaleperson']);
}

?>

<?php
if ($_POST['submit']) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (strlen($_POST['SalesmanCode']) > 3) {
		$InputError = 1;
		echo "The Sales-person code must be three characters or less long";
		die;
	} elseif (strlen($_POST['SalesmanCode'])==0 OR $_POST['SalesmanCode']=="") {
		$InputError = 1;
		echo "The sales-person code cannot be empty";
		die;
	} elseif (strlen($_POST['SalesmanName']) > 30) {
		$InputError = 1;
		echo "The Sales-person name must be thity characters or less long";
		die;
	} elseif (strlen($_POST['SManTel']) > 20) {
		$InputError = 1;
		echo "The Sales-person telephone number must be twenty characters or less long";
		die;
	} elseif (strlen($_POST['SManFax']) > 20) {
		$InputError = 1;
		echo "The Sales-person telephone number must be twenty characters or less long";
		die;
	} elseif (!is_double((double)$_POST['CommissionRate1']) OR !is_double((double) $_POST['CommissionRate2']))						{	$InputError = 1;
		echo "The commission rates must be an floating point numbers";
	} elseif (!is_double((double)$_POST['Breakpoint'])) {
		$InputError = 1;
		echo "The breakpoint should be a floating point number";
	}

	if (strlen($_POST['SManTel'])==0){
	  $_POST['SManTel']="";
	}
	if (strlen($_POST['SManFax'])==0){
	  $_POST['SManFax']="";
	}
	if (strlen($_POST['CommissionRate1'])==0){
	  $_POST['CommissionRate1']=0;
	}
	if (strlen($_POST['CommissionRate2'])==0){
	  $_POST['CommissionRate2']=0;
	}
	if (strlen($_POST['Breakpoint'])==0){
	  $_POST['Breakpoint']=0;
	}

	if (isset($SelectedSaleperson) AND $InputError !=1) {

		/*SelectedSaleperson could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/

		$sql = "UPDATE Salesman SET SalesmanName='" . $_POST['SalesmanName'] . "', CommissionRate1=" . $_POST['CommissionRate1'] . ", SManTel='" . $_POST['SManTel'] . "', SManFax='" . $_POST['SManFax'] . "', Breakpoint=" . $_POST['Breakpoint'] . ", CommissionRate2=" . $_POST['CommissionRate2'] . " WHERE SalesmanCode = '$SelectedSaleperson'";

		$msg = "Sales person record for " . $_POST['SalesmanName'] . " has been updated.";
	} elseif ($InputError !=1) {

	/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */

		$sql = "INSERT INTO Salesman (SalesmanCode, SalesmanName, CommissionRate1, CommissionRate2, Breakpoint, SManTel, SManFax) VALUES ('" . $_POST['SalesmanCode'] . "', '" . $_POST['SalesmanName'] . "', " . $_POST['CommissionRate1'] . ", " . $_POST['CommissionRate2'] . ", " . $_POST['Breakpoint'] . ", '" . $_POST['SManTel'] . "', '" . $_POST['SManFax'] . "')";

		$msg = "A new sales person record has been added for " . $_POST['SalesmanName'];
	}
	//run the SQL from either of the above possibilites
	$result = DB_query($sql,$db);
	if (DB_error_no($db)!=0){
	   echo "<BR>The insert or update of the salesperson failed because " . DB_error_msg($db);
	   if ($debug==1){
	      echo "<BR>The SQL that was used - and failed was: <BR>" .$sql;
	   }
	} else {
		echo "<BR>$msg";
		unset($SelectedSalesperson);
		unset($_POST['SalesmanCode']);
		unset($_POST['SalesmanName']);
		unset($_POST['CommissionRate1']);
		unset($_POST['CommissionRate2']);
		unset($_POST['Breakpoint']);
		unset($_POST['SManFax']);
		unset($_POST['SManTel']);

	}

} elseif ($_GET['delete']) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorsMaster'

	$sql= "SELECT COUNT(*) FROM CustBranch WHERE  CustBranch.Salesman='$SelectedSaleperson'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		echo "<BR>Cannot delete this sales-person because branches are set up referring to this sales-person - first alter the branches concerned.";
		echo "<br> There are " . $myrow[0] . " branches that refer to this sales-person";

	} else {
		$sql= "SELECT COUNT(*) FROM SalesAnalysis WHERE SalesAnalysis.Salesperson='$SelectedSaleperson'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			echo "<BR>Cannot delete this sales-person because sales analysis records refer to them.";
		echo "<BR>There are " . $myrow[0] . " sales analysis records that refer to this sales-person";
		} else {

			$sql="DELETE FROM Salesman WHERE SalesmanCode='$SelectedSaleperson'";
			$result = DB_query($sql,$db);


			if (DB_error_no($db) !=0) {
				echo "<BR>The sales-person could not be deleted because - " . DB_error_msg($db);
			} else {
				echo "<BR>Sales person $SelectedSalesperson has been deleted from the database! <p>";
				unset ($SelectedSalesperson);
				unset($delete);
			}

		}
	} //end if Sales-person used in GL accounts

} 

if (!isset($SelectedSaleperson)) {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedSaleperson will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of Sales-persons will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

	$sql = "SELECT SalesmanCode, SalesmanName, SManTel, SManFax, CommissionRate1, Breakpoint, CommissionRate2 FROM Salesman";
	$result = DB_query($sql,$db);

	echo "<CENTER><table border=1>\n";
	echo "<tr><td class='tableheader'>Code</td><td class='tableheader'>Name</td><td class='tableheader'>Telephone</td><td class='tableheader'>Facsimile</td><td class='tableheader'>Comm Rate 1</td><td class='tableheader'>Break</td><td class='tableheader'>Comm Rate 2</td></tr>\n";

	while ($myrow=DB_fetch_row($result)) {


	printf("<tr><td><FONT SIZE=2>%s</td><td><FONT SIZE=2>%s</td><td><FONT SIZE=2>%s</FONT></td><td><FONT SIZE=2>%s</FONT></td><td><FONT SIZE=2>%s</FONT></td><td><FONT SIZE=2>%s</FONT></td><td><FONT SIZE=2>%s</FONT></td><td><a href=\"%sSelectedSaleperson=%s\">Edit</a></td><td><a href=\"%sSelectedSaleperson=%s&delete=1\">Delete</a></td></tr>", $myrow[0], $myrow[1], $myrow[2], $myrow[3], $myrow[4], $myrow[5], $myrow[6], $_SERVER['PHP_SELF'] . "?" . SID, $myrow[0],$_SERVER['PHP_SELF'] . "?" . SID,$myrow[0]);

	} //END WHILE LIST LOOP

} //end of ifs and buts!

?>
</table></CENTER>
<p>
<?php
if (isset($SelectedSaleperson)) {	?>
	<Center><a href="<?php echo $_SERVER['PHP_SELF'] . "?" . SID;?>">Show All Sales People</a></Center>
<?php } ?>


<P>


<?php

if (! isset($_GET['delete'])) {

	echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . "?" . SID . ">";

	if (isset($SelectedSaleperson)) {
		//editing an existing Sales-person

		$sql = "SELECT SalesmanCode, SalesmanName, SManTel, SManFax, CommissionRate1, Breakpoint, CommissionRate2  FROM Salesman WHERE SalesmanCode='$SelectedSaleperson'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['SalesmanCode'] = $myrow["SalesmanCode"];
		$_POST['SalesmanName'] = $myrow["SalesmanName"];
		$_POST['SManTel'] = $myrow["SManTel"];
		$_POST['SManFax'] = $myrow["SManFax"];
		$_POST['CommissionRate1']  = $myrow["CommissionRate1"];
		$_POST['Breakpoint'] = $myrow["Breakpoint"];
		$_POST['CommissionRate2']  = $myrow["CommissionRate2"];


		echo "<INPUT TYPE=HIDDEN NAME='SelectedSaleperson' VALUE='" . $SelectedSaleperson . "'>";
		echo "<INPUT TYPE=HIDDEN NAME='SalesmanCode' VALUE='" . $_POST['SalesmanCode'] . "'>";
		echo "<CENTER><TABLE> <TR><TD>Sales-person code:</TD><TD>";
		echo $_POST['SalesmanCode'] . "</TD></TR>";

	} else { //end of if $SelectedSaleperson only do the else when a new record is being entered

		echo "<CENTER><TABLE><TR><TD>Sales-person code:</TD><TD><input type='Text' name='SalesmanCode'SIZE=3 MAXLENGTH=3></TD></TR>";
	}

	if (strlen($_POST['SManTel'])==0){
	  $_POST['SManTel']="";
	}
	if (strlen($_POST['SManFax'])==0){
	  $_POST['SManFax']="";
	}
	if (strlen($_POST['CommissionRate1'])){
	  $_POST['CommissionRate1']=0;
	}
	if (strlen($_POST['CommissionRate2'])){
	  $_POST['CommissionRate2']=0;
	}
	if (strlen($_POST['Breakpoint'])){
	  $_POST['Breakpoint']=0;
	}


	echo "<TR><TD>Sales-person Name:</TD><TD><INPUT TYPE='text' name='SalesmanName'  SIZE=30 MAXLENGTH=30 VALUE='" . $_POST['SalesmanName'] . "'></TD></TR>";
	echo "<TR><TD>Telephone No.:</TD><TD><INPUT TYPE='Text' name='SManTel' SIZE=20 MAXLENGTH=20 VALUE='" . $_POST['SManTel'] . "'></TD></TR>";
	echo "<TR><TD>Facsimile No.:</TD><TD><INPUT TYPE='Text' name='SManFax' SIZE=20 MAXLENGTH=20 VALUE='" . $_POST['SManFax'] . "'></TD></TR>";
	echo "<TR><TD>Commission Rate 1.:</TD><TD><INPUT TYPE='Text' name='CommissionRate1' SIZE=5 MAXLENGTH=5 VALUE=" . $_POST['CommissionRate1'] . "></TD></TR>";
	echo "<TR><TD>Breakpoint:</TD><TD><INPUT TYPE='Text' name='Breakpoint' SIZE=6 MAXLENGTH=6 VALUE=" . $_POST['Breakpoint'] . "></TD></TR>";
	echo "<TR><TD>Commission Rate 2.:</TD><TD><INPUT TYPE='Text' name='CommissionRate2' SIZE=5 MAXLENGTH=5 VALUE=" . $_POST['CommissionRate2']. "></TD></TR>";

	echo "</TABLE>";

	echo "<CENTER><input type='Submit' name='submit' value='Enter Information'></CENTER>";

	echo "</FORM>";

} //end if record deleted no point displaying form to add record


include("includes/footer.inc");
?>
