<?php
/* $Revision: 1.3 $ */

$PageSecurity = 7;

include("includes/session.inc");

if (($_GET["Type"]=='Receipts') OR ($_POST["Type"]=='Receipts')){
	$Type = 'Receipts';
	$TypeName =_('Receipts');
	$title = _('Bank Account Deposits Matching');
} elseif (($_GET["Type"]=='Payments') OR ($_POST["Type"]=='Payments')) {
	$Type = 'Payments';
	$TypeName =_('Payments');
	$title = _('Bank Account Payments Matching');
} else {
	echo '<P>' . _('This page must be called with a bank transaction type. It should not be called directly.');
	include ('includes/footer.inc');
	exit;
}

include('includes/header.inc');
include('includes/DateFunctions.inc');

if (isset($_POST['Update']) AND $_POST['RowCounter']>1){
	for ($Counter=1;$Counter <= $_POST['RowCounter']; $Counter++){
		if ($_POST["Clear_" . $Counter]==True){
			/*Update the banktrans recoord to match it off */
			$sql = "UPDATE BankTrans SET AmountCleared=(Amount/ExRate) WHERE BankTransID=" . $_POST["BankTrans_" . $Counter];
			$ErrMsg =  _('Could not match off this payment beacause');
			$result = DB_query($sql,$db,$ErrMsg);

		} elseif (is_numeric((float) $_POST["AmtClear_" . $Counter]) AND (($_POST["AmtClear_" . $Counter]<0 AND $Type=='Payments') OR ($Type=='Receipts' AND ($_POST["AmtClear_" . $Counter]>0)))){
			/*if the amount entered was numeric and negative for a payment or positive for a receipt */
			$sql = "UPDATE BankTrans SET AmountCleared=" .  $_POST["AmtClear_" . $Counter] . " WHERE BankTransID=" . $_POST["BankTrans_" . $Counter];

			$ErrMsg = _('Could not update the amount matched off this bank transaction because');
			$result = DB_query($sql,$db,$ErrMsg);

		} elseif ($_POST["Unclear_" . $Counter]==True){
			$sql = "UPDATE BankTrans SET AmountCleared = 0 WHERE BankTransID=" . $_POST["BankTrans_" . $Counter];
			$ErrMsg =  _('Could not un-clear this bank transaction because');
			$result = DB_query($sql,$db,$ErrMsg);
		}
	}
 	/*Show the updated position with the same criteria as previously entered*/
 	$_POST["ShowTransactions"] = True;
}


echo "<FORM ACTION='". $_SERVER['PHP_SELF'] . "?" . SID . "' METHOD=POST>";

echo "<INPUT TYPE=HIDDEN Name=Type Value=$Type>";

echo '<TABLE><TR>';
echo '<TD ALIGN=RIGHT>' . _('Bank Account') . ':</TD><TD COLSPAN=3><SELECT name="BankAccount">';

$sql = "SELECT AccountCode, BankAccountName FROM BankAccounts";
$resultBankActs = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultBankActs)){
	if ($myrow["AccountCode"] == $_POST['BankAccount']){
	     echo "<OPTION SELECTED Value='" . $myrow["AccountCode"] . "'>" . $myrow["BankAccountName"];
	} else {
	     echo "<OPTION Value='" . $myrow["AccountCode"] . "'>" . $myrow["BankAccountName"];
	}
}

echo '</SELECT></TD></TR>';

if (!isset($_POST['BeforeDate']) OR !Is_Date($_POST['BeforeDate'])){
   $_POST['BeforeDate'] = Date($DefaultDateFormat);
}
if (!isset($_POST['AfterDate']) OR !Is_Date($_POST['AfterDate'])){
   $_POST['AfterDate'] = Date($DefaultDateFormat, Mktime(0,0,0,Date("m")-3,Date("d"),Date("y")));
}

