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
$fk_warehouse   = GETPOST("entrepot");
$fk_customer    = GETPOST("customer");
$fk_pricelevel  = 1;
$ticket_path    = "";

$query="select * from " . MAIN_DB_PREFIX . "simplepos_settings order by rowid limit 1;";
$result = $db->query($query);
if($result)
{
    $fetch = $db->fetch_object($result);
    //$fk_warehouse  = $fetch->fk_warehouse;
    //$fk_customer   = $fetch->fk_customer;
    $fk_pricelevel = $fetch->fk_pricelevel; 
    $ticket_path   = $fetch->ticket_path;
}

//SAVE TICKET
if(!empty($action))
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
include dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "SimpleSerial.php";

// Let's start the class
$serial = new SimpleSerial("/dev/ttyUSB0", 19200, 8, "none", 1);
$serial->initialize();

//GET REP
$queryRep = "select rep from " . MAIN_DB_PREFIX . "pos;";
$resRep = $db->query($queryRep);
if($resRep)
{
    $retObj = $db->fetch_array($resRep);
    $repdb  = $retObj[0];
    $rep    = json_decode(str_replace("@","\"",$repdb));
}
else
{
    print "ERRORE: REPARTI NON IMPOSTATI!";
    $serial->deviceClose();
    return;
}

$i=0;
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
    $message = "\\\"" . $prod_label . "\\\"" . $prod_qty . "*" . $prod_price . "H" . $prod_rep_idx . "R";
    //print "<p>$message</p>";
    $serial->msg($message);
    usleep(50000);
}

if($method==1)
{
    $serial->msg($cash . "H" . $method . "T");
}
else if($method==1 && $cash==0)
{
    $serial->msg($method . "T");
}
else
{
    $serial->msg($method . "T");
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
