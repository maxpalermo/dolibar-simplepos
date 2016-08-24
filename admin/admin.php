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

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Dolibarr environment
$res = @include ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "main.inc.php"; // From htdocs directory
if (! $res) {
	$res = @include ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "main.inc.php"; // From "custom" directory
}

global $langs, $user, $db;

// Libraries
require_once DOL_DOCUMENT_ROOT . DIRECTORY_SEPARATOR . "core" . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "admin.lib.php";
require_once ".." . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "simplePOS.lib.php";

// Translations
$langs->load("simplePOS@simplePOS");

// Access control
if (! $user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

//Open text SQL file
$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR .  "create.sql";
$sqlfile = fopen($path, "r") or die("Impossibile aprire il file SQL!");
$sql = fread($sqlfile,  filesize($path));
$sql = str_replace("#__", MAIN_DB_PREFIX, $sql);
fclose($sqlfile);

$result = $db->query($sql);
if(!$result)
{
    print $sql;
    print "<br>";
    print $db->lasterrno() . ": " . $db->lasterror();
    print "<br>";
    die("Impossibile creare la tabella simplepos_settings");
}

/*
 * Actions
 */


/*
 * View
 */
$page_name = "Setup SimplePOS";
$js     = array("simplePOS/js/jquery-ui/jquery-ui.min.js");
$css    = array("simplePOS/js/jquery-ui/jquery-ui.min.css","simplePOS/js/jquery-ui/jquery-ui.theme.css","simplePOS/css/style.css");
llxHeader('', $langs->trans($page_name),'','',0,0,$js,$css);

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
	. $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = simplePOSAdminPrepareHead();
dol_fiche_head(
	$head,
	'settings',
	$langs->trans("simplePOS"),
	0,
	"settings@simplePOS"
);

$query = "select * from ".MAIN_DB_PREFIX."simplepos_settings";
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

if($fk_customer)
{
    $query = "select nom from ".MAIN_DB_PREFIX."societe where rowid=$fk_customer";
    $ret = $db->query($query);
    $customer = $db->fetch_object($ret)->nom;
}

if($fk_warehouse)
{
    $query = "select label from ".MAIN_DB_PREFIX."entrepot where rowid=$fk_warehouse";
    $ret = $db->query($query);
    $warehouse = $db->fetch_object($ret)->label;
}


?>

<form>
    <fieldset>
        <legend><?php print $langs->trans("POS_TITLE_ADMIN_PAGE");?></legend>
        <table class="table">
            <tbody>
                <tr>
                    <td colspan="2"><h2>Parametri POS</h2></td>
                </tr>
                <tr valign="middle">
                    <td><label>Cliente</label></td>
                    <td>    
                        <div class='ui-widget'>
                            <label class="prebutton icon-customer yellow"></label>
                            <input id="cmbCustomer" rowid="<?php print $fk_customer; ?>" placeholder="digita % per tutti i risultati" value="<?php if($fk_customer) print $customer; ?>"/>
                        </div>
                    </td>
                </tr>
                <tr valign="middle">
                    <td><label>Magazzino</label></td>
                    <td>
                        <div class='ui-widget'>
                            <label class="prebutton icon-warehouse blue"></label>
                            <input id="cmbWarehouse" rowid="<?php print $fk_warehouse; ?>" placeholder="digita % per tutti i risultati" value="<?php if($fk_warehouse) print $warehouse; ?>"/>
                        </div>
                    </td>
                </tr>
                <tr valign="middle">
                    <td><label>Listino prezzi</label></td>
                    <td>
                        <div class='ui-widget'>
                            <label class="prebutton icon-pricelevel green"></label>
                            <input id="cmbPriceLevel" rowid="<?php print $fk_pricelevel; ?>" placeholder="digita % per tutti i risultati" value="<?php if($fk_pricelevel) print "Listino $fk_pricelevel"; ?>"/>
                        </div>
                    </td>
                </tr>
                <tr valign="middle">
                    <td><label>Percorso scontrino</label></td>
                    <td>
                        <div class='ui-widget'>
                            <label class="prebutton icon-pricelevel green"></label>
                            <input id="txtTicketPath" value="<?php if($ticket_path) print "$ticket_path"; ?>"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><br/><hr/><br/></td>
                </tr>
                <tr>
                    <td colspan="2"><h2>Parametri Comunicazione Seriale</h2></td>
                </tr>
                <tr valign="middle">
                    <td><label>Misuratore fiscale</label></td>
                    <td><input type="text" class="input-autocomplete" id="txtCashRegister" value="<?php print $cash_register; ?>"/></td>
                </tr>
                <tr valign="middle">
                    <td><label>Porta seriale</label></td>
                    <td><input type="text" class="input-autocomplete" id="txtSerialPort" value="<?php print $serial_port; ?>"/></td>
                </tr>
                <tr valign="middle">
                    <td><label>BaudRate</label></td>
                    <td><input type="text" class="input-autocomplete" id="txtBaudRate" value="<?php print $baudrate; ?>"/></td>
                </tr>
                <tr valign="middle">
                    <td><label>Parity</label></td>
                    <td><input type="text" class="input-autocomplete" id="txtParity" value="<?php print $parity; ?>"/></td>
                </tr>
                <tr valign="middle">
                    <td><label>Char Length</label></td>
                    <td><input type="text" class="input-autocomplete" id="txtCharLength" value="<?php print $charlength; ?>"/></td>
                </tr>
                <tr valign="middle">
                    <td><label>Stop Bits</label></td>
                    <td><input type="text" class="input-autocomplete" id="txtStopBits" value="<?php print $stopbits; ?>"/></td>
                </tr>
                <tr valign="middle">
                    <td><label>Flow control</label></td>
                    <td><input type="text" class="input-autocomplete" id="txtFlowControl" value="<?php print $flowcontrol; ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right;">
                        <br/>
                        <input type="button" class="button" value="SALVA" id="btnSave" >
                        <input type="button" class="button" value="AZZERA" >
                        <br/>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</form>
<select id="listbox" style="display: none;"></select>
<script type="text/javascript">
    $(document).ready(function(){
        $("#cmbCustomer").focus(function(){$(this).removeClass("border-error");});
        $("#cmbWarehouse").focus(function(){$(this).removeClass("border-error");});
        $("#cmbPriceLevel").focus(function(){$(this).removeClass("border-error");});
        
        $('#cmbCustomer').autocomplete({
            source      : '../ajax/getCustomers.php',
            minLength   : 1,
            select      : function(event,ui)
                        {
                           event.preventDefault();
                           console.log("Customer id: " + ui.item.id);
                           $("#cmbCustomer").attr("rowid",ui.item.id);
                        }
            
        });
        
        $('#cmbWarehouse').autocomplete({
            source      : '../ajax/getWarehouse.php',
            minLength   : 1,
            select      : function(event,ui)
                        {
                           event.preventDefault();
                           $("#cmbWarehouse").attr("rowid",ui.item.id);
                        }
            
        });
        
        $('#cmbPriceLevel').autocomplete({
            source      : '../ajax/getPriceLevel.php',
            minLength   : 1,
            select      : function(event,ui)
                        {
                           event.preventDefault();
                           $("#cmbPriceLevel").attr("rowid",ui.item.id);
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