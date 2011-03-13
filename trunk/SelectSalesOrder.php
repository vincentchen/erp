<?php

/* $Id$*/

include('includes/session.inc');
$title = _('Search Outstanding Sales Orders');
include('includes/header.inc');


if (isset($_POST['PlacePO'])){ /*user hit button to place PO for selected orders */
	
	/*Note the button would not have been displayed if the user had no authority to create purchase orders */
	$OrdersToPlacePOFor = '';
	for ($i=1;$i<count($_POST);$i++){
		if (isset($_POST['PlacePO_' . $i])) { //checkboxes only set if they are checked
			if ($OrdersToPlacePOFor==''){
				$OrdersToPlacePOFor .= ' orderno=' . $_POST['OrderNo_PO_'.$i];
			} else {
				$OrdersToPlacePOFor .= ' OR orderno=' . $_POST['OrderNo_PO_'.$i];
			}
		}
	}
	if (strlen($OrdersToPlacePOFor)==''){
		prnMsg(_('There were no sales orders checked to place purchase orders for. No purchase orders will be created.'),'info');
	} else {
   /*  Now build SQL of items to purchase with purchasing data and preferred suppliers - sorted by preferred supplier */
		$sql = "SELECT purchdata.supplierno,
					 purchdata.stockid,
					 purchdata.price,
					 purchdata.suppliers_partno,
					 purchdata.supplierdescription,
					 purchdata.conversionfactor,
					 purchdata.leadtime,
					 purchdata.suppliersuom,
					 stockmaster.kgs,
					 stockmaster.volume,
					 stockcategory.stockact,
					 SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS orderqty
				FROM purchdata INNER JOIN salesorderdetails ON
					 purchdata.stockid = salesorderdetails.stkcode
					 INNER JOIN stockmaster  ON
					 purchdata.stockid = stockmaster.stockid
					 INNER JOIN stockcategory ON
					 stockmaster.categoryid = stockcategory.categoryid
				WHERE purchdata.preferred=1 
				AND purchdata.effectivefrom <='" . Date('Y-m-d') . "'
				AND (" . $OrdersToPlacePOFor . ")
				GROUP BY purchdata.supplierno,
							purchdata.stockid,
							purchdata.price,
							purchdata.suppliers_partno,
							purchdata.supplierdescription,
							purchdata.conversionfactor,
							purchdata.leadtime,
							purchdata.suppliersuom,
							stockmaster.kgs,
							stockmaster.volume,
							stockcategory.stockact
				ORDER BY purchdata.supplierno,
							 purchdata.stockid";
		$ErrMsg = _('Unable to retrieve the items on the selected orders for creating purchase orders for');
		$ItemResult = DB_query($sql,$db,$ErrMsg);
		
		if (DB_num_rows($ItemResult)==0){
			prnMsg(_('There might be no supplier purchasing data set up for any items on the selected sales order(s). No purchase orders have been created'),'warn');
		} else {
			/*Now get the default delivery address details from the users default stock location */
			$sql = "SELECT locationname,
							deladd1,
							deladd2,
							deladd3,
							deladd4,
							deladd5,
							deladd6,
							tel,
							contact
					FROM locations 
					WHERE loccode = '" .$_SESSION['UserStockLocation']  . "'";
			$ErrMsg = _('The delivery address for the order could not be obtained from the user default stock location');
			$DelAddResult = DB_query($sql, $db,$ErrMsg);
			$DelAddRow = DB_fetch_array($DelAddResult);
			
			$SupplierID = '';
			if (IsEmailAddress($_SESSION['UserEmail'])){
				$UserDetails  = ' <a href="mailto:' . $_SESSION['UserEmail'] . '">' . $_SESSION['UsersRealName']. '</a>';
			} else {
				$UserDetails  = ' ' . $_SESSION['UsersRealName'] . ' ';
			}
			
			while ($ItemRow = DB_fetch_array($ItemResult)){
				
				if ($SupplierID != $ItemRow['supplierno']){ 
					/* This order item is purchased from a different supplier so need to finish off the authorisation of the previous order and start a new order */
					
					if ($SupplierID !='' AND $_SESSION['AutoAuthorisePO']==1) { 
						//if the user has authority to authorise the PO then it should be created as authorised
						$AuthSQL ="SELECT authlevel
									FROM purchorderauth
									WHERE userid='".$_SESSION['UserID']."'
									AND currabrev='".$SuppRow['currcode']."'";
			
						$AuthResult=DB_query($AuthSQL,$db);
						$AuthRow=DB_fetch_array($AuthResult);
					
						if (DB_num_rows($AuthResult) > 0 AND $AuthRow['authlevel'] > $Order_Value) { //user has authority to authrorise as well as create the order
							$StatusComment=date($_SESSION['DefaultDateFormat']).' - ' . _('Order Created and Authorised by') . $UserDetails . ' - '._('Auto created from sales orders') .'<br />';
							$result = DB_query("UPDATE purchorders SET allowprint=1, 
																status='Authorised',
																statuscomment='" . $StatusComment . "'
												WHERE orderno='" . $OrderNo . "'",
												$db);
						} else { // no authority to authorise this order
							if (DB_num_rows($AuthResult) ==0){
								$AuthMessage = _('Your authority to approve purchase orders in') . ' ' .$SuppRow['currcode'] . ' ' . _('has not yet been set up') . '<br />';
							} else {
								$AuthMessage = _('You can only authorise up to').' '.$SuppRow['currcode'].' '.$AuthRow['authlevel'].'.<br />';
							}
						
							prnMsg( _('You do not have permission to authorise this purchase order').'.<br />'. _('This order is for').' '.
							$SuppRow['currcode'] . ' '. $Order_Value .'. '.
							$AuthMessage . _('If you think this is a mistake please contact the systems administrator') . '<br />'.
							_('The order has been created with a status of pending and will require authorisation'), 'warn');
						}
					} //end of authorisation status settings
					
					if ($SupplierID !=''){ //then we have just added a purchase order
						echo '<p>';
						prnMsg(_('Purchase Order') . ' ' . $PO_OrderNo . ' ' . _('on') . ' ' . $ItemRow['supplierno'] . ' ' . _('has been created'),'success');
						DB_Txn_Commit($db);
					}
					/*Starting a new purchase order with a different supplier */
					$result = DB_Txn_Begin($db);
					include('includes/SQL_CommonFunctions.inc');
					$PO_OrderNo =  GetNextTransNo(18, $db); //get the next PO number
					
					$SupplierID = $ItemRow['supplierno'];
					$Order_Value =0;
					/*Now get all the required details for the supplier */
					$sql = "SELECT address1,
								address2,
								address3,
								address4,
								address5,
								address6,
								telephone,
								paymentterms,
								currcode,
								rate
							FROM suppliers INNER JOIN currencies
							ON suppliers.currcode = currencies.currabrev
							WHERE supplierid='" . $SupplierID . "'";
					
					$ErrMsg = _('Could not get the supplier information for the order');
					$SuppResult = DB_query($sql, $db, $ErrMsg);
					$SuppRow = DB_fetch_array($SuppResult);
					
					$StatusComment=date($_SESSION['DefaultDateFormat']).' - ' . _('Order Created by') . ' ' . $UserDetails . ' - '._('Auto created from sales orders') .'<br />';
					/*Insert to purchase order header record */
					$sql = "INSERT INTO purchorders (	orderno,
													supplierno,
													orddate,
													rate,
													initiator,
													intostocklocation,
													deladd1,
													deladd2,
													deladd3,
													deladd4,
													deladd5,
													deladd6,
													tel,
													suppdeladdress1,
													suppdeladdress2,
													suppdeladdress3,
													suppdeladdress4,
													suppdeladdress5,
													suppdeladdress6,
													supptel,
													version,
													revised,
													deliveryby,
													status,
													stat_comment,
													deliverydate,
													paymentterms,
													allowprint)
									VALUES(	'" . $PO_OrderNo . "',
										'" . $SupplierID . "',
										'" . Date('Y-m-d') . "',
										'" . $SuppRow['rate'] . "',
										'" . $_SESSION['UsersRealName'] . "',
										'" . $_SESSION['UserStockLocation'] . "',
										'" . $DelAddRow['locationname'] . "',
										'" . $DelAddRow['deladd1'] . "',
										'" . $DelAddRow['deladd2'] . "',
										'" . $DelAddRow['deladd3'] . "',
										'" . $DelAddRow['deladd4'] . "',
										'" . $DelAddRow['deladd5'] . ' ' . $DelAddRow['deladd6'] . "',
										'" . $DelAddRow['tel'] . "',
										'" . $SuppRow['address1'] . "',
										'" . $SuppRow['address2'] . "',
										'" . $SuppRow['address3'] . "',
										'" . $SuppRow['address4'] . "',
										'" . $SuppRow['address5'] . "',
										'" . $SuppRow['address6'] . "',
										'" . $SuppRow['telephone'] . "',
										'1.0',
										'" . Date('Y-m-d') . "',
										'" . $_SESSION['Default_Shipper'] . "',
										'Pending',
										'" . $StatusComment . "',
										'" . Date('Y-m-d') . "',
										'" . $SuppRow['paymentterms'] . "',
										0)";
		
					$ErrMsg =  _('The purchase order header record could not be inserted into the database because');
					$DbgMsg = _('The SQL statement used to insert the purchase order header record and failed was');
					$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
				} //end if it's a new supplier and PO to create
				
				/*reminder we are in a loop of the total of each item to place a purchase order for based on a selection of sales orders */
				$DeliveryDate = DateAdd(Date($_SESSION['DefaultDateFormat']),'d',$ItemRow['leadtime']);
				$sql = "INSERT INTO purchorderdetails ( orderno,
													itemcode,
													deliverydate,
													itemdescription,
													glcode,
													unitprice,
													quantityord,
													suppliersunit,
													suppliers_partno,
													kgs,
													cuft,
													conversionfactor )
											VALUES (
													'" . $PO_OrderNo . "',
													'" . $ItemRow['stockid'] . "',
													'" . FormatDateForSQL($DeliveryDate) . "',
													'" . $ItemRow['suppliers_partno']  . '  ' . $ItemRow['supplierdescription']  . "',
													'" . $ItemRow['stockact'] . "',
													'" . $ItemRow['price'] . "',
													'" . $ItemRow['orderqty'] . "',
													'" . $ItemRow['suppliersuom'] . "',
													'" . $ItemRow['suppliers_partno'] . "',
													'" . $ItemRow['kgs'] . "',
													'" . $ItemRow['volume'] . "',
													'" . $ItemRow['conversionfactor']  . "')";
				$ErrMsg =_('One of the purchase order detail records could not be inserted into the database because');
				$DbgMsg =_('The SQL statement used to insert the purchase order detail record and failed was');
				
				$result =DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
				$Order_Value  += ($ItemRow['price']*$ItemRow['orderqty']);
			} /* end of the loop round the items on the sales order  that we wish to place purchase orders for */
			if ($SupplierID !=''){ //then we have just added a purchase order
				echo '<p>';
				prnMsg(_('Purchase Order') . ' ' . $PO_OrderNo . ' ' . _('on') . ' ' . $ItemRow['supplierno'] . ' ' . _('has been created'),'success');
				DB_Txn_Commit($db);
			}
			$result = DB_query("UPDATE salesorders SET poplaced=1 WHERE " . $OrdersToPlacePOFor,$db);
		}/*There were items that had purchasing data set up to create POs for */
	} /* there were sales orders checked to place POs for */
}/*end of purchase order creation code */
/* ******************************************************************************************* */




