<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2015 Massimiliano Palermo      <maxx.palermo@gmail.com>
 * 
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
function getCurrentCustomer(DoliDBMysqli $db)
{
    $_query = "select pos.fk_customer,soc.nom as `name` FROM #__pos pos, #__societe soc WHERE soc.rowid=pos.fk_customer";
    $query = str_replace("#__", MAIN_DB_PREFIX, $_query);
    $res = $db->query($query);
    if($res)
    {
        return $db->fetch_object($res);
    }
    
    $error = new stdClass();
    $error->code = $db->lasterrno();
    $error->message = $db->lasterror();
    $error->query = $db->lastqueryerror();
    return $error;
}

function getCurrentEntrepot(DoliDBMysqli $db)
{
    $_query = "select pos.fk_warehouse,e.label as `name` FROM #__pos pos, #__entrepot e WHERE e.rowid=pos.fk_warehouse";
    $query = str_replace("#__", MAIN_DB_PREFIX, $_query);
    $res = $db->query($query);
    if($res)
    {
        return $db->fetch_object($res);
    }
    
    $error = new stdClass();
    $error->code = $db->lasterrno();
    $error->message = $db->lasterror();
    $error->query= $db->lastqueryerror();
    return $error;
}

/**
 *       \file       htdocs/core/tools.php
 *       \brief      Home page for top menu tools
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$login=true;
	
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "framework.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR .  "lib" . DIRECTORY_SEPARATOR . "simplePOS.lib.php";

$langs->load("companies");
$langs->load("other");

global $db;

$customer = getCurrentCustomer($db);
$entrepot = getCurrentEntrepot($db);

/*
 * View
 */

//$socstatic=new Societe($db);

llxHeader("","SIMPLE POS","");

$text="Gestione semplificata punto vendita";

print_fiche_titre($text);

// Configuration header
$head = prepareHead();
dol_fiche_head(
	$head,
	'pos',
	"Gestione punto vendita POS",
	0,
	"pictovalue@simplePOS"
);

// QUI VA LA PARTE HTML

/*
print "<pre>";
print "CUSTOMER:\n";
print print_r($customer,1);
print "\n";
print "ENTREPOT:\n";
print print_r($entrepot,1);
print "</pre>";
*/
?>

<link rel="stylesheet" type="text/css" href="../js/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="../js/jquery-ui/jquery-ui.theme.css">
<link rel="stylesheet" type="text/css" href="../css/style.css">
<link rel="stylesheet" type="text/css" href="../css/ticket-table.css">

