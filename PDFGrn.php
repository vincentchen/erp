<?php

/* $Id$*/

//$PageSecurity = 2; Now comes from DB - read in from session
include('includes/session.inc');

if (isset($_GET['GRNNo'])) {
	$GRNNo=$_GET['GRNNo'];
} else {
	$GRNNo='';
}

$FormDesign = simplexml_load_file($PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/FormDesigns/GoodsReceived.xml');

// Set the paper size/orintation
$PaperSize = $FormDesign->PaperSize;
$PageNumber=1;
$line_height=$FormDesign->LineHeight;
include('includes/PDFStarter.php');
$pdf->addInfo('Title', _('Goods Received Note') );

$sql="SELECT grns.itemcode,
		grns.grnno,
		grns.deliverydate,
		grns.itemdescription,
		grns.qtyrecd,
		grns.supplierid,
		purchorderdetails.suppliersunit,
		purchorderdetails.conversionfactor,
		stockmaster.units,
		stockmaster.decimalplaces
	FROM grns INNER JOIN purchorderdetails
	ON grns.podetailitem=purchorderdetails.podetailitem
	LEFT JOIN stockmaster
	ON grns.itemcode=stockmaster.stockid
	WHERE grnbatch='".$GRNNo."'";

$GRNResult=DB_query($sql, $db);

if(DB_num_rows($GRNResult)>0) { //there are GRNs to print
	
	$sql = "SELECT suppliers.suppname,
		suppliers.address1,
		suppliers.address2 ,
		suppliers.address3,
		suppliers.address4,
		suppliers.address5,
		suppliers.address6
	FROM grns INNER JOIN suppliers
	ON grns.supplierid=suppliers.supplierid
	WHERE grnbatch='".$GRNNo."'";
	$SuppResult = DB_query($sql,$db,_('Could not get the supplier of the selected GRN'));
	$SuppRow = DB_fetch_array($SuppResult);
	
	include ('includes/PDFGrnHeader.inc'); //head up the page
	
	$YPos=$FormDesign->Data->y;
	while ($myrow = DB_fetch_array($GRNResult)) {

		if (is_numeric($myrow['decimalplaces'])){
			$DecimalPlaces=$myrow['decimalplaces'];
		} else {
			$DecimalPlaces=2;
		}
		if (is_numeric($myrow['conversionfactor']) AND $myrow['conversionfactor'] !=0){
			$SuppliersQuantity=number_format($myrow['qtyrecd']/$myrow['conversionfactor'],$DecimalPlaces);
		} else {
			$SuppliersQuantity=number_format($myrow['qtyrecd'],$DecimalPlaces);
		}
		$OurUnitsQuantity=number_format($myrow['qtyrecd'],$DecimalPlaces);
		$DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
		
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column1->x,$Page_Height-$YPos,$FormDesign->Data->Column1->Length,$FormDesign->Data->Column1->FontSize, $myrow['itemcode']);
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column2->x,$Page_Height-$YPos,$FormDesign->Data->Column2->Length,$FormDesign->Data->Column2->FontSize, $myrow['itemdescription']);
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column3->x,$Page_Height-$YPos,$FormDesign->Data->Column3->Length,$FormDesign->Data->Column3->FontSize, $DeliveryDate);
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column4->x,$Page_Height-$YPos,$FormDesign->Data->Column4->Length,$FormDesign->Data->Column4->FontSize, $SuppliersQuantity, 'right');
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column5->x,$Page_Height-$YPos,$FormDesign->Data->Column5->Length,$FormDesign->Data->Column5->FontSize, $myrow['suppliersunit'], 'left');
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column6->x,$Page_Height-$YPos,$FormDesign->Data->Column6->Length,$FormDesign->Data->Column6->FontSize, $OurUnitsQuantity, 'right');
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column7->x,$Page_Height-$YPos,$FormDesign->Data->Column7->Length,$FormDesign->Data->Column7->FontSize, $myrow['units'], 'left');
		$YPos += $line_height;

		if ($YPos >= $FormDesign->LineAboveFooter->starty){
			/* We reached the end of the page so finsih off the page and start a newy */
			$PageNumber++;
			$YPos=$FormDesign->Data->y;
			include ('includes/PDFGrnHeader.inc');
		} //end if need a new page headed up
	} //end of loop around GRNs to print

	$LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x,$Page_Height-$FormDesign->ReceiptDate->y,$FormDesign->ReceiptDate->FontSize, _('Date of Receipt: ') . $DeliveryDate);
	$LeftOvers = $pdf->addText($FormDesign->SignedFor->x,$Page_Height-$FormDesign->SignedFor->y,$FormDesign->SignedFor->FontSize, _('Signed for ').'______________________');
    $pdf->OutputD($_SESSION['DatabaseName'] . '_GRN_' . date('Y-m-d').'.pdf');//UldisN
    $pdf->__destruct(); //UldisN
} else { //there were not GRNs to print
		$title = _('GRN Error');
	include('includes/header.inc');
	prnMsg(_('There were no GRNs to print'),'warn');
	echo '<br><a href="'.$rootpath.'/index.php?' . SID . '">'. _('Back to the menu').'</a>';
	include('includes/footer.inc');
}
?>