/*To the sales order selection form */

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/sales.png" title="' . _('Sales') . '" alt="" />' . ' ' . _('Outstanding Sales Orders') . '</p> ';

echo '<form action=' . $_SERVER['PHP_SELF'] .'?' .SID . ' method=post>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';


if (isset($_POST['ResetPart'])){
     unset($_REQUEST['SelectedStockItem']);
}

echo '<p><div class="centre">';

if (isset($_REQUEST['OrderNumber']) AND $_REQUEST['OrderNumber']!='') {
	$_REQUEST['OrderNumber'] = trim($_REQUEST['OrderNumber']);
	if (!is_numeric($_REQUEST['OrderNumber'])){
		echo '<br><b>' . _('The Order Number entered MUST be numeric') . '</b><br>';
		unset ($_REQUEST['OrderNumber']);
		include('includes/footer.inc');
		exit;
	} else {
		echo _('Order Number') . ' - ' . $_REQUEST['OrderNumber'];
	}
} else {
	if (isset($_REQUEST['SelectedCustomer'])) {
		echo _('For customer') . ': ' . $_REQUEST['SelectedCustomer'] . ' ' . _('and') . ' ';
		echo "<input type=hidden name='SelectedCustomer' value=" . $_REQUEST['SelectedCustomer'] . '>';
	}
	if (isset($_REQUEST['SelectedStockItem'])) {
		 echo _('for the part') . ': ' . $_REQUEST['SelectedStockItem'] . ' ' . _('and') . " <input type=hidden name='SelectedStockItem' value='" . $_REQUEST['SelectedStockItem'] . "'>";
	}
}

