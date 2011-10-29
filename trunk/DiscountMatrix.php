<?php

/* $Id$*/

include('includes/session.inc');
$title = _('Discount Matrix Maintenance');
include('includes/header.inc');

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();
$i=1;

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $title.'</p><br />';

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	if (!is_numeric($_POST['QuantityBreak'])){
		prnMsg( _('The quantity break must be entered as a positive number'),'error');
		$InputError =1;
		$Errors[$i] = 'QuantityBreak';
		$i++;
	}

	if ($_POST['QuantityBreak']<=0){
		prnMsg( _('The quantity of all items on an order in the discount category') . ' ' . $_POST['DiscountCategory'] . ' ' . _('at which the discount will apply is 0 or less than 0') . '. ' . _('Positive numbers are expected for this entry'),'warn');
		$InputError =1;
		$Errors[$i] = 'QuantityBreak';
		$i++;
	}
	if (!is_numeric($_POST['DiscountRate'])){
		prnMsg( _('The discount rate must be entered as a positive number'),'warn');
		$InputError =1;
		$Errors[$i] = 'DiscountRate';
		$i++;
	}
	if ($_POST['DiscountRate']<=0 OR $_POST['DiscountRate']>=70){
		prnMsg( _('The discount rate applicable for this record is either less than 0% or greater than 70%') . '. ' . _('Numbers between 1 and 69 are expected'),'warn');
		$InputError =1;
		$Errors[$i] = 'DiscountRate';
		$i++;
	}

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	if ($InputError !=1) {

		$sql = "INSERT INTO discountmatrix (salestype,
							discountcategory,
							quantitybreak,
							discountrate)
					VALUES('" . $_POST['SalesType'] . "',
						'" . $_POST['DiscountCategory'] . "',
						'" . $_POST['QuantityBreak'] . "',
						'" . ($_POST['DiscountRate']/100) . "')";

		$result = DB_query($sql,$db);
		prnMsg( _('The discount matrix record has been added'),'success');
		echo '<br />';
		unset($_POST['DiscountCategory']);
		unset($_POST['SalesType']);
		unset($_POST['QuantityBreak']);
		unset($_POST['DiscountRate']);
	}
} elseif (isset($_GET['Delete']) and $_GET['Delete']=='yes') {
/*the link to delete a selected record was clicked instead of the submit button */

	$sql="DELETE FROM discountmatrix
		WHERE discountcategory='" .$_GET['DiscountCategory'] . "'
		AND salestype='" . $_GET['SalesType'] . "'
		AND quantitybreak='" . $_GET['QuantityBreak']."'";

	$result = DB_query($sql,$db);
	prnMsg( _('The discount matrix record has been deleted'),'success');
	echo '<br />';
}

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';


echo '<table class="selection">';

$sql = "SELECT typeabbrev,
		sales_type
		FROM salestypes";

$result = DB_query($sql, $db);

echo '<tr><td>' . _('Customer Price List') . ' (' . _('Sales Type') . '):</td><td>';

echo '<select tabindex=1 name="SalesType">';

while ($myrow = DB_fetch_array($result)){
	if (isset($_POST['SalesType']) and $myrow['typeabbrev']==$_POST['SalesType']){
		echo '<option selected value="' . $myrow['typeabbrev'] . '">' . $myrow['sales_type'] . '</option>';
	} else {
		echo '<option value="' . $myrow['typeabbrev'] . '">' . $myrow['sales_type'] . '</option>';
	}
}

echo '</select>';


$sql = "SELECT DISTINCT discountcategory FROM stockmaster WHERE discountcategory <>''";
$result = DB_query($sql, $db);
if (DB_num_rows($result) > 0) {
	echo '<tr><td>'. _('Discount Category Code') .': </td>';
	echo '<td><select name="DiscountCategory">';

	while ($myrow = DB_fetch_array($result)){
		if ($myrow['discountcategory']==$_POST['DiscCat']){
			echo '<option selected value="' . $myrow['discountcategory'] . '">' . $myrow['discountcategory'] . '</option>';
		} else {
			echo '<option value="' . $myrow['discountcategory'] . '">' . $myrow['discountcategory'] . '</option>';
		}
	}
	echo '</select></td>';
} else {
	echo '<input type="hidden" name="DiscountCategory" value="">';
}

echo '<tr><td>' . _('Quantity Break') . '</td>
		<td><input class="number" tabindex=3 ' . (in_array('QuantityBreak',$Errors) ? 'class="inputerror"' : '') .' type="text" name="QuantityBreak" size=10 maxlength=10></td></tr>';

echo '<tr><td>' . _('Discount Rate') . ' (%):</td>
		<td><input class="number" tabindex=4 ' . (in_array('DiscountRate',$Errors) ? 'class="inputerror"' : '') . 'type="text" name="DiscountRate" size=11 maxlength=14></td></tr>';
echo '</table><br />';

echo '<div class="centre"><input tabindex=5 type="submit" name="submit" value="' . _('Enter Information') . '"></div><br />';

$sql = "SELECT sales_type,
			salestype,
			discountcategory,
			quantitybreak,
			discountrate
		FROM discountmatrix INNER JOIN salestypes
			ON discountmatrix.salestype=salestypes.typeabbrev
		ORDER BY salestype,
			discountcategory,
			quantitybreak";

$result = DB_query($sql,$db);

echo '<table class=selection>';
echo '<tr>
		<th>' . _('Sales Type') . '</th>
		<th>' . _('Discount Category') . '</th>
		<th>' . _('Quantity Break') . '</th>
		<th>' . _('Discount Rate') . ' %' . '</th>
	</tr>';

$k=0; //row colour counter

while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k=1;
	}
	$DeleteURL = htmlspecialchars($_SERVER['PHP_SELF']) . '?Delete=yes&SalesType=' . $myrow['salestype'] . '&DiscountCategory=' . $myrow['discountcategory'] . '&QuantityBreak=' . $myrow['quantitybreak'];

	printf('<td>%s</td>
		<td>%s</td>
		<td class="number">%s</td>
		<td class="number">%s</td>
		<td><a href="%s" onclick="return confirm(\'' . _('Are you sure you wish to delete this discount matrix record?') . '\');">' . _('Delete') . '</td>
		</tr>',
		$myrow['sales_type'],
		$myrow['discountcategory'],
		$myrow['quantitybreak'],
		$myrow['discountrate']*100 ,
		$DeleteURL);

}

echo '</table>';

echo '</form>';

include('includes/footer.inc');
?>