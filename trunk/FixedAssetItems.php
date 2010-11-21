<?php

/* $Id$ */
/* $Revision: 1.3 $ */

$PageSecurity = 11;

include('includes/session.inc');
$title = _('Fixed Assets');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

echo '<a href="' . $rootpath . '/SelectAsset.php?' . SID . '">' . _('Back to Select') . '</a><br>' . "\n";

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/money_add.png" title="' .
		_('Fixed Asset Items') . '" alt="">' . ' ' . $title . '</p>';

/* If this form is called with the AssetID then it is assumed that the asset is to be modified  */
if (isset($_GET['AssetID'])){
	$AssetID =$_GET['AssetID'];
} elseif (isset($_POST['AssetID'])){
	$AssetID =$_POST['AssetID'];
} elseif (isset($_POST['Select'])){
	$AssetID =$_POST['Select'];
} else {
	$AssetID = '';
}

if (isset($_FILES['ItemPicture']) AND $_FILES['ItemPicture']['name'] !='') {

	$result    = $_FILES['ItemPicture']['error'];
 	$UploadTheFile = 'Yes'; //Assume all is well to start off with
	$filename = $_SESSION['part_pics_dir'] . '/ASSET_' . $AssetID . '.jpg';

	 //But check for the worst
	if (strtoupper(substr(trim($_FILES['ItemPicture']['name']),strlen($_FILES['ItemPicture']['name'])-3))!='JPG'){
		prnMsg(_('Only jpg files are supported - a file extension of .jpg is expected'),'warn');
		$UploadTheFile ='No';
	} elseif ( $_FILES['ItemPicture']['size'] > ($_SESSION['MaxImageSize']*1024)) { //File Size Check
		prnMsg(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $_SESSION['MaxImageSize'],'warn');
		$UploadTheFile ='No';
	} elseif ( $_FILES['ItemPicture']['type'] == "text/plain" ) {  //File Type Check
		prnMsg( _('Only graphics files can be uploaded'),'warn');
         	$UploadTheFile ='No';
	} elseif (file_exists($filename)){
		prnMsg(_('Attempting to overwrite an existing item image'),'warn');
		$result = unlink($filename);
		if (!$result){
			prnMsg(_('The existing image could not be removed'),'error');
			$UploadTheFile ='No';
		}
	}

	if ($UploadTheFile=='Yes'){
		$result  =  move_uploaded_file($_FILES['ItemPicture']['tmp_name'], $filename);
		$message = ($result)?_('File url') ."<a href='". $filename ."'>" .  $filename . '</a>' : _('Something is wrong with uploading a file');
	}
 /* EOR Add Image upload for New Item  - by Ori */
}

