<?php
/* $Revision: 1.1 $ */
// MRPCalendar.php
// Maintains the calendar of valid manufacturing dates for MRP

$PageSecurity=9;

include('includes/session.inc');
$title = _('MRP Calendar');
include('includes/header.inc');


if (isset($_POST['ChangeDate'])){
	$ChangeDate =trim(strtoupper($_POST['ChangeDate']));
} elseif (isset($_GET['ChangeDate'])){
	$ChangeDate =trim(strtoupper($_GET['ChangeDate']));
}

if (isset($_POST['submit'])) {
    submit($db,$ChangeDate);
} elseif (isset($_POST['update'])) {
    update($db,$ChangeDate);
} elseif (isset($_POST['listall'])) {
    listall($db);
} else {
    display($db,$ChangeDate);
}


function submit(&$db,&$ChangeDate) { //####SUBMIT_SUBMIT_SUBMIT_SUBMIT_SUBMIT_SUBMIT_SUBMIT_SUBMIT####

	//initialize no input errors
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (!is_date($_POST['FromDate'])) {
		$InputError = 1;
		prnMsg(_('Invalid From Date'),'error');
	} 
	if (!is_date($_POST['ToDate'])) {
		$InputError = 1;
		prnMsg(_('Invalid To Date'),'error');
	}

    if ($inputerror !=1) {
		$formatdate = FormatDateForSQL($_POST['FromDate']);
		echo "</br>Format Date: $formatdate</br>";
		
		$convertdate = ConvertSQLDate($formatdate);
		echo "</br>Convert Date: $convertdate</br>";
		$dayofweek = DayOfWeekFromSQLDate($formatdate);
		echo "</br>Day of week: $dayofweek</br>";
		//$dateadd = DateAdd($_POST['FromDate'],"d",-3);
		$dateadd = DateAdd($convertdate,"d",-8);
		
		echo "</br>Date Add: $dateadd</br>";
		$dategreater = Date1GreaterThanDate2($_POST['ToDate'],$_POST['FromDate']);
		echo "</br>Date Greater: $dategreater</br>";
		$datediff = DateDiff($_POST['ToDate'],$_POST['FromDate'],"d"); // Date1 minus Date2
		echo "</br>Date Difference: $datediff</br>";
		
		
		$sql = 'DROP TABLE IF EXISTS mrpcalendar';
		$result = DB_query($sql,$db);
		
		$sql = 'CREATE TABLE mrpcalendar (
					calendardate date NOT NULL,
					daynumber int(6) NOT NULL,
					manufacturingflag smallint(6) NOT NULL default "1",
					INDEX (daynumber),
					PRIMARY KEY (calendardate))';
		$ErrMsg = _('The SQL to to create passbom failed with the message');
		$result = DB_query($sql,$db,$ErrMsg);
		
		$i = 0;
		
		// $daystext used so can get text of day based on the value get from DayOfWeekFromSQLDate of
		// the calendar date. See if that text is in the ExcludeDays array
		$daysText = array(_('Sunday'),_('Monday'),_('Tuesday'),_('Wednesday'),_('Thursday'),_('Friday'),_('Saturday'));
		$ExcludeDays = array($_POST['Sunday'],$_POST['Monday'],$_POST['Tuesday'],$_POST['Wednesday'],
							 $_POST['Thursday'],$_POST['Friday'],$_POST['Saturday']);
		$caldate = $_POST['FromDate'];
		for ($i = 0; $i <= $datediff; $i++) {
			 $dateadd = FormatDateForSQL(DateAdd($caldate,"d",$i));
			 
			 // If the check box for the calendar date's day of week was clicked, set the manufacturing flag to 0
			 $dayofweek = DayOfWeekFromSQLDate($dateadd);
			 $manuflag = 1;
			 foreach ($ExcludeDays as $exday) {
				 if ($exday == $daysText[$dayofweek]) {
					 $manuflag = 0;
				 }
			 }
			 
			// echo "</br>Date: $dateadd Day: $dayofweek";
			 $sql = "INSERT INTO mrpcalendar (
						calendardate,
						daynumber,
						manufacturingflag)
					 VALUES ('$dateadd',
							'1',
							'$manuflag')";
			$result = DB_query($sql,$db,$ErrMsg);
		}
		
		// Update daynumber. Set it so non-manufacturing days will have the same daynumber as a valid
		// manufacturing day that precedes it. That way can read the table by the non-manufacturing day,
		// subtract the leadtime from the daynumber, and find the valid manufacturing day with that daynumber.
		$daynumber = 1;
		$sql = 'SELECT * FROM mrpcalendar ORDER BY calendardate';
		$result = DB_query($sql,$db,$ErrMsg);
		while ($myrow = DB_fetch_array($result)) {
			   if ($myrow['manufacturingflag'] == "1") {
				   $daynumber++;
			   }
			   $caldate = $myrow['calendardate'];
			   $sql = "UPDATE mrpcalendar SET daynumber = '$daynumber'
						WHERE calendardate = '$caldate'";
			   $resultupdate = DB_query($sql,$db,$ErrMsg);
		}
		echo '</br>' . _('Number of days') . ':' . $i . '</br>';
		prnMsg(_("The MRP Calendar has been created"),'success');
		display($db,$ChangeDate);
    } // End of if inputerror != 1

} // End of function submit()


