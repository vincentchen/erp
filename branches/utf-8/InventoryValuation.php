<?php
/* $Id$  */
$PageSecurity = 2;
include('includes/session.inc');

If (isset($_POST['PrintPDF'])
	AND isset($_POST['FromCriteria'])
	AND strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND strlen($_POST['ToCriteria'])>=1){

	include('includes/PDFStarter.php');

	$pdf->addinfo('Title',_('Inventory Valuation Report'));
	$pdf->addinfo('Subject',_('Inventory Valuation'));
	$FontSize=9;
	$PageNumber=1;
	$line_height=12;

      /*Now figure out the inventory data to report for the category range under review */
	if ($_POST['Location']=='All'){
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
				stockmaster.description,
				SUM(locstock.quantity) AS qtyonhand,
				stockmaster.units,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				SUM(locstock.quantity) *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster,
				stockcategory,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			GROUP BY stockmaster.categoryid,
				stockcategory.categorydescription,
				unitcost,
				stockmaster.units,
				stockmaster.materialcost,
				stockmaster.labourcost,
				stockmaster.overheadcost,
				stockmaster.stockid,
				stockmaster.description
			HAVING SUM(locstock.quantity)!=0
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			ORDER BY stockmaster.categoryid,
				stockmaster.stockid";
	} else {
		$SQL = "SELECT stockmaster.categoryid,
				stockcategory.categorydescription,
				stockmaster.stockid,
				stockmaster.description,
				stockmaster.units,
				locstock.quantity AS qtyonhand,
				stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
				locstock.quantity *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
			FROM stockmaster,
				stockcategory,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid=stockcategory.categoryid
			AND locstock.quantity!=0
			AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
			AND locstock.loccode = '" . $_POST['Location'] . "'
			ORDER BY stockmaster.categoryid,
				stockmaster.stockid";
	}
	$InventoryResult = DB_query($SQL,$db,'','',false,true);

	if (DB_error_no($db) !=0) {
	  $title = _('Inventory Valuation') . ' - ' . _('Problem Report');
	  include('includes/header.inc');
	   prnMsg( _('The inventory valuation could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
	   echo "<br><a href='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</a>';
	   if ($debug==1){
	      echo "<br>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}
	if (DB_num_rows($InventoryResult)==0){
		$title = _('Print Inventory Valuation Error');
		include('includes/header.inc');
		prnMsg(_('There were no items with any value to print out for the location specified'),'info');
		echo "<br><a href='$rootpath/index.php?" . SID . "'>" . _('Back to the menu') . '</a>';
		include('includes/footer.inc');
		exit;	
	}	

	include ('includes/PDFInventoryValnPageHeader.inc');

    $Tot_Val=0;
	$Category = '';
	$CatTot_Val=0;
    $CatTot_Qty=0;

	While ($InventoryValn = DB_fetch_array($InventoryResult,$db)){

		if ($Category!=$InventoryValn['categoryid']){
			$FontSize=10;
			if ($Category!=''){ /*Then it's NOT the first time round */

				/* need to print the total of previous category */
				if ($_POST['DetailedReport']=='Yes'){
					$YPos -= (2*$line_height);
					if ($YPos < $Bottom_Margin + (3*$line_height)){
		 				  include('includes/PDFInventoryValnPageHeader.inc');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,_('Total for') . ' ' . $Category . ' - ' . $CategoryName);
				}

				$DisplayCatTotVal = number_format($CatTot_Val,2);
				$DisplayCatTotQty = number_format($CatTot_Qty,0);
                                $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	                        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');
                                $YPos -=$line_height;

				If ($_POST['DetailedReport']=='Yes'){
				/*draw a line under the CATEGORY TOTAL*/
					$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
					$YPos -=(2*$line_height);
				}
				$CatTot_Val=0;
                                $CatTot_Qty=0;
			}
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize,$InventoryValn['categoryid'] . ' - ' . $InventoryValn['categorydescription']);
			$Category = $InventoryValn['categoryid'];
			$CategoryName = $InventoryValn['categorydescription'];
		}

		if ($_POST['DetailedReport']=='Yes'){
			$YPos -=$line_height;
			$FontSize=8;

			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize,$InventoryValn['stockid']);				
			$LeftOvers = $pdf->addTextWrap(170,$YPos,220,$FontSize,$InventoryValn['description']);
			$DisplayUnitCost = number_format($InventoryValn['unitcost'],2);
			$DisplayQtyOnHand = number_format($InventoryValn['qtyonhand'],0);
			$DisplayItemTotal = number_format($InventoryValn['itemtotal'],2);

			$LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayQtyOnHand,'right');
			$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,$DisplayUnitCost, 'right');
			$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayItemTotal, 'right');
			$LeftOvers = $pdf->addTextWrap(400,$YPos,60,$FontSize,$InventoryValn['units'],'right');

		}
		$Tot_Val += $InventoryValn['itemtotal'];
		$CatTot_Val += $InventoryValn['itemtotal'];
        $CatTot_Qty += $InventoryValn['qtyonhand'];

		if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFInventoryValnPageHeader.inc');
		}

	} /*end inventory valn while loop */

	$FontSize =10;
