<?php
/* $Id: ConnectDB_PDO_mysql.inc 6653 2014-03-31 02:19:22Z serakfalcon $ */

define ('LIKE','LIKE');

if (!isset($mysqlport)){
	$mysqlport = 3306;
}
global $db;	// Make sure it IS global, regardless of our context
global $AffectedRows; //PDO does this at the statement level, emulate


try {
    if (isset($DatabaseName)) {
        $db = new PDO('mysql:host=' . $host . ';' . (($mysqlport==3306) ? '' : 'port=' . $mysqlport . ';') . 'dbname='. $DatabaseName . ';charset=UTF8', $DBUser, $DBPassword);
    } else {
        $db = new PDO('mysql:host=' .$host. ';' . (($mysqlport==3306) ? '' : 'port=' . $mysqlport . ';') . 'dbname='. $_SESSION['DatabaseName'].';charset=UTF8', $DBUser, $DBPassword);
    }
} catch (PDOException $exception) {
    echo _('Connect failed') . ': ' . $exception->getMessage();
	session_unset();
	session_destroy();
	echo '<p>' . _('Click') . ' ' . '<a href="index.php">' . _('here') . '</a>' . ' '  ._('to try logging in again') . '</p>';
	exit();
}

if ( !$db ) {
	echo '<br />' . _('The configuration in the file config.php for the database user name and password do not provide the information required to connect to the database server');
	exit;
}

$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //prevent injections by forcing 'true' prepared statements

require_once ($PathPrefix .'includes/MiscFunctions.php');

/**
* DB wrapper functions to change only once for whole application
* Will accept an array $Parameters for prepared statement with bound variables
*
* @param $SQL MySQL query string with or without bindvars
* @param $Parameters <array> array of typed parameters for $SQL bind var query
* @param $ErrorMessage <string> optional
* @param $DebugMessage <string> optional
* @param $Transaction <bool> default false
* @param $TrapErrors <bool> default true
*
* @return  $result associative array
*/



