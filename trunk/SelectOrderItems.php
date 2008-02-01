<?php
/* $Revision: 1.62 $ */

include('includes/DefineCartClass.php');
$PageSecurity = 1;
/* Session started in session.inc for password checking and authorisation level check
config.php is in turn included in session.inc*/

include('includes/session.inc');

if (isset($_GET['ModifyOrderNumber'])) {
	$title = _('Modifying Order') . ' ' . $_GET['ModifyOrderNumber'];
} else {
	$title = _('Select Order Items');
}

include('includes/header.inc');
include('includes/GetPrice.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_POST['QuickEntry'])){
   unset($_POST['PartSearch']);
}

if ($_POST['order_items']){
	foreach($_POST['itm'] as $key => $value)
	{
		$NewItem_array[$key] = trim($value);
	}	
	
}


if (isset($_GET['NewItem'])){
	
	
	$NewItem = trim($_GET['NewItem']);
}


if (isset($_GET['NewOrder'])){
  /*New order entry - clear any existing order details from the Items object and initiate a newy*/
	 if (isset($_SESSION['Items'])){
		unset ($_SESSION['Items']->LineItems);
		$_SESSION['Items']->ItemsOrdered=0;
		unset ($_SESSION['Items']);
	}

	$_SESSION['ExistingOrder']=0;
	$_SESSION['Items'] = new cart;

	if (count($_SESSION['AllowedPageSecurityTokens'])==1){ //its a customer logon
		$_SESSION['Items']->DebtorNo=$_SESSION['CustomerID'];
		$_SESSION['RequireCustomerSelection']=0;
	} else {
		$_SESSION['Items']->DebtorNo='';
		$_SESSION['RequireCustomerSelection']=1;
	}

}

echo '<A HREF="'. $rootpath . '/SelectSalesOrder.php?' . SID . '">'. _('Back to Sales Orders'). '</A><BR>';

if (isset($_GET['ModifyOrderNumber'])
	AND $_GET['ModifyOrderNumber']!=''){

/* The delivery check screen is where the details of the order are either updated or inserted depending on the value of ExistingOrder */

	if (isset($_SESSION['Items'])){
		unset ($_SESSION['Items']->LineItems);
		unset ($_SESSION['Items']);
	}
	$_SESSION['ExistingOrder']=$_GET['ModifyOrderNumber'];
	$_SESSION['RequireCustomerSelection'] = 0;
	$_SESSION['Items'] = new cart;

/*read in all the guff from the selected order into the Items cart  */

	if ($_SESSION['vtiger_integration']==1){
		$OrderHeaderSQL = 'SELECT salesorders.debtorno,
				debtorsmaster.name,
				salesorders.branchcode,
				salesorders.vtiger_accountid,
				salesorders.customerref,
				salesorders.comments,
				salesorders.orddate,
				salesorders.ordertype,
				salestypes.sales_type,
				salesorders.shipvia,
				salesorders.deliverto,
				salesorders.deladd1,
				salesorders.deladd2,
				salesorders.deladd3,
				salesorders.deladd4,
				salesorders.deladd5,
				salesorders.deladd6,
				salesorders.contactphone,
				salesorders.contactemail,
				salesorders.freightcost,
				salesorders.deliverydate,
				debtorsmaster.currcode,
				salesorders.fromstkloc,
				salesorders.printedpackingslip,
				salesorders.datepackingslipprinted,
				salesorders.quotation,
				salesorders.deliverblind,
				debtorsmaster.customerpoline,
				custbranch.estdeliverydays
			FROM salesorders,
				debtorsmaster,
				salestypes,
				custbranch
			WHERE salesorders.ordertype=salestypes.typeabbrev
			AND salesorders.debtorno = debtorsmaster.debtorno
			AND salesorders.debtorno = custbranch.debtorno
			AND salesorders.branchcode = custbranch.branchcode
			AND salesorders.orderno = ' . $_GET['ModifyOrderNumber'];

	} else {
		$OrderHeaderSQL = 'SELECT salesorders.debtorno,
				debtorsmaster.name,
				salesorders.branchcode,
				salesorders.customerref,
				salesorders.comments,
				salesorders.orddate,
				salesorders.ordertype,
				salestypes.sales_type,
				salesorders.shipvia,
				salesorders.deliverto,
				salesorders.deladd1,
				salesorders.deladd2,
				salesorders.deladd3,
				salesorders.deladd4,
				salesorders.deladd5,
				salesorders.deladd6,
				salesorders.contactphone,
				salesorders.contactemail,
				salesorders.freightcost,
				salesorders.deliverydate,
				debtorsmaster.currcode,
				salesorders.fromstkloc,
				salesorders.printedpackingslip,
				salesorders.datepackingslipprinted,
				salesorders.quotation,
				salesorders.deliverblind,
				debtorsmaster.customerpoline,
				custbranch.estdeliverydays
			FROM salesorders,
				debtorsmaster,
				salestypes,
				custbranch
			WHERE salesorders.ordertype=salestypes.typeabbrev
			AND salesorders.debtorno = debtorsmaster.debtorno
			AND salesorders.debtorno = custbranch.debtorno
			AND salesorders.branchcode = custbranch.branchcode
			AND salesorders.orderno = ' . $_GET['ModifyOrderNumber'];
	}

	$ErrMsg =  _('The order cannot be retrieved because');
	$GetOrdHdrResult = DB_query($OrderHeaderSQL,$db,$ErrMsg);

	if (DB_num_rows($GetOrdHdrResult)==1) {

		$myrow = DB_fetch_array($GetOrdHdrResult);
		$_SESSION['Items']->OrderNo = $_GET['ModifyOrderNumber'];
		$_SESSION['Items']->DebtorNo = $myrow['debtorno'];
/*CustomerID defined in header.inc */
		$_SESSION['Items']->Branch = $myrow['branchcode'];
		$_SESSION['Items']->CustomerName = $myrow['name'];
		$_SESSION['Items']->CustRef = $myrow['customerref'];
		$_SESSION['Items']->Comments = $myrow['comments'];

		$_SESSION['Items']->DefaultSalesType =$myrow['ordertype'];
		$_SESSION['Items']->SalesTypeName =$myrow['sales_type'];
		$_SESSION['Items']->DefaultCurrency = $myrow['currcode'];
		$_SESSION['Items']->ShipVia = $myrow['shipvia'];
		$BestShipper = $myrow['shipvia'];
		$_SESSION['Items']->DeliverTo = $myrow['deliverto'];
		$_SESSION['Items']->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
		$_SESSION['Items']->DelAdd1 = $myrow['deladd1'];
		$_SESSION['Items']->DelAdd2 = $myrow['deladd2'];
		$_SESSION['Items']->DelAdd3 = $myrow['deladd3'];
		$_SESSION['Items']->DelAdd4 = $myrow['deladd4'];
		$_SESSION['Items']->DelAdd5 = $myrow['deladd5'];
		$_SESSION['Items']->DelAdd6 = $myrow['deladd6'];
		$_SESSION['Items']->PhoneNo = $myrow['contactphone'];
		$_SESSION['Items']->Email = $myrow['contactemail'];
		$_SESSION['Items']->Location = $myrow['fromstkloc'];
		$_SESSION['Items']->Quotation = $myrow['quotation'];
		$_SESSION['Items']->FreightCost = $myrow['freightcost'];
		$_SESSION['Items']->Orig_OrderDate = $myrow['orddate'];
		$_SESSION['PrintedPackingSlip'] = $myrow['printedpackingslip'];
		$_SESSION['DatePackingSlipPrinted'] = $myrow['datepackingslipprinted'];
		$_SESSION['Items']->DeliverBlind = $myrow['deliverblind'];
		$_SESSION['Items']->DefaultPOLine = $myrow['customerpoline'];
		$_SESSION['Items']->DeliveryDays = $myrow['estdeliverydays'];

/*need to look up customer name from debtors master then populate the line items array with the sales order details records */

		if ($_SESSION['vtiger_integration']==1){

			$LineItemsSQL = "SELECT salesorderdetails.orderlineno,
				salesorderdetails.stkcode,
				stockmaster.vtiger_productid,
				stockmaster.description,
				stockmaster.volume,
				stockmaster.kgs,
				stockmaster.units,
				salesorderdetails.unitprice,
				salesorderdetails.quantity,
				salesorderdetails.discountpercent,
				salesorderdetails.actualdispatchdate,
				salesorderdetails.qtyinvoiced,
				salesorderdetails.narrative,
				salesorderdetails.itemdue,
				salesorderdetails.poline,
				locstock.quantity as qohatloc,
				stockmaster.mbflag,
				stockmaster.discountcategory,
				stockmaster.decimalplaces,
				salesorderdetails.completed=0
				FROM salesorderdetails INNER JOIN stockmaster
				ON salesorderdetails.stkcode = stockmaster.stockid
				INNER JOIN locstock ON locstock.stockid = stockmaster.stockid
				WHERE  locstock.loccode = '" . $myrow['fromstkloc'] . "'
				AND salesorderdetails.orderno =" . $_GET['ModifyOrderNumber'] . "
				ORDER BY salesorderdetails.orderlineno";
		} else {

			$LineItemsSQL = "SELECT salesorderdetails.orderlineno,
				salesorderdetails.stkcode,
				stockmaster.description,
				stockmaster.volume,
				stockmaster.kgs,
				stockmaster.units,
				salesorderdetails.unitprice,
				salesorderdetails.quantity,
				salesorderdetails.discountpercent,
				salesorderdetails.actualdispatchdate,
				salesorderdetails.qtyinvoiced,
				salesorderdetails.narrative,
				salesorderdetails.itemdue,
				salesorderdetails.poline,
				locstock.quantity as qohatloc,
				stockmaster.mbflag,
				stockmaster.discountcategory,
				stockmaster.decimalplaces,
				salesorderdetails.completed
				FROM salesorderdetails INNER JOIN stockmaster
				ON salesorderdetails.stkcode = stockmaster.stockid
				INNER JOIN locstock ON locstock.stockid = stockmaster.stockid
				WHERE  locstock.loccode = '" . $myrow['fromstkloc'] . "'
				AND salesorderdetails.orderno =" . $_GET['ModifyOrderNumber'] . "
				ORDER BY salesorderdetails.orderlineno";
		}

		$ErrMsg = _('The line items of the order cannot be retrieved because');
		$LineItemsResult = db_query($LineItemsSQL,$db,$ErrMsg);
		if (db_num_rows($LineItemsResult)>0) {

			while ($myrow=db_fetch_array($LineItemsResult)) {
					if ($myrow['completed']==0){
						$_SESSION['Items']->add_to_cart($myrow['stkcode'],
								$myrow['quantity'],
								$myrow['description'],
								$myrow['unitprice'],
								$myrow['discountpercent'],
								$myrow['units'],
								$myrow['volume'],
								$myrow['kgs'],
								$myrow['qohatloc'],
								$myrow['mbflag'],
								$myrow['actualdispatchdate'],
								$myrow['qtyinvoiced'],
								$myrow['discountcategory'],
								0,	/*Controlled*/
								0,	/*Serialised */
								$myrow['decimalplaces'],
								$myrow['narrative'],
								'No', /* Update DB */
								$myrow['orderlineno'],
								0,
								'',
								$myrow['itemdue'],
								$myrow['poline']
								);
				/*Just populating with existing order - no DBUpdates */
					}
					$LastLineNo = $myrow['orderlineno'];
			} /* line items from sales order details */
			 $_SESSION['Items']->LineCounter = $LastLineNo+1;
		} //end of checks on returned data set
	}
}
		
