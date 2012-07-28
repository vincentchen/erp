<?php

/*
date validation and parsing functions

These functions refer to the global variable defining the date format
The date format is defined in config.php called DefaultDateFormat
this can be a string either "d/m/Y" for UK/Australia/New Zealand dates or
"m/d/Y" for US/Canada format dates

*/

function Is_Date($DateEntry) {

	global $DefaultDateFormat;

	$DateEntry =Trim($DateEntry);

	if (strpos($DateEntry,"/")) {
		$Date_Array = explode("/",$DateEntry);
	} elseif (strpos ($DateEntry,"-")) {
		$Date_Array = explode("-",$DateEntry);
	} elseif (strlen($DateEntry)==6) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,2);
	} elseif (strlen($DateEntry)==8) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,4);
	}


	If ((int)$Date_Array[2] >9999) {
		Return 0;
	}


	if (is_long((int)$Date_Array[0]) AND is_long((int)$Date_Array[1]) AND is_long((int)$Date_Array[2])) {
		if ($DefaultDateFormat=="d/m/Y"){
			if (checkdate((int)$Date_Array[1],(int)$Date_Array[0],(int)$Date_Array[2])){
				Return 1;
			} else {
				Return 0;
			}
		} elseif ($DefaultDateFormat=="m/d/Y"){

			if (checkdate((int)$Date_Array[0],(int)$Date_Array[1],(int)$Date_Array[2])){
				Return 1;
			} else {
				Return 0;
			}
		} else { /*Can't be in an appropriate DefaultDateFormat */
			Return 0;
		}
	}else { // end if all numeric inputs
		Return 0;
	}

} //end of Is_Date function



//_______________________________________________________________

function MonthAndYearFromSQLDate($DateEntry) {

	global $DefaultDateFormat;

	if (strpos($DateEntry,"/")) {
		$Date_Array = explode("/",$DateEntry);
	} elseif (strpos ($DateEntry,"-")) {
		$Date_Array = explode("-",$DateEntry);
	}

	if (strlen($Date_Array[2])>4) {
		$Date_Array[2]= substr($Date_Array[2],0,2);
	}

	Return Date("F Y", mktime(0,0,0, (int)$Date_Array[1],(int)$Date_Array[2],(int)$Date_Array[0]));

}




function ConvertSQLDate($DateEntry) {

//for MySQL dates are in the format YYYY-mm-dd

	global $DefaultDateFormat;

	if (strpos($DateEntry,"/")) {
		$Date_Array = explode("/",$DateEntry);
	} elseif (strpos ($DateEntry,"-")) {
		$Date_Array = explode("-",$DateEntry);
	}

	if (strlen($Date_Array[2])>4) {  /*chop off the time stuff */
		$Date_Array[2]= substr($Date_Array[2],0,2);
	}


	if ($DefaultDateFormat=="d/m/Y"){
		Return $Date_Array[2]."/".$Date_Array[1]."/".$Date_Array[0];
	} elseif ($DefaultDateFormat=="m/d/Y"){
		Return $Date_Array[1]."/".$Date_Array[2]."/".$Date_Array[0];
	}

} // end function ConvertSQLDate

function SQLDateToEDI($DateEntry) {

//for MySQL dates are in the format YYYY-mm-dd
//EDI format 102 dates are in the format CCYYMMDD - just need to lose the seperator

	if (strpos($DateEntry,"/")) {
		$Date_Array = explode("/",$DateEntry);
	} elseif (strpos ($DateEntry,"-")) {
		$Date_Array = explode("-",$DateEntry);
	}

	if (strlen($Date_Array[2])>4) {  /*chop off the time stuff */
		$Date_Array[2]= substr($Date_Array[2],0,2);
	}

	Return $Date_Array[0].$Date_Array[1].$Date_Array[2];

} // end function SQLDateToEDI

