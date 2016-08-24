<?php

$dir=__DIR__;
$explode=explode(DIRECTORY_SEPARATOR .  "htdocs",$dir);
$root=$explode[0];
$htdocs = DIRECTORY_SEPARATOR . "htdocs" . DIRECTORY_SEPARATOR ; 

require_once $root. $htdocs . "filefunc.inc.php";
require_once $root. $htdocs . "core" . DIRECTORY_SEPARATOR . "db" . DIRECTORY_SEPARATOR . "mysqli.class.php";
require_once $root. $htdocs . "conf" .DIRECTORY_SEPARATOR . "conf.php";

$dbUser=$dolibarr_main_db_user;
$dbPass=$dolibarr_main_db_pass;
$dbType=$dolibarr_main_db_type;
$dbName=$dolibarr_main_db_name;
$dbHost=$dolibarr_main_db_host;
$user=$_SESSION["pos-user"];
$pass=$_SESSION["pos-pass"];
$db=new DoliDBMysqli($dbType, $dbHost, $dbUser, $dbPass);
$db->connect($dbHost, $dbUser, $dbPass, $dbName);

function escape($input)
{
	return str_ireplace("'", "\'", $input);
}

function delLastChar($input)
{
	return substr($input, 0, strlen($input)-1);
}

function getLastId($tablename,$field)
{
	global $db;
	$query = "select max($field) as lastid from $tablename";
	
	$ret = $db->query($query);
	if($ret)
	{
		$rs = $db->fetch_object($ret);
		return $rs->lastid;
	}
	else
	{
		return 0;
	}
}