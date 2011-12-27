<?php
/* $Id$*/

include('includes/session.inc');
$title = _('Inventory Location Transfer Shipment');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_POST['Submit']) OR isset($_POST['EnterMoreItems'])){
/*Trap any errors in input */

	$InputError = False; /*Start off hoping for the best */
	$TotalItems = 0;
	//Make sure this Transfer has not already been entered... aka one way around the refresh & insert new records problem
	$result = DB_query("SELECT * FROM loctransfers WHERE reference='" . $_POST['Trf_ID'] . "'",$db);
	if (DB_num_rows($result)!=0){
		$InputError = true;
		$ErrorMessage = _('This transaction has already been entered') . '. ' . _('Please start over now').'<br />';
		unset($_POST['submit']);
		unset($_POST['EnterMoreItems']);
		for ($i=$_POST['LinesCounter']-10;$i<$_POST['LinesCounter'];$i++){
			unset($_POST['StockID' . $i]);
			unset($_POST['StockQTY' . $i]);
		}
	}  else {
      if ($_FILES['SelectedTransferFile']['name']) { //start file processing
      	//initialize
       	$InputError = false;
        $ErrorMessage='';
        //get file handle
        $FileHandle = fopen($_FILES['SelectedTransferFile']['tmp_name'], 'r');
        $TotalItems=0;
    	//loop through file rows
    	while ( ($myrow = fgetcsv($FileHandle, 10000, ',')) !== FALSE ) {

    		if (count($myrow) != 2){
    			prnMsg (_('File contains') . ' '. count($myrow) . ' ' . _('columns, but only 2 columns are expected. The comma separated file should have just two columns the first for the item code and the second for the quantity to transfer'),'error');
    			fclose($FileHandle);
    			include('includes/footer.inc');
    			exit;
    		}

    		// cleanup the data (csv files often import with empty strings and such)
    		$StockID='';
    		$Quantity=0;
    		for ($i=0; $i<count($myrow);$i++) {
    			switch ($i) {
    				case 0:
    					$StockID = trim(mb_strtoupper($myrow[$i]));
                        $result = DB_query("SELECT COUNT(stockid) FROM stockmaster WHERE stockid='" . $StockID . "'",$db);
            			$StockIDCheck = DB_fetch_row($result);
            			if ($StockIDCheck[0]==0){
            				$InputError = True;
            				$ErrorMessage .= _('The part code entered of'). ' ' . $StockID . ' '. _('is not set up in the database') . '. ' . _('Only valid parts can be entered for transfers'). '<br />';
           				}
    					break;
    				case 1:
           				$Quantity = filter_number_format($myrow[$i]);
    					if (!is_numeric($Quantity)){
				           $InputError = True;
                           $ErrorMessage .= _('The quantity entered for'). ' ' . $StockID . ' ' . _('of') . $Quantity . ' '. _('is not numeric.') . _('The quantity entered for transfers is expected to be numeric');
                        }
    					break;
                } // end switch statement
                if ($_SESSION['ProhibitNegativeStock']==1){
                    // Only if stock exists at this location
            		$result = DB_query("SELECT quantity
            							FROM locstock
            							WHERE stockid='" . $StockID . "'
            							AND loccode='".$_POST['FromStockLocation']."'",
            							$db);
                    $CheckStockRow = DB_fetch_array($result);
            		if ($CheckStockRow['quantity'] < $Quantity){
            			$InputError = True;
            			$ErrorMessage .= _('The item'). ' ' . $StockID . ' ' . _('does not have enough stock available (') . ' ' . $CheckStockRow['quantity'] . ')' . ' ' . _('The quantity required to transfer was') .  ' ' . $Quantity . '.<br />';
                    }
                }
            } // end for loop through the columns on the row being processed
			if ($StockID!='' AND $Quantity!=0){
				$_POST['StockID' . $TotalItems] = $StockID;
				$_POST['StockQTY' . $TotalItems] = $Quantity;
				$StockID='';
				$Quantity=0;
				$TotalItems++;
			}
          } //end while there are lines in the CSV file
          $_POST['LinesCounter']=$TotalItems;
       } //end if there is a CSV file to import
          else { // process the manually input lines
			$ErrorMessage='';
			
			if (isset($_POST['ClearAll'])){
				unset($_POST['EnterMoreItems']);
				for ($i=$_POST['LinesCounter']-10;$i<$_POST['LinesCounter'];$i++){
					unset($_POST['StockID' . $i]);
					unset($_POST['StockQTY' . $i]);
				}
			}
			for ($i=$_POST['LinesCounter']-10;$i<$_POST['LinesCounter'];$i++){
				if (isset($_POST['Delete' . $i])){ //check box to delete the item is set
					unset($_POST['StockID' . $i]);
					unset($_POST['StockQTY' . $i]);
				}
				if (isset($_POST['StockID' . $i]) AND $_POST['StockID' . $i]!=''){
					$_POST['StockID' . $i]=trim(mb_strtoupper($_POST['StockID' . $i]));
					$result = DB_query("SELECT COUNT(stockid) FROM stockmaster WHERE stockid='" . $_POST['StockID' . $i] . "'",$db);
					$myrow = DB_fetch_row($result);
					if ($myrow[0]==0){
						$InputError = True;
						$ErrorMessage .= _('The part code entered of'). ' ' . $_POST['StockID' . $i] . ' '. _('is not set up in the database') . '. ' . _('Only valid parts can be entered for transfers'). '<br />';
						$_POST['LinesCounter'] -= 10;
					}
					DB_free_result( $result );
					if (!is_numeric(filter_number_format($_POST['StockQTY' . $i]))){
						$InputError = True;
						$ErrorMessage .= _('The quantity entered of'). ' ' . $_POST['StockQTY' . $i] . ' '. _('for part code'). ' ' . $_POST['StockID' . $i] . ' '. _('is not numeric') . '. ' . _('The quantity entered for transfers is expected to be numeric').'<br />';
						$_POST['LinesCounter'] -= 10;
					}
					if (filter_number_format($_POST['StockQTY' . $i]) <= 0){
						$InputError = True;
						$ErrorMessage .= _('The quantity entered for').' '. $_POST['StockID' . $i] . ' ' . _('is less than or equal to 0') . '. ' . _('Please correct this or remove the item').'<br />';
						$_POST['LinesCounter'] -= 10;
					}
					if ($_SESSION['ProhibitNegativeStock']==1){
						// Only if stock exists at this location
						$result = DB_query("SELECT quantity
											FROM locstock
											WHERE stockid='" . $_POST['StockID' . $i] . "'
											AND loccode='".$_POST['FromStockLocation']."'",
											$db);
	
						$myrow = DB_fetch_row($result);
						if ($myrow[0] < filter_number_format($_POST['StockQTY' . $i])){
							$InputError = True;
							$ErrorMessage .= _('The part code entered of'). ' ' . $_POST['StockID' . $i] . ' '. _('does not have enough stock available for transfer.') . '.<br />';
							$_POST['LinesCounter'] -= 10;
						}
					}
					DB_free_result( $result );
					$TotalItems++;
				}
			}//for all LinesCounter
		}
	
		if ($TotalItems == 0){
			$InputError = True;
			$ErrorMessage .= _('You must enter at least 1 Stock Item to transfer').'<br />';
		}
	
	/*Ship location and Receive location are different */
		if ($_POST['FromStockLocation']==$_POST['ToStockLocation']){
			$InputError=True;
			$ErrorMessage .= _('The transfer must have a different location to receive into and location sent from');
		}
	 } //end if the transfer is not a duplicated
}

