<?php
/* $Revision: 1.6 $ */
if (isset($_GET['OrderNumber'])) {
	$title = "Reviewing Sales Order Number " . $_GET['OrderNumber'];
} else {
	die ("<BR><BR><BR>This page must be called with a sales order number to review.<BR>ie http://????/OrderDetails.php?OrderNumber=<i>xyz</i><BR>Click on back.");
}

$PageSecurity = 2;

include("includes/DefineCartClass.php");
/* Session started in header.inc for password checking and authorisation level check */
include("includes/session.inc");
include("includes/header.inc");
include("includes/DateFunctions.inc");

if (isset($_SESSION['Items'])){
	unset ($_SESSION['Items']->LineItems);
	unset ($_SESSION['Items']);
}

$_SESSION['Items'] = new cart;

/*read in all the guff from the selected order into the Items cart  */


$OrderHeaderSQL = "SELECT
			SalesOrders.DebtorNo,
			DebtorsMaster.Name,
			SalesOrders.BranchCode,
			SalesOrders.CustomerRef,
			SalesOrders.Comments,
			SalesOrders.OrdDate,
			SalesOrders.OrderType,
			SalesOrders.ShipVia,
			SalesOrders.DeliverTo,
			SalesOrders.DelAdd1,
			SalesOrders.DelAdd2,
			SalesOrders.DelAdd3,
			SalesOrders.DelAdd4,
			SalesOrders.ContactPhone,
			SalesOrders.ContactEmail,
			SalesOrders.FreightCost,
			SalesOrders.DeliveryDate,
			DebtorsMaster.CurrCode,
			SalesOrders.FromStkLoc
		FROM
			SalesOrders,
			DebtorsMaster
		WHERE
			SalesOrders.DebtorNo = DebtorsMaster.DebtorNo
		AND SalesOrders.OrderNo = " . $_GET['OrderNumber'];

$ErrMsg = "<BR>The order cannot be retrieved because ";
$DbgMsg = "<BR>The SQL that failed to get the order header was ";
$GetOrdHdrResult = DB_query($OrderHeaderSQL,$db, $ErrMsg, $DbgMsg);

if (DB_num_rows($GetOrdHdrResult)==1) {

	$myrow = DB_fetch_array($GetOrdHdrResult);

	$_SESSION['CustomerID'] = $myrow["DebtorNo"];
/*CustomerID defined in header.inc */
	$_SESSION['Items']->Branch = $myrow["BranchCode"];
	$_SESSION['Items']->CustomerName = $myrow["Name"];
	$_SESSION['Items']->CustRef = $myrow["CustomerRef"];
	$_SESSION['Items']->Comments = $myrow["Comments"];

	$_SESSION['Items']->DefaultSalesType =$myrow["OrderType"];
	$_SESSION['Items']->DefaultCurrency = $myrow["CurrCode"];
	$BestShipper = $myrow["ShipVia"];
	$_SESSION['Items']->DeliverTo = $myrow["DeliverTo"];
	$_SESSION['Items']->DeliveryDate = ConvertSQLDate($myrow["DeliveryDate"]);
	$_SESSION['Items']->BrAdd1 = $myrow["DelAdd1"];
	$_SESSION['Items']->BrAdd2 = $myrow["DelAdd2"];
	$_SESSION['Items']->BrAdd3 = $myrow["DelAdd3"];
	$_SESSION['Items']->BrAdd4 = $myrow["DelAdd4"];
	$_SESSION['Items']->PhoneNo = $myrow["ContactPhone"];
	$_SESSION['Items']->Email = $myrow["ContactEmail"];
	$_SESSION['Items']->Location = $myrow["FromStkLoc"];
	$FreightCost = $myrow["FreightCost"];
	$_SESSION['Items']->Orig_OrderDate = $myrow["OrdDate"];


	/* SHOW ALL THE ORDER INFO IN ONE PLACE */

	echo "<BR><BR><CENTER><TABLE BGCOLOR='#CCCCCC'>";
	echo "<TR>
		<TD>Customer Code:</TD>
		<TD><FONT COLOR=BLUE><B><A HREF='$rootpath/SelectCustomer.php?Select=" . $_SESSION['CustomerID'] . "'>" . $_SESSION['CustomerID'] . "</A></B></TD>
		<TD>Customer Name:</TD><TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->CustomerName . "</B></TD>
	</TR>";
	echo "<TR>
		<TD>Customer Reference:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->CustRef . "</FONT></B></TD>
		<TD>Deliver To:</TD><TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->DeliverTo . "</B></TD>
	</TR>";
	echo "<TR>
		<TD>Ordered On:</TD>
		<TD><FONT COLOR=BLUE><B>" . ConvertSQLDate($_SESSION['Items']->Orig_OrderDate) . "</FONT></B></TD>
		<TD>Delivery Address 1:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->BrAdd1 . "</FONT></B></TD>
	</TR>";
	echo "<TR>
		<TD>Requested Delivery:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->DeliveryDate . "</FONT></B></TD>
		<TD>Delivery Address 2:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->BrAdd2 . "</FONT></B></TD>
	</TR>";
	echo "<TR>
		<TD>Order Currency:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->DefaultCurrency . "</FONT></B></TD>
		<TD>Delivery Address 3:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->BrAdd3 . "</FONT></B></TD>
	</TR>";
	echo "<TR>
		<TD>Deliver From Loc'n:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->Location . "</FONT></B></TD>
		<TD>Delivery Address 4:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->BrAdd4 . "</FONT></B></TD>
	</TR>";
	echo "<TR>
		<TD>Freight Cost:</TD>
		<TD><FONT COLOR=BLUE><B>" . $FreightCost . "</FONT></B></TD><TD>Telephone:</TD>
		<TD><FONT COLOR=BLUE><B>" . $_SESSION['Items']->PhoneNo . "</FONT></B></TD>
	</TR>";
	echo "<TR>
		<TD COLSPAN=2></TD>
		<TD>E-mail:</TD>
		<TD><FONT COLOR=BLUE><B><A HREF='mailto:" . $_SESSION['Items']->Email . "'>" . $_SESSION['Items']->Email . "</A></FONT></B></TD>
	</TR>";
	echo "</TABLE>";

	echo $_SESSION['Items']->Comments . "<BR></CENTER>";

/*Now get the line items */

	$LineItemsSQL = "SELECT
				StkCode,
				StockMaster.Description,
				StockMaster.Volume,
				StockMaster.KGS,
				StockMaster.DecimalPlaces,
				StockMaster.MBflag,
				StockMaster.Units,
				StockMaster.DiscountCategory,
				StockMaster.Controlled,
				StockMaster.Serialised,
				UnitPrice,
				Quantity,
				DiscountPercent,
				ActualDispatchDate,
				QtyInvoiced
			FROM SalesOrderDetails, StockMaster
			WHERE SalesOrderDetails.StkCode = StockMaster.StockID AND OrderNo =" . $_GET['OrderNumber'];

	$ErrMsg = "<BR>The line items of the order cannot be retrieved because ";
	$Dbgmsg = "<BR>The SQL used to retrieve the line items, that failed was ";
	$LineItemsResult = db_query($LineItemsSQL,$db, $ErrMsg, $DbgMsg);

	if (db_num_rows($LineItemsResult)>0) {

		while ($myrow=db_fetch_array($LineItemsResult)) {

			$_SESSION['Items']->add_to_cart($myrow["StkCode"],
							$myrow["Quantity"],
							$myrow["Description"],
							$myrow["UnitPrice"],
							$myrow["DiscountPercent"],
							$myrow["Units"],
							$myrow["Volume"],
							$myrow["KGS"],
							0,
							$myrow['MBflag'],
							$myrow["ActualDispatchDate"],
							$myrow["QtyInvoiced"],
							$myrow['DiscountCategory'],
							$myrow['Controlled'],
							$myrow['Serialised'],
							$myrow['DecimalPlaces']
						);

		} /* line items from sales order details */
	} //end of checks on returned data set
}


