<?php

/* $Id$*/

/*The supplier transaction uses the SuppTrans class to hold the information about the invoice or credit note
the SuppTrans class contains an array of GRNs objects - containing details of GRNs for invoicing/crediting and also
an array of GLCodes objects - only used if the AP - GL link is effective */

include('includes/DefineSuppTransClass.php');

/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');

$title = _('Supplier Transaction General Ledger Analysis');

include('includes/header.inc');

if (!isset($_SESSION['SuppTrans'])){
	prnMsg(_('To enter a supplier invoice or credit note the supplier must first be selected from the supplier selection screen') . ', ' . _('then the link to enter a supplier invoice or supplier credit note must be clicked on'),'info');
	echo '<br /><a href="' . $rootpath . '/SelectSupplier.php">' . _('Select A Supplier') . '</a>';
	include('includes/footer.inc');
	exit;
	/*It all stops here if there aint no supplier selected and transaction initiated ie $_SESSION['SuppTrans'] started off*/
}

/*If the user hit the Add to transaction button then process this first before showing  all GL codes on the transaction otherwise it wouldnt show the latest addition*/

if (isset($_POST['AddGLCodeToTrans']) AND $_POST['AddGLCodeToTrans'] == _('Enter GL Line')){

	$InputError = False;
	if ($_POST['GLCode'] == ''){
		$_POST['GLCode'] = $_POST['AcctSelection'];
	}

	if ($_POST['GLCode'] == ''){
		prnMsg( _('You must select a general ledger code from the list below') ,'warn');
		$InputError = True;
	}

	$sql = "SELECT accountcode,
			accountname
		FROM chartmaster
		WHERE accountcode='" . $_POST['GLCode'] . "'";
	$result = DB_query($sql, $db);
	if (DB_num_rows($result) == 0 and $_POST['GLCode'] != ''){
		prnMsg(_('The account code entered is not a valid code') . '. ' . _('This line cannot be added to the transaction') . '.<br />' . _('You can use the selection box to select the account you want'),'error');
		$InputError = True;
	} else if ($_POST['GLCode'] != '') {
		$myrow = DB_fetch_row($result);
		$GLActName = $myrow[1];
		if (!is_numeric($_POST['Amount'])){
			prnMsg( _('The amount entered is not numeric') . '. ' . _('This line cannot be added to the transaction'),'error');
			$InputError = True;
		} elseif ($_POST['JobRef'] != ''){
			$sql = "SELECT contractref FROM contracts WHERE contractref='" . $_POST['JobRef'] . "'";
			$result = DB_query($sql, $db);
			if (DB_num_rows($result) == 0){
				prnMsg( _('The contract reference entered is not a valid contract, this line cannot be added to the transaction'),'error');
				$InputError = True;
			}
		}
	}

	if ($InputError == False){
		
		$_SESSION['SuppTrans']->Add_GLCodes_To_Trans($_POST['GLCode'],
													$GLActName,
													$_POST['Amount'],
													$_POST['Narrative']);
		unset($_POST['GLCode']);
		unset($_POST['Amount']);
		unset($_POST['JobRef']);
		unset($_POST['Narrative']);
		unset($_POST['AcctSelection']);
	}
}

if (isset($_GET['Delete'])){
	$_SESSION['SuppTrans']->Remove_GLCodes_From_Trans($_GET['Delete']);
}

if (isset($_GET['Edit'])){
	$_POST['GLCode'] = $_SESSION['SuppTrans']->GLCodes[$_GET['Edit']]->GLCode;
	$_POST['AcctSelection']= $_SESSION['SuppTrans']->GLCodes[$_GET['Edit']]->GLCode;
	$_POST['Amount'] = $_SESSION['SuppTrans']->GLCodes[$_GET['Edit']]->Amount;
	$_POST['JobRef'] = $_SESSION['SuppTrans']->GLCodes[$_GET['Edit']]->JobRef;
	$_POST['Narrative'] = $_SESSION['SuppTrans']->GLCodes[$_GET['Edit']]->Narrative;
	$_SESSION['SuppTrans']->Remove_GLCodes_From_Trans($_GET['Edit']);
}

