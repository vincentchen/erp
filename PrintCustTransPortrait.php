<?php

/* $Id$ */

include('includes/session.inc');

if (isset($_GET['FromTransNo'])) {
	$FromTransNo = filter_number_format($_GET['FromTransNo']);
} elseif (isset($_POST['FromTransNo'])){
	$FromTransNo = filter_number_format($_POST['FromTransNo']);
} else {
	$FromTransNo = '';
}

if (isset($_GET['InvOrCredit'])) {
	$InvOrCredit = $_GET['InvOrCredit'];
} elseif (isset($_POST['InvOrCredit'])) {
	$InvOrCredit = $_POST['InvOrCredit'];
}

if (isset($_GET['PrintPDF'])) {
	$PrintPDF = $_GET['PrintPDF'];
} elseif (isset($_POST['PrintPDF'])) {
	$PrintPDF = $_POST['PrintPDF'];
}

If (!isset($_POST['ToTransNo'])
	OR trim($_POST['ToTransNo'])==''
	OR filter_number_format($_POST['ToTransNo']) < $FromTransNo) {

	$_POST['ToTransNo'] = $FromTransNo;
}

$FirstTrans = $FromTransNo; /* Need to start a new page only on subsequent transactions */

If (isset($PrintPDF)
	and $PrintPDF!=''
	and isset($FromTransNo)
	and isset($InvOrCredit)
	and $FromTransNo!=''){

	include ('includes/class.pdf.php');


    $Page_Width=595;
    $Page_Height=842;
    $Top_Margin=30;
    $Bottom_Margin=30;
    $Left_Margin=40;
    $Right_Margin=30;

	$pdf = new Cpdf('P', 'pt', 'A4');
	$pdf->addInfo('Author','webERP ' . $Version);
	$pdf->addInfo('Creator','webERP http://www.weberp.org');

	if ($InvOrCredit=='Invoice'){
		$pdf->addInfo('Title',_('Sales Invoice') . ' ' . $FromTransNo . ' to ' . $_POST['ToTransNo']);
		$pdf->addInfo('Subject',_('Invoices from') . ' ' . $FromTransNo . ' ' . _('to') . ' ' . $_POST['ToTransNo']);
	} else {
		$pdf->addInfo('Title',_('Sales Credit Note') );
		$pdf->addInfo('Subject',_('Credit Notes from') . ' ' . $FromTransNo . ' ' . _('to') . ' ' . $_POST['ToTransNo']);
	}

	$pdf->setAutoPageBreak(0);
	$pdf->setPrintHeader(false);
	$pdf->AddPage();
	$pdf->cMargin = 0;

	$FirstPage = true;
	$line_height=16;

	while ($FromTransNo <= filter_number_format($_POST['ToTransNo'])){

	/*retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */

	// check if the user has set a default bank account for invoices, if not leave it blank
		$sql = "SELECT bankaccounts.invoice,
					bankaccounts.bankaccountnumber,
					bankaccounts.bankaccountcode
				FROM bankaccounts
				WHERE bankaccounts.invoice = '1'";
		$result=DB_query($sql,$db,'','',false,false);
		if (DB_error_no($db)!=1) {
			if (DB_num_rows($result)==1){
				$myrow = DB_fetch_array($result);
				$DefaultBankAccountNumber = _('Account:') .' ' .$myrow['bankaccountnumber'];
				$DefaultBankAccountCode =  _('Bank Code:') .' ' .$myrow['bankaccountcode'];
			} else {
				$DefaultBankAccountNumber = '';
				$DefaultBankAccountCode =  '';
			}
		} else {
			$DefaultBankAccountNumber = '';
			$DefaultBankAccountCode =  '';
		}
// gather the invoice data

		if ($InvOrCredit=='Invoice') {
			$sql = "SELECT debtortrans.trandate,
							debtortrans.ovamount,
							debtortrans.ovdiscount,
							debtortrans.ovfreight,
							debtortrans.ovgst,
							debtortrans.rate,
							debtortrans.invtext,
							debtortrans.consignment,
							debtorsmaster.name,
							debtorsmaster.address1,
							debtorsmaster.address2,
							debtorsmaster.address3,
							debtorsmaster.address4,
							debtorsmaster.address5,
							debtorsmaster.address6,
							debtorsmaster.currcode,
							debtorsmaster.invaddrbranch,
							debtorsmaster.taxref,
							paymentterms.terms,
							salesorders.deliverto,
							salesorders.deladd1,
							salesorders.deladd2,
							salesorders.deladd3,
							salesorders.deladd4,
							salesorders.deladd5,
							salesorders.deladd6,
							salesorders.customerref,
							salesorders.orderno,
							salesorders.orddate,
							locations.locationname,
							shippers.shippername,
							custbranch.brname,
							custbranch.braddress1,
							custbranch.braddress2,
							custbranch.braddress3,
							custbranch.braddress4,
							custbranch.braddress5,
							custbranch.braddress6,
							custbranch.brpostaddr1,
							custbranch.brpostaddr2,
							custbranch.brpostaddr3,
							custbranch.brpostaddr4,
							custbranch.brpostaddr5,
							custbranch.brpostaddr6,
							salesman.salesmanname,
							debtortrans.debtorno,
							debtortrans.branchcode,
							currencies.decimalplaces
						FROM debtortrans INNER JOIN debtorsmaster
						ON debtortrans.debtorno=debtorsmaster.debtorno
						INNER JOIN custbranch
						ON debtortrans.debtorno=custbranch.debtorno
						AND debtortrans.branchcode=custbranch.branchcode
						INNER JOIN salesorders
						ON debtortrans.order_ = salesorders.orderno
						INNER JOIN shippers
						ON debtortrans.shipvia=shippers.shipper_id
						INNER JOIN salesman
						ON custbranch.salesman=salesman.salesmancode
						INNER JOIN locations
						ON salesorders.fromstkloc=locations.loccode
						INNER JOIN paymentterms
						ON debtorsmaster.paymentterms=paymentterms.termsindicator
						INNER JOIN currencies
						ON debtorsmaster.currcode=currencies.currabrev
						WHERE debtortrans.type=10
						AND debtortrans.transno='" . $FromTransNo . "'";

			if (isset($_POST['PrintEDI']) and $_POST['PrintEDI']=='No') {
				$sql = $sql . " AND debtorsmaster.ediinvoices=0";
			}
		} else {
			$sql = "SELECT debtortrans.trandate,
							debtortrans.ovamount,
							debtortrans.ovdiscount,
							debtortrans.ovfreight,
							debtortrans.ovgst,
							debtortrans.rate,
							debtortrans.invtext,
							debtorsmaster.invaddrbranch,
							debtorsmaster.name,
							debtorsmaster.address1,
							debtorsmaster.address2,
							debtorsmaster.address3,
							debtorsmaster.address4,
							debtorsmaster.address5,
							debtorsmaster.address6,
							debtorsmaster.currcode,
							debtorsmaster.taxref,
							custbranch.brname,
							custbranch.braddress1,
							custbranch.braddress2,
							custbranch.braddress3,
							custbranch.braddress4,
							custbranch.braddress5,
							custbranch.braddress6,
							custbranch.brpostaddr1,
							custbranch.brpostaddr2,
							custbranch.brpostaddr3,
							custbranch.brpostaddr4,
							custbranch.brpostaddr5,
							custbranch.brpostaddr6,
							salesman.salesmanname,
							debtortrans.debtorno,
							debtortrans.branchcode,
							paymentterms.terms,
							currencies.decimalplaces
						FROM debtortrans INNER JOIN debtorsmaster
						ON debtortrans.debtorno=debtorsmaster.debtorno
						INNER JOIN custbranch
						ON debtortrans.debtorno=custbranch.debtorno
						AND debtortrans.branchcode=custbranch.branchcode
						INNER JOIN salesman
						ON custbranch.salesman=salesman.salesmancode
						INNER JOIN paymentterms
						ON debtorsmaster.paymentterms=paymentterms.termsindicator
						INNER JOIN currencies
						ON debtorsmaster.currcode=currencies.currabrev
						WHERE debtortrans.type=11
						AND debtortrans.transno='" . $FromTransNo . "'";


			if (isset($_POST['PrintEDI']) and $_POST['PrintEDI']=='No'){
				$sql = $sql . ' AND debtorsmaster.ediinvoices=0';
			}
		}

	   $result=DB_query($sql,$db,'','',false,false);

	   if (DB_error_no($db)!=0) {

			$title = _('Transaction Print Error Report');
			include ('includes/header.inc');

			prnMsg( _('There was a problem retrieving the invoice or credit note details for note number') . ' ' . $InvoiceToPrint . ' ' . _('from the database') . '. ' . _('To print an invoice, the sales order record, the customer transaction record and the branch record for the customer must not have been purged') . '. ' . _('To print a credit note only requires the customer, transaction, salesman and branch records be available'),'error');
			if ($debug==1){
			    prnMsg (_('The SQL used to get this information that failed was') . '<br />' . $sql,'error');
			}
			include ('includes/footer.inc');
			exit;
	    }
	    if (DB_num_rows($result)==1){
			$myrow = DB_fetch_array($result);

			$ExchRate = $myrow['rate'];

			if ($InvOrCredit == 'Invoice') {
				$sql = "SELECT stockmoves.stockid,
						stockmaster.description,
						-stockmoves.qty as quantity,
						stockmoves.discountpercent,
						((1 - stockmoves.discountpercent) * stockmoves.price * " . $ExchRate . "* -stockmoves.qty) AS fxnet,
						(stockmoves.price * " . $ExchRate . ") AS fxprice,
						stockmoves.narrative,
						stockmaster.controlled,
						stockmaster.serialised,
						stockmaster.units,
						stockmoves.stkmoveno,
						stockmaster.decimalplaces
					FROM stockmoves INNER JOIN stockmaster
					ON stockmoves.stockid = stockmaster.stockid
					WHERE stockmoves.type=10
					AND stockmoves.transno='" . $FromTransNo . "'
					AND stockmoves.show_on_inv_crds=1";
			} else {
				/* only credit notes to be retrieved */
				$sql = "SELECT stockmoves.stockid,
						stockmaster.description,
						stockmoves.qty as quantity,
						stockmoves.discountpercent,
						((1 - stockmoves.discountpercent) * stockmoves.price * " . $ExchRate . " * stockmoves.qty) AS fxnet,
						(stockmoves.price * " . $ExchRate . ") AS fxprice,
						stockmoves.narrative,
						stockmaster.controlled,
						stockmaster.serialised,
						stockmaster.units,
						stockmoves.stkmoveno,
						stockmaster.decimalplaces
					FROM stockmoves INNER JOIN stockmaster
					ON stockmoves.stockid = stockmaster.stockid
					WHERE stockmoves.type=11
					AND stockmoves.transno='" . $FromTransNo . "'
					AND stockmoves.show_on_inv_crds=1";
			} // end else

		$result=DB_query($sql,$db);
		if (DB_error_no($db)!=0) {
			$title = _('Transaction Print Error Report');
			include ('includes/header.inc');
			echo '<br />' . _('There was a problem retrieving the invoice or credit note stock movement details for invoice number') . ' ' . $FromTransNo . ' ' . _('from the database');
			if ($debug==1){
			    echo '<br />' . _('The SQL used to get this information that failed was') . '<br />' . $sql;
			}
			include('includes/footer.inc');
			exit;
		}


		if (DB_num_rows($result)>0){

			$FontSize = 10;
			$PageNumber = 1;

			include('includes/PDFTransPageHeaderPortrait.inc');
			$FirstPage = False;

		    while ($myrow2=DB_fetch_array($result)){
					if ($myrow2['discountpercent'] == 0) {
						$DisplayDiscount = '';
					} else {
						$DisplayDiscount = locale_number_format($myrow2['discountpercent'] * 100, 2) . '%';
						$DiscountPrice = $myrow2['fxprice'] * (1 - $myrow2['discountpercent']);
					}
				$DisplayNet = locale_number_format($myrow2['fxnet'],$myrow['decimalplaces']);
				$DisplayPrice = locale_number_format($myrow2['fxprice'],$myrow['decimalplaces']);
				$DisplayQty = locale_number_format($myrow2['quantity'],$myrow2['decimalplaces']);

				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,71,$FontSize,$myrow2['stockid']);
				$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,186,$FontSize,$myrow2['description']);
				$lines=1;

				while ($LeftOvers!='') {
					$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos-(10*$lines),186,$FontSize,$LeftOvers);
					$lines++;
				}

				$LeftOvers = $pdf->addTextWrap($Left_Margin+270,$YPos,76,$FontSize,$DisplayPrice,'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+350,$YPos,36,$FontSize,$DisplayQty,'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,26,$FontSize,$myrow2['units'],'center');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+420,$YPos,26,$FontSize,$DisplayDiscount,'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+450,$YPos,72,$FontSize,$DisplayNet,'right');

				if ($myrow2['controlled']==1){

					$GetControlMovts = DB_query("SELECT moveqty,
														serialno
												 FROM stockserialmoves
												 WHERE stockmoveno='" . $myrow2['stkmoveno'] . "'",$db);

					if ($myrow2['serialised']==1){
						while ($ControlledMovtRow = DB_fetch_array($GetControlMovts)){
							$YPos -= (10*$lines);
							$LeftOvers = $pdf->addTextWrap($Left_Margin+82,$YPos,100,$FontSize,$ControlledMovtRow['serialno'],'left');
							if ($YPos-$line_height <= $Bottom_Margin){
								/* head up a new invoice/credit note page */
								/*draw the vertical column lines right to the bottom */
								PrintLinesToBottom ();
	   		        			include ('includes/PDFTransPageHeaderPortrait.inc');
			   				} //end if need a new page headed up
						}
					} else {
						while ($ControlledMovtRow = DB_fetch_array($GetControlMovts)){
							$YPos -= (10*$lines);
							$LeftOvers = $pdf->addTextWrap($Left_Margin+82,$YPos,100,$FontSize,(-$ControlledMovtRow['moveqty']) . ' x ' . $ControlledMovtRow['serialno'],'left');
							if ($YPos-$line_height <= $Bottom_Margin){
								/* head up a new invoice/credit note page */
								/*draw the vertical column lines right to the bottom */
								PrintLinesToBottom ();
	   		        			include ('includes/PDFTransPageHeaderPortrait.inc');
			   				} //end if need a new page headed up
						}
					}
				}
				$YPos -= ($FontSize*$lines);

				$lines=explode('\r\n',htmlspecialchars_decode($myrow2['narrative']));
				for ($i=0;$i<sizeOf($lines);$i++) {
				while (mb_strlen($lines[$i])>1){
					if ($YPos-$line_height <= $Bottom_Margin){
						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
	   		        	include ('includes/PDFTransPageHeaderPortrait.inc');
			   		} //end if need a new page headed up
			   		/*increment a line down for the next line item */
			   		if (mb_strlen($lines[$i])>1){
						$lines[$i] = $pdf->addTextWrap($Left_Margin+85,$YPos,181,$FontSize,stripslashes($lines[$i]));
					}
					$YPos -= ($line_height);
				}
				}
				if ($YPos <= $Bottom_Margin){

					/* head up a new invoice/credit note page */
					/*draw the vertical column lines right to the bottom */
					PrintLinesToBottom ();
					include ('includes/PDFTransPageHeaderPortrait.inc');
				} //end if need a new page headed up
			} /*end while there are line items to print out*/

		} /*end if there are stock movements to show on the invoice or credit note*/

		$YPos -= $line_height;

		/* check to see enough space left to print the 4 lines for the totals/footer */
		if (($YPos-$Bottom_Margin)<(2*$line_height)){
			PrintLinesToBottom ();
			include ('includes/PDFTransPageHeaderPortrait.inc');
		}
		/*Print a column vertical line  with enough space for the footer*/
		/*draw the vertical column lines to 4 lines shy of the bottom
		to leave space for invoice footer info ie totals etc*/
		$pdf->line($Left_Margin+78, $TopOfColHeadings+12,$Left_Margin+78,$Bottom_Margin+(4*$line_height));

		/*Print a column vertical line */
		$pdf->line($Left_Margin+268, $TopOfColHeadings+12,$Left_Margin+268,$Bottom_Margin+(4*$line_height));

		/*Print a column vertical line */
		$pdf->line($Left_Margin+348, $TopOfColHeadings+12,$Left_Margin+348,$Bottom_Margin+(4*$line_height));

		/*Print a column vertical line */
		$pdf->line($Left_Margin+388, $TopOfColHeadings+12,$Left_Margin+388,$Bottom_Margin+(4*$line_height));

		/*Print a column vertical line */
		$pdf->line($Left_Margin+418, $TopOfColHeadings+12,$Left_Margin+418,$Bottom_Margin+(4*$line_height));

		$pdf->line($Left_Margin+448, $TopOfColHeadings+12,$Left_Margin+448,$Bottom_Margin+(4*$line_height));

		/*Rule off at bottom of the vertical lines */
		$pdf->line($Left_Margin, $Bottom_Margin+(4*$line_height),$Page_Width-$Right_Margin,$Bottom_Margin+(4*$line_height));

		/*Now print out the footer and totals */

		if ($InvOrCredit=='Invoice') {

		     $DisplaySubTot = locale_number_format($myrow['ovamount'],$myrow['decimalplaces']);
		     $DisplayFreight = locale_number_format($myrow['ovfreight'],$myrow['decimalplaces']);
		     $DisplayTax = locale_number_format($myrow['ovgst'],$myrow['decimalplaces']);
		     $DisplayTotal = locale_number_format($myrow['ovfreight']+$myrow['ovgst']+$myrow['ovamount'],$myrow['decimalplaces']);
		} else {
		     $DisplaySubTot = locale_number_format(-$myrow['ovamount'],$myrow['decimalplaces']);
		     $DisplayFreight = locale_number_format(-$myrow['ovfreight'],$myrow['decimalplaces']);
		     $DisplayTax = locale_number_format(-$myrow['ovgst'],$myrow['decimalplaces']);
		     $DisplayTotal = locale_number_format(-$myrow['ovfreight']-$myrow['ovgst']-$myrow['ovamount'],$myrow['decimalplaces']);
		}
	/*Print out the invoice text entered */
		$YPos = $Bottom_Margin+(3*$line_height);
	/* Print out the payment terms */

  		$pdf->addTextWrap($Left_Margin+5,$YPos+3,280,$FontSize,_('Payment Terms') . ': ' . $myrow['terms']);

		$FontSize =8;
		$LeftOvers=explode('\r\n',DB_escape_string($myrow['invtext']));
		for ($i=0;$i<sizeOf($LeftOvers);$i++) {
			$pdf->addText($Left_Margin+5, $YPos-8-($i*8), $FontSize, $LeftOvers[$i]);
		}
		$FontSize = 10;

		$pdf->addText($Page_Width-$Right_Margin-220, $YPos+12,$FontSize, _('Sub Total'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin+450,$YPos+5,72,$FontSize,$DisplaySubTot, 'right');

		$pdf->addText($Page_Width-$Right_Margin-220, ($YPos+12)-$line_height+5,$FontSize, _('Freight'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin+450,$YPos-$line_height+5,72,$FontSize,$DisplayFreight, 'right');

		$pdf->addText($Page_Width-$Right_Margin-220, ($YPos+12)-(2*$line_height)+5,$FontSize, _('Tax'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin+450,$YPos-(2*$line_height)+5,72, $FontSize,$DisplayTax, 'right');

		/*rule off for total */
		$pdf->line($Page_Width-$Right_Margin-222, $YPos-(2*$line_height),$Page_Width-$Right_Margin,$YPos-(2*$line_height));

		/*vertical to separate totals from comments and ROMALPA */
		$pdf->line($Page_Width-$Right_Margin-222, $YPos+$line_height,$Page_Width-$Right_Margin-222,$Bottom_Margin);

		$YPos+=10;
		if ($InvOrCredit=='Invoice'){
			$pdf->addText($Page_Width-$Right_Margin-220, ($YPos+6) - ($line_height*3),$FontSize, _('TOTAL INVOICE'));
			$FontSize=8;
			$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-18,280,$FontSize,$_SESSION['RomalpaClause']);
			while (mb_strlen($LeftOvers)>0 AND $YPos > $Bottom_Margin){
				$YPos -=10;
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-18,280,$FontSize,$LeftOvers);
			}
/* Add Images for Visa / Mastercard / Paypal */
			if (file_exists('companies/' . $_SESSION['DatabaseName'] . '/payment.jpg')) {
				$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/payment.jpg',$Page_Width/2 -60,$YPos-15,0,20);
			}
// Print Bank acount details if available and default for invoices is selected
            $pdf->addText($Page_Width-$Right_Margin-450, $YPos - ($line_height*3)+22,$FontSize, $DefaultBankAccountCode . '  ' . $DefaultBankAccountNumber);
			$FontSize=10;
		} else {
			$pdf->addText($Page_Width-$Right_Margin-220, ($YPos+6)-($line_height*3),$FontSize, _('TOTAL CREDIT'));
 		}
		$LeftOvers = $pdf->addTextWrap($Left_Margin+450,35,72, $FontSize,$DisplayTotal, 'right');
	    } /* end of check to see that there was an invoice record to print */

	    $FromTransNo++;
	} /* end loop to print invoices */

	if (isset($_GET['Email'])){ //email the invoice to address supplied
		include('includes/header.inc');

		include ('includes/htmlMimeMail.php');
		$FileName = $_SESSION['reports_dir'] . '/' . $_SESSION['DatabaseName'] . '_' . $InvOrCredit . '_' . $_GET['FromTransNo'] . '.pdf';
		$pdf->Output($FileName,'F');
		$mail = new htmlMimeMail();

		$Attachment = $mail->getFile($FileName);
		$mail->setText(_('Please find attached') . ' ' . $InvOrCredit . ' ' . $_GET['FromTransNo'] );
		$mail->SetSubject($InvOrCredit . ' ' . $_GET['FromTransNo']);
		$mail->addAttachment($Attachment, $FileName, 'application/pdf');
		$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
		$result = $mail->send(array($_GET['Email']));

		unlink($FileName); //delete the temporary file

		$title = _('Emailing') . ' ' .$InvOrCredit . ' ' . _('Number') . ' ' . $FromTransNo;
		include('includes/header.inc');
		echo '<p>' . $InvOrCredit . ' '  . _('number') . ' ' . $FromTransNo . ' ' . _('has been emailed to') . ' ' . $_GET['Email'];
		include('includes/footer.inc');
		exit;

	} else { //its not an email just print the invoice to PDF
		$pdf->OutputD($_SESSION['DatabaseName'] . '_' . $InvOrCredit . '_' . $FromTransNo . '.pdf');

	}
	$pdf->__destruct();
} else { /*The option to print PDF was not hit */

	$title=_('Select Invoices/Credit Notes To Print');
	include('includes/header.inc');

	if (!isset($FromTransNo) OR $FromTransNo=='') {

	/*if FromTransNo is not set then show a form to allow input of either a single invoice number or a range of invoices to be printed. Also get the last invoice number created to show the user where the current range is up to */

		echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" method="post">';
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

		echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/printer.png" title="' . _('Print') . '" alt="" />' . ' ' . _('Print Invoices or Credit Notes (Portrait Mode)') . '</p>';

		echo '<table class="selection">
				<tr>
					<td>' . _('Print Invoices or Credit Notes') . '</td>
					<td><select name="InvOrCredit">';

		if ($InvOrCredit=='Invoice' OR !isset($InvOrCredit)){

		   echo '<option selected value="Invoice">' . _('Invoices') . '</option>';
		   echo '<option value="Credit">' . _('Credit Notes') . '</option>';
		} else {
		   echo '<option selected value="Credit">' . _('Credit Notes') . '</option>';
		   echo '<option value="Invoice">' . _('Invoices') . '</option>';
		}
		echo '</select></td>
			</tr>';

		echo '<tr>
				<td>' . _('Print EDI Transactions') . '</td>
				<td><select name="PrintEDI">';

		if ($InvOrCredit=='Invoice' OR !isset($InvOrCredit)){

		   echo '<option selected value="No">' . _('Do not Print PDF EDI Transactions') . '</option>';
		   echo '<option value="Yes">' . _('Print PDF EDI Transactions Too') . '</option>';

		} else {

		   echo '<option value="No">' . _('Do not Print PDF EDI Transactions') . '</option>';
		   echo '<option selected value="Yes">' . _('Print PDF EDI Transactions Too') . '</option>';

		}

		echo '</select></td>
			</tr>';
		echo '<tr>
				<td>' . _('Start invoice/credit note number to print') . '</td>
				<td><input class="number" type="text" max="6" size="7" name="FromTransNo" /></td>
			</tr>';
		echo '<tr>
				<td>' . _('End invoice/credit note number to print') . '</td>
				<td><input class="number" type="text" max="6" size="7" name="ToTransNo" /></td>
			</tr>
			</table>';
		echo '<div class="centre">
				<br />
				<input type="submit" name="Print" value="' . _('Print Preview') . '" />
				<br />
				<input type="submit" name="PrintPDF" value="' . _('Print PDF') . '" />
			</div>';

		$sql = "SELECT typeno FROM systypes WHERE typeid=10";

		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);

		echo '<div class="page_help_text"><b>' . _('The last invoice created was number') . ' ' . $myrow[0] . '</b><br />' . _('If only a single invoice is required') . ', ' . _('enter the invoice number to print in the Start transaction number to print field and leave the End transaction number to print field blank') . '. ' . _('Only use the end invoice to print field if you wish to print a sequential range of invoices') . '';

		$sql = "SELECT typeno FROM systypes WHERE typeid='11'";

		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);

		echo '<br /><b>' . _('The last credit note created was number') . ' ' . $myrow[0] . '</b><br />' . _('A sequential range can be printed using the same method as for invoices above') . '. ' . _('A single credit note can be printed by only entering a start transaction number') . '</div>';

	} else {

		while ($FromTransNo <= $_POST['ToTransNo']){

	/*retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */

			if ($InvOrCredit=='Invoice') {

			   $sql = "SELECT debtortrans.trandate,
							debtortrans.ovamount,
							debtortrans.ovdiscount,
							debtortrans.ovfreight,
							debtortrans.ovgst,
							debtortrans.rate,
							debtortrans.invtext,
							debtortrans.consignment,
							debtorsmaster.name,
							debtorsmaster.address1,
							debtorsmaster.address2,
							debtorsmaster.address3,
							debtorsmaster.address4,
							debtorsmaster.address5,
							debtorsmaster.address6,
							debtorsmaster.currcode,
							salesorders.deliverto,
							salesorders.deladd1,
							salesorders.deladd2,
							salesorders.deladd3,
							salesorders.deladd4,
							salesorders.deladd5,
							salesorders.deladd6,
							salesorders.customerref,
							salesorders.orderno,
							salesorders.orddate,
							shippers.shippername,
							custbranch.brname,
							custbranch.braddress1,
							custbranch.braddress2,
							custbranch.braddress3,
							custbranch.braddress4,
							custbranch.braddress5,
							custbranch.braddress6,
							salesman.salesmanname,
							debtortrans.debtorno,
							currencies.decimalplaces
						FROM debtortrans INNER JOIN debtorsmaster
						ON debtortrans.debtorno=debtorsmaster.debtorno
						INNER JOIN custbranch
						ON debtortrans.debtorno=custbranch.debtorno
						AND debtortrans.branchcode=custbranch.branchcode
						INNER JOIN salesorders
						ON debtortrans.order_ = salesorders.orderno
						INNER JOIN shippers
						ON debtortrans.shipvia=shippers.shipper_id
						INNER JOIN salesman
						ON custbranch.salesman=salesman.salesmancode
						INNER JOIN locations
						ON salesorders.fromstkloc=locations.loccode
						INNER JOIN paymentterms
						ON debtorsmaster.paymentterms=paymentterms.termsindicator
						INNER JOIN currencies
						ON debtorsmaster.currcode=currencies.currabrev
						WHERE debtortrans.type=10
						AND debtortrans.transno='" . $FromTransNo . "'";
			} else { //its a credit note

			   $sql = "SELECT debtortrans.trandate,
					   		debtortrans.ovamount,
							debtortrans.ovdiscount,
							debtortrans.ovfreight,
							debtortrans.ovgst,
							debtortrans.rate,
							debtortrans.invtext,
							debtorsmaster.name,
							debtorsmaster.address1,
							debtorsmaster.address2,
							debtorsmaster.address3,
							debtorsmaster.address4,
							debtorsmaster.address5,
							debtorsmaster.address6,
							debtorsmaster.currcode,
							custbranch.brname,
							custbranch.braddress1,
							custbranch.braddress2,
							custbranch.braddress3,
							custbranch.braddress4,
							custbranch.braddress5,
							custbranch.braddress6,
							salesman.salesmanname,
							debtortrans.debtorno,
							currencies.decimalplaces
						FROM debtortrans INNER JOIN debtorsmaster
						ON debtortrans.debtorno=debtorsmaster.debtorno
						INNER JOIN custbranch
						ON debtortrans.debtorno=custbranch.debtorno
						AND debtortrans.branchcode=custbranch.branchcode
						INNER JOIN salesman
						ON custbranch.salesman=salesman.salesmancode
						INNER JOIN paymentterms
						ON debtorsmaster.paymentterms=paymentterms.termsindicator
						INNER JOIN currencies
						ON debtorsmaster.currcode=currencies.currabrev
						WHERE debtortrans.type=11
						AND debtortrans.transno='" . $FromTransNo . "'";

			}

			$result=DB_query($sql,$db);
			if (DB_num_rows($result)==0 OR DB_error_no($db)!=0) {
				echo '<p>' . _('There was a problem retrieving the invoice or credit note details for note number') . ' ' . $InvoiceToPrint . ' ' . _('from the database') . '. ' . _('To print an invoice, the sales order record, the customer transaction record and the branch record for the customer must not have been purged') . '. ' . _('To print a credit note only requires the customer, transaction, salesman and branch records be available');
				if ($debug==1){
					prnMsg( _('The SQL used to get this information that failed was') . '<br />' . $sql,'warn');
				}
				break;
				include('includes/footer.inc');
				exit;
			} elseif (DB_num_rows($result)==1){

				$myrow = DB_fetch_array($result);
	/* Then there's an invoice (or credit note) to print. So print out the invoice header and GST Number from the company record */
				if (count($_SESSION['AllowedPageSecurityTokens'])==1 AND in_array(1, $_SESSION['AllowedPageSecurityTokens']) AND $myrow['debtorno'] != $_SESSION['CustomerID']){
					echo '<p><font color="red" size="4">' . _('This transaction is addressed to another customer and cannot be displayed for privacy reasons') . '. ' . _('Please select only transactions relevant to your company');
					exit;
				}

				$ExchRate = $myrow['rate'];
				$PageNumber = 1;

				echo '<table class="table1">
						<tr>
							<td valign=top width=10%><img src="' . $_SESSION['LogoFile'] . '" /></td>
							<td bgcolor="#bbb"><b>';

				if ($InvOrCredit=='Invoice') {
				   echo '<font size="4">' . _('TAX INVOICE') . ' ';
				} else {
				   echo '<font color="red" size="4">' . _('TAX CREDIT NOTE') . ' ';
				}
				echo '</b>' . _('Number') . ' ' . $FromTransNo . '</font>
					<br /><font size="1">' . _('Tax Authority Ref') . '. ' . $_SESSION['CompanyRecord']['gstno'] . '</td>
					</tr>
					</table>';

	/*Now print out the logo and company name and address */
				echo '<table class"table1">
						<tr>
							<td><font size="4" color="#333"><b>' . $_SESSION['CompanyRecord']['coyname'] . '</b></font>
							<br />';
				echo $_SESSION['CompanyRecord']['regoffice1'] . '<br />';
				echo $_SESSION['CompanyRecord']['regoffice2'] . '<br />';
				echo $_SESSION['CompanyRecord']['regoffice3'] . '<br />';
				echo $_SESSION['CompanyRecord']['regoffice4'] . '<br />';
				echo $_SESSION['CompanyRecord']['regoffice5'] . '<br />';
				echo $_SESSION['CompanyRecord']['regoffice6'] . '<br />';
				echo _('Telephone') . ': ' . $_SESSION['CompanyRecord']['telephone'] . '<br />';
				echo _('Facsimile') . ': ' . $_SESSION['CompanyRecord']['fax'] . '<br />';
				echo _('Email') . ': ' . $_SESSION['CompanyRecord']['email'] . '<br />';

				echo '</td>
					<td width="50%" class="number">';

	/*Now the customer charged to details in a sub table within a cell of the main table*/

				echo '<table class="table1">
						<tr>
							<td align="left" bgcolor="#bbb"><b>' . _('Charge To') . ':</b></td>
						</tr>
						<tr>
							<td bgcolor="#eee">';
				echo $myrow['name'] .
					'<br />' . $myrow['address1'] .
					'<br />' . $myrow['address2'] .
					'<br />' . $myrow['address3'] .
					'<br />' . $myrow['address4'] .
					'<br />' . $myrow['address5'] .
					'<br />' . $myrow['address6'];

				echo '</td>
					</tr>
					</table>';
				/*end of the small table showing charge to account details */
				echo _('Page') . ': ' . $PageNumber;
				echo '</td>
					</tr>
					</table>';
				/*end of the main table showing the company name and charge to details */

				if ($InvOrCredit=='Invoice') {

				   echo '<table class="table1">
				   		<tr>
				   			<td align=left bgcolor="#bbb"><b>' . _('Charge Branch') . ':</b></td>
							<td align=left bgcolor="#bbb"><b>' . _('Delivered To') . ':</b></td>
						</tr>';
				   echo '<tr>
				   		<td bgcolor="#eee">' .$myrow['brname'] .
									'<br />' . $myrow['braddress1'] .
									'<br />' . $myrow['braddress2'] .
									'<br />' . $myrow['braddress3'] .
									'<br />' . $myrow['braddress4'] .
									'<br />' . $myrow['braddress5'] .
									'<br />' . $myrow['braddress6'] .
						'</td>';

				   echo '<td bgcolor="#eee">' . $myrow['deliverto'] .
									'<br />' . $myrow['deladd1'] .
									'<br />' . $myrow['deladd2'] .
									'<br />' . $myrow['deladd3'] .
									'<br />' . $myrow['deladd4'] .
									'<br />' . $myrow['deladd5'] .
									'<br />' . $myrow['deladd6'] .
						'</td>';
				   echo '</tr>
				   </table><hr>';

				   echo '<table class="table1">
				   		<tr>
							<td align=left bgcolor="#bbb"><b>' . _('Your Order Ref') . '</b></td>
							<td align=left bgcolor="#bbb"><b>' . _('Our Order No') . '</b></td>
							<td align=left bgcolor="#bbb"><b>' . _('Order Date') . '</b></td>
							<td align=left bgcolor="#bbb"><b>' . _('Invoice Date') . '</b></td>
							<td align=left bgcolor="#bbb"><b>' . _('Sales Person') . '</font></b></td>
							<td align=left bgcolor="#bbb"><b>' . _('Shipper') . '</b></td>
							<td align=left bgcolor="#bbb"><b>' . _('Consignment Ref') . '</b></td>
						</tr>';
				   	echo '<tr>
							<td bgcolor="#EEEEEE">' . $myrow['customerref'] . '</td>
							<td bgcolor="#EEEEEE">' .$myrow['orderno'] . '</td>
							<td bgcolor="#EEEEEE">' . ConvertSQLDate($myrow['orddate']) . '</td>
							<td bgcolor="#EEEEEE">' . ConvertSQLDate($myrow['trandate']) . '</td>
							<td bgcolor="#EEEEEE">' . $myrow['salesmanname'] . '</td>
							<td bgcolor="#EEEEEE">' . $myrow['shippername'] . '</td>
							<td bgcolor="#EEEEEE">' . $myrow['consignment'] . '</td>
						</tr>
					</table>';

				   $sql ="SELECT stockmoves.stockid,
						   		stockmaster.description,
								-stockmoves.qty as quantity,
								stockmoves.discountpercent,
								((1 - stockmoves.discountpercent) * stockmoves.price * " . $ExchRate . "* -stockmoves.qty) AS fxnet,
								(stockmoves.price * " . $ExchRate . ") AS fxprice,
								stockmoves.narrative,
								stockmaster.units,
								stockmaster.decimalplaces
							FROM stockmoves INNER JOIN stockmaster
							ON stockmoves.stockid = stockmaster.stockid
							WHERE stockmoves.type=10
							AND stockmoves.transno='" . $FromTransNo . "'
							AND stockmoves.show_on_inv_crds=1";

				} else { /* then its a credit note */

				   echo '<table width="50%">
						<tr>
							<td align=left bgcolor="#BBBBBB"><b>' . _('Branch') . ':</b></td>
						</tr>';
				   echo '<tr>
							<td bgcolor="#EEEEEE">' . $myrow['brname'] .
										'<br />' . $myrow['braddress1'] .
										'<br />' . $myrow['braddress2'] .
										'<br />' . $myrow['braddress3'] .
										'<br />' . $myrow['braddress4'] .
										'<br />' . $myrow['braddress5'] .
										'<br />' . $myrow['braddress6'] .
								'</td>
					</tr></table>';
				   echo '<hr>
						<table class="table1">
						<tr>
					   		<td align="left" bgcolor="#bbbbbb"><b>' . _('Date') . '</b></td>
							<td align=left bgcolor="#BBBBBB"><b>' . _('Sales Person') . '</font></b></td>
						</tr>';
				   echo '<tr>
					   		<td bgcolor="#EEEEEE">' . ConvertSQLDate($myrow['trandate']) . '</td>
							<td bgcolor="#EEEEEE">' . $myrow['salesmanname'] . '</td>
						</tr>
						</table>';


				   $sql ="SELECT stockmoves.stockid,
						   		stockmaster.description,
								stockmoves.qty as quantity,
								stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * " . $ExchRate . " * stockmoves.qty) AS fxnet,
								(stockmoves.price * " . $ExchRate . ") AS fxprice,
								stockmaster.units,
								stockmoves.narrative,
								stockmaster.decimalplaces
							FROM stockmoves INNER JOIN stockmaster
							ON stockmoves.stockid = stockmaster.stockid
							WHERE stockmoves.type=11
							AND stockmoves.transno='" . $FromTransNo . "'
							AND stockmoves.show_on_inv_crds=1";
				}

				echo '<hr>';
				echo '<div class="centre"><font size=2>' . _('All amounts stated in') . ' ' . $myrow['currcode'] . '</font></div>';

				$result=DB_query($sql,$db);
				if (DB_error_no($db)!=0) {
					echo '<br />' . _('There was a problem retrieving the invoice or credit note stock movement details for invoice number') . ' ' . $FromTransNo . ' ' . _('from the database');
					if ($debug==1){
						 echo '<br />' . _('The SQL used to get this information that failed was') . '<br />' . $sql;
					}
					exit;
				}

				if (DB_num_rows($result)>0){
					echo '<table class="table1">
							<tr>
								<th>' . _('Item Code') . '</th>
								<th>' . _('Item Description') . '</th>
								<th>' . _('Quantity') . '</th>
								<th>' . _('Unit') . '</th>
								<th>' . _('Price') . '</th>
								<th>' . _('Discount') . '</th>
								<th>' . _('Net') . '</th>
							</tr>';

					$LineCounter =17;
					$k=0;	//row colour counter

					while ($myrow2=DB_fetch_array($result)){

					      if ($k==1){
						  $RowStarter = '<tr class="EvenTableRows">';
						  $k=0;
					      } else {
						  $RowStarter = '<tr class="OddTableRows">';
						  $k=1;
					      }

					      echo $RowStarter;

					      $DisplayPrice =locale_number_format($myrow2['fxprice'],$myrow['decimalplaces']);
					      $DisplayQty = locale_number_format($myrow2['quantity'],$myrow2['decimalplaces']);
					      $DisplayNet = locale_number_format($myrow2['fxnet'],$myrow['decimalplaces']);

					      if ($myrow2['discountpercent']==0){
						   $DisplayDiscount ='';
					      } else {
						   $DisplayDiscount = locale_number_format($myrow2['discountpercent']*100,2) . '%';
					      }

					      printf ('<td>%s</td>
						      		<td>%s</td>
									<td class="number">%s</td>
									<td class="number">%s</td>
									<td class="number">%s</td>
									<td class="number">%s</td>
									<td class="number">%s</td>
									</tr>',
									$myrow2['stockid'],
									$myrow2['description'],
									$DisplayQty,
									$myrow2['units'],
									$DisplayPrice,
									$DisplayDiscount,
									$DisplayNet);

					      if (mb_strlen($myrow2['narrative'])>1){
					      		echo $RowStarter .
									'<td></td>
									<td colspan="6">' . $myrow2['narrative'] . '</td>
									</tr>';
							$LineCounter++;
					      }

					      $LineCounter++;

					      if ($LineCounter == ($_SESSION['PageLength'] - 2)){

						/* head up a new invoice/credit note page */

						   $PageNumber++;
						   echo '</table>
								<table class="table1">
								<tr>
									<td valign="top"><img src="' . $_SESSION['LogoFile'] . '" /></td>
									<td bgcolor="#bbb"><b>';

						   if ($InvOrCredit=='Invoice') {
							    echo '<font size="4">' . _('TAX INVOICE') . ' ';
						   } else {
							    echo '<font color="red" size="4">' . _('TAX CREDIT NOTE') . ' ';
						   }
						   echo '</b>' . _('Number') . ' ' . $FromTransNo . '</font><br /><font size=1>' . _('GST Number') . ' - ' . $_SESSION['CompanyRecord']['gstno'] . '</td>
							</tr>
							</table>';

	/*Now print out company name and address */
						    echo '<table class="table1">
									<tr>
										<td><font size=4 color="#333"><b>' . $_SESSION['CompanyRecord']['coyname'] . '</b></font><br />';
						    echo $_SESSION['CompanyRecord']['regoffice1'] . '<br />';
						    echo $_SESSION['CompanyRecord']['regoffice2'] . '<br />';
						    echo $_SESSION['CompanyRecord']['regoffice3'] . '<br />';
						    echo $_SESSION['CompanyRecord']['regoffice4'] . '<br />';
						    echo $_SESSION['CompanyRecord']['regoffice5'] . '<br />';
						    echo $_SESSION['CompanyRecord']['regoffice6'] . '<br />';
						    echo _('Telephone') . ': ' . $_SESSION['CompanyRecord']['telephone'] . '<br />';
						    echo _('Facsimile') . ': ' . $_SESSION['CompanyRecord']['fax'] . '<br />';
						    echo _('Email') . ': ' . $_SESSION['CompanyRecord']['email'] . '<br />';
						    echo '</td><td class="number">' . _('Page') . ': ' .  $PageNumber . '</td>
								</tr>
								</table>';
						    echo '<table class="table1">
									<tr>
										<th>' . _('Item Code') . '</th>
										<th>' . _('Item Description') . '</th>
										<th>' . _('Quantity') . '</th>
										<th>' . _('Unit') . '</th>
										<th>' . _('Price') . '</th>
										<th>' . _('Discount') . '</th>
										<th>' . _('Net') . '</th>
									</tr>';

						    $LineCounter = 10;

					      } //end if need a new page headed up
					} //end while there are line items to print out
					echo '</table>';
				} /*end if there are stock movements to show on the invoice or credit note*/

				/* check to see enough space left to print the totals/footer */
				$LinesRequiredForText = floor(mb_strlen($myrow['invtext'])/140);

				if ($LineCounter >= ($_SESSION['PageLength'] - 8 - $LinesRequiredForText)){

					/* head up a new invoice/credit note page */

					$PageNumber++;
					echo '<table class="table1">
							<tr>
								<td valign="top"><img src="' . $_SESSION['LogoFile'] . '" /></td>
								<td bgcolor="#bbb"><b>';
					if ($InvOrCredit=='Invoice') {
					      echo '<font size="4">' . _('TAX INVOICE') .' ';
					} else {
					      echo '<font color="red" size="4">' . _('TAX CREDIT NOTE') . ' ';
					}
					echo '</b>' . _('Number') . ' ' . $FromTransNo . '</font>
							<br />
							<font size="1">' . _('GST Number') . ' - ' . $_SESSION['CompanyRecord']['gstno'] . '</font></td>
						</tr>
						</table>';

	/*Print out the logo and company name and address */
					echo '<table class="table1">
							<tr>
								<td><font size=4 color="#333"><b>' . $_SESSION['CompanyRecord']['coyname'] . '</b></font><br />';
					echo $_SESSION['CompanyRecord']['regoffice1'] . '<br />';
					echo $_SESSION['CompanyRecord']['regoffice2'] . '<br />';
					echo $_SESSION['CompanyRecord']['regoffice3'] . '<br />';
					echo $_SESSION['CompanyRecord']['regoffice4'] . '<br />';
					echo $_SESSION['CompanyRecord']['regoffice5'] . '<br />';
					echo $_SESSION['CompanyRecord']['regoffice6'] . '<br />';
					echo _('Telephone') . ': ' . $_SESSION['CompanyRecord']['telephone'] . '<br />';
					echo _('Facsimile') . ': ' . $_SESSION['CompanyRecord']['fax'] . '<br />';
					echo _('Email') . ': ' . $_SESSION['CompanyRecord']['email'] . '<br />';
					echo '</td><td class="number">' . _('Page') . ': ' . $PageNumber . '</td>
						</tr>
						</table>';
					echo '<table class="table1">
							<tr>
								<th>' . _('Item Code') . '</th>
								<th>' . _('Item Description') . '</th>
								<th>' . _('Quantity') . '</th>
								<th>' . _('Unit') . '</th>
								<th>' . _('Price') . '</th>
								<th>' . _('Discount') . '</th>
								<th>' . _('Net') . '</th>
							</tr>
						</table>';

					$LineCounter = 10;
				}

	/*Space out the footer to the bottom of the page */

				echo '<br /><br />' . $myrow['invtext'];

				$LineCounter=$LineCounter+2+$LinesRequiredForText;
				while ($LineCounter < ($_SESSION['PageLength'] -6)){
					echo '<br />';
					$LineCounter++;
				}

	/*Now print out the footer and totals */

				if ($InvOrCredit=='Invoice') {

				   $DisplaySubTot = locale_number_format($myrow['ovamount'],$myrow['decimalplaces']);
				   $DisplayFreight = locale_number_format($myrow['ovfreight'],$myrow['decimalplaces']);
				   $DisplayTax = locale_number_format($myrow['ovgst'],$myrow['decimalplaces']);
				   $DisplayTotal = locale_number_format($myrow['ovfreight']+$myrow['ovgst']+$myrow['ovamount'],$myrow['decimalplaces']);
				} else {
				   $DisplaySubTot = locale_number_format(-$myrow['ovamount'],$myrow['decimalplaces']);
				   $DisplayFreight = locale_number_format(-$myrow['ovfreight'],$myrow['decimalplaces']);
				   $DisplayTax = locale_number_format(-$myrow['ovgst'],$myrow['decimalplaces']);
				   $DisplayTotal = locale_number_format(-$myrow['ovfreight']-$myrow['ovgst']-$myrow['ovamount'],$myrow['decimalplaces']);
				}
	/*Print out the invoice text entered */
				echo '<table class="table1"><tr>
					<td class="number">' . _('Sub Total') . '</td>
					<td class="number" bgcolor="#EEEEEE" width="15%">' . $DisplaySubTot . '</td></tr>';
				echo '<tr><td class="number">' . _('Freight') . '</td>
					<td class="number" bgcolor="#EEEEEE">' . $DisplayFreight . '</td></tr>';
				echo '<tr><td class="number">' . _('Tax') . '</td>
					<td class="number" bgcolor="#EEEEEE">' . $DisplayTax . '</td></tr>';
				if ($InvOrCredit=='Invoice'){
				     echo '<tr><td class="number"><b>' . _('TOTAL INVOICE') . '</b></td>
				     	<td class="number" bgcolor="#EEEEEE"><u><b>' . $DisplayTotal . '</b></u></td></tr>';
				} else {
				     echo '<tr><td class="number"><font color="red"><b>' . _('TOTAL CREDIT') . '</b></font></td>
				     		<td class="number" bgcolor="#EEEEEE"><font color="red"><u><b>' . $DisplayTotal . '</b></u></font></td></tr>';
				}
				echo '</table>';
			} /* end of check to see that there was an invoice record to print */
			$FromTransNo++;
		} /* end loop to print invoices */
	} /*end of if FromTransNo exists */
	include('includes/footer.inc');

} /*end of else not PrintPDF */



function PrintLinesToBottom () {

	global $pdf;
	global $PageNumber;
	global $TopOfColHeadings;
	global $Left_Margin;
	global $Bottom_Margin;
	global $line_height;

/*draw the vertical column lines right to the bottom */
	$pdf->line($Left_Margin+78, $TopOfColHeadings+12,$Left_Margin+78,$Bottom_Margin);

	/*Print a column vertical line */
	$pdf->line($Left_Margin+268, $TopOfColHeadings+12,$Left_Margin+268,$Bottom_Margin);

	/*Print a column vertical line */
	$pdf->line($Left_Margin+348, $TopOfColHeadings+12,$Left_Margin+348,$Bottom_Margin);

	/*Print a column vertical line */
	$pdf->line($Left_Margin+388, $TopOfColHeadings+12,$Left_Margin+388,$Bottom_Margin);

	/*Print a column vertical line */
	$pdf->line($Left_Margin+418, $TopOfColHeadings+12,$Left_Margin+418,$Bottom_Margin);

	$pdf->line($Left_Margin+448, $TopOfColHeadings+12,$Left_Margin+448,$Bottom_Margin);

	$PageNumber++;

}

?>