$locsql = "SELECT locationname
		   FROM locations
		   WHERE loccode='" . $_SESSION['Items']->Location ."'";
$locresult = db_query($locsql, $db);
$locrow = db_fetch_array($locresult);
$location = $locrow[0];

if (!isset($_SESSION['Items'])){
	/* It must be a new order being created $_SESSION['Items'] would be set up from the order
	modification code above if a modification to an existing order. Also $ExistingOrder would be
	set to 1. The delivery check screen is where the details of the order are either updated or
	inserted depending on the value of ExistingOrder */

	$_SESSION['ExistingOrder']=0;
	$_SESSION['Items'] = new cart;
	$_SESSION['PrintedPackingSlip'] =0; /*Of course cos the order aint even started !!*/

	if (in_array(2,$_SESSION['AllowedPageSecurityTokens']) AND ($_SESSION['Items']->DebtorNo=='' OR !isset($_SESSION['Items']->DebtorNo))){

	/* need to select a customer for the first time out if authorisation allows it and if a customer
	 has been selected for the order or not the session variable CustomerID holds the customer code
	 already as determined from user id /password entry  */
		$_SESSION['RequireCustomerSelection'] = 1;
	} else {
		$_SESSION['RequireCustomerSelection'] = 0;
	}
}

if (isset($_POST['ChangeCustomer']) AND $_POST['ChangeCustomer']!=''){

	if ($_SESSION['Items']->Any_Already_Delivered()==0){
		$_SESSION['RequireCustomerSelection']=1;
	} else {
		prnMsg(_('The customer the order is for cannot be modified once some of the order has been invoiced'),'warn');
	}
}

$msg='';

if (isset($_POST['SearchCust']) AND $_SESSION['RequireCustomerSelection']==1 AND in_array(2,$_SESSION['AllowedPageSecurityTokens'])){

	If (($_POST['CustKeywords']!='') AND (($_POST['CustCode']!='') OR ($_POST['CustPhone']!=''))) {
		$msg= _('Customer name keywords have been used in preference to the customer code or phone entered');
	}
	If (($_POST['CustCode']!='') AND ($_POST['CustPhone']!='')) {
		$msg=_('Customer code has been used in preference to the customer phone entered') . '.';
	}
	If (($_POST['CustKeywords']=='') AND ($_POST['CustCode']=='')  AND ($_POST['CustPhone']=='')) {
		$msg=_('At least one Customer Name keyword OR an extract of a Customer Code or phone number must be entered for the search');
	} else {
		If (strlen($_POST['CustKeywords'])>0) {
		//insert wildcard characters in spaces
			$_POST['CustKeywords'] = strtoupper(trim($_POST['CustKeywords']));
			$i=0;
			$SearchString = '%';
			while (strpos($_POST['CustKeywords'], ' ', $i)) {
				$wrdlen=strpos($_POST['CustKeywords'],' ',$i) - $i;
				$SearchString=$SearchString . substr($_POST['CustKeywords'],$i,$wrdlen) . '%';
				$i=strpos($_POST['CustKeywords'],' ',$i) +1;
			}
			$SearchString = $SearchString. substr($_POST['CustKeywords'],$i).'%';

			$SQL = "SELECT custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
					custbranch.branchcode,
					custbranch.debtorno
				FROM custbranch
				WHERE custbranch.brname " . LIKE . " '$SearchString'
				AND custbranch.disabletrans=0
				ORDER BY custbranch.debtorno, custbranch.branchcode";

		} elseif (strlen($_POST['CustCode'])>0){

			$_POST['CustCode'] = strtoupper(trim($_POST['CustCode']));

			$SQL = "SELECT custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
					custbranch.branchcode,
					custbranch.debtorno
				FROM custbranch
				WHERE custbranch.debtorno " . LIKE . " '%" . $_POST['CustCode'] . "%' OR custbranch.branchcode " . LIKE . " '%" . $_POST['CustCode'] . "%'
				AND custbranch.disabletrans=0
				ORDER BY custbranch.debtorno";
		} elseif (strlen($_POST['CustPhone'])>0){
			$SQL = "SELECT custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
					custbranch.branchcode,
					custbranch.debtorno
				FROM custbranch
				WHERE custbranch.phoneno " . LIKE . " '%" . $_POST['CustPhone'] . "%'
				AND custbranch.disabletrans=0
				ORDER BY custbranch.debtorno";
		}

		$ErrMsg = _('The searched customer records requested cannot be retrieved because');
		$result_CustSelect = DB_query($SQL,$db,$ErrMsg);

		if (DB_num_rows($result_CustSelect)==1){
			$myrow=DB_fetch_array($result_CustSelect);
			$_POST['Select'] = $myrow['debtorno'] . ' - ' . $myrow['branchcode'];
		} elseif (DB_num_rows($result_CustSelect)==0){
			prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
		}
	} /*one of keywords or custcode was more than a zero length string */
} /*end of if search for customer codes/names */