function update(&$db,&$ChangeDate)  {//####UPDATE_UPDATE_UPDATE_UPDATE_UPDATE_UPDATE_UPDATE_####

// Change manufacturing flag for a date. The value "1" means the date is a manufacturing date.
// After change the flag, re-calculate the daynumber for all dates.

	if (!is_date($ChangeDate)) {
		prnMsg(_('Invalid From Date'),'error');
	} 

    $inputerror = 0;
    $caldate = FormatDateForSQL($ChangeDate);
	$sql= "SELECT COUNT(*),mrpcalendar.* FROM mrpcalendar WHERE calendardate='$caldate'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] < 1) {
	    $InputError = 1;
		prnMsg(_('Invalid Change Date'),'error');
	}
	
	if ($inputerror != 1) {
		$newmanufacturingflag = 0;
		if ($myrow[3] == 0) {
			$newmanufacturingflag = 1;
		}
		$sql = "UPDATE mrpcalendar SET manufacturingflag = '$newmanufacturingflag'
					WHERE calendardate = '$caldate'";
		$resultupdate = DB_query($sql,$db,$ErrMsg);
		prnMsg(_('The MRP calendar record has been updated for') . ' ' .  $ChangeDate,'success');
		unset ($ChangeDate);
		display($db,$ChangeDate);
		
		// Have to update daynumber any time change a date from or to a manufacturing date
		// Update daynumber. Set it so non-manufacturing days will have the same daynumber as a valid
		// manufacturing day that precedes it. That way can read the table by the non-manufacturing day,
		// subtract the leadtime from the daynumber, and find the valid manufacturing day with that daynumber.
		$daynumber = 1;
		$sql = 'SELECT * FROM mrpcalendar ORDER BY calendardate';
		$result = DB_query($sql,$db,$ErrMsg);
		while ($myrow = DB_fetch_array($result)) {
			   if ($myrow['manufacturingflag'] == '1') {
				   $daynumber++;
			   }
			   $caldate = $myrow['calendardate'];
			   $sql = "UPDATE mrpcalendar SET daynumber = '$daynumber'
						WHERE calendardate = '$caldate'";
			   $resultupdate = DB_query($sql,$db,$ErrMsg);
		} // End of while
	} // End of if inputerror !=1

} // End of function update()


