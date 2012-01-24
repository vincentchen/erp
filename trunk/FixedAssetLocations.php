<?php

/* $Id$ */

include('includes/session.inc');
$title = _('Fixed Asset Locations');
include('includes/header.inc');
echo '<p class="page_title_text">
		<img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $title.'
	</p>';

if (isset($_POST['submit']) AND !isset($_POST['delete'])) {
	$InputError=0;
	if (!isset($_POST['LocationID']) OR mb_strlen($_POST['LocationID'])<1) {
		prnMsg(_('You must enter at least one character in the location ID'),'error');
		$InputError=1;
	}
	if (!isset($_POST['LocationDescription']) OR mb_strlen($_POST['LocationDescription'])<1) {
		prnMsg(_('You must enter at least one character in the location description'),'error');
		$InputError=1;
	}
	if ($InputError==0) {
		$sql="INSERT INTO fixedassetlocations
							VALUES ('".$_POST['LocationID']."',
									'".$_POST['LocationDescription']."',
									'".$_POST['ParentLocationID']."')";
		$result=DB_query($sql, $db);
	}
}
if (isset($_GET['SelectedLocation'])) {
	$sql="SELECT * FROM fixedassetlocations 
		WHERE locationid='".$_GET['SelectedLocation']."'";
	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);
	$LocationID = $myrow['locationid'];
	$LocationDescription = $myrow['locationdescription'];
	$ParentLocationID = $myrow['parentlocationid'];

} else {
	$LocationID = '';
	$LocationDescription = '';
}

//Attempting to update fields

if (isset($_POST['update']) and !isset($_POST['delete'])) {
		$InputError=0;
		if (!isset($_POST['LocationDescription']) or mb_strlen($_POST['LocationDescription'])<1) {
			prnMsg(_('You must enter at least one character in the location description'),'error');
			$InputError=1;
		}
		if ($InputError==0) {
			 $sql="UPDATE fixedassetlocations 
					SET locationdescription='" . $_POST['LocationDescription'] . "',
						parentlocationid='" . $_POST['ParentLocationID'] . "'
					WHERE locationid ='" . $_POST['LocationID'] . "'";
					
			 $result=DB_query($sql,$db);
			 echo '<meta http-equiv="Refresh" content="0; url="'.htmlspecialchars($_SERVER['PHP_SELF']).'">';
		}
} else {
	// if you are not updating then you want to delete but lets be sure first.
	if (isset($_POST['delete']))  {
		$InputError=0;

		$sql="SELECT COUNT(locationid) FROM fixedassetlocations WHERE parentlocationid='" . $_POST['LocationID']."'";
		$result = DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
		if ($myrow[0]>0) {
			prnMsg(_('This location has child locations so cannot be removed'), 'warning');
			$InputError=1;
		}
		$sql="SELECT COUNT(assetid) FROM fixedassets WHERE assetlocation='" . $_POST['LocationID']."'";
		$result = DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
		if ($myrow[0]>0) {
			prnMsg(_('You have assets in this location location so it cannot be removed'), 'warn');
			$InputError=1;
		}
		if ($InputError==0) {
			$sql = "DELETE FROM fixedassetlocations WHERE locationid = '".$_POST['LocationID']."'";
			$result = DB_query($sql,$db);
			prnMsg(_('The location has been deleted successfully'), 'success');
		}
	}
}

$sql='SELECT * FROM fixedassetlocations';
$result=DB_query($sql, $db);

if (DB_num_rows($result) > 0) {
	echo '<table class="selection">
		<tr>
			<th>'._('Location ID').'</th>
			<th>'._('Location Description').'</th>
			<th>'._('Parent Location').'</th>
		</tr>';
}
while ($myrow=DB_fetch_array($result)) {
	echo '<tr>
			<td>' . $myrow['locationid'] . '</td>
			<td>' . $myrow['locationdescription'] . '</td>';
	$ParentSql="SELECT locationdescription FROM fixedassetlocations WHERE locationid='".$myrow['parentlocationid']."'";
	$ParentResult=DB_query($ParentSql, $db);
	$ParentRow=DB_fetch_array($ParentResult);
	echo '<td>' . $ParentRow['locationdescription'] . '</td>
		<td><a href="'.htmlspecialchars($_SERVER['PHP_SELF']) . '?SelectedLocation='.$myrow['locationid'].'">' .  _('Edit') . '</td>';
}

echo '</table>
	<br />';
echo '<form name="LocationForm" method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) .  '">
	<table class="selection">
	<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
	<tr>
		<th style="text-align:left">'._('Location ID').'</th>';
if (isset($_GET['SelectedLocation'])) {
	echo '<input type="hidden" name="LocationID" value="'.$LocationID.'" />';
	echo '<td>'.$LocationID.'</td>';
} else {
	echo '<td><input type="text" name="LocationID" size="6" value="'.$LocationID.'" /></td>
		</tr>';
}

echo '<tr>
		<th style="text-align:left">'._('Location Description').'</th>
		<td><input type="text" name="LocationDescription" size="20" value="'.$LocationDescription.'" /></td>
	</tr>
	<tr>
		<th style="text-align:left">'._('Parent Location').'</th>
		<td><select name="ParentLocationID">';

$sql="SELECT locationid, locationdescription FROM fixedassetlocations";
$result=DB_query($sql, $db);

echo '<option value=""></option>';
while ($myrow=DB_fetch_array($result)) {
	if ($myrow['locationid']==$ParentLocationID) {
		echo '<option selected="selected" value="' . $myrow['locationid'] . '">' . $myrow['locationdescription'].'</option>';
	} else {
		echo '<option value="' . $myrow['locationid'] . '">' . $myrow['locationdescription'] . '</option>';
	}
}
echo '</select></td>
	</tr>
	</table>
	<br />';

echo '<div class="centre">';
if (isset($_GET['SelectedLocation'])) {
	echo '<input type="Submit" name="update" value="' . _('Update Information') . '" />';
	echo '<br />
		<br />
		<<input type="Submit" name="delete" value="' . _('Delete This Location') . '" />';
} else {
	echo '<input type="submit" name="submit" value="' . _('Enter Information') . '" />';
}
echo '</div>
	</form>';

include('includes/footer.inc');
?>