if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();
$InputError = 0;

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$i=1;


	if (!isset($_POST['Description']) or strlen($_POST['Description']) > 50 OR strlen($_POST['Description'])==0) {
		$InputError = 1;
		prnMsg (_('The asset description must be entered and be fifty characters or less long. It cannot be a zero length string either, a description is required'),'error');
		$Errors[$i] = 'Description';
		$i++;
	}
	if (strlen($_POST['LongDescription'])==0) {
		$InputError = 1;
		prnMsg (_('The asset long description cannot be a zero length string, a long description is required'),'error');
		$Errors[$i] = 'LongDescription';
		$i++;
	}
	
	if (strlen($_POST['BarCode']) >20) {
		$InputError = 1;
		prnMsg(_('The barcode must be 20 characters or less long'),'error');
		$Errors[$i] = 'BarCode';
		$i++;
	}
	
	if (trim($_POST['AssetCategoryID'])==''){
		$InputError = 1;
		prnMsg(_('There are no asset categories defined. All assets must belong to a valid category,'),'error');
		$Errors[$i] = 'AssetCategoryID';
		$i++;
	}
	if (!is_numeric($_POST['DepnRate']) OR $_POST['DepnRate']>100 OR $_POST['DepnRate']<0){
		$InputError = 1;
		prnMsg(_('The depreciation rate is expected to be a number between 0 and 100'),'error');
		$Errors[$i] = 'DepnRate';
		$i++;
	}
	if (!Is_Date($_POST['DatePurchased'])){
		$InputError = 1;
		prnMsg(_('The date that the asset was purchased must be entered in the format') . ' ' . $SESSION['DefaultDateFormat'],'error');
		$Errors[$i] = 'DatePurchased';
		$i++;
	}
	
	if ($InputError !=1){
		
		if ($_POST['submit']==_('Update')) { /*so its an existing one */

			/*Start a transaction to do the whole lot inside */
			$result = DB_Txn_Begin($db);
		
			/*Need to check if changing the balance sheet codes - as will need to do journals for the cost and accum depn of the asset to the new category */
			$result = DB_query("SELECT assetcategoryid,  cost, accumdepn, costact, accumdepnact FROM fixedassets INNER JOIN fixedassetcategories WHERE assetid='" . $AssetID . "'",$db);
			$OldDetails = DB_fetch_array($result);
			if ($OldDetails['assetcategoryid'] !=$_POST['AssetCategoryID']  AND $OldDetails['cost']!=0){
				
				$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']),$db);
				/* Get the new account codes for the new asset category */
				$result = DB_query("SELECT costact, accumdepnact FROM fixedassetcategories WHERE categoryid='" . $_POST['AssetCategoryID'] . "'",$db);
				$NewAccounts = DB_fetch_array($result);
				
				$TransNo = GetNextTransNo( 42, $db); /* transaction type is asset category change */

				//credit cost for the old category
				$SQL = "INSERT INTO gltrans (type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount) ";
				$SQL= $SQL . "VALUES (42,
							'" . $TransNo . "',
							'" . Date('Y-m-d') . "',
							'" . $PeriodNo . "',
							'" . $OldDetails['costact'] . "',
							'" . $AssetID . ' ' . _('change category') . ' ' . $OldDetails['assetcategoryid'] . ' - ' . $_POST['AssetCategoryID'] . "',
							'" . -$OldDetails['cost']. "'
							)";
				$ErrMsg = _('Cannot insert a GL entry for the change of asset category because');
				$DbgMsg = _('The SQL that failed to insert the cost GL Trans record was');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			
				//debit cost for the new category
				$SQL = "INSERT INTO gltrans (type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount) ";
				$SQL= $SQL . "VALUES (42,
							'" . $TransNo . "',
							'" . Date('Y-m-d') . "',
							'" . $PeriodNo . "',
							'" . $NewAccounts['costact'] . "',
							'" . $AssetID . ' ' . _('change category') . ' ' . $OldDetails['assetcategoryid'] . ' - ' . $_POST['AssetCategoryID'] . "',
							'" . $OldDetails['cost']. "'
							)";
				$ErrMsg = _('Cannot insert a GL entry for the change of asset category because');
				$DbgMsg = _('The SQL that failed to insert the cost GL Trans record was');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				if ($OldDetails['accumdepn']!=0) {
					//debit accumdepn for the old category
					$SQL = "INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount) ";
					$SQL= $SQL . "VALUES (42,
								'" . $TransNo . "',
								'" . Date('Y-m-d') . "',
								'" . $PeriodNo . "',
								'" . $OldDetails['accumdepnact'] . "',
								'" . $AssetID . ' ' . _('change category') . ' ' . $OldDetails['assetcategoryid'] . ' - ' . $_POST['AssetCategoryID'] . "',
								'" . $OldDetails['accumdepn']. "'
								)";
					$ErrMsg = _('Cannot insert a GL entry for the change of asset category because');
					$DbgMsg = _('The SQL that failed to insert the cost GL Trans record was');
					$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				
					//credit accum depn for the new category
					$SQL = "INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount) ";
					$SQL= $SQL . "VALUES (42,
								'" . $TransNo . "',
								'" . Date('Y-m-d') . "',
								'" . $PeriodNo . "',
								'" . $NewAccounts['accumdepnact'] . "',
								'" . $AssetID . ' ' . _('change category') . ' ' . $OldDetails['assetcategoryid'] . ' - ' . $_POST['AssetCategoryID'] . "',
								'" . $OldDetails['accumdepn']. "'
								)";
					$ErrMsg = _('Cannot insert a GL entry for the change of asset category because');
					$DbgMsg = _('The SQL that failed to insert the cost GL Trans record was');
					$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				} /*end if there was accumulated depreciation for the asset */
			} /* end if there is a change in asset category */
			$sql = "UPDATE fixedassets
									SET longdescription='" . $_POST['LongDescription'] . "',
										description='" . $_POST['Description'] . "',
										assetcategoryid='" . $_POST['AssetCategoryID'] . "',
										assetlocation='" . $_POST['AssetLocation'] . "',
										datepurchased='" . FormatDateForSQL($_POST['DatePurchased']) . "',
										depntype='" . $_POST['DepnType'] . "',
										depnrate='" . $_POST['DepnRate'] . "',
										barcode='" . $_POST['BarCode'] . "',
										serialno='" . $_POST['SerialNo'] . "'
									WHERE assetid='" . $AssetID . "'";

			$ErrMsg = _('The asset could not be updated because');
			$DbgMsg = _('The SQL that was used to update the asset and failed was');
			$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

			prnMsg( _('Asset') . ' ' . $AssetID . ' ' . _('has been updated'), 'success');
			echo '<br>';
		} else { //it is a NEW part
			$sql = "INSERT INTO fixedassets (
																		description,
																		longdescription,
																		assetcategoryid,
																		assetlocation,
																		datepurchased,
																		depntype,
																		depnrate,
																		barcode,
																		serialno)
																	VALUES (
																		'" . $_POST['Description'] . "',
																		'" . $_POST['LongDescription'] . "',
																		'" . $_POST['AssetCategoryID'] . "',
																		'" . $_POST['AssetLocation'] . "',
																		'" . FormatDateForSQL($_POST['DatePurchased']) . "',
																		'" . $_POST['DepnType'] . "',
																		'" . $_POST['DepnRate']. "',
																		'" . $_POST['BarCode'] . "',
																		'" . $_POST['SerialNo'] . "'
																		)";
			$ErrMsg =  _('The asset could not be added because');
			$DbgMsg = _('The SQL that was used to add the asset failed was');
			$result = DB_query($sql,$db, $ErrMsg, $DbgMsg);
			
			if (DB_error_no($db) ==0) {
				prnMsg( _('The new asset has been added to the database'),'success');
				unset($_POST['LongDescription']);
				unset($_POST['Description']);
//				unset($_POST['AssetCategoryID']);
//				unset($_POST['AssetLocation']);
				unset($_POST['DatePurchased']);
//				unset($_POST['DepnType']);
//				unset($_POST['DepnRate']);
				unset($_POST['BarCode']);
				unset($_POST['SerialNo']);
			}//ALL WORKED SO RESET THE FORM VARIABLES
			$result = DB_Txn_Commit($db);
		}
	} else {
		echo '<br>'. "\n";
		prnMsg( _('Validation failed, no updates or deletes took place'), 'error');
	}

} elseif (isset($_POST['delete']) AND strlen($_POST['delete']) >1 ) {
//the button to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;
	//what validation is required before allowing deletion of assets ....  maybe there should be no deletion option?
	$result = DB_query('SELECT cost, accumdepn, accumdepnact, costact FROM fixedassets INNER JOIN fixedassetcategories ON fixedassets.assetcategoryid=fixedassetcategories.categoryid WHERE assetid="' . $AssetID . '"', $db);
	$AssetRow = DB_fetch_array($result);
	$NBV = $AssetRow['cost'] -$AssetRow['accumdepn'];
	if ($NBV!=0) {
		$CancelDelete =1; //cannot delete assets where NBV is not 0
	}
	$result = DB_query('SELECT * FROM fixedassettrans WHERE assetid="' . $AssetID . '"',$db);
	if (DB_num_rows($result) > 0){
		$CancelDelete =1; /*cannot delete assets with transactions */
	}
	
	if ($CancelDelete==0) {
		$result = DB_Txn_Begin($db);
		
		/*Need to remove cost and accumulate depreciation from cost and accumdepn accounts */
		$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']),$db);
		$TransNo = GetNextTransNo( 43, $db); /* transaction type is asset deletion - (and remove cost/accumdepn from GL) */
		if ($AssetRow['cost'] > 0){
			//credit cost for the asset deleted
			$SQL = "INSERT INTO gltrans (type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
			$SQL= $SQL . "VALUES (43,
						'" . $TransNo . "',
						'" . Date('Y-m-d') . "',
						'" . $PeriodNo . "',
						'" . $AssetRow['costact'] . "',
						'" . _('Delete asset') . ' ' . $AssetID . "',
						'" . -$AssetRow['cost']. "'
						)";
			$ErrMsg = _('Cannot insert a GL entry for the deletion of the asset because');
			$DbgMsg = _('The SQL that failed to insert the cost GL Trans record was');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		
			//debit accumdepn for the depreciation removed on deletion of this asset
			$SQL = "INSERT INTO gltrans (type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
			$SQL= $SQL . "VALUES (43,
						'" . $TransNo . "',
						'" . Date('Y-m-d') . "',
						'" . $PeriodNo . "',
						'" . $AssetRow['accumdepnact'] . "',
						'" . _('Delete asset') . ' ' . $AssetID . "',
						'" . $Asset['accumdepn']. "'
						)";
			$ErrMsg = _('Cannot insert a GL entry for the reversal of accumulated depreciation on deletion of the asset because');
			$DbgMsg = _('The SQL that failed to insert the cost GL Trans record was');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			
		} //end if cost > 0
		
		
		$sql="DELETE FROM fixedassets WHERE assetid='" . $AssetID . "'";
		$result=DB_query($sql,$db, _('Could not delete the asset record'),'',true);

		$result = DB_Txn_Commit($db);

		prnMsg(_('Deleted the asset  record for asset number' ) . ' ' . $AssetID );
		unset($_POST['LongDescription']);
		unset($_POST['Description']);
		unset($_POST['AssetCategoryID']);
		unset($_POST['AssetLocation']);
		unset($_POST['DatePurchased']);
		unset($_POST['DepnType']);
		unset($_POST['DepnRate']);
		unset($_POST['BarCode']);
		unset($_POST['SerialNo']);
		unset($AssetID);
		unset($_SESSION['SelectedAsset']);

	} //end if OK Delete Asset
} /* end if delete asset */

