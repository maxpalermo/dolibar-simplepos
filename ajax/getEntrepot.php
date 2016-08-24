<?php
header("content-type:application/json");
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR .  "framework.php";

//Get records from database
$term = filter_input(INPUT_GET, "term");
//$term=escape($_GET['term']);

//Get records from database
$query="select rowid,label from ".MAIN_DB_PREFIX."entrepot where label like '%$term%' order by label LIMIT 50";

//print $query;
$rows = array();
$result = $db->query($query);

while($row = $db->fetch_object($result))
{
	$record=new stdClass();
	/*
        $record['result']="OK";
	
	$structRec=array();
		$structRec['rowid']=$row->rowid;
		$structRec['name']=htmlentities($row->nom);
		$structRec['address']=htmlentities($row->address);
		$structRec['town']=htmlentities($row->town);
	
	$record['record']=$structRec; 
         */
        $record->id = $row->rowid;
        $record->label = $row->label;
        $record->value = $row->label;
	$rows[]=$record;
};

//Return result to jTable
print json_encode($rows);
//print_r ($rows);