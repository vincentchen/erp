<?php

/* $Id$ */

include('includes/session.inc');
$title = _('Maintenance Of Petty Cash Expenses For a Type Tab');
include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/money_add.png" title="' . _('Payment Entry')
	. '" alt="" />' . ' ' . $title . '</p>';

if (isset($_POST['SelectedType'])){
	$SelectedType = strtoupper($_POST['SelectedType']);
} elseif (isset($_GET['SelectedType'])){
	$SelectedType = strtoupper($_GET['SelectedType']);
} else {
	$SelectedType='';
}

if (!isset($_GET['delete']) and (ContainsIllegalCharacters($SelectedType) OR mb_strpos($SelectedType,' ')>0)){
	$InputError = 1;
	prnMsg(_('The petty cash tab type contain any of the following characters " \' - & or a space'),'error');
} 

if (isset($_POST['SelectedTab'])){
	$SelectedTab = strtoupper($_POST['SelectedTab']);
} elseif (isset($_GET['SelectedTab'])){
	$SelectedTab = strtoupper($_GET['SelectedTab']);
}

if (isset($_POST['Cancel'])) {
	unset($SelectedTab);
	unset($SelectedType);
}

if (isset($_POST['Process'])) {

	if ($_POST['SelectedTab'] == '') {
		echo prnMsg(_('You have not selected a tab to maintain the expenses on'),'error');
		echo '<br />';
		unset($SelectedTab);
		unset($_POST['SelectedTab']);
	}
}

if (isset($_POST['submit'])) {

	$InputError=0;
	
	if ($_POST['SelectedExpense']=='') {
		$InputError=1;
		echo prnMsg(_('You have not selected an expense to add to this tab'),'error');
		echo '<br />';
		unset($SelectedTab);
	}

	if ( $InputError !=1 ) {

		// First check the type is not being duplicated

		$checkSql = "SELECT count(*)
			     FROM pctabexpenses
			     WHERE typetabcode= '" .  $_POST['SelectedTab'] . "'
				 AND codeexpense = '" .  $_POST['SelectedExpense'] . "'";

		$checkresult = DB_query($checkSql,$db);
		$checkrow = DB_fetch_row($checkresult);

		if ( $checkrow[0] >0) {
			$InputError = 1;
			prnMsg( _('The Expense') . ' ' . $_POST['codeexpense'] . ' ' ._('already exists in this Type of Tab'),'error');
		} else {
			// Add new record on submit
			$sql = "INSERT INTO pctabexpenses (typetabcode,
												codeexpense)
										VALUES ('" . $_POST['SelectedTab'] . "',
												'" . $_POST['SelectedExpense'] . "')";

			$msg = _('Expense code:') . ' ' . $_POST['SelectedExpense'].' '._('for Type of Tab:') .' '. $_POST['SelectedTab'] .  ' ' . _('has been created');
			$checkSql = "SELECT count(typetabcode)
							FROM pctypetabs";
			$result = DB_query($checkSql, $db);
			$row = DB_fetch_row($result);
		}
	}

	if ( $InputError !=1) {
	//run the SQL from either of the above possibilites
		$result = DB_query($sql,$db);
		prnMsg($msg,'success');
		unset($_POST['SelectedExpense']);
	}

} elseif ( isset($_GET['delete']) ) {
	$sql="DELETE FROM pctabexpenses
		WHERE typetabcode='".$SelectedTab."'
		AND codeexpense='".$SelectedType."'";

	$ErrMsg = _('The Tab Type record could not be deleted because');
	$result = DB_query($sql,$db,$ErrMsg);
	prnMsg(_('Expense code').' '. $SelectedType .' '. _('for type of tab').' '. $SelectedTab .' '. _('has been deleted') ,'success');
	unset($_GET['delete']);
}

