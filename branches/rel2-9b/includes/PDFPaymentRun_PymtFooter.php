<?php
/* $Revision: 1.4 $ */
/*Code to print footer details for each supplier being paid and process payment total for each supplier
as necessary an include file used since the same code is used twice */
$YPos -= (0.5*$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);

$LeftOvers = $pdf->addTextWrap($Left_Margin+10,$YPos,340-$Left_Margin,$FontSize,_('Total Due For') . ' ' . $SupplierName, 'left');

$TotalPayments += $AccumBalance;
$TotalAccumDiffOnExch += $AccumDiffOnExch;

$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,number_format($AccumBalance,2), 'right');
$LeftOvers = $pdf->addTextWrap(405,$YPos,60,$FontSize,number_format($AccumDiffOnExch,2), 'right');


if (isset($_POST['PrintPDFAndProcess'])){

	if (is_numeric($_POST['Ref'])) {
		$PaytReference = $_POST['Ref'] + $RefCounter;
	} else {
		$PaytReference = $_POST['Ref'] . ($RefCounter + 1);
	}
	$RefCounter++;

	/*Do the inserts for the payment transaction into the Supp Trans table*/

	$SQL = "INSERT INTO SuppTrans (Type,
					TransNo,
					SuppReference,
					SupplierNo,
					TranDate,
					DueDate,
					Settled,
					Rate,
					OvAmount,
					DiffOnExch,
					Alloc) ";
	$SQL = $SQL .  "VALUES (22,
				" . $SuppPaymentNo . ",
				'" . $PaytReference . "',
				'" . $SupplierID . "',
				'" . FormatDateForSQL($_POST['AmountsDueBy']) . "',
				'" . FormatDateForSQL($_POST['AmountsDueBy']) . "',
				1,
				" . $_POST['ExRate'] . ",
				" . -$AccumBalance . ",
				" . -$AccumDiffOnExch . ",
				" . -$AccumBalance . ")";
	$ProcessResult = DB_query($SQL,$db,'','',false,false);
	if (DB_error_no($db) !=0) {
		$title = _("Payment Processing") . " - " . _("Problem Report") .".... ";
		include("includes/header.inc");
		echo _("None of the payments will be processed because the payment record for") . " " . $SupplierName . " " . _("could not be inserted because") . " - " . DB_error_msg($db);
		echo "<BR><A HREF='$rootpath/index.php'>" . _("Back to the menu") . "</A>";
		if ($debug==1){
			echo "<BR>" . _("The SQL that failed was") . " $SQL";
		}
		$SQL= "rollback";
		$ProcessResult = DB_query($SQL,$db);

		include("includes/footer.inc");
		exit;
	}

	$PaymentTransID = DB_Last_Insert_ID($db);

	/*Do the inserts for the allocation record against the payment for this charge */

	foreach ($Allocs AS $AllocTrans){ /*loop through the array of allocations */

		$SQL = 'INSERT INTO SuppAllocs (Amt,
						DateAlloc,
						TransID_AllocFrom,
						TransID_AllocTo)
				VALUES (' . $AllocTrans->Amount . ',
		                         "' . FormatDateForSQL($_POST['AmountsDueBy']) . '",
                	                ' . $PaymentTransID . ',
                        	        ' . $AllocTrans->TransID . ')';

		$ProcessResult = DB_query($SQL,$db);
		if (DB_error_no($db) !=0) {
			$title = _('Payment Processing - Problem Report') . '.... ';
			include('includes/header.inc');
			echo '<BR>' . _('None of the payments will be processed since an allocation record for') . $SupplierName . _('could not be inserted because') . ' - ' . DB_error_msg($db);
			echo '<BR><A HREF="' . $rootpath . '/index.php">' . _('Back to the menu') . '</A>';
			if ($debug==1){
				echo '<BR>' . _('The SQL that failed was') . $SQL;
			}
			$SQL= 'rollback';
			$ProcessResult = DB_query($SQL,$db);
			include('includes/footer.inc');
			exit;
		}
	} /*end of the loop to insert the allocation records */


	/*Do the inserts for the payment transaction into the BankTrans table*/
	$SQL="INSERT INTO BankTrans (BankAct,
					Ref,
					ExRate,
					TransDate,
					BankTransType,
					Amount) ";
   	$SQL = $SQL .  "VALUES ( " . $_POST['BankAccount'] . ",
				'" . $PaytReference . " " . $SupplierID . "',
				" . $_POST['ExRate'] . ",
				'" . FormatDateForSQL($_POST['AmountsDueBy']) . "',
				'" . $_POST['PaytType'] . "',
				" .  -$AccumBalance . ")";
	$ProcessResult = DB_query($SQL,$db,'','',false,false);
	if (DB_error_no($db) !=0) {
		$title = _("Payment Processing") . " - " . _("Problem Report") . ".... ";
		include("includes/header.inc");
		echo "<BR>" . _("None of the payments will be processed because the bank account payment record for") . " " . $SupplierName . " " . _("could not be inserted because") . " - " . DB_error_msg($db);
		echo "<BR><A HREF='$rootpath/index.php'>" . _("Back to the menu") . "</A>";
		if ($debug==1){
			echo "<BR>" . _("The SQL that failed was") . " $SQL";
		}
		$SQL= "rollback";
		$ProcessResult = DB_query($SQL,$db);

		include("includes/footer.inc");
		exit;
	}


	/*If the General Ledger Link is activated */
	if ($CompanyRecord["GLLink_Creditors"]==1){

		$PeriodNo = GetPeriod($_POST['AmountsDueBy'],$db);

		/*Do the GL trans for the payment CR bank */

		$SQL = "INSERT INTO GLTrans (Type,
						TypeNo,
						TranDate,
						PeriodNo,
						Account,
						Narrative,
						Amount )
				VALUES (22,
					" . $SuppPaymentNo . ",
					'" . FormatDateForSQL($_POST['AmountsDueBy']) . "',
					" . $PeriodNo . ",
					" . $_POST['BankAccount'] . ",
					'" . $SupplierID . " - " . $SupplierName . ' ' . _('payment run on') . ' ' . Date($DefaultDateFormat) . ' - ' . $PaytReference . "',
					" . (-$AccumBalance/ $_POST['ExRate']) . ")";

		$ProcessResult = DB_query($SQL,$db,'','',false,false);
		if (DB_error_no($db) !=0) {
			$title = _("Payment Processing") . " - " . _("Problem Report") . ".... ";
			include("includes/header.inc");
			echo "<BR>" . _("None of the payments will be processed since the general ledger posting for the payment to") . " " . $SupplierName . " " . _("could not be inserted because") . " - " . DB_error_msg($db);
			echo "<BR><A HREF='$rootpath/index.php'>" . _("Back to the menu") . "</A>";
			if ($debug==1){
				echo "<BR>" . _("The SQL that failed was") . " $SQL";
			}
			$SQL= "rollback";
			$ProcessResult = DB_query($SQL,$db);

			include("includes/footer.inc");
			exit;
		}

		/*Do the GL trans for the payment DR creditors */

		$SQL = 'INSERT INTO GLTrans (Type,
						TypeNo,
						TranDate,
						PeriodNo,
						Account,
						Narrative,
						Amount )
				VALUES (22,
					' . $SuppPaymentNo . ",
					'" . FormatDateForSQL($_POST['AmountsDueBy']) . "',
					" . $PeriodNo . ',
					' . $CompanyRecord['CreditorsAct'] . ",
					'" . $SupplierID . ' - ' . $SupplierName . ' ' . _('payment run on') . ' ' . Date($DefaultDateFormat) . ' - ' . $PaytReference . "',
					" . ($AccumBalance/ $_POST['ExRate']  + $AccumDiffOnExch) . ')';

		$ProcessResult = DB_query($SQL,$db,'','',false,false);
		if (DB_error_no($db) !=0) {
			$title = _('Payment Processing') . ' - ' . _('Problem Report') . '.... ';
			include('includes/header.inc');
			echo '<BR>' . _('None of the payments will be processed since the general ledger posting for the payment to') . ' ' . $SupplierName . ' ' . _('could not be inserted because') . ' - ' . DB_error_msg($db);
			echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
			if ($debug==1){
				echo '<BR>' . _('The SQL that failed was') . " $SQL";
			}
			$SQL= 'rollback';
			$ProcessResult = DB_query($SQL,$db);

			echo '</body</html>';
			exit;
		}

		/*Do the GL trans for the exch diff */
		if ($AccumDiffOnExch != 0){
			$SQL = 'INSERT INTO GLTrans (Type,
							TypeNo,
							TranDate,
							PeriodNo,
							Account,
							Narrative,
							Amount )
						VALUES (22,
							' . $SuppPaymentNo . ",
							'" . FormatDateForSQL($_POST['AmountsDueBy']) . "',
							" . $PeriodNo . ',
							' . $CompanyRecord['PurchasesExchangeDiffAct'] . ",
							'" . $SupplierID . ' - ' . $SupplierName . ' ' . _('payment run on') . ' ' . Date($DefaultDateFormat) . " - " . $PaytReference . "',
							" . (-$AccumDiffOnExch) . ")";

			$ProcessResult = DB_query($SQL,$db,'','',false,false);
			if (DB_error_no($db) !=0) {
				$title = _('Payment Processing') . ' - ' . _('Problem Report') . '.... ';
				include('includes/header.inc');
				echo '<BR>' . _('None of the payments will be processed since the general ledger posting for the exchange difference on') . ' ' . $SupplierName . ' ' . _('could not be inserted because') .' - ' . DB_error_msg($db);
				echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
				if ($debug==1){
					echo '<BR>' . _('The SQL that failed was') . " $SQL";
				}
				$SQL= 'rollback';
				$ProcessResult = DB_query($SQL,$db);

				include('includes/footer.inc');
				exit;
			}
		}
	} /*end if GL linked to creditors */


}

$YPos -= (1.5*$line_height);

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);

$YPos -= $line_height;

if ($YPos < $Bottom_Margin + $line_height){
	$PageNumber++;
	include('includes/PDFPaymentRunPageHeader.inc');
}

?>