function ConvertToEDIDate($DateEntry) {

/* takes a date in a the format specified in $DefaultDateFormat
and converts to a yyyymmdd - EANCOM format 102*/

	global $DefaultDateFormat;

	$DateEntry = trim($DateEntry);

	if (strpos($DateEntry,"/")) {
		$Date_Array = explode("/",$DateEntry);
	} elseif (strpos ($DateEntry,"-")) {
		$Date_Array = explode("-",$DateEntry);
	} elseif (strlen($DateEntry)==6) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,2);
	} elseif (strlen($DateEntry)==8) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,4);
	}


//to modify assumption in 2030

	If ((int)$Date_Array[2] <60) {
		$Date_Array[2] = "20".$Date_Array[2];
	} elseif ((int)$Date_Array[2] >59 AND (int)$Date_Array[2] <100) {
		$Date_Array[2] = "19".$Date_Array[2];
	} elseif ((int)$Date_Array[2] >9999) {
		Return 0;
	}

	if ($DefaultDateFormat=="d/m/Y"){
		Return $Date_Array[2].$Date_Array[1].$Date_Array[0];

	} elseif ($DefaultDateFormat=="m/d/Y") {
		Return $Date_Array[2].$Date_Array[0].$Date_Array[1];

	}

} // end function to convert DefaultDateFormat Date to EDI format 102





function Format_Date($DateEntry) {

	$DateEntry =trim($DateEntry);

	if (strpos($DateEntry,"/")) {
		$Date_Array = explode("/",$DateEntry);
	} elseif (strpos ($DateEntry,"-")) {
		$Date_Array = explode("-",$DateEntry);
	} elseif (strlen($DateEntry)==6) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,2);
	} elseif (strlen($DateEntry)==8) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,4);
	}

//to modify assumption in 2030

	If ((int)$Date_Array[2] <60) {
		$Date_Array[2] = "20".$Date_Array[2];
	} elseif ((int)$Date_Array[2] >59 AND (int)$Date_Array[2] <100)						{
		$Date_Array[2] = "19".$Date_Array[2];
	} elseif ((int)$Date_Array[2] >9999) {
		Return 0;
	}

	If (is_long((int)$Date_Array[0]) AND is_long((int)$Date_Array[1]) AND is_long((int)$Date_Array[2])) {
		if ($DefaultDateFormat=="d/m/Y"){
			if (checkdate((int)$Date_Array[1],(int)$Date_Array[0],(int)$Date_Array[2])){
				Return $Date_Array[0]."/".$Date_Array[1]."/".$Date_Array[2];
			}
		} elseif ($DefaultDateFormat="m/d/Y"){
			if (checkdate((int)$Date_Array[0],(int)$Date_Array[1],(int)$Date_Array[2]))							{
				Return $Date_Array[0]."/".$Date_Array[1]."/".$Date_Array[2];
			}
		} // end if check date
	} else { // end if all numeric inputs
		Return 0;
	}
}// end of function




function FormatDateForSQL($DateEntry) {

/* takes a date in a the format specified in $DefaultDateFormat
and converts to a yyyy/mm/dd format */

	global $DefaultDateFormat;

	$DateEntry = trim($DateEntry);

	if (strpos($DateEntry,"/")) {
		$Date_Array = explode("/",$DateEntry);
	} elseif (strpos ($DateEntry,"-")) {
		$Date_Array = explode("-",$DateEntry);
	} elseif (strlen($DateEntry)==6) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,2);
	} elseif (strlen($DateEntry)==8) {
		$Date_Array[0]= substr($DateEntry,0,2);
		$Date_Array[1]= substr($DateEntry,2,2);
		$Date_Array[2]= substr($DateEntry,4,4);
	}


//to modify assumption in 2030

	If ((int)$Date_Array[2] <60) {
		$Date_Array[2] = "20".$Date_Array[2];
	} elseif ((int)$Date_Array[2] >59 AND (int)$Date_Array[2] <100) {
		$Date_Array[2] = "19".$Date_Array[2];
	} elseif ((int)$Date_Array[2] >9999) {
		Return 0;
	}

	if ($DefaultDateFormat=="d/m/Y"){
		Return $Date_Array[2]."/".$Date_Array[1]."/".$Date_Array[0];

	} elseif ($DefaultDateFormat=="m/d/Y") {
		Return $Date_Array[2]."/".$Date_Array[0]."/".$Date_Array[1];

	}

}// end of function

