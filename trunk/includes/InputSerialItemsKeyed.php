<?php
/* $Revision: 1.2 $ */
/*Input Serial Items - used for inputing serial numbers or batch/roll/bundle references
for controlled items - used in:
- ConfirmDispatchControlledInvoice.php
- GoodsReceivedControlled.php
- StockAdjustments.php
- StockTransfers.php
- CreditItemsControlled.php

*/

//we start with a batch or serial no header and need to display something for verification...
global $tableheader;

if (isset($_GET['LineNo'])){
	$LineNo = $_GET['LineNo'];
} elseif (isset($_POST['LineNo'])){
	$LineNo = $_POST['LineNo'];
}

/*Display the batches already entered with quantities if not serialised */
echo "<br><A HREF='" . $_SERVER['PHP_SELF'] . "?" . SID . "DELETEALL=YES&StockID=" . $LineItem->StockID . "&LineNo=" . $LineNo ."'>Remove All</a><br>";


echo "<INPUT TYPE=HIDDEN NAME='LineNo' VALUE=$LineNo>";


echo "<TABLE>";
echo $tableheader;

$TotalQuantity = 0; /*Variable to accumulate total quantity received */
$RowCounter =0;

foreach ($LineItem->SerialItems as $Bundle){

	if ($RowCounter == 10){
		echo $tableheader;
		$RowCounter =0;
	} else {
		$RowCounter++;
	}

	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k=1;
	}

	echo "<TD>" . $Bundle->BundleRef . "</TD>";

	if ($LineItem->Serialised==0){
		echo "<TD ALIGN=RIGHT>" . number_format($Bundle->BundleQty, $LineItem->DecimalPlaces) . "</TD>";
	}

	echo "<TD><A HREF='" . $_SERVER['PHP_SELF'] . "?" . SID . "Delete=" . $Bundle->BundleRef . "&StockID=" . $LineItem->StockID . "&LineNo=" . $LineNo ."'>Delete</A></TD></TR>";

	$TotalQuantity += $Bundle->BundleQty;
}


/*Display the totals and rule off before allowing new entries */
if ($LineItem->Serialised==1){
	echo "<TR><TD ALIGN=RIGHT><B>Total Quantity: " . number_format($TotalQuantity,$LineItem->DecimalPlaces) . "</B></TD></TR>";
	echo "<TR><TD><HR></TD></TR>";
} else {
	echo "<TR><TD ALIGN=RIGHT><B>Total Quantity:</B></TD><TD ALIGN=RIGHT><B>" . number_format($TotalQuantity,$LineItem->DecimalPlaces) . "</B></TD></TR>";
	echo "<TR><TD COLSPAN=2><HR></TD></TR>";
}

/*Close off old table */
echo "</TABLE>";

/*Start a new table for the Serial/Batch ref input  in one column (as a sub table
then the multi select box for selection of existing bundle/serial nos for dispatch if applicable*/
echo "<TABLE><TR><TD>";

/*in the first column add a table for the input of newies */
echo "<TABLE>";
echo $tableheader;



for ($i=0;$i < 10;$i++){

	echo "<TR><td><input type=text name='SerialNo" . $i ."' size=21  maxlength=20></td>";

	/*if the item is controlled not serialised - batch quantity required so just enter bundle refs
	into the form for entry of quantites manually */

	if ($LineItem->Serialised==1){
		echo "<input type=hidden name='Qty" . $i ."' Value=1></TR>";
	} else {
		echo "<TD><input type=text name='Qty" . $i ."' size=11  maxlength=10></TR>";
	}
}

echo "</table></TD>"; /*end of nested table */
?>
