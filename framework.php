<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$basepath = explode("htdocs",$_SERVER["REQUEST_URI"]);
$baseroot = $_SERVER["DOCUMENT_ROOT"] . $basepath[0] . "htdocs" . DIRECTORY_SEPARATOR;


require_once $baseroot . "main.inc.php";
//require_once $baseroot . "filefunc.inc.php";
//require_once $baseroot . "core" . DIRECTORY_SEPARATOR . "db" . DIRECTORY_SEPARATOR . "mysqli.class.php";
//require_once $baseroot . "conf" . DIRECTORY_SEPARATOR . "conf.php";

//$dbUser=$dolibarr_main_db_user;
//$dbPass=$dolibarr_main_db_pass;
//$dbType=$dolibarr_main_db_type;
//$dbName=$dolibarr_main_db_name;
//$dbHost=$dolibarr_main_db_host;
//$db=new DoliDBMysql($dbType, $dbHost, $dbUser, $dbPass);
//$db->connect($dbHost, $dbUser, $dbPass, $dbName);

//if (!$db->select_db($dbName))
//{
//    print "<h1>DATABASE NON SELEZIONATO</h1>";
//    exit;
//}

function escape($input)
{
	return str_ireplace("'", "\'", $input);
}

function delLastChar($input)
{
	return substr($input, 0, strlen($input)-1);
}