function Date1GreaterThanDate2 ($Date1, $Date2) {

/* returns 1 true if Date1 is greater than Date 2 */

	global $DefaultDateFormat;

	$Date1 = trim($Date1);
	$Date2 = trim($Date2);
	$Date1_array = explode("/", $Date1);
	$Date2_array = explode("/", $Date2);

	/*The 2 element of the array will be the year in either d/m/Y or m/d/Y formats */

	If ($Date1_array[2] > $Date2_array[2]){
		Return 1;
	} Elseif ($Date1_array[2] == $Date2_array[2]){

	/*The 0 and 1 elements of the array are switched depending on the format used */

		if ($DefaultDateFormat=="d/m/Y"){
			If ($Date1_array[1] > $Date2_array[1]){
				Return 1;
			} Elseif ($Date1_array[1]==$Date2_array[1]){
				If ($Date1_array[0] > $Date2_array[0]){
					Return 1;
				}
			}

		} elseif ($DefaultDateFormat =="m/d/Y"){
			If ($Date1_array[0] > $Date2_array[0]){
				Return 1;
			} Elseif ($Date1_array[0]==$Date2_array[0]){
				If ($Date1_array[1] > $Date2_array[1]){
					Return 1;
				}
			}
		}
	}
	Return 0;
}

function CalcDueDate($TranDate, $DayInFollowingMonth, $DaysBeforeDue){

	global $DefaultDateFormat;

	$TranDate = trim($TranDate);

	$Date_array = explode("/", $TranDate);

	if ($DefaultDateFormat=="d/m/Y"){
		if ($DayInFollowingMonth==0){ /*then it must be set up for DaysBeforeDue type */

			$DayDue = $Date_array[0]+$DaysBeforeDue;
			$MonthDue = $Date_array[1];
			$YearDue = $Date_array[2];

		} elseif($DayInFollowingMonth>=29) { //take the last day of month

			$DayDue = 0;
			$MonthDue = $Date_array[1]+2;
			$YearDue = $Date_array[2];
		} else {
			$DayDue = $DayInFollowingMonth;
			$MonthDue = $Date_array[1]+1;
			$YearDue = $Date_array[2];

		}
	} elseif ($DefaultDateFormat=="m/d/Y"){
		if ($DayInFollowingMonth==0){ /*then it must be set up for DaysBeforeDue type */
			$DayDue = $Date_array[1]+$DaysBeforeDue;
			$MonthDue = $Date_array[0];
			$YearDue = $Date_array[2];

		} elseif($DayInFollowingMonth>=29) { //take the last day of month

			$DayDue = 0;
			$MonthDue = $Date_array[0]+2;
			$YearDue = $Date_array[2];
		} else {
			$DayDue = $DayInFollowingMonth;
			$MonthDue = $Date_array[0]+1;
			$YearDue = $Date_array[2];
		}
	}
	Return Date($DefaultDateFormat, mktime(0,0,0, $MonthDue, $DayDue,$YearDue));

}



