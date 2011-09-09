<?php

/* $Id$*/

/*The supplier transaction uses the SuppTrans class to hold the information about the credit note
the SuppTrans class contains an array of GRNs objects - containing details of GRNs for invoicing and also
an array of GLCodes objects - only used if the AP - GL link is effective */


include('includes/DefineSuppTransClass.php');
/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');

$title = _('Enter Supplier Credit Note Against Goods Received');

include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/magnifier.png" title="' . _('Dispatch') .
		'" alt="" />' . ' ' . $title . '</p>';

if (!isset($_SESSION['SuppTrans'])){
	prnMsg(_('To enter a supplier transactions the supplier must first be selected from the supplier selection screen') . ', ' . _('then the link to enter a supplier credit note must be clicked on'),'info');
	echo '<br /><a href="' . $rootpath . '/SelectSupplier.php">' . _('Select A Supplier to Enter a Transaction For') . '</a>';
	include('includes/footer.inc');
	exit;
	/*It all stops here if there aint no supplier selected and credit note initiated ie $_SESSION['SuppTrans'] started off*/
}

/*If the user hit the Add to Credit Note button then process this first before showing all GRNs on the credit note otherwise it wouldnt show the latest addition*/

if (isset($_POST['AddGRNToTrans'])){

	$InputError=False;

	$Complete = False;

	if (!is_numeric($_POST['ChgPrice']) AND $_POST['ChgPrice']<0){
		$InputError = True;
		prnMsg(_('The price charged in the suppliers currency is either not numeric or negative') . '. ' . _('The goods received cannot be credited at this price'),'error');
	}

	if ($InputError==False){
		$_SESSION['SuppTrans']->Add_GRN_To_Trans($_POST['GRNNumber'],
												$_POST['PODetailItem'],
												$_POST['ItemCode'],
												$_POST['ItemDescription'],
												$_POST['QtyRecd'],
												$_POST['Prev_QuantityInv'],
												$_POST['This_QuantityCredited'],
												$_POST['OrderPrice'],
												$_POST['ChgPrice'],
												$Complete,
												$_POST['StdCostUnit'],
												$_POST['ShiptRef'],
												$_POST['JobRef'],
												$_POST['GLCode'],
												$_POST['PONo'],
												$_POST['AssetID']);
	}
}

if (isset($_GET['Delete'])){

	$_SESSION['SuppTrans']->Remove_GRN_From_Trans($_GET['Delete']);

}

/*Show all the selected GRNs so far from the SESSION['SuppTrans']->GRNs array */

echo '<table cellpadding=0 class=selection>';
echo '<tr><th colspan=6><font size=3 color=navy>' . _('Credits Against Goods Received Selected') . '</font></th></tr>';
$TableHeader = '<tr><th>' . _('GRN') . '</th>
					<th>' . _('Item Code') . '</th>
					<th>' . _('Description') . '</th>
					<th>' . _('Quantity Credited') . '</th>
					<th>' . _('Price Credited in') . ' ' . $_SESSION['SuppTrans']->CurrCode . '</th>
					<th>' . _('Line Value in') . ' ' . $_SESSION['SuppTrans']->CurrCode . '</th></tr>';

echo $TableHeader;

$TotalValueCharged=0;
$i=0;

foreach ($_SESSION['SuppTrans']->GRNs as $EnteredGRN){

	echo '<tr><td>' . $EnteredGRN->GRNNo . '</td>
            <td>' . $EnteredGRN->ItemCode . '</td>
            <td>' . $EnteredGRN->ItemDescription . '</td>
            <td class=number>' . locale_number_format($EnteredGRN->This_QuantityInv,2) . '</td>
            <td class=number>' . locale_number_format($EnteredGRN->ChgPrice,2) . '</td>
            <td class=number>' . locale_number_format($EnteredGRN->ChgPrice * $EnteredGRN->This_QuantityInv,2) . '</td>
            <td><a href="' . $_SERVER['PHP_SELF'] . '?' . SID . 'Delete=' . $EnteredGRN->GRNNo . '">' . _('Delete') . '</a></td></tr>';

	$TotalValueCharged = $TotalValueCharged + ($EnteredGRN->ChgPrice * $EnteredGRN->This_QuantityInv);

	$i++;
	if ($i>15){
		$i=0;
		echo $TableHeader;
	}
}

echo '<tr>
	<td colspan=5 class=number><font size=2 color=navy>' . _('Total Value Credited Against Goods') . ':</font></td>
          <td class=number><font size=2 color=navy><U>' . locale_number_format($TotalValueCharged,2) . '</U></font></td>
          </tr>';
echo '</table>
	<br />
	<div class=centre><a href="' . $rootpath . '/SupplierCredit.php?">' . _('Back to Credit Note Entry') . '</a></div>';

