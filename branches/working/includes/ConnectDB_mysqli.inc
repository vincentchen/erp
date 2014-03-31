<?php
/* $Id$ */

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
	echo _('Connect failed') . ': ' . mysqli_connect_error();
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

class mysqliStatementResultWrapper implements Iterator {
    public $num_rows;
    public $field_count;
    private $results;
    private $position;
    private $meta;
    
    public function __construct(mysqli_stmt &$Statement) {
        $this->position = 0;
        $this->results = bindResults($Statement);
        $this->num_rows = count($this->results);
        $this->meta = $Statement->result_metadata();
    }
    
    //in order to use this in foreach loops, needs these functions (a part of implementing Iterator)
    function rewind() {
        $this->position = 0;
    }
    
    function current() {
        return $this->results[$this->position];
    }
    
    function key() {
        return $this->position;
    }
    
    function next() {
        ++$this->position;
    }
    
    function valid() {
        return isset($this->results[$this->position]);
    }
    
    public function fetch_row() {
        if ($this->valid()) {
            $return = $this->results[$this->position][1];
            $this->next();
            return $return;
        } else {
            return false;
        }
    }
    
    public function fetch_assoc() {
        if ($this->valid()) {
            $return = $this->results[$this->position][0];
            $this->next();
            return $return;
        } else {
            return false;
        }
    }
    
    public function fetch_array($resulttype = MYSQLI_BOTH) {
        switch($resulttype) {
            case MYSQLI_BOTH:
                if ($this->valid()) {
                    //return both associative and integer positions
                    return $this->results[$this->position][0] + $this->results[$this->position][1];
                } else {
                    return false;
                }
                break;
            case MYSQLI_ASSOC:
                return $this->fetch_assoc();
                break;
            case MYSQLI_NUM:
                return $this->fetch_row();
                break;
        }
    }
    
    public function data_seek($whichRow) {
        if (isset($this->results[$whichRow])) {
            $this->position = $whichRow;
            return true;
        } else {
            return false;
        }
    }
    
    function close() {
    
    }
    
}