function DateDiff ($Date1, $Date2, $Period) {

/* expects dates in the format specified in $DefaultDateFormat - period can be one of 'd','w','y','m'
months are assumed to be 30 days and years 365.25 days This only works
provided that both dates are after 1970. Also only works for dates up to the year 2035 ish */

	global $DefaultDateFormat;

	$Date1 = trim($Date1);
	$Date2 = trim($Date2);
	$Date1_array = explode("/", $Date1);
	$Date2_array = explode("/", $Date2);

	if ($DefaultDateFormat=="d/m/Y"){
		$Date1_Stamp = mktime(0,0,0, (int)$Date1_array[1],(int)$Date1_array[0],(int)$Date1_array[2]);
		$Date2_Stamp = mktime(0,0,0, (int)$Date2_array[1],(int)$Date2_array[0],(int)$Date2_array[2]);
	} elseif ($DefaultDateFormat=="m/d/Y"){
		$Date1_Stamp = mktime(0,0,0, (int)$Date1_array[0],(int)$Date1_array[1],(int)$Date1_array[2]);
		$Date2_Stamp = mktime(0,0,0, (int)$Date2_array[0],(int)$Date2_array[1],(int)$Date2_array[2]);
	}
	$Difference = $Date1_Stamp - $Date2_Stamp;

/* Difference is the number of seconds between each date negative if Date 2 > Date 1 */

	switch ($Period) {
	case "d":
		Return (int) ($Difference/(24*60*60));
		break;
	case "w":
		Return (int) ($Difference/(24*60*60*7));
		break;
	case "m":
		Return (int) ($Difference/(24*60*60*30));
		break;
	case "s":
		Return $Difference;
		break;
	case "y":
		Return (int) ($Difference/(24*60*60*365.25));
		break;
	default:
		Return 0;
	}

}


Function CalcEarliestDispatchDate (){

/*This function will need to be modified depending on the business - many businesses run 24x7
The default assumes no delivery on Sat and Sun*/

	global $DispatchCutOffTime; /*Defined in config.php */

	$EarliestDispatch = Mktime();
	If (Date("w",$EarliestDispatch)==0 ){

/*if today is a sunday the dispatch date must be tomorrow (Monday) or after */

		$EarliestDispatch = Mktime(0,0,0,Date("m",$EarliestDispatch),Date("d",$EarliestDispatch)+1,Date("y",$EarliestDispatch));

	} elseIf (Date("w",$EarliestDispatch)==6){

/*if today is a saturday the dispatch date must be Monday or after */

		$EarliestDispatch = Mktime(0,0,0,Date("m",$EarliestDispatch),Date("d",$EarliestDispatch)+2,Date("y",$EarliestDispatch));

	}elseif (Date("H")>$DispatchCutOffTime) {

/* If the hour is after Dispatch Cut Off Time default dispatch date to tomorrow */

		$EarliestDispatch = Mktime(0,0,0,Date("m"),Date("d")+1,Date("y"));
	}
	Return $EarliestDispatch;
}


