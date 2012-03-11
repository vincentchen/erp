<?php

/* $Id$*/

include('includes/session.inc');

$title = _('Change Asset Location');

include('includes/header.inc');

foreach ($_POST as $AssetToMove => $Value) { //Value is not used?
	if (mb_substr($AssetToMove,0,4)=='Move') { // the form variable is of the format MoveAssetID so need to strip the move bit off
		$AssetID=mb_substr($AssetToMove,4);
		$sql="UPDATE fixedassets
					SET assetlocation='".$_POST['Location'.$AssetID] ."'
					WHERE assetid='". $AssetID . "'";

		$result=DB_query($sql, $db);
	}
}

if (isset($_GET['AssetID'])) {
	$AssetID=$_GET['AssetID'];
} else if (isset($_POST['AssetID'])) {
	$AssetID=$_POST['AssetID'];
} else {
	$sql="SELECT categoryid, categorydescription FROM fixedassetcategories";
	$result=DB_query($sql, $db);
	echo '<form action="'. htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/magnifier.png" title="' . _('Search') .
		'" alt="" />' . ' ' . $title . '</p>';
	echo '<table class="selection"><tr>';
	echo '<td>'. _('In Asset Category') . ': ';
	echo '<select name="AssetCat">';

	if (!isset($_POST['AssetCat'])) {
		$_POST['AssetCat'] = '';
	}

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['categoryid'] == $_POST['AssetCat']) {
			echo '<option selected="selected" value="' . $myrow['categoryid'] . '">' . $myrow['categorydescription'] . '</option>';
		} else {
			echo '<option value="' . $myrow['categoryid'] . '">' . $myrow['categorydescription'] . '</option>';
		}
	}

	echo '</select>';
	echo '<td>'. _('Enter partial') . '<b> ' . _('Description') . '</b>:</td><td>';


	if (isset($_POST['Keywords'])) {
		echo '<input type="text" name="Keywords" value="' . trim($_POST['Keywords'],'%') . '" size="20" maxlength="25" />';
	} else {
		echo '<input type="text" name="Keywords" size="20" maxlength="25" />';
	}

	echo '</td></tr><tr><td></td>';

	echo '<td><b>' . _('OR').' ' . '</b>' . _('Enter partial') .' <b>'. _('Asset Code') . '</b>:</td>';
	echo '<td>';

	if (isset($_POST['AssetID'])) {
		echo '<input type="text" name="AssetID" value="'. trim($_POST['AssetID'],'%') . '" size="15" maxlength="18" />';
	} else {
		echo '<input type="text" name="AssetID" size="15" maxlength="18" />';
	}

	echo '</td></tr></table><br />';

	echo '<div class="centre"><input type="submit" name="Search" value="'. _('Search Now') . '" /></div></form><br />';
}

if (isset($_POST['Search'])) {
	if ($_POST['AssetCat']=='All') {
		$_POST['AssetCat']='%';
	}
	if (isset($_POST['Keywords'])) {
		$_POST['Keywords']='%'.$_POST['Keywords'].'%';
	} else {
		$_POST['Keywords']='%';
	}
	if (isset($_POST['AssetID'])) {
		$_POST['AssetID']='%'.$_POST['AssetID'].'%';
	} else {
		$_POST['AssetID']='%';
	}
	
	$sql= "SELECT fixedassets.assetid,
					fixedassets.cost,
					fixedassets.accumdepn,
					fixedassets.description,
					fixedassets.depntype,
					fixedassets.serialno,
					fixedassets.barcode,
					fixedassets.assetlocation,
					fixedassetlocations.locationdescription
				FROM fixedassets
				INNER JOIN fixedassetlocations
				ON fixedassets.assetlocation=fixedassetlocations.locationid
				WHERE fixedassets.assetcategoryid " . LIKE . "'".$_POST['AssetCat']."'
				AND fixedassets.description " . LIKE . "'".$_POST['Keywords']."'
				AND fixedassets.assetid " . LIKE . "'".$_POST['AssetID']."'
				AND fixedassets.serialno " . LIKE . "'".$_POST['SerialNumber']."'";
	$Result=DB_query($sql, $db);
	echo '<form action="'. htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
			<table class="selection">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<tr>
			<th>'._('Asset ID') . '</th>
			<th>' . _('Description') . '</th>
			<th>' . _('Serial number') . '</th>
			<th>' . _('Purchase Cost') . '</th>
			<th>' . _('Total Depreciation') . '</th>
			<th>' . _('Current Location') . '</th>
			<th>' . _('Move To :') . '</th>
		</tr>';

	$locationsql="SELECT locationid, locationdescription from fixedassetlocations";
	$LocationResult=DB_query($locationsql, $db);

	while ($myrow=DB_fetch_array($Result)) {

		echo '<tr>
				<td>'.$myrow['assetid'].'</td>
				<td>'.$myrow['description'].'</td>
				<td>'.$myrow['serialno'].'</td>
				<td class="number">'.locale_number_format($myrow['cost'],$_SESSION['CompanyRecord']['decimalplaces']).'</td>
				<td class="number">'.locale_number_format($myrow['accumdepn'],$_SESSION['CompanyRecord']['decimalplaces']).'</td>
				<td>'.$myrow['locationdescription'].'</td>';
		echo '<td><select name="Location'.$myrow['assetid'].'" onchange="ReloadForm(Move'.$myrow['assetid'].')">';
		echo '<option></option>';
		while ($LocationRow=DB_fetch_array($LocationResult)) {
			if ($LocationRow['locationid']==$myrow['location']) {
				echo '<option selected="selected" value="'.$LocationRow['locationid'].'">'.$LocationRow['locationdescription'] . '</option>';
			} else {
				echo '<option value="'.$LocationRow['locationid'].'">'.$LocationRow['locationdescription'].'</option>';
			}
		}
		DB_data_seek($LocationResult,0);
		echo '</select></td>';
		echo '<input type="hidden" name="AssetCat" value="' . $_POST['AssetCat'].'" />';
		echo '<input type="hidden" name="Keywords" value="' . $_POST['Keywords'].'" />';
		echo '<input type="hidden" name="AssetID" value="' . $_POST['AssetID'].'" />';
		echo '<input type="hidden" name="Search" value="' . $_POST['Search'].'" />';
		echo '<td><input type="submit" name="Move'.$myrow['assetid'].'" value="Move" /></td>';
		echo '</tr>';
	}
	echo '</table></form>';
}

include('includes/footer.inc');

?>