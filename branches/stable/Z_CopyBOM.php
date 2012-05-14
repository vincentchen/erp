<?php
/**
 * Author: Ashish Shukla <gmail.com!wahjava>
 *
 * Script to duplicate BoMs.
 */
/* $Id$*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

if(isset($_POST['Submit'])) {
  $StkID = $_POST['StkID'];
  $type = $_POST['type'];
  $newStkID = '';
  $InputError = 0; //assume the best
  if($type == 'N') {
	  $newStkID = $_POST['toStkID'];
	  if(empty($newStkID)){
		  $InputError = 1;
		  prnMsg(_('The new item code cannot be blank. Enter a new code for the item to copy the BOM to'),'error');
	  }
  } else {
    $newStkID = $_POST['exStkID'];
  }

  if($InputError == 0){
  DB_Txn_Begin($db);

  if($type == 'N') {
      /* duplicate rows into stockmaster */
	  $sql = "INSERT INTO stockmaster (stockid,
		  				categoryid,
						description,
						longdescription,
						units,
						mbflag,
						actualcost,
						lastcost,
						materialcost,
						labourcost,
						overheadcost,
						lowestlevel,
						discontinued,
						controlled,
						eoq,
						volume,
						kgs,
						barcode,
						discountcategory,
						taxcatid,
						serialised,
						appendfile,
						perishable,
						decimalplaces)
			              select '" . $newStkID . "' as stockid,
						categoryid,
						description,
						longdescription,
						units,
						mbflag,
						actualcost,
						lastcost,
						materialcost,
						labourcost,
						overheadcost,
						lowestlevel,
						discontinued,
						controlled,
						eoq,
						volume,
						kgs,
						barcode,
						discountcategory,
						taxcatid,
						serialised,
						appendfile,
						perishable,
						decimalplaces
					FROM stockmaster
					WHERE stockid='".$StkID."';";
      $result = DB_query($sql, $db);
    }
  else
    {
      $sql = "SELECT actualcost, lastcost, materialcost, labourcost, overheadcost, lowestlevel
              FROM stockmaster WHERE stockid='".$StkID."';";
      $result = DB_query($sql, $db);

      $row = DB_fetch_row($result);

      $sql = "UPDATE stockmaster SET
		              actualcost      = ".$row[0].",
		              lastcost        = ".$row[1].",
		              materialcost    = ".$row[2].",
		              labourcost      = ".$row[3].",
		              overheadcost    = ".$row[4].",
		              lowestlevel     = ".$row[5]."
		       WHERE stockid='".$newStkID."';";
      $result = DB_query($sql, $db);
    }

  $sql = "INSERT INTO bom
				SELECT '".$newStkID."' as parent,
						component,
						workcentreadded,
						loccode,
						effectiveafter,
						effectiveto,
						quantity,
						autoissue
				FROM bom
				WHERE parent='".$StkID."';";
  $result = DB_query($sql, $db);

  if($type == 'N')
    {
      $sql = "INSERT INTO locstock
		      SELECT loccode, '".$newStkID."' as stockid,0 as quantity,
		      reorderlevel
		      FROM locstock
		      WHERE stockid='".$StkID."';";
      $result = DB_query($sql, $db);
    }

  DB_Txn_Commit($db);

  UpdateCost($db, $newStkID);

  header('Location: BOMs.php?Select='.$newStkID);
  }
}

 else
   {
     $title = _('UTILITY PAGE To Copy a BOM');
     include('includes/header.inc');

     echo '<form method="post" action="Z_CopyBOM.php">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

     $sql = "SELECT stockid, description FROM stockmaster WHERE stockid IN (SELECT DISTINCT parent FROM bom) AND  mbflag IN ('M', 'A', 'K','G');";
     $result = DB_query($sql, $db);

     echo '<br />
			<br />' . _('From Stock ID') . ': <select name="StkID">';
     while($row = DB_fetch_row($result)) {
		echo '<option value="' . $row[0] . '">' . $row[0]. ' -- ' . $row[1] . '</option>';
	}
	echo '</select>
		<br/>
		<input type="radio" name="type" value="N" checked="" />' ._('To New Stock ID') .':
		<input type="text" maxlength="20" name="toStkID" />';
	
	$sql = "SELECT stockid, 
					description 
			FROM stockmaster 
			WHERE stockid NOT IN (SELECT DISTINCT parent FROM bom) 
			AND mbflag IN ('M', 'A', 'K','G');";
	$result = DB_query($sql, $db);
	
	if(DB_num_rows($result) > 0) {
		echo '<br/>
			<input type="radio" name="type" value="E" />' . _('To Existing Stock ID') . ': 
			<select name="exStkID">';
		while($row = DB_fetch_row($result)){
			echo '<option value="' . $row[0] .'">' . $row[0] . ' -- ' . $row[1] . '</option>';
		}
		echo '</select>';
	}
	
	echo '<br />
		<input type="submit" name="Submit" value="' . _('Submit') . '" />';
	
	include('includes/footer.inc');
}
?>