<?php

$PageSecurity = 2;

/* $Revision: 1.8 $ */


If (isset($_POST['PrintPDF'])
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){

	include('config.php');
	include('includes/ConnectDB.inc');

	include('includes/PDFStarter_ros.inc');
	include('includes/DateFunctions.inc');

	$FontSize=12;
	$pdf->addinfo('Title',_('Aged Customer Balance Listing'));
	$pdf->addinfo('Subject',_('Aged Customer Balances'));

	$PageNumber=0;
	$line_height=12;

      /*Now figure out the aged analysis for the customer range under review */

	if ($_POST['All_Or_Overdues']=='All'){
		$SQL = "SELECT DebtorsMaster.DebtorNo, 
				DebtorsMaster.Name, 
				Currencies.Currency, 
				PaymentTerms.Terms,
				DebtorsMaster.CreditLimit, 
				HoldReasons.DissallowInvoices, 
				HoldReasons.ReasonDescription,
				Sum(
					DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc
				) AS Balance,
				Sum(
					CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate)) >= PaymentTerms.DaysBeforeDue  
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
						ELSE 0 END
					ELSE 
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= 0 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc ELSE 0 END
					END
				) AS Due,
				Sum(
					CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate)) > PaymentTerms.DaysBeforeDue AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays1 . ") 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= " . $PastDueDays1 . " 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
						ELSE 0 END
					END
				) AS Overdue1,
				Sum(
					CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
					THEN
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate)) > PaymentTerms.DaysBeforeDue AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays2 . ") 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc ELSE 0 END
					ELSE
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL(PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= " . $PastDueDays2 . " 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
						ELSE 0 END
					END
				) AS Overdue2
				FROM DebtorsMaster, 
					PaymentTerms, 
					HoldReasons, 
					Currencies, 
					DebtorTrans 
				WHERE DebtorsMaster.PaymentTerms = PaymentTerms.TermsIndicator 
					AND DebtorsMaster.CurrCode = Currencies.CurrAbrev 
					AND DebtorsMaster.HoldReason = HoldReasons.ReasonCode 
					AND DebtorsMaster.DebtorNo = DebtorTrans.DebtorNo
					AND DebtorsMaster.DebtorNo >= '" . $_POST['FromCriteria'] . "' 
					AND DebtorsMaster.DebtorNo <= '" . $_POST['ToCriteria'] . "' 
					AND DebtorsMaster.CurrCode ='" . $_POST['Currency'] . "'
				GROUP BY DebtorsMaster.DebtorNo, 
					DebtorsMaster.Name, 
					Currencies.Currency, 
					PaymentTerms.Terms, 
					PaymentTerms.DaysBeforeDue, 
					PaymentTerms.DayInFollowingMonth, 
					DebtorsMaster.CreditLimit, 
					HoldReasons.DissallowInvoices, 
					HoldReasons.ReasonDescription
				HAVING 
					Sum(DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc) <>0";

	} elseif ($_POST['All_Or_Overdues']=='OverduesOnly') {

	      $SQL = "SELECT DebtorsMaster.DebtorNo, 
	      		DebtorsMaster.Name, 
	      		Currencies.Currency, 
	      		PaymentTerms.Terms,
			DebtorsMaster.CreditLimit, 
	      		HoldReasons.DissallowInvoices, 
	      		HoldReasons.ReasonDescription,
			Sum(
	      			DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc
	      		) AS Balance,
			Sum(
	      			CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
	      				THEN
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= PaymentTerms.DaysBeforeDue  
	      					THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
	      					ELSE 0 END						
	      				ELSE
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= 0 )
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc ELSE 0 END
					END
	      		) AS Due,
			Sum(
		      		CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
	      				THEN
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) > PaymentTerms.DaysBeforeDue AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays1 . ") 
	      					THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= " . $PastDueDays1 . ") 
	      					THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
	      					ELSE 0 END
					END
	      		) AS Overdue1,
			Sum(
		      		CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
	      				THEN
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) > PaymentTerms.DaysBeforeDue AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays2 . ") 
	      					THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= " . $PastDueDays2 . ") 
	      					THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
	      					ELSE 0 END
					END
	      		) AS Overdue2
			FROM DebtorsMaster, 
	      			PaymentTerms, 
	      			HoldReasons, 
	      			Currencies, 
	      			DebtorTrans 
	      		WHERE DebtorsMaster.PaymentTerms = PaymentTerms.TermsIndicator 
	      		AND DebtorsMaster.CurrCode = Currencies.CurrAbrev 
	      		AND DebtorsMaster.HoldReason = HoldReasons.ReasonCode 
	      		AND DebtorsMaster.DebtorNo = DebtorTrans.DebtorNo
			AND DebtorsMaster.DebtorNo >= '" . $_POST['FromCriteria'] . "' 
	      		AND DebtorsMaster.DebtorNo <= '" . $_POST['ToCriteria'] . "' 
	      		AND DebtorsMaster.CurrCode ='" . $_POST['Currency'] . "'
			GROUP BY DebtorsMaster.DebtorNo, 
	      			DebtorsMaster.Name, 
	      			Currencies.Currency, 
	      			PaymentTerms.Terms, 
	      			PaymentTerms.DaysBeforeDue, 
	      			PaymentTerms.DayInFollowingMonth, 
	      			DebtorsMaster.CreditLimit, 
	      			HoldReasons.DissallowInvoices, 
	      			HoldReasons.ReasonDescription
			HAVING Sum(
				CASE WHEN (PaymentTerms.DaysBeforeDue > 0) 
	      				THEN
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) > PaymentTerms.DaysBeforeDue AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays1 . ") 
	      					THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
	      					ELSE 0 END
	      				ELSE
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= " . $PastDueDays1 . ") 
	      					THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
	      					ELSE 0 END
					END
	      			) > 0";

	} elseif ($_POST['All_Or_Overdues']=='HeldOnly'){

		$SQL = "SELECT DebtorsMaster.DebtorNo, 
					DebtorsMaster.Name, 
					Currencies.Currency, 
					PaymentTerms.Terms,
					DebtorsMaster.CreditLimit, 
					HoldReasons.DissallowInvoices, 
					HoldReasons.ReasonDescription,
			Sum(DebtorTrans.OvAmount + 
				DebtorTrans.OvGST + 
				DebtorTrans.OvFreight + 
				DebtorTrans.OvDiscount - 
				DebtorTrans.Alloc) AS Balance,
			Sum(
				CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
					THEN
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= PaymentTerms.DaysBeforeDue  
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
						ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate,INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= 0) 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
						ELSE 0 END
				END
			) AS Due,
			Sum(
				CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
					THEN
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) > PaymentTerms.DaysBeforeDue
						AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays1 . ") 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= " . $PastDueDays1 . ") 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
					ELSE 0 END
				END
			) AS Overdue1,
			Sum(
				CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
					THEN
						CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) > PaymentTerms.DaysBeforeDue 
						AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays2 . ") 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
						ELSE 0 END
					ELSE
						CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= ".$PastDueDays2 . ") 
						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
					ELSE 0 END
				END
			) AS Overdue2
		FROM DebtorsMaster, 
		PaymentTerms, 
		HoldReasons, 
		Currencies, 
		DebtorTrans 
		WHERE DebtorsMaster.PaymentTerms = PaymentTerms.TermsIndicator 
		AND DebtorsMaster.CurrCode = Currencies.CurrAbrev 
		AND DebtorsMaster.HoldReason = HoldReasons.ReasonCode 
		AND DebtorsMaster.DebtorNo = DebtorTrans.DebtorNo
		AND HoldReasons.DissallowInvoices=1
		AND DebtorsMaster.DebtorNo >= '" . $_POST['FromCriteria'] . "'
		AND DebtorsMaster.DebtorNo <= '" . $_POST['ToCriteria'] . "'
		AND DebtorsMaster.CurrCode ='" . $_POST['Currency'] . "'
		GROUP BY DebtorsMaster.DebtorNo, 
		DebtorsMaster.Name, 
		Currencies.Currency, 
		PaymentTerms.Terms, 
		PaymentTerms.DaysBeforeDue, 
		PaymentTerms.DayInFollowingMonth, 
		DebtorsMaster.CreditLimit, 
		HoldReasons.DissallowInvoices, 
		HoldReasons.ReasonDescription
		HAVING Sum(
			DebtorTrans.OvAmount + 
			DebtorTrans.OvGST + 
			DebtorTrans.OvFreight + 
			DebtorTrans.OvDiscount - 
			DebtorTrans.Alloc
		) <>0";
	}
	
	$CustomerResult = DB_query($SQL,$db,'','',False,False); /*dont trap errors handled below*/

	if (DB_error_no($db) !=0) {
		$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '.... ';
		include('includes/header.inc');
		echo '<P>' . _('The customer details could not be retrieved by the SQL because') . ' ' . DB_error_msg($db);
		echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
		if ($debug==1){
			echo "<BR>$SQL";
		}
		include('includes/footer.inc');
		exit;
	}

	include ('includes/PDFAgedDebtorsPageHeader.inc');

	$TotBal=0;
	$TotCur=0;
	$TotDue=0;
	$TotOD1=0;
	$TotOD2=0;

	While ($AgedAnalysis = DB_fetch_array($CustomerResult,$db)){

		$DisplayDue = number_format($AgedAnalysis['Due']-$AgedAnalysis['Overdue1'],2);
		$DisplayCurrent = number_format($AgedAnalysis['Balance']-$AgedAnalysis['Due'],2);
		$DisplayBalance = number_format($AgedAnalysis['Balance'],2);
		$DisplayOverdue1 = number_format($AgedAnalysis['Overdue1']-$AgedAnalysis['Overdue2'],2);
		$DisplayOverdue2 = number_format($AgedAnalysis['Overdue2'],2);

		$TotBal += $AgedAnalysis['Balance'];
		$TotDue += ($AgedAnalysis['Due']-$AgedAnalysis['Overdue1']);
		$TotCurr += ($AgedAnalysis['Balance']-$AgedAnalysis['Due']);
		$TotOD1 += ($AgedAnalysis['Overdue1']-$AgedAnalysis['Overdue2']);
		$TotOD2 += $AgedAnalysis['Overdue2'];

		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,220-$Left_Margin,$FontSize,$AgedAnalysis['DebtorNo'] . ' - ' . $AgedAnalysis['Name'],'left');
		$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
		$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
		$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
		$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
		$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');

		$YPos -=$line_height;
		if ($YPos < $Bottom_Margin + $line_height){
		      include('includes/PDFAgedDebtorsPageHeader.inc');
		}


		if ($_POST['DetailedReport']=='Yes'){

		   /*draw a line under the customer aged analysis*/
		   $pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);

