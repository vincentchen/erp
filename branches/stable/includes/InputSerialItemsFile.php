<?php
/* $Id$*/
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
global $LineItem;
//$LineNo = initPvar('LineNo', $LineNo);
if (isset($_GET['LineNo'])){
	$LineNo = $_GET['LineNo'];
} elseif (isset($_POST['LineNo'])){
	$LineNo = $_POST['LineNo'];
}

echo '<div class="centre">';
echo '<table>';
echo $tableheader;

$TotalQuantity = 0; /*Variable to accumulate total quantity received */
$RowCounter =0;

/*Display the batches already entered with quantities if not serialised */
foreach ($LineItem->SerialItems as $Bundle){

	$RowCounter++;
	//only show 1st 10 lines
	if ($RowCounter < 10){
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		echo '<td>' . $Bundle->BundleRef . '</td>';

		if ($LineItem->Serialised==0){
			echo '<td align=right>' . locale_number_format($Bundle->BundleQty, $LineItem->DecimalPlaces) . '</td>';
		}
	}

	$TotalQuantity += $Bundle->BundleQty;
}


/*Display the totals and rule off before allowing new entries */
if ($LineItem->Serialised==1){
	echo '<tr><td align=right><b>'.  _('Total Quantity'). ': ' . locale_number_format($TotalQuantity,$LineItem->DecimalPlaces) . '</b></td></tr>';
	echo '<tr><td><hr></td></tr>';
} else {
	echo '<tr><td align=right><b>'. _('Total Quantity'). ':</b></td><td align=right><b>' . locale_number_format($TotalQuantity,$LineItem->DecimalPlaces) . '</b></td></tr>';
	echo '<tr><td colspan=2><hr></td></tr>';
}

echo '</table><hr>';


//DISPLAY FILE INFO
// do some inits & error checks...
$ShowFileInfo = false;
if (!isset($_SESSION['CurImportFile']) ){
		$_SESSION['CurImportFile'] = '';
		$LineItem->SerialItemsValid=false;
}
if ($_FILES['ImportFile']['name'] == '' AND $_SESSION['CurImportFile'] == ''){
	$msg = _('Please Choose a file and then click Set Entry Type to upload a file for import');
	prnMsg($msg);
	$LineItem->SerialItemsValid=false;
	echo '</td></tr></table>';
	include('includes/footer.inc');
	exit();
}
if ($_FILES['ImportFile']['error'] != '' && !isset($_SESSION['CurImportFile'])){
		echo _('There was a problem with the uploaded file') . '. ' . _('We received').':<br />'.
				 _('Name').':'.$_FILES['ImportFile']['name'].'<br />'.
				 _('Size').':'.locale_number_format($_FILES['ImportFile']['size']/1024,2).'kb<br />'.
				 _('Type').':'.$_FILES['ImportFile']['type'].'<br />';
		echo '<br />'._('Error was').' '.$_FILES['ImportFile']['error'].'<br />';
		$LineItem->SerialItemsValid=false;
		echo '</td></tr></table>';
		include('includes/footer.inc');
		exit();
} elseif ($_FILES['ImportFile']['name']!=''){
	//User has uploaded importfile. reset items, then just 'get hold' of it for later.

	$LineItem->SerialItems=array();
	$LineItem->SerialItemsValid=false;
	$_SESSION['CurImportFile']['Processed']=false;
	$_SESSION['CurImportFile'] = $_FILES['ImportFile'];
	$_SESSION['CurImportFile']['tmp_name'] = $_SESSION['reports_dir'] . '/' . $LineItem->StockID.'_'.$LineNo.'_'.uniqid(4);
	if (!move_uploaded_file($_FILES['ImportFile']['tmp_name'],$_SESSION['CurImportFile']['tmp_name'])){
		prnMsg(_('Error moving temporary file') . '. ' . _('Please check your configuration'),'error' );
		$LineItem->SerialItemsValid=false;
		echo '</td></tr></table>';
		include('includes/footer.inc');
		exit;
	}

	if ($_FILES['ImportFile']['name']!=''){
		echo _('Successfully received').':<br />';
		$ShowFileInfo = true;
	}
} elseif (isset($_SESSION['CurImportFile']) && $_SESSION['CurImportFile']['Processed'] ) {
	//file exists, some action performed...
	echo _('Working with'). ':<br />';
	$ShowFileInfo = true;
} elseif ($LineItem->SerialItemsValid && $_SESSION['CurImportFile']['Processed']){
	$ShowInfo = true;
}
if ($ShowFileInfo){
	/********************************************
	  Display file info for visual verification
	********************************************/
	echo '<table>';
	echo '<tr><td>'._('Name').':</td><td>'.$_SESSION['CurImportFile']['name'].'</td></tr>
		<tr><td>'. _('Size') .':</td><td>' . locale_number_format($_SESSION['CurImportFile']['size']/1024,4) . 'kb</td></tr>
		<tr><td>'. _('Type') .':</td><td>' . $_SESSION['CurImportFile']['type'] . '</td></tr>
		<tr><td>'. _('TempName') .':</td><td>' . $_SESSION['CurImportFile']['tmp_name'] . '</td></tr>
	   <tr><td>'. _('Status') .':</td><td>' . ($LineItem->SerialItemsValid?getMsg(_('Valid'),'success'):getMsg(_('Invalid'),'error')) . '</td></tr>
	   </table>'. $invalid_imports.' '. _('out of') .' '.$TotalLines.' '. _('records are invalid').'<br />';
	$filename = $_SESSION['CurImportFile']['tmp_name'];
}

if (!$LineItem->SerialItemsValid AND !$_SESSION['CurImportFile']['Processed']){
		// If all items are not valid, show the raw first 10 lines of the file. maybe it will help.
	$filename = $_SESSION['CurImportFile']['tmp_name'];
		$handle = fopen($filename, 'r');
		$i=0;
		while (!feof($handle) && $i < 10) {
				$contents .= fgets($handle, 4096);
				$i++;
		}
		fclose($handle);

	echo '<br /><form method="post">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<input type="submit" name="ValidateFile" value=' . _('Validate File') . '>
			<input type="hidden" name="LineNo" value="' . $LineNo . '">
			<input type="hidden" name="identifier" value="' . $identifier . '">
			<input type="hidden" name="StockID" value="' . $StockID . '">
			<input type="hidden" name="EntryType" value="FILE">
			</form>
			<p>'. _('1st 10 Lines of File'). '....
			<hr width=15%>
		<pre>';

	echo $contents;

	echo '</pre>';

} else {
		//Otherwise we have all valid records. show the first (100)  for visual verification.
	echo _('Below are the 1st 100 records as parsed');
	echo '<hr width=20%>';
	foreach($LineItem->SerialItems as $SItem){
		echo $SItem->BundleRef.'<br />';
		$i++;
		if ($i == 100) {
			break;
		}
	}
}

?>