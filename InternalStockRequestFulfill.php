<?php

/* $Id: InternalStockRequestFulfill.php  $*/

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

			$Narrative = _('Issue') . ' ' . $Quantity . ' ' . _('of') . ' '. $StockID . ' ' . _('to department') .' ' . _('from') . ' ' . $Location ;

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
	$sql = "SELECT loccode, locationname 
			FROM locations 
			WHERE internalrequest = 1
			ORDER BY locationname";
	$resultStkLocs = DB_query($sql,$db);
	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_SESSION['Adjustment']->StockLocation)){
			if ($myrow['loccode'] == $_SESSION['Adjustment']->StockLocation){
				echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			} else {
				echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			$_POST['StockLocation']=$myrow['loccode'];
		} else {
		 echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}
	echo '</select></td></tr>';
	echo '</table><br />';
	echo '<div class="centre"><input type="submit" name="EnterAdjustment" value="'. _('Show Requests'). '" /></div>';
    echo '</div>
          </form>';
	include('includes/footer.inc');
	exit;
}

/* Retrieve the requisition header information
 */
if (isset($_POST['Location'])) {
	$sql="SELECT stockrequest.dispatchid,
			locations.locationname,
			stockrequest.despatchdate,
			stockrequest.narrative,
			departments.description,
			www_users.realname,
			www_users.email
		FROM stockrequest
		LEFT JOIN departments
			ON stockrequest.departmentid=departments.departmentid
		LEFT JOIN locations
			ON stockrequest.loccode=locations.loccode
		LEFT JOIN www_users
			ON www_users.userid=departments.authoriser
	WHERE stockrequest.authorised=1
		AND stockrequest.closed=0
		AND stockrequest.loccode='".$_POST['Location']."'";
	$result=DB_query($sql, $db);

	if (DB_num_rows($result)==0) {
		prnMsg( _('There are no outstanding authorised requests for this location'), 'info');
		echo '<br />';
		echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Select another location') . '</a></div>';
		include('includes/footer.inc');
		exit;
	}

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection"><tr>';

	/* Create the table for the purchase order header */
	echo '<th>'._('Request Number').'</th>';
	echo '<th>'._('Department').'</th>';
	echo '<th>'._('Location Of Stock').'</th>';
	echo '<th>'._('Requested Date').'</th>';
	echo '<th>'._('Narrative').'</th>';
	echo '</tr>';

	while ($myrow=DB_fetch_array($result)) {

		echo '<tr>';
		echo '<td>'.$myrow['dispatchid'].'</td>';
		echo '<td>'.$myrow['description'].'</td>';
		echo '<td>'.$myrow['locationname'].'</td>';
		echo '<td>'.ConvertSQLDate($myrow['despatchdate']).'</td>';
		echo '<td>'.$myrow['narrative'].'</td>';
		echo '</tr>';
		$linesql="SELECT stockrequestitems.dispatchitemsid,
						stockrequestitems.dispatchid,
						stockrequestitems.stockid,
						stockrequestitems.decimalplaces,
						stockrequestitems.uom,
						stockmaster.description,
						stockrequestitems.quantity,
						stockrequestitems.qtydelivered
				FROM stockrequestitems
				LEFT JOIN stockmaster
				ON stockmaster.stockid=stockrequestitems.stockid
			WHERE dispatchid='".$myrow['dispatchid'] . "'
				AND completed=0";
		$lineresult=DB_query($linesql, $db);

		echo '<tr><td></td><td colspan="5" align="left"><table class="selection" align="left">';
		echo '<th>'._('Product').'</th>';
		echo '<th>'._('Quantity') . '<br />' . _('Required').'</th>';
		echo '<th>'._('Quantity') . '<br />' . _('Delivered').'</th>';
		echo '<th>'._('Units').'</th>';
		echo '<th>'._('Completed').'</th>';
		echo '<th>'._('Tag').'</th>';
		echo '</tr>';

		while ($linerow=DB_fetch_array($lineresult)) {
			echo '<tr>';
			echo '<td>'.$linerow['description'].'</td>';
			echo '<td class="number">'.locale_number_format($linerow['quantity']-$linerow['qtydelivered'],$linerow['decimalplaces']).'</td>';
			echo '<td class="number">
					<input type="text" class="number" name="'. $linerow['dispatchid'] . 'Qty'. $linerow['dispatchitemsid'] . '" value="'.locale_number_format($linerow['quantity']-$linerow['qtydelivered'],$linerow['decimalplaces']).'" />
				</td>';
			echo '<td>'.$linerow['uom'].'</td>';
			echo '<td><input type="checkbox" name="'. $linerow['dispatchid'] . 'Completed'. $linerow['dispatchitemsid'] . '" /></td>';
			//Select the tag
			echo '<td><select name="'. $linerow['dispatchid'] . 'Tag'. $linerow['dispatchitemsid'] . '">';

			$SQL = "SELECT tagref,
							tagdescription
						FROM tags
						ORDER BY tagref";

			$TagResult=DB_query($SQL,$db);
			echo '<option value=0>0 - None</option>';
			while ($mytagrow=DB_fetch_array($TagResult)){
				if (isset($_SESSION['Adjustment']->tag) and $_SESSION['Adjustment']->tag==$mytagrow['tagref']){
					echo '<option selected="selected" value="' . $mytagrow['tagref'] . '">' . $mytagrow['tagref'].' - ' .$myrow['tagdescription'] . '</option>';
				} else {
					echo '<option value="' . $mytagrow['tagref'] . '">' . $mytagrow['tagref'].' - ' .$mytagrow['tagdescription'] . '</option>';
				}
			}
			echo '</select></td>';
// End select tag
			echo '</tr>';
			echo '<input type="hidden" class="number" name="'. $linerow['dispatchid'] . 'StockID'. $linerow['dispatchitemsid'] . '" value="'.$linerow['stockid'].'" />';
			echo '<input type="hidden" class="number" name="'. $linerow['dispatchid'] . 'Location'. $linerow['dispatchitemsid'] . '" value="'.$_POST['Location'].'" />';
			echo '<input type="hidden" class="number" name="'. $linerow['dispatchid'] . 'RequestedQuantity'. $linerow['dispatchitemsid'] . '" value="'.locale_number_format($linerow['quantity']-$linerow['qtydelivered'],$linerow['decimalplaces']).'" />';
		} // end while order line detail
		echo '</table></td></tr>';
	} //end while header loop
	echo '</table>';
	echo '<br /><div class="centre"><input type="submit" name="UpdateAll" value="' . _('Update'). '" /></div>
          </div>
          </form>';
}

include('includes/footer.inc');

?>
