<?php
/* $Id$*/
/*	Script to print a price list by inventory category */
/*	Output column sizes:
		* stockmaster.stockid, varchar(20), len = 20chr
		* stockmaster.description, varchar(50), len = 50chr
		* prices.startdate, date, len = 10chr
		* prices.enddate, date/'No End Date', len = 12chr
		* custbranch.brname, varchar(40), len = 40chr
		* Gross Profit, calculated, len = 8chr
		* prices.price, decimal(20,4), len = 20chr + 4spaces */

include('includes/session.inc');

If (isset($_POST['PrintPDF'])
	AND isset($_POST['FromCriteria'])
	AND mb_strlen($_POST['FromCriteria'])>=1
	AND isset($_POST['ToCriteria'])
	AND mb_strlen($_POST['ToCriteria'])>=1) {

/*	if ($_POST['CustomerSpecials']=='Customer Special Prices Only') {
		// To do: For special prices, change from portrait to landscape orientation.
	}*/
	include('includes/PDFStarter.php');// Sets $PageNumber, page width, page height, top margin, bottom margin, left margin and right margin.

	$pdf->addInfo('Title', _('Price list by inventory category') );
	$pdf->addInfo('Subject', _('Price List') );

	$FontSize=10;
	$line_height=12;

	/*Now figure out the inventory data to report for the category range under review */
	if ($_POST['CustomerSpecials']==_('Customer Special Prices Only')) {

		if ($_SESSION['CustomerID']=='') {
			$Title = _('Special price List - No Customer Selected');
			$ViewTopic = 'SalesTypes';// Filename in ManualContents.php's TOC.
			$BookMark = 'PDFPriceList';// Anchor's id in the manual's html document.
			include('includes/header.inc');
			echo '<br />';
			prnMsg( _('The customer must first be selected from the select customer link') . '. ' . _('Re-run the price list once the customer has been selected') );
			echo '<br /><br /><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Back') . '</a>';
			include('includes/footer.inc');
			exit;
		}
		if (!Is_Date($_POST['EffectiveDate'])) {
			$Title = _('Special price List - No Customer Selected');
			$ViewTopic = 'SalesTypes';// Filename in ManualContents.php's TOC.
			$BookMark = 'PDFPriceList';// Anchor's id in the manual's html document.
			include('includes/header.inc');
			prnMsg(_('The effective date must be entered in the format') . ' ' . $_SESSION['DefaultDateFormat'],'error');
			echo '<br /><br /><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Back') . '</a>';
			include('includes/footer.inc');
			exit;
		}

		$SQL = "SELECT debtorsmaster.name,
				debtorsmaster.salestype
				FROM debtorsmaster
				WHERE debtorno = '" . $_SESSION['CustomerID'] . "'";
		$CustNameResult = DB_query($SQL,$db);
		$CustNameRow = DB_fetch_row($CustNameResult);
		$CustomerName = $CustNameRow[0];
		$SalesType = $CustNameRow[1];

		$SQL = "SELECT prices.typeabbrev,
  						prices.stockid,
  						stockmaster.description,
  						stockmaster.longdescription,
  						prices.currabrev,
  						prices.startdate,
  						prices.enddate,
  						prices.price,
  						stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS standardcost,
  						stockmaster.categoryid,
  						stockcategory.categorydescription,
  						prices.debtorno,
  						prices.branchcode,
  						custbranch.brname,
  						currencies.decimalplaces
						FROM stockmaster INNER JOIN	stockcategory
						ON stockmaster.categoryid=stockcategory.categoryid
						INNER JOIN prices
						ON stockmaster.stockid=prices.stockid
						INNER JOIN currencies
						ON prices.currabrev=currencies.currabrev
                        LEFT JOIN custbranch
						ON prices.debtorno=custbranch.debtorno
						AND prices.branchcode=custbranch.branchcode
						WHERE prices.typeabbrev = '" . $SalesType . "'
						AND stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
						AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
						AND prices.debtorno='" . $_SESSION['CustomerID'] . "'
						AND prices.startdate<='" . FormatDateForSQL($_POST['EffectiveDate']) . "'
						AND (prices.enddate='0000-00-00' OR prices.enddate >'" . FormatDateForSQL($_POST['EffectiveDate']) . "')
						ORDER BY prices.currabrev,
							stockmaster.categoryid,
							stockmaster.stockid,
							prices.startdate";

	} else { /* the sales type list only */

		$SQL = "SELECT sales_type FROM salestypes WHERE typeabbrev='" . $_POST['SalesType'] . "'";
		$SalesTypeResult = DB_query($SQL,$db);
		$SalesTypeRow = DB_fetch_row($SalesTypeResult);
		$SalesTypeName = $SalesTypeRow[0];

		$SQL = "SELECT	prices.typeabbrev,
        				prices.stockid,
        				prices.startdate,
        				prices.enddate,
        				stockmaster.description,
        				stockmaster.longdescription,
        				prices.currabrev,
        				prices.price,
        				stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost as standardcost,
        				stockmaster.categoryid,
        				stockcategory.categorydescription,
        				currencies.decimalplaces
				FROM stockmaster INNER JOIN	stockcategory
	   			     ON stockmaster.categoryid=stockcategory.categoryid
				INNER JOIN prices
    				ON stockmaster.stockid=prices.stockid
				INNER JOIN currencies
					ON prices.currabrev=currencies.currabrev
                WHERE stockmaster.categoryid >= '" . $_POST['FromCriteria'] . "'
    			AND stockmaster.categoryid <= '" . $_POST['ToCriteria'] . "'
    			AND prices.typeabbrev='" . $_POST['SalesType'] . "'
    			AND prices.startdate<='" . FormatDateForSQL($_POST['EffectiveDate']) . "'
    			AND (prices.enddate='0000-00-00' OR prices.enddate>'" . FormatDateForSQL($_POST['EffectiveDate']) . "')
    			AND prices.debtorno=''
    			ORDER BY prices.currabrev,
    				stockmaster.categoryid,
    				stockmaster.stockid,
    				prices.startdate";
	}
	$PricesResult = DB_query($SQL,$db,'','',false,false);

	if (DB_error_no($db) !=0) {
		$Title = _('Price List') . ' - ' . _('Problem Report....');
		include('includes/header.inc');
		prnMsg( _('The Price List could not be retrieved by the SQL because'). ' - ' . DB_error_msg($db), 'error');
		echo '<br /><a href="' .$RootPath .'/index.php">' .   _('Back to the menu'). '</a>';
		if ($debug==1) {
			prnMsg(_('For debugging purposes the SQL used was:') . $SQL,'error');
		}
		include('includes/footer.inc');
		exit;
	}
	if (DB_num_rows($PricesResult)==0) {
		$Title = _('Print Price List Error');
		include('includes/header.inc');
		prnMsg(_('There were no price details to print out for the customer or category specified'),'warn');
		echo '<br /><a href="'.htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' .  _('Back') . '</a>';
		include('includes/footer.inc');
		exit;
	}

	PageHeader();

	$CurrCode ='';
	$Category = '';
	$CatTot_Val=0;

	While ($PriceList = DB_fetch_array($PricesResult,$db)) {

		if ($CurrCode != $PriceList['currabrev']) {
			$FontSize = 10;
			$YPos -= 2*$line_height;			
			require_once('includes/CurrenciesArray.php');// To get the currency name from the currency code.
			$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 300-$Left_Margin, $FontSize,
				$PriceList['currabrev'] . ' - ' . _($CurrencyName[$PriceList['currabrev']]));
			$CurrCode = $PriceList['currabrev'];
		}

		if ($Category != $PriceList['categoryid']) {
			$FontSize = 10;
			$YPos -= 2*$line_height;
			$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 300-$Left_Margin, $FontSize, 
				$PriceList['categoryid'] . ' - ' . $PriceList['categorydescription']);
			$Category = $PriceList['categoryid'];
		}

		$FontSize = 8;
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 80, $FontSize, 
			$PriceList['stockid']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+80, $YPos, 200, $FontSize, 
			$PriceList['description']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+280, $YPos, 40, $FontSize, 
			ConvertSQLDate($PriceList['startdate']));
		if ($PriceList['enddate']!='0000-00-00') {
			$DisplayEndDate = ConvertSQLDate($PriceList['enddate']);
		} else {
			$DisplayEndDate = _('No End Date');
		}
		$LeftOvers = $pdf->addTextWrap($Left_Margin+320, $YPos, 48, $FontSize, 
			$DisplayEndDate);

		// Shows gross profit percentage:
		if ($_POST['ShowGPPercentages']=='Yes') {
			$DisplayGPPercent = '-';
			if ($PriceList['price']!=0) {
				$DisplayGPPercent = locale_number_format((($PriceList['price']-$PriceList['standardcost'])*100/$PriceList['price']), 2) . '%';
			}			
			$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-128, $YPos, 32, $FontSize, 
				$DisplayGPPercent, 'right');
		}
		// Displays unit price:
		$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-96, $YPos, 96, $FontSize, 
			locale_number_format($PriceList['price'],$PriceList['decimalplaces']), 'right');

		if ($_POST['CustomerSpecials']=='Customer Special Prices Only') {
			/*Need to show to which branch the price relates */
			if ($PriceList['branchcode']!='') {
				$LeftOvers = $pdf->addTextWrap($Left_Margin+376, $YPos, 160, $FontSize, $PriceList['brname']);
			} else {
				$LeftOvers = $pdf->addTextWrap($Left_Margin+376, $YPos, 160, $FontSize, _('All'));
			}

		} else If ($_POST['CustomerSpecials']=='Full Description') {
			// Prints item image:
			$YPosImage = $YPos;// Initializes the image bottom $YPos.
			if(file_exists($_SESSION['part_pics_dir'] . '/' .$PriceList['stockid'].'.jpg') ) {
				$img = imagecreatefromjpeg($_SESSION['part_pics_dir'] . '/' . $PriceList['stockid'] . '.jpg');
				if($YPos-36 < $Bottom_Margin) {// If the image bottom reaches the bottom margin, do PageHeader().
					PageHeader();
				}				
				$LeftOvers = $pdf->Image($_SESSION['part_pics_dir'] . '/'.$PriceList['stockid'].'.jpg', 
					$Left_Margin+3, $Page_Height-$YPos, 36, 36);
				$YPosImage = $YPos-36;// Stores the $YPos of the image bottom (see bottom).
			}
			// Prints stockmaster.longdescription:
			$XPos = $Left_Margin+80;// Takes out this calculation from the loop.
			$Width = $Page_Width-$Right_Margin-$XPos;// Takes out this calculation from the loop.
			$FontSize2 = $FontSize*0.80;// Font size and line height of Full Description section.
			$Split = explode("\r\n", $PriceList['longdescription']);
			foreach ($Split as $LeftOvers) {
				$LeftOvers = stripslashes($LeftOvers);
				while(mb_strlen($LeftOvers)>1) {
					$YPos -= $FontSize2;
					if ($YPos < $Bottom_Margin) {// If the description line reaches the bottom margin, do PageHeader().
						PageHeader();
						$YPosImage = $YPos;// Resets the image bottom $YPos.
					}
					$LeftOvers = $pdf->addTextWrap($XPos, $YPos, $Width, $FontSize2, $LeftOvers);
				}
			}

			// Assigns to $YPos the lowest $YPos value between the image and the description:
			$YPos = min($YPosImage, $YPos);
		}/* Endif full descriptions*/

		if ($YPos < $Bottom_Margin + $line_height) {
		   PageHeader();
		}
	} /*end inventory valn while loop */

	$FontSize = 10;
	$FileName=$_SESSION['DatabaseName']. '_' . _('Price_List') . '_' . date('Y-m-d').'.pdf';
	ob_clean();
	$pdf->OutputD($FileName);
	$pdf->__destruct();

} else { /*The option to print PDF was not hit */
	$Title = _('Price Listing');
	$ViewTopic = 'SalesTypes';// Filename in ManualContents.php's TOC.
	$BookMark = 'PDFPriceList';// Anchor's id in the manual's html document.
	include('includes/header.inc');
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Price List') . '" alt="" />
         ' . ' ' . _('Print a price list by inventory category') . '</p>';

	if (!isset($_POST['FromCriteria']) or !isset($_POST['ToCriteria'])) {
		/*if $FromCriteria is not set then show a form to allow input */

		echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
        echo '<div>';
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
        echo '<table class="selection">';

		$sql='SELECT categoryid, categorydescription FROM stockcategory ORDER BY categoryid';
		$CatResult= DB_query($sql,$db);
		$SelectCat='';
		While ($myrow = DB_fetch_array($CatResult)) {
			$SelectCat .= "<option value='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'] . '</option>';
		}
		$SelectCat .= '</select></td></tr>';
		echo '<tr><td>' .  _('From Inventory Category Code') . ':</td>
                  <td><select name="FromCriteria">';
		echo $SelectCat;
		echo '<tr><td>' . _('To Inventory Category Code') . ':</td>
                  <td><select name="ToCriteria">';
		echo $SelectCat;

		echo '<tr><td>' . _('For Sales Type/Price List').':</td>
                  <td><select name="SalesType">';
		$sql = "SELECT sales_type, typeabbrev FROM salestypes";
		$SalesTypesResult=DB_query($sql,$db);

		while ($myrow=DB_fetch_array($SalesTypesResult)) {
			echo '<option value="' . $myrow['typeabbrev'] . '">' . $myrow['sales_type'] . '</option>';
		}
		echo '</select></td></tr>';

		echo '<tr>
				<td>' . _('Show Gross Profit %') . ':</td>
				<td><select name="ShowGPPercentages">
					<option selected="selected" value="No">' .  _('Prices Only') . '</option>
					<option value="Yes">' .  _('Show GP % too') . '</option>
					</select></td>
			</tr>
			<tr>
				<td>' . _('Price Listing Type'). ':</td><td><select name="CustomerSpecials">
					<option selected="selected" value="Sales Type Prices">' .  _('Default Sales Type Prices') . '</option>
					<option value="Customer Special Prices Only">' .  _('Customer Special Prices Only') . '</option>
					<option value="Full Description">' .  _('Full Description') . '</option>
					</select></td>
			</tr>
			<tr>
				<td>' . _('Effective As At') . ':</td>
				<td><input type="text" required="required" size="11" class="date"	alt="' . $_SESSION['DefaultDateFormat'] . '" name="EffectiveDate" value="' . Date($_SESSION['DefaultDateFormat']) . '" /></td>
			</tr>
			</table>
			<br />
			<div class="centre">
				<input type="submit" name="PrintPDF" value="'. _('Print PDF'). '" />
			</div>
			</div>
		</form>';
	}
	include('includes/footer.inc');
} /*end of else not PrintPDF */