Function GetPeriod ($TransDate, &$db) {

/*Gets the period for a transaction from the date entered from the period table -
if the date entered is out of range monthly periods are inserted as necessary and
the correct, newly inserted period returned

Dates are parsed using the DefaultDateFormat string */

	global $DefaultDateFormat;

	if (strpos($TransDate,"/")){
		$Date_array = explode("/", $TransDate);
	} elseif (strpos($TransDate,"-")){
		$Date_array = explode("-", $TransDate);
	} else {
		echo "<BR>Dates must be entered in the format $DefaultDateFormat";
		exit;
	}

	If ((int)$Date_array[2] <60) {
		$Date_array[2] = "20". $Date_array[2];
	} elseif ((int)$Date_array[2] >59 AND (int)$Date_array[2] <100) {
		$Date_array[2] = "19". $Date_array[2];
	}

	if ($DefaultDateFormat=="d/m/Y"){
		$TransDate = mktime(0,0,0,$Date_array[1],$Date_array[0],$Date_array[2]);
	} elseif ($DefaultDateFormat=="m/d/Y"){
		$TransDate = mktime(0,0,0,$Date_array[0],$Date_array[1],$Date_array[2]);
	}
	$MonthAfterTransDate = Mktime(0,0,0,Date("m",$TransDate)+1,Date("d",$TransDate),Date("Y",$TransDate));

	$GetPrdSQL = "SELECT PeriodNo FROM Periods WHERE LastDate_in_Period < '" . Date("Y/m/d", $MonthAfterTransDate) . "' AND LastDate_in_Period >= '" . Date("Y/m/d", $TransDate) . "'";

	$GetPrdResult = DB_query($GetPrdSQL,$db);

	If (DB_error_no($db) != 0){
		echo "An error occurred in retrieving the period number :- " . DB_error_msg($db);
		return 0;
		exit;

	} elseif (DB_num_rows($GetPrdResult)==0) {

/*The date entered does not fall between currently defined period ranges.
Need to insert some new periods */

		DB_free_result($GetPrdResult);
		$GetPrdSQL = "SELECT Max(LastDate_in_Period), Max(PeriodNo) FROM Periods";
		$GetPrdResult = DB_query($GetPrdSQL,$db);
		$myrow = DB_fetch_row($GetPrdResult);

		$Date_array = explode("-", $myrow[0]);

		$LastPeriodEnd = mktime(0,0,0,$Date_array[1]+1,0,$Date_array[0]);
		$LastPeriodNo = $myrow[1];

		if (DateDiff(Date($DefaultDateFormat,$TransDate), Date($DefaultDateFormat, $LastPeriodEnd),"d")>0){

		/*Then the date entered is after the currently defined period */

			While (DateDiff(Date($DefaultDateFormat, $TransDate), Date($DefaultDateFormat, $LastPeriodEnd),"d")>0){

/* The date of the last period added is less than the transaction date */

				$MonthOfLastPeriod = Date("m", $LastPeriodEnd);
				If ($MonthOfLastPeriod ==12) {
					$LastPeriodEnd =  Mktime(0,0,0,2,0,Date("Y",$LastPeriodEnd)+1);
				} else {
					$LastPeriodEnd = Mktime(0,0,0,($MonthOfLastPeriod +2),0,Date("Y",$LastPeriodEnd));
				}

				$LastPeriodNo = $LastPeriodNo + 1;

				$GetPrdSQL = "INSERT INTO Periods (PeriodNo, LastDate_in_Period) VALUES (" . $LastPeriodNo . ", '" . Date("Y/m/d", $LastPeriodEnd) . "')";
				$GetPrdResult = DB_query($GetPrdSQL,$db);
				if (DB_error_no($db) != 0){
				      echo "<BR>An error occurred in adding a new period number :- " . DB_error_msg($db);
				      return 0;
				      exit;
				}

			}
			return $LastPeriodNo;

		} else {
		/* then the transactions date must be before periods have been created for need to insert periods before currently defined periods */

			$GetPrdSQL = "SELECT Min(LastDate_in_Period), Min(PeriodNo) FROM Periods";
			$GetPrdResult = DB_query($GetPrdSQL,$db);
			if (DB_error_no($db) != 0){
			      echo "An error occurred in getting the first period number in the database:- " . DB_error_msg($db);
			      return 0;
			      exit;
			}
			$myrow = DB_fetch_row($GetPrdResult);

			$Date_array = explode("-", $myrow[0]);

			$FirstPeriodEnd = mktime(0,0,0,$Date_array[1]+1,0,$Date_array[0]);
			$FirstPeriodNo = $myrow[1];


			While (DateDiff(Date($DefaultDateFormat, $TransDate), Date($DefaultDateFormat, $FirstPeriodEnd), "d")<0){

			/* The date of the first period is after the transaction date */

				$FirstPeriodEnd = Mktime(0,0,0, Date("m",$FirstPeriodEnd), 0, Date("Y",$FirstPeriodEnd));

				$FirstPeriodNo = $FirstPeriodNo - 1;
				$GetPrdSQL = "INSERT INTO Periods (PeriodNo, LastDate_in_Period) VALUES (" . $FirstPeriodNo . ", '" . Date("Y/m/d", $FirstPeriodEnd) . "')";
				if (DB_error_no($db) != 0){
				      echo "An error occurred in inserting periods before the first period to accomodate back dated transactions:- " . DB_error_msg($db);
				      return 0;
				      exit;
				}
				$GetPrdResult = DB_query($GetPrdSQL,$db);

			}
			return $FirstPeriodNo + 1 ;

		} /*end of logic for dates before currently defined periods */
	} else {

	/*the date is in a range currently defined by period numbers */
		$myrow = DB_fetch_row ($GetPrdResult);
		return $myrow[0];

	}


} /*end of get period function */

?>