if (!isset($SelectedTab)){

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedType will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of sales types will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">'; //Main table
	
	echo '<tr><td>' . _('Select Type of Tab') . ':</td><td><select name="SelectedTab">';

	DB_free_result($result);
	$SQL = "SELECT typetabcode,
					typetabdescription
			FROM pctypetabs";

	$result = DB_query($SQL,$db);
	echo '<option value="">' . _('Not Yet Selected') . '</option>';
	while ($myrow = DB_fetch_array($result)) {
		if (isset($SelectedTab) and $myrow['typetabcode']==$SelectedTab) {
			echo '<option selected value="';
		} else {
			echo '<option value="';
		}
		echo $myrow['typetabcode'] . '">' . $myrow['typetabcode'] . ' - ' . $myrow['typetabdescription'] . '</option>';

	} //end while loop

	echo '</select></td></tr>';

	   	echo '</table>'; // close table in first column
   	echo '</td></tr></table>'; // close main table

	echo '<p><div class="centre"><input type=submit name="Process" value="' . _('Accept') . '">
				<input type=submit name="Cancel" value="' . _('Cancel') . '"></div>';

	echo '</form>';

}

//end of ifs and buts!
if (isset($_POST['process'])OR isset($SelectedTab)) {

	echo '<p><div class="centre"><a href="' . $_SERVER['PHP_SELF'] . '">' . _('Expense Codes for Type of Tab ') . ' ' .$SelectedTab. '</a></div><p>';
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	
	echo '<input type="hidden" name="SelectedTab" value="' . $SelectedTab . '">';

	$sql = "SELECT pctabexpenses.codeexpense, 
					pcexpenses.description
			FROM pctabexpenses INNER JOIN pcexpenses
			ON pctabexpenses.codeexpense=pcexpenses.codeexpense
			WHERE pctabexpenses.typetabcode='".$SelectedTab."'
			ORDER BY pctabexpenses.codeexpense ASC";

	$result = DB_query($sql,$db);

	echo '<br />
			<table class="selection">';
	echo '<tr><th colspan="3"><font size="2" color="navy">' . _('Expense Codes for Type of Tab ') . ' ' .$SelectedTab. '</font></th></tr>';
	echo '<tr>
			<th>' . _('Expense Code') . '</th>
			<th>' . _('Description') . '</th>
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

	printf('<td>%s</td>
			<td>%s</td>
			<td><a href="%s?SelectedType=%s&delete=yes&SelectedTab=' . $SelectedTab . '" onclick="return confirm(\'' . _('Are you sure you wish to delete this expense code?') . '\');">' . _('Delete') . '</td>
			</tr>',
			$myrow['codeexpense'],
			$myrow['description'],
			$_SERVER['PHP_SELF'], 
			$myrow['codeexpense'],
			$_SERVER['PHP_SELF'], 
			$myrow['codeexpense']);
	}
	//END WHILE LIST LOOP
	echo '</table>';

	if (! isset($_GET['delete'])) {

		
		echo '<br /><table  class="selection">'; //Main table
	
		echo '<tr><td>' . _('Select Expense Code') . ':</td><td><select name="SelectedExpense">';
	
		DB_free_result($result);
		$SQL = "SELECT codeexpense,
						description
				FROM pcexpenses";
	
		$result = DB_query($SQL,$db);
		if (!isset($_POST['SelectedExpense'])){
			echo '<option selected value="">' . _('Not Yet Selected') . '</option>';
		}
		while ($myrow = DB_fetch_array($result)) {
			if (isset($_POST['SelectedExpense']) AND $myrow['codeexpense']==$_POST['SelectedExpense']) {
				echo '<option selected value="';
			} else {
				echo '<option value="';
			}
			echo $myrow['codeexpense'] . '">' . $myrow['codeexpense'] . ' - ' . $myrow['description'] . '</option>';
	
		} //end while loop
	
		echo '</select></td></tr>';
	
	   	echo '</td></tr></table>'; // close main table
			
		echo '<p><div class="centre"><input type=submit name=submit value="' . _('Accept') . '">
									<input type=submit name="Cancel" value="' . _('Cancel') . '"></div>';
	
		echo '</form>';
	
	} // end if user wish to delete
}

include('includes/footer.inc');
?>