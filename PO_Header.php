<?php
if ($_GET['ModifyOrderNumber']) {
	$title = "Modify Purchase Order " . $_GET['ModifyOrderNumber'];
} else {
	$title = "Purchase Order Entry";
}

$PageSecurity = 4;

include("includes/DefinePOClass.php");
/* Session started in header.inc for password checking and authorisation level check */
include("includes/session.inc");
include("includes/header.inc");
include("includes/DateFunctions.inc");
include("includes/SQL_CommonFunctions.inc");

/*Page is called with NewOrder=Yes when a new order is to be entered
the session variable that holds all the PO data $_SESSION['PO'] is unset to allow
all new details to be created */

if (isset($_GET['NewOrder']) and isset($_SESSION['PO'])){
     unset($_SESSION['PO']);
     $_SESSION['ExistingOrder']=0;
}

If ($_POST['EnterLines']=="Enter Line Items"){
/*User hit the button to enter line items - ensure session variables updated then meta refresh to PO_Items.php*/

	$_SESSION['PO']->Location=$_POST['StkLocation'];
	$_SESSION['PO']->DelAdd1 = $_POST['DelAdd1'];
	$_SESSION['PO']->DelAdd2 = $_POST['DelAdd2'];
	$_SESSION['PO']->DelAdd3 = $_POST['DelAdd3'];
	$_SESSION['PO']->DelAdd4 = $_POST['DelAdd4'];
	$_SESSION['PO']->Initiator = $_POST['Initiator'];
	$_SESSION['PO']->RequisitionNo = $_POST['Requisition'];
	$_SESSION['PO']->ExRate = $_POST['ExRate'];
	$_SESSION['PO']->Comments = $_POST['Comments'];
	if ($_POST['RePrint']==1){
		$_SESSION['PO']->AllowPrintPO=1;
		$sql = "UPDATE PurchOrders SET PurchOrders.AllowPrint=1 WHERE PurchOrders.OrderNo=" . $_SESSION['PO']->OrderNo;
		$updateResult = DB_query($sql,$db);
		if (DB_error_no($db) !=0){
			echo "<BR>An error occurred updating the purchase order to allow reprints. The error says " . DB_error_msg($db);
			if ($debug==1){
				echo "<BR>The SQL used was:<BR>$sql";
			}
		}
	}

	echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . "/PO_Items.php?" . SID . "'>";
	echo "<P>You should automatically be forwarded to the entry of the purchase order line items page. If this does not happen (if the browser doesn't support META Refresh) <a href='" . $rootpath . "/PO_Items.php?" . SID . "'>click here</a> to continue.<br>";
}
/*The page can be called with ModifyOrderNumber=x where x is a purchase order number
The page then looks up the details of order x and allows these details to be modified */

if ($_GET['ModifyOrderNumber']!=""){
      include ("includes/PO_ReadInOrder.inc");
}

if (isset($_POST['CancelOrder']) AND $_POST['CancelOrder']!="") { /*The cancel button on the header screen - to delete order */
	$OK_to_delete = 1;	 //assume this in the first instance

	if(!isset($_SESSION['ExistingOrder']) OR $_SESSION['ExistingOrder']!=0) { //need to check that not already dispatched or invoiced by the supplier

		if($_SESSION['PO']->Any_Already_Received()==1){
			$OK_to_delete =0;
			echo "<BR>This order cannot be cancelled because some of it has already been received. The line item quantities may be modified to quantities more than already received. Prices cannot be altered for lines that have already been received and quantities cannot be reduced below the quantity already received.";
		}

	}

	if ($OK_to_delete==1){
		unset($_SESSION['PO']->LineItems);
		unset($_SESSION['PO']);
		$_SESSION['PO'] = new PurchOrder;
		$_SESSION['RequireSupplierSelection'] = 1;

		if($_SESSION['ExistingOrder']!=0){
			$SQL = "DELETE FROM PurchOrders WHERE PurchOrders.OrderNo=" . $_SESSION['ExistingOrder'];
			$DelResult=DB_query($SQL,$db);
			if (DB_error_no($db) !=0) {
				echo "<BR>The order header could not be deleted because - " . DB_error_msg($db);
			}

			$SQL = "DELETE FROM PurchOrderDetails WHERE PurchOrderDetails.OrderNo =" . $_SESSION['ExistingOrder'];
			$DelResult=DB_query($SQL,$db);
			if (DB_error_no($db) !=0) {
				echo "<BR>The order detail lines could not be deleted because - " . DB_error_msg($db);
			}
		 }
	}
}

