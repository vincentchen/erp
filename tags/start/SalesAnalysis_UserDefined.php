<?php

include("config.php");
include("includes/ConnectDB.inc");

$PageSecurity = 2;
if (isset($SessionSavePath)){
	session_save_path($SessionSavePath);
}

session_start();

if (! in_array($PageSecurity,$SecurityGroups[$_SESSION["AccessLevel"]])){
	echo "<html><body><BR><BR><BR><BR><BR><BR><BR><CENTER><FONT COLOR=RED SIZE=4><B>The security settings on your account do not permit you to access this function.</B></FONT></body></html>";
	exit;
}

include("includes/ConstructSQLForUserDefinedSalesReport.inc");

if (isset($_GET['ProducePDF'])){

	include ("includes/PDFSalesAnalysis.inc");

	if ($Counter >0){
 		$pdfcode = $pdf->output();
		$len = strlen($pdfcode);

        	if ($len<=20){
			$title = "Printing Sales Analysis Error";
			include("includes/header.inc");
			echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>Back to the menu</A>";
			include("includes/footer.inc");
			exit;
        	} else {
			header("Content-type: application/pdf");
			header("Content-Length: " . $len);
			header("Content-Disposition: inline; filename=SalesAnalysis.pdf");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public");

			$pdf->Stream();
		}
	} else {
	    $title = "User Defined Sales Analysis Problem .... ";
	   include("includes/header.inc");
	    echo "<P>The report didn't have any none zero lines of information to show and so it has not been created.";
	    echo "<BR><A HREF='$rootpath/SalesAnalRepts.php?" . SID . "SelectedReport=" . $_GET['ReportID'] . "'>Look at the design of this report</A>";
	    echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>Back to the menu</A>";
	    include("includes/footer.inc");
	    exit;
	}
} /* end if we wanted a PDF file */



if ($_GET['ProduceCVSFile']==True){

	include("includes/CSVSalesAnalysis.inc");

	 $title = "Sales Analysis Comma Seperated File (CSV) Generation";
	include("includes/header.inc");

	 echo "http://" . getenv(SERVER_NAME) . $rootpath . "/" . $reports_dir .  "/SalesAnalysis.csv";
	 echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . "/" . $reports_dir .  "/SalesAnalysis.csv'>";

	 echo "<P>You should automatically be forwarded to the CSV Sales Analysis file when it is ready.	If this does not happen, <a href='" . $rootpath . "/" . $reports_dir . "/SalesAnalysis.csv'>click here</a> to continue.<br>";
	 include("includes/footer.inc");
}


?>