<?php
$title = "Stock Category Maintenance";

$PageSecurity = 11;

include("includes/session.inc");
include("includes/header.inc");

if (isset($_GET['SelectedCategory'])){
	$SelectedCategory = strtoupper($_GET['SelectedCategory']);
} else if (isset($_POST['SelectedCategory'])){
	$SelectedCategory = strtoupper($_POST['SelectedCategory']);
}

if ($_POST['submit']) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	$_POST["CategoryID"] = strtoupper($_POST["CategoryID"]);

	if (strlen($_POST['CategoryID']) > 6) {
		$InputError = 1;
		echo "<BR>The Inventory Category code must be six characters or less long";
	} elseif (strlen($_POST['CategoryID'])==0) {
		$InputError = 1;
		echo "<BR>The Inventory category code must be at least 1 character but less than six characters long";
	} elseif (strlen($_POST['CategoryDescription']) >20) {
		$InputError = 1;
		echo "<BR>The Sales category description must be twenty characters or less long";
	} elseif ($_POST['StockType'] !='D' AND $_POST['StockType'] !='L' AND $_POST['StockType'] !='F' AND $_POST['StockType'] !='M') {
		$InputError = 1;
		echo "<BR>The stock type selected must be one of 'D' - Dummy item, 'L' - Labour stock item, 'F' - Finished product or 'M' - Raw Materials";
	}

	if ($SelectedCategory AND $InputError !=1) {

		/*SelectedCategory could also exist if submit had not been clicked this code
		would not run in this case cos submit is false of course  see the
		delete code below*/

		$sql = "UPDATE StockCategory SET StockType = '" . $_POST['StockType'] . "', CategoryDescription = '" . $_POST['CategoryDescription'] . "', StockAct = " . $_POST['StockAct'] . ", AdjGLAct = " . $_POST['AdjGLAct'] . ", PurchPriceVarAct = " . $_POST['PurchPriceVarAct'] . ", MaterialUseageVarAc = " . $_POST['MaterialUseageVarAc'] . ", WIPAct = " . $_POST['WIPAct'] . " WHERE CategoryID = '$SelectedCategory'";
		$msg = "The stock category record has been updated.";
	} elseif ($InputError !=1) {

	/*Selected category is null cos no item selected on first time round so must be adding a	record must be submitting new entries in the new stock category form */

		$sql = "INSERT INTO StockCategory (CategoryID, StockType, CategoryDescription, StockAct, AdjGLAct, PurchPriceVarAct, MaterialUseageVarAc, WIPAct) VALUES ('" . $_POST['CategoryID'] . "', '" . $_POST['StockType'] . "', '" . $_POST['CategoryDescription'] . "', " . $_POST['StockAct'] . ", " . $_POST['AdjGLAct'] . ", " . $_POST['PurchPriceVarAct'] . ", " . $_POST['MaterialUseageVarAc'] . ", " . $_POST['WIPAct'] . ")";
		$msg = "A new stock category record has been added.";
	}
	//run the SQL from either of the above possibilites
	$result = DB_query($sql,$db);
	unset ($SelectedCategory);
	unset($_POST['CategoryID']);
	unset($_POST['StockType']);
	unset($_POST['CategoryDescription']);
	unset($_POST['StockAct']);
	unset($_POST['AdjGLAct']);
	unset($_POST['PurchPriceVarAct']);
	unset($_POST['MaterialUseageVarAc']);
	unset($_POST['WIPAct']);
	echo "<BR>$msg";

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'StockMaster'

	$sql= "SELECT COUNT(*) FROM StockMaster WHERE StockMaster.CategoryID='$SelectedCategory'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		echo "<BR>Cannot delete this stock category because stock items have been created using this stock category.";
		echo "<br> There are " . $myrow[0] . " items referring to this stock category code";

	} else {
		$sql = "SELECT COUNT(*) FROM SalesGLPostings WHERE StkCat='$SelectedCategory'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			echo "<BR>Cannot delete this stock category because it is used by the sales - GL posting interface. Delete any records in the Sales GL Interface set up using this stock category first.";
		} else {
			$sql = "SELECT COUNT(*) FROM COGSGLPostings WHERE StkCat='$SelectedCategory'";
			$result = DB_query($sql,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]>0) {
				echo "<BR>Cannot delete this stock category because it is used by the cost of sales - GL posting interface. Delete any records in the Cost of Sales GL Interface set up using this stock category first.";
			} else {
				$sql="DELETE FROM StockCategory WHERE CategoryID='$SelectedCategory'";
				$result = DB_query($sql,$db);
				echo "<BR>The stock category $SelectedCategory has been deleted ! <p>";
				unset ($SelectedCategory);
			}
		}
	} //end if stock category used in debtor transactions
}

