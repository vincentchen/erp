<?php
/* $Id$*/

include('includes/session.inc');
$title = _('Customer Type (Group) Notes');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['Id'])){
	$Id = (int)$_GET['Id'];
} else if (isset($_POST['Id'])){
	$Id = (int)$_POST['Id'];
}
if (isset($_POST['DebtorType'])){
	$DebtorType = $_POST['DebtorType'];
} elseif (isset($_GET['DebtorType'])){
	$DebtorType = $_GET['DebtorType'];
}
echo '<a href="' . $rootpath . '/SelectCustomer.php?DebtorType='.$DebtorType.'">' . _('Back to Select Customer') . '</a><br />';

if (isset($_POST['submit']) ) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (!is_long((integer)$_POST['priority'])) {
		$InputError = 1;
		prnMsg( _('The Contact priority must be an integer.'), 'error');
	} elseif (mb_strlen($_POST['note']) >200) {
		$InputError = 1;
		prnMsg( _('The contacts notes must be two hundred characters or less long'), 'error');
	} elseif( trim($_POST['note']) == '' ) {
		$InputError = 1;
		prnMsg( _('The contacts notes may not be empty'), 'error');
	}

	if ($Id and $InputError !=1) {

		$sql = "UPDATE debtortypenotes SET note='" . $_POST['note'] . "',
											date='" . FormatDateForSQL($_POST['date']) . "',
											href='" . $_POST['href'] . "',
											priority='" . $_POST['priority'] . "'
										WHERE typeid ='".$DebtorType."'
										AND noteid='".$Id."'";
		$msg = _('Customer Group Notes') . ' ' . $DebtorType  . ' ' . _('has been updated');
	} elseif ($InputError !=1) {

		$sql = "INSERT INTO debtortypenotes (typeid,
											href,
											note,
											date,
											priority)
									VALUES ('" . $DebtorType. "',
											'" . $_POST['href'] . "',
											'" . $_POST['note'] . "',
											'" . FormatDateForSQL($_POST['date']) . "',
											'" . $_POST['priority'] . "')";
		$msg = _('The contact group notes record has been added');
	}

	if ($InputError !=1) {
		$result = DB_query($sql,$db);

		echo '<br />';
		prnMsg($msg, 'success');
		unset($Id);
		unset($_POST['note']);
		unset($_POST['noteid']);
	}
} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'SalesOrders'

	$sql="DELETE FROM debtortypenotes
			WHERE noteid='".$Id."'
			AND typeid='".$DebtorType."'";
	$result = DB_query($sql,$db);

	echo '<br />';
	prnMsg( _('The contact group note record has been deleted'), 'success');
	unset($Id);
	unset($_GET['delete']);

}

if (!isset($Id)) {
	$SQLname="SELECT typename from debtortype where typeid='".$DebtorType."'";
	$result = DB_query($SQLname,$db);
	$myrow = DB_fetch_array($result);
	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/customer.png" title="' . _('Search') . '" alt="" />'  . _('Notes for Customer Type').': <b>' .$myrow['typename'].'</b></p>
		<br />';

	$sql = "SELECT noteid,
					typeid,
					href,
					note,
					date,
					priority
				FROM debtortypenotes
				WHERE typeid='".$DebtorType."'
				ORDER BY date DESC";
	$result = DB_query($sql,$db);
			//echo '<br />'.$sql;

	echo '<table class="selection">';
	echo '<tr>
			<th>' . _('Date') . '</th>
			<th>' . _('Note') . '</th>
			<th>' . _('href') . '</th>
			<th>' . _('Priority') . '</th>
		</tr>';

	$k=0; //row colour counter

	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			echo '<tr class="OddTableRows">';
			$k=0;
		} else {
			echo '<tr class="EvenTableRows">';
			$k=1;
		}
		printf('<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td><a href="%sId=%s&amp;DebtorType=%s">'. _('Edit') . '</a></td>
				<td><a href="%sId=%s&amp;DebtorType=%s&amp;delete=1">'. _('Delete') . '</a></td></tr>',
				$myrow['date'],
				$myrow['note'],
				$myrow['href'],
				$myrow['priority'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?',
				$myrow['noteid'],
				$myrow['typeid'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?',
				$myrow['noteid'],
				$myrow['typeid']);

	}
	//END WHILE LIST LOOP
	echo '</table>';
}
if (isset($Id)) {
	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?DebtorType=' . $DebtorType . '">' . _('Review all notes for this Customer Type') .'</a>
		</div>';
}

if (!isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?DebtorType='.$DebtorType.'">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	if (isset($Id)) {
		//editing an existing

		$sql = "SELECT noteid,
					typeid,
					href,
					note,
					date,
					priority
				FROM debtortypenotes
				WHERE noteid=".$Id."
					AND typeid='".$DebtorType."'";

		$result = DB_query($sql, $db);
				//echo '<br />'.$sql;

		$myrow = DB_fetch_array($result);

		$_POST['noteid'] = $myrow['noteid'];
		$_POST['note']	= $myrow['note'];
		$_POST['href']  = $myrow['href'];
		$_POST['date']  = $myrow['date'];
		$_POST['priority']  = $myrow['priority'];
		$_POST['typeid']  = $myrow['typeid'];
		echo '<input type="hidden" name="Id" value="'. $Id .'" />';
		echo '<input type="hidden" name="Con_ID" value="' . $_POST['noteid'] . '" />';
		echo '<input type="hidden" name="DebtorType" value="' . $_POST['typeid'] . '" />';
		echo '<table class="selection">
				<tr>
					<td>'. _('Note ID').':</td>
					<td>' . $_POST['noteid'] . '</td>
				</tr>';
	} else {
		echo '<table class="selection">';
		$_POST['noteid'] = '';
		$_POST['note']  = '';
		$_POST['href']  = '';
		$_POST['date']  = '';
		$_POST['priority']  = '';
		$_POST['typeid']  = '';
	}

	echo '<tr>
			<td>'._('Contact Group Note').':</td>
			<td><textarea name="note" rows="3" cols="32">'. $_POST['note'].'</textarea></td>
		</tr>
		<tr>
			<td>'. _('Web site').':</td>
			<td><input type="text" name="href" value="'. $_POST['href'].'" size="35" maxlength="100" /></td>
		</tr>
		<tr>
			<td>'. _('Date').':</td>
			<td><input type="text" name="date" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" value="'. $_POST['date']. '" size="10" maxlength="10" /></td>
		</tr>
		<tr>
			<td>'. _('Priority').':</td>
			<td><input type="text" name="priority" value="'. $_POST['priority'].'" size="1" maxlength="3" /></td>
		</tr>
		</table>
		<br />
		<div class="centre">
			<input type="submit" name="submit" value="'. _('Enter Information').'" />
		</div>
        </div>
		</form>';

} //end if record deleted no point displaying form to add record

include('includes/footer.inc');
?>