/* Now get all the GRNs for this supplier from the database
after the date entered */
if (!isset($_POST['Show_Since'])){
	$_POST['Show_Since'] =  Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m')-2,Date('d'),Date('Y')));
}

$SQL = "SELECT grnno,
				purchorderdetails.orderno,
				purchorderdetails.unitprice,
				grns.itemcode, grns.deliverydate,
				grns.itemdescription,
				grns.qtyrecd,
				grns.quantityinv,
				purchorderdetails.stdcostunit,
				purchorderdetails.assetid
		FROM grns INNER JOIN purchorderdetails
		ON grns.podetailitem=purchorderdetails.podetailitem 
		WHERE grns.supplierid ='" . $_SESSION['SuppTrans']->SupplierID . "' AND
		grns.deliverydate >= '" . FormatDateForSQL($_POST['Show_Since']) . "'
		ORDER BY grns.grnno";
$GRNResults = DB_query($SQL,$db);

if (DB_num_rows($GRNResults)==0){
	prnMsg(_('There are no goods received records for') . ' ' . $_SESSION['SuppTrans']->SupplierName . ' ' . _('since') . ' ' . $_POST['Show_Since'] . '<br /> ' . _('To enter a credit against goods received') . ', ' . _('the goods must first be received using the link below to select purchase orders to receive'),'info');
	echo '<p><a href="' . $rootpath . '/PO_SelectOSPurchOrder.php?SupplierID=' . $_SESSION['SuppTrans']->SupplierID . '">' . _('Select Purchase Orders to Receive') . '</a>';
}


/*Set up a table to show the GRNs outstanding for selection */
echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<br /><table cellpadding=2 colspan=7 class=selection>';

echo '<tr><th colspan=10><font size=3 color=navy>' . _('Show Goods Received Since') . ':&nbsp;</font>';
echo '<input type=Text name="Show_Since" maxlength=11 size=12 class=date alt='.$_SESSION['DefaultDateFormat'].' value="' . $_POST['Show_Since'] . '" /><input type="submit" name="FindGRNs" value="' . _('Display GRNs') . '" /><font size=3 color=navy> ';
echo  _('From') . ' ' . $_SESSION['SuppTrans']->SupplierName . '</font></th></tr>';