if (isset($_POST['SearchParts'])){

	if ($_POST['Keywords'] AND $_POST['StockCode']) {
		echo _('Stock description keywords have been used in preference to the Stock code extract entered');
	}
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

		$SQL = "SELECT stockmaster.stockid,
				stockmaster.description,
				SUM(locstock.quantity) AS qoh,
				stockmaster.units
			FROM stockmaster,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.description " . LIKE . " '" . $SearchString . "'
			AND stockmaster.categoryid='" . $_POST['StockCat']. "'
			GROUP BY stockmaster.stockid,
				stockmaster.description,
				stockmaster.units
			ORDER BY stockmaster.stockid";

	 } elseif (isset($_POST['StockCode'])){
		$SQL = "SELECT stockmaster.stockid,
				stockmaster.description,
				sum(locstock.quantity) as qoh,
				stockmaster.units
			FROM stockmaster,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%'
			AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
			GROUP BY stockmaster.stockid,
				stockmaster.description,
				stockmaster.units
			ORDER BY stockmaster.stockid";

	 } elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		$SQL = "SELECT stockmaster.stockid,
				stockmaster.description,
				sum(locstock.quantity) as qoh,
				stockmaster.units
			FROM stockmaster,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			AND stockmaster.categoryid='" . $_POST['StockCat'] ."'
			GROUP BY stockmaster.stockid,
				stockmaster.description,
				stockmaster.units
			ORDER BY stockmaster.stockid";
	 }

	$ErrMsg =  _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL used to retrieve the searched parts was');
	$StockItemsResult = DB_query($SQL,$db,$ErrMsg,$DbgMsg);

}

