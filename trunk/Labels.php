<?php
/**
 *  <b>Labels XML file Managment</b>
 *	This program maintains the XML file containing the label defintions
 *	used to print the products prices tickets
 * @author  Marcos R Garcia <addsmgt at gmail dot com>
 * @version 1.1
 * Last changes: 2010-08-31
 *
 * 
 * Phil's notes:
 * This is an example of clever code that works but IMHO is very hard to decipher and modify - sorry Marcos!
 * It is in webERP because it fulfills a useful function on its own but don't expect to be able to modify it!!
 * I would prefer this code was re-written in a manner consistent with the rest of webERP
 * There are many meaningless variable names
 * unecessary abrastaction at every turn
 * No class is necessary for this script IMHO
 * Data held in an xml file instead of the database
 * Variable naming uses a lower case first character
 * $debug is used - but is a webERP global to trap SQL errors for sysadmins
 * 
**/

$Version_adds= 1.1;

include('includes/session.inc');
$title=_('Label Templates Maintainance');
include('includes/header.inc');

$debug=false;
include('includes/DefineLabelClass.php');


//Phil why xml? Why not use the database like every other part of webERP for storing data

$allLabels =				 //!< The variable $allLabels is the global variable that contains the list
		getXMLFile($_SESSION['reports_dir'] . '/labels.xml'); //!< of all the label objects defined until now. In case of a fresh
								 //!<  installation or an empty XML labels file it holds a NULL value.

if ($debug) {
	echo '<br />' ;
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
//exit();
}

/** Check if some action has been requested
*/
$showList=true; // By default will show the tamplates list

/**
 *  Save the changes in template?
 */
if (isset($_POST['Update'])) {
	// Get the data from the user input & validate it (not new)
	//getData function 
	$Label=getData($_POST, false, $ok);
	// If all OK try to update the requested label
	if (!$ok OR !updateLabel($Label)) {
		// show the data label from the input data as update data (id read only)}
		showLabel($Label, _('Correct data'), $theme, false);
		$showList=false;
	}

/**
 *  Save the data for a new template?
 */
} elseif (isset($_POST['Save'])) {
	// Get the data from the user input & validate it for new id
	$Label=getData($_POST, true, $ok);
	if (!$ok OR !createLabel($Label)) { //
		showLabel($Label, _('Correct data'), $theme, false);
		$showList=false;
	}

/**
 *  Get the data from an old one to create a new template?
 */
} elseif (isset($_POST['Copy'])) {
	$Label=$allLabels->getLabel($_POST['labelID']);
	$Label->id = _('New ID');   // Well, where did I get it? of course from the user, but ..
	showLabel($Label, _('Edit data new label'), $theme, false);
	$showList=false;

/**
 *  Change some data from an old template?
 */
} elseif (isset($_POST['Edit'])) {
	$Label=$allLabels->getLabel($_POST['labelID']);
	showLabel($Label, _('Edit data label'), $theme, true);
	$showList=false;

/**
 *  Eliminate an unnecesary template?
 */
} elseif (isset($_POST['Delete'])) {
	$allLabels=deleteLabel($allLabels, $_POST['labelID']);

/**
 *  Create a new template?
 */
} elseif (isset($_POST['New'])) {
	showLabel(null, _('New label'), $theme);
	$showList=false;

/**
 *  Do nothing? only show the list (if it exist))
 */
} elseif (isset($_POST['Cancel'])) {
	;   // showLabelList

/**
 *  No action requested, show all or get the first one
 */
} else {
	if ($allLabels==null OR count($allLabels->label)<1) {
		showLabel(null, _('There is no labels, create a new one'), $theme);
		$showList=false;
	}
}

/**
 *  The default is to show the list of labels templates yet defined,
 *  the exception occurs when previously has been selected a particular
 *  template, for edit, copy or to create a new one
 */
if ($showList) {
	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $title.'</p>';
	showLabelsList($allLabels->label);
}
include('includes/footer.inc');
exit();

/*! \brief getData - Gets the input data from the user's submit
 *
 *  This code constructs a new objet Label from the form's data
 *  provided by the user. It validates the data too.
 *  @param $data	The array of strings gived by the user
 *  @param $new	 This flag indicates that the labels is new, so check no repeated id
 *  @param $ok	  The variable where the routine gives the validation result.
 *  @return		 The Label object constructed qith the data, included errors.
 */
function getData($data, $new, &$ok) {
	$ok = validData($data, $new);
	return newLabel($data);
}

