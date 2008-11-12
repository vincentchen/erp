<?php

/* Check that the stock code exists*/
	function VerifyWorkOrderExists($WorkOrder, $i, $Errors, $db) {
		$Searchsql = "SELECT count(wo)
				FROM workorders
				WHERE wo='".$WorkOrder."'";
		$SearchResult=DB_query($Searchsql, $db);
		$answer = DB_fetch_array($SearchResult);
		if ($answer[0]==0) {
			$Errors[$i] = WorkOrderDoesntExist;
		}
		return $Errors;
	}

/* Check that the stock location is set up in the weberp database */
	function VerifyStockLocation($location, $i, $Errors, $db) {
		$Searchsql = 'SELECT COUNT(loccode)
					 FROM locations
					  WHERE loccode="'.$location.'"';
		$SearchResult=DB_query($Searchsql, $db);
		$answer = DB_fetch_row($SearchResult);
		if ($answer[0] == 0) {
			$Errors[$i] = LocationCodeNotSetup;
		}
		return $Errors;
	}

/* Verify that the quantity figure is numeric */
	function VerifyIssuedQuantity($quantity, $i, $Errors) {
		if (!is_numeric($quantity)) {
			$Errors[$i] = InvalidIssuedQuantity;
		}
		return $Errors;
	}