/*Show all the selected GLCodes so far from the SESSION['SuppInv']->GLCodes array */
if ($_SESSION['SuppTrans']->InvoiceOrCredit == 'Invoice'){
	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/transactions.png" title="' . _('General Ledger') . '" alt="" />' . ' '
	. _('General Ledger Analysis of Invoice From') . ' ' . $_SESSION['SuppTrans']->SupplierName;
} else {
	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/transactions.png" title="' . _('General Ledger') . '" alt="" />' . ' '
	. _('General Ledger Analysis of Credit Note From') . ' ' . $_SESSION['SuppTrans']->SupplierName;
}
echo '</p><table cellpadding=2 class=selection>';

$TableHeader = '<tr>
				<th>' . _('Account') . '</th>
				<th>' . _('Name') . '</th>
				<th>' . _('Amount') . '<br />' . _('in') . ' ' . $_SESSION['SuppTrans']->CurrCode . '</th>
				<th>' . _('Narrative') . '</th>
				</tr>';
echo $TableHeader;
$TotalGLValue=0;
$i=0;

foreach ( $_SESSION['SuppTrans']->GLCodes AS $EnteredGLCode){

	echo '<tr>
		<td>' . $EnteredGLCode->GLCode . '</td>
		<td>' . $EnteredGLCode->GLActName . '</td>
		<td class=number>' . locale_number_format($EnteredGLCode->Amount,$_SESSION['SuppTrans']->CurrDecimalPlaces) . '</td>
		<td>' . $EnteredGLCode->Narrative . '</td>
		<td><a href="' . $_SERVER['PHP_SELF'] . '?Edit=' . $EnteredGLCode->Counter . '">' . _('Edit') . '</a></td>
		<td><a href="' . $_SERVER['PHP_SELF'] . '?Delete=' . $EnteredGLCode->Counter . '">' . _('Delete') . '</a></td>
		</tr>';

	$TotalGLValue += $EnteredGLCode->Amount;

	$i++;
	if ($i>15){
		$i = 0;
		echo $TableHeader;
	}
}

echo '<tr>
	<td colspan=2 class=number><font size=4 color=blue>' . _('Total') . ':</font></td>
	<td class=number><font size=2 color=navy><u>' . locale_number_format($TotalGLValue,$_SESSION['SuppTrans']->CurrDecimalPlaces) . '</u></font></td>
	</tr>
	</table>';


if ($_SESSION['SuppTrans']->InvoiceOrCredit == 'Invoice'){
	echo '<br /><div class="centre"><a href="' . $rootpath . '/SupplierInvoice.php">' . _('Back to Invoice Entry') . '</a></div>';
} else {
	echo '<br /><div class="centre"><a href="' . $rootpath . '/SupplierCredit.php">' . _('Back to Credit Note Entry') . '</a></div>';
}

/*Set up a form to allow input of new GL entries */
echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<br /><table class="selection">';
if (!isset($_POST['GLCode'])) {
	$_POST['GLCode']='';
}
echo '<tr>
	<td>' . _('Account Code') . ':</td>
	<td><input type="text" name="GLCode" size=12 maxlength=11 VALUE="' .  $_POST['GLCode'] . '"></td>
	<input type="hidden" name="JobRef" value="">
	</tr>';
echo '<tr>
	<td>' . _('Account Selection') . ':<br />(' . _('If you know the code enter it above') . '<br />' . _('otherwise select the account from the list') . ')</td>
	<td><select name="AcctSelection">';

$sql = "SELECT accountcode, accountname FROM chartmaster ORDER BY accountcode";

$result = DB_query($sql, $db);
echo '<option value=""></option>';
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['accountcode'] == $_POST['AcctSelection']) {
		echo '<option selected value="';
	} else {
		echo '<option value="';
	}
	echo $myrow['accountcode'] . '">' . $myrow['accountcode'] . ' - ' . $myrow['accountname'] . '</option>';
}

echo '</select>
	</td>
	</tr>';
if (!isset($_POST['Amount'])) {
	$_POST['Amount']=0;
}
echo '<tr>
	<td>' . _('Amount') . ':</td>
	<td><input type="text" class="number" name="Amount" size="12" maxlength="11" value="' .  $_POST['Amount'] . '"></td>
	</tr>';

if (!isset($_POST['Narrative'])) {
	$_POST['Narrative']='';
}
echo '<tr>
	<td>' . _('Narrative') . ':</td>
	<td><textarea name="Narrative" cols=40 rows=2>' .  $_POST['Narrative'] . '</textarea></td>
	</tr>
	</table><br />';

echo '<div class="centre"><input type="submit" name="AddGLCodeToTrans" value="' . _('Enter GL Line') . '"></div>';

echo '</form>';
include('includes/footer.inc');
?>