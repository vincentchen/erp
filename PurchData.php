<?php
/* $Revision: 1.3 $ */

$PageSecurity = 4;

include('includes/session.inc');

$title = _('Supplier Purchasing Data');

include('includes/header.inc');
include('includes/DateFunctions.inc');


if (isset($_GET['SupplierID'])){
	$SupplierID = strtoupper($_GET['SupplierID']);
} elseif (isset($_POST['SupplierID'])){
	$SupplierID = strtoupper($_POST['SupplierID']);
}

if (isset($_GET['StockID'])){
	$StockID = strtoupper($_GET['StockID']);
} elseif (isset($_POST['StockID'])){
	$StockID = strtoupper($_POST['StockID']);
}


if (isset($SupplierID) AND $SupplierID!=""){			   /*NOT EDITING AN EXISTING BUT SUPPLIER SELECTED OR ENTERED*/
   $sql = "SELECT SuppName, CurrCode FROM Suppliers WHERE SupplierID='$SupplierID'";

   $ErrMsg = _('The supplier details for the selected supplier could not be retrieved because');
   $DbgMsg = _('The SQL that failed was');
   $SuppSelResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

   if (DB_num_rows($SuppSelResult) ==1){
		$myrow = DB_fetch_array($SuppSelResult);
		$SuppName = $myrow['SuppName'];
		$CurrCode = $myrow['CurrCode'];
   } else {
		echo '</CENTER><BR><FONT COLOR=RED>' . _('WARNING: The supplier code') . ' ' . $SupplierID . ' ' . _('is not an existing supplier in the database. You must enter an alternative supplier code or select a supplier using the search facility below') . '<CENTER>';
		unset($SupplierID);
   }
}

if ((isset($_POST['AddRecord']) OR isset($_POST['UpdateRecord'])) AND isset($SupplierID)){	      /*Validate Inputs */
   $InputError = 0; /*Start assuming the best */
   if ($StockID=="" OR !isset($StockID)){
      $InputError=1;
      echo '<BR><FONT COLOR=RED>' . _('There is no stock item set up enter the stock code or select a stock item using the search page') . '.</FONT>';
   }
   if (! is_numeric($_POST['Price']) OR $_POST['Price']==0){
      $InputError =1;
      unset($_POST['Price']);
      echo '<BR><FONT COLOR=RED>' . _('The price entered was not numeric (a number is expected) - no changes have been made to the database') . '</FONT>';
   }
   if (! is_numeric($_POST['LeadTime'])){
      $InputError =1;
      unset($_POST['LeadTime']);
      echo '<BR><FONT COLOR=RED>' . _('The lead time entered was not numeric (a number is expected) - no changes have been made to the database') . '</FONT>';
   }
   if (!is_numeric($_POST['ConversionFactor'])){
      $InputError =1;
      unset($_POST['ConversionFactor']);
      echo '<BR><FONT COLOR=RED>' . _('The conversion factor entered was not numeric (a number is expected). The conversion factor is the number by which the price must be divided by to get the unit price in our unit of measure') . '. <BR>' . _('Eg. The supplier sells an item by the tonne and we hold stock by the kg. The suppliers price must be divided by 1000 to get to our cost per kg. The conversion factor to enter is 1000') . '. <BR><BR>' . _('No changes will be made to the database') . '</FONT>';
   }


   if ($InputError==0 AND isset($_POST['AddRecord'])){

      $sql = "INSERT INTO PurchData (SupplierNo,
					StockID,
					Price,
					SuppliersUOM,
					ConversionFactor,
					SupplierDescription,
					LeadTime,
					Preferred)
			VALUES ('" . $SupplierID . "',
				'" . $StockID . "',
				" . $_POST['Price'] . ",
				'" . $_POST['SuppliersUOM'] . "',
				" . $_POST['ConversionFactor'] . ",
				'" . $_POST['SupplierDescription'] . "',
				" . $_POST['LeadTime'] . ",
				" . $_POST['Preferred'] . ")";

	$ErrMsg = _('The supplier purchasing details could not be added to the database because');
	$DbgMsg = _('The SQL that failed was');
	$AddResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

	prnMsg( _('This supplier purchasing data has been added to the database'),'success');

   }
   if ($InputError==0 AND isset($_POST['UpdateRecord'])){

      $sql = "UPDATE PurchData SET
			        Price=" . $_POST['Price'] . ",
				SuppliersUOM='" . $_POST['SuppliersUOM'] . "',
				ConversionFactor=" . $_POST['ConversionFactor'] . ",
				SupplierDescription='" . $_POST['SupplierDescription'] . "',
				LeadTime=" . $_POST['LeadTime'] . ",
				Preferred=" . $_POST['Preferred'] . "
		WHERE StockID='$StockID'
		AND SupplierNo='$SupplierID'";


     $ErrMsg = _('The supplier purchasing details could not be update because');
     $DbgMsg = _('The SQL that failed was');

     $UpdResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

     prnMsg (_('Supplier purchasing data has been updated'),'success');

   }

   if ($InputError==0 AND (isset($_POST['UpdateRecord']) OR isset($_POST['AddRecord']))){
      /*update or insert took place and need to clear the form  */
      unset($SupplierID);
      unset($_POST['Price']);
      unset($CurrCode);
      unset($_POST['SuppliersUOM']);
      unset($_POST['ConversionFactor']);
      unset($_POST['SupplierDescription']);
      unset($_POST['LeadTime']);
      unset($_POST['Preferred']);
   }
}


