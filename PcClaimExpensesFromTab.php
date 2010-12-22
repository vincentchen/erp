<?php
/* $Revision: 1.0 $ */

//$PageSecurity = 6;

include('includes/session.inc');
$title = _('Claim Petty Cash Expenses From Tab');
include('includes/header.inc');


if (isset($_POST['SelectedTabs'])){
	$SelectedTabs = strtoupper($_POST['SelectedTabs']);
} elseif (isset($_GET['SelectedTabs'])){
	$SelectedTabs = strtoupper($_GET['SelectedTabs']);
}

if (isset($_POST['SelectedIndex'])){
	$SelectedIndex = $_POST['SelectedIndex'];
} elseif (isset($_GET['SelectedIndex'])){
	$SelectedIndex = $_GET['SelectedIndex'];
}

if (isset($_POST['Days'])){
	$Days = $_POST['Days'];
} elseif (isset($_GET['Days'])){
	$Days = $_GET['Days'];
}

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();

if (isset($_POST['submit'])) {
//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$i=1;

	if ($_POST['amount']==0) {
		$InputError = 1;
		prnMsg('<br>' . _('The Amount must be greater than 0'),'error');
		$Errors[$i] = 'TabCode';
		$i++;
	}

	if (isset($SelectedIndex) AND $InputError !=1)  {
		$sql = "UPDATE pcashdetails
			SET date = '".FormatDateForSQL($_POST['Date'])."',
			codeexpense = '" . $_POST['SelectedExpense'] . "',
			amount = '" .- $_POST['amount'] . "',
			notes = '" . $_POST['Notes'] . "',
			receipt = '" . $_POST['Receipt'] . "'
			WHERE counterindex = '".$SelectedIndex."'";

		$msg = _('The Expense Claim on Tab') . ' ' . $SelectedTabs . ' ' .  _('has been updated');

	} elseif ($InputError !=1 ) {

		// First check the type is not being duplicated
		// Add new record on submit

		$sql = "INSERT INTO pcashdetails
					(counterindex,
					tabcode,
					date,
					codeexpense,
					amount,
					authorized,
					posted,
					notes,
					receipt)
			VALUES ('','" . $_POST['SelectedTabs'] . "',
					'".FormatDateForSQL($_POST['Date'])."',
					'" . $_POST['SelectedExpense'] . "',
					'" .- $_POST['amount'] . "',
					'',
					'',
					'" . $_POST['Notes'] . "',
					'" . $_POST['Receipt'] . "'
					)";

		$msg = _('The Expense Claim on Tab') . ' ' . $_POST["SelectedTabs"] .  ' ' . _('has been created');
	}

	if ( $InputError !=1) {
		//run the SQL from either of the above possibilites
		$result = DB_query($sql,$db);
		prnMsg($msg,'success');

		unset($_POST['SelectedExpense']);
		unset($_POST['amount']);
		unset($_POST['Date']);
		unset($_POST['Notes']);
		unset($_POST['Receipt']);
	}

} elseif ( isset($_GET['delete']) ) {

	$sql="DELETE FROM pcashdetails
			WHERE counterindex='".$SelectedIndex."'";
	$ErrMsg = _('Petty Cash Expense record could not be deleted because');
	$result = DB_query($sql,$db,$ErrMsg);
	prnMsg(_('Petty cash Expense record') .  ' ' . $SelectedTabs  . ' ' . _('has been deleted') ,'success');

	unset($_GET['delete']);

}//end of get delete

if (!isset($SelectedTabs)){

	/* It could still be the first time the page has been run and a record has been selected for modification - SelectedTabs will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
	then none of the above are true and the list of sales types will be displayed with
	links to delete or edit each. These will call the same page again and allow update/input
	or deletion of the records*/
	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/money_add.png" title="' . _('Payment Entry')
	. '" alt="">' . ' ' . $title . '</p>';

	echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<p><table class=selection>'; //Main table

	echo '<tr><td>' . _('Petty Cash Tabs for User ') . $_SESSION['UserID'] . ":</td><td><select name='SelectedTabs'>";

	DB_free_result($result);
	$SQL = "SELECT tabcode
		FROM pctabs
		WHERE usercode='" . $_SESSION['UserID'] . "'";

	$result = DB_query($SQL,$db);

	while ($myrow = DB_fetch_array($result)) {
		if (isset($_POST['SelectTabs']) and $myrow['tabcode']==$_POST['SelectTabs']) {
			echo "<option selected VALUE='";
		} else {
			echo "<option VALUE='";
		}
		echo $myrow['tabcode'] . "'>" . $myrow['tabcode'];

	} //end while loop

	echo '</select></td></tr>';
   	echo '</td></tr></table>'; // close main table

	echo '<p><div class="centre"><input type=submit name=process VALUE="' . _('Accept') . '"><input type=submit name=Cancel VALUE="' . _('Cancel') . '"></div>';

	echo '</form>';

}

