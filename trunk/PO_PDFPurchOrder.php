<?php
/* $Revision: 1.7 $ */

$PageSecurity = 2;
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/DateFunctions.inc');

if(!isset($_GET['OrderNo']) && !isset($_POST['OrderNo'])){
        $title = _('Select a Purchase Order');
        include('includes/header.inc');
        echo '<div align=center><br><br><br>';
        prnMsg( _('Select an Puchase Order Number to Print before calling this page') , 'error');
        echo '<BR><BR><BR><table class="table_index">
		<tr><td class="menu_group_item">
                <li><a href="'. $rootpath . '/PO_SelectOSPurchOrder.php?'.SID .'">' . _('Outstanding Purchase Orders') . '</a></li>
                <li><a href="'. $rootpath . '/PO_SelectPurchOrder.php?'. SID .'">' . _('Purchase Order Inquiry') . '</a></li>
                </td></tr></table></DIV><BR><BR><BR>';
        include('includes/footer.inc');
        exit();

	echo '<CENTER><BR><BR><BR>' . _('This page must be called with a purchase order number to print');
	echo '<BR><A HREF="'. $rootpath . '/index.php?' . SID . '">' . _('Back to the menu') . '</A></CENTER>';
	exit;
}

if (isset($_GET['OrderNo'])){
	$OrderNo = $_GET['OrderNo'];
} elseif (isset($_POST['OrderNo'])){
	$OrderNo = $_POST['OrderNo'];
}

$title = _('Print Purchase Order Number').' '. $OrderNo;

$ViewingOnly = 0;
if (isset($_GET['ViewingOnly']) && $_GET['ViewingOnly']!='') {
	$ViewingOnly = $_GET['ViewingOnly'];
} elseif (isset($_POST['ViewingOnly']) && $_POST['ViewingOnly']!='') {
	$ViewingOnly = $_POST['ViewingOnly'];
}


if (isset($_POST['DoIt'])  AND ($_POST['PrintOrEmail']=='Print' || $ViewingOnly==1) ){
	$MakePDFThenDisplayIt = True;
} elseif (isset($_POST['DoIt']) AND $_POST['PrintOrEmail']=='Email' AND strlen($_POST['EmailTo'])>6){
	$MakePDFThenEmailIt = True;
}