function validData($data, $new) {
	global $allLabels, $DimensionTags, $DataTags;

// Check the heading data
	$errors=array();
	if ($new) {
		if (empty($data['id'])) {
			$errors[]=_('Id required');
		} elseif ($allLabels!=null AND $allLabels->findLabel($data['id'])!==false){
			$errors[]=_('This id exists in previous list');
		}
	}
	if (empty($data['description']))
		$errors=_('the description is required');

// Check the dimensions data
	foreach ($DimensionTags as $iTag=>$tag) {
		if ($tag['type']=='s') {
			continue;  // select type does not require validation
		}
		$dd = trim($data[$iTag]);
		$desc=$tag['desc'];
		switch ($tag['type']) {
		case 'n':
			if (!is_numeric($dd))
				$errors[]= _('The value of').' '.$desc.' '._('would be numeric');
			elseif ((float)$data[$iTag]<=0)
				$errors[]= _('The value of').' '.$desc.' '._('requires a positive value').$dd;
			break;
		case 'i':
			if (!is_numeric($dd) OR (int)$data[$iTag]<=0)
				$errors[]= _('The value of').' '.$tag['desc'].' '._('would be a positive integer');
			break;
		}
	}
	// Checking consistency between data
	// Rh > He
	$tag = $DimensionTags[$iTag='Rh'];
	if ((float)$data['Rh'] < (float)$data['He'] ) {
		$desc=$tag['desc'];
		$errors[]= _('The value of').' '.$desc.' '._('requires to be greater than the height of the labels');
	}
	// Sh >= rows*Rh+Tm
	$tag = $DimensionTags[$iTag='Sh'];
	if ((float)$data['Sh'] <= (float)$data['Tm'] + ( (int)$data['Rows']*((float)$data['Rh']) ) ) {
		$desc=$tag['desc'];
		$errors[]= _('The value of').' '.$desc.' '._('requires to be greater than the height of all the rows, including the top margin');
	}
	// Cw > Wi
	$tag = $DimensionTags[$iTag='Cw'];
	if ((float)$data['Cw'] < (float)$data['Wi'] ) {
		$desc=$tag['desc'];
		$errors[]= _('The value of').' '.$desc.' '._('requires to be greater than the width of the labels');
	}
	// Sw >= Cols*Cw+Lm
	$tag = $DimensionTags[$iTag='Sw'];
	if ((float)$data['Sw'] <= (float)$data['Lm'] + ( (int)$data['Cols']*((float)$data['Cw']) ) ) {
		$desc=$tag['desc'];
		$errors[]= _('The value of').' '.$desc.' '._('requires to be greater than the width of all the cols, including the left margin');
	}

	$rowCount=0;
	$jRow=0;
	foreach ($data['row'] as $iRow=>$row) {
		$jRow++;
		if (empty($row)) continue; // The empty row indicates no data
		$rowCount++;  // we have data

		if (!is_numeric($row) ) $row=0;
		else $row = (float)$row;
		if ($row<=0)
			$errors[]= _('The vert. pos. value would be positive') ." ($jRow)";
		elseif ((float)$row>(float)$data['He'])
			$errors[]= _('The value of the vert. pos. would be less than')." ". $data['He'] ."($jRow)";

	// now the rest of the line data is validated
		foreach ($DataTags as $iTag=>$tag) {
			if ($tag['type']=='s'  // select type does not require validation
				OR $iTag == 'row') continue;  // the row is just validated
			$dd = trim($data[$iTag][$iRow]);
			$desc=$tag['desc'];
			if ($tag['type']=='n') {
				if (!is_numeric($dd))
					$errors[]= _('The value of').' '.$desc.' '._('would be numeric')." ($jRow)";
				elseif (empty($dd) OR (float)$dd<=0)
					$errors[]= _('The value of').' '.$desc.' '._('requires a positive value')." ($jRow)";
			}
			switch ($iTag) {
			case 'font':
				if ((float)$dd+$row >= (float)$data['He']) {
					$errors[]= _('The value of').' '.$desc.' '._('in this position exceeds the label height').
						" ($jRow)";
				}
				break;
			case 'pos':
				$posD = (float)$dd;
				break;
			case 'max':
				if ((float)$dd+$posD >= (float)$data['Wi']) {
					$errors[]= _('The position and lenght of the string leads the text to get out of the label'). " ($jRow)";
				}
				break;
			}
		}
	}
	// Display the errors detected
	if (count($errors)>0) {
		foreach($errors as $err)
			prnMsg($err);
		return false;
	}
	return true;  // all data are valid!
}

/*! \brief Shows the label requested
 *
 *  It shows the data label from the input $data as update data (id read only)
 *  if the third parameter is true or a fresh data label (new label). It is
 *  possible that the combination $data valid and $ReadOnly false occurs when
 *  invalid data needs to be recaptured because an error in a new label capture.
 */
