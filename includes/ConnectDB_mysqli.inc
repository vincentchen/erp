<?php
/* $Id$ */

/* PeterMoulding.com
20071102 Change from mysql to mysqli;
20071102 Add $db to DB_escape_string();
*/

define ('LIKE','LIKE');

if (!isset($mysqlport)){
	$mysqlport = 3306;
}
global $db;	// Make sure it IS global, regardless of our context

$db = mysqli_connect($host , $DBUser, $DBPassword,$_SESSION['DatabaseName'], $mysqlport);
//$result=DB_query('SET sql_mode = ANSI', $db);


//this statement sets the charset to be used for sending data to and from the db server
//if not set, both mysqli server and mysqli client/library may assume otherwise
mysqli_set_charset($db, 'utf8');

/* check connection */
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	session_unset();
	session_destroy();
	echo '<p>' . _('Click') . ' ' . '<a href="index.php">' . _('here') . '</a>' . ' '  ._('to try logging in again') . '</p>';
	exit();
}

if ( !$db ) {
	echo '<br />' . _('The configuration in the file config.php for the database user name and password do not provide the information required to connect to the database server');
	exit;
}

/* Update to allow RecurringSalesOrdersProcess.php to run via cron */
if (isset($DatabaseName)) {
	if (!mysqli_select_db($db,$DatabaseName)) {
		echo '<br />' . _('The company name entered does not correspond to a database on the database server specified in the config.php configuration file. Try logging in with a different company name');
		echo '<br /><a href="index.php">' . _('Back to login page') . '</A>';
		unset ($DatabaseName);
		exit;
	}
} else {
	if (!mysqli_select_db($db,$_SESSION['DatabaseName'])) {
		echo '<br />' . _('The company name entered does not correspond to a database on the database server specified in the config.php configuration file. Try logging in with a different company name');
		echo '<br /><a href="index.php">' . _('Back to login page') . '</A>';

		unset ($_SESSION['DatabaseName']);
		exit;
        }
}

require_once ($PathPrefix .'includes/MiscFunctions.php');


/**
* DB wrapper functions to change only once for whole application
* Will accept an array $BindVars for prepared statement with bound variables
*
* @param $SQL MySQL query string with or without bindvars
* @param $Conn  valid mysqli connection
* @param $ErrorMessage <string> optional
* @param $DebugMessage <string> optional
* @param $Transaction <bool> default false
* @param $TrapErrors <bool> default true
* @param $BindVars <array> array of typed parameters for $SQL bind var query
*
* @return  $result associative array
*/
function DB_query ($SQL,
				   &$Conn,
				   $ErrorMessage='',
				   $DebugMessage= '',
				   $Transaction=false,
				   $TrapErrors=true,
				   $BindVars = null){

	global $debug;
	global $PathPrefix;
	global $db;

	if (!is_null($BindVars)) {
		$Query = filter_var($SQL, FILTER_SANITIZE_STRING); //sanitize the string
		$stmt = $Conn->prepare($Query);

		if (is_array($BindVars) === true) {
			$params = array(''); // Create the empty 0 index
			foreach ($BindVars as $prop => $val) {

				switch (gettype($val)) {
					case 'NULL':
					case 'string':
						$params[0] .= 's';
						break;

					case 'integer':
					case 'boolean':
						$params[0] .= 'i';
						break;

					case 'blob':
						$params[0] .= 'b';
						break;

					case 'double':
						$params[0] .= 'd';
						break;
				}

				array_push($params, $BindVars[$prop]);
			}
			call_user_func_array(array($stmt, 'bind_param'), refValues($params));
		}

		$stmt->execute();
		$result = bindResults($stmt);
		$AffectedRows = $stmt->affected_rows;

	} else {
		$result=mysqli_query($Conn, $SQL);
		$AffectedRows = DB_affected_rows($result);
	}
	$_SESSION['LastInsertId'] = mysqli_insert_id($Conn);
	if ($DebugMessage == '') {
		$DebugMessage = _('The SQL that failed was');
	}

	if (DB_error_no($Conn) != 0 AND $TrapErrors==true){
		if ($TrapErrors){
			require_once($PathPrefix . 'includes/header.inc');
		}
		prnMsg($ErrorMessage . '<br />' . DB_error_msg($Conn),'error', _('Database Error'). ' ' .DB_error_no($Conn));
		if ($debug==1){
			prnMsg($DebugMessage. '<br />' . $SQL . '<br />','error',_('Database SQL Failure'));
		}
		if ($Transaction){
			$SQL = 'rollback';
			$Result = DB_query($SQL,$Conn);
			if (DB_error_no($Conn) !=0){
				prnMsg(_('Error Rolling Back Transaction'), 'error', _('Database Rollback Error'). ' ' .DB_error_no($Conn) );
			}else{
				prnMsg(_('Rolling Back Transaction OK'), 'error', _('Database Rollback Due to Error Above'));
			}
		}
		if ($TrapErrors){
			include($PathPrefix . 'includes/footer.inc');
			exit;
		}
	} elseif (isset($_SESSION['MonthsAuditTrail']) and (DB_error_no($Conn)==0 AND $_SESSION['MonthsAuditTrail']>0) AND ($AffectedRows>0)){
		$SQLArray = explode(' ', $SQL);

		if (($SQLArray[0] == 'INSERT')
			OR ($SQLArray[0] == 'UPDATE')
			OR ($SQLArray[0] == 'DELETE')) {

			if ($SQLArray[2]!='audittrail'){ // to ensure the auto delete of audit trail history is not logged
				if (is_null($Bindvars)) {
					$AuditString = mysqli_escape_string($db,$SQL);
				} else {
					$AuditString =  mysqli_escape_string($db,$Query ."\n". json_encode($params));
				}
				$AuditSQL = "INSERT INTO audittrail (transactiondate,
									userid,
									querystring)
						VALUES('" . Date('Y-m-d H:i:s') . "',
							'" . trim($_SESSION['UserID']) . "',
							'" . $AuditString . "')";

				$AuditResult = mysqli_query($Conn, $AuditSQL);

			}
		}
	}
	if (!is_null($BindVars)) {
		$stmt->close();
	}
	return $result;

}

