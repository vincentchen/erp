<?php
/* $Revision: 1.1 $ */

$PageSecurity =1;

include("config.php");
include("includes/ConnectDB.inc");
session_start();
include("includes/PDFStarter_ros.inc");
include("includes/DateFunctions.inc");

if (!isset($_GET['TransferNo'])){
	include ("includes/header.inc");
	echo "<P>This page must be called with a location transfer reference number";
	include ("includes/footer.inc");
	exit;
}

$FontSize=10;
$pdf->addinfo('Title',"Inventory Location Transfer BOL");
$pdf->addinfo('Subject',"Inventory Location Transfer BOL # " . $_GET['Trf_ID']);

$sql = "SELECT LocTransfers.Reference,
			   LocTransfers.StockID,
			   StockMaster.Description,
			   LocTransfers.ShipQty,
			   LocTransfers.ShipDate,
			   LocTransfers.ShipLoc,
			   Locations.LocationName AS ShipLocName,
			   LocTransfers.RecLoc,
			   LocationsRec.LocationName AS RecLocName
			   FROM LocTransfers
			   INNER JOIN StockMaster ON LocTransfers.StockID=StockMaster.StockID
			   INNER JOIN Locations ON LocTransfers.ShipLoc=Locations.LocCode
			   INNER JOIN Locations AS LocationsRec ON LocTransfers.RecLoc = LocationsRec.LocCode
			   WHERE LocTransfers.Reference=" . $_GET['TransferNo'];

$result = DB_query($sql,$db);

if (DB_error_no($db)!=0){
	include ("includes/header.inc");
	echo "<P>An error occurred retrieving the items on the transfer.";
	if ($debug==1){
		echo "The error message returned by the database was " . DB_error_msg($db) . "<BR>The SQL that failed was:<BR>" . $sql;
	}
	echo "<P>This page must be called with a location transfer reference number";
	include ("includes/footer.inc");
	exit;
}
If (DB_num_rows($result)==0){
	include ("includes/header.inc");
	echo "<P>The transfer reference selected does not appear to be set up - enter the items to be transferred first";
	include ("includes/footer.inc");
	exit;
}

$TransferRow = DB_fetch_array($result);

$PageNumber=1;
include ("includes/PDFStockLocTransferHeader.inc");
$line_height=30;
$FontSize=10;

do {

	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize,$TransferRow['StockID'], "left");
	$LeftOvers = $pdf->addTextWrap(150,$YPos,200,$FontSize,$TransferRow['Description'], "left");
	$LeftOvers = $pdf->addTextWrap(350,$YPos,60,$FontSize,$TransferRow['ShipQty'], "right");
	
	$pdf->line($Left_Margin, $YPos-2,$Page_Width-$Right_Margin, $YPos-2);

	$YPos -= $line_height;

	if ($YPos < $Bottom_Margin + $line_height) {
		$PageNumber++;
		include("includes/PDFStockLocTransferHeader.inc");
	}

} while ($TransferRow = DB_fetch_array($result));

$pdfcode = $pdf->output();
$len = strlen($pdfcode);


if ($len<=20){
	$title = "Print Stock Location Transfer Error";
	include("includes/header.inc");
	echo "<p>There was no stock location transfer to print out";
	echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>Back to the menu</A>";
	include("includes/footer.inc");
	exit;
} else {
	header("Content-type: application/pdf");
	header("Content-Length: " . $len);
	header("Content-Disposition: inline; filename=StockLocTrfShipment.pdf");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Pragma: public");

	$pdf->Stream();
}
?>