/* Verify that the quantity figure is numeric */
	function VerifyReceivedQuantity($quantity, $i, $Errors) {
		if (!is_numeric($quantity)) {
			$Errors[$i] = InvalidReceivedQuantity;
		}
		return $Errors;
	}

	function VerifyTransactionDate($TransactionDate, $i, $Errors, $db) {
		$sql='select confvalue from config where confname="'.DefaultDateFormat.'"';
		$result=DB_query($sql, $db);
		$myrow=DB_fetch_array($result);
		$DateFormat=$myrow[0];
		$DateArray=explode('/',$TransactionDate);
		if ($DateFormat=='d/m/Y') {
			$Day=$DateArray[0];
			$Month=$DateArray[1];
			$Year=$DateArray[2];
		}
		if ($DateFormat=='m/d/Y') {
			$Day=$DateArray[1];
			$Month=$DateArray[0];
			$Year=$DateArray[2];
		}
		if ($DateFormat=='Y/m/d') {
			$Day=$DateArray[2];
			$Month=$DateArray[1];
			$Year=$DateArray[0];
		}
		if (!checkdate(intval($Month), intval($Day), intval($Year))) {
			$Errors[$i] = InvalidTransactionDate;
		}
		return $Errors;
	}

	function VerifyRequiredByDate($RequiredByDate, $i, $Errors, $db) {
		$sql='select confvalue from config where confname="'.DefaultDateFormat.'"';
		$result=DB_query($sql, $db);
		$myrow=DB_fetch_array($result);
		$DateFormat=$myrow[0];
		$DateArray=explode('/',$RequiredByDate);
		if ($DateFormat=='d/m/Y') {
			$Day=$DateArray[0];
			$Month=$DateArray[1];
			$Year=$DateArray[2];
		}
		if ($DateFormat=='m/d/Y') {
			$Day=$DateArray[1];
			$Month=$DateArray[0];
			$Year=$DateArray[2];
		}
		if ($DateFormat=='Y/m/d') {
			$Day=$DateArray[2];
			$Month=$DateArray[1];
			$Year=$DateArray[0];
		}
		if (!checkdate(intval($Month), intval($Day), intval($Year))) {
			$Errors[$i] = InvalidRequiredByDate;
		}
		return $Errors;
	}

	function VerifyStartDate($StartDate, $i, $Errors, $db) {
		$sql='select confvalue from config where confname="'.DefaultDateFormat.'"';
		$result=DB_query($sql, $db);
		$myrow=DB_fetch_array($result);
		$DateFormat=$myrow[0];
		$DateArray=explode('/',$StartDate);
		if ($DateFormat=='d/m/Y') {
			$Day=$DateArray[0];
			$Month=$DateArray[1];
			$Year=$DateArray[2];
		}
		if ($DateFormat=='m/d/Y') {
			$Day=$DateArray[1];
			$Month=$DateArray[0];
			$Year=$DateArray[2];
		}
		if ($DateFormat=='Y/m/d') {
			$Day=$DateArray[2];
			$Month=$DateArray[1];
			$Year=$DateArray[0];
		}
		if (!checkdate(intval($Month), intval($Day), intval($Year))) {
			$Errors[$i] = InvalidStartDate;
		}
		return $Errors;
	}

	function VerifyCostIssued($CostIssued, $i, $Errors) {
		if (!is_numeric($CostIssued)) {
			$Errors[$i] = InvalidCostIssued;
		}
		return $Errors;
	}

	function VerifyQtyReqd($QtyReqd, $i, $Errors) {
		if (!is_numeric($QtyReqd)) {
			$Errors[$i] = InvalidQuantityRequired;
		}
		return $Errors;
	}

	function VerifyQtyRecd($QtyRecd, $i, $Errors) {
		if (!is_numeric($QtyRecd)) {
			$Errors[$i] = InvalidQuantityReceived;
		}
		return $Errors;
	}

	function VerifyStdCost($StdCost, $i, $Errors) {
		if (!is_numeric($StdCost)) {
			$Errors[$i] = InvalidStandardCost;
		}
		return $Errors;
	}

	function VerifyLotSerialNumber($nextlotsnref, $i, $Errors) {
		if (strlen($nextlotsnref)>20) {
			$Errors[$i] = IncorrectSerialNumber;
		}
		return $Errors;
	}

	function InsertWorkOrder($WorkOrderDetails, $user, $password) {
		$Errors = array();
		$db = db($user, $password);
		if (gettype($db)=='integer') {
			$Errors[0]=NoAuthorisation;
			return $Errors;
		}
		foreach ($WorkOrderDetails as $key => $value) {
			$WorkOrderDetails[$key] = DB_escape_string($value);
		}
		$WorkOrder['wo']=GetNextTransactionNo(40, $db);
		$WorkOrderItem['wo']=$WorkOrder['wo'];
		if (isset($WorkOrderDetails['loccode'])){
			$Errors=VerifyFromStockLocation($WorkOrderDetails['loccode'], sizeof($Errors), $Errors, $db);
			$WorkOrder['loccode']=$WorkOrderDetails['loccode'];
		}
		if (isset($WorkOrderDetails['requiredby'])){
			$Errors=VerifyRequiredByDate($WorkOrderDetails['requiredby'], sizeof($Errors), $Errors, $db);
			$WorkOrder['requiredby']=$WorkOrderDetails['requiredby'];
		}
		if (isset($WorkOrderDetails['startdate'])){
			$Errors=VerifyStartDate($WorkOrderDetails['startdate'], sizeof($Errors), $Errors, $db);
			$WorkOrder['startdate']=$WorkOrderDetails['startdate'];
		}
		if (isset($WorkOrderDetails['costissued'])){
			$Errors=VerifyCostIssued($WorkOrderDetails['costissued'], sizeof($Errors), $Errors, $db);
			$WorkOrder['costissued']=$WorkOrderDetails['costissued'];
		}
		if (isset($WorkOrderDetails['closed'])){
			$Errors=VerifyCompleted($WorkOrderDetails['closed'], sizeof($Errors), $Errors);
			$WorkOrder['closed']=$WorkOrderDetails['closed'];
		}
		if (isset($WorkOrderDetails['stockid'])){
			$Errors=VerifyStockCodeExists($WorkOrderDetails['stockid'], sizeof($Errors), $Errors, $db);
			$WorkOrderItem['stockid']=$WorkOrderDetails['stockid'];
		}
		if (isset($WorkOrderDetails['qtyreqd'])){
			$Errors=VerifyQtyReqd($WorkOrderDetails['qtyreqd'], sizeof($Errors), $Errors);
			$WorkOrderItem['qtyreqd']=$WorkOrderDetails['qtyreqd'];
		}
		if (isset($WorkOrderDetails['qtyrecd'])){
			$Errors=VerifyQtyRecd($WorkOrderDetails['qtyrecd'], sizeof($Errors), $Errors);
			$WorkOrderItem['qtyrecd']=$WorkOrderDetails['qtyrecd'];
		}
		if (isset($WorkOrderDetails['stdcost'])){
			$Errors=VerifyStdCost($WorkOrderDetails['stdcost'], sizeof($Errors), $Errors);
			$WorkOrderItem['stdcost']=$WorkOrderDetails['stdcost'];
		}
		if (isset($WorkOrderDetails['nextlotsnref'])){
			$Errors=VerifyLotSerialNumber($WorkOrderDetails['nextlotsnref'], sizeof($Errors), $Errors);
			$WorkOrderItem['nextlotsnref']=$WorkOrderDetails['nextlotsnref'];
		}

		$WOFieldNames='';
		$WOFieldValues='';
		foreach ($WorkOrder as $key => $value) {
			$WOFieldNames.=$key.', ';
			$WOFieldValues.='"'.$value.'", ';
		}
		$ItemFieldNames='';
		$ItemFieldValues='';
		foreach ($WorkOrderItem as $key => $value) {
			$ItemFieldNames.=$key.', ';
			$ItemFieldValues.='"'.$value.'", ';
		}
		if (sizeof($Errors)==0) {
			$wosql = 'INSERT INTO workorders ('.substr($WOFieldNames,0,-2).') '.
				'VALUES ('.substr($WOFieldValues,0,-2).') ';
			$itemsql = 'INSERT INTO woitems ('.substr($ItemFieldNames,0,-2).') '.
				'VALUES ('.substr($ItemFieldValues,0,-2).') ';
			$systypessql = 'UPDATE systypes set typeno='.GetNextTransactionNo(40, $db).' where typeid=40';
			DB_Txn_Begin($db);
			$woresult = DB_Query($wosql, $db);
			$itemresult = DB_Query($itemsql, $db);
			$systyperesult = DB_Query($systypessql, $db);
			DB_Txn_Commit($db);
			if (DB_error_no($db) != 0) {
				$Errors[0] = DatabaseUpdateFailed;
			} else {
				$Errors[0]=0;
				$Errors[1]=$WorkOrder['wo'];
			}
		}
		return $Errors;
	}

	function WorkOrderIssue($WONumber, $StockID, $Location, $Quantity, $TranDate, $user, $password) {
		$Errors = array();
		$db = db($user, $password);
		if (gettype($db)=='integer') {
			$Errors[0]=NoAuthorisation;
			return $Errors;
		}
		$Errors = VerifyStockCodeExists($StockID, sizeof($Errors), $Errors, $db);
		$Errors = VerifyWorkOrderExists($WONumber, sizeof($Errors), $Errors, $db);
		$Errors = VerifyStockLocation($Location, sizeof($Errors), $Errors, $db);
		$Errors = VerifyIssuedQuantity($Quantity, sizeof($Errors), $Errors);
		$Errors = VerifyTransactionDate($TranDate, sizeof($Errors), $Errors);
		if (sizeof($Errors>0)) {
			return $Errors;
		} else {
			$balances=GetStockBalance($StockID, $user, $password);
			$balance=0;
			for ($i=0; $i<sizeof($balances); $i++) {
				$balance=$balance+$balances[$i]['quantity'];
			}
			$newqoh = $Quantity + $balance;
			$itemdetails = GetStockItem($StockID, $user, $password);
			$wipglact=GetCategoryGLCode($itemdetails['categoryid'], 'wipact', $db);
			$stockact=GetCategoryGLCode($itemdetails['categoryid'], 'stockact', $db);
			$cost=$itemdetails['materialcost']+$itemdetails['labourcost']+$itemdetails['overheadcost'];

			$stockmovesql='INSERT INTO stockmoves (stockid, type, transno, loccode, trandate, prd, reference, qty, newqoh,
				price, standardcost)
				VALUES ("'.$StockID.'", 28,'.GetNextTransactionNo(28, $db).',"'.$Location.'","'.$TranDate.
				'",'.GetPeriodFromTransactionDate($TranDate, sizeof($Errors), $Errors, $db).
				',"'.$WONumber.'",'.$Quantity.','.$newqoh.','.$cost.','.$cost.')';
			$locstocksql='UPDATE locstock SET quantity = quantity + '.$Quantity.' WHERE loccode="'.
				$Location.'" AND stockid="'.$StockID.'"';
			$glupdatesql1='INSERT INTO gltrans (type, typeno, trandate, periodno, account, amount, narrative)
						VALUES (28,'.GetNextTransactionNo(28, $db).',"'.$TranDate.
						'",'.GetPeriodFromTransactionDate($TranDate, sizeof($Errors), $Errors, $db).
						','.$wipglact.','.$cost*-$Quantity.
						',"'.$StockID.' x '.$Quantity.' @ '.$cost.'")';
			$glupdatesql2='INSERT INTO gltrans (type, typeno, trandate, periodno, account, amount, narrative)
						VALUES (28,'.GetNextTransactionNo(28, $db).',"'.$TranDate.
						'",'.GetPeriodFromTransactionDate($TranDate, sizeof($Errors), $Errors, $db).
						','.$stockact.','.$cost*$Quantity.
						',"'.$StockID.' x '.$Quantity.' @ '.$cost.'")';
			$systypessql = 'UPDATE systypes set typeno='.GetNextTransactionNo(28, $db).' where typeid=28';

			DB_Txn_Begin($db);
			DB_query($stockmovesql, $db);
			DB_query($locstocksql, $db);
			DB_query($glupdatesql1, $db);
			DB_query($glupdatesql2, $db);
			DB_query($systypessql, $db);
			DB_Txn_Commit($db);
			if (DB_error_no($db) != 0) {
				$Errors[0] = DatabaseUpdateFailed;
				return $Errors;
			} else {
				return 0;
			}
		}
	}

	function WorkOrderReceive($WONumber, $StockID, $Location, $Quantity, $TranDate, $user, $password) {
		$Errors = array();
		$db = db($user, $password);
		if (gettype($db)=='integer') {
			$Errors[0]=NoAuthorisation;
			return $Errors;
		}
		$Errors = VerifyStockCodeExists($StockID, sizeof($Errors), $Errors, $db);
		$Errors = VerifyWorkOrderExists($WONumber, sizeof($Errors), $Errors, $db);
		$Errors = VerifyStockLocation($Location, sizeof($Errors), $Errors, $db);
		$Errors = VerifyReceivedQuantity($Quantity, sizeof($Errors), $Errors);
		$Errors = VerifyTransactionDate($TranDate, sizeof($Errors), $Errors);
		if (sizeof($Errors>0)) {
			return $Errors;
		} else {
			$itemdetails = GetStockItem($StockID, $user, $password);
			$balances=GetStockBalance($StockID, $user, $password);
			$balance=0;
			for ($i=0; $i<sizeof($balances); $i++) {
				$balance=$balance+$balances[$i]['quantity'];
			}
			$newqoh = $Quantity + $balance;
			$cost=$itemdetails['materialcost']+$itemdetails['labourcost']+$itemdetails['overheadcost'];
			$stockmovesql='INSERT INTO stockmoves (stockid, type, transno, loccode, trandate, prd, reference, qty, newqoh,
				price, standardcost)
				VALUES ("'.$StockID.'", 26,'.GetNextTransactionNo(26, $db).',"'.$Location.'","'.$TranDate.
				'",'.GetPeriodFromTransactionDate($TranDate, sizeof($Errors), $Errors, $db).
				',"'.$WONumber.'",'.$Quantity.','.$newqoh.','.$cost.','.$cost.')';
			$locstocksql='UPDATE locstock SET quantity = quantity + '.$Quantity.' WHERE loccode="'.
				$Location.'" AND stockid="'.$StockID.'"';
			DB_Txn_Begin($db);
			DB_query($stockmovesql, $db);
			DB_query($locstocksql, $db);
			DB_Txn_Commit($db);
			if (DB_error_no($db) != 0) {
				$Errors[0] = DatabaseUpdateFailed;
				return $Errors;
			} else {
				return 0;
			}
		}
	}
?>