$sql = "SELECT SysTypes.TypeName, 
			   			DebtorTrans.TransNo, 
			   			DebtorTrans.TranDate,
				   		(DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc) AS Balance,
						(CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
							THEN 
		   						(CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate)) >= PaymentTerms.DaysBeforeDue 
		   						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
		   						ELSE 0 END)
							ELSE 
		   						(CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= 0 
		   						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
		   						ELSE 0 END)
						END) AS Due,
						(CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
		   					THEN
								(CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) > PaymentTerms.DaysBeforeDue AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays1 . ") THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc ELSE 0 END)
		   					ELSE
								(CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= " . $PastDueDays1 . ") 
		   						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
		   						ELSE 0 END)
						END) AS Overdue1,
						(CASE WHEN (PaymentTerms.DaysBeforeDue > 0)
		   					THEN
								(CASE WHEN TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) > PaymentTerms.DaysBeforeDue AND TO_DAYS(Now()) - TO_DAYS(DebtorTrans.TranDate) >= (PaymentTerms.DaysBeforeDue + " . $PastDueDays2 . ") 
		   						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
		   						ELSE 0 END)
		 					ELSE
								(CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(DebtorTrans.TranDate, INTERVAL 1 MONTH), INTERVAL (PaymentTerms.DayInFollowingMonth - DAYOFMONTH(DebtorTrans.TranDate)) DAY)) >= " . $PastDueDays2 . ") 
		   						THEN DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc 
		   						ELSE 0 END)
						END) AS Overdue2
				   FROM DebtorsMaster, 
		   				PaymentTerms, 
		   				DebtorTrans, 
		   				SysTypes
				   WHERE SysTypes.TypeID = DebtorTrans.Type 
		   				AND DebtorsMaster.PaymentTerms = PaymentTerms.TermsIndicator 
		   				AND DebtorsMaster.DebtorNo = DebtorTrans.DebtorNo
				   		AND DebtorTrans.DebtorNo = '" . $AgedAnalysis[q1_DebtorNo] . "' 
		   				AND ABS(DebtorTrans.OvAmount + DebtorTrans.OvGST + DebtorTrans.OvFreight + DebtorTrans.OvDiscount - DebtorTrans.Alloc)>0.004";
		   
		   		   
		    $DetailResult = DB_query($sql,$db,'','',False,False); /*Dont trap errors */
		    if (DB_error_no($db) !=0) {
			$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '....';
			include('includes/header.inc');
			echo '<BR><BR>' . _('The details of outstanding transactions for customer') . ' - ' . $AgedAnalysis['DebtorNo'] . ' ' . _('could not be retrieved because') . ' - ' . DB_error_msg($db);
			echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
			if ($debug==1){
				echo '<BR>' . _('The SQL that failed was') . '<P>' . $sql;
			}
			include('includes/footer.inc');
			exit;
		    }

		    while ($DetailTrans = DB_fetch_array($DetailResult)){

		    	    $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,60,$FontSize,$DetailTrans['TypeName'],'left');
			    $LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,60,$FontSize,$DetailTrans['TransNo'],'left');
			    $DisplayTranDate = ConvertSQLDate($DetailTrans['TranDate']);
			    $LeftOvers = $pdf->addTextWrap($Left_Margin+125,$YPos,75,$FontSize,$DisplayTranDate,'left');

			    $DisplayDue = number_format($DetailTrans['Due']-$DetailTrans['Overdue1'],2);
			    $DisplayCurrent = number_format($DetailTrans['Balance']-$DetailTrans['Due'],2);
			    $DisplayBalance = number_format($DetailTrans['Balance'],2);
			    $DisplayOverdue1 = number_format($DetailTrans['Overdue1']-$DetailTrans['Overdue2'],2);
			    $DisplayOverdue2 = number_format($DetailTrans['Overdue2'],2);

			    $LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayBalance,'right');
			    $LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayCurrent,'right');
			    $LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayDue,'right');
			    $LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayOverdue1,'right');
			    $LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayOverdue2,'right');

			    $YPos -=$line_height;
			    if ($YPos < $Bottom_Margin + $line_height){
				$PageNumber++;
				include('includes/PDFAgedDebtorsPageHeader.inc');
			    }

		    } /*end while there are detail transactions to show */
		    $FontSize=8;
		    /*draw a line under the detailed transactions before the next customer aged analysis*/
		    $pdf->line($Page_Width-$Right_Margin, $YPos+10,$Left_Margin, $YPos+10);
		} /*Its a detailed report */
	} /*end customer aged analysis while loop */

	$YPos -=$line_height;
	if ($YPos < $Bottom_Margin + (2*$line_height)){
		$PageNumber++;
		include('includes/PDFAgedDebtorsPageHeader.inc');
	} elseif ($_POST['DetailedReport']=='Yes') {
		//dont do a line if the totals have to go on a new page
		$pdf->line($Page_Width-$Right_Margin, $YPos+10 ,220, $YPos+10);
	}

	$DisplayTotBalance = number_format($TotBal,2);
	$DisplayTotDue = number_format($TotDue,2);
	$DisplayTotCurrent = number_format($TotCurr,2);
	$DisplayTotOverdue1 = number_format($TotOD1,2);
	$DisplayTotOverdue2 = number_format($TotOD2,2);

	$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,$DisplayTotBalance,'right');
	$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,$DisplayTotCurrent,'right');
	$LeftOvers = $pdf->addTextWrap(340,$YPos,60,$FontSize,$DisplayTotDue,'right');
	$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$DisplayTotOverdue1,'right');
	$LeftOvers = $pdf->addTextWrap(460,$YPos,60,$FontSize,$DisplayTotOverdue2,'right');

	$buf = $pdf->output();
	$len = strlen($buf);

	if ($len < 1000) {
		$title = _('Aged Customer Account Analysis') . ' - ' . _('Problem Report') . '....';
		include('includes/header.inc');
		prnMsg(_('There are no customers meeting the critiera specified to list'),'info');
		if ($debug==1){
			prnMsg($SQL,'info');
		}
		echo "<BR><A HREF='$rootpath/index.php'>" . _('Back to the menu') . '</A>';
		include('includes/footer.inc');
		exit;
	}

	header('Content-type: application/pdf');
	header("Content-Length: $len");
	header('Content-Disposition: inline; filename=AgedDebtors.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->stream();

} else { /*The option to print PDF was not hit */

	include('includes/session.inc');
	$title=_('Aged Debtor Analysis');
	include('includes/header.inc');
	include('includes/SQL_CommonFunctions.inc');

	$CompanyRecord = ReadInCompanyRecord($db);

	if (strlen($_POST['FromCriteria'])<1 || strlen($_POST['ToCriteria'])<1) {

	/*if $FromCriteria is not set then show a form to allow input	*/

		echo '<FORM ACTION=' . $_SERVER['PHP_SELF'] . " METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('From Customer Code') . ':' . "</FONT></TD><TD><input Type=text maxlength=6 size=7 name=FromCriteria value='1'></TD></TR>";
		echo '<TR><TD>' . _('To Customer Code') . ':' . "</TD><TD><input Type=text maxlength=6 size=7 name=ToCriteria value='zzzzzz'></TD></TR>";

		echo '<TR><TD>' . _('All balances or overdues only') . ':' . "</TD><TD><SELECT name='All_Or_Overdues'>";
		echo "<OPTION SELECTED Value='All'>" . _('All customers with balances');
		echo "<OPTION Value='OverduesOnly'>" . _('Overdue accounts only');
		echo "<OPTION Value='HeldOnly'>" . _('Held accounts only');
		echo '</SELECT></TD></TR>';



		echo '<TR><TD>' . _('Only show customers trading in') . ':' . "</TD><TD><SELECT name='Currency'>";

		$sql = 'SELECT Currency, CurrAbrev FROM Currencies';

		$result=DB_query($sql,$db);


		while ($myrow=DB_fetch_array($result)){
		      if ($myrow['CurrAbrev'] == $CompanyRecord['CurrencyDefault']){
				echo "<OPTION SELECTED Value='" . $myrow['CurrAbrev'] . "'>" . $myrow['Currency'];
		      } else {
			      echo "<OPTION Value='" . $myrow['CurrAbrev'] . "'>" . $myrow['Currency'];
		      }
		}
		echo '</SELECT></TD></TR>';

		echo '<TR><TD>' . _('Summary or detailed report') . ':' . "</TD>
			<TD><SELECT name='DetailedReport'>";
		echo "<OPTION SELECTED Value='No'>" . _('Summary Report');
		echo "<OPTION Value='Yes'>" . _('Detailed Report');
		echo '</SELECT></TD></TR>';

		echo "</TABLE><INPUT TYPE=Submit Name='PrintPDF' Value='" . _('Print PDF') , "'></CENTER>";
	}
	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>