if (isset($_GET['Delete'])){

   $sql = "DELETE FROM PurchData WHERE SupplierNo='$SupplierID' AND StockID='$StockID'";
   $ErrMsg =  _('The supplier purchasing details could not be deleted because');
   $DelResult=DB_query($sql,$db,$ErrMsg);

   prnMsg( _('This purchasing data record has been sucessfully deleted'),'success');
   unset ($SupplierID);
}


$result = DB_query("SELECT Description, Units, MBflag FROM StockMaster WHERE StockID='$StockID'",$db);
$myrow = DB_fetch_row($result);
if (DB_num_rows($result)==1){
   if ($myrow[2]=="D" OR $myrow[2]=="A" OR $myrow[2]=="K"){
	echo '<P><FONT SIZE=3><B>' . $StockID - $myrow[0] . ' </B><P> ' . _('The part selected is a dummy part or an assembly/kit set part - it is not purchased. Entry of purchasing information is therefore inappropriate') . '.</FONT><HR>';
	exit;
   } else {
	echo '<BR><FONT COLOR=BLUE SIZE=3><B>' . $StockID - $myrow[0] . ' </B>  (' . _('In Units of') . ' ' . $myrow[1] . ' )</FONT>';
   }
} else {
  echo '<BR><FONT COLOR=RED SIZE=3><B>' . _('Stock Item') . ' - ' . $StockID . ' ' . _('is not defined in the database') . '</B></FONT><BR>';
}

echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';
echo _('Stock Code') . ':<input type=text name="StockID" value="' . $StockID . '" size=21 maxlength=20>';
echo '    <INPUT TYPE=SUBMIT NAME="ShowSupplierDetails" VALUE="' . _('Show Suppliers') . '">';
echo '    <A HREF="' . $rootpath . '/SelectProduct.php?' . SID . '">' . _('Select Product') . '</A>';
echo '<HR><CENTER>';