/*Print out the category totals */
	if ($_POST['DetailedReport']=='Yes'){
		$YPos -= (2*$line_height);
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,260-$Left_Margin,$FontSize, _('Total for') . ' ' . $Category . ' - ' . $CategoryName, 'left');
	}
	$DisplayCatTotVal = number_format($CatTot_Val,2);
        $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayCatTotVal, 'right');
	$DisplayCatTotQty = number_format($CatTot_Qty,0);
        $LeftOvers = $pdf->addTextWrap(380,$YPos,60,$FontSize,$DisplayCatTotQty, 'right');

	If ($_POST['DetailedReport']=='Yes'){
		/*draw a line under the CATEGORY TOTAL*/
		$YPos -= ($line_height);
		$pdf->line($Left_Margin, $YPos+$line_height-2,$Page_Width-$Right_Margin, $YPos+$line_height-2);
	}

	$YPos -= (2*$line_height);

	if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFInventoryValnPageHeader.inc');
	}
/*Print out the grand totals */
	$LeftOvers = $pdf->addTextWrap(80,$YPos,260-$Left_Margin,$FontSize,_('Grand Total Value'), 'right');
	$DisplayTotalVal = number_format($Tot_Val,2);
    $LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,$DisplayTotalVal, 'right');

	$pdf->OutputD($_SESSION['DatabaseName'] . '_Inventory_Valuation_' . Date('Y-m-d') . '.pdf');
	$pdf->__destruct();
} else { /*The option to print PDF was not hit */

	$title=_('Inventory Valuation Reporting');
	include('includes/header.inc');


	if (empty($_POST['FromCriteria']) or empty($_POST['ToCriteria'])) {

	/*if $FromCriteria is not set then show a form to allow input	*/

		echo '<form action=' . $_SERVER['PHP_SELF'] . " method='POST'><table>";

		echo '<tr><td>' . _('From Inventory Category Code') . ':</font></td><td><select name=FromCriteria>';

		$sql='SELECT categoryid, categorydescription FROM stockcategory ORDER BY categoryid';
		$CatResult= DB_query($sql,$db);
		While ($myrow = DB_fetch_array($CatResult)){
			echo "<option value='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'];
		}
		echo '</select></td></tr>';

		echo '<tr><td>' . _('To Inventory Category Code') . ':</td><td><select name=ToCriteria>';

		/*Set the index for the categories result set back to 0 */
		DB_data_seek($CatResult,0);

		While ($myrow = DB_fetch_array($CatResult)){
			echo "<option value='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'];
		}
		echo '</select></td></tr>';

		echo '<tr><td>' . _('For Inventory in Location') . ":</td><td><select name='Location'>";
		$sql = 'SELECT loccode, locationname FROM locations';
		$LocnResult=DB_query($sql,$db);

		echo "<option value='All'>" . _('All Locations');

		while ($myrow=DB_fetch_array($LocnResult)){
		          echo "<option Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		      		}
		echo '</select></td></tr>';

		echo '<tr><td>' . _('Summary or Detailed Report') . ":</td><td><select name='DetailedReport'>";
		echo "<option selected Value='No'>" . _('Summary Report');
		echo "<option Value='Yes'>" . _('Detailed Report');
		echo '</select></td></tr>';

		echo "</table><div class='centre'><input type=Submit Name='PrintPDF' Value='" . _('Print PDF') . "'></div>";
	}
	include('includes/footer.inc');

} /*end of else not PrintPDF */

?>