echo '<form name="AssetForm" enctype="multipart/form-data" method="post" action="' . $_SERVER['PHP_SELF'] . '?' .SID .
	'"><table class=selection>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';


if (!isset($AssetID) or $AssetID=='') {

/*If the page was called without $AssetID passed to page then assume a new asset is to be entered other wise the form showing the fields with the existing entries against the asset will show for editing with a hidden AssetID field. New is set to flag that the page may have called itself and still be entering a new asset, in which case the page needs to know not to go looking up details for an existing asset*/

	$New = 1;
	echo '<input type="hidden" name="New" value="">'. "\n";
	
} elseif ($InputError!=1) { // Must be modifying an existing item and no changes made yet - need to lookup the details

	$sql = "SELECT assetid,
					description,
					longdescription,
					assetcategoryid,
					serialno,
					assetlocation,
					datepurchased,
					depntype,
					depnrate
		FROM fixedassets
		WHERE assetid ='" . $AssetID . "'";

	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);

	$_POST['LongDescription'] = $myrow['longdescription'];
	$_POST['Description'] = $myrow['description'];
	$_POST['AssetCategoryID']  = $myrow['assetcategoryid'];
	$_POST['SerialNo']  = $myrow['serialno'];
	$_POST['AssetLocation']  = $myrow['assetlocation'];
	$_POST['DatePurchased']  = ConvertSQLDate($myrow['DatePurchased']);
	$_POST['DepnType']  = $myrow['depntype'];
	$_POST['BarCode']  = $myrow['barcode'];
	$_POST['DepnRate']  = $myrow['depnrate'];
	
	echo '<tr><td>' . _('Asset Code') . ':</td><td>'.$AssetID.'</td></tr>'. "\n";
	echo '<input type="Hidden" name="AssetID" value='.$AssetID.'>'. "\n";

} else { // some changes were made to the data so don't re-set form variables to DB ie the code above
	echo '<tr><td>' . _('Asset Code') . ':</td><td>' . $AssetID . '</td></tr>';
	echo '<input type="Hidden" name="AssetID" value="' . $AssetID . '">';
}

