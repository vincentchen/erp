<?php
/* $Revision: 1.3 $ */
/*This page adds the total of allocation records and compares this to the recorded allocation total in DebtorTrans table */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Customer Allocations != DebtorTrans.Alloc');
include('includes/header.inc');

/*First off get the DebtorTransID of all invoices where allocations dont agree to the recorded allocation */
$sql = "SELECT DebtorTrans.ID,
		DebtorTrans.DebtorNo,
		DebtorTrans.TransNo,
		OvAmount+OvGST AS TotAmt,
		Sum(CustAllocns.Amt) AS TotalAlloc,
		DebtorTrans.Alloc
	FROM DebtorTrans
		INNER JOIN CustAllocns ON DebtorTrans.ID=CustAllocns.TransID_AllocTo
	WHERE DebtorTrans.Type=10
	GROUP BY DebtorTrans.ID,
		DebtorTrans.Type=10,
		OvAmount+OvGST,
		DebtorTrans.Alloc
	HAVING Sum(CustAllocns.Amt) < DebtorTrans.Alloc - 1";

$result = DB_query($sql,$db);

if (DB_num_rows($result)==0){
	prnMsg(_('There are no inconsistencies with allocations. All is well!'),'info');
}

while ($myrow = DB_fetch_array($result)){
	$AllocToID = $myrow['ID'];

	echo '<BR>' . _('Allocations Made against') . ' ' . $myrow['DebtorNo'] . ' ' . _('Invoice Number') . ': ' . $myrow['TransNo'];
	echo '<BR>' . _('Orginal Invoice Total') . ': '. $myrow['TotAmt'];
	echo '<BR>' . _('Total amount recorded as allocated against it') . ': ' . $myrow['Alloc'];
	echo '<BR>' . _('Total of Allocation records') . ': ' . $myrow['TotalAlloc'];

	$sql = 'SELECT Type,
			TransNo,
			TranDate,
			DebtorTrans.DebtorNo,
			Reference,
			Rate,
			OvAmount+OvGST+OvFreight+OvDiscount AS TotalAmt,
			CustAllocns.Amt
		FROM DebtorTrans
			INNER JOIN CustAllocns ON DebtorTrans.ID=CustAllocns.TransID_AllocFrom
		WHERE CustAllocns.TransID_AllocTo='. $AllocToID;

	$ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because');
	$TransResult = DB_query($sql,$db,$ErrMsg);

	echo '<TABLE CELLPADDING=2 BORDER=2>';

	$tableheader = "<TR>
				<TD class='tableheader'>" . _('Type') . "</TD>
				<TD class='tableheader'>" . _('Number') . "</TD>
				<TD class='tableheader'>" . _('Reference') . "</TD>
				<TD class='tableheader'>" . _('Ex Rate') . "</TD>
				<TD class='tableheader'>" . _('Amount') . "</TD>
				<TD class='tableheader'>" . _('Alloc') . "</TD></TR>";
	echo $tableheader;

	$RowCounter = 1;
	$k = 0; //row colour counter
	$AllocsTotal = 0;

	while ($myrow1=DB_fetch_array($TransResult)) {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}

		if ($myrow1['Type']==11){
			$TransType = _('Credit Note');
		} else {
			$TransType = _('Receipt');
		}
		printf( "<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
			$TransType,
			$myrow1['TransNo'],
			$myrow1['Reference'],
			$myrow1['ExRate'],
			$myrow1['TotalAmt'],
			$myrow1['Amt']);

		$RowCounter++;
		If ($RowCounter == 12){
			$RowCounter=1;
			echo $tableheader;
		}
		//end of page full new headings if
		$AllocsTotal +=$myrow1['Amt'];
	}
	//end of while loop
	echo "<TR><TD COLSPAN = 6 ALIGN=RIGHT>" . number_format($AllocsTotal,2) . '</TD></TR>';
	echo '</TABLE><HR>';
}

echo '</FORM></CENTER>';

include('includes/footer.inc');

?>