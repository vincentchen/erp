<?php
//The scripts used to modify accountcode to varchar(20). If maybe damange your data. So please do not run this sripts unless you konw what you're doing
include('includes/session.inc');
$title = _('Update SQL foreign keys');
include('includes/header.inc');
echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" 
	title="' . _('Search') . '" alt="" />' . '' . $title.'</p><br />';

$sql = "SELECT table_name, constraint_name, column_name, referenced_table_name, referenced_column_name    
					  FROM information_schema.key_column_usage
					  WHERE constraint_schema = '".$_SESSION['DatabaseName']."' 
					  AND referenced_column_name='accountcode' ORDER by table_name DESC";
$ErrMsg = _('Failed to retreive constraint information from schema');
$DbgMsg = _('The SQL failed to retrieve constraint information is ');
$result = DB_query($sql,$db,$ErrMsg,$DbMsg);

//drop those constraint key
db_Txn_begin($db);
DB_data_seek($result,0);
while($myrow = DB_fetch_array($result)) {

	$sql = "ALTER TABLE " . $myrow['table_name'] . " DROP FOREIGN KEY " . $myrow['constraint_name'];
	$ErrMsg = "Failed to drop foreign key " . $myrow['constraint_name'] . '<br/>';
	$DbgMsg = "The SQL failed to drop constrainted foreign key is ";
	$resut1 = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	if(DB_error_no($db)!=0){
		db_Txn_Rollback($db);
	}
	prnMsg(_('The foreign key' . ' ' . $myrow['constraint_name'] . ' ' . 'of table ' . ' ' . $myrow['table_name'] 
		. ' has been drop'),'info');

	$sql = "ALTER TABLE " . $myrow['table_name'] . " MODIFY COLUMN " . $myrow['column_name'] 
		. " varchar(20) NOT NULL DEFAULT '0' ";

	$ErrMsg = "Failed to modify column " . $myrow['column_name'] . '<br/>';
	$DbgMsg = "The SQL failed to modify column is ";
	$result1 = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	if(DB_error_no($db)!=0){
		db_Txn_Rollback($db);
	}
	prnMsg(_('The column'.' ' . $myrow['column_name'] . ' ' . 'has been modified'),'info');
	
}
// Drop primary key 

// Get those tables with accountcode as Primary key

$sql = "SELECT table_name FROM information_schema.columns WHERE column_key='PRI' and column_name='accountcode' and table_schema='" . $_SESSION['DatabaseName'] . "'";
$ErrMsg = "Failed to retrieve primary key information from schema";
$DbgMsg = "The SQL failed to retrive primary key is";
$PRIresult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

while($PRIrow = DB_fetch_array($PRIresult)){

	$sql = "SELECT count(*), table_name FROM information_schema.columns WHERE column_key='PRI' and table_name='" . $PRIrow['table_name']
		. "' and table_schema='" . $_SESSION['DatabaseName'] . "' group by table_name having count(*)>1";
	$ErrMsg = "Failed to retrieve table_name from schema for PRI key";
	$DbgMsg = "The SQL failed to retrieve table_name is ";
	$CompositePRIresult = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	if(DB_num_rows($CompositePRIresult)>0){
		//Get the primary key
		$sqlPRI = "SELECT column_name FROM information_schema.columns WHERE column_key='PRI' AND table_name='" . $PRIrow['table_name'] . "' 
		        	AND table_schema='" . $_SESSION['DatabaseName'] . "'";
		$ErrMsg = "Failed to get composite Primary key columns ";
		$DbgMsg = "The SQL failed to get composite primary key is ";
		$CompositePRIcolums = DB_query($sqlPRI,$db,$ErrMsg,$DbgMsg);
		$PRIComponent='';
		while($CompositePRIrow = DB_fetch_array($CompositePRIcolums)){
			$PRIComponent .= $CompositePRIrow['column_name'] . ',';
		}
		$PRIComponent = substr($PRIComponent,0,-1);

		//Drop the composit primary key
		$sql = "ALTER table " . $PRIrow['table_name'] . " DROP PRIMARY KEY ";
		$ErrMsg = "Failed to drop primary key of table " . $PRIrow['table_name'];
		$DbgMsg = "The SQL failed to drop primary key is ";
		$DropPRIresult = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		if(DB_error_no($db)!=0){
			DB_Txn_Rollback($db);
		}
		prnMsg('Primary key of table '.$PRIrow['table_name'] . ' has been dropped','info');

		//Modify the accountcode column to varchar(20)
		$sql = "ALTER table " . $PRIrow['table_name'] . " MODIFY column accountcode varchar(20) NOT NULL DEFAULT '0'";
		$ErrMsg = "Failed to modify column of table " . $PRIrow['table_name'];
		$DbgMsg = "The SQL failed to modify column is ";
		$ModifyPRIColumn = DB_query($sql,$db,$ErrMsg,$DgbMsg);
		if(DB_error_no($db)!=0){
			DB_Txn_Rollback($db);
		}
		prnMsg('Column accountcode for table ' . $PRIrow['table_name'] . ' has been modified','info');

		//Restore the composite primary key
		$sql = "ALTER table " . $PRIrow['table_name'] . " ADD PRIMARY KEY (" . $PRIComponent . ")";
		$ErrMsg = "Failed to add the composite primary key for table " . $PRIrow['table_name'];
		$DbgMsg = "The SQL failed to add primary key is ";
		$AddPRIresult = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		if(DB_error_no($db)!=0){
			DB_Txn_Rollback($db);
		}
		prnMsg('Primary key (' . $PRIComponent . ') has been added to table ' . $PRIrow['table_name'], 'info');

	}//end of dealing with one composite primary key
}//end of composite primary key handling loop

//It's time to alter the column of chartmaster
$sql = "ALTER TABLE chartmaster MODIFY COLUMN accountcode varchar(20) NOT NULL Default '0'";
$ErrMsg = "Failed to alter table chartmaster ";
$DbgMsg = "The SQL failed to modify chartmaster is ";
$Alterresult = DB_query($sql,$db,$ErrMsg,$DbgMsg);
if(DB_error_no($db)!=0){
	DB_Txn_Rollback($db);
}
prnMsg('Column accountcode for table chartmaster has been modified','info');

//rebuild foreign keys, get the information from  $result
db_data_seek($result,0);
while($myrow = DB_fetch_array($result)){
	$FKsql = "ALTER TABLE " . $myrow['table_name'] . " ADD CONSTRAINT " . $myrow['constraint_name'] . " FOREIGN KEY (" . $myrow['column_name'] . ") REFERENCES " 
		. $myrow['referenced_table_name'] . "(" . $myrow['referenced_column_name'] . ")";
	$ErrMsg = "Failed to add foreign key " . $myrow['column_name'] . " for column " . $myrow['column_name'] . " for table " . $myrow['table_name'];
	$DbgMsg = "The SQL faild to add foreign key is ";
	$AddFKresult = DB_query($FKsql,$db,$ErrMsg,$DbgMsg);
	if(DB_error_no($db)!=0){
		DB_Txn_Rollback($db);
	}
	prnMsg('CONSTRAINT ' . $myrow['constraint_name'] . ' FOREIGN KEY (' . $myrow['column_name'] . ') REFERENCES ' . $myrow['referenced_table_name'] .'(' . $myrow['referenced_column_name'] . ') has been added to table ' . $myrow['table_name'], 'info');

} //end of adding foreign key loop

db_Txn_commit($db);

prnMsg('Update the accountcode to varchar(20) succeed', 'success');
?>









	