if (!isset($SelectedCategory)) {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedCategory will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of stock categorys will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

	$sql = "SELECT * FROM StockCategory";
	$result = DB_query($sql,$db);

	echo "<CENTER><table border=1>\n";
	echo "<tr><td class='tableheader'>Cat Code</td><td class='tableheader'>Description</td><td class='tableheader'>Type</td><td class='tableheader'>Stock GL</td><td class='tableheader'>Adjts GL</td><td class='tableheader'>Price Var GL</td><td class='tableheader'>Usage Var GL</td><td class='tableheader'>WIP GL</td></tr>\n";

	$k=0; //row colour counter

	while ($myrow = DB_fetch_row($result)) {
		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}
		printf("<td>%s</td><td>%s</td><td>%s</td><td ALIGN=RIGHT>%s</td><td ALIGN=RIGHT>%s</td><td ALIGN=RIGHT>%s</td><td ALIGN=RIGHT>%s</td><td ALIGN=RIGHT>%s</td><td><a href=\"%sSelectedCategory=%s\">Edit</td><td><a href=\"%sSelectedCategory=%s&delete=yes\">DELETE</td></tr>", $myrow[0], $myrow[1], $myrow[2], $myrow[3], $myrow[4], $myrow[5], $myrow[6], $myrow[7], $_SERVER['PHP_SELF'] . "?" . SID, $myrow[0], $_SERVER['PHP_SELF'] . "?" . SID, $myrow[0]);
	}
	//END WHILE LIST LOOP
}

//end of ifs and buts!

?>
</table></CENTER>
<p>
<?php
if ($SelectedCategory) {  ?>
	<Center><a href="<?php echo $_SERVER['PHP_SELF'] . "?" . SID;?>">Show All Stock Categories</a></Center>
<?php } ?>

<P>

<?php