/**
* DB wrapper functions to change only once for whole application
* Will accept an array $Parameters for prepared statement with bound variables
*
* @param $SQL MySQL query string with or without bindvars
* @param $Conn  valid mysqli connection
* @param $ErrorMessage <string> optional
* @param $DebugMessage <string> optional
* @param $Transaction <bool> default false
* @param $TrapErrors <bool> default true
* @param $Parameters <array> array of typed parameters for $SQL bind var query
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

	if (isset($Parameters)) {
		$Query = filter_var($SQL, FILTER_SANITIZE_STRING); //sanitize the string
		if (!($PreparedStatement = $db->prepare($Query))) {
            if ($TrapErrors){
                require_once($PathPrefix . 'includes/header.inc');
            }
            prnMsg($ErrorMessage . '<br />' . DB_error_msg($db),'error', _('Database Error'). ' ' .DB_error_no($db));
            if ($debug==1){
                prnMsg($DebugMessage. '<br />' . $SQL . '<br />','error',_('Database SQL Failure'));
            }
            if ($TrapErrors){
                include($PathPrefix . 'includes/footer.inc');
                exit;
            }
        }

		if (is_array($Parameters) === true) {
			$PreparedParameters = array(''); // Create the empty 0 index array in the format required for
			foreach ($Parameters as $ParameterKey => $ParameterValue) {

				switch (gettype($ParameterValue)) {
					case 'integer':
					case 'boolean':
						$PreparedParameters[0] .= 'i';
						break;

					case 'blob':
						$PreparedParameters[0] .= 'b';
						break;

					case 'double':
						$PreparedParameters[0] .= 'd';
						break;
                    default:
                        $PreparedParameters[0] .= 's';
				}
                
				array_push($PreparedParameters, $Parameters[$ParameterKey]);
			}
			call_user_func_array(array($PreparedStatement, 'bind_param'), refValues($PreparedParameters));
		} else {
            switch(gettype($Parameters)) {
                case 'integer':
                case 'boolean':
                    $PreparedParameter = 'i';
                    break;
                case 'double':
                    $PreparedParameter = 'd';
                    break;
                default:
                    $PreparedParameter = 's';
            }
            $PreparedStatement->bind_param($PreparedParameter,$Parameters);
        }

		
		$PreparedStatement->execute();
        $PreparedStatement->store_result(); //make sure row_count will be accurate
        $result = new mysqliStatementResultWrapper($PreparedStatement);
        $PreparedStatement->close();
	} else {
		$result=mysqli_query($db, $SQL);
	}
    
	$_SESSION['LastInsertId'] = mysqli_insert_id($db);
	if ($DebugMessage == '') {
		$DebugMessage = _('The SQL that failed was');
	}

	if (DB_error_no($db) != 0 AND $TrapErrors==true){
		if ($TrapErrors){
			require_once($PathPrefix . 'includes/header.inc');
		}
		prnMsg($ErrorMessage . '<br />' . DB_error_msg($db),'error', _('Database Error'). ' ' .DB_error_no($db));
		if ($debug==1){
			prnMsg($DebugMessage. '<br />' . $SQL . '<br />','error',_('Database SQL Failure'));
		}
		if ($Transaction){
			$SQL = 'rollback';
			$Result = DB_query($SQL);
			if (DB_error_no($db) !=0){
				prnMsg(_('Error Rolling Back Transaction'), 'error', _('Database Rollback Error'). ' ' .DB_error_no($db) );
			}else{
				prnMsg(_('Rolling Back Transaction OK'), 'error', _('Database Rollback Due to Error Above'));
			}
		}
		if ($TrapErrors){
			include($PathPrefix . 'includes/footer.inc');
			exit;
		}
	} elseif (isset($_SESSION['MonthsAuditTrail']) && ($db->errno==0 && $_SESSION['MonthsAuditTrail']>0) && ($db->affected_rows)>0){
		$SQLArray = explode(' ', $SQL);

		if (($SQLArray[0] == 'INSERT')
			OR ($SQLArray[0] == 'UPDATE')
			OR ($SQLArray[0] == 'DELETE')) {

			if ($SQLArray[2]!='audittrail'){ // to ensure the auto delete of audit trail history is not logged
				if (is_null($Bindvars)) {
					$AuditString = $db->mysqli_real_escape_string($SQL);
				} else {
					$AuditString =  $db->mysqli_escape_string($Query ."\n". json_encode($PreparedParameters));
				}
				}
				$AuditSQL = "INSERT INTO audittrail (transactiondate,
									userid,
									querystring)
						VALUES('" . Date('Y-m-d H:i:s') . "',
							'" . trim($_SESSION['UserID']) . "',
							'" . $AuditString . "')";

				$AuditResult = DB_query($AuditSQL);

			}
		}
	return $result;

}

function refValues($ArrayVariable) {
	//Reference is required for PHP 5.3+
	if (strnatcmp(phpversion(), '5.3') >= 0) {
		$Refs = array();
		foreach ($ArrayVariable as $ArrayKey => $ArrayValue) {
			$Refs[$ArrayKey] = & $ArrayVariable[$ArrayKey];
		}
		return $Refs;
	}
	return $ArrayVariable;
}

function bindResults(mysqli_stmt $PreparedStatement) {
	$Parameters = array();
	$results = array();
	$Meta = $PreparedStatement->result_metadata();

	// if $meta is false yet sqlstate is true, there's no sql error but the query is
	// most likely an update/insert/delete which doesn't produce any results
	if(!$Meta && $PreparedStatement->sqlstate) {
		return array();
	}

	$Row = array();
	while ($Field = $Meta->fetch_field()) {
		$Row[$Field->name] = null;
		$Parameters[] = & $Row[$Field->name];
	}

	call_user_func_array(array($PreparedStatement, 'bind_result'), $Parameters);

	while ($PreparedStatement->fetch()) {
		$assocResults = array();
        $arrayResults = array();
        $i = 0;
		foreach ($Row as $key => $val) {
			$assocResults[$key] = $val;
            $arrayResults[$i] = $val;
            $i++;
		}
		array_push($results, array($assocResults,$arrayResults));
	}

	return $results;
}



function DB_query ($SQL,
					&$Conn = null,
					$ErrorMessage='',
					$DebugMessage= '',
					$Transaction=false,
					$TrapErrors=true){

	global $debug;
	global $PathPrefix;
    global $db;
    

	$result= $db->query($SQL);

	$_SESSION['LastInsertId'] = mysqli_insert_id($db);

	if ($DebugMessage == '') {
		$DebugMessage = _('The SQL that failed was');
	}

	if (DB_error_no($db) != 0 AND $TrapErrors==true){
		if ($TrapErrors){
			require_once($PathPrefix . 'includes/header.inc');
		}
		prnMsg($ErrorMessage . '<br />' . DB_error_msg($db),'error', _('Database Error'). ' ' .DB_error_no($db));
		if ($debug==1){
			prnMsg($DebugMessage. '<br />' . $SQL . '<br />','error',_('Database SQL Failure'));
		}
		if ($Transaction){
			$SQL = 'rollback';
			$Result = DB_query($SQL);
			if (DB_error_no($db) !=0){
				prnMsg(_('Error Rolling Back Transaction'), 'error', _('Database Rollback Error'). ' ' .DB_error_no($db) );
			}else{
				prnMsg(_('Rolling Back Transaction OK'), 'error', _('Database Rollback Due to Error Above'));
			}
		}
		if ($TrapErrors){
			include($PathPrefix . 'includes/footer.inc');
			exit;
		}
	} elseif (isset($_SESSION['MonthsAuditTrail']) and (DB_error_no($db)==0 AND $_SESSION['MonthsAuditTrail']>0) AND (DB_affected_rows($result)>0)){

		$SQLArray = explode(' ', $SQL);

		if (($SQLArray[0] == 'INSERT')
			OR ($SQLArray[0] == 'UPDATE')
			OR ($SQLArray[0] == 'DELETE')) {

			if ($SQLArray[2]!='audittrail'){ // to ensure the auto delete of audit trail history is not logged
				$AuditSQL = "INSERT INTO audittrail (transactiondate,
									userid,
									querystring)
						VALUES('" . Date('Y-m-d H:i:s') . "',
							'" . trim($_SESSION['UserID']) . "',
							'" . DB_escape_string($SQL) . "')";

				$AuditResult = $db->query($AuditSQL);
			}
		}
	}
	return $result;

}




function DB_fetch_row (&$Result) {
	Return $Result->fetch_row();
}

function DB_fetch_assoc (&$Result) {
	Return $Result->fetch_assoc();
}

function DB_fetch_array (&$Result) {
	return $Result->fetch_array();
}

function DB_data_seek (&$Result,$Record) {
    if ($Result) {
        return $Result->data_seek($Record);
    }

}

function DB_free_result (&$Result){
    if ($Result) {
        $Result->close();
    }
}

function DB_num_rows (&$Result){
    If ($Result) {
        return $Result->num_rows;
    } else {
        return 0;
    }
}

function DB_affected_rows(){
	global $db;
	return mysqli_affected_rows($db);
}

function DB_error_no (){
    global $db;
	return mysqli_errno($db);
}

function DB_error_msg(){
    global $db;
	return mysqli_error($db);
}

function DB_Last_Insert_ID(&$Conn = null, $Table = null, $FieldName = null){
	return (isset($_SESSION['LastInsertId'])) ? $_SESSION['LastInsertId'] : 0;
}

function DB_escape_string($String){
	global $db;
	return mysqli_real_escape_string($db, $String);
}

function DB_show_tables(){
	Return DB_query('SHOW TABLES');
}

function DB_show_fields($TableName){
	$Result = DB_query("DESCRIBE `$TableName`");
	Return $Result;
}

function interval( $val, $Inter ){
		global $dbtype;
		return "\n".'interval ' . $val . ' '. $Inter."\n";
}

function DB_Maintenance($Conn){

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
	mysqli_query($db,'SET autocommit=0');
	mysqli_query($db,'START TRANSACTION');
}

function DB_Txn_Commit(){
    global $db;
	mysqli_query($db,'COMMIT');
	mysqli_query($db,'SET autocommit=1');
}

function DB_Txn_Rollback(){
    global $db;
	$db->rollback();
}
function DB_IgnoreForeignKeys(){
    global $db;
	mysqli_query($db,'SET FOREIGN_KEY_CHECKS=0');
}
function DB_ReinstateForeignKeys(){
    global $db;
	mysqli_query($db, 'SET FOREIGN_KEY_CHECKS=1');
}
?>
