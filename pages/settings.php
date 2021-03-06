<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <year>  <name of author>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/admin.php
 * 	\ingroup	simplePOS
 * 	\brief		Setting page of simplePOS module
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "framework.php";

global $langs, $user, $db;

// Libraries
require_once $baseroot . "core" . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "admin.lib.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .  ".." . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "simplePOS.lib.php";

// Translations
$langs->load("simplePOS@simplePOS");

// Access control
if (! $user->admin) {
	accessforbidden();
}

// Parameters
$action         = GETPOST("action");
$com_port       = GETPOST("com_port");
$baud_rate      = GETPOST("baud_rate");
$bit_data       = GETPOST("bit_data");
$parity         = GETPOST("parity");
$bit_stop       = GETPOST("bit_stop");
$id_customer    = GETPOST("id_customer");  
$id_warehouse   = GETPOST("id_warehouse"); 
$id_pricelist   = GETPOST("id_pricelist"); 
$rep            = GETPOST("rep","array");
$rep_json       = json_encode($rep);

$errors;

if(1==2){$db = new DoliDBMysqli();}
    
$createTable = "CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX .  "pos` ( "
        . "`rowid` INT NOT NULL AUTO_INCREMENT , "
        . "`baud_rate` INT NOT NULL , "
        . "`bit_data` INT NOT NULL , "
        . "`parity` VARCHAR(20) NOT NULL , "
        . "`bit_stop` INT NOT NULL , "
        . "`com_port` VARCHAR(100) NOT NULL , "
        . "`fk_customer` INT NOT NULL , "
        . "`fk_warehouse` INT NOT NULL , "
        . "`fk_pricelist` INT NOT NULL , "
        . "`rep` VARCHAR(255) NOT NULL , "
        . "`fk_user_do` INT NOT NULL , "
        . "`tms` TIMESTAMP NOT NULL , "
        . "PRIMARY KEY (`rowid`)"
        . ") ENGINE = InnoDB;";
$res1 = $db->query($createTable);
if(!$res1)
{
    
    addLogError($db);
}


if($action=="SAVE")
{   
    $res2 = $db->query("truncate table " . MAIN_DB_PREFIX . "pos;");
    if(!$res2)
    {
        addLogError($db);
    }
    
    $columns = [
        "rowid",
        "baud_rate",
        "bit_data",
        "parity",
        "bit_stop",
        "com_port",
        "fk_customer",
        "fk_warehouse",
        "fk_pricelist",
        "rep",
        "fk_user_do"
    ];
    $values = [
        apc("1"),
        apc($baud_rate),
        apc($bit_data),
        apc($parity),
        apc($bit_stop),
        apc($com_port),
        apc($id_customer),
        apc($id_warehouse),
        apc($id_pricelist),
        apc(str_replace("\"","@",$rep_json)),
        $user->id
    ];
    $query_add = "INSERT INTO " . MAIN_DB_PREFIX . "pos (" . implode(",",$columns) . ") VALUES (" . implode(",",$values) . ");";
    
    print "<p>$query_add</p>";
    
    $res3 = $db->query($query_add);
    if(!$res3)
    {
        addLogError($db);
    }
    $save = TRUE;
}
else
{
    $queryGet = "select * from " . MAIN_DB_PREFIX . "pos";
    $result = $db->query($queryGet);
    if($result)
    {
        $record = $db->fetch_object($resultset);
        $com_port       = $record->com_port;
        $baud_rate      = $record->baud_rate;
        $bit_data       = $record->bit_data;
        $parity         = $record->parity;
        $bit_stop       = $record->bit_stop;
        $id_customer    = $record->fk_customer;
        $id_warehouse   = $record->fk_warehouse;
        $id_pricelist   = $record->fk_pricelist;
        $json_rep       = $record->rep;
        $rep            = json_decode(str_replace("@","\"",$json_rep));
    }
    else
    {
        addLogError($db);
    }
}
/*
 * Actions
 */


/*
 * View
 */
$page_name = "Setup SimplePOS";
$js     = array("simplePOS/js/jquery-ui/jquery-ui.min.js");
$css    = array(
                    "simplePOS/js/jquery-ui/jquery-ui.min.css",
                    "simplePOS/js/jquery-ui/jquery-ui.theme.css",
                    "simplePOS/css/style.css"
                );
llxHeader('', $langs->trans($page_name),'','',0,0,$js,$css);

$text="Gestione semplificata punto vendita";
print_fiche_titre($langs->trans($text));

// Configuration header
$head = prepareHead();
dol_fiche_head(
	$head,
	'settings',
	$langs->trans("Gestione punto vendita POS"),
	0,
	"pictovalue@simplePOS"
);

$query = "select * from ".MAIN_DB_PREFIX."pos";
$ret=$db->query($query);
if($ret)
{
    $record=$db->fetch_object($ret);
    $fk_customer    = $record->fk_customer;
    $customer       = "";
    $fk_warehouse   = $record->fk_warehouse;
    $warehouse      = "";
    $fk_pricelevel  = $record->fk_pricelevel;
    $ticket_path    = $record->ticket_path;
    $cash_register  = $record->cash_register;
    $serial_port    = $record->serial_port;
    $baudrate       = $record->baudrate;
    $parity         = $record->parity;
    $charlength     = $record->charlength;
    $stopbits       = $record->stopbits;
    $flowcontrol    = $record->flowcontrol;
}
else
{
    $fk_customer    = "";
    $customer       = "";
    $fk_warehouse   = "";
    $warehouse      = "";
    $fk_pricelevel  = "";
    $ticket_path    = "";
    $cash_register  = "";
    $serial_port    = "";
    $baudrate       = "";
    $parity         = "";
    $charlength     = "";
    $stopbits       = "";
    $flowcontrol    = "";
}

if($id_customer)
{
    $query = "select nom from ".MAIN_DB_PREFIX."societe where rowid=$fk_customer";
    $ret = $db->query($query);
    $customer = $db->fetch_object($ret)->nom;
}

if($id_warehouse)
{
    $query = "select label from ".MAIN_DB_PREFIX."entrepot where rowid=$fk_warehouse";
    $ret = $db->query($query);
    $warehouse = $db->fetch_object($ret)->label;
}

if($id_pricelist)
{
    $pricelist = "Listino $id_pricelist";
}

?>
<style>
    #POS
    {
        display: block;
        padding: 10px;
        width:auto;
        border: 1px solid #999999;
        border-radius: 10px;
        background-color: #DDDFDD;
        box-shadow: 4px 4px 4px #ddddaa;
        margin-bottom: 20px;
    }
    
    #POS legend
    {
        display: inline-block;
        width: auto;
        font-size: 1.5em;
        font-stretch: expanded;
        font-weight: bold;
        color: #222222;
    }
    
    #POS h3
    {
        display: block;
        width: auto;
        font-size: 2em;
        font-stretch: expanded;
        font-weight: bold;
        color: #222222;
        margin-bottom: 10px;
    }
    
    #POS > fieldset > div > label
    {
        display: inline-block;
        min-width: 150px;
        font-size: 1.2em;
        font-stretch: condensed;
        font-weight: lighter;
        color: #222222;
        text-shadow: 1px 1px 1px #ddddaa;
    }
    
    #POS input[type=text]
    {
        display: inline-block;
        margin-left: 10px;
        width: 150px;
        font-size: 1.2em;
        font-stretch: condensed;
        font-weight: lighter;
        color: #222222;
        background-color: #ffffff;
        text-shadow: 1px 1px 1px #ddddaa;
        border-radius: 5px;
        border: 1px solid #999999;
    }
    
    #POS .ui-widget
    {
        display: inline-block;
        margin-left: 10px;
    }
</style>

 <div style="width:auto; border: 4px solid #995555; background-color: #DD9999; box-shadow: 4px 4px 4px #999999; <?php if(count($errors)==0){print "display: none;";} ?>">
    <div style="text-align: left; font-size: 1.5em; color: white; text-shadow: 1px 1px 1px #995555;">
        <?php
        
        foreach($errors as $error)
        {
            print "<h4>ERRORE:</h4>";
            print "<p>NUM:" .  $error["code"] . "</p>";
            print "<p>MESSAGGIO:" .  $error["message"] . "</p>";
            print "<p>QUERY:" .  $error["query"] . "</p>";
            print "<hr>";
        }
        
        ?>
    </div>
</div>

<form id="POS" method="POST">
    <fieldset>
        
        <?php 
            if($save)
            {
                ?>
                
        <div style="width:auto; border: 4px solid #559955; background-color: #99dd99; box-shadow: 4px 4px 4px #999999; ">
            <h3 style="text-align: center; font-size: 1.5em; color: white; text-shadow: 1px 1px 1px #559955;">
                IMPOSTAZIONI SALVATE IN ARCHIVIO.
            </h3>
        </div>
        
                <?php
            }
        
        ?>
        
        <legend><?php print $langs->trans("POS_TITLE_ADMIN_PAGE");?></legend>
        <div style="width: 60%; float: left;">
            <h3><?php print $langs->trans("POS_PARAMETERS_SERIAL");?></h3>
            
            <label><?php print $langs->trans("POS_COM_PORT");?></label>
            <input type="text" name="com_port" value="<?php print $com_port; ?>">
            <br>
            <label><?php print $langs->trans("POS_BAUD_RATE");?></label>
            <input type="text" name="baud_rate" value="<?php print $baud_rate; ?>">
            <br>
            <label><?php print $langs->trans("POS_BIT_DATA");?></label>
            <input type="text" name="bit_data" value="<?php print $bit_data; ?>">
            <br>
            <label><?php print $langs->trans("POS_PARITY");?></label>
            <input type="text" name="parity" value="<?php print $parity; ?>">
            <br>
            <label><?php print $langs->trans("POS_BIT_STOP");?></label>
            <input type="text" name="bit_stop" value="<?php print $bit_stop; ?>">
            <br>
            <hr>
            <h3><?php print $langs->trans("POS_PARAMETERS_GENERAL");?></h3>
            <input type="hidden" name="action" id="action" value="">
            <label><?php print $langs->trans("POS_CUSTOMER");?></label>
            <div class='ui-widget'>
                <label class="prebutton icon-customer yellow" onmouseover="this.style.cursor='pointer'" onclick="$('#cmbCustomer').autocomplete('search','%');"></label>
                <input type="hidden" name="id_customer" value="<?php print $fk_customer; ?>">
                <input id="cmbCustomer" rowid="<?php print $fk_customer; ?>" placeholder="digita % per tutti i risultati" value="<?php if($fk_customer) print $customer; ?>"/>
            </div>
            <br>
            <label><?php print $langs->trans("POS_WAREHOUSE");?></label>
            <div class='ui-widget'>
                <label class="prebutton icon-warehouse blue" onmouseover="this.style.cursor='pointer'" onclick="$('#cmbWarehouse').autocomplete('search','%');"></label>
                <input type="hidden" name="id_warehouse" value="<?php print $fk_warehouse; ?>">
                <input id="cmbWarehouse" rowid="<?php print $fk_warehouse; ?>" placeholder="digita % per tutti i risultati" value="<?php if($fk_warehouse) print $warehouse; ?>"/>
            </div>
            <br>
            <label><?php print $langs->trans("POS_PRICELIST");?></label>
            <div class='ui-widget'>
                <label class="prebutton icon-pricelevel green" onmouseover="this.style.cursor='pointer'" onclick="$('#cmbPriceLevel').autocomplete('search','%');"></label>
                <input type="hidden" name="id_pricelist" value="<?php print $fk_pricelevel; ?>">
                <input id="cmbPriceLevel" rowid="<?php print $id_pricelist; ?>" placeholder="digita % per tutti i risultati" value="<?php if($id_pricelist) print "$pricelist"; ?>"/>
            </div>
            <br>
            <hr>
            <div>
            <input type="submit" value="SALVA" style='float: right;margin-right: 10px;' id="submit-btn">
            <br>
        </div>
        </div>
        
        <div style="width: 30%; float: right;">
            <h3><?php print $langs->trans("POS_PARAMETERS_REP");?></h3>
            <?php
                for($i=1;$i<17;$i++)
                {
                    ?>
                        <label><?php print $langs->trans("REP_$i");?></label>
                        <input type="text" name="<?php print "rep[]"; ?>"  value="<?php print $rep[$i-1]; ?>">
                        <br>
                    <?php
                }
            ?>
        </div>
        
        <br style="clear: both;">
        
    </fieldset>
</form>
<select id="listbox" style="display: none;"></select>
<script type="text/javascript">
    $(document).ready(function(){
        var ok = false;
        
        $("#cmbCustomer").focus(function(){$(this).removeClass("border-error");});
        $("#cmbWarehouse").focus(function(){$(this).removeClass("border-error");});
        $("#cmbPriceLevel").focus(function(){$(this).removeClass("border-error");});
        
        $('#cmbCustomer').autocomplete({
            source      : '../ajax/getCustomers.php',
            select      : function(event,ui)
                        {
                           event.preventDefault();
                           $("[name='id_customer']").val(ui.item.id);
                           $("#cmbCustomer").val(ui.item.label);
                        }
            
        });
        
        $('#cmbWarehouse').autocomplete({
            source      : '../ajax/getWarehouse.php',
            select      : function(event,ui)
                        {
                           event.preventDefault();
                           $("[name='id_warehouse']").val(ui.item.id);
                           $("#cmbWarehouse").val(ui.item.label);
                        }
            
        });
        
        $('#cmbPriceLevel').autocomplete({
            source      : '../ajax/getPriceLevel.php',
            select      : function(event,ui)
                        {
                           event.preventDefault();
                           $("[name='id_pricelist']").val(ui.item.id);
                           $("#cmbPriceLevel").val(ui.item.label);
                        }
            
        });
        
        $("#btnSave").click(function()
        {
            var error=false;
            
            if($("#cmbCustomer").attr("rowid")==="0")
            {
                $("#cmbCustomer").addClass("border-error");
                error=true;
            }
            
            if($("#cmbWarehouse").attr("rowid")==="0")
            {
                $("#cmbWarehouse").addClass("border-error");
                error=true;
            }
            
            if($("#cmbPriceLevel").attr("rowid")==="0")
            {
                $("#cmbPriceLevel").addClass("border-error");
                error=true;
            }
            
            if(error) return;
            
            $.ajax(
            {
                method: "POST",
                url: "../ajax/saveSettings.php",
                data:   { 
                            fk_customer:    $("#cmbCustomer").attr("rowid"),
                            fk_warehouse:   $("#cmbWarehouse").attr("rowid"),
                            fk_pricelevel:  $("#cmbPriceLevel").attr("rowid"),
                            ticket_path  :  $("#txtTicketPath").val(),
                            cash_register:  $("#txtCashRegister").val(),
                            serial_port:    $("#txtSerialPort").val(),
                            baudrate:       $("#txtBaudRate").val(),
                            parity:         $("#txtParity").val(),
                            charlength:     $("#txtCharLength").val(),
                            stopbits:       $("#txtStopBits").val(),
                            flowcontrol:    $("#txtFlowControl").val()
                        }
                            
            })
                .done(function( msg )
            {
                  alert( "Data Saved: " + msg );
            });
        });
        
        $("#POS").submit(function(e){
            if(ok) {return;}
            console.log("SUBMIT");
            e.preventDefault();
            $("#action").val("SAVE"); 
            ok=true;
            $(this).submit();
        });
    });
</script>

<?php
// Page end
dol_fiche_end();
llxFooter();


/*
 * Fatal error: Call to a member function getPhotoUrl() on null in /home/massimiliano/www/dolibarr/dolibarr-3.8.2/htdocs/main.inc.php on line 1447
Call Stack
#	Time	Memory      Function                Location
1	0.0016	251272      {main}( )               .../admin.php:0
2	0.0660	1178760     accessforbidden( )      .../admin.php:47
3	0.0699	1228624     llxHeader( )            .../security.lib.php:541
4	0.0702	1232416     top_menu( )             .../main.inc.php:918
 */


function apc($value)
{
    return "\"" . $value . "\"";
}

function addLogError($db)
{
    global $errors;
    $errors[]["code"] = $db->lasterrno();
    $errors[]["message"] = $db->lasterror();
    $errors[]["query"] = $db->lastquery();
}