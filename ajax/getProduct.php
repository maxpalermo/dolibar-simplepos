<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR .  "framework.php";
require dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "ClassProduct.php";

$term=escape(filter_input(INPUT_GET, "term"));
$Product = new ClassProduct($db);
$json_barcode = $Product->getProductByBarCode($term);
if(!empty($json_barcode))
{
    print $json_barcode;
    exit;
}
$json_ref = $Product->getProductByRef($term);
if(!empty($json_ref))
{
    print $json_ref;
    exit;
}

$json_label = $Product->getProductByLabel($term);
if(!empty($json_label))
{
    print $json_label;
    exit;
}