//end of ifs and buts!
if (isset($_POST['process'])OR isset($SelectedTabs)) {

	echo '<p><div class="centre"><a href="' . $_SERVER['PHP_SELF'] . '?' . SID . '">' . _('Petty Cash Tab ') . '' .$SelectedTabs. '<a/></div><p>';
/* RICARD */
	if (! isset($_GET['edit']) OR isset ($_POST['GO'])){
		echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo "<div class='centre'><p>" . _('Detail Of Movements For Last ') .': ';

		if(!isset ($Days)){
			$Days=30;
		}
		echo "<input type=hidden name='SelectedTabs' VALUE=" . $SelectedTabs . ">";
		echo "<input type=text class=number name='Days' VALUE=" . $Days . " MAXLENGTH =3 size=4> Days ";
		echo '<input type=submit name="Go" value="' . _('Go') . '">';
		echo '<p></div></form>';

		if (isset($_POST['Cancel'])) {
			unset($_POST['SelectedExpense']);
			unset($_POST['amount']);
			unset($_POST['Date']);
			unset($_POST['Notes']);
			unset($_POST['Receipt']);
		}

		$sql = "SELECT * FROM pcashdetails
				WHERE tabcode='".$SelectedTabs."'
					AND date >=DATE_SUB(CURDATE(), INTERVAL ".$Days." DAY)
				ORDER BY date, counterindex ASC";

		$result = DB_query($sql,$db);

		echo '<br><table BORDER=1>';
		echo "<tr>
			<th>" . _('Date Of Expense') . "</th>
			<th>" . _('Expense Description') . "</th>
			<th>" . _('Amount') . "</th>
			<th>" . _('Authorized') . "</th>
			<th>" . _('Notes') . "</th>
			<th>" . _('Receipt') . "</th>
		</tr>";

		$k=0; //row colour counter

		while ($myrow = DB_fetch_row($result)) {
			if ($k==1){
				echo '<tr class="EvenTableRows">';
				$k=0;
			} else {
				echo '<tr class="OddTableRows">';
				$k=1;
			}

			$sqldes="SELECT description
						FROM pcexpenses
						WHERE codeexpense='". $myrow['3'] . "'";

			$ResultDes = DB_query($sqldes,$db);
			$Description=DB_fetch_array($ResultDes);

			if (!isset($Description['0'])){
				$Description['0']='ASSIGNCASH';
			}

			if (($myrow['5'] == "0000-00-00") and ($Description['0'] != 'ASSIGNCASH')){
				// only movements NOT authorized can be modified or deleted
				printf("<td>%s</td>
					<td>%s</td>
					<td class=number>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td><a href='%sSelectedIndex=%s&SelectedTabs=$SelectedTabs&Days=$Days&edit=yes'>" . _('Edit') . "</td>
					<td><a href='%sSelectedIndex=%s&SelectedTabs=$SelectedTabs&Days=$Days&delete=yes' onclick=\"return confirm('" . _('Are you sure you wish to delete this code and the expense it may have set up?') . "');\">" . _('Delete') . "</td>
					</tr>",
					ConvertSQLDate($myrow['2']),
					$Description['0'],
					number_format($myrow['4'],2),
					ConvertSQLDate($myrow['5']),
					$myrow['7'],
					$myrow['8'],
					$_SERVER['PHP_SELF'] . '?' . SID, $myrow['0'],
					$_SERVER['PHP_SELF'] . '?' . SID, $myrow['0']);
			} else {
				printf("<td>%s</td>
					<td>%s</td>
					<td class=number>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					</tr>",
					ConvertSQLDate($myrow['2']),
					$Description['0'],
					number_format($myrow['4'],2),
					ConvertSQLDate($myrow['5']),
					$myrow['7'],
					$myrow['8']);

			}

		}
		//END WHILE LIST LOOP

		$sqlamount="SELECT sum(amount)
					FROM pcashdetails
					WHERE tabcode='".$SelectedTabs."'";

		$ResultAmount = DB_query($sqlamount,$db);
		$Amount=DB_fetch_array($ResultAmount);

		if (!isset($Amount['0'])) {
			$Amount['0']=0;
		}

		echo "<tr><td colspan=4 style=text-align:right >" . _('Current balance') . ":</td>
					<td colspan=2>".number_format($Amount['0'],2)."</td></tr>";


		echo '</table>';
		}

	if (! isset($_GET['delete'])) {

		echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<p><table border=1>'; //Main table
		echo '<td><table>'; // First column


		if ( isset($_GET['edit'])) {
			$sql = "SELECT *
				FROM pcashdetails
				WHERE counterindex='".$SelectedIndex."'";

			$result = DB_query($sql, $db);
			$myrow = DB_fetch_array($result);

			$_POST['Date'] = ConvertSQLDate($myrow['date']);
			$_POST['SelectedExpense'] = $myrow['codeexpense'];
			$_POST['Amount']  =  -$myrow['amount'];
			$_POST['Notes']  = $myrow['notes'];
			$_POST['Receipt']  = $myrow['receipt'];

			echo "<input type=hidden name='SelectedTabs' VALUE=" . $SelectedTabs . ">";
			echo "<input type=hidden name='SelectedIndex' VALUE=" . $SelectedIndex. ">";
			echo "<input type=hidden name='Days' VALUE=" .$Days. ">";

		}//end of Get Edit

		if (!isset($_POST['Date'])) {
			$_POST['Date']=Date("d/m/Y");
		}

		echo '<tr><td>' . _('Date Of Expense') . ":</td>";
		echo '<td><input type=text class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="Date" size=10 maxlength=10 value=' . $_POST['Date']. '></td></tr>';
		echo '<tr><td>' . _('Code Of Expense') . ":</td><td><select name='SelectedExpense'>";

		DB_free_result($result);

		$SQL = "SELECT pcexpenses.codeexpense,
					pcexpenses.description
			FROM pctabexpenses, pcexpenses, pctabs
			WHERE pctabexpenses.codeexpense = pcexpenses.codeexpense
				AND pctabexpenses.typetabcode = pctabs.typetabcode
				AND pctabs.tabcode = '".$SelectedTabs."'
			ORDER BY pcexpenses.codeexpense ASC";

		$result = DB_query($SQL,$db);

		while ($myrow = DB_fetch_array($result)) {
			if (isset($_POST['SelectedExpense']) and $myrow['codeexpense']==$_POST['SelectedExpense']) {
				echo "<option selected VALUE='";
			} else {
				echo "<option VALUE='";
			}
			echo $myrow['codeexpense'] . "'>" . $myrow['codeexpense'] . ' - ' . $myrow['description'];

		} //end while loop

		echo '</select></td></tr>';

		if (!isset($_POST['Amount'])) {
			$_POST['Amount']=0;
		}

		echo "<tr><td>" . _('Amount') . ":</td><td><input type='Text' class='number' name='amount' size='12' maxlength='11' value='" . $_POST['Amount'] . "'></td></tr>";

		if (!isset($_POST['Notes'])) {
			$_POST['Notes']='';
		}

		echo "<tr><td>" . _('Notes') . ":</td><td><input type='Text' name='Notes' size=50 maxlength=49 value='" . $_POST['Notes'] . "'></td></tr>";

		if (!isset($_POST['Receipt'])) {
			$_POST['Receipt']='';
		}

		echo "<tr><td>" . _('Receipt') . ":</td><td><input type='Text' name='Receipt' size=50 maxlength=49 value='" . $_POST['Receipt'] . "'></td></tr>";
		echo "<input type=hidden name='SelectedTabs' VALUE=" . $SelectedTabs . ">";
		echo "<input type=hidden name='Days' VALUE=" .$Days. ">";
		echo '</table>'; // close table in first column
		echo '</td></tr></table>'; // close main table
		echo '<p><div class="centre"><input type=submit name=submit VALUE="' . _('Accept') . '"><input type=submit name=Cancel VALUE="' . _('Cancel') . '"></div>';
		echo '</form>';

	} // end if user wish to delete

}

include('includes/footer.inc');
?>