if (!isset($_SESSION['PO'])){
	/* It must be a new order being created $_SESSION['PO'] would be set up from the order modification code above if a modification to an existing order. Also $ExistingOrder would be set to 1. The delivery check screen is where the details of the order are either updated or inserted depending on the value of ExistingOrder */

	Session_register("PO");
	Session_register("RequireSupplierSelection");
	Session_register("ExistingOrder");

	$_SESSION['ExistingOrder']=0;
	$_SESSION['PO'] = new PurchOrder;
	$_SESSION['PO']->AllowPrintPO = 1; /*Of course cos the order aint even started !!*/
	$CompanyRecord = ReadInCompanyRecord($db);
	$_SESSION['PO']->GLLink = $CompanyRecord["GLLink_Stock"];

	if ($_SESSION['PO']->SupplierID=="" OR !isset($_SESSION['PO']->SupplierID)){

/* a session variable will have to maintain if a supplier has been selected for the order or not the session variable supplierID holds the supplier code already as determined from user id /password entry  */
		$_SESSION['RequireSupplierSelection'] = 1;
	} else {
		$_SESSION['RequireSupplierSelection'] = 0;
	}
}

if ($_POST['ChangeSupplier']!=""){

/* change supplier only allowed with appropriate permissions - button only displayed to modify is AccessLevel >10  (see below)*/
	if ($_SESSION['PO']->Any_Already_Received()==0){
		$_SESSION['RequireSupplierSelection']=1;
	} else {
		echo "<BR><BR><B>Cannot modify the supplier of the order once some of the order has been received.</B>";
	}
}

if (isset($_POST['SearchSuppliers'])){

	If (strlen($_POST['Keywords'])>0 AND strlen($_POST['SuppCode'])>0) {
		$msg="Supplier name keywords have been used in preference to the supplier code extract entered.";
	}
	If ($_POST['Keywords']=="" AND $_POST['SuppCode']=="") {
		$msg="At least one supplier name keyword OR an extract of a supplier code must be entered for the search";
	} else {
		If (strlen($_POST['Keywords'])>0) {
		//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString. substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT Suppliers.SupplierID, Suppliers.SuppName, Suppliers.CurrCode FROM Suppliers WHERE Suppliers.SuppName LIKE '$SearchString'";

		} elseif (strlen($_POST['SuppCode'])>0){
			$SQL = "SELECT Suppliers.SupplierID, Suppliers.SuppName, Suppliers.CurrCode FROM Suppliers WHERE Suppliers.SupplierID LIKE '%" . $_POST['SuppCode'] . "%'";
		}

		$result_SuppSelect = DB_query($SQL,$db);
		if (DB_error_no($db) !=0) {
			echo "<BR>The searched supplier records requested cannot be retrieved because - " . DB_error_msg($db) . "<BR>SQL used to retrieve the supplier details was:<BR>$SQL";
			exit;
		}
		if (DB_num_rows($result_SuppSelect)==1){
			$myrow=DB_fetch_array($result_SuppSelect);
			$_POST['Select'] = $myrow["SupplierID"];
		} elseif (DB_num_rows($result_SuppSelect)==0){
			echo "<P>No supplier records contain the selected text - please alter your search criteria and try again.";
		}
	} /*one of keywords or SuppCode was more than a zero length string */
} /*end of if search for supplier codes/names */