if (!isset($_GET['Edit'])){
   $sql = "SELECT  SupplierNo,
			SuppName,
			Price,
			CurrCode,
			SuppliersUOM,
			SupplierDescription,
			LeadTime,
			Preferred
			FROM PurchData INNER JOIN Suppliers ON PurchData.SupplierNo=Suppliers.SupplierID
			WHERE StockID = '" . $StockID . "'";

   $ErrMsg =  _('The supplier purchasing details for the selected part could not be retrieved because');
   $PurchDataResult = DB_query($sql, $db,$ErrMsg);


   if (DB_num_rows($PurchDataResult)==0){
      echo _('There is no purchasing data set up for the part selected');
   } else {

     echo '<TABLE CELLPADDING=2 BORDER=2>';
     $TableHeader = '<TR><TD class="tableheader">' . _('Supplier') . '</TD>
     			<TD class="tableheader">' . _('Price') . '</TD>
			<TD class="tableheader">' . _('Currency') . '</TD>
			<TD class="tableheader">' . _('Supplier Unit') . '</TD>
			<TD class="tableheader">' . _('Lead Time') . '</TD>
			<TD class="tableheader">' . _('Preferred') . '</TD>
		</TR>';

     echo $TableHeader;

     $CountPreferreds =0;
     $k=0; //row colour counter

     while ($myrow=DB_fetch_array($PurchDataResult)) {
	if ($myrow["Preferred"]==1){
	     echo '<tr bgcolor="$BGColour">';
	} elseif ($k==1){
		echo '<tr bgcolor="#CCCCCC">';
		$k=0;
	} else {
		echo '<tr bgcolor="#EEEEEE">';
		$k++;
	}
	if ($myrow["Preferred"]==1){
	  $DisplayPreferred="Yes";
	  $CountPreferreds++;
	} else {
	  $DisplayPreferred="No";
	}

	printf("<td>%s</td>
	        <td ALIGN=RIGHT>%s</td>
		<td>%s</td><td>%s</td>
		<td ALIGN=RIGHT>%s " . _('days') . "</td>
		<td>%s</td>
		<td><a href='%s?StockID=%s&SupplierID=%s&Edit=1'>" . _('Edit') . "</a></td>
		<td><a href='%s?StockID=%s&SupplierID=%s&Delete=1'>" . _('Delete') . "</a></td>
		</tr>",
		$myrow['SuppName'],
		number_format($myrow['Price'],2),
		$myrow['CurrCode'],
		$myrow['SuppliersUOM'],
		$myrow['LeadTime'],
		$DisplayPreferred,
		$_SERVER['PHP_SELF'],
		$StockID,
		$myrow['SupplierNo'],
		$_SERVER['PHP_SELF'],
		$StockID,
		$myrow['SupplierNo']
		);

	$j++;
	If ($j == 12){
		$j=1;

		echo $TableHeader;

	} //end of page full new headings
    } //end of while loop
    echo '</TABLE>';
    if ($CountPreferreds>1){
	      echo '<BR><B><FONT COLOR=RED>' . _('WARNING: There are now') . ' ' . $CountPreferreds . ' ' . _('preferred suppliers set up for') . ' ' . $StockID . ' ' . _('you should edit the supplier purchasing data to make only one supplier the preferred supplier') . '</B></FONT>';
    } elseif($CountPreferreds==0){
	      echo '<BR><B><FONT COLOR=RED>' . _('WARNING: There are NO preferred suppliers set up for') . ' ' . $StockID . ' ' . _('you should make one supplier only the preferred supplier') . '</B></FONT>';
    }
  } // end of there are purchsing data rows to show
  echo '<HR>';
} /* Only show the existing purchasing data records if one is not being edited */


/*Show the input form for new supplier purchasing details */

if (isset($_GET['Edit'])){

	$sql = "SELECT SupplierNo,
				SuppName,
				Price,
				CurrCode,
				SuppliersUOM,
				SupplierDescription,
				LeadTime,
				ConversionFactor,
				Preferred
		FROM PurchData INNER JOIN Suppliers ON PurchData.SupplierNo=Suppliers.SupplierID
		WHERE PurchData.SupplierNo='$SupplierID'
		AND PurchData.StockID='$StockID'";

	$ErrMsg = _('The supplier purchasing details for the selected supplier and item could not be retrieved because');
	$EditResult = DB_query($sql, $db, $ErrMsg);

	$myrow = DB_fetch_array($EditResult);

	$SuppName = $myrow['SuppName'];
	$_POST['Price'] = $myrow['Price'];
	$CurrCode = $myrow['CurrCode'];
	$_POST['SuppliersUOM'] = $myrow['SuppliersUOM'];
	$_POST['SupplierDescription'] = $myrow['SupplierDescription'];
	$_POST['LeadTime'] = $myrow['LeadTime'];
	$_POST['ConversionFactor'] = $myrow['ConversionFactor'];
	$_POST['Preferred'] = $myrow['Preferred'];

}