<div>
    <form>
        <fieldset>
            <legend>Impostazioni</legend>
            <div class='rich-button'>
                <label class="prebutton icon-customer blue"></label>
                <input type=text" style="margin-right: 45px;" id="cmbCustomer" rowid="<?php print $customer->fk_customer; ?>" placeholder="digita % per tutti i risultati" value="<?php if(!empty($customer->name)){ print $customer->name;} ?>"/>
                <img class="drop-down" src="../img/img-dropdown.png" onclick="$('#cmbCustomer').autocomplete('search','%')">
            </div>
            <div class='rich-button'>
                <label class="prebutton icon-warehouse blue"></label>
                <input type="text" style="margin-right: 45px;" id="cmbEntrepot" rowid="<?php print $entrepot->fk_warehouse; ?>" placeholder="digita % per tutti i risultati" value="<?php if(!empty($entrepot->name)){ print $entrepot->name;} ?>"/>
                <img class="drop-down" src="../img/img-dropdown.png" onclick="$('#cmbEntrepot').autocomplete('search','%')">
            </div>
            <br>
        </fieldset>
        
        <br/><br style="clear: both;"/>
        
        <fieldset>
            <legend>Iserisci articolo</legend>
            <div class="rich-button">
                <label class="icon-sum yellow"></label>
                <input type="number" id="txtQty" value="1" style="width: 36px; padding-right: 2px; margin-right: 15px;"/>
            </div>
            <!-- Casella di ricerca per i prodotti -->
            <input type="hidden" id="product_rowid" value="0">
            <input type="hidden" id="product_fk_stock" value="0">
            <input type="hidden" id="product_fk_batch" value="0">
            <input type="hidden" id="product_batch" value="0">
            <input type="hidden" id="product_eatby" value="0">
            <div class="rich-button">
                <label class="icon-item green"></label>
                <input id="cmbItem" placeholder="digita % per tutti i risultati"/>
                <img class="drop-down" src="../img/img-dropdown.png" onclick="$('#cmbItem').autocomplete('search','%')">
            </div>
            <!-- Casella del prezzo -->
            <div class="rich-button">
                <label class="icon-product-price blue"></label>
                <input type="number" id="txtProduct_price" style="width: 100px; text-align: right; padding-right: 2px;" class="ui-autocomplete-input"/>
            </div>
             <!-- Casella dell'iva -->
            <div class="rich-button">
                <label class="icon-product-tva blue"></label>
                <input type="number" id="txtProduct_tva" style="width: 48px; text-align: right; padding-right: 2px;" class="ui-autocomplete-input"/>
            </div>
            <!-- PULSANTE AGGIUNGI -->
            <div style="clear: both; margin-top: 15px;">
                <div style="float: right; padding-right: 10px;">
                    <input type="button" class="button blue" value="AGGIUNGI" onclick="addProduct();">
                </div>
            </div>
            
        </fieldset>
        
        <br/> <br/>
        <style>
            .btn-input
            {
                width: auto;
            }
        </style>
        <div>
            <div style="width: auto;">
                <fieldset>
                    <legend>Operazioni</legend>
                    <input type="button" class="button button-left btn-input" value="NUOVO" onclick="newTicket();">
                    <input type="button" class="button button-left btn-input" value="SALVA" id="btnSave" disabled="disabled">
                    <input type="button" class="button button-left btn-input" value="ELIMINA" disabled="disabled">
                    <input type="button" class="button button-left btn-input" value="STAMPA" id="btnPrint">
                    <input type="button" class="button button-left btn-input" value="LETTURA REPARTI" id="btnReadRep">
                    <input type="button" class="button button-left btn-input" value="LETTURA GIORNALIERA" id="btnReadDay">
                    <input type="button" class="button button-left btn-input" value="AZZERA REPARTI" id="btnZeroRep">
                    <input type="button" class="button button-left btn-input" value="CHIUSURA FISCALE" id="btnCloseDay">
                    <input type="button" class="button button-left btn-input" value="LOGOUT">
                </fieldset>
            </div>
            <div style="width: auto;">
                <fieldset>
                    <legend>Scontrino</legend>
                    <div class="ticket-table">
                    <table id="ticket-list">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkall" onclick="checkAll();" style="margin-top:5px;"></th>
                                <th>qta</th>
                                <th>articolo</th>
                                <th>prezzo</th>
                                <th>% iva</th>
                                <th>imponibile</th>
                                <th>iva</th>
                                <th>totale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="0">
                                <td>
                                    <input type="checkbox" name="checkRow" style="margin-top:5px;">
                                    <input type="button" class="td-icon-delete">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">TOTALE</td>
                                <td><img src='../img/icon_euro.png' style='padding-right: 5px; padding-left: 7px;'></td>
                                <td>0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                    </div>
                </fieldset>
            </div>
            <div style="float: left; width: 28%; display: none;">
                <fieldset>
                    <legend>Scontrini giornalieri</legend>
                    <table class="table">
                        <tbody>
                            <!-- filled by Ajax -->
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
        
        <br style="clear:both;" />
            
    </form>
</div>
<div id="result" style="display: none;">
    
</div>
<div id="pay-dialog" style="display: none;">
    <div>
        <h3>METODO DI PAGAMENTO</h3>
        <input type="radio" name="pay-method[]" checked="checked" value="1"><label>CONTANTI</label>
        <br>
        <input type="radio" name="pay-method[]" value="2"><label>CARTE</label>
        <br>
        <input type="radio" name="pay-method[]" value="3"><label>ASSEGNO</label>
        <br>
        <input type="radio" name="pay-method[]" value="4"><label>CREDITO</label>
        <br>
    </div>
    <div id="pay-check">
        <div>
            <label style="min-width: 150px;">IMPORTO</label><br>
            <input type="number" id="pay-total" readonly="readonly">
            <br>
        </div>
        <div>
            <label style="min-width: 150px;">CONTANTI</label><br>
            <input type="number" id="pay-cash" >
            <br>
        </div>
        <div>
            <label style="min-width: 150px;">RESTO</label><br>
            <input type="number" id="pay-change" readonly="readonly">
            <br>
        </div>
        
    </div>
    <div>
        <input type="button" id="btnOkSave" value="OK">
    </div>
