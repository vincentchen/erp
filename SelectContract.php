<?php

/* $Id: $*/

$PageSecurity = 6;

include('includes/session.inc');
$title = _('Select Contract');
include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/contract.png" title="' . _('Contracts') . '" alt="">' . ' ' . _('Select A Contract') . '</p> ';

echo '<form action=' . $_SERVER['PHP_SELF'] .'?' .SID . ' method=post>';


echo '<p><div class="centre">';

if (isset($_REQUEST['ContractRef']) AND $_REQUEST['ContractRef']!='') {
	$_REQUEST['ContractRef'] = trim($_REQUEST['ContractRef']);
	echo _('Contract Reference') . ' - ' . $_REQUEST['ContractRef'];
} else {
	if (isset($_REQUEST['SelectedCustomer'])) {
		echo _('For customer') . ': ' . $_REQUEST['SelectedCustomer'] . ' ' . _('and') . ' ';
		echo '<input type="hidden" name="SelectedCustomer" value="' . $_REQUEST['SelectedCustomer'] . '">';
	}
}

if (!isset($_REQUEST['ContractRef']) or $_REQUEST['ContractRef']==''){

	echo _('Contract Reference') . ': <input type="text" name="ContractRef" maxlength=20 size=20>&nbsp&nbsp';
	echo '<select name="Status">';
		
	if (isset($_GET['Status'])){
		$_POST['Status']=$_GET['Status'];
	}
	if (!isset($_POST['Status'])){
		$_POST['Status']=4;
	}
	if ($_POST['Status']==0){
		echo '<option selected value="0">' . _('Not Yet Quoted'). '</option>';
		echo '<option value="1">' . _('Quoted - No Order Placed'). '</option>';
		echo '<option value="2">' . _('Order Placed') . '</option>';
		echo '<option value="3">' . _('Completed') . '</option>';
		echo '<option value="4">' . _('All Contracts') . '</option>';
	} elseif($_POST['Status']==1) {
		echo '<option value="0">' . _('Not Yet Quoted'). '</option>';
		echo '<option selected value="1">' . _('Quoted - No Order Placed'). '</option>';
		echo '<option value="2">' . _('Order Placed') . '</option>';
		echo '<option value="3">' . _('Completed') . '</option>';
		echo '<option value="4">' . _('All Contracts') . '</option>';
	} elseif($_POST['Status']==2) {
		echo '<option value="0">' . _('Not Yet Quoted'). '</option>';
		echo '<option value="1">' . _('Quoted - No Order Placed'). '</option>';
		echo '<option selected value="2">' . _('Order Placed') . '</option>';
		echo '<option value="3">' . _('Completed') . '</option>';
		echo '<option value="4">' . _('All Contracts') . '</option>';
	} elseif($_POST['Status']==3) {
		echo '<option value="0">' . _('Not Yet Quoted'). '</option>';
		echo '<option value="1">' . _('Quoted - No Order Placed'). '</option>';
		echo '<option value="2">' . _('Order Placed') . '</option>';
		echo '<option selected value="3">' . _('Completed') . '</option>';	
		echo '<option value="4">' . _('All Contracts') . '</option>';
	} elseif($_POST['Status']==4) {
		echo '<option value="0">' . _('Not Yet Quoted'). '</option>';
		echo '<option value="1">' . _('Quoted - No Order Placed'). '</option>';
		echo '<option value="2">' . _('Order Placed') . '</option>';
		echo '<option value="3">' . _('Completed') . '</option>';	
		echo '<option selected value="4">' . _('All Contracts') . '</option>';
	}
	echo '</select> &nbsp&nbsp';
}
echo '<input type="submit" name="SearchContracts" VALUE="' . _('Search') . '">';
echo '&nbsp;&nbsp;<a href="' . $rootpath . '/Contracts.php?' . SID . '">' . _('New Contract') . '</a>';


//figure out the SQL required from the inputs available