if (! $_GET['delete']) {

	echo "<FORM METHOD='post' action='" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";

	if ($SelectedCategory) {
		//editing an existing stock category

		$sql = "SELECT CategoryID, StockType, CategoryDescription, StockAct, AdjGLAct, PurchPriceVarAct, MaterialUseageVarAc, WIPAct FROM StockCategory WHERE CategoryID='$SelectedCategory'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['CategoryID'] = $myrow["CategoryID"];
		$_POST['StockType']  = $myrow["StockType"];
		$_POST['CategoryDescription']  = $myrow["CategoryDescription"];
		$_POST['StockAct']  = $myrow["StockAct"];
		$_POST['AdjGLAct']  = $myrow["AdjGLAct"];
		$_POST['PurchPriceVarAct']  = $myrow["PurchPriceVarAct"];
		$_POST['MaterialUseageVarAc']  = $myrow["MaterialUseageVarAc"];
		$_POST['WIPAct']  = $myrow["WIPAct"];

		echo "<INPUT TYPE=HIDDEN NAME='SelectedCategory' VALUE='$SelectedCategory'>";
		echo "<INPUT TYPE=HIDDEN NAME='CategoryID' VALUE='" . $_POST['CategoryID'] . "'>";
		echo "<CENTER><TABLE><TR><TD>Category Code:</TD><TD>'" . $_POST['CategoryID'] . "'</TD></TR>";

	} else { //end of if $SelectedCategory only do the else when a new record is being entered

		echo "<CENTER><TABLE><TR><TD>Category Code:</TD><TD><input type='Text' name='CategoryID' SIZE=7 MAXLENGTH=6 value='" . $_POST['CategoryID'] . "'></TD></TR>";
	}

	//SQL to poulate account selection boxes
	$sql = "SELECT AccountCode, AccountName FROM ChartMaster, AccountGroups WHERE ChartMaster.Group_=AccountGroups.GroupName AND AccountGroups.PandL=0 ORDER BY AccountCode";

	$result = DB_query($sql,$db);

	echo "<TR><TD>Category Description:</TD><TD><input type='Text' name='CategoryDescription' SIZE=22 MAXLENGTH=20 value='" . $_POST['CategoryDescription'] ."'></TD></TR>";

	echo "<TR><TD>Stock Type:</TD><TD><SELECT name='StockType'>";
		if ($_POST['StockType']=="F") {
			echo "<OPTION SELECTED VALUE='F'>Finished Goods";
		} else {
			echo "<OPTION VALUE='F'>Finished Goods";
		}
		if ($_POST['StockType']=="M") {
			echo "<OPTION SELECTED VALUE='M'>Raw Materials";
		} else {
			echo "<OPTION VALUE='M'>Raw Materials";
		}
		if ($_POST['StockType']=="D") {
			echo "<OPTION SELECTED VALUE='D'>Dummy Item - (No Movements)";
		} else {
			echo "<OPTION VALUE='D'>Dummy Item - (No Movements)";
		}
		if ($_POST['StockType']=="L") {
			echo "<OPTION SELECTED VALUE='L'>Labour";
		} else {
			echo "<OPTION VALUE='L'>Labour";
		}

	echo "</SELECT></TD></TR>";


	echo "<TR><TD>Stock GL Code:</TD><TD><SELECT name='StockAct'>";

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow["AccountCode"]==$_POST['StockAct']) {
			echo "<OPTION SELECTED VALUE=";
		} else {
			echo "<OPTION VALUE=";
		}
		echo $myrow["AccountCode"] . ">" . $myrow["AccountName"];
	} //end while loop
	DB_data_seek($result,0);
	echo "</SELECT></TD></TR>";

	echo "<TR><TD>WIP GL Code:</TD><TD><SELECT name='WIPAct'>";

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow["AccountCode"]==$_POST['WIPAct']) {
			echo "<OPTION SELECTED VALUE=";
		} else {
			echo "<OPTION VALUE=";
		}
		echo $myrow["AccountCode"] . ">" . $myrow["AccountName"];

	} //end while loop
	echo "</SELECT></TD></TR>";

	$sql = "SELECT AccountCode, AccountName FROM ChartMaster, AccountGroups WHERE ChartMaster.Group_=AccountGroups.GroupName AND AccountGroups.PandL!=0 ORDER BY AccountCode";

	$result1 = DB_query($sql,$db);

	echo "<TR><TD>Stock Adjustments GL Code:</TD><TD><SELECT name='AdjGLAct'>";

	while ($myrow = DB_fetch_array($result1)) {
		if ($myrow["AccountCode"]==$_POST['AdjGLAct']) {
			echo "<OPTION SELECTED VALUE=";
		} else {
			echo "<OPTION VALUE=";
		}
		echo $myrow["AccountCode"] . ">" . $myrow["AccountName"];

	} //end while loop
	DB_data_seek($result1,0);
	echo "</SELECT></TD></TR>";

	echo "<TR><TD>Price Variance GL Code:</TD><TD><SELECT name='PurchPriceVarAct'>";

	while ($myrow = DB_fetch_array($result1)) {
		if ($myrow["AccountCode"]==$_POST['PurchPriceVarAct']) {
			echo "<OPTION SELECTED VALUE=";
		} else {
			echo "<OPTION VALUE=";
		}
		echo $myrow["AccountCode"] . ">" . $myrow["AccountName"];

	} //end while loop
	DB_data_seek($result1,0);

	echo "</SELECT></TD></TR>";

	echo "<TR><TD>Usage Variance GL Code:</TD><TD><SELECT name='MaterialUseageVarAc'>";

	while ($myrow = DB_fetch_array($result1)) {
		if ($myrow["AccountCode"]==$_POST['MaterialUseageVarAc']) {
			echo "<OPTION SELECTED VALUE=";
		} else {
			echo "<OPTION VALUE=";
		}
		echo $myrow["AccountCode"] . ">" . $myrow["AccountName"];

	} //end while loop
	DB_free_result($result1);
	echo "</SELECT></TD></TR></TABLE>";

	echo "<CENTER><input type='Submit' name='submit' value='Enter Information'>";

	echo "</FORM>";

} //end if record deleted no point displaying form to add record


include("includes/footer.inc");
?>
