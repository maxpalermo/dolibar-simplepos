<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$action=filter_input(INPUT_POST,"action");

include dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "SimpleSerial.php";

// Let's start the class
$serial = new SimpleSerial("/dev/ttyUSB0", 19200, 8, "none", 1);
$serial->initialize();

// To write into
switch($action)
{
    case "readrep":
        $serial->msg("2f");
        break;
    case "readday":
        $serial->msg("1f");
        break;
    case "zerorep":
        $serial->msg("2F");
        break;
    case "closeday":
        $serial->msg("1F");
        break;
}