echo "<CENTER><B>Line Details</B>
	<TABLE CELLPADDING=2 COLSPAN=9 BORDER=1>
	<TR>
		<TD class='tableheader'>Item Code</TD>
		<TD class='tableheader'>Item Description</TD>
		<TD class='tableheader'>Quantity</TD>
		<TD class='tableheader'>Unit</TD>
		<TD class='tableheader'>Price</TD>
		<TD class='tableheader'>Discount</TD>
		<TD class='tableheader'>Total</TD>
		<TD class='tableheader'>Qty Del</TD>
		<TD class='tableheader'>Last Del</TD>
	</TR>";

$_SESSION['Items']->total = 0;
$_SESSION['Items']->totalVolume = 0;
$_SESSION['Items']->totalWeight = 0;
$k =0;  //row colour counter
foreach ($_SESSION['Items']->LineItems as $StockItem) {

	$LineTotal =	$StockItem->Quantity * $StockItem->Price * (1 - $StockItem->DiscountPercent);
	$DisplayLineTotal = number_format($LineTotal,2);
	$DisplayPrice = number_format($StockItem->Price,2);
	$DisplayQuantity = number_format($StockItem->Quantity,2);
	$DisplayDiscount = number_format(($StockItem->DiscountPercent * 100),2) . "%";
	$DisplayQtyInvoiced = number_format($StockItem->QtyInv,2);
	if ($StockItem->QtyInv>0){
		  $DisplayActualDeliveryDate = ConvertSQLDate($StockItem->ActDispDate);
	} else {
		  $DisplayActualDeliveryDate = "N/A";
	}

	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k=1;
	}

	echo 	"<TD>$StockItem->StockID</TD>
		<TD>$StockItem->ItemDescription</TD>
		<TD ALIGN=RIGHT>$DisplayQuantity</TD>
		<TD>$StockItem->Units</TD>
		<TD ALIGN=RIGHT>$DisplayPrice</TD>
		<TD ALIGN=RIGHT>$DisplayDiscount</TD>
		<TD ALIGN=RIGHT>$DisplayLineTotal</TD>
		<TD ALIGN=RIGHT>$DisplayQtyInvoiced</TD>
		<TD>$DisplayActualDeliveryDate</TD>
	</TR>";

	$_SESSION['Items']->total = $_SESSION['Items']->total + $LineTotal;
	$_SESSION['Items']->totalVolume = $_SESSION['Items']->totalVolume + $StockItem->Quantity * $StockItem->Volume;
	$_SESSION['Items']->totalWeight = $_SESSION['Items']->totalWeight + $StockItem->Quantity * $StockItem->Weight;
}

$DisplayTotal = number_format($_SESSION['Items']->total,2);
echo "<TR>
	<TD COLSPAN=5 ALIGN=RIGHT><B>TOTAL Excl Tax/Freight</B></TD>
	<TD COLSPAN=2 ALIGN=RIGHT>$DisplayTotal</TD>
	</TR>
	</TABLE>";

$DisplayVolume = number_format($_SESSION['Items']->totalVolume,2);
$DisplayWeight = number_format($_SESSION['Items']->totalWeight,2);
echo "<TABLE BORDER=1>
	<TR>
		<TD>Total Weight:</TD>
		<TD>$DisplayWeight</TD>
		<TD>Total Volume:</TD>
		<TD>$DisplayVolume</TD>
	</TR>
</TABLE>";

include("includes/footer.inc");
?>