function DB_PreparedQuery ($SQL,
                           $Parameters = null,
						   $ErrorMessage='',
						   $DebugMessage= '',
						   $Transaction=false,
						   $TrapErrors=true){

	global $debug;
	global $PathPrefix;
	global $db;
    global $AffectedRows;
    
    $AffectedRows = 0;
    if ($Transaction) {
        $db->beginTransaction();
    }
    
	if (isset($Parameters)) {
		$Query = filter_var($SQL, FILTER_SANITIZE_STRING); //sanitize the string
		if (!($PreparedStatement = $db->prepare($Query))) {
            if ($TrapErrors){
                require_once($PathPrefix . 'includes/header.inc');
            }
            prnMsg($ErrorMessage . '<br />' . DB_error_msg(),'error', _('Database Error'). ' ' .DB_error_no());
            if ($debug==1){
                prnMsg($DebugMessage. '<br />' . $SQL . '<br />','error',_('Database SQL Failure'));
            }
            if ($TrapErrors){
                include($PathPrefix . 'includes/footer.inc');
                exit;
            }
        }

		if (is_array($Parameters) === true) {
			$PreparedStatement->execute($Parameters);
		} else {
            $PreparedStatement->execute(array($Parameters));
        }
        $result = $PreparedStatement;
	} else {
		$result= $db->query($SQL);
	}
    
    
    if ($Transaction) {
        $db->commit();
    }
    
    $_SESSION['LastInsertId'] = $db->lastInsertId;
	if ($DebugMessage == '') {
		$DebugMessage = _('The SQL that failed was');
	}
    
	if (DB_error_no($db) != 0 AND $TrapErrors==true){
		if ($TrapErrors){
			require_once($PathPrefix . 'includes/header.inc');
		}
		prnMsg($ErrorMessage . '<br />' . DB_error_msg(),'error', _('Database Error'). ' ' .DB_error_no());
		if ($debug==1){
			prnMsg($DebugMessage. '<br />' . $SQL . '<br />','error',_('Database SQL Failure'));
		}
		if ($Transaction) {
			$db->rollback();
			if (DB_error_no() !=0){
				prnMsg(_('Error Rolling Back Transaction'), 'error', _('Database Rollback Error'). ' ' .DB_error_no() );
			}else{
				prnMsg(_('Rolling Back Transaction OK'), 'error', _('Database Rollback Due to Error Above'));
			}
		}
		if ($TrapErrors){
			include($PathPrefix . 'includes/footer.inc');
			exit;
		}
	} elseif (isset($_SESSION['MonthsAuditTrail']) && (DB_error_no()==0 && $_SESSION['MonthsAuditTrail']>0) && ($result->columnCount() == 0) && ($result->rowCount() > 0)){
            $SQLArray = explode($query,' ');
            /*db info works on insert, update, alter table or load data infile, otherwise look for a delete
            if the third element is audittrail, don't log this as it is either DELETE FROM audittrail or INSERT INTO audittrail.  */
            if ($SQLArray[2] != 'audittrail') {
                $db->executeQuery(  'INSERT INTO audittrail (transactiondate,userid,querystring)
                                    VALUES(?,?,?);',array(Date('Y-m-d H:i:s'),trim($_SESSION['UserID']),$query . '/n' . json_encode($params)));
 
            }
    }
        

        //PDO mysql will return rowCount for all queries, make sure it's not a select query
    if (!$result->columnCount()) {
        $AffectedRows = $result->rowCount();
    }
	return $result;

}

function DB_query ($SQL,
					&$Conn = null,
					$ErrorMessage='',
					$DebugMessage= '',
					$Transaction=false,
					$TrapErrors=true){

	return DB_PreparedQuery($SQL,null,$ErrorMessage,$DebugMessage,$Transaction,$TrapErrors);

}




function DB_fetch_row (&$Result) {
	Return $Result->fetch(PDO::FETCH_BOTH);
}

function DB_fetch_assoc (&$Result) {
	Return $Result->fetch(PDO::FETCH_ASSOC);
}

function DB_fetch_array (&$Result) {
	return $Result->fetch(PDO::FETCH_BOTH);
}

function DB_data_seek (&$Result,$Record) {
    if ($Result) {
        $return = $Result->fetchAll();
        return $return[$Record];
    }

}

function DB_free_result (&$Result){
    if ($Result) {
        $Result->closeCursor();
    }
}

function DB_num_rows (&$Result){
    //PDO_mysql supports num rows but returns affected rows if a select: don't allow that behavior
    if ($Result->columnCount()) {
        return $Result->rowCount();
    } else {
        return 0;
    }
}

function DB_affected_rows(){
	global $AffectedRows;
    return $AffectedRows;
}

function DB_error_no() {
    global $db;
	return $db->errorCode();
}

function DB_error_msg(){
    global $db;
	return $db->errorInfo();
}

function DB_Last_Insert_ID(&$Conn = null, $Table = null, $FieldName = null){
	return (isset($_SESSION['LastInsertId'])) ? $_SESSION['LastInsertId'] : 0;
}

function DB_escape_string($String){
	global $db;
	return $db->quote($String);
}

function DB_show_tables(){
	Return DB_query('SHOW TABLES');
}

function DB_show_fields($TableName){
	Return DB_query("DESCRIBE `$TableName`");
}

function interval( $val, $Inter ){
		global $dbtype;
		return "\n".'interval ' . $val . ' '. $Inter."\n";
}

function DB_Maintenance(){

	prnMsg(_('The system has just run the regular database administration and optimisation routine.'),'info');

	$TablesResult = DB_query('SHOW TABLES');
	while ($myrow = DB_fetch_row($TablesResult)){
		$Result = DB_query('OPTIMIZE TABLE `' . $myrow[0] . '`');
	}

	$Result = DB_query("UPDATE config
				SET confvalue='" . Date('Y-m-d') . "'
				WHERE confname='DB_Maintenance_LastRun'");
}

function DB_Txn_Begin(){
    global $db;
    $db->beginTransaction();
}

function DB_Txn_Commit(){
    global $db;
	$db->commit();
}

function DB_Txn_Rollback(){
    global $db;
	$db->rollback();
}
function DB_IgnoreForeignKeys(){
    global $db;
	$db->query('SET FOREIGN_KEY_CHECKS=0');
}
function DB_ReinstateForeignKeys(){
    global $db;
	$db->query('SET FOREIGN_KEY_CHECKS=1');
}
?>
