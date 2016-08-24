<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "SimpleSerial.php";
$serial = new SimpleSerial("/dev/ttyUSB0", 19200, 8, "none", 1);
$serial->initialize();
$serial->msg("5*1000H1R");
$serial->msg("H1T");
exit;
//print exec("echo 2f > /dev/ttyUSB0");

print exec('echo "\"Succo"5*150H1R\" > /dev/ttyUSB0');
//print exec("echo \"Semi di girasole\"2*199H1R > /dev/ttyUSB0");
//print exec("echo \"Pane integrale 500gr.\"1*100H2R > /dev/ttyUSB0");
//print exec("echo \"Pane di soia 500gr.\"1*100H2R > /dev/ttyUSB0");
//print exec("echo \"Wurstel di soia 500gr\"2*299H2R > /dev/ttyUSB0");
//print exec("echo \"Seitan 500gr.\"1*799H1R > /dev/ttyUSB0");
print exec('echo \"5000H1T\" > /dev/ttyUSB0');

exit;


error_reporting(E_ALL);
ini_set('display_errors', 1);

include dirname(__FILE__) . DIRECTORY_SEPARATOR . "PhpSerial.php";

// Let's start the class
$serial = new PhpSerial();

// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
//$serial->deviceSet("COM1");
$serial->deviceSet("/dev/ttyUSB0");

// We can change the baud rate, parity, length, stop bits, flow control
$serial->confBaudRate(19200);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl("none");

// Then we need to open it
$serial->deviceOpen();

// To write into
$serial->sendMessage("\"Succo\"5*150H1R");
$serial->sendMessage("\"Semi di girasole\"2*199H1R");
$serial->sendMessage("\"Pane integrale 500gr.\"1*100H2R");
$serial->sendMessage("\"Pane di soia 500gr.\"1*100H2R");
$serial->sendMessage("\"Wurstel di soia 500gr\"2*299H2R");
$serial->sendMessage("\"Seitan 500gr.\"1*799H1R");
$serial->sendMessage("5000H1T");



$serial->deviceClose();
