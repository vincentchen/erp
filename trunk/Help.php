<?php
/* $Revision: 1.5 $ */

if (isset($_GET['Title'])){
	$HelpPageTitle = $_GET['Title'];
}elseif(isset($_POST['HelpPageTitle'])){
	$HelpPageTitle = $_POST['HelpPageTitle'];
}

$title = "Help On " . $HelpPageTitle;

$PageSecurity = 1;

include("includes/session.inc");
include("includes/header.inc");
include('includes/htmlMimeMail.php');

if (isset($_GET['Page'])){
	$Page = $_GET['Page'];
} elseif (isset($_POST['Page'])){
	$Page = $_POST['Page'];
}

if (isset($_GET['Title'])){
	$HelpPageTitle = $_GET['Title'];
} elseif (isset($_POST['HelpPageTitle'])){
	$HelpPageTitle = $_POST['HelpPageTitle'];
}

if (isset($_GET['HelpID'])){
	$HelpID = $_GET['HelpID'];
} elseif (isset($_POST['HelpID'])){
	$HelpID = $_POST['HelpID'];
}

if ($_POST['submit']) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (strlen($_POST['Narrative']) < 3) {
		$InputError = 1;
		echo "<BR>The narrative must be typo its less than two characters long! <BR>It was ignored.";
	}

	if ($InputError !=1 AND isset($_POST['HelpID'])) {

		$sql = "UPDATE Help SET Narrative = '" . $_POST['Narrative'] . "' WHERE PageID =" . $_POST['PageID'];
		$msg = "The help record has been updated.";
	} elseif ($InputError !=1) {

	/*Must be submitting new entries in the help narrative addition form */
		
		if ($_SESSION['ModulesEnabled'][7]!=1){ /*User has no access to system set up */
		/*User help records will be added as help type U */
			$HelpType = "U";
		} else {
		/*Sys Admin help admin records will be added as help type A */
			$HelpType = "A";
		}
		$sql = "INSERT INTO Help (PageID, Narrative, HelpType) VALUES (" . $_POST['PageID'] . ", '" . $_POST['Narrative'] . "')";
		$msg = "The new help narrative has been added";
		if ($ContributeHelpText==true){
			$Recipients = array("'Phil' <p.daintree@paradise.net.nz>");
			$mail = new htmlMimeMail();
			$mail->setText($sql);
			$mail->setSubject("Help Text Contribution");
			$mail->setFrom($CompanyName . "<" . $CompanyRecord['Email'] . ">");
			$result = $mail->send($Recipients);
			$msg .= "<BR>Many thanks for contributing to the project!";
		}

	}
	//run the SQL from either of the above possibilites
	$result = DB_query($sql,$db);
	echo "<BR>$msg";
	unset($_POST['Narrative']);

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button


	$sql="DELETE FROM Help WHERE ID=" . $_GET['HelpID'];

	$ErrMsg = "The help narrative could not be deleted because";
	$DbgMsg = "<BR>The following SQL was used:";

	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

	echo "<BR>The selected help narrative has been deleted <p>";
	
}

if (!isset($Page)){ /*the help page was called without specifying the page */
	echo "<BR>The help page must be called from a link on the page on which help is required.";
}

/*Display the help */

/*First off retrieve the overview of the pages function from the Scripts table */


$sql = "SELECT PageID, PageDescription FROM Scripts WHERE FileName='" . $Page ."'";
$result = DB_query($sql,$db);

echo "<CENTER><table border=0 cellpadding=0>\n";
echo "<tr><td class='HelpTableHeader'>Help On " . $HelpPageTitle ."</td></tr>\n";

$myrow = DB_fetch_row($result);

echo "<TR><TD><FONT SIZE=3><BR>" . $myrow[1] . "<BR></TD></TR>";

echo "<TR><TD><HR></TD></TR>";

$PageID = $myrow[0];

/*Now get the help records recorded for PageID */

$sql = "SELECT Narrative, ID, HelpType FROM Help WHERE PageID=" . $PageID . " ORDER BY HelpType, ID";
$result = DB_query($sql,$db);

while ($myrow = DB_fetch_row($result)) {

	if ($_SESSION['ModulesEnabled'][7]!=1 AND ($myrow[2]=='S' OR $myrow[2]=='A')){
		
		/*Help is Admin or System help and cannot be edited or deleted by non Sys Admins */
		
		printf("<tr><td>%s</td></tr>", $myrow[0]);
		
	} else { /*allow System Admins to modify/delete any help */
	
		printf("<tr><td>%s</td><td><a href='%sHelpID=%s&Page=%s&Title=%s'>Edit</td><td>&nbsp;<a href='%sHelpID=%s&delete=yes&Page=%s&Title=%s'>DELETE</td></tr>", $myrow[0], $_SERVER['PHP_SELF'] . "?" . SID, $myrow[1], $Page, $HelpPageTitle, $_SERVER['PHP_SELF'] . "?" . SID, $myrow[1], $Page, $HelpPageTitle);
	}
//END WHILE LIST LOOP
}

echo "</table></CENTER>";

echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . "?" . SID . ">";

if (isset($_GET['HelpID']) AND ! isset($_GET['delete'])) {
	//editing an existing sales type

	$sql = "SELECT Narrative FROM Help WHERE ID=" . $_GET['HelpID'];

	$result = DB_query($sql, $db);
	$myrow = DB_fetch_row($result);

	echo "<INPUT TYPE=HIDDEN NAME='HelpID' VALUE=" . $HelpID . ">";

	$NarrativeHeading = "Edit This Comment";
	$_POST['Narrative'] = $myrow[0];

}

echo "<INPUT TYPE=HIDDEN NAME='PageID' VALUE=" . $PageID . ">";
echo "<INPUT TYPE=HIDDEN NAME='Page' VALUE='" . $Page . "'>";
echo "<INPUT TYPE=HIDDEN NAME='HelpPageTitle' VALUE='" . $HelpPageTitle . "'>";

echo "<TABLE><TR><TD><FONT COLOR=BLUE><B>";

if (isset($NarrativeHeading)){
	echo $NarrativeHeading;
} else {
	echo "Add New Help Comment";
}
echo ":</FONT></B></TD></TR>";

echo "<TR><TD><textarea name='Narrative' cols=100% rows=3>" . $_POST['Narrative'] . "</textarea></TD></TR>";

echo "</TABLE>";

if ($ContributeHelpText==true){
	echo "<P><FONT=1 COLOR=BLUE><B>Notice:</B><BR>The system is set to send a copy of the help text you add here to the developer for inclusion in the project. You can turn this option off by setting the ContributeHelpText variable in config.php to false. However, contributions are sorely needed and your input would be appreciated!";
}

echo "<CENTER><input type='Submit' name='submit' value='Enter Information'>";


echo "</FORM>";


include("includes/footer.inc");
?>
