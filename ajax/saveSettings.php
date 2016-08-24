<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR .  "framework.php";

$fk_customer    = intval(filter_input(INPUT_POST, "fk_customer"));
$fk_warehouse   = intval(filter_input(INPUT_POST, "fk_warehouse"));
$fk_pricelevel  = intval(filter_input(INPUT_POST, "fk_pricelevel"));
$ticket_path    = filter_input(INPUT_POST, "ticket_path");
$cash_register  = filter_input(INPUT_POST, "cash_register");
$serial_port    = filter_input(INPUT_POST, "serial_port");
$baudrate       = intval(filter_input(INPUT_POST, "baudrate"));
$parity         = filter_input(INPUT_POST, "parity");
$charlength     = intval(filter_input(INPUT_POST, "charlength"));
$stopbits       = intval(filter_input(INPUT_POST, "stopbits"));
$flowcontrol    = filter_input(INPUT_POST, "flowcontrol");

$query="delete from ".MAIN_DB_PREFIX."simplepos_settings";
$db->query($query);

$columns = [
    "`fk_customer`",
    "`fk_warehouse`",
    "`fk_pricelevel`",
    "`ticket_path`",
    "`cash_register`",
    "`serial_port`",
    "`baudrate`",
    "`parity`",
    "`charlength`",
    "`stopbits`",
    "`flowcontrol`"
];

$values = [
    $fk_customer,
    $fk_warehouse,
    $fk_pricelevel,
    "'" . mysql_escape_string($ticket_path) . "'",
    "'" . mysql_escape_string($cash_register) . "'",
    "'" . mysql_escape_string($serial_port) . "'",
    $baudrate,
    "'" . mysql_escape_string($parity) . "'",
    $charlength,
    $stopbits,
    "'" . mysql_escape_string($flowcontrol) . "'"
];

$insert = "INSERT INTO " . MAIN_DB_PREFIX . "simplepos_settings (" . implode(",",$columns) . ") VALUES (" . implode(",",$values) . ");";

$result =  $db->query($insert);
if(!$result)
{
    print $insert;
    print $db->lasterrno() . ": " . $db->lasterror();
}
else
{
    print "1";
}
        
        