// will only be true if page called from customer selection form or set because only one customer
// record returned from a search so parse the $Select string into customer code and branch code */
if (isset($_POST['Select']) AND $_POST['Select']!='') {

	$_SESSION['Items']->Branch = substr($_POST['Select'],strpos($_POST['Select'],' - ')+3);

	$_POST['Select'] = substr($_POST['Select'],0,strpos($_POST['Select'],' - '));

	// Now check to ensure this account is not on hold */
	$sql = "SELECT debtorsmaster.name,
			holdreasons.dissallowinvoices,
			debtorsmaster.salestype,
			salestypes.sales_type,
			debtorsmaster.currcode,
			debtorsmaster.customerpoline
		FROM debtorsmaster,
			holdreasons,
			salestypes
		WHERE debtorsmaster.salestype=salestypes.typeabbrev
		AND debtorsmaster.holdreason=holdreasons.reasoncode
		AND debtorsmaster.debtorno = '" . $_POST['Select'] . "'";

	$ErrMsg = _('The details of the customer selected') . ': ' .  $_POST['Select'] . ' ' . _('cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the customer details and failed was') . ':';
	$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

	$myrow = DB_fetch_row($result);
	if ($myrow[1] != 1){
		if ($myrow[1]==2){
			prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently flagged as an account that needs to be watched. Please contact the credit control personnel to discuss'),'warn');
		}

		$_SESSION['Items']->DebtorNo=$_POST['Select'];
		$_SESSION['RequireCustomerSelection']=0;
		$_SESSION['Items']->CustomerName = $myrow[0];

# the sales type determines the price list to be used by default the customer of the user is
# defaulted from the entry of the userid and password.

		$_SESSION['Items']->DefaultSalesType = $myrow[2];
		$_SESSION['Items']->SalesTypeName = $myrow[3];
		$_SESSION['Items']->DefaultCurrency = $myrow[4];
		$_SESSION['Items']->DefaultPOLine = $myrow[5];



# the branch was also selected from the customer selection so default the delivery details from the customer branches table CustBranch. The order process will ask for branch details later anyway

		$sql = "SELECT custbranch.brname,
				custbranch.braddress1,
				custbranch.braddress2,
				custbranch.braddress3,
				custbranch.braddress4,
				custbranch.braddress5,
				custbranch.braddress6,
				custbranch.phoneno,
				custbranch.email,
				custbranch.defaultlocation,
				custbranch.defaultshipvia,
				custbranch.deliverblind,
                custbranch.specialinstructions,
                custbranch.estdeliverydays
			FROM custbranch
			WHERE custbranch.branchcode='" . $_SESSION['Items']->Branch . "'
			AND custbranch.debtorno = '" . $_POST['Select'] . "'";

		$ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
		$DbgMsg = _('SQL used to retrieve the branch details was') . ':';
		$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

		if (DB_num_rows($result)==0){

			prnMsg(_('The branch details for branch code') . ': ' . $_SESSION['Items']->Branch . ' ' . _('against customer code') . ': ' . $_POST['Select'] . ' ' . _('could not be retrieved') . '. ' . _('Check the set up of the customer and branch'),'error');

			if ($debug==1){
				echo '<BR>' . _('The SQL that failed to get the branch details was') . ':<BR>' . $sql;
			}
			include('includes/footer.inc');
			exit;
		}

		$myrow = DB_fetch_row($result);
		$_SESSION['Items']->DeliverTo = $myrow[0];
		$_SESSION['Items']->DelAdd1 = $myrow[1];
		$_SESSION['Items']->DelAdd2 = $myrow[2];
		$_SESSION['Items']->DelAdd3 = $myrow[3];
		$_SESSION['Items']->DelAdd4 = $myrow[4];
		$_SESSION['Items']->DelAdd5 = $myrow[5];
		$_SESSION['Items']->DelAdd6 = $myrow[6];
		$_SESSION['Items']->PhoneNo = $myrow[7];
		$_SESSION['Items']->Email = $myrow[8];
		$_SESSION['Items']->Location = $myrow[9];
		$_SESSION['Items']->ShipVia = $myrow[10];
		$_SESSION['Items']->DeliverBlind = $myrow[11];
		$_SESSION['Items']->SpecialInstructions = $myrow[12];
		$_SESSION['Items']->DeliveryDays = $myrow[13];

		if ($_SESSION['Items']->SpecialInstructions)
		  prnMsg($_SESSION['Items']->SpecialInstructions,'warn');

		if ($_SESSION['CheckCreditLimits'] > 0){  /*Check credit limits is 1 for warn and 2 for prohibit sales */
			$_SESSION['Items']->CreditAvailable = GetCreditAvailable($_POST['Select'],$db);

			if ($_SESSION['CheckCreditLimits']==1 AND $_SESSION['Items']->CreditAvailable <=0){
				prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently at or over their credit limit'),'warn');
			} elseif ($_SESSION['CheckCreditLimits']==2 AND $_SESSION['Items']->CreditAvailable <=0){
				prnMsg(_('No more orders can be placed by') . ' ' . $myrow[0] . ' ' . _(' their account is currently at or over their credit limit'),'warn');
				include('includes/footer.inc');
				exit;
			}
		}

	} else {
		prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently on hold please contact the credit control personnel to discuss'),'warn');
	}

} elseif (!$_SESSION['Items']->DefaultSalesType OR $_SESSION['Items']->DefaultSalesType=='')	{

#Possible that the check to ensure this account is not on hold has not been done
#if the customer is placing own order, if this is the case then
#DefaultSalesType will not have been set as above

	$sql = "SELECT debtorsmaster.name,
			holdreasons.dissallowinvoices,
			debtorsmaster.salestype,
			debtorsmaster.currcode,
			debtorsmaster.customerpoline
		FROM debtorsmaster, holdreasons
		WHERE debtorsmaster.holdreason=holdreasons.reasoncode
		AND debtorsmaster.debtorno = '" . $_SESSION['Items']->DebtorNo . "'";

	$ErrMsg = _('The details for the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
	$DbgMsg = _('SQL used to retrieve the customer details was') . ':<BR>' . $sql;
	$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

	$myrow = DB_fetch_row($result);
	if ($myrow[1] == 0){

		$_SESSION['Items']->CustomerName = $myrow[0];

# the sales type determines the price list to be used by default the customer of the user is
# defaulted from the entry of the userid and password.

		$_SESSION['Items']->DefaultSalesType = $myrow[2];
		$_SESSION['Items']->DefaultCurrency = $myrow[3];
		$_SESSION['Items']->Branch = $_SESSION['UserBranch'];
		$_SESSION['Items']->DefaultPOLine = $myrow[4];


	// the branch would be set in the user data so default delivery details as necessary. However,
	// the order process will ask for branch details later anyway

		$sql = "SELECT custbranch.brname,
			custbranch.braddress1,
			custbranch.braddress2,
			custbranch.braddress3,
			custbranch.braddress4,
			custbranch.braddress5,
			custbranch.braddress6,
			custbranch.phoneno,
			custbranch.email,
			custbranch.defaultlocation,
			custbranch.deliverblind,
			custbranch.estdeliverydays
			FROM custbranch
			WHERE custbranch.branchcode='" . $_SESSION['Items']->Branch . "'
			AND custbranch.debtorno = '" . $_SESSION['Items']->DebtorNo . "'";

		$ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
		$DbgMsg = _('SQL used to retrieve the branch details was');
		$result =DB_query($sql,$db,$ErrMsg, $DbgMsg);

		$myrow = DB_fetch_row($result);
		$_SESSION['Items']->DeliverTo = $myrow[0];
		$_SESSION['Items']->DelAdd1 = $myrow[1];
		$_SESSION['Items']->DelAdd2 = $myrow[2];
		$_SESSION['Items']->DelAdd3 = $myrow[3];
		$_SESSION['Items']->DelAdd4 = $myrow[4];
		$_SESSION['Items']->DelAdd5 = $myrow[5];
		$_SESSION['Items']->DelAdd6 = $myrow[6];
		$_SESSION['Items']->PhoneNo = $myrow[7];
		$_SESSION['Items']->Email = $myrow[8];
		$_SESSION['Items']->Location = $myrow[9];
		$_SESSION['Items']->DeliverBlind = $myrow[10];
		$_SESSION['Items']->DeliveryDays = $myrow[11];

	} else {
		prnMsg(_('Sorry, your account has been put on hold for some reason, please contact the credit control personnel.'),'warn');
		include('includes/footer.inc');
		exit;
	}
}

if ($_SESSION['RequireCustomerSelection'] ==1
	OR !isset($_SESSION['Items']->DebtorNo)
	OR $_SESSION['Items']->DebtorNo=='' ) {
	?>

	<BR><BR><FONT SIZE=3><B><?php echo _('Customer Selection'); ?></B></FONT>

	<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' .SID; ?>" METHOD=POST>
	<B><?php echo '<BR>' . $msg; ?></B>
	<TABLE CELLPADDING=3 COLSPAN=4>
	<TR>
	<TD><FONT SIZE=1><?php echo _('name'); ?>:</FONT></TD>
	<TD><INPUT TYPE="Text" NAME="CustKeywords" SIZE=20	MAXLENGTH=25></TD>
	<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
	<TD><FONT SIZE=1><?php echo _('Part of the code'); ?>:</FONT></TD>
	<TD><INPUT TYPE="Text" NAME="CustCode" SIZE=15	MAXLENGTH=18></TD>
	<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
	<TD><FONT SIZE=1><?php echo _('Part of the phone'); ?>:</FONT></TD>
	<TD><INPUT TYPE="Text" NAME="CustPhone" SIZE=15	MAXLENGTH=18></TD>
	</TR>
	</TABLE>
	<CENTER><INPUT TYPE=SUBMIT NAME="SearchCust" VALUE="<?php echo _('Search Now'); ?>">
	<INPUT TYPE=SUBMIT ACTION=RESET VALUE="<?php echo _('Reset'); ?>"></CENTER>

	<script language='JavaScript' type='text/javascript'>
    	//<![CDATA[
            <!--
            document.forms[0].CustCode.select();
            document.forms[0].CustCode.focus();
            //-->
    	//]]>
	</script>
	<?php

	If (isset($result_CustSelect)) {

		echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';

		$TableHeader = '<TR>
				<TH>' . _('Code') . '</TH>
				<TH>' . _('Branch') . '</TH>
				<TH>' . _('Contact') . '</TH>
				<TH>' . _('Phone') . '</TH>
				<TH>' . _('Fax') . '</TH>
				</TR>';
		echo $TableHeader;

		$j = 1;
		$k = 0; //row counter to determine background colour

		while ($myrow=DB_fetch_array($result_CustSelect)) {

			if ($k==1){
				echo '<tr bgcolor="#CCCCCC">';
				$k=0;
			} else {
				echo '<tr bgcolor="#EEEEEE">';
				$k=1;
			}

			printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s - %s'</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				</tr>",
				$myrow['debtorno'],
				$myrow['branchcode'],
				$myrow['brname'],
				$myrow['contactname'],
				$myrow['phoneno'],
				$myrow['faxno']);

			$j++;
			If ($j == 11){
				$j=1;
				echo $TableHeader;
			}
//end of page full new headings if
		}
//end of while loop

		echo '</TABLE>';

	}//end if results to show

//end if RequireCustomerSelection
} else { //dont require customer selection
// everything below here only do if a customer is selected
 	if (isset($_POST['CancelOrder'])) {
		$OK_to_delete=1;	//assume this in the first instance

		if($_SESSION['ExistingOrder']!=0) { //need to check that not already dispatched

			$sql = "SELECT qtyinvoiced
				FROM salesorderdetails
				WHERE orderno=" . $_SESSION['ExistingOrder'] . "
				AND qtyinvoiced>0";

			$InvQties = DB_query($sql,$db);

			if (DB_num_rows($InvQties)>0){

				$OK_to_delete=0;

				prnMsg( _('There are lines on this order that have already been invoiced. Please delete only the lines on the order that are no longer required') . '<P>' . _('There is an option on confirming a dispatch/invoice to automatically cancel any balance on the order at the time of invoicing if you know the customer will not want the back order'),'warn');
			}
		}

		if ($OK_to_delete==1){
			if($_SESSION['ExistingOrder']!=0){

				$SQL = 'DELETE FROM salesorderdetails WHERE salesorderdetails.orderno =' . $_SESSION['ExistingOrder'];
				$ErrMsg =_('The order detail lines could not be deleted because');
				$DelResult=DB_query($SQL,$db,$ErrMsg);

				$SQL = 'DELETE FROM salesorders WHERE salesorders.orderno=' . $_SESSION['ExistingOrder'];
				$ErrMsg = _('The order header could not be deleted because');
				$DelResult=DB_query($SQL,$db,$ErrMsg);

				$_SESSION['ExistingOrder']=0;
			}

			unset($_SESSION['Items']->LineItems);
			$_SESSION['Items']->ItemsOrdered=0;
			unset($_SESSION['Items']);
			$_SESSION['Items'] = new cart;

			if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
				$_SESSION['RequireCustomerSelection'] = 1;
			} else {
				$_SESSION['RequireCustomerSelection'] = 0;
			}
			echo '<BR><BR>';
			prnMsg(_('This sales order has been cancelled as requested'),'success');
			include('includes/footer.inc');
			exit;
		}
	} else { /*Not cancelling the order */

		echo '<BR><BR><CENTER><FONT SIZE=4><B>';

		if ($_SESSION['Items']->Quotation==1){
			echo _('Quotation for') . ' ';
		} else {
			echo _('Order for') . ' ';
		}

		echo _('Customer No.') . ': ' . $_SESSION['Items']->DebtorNo;
		echo '&nbsp;&nbsp;' . _('Customer Name') . ' : ' . $_SESSION['Items']->CustomerName;
		echo '<BR>' . _('Deliver To') . ': ' . $_SESSION['Items']->DeliverTo;
		echo '&nbsp;&nbsp;' . _('From Location') . ': ' . $location;
		echo '<BR>' . _('Sales Type') . '/' . _('Price List') . ': ' . $_SESSION['Items']->SalesTypeName;
		echo '</B></FONT></CENTER>';
	}

	If (isset($_POST['Search'])){

		If ($_POST['Keywords'] AND $_POST['StockCode']) {
			$msg='<BR>' . _('Stock description keywords have been used in preference to the Stock code extract entered') . '.';
		}
		If (strlen($_POST['Keywords'])>0) {
			//insert wildcard characters in spaces
			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			$i=0;
			$SearchString = '%';
			while (strpos($_POST['Keywords'], ' ', $i)) {
				$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
				$i=strpos($_POST['Keywords'],' ',$i) +1;
			}
			$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster,
						stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.description " . LIKE . " '$SearchString'
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
			}

		} elseif (strlen($_POST['StockCode'])>0){

			$_POST['StockCode'] = strtoupper($_POST['StockCode']);
			$SearchString = '%' . $_POST['StockCode'] . '%';

			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
			}

		} else {
			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
			  }
		}

		$SQL = $SQL . ' LIMIT ' . $_SESSION['DisplayRecordsMax'];

		$ErrMsg = _('There is a problem selecting the part records to display because');
		$DbgMsg = _('The SQL used to get the part selection was');
		$SearchResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

		if (DB_num_rows($SearchResult)==0 ){
			prnMsg (_('There are no products available meeting the criteria specified'),'info');

			if ($debug==1){
				prnMsg(_('The SQL statement used was') . ':<BR>' . $SQL,'info');
			}
		}
		if (DB_num_rows($SearchResult)==1){
			$myrow=DB_fetch_array($SearchResult);
			$NewItem = $myrow['stockid'];
			DB_data_seek($SearchResult,0);
		}

	} //end of if search

