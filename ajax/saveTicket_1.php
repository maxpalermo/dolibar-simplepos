<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR .  "framework.php";

$json_rows      = filter_input(INPUT_POST, "rows");
$userid         = filter_input(INPUT_POST, "user");
$rows           = json_decode($json_rows);
$method         = GETPOST("method");
$cash           = round(GETPOST("cash")*100);
$action         = GETPOST("action");
$fk_warehouse   = 0;
$fk_customer    = 0;
$fk_pricelevel  = 0;
$ticket_path    = "";

$query="select * from " . MAIN_DB_PREFIX . "simplepos_settings order by rowid limit 1;";
$result = $db->query($query);
if($result)
{
    $fetch = $db->fetch_object($result);
    $fk_warehouse  = $fetch->fk_warehouse;
    $fk_customer   = $fetch->fk_customer;
    $fk_pricelevel = $fetch->fk_pricelevel; 
    $ticket_path   = $fetch->ticket_path;
}

//SAVE TICKET
if($action=="save")
{
    $columns = [
    "`datem`",
    "`fk_product`",
    "`fk_entrepot`",
    "`value`",
    "`price`",
    "`type_mouvement`",
    "`fk_user_author`",
    "`label`"
    ];
    $ticket_date = date_format(date_create(),"d-m-Y H:i:s");
    $ticket_code = date_format(date_create(),"YmdHis");

    $db->begin(); // START TRANSACTION

    foreach($rows as $product)
    {
        $values = [
            "NOW()",
            $product->rowid,
            $fk_warehouse,
            -$product->qty,
            $product->price,
            0,
            $userid,
            "'($ticket_code) SCONTRINO DEL " . $ticket_date . "'"
        ];

        $insert = "insert into " . MAIN_DB_PREFIX . "stock_mouvement (" . implode(",",$columns) . ") values (" . implode(",",$values) . ");";
        $res_insert = $db->query($insert);
        if($res_insert)
        {
            $update1 = "update " . MAIN_DB_PREFIX . "product set stock = stock - " . $product->qty . " where rowid = " . $product->rowid . ";";
            $res_update1 = $db->query($update1);
            if($res_update1)
            {
                $update2 = "update " . MAIN_DB_PREFIX . "product_stock set reel = reel - " . $product->qty . ", pmp = " . $product->price . " where fk_product = " . $product->rowid . " and fk_entrepot = $fk_warehouse;";
                $res_update2 = $db->query($update2);
                if(!$res_update2)
                {
                    print $update2;
                    print $db->lasterrno() . ": " . $db->lasterror();
                    $db->rollback(); //REVERT TRANSACTION
                    return;
                }
            }   
            else
            {
                print $update1;
                print $db->lasterrno() . ": " . $db->lasterror();
                $db->rollback(); //REVERT TRANSACTION
                return;
            }
        }
        else
        {
            print $insert;
            print $db->lasterrno() . ": " . $db->lasterror();
            $db->rollback(); //REVERT TRANSACTION
            return;
        }
    }
    $db->commit(); // CLOSE TRANSACTION   
}

//PRINT TICKET
include dirname(__FILE__) . DIRECTORY_SEPARATOR . "PhpSerial.php";

$queryCOM = "select * from ". MAIN_DB_PREFIX . "pos LIMIT 1";
$resCOM = $db->query($queryCOM);
if($resCOM)
{
    $rs = $db->fetch_object($resCOM);
    $baud_rate  = $rs->baud_rate;
    $bit_data   = $rs->bit_data;
    $parity     = $rs->parity;
    $bit_stop   = $rs->bit_stop;
    $com_port   = $rs->com_port;
}

// Let's start the class
$serial = new PhpSerial();

// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
//$serial->deviceSet("COM1");
$serial->deviceSet($com_port);

// We can change the baud rate, parity, length, stop bits, flow control
$serial->confBaudRate($baud_rate);
$serial->confParity($parity);
$serial->confCharacterLength($bit_data);
$serial->confStopBits($bit_stop);
$serial->confFlowControl("none");

// Then we need to open it
$serial->deviceOpen();


//GET REP
$queryRep = "select rep from " . MAIN_DB_PREFIX . "pos;";
$resRep = $db->query($queryRep);
if($resRep)
{
    $rep = json_decode(str_replace("@","\"",$db->fetch_object()->rep));
}
else
{
    print "ERRORE: REPARTI NON IMPOSTATI!";
    $serial->deviceClose();
    return;
}

foreach($rows as $product)
{
    $prod_label     = strtoupper(substr($product->label, 0, 20));
    $prod_lordo     = ($product->price * (100+$product->tva_tx))/100;
    $prod_price     = round($prod_lordo * 100);
    $prod_iva       = $product->tva_tx;
    $prod_qty       = $product->qty;
    $prod_rep_idx   = array_search($prod_iva, $rep)+1;
    
//    print "lordo: " . $prod_lordo . "<br>";
//    print "price: " . $prod_price . "<br>";
//    print "iva: "   . $product->tva_tx . "<br>";
//    print "rep: " . $prod_rep_idx . "<br>";
    
    //WRITE TICKET
    $serial->sendMessage("\"" . $prod_label . "\"" . $prod_qty . "*" . $prod_price . "H" . $prod_rep_idx . "R");
}

if($method==1)
{
    $serial->sendMessage($cash . "H" . $method . "T");
}
else if($methid==1 && $cash==0)
{
    $serial->sendMessage($method . "T");
}
else
{
    $serial->sendMessage($method . "T");
}

if($action=="save")
{
    print "<h3>Scontrino salvato in archivio</h3>";
}
else
{
    print "<h3>Operazione eseguita</h3>";
}


// To write into
//$serial->sendMessage("\"Semi di girasole\"2*199H1R");
//$serial->sendMessage("\"Pane integrale 500gr.\"1*100H2R");
//$serial->sendMessage("\"Pane di soia 500gr.\"1*100H2R");
//$serial->sendMessage("\"Wurstel di soia 500gr\"2*299H2R");
//$serial->sendMessage("\"Seitan 500gr.\"1*799H1R");
//$serial->sendMessage("5000H1T");

//$serial->sendMessage("j");
//$serial->sendMessage("\"BIO.VI.TA\"@");
//$serial->sendMessage("\"PUNTI BONUS: 100\"@");
//$serial->sendMessage("\"PUNTI TOTALI: 12780\"@");
//$serial->sendMessage("J");

$serial->deviceClose();