echo '<TABLE>';

if (isset($_GET['Edit'])){
    echo '<TR><TD>' . _('Supplier Code') . ':</TD><TD><INPUT TYPE=HIDDEN NAME="SupplierID" VALUE="' . $SupplierID . '">' . $SupplierID . ' - ' . $SuppName . '</TD></TR>';
} else {
    echo '<TR><TD>' . _('Supplier Code') . ':</TD><TD><INPUT TYPE=TEXT NAME="SupplierID" MAXLENGTH=10 SIZE=11 VALUE="' . $SupplierID . '">';
    if (!isset($SuppName) OR $SuppName=""){
	echo '<FONT SIZE=1>' . '(' . _('A search facility is available below if necessary') . ')';
    } else {
	echo $SuppName;
    }
    echo '</TD></TR>';
}

echo '<TR><TD>' . _('Currency') . ':</TD><TD><INPUT TYPE=HIDDEN NAME="CurrCode" . VALUE="' . $CurrCode . '">' . $CurrCode . '</TD></TR>';
echo '<TR><TD>' . _('Price (in Supplier Currency)') . ':</TD><TD><INPUT TYPE=TEXT NAME="Price" MAXLENGTH=12 SIZE=12 VALUE=' . $_POST['Price'] . '></TD></TR>';
echo '<TR><TD>' . _('Suppliers Unit of Measure') . ':</TD><TD><INPUT TYPE=TEXT NAME="SuppliersUOM" MAXLENGTH=50 SIZE=51 VALUE="' . $_POST['SuppliersUOM'] . '"></TD></TR>';
if (!isset($_POST['ConversionFactor']) OR $_POST['ConversionFactor']==""){
   $_POST['ConversionFactor']=1;
}
echo '<TR><TD>' . _('Conversion Factor (to our UOM)') . ':</TD><TD><INPUT TYPE=TEXT NAME="ConversionFactor" MAXLENGTH=12 SIZE=12 VALUE=' . $_POST['ConversionFactor'] . '></TD></TR>';
echo '<TR><TD>' . _('Supplier Code or Description') . ':</TD><TD><INPUT TYPE=TEXT NAME="SupplierDescription" MAXLENGTH=50 SIZE=51 VALUE="' . $_POST['SupplierDescription'] . '"></TD></TR>';
if (!isset($_POST['LeadTime']) OR $_POST['LeadTime']==""){
   $_POST['LeadTime']=1;
}
echo '<TR><TD>' . _('Lead Time (in days from date of order)') . ':</TD><TD><INPUT TYPE=TEXT NAME="LeadTime" MAXLENGTH=10 SIZE=11 VALUE=' . $_POST['LeadTime'] . '></TD></TR>';
echo '<TR><TD>' . _('Preferred Supplier') . ':</TD><TD><SELECT NAME="Preferred">';

if ($_POST['Preferred']==1){
	echo '<OPTION SELECTED VALUE=1>' . _('Yes');
	echo '<OPTION VALUE=0>' . _('No');
} else {
	echo '<OPTION VALUE=1>' . _('Yes');
	echo '<OPTION SELECTED VALUE=0>' . _('No');
}
echo '</SELECT></TD></TR></TABLE>';

if (isset($_GET['Edit'])){
   echo '<INPUT TYPE=SUBMIT NAME="UpdateRecord" VALUE="' . _('Update') . '">';
} else {
   echo '<INPUT TYPE=SUBMIT NAME="AddRecord" VALUE="' . _('Add') . '">';
}

echo '<HR>';

