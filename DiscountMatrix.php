<?php
/* $Revision: 1.4 $ */

$PageSecurity = 11;
include('includes/session.inc');
$title = _('Discount Matrix Maintenance');
include('includes/header.inc');

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	if (!is_numeric($_POST['QuantityBreak'])){
		prnMsg( _('The quantity break must be entered as a positive number'),'error');
		$InputError =1;
	}

	if ($_POST['QuantityBreak']<=0){
		prnMsg( _('The quantity of all items on an order in the discount category') . ' ' . $_POST['DiscountCategory'] . ' ' . _('at which the discount will apply is 0 or less than 0') . '. ' . _('Positive numbers are expected for this entry'),'warn');
		$InputError =1;
	}
	if (!is_numeric($_POST['DiscountRate'])){
		prnMsg( _('The discount rate must be entered as a positive number'),'warn');
		$InputError =1;
	}
	if ($_POST['DiscountRate']<=0 OR $_POST['DiscountRate']>=70){
		prnMsg( _('The discount rate applicable for this record is either less than 0% or greater than 70%') . '. ' . _('Numbers between 1 and 69 are expected'),'warn');
		$InputError =1;
	}

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	if ($InputError !=1) {

		$sql = "INSERT INTO DiscountMatrix (SalesType, DiscountCategory, QuantityBreak, DiscountRate) VALUES('" . $_POST['SalesType'] . "', '" . $_POST['DiscountCategory'] . "', " . $_POST['QuantityBreak'] . ", " . ($_POST['DiscountRate']/100) . ')';

		$result = DB_query($sql,$db);
		prnMsg( _('The discount matrix record has been added'),'success');
		unset($_POST['DiscountCategory']);
		unset($_POST['SalesType']);
		unset($_POST['QuantityBreak']);
		unset($_POST['DiscountRate']);
	}
} elseif ($_GET['Delete']=='yes') {
/*the link to delete a selected record was clicked instead of the submit button */

	$sql="DELETE FROM DiscountMatrix
		WHERE DiscountCategory='" .$_GET['DiscountCategory'] . "'
		AND SalesType='" . $_GET['SalesType'] . "'
		AND QuantityBreak=" . $_GET['QuantityBreak'];

	$result = DB_query($sql,$db);
	prnMsg( _('The discount matrix record has been deleted'),'success');
}

$sql = 'SELECT Sales_Type,
		SalesType,
		DiscountCategory,
		QuantityBreak,
		DiscountRate
	FROM DiscountMatrix INNER JOIN SalesTypes
		ON DiscountMatrix.SalesType=SalesTypes.TypeAbbrev
	ORDER BY SalesType,
		DiscountCategory,
		QuantityBreak';

$result = DB_query($sql,$db);

echo '<CENTER><table>';
echo "<tr><td class='tableheader'>" . _('Sales Type') . "</td>
	<td class='tableheader'>" . _('Discount Category') . "</td>
	<td class='tableheader'>" . _('Quantity Break') . "</td>
	<td class='tableheader'>" . _('Discount Rate') . ' %' . "</td></TR>";

$k=0; //row colour counter

while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k=1;
	}
	$DeleteURL = $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=yes&SalesType=' . $myrow['SalesType'] . '&DiscountCategory=' . $myrow['DiscountCategory'] . '&QuantityBreak=' . $myrow['QuantityBreak'];

	printf("<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td><a href='%s'>" . _('Delete') . '</td>
		</tr>',
		$myrow['Sales_Type'],
		$myrow['DiscountCategory'],
		$myrow['QuantityBreak'],
		number_format($myrow['DiscountRate']*100,2) ,
		$DeleteURL);

}

echo '</TABLE><HR>';

echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';


echo '<TABLE>';

$sql = 'SELECT TypeAbbrev,
		Sales_Type
	FROM SalesTypes';

$result = DB_query($sql, $db);

echo '<TR><TD>' . _('Customer Price List') . ' (' . _('Sales Type') . '):</TD><TD>';

echo "<SELECT NAME='SalesType'>";

while ($myrow = DB_fetch_array($result)){
	if ($myrow['TypeAbbrev']==$_POST['SalesType']){
		echo "<OPTION SELECTED VALUE='" . $myrow['TypeAbbrev'] . "'>" . $myrow['Sales_Type'];
	} else {
		echo "<OPTION VALUE='" . $myrow['TypeAbbrev'] . "'>" . $myrow['Sales_Type'];
	}
}

echo '</SELECT>';


echo '<TR><TD>' . _('Discount Category Code') . ':</TD><TD>';

echo "<INPUT TYPE='Text' NAME='DiscountCategory' MAXLENGTH=2 SIZE=2 VALUE='" . $_POST['DiscCat'] . "'></TD></TR>";

echo '<TR><TD>' . _('Quantity Break') . ":</TD><TD><input type='Text' name='QuantityBreak' SIZE=10 MAXLENGTH=10></TD></TR>";

echo '<TR><TD>' . _('Discount Rate') . " (%):</TD><TD><input type='Text' name='DiscountRate' SIZE=4 MAXLENGTH=4></TD></TR>";
echo '</TABLE>';

echo "<CENTER><input type='Submit' name='submit' value='" . _('Enter Information') . "'></CENTER>";

echo '</FORM>';

include('includes/footer.inc');
?>