if ($_POST['Select']) {

/*will only be true if page called from supplier selection form or set because only one supplier record returned from a search
 so parse the $Select string into supplier code and branch code */

	$sql = "SELECT Suppliers.SuppName, Suppliers.CurrCode, Currencies.Rate From Suppliers INNER JOIN Currencies ON Suppliers.CurrCode=Currencies.CurrAbrev WHERE SupplierID='" . $_POST['Select'] . "'";
	$result =DB_query($sql,$db);
	if (DB_error_no($db) !=0) {
		echo "<BR>The supplier record of the supplier selected: " . $_POST['Select']  ." cannot be retrieved because - " . DB_error_msg($db);
		if ($debug==1){
			echo "<BR>The SQL used to retrieve the supplier details (and failed) was:<BR>$sql";
		}
		exit;
	}
	$myrow = DB_fetch_row($result);
	$_SESSION['PO']->SupplierID = $_POST['Select'];
	$_SESSION['RequireSupplierSelection'] = 0;
	$_SESSION['PO']->SupplierName = $myrow[0];
	$_SESSION['PO']->CurrCode = $myrow[1];
	$_SESSION['PO']->ExRate = $myrow[2];
	$_POST['ExRate'] = $myrow[2];
}



if ($_SESSION['RequireSupplierSelection'] ==1 OR !isset($_SESSION['PO']->SupplierID) OR $_SESSION['PO']->SupplierID=="" ) {

	?>

	<FONT SIZE=3><B> - Supplier Selection</B></FONT><BR>

	<FORM ACTION="<?php $_SERVER['PHP_SELF']; ?>" METHOD=POST>
	<B><?php echo $msg; ?></B>
	<TABLE CELLPADDING=3 COLSPAN=4>
	<TR>
	<TD><FONT SIZE=1>Enter text in the supplier name:</FONT></TD>
	<TD><INPUT TYPE="Text" NAME="Keywords" SIZE=20	MAXLENGTH=25></TD>
	<TD><FONT SIZE=3><B>OR</B></FONT></TD>
	<TD><FONT SIZE=1>Enter text extract in the supplier code:</FONT></TD>
	<TD><INPUT TYPE="Text" NAME="SuppCode" SIZE=15	MAXLENGTH=18></TD>
	</TR>
	</TABLE>
	<CENTER><INPUT TYPE=SUBMIT NAME="SearchSuppliers" VALUE="Search Now">
	<INPUT TYPE=SUBMIT ACTION=RESET VALUE="Reset"></CENTER>


	<?php

	If ($result_SuppSelect) {

		echo "<BR><CENTER><TABLE CELLPADDING=3 COLSPAN=7 BORDER=1>";
		
		$tableheader = "<TR><TD class='tableheader'>Code</TD><TD class='tableheader'>Supplier Name</TD><TD class='tableheader'>Currency</TD></B></TR>";
		echo $tableheader;

		$j = 1;
		$k = 0; //row counter to determine background colour

		while ($myrow=DB_fetch_array($result_SuppSelect)) {

			if ($k==1){
				echo "<tr bgcolor='#CCCCCC'>";
				$k=0;
			} else {
				echo "<tr bgcolor='#EEEEEE'>";
				$k++;
			}

			printf("<td><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</td><td>%s</td><td>%s</td></tr>", $myrow["SupplierID"], $myrow["SuppName"], $myrow["CurrCode"]);

			$j++;
			If ($j == 11){
				$j=1;
				echo $tableheader;
			}
//end of page full new headings if
		}
//end of while loop

		echo "</TABLE></CENTER>";

	}
//end if results to show

//end if RequireSupplierSelection
} else {
// everything below here only do if a supplier is selected

	echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "?" . SID . "' METHOD=POST>";

	echo "<CENTER>Purchase Order: <FONT COLOR=BLUE SIZE=4><B>" . $_SESSION['PO']->OrderNo . " " . $_SESSION['PO']->SupplierName . "</U> </B></FONT> - All amounts stated in " . $_SESSION['PO']->CurrCode . "<BR><BR>";

	/*Set up form for entry of order header stuff */

	if(($_POST['StkLocation']=="" OR !isset($_POST['StkLocation'])) AND (isset($_SESSION['PO']->Location) AND $_SESSION['PO']->Location!="")){
	    /*The session variables are set but the form variables have been lost
	    need to restore the form variables from the session */
	    $_POST['StkLocation']=$_SESSION['PO']->Location;
	    $_POST['DelAdd1']=$_SESSION['PO']->DelAdd1;
	    $_POST['DelAdd2']=$_SESSION['PO']->DelAdd2;
	    $_POST['DelAdd3']=$_SESSION['PO']->DelAdd3;
	    $_POST['DelAdd4']=$_SESSION['PO']->DelAdd4;
	    $_POST['Initiator']=$_SESSION['PO']->Initiator;
	    $_POST['Requisition']=$_SESSION['PO']->RequisitionNo;
	    $_POST['ExRate']=$_SESSION['PO']->ExRate;
	    $_POST['Comments']=$_SESSION['PO']->Comments;
	}

	echo "<TABLE BORDER=1><TR><TD><FONT COLOR=BLUE SIZE=4><B>Delivery To</B></FONT></TD><TD><FONT COLOR=BLUE SIZE=4><B>Order Initiation Details</B></FONT></TD></TR><TR><TD>";
	/*nested table level1 */
	  echo "<TABLE><TR><TD>Receive Into:</TD><TD><SELECT NAME='StkLocation'>";
	  $sql = "SELECT LocCode, LocationName FROM Locations";
	  $LocnResult = DB_query($sql,$db);

	  while ($LocnRow=DB_fetch_array($LocnResult)){
		 if ($_POST['StkLocation'] == $LocnRow["LocCode"] OR ($_POST['StkLocation']=="" AND $LocnRow["LocCode"]==$_SESSION['UserStockLocation'])){
			 echo "<OPTION SELECTED Value='" . $LocnRow["LocCode"] . "'>" . $LocnRow["LocationName"];
		 } else {
			 echo "<OPTION Value='" . $LocnRow["LocCode"] . "'>" . $LocnRow["LocationName"];
		 }
	  }
	  echo "</SELECT></TD></TR>";
	 if (!isset($_POST['StkLocation']) OR $_POST['StkLocation']==""){ /*If this is the first time the form loaded set up defaults */
	     $_POST['StkLocation'] = $_SESSION['UserStockLocation'];
	     $sql = "SELECT DelAdd1, DelAdd2, DelAdd3, Tel FROM Locations WHERE LocCode='" . $_POST['StkLocation'] . "'";
	     $LocnAddrResult = DB_query($sql,$db);
	     if (DB_num_rows($LocnAddrResult)==1){
		  $LocnRow = DB_fetch_row($LocnAddrResult);
		  $_POST['DelAdd1'] = $LocnRow[0];
		  $_POST['DelAdd2'] = $LocnRow[1];
		  $_POST['DelAdd3'] = $LocnRow[2];
		  $_POST['DelAdd4'] = $LocnRow[3];
		  $_SESSION['PO']->Location= $_POST['StkLocation'];
		  $_SESSION['PO']->DelAdd1 = $_POST['DelAdd1'];
		  $_SESSION['PO']->DelAdd2 = $_POST['DelAdd2'];
		  $_SESSION['PO']->DelAdd3 = $_POST['DelAdd3'];
		  $_SESSION['PO']->DelAdd4 = $_POST['DelAdd4'];

	     } else { /*The default location of the user is crook */
		  echo "<BR>The default stock location set up for this user is not a currently defined stock location. Your system administrator needs to amend your user record.";
	     }
	  } elseif ($_POST['DelAdd1']==""){
	      $sql = "SELECT DelAdd1, DelAdd2, DelAdd3, Tel FROM Locations WHERE LocCode='" . $_POST['StkLocation'] . "'";
	      $LocnAddrResult = DB_query($sql,$db);
	      if (DB_num_rows($LocnAddrResult)==1){
		  $LocnRow = DB_fetch_row($LocnAddrResult);
		  $_POST['DelAdd1'] = $LocnRow[0];
		  $_POST['DelAdd2'] = $LocnRow[1];
		  $_POST['DelAdd3'] = $LocnRow[2];
		  $_POST['DelAdd4'] = $LocnRow[3];
		  $_SESSION['PO']->Location= $_POST['StkLocation'];
		  $_SESSION['PO']->DelAdd1 = $_POST['DelAdd1'];
		  $_SESSION['PO']->DelAdd2 = $_POST['DelAdd2'];
		  $_SESSION['PO']->DelAdd3 = $_POST['DelAdd3'];
		  $_SESSION['PO']->DelAdd4 = $_POST['DelAdd4'];
	      }
	  }
	  echo "<TR><TD>Deliver to - Street:</TD><TD><INPUT TYPE=text NAME=DelAdd1 SIZE=41 MAXLENGTH=40 Value='" . $_POST['DelAdd1'] . "'></TD></TR>";
	  echo "<TR><TD>Deliver to - Suburb:</TD><TD><INPUT TYPE=text NAME=DelAdd2 SIZE=41 MAXLENGTH=40 Value='" . $_POST['DelAdd2'] . "'></TD></TR>";
	  echo "<TR><TD>Deliver to - City:</TD><TD><INPUT TYPE=text NAME=DelAdd3 SIZE=41 MAXLENGTH=40 Value='" . $_POST['DelAdd3'] . "'></TD></TR>";
	  echo "<TR><TD>Deliver to - Phone:</TD><TD><INPUT TYPE=text NAME=DelAdd4 SIZE=31 MAXLENGTH=30 Value='" . $_POST['DelAdd4'] . "'></TD></TR>";
	  echo "</TABLE>"; /* end of sub table */

	  {
	  echo "</TD><TD>"; /*sub table nested */
	  echo "<TABLE><TR><TD>Originally Ordered:</TD><TD>";
	  if ($_SESSION['ExistingOrder']==1){ echo
		 ConvertSQLDate($_SESSION['PO']->Orig_OrderDate);
	  } else {
	  	/* DefaultDateFormat defined in config.php */
		 echo Date($DefaultDateFormat);
	  }

	  echo "</TD></TR>";
	  echo "<TR><TD>Initiated By:</TD><TD><INPUT TYPE=TEXT NAME='Initiator' SIZE=11 MAXLENGTH=10 VALUE=" . $_POST['Initiator'] . "></TD></TR>";
	  echo "<TR><TD>Requistion Ref:</TD><TD><INPUT TYPE=TEXT NAME='Requisition' SIZE=16 MAXLENGTH=15 VALUE=" . $_POST['Requisition'] . "></TD></TR>";

	  echo "<TR><TD>Exchange Rate:</TD><TD><INPUT TYPE=TEXT NAME='ExRate' SIZE=16 MAXLENGTH=15 VALUE=" . $_POST['ExRate'] . "></TD></TR>";
	  echo "<TR><TD>Date Printed:</TD><TD>";

	  if (isset($_SESSION['PO']->DatePurchaseOrderPrinted) AND strlen($_SESSION['PO']->DatePurchaseOrderPrinted)>6){
	     echo ConvertSQLDate($_SESSION['PO']->DatePurchaseOrderPrinted);
	     $Printed = True;		 
	  } else {
	     $Printed = False;	  
	     echo "Not Yet Printed";
	  }

	  if ($_SESSION['PO']->AllowPrintPO==0 AND $_POST['RePrint']!=1){
	     echo "<TR><TD>Allow Reprint:</TD><TD><SELECT NAME='RePrint'><OPTION SELECTED VALUE=0>No<OPTION VALUE=1>Yes</SELECT></TD></TR>";
	  } elseif ($Printed) {
	     echo "<TR><TD COLSPAN=2 ALIGN=CENTER><A target='_blank'  HREF='$rootpath/PO_PDFPurchOrder.php?" . SID . "OrderNo=" . $_SESSION['ExistingOrder'] . "'>Reprint Now</A></TD></TR>";
	  }
	  echo "</TD></TR></TABLE>"; /*end of sub table */
	  }
 	  echo "</TD></TR><TR><TD VALIGN=TOP COLSPAN=2>Comments:";
	  echo "<textarea name='Comments' cols=70 rows=2>" . $_POST['Comments'] . "</textarea>";
	  echo "</TD></TR></TABLE>"; /* end of main table */
	  echo "<INPUT TYPE=SUBMIT Name='EnterLines' VALUE='Enter Line Items'><INPUT TYPE=SUBMIT Name='ChangeSupplier' VALUE='Change supplier'><BR><BR><INPUT TYPE=SUBMIT NAME='CancelOrder' VALUE='Cancel and Delete The Whole Order'>";

} /*end of if supplier selected */

echo "</form>";
include("includes/footer.inc");
?>

