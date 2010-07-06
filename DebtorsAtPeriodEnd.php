<?php
$PageSecurity = 2;

/* $Id$*/

/* $Revision: 1.16 $ */
include('includes/session.inc');

if (isset($_POST['PrintPDF'])
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){

	include('includes/PDFStarter.php');
	$pdf->addInfo('Title',_('Customer Balance Listing'));
	$pdf->addInfo('Subject',_('Customer Balances'));
	$FontSize=12;
	$PageNumber=0;
	$line_height=12;

	/*Get the date of the last day in the period selected */

	$SQL = "SELECT lastdate_in_period FROM periods WHERE periodno = '" . $_POST['PeriodEnd']."'";
	$PeriodEndResult = DB_query($SQL,$db,_('Could not get the date of the last day in the period selected'));
	$PeriodRow = DB_fetch_row($PeriodEndResult);
	$PeriodEndDate = ConvertSQLDate($PeriodRow[0]);

	  /*Now figure out the aged analysis for the customer range under review */

	$SQL = "SELECT debtorsmaster.debtorno,
			debtorsmaster.name,
  			currencies.currency,
			SUM((debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/debtortrans.rate) AS balance,
			SUM(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc) AS fxbalance,
			SUM(CASE WHEN debtortrans.prd > '" . $_POST['PeriodEnd'] . "' THEN
			(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount)/debtortrans.rate ELSE 0 END) AS afterdatetrans,
			SUM(CASE WHEN debtortrans.prd > '" . $_POST['PeriodEnd'] . "'
				AND (debtortrans.type=11 OR debtortrans.type=12) THEN
				debtortrans.diffonexch ELSE 0 END) AS afterdatediffonexch,
			SUM(CASE WHEN debtortrans.prd > '" . $_POST['PeriodEnd'] . "' THEN
			debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount ELSE 0 END
			) AS fxafterdatetrans
			FROM debtorsmaster,
				currencies,
				debtortrans
			WHERE debtorsmaster.currcode = currencies.currabrev
			AND debtorsmaster.debtorno = debtortrans.debtorno
			AND debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
			AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'
			GROUP BY debtorsmaster.debtorno,
				debtorsmaster.name,
				currencies.currency";

	$CustomerResult = DB_query($SQL,$db,'','',false,false);

	if (DB_error_no($db) !=0) {
		$title = _('Customer Balances') . ' - ' . _('Problem Report');
		include('includes/header.inc');
		prnMsg(_('The customer details could not be retrieved by the SQL because') . DB_error_msg($db),'error');
		echo "<br><a href='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</a>';
		if ($debug==1){
			echo "<br>$SQL";
		}
		include('includes/footer.inc');
		exit;
	}

	if (DB_num_rows($CustomerResult) == 0) {
		$title = _('Customer Balances') . ' - ' . _('Problem Report');
		include('includes/header.inc');
		prnMsg(_('The customer details listing has no clients to report on'),'warn');
		echo "<br><a href='".$rootpath."'/index.php?.'" . SID . "'>" . _('Back to the menu') . "</a>";
		include('includes/footer.inc');
		exit;
	}

	include ('includes/PDFDebtorBalsPageHeader.inc');

	$TotBal=0;

	while ($DebtorBalances = DB_fetch_array($CustomerResult,$db)){

		$Balance = $DebtorBalances['balance'] - $DebtorBalances['afterdatetrans'] + $DebtorBalances['afterdatediffonexch'] ;
		$FXBalance = $DebtorBalances['fxbalance'] - $DebtorBalances['fxafterdatetrans'];

		if (abs($Balance)>0.009 OR ABS($FXBalance)>0.009) {

			$DisplayBalance = number_format($DebtorBalances['balance'] - $DebtorBalances['afterdatetrans'],2);
			$DisplayFXBalance = number_format($DebtorBalances['fxbalance'] - $DebtorBalances['fxafterdatetrans'],2);

			$TotBal += $Balance;

			$LeftOvers = $pdf->addTextWrap($Left_Margin+3,$YPos,220-$Left_Margin,$FontSize,$DebtorBalances['debtorno'] .
				' - ' . html_entity_decode($DebtorBalances['name']),'left');
			$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
			$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayFXBalance,'right');
			$LeftOvers = $pdf->addTextWrap(350,$YPos,100,$FontSize,$DebtorBalances['currency'],'left');


			$YPos -=$line_height;
			if ($YPos < $Bottom_Margin + $line_height){
				include('includes/PDFDebtorBalsPageHeader.inc');
			}
		}
	} /*end customer aged analysis while loop */

	$YPos -=$line_height;
	if ($YPos < $Bottom_Margin + (2*$line_height)){
		$PageNumber++;
		include('includes/PDFDebtorBalsPageHeader.inc');
	}

	$DisplayTotBalance = number_format($TotBal,2);

	$LeftOvers = $pdf->addTextWrap(50,$YPos,160,$FontSize,_('Total balances'),'left');
	$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayTotBalance,'right');
	/* UldisN
	$buf = $pdf->output();
	$len = strlen($buf);

	header('Content-type: application/pdf');
	header("Content-Length: ".$len);
	header('Content-Disposition: inline; filename=DebtorBals.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->stream();
	*/
	$pdf->OutputD($_SESSION['DatabaseName'] . '_DebtorBals_' . date('Y-m-d').'.pdf');//UldisN
	$pdf->__destruct(); //UldisN

} else { /*The option to print PDF was not hit */

	$title=_('Debtor Balances');
	include('includes/header.inc');
	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/customer.png" title="' . _('Search') .
	 '" alt="">' . ' ' . $title.'<br>';

	if (!isset($_POST['FromCriteria']) || !isset($_POST['ToCriteria'])) {

	/*if $FromCriteria is not set then show a form to allow input	*/

		echo '<form action=' . $_SERVER['PHP_SELF'] . " method='POST'><table class=selection>";

		echo '<tr><td>' . _('From Customer Code') .":</font></td><td><input tabindex=1 Type=text maxlength=6 size=7 name=FromCriteria value='1'></td></tr>";
		echo '<tr><td>' . _('To Customer Code') . ":</td><td><input tabindex=2 Type=text maxlength=6 size=7 name=ToCriteria value='zzzzzz'></td></tr>";

		echo '<tr><td>' . _('Balances As At') . ":</td><td><select tabindex=3 Name='PeriodEnd'>";

		$sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
		$Periods = DB_query($sql,$db,_('Could not retrieve period data because'),_('The SQL that failed to get the period data was'));

		while ($myrow = DB_fetch_array($Periods,$db)){

			echo '<option VALUE=' . $myrow['periodno'] . '>' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']);

		}
	}

	echo '</select></td></tr>';


	echo "</table><br><div class='centre'><input tabindex=5 type=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'></div>";

	include('includes/footer.inc');
} /*end of else not PrintPDF */

?>