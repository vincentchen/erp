<?php
/* $Revision: 1.5 $ */

/*this class was an extension to the fpdf class using a syntax that the original reports were written in
(the R &OS pdf.php class) - due to limitation of this class for foreign character support this wrapper class
was written to allow the same code base to use the more functional fpdf.class by Olivier Plathey */

include ('class.pdf.php');

/*
//	Changes to move from FPDF to TCPDF to support UTF-8 by Javier de Lorenzo-Cáceres <info@civicom.eu> 
*/

if (!isset($PaperSize)){				// Javier: Results True, it's not set.
	$PaperSize = $_SESSION['DefaultPageSize'];	// Javier: DefaultPageSize is taken from DB, www_users, pagesize = A4
}

/* Javier: TCPDF supports 45 standard ISO (DIN) paper formats and 4 american common formats and does this cordinates calculation.
		However, reports use this units */

switch ($PaperSize) {

  case 'A4':

	$DocumentPaper = 'A4'; $DocumentOrientation ='P';

      $Page_Width=595; // DIN-A4 is 210 mm width, i.e., 595,2756 points (inches * 72 ppp)
      $Page_Height=842;
      $Top_Margin=30;
      $Bottom_Margin=30;
      $Left_Margin=40;
      $Right_Margin=30;
      break;

  case 'A4_Landscape':

	$DocumentPaper = 'A4'; $DocumentOrientation ='L';

      $Page_Width=842;
      $Page_Height=595;
      $Top_Margin=30;
      $Bottom_Margin=30;
      $Left_Margin=40;
      $Right_Margin=30;
      break;

   case 'A3':

	$DocumentPaper = 'A3'; $DocumentOrientation ='P';

      $Page_Width=842;
      $Page_Height=1190;
      $Top_Margin=50;
      $Bottom_Margin=50;
      $Left_Margin=50;
      $Right_Margin=40;
      break;

   case 'A3_landscape':

	$DocumentPaper = 'A3'; $DocumentOrientation ='L';

      $Page_Width=1190;
      $Page_Height=842;
      $Top_Margin=50;
      $Bottom_Margin=50;
      $Left_Margin=50;
      $Right_Margin=40;
      break;

   case 'letter':

	$DocumentPaper = 'LETTER'; $DocumentOrientation ='P';

      $Page_Width=612;
      $Page_Height=792;
      $Top_Margin=30;
      $Bottom_Margin=30;
      $Left_Margin=30;
      $Right_Margin=25;
      break;

   case 'letter_landscape':

	$DocumentPaper = 'LETTER'; $DocumentOrientation ='L';

      $Page_Width=792;
      $Page_Height=612;
      $Top_Margin=30;
      $Bottom_Margin=30;
      $Left_Margin=30;
      $Right_Margin=25;
      break;

   case 'legal':

	$DocumentPaper = 'LEGAL'; $DocumentOrientation ='P';

      $Page_Width=612;
      $Page_Height=1008;
      $Top_Margin=50;
      $Bottom_Margin=40;
      $Left_Margin=30;
      $Right_Margin=25;
      break;

   case 'legal_landscape':

	$DocumentPaper = 'LEGAL'; $DocumentOrientation ='L';

      $Page_Width=1008;
      $Page_Height=612;
      $Top_Margin=50;
      $Bottom_Margin=40;
      $Left_Margin=30;
      $Right_Margin=25;
      break;
}

// Javier: $PageSize = array(0,0,$Page_Width,$Page_Height);
// Javier: $pdf = new Cpdf($PageSize);
$pdf = new Cpdf($DocumentOrientation, 'pt', $DocumentPaper);

$pdf->addInfo('Creator', 'WebERP http://www.weberp.org');
$pdf->addInfo('Author', 'WebERP ' . $Version);


/* Javier: Brought from class.pdf.php constructor
	Next step is to move it to each report to get the advantage of Document Header */
$pdf->setAutoPageBreak(0);
$pdf->setPrintHeader(false); // must be called before Add Page
$pdf->AddPage();
//	$this->SetLineWidth(1); Javier: It was ok but now is too gross for TCPDF. TCPDF default is 0'57 pt (0'2 mm) which is ok.
$pdf->cMargin = 0;
/* END Brought from class.pdf.php constructor */

// Javier: TCPDF now supports CJK
/*depending on the language this font is modified see includes/class.pdf.php
	selectFont method interprets the text helvetica to be:
	for Chinese - BIg5
	for Japanese - SJIS
	for Korean - UHC

$pdf->selectFont('helvetica'); */
$pdf->selectFont();
?>