if (isset($OrderNo) && $OrderNo != "" && $OrderNo > 0){
	//Check this up front. Note that the myrow recordset is carried into the actual make pdf section
	/*retrieve the order details from the database to print */
	$ErrMsg = _('There was a problem retrieving the purchase order header details for Order Number'). ' ' . $OrderNo .
			' ' . _('from the database');
	$sql = "SELECT
			PurchOrders.SupplierNo,
			Suppliers.SuppName,
			Suppliers.Address1,
			Suppliers.Address2,
			Suppliers.Address3,
			Suppliers.Address4,
			PurchOrders.Comments,
			PurchOrders.OrdDate,
			PurchOrders.Rate,
			PurchOrders.DatePrinted,
			PurchOrders.DelAdd1,
			PurchOrders.DelAdd2,
			PurchOrders.DelAdd3,
			PurchOrders.DelAdd4,
			PurchOrders.AllowPrint,
			PurchOrders.RequisitionNo,
			PurchOrders.Initiator,
			Suppliers.CurrCode
		FROM PurchOrders INNER JOIN Suppliers
			ON PurchOrders.SupplierNo = Suppliers.SupplierID
		WHERE PurchOrders.OrderNo = " . $OrderNo;
	$result=DB_query($sql,$db, $ErrMsg);

	if (DB_num_rows($result)==0){ /*There is ony one order header returned */

		$title = _('Print Purchase Order Error');
		include('includes/header.inc');
		echo '<div align=center><br><br><br>';
		prnMsg( _('Unable to Locate Purchase Order Number') . ' : ' . $OrderNo . ' ', 'error');
		echo '<BR><BR><BR><table class="table_index">
			<tr><td class="menu_group_item">
	                <li><a href="'. $rootpath . '/PO_SelectOSPurchOrder.php?'.SID .'">' . _('Outstanding Purchase Orders') . '</a></li>
        	        <li><a href="'. $rootpath . '/PO_SelectPurchOrder.php?'. SID .'">' . _('Purchase Order Inquiry') . '</a></li>
                	</td></tr></table></DIV><BR><BR><BR>';
		include('includes/footer.inc');
		exit();

	} elseif (DB_num_rows($result)==1){ /*There is ony one order header returned */

	   $POHeader = DB_fetch_array($result);
	   if ($ViewingOnly==0) {
		   if ($POHeader['AllowPrint']==0){
			  $title = _('Purchase Order Already Printed');
			  include('includes/header.inc');
			  echo '<P>';
			  prnMsg( _('Purchase order number').' ' . $OrderNo . ' '.
				_('has previously been printed') . '. ' . _('It was printed on'). ' ' .
				ConvertSQLDate($POHeader['DatePrinted']) . '<BR>'.
				_('To re-print the order it must be modified to allow a reprint'). '<BR>'.
				_('This check is there to ensure that duplicate purchase orders are not sent to the supplier	resulting in several deliveries of the same supplies'), 'warn');
           echo '<BR><table class="table_index">
                <tr><td class="menu_group_item">
 					 <LI><A HREF="' . $rootpath . '/PO_PDFPurchOrder.php?' . SID . 'OrderNo=' . $OrderNo . '&ViewingOnly=1">'.
				_('Print This Order as a Copy'). '</A>
 				<LI><A HREF="' . $rootpath . '/PO_Header.php?' . SID . 'ModifyOrderNumber=' . $OrderNo . '">'.
				_('Modify the order to allow a real reprint'). '</A>' .
			  	'<LI><A HREF="'. $rootpath .'/PO_SelectPurchOrder.php?' . SID . '">'.
				_('Select another order'). '</A>'.
			  	'<LI><A HREF="' . $rootpath . '/index.php?' . SID . '">'. _('Back to the menu').'</A>';
			  echo '</body</html>';
			  include('includes/footer.inc');
			  exit;
		   }//AllowedToPrint
	   }//not ViewingOnly
	}// 1 valid record
}//if there is a valid order number