if (isset($_POST['StockID'])){
	$StockID = trim(strtoupper($_POST['StockID']));
} elseif (isset($_GET['StockID'])){
	$StockID = trim(strtoupper($_GET['StockID']));
}

if (!isset($StockID)) {

     /* Not appropriate really to restrict search by date since may miss older
     ouststanding orders
	$OrdersAfterDate = Date('d/m/Y',Mktime(0,0,0,Date('m')-2,Date('d'),Date('Y')));
     */

	if (!isset($_REQUEST['OrderNumber']) or $_REQUEST['OrderNumber']==''){

		echo '<table class=selection>';
		echo '<tr><td>' . _('Order number') . ": </td><td><input type=text name='OrderNumber' maxlength=8 size=9></td><td>" .
				_('From Stock Location') . ":</td><td><select name='StockLocation'> ";

		$sql = 'SELECT loccode, locationname FROM locations';

		$resultStkLocs = DB_query($sql,$db);

		while ($myrow=DB_fetch_array($resultStkLocs)){
			if (isset($_POST['StockLocation'])){
				if ($myrow['loccode'] == $_POST['StockLocation']){
				     echo '<option selected Value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
				} else {
				     echo '<option Value="' . $myrow['loccode'] . '">' . $myrow['locationname']. '</option>';
				}
			} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
				 echo '<option selected Value="' . $myrow['loccode'] . '">' . $myrow['locationname']. '</option>';
			} else {
				 echo '<option Value="' . $myrow['loccode'] . '">' . $myrow['locationname']. '</option>';
			}
		}

		echo '</select></td><td>';
		echo '<select name="Quotations">';

		if ($_GET['Quotations']=='Quotes_Only'){
			$_POST['Quotations']='Quotes_Only';
		}

		if ($_POST['Quotations']=='Quotes_Only'){
			echo '<option selected VALUE="Quotes_Only">' . _('Quotations Only');
			echo '<option VALUE="Orders_Only">' . _('Orders Only');
		} else {
			echo '<option selected VALUE="Orders_Only">' . _('Orders Only');
			echo '<option VALUE="Quotes_Only">' . _('Quotations Only');
		}

		echo '</select> </td><td>';
		echo '<input type=submit name="SearchOrders" VALUE="' . _('Search') . '"></td>';
    echo '&nbsp;&nbsp;<td><a href="' . $rootpath . '/SelectOrderItems.php?' . SID . '&NewOrder=Yes">' .
		_('Add Sales Order') . '</a></td></tr></table>';
	}

	$SQL='SELECT categoryid,
			categorydescription
		FROM stockcategory
		ORDER BY categorydescription';

	$result1 = DB_query($SQL,$db);

	echo '<br /><table class="selection">';
	echo '<tr><th colspan=6><font size=3 color=navy>' . _('To search for sales orders for a specific part use the part selection facilities below');
	echo '</th></tr>';
	echo '<tr>
      		<td><font size="1">' . _('Select a stock category') . ':</font>
      			<select name="StockCat">';

	while ($myrow1 = DB_fetch_array($result1)) {
		echo '<option value="'. $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	}

      echo '</select>
      		<td><font size=1>' . _('Enter text extract(s) in the description') . ":</font></td>
      		<td><input type='Text' name='Keywords' size=20 maxlength=25></td>
	</tr>
      	<tr><td></td>
      		<td><font size 3><b>" . _('OR') . ' </b></font><font size=1>' . _('Enter extract of the Stock Code') . "</b>:</font></td>
      		<td><input type='Text' name='StockCode' size=15 maxlength=18></td>
      	</tr>
      </table>";
	echo "<br /><input type=submit name='SearchParts' VALUE='" . _('Search Parts Now') .
			"'><input type=submit name='ResetPart' VALUE='" . _('Show All') . "'></div><br />";