function showLabel($Label, $msg, $theme, $ReadOnly=false) {
	global $rootpath;
	if ($Label==null)
		$Label = newLabel();
	if  ($ReadOnly) {
		$name = 'Update';
		$value = _('Update');
	} else {
		$name = 'Save';
		$value = _('Save');
	}

	$vCancel = _('Cancel');

	$TableGD = setTableGD($Label, $ReadOnly);
	$TableLines = setTableLines($Label->data->line);

	echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $msg.'</p>';

	echo '<br />
	<form action="'.htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8').'" method="post">
    <div>
	<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<div class="centre">
		<table border="2" cellspacing="4" class="selection">
			<tbody>
			<tr>
				<td align="center"><img src="'.$rootpath.'/css/paramsLabel.png" alt="" /></td>
				<td>'.$TableGD.'</td>
			</tr>
			<tr>
				<td align="center"><img src="'.$rootpath.'/css/labelsDim.png" alt="" /></td>
				<td>'.$TableLines.'</td>
			</tr>
			</tbody>
		</table>
		<br />
		<input type="submit" name="'.$name.'" value="'.$value.'" />
		<input type="submit" name="Cancel" value="'.$vCancel.'" />
        </div>
    </div>
	</form>';
}

function setTableGD($Label, $ReadOnly) {
	global $GlobalTags, $DimensionTags;
	$html='<table class="selection">';
	$html .= setDataFields($GlobalTags, 0, $Label, $specialTag='id', $ReadOnly);
	$html .= setDataFields($DimensionTags, 1, $Label->dimensions);
	$html .= '
		</table>';
	return $html;
}

function setDataFields($tags, $withTagId, $data, $specialTag=false, $ReadOnly=false) {
	$iCol=0;
	$html = '';
	foreach ($tags as $iTag=>$tag) {
		$vDat = (is_object($data) AND isset($data->$iTag))?$data->$iTag:'';
		if ($tag['type']=='s') {
			$input ='<select name="'. $iTag . '">';
			foreach ($tag['values'] as $i=>$val) {
				$xSel = ($vDat==$i)?' selected="selected"' : '';
				$input .= '
					<option value="'. $i .'"'. $xSel .'>'.$val.'</option>';
			}
			$input .= '
				</select>';
		} else {
			$ro='';
			if ($ReadOnly AND $specialTag==$iTag)
				$ro='readonly="readonly"';
			$input = '<input type="text" name="'. $iTag .'" value="'. $vDat .'" size="'. $tag['sz'] .'" maxlength="'. $tag['maxsz'] .'" '. $ro .' />';
		}
		if (!$iCol++)  // when zero begins a line
			$html .= '
			<tr>';
		else
			$iCol=0;
		$wTag= $withTagId ? ('&nbsp;('.$iTag.')') :'';
		$html .= '
				<td align="right">' . $tag['desc'] . $wTag . ':</td>
				<td>' . $input . '</td>';
		if (!$iCol)
			$html .= '
			</tr>';
	}
	if ($iCol)  // if the count ends in an odd count, add the end of line
		$html .= '
			</tr>';

	return $html;
}

function setTableLines($lineArray) {
	global $DataTags;
	$html='<table class="selection">';
	$html .= setTableHeader($DataTags);

	$iCount=MAX_LINES_PER_LABEL;
	foreach ($lineArray as $i=>$data) {
		$iCount--;
		$html .= setLineFields($DataTags, $data);
	}
	while ($iCount-->0)
		$html .= setLineFields($DataTags, null);
	$html .= '
		</table>';
	return $html;
}

function setTableHeader($tags) {
	$html= '
			<tr>';
	foreach ($tags as $tit)
		$html .= '
				<th>' . $tit['desc'] . '</th>';
	return $html . '
			</tr>';
}

function setLineFields($tags, $data) {
	$html = '
		<tr>';
	foreach ($tags as $iTag=>$tag) {
		$vDat = ($data!=null AND isset($data->$iTag))?$data->$iTag:'';
		if ($tag['type']=='s') {
			$input ='<select name="'. $iTag . '[]">';
			foreach ($tag['values'] as $kI=>$kVal) {
				$xSel = ($vDat==$kI) ? ' selected="selected"':'';
				$input .= '
					<option value="'. $kI .'"'. $xSel .'>'.$kVal.'</option>';
			}
			$input .= '
				</select>';
		} else {
			$input = '<input type="text" name="'. $iTag .'[]" value="'. $vDat .'" size="'. $tag['sz'] .'" maxlength="'. $tag['maxsz'] .'" />';
		}
		$html .= '
				<td align="center">' . $input . '</td>';
	}
	$html .= '
		</tr>';
	return $html;
}

