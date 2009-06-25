<?php
/* $Revision: 1.2 $ */
// MRPShortages.php - Report of parts with demand greater than supply as determined by MRP
$PageSecurity = 2;
include('includes/session.inc');

If (isset($_POST['PrintPDF'])) {

	include('includes/PDFStarter.php');

	$FontSize=9;
	$pdf->addinfo('Title',_('MRP Shortages Report'));
	$pdf->addinfo('Subject',_('MRP Shortages'));

	$PageNumber=1;
	$line_height=12;
// Several problems with origsql. SUMs for supply and demand were incorrect - for instance, if
// there were 2 supply records and 3 demand records, the sum for supply would be 3 times what
// it should have been and the sum of the demand would be 2 times what it should have been.
// Fixed that by using subqueries. Then had a problem because the demand and supply fields
// were not being recognized when tried to define extcost, so had to use the subqueries again
// in the calculation of extcost
//    *** not using $origsql - just have it here for reference ***
/*	$origsql = "SELECT stockmaster.stockid,
	                   stockmaster.description,
	                   stockmaster.mbflag,
	                   stockmaster.actualcost,
	                   stockmaster.decimalplaces,
	IF(supplyquantity IS NULL,0,SUM(supplyquantity)) as supply,
     SUM(quantity) as demand, ((SUM(quantity) - IF(supplyquantity IS NULL,0,SUM(supplyquantity))) * actualcost) as extcost
      FROM stockmaster
      LEFT JOIN mrpsupplies ON stockid = mrpsupplies.part 
      LEFT JOIN mrprequirements ON stockid = mrprequirements.part
      GROUP BY stockid
      HAVING demand > supply
      ORDER BY " . $_POST['Sort'];
*/

	// Only include directdemand mrprequirements so don't have demand for top level parts and also
	// show demand for the lower level parts that the upper level part generates. See MRP.php for
	// more notes - Decided not to exclude derived demand so using $sql, not $sqlexclude
	$sqlexclude = "SELECT stockmaster.stockid,
	                   stockmaster.description,
	                   stockmaster.mbflag,
	                   stockmaster.actualcost,
	                   stockmaster.decimalplaces,
	(SELECT IF(supplyquantity IS NULL,0,SUM(supplyquantity)) 
	 FROM mrpsupplies
	 GROUP BY mrpsupplies.part
	 WHERE stockid = mrpsupplies.part) AS supply,
    (SELECT SUM(quantity) 
      FROM mrprequirements
      GROUP BY mrprequirements.part
      WHERE stockid = mrprequirements.part AND mrprequirements.directdemand='1') AS demand,
      (((SELECT SUM(quantity) 
      FROM mrprequirements
      GROUP BY mrprequirements.part
      WHERE stockid = mrprequirements.part AND mrprequirements.directdemand='1') -
      (SELECT IF(supplyquantity IS NULL,0,SUM(supplyquantity)) 
	      FROM mrpsupplies 
	      WHERE stockid = mrpsupplies.part)) * actualcost) as extcost
      FROM stockmaster
      GROUP BY stockmaster.stockid,
			   stockmaster.description,
			   stockmaster.mbflag,
			   stockmaster.actualcost,
			   stockmaster.decimalplaces,
			   supply,
			   demand,
			   extcost
      HAVING demand > supply
      ORDER BY " . $_POST['Sort'];
   	$sql = "SELECT stockmaster.stockid,
        stockmaster.description,
        stockmaster.mbflag,
        stockmaster.actualcost,
        stockmaster.decimalplaces,
     (SELECT SUM(IF(supplyquantity IS NULL,0,supplyquantity)) 
      FROM mrpsupplies
      WHERE stockid = mrpsupplies.part GROUP BY stockid) AS supply,
         (SELECT SUM(quantity) 
           FROM mrprequirements
           WHERE stockid = mrprequirements.part GROUP BY stockid)
           AS demand,
           (
           ((SELECT SUM(IF(supplyquantity IS NULL,0,supplyquantity)) 
      FROM mrpsupplies
      WHERE stockid = mrpsupplies.part GROUP BY stockid) -
      (SELECT SUM(quantity) 
           FROM mrprequirements
           WHERE stockid = mrprequirements.part GROUP BY stockid))
           * stockmaster.actualcost
           ) as extcost
           FROM stockmaster
           GROUP BY stockmaster.stockid,
			   stockmaster.description,
			   stockmaster.mbflag,
			   stockmaster.actualcost,
			   stockmaster.decimalplaces,
			   supply,
			   demand
			    HAVING (SELECT SUM(quantity) 
           FROM mrprequirements
           WHERE stockid = mrprequirements.part GROUP BY stockid) > 
           (SELECT SUM(IF(supplyquantity IS NULL,0,supplyquantity)) 
      FROM mrpsupplies
      WHERE stockid = mrpsupplies.part GROUP BY stockid)
			  ORDER BY " . $_POST['Sort'] . $sortorder;
	$result = DB_query($sql,$db,'','',false,true);

	if (DB_error_no($db) !=0) {
	  $title = _('MRP Shortages') . ' - ' . _('Problem Report');
	  include('includes/header.inc');
	   prnMsg( _('The MRP shortages could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
	   echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
	      echo "<BR>$sql";
	   }
	   include('includes/footer.inc');
	   exit;
	}

	PrintHeader($pdf,$YPos,$PageNumber,$Page_Height,$Top_Margin,$Left_Margin,$Page_Width,
	                   $Right_Margin);

    $Total_Shortage=0;
    $Partctr = 0;
    $fill = false;
    $pdf->SetFillColor(224,235,255);  // Defines color to make alternating lines highlighted
	While ($myrow = DB_fetch_array($result,$db)){
        if ($myrow['demand'] > $myrow['supply']) {
			$YPos -=$line_height;
			$FontSize=8;
			
			// Use to alternate between lines with transparent and painted background
			if ($_POST['Fill'] == 'yes'){
				$fill=!$fill;
			}
	
			// Parameters for addTextWrap are defined in /includes/class.pdf.php
			// 1) X position 2) Y position 3) Width
			// 4) Height 5) Text 6) Alignment 7) Border 8) Fill - True to use SetFillColor
			// and False to set to transparent
			$shortage = ($myrow['demand'] - $myrow['supply']) * -1;
			$extcost = $shortage * $myrow['actualcost'];
			$pdf->addTextWrap($Left_Margin,$YPos,90,$FontSize,$myrow['stockid'],'',0,$fill);				
			$pdf->addTextWrap(130,$YPos,150,$FontSize,$myrow['description'],'',0,$fill);
			$pdf->addTextWrap(280,$YPos,25,$FontSize,$myrow['mbflag'],'right',0,$fill);
			$pdf->addTextWrap(305,$YPos,55,$FontSize,number_format($myrow['actualcost'],2),'right',0,$fill);
			$pdf->addTextWrap(360,$YPos,50,$FontSize,number_format($myrow['supply'],
			                 $myrow['decimalplaces']),'right',0,$fill);
			$pdf->addTextWrap(410,$YPos,50,$FontSize,number_format($myrow['demand'],
			                 $myrow['decimalplaces']),'right',0,$fill);
			$pdf->addTextWrap(460,$YPos,50,$FontSize,number_format($shortage,
			                 $myrow['decimalplaces']),'right',0,$fill);
			$pdf->addTextWrap(510,$YPos,60,$FontSize,number_format($myrow['extcost'],2),'right',0,$fill);
	
			$Total_Shortage += $myrow['extcost'];
			$Partctr++;
	
			if ($YPos < $Bottom_Margin + $line_height){
			   PrintHeader($pdf,$YPos,$PageNumber,$Page_Height,$Top_Margin,$Left_Margin,$Page_Width,
	                   $Right_Margin);
			}
		}

	} /*end while loop */

	$FontSize =8;
	$YPos -= (2*$line_height);

	if ($YPos < $Bottom_Margin + $line_height){
		   PrintHeader($pdf,$YPos,$PageNumber,$Page_Height,$Top_Margin,$Left_Margin,$Page_Width,
	                   $Right_Margin);
	}
/*Print out the grand totals */
    $pdf->addTextWrap($Left_Margin,$YPos,120,$FontSize,_('Number of Parts: '), 'left');
    $pdf->addTextWrap(150,$YPos,30,$FontSize,$Partctr, 'left');
	$pdf->addTextWrap(300,$YPos,180,$FontSize,_('Total Extended Shortage:'), 'right');
	$DisplayTotalVal = number_format($Total_Shortage,2);
    $pdf->addTextWrap(510,$YPos,60,$FontSize,$DisplayTotalVal, 'right');

	$pdfcode = $pdf->output();
	$len = strlen($pdfcode);

	if ($len<=20){
			$title = _('Print MRP Shortages Error');
			include('includes/header.inc');
			prnMsg(_('There were no items with demand greater than supply'),'error');
			echo "<BR><A HREF='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</A>';
			include('includes/footer.inc');
			exit;
	} else {
			header('Content-type: application/pdf');
			header("Content-Length: " . $len);
			header('Content-Disposition: inline; filename=MRPShortages.pdf');
			header('Expires: 0');
			header('Cache-Control: private, post-check=0, pre-check=0');
			header('Pragma: public');
	
			$pdf->Stream();
	}
	
} else { /*The option to print PDF was not hit so display form */

	$title=_('MRP Shortages Reporting');
	include('includes/header.inc');

	echo '</br></br><form action=' . $_SERVER['PHP_SELF'] . " method='post'><center><table>";
	echo '<tr><td>' . _('Sort') . ":</td><td><select name='Sort'>";
	echo "<option selected value='extcost'>" . _('Extended Shortage Dollars');
	echo "<option value='stockid'>" . _('Part Number');
	echo '</select></td></tr>';
	echo '<tr><td>' . _('Print Option') . ":</td><td><select name='Fill'>";
	echo "<option selected value='yes'>" . _('Print With Alternating Highlighted Lines');
	echo "<option value='no'>" . _('Plain Print');
	echo '</select></td></tr>';
	echo "</table></br><input type=submit name='PrintPDF' value='" . _('Print PDF') . "'></center>";

	include('includes/footer.inc');

} /*end of else not PrintPDF */


function PrintHeader(&$pdf,&$YPos,&$PageNumber,$Page_Height,$Top_Margin,$Left_Margin,
                     $Page_Width,$Right_Margin) {

$line_height=12;
/*PDF page header for MRP Shortages report */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=9;
$YPos= $Page_Height-$Top_Margin;

$pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);

$YPos -=$line_height;

$pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,_('MRP Shortages Report'));
$pdf->addTextWrap($Page_Width-$Right_Margin-110,$YPos,160,$FontSize,_('Printed') . ': ' . 
     Date($_SESSION['DefaultDateFormat']) . '   ' . _('Page') . ' ' . $PageNumber,'left');

$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

//$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
//$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height);
//$pdf->line($Left_Margin, $YPos- $line_height,$Page_Width-$Right_Margin, $YPos- $line_height);
//$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height);

/*set up the headings */
$Xpos = $Left_Margin+1;

$pdf->addTextWrap($Xpos,$YPos,130,$FontSize,_('Part Number'), 'left');
$pdf->addTextWrap(130,$YPos,150,$FontSize,_('Description'), 'left');
$pdf->addTextWrap(285,$YPos,20,$FontSize,_('M/B'), 'right');
$pdf->addTextWrap(305,$YPos,55,$FontSize,_('Actual Cost'), 'right');
$pdf->addTextWrap(360,$YPos,50,$FontSize,_('Supply'), 'right');
$pdf->addTextWrap(410,$YPos,50,$FontSize,_('Demand'), 'right');
$pdf->addTextWrap(460,$YPos,50,$FontSize,_('Shortage'), 'right');
$pdf->addTextWrap(510,$YPos,60,$FontSize,_('Ext. Shortage'), 'right');

$FontSize=8;
$YPos =$YPos - (2*$line_height);
$PageNumber++;
} // End of PrintHeader function

?>