if (DB_num_rows($GRNResults)>0){
	$TableHeader = '<tr><th>' . _('GRN') . '</th>
						<th>' . _('Order') . '</th>
						<th>' . _('Item Code') . '</th>
						<th>' . _('Description') . '</th>
						<th>' . _('Delivered') . '</th>
						<th>' . _('Total Qty') . '<br />' . _('Received') . '</th>
						<th>' . _('Qty Already') . '<br />' . _('credit noted') . '</th>
						<th>' . _('Qty Yet') . '<br />' . _('To credit note') . '</th>
						<th>' . _('Order Price') . '<br />' . $_SESSION['SuppTrans']->CurrCode . '</th>
						<th>' . _('Line Value') . '<br />' . _('In') . ' ' . $_SESSION['SuppTrans']->CurrCode . '</th>
						</tr>';
	
	echo $TableHeader;
	
	$i=0;
	while ($myrow=DB_fetch_array($GRNResults)){
	
		$GRNAlreadyOnCredit = False;
	
		foreach ($_SESSION['SuppTrans']->GRNs as $EnteredGRN){
			if ($EnteredGRN->GRNNo == $myrow['grnno']) {
				$GRNAlreadyOnCredit = True;
			}
		}
		if ($GRNAlreadyOnCredit == False){
			echo '<tr><td><input type=Submit name="GRNNo" Value="' . $myrow['grnno'] . '"></td>
	              		<td>' . $myrow['orderno'] . '</td>
	              		<td>' . $myrow['itemcode'] . '</td>
	              		<td>' . $myrow['itemdescription'] . '</td>
	              		<td>' . ConvertSQLDate($myrow['deliverydate']) . '</td>
	              		<td class=number>' . locale_number_format($myrow['qtyrecd'],2) . '</td>
	              		<td class=number>' . locale_number_format($myrow['quantityinv'],2) . '</td>
	              		<td class=number>' . locale_number_format($myrow['qtyrecd'] - $myrow['quantityinv'],2) . '</td>
	              		<td class=number>' . locale_number_format($myrow['unitprice'],2) . '</td>
	              		<td class=number>' . locale_number_format($myrow['unitprice']*($myrow['qtyrecd'] - $myrow['quantityinv']),2) . '</td>
	              	</tr>';
			$i++;
			if ($i>15){
				$i=0;
				echo $TableHeader;
			}
		}
	}
	
	echo '</table>';
	
	if (isset($_POST['GRNNo']) AND $_POST['GRNNo']!=''){
	
		$SQL = "SELECT grnno,
	                 grns.podetailitem,
	                 purchorderdetails.orderno,
	                 purchorderdetails.unitprice,
	                 purchorderdetails.glcode,
	                 grns.itemcode,
	                 grns.deliverydate,
	                 grns.itemdescription,
	                 grns.quantityinv,
	                 grns.qtyrecd,
	                 grns.qtyrecd - grns.quantityinv
	                 AS qtyostdg,
	                 purchorderdetails.stdcostunit,
	                 purchorderdetails.shiptref,
	                 purchorderdetails.jobref,
	                 shipments.closed,
	                 purchorderdetails.assetid
	                 FROM grns,
	                      purchorderdetails
	                 LEFT JOIN shipments ON purchorderdetails.shiptref=shipments.shiptref
	                 WHERE grns.podetailitem=purchorderdetails.podetailitem AND
	                       grns.grnno='" .$_POST['GRNNo'] . "'";
		$GRNEntryResult = DB_query($SQL,$db);
		$myrow = DB_fetch_array($GRNEntryResult);
	
		echo '<br /><table class=selection>';
		echo '<tr><th colspan=6><font size=3 color=navy>' . _('GRN Selected For Adding To A Suppliers Credit Note') . '</font></th></tr>';
		echo '<tr><th>' . _('GRN') . '</th>
	                   <th>' . _('Item') . '</th>
	                   <th>' . _('Quantity') . '<br />' . _('Outstanding') . '</th>
	                   <th>' . _('Quantity') . '<br />' . _('credited') . '</th>
	                   <th>' . _('Order') . '<br />' . _('Price') . ' ' . $_SESSION['SuppTrans']->CurrCode . '</th>
	                   <th>' . _('Credit') . '<br />' . _('Price') . ' ' . $_SESSION['SuppTrans']->CurrCode . '</th>
	                   </tr>';
	
		echo '<tr><td>' . $_POST['GRNNo'] . '</td>
	            <td>' . $myrow['itemcode'] . ' ' . $myrow['itemdescription'] . '</td>
	            <td class=number>' . locale_number_format($myrow['qtyostdg'],2) . '</td>
	            <td><input type=Text Name="This_QuantityCredited" Value=' . $myrow['qtyostdg'] . ' size=11 maxlength=10></td>
	            <td class=number>' . $myrow['unitprice'] . '</td>
	            <td><input type=Text Name="ChgPrice" Value=' . $myrow['unitprice'] . ' size=11 maxlength=10></td>
	            </tr>';
		echo '</table>';
	
		if ($myrow['closed']==1){ /*Shipment is closed so pre-empt problems later by warning the user - need to modify the order first */
			echo '<input type=hidden name="ShiptRef" Value="">';
			prnMsg(_('Unfortunately the shipment that this purchase order line item was allocated to has been closed') . ' - ' . _('if you add this item to the transaction then no shipments will not be updated') . '. ' . _('If you wish to allocate the order line item to a different shipment the order must be modified first'),'error');
		} else {
			echo '<input type=hidden name="ShiptRef" Value="' . $myrow['shiptref'] . '">';
		}
	
		echo '<br /><div class=centre><input type=Submit Name="AddGRNToTrans" Value="' . _('Add to Credit Note') . '"></div>';
	
	
		echo '<input type=hidden name="GRNNumber" value=' . $_POST['GRNNo'] . '>';
		echo '<input type=hidden name="ItemCode" value="' . $myrow['itemcode'] . '">';
		echo '<input type=hidden name="ItemDescription" value="' . $myrow['itemdescription'] . '">';
		echo '<input type=hidden name="QtyRecd" value=' . $myrow['qtyrecd'] . '>';
		echo '<input type=hidden name="Prev_QuantityInv" value=' . $myrow['quantityinv'] . '>';
		echo '<input type=hidden name="OrderPrice" value=' . $myrow['unitprice'] . '>';
		echo '<input type=hidden name="StdCostUnit" value=' . $myrow['stdcostunit'] . '>';
	
		echo '<input type=hidden name="JobRef" Value="' . $myrow['jobref'] . '">';
		echo '<input type=hidden name="GLCode" Value="' . $myrow['glcode'] . '">';
		echo '<input type=hidden name="PODetailItem" Value="' . $myrow['podetailitem'] . '">';
		echo '<input type=hidden name="PONo" Value="' . $myrow['orderno'] . '">';
		echo '<input type=hidden name="AssetID" Value="' . $myrow['assetid'] . '">';
	}
} //end if there were GRNs to select
echo '</form>';
include('includes/footer.inc');
?>