</div>
<script type="text/javascript" src="../js/jquery-ui/jquery-ui.min.js" ></script>
<script type="text/javascript">
    var dialog;
    var pay_method=1;
    var action = "save";
    
    $(document).ready(function()
    {
        dialog = $("#pay-dialog").dialog({
            width: "auto",
            height: "auto",
            modal: true
        }).dialog("close");
        
        //$(document).keypress(function(e){
        //    console.log(e.which);
        //});
        
        $("#pay-cash").on("change",function(){
            var total = $("#pay-total").val();
            var cash  = $("#pay-cash").val();
            var change = total-cash;
            $("#pay-change").val(Number(change).toFixed(2));
            $(this).val(Number(this.value).toFixed(2));
        });
        
        $("[name='pay-method[]']").on("click",function(){
            console.log (this.value);
            pay_method = this.value;
            if(this.value==="1")
            {
                $("#pay-check").show();
            }
            else
            {
                $("#pay-check").hide();
                $("#pay-cash").val($("#pay-total").val());
            }
        });
        
        $("#btnReadRep").click(function(){action="readrep";printTicket();});
        $("#btnReadDay").click(function(){action="readday";printTicket();});
        $("#btnZeroRep").click(function(){action="zerorep";printTicket();});
        $("#btnCloseDay").click(function(){action="closeday";printTicket();});
        
        
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
                           $("#cmbCustomer").val(ui.item.label);
                        }
            
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
        .append( "<a>" + item.label + "<br>" + item.desc + "</a>" )
        .appendTo( ul );
        };
        
        $('#cmbEntrepot').autocomplete({
            source      : '../ajax/getEntrepot.php',
            minLength   : 1,
            select      : function(event,ui)
                        {
                           event.preventDefault();
                           console.log("Entrepot id: " + ui.item.id);
                           $("#cmbEntrepot").attr("rowid",ui.item.id);
                           $("#cmbEntrepot").val(ui.item.label);
                        }
            
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
        .append( "<a>" + item.label + "<br>" + item.desc + "</a>" )
        .appendTo( ul );
        };
        
        $('#cmbItem').autocomplete({
            source      : '../ajax/getProduct.php',
            minLength   : 1,
            response    : function(event,ui) 
                        {
                            if (ui.content.length === 1)
                            {
                                ui.item = ui.content[0];
                                $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                                $(this).autocomplete('close');
                            }
                        },
            select      : function(event,ui)
                        {
                           event.preventDefault();
                           $("#cmbItem").val(ui.item.label);
                           $("#product_rowid").val(ui.item.rowid);
                           $("#product_fk_stock").val(ui.item.fk_stock);
                           $("#product_fk_batch").val(ui.item.fk_batch);
                           $("#product_batch").val(ui.item.batch);
                           $("#product_eatby").val(ui.item.eatby);
                           $("#txtProduct_price").val(ui.item.price);
                           $("#txtProduct_tva").val(ui.item.tva_tx);
                           addProduct();
                           clearProduct();
                        }
            
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
                
                console.log(item);
                
                return $( "<li>" )
                    .append( "<a>" + 
                                "<table>\n" +
                                "\t<tbody>\n" +
                                "\t\t<tr>\n" +
                                "\t\t\t<td colspan='4'>" +
                                "<img src='../img/icon_product_item.png'  style='padding-right: 5px; padding-left: 7px;'><strong>" + item.label + "</strong></td>" +
                                "\t\t</tr>\n" +
                                "\t\t<tr>\n" +
                                "\t\t\t<td>" +
                                "<img src='../img/icon_product_batch.png' style='padding-right: 5px; padding-left: 7px;'>" + item.batch + "</td>\n" +
                                "\t\t\t<td>" +
                                "<img src='../img/icon_product_eatby.png' style='padding-right: 5px; padding-left: 7px;'>" + item.eatby + "</td>\n" +
                                "\t\t\t<td>" +
                                "<img src='../img/icon_product_stock.png' style='padding-right: 5px; padding-left: 7px;'>" + item.qty +"</td>\n" +
                                "\t\t\t<td>" +
                                "<img src='../img/icon_euro.png' style='padding-right: 5px; padding-left: 7px;'>" + item.price_ttc +"</td>\n" +
                                "\t\t</tr>\n" +
                                "\t</tbody>\n" +
                                "</table>\n" +
                                 "</a>" )
                    .appendTo( ul );
        };
        
        $("#btnSave").click(function()
        {
            action = "save";
            $("#pay-total").val($("#ticket-list > tfoot > tr > td:last").text());
            $("#pay-cash").val($("pay-total").val());
            dialog.dialog("open");
            return;
        });
        
        $("#btnPrint").click(function(){
            action = "print";
            $("#pay-total").val($("#ticket-list > tfoot > tr > td:last").text());
            $("#pay-cash").val($("pay-total").val());
            dialog.dialog("open");
            return;
        });
        
        $("#btnOkSave").click(function()
        {
            $("#pay-dialog").dialog("close");
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
            
            var rows = new Array();
            $("#ticket-list tr").each(function(){
                var rowid     = $(this).attr("id");
                var qty       = $(this).find("td:nth-child(2)").text();
                var label     = $(this).find("td:nth-child(3)").text();
                var price     = $(this).find("td:nth-child(4)").text();
                var tva_tx    = $(this).find("td:nth-child(5)").text(); 
                var tot_price = $(this).find("td:nth-child(6)").text();
                var tot_tx    = $(this).find("td:nth-child(7)").text();
                var total     = $(this).find("td:nth-child(8)").text();
                
                var row = {
                    rowid     : rowid, 
                    qty       : qty, 
                    label     : label,
                    price     : price,
                    tva_tx    : tva_tx,
                    tot_price : tot_price,
                    tot_tx    : tot_tx,
                    total     : total
                };
                if(rowid){rows.push(row);}
            });
            
            $.ajax(
            {
                method: "POST",
                url: "../ajax/saveTicket.php",
                data:   { 
                            customer: $("#cmbCustomer").attr("rowid"),
                            entrepot: $("#cmbEntrepot").attr("rowid"),
                            rows    : JSON.stringify(rows),
                            method  : pay_method,
                            cash    : $("#pay-cash").val(),
                            action  : "save",
                            user    : "<?php print $user->id; ?>",
                            total   : $("#ticket-list > tfoot > tr > td:last").text()
                        }
                            
            })
                .done(function( msg )
            {
                if(msg==="1"){msg="Scontrino salvato in archivio.";}
                $("#result").dialog({
                      modal: true,
                      width: "auto",
                      height: "auto",
                      buttons: { OK: function(){$(this).dialog("close");}}
                  }).html("<pre>" + msg + "</pre>").dialog("open");
            });
        });
    });
    
    function clearProduct()
    {
        $("#txtQty").val("1");
        $("#cmbItem").val("");
        $("#txtProduct_price").val("0.00");
        $("#txtProduct_tva").val("0.00");
    }
    
    function addProduct()
    {
        var row;
        var rowid=$("#product_rowid").val();
        var fk_stock=$("#product_fk_stock").val();
        var fk_batch=$("#product_fk_batch").val();
        var batch=$("#product_batch").val();
        var eatby=$("#product_eatby").val();
        var qty=$("#txtQty").val();
        var label=$("#cmbItem").val();
        var price=$("#txtProduct_price").val();
        var tva_tx=$("#txtProduct_tva").val();
        var imponibile=Number(Number(qty) * Number(price)).toFixed(2);
        var iva=Number((Number(tva_tx)/100)*imponibile).toFixed(2);
        var totale=Number(Number(iva) + Number(imponibile)).toFixed(2);
        
        row = "<tr id=\"" + rowid + "\" fk_stock=\"" + fk_stock + "\" fk_batch=\"" + fk_batch + "\" batch=\"" + batch + "\" eatby = \"" + eatby + "\">\n";
        row = row + "<td>\n" +
              "<input type=\"checkbox\" name=\"checkRow\" style=\"margin-top:5px;\">\n" +
              "\t<input type=\"button\" onclick=\"delRow(this);\" class=\"td-icon-delete\">\n" +
              "</td>";
        row = row + "<td>" + qty + "</td>\n";
        row = row + "<td>" + label + "</td>\n";
        row = row + "<td>" + price + "</td>\n";
        row = row + "<td>" + tva_tx + "</td>\n";
        row = row + "<td>" + imponibile + "</td>\n";
        row = row + "<td>" + iva + "</td>\n";
        row = row + "<td>" + totale + "</td>\n";
        row = row + "</tr>\n";
        
        $("#ticket-list tbody").append(row);
        if($("#ticket-list > tbody >tr:first").attr("id")==="0")
        {
            $("#ticket-list > tbody >tr:first").remove();
        }
        calculateTotal();
    }
    
    function calculateTotal()
    {
        var cellTotal=0;
        $("#ticket-list > tbody > tr").each(function(){
            var td = $(this).find("td").eq(7).text();
            cellTotal += Number(td);
        });
        $("#ticket-list > tfoot > tr > td:last").text(Number(cellTotal).toFixed(2));
    }
    
    function delRow(button)
    {
        $(button).closest("tr").remove();
        calculateTotal();
    }
    
    function newTicket()
    {
        console.log("reload");
        location.reload();
    }
    
    function printTicket()
    {
        $.ajax({
            method: "POST",
            url: "../ajax/printTicket.php",
            data: {action : action}
          });
    }
</script>

<?php

llxFooter();

$db->close();