if (isset($_POST['SearchSupplier'])){

	If (isset($_POST['Keywords']) AND isset($_POST['SupplierCode'])) {
		$msg=_('Supplier name keywords have been used in preference to the Supplier code extract entered') . '.';
	}
	If ($_POST['Keywords']=="" AND $_POST['SupplierCode']=="") {
		$msg=_('At least one Supplier name keyword OR an extract of a Supplier code must be entered for the search');
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

			$SQL = "SELECT SupplierID,
					SuppName,
					CurrCode,
					Address1,
					Address2,
					Address3
					FROM Suppliers WHERE SuppName LIKE '$SearchString'";

		} elseif (strlen($_POST['SupplierCode'])>0){
			$SQL = "SELECT SupplierID,
					SuppName,
					CurrCode,
					Address1,
					Address2,
					Address3
				FROM Suppliers
				WHERE SupplierID LIKE '%" . $_POST['SupplierCode'] . "%'";
		}

		$ErrMsg = _('The suppliers matching the criteria entered could not be retrieved because');
		$DbgMsg =  _('The SQL to retireve supplier details that failed was');
		$SuppliersResult = DB_query($SQL,$db,$ErrMsg,$DbgMsg);

	} //one of keywords or SupplierCode was more than a zero length string
} //end of if search

?>

<B><?php echo '<BR>' . $msg; ?></B>
<TABLE CELLPADDING=3 COLSPAN=4>
<TR>
<TD><?php echo _('Text in the Supplier'); ?> <B><?php echo _('NAME'); ?></B>:</FONT></TD>
<TD><INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25></TD>
<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
<TD><?php echo _('Text in Supplier'); ?> <B><?php echo _('CODE'); ?></B>:</FONT></TD>
<TD><INPUT TYPE="Text" NAME="SupplierCode" SIZE=15 MAXLENGTH=18></TD>
</TR>
</TABLE>
<CENTER><INPUT TYPE=SUBMIT NAME="SearchSupplier" VALUE="<?php echo _('Find Suppliers Now'); ?>">
<INPUT TYPE=SUBMIT ACTION=RESET VALUE="<?php echo _('Reset'); ?>"></CENTER>
<HR>

<?php

If (isset($SuppliersResult)) {

	echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';
	$TableHeader = '<TR><TD class="tableheader">' . _('Code') . '</TD>
	                	<TD class="tableheader">' . _('Supplier Name') . '</TD>
				<TD class="tableheader">' . _('Currency') . '</TD>
				<TD class="tableheader">' . _('Address 1') . '</TD>
				<TD class="tableheader">' . _('Address 2') . '</TD>
				<TD class="tableheader">' . _('Address 3') . '</TD>
			</TR>';
	echo $TableHeader;

	$j = 1;

	while ($myrow=DB_fetch_array($SuppliersResult)) {

		printf("<tr><td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='SupplierID' VALUE='%s'</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
				<td><FONT SIZE=1>%s</FONT></td>
			</tr>",
			$myrow['SupplierID'],
			$myrow['SuppName'],
			$myrow['CurrCode'],
			$myrow['Address1'],
			$myrow['Address2'],
			$myrow['Address3']
			);

		$j++;
		If ($j == 11){
			$j=1;
			echo $TableHeader;
		}
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}
//end if results to show


if (isset($StockID) AND strlen($StockID)!=0){
   echo '<BR><A HREF="' . $rootpath . '/StockStatus.php?' . SID . '&StockID=' . $StockID . '">' . _('Show Stock Status') . '</A>';
   echo '<BR><A HREF="' . $rootpath . '/StockMovements.php?' . SID . '&StockID=' . $StockID . '&StockLocation=' . $StockLocation . '">' . _('Show Stock Movements') . '</A>';
   echo '<BR><A HREF="' . $rootpath . '/SelectSalesOrder.php?' . SID . '&SelectedStockItem=' . $StockID . '&StockLocation=' . $StockLocation . '">' . _('Search Outstanding Sales Orders') . '</A>';
   echo '<BR><A HREF="' . $rootpath . '/SelectCompletedOrder.php?' .SID . '&SelectedStockItem=' . $StockID . '">' . _('Search Completed Sales Orders') . '</A>';
}

echo '</form></center>';
include('includes/footer.inc');
?>