echo '<TR><TD>' . _('Show') . ' ' . $TypeName . ' ' . _('before') . ':</TD><TD><INPUT TYPE=TEXT NAME="BeforeDate" SIZE=12 MAXLENGTH=12 Value="' . $_POST['BeforeDate'] . '"></TD>';
echo '<TD>' . _('But after') . ':</TD><TD><INPUT TYPE=TEXT NAME="AfterDate" SIZE=12 MAXLENGTH=12 Value="' . $_POST['AfterDate'] . '"></TD></TR>';
echo '<TR><TD COLSPAN=3>' . _('Choose Outstanding') . ' ' . $TypeName . ' ' . _('only or All') . ' ' . $TypeName . ' ' . _('in the date range') . ':</TD><TD><SELECT NAME="Ostg_or_All">';

if ($_POST["Ostg_or_All"]=='All'){
	echo '<OPTION SELECTED Value="All">' . _('Show All') . ' ' . $TypeName . ' ' . _('in the date range');
	echo '<OPTION Value="Ostdg">' . _('Show Only Un-matched') . ' ' . $TypeName;
} else {
	echo '<OPTION Value="All">' . _('Show All') . ' ' . $TypeName . ' ' . _('in the date range');
	echo '<OPTION SELECTED Value="Ostdg">' . _('Show Only Un-matched') . ' ' . $TypeName;
}
echo '</SELECT></TD></TR>';

echo '<TR><TD COLSPAN=3>' . _('Choose to display only the first 20 matching') . ' ' . $TypeName . ' ' . _('or all') . ' ' . $TypeName . ' ' . _('meeting the criteria') . ':</TD><TD><SELECT NAME="First20_or_All">';
if ($_POST["First20_or_All"]=='All'){
	echo '<OPTION SELECTED Value="All">' . _('Show All') . ' ' . $TypeName . ' ' . _('in the date range');
	echo '<OPTION Value="First20">' . _('Show Only The First 20') . ' ' . $TypeName;
} else {
	echo '<OPTION Value="All">' . _('Show All') . ' ' . $TypeName . _('in the date range');
	echo '<OPTION SELECTED Value="First20">' . _('Show Only The First 20') . ' ' . $TypeName;
}
echo '</SELECT></TD></TR>';


echo '</TABLE><CENTER><INPUT TYPE=SUBMIT NAME="ShowTransactions" VALUE="' . _('Show Selected') . ' ' . $TypeName . '">';
echo "<P><A HREF='$rootpath/BankReconciliation.php?" . SID . "'>" . _('Show Reconciliation') . '</A>';
echo '<HR>';

$InputError=0;
if (!Is_Date($_POST['BeforeDate'])){
	$InputError =1;
	echo '<P>' . _('The date entered for the field to show') . ' ' . $TypeName . ' ' . _('before, is not entered in a recognised date format. Entry is expected in the format') . ' ' . $DefaultDateFormat;
}
if (!Is_Date($_POST['AfterDate'])){
	$InputError =1;
	echo '<P>' . _('The date entered for the field to show') . ' ' . $Type . _('after, is not entered in a recognised date format. Entry is expected in the format') . ' ' . $DefaultDateFormat;
}