/*! \brief Shows the current label list
*
*  When the user begin or finishes an maintance action, the program
*  shows the current list, in particular, the ID and the description of the label.
*
*  @param  $list The label object list to be displayed
*  @return Nothing
*/
function showLabelsList($list) {
	$txt= array(_('Label id'),_('Description'), _('Label array'),
		  _('New'), _('Edit'), _('Copy'), _('Delete'),
		  _('Do you really want to erase the label')
	);
	// The header of the list
	echo '<script type="text/javascript">
		function submitForm(formID, action, value) {
			document.getElementById(\'action\').name=action;
			document.getElementById(\'labelID\').value=value;
			document.getElementById(formID).submit();
		}
		function areYouSure(form, action, value) {
			var r=confirm("'.$txt[7].'");
			if (r) {
				submitForm(form, action, value);
			}
		}
	</script>
	<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post" id="form1">
    <div>
	<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<input type="hidden" name="action" id="action" value="" />
		<input type="hidden" name="labelID" id="labelID" value="" />
    </div>
	</form>
	<div class="centre">
	<table class="selection">
		<thead>
			<tr>
				<th>'.$txt[0].'</th>
				<th>'.$txt[1].'</th>
				<th>'.$txt[2].'</th>
				<th><input type="submit" name="new" onclick="submitForm(\'form1\',\'New\',\'\');" value="'.$txt[3].'" />
				</th>
			</tr>
		</thead>
		<tbody>';
	foreach ($list as $Label) {
		$dim = (string)$Label->dimensions->Rows. ' x '. (string)$Label->dimensions->Cols;
		echo '
			<tr><td>'. $Label->id . '</td>
				<td>'. $Label->description . '</td>
				<td><div class="centre">'. $dim . '</div></td>
				<td><input type="submit" onclick="submitForm('. "'form1','Edit','". $Label->id . "');" .'" value="' . $txt[4]. '" />
				<input type="submit" onclick="submitForm('. "'form1','Copy','". $Label->id . "');" .'" value="' . $txt[5]. '" />
				<input type="submit" onclick="areYouSure('. "'form1','Delete','". $Label->id . "');" .'" value="' . $txt[6]. '" />
				</td>
			</tr>';
	}
	echo '
		</tbody>
		</table>
	</div>';
}

/*! \brief Generates a new label
*
*  After the user gives the label data, this routine tries to insert
*  the new label in the current list.
*
*  @param  $Label The object label that will replace an old one
*  @return True when the update was ok
*/
function createLabel($Label) {
	global $allLabels;
	$new = emptyList();
	$done=false;
	if ($allLabels!=null) {
		foreach ($allLabels as $oldLabel) {
			if (!$done AND (string)$oldLabel->id >= (string)$Label->id) {
				$new->addLabel($Label);
				$done=true;
			}
			$new->addLabel($oldLabel);  // inser data in the list, replacing the old one
		}
	}
	if (!$done)
		$new->addLabel($Label);
	$allLabels = $new;
	rewrite($allLabels);	 // rewrite it to the XML file
	return true;
}

/*! \brief Update the label data
*
*  After the user modifies the label data this routine tries to update
*  the corresponding structure in the label list with the object given.
*  If the label with the id of the new one is found, the object could
*  be accepted in the list and written to the XML file.
*
*  @param  $Label The object label that will replace an old one
*  @return True when the update was ok
*/
function updateLabel($Label) {
	global $allLabels;
	$new = emptyList();
	foreach ($allLabels as $oldLabel) {
		if ((string)$oldLabel->id == (string)$Label->id)
			$new->addLabel($Label);
		else
			$new->addLabel($oldLabel);  // inser data in the list, replacing the old one
	}
	$allLabels = $new;
	rewrite($allLabels);	 // rewrite it to the XML file
	return true;
}

/*! \brief Label elimination from the list.
 *
 *  This routine eliminates one label from the global list $allLabels. It
 *  just find the index that correspond to the id given as input data.
 *  Because this function is called from the selection of a button with
 *  correct data, it would be unsual that some error exist, but it is
 *  included the code for the validation of some this strange case.
 *
 *  @param  $LabelID is the identifier of the label to delete.
 *  @see	$allLabels
 *  @return true in case of success
 */
function deleteLabel($list, $LabelID) {
	$new = emptyList();
	foreach ($list as $Label) {
		if ((string)$Label->id!=$LabelID)
			$new->addLabel($Label);
	}
	rewrite($new);
	return $new;
}

function rewrite($list) {
	// First rename the previous XML file
	if (is_file(LABELS_FILE) AND !rename(LABELS_FILE, LABELS_FILE.".bak"))
		abortMsg(_('Could not rename the previous file.'));
	$result=$list->asXML(LABELS_FILE);
	if (!$result)
		abortMsg(_('Could not create the new XML file.').': '.LABELS_FILE);
}

?>