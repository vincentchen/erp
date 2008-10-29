<?php

	include 'api_php.php';

	include '../xmlrpc/lib/xmlrpc.inc';
	include '../xmlrpc/lib/xmlrpcs.inc';

	unset($Parameter);
	unset($ReturnValue);
	$Description = _('This function is used to insert a new customer into the webERP database.');
	$Parameter[0]['name'] = _('Customer Details');
	$Parameter[0]['description'] = _('A set of key/value pairs where the key must be identical to the name of the field to be updated. ')
			._('The field names can be found ').'<a href="Z_DescribeTable.php?table=debtorsmaster">'._('here ').'</a>'
			._('and are case sensitive. ')._('The values should be of the correct type, and the api will check them before updating the database. ')
			._('It is not necessary to include all the fields in this parameter, the database default value will be used if the field is not given.')
			.'<p>'._('If the Create Debtor Codes Automatically flag is set, then anything sent in the debtorno field will be ignored, and the debtorno field will be set automatically.');
	$Parameter[1]['name'] = _('User name');
	$Parameter[1]['description'] = _('A valid weberp username. This user should have security access  to this data.');
	$Parameter[2]['name'] = _('User password');
	$Parameter[2]['description'] = _('The weberp password associoated with this user name. ');
	$ReturnValue[0] = _('This function returns an array of integers. ').
		_('If the first element is zero then the function was successful. ').
		_('Otherwise an array of error codes is returned and no insertion takes place. ');

	$doc = '<tr><td><b><u>'._('Description').'</u></b></td><td colspan=2>' .$Description.'</td></tr>
			<tr><td valign="top"><b><u>'._('Parameters').'</u></b></td>';
	for ($i=0; $i<sizeof($Parameter); $i++) {
		$doc .= '<tr><td valign="top">'.$Parameter[$i]['name'].'</td><td>'.
			$Parameter[$i]['description'].'</td></tr>';
	}
	$doc .= '<tr><td><b><u>'._('Return Values').'</td></tr>';
	for ($i=0; $i<sizeof($ReturnValue); $i++) {
		$doc .= '<tr><td></td><td valign="top">'.$ReturnValue[$i].'</td></tr>';
	}
	$doc .= '</table>';
	$InsertCustomer_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertCustomer_doc = $doc;

	function xmlrpc_InsertCustomer($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertCustomer(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	unset($Parameter);
	unset($ReturnValue);
	$Description = _('This function is used to insert a new customer branch into the webERP database.');
	$Parameter[0]['name'] = _('Branch Details');
	$Parameter[0]['description'] = _('A set of key/value pairs where the key must be identical to the name of the field to be updated. ')
			._('The field names can be found ').'<a href="Z_DescribeTable.php?table=custbranch">'._('here ').'</a>'
			._('and are case sensitive. ')._('The values should be of the correct type, and the api will check them before updating the database. ')
			._('It is not necessary to include all the fields in this parameter, the database default value will be used if the field is not given.');
	$Parameter[1]['name'] = _('User name');
	$Parameter[1]['description'] = _('A valid weberp username. This user should have security access  to this data.');
	$Parameter[2]['name'] = _('User password');
	$Parameter[2]['description'] = _('The weberp password associoated with this user name. ');
	$ReturnValue[0] = _('This function returns an array of integers. ').
		_('If the first element is zero then the function was successful. ').
		_('Otherwise an array of error codes is returned and no insertion takes place. ');

	$doc = '<tr><td><b><u>'._('Description').'</u></b></td><td colspan=2>' .$Description.'</td></tr>
			<tr><td valign="top"><b><u>'._('Parameters').'</u></b></td>';
	for ($i=0; $i<sizeof($Parameter); $i++) {
		$doc .= '<tr><td valign="top">'.$Parameter[$i]['name'].'</td><td>'.
			$Parameter[$i]['description'].'</td></tr>';
	}
	$doc .= '<tr><td><b><u>'._('Return Values').'</td></tr>';
	for ($i=0; $i<sizeof($ReturnValue); $i++) {
		$doc .= '<tr><td></td><td valign="top">'.$ReturnValue[$i].'</td></tr>';
	}
	$doc .= '</table>';
	$InsertBranch_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertBranch_doc = $doc;

	function xmlrpc_InsertBranch($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertBranch(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	unset($Parameter);
	unset($ReturnValue);
	$Description = _('This function is used to modify a customer which is already setup in the webERP database.');
	$Parameter[0]['name'] = _('Customer Details');
	$Parameter[0]['description'] = _('A set of key/value pairs where the key must be identical to the name of the field to be updated. ')
			._('The field names can be found ').'<a href="Z_DescribeTable.php?table=debtorsmaster">'._('here ').'</a>'
			._('and are case sensitive. ')._('The values should be of the correct type, and the api will check them before updating the database. ')
			._('It is not necessary to include all the fields in this parameter, the database default value will be used if the field is not given.')
			.'<p>'._('The debtorno must already exist in the weberp database.');
	$Parameter[1]['name'] = _('User name');
	$Parameter[1]['description'] = _('A valid weberp username. This user should have security access  to this data.');
	$Parameter[2]['name'] = _('User password');
	$Parameter[2]['description'] = _('The weberp password associoated with this user name. ');
	$ReturnValue[0] = _('This function returns an array of integers. ').
		_('If the first element is zero then the function was successful. ').
		_('Otherwise an array of error codes is returned and no insertion takes place. ');

	$doc = '<tr><td><b><u>'._('Description').'</u></b></td></tr><tr><td></td><td colspan=2>' .$Description.'</td></tr>
			<tr><td valign="top"><b><u>'._('Parameters').'</u></b></td>';
	for ($i=0; $i<sizeof($Parameter); $i++) {
		$doc .= '<tr><td valign="top">'.$Parameter[$i]['name'].'</td><td>'.
			$Parameter[$i]['description'].'</td></tr>';
	}
	$doc .= '<tr><td><b><u>'._('Return Value').'</td></tr>';
	for ($i=0; $i<sizeof($ReturnValue); $i++) {
		$doc .= '<tr><td></td><td valign="top">'.$ReturnValue[$i].'</td></tr>';
	}
	$doc .= '</table>';

	$ModifyCustomer_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$ModifyCustomer_doc = $doc;

	function xmlrpc_ModifyCustomer($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(ModifyCustomer(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	unset($Parameter);
	unset($ReturnValue);
	$Description = _('This function is used to modify a customer branch which is already setup in the webERP database.');
	$Parameter[0]['name'] = _('Branch Details');
	$Parameter[0]['description'] = _('A set of key/value pairs where the key must be identical to the name of the field to be updated. ')
			._('The field names can be found ').'<a href="Z_DescribeTable.php?table=custbranch">'._('here ').'</a>'
			._('and are case sensitive. ')._('The values should be of the correct type, and the api will check them before updating the database. ')
			._('It is not necessary to include all the fields in this parameter, the database default value will be used if the field is not given.')
			.'<p>'._('The branchcode/debtorno combination must already exist in the weberp database.');
	$Parameter[1]['name'] = _('User name');
	$Parameter[1]['description'] = _('A valid weberp username. This user should have security access  to this data.');
	$Parameter[2]['name'] = _('User password');
	$Parameter[2]['description'] = _('The weberp password associoated with this user name. ');
	$ReturnValue[0] = _('This function returns an array of integers. ').
		_('If the first element is zero then the function was successful. ').
		_('Otherwise an array of error codes is returned and no insertion takes place. ');

	$doc = '<tr><td><b><u>'._('Description').'</u></b></td></tr><tr><td></td><td colspan=2>' .$Description.'</td></tr>
			<tr><td valign="top"><b><u>'._('Parameters').'</u></b></td>';
	for ($i=0; $i<sizeof($Parameter); $i++) {
		$doc .= '<tr><td valign="top">'.$Parameter[$i]['name'].'</td><td>'.
			$Parameter[$i]['description'].'</td></tr>';
	}
	$doc .= '<tr><td><b><u>'._('Return Value').'</td></tr>';
	for ($i=0; $i<sizeof($ReturnValue); $i++) {
		$doc .= '<tr><td></td><td valign="top">'.$ReturnValue[$i].'</td></tr>';
	}
	$doc .= '</table>';

	$ModifyBranch_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$ModifyBranch_doc = $doc;

	function xmlrpc_ModifyBranch($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(ModifyBranch(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	unset($Parameter);
	unset($ReturnValue);
	$Description = _('This function is used to retrieve the details of a customer branch from the webERP database.');
	$Parameter[0]['name'] = _('Debtor number');
	$Parameter[0]['description'] = _('This is a string value. It must be a valid debtor number that is already in the webERP database.');
	$Parameter[1]['name'] = _('Branch Code');
	$Parameter[1]['description'] = _('This is a string value. It must be a valid branch code that is already in the webERP database, and associated with the debtorno in Parameter[0]');
	$Parameter[2]['name'] = _('User name');
	$Parameter[2]['description'] = _('A valid weberp username. This user should have security access  to this data.');
	$Parameter[3]['name'] = _('User password');
	$Parameter[3]['description'] = _('The weberp password associoated with this user name. ');
	$ReturnValue[0] = _('If successful this function returns a set of key/value pairs containing the details of this branch. ').
		_('The key will be identical with field name from the custbranch table. All fields will be in the set regardless of whether the value was set.').'<p>'.
		_('Otherwise an array of error codes is returned. ');

	$doc = '<tr><td><b><u>'._('Description').'</u></b></td></tr><tr><td></td><td colspan=2>' .$Description.'</td></tr>
			<tr><td valign="top"><b><u>'._('Parameters').'</u></b></td>';
	for ($i=0; $i<sizeof($Parameter); $i++) {
		$doc .= '<tr><td valign="top">'.$Parameter[$i]['name'].'</td><td>'.
			$Parameter[$i]['description'].'</td></tr>';
	}
	$doc .= '<tr><td><b><u>'._('Return Value').'</td></tr>';
	for ($i=0; $i<sizeof($ReturnValue); $i++) {
		$doc .= '<tr><td></td><td valign="top">'.$ReturnValue[$i].'</td></tr>';
	}
	$doc .= '</table>';

	$GetCustomerBranch_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetCustomerBranch_doc = $doc;

	function xmlrpc_GetCustomerBranch($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetCustomerBranch($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 	$xmlrpcmsg->getParam(2)->scalarval(),
				 		$xmlrpcmsg->getParam(3)->scalarval())));
	}

	unset($Parameter);
	unset($ReturnValue);
	$Description = _('This function is used to retrieve the details of a customer from the webERP database.');
	$Parameter[0]['name'] = _('Debtor number');
	$Parameter[0]['description'] = _('This is a string value. It must be a valid debtor number that is already in the webERP database.');
	$Parameter[1]['name'] = _('User name');
	$Parameter[1]['description'] = _('A valid weberp username. This user should have security access  to this data.');
	$Parameter[2]['name'] = _('User password');
	$Parameter[2]['description'] = _('The weberp password associoated with this user name. ');
	$ReturnValue[0] = _('If successful this function returns a set of key/value pairs containing the details of this customer. ').
		_('The key will be identical with field name from the debtorsmaster table. All fields will be in the set regardless of whether the value was set.').'<p>'.
		_('Otherwise an array of error codes is returned. ');

	$doc = '<tr><td><b><u>'._('Description').'</u></b></td></tr><tr><td></td><td colspan=2>' .$Description.'</td></tr>
			<tr><td valign="top"><b><u>'._('Parameters').'</u></b></td>';
	for ($i=0; $i<sizeof($Parameter); $i++) {
		$doc .= '<tr><td valign="top">'.$Parameter[$i]['name'].'</td><td>'.
			$Parameter[$i]['description'].'</td></tr>';
	}
	$doc .= '<tr><td><b><u>'._('Return Value').'</td></tr>';
	for ($i=0; $i<sizeof($ReturnValue); $i++) {
		$doc .= '<tr><td></td><td valign="top">'.$ReturnValue[$i].'</td></tr>';
	}
	$doc .= '</table>';

	$GetCustomer_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetCustomer_doc = $doc;

	function xmlrpc_GetCustomer($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetCustomer($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$SearchCustomers_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$SearchCustomers_doc = 'This function returns an array containing the account numbers of those customers
			that meet the criteria given. Any field in debtorsmaster can be search on.';

	function xmlrpc_SearchCustomers($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(SearchCustomers($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval(),
				 			$xmlrpcmsg->getParam(3)->scalarval())));
	}

	$GetCurrencyList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetCurrencyList_doc = 'This function returns an array containing a list of all currencies setup on webERP';

	function xmlrpc_GetCurrencyList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetCurrencyList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetCurrencyDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetCurrencyDetails_doc = 'This function returns an associative array containing the details of the currency
			 sent as a parameter';

	function xmlrpc_GetCurrencyDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetCurrencyDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetSalesTypeList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetSalesTypeList_doc = 'This function returns an array containing a list of all sales types setup on webERP';

	function xmlrpc_GetSalesTypeList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetSalesTypeList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetSalesTypeDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetSalesTypeDetails_doc = 'This function returns an associative array containing the details of the sales type
			 sent as a parameter';

	function xmlrpc_GetSalesTypeDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetSalesTypeDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetHoldReasonList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetHoldReasonList_doc = 'This function returns an array containing a list of all hold reason codes setup on webERP';

	function xmlrpc_GetHoldReasonList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetHoldReasonList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetHoldReasonDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetHoldReasonDetails_doc = 'This function returns an associative array containing the details of the hold reason
			 sent as a parameter';

	function xmlrpc_GetHoldReasonDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetHoldReasonDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetPaymentTermsList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetPaymentTermsList_doc = 'This function returns an array containing a list of all payment terms setup on webERP';

	function xmlrpc_GetPaymentTermsList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetPaymentTermsList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetPaymentTermsDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetPaymentTermsDetails_doc = 'This function returns an associative array containing the details of the payment terms
			 sent as a parameter';

	function xmlrpc_GetPaymentTermsDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetPaymentTermsDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$InsertStockItem_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertStockItem_doc = 'This function takes an associative array containing the details of a stock item to
			to be inserted, where the keys of the array are the field names in the table stockmaster. ';

	function xmlrpc_InsertStockItem($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertStockItem(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$ModifyStockItem_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$ModifyStockItem_doc = 'This function takes an associative array containing the details of a stock item to
			to be updated, where the keys of the array are the field names in the table stockmaster. ';

	function xmlrpc_ModifyStockItem($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(ModifyStockItem(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetStockItem_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetStockItem_doc = 'This function returns an associative array containing the details of the item
			whose stockid is passed to it.';

	function xmlrpc_GetStockItem($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetStockItem($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$SearchStockItems_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$SearchStockItems_doc = 'This function returns an array containing the account numbers of those items
			that meet the criteria given. Any field in stockmaster can be search on.';

	function xmlrpc_SearchStockItems($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(SearchStockItems($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval(),
				 			$xmlrpcmsg->getParam(3)->scalarval())));
	}

	$GetStockBalance_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetStockBalance_doc = 'This function returns the quantity of stock on hand a the location given';

	function xmlrpc_GetStockBalance($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetStockBalance($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval(),
				 			$xmlrpcmsg->getParam(3)->scalarval())));
	}

	$GetAllocatedStock_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetAllocatedStock_doc = 'This function returns the quantity of stock allocated to sales orders';

	function xmlrpc_GetAllocatedStock($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetAllocatedStock($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 			$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetOrderedStock_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetOrderedStock_doc = 'This function returns the quantity of stock outstanding on purchase orders';

	function xmlrpc_GetOrderedStock($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetOrderedStock($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 			$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$SetStockPrice_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$SetStockPrice_doc = 'This function sets a price for a stock item/currency/pricelist combination';

	function xmlrpc_SetStockPrice($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(SetStockPrice($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval(),
				 			$xmlrpcmsg->getParam(3)->scalarval(),
				 				$xmlrpcmsg->getParam(4)->scalarval(),
				 					$xmlrpcmsg->getParam(5)->scalarval())));
	}

	$GetStockPrice_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetStockPrice_doc = 'This function retrieves the price for a stock item/currency/pricelist combination';

	function xmlrpc_GetStockPrice($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetStockPrice($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval(),
				 			$xmlrpcmsg->getParam(3)->scalarval(),
			 					$xmlrpcmsg->getParam(4)->scalarval())));
	}

	$InsertSalesInvoice_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertSalesInvoice_doc = 'This function inserts a sales invoice into webERP';

	function xmlrpc_InsertSalesInvoice($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertSalesInvoice(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$InsertSalesCredit_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertSalesCredit_doc = 'This function inserts a sales credit note into webERP';

	function xmlrpc_InsertSalesCredit($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertSalesCedit(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$InsertSalesOrderHeader_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertSalesOrderHeader_doc = 'This function inserts a sales order header into webERP';

	function xmlrpc_InsertSalesOrderHeader($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertSalesOrderHeader(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$ModifySalesOrderHeader_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$ModifySalesOrderHeader_doc = 'This function modifies a sales order header already in webERP';

	function xmlrpc_ModifySalesOrderHeader($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(ModifySalesOrderHeader(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$InsertSalesOrderLine_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertSalesOrderLine_doc = 'This function inserts a sales order line into webERP';

	function xmlrpc_InsertSalesOrderLine($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertSalesOrderLine(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$ModifySalesOrderLine_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$ModifySalesOrderLine_doc = 'This function modifies a sales order line in webERP';

	function xmlrpc_ModifySalesOrderLine($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(ModifySalesOrderLine(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$InsertGLAccount_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertGLAccount_doc = 'This function inserts a General ledger account code';

	function xmlrpc_InsertGLAccount($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertGLAccount(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$InsertGLAccountSection_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertGLAccountSection_doc = 'This function inserts a General ledger account section';

	function xmlrpc_InsertGLAccountSection($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertGLAccountSection(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}
	$InsertGLAccountGroup_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertGLAccountGroup_doc = 'This function inserts a General ledger account Group';

	function xmlrpc_InsertGLAccountGroup($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertGLAccountGroup(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetLocationList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetLocationList_doc = 'This function returns an array containing a list of all locations setup on webERP';

	function xmlrpc_GetLocationList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetLocationList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetLocationDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetLocationDetails_doc = 'This function returns an associative array containing the details of the Location
			 sent as a parameter';

	function xmlrpc_GetLocationDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetLocationDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetShipperList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetShipperList_doc = 'This function returns an array containing a list of all Shippers setup on webERP';

	function xmlrpc_GetShipperList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetShipperList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetShipperDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetShipperDetails_doc = 'This function returns an associative array containing the details of the Shipper
			 sent as a parameter';

	function xmlrpc_GetShipperDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetShipperDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetSalesAreasList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetSalesAreasList_doc = 'This function returns an array containing a list of all Sales areas setup on webERP';

	function xmlrpc_GetSalesAreasList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetSalesAreasList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetSalesAreaDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetSalesAreaDetails_doc = 'This function returns an associative array containing the details of the Sales area
			 sent as a parameter';

	function xmlrpc_GetSalesAreaDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetSalesAreaDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetSalesmanList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetSalesmanList_doc = 'This function returns an array containing a list of all Salesman codes setup on webERP';

	function xmlrpc_GetSalesmanList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetSalesmanList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetSalesmanDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetSalesmanDetails_doc = 'This function returns an associative array containing the details of the Salesman
			 sent as a parameter';

	function xmlrpc_GetSalesmanDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetSalesmanDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetTaxgroupList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetTaxgroupList_doc = 'This function returns an array containing a list of all Taxgroup codes setup on webERP';

	function xmlrpc_GetTaxgroupList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetTaxgroupList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetTaxgroupDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetTaxgroupDetails_doc = 'This function returns an associative array containing the details of the Taxgroup
			 sent as a parameter';

	function xmlrpc_GetTaxgroupDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetTaxgroupDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetCustomerTypeList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetCustomerTypeList_doc = 'This function returns an array containing a list of all Customer Type ids setup on webERP';

	function xmlrpc_GetCustomerTypeList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetCustomerTypeList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetCustomerTypeDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetCustomerTypeDetails_doc = 'This function returns an associative array containing the details of the Customer Type
			 sent as a parameter';

	function xmlrpc_GetCustomerTypeDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetCustomerTypeDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$InsertStockCategory_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertStockCategory_doc = 'This function takes an associative array containing the details of a category to
			to be inserted, where the keys of the array are the field names in the table stockcategory. ';

	function xmlrpc_InsertStockCategory($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertStockCategory(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$ModifyStockCategory_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$ModifyStockCategory_doc = 'This function takes an associative array containing the details of a category to
			to be Modified, where the keys of the array are the field names in the table stockcategory. ';

	function xmlrpc_ModifyStockCategory($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(ModifyStockCategory(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetStockCategory_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetStockCategory_doc = 'This function returns an associative array containing the details of the stock
			category whose id is passed to it.';

	function xmlrpc_GetStockCategory($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetStockCategory($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$SearchStockCategories_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$SearchStockCategories_doc = 'This function returns an array containing the stock categories
			that meet the criteria given. Any field in stockcategory can be search on.';

	function xmlrpc_SearchStockCategories($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(SearchStockCategories($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval(),
				 			$xmlrpcmsg->getParam(3)->scalarval())));
	}

	$GetGLAccountList_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$GetGLAccountList_doc = 'This function returns an array containing a list of all general ledger accounts setup on webERP';

	function xmlrpc_GetGLAccountList($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetGLAccountList($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval())));
	}

	$GetGLAccountDetails_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetGLAccountDetails_doc = 'This function returns an associative array containing the details of the GL Account
			 sent as a parameter';

	function xmlrpc_GetGLAccountDetails($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetGLAccountDetails($xmlrpcmsg->getParam(0)->scalarval(),
			$xmlrpcmsg->getParam(1)->scalarval(),
				$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$GetStockTaxRate_sig = array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$GetStockTaxRate_doc = 'This function returns the sales tax rate for the given stock code/tax authority';

	function xmlrpc_GetStockTaxRate($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(GetStockTaxRate($xmlrpcmsg->getParam(0)->scalarval(),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval(),
				 			$xmlrpcmsg->getParam(3)->scalarval())));
	}

	unset($Parameter);
	unset($ReturnValue);
	$Description = _('This function is used to insert a new supplier into the webERP database.');
	$Parameter[0]['name'] = _('Supplier Details');
	$Parameter[0]['description'] = _('A set of key/value pairs where the key must be identical to the name of the field to be updated. ')
			._('The field names can be found ').'<a href="Z_DescribeTable.php?table=suppliers">'._('here ').'</a>'
			._('and are case sensitive. ')._('The values should be of the correct type, and the api will check them before updating the database. ')
			._('It is not necessary to include all the fields in this parameter, the database default value will be used if the field is not given.');
	$Parameter[1]['name'] = _('User name');
	$Parameter[1]['description'] = _('A valid weberp username. This user should have security access  to this data.');
	$Parameter[2]['name'] = _('User password');
	$Parameter[2]['description'] = _('The weberp password associoated with this user name. ');
	$ReturnValue[0] = _('This function returns an array of integers. ').
		_('If the first element is zero then the function was successful. ').
		_('Otherwise an array of error codes is returned and no insertion takes place. ');

	$doc = '<tr><td><b><u>'._('Description').'</u></b></td><td colspan=2>' .$Description.'</td></tr>
			<tr><td valign="top"><b><u>'._('Parameters').'</u></b></td>';
	for ($i=0; $i<sizeof($Parameter); $i++) {
		$doc .= '<tr><td valign="top">'.$Parameter[$i]['name'].'</td><td>'.
			$Parameter[$i]['description'].'</td></tr>';
	}
	$doc .= '<tr><td><b><u>'._('Return Values').'</td></tr>';
	for ($i=0; $i<sizeof($ReturnValue); $i++) {
		$doc .= '<tr><td></td><td valign="top">'.$ReturnValue[$i].'</td></tr>';
	}
	$doc .= '</table>';
	$InsertSupplier_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$InsertSupplier_doc = $doc;

	function xmlrpc_InsertSupplier($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(InsertSupplier(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	unset($Parameter);
	unset($ReturnValue);
	$Description = _('This function is used to modify a supplier which is already setup in the webERP database.');
	$Parameter[0]['name'] = _('Supplier Details');
	$Parameter[0]['description'] = _('A set of key/value pairs where the key must be identical to the name of the field to be updated. ')
			._('The field names can be found ').'<a href="Z_DescribeTable.php?table=suppliers">'._('here ').'</a>'
			._('and are case sensitive. ')._('The values should be of the correct type, and the api will check them before updating the database. ')
			._('It is not necessary to include all the fields in this parameter, the database default value will be used if the field is not given.')
			.'<p>'._('The supplierid must already exist in the weberp database.');
	$Parameter[1]['name'] = _('User name');
	$Parameter[1]['description'] = _('A valid weberp username. This user should have security access  to this data.');
	$Parameter[2]['name'] = _('User password');
	$Parameter[2]['description'] = _('The weberp password associoated with this user name. ');
	$ReturnValue[0] = _('This function returns an array of integers. ').
		_('If the first element is zero then the function was successful. ').
		_('Otherwise an array of error codes is returned and no modification takes place. ');

	$doc = '<tr><td><b><u>'._('Description').'</u></b></td></tr><tr><td></td><td colspan=2>' .$Description.'</td></tr>
			<tr><td valign="top"><b><u>'._('Parameters').'</u></b></td>';
	for ($i=0; $i<sizeof($Parameter); $i++) {
		$doc .= '<tr><td valign="top">'.$Parameter[$i]['name'].'</td><td>'.
			$Parameter[$i]['description'].'</td></tr>';
	}
	$doc .= '<tr><td><b><u>'._('Return Value').'</td></tr>';
	for ($i=0; $i<sizeof($ReturnValue); $i++) {
		$doc .= '<tr><td></td><td valign="top">'.$ReturnValue[$i].'</td></tr>';
	}
	$doc .= '</table>';

	$ModifyCustomer_sig = array(array($xmlrpcStruct, $xmlrpcStruct, $xmlrpcString, $xmlrpcString));
	$ModifyCustomer_doc = $doc;

	function xmlrpc_ModifyCustomer($xmlrpcmsg) {
		return new xmlrpcresp(php_xmlrpc_encode(ModifyCustomer(php_xmlrpc_decode($xmlrpcmsg->getParam(0)),
				 $xmlrpcmsg->getParam(1)->scalarval(),
				 		$xmlrpcmsg->getParam(2)->scalarval())));
	}

	$s = new xmlrpc_server( array(
		"weberp.xmlrpc_InsertCustomer" => array(
			"function" => "xmlrpc_InsertCustomer",
			"signature" => $InsertCustomer_sig,
			"docstring" => $InsertCustomer_doc),
		"weberp.xmlrpc_ModifyCustomer" => array(
			"function" => "xmlrpc_ModifyCustomer",
			"signature" => $ModifyCustomer_sig,
			"docstring" => $ModifyCustomer_doc),
		"weberp.xmlrpc_GetCustomer" => array(
			"function" => "xmlrpc_GetCustomer",
			"signature" => $GetCustomer_sig,
			"docstring" => $GetCustomer_doc),
		"weberp.xmlrpc_SearchCustomers" => array(
			"function" => "xmlrpc_SearchCustomers",
			"signature" => $SearchCustomers_sig,
			"docstring" => $SearchCustomers_doc),
		"weberp.xmlrpc_GetCurrencyList" => array(
			"function" => "xmlrpc_GetCurrencyList",
			"signature" => $GetCurrencyList_sig,
			"docstring" => $GetCurrencyList_doc),
		"weberp.xmlrpc_GetCurrencyDetails" => array(
			"function" => "xmlrpc_GetCurrencyDetails",
			"signature" => $GetCurrencyDetails_sig,
			"docstring" => $GetCurrencyDetails_doc),
		"weberp.xmlrpc_GetSalesTypeList" => array(
			"function" => "xmlrpc_GetSalesTypeList",
			"signature" => $GetSalesTypeList_sig,
			"docstring" => $GetSalesTypeList_doc),
		"weberp.xmlrpc_GetSalesTypeDetails" => array(
			"function" => "xmlrpc_GetSalesTypeDetails",
			"signature" => $GetSalesTypeDetails_sig,
			"docstring" => $GetSalesTypeDetails_doc),
		"weberp.xmlrpc_GetHoldReasonList" => array(
			"function" => "xmlrpc_GetHoldReasonList",
			"signature" => $GetHoldReasonList_sig,
			"docstring" => $GetHoldReasonList_doc),
		"weberp.xmlrpc_GetHoldReasonDetails" => array(
			"function" => "xmlrpc_GetHoldReasonDetails",
			"signature" => $GetHoldReasonDetails_sig,
			"docstring" => $GetHoldReasonDetails_doc),
		"weberp.xmlrpc_GetPaymentTermsList" => array(
			"function" => "xmlrpc_GetPaymentTermsList",
			"signature" => $GetPaymentTermsList_sig,
			"docstring" => $GetPaymentTermsList_doc),
		"weberp.xmlrpc_GetPaymentTermsDetails" => array(
			"function" => "xmlrpc_GetPaymentTermsDetails",
			"signature" => $GetPaymentTermsDetails_sig,
			"docstring" => $GetPaymentTermsDetails_doc),
		"weberp.xmlrpc_InsertStockItem" => array(
			"function" => "xmlrpc_InsertStockItem",
			"signature" => $InsertStockItem_sig,
			"docstring" => $InsertStockItem_doc),
		"weberp.xmlrpc_ModifyStockItem" => array(
			"function" => "xmlrpc_ModifyStockItem",
			"signature" => $ModifyStockItem_sig,
			"docstring" => $ModifyStockItem_doc),
		"weberp.xmlrpc_GetStockItem" => array(
			"function" => "xmlrpc_GetStockItem",
			"signature" => $GetStockItem_sig,
			"docstring" => $GetStockItem_doc),
		"weberp.xmlrpc_SearchStockItems" => array(
			"function" => "xmlrpc_SearchStockItems",
			"signature" => $SearchStockItems_sig,
			"docstring" => $SearchStockItems_doc),
		"weberp.xmlrpc_GetStockBalance" => array(
			"function" => "xmlrpc_GetStockBalance",
			"signature" => $GetStockBalance_sig,
			"docstring" => $GetStockBalance_doc),
		"weberp.xmlrpc_GetAllocatedStock" => array(
			"function" => "xmlrpc_GetAllocatedStock",
			"signature" => $GetAllocatedStock_sig,
			"docstring" => $GetAllocatedStock_doc),
		"weberp.xmlrpc_GetOrderedStock" => array(
			"function" => "xmlrpc_GetOrderedStock",
			"signature" => $GetOrderedStock_sig,
			"docstring" => $GetOrderedStock_doc),
		"weberp.xmlrpc_SetStockPrice" => array(
			"function" => "xmlrpc_SetStockPrice",
			"signature" => $SetStockPrice_sig,
			"docstring" => $SetStockPrice_doc),
		"weberp.xmlrpc_GetStockPrice" => array(
			"function" => "xmlrpc_GetStockPrice",
			"signature" => $GetStockPrice_sig,
			"docstring" => $GetStockPrice_doc),
		"weberp.xmlrpc_InsertSalesInvoice" => array(
			"function" => "xmlrpc_InsertSalesInvoice",
			"signature" => $InsertSalesInvoice_sig,
			"docstring" => $InsertSalesInvoice_doc),
		"weberp.xmlrpc_InsertSalesCredit" => array(
			"function" => "xmlrpc_InsertSalesCredit",
			"signature" => $InsertSalesCredit_sig,
			"docstring" => $InsertSalesCredit_doc),
		"weberp.xmlrpc_InsertBranch" => array(
			"function" => "xmlrpc_InsertBranch",
			"signature" => $InsertBranch_sig,
			"docstring" => $InsertBranch_doc),
		"weberp.xmlrpc_ModifyBranch" => array(
			"function" => "xmlrpc_ModifyBranch",
			"signature" => $ModifyBranch_sig,
			"docstring" => $ModifyBranch_doc),
		"weberp.xmlrpc_GetCustomerBranch" => array(
			"function" => "xmlrpc_GetCustomerBranch",
			"signature" => $GetCustomerBranch_sig,
			"docstring" => $GetCustomerBranch_doc),
		"weberp.xmlrpc_InsertSalesOrderHeader" => array(
			"function" => "xmlrpc_InsertSalesOrderHeader",
			"signature" => $InsertSalesOrderHeader_sig,
			"docstring" => $InsertSalesOrderHeader_doc),
		"weberp.xmlrpc_ModifySalesOrderHeader" => array(
			"function" => "xmlrpc_ModifySalesOrderHeader",
			"signature" => $ModifySalesOrderHeader_sig,
			"docstring" => $ModifySalesOrderHeader_doc),
		"weberp.xmlrpc_InsertSalesOrderLine" => array(
			"function" => "xmlrpc_InsertSalesOrderLine",
			"signature" => $InsertSalesOrderLine_sig,
			"docstring" => $InsertSalesOrderLine_doc),
		"weberp.xmlrpc_ModifySalesOrderLine" => array(
			"function" => "xmlrpc_ModifySalesOrderLine",
			"signature" => $ModifySalesOrderLine_sig,
			"docstring" => $ModifySalesOrderLine_doc),
		"weberp.xmlrpc_InsertGLAccount" => array(
			"function" => "xmlrpc_InsertGLAccount",
			"signature" => $InsertGLAccount_sig,
			"docstring" => $InsertGLAccount_doc),
		"weberp.xmlrpc_InsertGLAccountSection" => array(
			"function" => "xmlrpc_InsertGLAccountSection",
			"signature" => $InsertGLAccountSection_sig,
			"docstring" => $InsertGLAccountSection_doc),
		"weberp.xmlrpc_InsertGLAccountGroup" => array(
			"function" => "xmlrpc_InsertGLAccountGroup",
			"signature" => $InsertGLAccountGroup_sig,
			"docstring" => $InsertGLAccountGroup_doc),
		"weberp.xmlrpc_GetLocationList" => array(
			"function" => "xmlrpc_GetLocationList",
			"signature" => $GetLocationList_sig,
			"docstring" => $GetLocationList_doc),
		"weberp.xmlrpc_GetLocationDetails" => array(
			"function" => "xmlrpc_GetLocationDetails",
			"signature" => $GetLocationDetails_sig,
			"docstring" => $GetLocationDetails_doc),
		"weberp.xmlrpc_GetShipperList" => array(
			"function" => "xmlrpc_GetShipperList",
			"signature" => $GetShipperList_sig,
			"docstring" => $GetShipperList_doc),
		"weberp.xmlrpc_GetShipperDetails" => array(
			"function" => "xmlrpc_GetShipperDetails",
			"signature" => $GetShipperDetails_sig,
			"docstring" => $GetShipperDetails_doc),
		"weberp.xmlrpc_GetSalesAreasList" => array(
			"function" => "xmlrpc_GetSalesAreasList",
			"signature" => $GetSalesAreasList_sig,
			"docstring" => $GetSalesAreasList_doc),
		"weberp.xmlrpc_GetSalesAreaDetails" => array(
			"function" => "xmlrpc_GetSalesAreaDetails",
			"signature" => $GetSalesAreaDetails_sig,
			"docstring" => $GetSalesAreaDetails_doc),
		"weberp.xmlrpc_GetSalesmanList" => array(
			"function" => "xmlrpc_GetSalesmanList",
			"signature" => $GetSalesmanList_sig,
			"docstring" => $GetSalesmanList_doc),
		"weberp.xmlrpc_GetSalesmanDetails" => array(
			"function" => "xmlrpc_GetSalesmanDetails",
			"signature" => $GetSalesmanDetails_sig,
			"docstring" => $GetSalesmanDetails_doc),
		"weberp.xmlrpc_GetTaxgroupList" => array(
			"function" => "xmlrpc_GetTaxgroupList",
			"signature" => $GetTaxgroupList_sig,
			"docstring" => $GetTaxgroupList_doc),
		"weberp.xmlrpc_GetTaxgroupDetails" => array(
			"function" => "xmlrpc_GetTaxgroupDetails",
			"signature" => $GetTaxgroupDetails_sig,
			"docstring" => $GetTaxgroupDetails_doc),
		"weberp.xmlrpc_GetCustomerTypeList" => array(
			"function" => "xmlrpc_GetCustomerTypeList",
			"signature" => $GetCustomerTypeList_sig,
			"docstring" => $GetCustomerTypeList_doc),
		"weberp.xmlrpc_GetCustomerTypeDetails" => array(
			"function" => "xmlrpc_GetCustomerTypeDetails",
			"signature" => $GetCustomerTypeDetails_sig,
			"docstring" => $GetCustomerTypeDetails_doc),
		"weberp.xmlrpc_InsertStockCategory" => array(
			"function" => "xmlrpc_InsertStockCategory",
			"signature" => $InsertStockCategory_sig,
			"docstring" => $InsertStockCategory_doc),
		"weberp.xmlrpc_ModifyStockCategory" => array(
			"function" => "xmlrpc_ModifyStockCategory",
			"signature" => $ModifyStockCategory_sig,
			"docstring" => $ModifyStockCategory_doc),
		"weberp.xmlrpc_GetStockCategory" => array(
			"function" => "xmlrpc_GetStockCategory",
			"signature" => $GetStockCategory_sig,
			"docstring" => $GetStockCategory_doc),
		"weberp.xmlrpc_SearchStockCategories" => array(
			"function" => "xmlrpc_SearchStockCategories",
			"signature" => $SearchStockCategories_sig,
			"docstring" => $SearchStockCategories_doc),
		"weberp.xmlrpc_GetGLAccountList" => array(
			"function" => "xmlrpc_GetGLAccountList",
			"signature" => $GetGLAccountList_sig,
			"docstring" => $GetGLAccountList_doc),
		"weberp.xmlrpc_GetGLAccountDetails" => array(
			"function" => "xmlrpc_GetGLAccountDetails",
			"signature" => $GetGLAccountDetails_sig,
			"docstring" => $GetGLAccountDetails_doc),
		"weberp.xmlrpc_GetStockTaxRate" => array(
			"function" => "xmlrpc_GetStockTaxRate",
			"signature" => $GetStockTaxRate_sig,
			"docstring" => $GetStockTaxRate_doc),
		"weberp.xmlrpc_InsertSupplier" => array(
			"function" => "xmlrpc_InsertSupplier",
			"signature" => $InsertSupplier_sig,
			"docstring" => $InsertSupplier_doc),
		"weberp.xmlrpc_ModifySupplier" => array(
			"function" => "xmlrpc_ModifySupplier",
			"signature" => $ModifySupplier_sig,
			"docstring" => $ModifySupplier_doc),
		)
	);

?>