if ($InputError !=1 AND isset($_POST["BankAccount"]) AND $_POST["BankAccount"]!="" AND isset($_POST["ShowTransactions"])){

	$SQLBeforeDate = FormatDateForSQL($_POST['BeforeDate']);
	$SQLAfterDate = FormatDateForSQL($_POST['AfterDate']);

	if ($_POST["Ostg_or_All"]=='All'){
		if ($Type=='Payments'){
			$sql = "SELECT BankTransID, Ref, AmountCleared, TransDate, Amount/ExRate AS Amt, BankTransType FROM BankTrans WHERE Amount <0 AND TransDate >= '". $SQLAfterDate . "' AND TransDate <= '" . $SQLBeforeDate . "' AND BankAct=" .$_POST["BankAccount"] . " ORDER BY BankTransID";
		} else { /* Type must == Receipts */
			$sql = "SELECT BankTransID, Ref, AmountCleared, TransDate, Amount/ExRate AS Amt, BankTransType FROM BankTrans WHERE Amount >0 AND TransDate >= '". $SQLAfterDate . "' AND TransDate <= '" . $SQLBeforeDate . "' AND BankAct=" .$_POST["BankAccount"] . " ORDER BY BankTransID";
		}
	} else { /*it must be only the outstanding bank trans required */
		if ($Type=='Payments'){
			$sql = "SELECT BankTransID, Ref, AmountCleared, TransDate, Amount/ExRate AS Amt, BankTransType FROM BankTrans WHERE Amount <0 AND TransDate >= '". $SQLAfterDate . "' AND TransDate <= '" . $SQLBeforeDate . "' AND BankAct=" .$_POST["BankAccount"] . " AND  ABS(AmountCleared - (Amount / ExRate)) > 0.009 ORDER BY BankTransID";
		} else { /* Type must == Receipts */
			$sql = "SELECT BankTransID, Ref, AmountCleared, TransDate, Amount/ExRate AS Amt, BankTransType FROM BankTrans WHERE Amount >0 AND TransDate >= '". $SQLAfterDate . "' AND TransDate <= '" . $SQLBeforeDate . "' AND BankAct=" .$_POST["BankAccount"] . " AND  ABS(AmountCleared - (Amount / ExRate)) > 0.009 ORDER BY BankTransID";
		}
	}
	if ($_POST["First20_or_All"]!='All'){
		$sql = $sql . " LIMIT 20";
	}

	$ErrMsg = _('The payments with the selected criteria could not be retrieved because');
	$PaymentsResult = DB_query($sql, $db, $ErrMsg);

	$TableHeader = '<TR><TD class="tableheader">'
	               . _('Ref')
			 . '</TD><TD class="tableheader">' . $TypeName . '</TD>
			 <TD class="tableheader">' . _('Date') . '</TD>
			 <TD class="tableheader">' . _('Amount') . '</TD>
			 <TD class="tableheader">' . _('Outstanding') . '</TD>
			 <TD COLSPAN=3 ALIGN=CENTER class="tableheader">' . _('Clear  /  Unclear') . '</TD>
		</TR>';
	echo '<TABLE CELLPADDING=2 BORDER=2>' . $TableHeader;


	$j = 1;  //page length counter
	$k=0; //row colour counter
	$i = 1; //no of rows counter

	while ($myrow=DB_fetch_array($PaymentsResult)) {

		$DisplayTranDate = ConvertSQLDate($myrow["TransDate"]);
		$Outstanding = $myrow["Amt"]- $myrow["AmountCleared"];
		if (ABS($Outstanding)<0.009){ /*the payment is cleared dont show the check box*/

			printf("<tr bgcolor='#CCCEEE'>
			        <td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td COLSPAN=2 ALIGN=CENTER>%s</td>
				<td ALIGN=CENTER><INPUT TYPE='checkbox' NAME='Unclear_%s'><INPUT TYPE=HIDDEN NAME='BankTrans_%s' VALUE=%s></TD>
				</tr>",
				$myrow["Ref"],
				$myrow["BankTransType"],
				$DisplayTranDate,
				number_format($myrow["Amt"],2),
				number_format($Outstanding,2),
				_('Un-clear'),
				$i,
				$i,
				$myrow["BankTransID"]);

		} else{
			if ($k==1){
				echo "<tr bgcolor='#CCCCCC'>";
				$k=0;
			} else {
				echo "<tr bgcolor='#EEEEEE'>";
				$k=1;
			}

			printf("<td>%s</td>
			        <td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=CENTER><INPUT TYPE='checkbox' NAME='Clear_%s'><INPUT TYPE=HIDDEN NAME='BankTrans_%s' VALUE=%s></td>
				<td COLSPAN=2><INPUT TYPE='text' MAXLENGTH=15 SIZE=15 NAME='AmtClear_%s'></td>
				</tr>",
				$myrow["Ref"],
				$myrow["BankTransType"],
				$DisplayTranDate,
				number_format($myrow["Amt"],2),
				number_format($Outstanding,2),
				$i,
				$i,
				$myrow["BankTransID"],
				$i
			);
		}

		$j++;
		If ($j == 12){
			$j=1;
			echo $TableHeader;
		}
	//end of page full new headings if
		$i++;
	}
	//end of while loop

	echo "</TABLE><CENTER><INPUT TYPE=HIDDEN NAME='RowCounter' VALUE=$i><INPUT TYPE=SUBMIT NAME='Update' VALUE='" . _('Update Matching') . '></CENTER>';

}


echo '</form>';
include('includes/footer.inc');
?>