if (isset($_REQUEST['ContractRef']) AND $_REQUEST['ContractRef'] !='') {
		$SQL = "SELECT contractref,
					   contractdescription,
					   categoryid,
					   contracts.debtorno,
					   debtorsmaster.name AS customername,
					   branchcode,
					   status,
					   orderno,
					   wo,
					   customerref,
					   requireddate
				FROM contracts INNER JOIN debtorsmaster 
				ON contracts.debtorno = debtorsmaster.debtorno
				WHERE contractref='". $_REQUEST['ContractRef'] ."'";
			
} else { //contractref not selected
	if (isset($_REQUEST['SelectedCustomer'])) {

		$SQL = "SELECT contractref,
					   contractdescription,
					   categoryid,
					   contracts.debtorno,
					   debtorsmaster.name AS customername,
					   branchcode,
					   status,
					   orderno,
					   wo,
					   customerref,
					   requireddate
				FROM contracts INNER JOIN debtorsmaster 
				ON contracts.debtorno = debtorsmaster.debtorno
				WHERE debtorno='". $_REQUEST['SelectedCustomer'] ."'";
		if ($_POST['Status']!=4){
			$SQL .= " AND status='" . $_POST['Status'] . "'";		
		}
	} else { //no customer selected
		$SQL = 'SELECT contractref,
					   contractdescription,
					   categoryid,
					   contracts.debtorno,
					   debtorsmaster.name AS customername,
					   branchcode,
					   status,
					   orderno,
					   wo,
					   customerref,
					   requireddate
				FROM contracts INNER JOIN debtorsmaster 
				ON contracts.debtorno = debtorsmaster.debtorno';
		if ($_POST['Status']!=4){
			$SQL .= " AND status='" . $_POST['Status'] . "'";		
		}			
	}
} //end not contract ref selected

$ErrMsg = _('No contracts were returned by the SQL because');
$ContractsResult = DB_query($SQL,$db,$ErrMsg);

/*show a table of the contracts returned by the SQL */

echo '<table cellpadding=2 colspan=7 WIDTH=100%>';

$tableheader = '<tr>
			    <th>' . _('Modify') . '</th>
				<th>' . _('Order') . '</th>
				<th>' . _('Issue To WO') . '</th>
				<th>' . _('Contract Ref') . '</th>
				<th>' . _('Description') . '</th>
				<th>' . _('Customer') . '</th>
				<th>' . _('Required Date') . '</th>
				</tr>';

echo $tableheader;

$j = 1;
$k=0; //row colour counter
while ($myrow=DB_fetch_array($ContractsResult)) {
	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k++;
	}

	$ModifyPage = $rootpath . '/Contracts.php?' . SID . '&ModifyContractRef=' . $myrow['contractref'];
	$OrderModifyPage = $rootpath . '/SelectOrderItems.php?' . SID . '&ModifyOrderNo=' . $myrow['orderno'];
	$IssueToWOPage = $rootpath . '/WOIssue.php?' . SID . '&WO=' . $myrow['wo'];
	$FormatedRequiredDate = ConvertSQLDate($myrow['requireddate']);
	
	if ($myrow['status']==0){ //still setting up the contract
		echo '<td><a href="' . $ModifyPage . '">' . _('Modify') . '</a></td>';
	} else {
		echo '<td>' . _('n/a') . '</td>';
	}
	if ($myrow['status']==1 OR $myrow['status']==2){ // quoted or ordered
		echo '<td><a href="' . $OrderModifyPage . '">' . $myrow['orderno'] . '</a></td>';
	} else {
		echo '<td>' . _('n/a') . '</td>';
	}	
	if ($myrow['status']==2){ //the customer has accepted the quote but not completed contract yet
		echo '<td><a href="' . $IssueToWOPage . '">' . $myrow['wo'] . '</a></td>';
	} else {
		echo '<td>' . _('n/a') . '</td>';
	}
	echo '<td>' . $myrow['contractref'] . '</td>
		  <td>' . $myrow['contractdescription'] . '</td>
		  <td>' . $myrow['customername'] . '</td>
		  <td>' . $FormatedRequiredDate . '</td></tr>';
		  
	$j++;
	if ($j == 12){
		$j=1;
		echo $tableheader;
	}
//end of page full new headings if
}
//end of while loop

echo '</table></form>';
include('includes/footer.inc');
?>