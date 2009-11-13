<?php

/* $Id$ */

/* $Revision: 1.90 $ */


$PageSecurity = 1;

include('includes/session.inc');
$title=_('Main Menu');

/*The module link codes are hard coded in a switch statement below to determine the options to show for each tab */
$ModuleLink = array('orders', 'AR', 'AP', 'PO', 'stock', 'manuf', 'GL', 'FA', 'system');
/*The headings showing on the tabs accross the main index used also in WWW_Users for defining what should be visible to the user */
$ModuleList = array(_('Sales'), _('Receivables'), _('Payables'), _('Purchases'), _('Inventory'), _('Manufacturing'), _('General Ledger'), _('Asset Manager'), _('Setup'));

if (isset($_GET['Application'])){ /*This is sent by this page (to itself) when the user clicks on a tab */
	$_SESSION['Module'] = $_GET['Application'];
}

include('includes/header.inc');

if (count($_SESSION['AllowedPageSecurityTokens'])==1){

/* if there is only one security access and its 1 (it has to be 1 for this page came up at all)- it must be a customer log on need to limit the menu to show only the customer accessible stuff this is what the page looks like for customers logging in */
?>

		<tr>
		<td class="menu_group_items">  <!-- Orders transaction options -->
		<table class="table_index">
			<tr>
			<td class="menu_group_item">
				<?php echo '<a href="' . $rootpath . '/CustomerInquiry.php?' . SID . '&CustomerID=' . $_SESSION['CustomerID'] . '">&bull; ' . _('Account Status') . '</a>'; ?>
			</td>
			</tr>
			<tr>
			<td class="menu_group_item">
				<?php echo '<a href="' . $rootpath . '/SelectOrderItems.php?' . SID . '&NewOrder=Yes">&bull; ' . _('Place An Order') . '</a>'; ?>
			</td>
			</tr>
			<tr>
			<td class="menu_group_item">
				<?php echo '<a href="' . $rootpath . '/SelectCompletedOrder.php?' . SID . '&SelectedCustomer=' . $_SESSION['CustomerID'] . "'>&bull; " . _('Order Status') . '</a>'; ?>
			</td>
			</tr>
		</table>
	</td>
<?php
	include('includes/footer.inc');
	exit;
} else {  /* Security settings DO allow seeing the main menu */

?>
		<table class="main_menu" width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
			<td class="main_menu">
				<table class="main_menu">
					<tr>

	<?php


	$i=0;

	while ($i < count($ModuleLink)){

		// This determines if the user has display access to the module see config.php and header.inc
		// for the authorisation and security code
		if ($_SESSION['ModulesEnabled'][$i]==1)	{

			// If this is the first time the application is loaded then it is possible that
			// SESSION['Module'] is not set if so set it to the first module that is enabled for the user
			if (!isset($_SESSION['Module'])OR $_SESSION['Module']==''){
				$_SESSION['Module']=$ModuleLink[$i];
			}
			if ($ModuleLink[$i] == $_SESSION['Module']){
				echo "<td class='main_menu_selected'><a href='". $_SERVER['PHP_SELF'] .'?'. SID . '&Application='. $ModuleLink[$i] ."'>". $ModuleList[$i] .'</a></td>';
			} else {
				echo "<td class='main_menu_unselected'><a href='". $_SERVER['PHP_SELF'] .'?'. SID . '&Application='. $ModuleLink[$i] ."'>". $ModuleList[$i] .'</a></td>';
			}
		}
		$i++;
	}

	?>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php


	switch ($_SESSION['Module']) {

	case 'orders': //Sales Orders
	?>

		<table width="100%">
			<tr>
			<td class="menu_group_area">
				<table width="100%" >

					<?php
  					// displays the main area headings
					  OptionHeadings();
					?>

					<tr>
					<td class="menu_group_items">  <!-- Orders transaction options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectOrderItems.php?' .SID . '&NewOrder=Yes">&bull; ' . _('Enter An Order or Quotation') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectSalesOrder.php?' . SID . '">&bull; ' . _('Outstanding Sales Orders/Quotations') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SpecialOrder.php?' .SID . '&NewSpecial=Yes">&bull; ' . _('Special Order') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectRecurringSalesOrder.php?' .SID . '">&bull; ' . _('Recurring Order Template') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/RecurringSalesOrdersProcess.php?' .SID . '">&bull; ' . _('Process Recurring Orders') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items"> <!-- Orders Inquiry options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectCompletedOrder.php?' . SID . '">&bull; ' . _('Order Inquiry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFPriceList.php?' . SID . '">&bull; ' . _('Print Price Lists') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFOrderStatus.php?' . SID . '">&bull; ' . _('Order Status Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFOrdersInvoiced.php?' . SID . '">&bull; ' . _('Orders Invoiced Reports') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFDeliveryDifferences.php?' . SID . '">&bull; ' . _('Order Delivery Differences Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFDIFOT.php?' . SID . '">&bull; ' . _('Delivery In Full On Time (DIFOT) Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SalesInquiry.php?' . SID . '">&bull; ' . _('Sales Order Detail Or Summary Inquiries') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/TopItems.php?' . SID . '">&bull; ' . _('Top Sales Items Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('ord'); ?>
							</td>
							</tr>
						</table>
					</td>

					<td class="menu_group_items"> <!-- Orders Maintenance options -->
						<table width="100%">
							<tr>
							  <td>

						        </td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;
	/* ****************** END OF ORDERS MENU ITEMS **************************** */


	Case 'AR': //Debtors Module

	unset($ReceiptBatch);
	unset($AllocTrans);

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">
						<table width="100%"class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectSalesOrder.php?' . SID . '">&bull; ' . _('Select Order to Invoice') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectCreditItems.php?' .SID . '&NewCredit=Yes">&bull; ' . _('Create A Credit Note') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/CustomerReceipt.php?' . SID . '&NewReceipt=Yes">&bull; ' . _('Enter Receipts') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">

								<?php echo '<a href="' . $rootpath . '/CustomerAllocations.php?' . SID . '">&bull; ' . _('Allocate Receipts or Credit Notes') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectCustomer.php?' . SID . '">&bull; ' . _('Customer Transaction Inquiries') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/CustWhereAlloc.php?' . SID . '">&bull; ' . _('Where Allocated Inquiry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php
									if ($_SESSION['InvoicePortraitFormat']==0){
										echo '<a href="' . $rootpath . '/PrintCustTrans.php?' . SID . '">&bull; ' . _('Print Invoices or Credit Notes') . '</a>';
									} else {
										echo '<a href="' . $rootpath . '/PrintCustTransPortrait.php?' . SID . '">&bull; ' . _('Print Invoices or Credit Notes') . '</a>';
									}
								?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PrintCustStatements.php?' . SID . '">&bull; ' . _('Print Statements') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SalesAnalRepts.php?' . SID . '">&bull; ' . _('Sales Analysis Reports') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/AgedDebtors.php?' . SID . '">&bull; ' . _('Aged Customer Balances/Overdues Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/CustomerTransInquiry.php?' . SID . '">&bull; ' . _('Transaction Inquiries') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFBankingSummary.php?' . SID . '">&bull; ' . _('Re-Print A Deposit Listing') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/DebtorsAtPeriodEnd.php?' . SID . '">&bull; ' . _('Debtor Balances At A Prior Month End') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFCustomerList.php?' . SID . '">&bull; ' . _('Customer Listing By Area/Salesperson') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SalesGraph.php?' . SID . '">&bull; ' . _('Sales Graphs') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('ar'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Customers.php?' . SID . '">&bull; ' . _('Add Customer') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectCustomer.php?' . SID . '">&bull; ' . _('Customers') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php

	/* ********************* 	END OF AR OPTIONS **************************** */
		break;

	Case 'AP': //Creditors Module

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items"> <!-- AP transaction options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectSupplier.php?' . SID . '">&bull; ' . _('Select Supplier') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . "/SupplierAllocations.php?" . SID . '">&bull; ' . _('Supplier Allocations') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">  <!-- AP Inquiries -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/AgedSuppliers.php?' . SID . '">&bull; ' . _('Aged Supplier Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SuppPaymentRun.php?' . SID . '">&bull; ' . _('Payment Run Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/OutstandingGRNs.php?' . SID . '">&bull; ' . _('Outstanding GRNs Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SupplierBalsAtPeriodEnd.php?' . SID . '">&bull; ' . _('Supplier Balances At A Prior Month End') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('ap'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">   <!-- AP Maintenance Options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Suppliers.php?' . SID . '">&bull; ' . _('Add Supplier') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Factors.php?' . SID . '">&bull; ' . _('Maintain Factor Companies') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;

	Case 'PO': /* Purchase Ordering */

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">  <!-- PO Transactions -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . '">&bull; ' . _('Purchase Orders') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '"<a href="' . $rootpath . '/PO_Header.php?&NewOrder=Yes' . SID . '">&bull; ' . _('Add Purchase Order') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PO_AuthoriseMyOrders.php?' . SID . '">&bull; ' . _('Orders to Authorise') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectSupplier.php?' . SID . '">&bull; ' . _('Shipment Entry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Shipt_Select.php?' . SID . '">&bull; ' . _('Select A Shipment') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">  <!-- PO Inquiries -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PO_SelectPurchOrder.php?' . SID . '">&bull; ' . _('Purchase Order Inquiry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/POReport.php?' . SID . '">&bull; ' . _('Purchase Order Detail Or Summary Inquiries') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('prch'); ?>
							</td>
							</tr>
					</table>
					</td>
					<td class="menu_group_items">   <!-- PO Maintenance -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PurchData.php?' . SID . '">&bull; ' . _('Maintain Purchasing Data') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;

	/* ****************************** END OF PURCHASING OPTIONS ******************************** */


	Case 'stock': //Inventory Module

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . '">&bull; ' . _('Receive Purchase Orders') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockLocTransfer.php' . SID . '">&bull; ' . _('Bulk Inventory Transfer') . ' - ' . _('Dispatch') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockLocTransferReceive.php?' . SID . '">&bull; ' . _('Bulk Inventory Transfer') . ' - ' . _('Receive') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockTransfers.php?' . SID . '">&bull; ' . _('Inventory Location Transfers') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockAdjustments.php?' . SID . '">&bull; ' . _('Inventory Adjustments') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/ReverseGRN.php?' . SID . '">&bull; ' . _('Reverse Goods Received') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockCounts.php?' . SID . '">&bull; ' . _('Enter Stock Counts') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . "/StockSerialItemResearch.php?" . SID . '">&bull; ' . _('Serial Item Research Tool') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . "/StockMovements.php?" . SID . '">&bull; ' . _('Inventory Item Movements') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockStatus.php?' . SID . '">&bull; ' . _('Inventory Item Status') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockUsage.php?' . SID . '">&bull; ' . _('Inventory Item Usage') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/InventoryQuantities.php?' . SID . '">&bull; ' . _('Inventory Quantities') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/ReorderLevel.php?' . SID . '">&bull; ' . _('Reorder Level') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockDispatch.php?' . SID . '">&bull; ' . _('Stock Dispatch') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/InventoryValuation.php?' . SID . '">&bull; ' . _('Inventory Valuation Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/InventoryPlanning.php?' . SID . '">&bull; ' . _('Inventory Planning Report') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/InventoryPlanningPrefSupplier.php?' . SID . '">&bull; ' . _('Inventory Planning Based On Preferred Supplier Data') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockCheck.php?' . SID . '">&bull; ' . _('Inventory Stock Check Sheets') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockQties_csv.php?' . SID . '">&bull; ' . _('Make Inventory Quantities CSV') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFStockCheckComparison.php?' . SID . '">&bull; ' . _('Compare Counts Vs Stock Check Data') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockLocMovements.php?' . SID . '">&bull; ' . _('All Inventory Movements By Location/Date') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockLocStatus.php?' . SID . '">&bull; ' . _('List Inventory Status By Location/Category') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockQuantityByDate.php?' . SID . '">&bull; ' . _('Historical Stock Quantity By Location/Category') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFStockNegatives.php?' . SID . '">&bull; ' . _('List Negative Stocks') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('inv'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Stocks.php?' . SID . '">&bull; ' . _('Add A New Item') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectProduct.php?' . SID . '">&bull; ' . _('Select An Item') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SalesCategories.php?' . SID . '">&bull; ' . _('Sales Category Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PricesBasedOnMarkUp.php?' . SID . '">&bull; ' . _('Add or Update Prices Based On Costs') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/ReorderLevelLocation.php?' . SID . '">&bull; ' . _('Reorder Level By Category/Location') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;

	/* ****************************** END OF INVENTORY OPTIONS *********************************** */

	Case 'manuf': //Manufacturing Module

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">

					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							  <td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/WorkOrderEntry.php?' . SID . '">&bull; ' . _('Work Order Entry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectWorkOrder.php?' . SID . '">&bull; ' . _('Select A Work Order') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectWorkOrder.php?' . SID . '">&bull; ' . _('Select A Work Order') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BOMInquiry.php?' . SID . '">&bull; ' . _('Costed Bill Of Material Inquiry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/WhereUsedInquiry.php?' . SID . '">&bull; ' . _('Where Used Inquiry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BOMIndented.php?' . SID . '">&bull; ' . _('Indented Bill Of Material Listing') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BOMExtendedQty.php?' . SID . '">&bull; ' . _('List Components Required') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BOMIndentedReverse.php?' . SID . '">&bull; ' . _('Indented Where Used Listing') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRPReport.php?' . SID . '">&bull; ' . _('MRP') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRPShortages.php?' . SID . '">&bull; ' . _('MRP Shortages') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRPPlannedPurchaseOrders.php?' . SID . '">&bull; ' . _('MRP Suggested Purchase Orders') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRPReschedules.php?' . SID . '">&bull; ' . _('MRP Reschedules Required') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('man'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/WorkCentres.php?' . SID . '">&bull; ' . _('Work Centre') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BOMs.php?' . SID . '">&bull; ' . _('Bills Of Material') . '</a>'; ?>
							</td>
							</tr>
							
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRPDemands.php?' . SID . '">&bull; ' . _('Master Schedule') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRPCreateDemands.php?' . SID . '">&bull; ' . _('Auto Create Master Schedule') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRP.php?' . SID . '">&bull; ' . _('MRP Calculation') . '</a>'; ?>
							</td>
							</tr>
							
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;


	Case 'system': //System setup

	?>
		<table width='100%'>
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%" >
					<tr>
					<td class="menu_group_headers">
						<table>
							<tr>
							<td>
								<?php echo '<img src="'. $rootpath . '/css/' . $theme . '/images/company.png" title="' . _('General Setup Options') . '" alt="">'; ?>
							</td>
							<td class="menu_group_headers_text">
								<?php echo _('General'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_headers">
						<table>
							<tr>
							<td>
								<?php echo '<img src="'. $rootpath . '/css/' . $theme . '/images/ar.png" title="' . _('Receivables/Payables Setup') . '" alt="">'; ?>
							</td>
							<td class="menu_group_headers_text">
								<?php echo _('Receivables/Payables'); ?>

							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_headers">
						<table>
							<tr>
							<td>
								<?php echo '<img src="'. $rootpath . '/css/' . $theme . '/images/inventory.png" title="' . _('Inventory Setup') . '" alt="">'; ?>
							</td>
							<td class="menu_group_headers_text">
								<?php echo _('Inventory Setup'); ?>
							</td>
							</tr>
						</table>
					</td>


					</tr>
					<tr>

					<td class="menu_group_items">	<!-- Gereral set up options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/CompanyPreferences.php?' . SID . '">&bull; ' . _('Company Preferences') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SystemParameters.php?' . SID . '">&bull; ' . _('Configuration Settings') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/WWW_Users.php?' . SID . '">&bull; ' . _('User Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/WWW_Access.php?' . SID . '">&bull; ' . _('Role Permissions') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BankAccounts.php?' . SID . '">&bull; ' . _('Bank Accounts') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Currencies.php?' . SID . '">&bull; ' . _('Currency Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/TaxAuthorities.php?' . SID . '">&bull; ' . _('Tax Authorities and Rates Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/TaxGroups.php?' . SID . '">&bull; ' . _('Tax Group Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/TaxProvinces.php?' . SID . '">&bull; ' . _('Dispatch Tax Province Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/TaxCategories.php?' . SID . '">&bull; ' . _('Tax Category Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PeriodsInquiry.php?' . SID . '">&bull; ' . _('List Periods Defined') . ' <font size=1>(' . _('Periods are automatically maintained') . ')</font></a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/reportwriter/admin/ReportCreator.php"><LI>' . _('Report Builder Tool') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/AuditTrail.php"><LI>' . _('View Audit Trail') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SystemCheck.php"><LI>' . _('View System Check') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/GeocodeSetup.php"><LI>' . _('Geocode Setup') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>

					<td class="menu_group_items">	<!-- AR/AP set-up options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SalesTypes.php?' . SID . '">&bull; ' . _('Sales Types') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/CustomerTypes.php?' . SID . '">&bull; ' . _('Customer Types') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/CreditStatus.php?' . SID . '">&bull; ' . _('Credit Status') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PaymentTerms.php?' . SID . '">&bull; ' . _('Payment Terms') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PO_AuthorisationLevels.php?' . SID . '">&bull; ' . _('Set Purchase Order Authorisation levels') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PaymentMethods.php?' . SID . '">&bull; ' . _('Payment Methods') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SalesPeople.php?' . SID . '">&bull; ' . _('Sales People') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Areas.php?' . SID . '">&bull; ' . _('Sales Areas') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Shippers.php?' . SID . '">&bull; ' . _('Shippers') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SalesGLPostings.php?' . SID . '">&bull; ' . _('Sales GL Interface Postings') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/COGSGLPostings.php?' . SID . '">&bull; ' . _('COGS GL Interface Postings') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/FreightCosts.php?' . SID . '">&bull; ' . _('Freight Costs Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/DiscountMatrix.php?' . SID . '">&bull; ' . _('Discount Matrix') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>

					<td class="menu_group_items">	<!-- Inventory set-up options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/StockCategories.php?' . SID . '">&bull; ' . _('Inventory Categories Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Locations.php?' . SID . '">&bull; ' . _('Inventory Locations Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/DiscountCategories.php?' . SID . '">&bull; ' . _('Discount Category Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/UnitsOfMeasure.php?' . SID . '">&bull; ' . _('Units of Measure') . '</a>'; ?>
							</td>
							</tr>
							<tr></tr>
							
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRPCalendar.php?' . SID . '">&bull; ' . _('MRP Available Production Days') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/MRPDemandTypes.php?' . SID . '">&bull; ' . _('MRP Demand Types') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
		break;

	Case 'GL': //General Ledger

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">		<!-- Gereral Ledger Option Headings-->

    					<?php OptionHeadings(); ?>

					<tr>
					<td class="menu_group_items"> <!-- General transactions options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Payments.php?' .SID . '&NewPayment=Yes">&bull; ' . _('Bank Account Payments Entry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/CustomerReceipt.php?' . SID . '&NewReceipt=Yes">&bull; ' . _('Bank Account Receipts Entry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/GLJournal.php?' .SID . '&NewJournal=Yes">&bull; ' . _('Journal Entry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BankMatching.php?' .SID . '&Type=Payments">&bull; ' . _('Bank Account Payments Matching') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BankMatching.php?' .SID . '&Type=Receipts">&bull; ' . _('Bank Account Receipts Matching') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">  <!-- Gereral inquiry options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/GLTrialBalance.php?' . SID . '">&bull; ' . _('Trial Balance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/SelectGLAccount.php?' . SID . '">&bull; ' . _('Account Inquiry') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/BankReconciliation.php?' . SID . '">&bull; ' . _('Bank Account Reconciliation Statement') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/PDFChequeListing.php?' . SID . '">&bull; ' . _('Cheque Payments Listing') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . "/GLProfit_Loss.php?" . SID . '">&bull; ' . _('Profit and Loss Statement') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/GLBalanceSheet.php?' . SID . '">&bull; ' . _('Balance Sheet') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . "/GLTagProfit_Loss.php?" . SID . '">&bull; ' . _('Tag Reports') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/Tax.php?' . SID . '">&bull; ' . _('Tax Reports') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('gl'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items">  <!-- Gereral Ledger Maintenance options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/GLAccounts.php?' . SID . '">&bull; ' . _('GL Account') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/GLBudgets.php?' . SID . '">&bull; ' . _('GL Budgets') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/AccountGroups.php?' . SID . '">&bull; ' . _('Account Groups') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/AccountSections.php?' . SID . '">&bull; ' . _('Account Sections') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/GLTags.php?' . SID . '">&bull; ' . _('GL Tags') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	<?php
	break;
	Case 'FA': //General Ledger

	?>
		<table width="100%">
			<tr>
			<td valign="top" class="menu_group_area">
				<table width="100%">		<!-- Fixed Asset Option Headings-->
					<?php OptionHeadings(); ?>
					<tr>
					<td class="menu_group_items"> <!-- General transactions options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/FixedAssetTransfer.php?' . SID . '">&bull; ' . _('Change Asset Location') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/FixedAssetJournal.php?NewJournal=Yes' . SID . '">&bull; ' . _('Depreciation Journal') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items"> <!-- General transactions options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/FixedAssetRegister.php?' . SID . '">&bull; ' . _('Asset Register') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo GetRptLinks('fa'); ?>
							</td>
							</tr>
						</table>
					</td>
					<td class="menu_group_items"> <!-- General transactions options -->
						<table width="100%" class="table_index">
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/FixedAssetCategories.php?' . SID . '">&bull; ' . _('Asset Categories Maintenance') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/FixedAssetItems.php?' . SID . '">&bull; ' . _('Add a new Asset Type') . '</a>'; ?>
							</td>
							</tr>
							<tr>
							<td class="menu_group_item">
								<?php echo '<a href="' . $rootpath . '/FixedAssetLocations.php?' . SID . '">&bull; ' . _('Add or Maintain Asset Locations') . '</a>'; ?>
							</td>
							</tr>
						</table>
					</td>
				</table>
			</td>
			</tr>
		</table>
<?php 
	break;
	} //end of module switch
} /* end of if security allows to see the full menu */

// all tables started are ended within this index script which means 2 outstanding from footer.

include('includes/footer.inc');

function OptionHeadings() {

global $rootpath, $theme;

?>

	<tr>
	<td class="menu_group_headers"> <!-- Orders option Headings -->
		<table>
			<tr>
			<td>
				<?php echo '<img src="' . $rootpath . '/css/' . $theme . '/images/transactions.png" title="' . _('Transactions') . '" alt="">'; ?>
			</td>
			<td class="menu_group_headers_text">
				<?php echo _('Transactions'); ?>
			</td>
			</tr>
		</table>
	</td>
	<td class="menu_group_headers">
		<table>
			<tr>
			<td>
				<?php echo '<img src="' . $rootpath . '/css/' . $theme . '/images/reports.png" title="' . _('Inquiries and Reports') . '" alt="">'; ?>
			</td>
			<td class="menu_group_headers_text">
				<?php echo _('Inquiries and Reports'); ?>
			</td>
			</tr>
		</table>
	</td>
	<td class="menu_group_headers">
		<table>
			<tr>
			<td>
				<?php echo '<img src="' . $rootpath . '/css/' . $theme . '/images/maintenance.png" title="' . _('Maintenance') . '" alt="">'; ?>
			</td>
			<td class="menu_group_headers_text">
				<?php echo _('Maintenance'); ?>
			</td>
			</tr>
		</table>
	</td>
	</tr>

<?php

}

function GetRptLinks($GroupID) {
/*
This function retrieves the reports given a certain group id as defined in /reports/admin/defaults.php
in the acssociative array $ReportGroups[]. It will fetch the reports belonging solely to the group
specified to create a list of links for insertion into a table to choose a report. Two table sections will
be generated, one for standard reports and the other for custom reports.
*/
	global $db, $rootpath;
	require_once('reportwriter/languages/en_US/reports.php');
	require_once('reportwriter/admin/defaults.php');

	$Title= array(_('Custom Reports'), _('Standard Reports and Forms'));

	$sql= "SELECT id, reporttype, defaultreport, groupname, reportname
		FROM reports ORDER BY groupname, reportname";
	$Result=DB_query($sql,$db,'','',false,true);
	$ReportList = '';
	while ($Temp = DB_fetch_array($Result)) $ReportList[] = $Temp;

	$RptLinks = '';
	for ($Def=1; $Def>=0; $Def--) {
		$RptLinks .= '<tr><td class="menu_group_headers"><div align="center">'.$Title[$Def].'</div></td></tr>';
		$NoEntries = true;
		if ($ReportList) { // then there are reports to show, show by grouping
			foreach ($ReportList as $Report) {
				if ($Report['groupname']==$GroupID AND $Report['defaultreport']==$Def) {
					$RptLinks .= '<tr><td class="menu_group_item">';
					$RptLinks .= '<a href="' . $rootpath . '/reportwriter/ReportMaker.php?action=go&reportid=' . $Report['id'] . '">&bull; ' . _($Report['reportname']) . '</a>';
					$RptLinks .= '</td></tr>';
					$NoEntries = false;
				}
			}
			// now fetch the form groups that are a part of this group (List after reports)
			$NoForms = true;
			foreach ($ReportList as $Report) {
				$Group=explode(':',$Report['groupname']); // break into main group and form group array
				if ($NoForms AND $Group[0]==$GroupID AND $Report['reporttype']=='frm' AND $Report['defaultreport']==$Def) {
					$RptLinks .= '<tr><td class="menu_group_item">';
					$RptLinks .= '<img src="' . $rootpath . '/css/' . $_SESSION['Theme'] . '/images/folders.gif" width="16" height="13">&nbsp;';
					$RptLinks .= '<a href="' . $rootpath . '/reportwriter/FormMaker.php?id=' . $Report['groupname'] . '">';
					$RptLinks .= $FormGroups[$Report['groupname']] . '</a>';
					$RptLinks .= '</td></tr>';
					$NoForms = false;
					$NoEntries = false;
				}
			}
		}
		if ($NoEntries) $RptLinks .= '<tr><td class="menu_group_item">&bull; ' . _('There are no reports to show!') . '</td></tr>';
	}
	return $RptLinks;
}

?>