if(isset($_POST['Submit']) AND $InputError==False){

	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to BEGIN Location Transfer transaction');
	
	DB_Txn_Begin($db);
	
	for ($i=0;$i < $_POST['LinesCounter'];$i++){

		if($_POST['StockID' . $i] != ''){
			$DecimalsSql = "SELECT decimalplaces
							FROM stockmaster
							WHERE stockid='" . $_POST['StockID' . $i] . "'";
			$DecimalResult = DB_Query($DecimalsSql, $db);
			$DecimalRow = DB_fetch_array($DecimalResult);
			$sql = "INSERT INTO loctransfers (reference,
								stockid,
								shipqty,
								shipdate,
								shiploc,
								recloc)
						VALUES ('" . $_POST['Trf_ID'] . "',
							'" . $_POST['StockID' . $i] . "',
							'" . round(filter_number_format($_POST['StockQTY' . $i]), $DecimalRow['decimalplaces']) . "',
							'" . Date('Y-m-d') . "',
							'" . $_POST['FromStockLocation']  ."',
							'" . $_POST['ToStockLocation'] . "')";
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to enter Location Transfer record for'). ' '.$_POST['StockID' . $i];
			$resultLocShip = DB_query($sql,$db, $ErrMsg);
		}
	}
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to COMMIT Location Transfer transaction');
	DB_Txn_Commit($db);

	prnMsg( _('The inventory transfer records have been created successfully'),'success');
	echo '<p><a href="'.$rootpath.'/PDFStockLocTransfer.php?TransferNo=' . $_POST['Trf_ID'] . '">'. _('Print the Transfer Docket'). '</a></p>';
	include('includes/footer.inc');

} else {
	//Get next Inventory Transfer Shipment Reference Number
	if (isset($_GET['Trf_ID'])){
		$Trf_ID = $_GET['Trf_ID'];
	} elseif (isset($_POST['Trf_ID'])){
		$Trf_ID = $_POST['Trf_ID'];
	}

	if(!isset($Trf_ID)){
		$Trf_ID = GetNextTransNo(16,$db);
	}

	if (isset($InputError) and $InputError==true){
		echo '<br />';

		prnMsg($ErrorMessage, 'error');
		echo '<br />';

	}

	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/supplier.png" title="' . _('Dispatch') . '" alt="" />' . ' ' . $title . '</p>';

	echo '<form enctype="multipart/form-data" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" method="post">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<table class="selection">';
	echo '<tr>
			<th colspan="4"><input type="hidden" name="Trf_ID" value="' . $Trf_ID . '" /><font size="3" color="blue">'. _('Inventory Location Transfer Shipment Reference').' # '. $Trf_ID. '</font></th>
		</tr>';

	$sql = "SELECT loccode, locationname FROM locations";
	$resultStkLocs = DB_query($sql,$db);
	
	echo '<tr>
			<td>' . _('From Stock Location') . ':</td>
			<td><select name="FromStockLocation">';
			
	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['FromStockLocation'])){
			if ($myrow['loccode'] == $_POST['FromStockLocation']){
				echo '<option selected value="' . $myrow['loccode'] . '">' . $myrow['locationname']. '</option>';
			} else {
				echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<option selected Value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			$_POST['FromStockLocation']=$myrow['loccode'];
		} else {
			echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}
	echo '</select></td>';

	DB_data_seek($resultStkLocs,0); //go back to the start of the locations result
	echo '<td>'._('To Stock Location').':</td>
			<td><select name="ToStockLocation">';
	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['ToStockLocation'])){
			if ($myrow['loccode'] == $_POST['ToStockLocation']){
				echo '<option selected value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			} else {
				echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<option selected value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			$_POST['ToStockLocation']=$myrow['loccode'];
		} else {
			echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}
	echo '</select></td></tr>';

	echo '<tr>
			<td>' . _('Upload CSV file of Transfer Items and Quantites') . ':</td>
			<td><input name="SelectedTransferFile" type="file" /></td>
		  </tr>
		  </table>';
	
	echo '<table class="selection">';

	$TableHeader = '<tr>
						<th>'. _('Item Code'). '</th>
						<th>'. _('Quantity'). '</th>
						<th>' . _('Clear All') . ':<input type="checkbox" name="ClearAll" /></th> 
					</tr>';
	echo $TableHeader;

	$k=0; /* page heading row counter */
	$j=0; /* row counter for reindexing */
	if(isset($_POST['LinesCounter'])){

		for ($i=0;$i < $_POST['LinesCounter'];$i++){
			if (!isset($_POST['StockID'. $i])){
				continue;
			}
			if ($_POST['StockID' . $i] ==''){
				break;
			}
			if ($k==18){
				echo $TableHeader;
				$k=0;
			}
			$k++;
			
			echo '<tr>
				<td><input type="text" name="StockID' . $j .'" size="21"  maxlength="20" value="' . $_POST['StockID' . $i] . '" /></td>
				<td><input type="text" name="StockQTY' . $j .'" size="10" maxlength="10" class="number" value="' . locale_number_format($_POST['StockQTY' . $i],'Variable') . '" /></td>
				<td>' . _('Delete') . '<input type="checkbox" name="Delete' . $j .'" /></td>
			</tr>';
			$j++;
		}
	}else {
		$j = 0;
	}
	// $i is incremented an extra time, so 9 to get 10...
	$z=($j + 9);

	while($j < $z) {
		if (!isset($_POST['StockID' . $j])) {
			$_POST['StockID' . $j]='';
		}
		if (!isset($_POST['StockQTY' . $j])) {
			$_POST['StockQTY' . $j]=0;
		}
		echo '<tr>
			<td><input type="text" name="StockID' . $j .'" size="21"  maxlength="20" value="' . $_POST['StockID' . $j] . '" /></td>
			<td><input type="text" name="StockQTY' . $j .'" size="10" maxlength="10" class="number" value="' . locale_number_format($_POST['StockQTY' . $j]) . '" /></td>
		</tr>';
		$j++;
	}

	echo '</table>
		<br />
		<div class="centre">
		<input type="hidden" name="LinesCounter" value="'. $j .'" />
		<input type=submit name="EnterMoreItems" value="'. _('Add More Items'). '" />
		<input type="submit" name="Submit" value="'. _('Create Transfer Shipment'). '" />
		<br />';
		
	echo '<script type="text/javascript">defaultControl(document.forms[0].StockID0);</script>';
	
	echo '</form>
		</div>';
	include('includes/footer.inc');
}
?>