if (isset($_POST['Description'])) {
	$Description = $_POST['Description'];
} else {
	$Description ='';
}
echo '<tr><td>' . _('Asset Description') . ' (' . _('short') . '):</td>
					<td><input ' . (in_array('Description',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Description" size=52 maxlength=50 value="' . $Description . '"></td></tr>'."\n";

if (isset($_POST['LongDescription'])) {
	$LongDescription = AddCarriageReturns($_POST['LongDescription']);
} else {
	$LongDescription ='';
}
echo '<tr><td>' . _('Asset Description') . ' (' . _('long') . '):</td><td><textarea ' . (in_array('LongDescription',$Errors) ?  'class="texterror"' : '' ) .'  name="LongDescription" cols=40 rows=4>' . stripslashes($LongDescription) . '</textarea></td></tr>'."\n";

if ($New == 0) { //ie not new at all!
	// Add image upload for New Item  - by Ori
	echo '<tr><td>'. _('Image File (.jpg)') . ':</td><td><input type="file" id="ItemPicture" name="ItemPicture"></td>';
	// EOR Add Image upload for New Item  - by Ori

	if (function_exists('imagecreatefromjpg')){
		$StockImgLink = '<img src="GetStockImage.php?SID&automake=1&textcolor=FFFFFF&bgcolor=CCCCCC'.
			'&AssetID='.urlencode($AssetID).
			'&text='.
			'&width=64'.
			'&height=64'.
			'" >';
	} else {
		if( isset($AssetID) and file_exists($_SESSION['part_pics_dir'] . '/ASSET_' .$AssetID.'.jpg') ) {
			$AssetImgLink = '<img src="' . $_SESSION['part_pics_dir'] . '/ASSET_' .$AssetID.'.jpg" >';
		} else {
			$AssetImgLink = _('No Image');
		}
	}
	
	if ($AssetImgLink!=_('No Image')) {
		echo '<td>' . _('Image') . '<br>'.$AssetImgLink . '</td></tr>';
	} else {
		echo '</td></tr>';
	}
	
	// EOR Add Image upload for New Item  - by Ori
} //only show the add image if the asset already exists - otherwise AssetID will not be set - and the image needs the AssetID to save

