<?php

$PageSecurity=1;

include('includes/session.inc');

$title = _('Fulfill Stock Requests');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/inventory.png" title="' . _('Contract') . '" alt="" />' . _('Fulfill Stock Requests') . '</p>';

if (isset($_POST['UpdateAll'])) {
	foreach ($_POST as $key => $value) {
		if (mb_strpos($key,'Qty')) {
			$RequestID = mb_substr($key,0, mb_strpos($key,'Qty'));
			$LineID = mb_substr($key,mb_strpos($key,'Qty')+3);
			$Quantity = $_POST[$RequestID.'Qty'.$LineID];
			$StockID = $_POST[$RequestID.'StockID'.$LineID];
			$Location = $_POST[$RequestID.'Location'.$LineID];
			$Tag = $_POST[$RequestID.'Tag'.$LineID];
			$RequestedQuantity = $_POST[$RequestID.'RequestedQuantity'.$LineID];
			if (isset($_POST[$RequestID.'Completed'.$LineID])) {
				$Completed=True;
			} else {
				$Completed=False;
			}

			$sql="SELECT materialcost, labourcost, overheadcost FROM stockmaster WHERE stockid='".$StockID."'";
			$result=DB_query($sql, $db);
			$myrow=DB_fetch_array($result);
			$StandardCost=$myrow['materialcost']+$myrow['labourcost']+$myrow['overheadcost'];

			$Narrative = _('Issue') . ' ' . $Quantity . ' ' . _('of') . ' '. $StockID . ' ' . _('to department');

			$AdjustmentNumber = GetNextTransNo(17,$db);
			$PeriodNo = GetPeriod (Date($_SESSION['DefaultDateFormat']), $db);
			$SQLAdjustmentDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));

			$Result = DB_Txn_Begin($db);

			// Need to get the current location quantity will need it later for the stock movement
			$SQL="SELECT locstock.quantity
					FROM locstock
					WHERE locstock.stockid='" . $StockID . "'
						AND loccode= '" . $Location . "'";
			$Result = DB_query($SQL, $db);
			if (DB_num_rows($Result)==1){
				$LocQtyRow = DB_fetch_row($Result);
				$QtyOnHandPrior = $LocQtyRow[0];
			} else {
				// There must actually be some error this should never happen
				$QtyOnHandPrior = 0;
			}

			if ($_SESSION['ProhibitNegativeStock']==0 or ($_SESSION['ProhibitNegativeStock']==1 and $QtyOnHandPrior>=$Quantity)) {

				$SQL = "INSERT INTO stockmoves (
									stockid,
									type,
									transno,
									loccode,
									trandate,
									prd,
									reference,
									qty,
									newqoh)
								VALUES (
									'" . $StockID . "',
									17,
									'" . $AdjustmentNumber . "',
									'" . $Location . "',
									'" . $SQLAdjustmentDate . "',
									'" . $PeriodNo . "',
									'" . $Narrative ."',
									'" . -$Quantity . "',
									'" . ($QtyOnHandPrior - $Quantity) . "'
								)";


				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
				$DbgMsg =  _('The following SQL to insert the stock movement record was used');
				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);


				/*Get the ID of the StockMove... */
				$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

				$SQL="UPDATE stockrequestitems
						SET qtydelivered=qtydelivered+".$Quantity."
						WHERE dispatchid='".$RequestID."'
							AND dispatchitemsid='".$LineID."'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the stock record was used');
				$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);

				$SQL = "UPDATE locstock SET quantity = quantity - '" . $Quantity . "'
									WHERE stockid='" . $StockID . "'
										AND loccode='" . $Location . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the stock record was used');

				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

				if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $StandardCost > 0){

					$StockGLCodes = GetStockGLCode($StockID,$db);

					$SQL = "INSERT INTO gltrans (type,
												typeno,
												trandate,
												periodno,
												account,
												amount,
												narrative,
												tag)
											VALUES (17,
												'"  .$AdjustmentNumber . "',
												'" . $SQLAdjustmentDate . "',
												'" . $PeriodNo . "',
												'" . $StockGLCodes['issueglact'] . "',
												'" . $StandardCost * ($Quantity) . "',
												'" . $Narrative . "',
												'" . $Tag . "'
											)";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
					$DbgMsg = _('The following SQL to insert the GL entries was used');
					$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);

					$SQL = "INSERT INTO gltrans (type,
												typeno,
												trandate,
												periodno,
												account,
												amount,
												narrative,
												tag)
											VALUES (17,
												'" . $AdjustmentNumber . "',
												'" . $SQLAdjustmentDate . "',
												'" . $PeriodNo . "',
												'" . $StockGLCodes['stockact'] . "',
												'" . $StandardCost * -$Quantity . "',
												'" . $Narrative . "',
												'" . $Tag . "'
											)";

					$Errmsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
					$DbgMsg = _('The following SQL to insert the GL entries was used');
					$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);
				}

				if (($Quantity>=$RequestedQuantity) or $Completed==True) {
					$SQL="UPDATE stockrequestitems
								SET completed=1
							WHERE dispatchid='".$RequestID."'
								AND dispatchitemsid='".$LineID."'";
					$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);
				}

				$Result = DB_Txn_Commit($db);

				$ConfirmationText = _('A stock issue for'). ' ' . $StockID . ' ' . _('has been created from location').' ' . $Location .' '. _('for a quantity of') . ' ' . $Quantity ;
				prnMsg( $ConfirmationText,'success');

				if ($_SESSION['InventoryManagerEmail']!=''){
					$ConfirmationText = $ConfirmationText . ' ' . _('by user') . ' ' . $_SESSION['UserID'] . ' ' . _('at') . ' ' . Date('Y-m-d H:i:s');
					$EmailSubject = _('Stock adjustment for'). ' ' . $StockID;
					mail($_SESSION['InventoryManagerEmail'],$EmailSubject,$ConfirmationText);
				}
			} else {
				$ConfirmationText = _('A stock issue for'). ' ' . $StockID . ' ' . _('from location').' ' . $Location .' '. _('for a quantity of') . ' ' . $Quantity . ' ' . _('cannot be created as there is insufficient stock and your system is configured to not allow negative stocks');
				prnMsg( $ConfirmationText,'warn');
			}
		}
	}
}

// Check if request can be closed and close if done.
if (isset($RequestID)) {
	$SQL="SELECT dispatchid
			FROM stockrequestitems
			WHERE dispatchid='".$RequestID."'
			AND completed=0";
	$Result=DB_query($SQL, $db);
	if (DB_num_rows($Result)==0) {
		$SQL="UPDATE stockrequest
				SET closed=1
			WHERE dispatchid='".$RequestID."'";
		$Result=DB_query($SQL, $db);
	}
}

if (!isset($_POST['Location'])) {
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection"><tr>';
	echo '<td>' . _('Choose a location to issue requests from') . '</td>
		<td><select name="Location">';
	$sql = "SELECT loccode, locationname FROM locations";
	$resultStkLocs = DB_query($sql,$db);
	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_SESSION['Adjustment']->StockLocation)){
			if ($myrow['loccode'] == $_SESSION['Adjustment']->StockLocation){
				echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			} else {
!ˆ!0<Mouse>C!‡!0