#Always do the stuff below if not looking for a customerid

	echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

	/*Process Quick Entry */

	 If (isset($_POST['QuickEntry']) or isset($_POST['Recalculate'])){ // if enter is pressed on the quick entry screen, the default button may be Recalculate
	     /* get the item details from the database and hold them in the cart object */

	     /*Discount can only be set later on  -- after quick entry -- so default discount to 0 in the first place */
	     $Discount = 0;

	     $i=1;
	     do {
			$QuickEntryCode = 'part_' . $i;
			$QuickEntryQty = 'qty_' . $i;
			$QuickEntryPOLine = 'poline_' . $i;
			$QuickEntryItemDue = 'itemdue_' . $i;

			$i++;

			$NewItem = strtoupper($_POST[$QuickEntryCode]);
			$NewItemQty = $_POST[$QuickEntryQty];
			$NewItemDue = $_POST[$QuickEntryItemDue];
-			$NewPOLine = $_POST[$QuickEntryPOLine];

			if (strlen($NewItem)==0){
				unset($NewItem);
				break;    /* break out of the loop if nothing in the quick entry fields*/
			}

			if(!Is_Date($NewItemDue)) {
					prnMsg(_('An invalid date entry was made for ') . ' ' . $NewItem . ' ' . _('The date entry') . ' ' . $NewItemDue . ' ' . ('must be in the format') . ' ' . $_SESSION['DefaultDateFormat'],'warn');
				//Attempt to default the due date to something sensible?
				$NewItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
			}
			/*Now figure out if the item is a kit set - the field MBFlag='K'*/
			$sql = "SELECT stockmaster.mbflag
					FROM stockmaster
					WHERE stockmaster.stockid='". $NewItem ."'";

			$ErrMsg = _('Could not determine if the part being ordered was a kitset or not because');
			$KitResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);


			if (DB_num_rows($KitResult)==0){
				prnMsg( _('The item code') . ' ' . $NewItem . ' ' . _('could not be retrieved from the database and has not been added to the order'),'warn');
			} elseif ($myrow=DB_fetch_array($KitResult)){
				if ($myrow['mbflag']=='K'){	/*It is a kit set item */
					$sql = "SELECT bom.component,
							bom.quantity
							FROM bom
							WHERE bom.parent='" . $NewItem . "'
							AND bom.effectiveto > '" . Date("Y-m-d") . "'
							AND bom.effectiveafter < '" . Date('Y-m-d') . "'";

					$ErrMsg =  _('Could not retrieve kitset components from the database because') . ' ';
					$KitResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

					$ParentQty = $NewItemQty;
					while ($KitParts = DB_fetch_array($KitResult,$db)){
						$NewItem = $KitParts['component'];
						$NewItemQty = $KitParts['quantity'] * $ParentQty;
						include('includes/SelectOrderItems_IntoCart.inc');
					}

				} else { /*Its not a kit set item*/
					include('includes/SelectOrderItems_IntoCart.inc');
				}
			}
	     } while ($i<=$_SESSION['QuickEntries']); /*loop to the next quick entry record */

	     unset($NewItem);
	 } /* end of if quick entry */


	 /*Now do non-quick entry delete/edits/adds */

	If ((isset($_SESSION['Items'])) OR isset($NewItem)){

		If(isset($_GET['Delete'])){
			//page called attempting to delete a line - GET['Delete'] = the line number to delete
			if($_SESSION['Items']->Some_Already_Delivered($_GET['Delete'])==0){
				$_SESSION['Items']->remove_from_cart($_GET['Delete'], 'Yes');  /*Do update DB */
			} else {
				prnMsg( _('This item cannot be deleted because some of it has already been invoiced'),'warn');
			}
		}

		foreach ($_SESSION['Items']->LineItems as $OrderLine) {

			if (isset($_POST['Quantity_' . $OrderLine->LineNumber])){

				$Quantity = $_POST['Quantity_' . $OrderLine->LineNumber];
				$Price = $_POST['Price_' . $OrderLine->LineNumber];
				$DiscountPercentage = $_POST['Discount_' . $OrderLine->LineNumber];
				$Narrative = $_POST['Narrative_' . $OrderLine->LineNumber];
				$ItemDue = $_POST['ItemDue_' . $OrderLine->LineNumber];
-				$POLine = $_POST['POLine_' . $OrderLine->LineNumber];

				if(!Is_Date($ItemDue)) {
					prnMsg(_('An invalid date entry was made for ') . ' ' . $NewItem . ' ' . _('The date entry') . ' ' . $ItemDue . ' ' . ('must be in the format') . ' ' . $_SESSION['DefaultDateFormat'],'warn');
					//Attempt to default the due date to something sensible?
					$ItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
				}
				If ($Quantity<0 OR $Price <0 OR $DiscountPercentage >100 OR $DiscountPercentage <0){
					prnMsg(_('The item could not be updated because you are attempting to set the quantity ordered to less than 0 or the price less than 0 or the discount more than 100% or less than 0%'),'warn');

				} elseif($_SESSION['Items']->Some_Already_Delivered($OrderLine->LineNumber)!=0 AND $_SESSION['Items']->LineItems[$OrderLine->LineNumber]->Price != $Price) {

					prnMsg(_('The item you attempting to modify the price for has already had some quantity invoiced at the old price the items unit price cannot be modified retrospectively'),'warn');

				} elseif($_SESSION['Items']->Some_Already_Delivered($OrderLine->LineNumber)!=0 AND $_SESSION['Items']->LineItems[$OrderLine->LineNumber]->DiscountPercent != ($DiscountPercentage/100)) {

					prnMsg(_('The item you attempting to modify has had some quantity invoiced at the old discount percent the items discount cannot be modified retrospectively'),'warn');

				} elseif ($_SESSION['Items']->LineItems[$OrderLine->LineNumber]->QtyInv > $Quantity){
					prnMsg( _('You are attempting to make the quantity ordered a quantity less than has already been invoiced') . '. ' . _('The quantity delivered and invoiced cannot be modified retrospectively'),'warn');
				} elseif ($OrderLine->Quantity !=$Quantity OR $OrderLine->Price != $Price OR ABS($OrderLine->Disc -$DiscountPercentage/100) >0.001 OR $OrderLine->Narrative != $Narrative OR $OrderLine->ItemDue != $ItemDue OR $Orderline->POLine != $POLine) {
					$_SESSION['Items']->update_cart_item($OrderLine->LineNumber,
										$Quantity,
										$Price,
										($DiscountPercentage/100),
										$Narrative,
										'Yes', /*Update DB */
										$ItemDue, /*added line 8/23/2007 by Morris Kelly to get line item due date*/
										$POLine);
				}
			} //page not called from itself - POST variables not set
		}
	}
	if (isset($_POST['DeliveryDetails'])){
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=' . $rootpath . '/DeliveryDetails.php?' . SID . '">';
		prnMsg(_('You should automatically be forwarded to the entry of the delivery details page') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' .
           '<a href="' . $rootpath . '/DeliveryDetails.php?' . SID . '">' . _('click here') . '</a> ' . _('to continue') . 'info');
	   	exit;
	}


	If (isset($NewItem)){
/* get the item details from the database and hold them in the cart object make the quantity 1 by default then add it to the cart */
/*Now figure out if the item is a kit set - the field MBFlag='K'*/
		$sql = "SELECT stockmaster.mbflag
		   		FROM stockmaster
				WHERE stockmaster.stockid='". $NewItem ."'";

		$ErrMsg =  _('Could not determine if the part being ordered was a kitset or not because');

		$KitResult = DB_query($sql, $db,$ErrMsg);

		$NewItemQty = 1; /*By Default */
		$Discount = 0; /*By default - can change later or discount category overide */

		if ($myrow=DB_fetch_array($KitResult)){
		   	if ($myrow['mbflag']=='K'){	/*It is a kit set item */
				$sql = "SELECT bom.component,
			    		bom.quantity
					FROM bom
					WHERE bom.parent='" . $NewItem . "'
					AND bom.effectiveto > '" . Date('Y-m-d') . "'
					AND bom.effectiveafter < '" . Date('Y-m-d') . "'";

				$ErrMsg = _('Could not retrieve kitset components from the database because');
				$KitResult = DB_query($sql,$db,$ErrMsg);

				$ParentQty = $NewItemQty;
				while ($KitParts = DB_fetch_array($KitResult,$db)){
					$NewItem = $KitParts['component'];
					$NewItemQty = $KitParts['quantity'] * $ParentQty;
					include('includes/SelectOrderItems_IntoCart.inc');
				}

			} else { /*Its not a kit set item*/

			     include('includes/SelectOrderItems_IntoCart.inc');
			}

		} /* end of if its a new item */

	} /*end of if its a new item */

	If (isset($NewItem_array) && isset($_POST['order_items'])){
/* get the item details from the database and hold them in the cart object make the quantity 1 by default then add it to the cart */
/*Now figure out if the item is a kit set - the field MBFlag='K'*/
		foreach($NewItem_array as $NewItem => $NewItemQty)
		{
				if($NewItemQty > 0)
				{
					$sql = "SELECT stockmaster.mbflag
							FROM stockmaster
							WHERE stockmaster.stockid='". $NewItem ."'";
			
					$ErrMsg =  _('Could not determine if the part being ordered was a kitset or not because');
			
					$KitResult = DB_query($sql, $db,$ErrMsg);
			
					//$NewItemQty = 1; /*By Default */
					$Discount = 0; /*By default - can change later or discount category overide */
					
					if ($myrow=DB_fetch_array($KitResult)){
						if ($myrow['mbflag']=='K'){	/*It is a kit set item */
							$sql = "SELECT bom.component,
								bom.quantity
								FROM bom
								WHERE bom.parent='" . $NewItem . "'
								AND bom.effectiveto > '" . Date('Y-m-d') . "'
								AND bom.effectiveafter < '" . Date('Y-m-d') . "'";
			
							$ErrMsg = _('Could not retrieve kitset components from the database because');
							$KitResult = DB_query($sql,$db,$ErrMsg);
			
							$ParentQty = $NewItemQty;
							while ($KitParts = DB_fetch_array($KitResult,$db)){
								$NewItem = $KitParts['component'];
								$NewItemQty = $KitParts['quantity'] * $ParentQty;
								include('includes/SelectOrderItems_IntoCart.inc');
							}
			
						} else { /*Its not a kit set item*/
							
						include('includes/SelectOrderItems_IntoCart.inc');
						}
			
					} /* end of if its a new item */
					
				} /*end of if its a new item */
				
		}
		
	}
	

	/* Run through each line of the order and work out the appropriate discount from the discount matrix */
	$DiscCatsDone = array();
	$counter =0;
	foreach ($_SESSION['Items']->LineItems as $OrderLine) {

		if ($OrderLine->DiscCat !="" AND ! in_array($OrderLine->DiscCat,$DiscCatsDone)){
			$DiscCatsDone[$Counter]=$OrderLine->DiscCat;
			$QuantityOfDiscCat =0;

			foreach ($_SESSION['Items']->LineItems as $StkItems_2) {
				/* add up total quantity of all lines of this DiscCat */
				if ($StkItems_2->DiscCat==$OrderLine->DiscCat){
					$QuantityOfDiscCat += $StkItems_2->Quantity;
				}
			}
			$result = DB_query("SELECT MAX(discountrate) AS discount
						FROM discountmatrix
						WHERE salestype='" .  $_SESSION['Items']->DefaultSalesType . "'
						AND discountcategory ='" . $OrderLine->DiscCat . "'
						AND quantitybreak <" . $QuantityOfDiscCat,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]!=0){ /* need to update the lines affected */
				foreach ($_SESSION['Items']->LineItems as $StkItems_2) {
					/* add up total quantity of all lines of this DiscCat */
					if ($StkItems_2->DiscCat==$OrderLine->DiscCat AND $StkItems_2->DiscountPercent < $myrow[0]){
						$_SESSION['Items']->LineItems[$StkItems_2->LineNumber]->DiscountPercent = $myrow[0];
					}
				}
			}
		}
	} /* end of discount matrix lookup code */

	if (count($_SESSION['Items']->LineItems)>0){ /*only show order lines if there are any */

/* This is where the order as selected should be displayed  reflecting any deletions or insertions*/

		echo '<CENTER>
			<TABLE CELLPADDING=2 COLSPAN=7 BORDER=1>
			<TR BGCOLOR=#800000>';
		if($_SESSION['Items']->DefaultPOLine == 1){
			echo '<TH>' . _('PO Line') . '</TH>';
		}
		echo '<TH>' . _('Item Code') . '</TH>
			<TH>' . _('Item Description') . '</TH>
			<TH>' . _('Quantity') . '</TH>
			<TH>' . _('QOH') . '</TH>
			<TH>' . _('Unit') . '</TH>
			<TH>' . _('Price') . '</TH>
			<TH>' . _('Discount') . '</TH>
			<TH>' . _('Total') . '</TH>
			<TH>' . _('Due Date') . '</TH></TR>';

		$_SESSION['Items']->total = 0;
		$_SESSION['Items']->totalVolume = 0;
		$_SESSION['Items']->totalWeight = 0;
		$k =0;  //row colour counter
		foreach ($_SESSION['Items']->LineItems as $OrderLine) {

			$LineTotal = $OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent);
			$DisplayLineTotal = number_format($LineTotal,2);
			$DisplayDiscount = number_format(($OrderLine->DiscountPercent * 100),2);
			$QtyOrdered = $OrderLine->Quantity;
			$QtyRemain = $QtyOrdered - $OrderLine->QtyInv;

			if ($OrderLine->QOHatLoc < $OrderLine->Quantity AND ($OrderLine->MBflag=='B' OR $OrderLine->MBflag=='M')) {
				/*There is a stock deficiency in the stock location selected */
				$RowStarter = '<tr bgcolor="#EEAABB">';
			} elseif ($k==1){
				$RowStarter = '<tr bgcolor="#CCCCCC">';
				$k=0;
			} else {
				$RowStarter = '<tr bgcolor="#EEEEEE">';
				$k=1;
			}

			echo $RowStarter;
			if($_SESSION['Items']->DefaultPOLine ==1){ //show the input field only if required
				echo '<TD><INPUT TYPE=TEXT NAME="POLine_' . $OrderLine->LineNumber . '" SIZE=20 MAXLENGTH=20 VALUE=' . $OrderLine->POLine . '></TD>';
			} else {
				echo '<input type="hidden" name="POLine_' .	 $OrderLine->LineNumber . '" value="">';
			}

			echo '<TD><A target="_blank" HREF="' . $rootpath . '/StockStatus.php?' . SID . '&StockID=' . $OrderLine->StockID . '&DebtorNo=' . $_SESSION['Items']->DebtorNo . '">' . $OrderLine->StockID . '</A></TD>
				<TD>' . $OrderLine->ItemDescription . '</TD>';

			echo '<TD><INPUT TYPE=TEXT NAME="Quantity_' . $OrderLine->LineNumber . '" SIZE=6 MAXLENGTH=6 VALUE=' . $OrderLine->Quantity . '>';
			if ($QtyRemain != $QtyOrdered){
				echo '<br>'.$OrderLine->QtyInv.' of '.$OrderLine->Quantity.' invoiced';
			}
			echo '</TD>
		<TD>' . $OrderLine->QOHatLoc . '</TD>
		<TD>' . $OrderLine->Units . '</TD>';

			if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
				/*OK to display with discount if it is an internal user with appropriate permissions */

				echo '<TD><INPUT TYPE=TEXT NAME="Price_' . $OrderLine->LineNumber . '" SIZE=16 MAXLENGTH=16 VALUE=' . $OrderLine->Price . '></TD>
					<TD><INPUT TYPE=TEXT NAME="Discount_' . $OrderLine->LineNumber . '" SIZE=5 MAXLENGTH=4 VALUE=' . ($OrderLine->DiscountPercent * 100) . '>%</TD>';

			} else {
				echo '<TD ALIGN=RIGHT>' . $OrderLine->Price . '</TD><TD></TD>';
				echo '<INPUT TYPE=HIDDEN NAME="Price_' . $OrderLine->LineNumber . '" VALUE=' . $OrderLine->Price . '>';
			}
			if ($_SESSION['Items']->Some_Already_Delivered($OrderLine->LineNumber)){
				$RemTxt = _('Clear Remaining');
			} else {
				$RemTxt = _('Delete');
			}
			echo '</TD><TD ALIGN=RIGHT>' . $DisplayLineTotal . '</FONT></TD>';
			$LineDueDate = $OrderLine->ItemDue;
			if (!Is_Date($OrderLine->ItemDue)){
				$LineDueDate = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
				$_SESSION['Items']->LineItems[$OrderLine->LineNumber]->ItemDue= $LineDueDate;
			}

			echo '<TD><INPUT TYPE=TEXT NAME="ItemDue_' . $OrderLine->LineNumber . '" SIZE=10 MAXLENGTH=10 VALUE=' . $LineDueDate . '></TD>';

			echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=' . $OrderLine->LineNumber . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');">' . $RemTxt . '</A></TD></TR>';

			if ($_SESSION['AllowOrderLineItemNarrative'] == 1){
				echo $RowStarter;
				echo '<TD COLSPAN=7><TEXTAREA  NAME="Narrative_' . $OrderLine->LineNumber . '" cols=100% rows=1>' . $OrderLine->Narrative . '</TEXTAREA><BR><HR></TD></TR>';
			} else {
				echo '<INPUT TYPE=HIDDEN NAME="Narrative" VALUE="">';
			}

			$_SESSION['Items']->total = $_SESSION['Items']->total + $LineTotal;
			$_SESSION['Items']->totalVolume = $_SESSION['Items']->totalVolume + $OrderLine->Quantity * $OrderLine->Volume;
			$_SESSION['Items']->totalWeight = $_SESSION['Items']->totalWeight + $OrderLine->Quantity * $OrderLine->Weight;

		} /* end of loop around items */

		$DisplayTotal = number_format($_SESSION['Items']->total,2);
		echo '<TR><TD></TD><TD><B>' . _('TOTAL Excl Tax/Freight') . '</B></TD><TD COLSPAN=6 ALIGN=RIGHT>' . $DisplayTotal . '</TD></TR></TABLE>';

		$DisplayVolume = number_format($_SESSION['Items']->totalVolume,2);
		$DisplayWeight = number_format($_SESSION['Items']->totalWeight,2);
		echo '<TABLE BORDER=1><TR><TD>' . _('Total Weight') . ':</TD>
                         <TD>' . $DisplayWeight . '</TD>
                         <TD>' . _('Total Volume') . ':</TD>
                         <TD>' . $DisplayVolume . '</TD>
                       </TR></TABLE>';


		echo '<BR><INPUT TYPE=SUBMIT NAME="Recalculate" Value="' . _('Re-Calculate') . '">
                <INPUT TYPE=SUBMIT NAME="DeliveryDetails" VALUE="' . _('Enter Delivery Details and Confirm Order') . '"><HR>';

	} # end of if lines