echo '<tr><td>' . _('Asset Category') . ':</td><td><select name="AssetCategoryID">';

$sql = 'SELECT categoryid, categorydescription FROM fixedassetcategories';
$ErrMsg = _('The asset categories could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve stock categories and failed was');
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

while ($myrow=DB_fetch_array($result)){
	if (!isset($_POST['AssetCategoryID']) or $myrow['categoryid']==$_POST['AssetCategoryID']){
		echo '<option selected VALUE="'. $myrow['categoryid'] . '">' . $myrow['categorydescription'];
	} else {
		echo '<option VALUE="'. $myrow['categoryid'] . '">' . $myrow['categorydescription'];
	}
	$category=$myrow['categoryid'];
}
echo '</select><a target="_blank" href="'. $rootpath . '/FixedAssetCategories.php?' . SID . '">'.' ' . _('Add or Modify Asset Categories') . '</a></td></tr>';
if (!isset($_POST['AssetCategoryID'])) {
	$_POST['AssetCategoryID']=$category;
}

if ($_POST['DatePurchased']==''){
	$_POST['DatePurchased'] = Date($_SESSION['DefaultDateFormat'],mktime(0,0,0,date('m'),0,date('Y')));
}

echo '<tr><td>' . _('Date Purchased') . ':</td><td><input ' . (in_array('DatePurchased',$Errors) ?  'class="inputerror"' : 'class="date"' ) . ' alt="' .$_SESSION['DefaultDateFormat'] . '" type="Text" name="DatePurchased" size=12 maxlength=10 value="' . $_POST['DatePurchased '] . '"></td></tr>';