function listall(&$db) { //####LISTALL_LISTALL_LISTALL_LISTALL_LISTALL_LISTALL_LISTALL_####

// List all records in date range
    $fromdate = FormatDateForSQL($_POST['FromDate']);
    $todate = FormatDateForSQL($_POST['ToDate']);
	$sql = "SELECT calendardate,
	               daynumber,
	               manufacturingflag,
	               DAYNAME(calendardate) as dayname
		FROM mrpcalendar
		WHERE calendardate >='$fromdate'
		  AND calendardate <='$todate'";

	$ErrMsg = _('The SQL to find the parts selected failed with the message');
	$result = DB_query($sql,$db,$ErrMsg);
		
	echo "</br><center><table border=1>
		<tr BGCOLOR =#800000>
		    <th>" . _('Date') . "</th>
			<th>" . _('Manufacturing Date') . "</th>
		</tr></font>";
    $ctr = 0;
	while ($myrow = DB_fetch_array($result)) {
	    $flag = _('Yes');
	    if ($myrow['manufacturingflag'] == 0) {
	        $flag = _('No');
	    }
		printf("<tr><td>%s</td>
				<td>%s</td>
				<td>%s</td>
				</tr>",
				ConvertSQLDate($myrow[0]),
				_($myrow[3]),
				$flag);
	} //END WHILE LIST LOOP
	
	echo '</table></center>';
    echo '</br></br>';
    unset ($ChangeDate);
    display($db,$ChangeDate);



} // End of function listall()


function display(&$db,&$ChangeDate)  {//####DISPLAY_DISPLAY_DISPLAY_DISPLAY_DISPLAY_DISPLAY_#####

// Display form fields. This function is called the first time
// the page is called, and is also invoked at the end of all of the other functions.

	echo '<form action=' . $_SERVER['PHP_SELF'] . '?' . SID .' method=post></br></br>';

	echo '<center><table>';

    echo '<tr>
        <td>' . _('From Date') . ':</td>
	    <td><input type="Text" name="FromDate" size=10 maxlength=10 value=' . $_POST['FromDate'] . '></td>
        <td>' . _('To Date') . ':</td>
	    <td><input type="Text" name="ToDate" size=10 maxlength=10 value=' . $_POST['ToDate'] . '></td>
	</tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td>' . _('Exclude The Following Days') . '</td></tr>
     <tr>
        <td>' . _('Saturday') . ':</td>
	    <td><input type="checkbox" name="Saturday" value="' . _('Saturday') . '"></td>
	</tr>
     <tr>
        <td>' . _('Sunday') . ':</td>
	    <td><input type="checkbox" name="Sunday" value="' . _('Sunday') . '"></td>
	</tr>
     <tr>
        <td>' . _('Monday') . ':</td>
	    <td><input type="checkbox" name="Monday" value="' . _('Monday') . '"></td>
	</tr>
     <tr>
        <td>' . _('Tuesday') . ':</td>
	    <td><input type="checkbox" name="Tuesday" value="' . _('Tuesday') . '"></td>
	</tr>
     <tr>
        <td>' . _('Wednesday') . ':</td>
	    <td><input type="checkbox" name="Wednesday" value="' . _('Wednesday') . '"></td>
	</tr>
     <tr>
        <td>' . _('Thursday') . ':</td>
	    <td><input type="checkbox" name="Thursday" value="' . _('Thursday') . '"></td>
	</tr>
     <tr>
        <td>' . _('Friday') . ':</td>
	    <td><input type="checkbox" name="Friday" value="' . _('Friday') . '"></td>
	</tr>
	<tr></tr><tr></tr>
	<tr>
	    <td></td>
	</tr>
	<tr>
	    <td></td>
	</tr>
	<tr>
	    <td></td>
	    <td><input type="submit" name="submit" value="' . _('Create Calendar') . '"></td>
	    <td></td>
	    <td><input type="submit" name="listall" value="' . _('List Date Range') . '"></td>
	</tr>
	</table>
	</br>';

echo '</br></br><hr/>';
echo '<center><table>';
echo '<tr>
        <td>' . _('Change Date Status') . ':</td>
	    <td><input type="Text" name="ChangeDate" size=12 maxlength=12 value=' . $_POST['ChangeDate'] . '></td>
	  </tr></table>';
echo '</br></br><center><input type="submit" name="update" value="' . _('Update') . '">';
echo '</form>';

} // End of function display()


include('includes/footer.inc');
?>