/* Now show the stock item selection search stuff below */

	 if (isset($_POST['PartSearch']) && $_POST['PartSearch']!='' || !isset($_POST['QuickEntry'])){

		echo '<input type="hidden" name="PartSearch" value="' .  _('Yes Please') . '">';

		$SQL="SELECT categoryid,
				categorydescription
			FROM stockcategory
			WHERE stocktype='F' OR stocktype='D'
			ORDER BY categorydescription";
		$result1 = DB_query($SQL,$db);

		echo '<B>' . $msg . '</B><TABLE><TR><TD><FONT SIZE=2>' . _('Select a stock category') . ':</FONT><SELECT NAME="StockCat">';

		if (!isset($_POST['StockCat'])){
			echo "<OPTION SELECTED VALUE='All'>" . _('All');
			$_POST['StockCat'] ='All';
		} else {
			echo "<OPTION VALUE='All'>" . _('All');
		}

		while ($myrow1 = DB_fetch_array($result1)) {

			if ($_POST['StockCat']==$myrow1['categoryid']){
				echo '<OPTION SELECTED VALUE=' . $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
			} else {
				echo '<OPTION VALUE='. $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
			}
		}

		?>

		</SELECT>
		<TD><FONT SIZE=2><?php echo _('Enter text extracts in the'); ?> <B><?php echo _('description'); ?></B>:</FONT></TD>
		<TD><INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25 VALUE="<?php if (isset($_POST['Keywords'])) echo $_POST['Keywords']; ?>"></TD></TR>
		<TR><TD></TD>
		<TD><FONT SIZE 3><B><?php echo _('OR'); ?> </B></FONT><FONT SIZE=2><?php echo _('Enter extract of the'); ?> <B><?php echo _('Stock Code'); ?></B>:</FONT></TD>
		<TD><INPUT TYPE="Text" NAME="StockCode" SIZE=15 MAXLENGTH=18 VALUE="<?php if (isset($_POST['StockCode'])) echo $_POST['StockCode']; ?>"></TD>
		</TR>
		</TABLE>
		<CENTER><INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>">
		<INPUT TYPE=SUBMIT Name="QuickEntry" VALUE="<?php echo _('Use Quick Entry'); ?>">


		<script language='JavaScript' type='text/javascript'>

            	document.forms[0].StockCode.select();
            	document.forms[0].StockCode.focus();

		</script>

		<?php
		
		if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
			echo '<INPUT TYPE=SUBMIT Name="ChangeCustomer" VALUE="' . _('Change Customer') . '">';
			echo '<BR><BR><a target="_blank" href="' . $rootpath . '/Stocks.php?' . SID . '"><B>' . _('Add a New Stock Item') . '</B></a>';
		}

		echo '</CENTER>';

		if (isset($SearchResult)) {

			echo '<CENTER><form name="orderform"><TABLE CELLPADDING=2 COLSPAN=7 BORDER=1>';
			$TableHeader = '<TR><TH>' . _('Code') . '</TH>
                          			<TH>' . _('Description') . '</TH>
                          			<TH>' . _('Units') . '</TH>
                          			<TH>' . _('On Hand') . '</TH>
                          			<TH>' . _('On Demand') . '</TH>
                          			<TH>' . _('On Order') . '</TH>
                          			<TH>' . _('Available') . '</TH>
                          			<TH>' . _('Quantity') . '</TH></TR>';
			echo $TableHeader;
			$j = 1;
			$k=0; //row colour counter

			while ($myrow=DB_fetch_array($SearchResult)) {

/*
				if (function_exists('imagecreatefrompng') ){
					$ImageSource = '<IMG SRC="GetStockImage.php?SID&automake=1&textcolor=FFFFFF&bgcolor=CCCCCC&StockID=' . urlencode($myrow['stockid']). '&text=&width=64&height=64">';
				} else {
					if(file_exists($_SERVER['DOCUMENT_ROOT'] . $rootpath. '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg')) {
						$ImageSource = '<IMG SRC="' .$_SERVER['DOCUMENT_ROOT'] . $rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg">';
					} else {
						$ImageSource = _('No Image');
					}
				}

*/
				// Find the quantity in stock at location
				$qohsql = "SELECT sum(quantity)
						   FROM locstock 
						   WHERE stockid='" .$myrow['stockid'] . "' AND 
						   loccode = '" . $_SESSION['Items']->Location . "'";
				$qohresult =  DB_query($qohsql,$db);
				$qohrow = DB_fetch_row($qohresult);
				$qoh = $qohrow[0];

				// Find the quantity on outstanding sales orders
				$sql = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
            			     FROM salesorderdetails,
                      			salesorders
			                 WHERE salesorders.orderno = salesorderdetails.orderno AND
            			     salesorders.fromstkloc='" . $_SESSION['Items']->Location . "' AND
 			                salesorderdetails.completed=0 AND
		 					salesorders.quotation=0 AND
                 			salesorderdetails.stkcode='" . $myrow['stockid'] . "'";

				$ErrMsg = _('The demand for this product from') . ' ' . $_SESSION['Items']->Location . ' ' .
				     _('cannot be retrieved because');
				$DemandResult = DB_query($sql,$db,$ErrMsg);

				$DemandRow = DB_fetch_row($DemandResult);
				if ($DemandRow[0] != null){
				  $DemandQty =  $DemandRow[0];
				} else {
				  $DemandQty = 0;
				}
				
				// Find the quantity on purchase orders
				$sql = "SELECT SUM(purchorderdetails.quantityord-purchorderdetails.quantityrecd) AS dem
            			     FROM purchorderdetails
			                 WHERE purchorderdetails.completed=0 AND
                			purchorderdetails.itemcode='" . $myrow['stockid'] . "'";

				$ErrMsg = _('The order details for this product cannot be retrieved because');
				$PurchResult = db_query($sql,$db,$ErrMsg);

				$PurchRow = db_fetch_row($PurchResult);
				if ($PurchRow[0]!=null){
				  $PurchQty =  $PurchRow[0];
				} else {
				  $PurchQty = 0;
				}

				// Find the quantity on works orders				
				$sql = "SELECT SUM(woitems.qtyreqd - woitems.qtyrecd) AS dedm
				       FROM woitems
				       WHERE stockid='" . $myrow['stockid'] ."'";
				$ErrMsg = _('The order details for this product cannot be retrieved because');
				$WoResult = db_query($sql,$db,$ErrMsg);

				$WoRow = db_fetch_row($WoResult);
				if ($WoRow[0]!=null){
				  $WoQty =  $WoRow[0];
				} else {
				  $WoQty = 0;
				}
				
				if ($k==1){
					echo '<tr bgcolor="#CCCCCC">';
					$k=0;
				} else {
					echo '<tr bgcolor="#EEEEEE">';
					$k=1;
				}
				$OnOrder = $PurchQty + $WoQty;
				
				$Available = $qoh - $DemandQty + $OnOrder;

				printf("<TD><FONT SIZE=1>%s</FONT></TD>
					<TD><FONT SIZE=1>%s</FONT></TD>
					<TD><FONT SIZE=1>%s</FONT></TD>
					<TD style='text-align:center'><FONT SIZE=1>%s</FONT></TD>
					<TD style='text-align:center'><FONT SIZE=1>%s</FONT></TD>
					<TD style='text-align:center'><FONT SIZE=1>%s</FONT></TD>
					<TD style='text-align:center'><FONT SIZE=1>%s</FONT></TD>
					<TD><FONT SIZE=1><input type='textbox' size=6 name='itm[".$myrow['stockid']."]' value=0>"
					. '</FONT></TD>
					</TR>',
					$myrow['stockid'],
					$myrow['description'],
					$myrow['units'],
					$qoh,
					$DemandQty,
					$OnOrder,
					$Available,
					$ImageSource,
					$rootpath,
					SID,
					$myrow['stockid']);

				$j++;
				If ($j == 25){
					$j=1;
					echo $TableHeader;
				}
	#end of page full new headings if
			}
	#end of while loop
			echo '<tr><td align=center colspan=8><input type="hidden" name="order_items" value=1><input type="submit" value="Order"></td></tr>';
			echo '</TABLE></form>';

		}#end if SearchResults to show
	} /*end of PartSearch options to be displayed */
	   else { /* show the quick entry form variable */
		  /*FORM VARIABLES TO POST TO THE ORDER  WITH PART CODE AND QUANTITY */
	     	echo '<br><center><font size=4 color=blue><b>' . _('Quick Entry') . '</b></font><br>
	     			<table border=1>
					<tr>';
			/*do not display colum unless customer requires po line number by sales order line*/
		 	if($_SESSION['Items']->DefaultPOLine ==1){
				echo	'<TH>' . _('PO Line') . '</td>';
			}
			echo '<TH>' . _('Part Code') . '</TH>
				<TH>' . _('Quantity') . '</TH>
				<TH>' . _('Due Date') . '</TH>
			</tr>';
			$DefaultDeliveryDate = DateAdd(Date($_SESSION['DefaultDateFormat']),'d',$_SESSION['Items']->DeliveryDays);
	    	for ($i=1;$i<=$_SESSION['QuickEntries'];$i++){

	     		echo '<tr bgcolor="#CCCCCC">';
	     		/* Do not display colum unless customer requires po line number by sales order line*/
	     		if($_SESSION['Items']->DefaultPOLine > 0){
					echo '<td><input type="text" name="poline_' . $i . '" size=21 maxlength=20></td>';
				}
				echo '<td><input type="text" name="part_' . $i . '" size=21 maxlength=20></td>
						<td><input type="text" name="qty_' . $i . '" size=6 maxlength=6></td>
						<td><input type="text" name="itemdue_' . $i . '" size=25 maxlength=25
						value="' . $DefaultDeliveryDate . '"></td></tr>';
	   		}

	     	echo '</table><input type="submit" name="QuickEntry" value="' . _('Quick Entry') . '">
                     <input type="submit" name="PartSearch" value="' . _('Search Parts') . '">';

?>
	     <script language='JavaScript' type='text/javascript'>
    //<![CDATA[
            <!--
            if ("undefined" == typeof(document.forms[0].poline_1) ) {
            	document.forms[0].part_1.select();
            	document.forms[0].part_1.focus();
            } else{
	        	document.forms[0].poline_1.select();
				document.forms[0].poline_1.focus();
			}
            //-->
    //]]>
	    </script>
<?php

	}
	if ($_SESSION['Items']->ItemsOrdered >=1){
      		echo '<CENTER><BR><INPUT TYPE=SUBMIT NAME="CancelOrder" VALUE="' . _('Cancel Whole Order') . '" onclick="return confirm(\'' . _('Are you sure you wish to cancel this entire order?') . '\');"></CENTER>';
	}
}#end of else not selecting a customer

echo '</FORM>';
include('includes/footer.inc');
?>