if (isset($StockItemsResult) and DB_num_rows($StockItemsResult)>0) {

	echo '<table cellpadding=2 colspan=7 class=selection>';
	$TableHeader = '<tr>
				<th>' . _('Code') . '</th>
				<th>' . _('Description') . '</th>
				<th>' . _('On Hand') . '</th>
				<th>' . _('Units') . '</th>
			</tr>';
	echo $TableHeader;

	$j = 1;
	$k=0; //row colour counter

	while ($myrow=DB_fetch_array($StockItemsResult)) {

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}

		printf("<td><input type=submit name='SelectedStockItem' VALUE='%s'</td>
			<td>%s</td>
			<td class=number>%s</td>
			<td>%s</td>
			</tr>",
			$myrow['stockid'],
			$myrow['description'],
			$myrow['qoh'],
			$myrow['units']);

		$j++;
		if ($j == 12){
			$j=1;
			echo $TableHeader;
		}
//end of page full new headings if
	}
//end of while loop

	echo '</table>';

}
//end if stock search results to show
  else {

	//figure out the SQL required from the inputs available
	if (isset($_POST['Quotations']) AND $_POST['Quotations']=='Orders_Only'){
		$Quotations = 0;
	} else {
		$Quotations =1;
	}
	if(!isset($_POST['StockLocation'])) {
		$_POST['StockLocation'] = '';
	}
	if (isset($_REQUEST['OrderNumber']) && $_REQUEST['OrderNumber'] !='') {
			$SQL = "SELECT salesorders.orderno,
					debtorsmaster.name,
					custbranch.brname,
					salesorders.customerref,
					salesorders.orddate,
					salesorders.deliverydate,
					salesorders.deliverto,
					salesorders.printedpackingslip,
					salesorders.poplaced,
					SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue
				FROM salesorders,
					salesorderdetails,
					debtorsmaster,
					custbranch
				WHERE salesorders.orderno = salesorderdetails.orderno
				AND salesorders.branchcode = custbranch.branchcode
				AND salesorders.debtorno = debtorsmaster.debtorno
				AND debtorsmaster.debtorno = custbranch.debtorno
				AND salesorderdetails.completed=0
				AND salesorders.orderno=". $_REQUEST['OrderNumber'] ."
				AND salesorders.quotation =" .$Quotations . "
				GROUP BY salesorders.orderno,
					debtorsmaster.name,
					custbranch.brname,
					salesorders.customerref,
					salesorders.orddate,
					salesorders.deliverydate,
					salesorders.deliverto,
					salesorders.printedpackingslip
				ORDER BY salesorders.orderno";
	} else {
	      /* $DateAfterCriteria = FormatDateforSQL($OrdersAfterDate); */

		if (isset($_REQUEST['SelectedCustomer'])) {

			if (isset($_REQUEST['SelectedStockItem'])) {
				$SQL = "SELECT salesorders.orderno,
						debtorsmaster.name,
						custbranch.brname,
						salesorders.customerref,
						salesorders.orddate,
						salesorders.deliverydate,
						salesorders.deliverto,
						salesorders.printedpackingslip,
						salesorders.poplaced,
						salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent) AS ordervalue
					FROM salesorders,
						salesorderdetails,
						debtorsmaster,
						custbranch
					WHERE salesorders.orderno = salesorderdetails.orderno
					AND salesorders.debtorno = debtorsmaster.debtorno
					AND debtorsmaster.debtorno = custbranch.debtorno
					AND salesorders.branchcode = custbranch.branchcode
					AND salesorderdetails.completed=0
					AND salesorders.quotation =" .$Quotations . "
					AND salesorderdetails.stkcode='". $_REQUEST['SelectedStockItem'] ."'
					AND salesorders.debtorno='" . $_REQUEST['SelectedCustomer'] ."'
					AND salesorders.fromstkloc = '". $_POST['StockLocation'] . "'
					ORDER BY salesorders.orderno";


			} else {
				$SQL = "SELECT salesorders.orderno,
						debtorsmaster.name,
						custbranch.brname,
						salesorders.customerref,
						salesorders.orddate,
						salesorders.deliverto,
						salesorders.printedpackingslip,
						salesorders.poplaced,
						salesorders.deliverydate, SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue
					FROM salesorders,
						salesorderdetails,
						debtorsmaster,
						custbranch
					WHERE salesorders.orderno = salesorderdetails.orderno
					AND salesorders.debtorno = debtorsmaster.debtorno
					AND debtorsmaster.debtorno = custbranch.debtorno
					AND salesorders.branchcode = custbranch.branchcode
					AND salesorders.quotation =" .$Quotations . "
					AND salesorderdetails.completed=0
					AND salesorders.debtorno='" . $_REQUEST['SelectedCustomer'] . "'
					AND salesorders.fromstkloc = '". $_POST['StockLocation'] . "'
					GROUP BY salesorders.orderno,
						debtorsmaster.name,
						salesorders.debtorno,
						custbranch.brname,
						salesorders.customerref,
						salesorders.orddate,
						salesorders.deliverto,
						salesorders.deliverydate
					ORDER BY salesorders.orderno";

			}
		} else { //no customer selected
			if (isset($_REQUEST['SelectedStockItem'])) {
				$SQL = "SELECT salesorders.orderno,
						debtorsmaster.name,
						custbranch.brname,
						salesorders.customerref,
						salesorders.orddate,
						salesorders.deliverto,
					  	salesorders.printedpackingslip,
					  	salesorders.poplaced,
						salesorders.deliverydate, SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue
					FROM salesorders,
						salesorderdetails,
						debtorsmaster,
						custbranch
					WHERE salesorders.orderno = salesorderdetails.orderno
					AND salesorders.debtorno = debtorsmaster.debtorno
					AND debtorsmaster.debtorno = custbranch.debtorno
					AND salesorders.branchcode = custbranch.branchcode
					AND salesorderdetails.completed=0
					AND salesorders.quotation =" .$Quotations . "
					AND salesorderdetails.stkcode='". $_REQUEST['SelectedStockItem'] . "'
					AND salesorders.fromstkloc = '". $_POST['StockLocation'] . "'
					GROUP BY salesorders.orderno,
						debtorsmaster.name,
						custbranch.brname,
						salesorders.customerref,
						salesorders.orddate,
						salesorders.deliverto,
						salesorders.deliverydate,
						salesorders.printedpackingslip
					ORDER BY salesorders.orderno";
			} else {
				$SQL = "SELECT salesorders.orderno,
						debtorsmaster.name,
						custbranch.brname,
						salesorders.customerref,
						salesorders.orddate,
						salesorders.deliverto,
						salesorders.deliverydate,
						salesorders.printedpackingslip,
						salesorders.poplaced,
						SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue
					FROM salesorders,
						salesorderdetails,
						debtorsmaster,
						custbranch
					WHERE salesorders.orderno = salesorderdetails.orderno
					AND salesorders.debtorno = debtorsmaster.debtorno
					AND debtorsmaster.debtorno = custbranch.debtorno
					AND salesorders.branchcode = custbranch.branchcode
					AND salesorderdetails.completed=0
					AND salesorders.quotation =" .$Quotations . "
					AND salesorders.fromstkloc = '". $_POST['StockLocation'] . "'
					GROUP BY salesorders.orderno,
						debtorsmaster.name,
						custbranch.brname,
						salesorders.customerref,
						salesorders.orddate,
						salesorders.deliverto,
						salesorders.deliverydate,
						salesorders.printedpackingslip
					ORDER BY salesorders.orderno";
			}

		} //end selected customer
	} //end not order number selected

	$ErrMsg = _('No orders or quotations were returned by the SQL because');
	$SalesOrdersResult = DB_query($SQL,$db,$ErrMsg);

	/*show a table of the orders returned by the SQL */
	if (DB_num_rows($SalesOrdersResult)>0) {
		
                /* Get users authority to place POs */
                $AuthSQL="SELECT cancreate
				FROM purchorderauth
				WHERE userid='". $_SESSION['UserID'] . "'";
			
		/*we don't know what currency these orders might be in but if no authority at all then don't show option*/
		$AuthResult=DB_query($AuthSQL,$db);
		$AuthRow=DB_fetch_array($AuthResult);

                echo '<table cellpadding=2 colspan=7 width=95% class=selection>';

		if (isset($_POST['Quotations']) and $_POST['Quotations']=='Orders_Only'){
			$tableheader = '<tr>
						<th>' . _('Modify') . '</th>
						<th>' . _('Invoice') . '</th>
						<th>' . _('Dispatch Note') . '</th>
						<th>' . _('Sales Order') . '</th>
						<th>' . _('Customer') . '</th>
						<th>' . _('Branch') . '</th>
						<th>' . _('Cust Order') . ' #</th>
						<th>' . _('Order Date') . '</th>
						<th>' . _('Req Del Date') . '</th>
						<th>' . _('Delivery To') . '</th>
						<th>' . _('Order Total') . '</th>';
			if ($AuthRow['cancreate']==0){ //If cancreate==0 then this means the user can create orders hmmm!!
				$tableheader .= '<th>' . _('Place PO') . '</th></tr>';
			} else {
				$tableheader .= '</tr>';
			}
		} else {  /* displaying only quotations */
			$tableheader = '<tr>
						<th>' . _('Modify') . '</th>
						<th>' . _('Print Quote') . '</th>
						<th>' . _('Customer') . '</th>
						<th>' . _('Branch') . '</th>
						<th>' . _('Cust Ref') . ' #</th>
						<th>' . _('Quote Date') . '</th>
						<th>' . _('Req Del Date') . '</th>
						<th>' . _('Delivery To') . '</th>
						<th>' . _('Quote Total') . '</th></tr>';
		}

		echo $tableheader;

		$i = 1;
                $j = 1;
		$k=0; //row colour counter
		while ($myrow=DB_fetch_array($SalesOrdersResult)) {


			if ($k==1){
				echo '<tr class="EvenTableRows">';
				$k=0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}

			$ModifyPage = $rootpath . "/SelectOrderItems.php?" . SID . '&ModifyOrderNumber=' . $myrow['orderno'];
			$Confirm_Invoice = $rootpath . '/ConfirmDispatch_Invoice.php?' . SID . '&OrderNumber=' .$myrow['orderno'];
	
			if ($_SESSION['PackNoteFormat']==1){ /*Laser printed A4 default */
				$PrintDispatchNote = $rootpath . '/PrintCustOrder_generic.php?' . SID . '&TransNo=' . $myrow['orderno'];
			} else { /*pre-printed stationery default */
				$PrintDispatchNote = $rootpath . '/PrintCustOrder.php?' . SID . '&TransNo=' . $myrow['orderno'];
			}
			$PrintSalesOrder = $rootpath . '/PrintSalesOrder_generic.php?' . SID . '&TransNo=' . $myrow['orderno'];
			$PrintQuotation = $rootpath . '/PDFQuotation.php?' . SID . '&QuotationNo=' . $myrow['orderno'];
			$FormatedDelDate = ConvertSQLDate($myrow['deliverydate']);
			$FormatedOrderDate = ConvertSQLDate($myrow['orddate']);
			$FormatedOrderValue = number_format($myrow['ordervalue'],2);
	
			if ($myrow['printedpackingslip']==0) {
			  $PrintText = _('Print');
			} else {
			  $PrintText = _('Reprint');
			}
	
			if ($_POST['Quotations']=='Orders_Only'){
	
	                     /*Check authority to create POs if user has authority then show the check boxes to select sales orders to place POs for otherwise don't provide this option */
	                        if ($AuthRow['cancreate']==0 AND $myrow['poplaced']==0){ //cancreate==0 if the user can create POs and not already placed
	        			printf("<td><a href='%s'>%s</a></td>
	        				<td><a href='%s'>" . _('Invoice') . "</a></td>
	        				<td><a target='_blank' href='%s'>" . $PrintText . " <IMG SRC='" .$rootpath."/css/".$theme."/images/pdf.png' title='" . _('Click for PDF') . "'></a></td>
	        				<td><a target='_blank' href='%s'>" . $PrintText . " <IMG SRC='" .$rootpath."/css/".$theme."/images/pdf.png' title='" . _('Click for PDF') . "'></a></td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td class=number>%s</td>
	        				<td><input type=checkbox name=PlacePO_%s value><input type=hidden name=OrderNo_PO_%s value=%s></td>
	        				</tr>",
	        				$ModifyPage,
	        				$myrow['orderno'],
	        				$Confirm_Invoice,
	        				$PrintDispatchNote,
	        				$PrintSalesOrder,
	        				$myrow['name'],
	        				$myrow['brname'],
	        				$myrow['customerref'],
	        				$FormatedOrderDate,
	        				$FormatedDelDate,
	        				$myrow['deliverto'],
	        				$FormatedOrderValue,
	                                        $i,
	                                        $i,
	                                        $myrow['orderno']);
	                        } else {  /*User is not authorised to create POs so don't even show the option */
	                               	printf("<td><a href='%s'>%s</a></td>
	        				<td><a href='%s'>" . _('Invoice') . "</a></td>
	        				<td><a target='_blank' href='%s'>" . $PrintText . " <IMG SRC='" .$rootpath."/css/".$theme."/images/pdf.png' title='" . _('Click for PDF') . "'></a></td>
	        				<td><a target='_blank' href='%s'>" . $PrintText . " <IMG SRC='" .$rootpath."/css/".$theme."/images/pdf.png' title='" . _('Click for PDF') . "'></a></td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td>%s</td>
	        				<td class=number>%s</td>
	        				</tr>",
	        				$ModifyPage,
	        				$myrow['orderno'],
	        				$Confirm_Invoice,
	        				$PrintDispatchNote,
	        				$PrintSalesOrder,
	        				$myrow['name'],
	        				$myrow['brname'],
	        				$myrow['customerref'],
	        				$FormatedOrderDate,
	        				$FormatedDelDate,
	        				$myrow['deliverto'],
	        				$FormatedOrderValue);
	                        }
	
			} else { /*must be quotes only */
				printf("<td><a href='%s'>%s</a></td>
					<td><a href='%s'>" . $PrintText . "</a></td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td class=number>%s</td>
					</tr>",
					$ModifyPage,
					$myrow['orderno'],
					$PrintQuotation,
					$myrow['name'],
					$myrow['brname'],
					$myrow['customerref'],
					$FormatedOrderDate,
					$FormatedDelDate,
					$myrow['deliverto'],
					$FormatedOrderValue);
			}
	                $i++;
			$j++;
			if ($j == 12){
				$j=1;
				echo $tableheader;
			}
		//end of page full new headings if
		}//end while loop through orders to display
		if ($_POST['Quotations']=='Orders_Only'  AND $AuthRow['cancreate']==0){ //cancreate==0 means can create POs
			echo '<tr><td colspan="10"><td><td colspan="2"><input type="submit" name="PlacePO" value="' . _('Place PO') . '" onclick="return confirm(\'' . _('This will create purchase orders for all the items on the checked sales orders above, based on the preferred supplier purchasing data held in the system. Are You Absolutely Sure?') . '\');"></td</tr>';
		}
		echo '</table>';
	} //end if there are some orders to show  
}

?>
</form>

<?php } //end StockID already selected

include('includes/footer.inc');
?>