$sql = 'SELECT locationid, locationdescription FROM fixedassetlocations';
$ErrMsg = _('The asset locations could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve asset locations and failed was');
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

echo '<tr><td>' . _('Asset Location') . ':</td><td><select name="AssetLocation">';
while ($myrow=DB_fetch_array($result)){
	if ($_POST['AssetLocation']==$myrow['locationid']){
		echo '<option selected value="' . $myrow['locationid'] .'">' . $myrow['locationdescription'] . '</option>';
	} else {
		echo '<option value="' . $myrow['locationid'] .'">' . $myrow['locationdescription'] . '</option>';
	}
}
echo '</select></td></tr>';

echo '<tr><td>' . _('Bar Code') . ':</td><td><input ' . (in_array('BarCode',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="BarCode" size=22 maxlength=20 value="' . $BarCode . '"></td></tr>';

echo '<tr><td>' . _('Serial Number') . ':</td><td><input ' . (in_array('SerialNo',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="SerialNo" size=32 maxlength=30 value="' . $_POST['SerialNo'] . '"></td></tr>';


echo '<tr><td>' . _('Depreciation Type') . ':</td><td><select name="DepnType">';

if (!isset($_POST['DepnType'])){
	$_POST['DepnType'] = 0; //0 = Straight line - 1 = Diminishing Value
}
if ($_POST['DepnType']==0){ //straight line
	echo '<option selected value="0">' . _('Straight Line') . '</option>';
	echo '<option value="1">' . _('Diminishing Value') . '</option>';
} else {
	echo '<option value="0">' . _('Straight Line') . '</option>';
	echo '<option selected value="1">' . _('Diminishing Value') . '</option>';
}

echo '</select></td></tr>';

echo '<tr><td>' . _('Depreciation Rate') . ':</td><td><input ' . (in_array('DepnRate',$Errors) ?  'class="inputerror"' : 'class="number"' ) .'  type="Text" name="DepnRate" size=3 maxlength=3 value="' . $_POST['DepnRate'] . '"></td></tr>';

echo '</table>';

if ($New==1) {
	echo '<div class=centre><br><input type="Submit" name="submit" value="' . _('Insert New Fixed Asset') . '">';

} else {
	
	echo '<br><div class=centre><input type="submit" name="submit" value="' . _('Update') . '"></div>';
	prnMsg( _('Only click the Delete button if you are sure you wish to delete the asset. Only assets with a zero book value can be deleted'), 'warn', _('WARNING'));
	echo '<br><div class=centre><input type="Submit" name="delete" value="' . _('Delete This Asset') . '" onclick="return confirm(\'' . _('Are You Sure? Only assets with a zero book value can be deleted.') . '\');"></div>';
}

echo '</form></div>';
include('includes/footer.inc');
?>