If ($MakePDFThenDisplayIt OR $MakePDFThenEmailIt){

	$PaperSize = 'A4_Landscape';

	include('includes/PDFStarter_ros.inc');

	$pdf->addinfo('Title', _('Purchase Order') );
	$pdf->addinfo('Subject', _('Purchase Order Number').' ' . $_GET['OrderNo']);

	$CompanyRecord = ReadInCompanyRecord ($db);
	if ($CompanyRecord==0){
	/*CompanyRecord will be 0 if the company information could not be retrieved */
	     exit;
	}

	$line_height=16;
	   /* Then there's an order to print and its not been printed already (or its been flagged for reprinting)
	   Now ... Has it got any line items */

	   $PageNumber = 1;
	   $ErrMsg = _('There was a problem retrieving the line details for order number') . ' ' . $OrderNo . ' ' .
			_('from the database');
	   $sql = "SELECT ItemCode,
	   			DeliveryDate,
				ItemDescription,
				UnitPrice,
				Units,
				QuantityOrd,
				DecimalPlaces
			FROM PurchOrderDetails LEFT JOIN StockMaster
				ON PurchOrderDetails.ItemCode=StockMaster.StockID
			WHERE OrderNo =" . $OrderNo;
	   $result=DB_query($sql,$db);

	   if (DB_num_rows($result)>0){
	   /*Yes there are line items to start the ball rolling with a page header */

		include('includes/PO_PDFOrderPageHeader.inc');

		$YPos-=$line_height;

		$OrderTotal = 0;

		while ($POLine=DB_fetch_array($result)){

			$sql = "SELECT SupplierDescription FROM PurchData WHERE StockID='" .
$POLine['ItemCode'] . "' AND SupplierNo ='" . $POHeader['SupplierNo'] . "'";
			$SuppDescRslt = DB_query($sql,$db);
			if (DB_error_no($db)==0){
				if (DB_num_rows($SuppDescRslt)==1){
					$SuppDescRow = DB_fetch_row($SuppDescRslt);
					if (strlen($SuppDescRow[0])>2){
						$ItemDescription = $SuppDescRow[0];
					} else {
						$ItemDescritpion = $POLine["ItemDescription"];
					}
				} else {
					$ItemDescritpion = $POLine["ItemDescription"];
				}

			} else {
				$ItemDescription = $POLine["ItemDescription"];
			}

			$DisplayQty = number_format($POLine['QuantityOrd'],$POLine['DecimalPlaces']);
			$DisplayPrice = number_format($POLine['UnitPrice'],2);
			$DisplayDelDate = ConvertSQLDate($POLine['DeliveryDate'],2);
			$DisplayLineTotal = number_format($POLine['UnitPrice']*$POLine['QuantityOrd'],2);

			$OrderTotal += ($POLine['UnitPrice']*$POLine['QuantityOrd']);

			$LeftOvers = $pdf->addTextWrap($Left_Margin+1,$YPos,64,$FontSize,$POLine['ItemCode'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+64+300,$YPos,85,$FontSize,$DisplayQty, 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+64+300+85+3,$YPos,37,$FontSize,$POLine['Units'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+64+300+85+3+37,$YPos,60,$FontSize,$DisplayDelDate, 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+64+300+85+40+60,$YPos,85,$FontSize,$DisplayPrice, 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+64+300+85+40+60+85,$YPos,85,$FontSize,$DisplayLineTotal, 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+1+64,$YPos,300,$FontSize,$ItemDescription, 'left');
			if (strlen($LeftOvers)>1){
				$LeftOvers = $pdf->addTextWrap($Left_Margin+1+64,$YPos-$line_height,300,$FontSize,$LeftOvers, 'left');
				$YPos-=$line_height;
			}

			if ($YPos-$line_height <= $Bottom_Margin){
		        /* We reached the end of the page so finsih off the page and start a newy */
				$PageNumber++;
				include ('includes/PO_PDFOrderPageHeader.inc');
			} //end if need a new page headed up

			/*increment a line down for the next line item */
			$YPos -= $line_height;

		} //end while there are line items to print out

		if ($YPos-$line_height <= $Bottom_Margin){ // need to ensure space for totals
		        $PageNumber++;
			include ('includes/PO_PDFOrderPageHeader.inc');
		} //end if need a new page headed up


		$DisplayOrderTotal = number_format($OrderTotal,2);
		$YPos = $Bottom_Margin + $line_height;
		$pdf->addText(560,$YPos, 14, _('Order Total'). ' ' . $POHeader['CurrCode']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+1+64+300+85+40+60+75,$YPos,95,14,$DisplayOrderTotal, 'right');

	} /*end if there are order details to show on the order*/
    //} /* end of check to see that there was an order selected to print */

    //failed var to allow us to print if the email fails.
    $failed = false;
    if ($MakePDFThenDisplayIt){

    	$buf = $pdf->output();
    	$len = strlen($buf);
    	header('Content-type: application/pdf');
    	header('Content-Length: ' . $len);
    	header('Content-Disposition: inline; filename=PurchaseOrder.pdf');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    	header('Pragma: public');

    	$pdf->stream();

    } else { /* must be MakingPDF to email it */

    	$pdfcode = $pdf->output();
	$fp = fopen( $reports_dir . '/PurchOrder.pdf','wb');
	fwrite ($fp, $pdfcode);
	fclose ($fp);

	include('includes/htmlMimeMail.php');

	$mail = new htmlMimeMail();
	$attachment = $mail->getFile($reports_dir . '/PurchOrder.pdf');
	$mail->setText( _('Please find herewith our purchase order number').' ' . $OrderNo);
	$mail->setSubject( _('Purchase Order Number').' ' . $OrderNo);
	$mail->addAttachment($attachment, 'PurchOrder.pdf', 'application/pdf');
	$mail->setFrom("$CompanyName <'" . $CompanyRecord['Email'] ."'>");
	$result = $mail->send(array($_POST['EmailTo']));
	if ($result==1){
		$failed = false;
		echo '<P>';
		prnMsg( _('Purchase order'). ' ' . $OrderNo.' ' . _('has been emailed to') .' ' . $_POST['EmailTo'] . ' ' . _('as directed'), 'success');
	} else {
		$failed = true;
		echo '<P>';
		prnMsg( _('Emailing Purchase order'). ' ' . $OrderNo.' ' . _('to') .' ' . $_POST['EmailTo'] . ' ' . _('failed'), 'error');
	}

    }

    if ($ViewingOnly==0 && !$failed) {
	$sql = "UPDATE PurchOrders SET AllowPrint=0, DatePrinted='" . Date('Y-m-d') . "' WHERE PurchOrders.OrderNo=" .$OrderNo;
	$result = DB_query($sql,$db);
    }

} /* There was enough info to either print or email the purchase order */
 else { /*the user has just gone into the page need to ask the question whether to print the order or email it to the supplier */

	include ('includes/header.inc');
	echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

	if ($ViewingOnly==1){
		echo '<INPUT TYPE=HIDDEN NAME="ViewingOnly" VALUE=1>';
	}
	echo '<BR><BR>';
	echo '<INPUT TYPE=HIDDEN NAME="OrderNo" VALUE="'. $OrderNo. '">';
	echo '<CENTER><TABLE><TR><TD>'. _('Print or Email the Order'). '</TD><TD>
		<SELECT NAME="PrintOrEmail">';

	if (!isset($_POST['PrintOrEmail'])){
		$_POST['PrintOrEmail'] = 'Print';
	}

	if ($_POST['PrintOrEmail']=='Print'){
		echo '<OPTION SELECTED VALUE="Print">'. _('Print');
		echo '<OPTION VALUE="Email">' . _('Email');
	} else {
		echo '<OPTION VALUE="Print">'. _('Print');
		echo '<OPTION SELECTED VALUE="Email">'. _('Email');
	}
	echo '</SELECT></TD></TR>';

	if ($_POST['PrintOrEmail']=='Email'){
		$ErrMsg = _('There was a problem retrieving the contact details for the supplier');
		$SQL = "SELECT Contact,
				Email
			FROM SupplierContacts INNER JOIN PurchOrders
			ON SupplierContacts.SupplierID=PurchOrders.SupplierNo
			WHERE PurchOrders.OrderNo=$OrderNo";
		$ContactsResult=DB_query($SQL,$db, $ErrMsg);

		if (DB_num_rows($ContactsResult)>0){
			echo '<TR><TD>'. _('Email to') .':</TD><TD><SELECT NAME="EmailTo">';
			while ($ContactDetails = DB_fetch_array($ContactsResult)){
				if (strlen($ContactDetails['Email'])>2 AND strpos($ContactDetails['Email'],'@')>0){
					if ($_POST['EmailTo']==$ContactDetails['Email']){
						echo '<OPTION SELECTED VALUE="' . $ContactDetails['Email'] . '">' . $ContactDetails['Contact'] . ' - ' . $ContactDetails['Email'];
					} else {
						echo '<OPTION VALUE="' . $ContactDetails['Email'] . '">' . $ContactDetails['Contact'] . ' - ' . $ContactDetails['Email'];
					}
				}
			}
			echo '</SELECT></TD></TR></TABLE>';
		} else {
			echo '</TABLE><BR>';
			prnMsg ( _('There are no contacts defined for the supplier of this order') . '. ' .
				_('You must first set up supplier contacts before emailing an order'), 'error');
			echo '<BR>';
		}
	} else {
		echo '</TABLE>';
	}
	echo '<BR><INPUT TYPE=SUBMIT NAME="DoIt" VALUE="' . _('OK') . '">';
	echo '</CENTER></FORM>';
	include('includes/footer.inc');
}
?>