function PageHeader () {
	global $pdf;
	global $Page_Width;
	global $Page_Height;
	global $Top_Margin;
	global $Bottom_Margin;
	global $Left_Margin;
	global $Right_Margin;
	global $PageNumber;
	global $YPos;
	global $FontSize;
	global $line_height;
	global $SalesTypeName;
	global $CustomerName;

	$PageNumber++;
	if ($PageNumber>1) {
		$pdf->newPage();
	}
	$YPos = $Page_Height-$Top_Margin;

	$FontSize=10;
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-140,$YPos,140,$FontSize, _('Printed').': ' . Date($_SESSION['DefaultDateFormat']) . '   '. _('Page'). ' ' . $PageNumber);
	$YPos -= $line_height;

	//Note, this is ok for multilang as this is the value of a Select, text in option is different
	if ($_POST['CustomerSpecials']==_('Customer Special Prices Only')) {
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,550,$FontSize, $CustomerName . ' ' . _('Prices for Categories').' ' . $_POST['FromCriteria'] . ' - ' . $_POST['ToCriteria'] . ' ' . _('Effective As At') . ' ' . $_POST['EffectiveDate']);
	} else {
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,550,$FontSize, $SalesTypeName . ' ' ._('Prices For Categories') . ' ' . $_POST['FromCriteria'] . ' - ' . $_POST['ToCriteria'] . ' ' . _('Effective As At') . ' ' . $_POST['EffectiveDate'] );
	}

	$YPos -=(2*$line_height);
	/*Draw a rectangle to put the headings in */

	$pdf->line($Left_Margin, $YPos+$line_height, $Page_Width-$Right_Margin, $YPos+$line_height);
	$pdf->line($Left_Margin, $YPos+$line_height, $Left_Margin, $YPos- $line_height);
	$pdf->line($Left_Margin, $YPos-$line_height, $Page_Width-$Right_Margin, $YPos- $line_height);
	$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height, $Page_Width-$Right_Margin, $YPos- $line_height);

	/*set up the headings */
	$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 80, $FontSize, _('Item Code'));// 20chr @ 8dpi.
	if($LeftOvers != '') {// If translated text is greater than column width, prints remainder.
		$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos-$FontSize, 80, $FontSize, $LeftOvers);
	}
	$LeftOvers = $pdf->addTextWrap($Left_Margin+80, $YPos, 200,$FontSize, _('Item Description'));// 50chr @ 8dpi.
	if($LeftOvers != '') {// If translated text is greater than column width, prints remainder.
		$LeftOvers = $pdf->addTextWrap($Left_Margin+80, $YPos-$FontSize, 200, $FontSize, $LeftOvers);
	}
	$LeftOvers = $pdf->addTextWrap($Left_Margin+280, $YPos, 96, $FontSize, _('Effective Date Range'), 'center');// (10+2+12)chr @ 8dpi.
	if($LeftOvers != '') {// If translated text is greater than column width, prints remainder.
		$LeftOvers = $pdf->addTextWrap($Left_Margin+280, $YPos-$FontSize, 96, $FontSize, $LeftOvers, 'center');
	}
			
	if ($_POST['CustomerSpecials']=='Customer Special Prices Only') {
		$LeftOvers = $pdf->addTextWrap($Left_Margin+376, $YPos, 160, $FontSize, _('Branch'));// 40chr @ 8dpd.
	}

	if ($_POST['ShowGPPercentages']=='Yes') {
		$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-128, $YPos, 32, $FontSize, _('Gross Profit'), 'right');// 8chr @ 8dpi.
		if($LeftOvers != '') {// If translated text is greater than column width, prints remainder.
			$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-128, $YPos-$FontSize, 32, $FontSize, $LeftOvers, 'right');
		}
	}
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-96, $YPos, 96, $FontSize, _('Price') , 'right');// 24chr @ 8dpd.

	$YPos -= (1.5 * $line_height);
}
?>