function refValues($arr)
{
	//Reference is required for PHP 5.3+
	if (strnatcmp(phpversion(), '5.3') >= 0) {
		$refs = array();
		foreach ($arr as $key => $value) {
			$refs[$key] = & $arr[$key];
		}
		return $refs;
	}
	return $arr;
}

function bindResults(mysqli_stmt $stmt)
{
	$parameters = array();
	$results = array();

	$meta = $stmt->result_metadata();

	// if $meta is false yet sqlstate is true, there's no sql error but the query is
	// most likely an update/insert/delete which doesn't produce any results
	if(!$meta && $stmt->sqlstate) {
		return array();
	}

	$row = array();
	while ($field = $meta->fetch_field()) {
		$row[$field->name] = null;
		$parameters[] = & $row[$field->name];
	}

	call_user_func_array(array($stmt, 'bind_result'), $parameters);

	while ($stmt->fetch()) {
		$x = array();
		foreach ($row as $key => $val) {
			$x[$key] = $val;
		}
		array_push($results, $x);
	}
	if (count($results) == 1) {
		//just return the row as an array
		$results = current($results);
	}
	return $results;
}

function DB_fetch_row (&$ResultIndex) {

	$RowPointer=mysqli_fetch_row($ResultIndex);
	Return $RowPointer;
}

function DB_fetch_assoc (&$ResultIndex) {

	$RowPointer=mysqli_fetch_assoc($ResultIndex);
	Return $RowPointer;
}

function DB_fetch_array (&$ResultIndex) {

	$RowPointer=mysqli_fetch_array($ResultIndex);
	Return $RowPointer;
}

function DB_data_seek (&$ResultIndex,$Record) {
	mysqli_data_seek($ResultIndex,$Record);
}

function DB_free_result (&$ResultIndex){
    if (is_resource($ResultIndex)) {
    	mysqli_free_result($ResultIndex);
    }
}

function DB_num_rows (&$ResultIndex){
	return mysqli_num_rows($ResultIndex);
}

function DB_affected_rows(&$ResultIndex){

	global $db;
	return mysqli_affected_rows($db);

}

function DB_error_no (&$Conn){
	return mysqli_errno($Conn);
}

function DB_error_msg(&$Conn){
	return mysqli_error($Conn);
}

function DB_Last_Insert_ID(&$Conn, $Table, $FieldName){
//	return mysqli_insert_id($Conn);
	if (isset($_SESSION['LastInsertId'])) {
		$Last_Insert_ID = $_SESSION['LastInsertId'];
	} else {
		$Last_Insert_ID = 0;
	}
//	unset($_SESSION['LastInsertId']);
	return $Last_Insert_ID;
}

function DB_escape_string($String){
	global $db;
	return mysqli_real_escape_string($db, $String);
}

function DB_show_tables(&$Conn){
	$Result = DB_query('SHOW TABLES',$Conn);
	Return $Result;
}

function DB_show_fields($TableName, &$Conn){
	$Result = DB_query("DESCRIBE $TableName",$Conn);
	Return $Result;
}

function interval( $val, $Inter ){
		global $dbtype;
		return "\n".'interval ' . $val . ' '. $Inter."\n";
}

function DB_Maintenance($Conn){

	prnMsg(_('The system has just run the regular database administration and optimisation routine.'),'info');

	$TablesResult = DB_query('SHOW TABLES',$Conn);
	while ($myrow = DB_fetch_row($TablesResult)){
		$Result = DB_query('OPTIMIZE TABLE ' . $myrow[0],$Conn);
	}

	$Result = DB_query("UPDATE config
				SET confvalue='" . Date('Y-m-d') . "'
				WHERE confname='DB_Maintenance_LastRun'",
				$Conn);
}

function DB_Txn_Begin($Conn){
	mysqli_query($Conn,'SET autocommit=0');
	mysqli_query($Conn,'START TRANSACTION');
}

function DB_Txn_Commit($Conn){
	mysqli_query($Conn,'COMMIT');
	mysqli_query($Conn,'SET autocommit=1');
}

function DB_Txn_Rollback($Conn){
	mysqli_query($Conn,'ROLLBACK');
}
function DB_IgnoreForeignKeys($Conn){
	mysqli_query($Conn,'SET FOREIGN_KEY_CHECKS=0');
}
function DB_ReinstateForeignKeys($Conn){
	mysqli_query($Conn, 'SET FOREIGN_KEY_CHECKS=1');
}
?>
