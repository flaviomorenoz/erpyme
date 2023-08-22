<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title.' | '.$Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
    <?= $Settings->rtl ? '<link href="'.$assets.'dist/css/rtl.css" rel="stylesheet" />' : ''; ?>
    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <style type="text/css">
        .iconos{ font-size:20px;}
    </style>
    <link rel="stylesheet" type="text/css" href="<?= site_url('themes/default/views/header.css'); ?>"/>
    <script type="text/javascript">
        function rellenar_valor(){
            var parametros = {
                metodo_pago : document.getElementById('metodo_pagox').value
            }
            $.ajax({
                data: parametros,
                type: "get",
                url: "pos/obtener_valor_metodo_pago",
                success : function(response){
                    document.getElementById("txt_metodo_valor").value = response
    
                    /* Para obtener el texto */
                    var combo = document.getElementById("metodo_pagox");
                    var selected = combo.options[combo.selectedIndex].text;
                    document.getElementById("txt_metodo_pago").value = selected;
                },
                error:function(res){
                    alert("Hay un error")
                }
            })
        }

        var jObe = "";
        
    </script>
</head>
<body class="skin-<?= $Settings->theme_style; ?> sidebar-collapse sidebar-mini pos">

    <div class="wrapper rtl rtl-inv">

        <header class="main-header">
            <a href="<?= site_url(); ?>" class="logo">
            <?php if ($store) { ?>
            <span class="logo-mini">POS</span>
            <span class="logo-lg"><?= $store->name == 'SIPOS' ? 'SI<b>POS</b>' : '<img src="'.base_url('uploads/'.$Settings->logo).'" alt="'.$Settings->name.'" />' ?></span>
            <?php } else { ?>
            <span class="logo-mini">POS</span>
            <span class="logo-lg"><?= $Settings->site_name == 'SIPOS' ? 'SI<b>POS</b>' : '<img src="'.base_url('uploads/'.$Settings->logo).'" alt="'.$Settings->site_name.'" />' ?></span>
            <?php } ?>
            </a>
            <nav class="navbar navbar-static-top" role="navigation">
                <ul class="nav navbar-nav pull-left">
                    <!-- <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?= $assets; ?>images/<?= $Settings->selected_language; ?>.png" alt="<?= $Settings->selected_language; ?>"></a>
                        <ul class="dropdown-menu">
                            <?php $scanned_lang_dir = array_map(function ($path) {
                                return basename($path);
                            }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                            foreach ($scanned_lang_dir as $entry) { ?>
                            <li><a href="<?= site_url('pos/language/' . $entry); ?>"><img
                                src="<?= $assets; ?>images/<?= $entry; ?>.png"
                                class="language-img"> &nbsp;&nbsp;<?= ucwords($entry); ?></a></li>
                                <?php } ?>
                        </ul>
                    </li> -->
                    <li><a href="#" class="clock" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>"></a></li>
                </ul>

                <!-- MENU DETALLE DE CAJA ----->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- <li><a href="#" class="clock"></a></li> -->
                        <!-- <li><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i></a></li> -->
                    <?php if ($Admin) { ?>
                        <!-- <li><a href="<?= site_url('settings'); ?>"><i class="fa fa-cogs"></i></a></li> -->
                    <?php } ?>

                        <li>
                            <div style="margin: 10px; color: white;">
                                <span id="botoncito" data-toggle="modal" data-target="#Modal_agregar_monto_caja" onclick="obtener_monto_caja()">Editar Caja</span>
                            </div>
                        </li>

                        <!--<li><a href="<?= site_url('pos/register_details'); ?>" data-toggle="ajax" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>"><?= lang('register_details'); ?></a></li>-->
                    <?php if ($Admin) { ?>
                        <li><a href="<?= site_url('pos/today_sale'); ?>" data-toggle="ajax" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>"><?= lang('today_sale'); ?></a></li>
                    <?php } ?>
                        <li><a href="<?= site_url('pos/close_register2'); ?>" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>"><?= lang('close_register'); ?></a></li>

                    <?php if ($this->db->dbdriver != 'sqlite3') { ?>
                        <!--<li><a href="<?= site_url('pos/view_bill'); ?>" target="_blank"><i class="fa fa-desktop"></i></a></li>-->
                    <?php } ?>
                        <li class="hidden-xs hidden-sm"><a href="<?= site_url('pos/shortcuts'); ?>" data-toggle="ajax" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>"><i class="fa fa-key"></i></a></li>

                    <?php if ($suspended_sales) { ?>
                        <li class="dropdown notifications-menu" id="suspended_sales">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning"><?=sizeof($suspended_sales);?></span>
                            </a>
                            <ul class="dropdown-menu" style="padding-right:5px;border-style: solid; border-color: gray; border-radius: 9px; border-width: 2px;margin-top:3px;">
                                <li class="header">
                                    <input type="text" autocomplete="off" data-list=".list-suspended-sales" name="filter-suspended-sales" id="filter-suspended-sales" class="form-control input-sm kb-text clearfix" placeholder="<?= lang('filter_by_reference'); ?>">
                                </li>
                                <li>
                                    <ul class="menu">
                                        <li class="list-suspended-sales">
                                            <?php
                                            foreach ($suspended_sales as $ss) {
                                                echo '<a href="'.site_url('pos/?hold='.$ss->id).'" class="load_suspended">'.$this->tec->hrld($ss->date).' ('.$ss->customer_name.')<br><div class="bold">'.$ss->hold_ref.'</div></a>';
                                            }
                                            ?>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="<?= site_url('sales/opened'); ?>"><?= lang('view_all'); ?></a></li>
                            </ul>
                        </li>
                    <?php } ?>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>;border-radius: 7px;">
                                <img src="<?= base_url('uploads/avatars/thumbs/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="user-image" alt="Avatar" />
                                <span><?= $this->session->userdata('first_name').' '.$this->session->userdata('last_name'); ?></span>
                            </a>
                            <ul class="dropdown-menu" style="padding-right:5px;border-style: solid; border-color: gray; border-radius: 9px; border-width: 2px;margin-top:3px;">
                                <li class="user-header" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>">
                                    <img src="<?= base_url('uploads/avatars/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="img-circle" alt="Avatar" />
                                    <p>
                                        <?= $this->session->userdata('email'); ?>
                                        <small><?= lang('member_since').' '.$this->session->userdata('created_on'); ?></small>
                                    </p>
                                </li>
                                <li class="user-footer" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>">
                                    <div class="pull-left">
                                        <a href="<?= site_url('users/profile/'.$this->session->userdata('user_id')); ?>" class="btn btn-default btn-flat"><?= lang('profile'); ?></a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat<?= $this->session->userdata('register_id') ? ' sign_out' : ''; ?>"><?= lang('sign_out'); ?></a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" data-toggle="control-sidebar" class="sidebar-icon" style="background-color: <?= COLOR_FONDO_PRINCIPAL ?>"><i class="fa fa-folder sidebar-icon"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

            <!-- TODO ESTE ASIDE CORRESPONDE AL MENU VERTICAL -->
            <aside class="main-sidebar">
                <section class="sidebar">
                    <ul class="sidebar-menu">

                <?php
                    // ****** LOS ACCESOS A LOS MODULOS *******
                    $usuario = $_SESSION["username"];
                    $cSql = "select a.username, a.group_id, b.modulo, b.permiso 
                        from tec_users a
                        inner join tec_permisos b on a.group_id = b.group_id
                        where a.username = '{$usuario}'";

                    $query  = $this->db->query($cSql); // ,array($usuario, $modulo)
                    $ar_mod = array();
                    $nx     = 0;
                    foreach($query->result() as $r){
                        $ar_mod[$nx]['modulo'] = $r->modulo;
                        $ar_mod[$nx]['permiso'] = $r->permiso;
                        $nx++;
                    }
                    $this->menu_principal->menu_principal2($Admin, $this->session->userdata('store_id'), $Settings->multi_store, $ar_mod); 
                ?>
                    
                    </ul>
                </section>
            </aside>

        <div class="content-wrapper">
            <div class="col-lg-12 alerts">
                <?php if ($error)  { ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-ban"></i> <?= lang('error'); ?></h4>
                        <?= $error; ?>
                    </div>
                    <?php } if ($message) { ?>
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-check"></i> <?= lang('Success'); ?></h4>
                        <?= $message; ?>
                    </div>
                <?php } ?>
            </div>

            <script>
                function gambito(){
                    graba_en_session('delivery','tipo_id');
                    if (document.getElementById('delivery').value == '1'){ // Directo en tienda
                        $("#paid_by").attr("disabled",false)
                        $(".items").show()
                    }else if(document.getElementById('delivery').value == '2'){ // PedidosYa
                        $("#paid_by").val("PedidosYa")
                        $("#paid_by").attr("disabled",true)
                        $("#valor_deliv").attr("readonly",true)
                        $(".items").show()
                    }else if(document.getElementById('delivery').value == '3'){  // Rappi
                        $("#paid_by").val("Rappi")
                        $("#paid_by").attr("disabled",true)
                        $("#valor_deliv").attr("readonly",true)
                        $(".items").show()
                    }else if(document.getElementById('delivery').value == '4'){ // Delivery Propio
                        $("#paid_by").attr("disabled",false)
                        //$("#valor_deliv").removeAttr("readonly")
                        $(".items").show()
                    }else if(document.getElementById('delivery').value == ''){
                        let nada = 0
                    }else{
                       $("#paid_by").attr("disabled",false)
                       $("#paid_by").val("")
                       $(".items").show()
                    }
                    $("#paid_by").change()
                    //document.getElementById('delivery').display = "none"
                    document.getElementById('cual_delivery').value = document.getElementById('delivery').value
                }

                function graba_en_session(valor, var1){
                    $.ajax({
                        data    : {valor:document.getElementById(valor).value, var1:var1},
                        type    : 'get',
                        url     : '<?= base_url('pos/set_session') ?>',
                        success : function(res){
                            console.log("Dato almacenado")
                        }
                    })
                }
            </script>

            <!-- TABLA GENERAL DEL ESCOJO DE PRODUCTOS -->
            <table style="width:100%;" class="layout-table">
                    <tr>
                        <td style="width: 460px;">
                            <div id="pos">
                                <?= form_open('pos', 'id="pos-sale-form"'); ?>
                                <div class="well well-sm" id="leftdiv">
                                    <div id="lefttop">
                                        <table width="100%">
                                            <tr>
                                                <td width="70%">
                                                    <div class="form-group">
                                                        <!--<select id="delivery" name="delivery" class="form-control" onchange="gambito()" style="margin:0px 0px 5px 0px;">
                                                            <option value="0">Escoja Opcion</option>
                                                            <option value="3">Directo Tienda</option>
                                                            <option value="1">Rappi</option>
                                                            <option value="2">PedidosYa</option>
                                                            <option value="4">Delivery Propio</option>
                                                            <option value="5">Delivery Didi</option>
                                                        </select>-->
                                                        <?php
                                                            $ar = array();
                                                            $result = $this->db->query("select a.id, a.descrip, a.activo from tec_tipo_precios a where a.activo='1' order by a.id")->result_array();
                                                            $ar = $this->fm->conver_dropdown($result, "id", "descrip", array(''=>'Seleccione'));
                                                            echo form_dropdown('delivery',$ar,$delivery,'class="form-control tip" id="delivery" required="required" onchange="gambito();this.disabled=true;"');
                                                        ?>
                                                    <div>
                                                </td>
                                                <td width="30%">
                                                    <div class="form-group">
                                                        <input type="text" name="valor_deliv" id="valor_deliv" class="form-control" placeholder="" readonly="readonly">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="form-group" style="margin-bottom:5px;">
                                            <div class="input-group">
                                                <?php foreach($customers as $customer){ 
                                                    $cus[$customer->id] = $customer->name.' - '.$customer->cf1; 
                                                }
                                                ?>
                                                <?= form_dropdown('customer_id', $cus, set_value('customer_id." ".cf1', $Settings->default_customer), 'id="spos_customer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control select2" style="width:100%;position:absolute;"'); ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                                    <a href="#" id="add-customer" class="external" data-toggle="modal" data-target="#myModal">
                                                        <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>

                                        <?php if ($eid && $Admin) { ?>
                                        <div class="form-group" style="margin-bottom:5px;">
                                            <?= form_input('date', set_value('date', $sale->date), 'id="date" required="required" class="form-control"'); ?>
                                        </div>
                                        <?php } ?>

                                        <div class="form-group" style="margin-bottom:5px;">
                                            <input type="text" name="hold_ref" value="<?= $reference_note; ?>" id="hold_ref" class="form-control kb-text" placeholder="<?=lang('reference_note')?>" />
                                        </div>
                                        
                                        <div class="form-group" style="margin-bottom:5px;">
                                            <input type="text" name="code" id="add_item" class="form-control" placeholder="<?=lang('search__scan')?>" />
                                        </div>
                                    </div>
                                    
                                    <div id="printhead" class="print">
                                        <?= $Settings->header; ?>
                                        <p><?= lang('date'); ?>: <?=date($Settings->dateformat)?></p>
                                    </div>
                                    
                                    <!-- PARTE DEL ESCOJO DE PRODUCTOS (INTERMEDIA) -->
                                    <div id="print" class="fixed-table-container">
                                        <div id="list-table-div">
                                            <div class="fixed-table-header">
                                                <table class="table table-striped table-condensed table-hover list-table" style="margin:0;">
                                                    <thead>
                                                        <tr class="success">
                                                            <th><?=lang('product')?></th>
                                                            <th style="width: 15%;text-align:center;"><?=lang('price')?></th>
                                                            <th style="width: 15%;text-align:center;"><?=lang('qty')?></th>
                                                            <th style="width: 20%;text-align:center;"><?=lang('subtotal')?></th>
                                                            <th style="width: 20px;" class="satu"><i class="fa fa-trash-o"></i></th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <table id="posTable" class="table table-striped table-condensed table-hover list-table" style="margin:0px;" data-height="100">
                                                <thead>
                                                    <tr class="success">
                                                        <th><?=lang('product')?></th>
                                                        <th style="width: 15%;text-align:center;"><?=lang('price')?></th>
                                                        <th style="width: 15%;text-align:center;"><?=lang('qty')?></th>
                                                        <th style="width: 20%;text-align:center;"><?=lang('subtotal')?></th>
                                                        <th style="width: 20px;" class="satu"><i class="fa fa-trash-o"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        
                                        <div style="clear:both;"></div>
                                        <div id="totaldiv">
                                            <table id="totaltbl" class="table table-condensed totals" style="margin-bottom:10px;">
                                                <tbody>
                                                    <tr class="info">
                                                        <td width="25%"><?=lang('total_items')?></td>
                                                        <td class="text-right" style="padding-right:10px;"><span id="count">0</span></td>
                                                        <td width="25%"><?=lang('total')?></td>
                                                        <td class="text-right" colspan="2"><span id="total">0</span></td>
                                                    </tr>
                                                    <tr class="info">
                                                        <td width="25%"><a href="#" id="add_discount"><?=lang('discount')?></a></td>
                                                        <td class="text-right" style="padding-right:10px;"><span id="ds_con">0</span></td>
                                                        <td width="25%"><a href="#" id="add_tax"><?=lang('order_tax')?></a> <a href="#" id="text_tax" ></a>    </td>
                                                        <td class="text-right"><span id="ts_con">0</span></td>
                                                    </tr>
                                                    <tr class="success">
                                                        <td colspan="2" style="font-weight:bold;">
                                                            <?=lang('total_payable')?>
                                                            <a role="button" data-toggle="modal" data-target="#noteModal">
                                                                <i class="fa fa-comment"></i>
                                                            </a>
                                                        </td>
                                                        <td class="text-right" colspan="2" style="font-weight:bold;"><span id="total-payable">0</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--<button type="button" onclick="cambiar_precios()">Cambiar precios</button>-->
                                    </div>
                                    
                                    <!-- boton "Pago" : GUARDAR, CANCELAR, IMPRIMIR, -->
                                        <script type="text/javascript">
                                            function imprimir_bill(){
                                                //console.log("alerta total")
                                                document.getElementById('id02').style.display='block'
                                                console.log(spositems)
                                                var filon = ""
                                                var subfilon = ""
                                                
                                                var net_price = ""
                                                var nAcu = 0
                                                $("#order-table2").empty();

                                                var cad = ""

                                                for(fila in spositems){
                                                    cad += "<tr>"

                                                    filon = spositems[fila]
                                                    subfilon = filon["row"]
                                                    
                                                    cad += "<td>" + subfilon["qty"] + "</td>"       

                                                    cad += "<td>" + filon["label"] + "</td>"

                                                    net_price = parseFloat(subfilon["price"]) + (parseFloat(subfilon["tax"])/100) * parseFloat(subfilon["price"])

                                                    cad += "<td>" + formatMoney(net_price) + "</td>"

                                                    cad += "</tr>"

                                                    $("#order-table2").append(cad);

                                                    cad = ""

                                                    nAcu += net_price
                                                }
                                                
                                                //Haciendo el total
                                                cad += "<tr>" + "<td colspan='2' style='text-align:right;padding-top:10px'>Importe a Pagar :&nbsp;&nbsp;&nbsp; </td><td style='text-align:left;font-weight:bold;padding-top:10px'>" + formatMoney(nAcu) + "</td>" + "</tr>"

                                                $("#order-table2").append(cad);
                                            }        
                                        </script>
                                    <div id="botbuttons" class="col-xs-12 text-center">
                                        <div class="row">
                                            <div class="col-xs-4" style="padding: 0;">
                                                <div class="btn-group-vertical btn-block">
                                                    <button type="button" class="btn btn-warning btn-block btn-flat" id="suspend"><?= lang('hold'); ?></button>
                                                    <button type="button" class="btn btn-danger btn-block btn-flat" id="reset"><?= lang('cancel'); ?></button>
                                                </div>
                                            </div>
                                            <div class="col-xs-4" style="padding: 0 5px;">
                                                <div class="btn-group-vertical btn-block">
                                                    <button type="button" class="btn bg-purple btn-block btn-flat" id="print_order"><?= lang('print_order'); ?></button>
                                                    <!--<button type="button" class="btn bg-navy btn-block btn-flat" id="print_bill"><?= lang('print_bill'); ?></button>-->
                                                    <button type="button" class="btn bg-navy btn-block btn-flat" onclick="imprimir_bill()"><?= lang('print_bill'); ?></button>
                                                </div>
                                            </div>
                                            <div class="col-xs-4" style="padding: 0;">
                                                <button type="button" class="btn btn-success btn-block btn-flat" id="<?= $eid ? 'submit-sale' : 'payment'; ?>" style="height:67px;"><?= $eid ? lang('submit') : lang('payment'); ?></button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="clearfix"></div>
                                    <span id="hidesuspend"></span>
                                    <input type="hidden" name="spos_note" value="" id="spos_note">

                                    <div id="payment-con">
                                        <input type="hidden" name="amount" id="amount_val" value="<?= $eid ? $sale->paid : ''; ?>"/>
                                        <input type="hidden" name="balance_amount" id="balance_val" value=""/>
                                        
                                        <input type="hidden" name="paid_by" id="paid_by_val" value="cash"/>
                                        <input type="hidden" name="txt_monto_paid_by" id="txt_monto_paid_by" />
                                        
                                        <input type="hidden" name="cc_no" id="cc_no_val" value=""/>
                                        <input type="hidden" name="paying_gift_card_no" id="paying_gift_card_no_val" value=""/>
                                        <input type="hidden" name="cc_holder" id="cc_holder_val" value=""/>
                                        <input type="hidden" name="cheque_no" id="cheque_no_val" value=""/>
                                        <input type="hidden" name="cc_month" id="cc_month_val" value=""/>
                                        <input type="hidden" name="cc_year" id="cc_year_val" value=""/>
                                        <input type="hidden" name="cc_type" id="cc_type_val" value=""/>
                                        <input type="hidden" name="cc_cvv2" id="cc_cvv2_val" value=""/>
                                        <input type="hidden" name="balance" id="balance_val" value=""/>
                                        <input type="hidden" name="payment_note" id="payment_note_val" value=""/>
                                        <input type="hidden" name="tipoDoc" id="tipoDoc_val" value=""/>

                                        <input type="hidden" name="txt_paid_by2"          id="txt_paid_by2">
                                        <input type="hidden" name="txt_monto_paid_by2"    id="txt_monto_paid_by2">
                                    </div>
                                    <input type="hidden" name="customer" id="customer" value="<?=$Settings->default_customer?>" />
                                    <input type="hidden" name="order_tax" id="tax_val" value="" />
                                    <input type="hidden" name="order_discount" id="discount_val" value="" />
                                    <input type="hidden" name="count" id="total_item" value="" />
                                    <input type="hidden" name="did" id="is_delete" value="<?=$sid;?>" />
                                    <input type="hidden" name="eid" id="is_delete" value="<?=$eid;?>" />
                                    <input type="hidden" name="total_items" id="total_items" value="0" />
                                    <input type="hidden" name="total_quantity" id="total_quantity" value="0" />
                                    <input type="submit" id="submit" value="Submit Sale" style="display: none;" />
                                    <input type="hidden" name="txt_tipoDocAfectado" id="txt_tipoDocAfectado" size="8"/>
                                    <input type="hidden" name="txt_serieDocfectado" id="txt_serieDocfectado" size="8" />
                                    <input type="hidden" name="txt_numDocfectado" id="txt_numDocfectado" size="8" />
                                    <input type="hidden" name="txt_codMotivo" id="txt_codMotivo" size="8" />
                                    <input type="hidden" name="txt_desMotivo" id="txt_desMotivo" size="8" />
                                    <input type="hidden" name="cual_delivery" id="cual_delivery" value="">
                                    <input type="hidden" name="correlativo" id="correlativo">
                                    <input type="hidden" name="txt_persona_delivery" id="txt_persona_delivery">
                                </div>
                                <?=form_close();?>
                                                          
                            </div>

                        </td>
                        <!-- ESTE ES DONDE SE MUESTRA LOS PRODUCTOS CON SUS FOTOS -->
                        <td>
                            <div class="contents" id="right-col">
                                <div id="item-list">
                                    <div class="items">
                                        <?php echo $products; ?>
                                    </div>
                                </div>
                                <div class="product-nav">
                                    <div class="btn-group btn-group-justified">
                                        <div class="btn-group">
                                            <button style="z-index:10002;" class="btn btn-warning pos-tip btn-flat" type="button" id="previous"><i class="fa fa-chevron-left"></i></button>
                                        </div>
                                        <div class="btn-group">
                                            <button style="z-index:10003;" class="btn btn-success pos-tip btn-flat" type="button" id="sellGiftCard"><i class="fa fa-credit-card" id="addIcon"></i> <?= lang('sell_gift_card') ?></button>
                                        </div>
                                        <div class="btn-group">
                                            <button style="z-index:10004;" class="btn btn-warning pos-tip btn-flat" type="button" id="next"><i class="fa fa-chevron-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
            </table>
        </div>
    </div>

    <!-- ES EL MENU VERTICAL DE LA DERECHA (CATEGORIAS) -->

    <aside class="control-sidebar control-sidebar-dark" id="categories-list">
        <div class="tab-content sb">
            <div class="tab-pane active sb" id="control-sidebar-home-tab">
                <div id="filter-categories-con">
                    <input type="text" autocomplete="off" data-list=".control-sidebar-menu" name="filter-categories" id="filter-categories" class="form-control sb col-xs-12 kb-text" placeholder="<?= lang('filter_categories'); ?>" style="margin-bottom: 20px;">
                </div>
                <div class="clearfix sb"></div>
                <div id="category-sidebar-menu">
                    <ul class="control-sidebar-menu">
                        <?php
                        foreach($categories as $category) {
                            if ($category->code != 'CT-9'){
                                echo '<li><a href="#" class="category'.($category->id == $Settings->default_category ? ' active' : '').'" id="'.$category->id.'">';
                                if ($category->image) {
                                    echo '<div class="menu-icon"><img src="'.base_url('uploads/thumbs/'.$category->image).'" alt="" class="img-thumbnail img-responsive"></div>';
                                } else {
                                    echo '<i class="menu-icon fa fa-folder-open bg-red"></i>';
                                }
                                echo '<div class="menu-info"><h4 class="control-sidebar-subheading">'.$category->code.'</h4><p>'.$category->name.'</p></div></a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </aside>

    <div class="control-sidebar-bg sb"></div>
</div>
</div>

<!-- ESTE DIV SE USA PARA MOSTRAR LA ORDEN -->
<link rel="stylesheet" href="<?= $assets ?>dev/css/w3.css">
<div class="w3-container">
  <!--<button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-black">Open Animated Modal</button>-->

  <div id="id01" class="w3-modal">
    <div class="w3-modal-content w3-animate-top w3-card-4" style="max-width: 400px;">
      <header class="w3-container w3-teal"> 
        <span onclick="document.getElementById('id01').style.display='none'" 
        class="w3-button w3-display-topright">&times;</span>
        <table border="0" style="width:100%">
            <td style="width:33%"><h2>Orden</h2></td>
            <td style="width:33%; text-align:right"><span onclick="window.print()" class="glyphicon glyphicon-print" style="margin-top:20px"></span></td>
            <td style="width:33%; padding-top:15px;" id="orden-hora" class="text-center"></td>
        </table>
      </header>
      <div class="w3-container">
        <table id="order-table" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
      </div>
      <footer class="w3-container w3-teal">
        <p></p>
      </footer>
    </div>
  </div>
</div>

<!-- ESTE DIV SE USA PARA MOSTRAR LA CUENTA -->
<link rel="stylesheet" href="<?= $assets ?>dev/css/w3.css">
<div class="w3-container">

  <div id="id02" class="w3-modal">
    <div class="w3-modal-content w3-animate-top w3-card-4" style="max-width: 400px;">
      <header class="w3-container w3-teal"> 
        <span onclick="document.getElementById('id02').style.display='none'" 
        class="w3-button w3-display-topright">&times;</span>
        <table border="0" style="width:100%">
            <tr>
            <td style="width:33%"><h3>Pre-Cuenta</h3></td>
            <td style="width:33%; text-align:right"><span onclick="window.print()" class="glyphicon glyphicon-print" style="margin-top:20px"></span></td>
            <td style="width:33%; padding-top:15px;" id="orden-hora" class="text-center"></td>
            </tr>
        </table>
      </header>
      <div class="w3-container">
        <p style="font-size: 16px;font-weight: bold;margin-top:15px;">Mesa_______ - <?= strtoupper($_SESSION["username"]) ?></p><br>
        <table id="order-table2" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
        <table style="margin-top:15px">
            <tr><td style="padding:10px">Razon Social ________________________________</td></tr>
            <tr><td style="padding:10px">Ruc ________________________________________</td></tr>
            <tr><td style="padding:10px">Direccion ___________________________________</td></tr>
        </table>
        <p style="font-size: 16px;font-weight: bold;margin-top:15px;">"Si requiere Factura o Boleta Eletrónica, llene los datos."</p><br>
      </div>
      <footer class="w3-container w3-teal">
        <p></p>
      </footer>
    </div>
  </div>
</div>

<div id="bill_tbl" style="display:none;"><span id="bill_span"></span>
    <style type="text/css">
        .prT table tfoot tr th {
            background: white !important;
            border-top: none !important;
        }
        .prT.table-condensed>tbody>tr {text-transform: uppercase;}
        .prT.table-condensed>tfoot>tr>td, .prT.table-condensed>tbody>tr>td {padding: 0px}
        .prT.table>tfoot>tr>td, .prT.table>tbody>tr>td {border: none}
        .prT.table>tfoot {border-top: 1px dashed;}
        .prT.table-striped>tbody>tr:nth-of-type(odd){background: white;}
    </style>
    <table id="bill-table" width="100%" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
    <table id="bill-total-table" width="100%" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
</div>

<div style="width:500px;background:#FFF;display:block">
    <div id="order-data" style="display:none;" class="text-center">
        <h1><?= $store->name; ?></h1>
        <h2><?= lang('order'); ?></h2>
        <div id="preo" class="text-left"></div>
    </div>
    <div id="bill-data" style="display:none;" class="text-center">
        <h1><?= $store->name; ?></h1>
        <h2><?= lang('bill'); ?></h2>
        <div id="preb" class="text-left"></div>
    </div>
</div>

<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>

<!-- MODAL AGREGAR MONTO ADICIONAL CAJA -->
<div class="modal fade" id="Modal_agregar_monto_caja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Caja</h4>
      </div>
      <div class="modal-body col-sm-4">
        <div class="form-group">
            Monto Inicial:
            <input type="text" id="monto_caja" name="monto_caja" placeholder="S/" class="form-control" readonly>
        </div>
        <div class="form-group">
            Monto:
            <input type="text" id="monto_caja_add" name="monto_caja_add" class="form-control" placeholder="S/" readonly>
        </div>
        <button onclick="agregar_monto_caja()" class="btn btn-primary">Guardar</button>
      </div>

      <div class="modal-footer">
        <button type="button" id="btn_cerrar" class="btn btn-default" data-dismiss="modal">Close</button>
        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
      </div>
    </div>
  </div>
</div>

<!-- LO LLAMARE MODAL 1 ? -->
<div class="modal" data-easein="flipYIn" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="myModalLabel"><?= lang('sell_gift_card'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger gcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="gcerror"></span>
                </div>
                <div class="form-group">
                    <?= lang("card_no", "gccard_no"); ?> *
                    <div class="input-group">
                        <?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#" id="genNo"><i class="fa fa-cogs"></i></a></div>
                    </div>
                </div>
                <input type="hidden" name="gcname" value="<?= lang('gift_card') ?>" id="gcname"/>
                <div class="form-group">
                    <?= lang("value", "gcvalue"); ?> *
                    <?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
                </div>
                <div class="form-group">
                    <?= lang("price", "gcprice"); ?> *
                    <?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
                </div>
                <div class="form-group">
                    <?= lang("expiry_date", "gcexpiry"); ?>
                    <?php echo form_input('gcexpiry', '', 'class="form-control" id="gcexpiry"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button type="button" id="addGiftCard" class="btn btn-primary"><?= lang('sell_gift_card') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE DESCUENTO GENERAL ? -->
<div class="modal" data-easein="flipYIn" id="dsModal" tabindex="-1" role="dialog" aria-labelledby="dsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="dsModalLabel">Ejemplo: 5%</h4>
            </div>
            <div class="modal-body">
                <input type='text' class='form-control input-sm kb-pad' id='get_ds' onClick='this.select();' value=''>

                <label class="checkbox" for="apply_to_order">
                    <input type="radio" name="apply_to" value="order" id="apply_to_order" checked="checked"/>
                    <?= lang('apply_to_order') ?>
                </label>
                <!--<label class="checkbox" for="apply_to_products">
                    <input type="radio" name="apply_to" value="products" id="apply_to_products"/>
                    <?= lang('apply_to_products') ?>
                </label>-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button type="button" id="updateDiscount" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- LO LLAMARE MODAL 3 ? -->
<div class="modal" data-easein="flipYIn" id="tsModal" tabindex="-1" role="dialog" aria-labelledby="tsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="tsModalLabel"><?= lang('tax_title'); ?></h4>
            </div>
            <div class="modal-body">
                <input type='text' class='form-control input-sm kb-pad' id='get_ts' onClick='this.select();' value=''>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button type="button" id="updateTax" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- LO LLAMARE MODAL 4 ? -->
<div class="modal" data-easein="flipYIn" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="noteModalLabel"><?= lang('note'); ?></h4>
            </div>
            <div class="modal-body">
                <textarea name="snote" id="snote" class="pa form-control kb-text"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button type="button" id="update-note" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- LO LLAMARE MODAL 5 ? -->
<div class="modal" data-easein="flipYIn" id="proModal" tabindex="-1" role="dialog" aria-labelledby="proModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="proModalLabel">
                    <?=lang('payment')?>
                </h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th style="width:25%;"><?= lang('net_price'); ?></th>
                        <th style="width:25%;"><span id="net_price"></span></th>
                        <th style="width:25%;"><?= lang('product_tax'); ?></th>
                        <th style="width:25%;"><span id="pro_tax"></span> <span id="pro_tax_method"></span></th>
                    </tr>
                </table>
                <input type="hidden" id="row_id" />
                <input type="hidden" id="item_id" />
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?=lang('unit_price', 'nPrice')?>
                            <input type="text" class="form-control input-sm kb-pad" id="nPrice" onClick="this.select();" placeholder="<?=lang('new_price')?>">
                        </div>
                        <div class="form-group">
                            <?=lang('discount', 'nDiscount')?>
                            <input type="text" class="form-control input-sm kb-pad" id="nDiscount" onClick="this.select();" placeholder="<?=lang('discount')?>">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?=lang('quantity', 'nQuantity')?>
                            <input type="text" class="form-control input-sm kb-pad" id="nQuantity" onClick="this.select();" placeholder="<?=lang('current_quantity')?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?=lang('comment', 'nComment')?>
                            <textarea class="form-control kb-text" id="nComment"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=lang('close')?></button>
                <button class="btn btn-success" id="editItem"><?=lang('update')?></button>
            </div>
        </div>
    </div>
</div>

<!-- LO LLAMARE MODAL 6 ? -->
<div class="modal" data-easein="flipYIn" id="susModal" tabindex="-1" role="dialog" aria-labelledby="susModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="susModalLabel"><?= lang('suspend_sale'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('type_reference_note'); ?></p>

                <div class="form-group">
                    <?= lang("reference_note", "reference_note"); ?>
                    <?php echo form_input('reference_note', $reference_note, 'class="form-control kb-text" id="reference_note"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
                <button type="button" id="suspend_sale" class="btn btn-primary"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="saleModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="opModal" tabindex="-1" role="dialog" aria-labelledby="opModalLabel" aria-hidden="true"></div>


<!-- FAMOSO MODAL DE PAGO -->
<div class="modal" data-easein="flipYIn" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-success">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="payModalLabel">
                    <?=lang('payment')?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-9">
                        <div class="font16">
                            <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                                <tbody>
                                    <tr>
                                        <td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_items"); ?></td>
                                        <td width="25%" class="text-right"><span id="item_count">0.00</span></td>
                                        <td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_payable"); ?></td>
                                        <td width="25%" class="text-right"><span id="twt">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td style="border-right-color: #FFF !important;"><?= lang("total_paying"); ?></td>
                                        <td class="text-right"><span id="total_paying">0.00</span></td>
                                        <td style="border-right-color: #FFF !important;"><?= lang("balance"); ?></td>
                                        <td class="text-right"><span id="balance">0.00</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?= lang('note', 'note'); ?>
                                    <textarea name="note" id="note" class="pa form-control kb-text"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 col-sm-4">
                                <div class="form-group">
                                    <?= lang("amount", "amount"); ?>
                                    <input name="amount" type="text" id="amount"
                                    class="pa form-control kb-pad amount" readonly />
                                </div>
                            </div>
                            <style type="text/css"> #paid_by option{color: blue;} </style>
                            
                            <div class="col-xs-3 col-sm-4">
                                <div class="form-group">
                                    <?= lang("paying_by", "paid_by"); ?>
                                    
                                    <?php
                                        $cSql = "select forma_pago, descrip from tec_forma_pagos where activo='1'";
                                        $result = $this->db->query($cSql)->result_array();
                                        $ar_p[""] = "--- Seleccione Tipo ---";
                                        foreach($result as $r){
                                            $ar_p[ $r["forma_pago"] ] = $r["descrip"];
                                        }

                                        echo '<div class="form-group">';
                                        echo form_dropdown('paid_by',$ar_p,"",'class="form-control tip" id="paid_by" required="required"');
                                        echo '</div>';
                                    
                                        echo '<div class="form-group">';
                                        echo form_dropdown('paid_by2',$ar_p,"",'class="form-control tip" id="paid_by2" required="required"');
                                        echo '</div>';
                                    ?>

                                </div>
                            </div>

                            <div class="col-xs-3 col-sm-4">
                                <div class="form-group">
                                    <label>Monto</label>
                                    <input type="text" name="monto_paid_by" id="monto_paid_by" class="form-control" onblur="suma_metodo()">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="monto_paid_by2" id="monto_paid_by2" class="form-control" onblur="suma_metodo()">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-xs-4 col-sm-4">
                            </div>
                            <div class="col-xs-3 col-sm-4">
                                Total Método:
                            </div>
                            <div class="col-xs-3 col-sm-4">
                                <span id="suma_metodo"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group gc" style="display: none;">
                                    <?= lang("gift_card_no", "gift_card_no"); ?>
                                    <input type="text" id="gift_card_no"
                                    class="pa form-control kb-pad gift_card_no gift_card_input"/>

                                    <div id="gc_details"></div>
                                </div>
                                <div class="pcc" style="display:none;">
                                    <div class="form-group">
                                        <input type="text" id="swipe" class="form-control swipe swipe_input"
                                        placeholder="<?= lang('focus_swipe_here') ?>"/>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <input type="text" id="pcc_no"
                                                class="form-control kb-pad"
                                                placeholder="<?= lang('cc_no') ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">

                                                <input type="text" id="pcc_holder"
                                                class="form-control kb-text"
                                                placeholder="<?= lang('cc_holder') ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-3">
                                            <div class="form-group">
                                                <select id="pcc_type"
                                                class="form-control pcc_type select2"
                                                placeholder="<?= lang('card_type') ?>">
                                                <option value="Visa"><?= lang("Visa"); ?></option>
                                                <option
                                                value="MasterCard"><?= lang("MasterCard"); ?></option>
                                                <option value="Amex"><?= lang("Amex"); ?></option>
                                                <option
                                                value="Discover"><?= lang("Discover"); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <input type="text" id="pcc_month"
                                            class="form-control kb-pad"
                                            placeholder="<?= lang('month') ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="form-group">

                                            <input type="text" id="pcc_year"
                                            class="form-control kb-pad"
                                            placeholder="<?= lang('year') ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="form-group">

                                            <input type="text" id="pcc_cvv2"
                                            class="form-control kb-pad"
                                            placeholder="<?= lang('cvv2') ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pcheque" style="display:none;">
                                <div class="form-group"><?= lang("cheque_no", "cheque_no"); ?>
                                    <input type="text" id="cheque_no"
                                    class="form-control cheque_no kb-text"/>
                                </div>
                            </div>
                            <div class="pcash">
                                <div class="form-group"><?= lang("payment_note", "payment_note"); ?>
                                    <input type="text" id="payment_note" class="form-control payment_note kb-text"/>
                                </div>
                            </div>
                            
                            <div id="div_ndc" style="display:none">
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for=""><?= lang('type_of_document_affected') ?></label>
                                        <div>
                                            <select name="tipoDocAfectado" id="tipoDocAfectado" name="tipoDocAfectado" class="form-control">
                                                <option value="1">Factura</option>
                                                <option value="2">Boleta</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-3">
                                        <label for="">Serie</label>
                                        <div>
                                          <input type="text" class="form-control" id="serieDocfectado" name="serieDocfectado" value="" placeholder="">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-3">
                                        <label for="">Comprobante</label>
                                        <div>
                                          <input type="text" class="form-control" id="numDocfectado" name="numDocfectado" value="" placeholder="000001">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row" id="div_nota_de_credito" style="display:none">
                                    <label for="" class="col-sm-3 col-form-label"><?= lang('Reason') ?></label>
                                    <div class="col-sm-9">
                                    <?php
                                        $result = $this->db->select("codigo, descrip")->get("tipo_ndc")->result_array();
                                        $ar = array();
                                        $ar[""] = "--Elija un Motivo--";
                                        foreach($result as $r){
                                            $ar[$r["codigo"]] = $r["descrip"];
                                        }
                                        echo form_dropdown('codMotivo',$ar,'','class="form-control" id="codMotivo" required="required" onchange="marcar_motivo_notas()"');
                                    ?>
                                    </div>
                                </div>

                                <div class="form-group row" id="div_nota_de_debito" style="display:none">
                                    <label for="" class="col-sm-3 col-form-label"><?= lang('Reason') ?></label>
                                    <div class="col-sm-9">
                                    <?php
                                        $result = $this->db->select("codigo, descrip")->get("tipo_ndd")->result_array();
                                        $ar = array();
                                        foreach($result as $r){
                                            $ar[$r["codigo"]] = $r["descrip"];
                                        }
                                        echo form_dropdown('codMotivo2',$ar,'','class="form-control" id="codMotivo2" required="required"');
                                    ?>
                                    </div>
                                </div>
                            </div>
                            <script>
                                function marcar_motivo_notas(){
                                    $("#txt_codMotivo").val($("#codMotivo").val())
                                    let combo = document.getElementById("codMotivo");
                                    let selected = combo.options[combo.selectedIndex].text;
                                    $("#txt_desMotivo").val(selected)
                                }
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-xs-3 text-center">
                    <div class="btn-group btn-group-vertical" style="width:100%;border-style: solid">
                        <button type="button" class="btn btn-info btn-block quick-cash" id="quick-payable">0.00
                        </button>
                        <?php
                        foreach (lang('quick_cash_notes') as $cash_note_amount) {
                            echo '<button type="button" class="btn btn-block btn-warning quick-cash">' . $cash_note_amount . '</button>';
                        }
                        ?>
                        <button type="button" class="btn btn-block btn-danger" id="clear-cash-notes"><?= lang('clear'); ?>
                        </button>
                    </div>
                    <select name="tipoDoc" id="tipoDoc" class="form-control" onchange="cambio_tipo_doc(this.value)" style="margin-top:15px">
                        <option value="">--Elija Tipo--</option>
                        <option value="Boleta">Boleta</option>
                        <option value="Factura">Factura</option>
                        <!--<option value="Nota_de_credito">Nota de Credito</option>
                        <option value="Nota_de_debito">Nota de debito</option>-->
                        <option value="Ticket">Ticket</option>
                    </select>
                    <input type="text" name="persona_delivery" id="persona_delivery" style="margin-top:15px" class="form-control" placeholder="personal delivery">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
            
            <!-- Boton "Enviar" en modal de Pago -->
            <button type="button" class="btn btn-primary" id="<?= $eid ? '' : 'submit-sale'; ?>" disabled><?=lang('submit')?></button>
        </div>
    </div>
</div>

</div>

<!-- MODAL AÑADIR CLIENTE -->
<div class="modal" data-easein="flipYIn" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="cModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="cerrar_mijo"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="cModalLabel">
                    <?=lang('add_customer')?>
                </h4>
            </div>
            <?= form_open('customers/add', 'id="customer-form"'); ?>
            <div class="modal-body">
                <div id="c-alert" class="alert alert-danger" style="display:none;"></div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="code">
                                <?= lang("name"); ?>
                            </label>
                            <?= form_input('name', '', 'class="form-control input-sm kb-text" id="cname"'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cemail">
                                <?= lang("email_address"); ?>
                            </label>
                            <?= form_input('email', '', 'class="form-control input-sm kb-text" id="cemail"'); ?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="phone">
                                <?= lang("phone"); ?>
                            </label>
                            <?= form_input('phone', '', 'class="form-control input-sm kb-pad" id="cphone"');?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cf1">
                                <?= lang("cf1"); ?>
                            </label>
                            <?= form_input('cf1', '', 'class="form-control input-sm kb-text" id="cf1" maxlength="8" minlength="8" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cf2">
                                <?= lang("cf2"); ?>
                            </label>
                            <?= form_input('cf2', '', 'class="form-control input-sm kb-text" id="cf2"');?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="margin-top:0;">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
                <!--<button type="submit" class="btn btn-primary" id="add_customer"> <?=lang('add_customer')?> </button>-->
                <button type="button" onclick="validar_customers();" class="btn btn-danger">Aceptar</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- LO LLAMARE MODAL IMPRIMIR TICKET PARA ORDEN (COCINA) -->
<style type="text/css">
    .print-body{
        font-size: 18px;
        font-weight: bold;
    }            
</style>

<div class="modal" data-easein="flipYIn" id="printModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:700px;">
    <div class="modal-content">
        <div class="modal-header np">
            <button type="button" class="close" id="print-modal-close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <button type="button" class="close mr10" onclick="window.print();"><i class="fa fa-print"></i></button>
            <h4 class="modal-title" id="print-title"></h4>
        </div>
        <div class="modal-body">
            <div id="print-body" class="print-body"></div>
        </div>
    </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="posModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="posModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>

<!-- MODAL DEL FAMOSO ESCOJO DELIVERY -->
<span id="btn_modal_metodos" class="" data-toggle="modal" data-target="#myModal"></span>

<input type="hidden" name="txt_metodo_pago" id="txt_metodo_pago">
<input type="hidden" name="txt_metodo_valor" id="txt_metodo_valor">

<script type="text/javascript">
    var base_url = '<?=base_url();?>', assets = '<?= $assets ?>';
    var dateformat = '<?=$Settings->dateformat;?>', timeformat = '<?= $Settings->timeformat ?>';
    <?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
    var Settings = <?= json_encode($Settings); ?>;
    var Store = <?= json_encode($store); ?>;

    var sid = false, username = '<?=$this->session->userdata('username');?>', spositems = {};
    $(window).load(function () {
        $('#mm_<?=$m?>').addClass('active');
        $('#<?=$m?>_<?=$v?>').addClass('active');
    });
    var pro_limit = <?=$Settings->pro_limit?>, java_applet = 0, count = 1, total = 0, an = 1, p_page = 0, page = 0, cat_id = <?=$Settings->default_category?>, tcp = <?=$tcp?>;
    var gtotal = 0, order_discount = 0, order_tax = 0, protect_delete = <?= ($Admin) ? 0 : ($Settings->pin_code ? 1 : 0); ?>;
    var order_data = {}, bill_data = {};
    var csrf_hash = '<?= $this->security->get_csrf_hash(); ?>';
    <?php
    if ($Settings->remote_printing == 2) {

        ?>
        var ob_store_name = "<?= printText($store->name, (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
        order_data.store_name = ob_store_name;
        bill_data.store_name = ob_store_name;

        ob_header = "";
        ob_header += "<?= printText($store->name.' ('.$store->code.')', (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n";
        <?php
        if ($store->address1) { ?>
            ob_header += "<?= printText($store->address1, (!empty($printer) ? $printer->char_per_line : ''));?>\r\n";
            <?php
        }
        if ($store->address2) { ?>
            ob_header += "<?= printText($store->address2, (!empty($printer) ? $printer->char_per_line : ''));?>\r\n";
            <?php
        }
        if ($store->city) { ?>
            ob_header += "<?= printText($store->city, (!empty($printer) ? $printer->char_per_line : ''));?>\r\n";
            <?php
        } ?>
        ob_header += "<?= printText(lang('tel').': '.$store->phone, (!empty($printer) ? $printer->char_per_line : ''));?>\r\n\r\n";
        ob_header += "<?= printText(str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), $store->receipt_header), (!empty($printer) ? $printer->char_per_line : ''));?>\r\n\r\n";

        order_data.header = ob_header + "<?= printText(lang('order'), (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n\r\n";
        bill_data.header = ob_header + "<?= printText(lang('bill'), (!empty($printer) ? $printer->char_per_line : '')); ?>\r\n\r\n";
        order_data.totals = '';
        order_data.payments = '';
        bill_data.payments = '';
        order_data.footer = '';
        bill_data.footer = "<?= lang('merchant_copy'); ?> \n";
        <?php
    }
    ?>
    var lang = new Array();
    lang['code_error'] = '<?= lang('code_error'); ?>';
    lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
    lang['please_add_product'] = '<?= lang('please_add_product'); ?>';
    lang['paid_less_than_amount'] = '<?= lang('paid_less_than_amount'); ?>';
    lang['x_suspend'] = '<?= lang('x_suspend'); ?>';
    lang['discount_title'] = '<?= lang('discount_title'); ?>';
    lang['update'] = '<?= lang('update'); ?>';
    lang['tax_title'] = '<?= lang('tax_title'); ?>';
    lang['leave_alert'] = '<?= lang('leave_alert'); ?>';
    lang['close'] = '<?= lang('close'); ?>';
    lang['delete'] = '<?= lang('delete'); ?>';
    lang['no_match_found'] = '<?= lang('no_match_found'); ?>';
    lang['wrong_pin'] = '<?= lang('wrong_pin'); ?>';
    lang['file_required_fields'] = '<?= lang('file_required_fields'); ?>';
    lang['enter_pin_code'] = '<?= lang('enter_pin_code'); ?>';
    lang['incorrect_gift_card'] = '<?= lang('incorrect_gift_card'); ?>';
    lang['card_no'] = '<?= lang('card_no'); ?>';
    lang['value'] = '<?= lang('value'); ?>';
    lang['balance'] = '<?= lang('balance'); ?>';
    lang['unexpected_value'] = '<?= lang('unexpected_value'); ?>';
    lang['inclusive'] = '<?= lang('inclusive'); ?>';
    lang['exclusive'] = '<?= lang('exclusive'); ?>';
    lang['total'] = '<?= lang('total'); ?>';
    lang['total_items'] = '<?= lang('total_items'); ?>';
    lang['order_tax'] = '<?= lang('order_tax'); ?>';
    lang['order_discount'] = '<?= lang('order_discount'); ?>';
    lang['total_payable'] = '<?= lang('total_payable'); ?>';
    lang['rounding'] = '<?= lang('rounding'); ?>';
    lang['grand_total'] = '<?= lang('grand_total'); ?>';
    lang['register_open_alert'] = '<?= lang('register_open_alert'); ?>';
    lang['discount'] = '<?= lang('discount'); ?>';
    lang['order'] = '<?= lang('order'); ?>';
    lang['bill'] = '<?= lang('bill'); ?>';
    lang['print'] = '<?= lang('print'); ?>';
    lang['merchant_copy'] = '<?= lang('merchant_copy'); ?>';
    lang['blv'] = '<?= lang('blv'); ?>';
    lang['sale_no_ref'] = '<?= lang('sale_no_ref'); ?>';
    lang['ccf1'] = '<?= lang('ccf1'); ?>';

    $(document).ready(function() {
        <?php if ($this->session->userdata('rmspos')) { ?>
            if (get('spositems')) { remove('spositems'); }
            if (get('spos_discount')) { remove('spos_discount'); }
            if (get('spos_tax')) { remove('spos_tax'); }
            if (get('spos_note')) { remove('spos_note'); }
            if (get('spos_customer')) { remove('spos_customer'); }
            if (get('amount')) { remove('amount'); }
            <?php $this->tec->unset_data('rmspos'); } ?>

            if (get('rmspos')) {
                if (get('spositems')) { remove('spositems'); }
                if (get('spos_discount')) { remove('spos_discount'); }
                if (get('spos_tax')) { remove('spos_tax'); }
                if (get('spos_note')) { remove('spos_note'); }
                if (get('spos_customer')) { remove('spos_customer'); }
                if (get('amount')) { remove('amount'); }
                remove('rmspos');
            }
            <?php if ($sid) { ?>
                
                store('spositems', JSON.stringify(<?=$items;?>));
                store('spos_discount', '<?=$suspend_sale->order_discount_id;?>');
                store('spos_tax', '<?=$suspend_sale->order_tax_id;?>');
                store('spos_customer', '<?=$suspend_sale->customer_id;?>');
                $('#spos_customer').select2().select2('val', '<?=$suspend_sale->customer_id;?>');
                store('rmspos', '1');
                $('#tax_val').val('<?=$suspend_sale->order_tax_id;?>');
                $('#discount_val').val('<?=$suspend_sale->order_discount_id;?>');
            <?php } elseif ($eid) { ?>
                $('#date').inputmask("y-m-d h:s:s", { "placeholder": "YYYY/MM/DD HH:mm:ss" });
                store('spositems', JSON.stringify(<?=$items;?>));
                store('spos_discount', '<?=$sale->order_discount_id;?>');
                store('spos_tax', '<?=$sale->order_tax_id;?>');
                store('spos_customer', '<?=$sale->customer_id;?>');
                store('sale_date', '<?=$sale->date;?>');
                $('#spos_customer').select2().select2('val', '<?=$sale->customer_id;?>');
                $('#date').val('<?=$sale->date;?>');
                store('rmspos', '1');
                $('#tax_val').val('<?=$sale->order_tax_id;?>');
                $('#discount_val').val('<?=$sale->order_discount_id;?>');
            <?php } else { ?>
                if (! get('spos_discount')) {
                    store('spos_discount', '<?=$Settings->default_discount;?>');
                    $('#discount_val').val('<?=$Settings->default_discount;?>');
                }
                if (! get('spos_tax')) {
                    store('spos_tax', '<?=$Settings->default_tax_rate;?>');
                    $('#tax_val').val('<?=$Settings->default_tax_rate;?>');
                }
            <?php } ?>

            if (ots = get('spos_tax')) {
                $('#tax_val').val(ots);
            }
            if (ods = get('spos_discount')) {
                $('#discount_val').val(ods);
            }
            bootbox.addLocale('bl',{OK:'<?= lang('ok'); ?>',CANCEL:'<?= lang('no'); ?>',CONFIRM:'<?= lang('yes'); ?>'});
            bootbox.setDefaults({closeButton:false,locale:"bl"});
            <?php if ($eid) { ?>
                $('#suspend').attr('disabled', true);
                $('#print_order').attr('disabled', true);
                $('#print_bill').attr('disabled', true);
            <?php } ?>
    });
</script>

<script type="text/javascript">
    var socket = null;
    <?php
    if ($Settings->remote_printing == 2) {
        ?>
        try {
            socket = new WebSocket('ws://127.0.0.1:6441');
            socket.onopen = function () {
                console.log('Connected');
                return;
            };
            socket.onclose = function () {
                console.log('Connection closed');
                return;
            };
        } catch (e) {
            console.log(e);
        }
        <?php
    }
    ?>
    function printBill(bill) {
        if (Settings.remote_printing == 1) {
            Popup($('#bill_tbl').html(), 'bill');
        } else if (Settings.remote_printing == 2) {
            if (socket.readyState == 1) {
                var socket_data = {'printer': <?= $Settings->local_printers ? "''" : json_encode($printer); ?>, 'logo': '<?= !empty($store->logo) ? base_url('uploads/'.$store->logo) : ''; ?>', 'text': bill};
                socket.send(JSON.stringify({
                    type: 'print-receipt',
                    data: socket_data
                }));
                return false;
            } else {
                bootbox.alert('<?= lang('pos_print_error'); ?>');
                return false;
            }
        }
    }
    var order_printers = <?= $Settings->local_printers ? "''" : json_encode($order_printers); ?>;
    function printOrder(order) {
        console.log("Estoy en printOrder...")
        if (Settings.remote_printing == 1) {
            console.log("A")
            Popup($('#order_tbl').html(), 'order');
        } else if (Settings.remote_printing == 2) {
            console.log("B")
            if (socket.readyState == 1) {
                if (order_printers == '') {

                    var socket_data = { 'printer': false, 'order': true,
                    'logo': '<?= !empty($store->logo) ? base_url('uploads/'.$store->logo) : ''; ?>',
                    'text': order };
                    socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));

                } else {

                    $.each(order_printers, function() {
                        var socket_data = {'printer': this, 'logo': '<?= !empty($store->logo) ? base_url('uploads/'.$store->logo) : ''; ?>', 'text': order};
                        socket.send(JSON.stringify({type: 'print-receipt', data: socket_data}));
                    });

                }
                return false;
            } else {
                bootbox.alert('<?= lang('pos_print_error'); ?>');
                return false;
            }
        }
    }
</script>

<?php
if (isset($print) && !empty($print)) {
    /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */
    include 'remote_printing.php';
}
?>

<script src="<?= $assets ?>dist/js/libraries.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/scripts.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dev/js/pos3.js" type="text/javascript"></script>
<?php if($Settings->remote_printing != 1 && $Settings->print_img) { ?>
<script src="<?= $assets ?>dist/js/htmlimg.js"></script>
<?php } ?>

<script type="text/javascript">

    $('#div_ndc').hide()

    function apunto(){
        var combo = document.getElementById("codMotivo");
        var selected = combo.options[combo.selectedIndex].text;
        alert(selected);
    }

    function verificar_datos_cliente(){
        
        let parametros = {
            tipoDoc     : document.getElementById("tipoDoc").value,
            idCustomer  : document.getElementById('spos_customer').value
        }
        $.ajax({
            data: parametros,
            url : "<?= base_url() ?>pos/verifica_datos_cliente",
            type: "get",
            
            success: function(response){
                if (response=='1'){
                    $('#total_items').val(an - 1);
                    $('#total_quantity').val(count - 1);
                    document.getElementById("submit").click()
                    $('#get_ds').val = 0
                }else{
                    console.log("tipoDoc:"+document.getElementById("tipoDoc").value)
                    console.log("spos_customer:"+document.getElementById('spos_customer').value)
                    alert("El cliente debe tener dni y/o Ruc")
                }
            },
            error : function(xhr, status) {
                alert('Disculpe, existió un problema');
            }
        })
    }

    function pedir_token(){
        let parametros = {
        }
        $.ajax({
            data: parametros,
            url : "<?= base_url() ?>pos/pedir_token",
            type: "get",
            success: function(response){
                if(response){
                    alert("Se actualizó correctamente el Token")
                }else{
                    alert("Inconvenientes con Actualizar el Token")
                }
            }
        })
    }

    function enviar_nota_de_credito(){
        let parametros = {
            $tipo_documento : "Nota de credito",
            $sale_id        : document.getElementById('numDocAfectado').value, // en verdad es serie + correlativo
            $data           : 0,
            $items          : 0
        }
        $.ajax({
            data: parametros,
            url : "<?= base_url() ?>pos/enviar_doc_sunat",
            type: "get",
            success: function(response){
                if (response=='1'){
                    alert("Se logró enviar la nota de Crédito")
                }else{
                    alert("No se pudo enviar la N.C")
                }
            }
        })
    }

    function cambio_tipo_doc(valorcito){
        $('#tipoDoc_val').val(valorcito)
        if (valorcito == "Nota_de_credito" || valorcito == "Nota_de_debito"){
            $('#div_ndc').show()
            console.log(valorcito)
            if(valorcito == "Nota_de_credito"){
                $('#div_nota_de_credito').show()
                $('#div_nota_de_debito').hide()
            }
            if(valorcito == "Nota_de_debito"){
                $('#div_nota_de_debito').show()
                $('#div_nota_de_credito').hide()
            }

        }else{
            $('#div_ndc').hide()
        }
    }

    function agregar_monto_caja(){
        var parametros = {
            tienda: <?= $this->session->userdata('store_id') ?>,
            monto : document.getElementById("monto_caja_add").value
        }
        $.ajax({
            data    : parametros,
            url     : "pos/agregar_monto_adicional",
            type    : "get",
            success : function(response){
                //console.log(response)
                alert(response)

                /*toastr.options = {
                  "debug": false,
                  "positionClass": "toast-bottom-right",
                  "onclick": null,
                  "fadeIn": 300,
                  "fadeOut": 100,
                  "timeOut": 3000,
                  "extendedTimeOut": 1000
                }*/
                //console.log("D");
                //toastr.info("Se actualizaron los datos.","Aviso")
                document.getElementById("btn_cerrar").click()
            }
        })
    }

    function obtener_monto_caja(){
        var parametros = {
            tienda: <?= $this->session->userdata('store_id') ?>,
        }
        $.ajax({
            data    : parametros,
            url     : "pos/obtener_monto_caja",
            type    : "get",
            success : function(response){
                let obj3 = JSON.parse(response)

                let nA = obj3[0].cash_in_hand * 1
                let nB = obj3[0].cash_in_hand_adicional * 1

                document.getElementById('monto_caja').value = nA.toFixed(2)
                document.getElementById('monto_caja_add').value = nB.toFixed(2)
            }
        })

    }

    $(document).ready( function(){
        // Se inactivan los items visuales
        $(".items").hide()

        // Se inactiva la busqueda de productos
        //$("#add_item").attr("disabled",true)

        borrar_datos_productos()
    })

    function borrar_datos_productos(){
        const arx = Object.entries(spositems)
        arx.forEach(([key,value])=>{
            delete spositems[key]
        })
        localStorage.setItem('spositems', JSON.stringify(spositems));
        loadItems();
    }

    function vaceando_vars(store_id=0){
        if (typeof(Storage) !== "undefined"){
            localStorage.setItem("compras_filtro_desde", "")
            localStorage.setItem("compras_filtro_hasta", "")
            if(store_id > 0){
                localStorage.setItem("compras_filtro_tienda",store_id)
            }
            localStorage.setItem("compras_filtro_proveedor", "")
            localStorage.setItem("compras_filtro_fec_emi", "")
            localStorage.setItem("compras_filtro_estado", "")
            //alert("En vaceando vars:" + localStorage.getItem("compras_filtro_tienda"))
        }
    }

    function vaceando_vars_gastus(store_id=0){
        if (typeof(Storage) !== "undefined"){
            localStorage.setItem("gastos_filtro_desde", "")
            localStorage.setItem("gastos_filtro_hasta", "")
            if(store_id > 0){
                localStorage.setItem("gastos_filtro_tienda",store_id)
            }
            localStorage.setItem("gastos_filtro_clasifica1", "")
            localStorage.setItem("gastos_filtro_clasifica2", "")
            localStorage.setItem("gastos_filtro_fec_emi", "")
            localStorage.setItem("gastos_filtro_estado", "")
            //alert("En vaceando vars:" + localStorage.getItem("gastos_filtro_tienda"))
        }
    }

    function correlativo(){
        $.ajax({
            data    : {store_id: <?= $this->session->userdata('store_id') ?>, tipoDocAfectado: 0},
            url     : '<?= base_url("pos/obtener_correlativo") ?>',
            type    : 'get',
            success : function(response){
                jObe = JSON.parse(response)
                console.log(jObe)
                document.getElementById("submit-sale").disabled = false
            }
        })
    }

    function validar_customers(){
        var cf1 = document.getElementById("cf1").value
        var cf2 = document.getElementById("cf2").value
        var len_cf1 = cf1.length
        var len_cf2 = cf2.length
        var name = document.getElementById("cname").value

        if(name.length == 0){
            alert("El nombre no puede ser vacio")
            return false
        }

        if (len_cf1 > 0){
            if(len_cf1 != 8){
                alert("Dni debe tener 8 digitos")
                return false
            }
        }
        if (len_cf2 > 0){
            if(len_cf2 != 11){
                alert("Ruc debe tener 11 digitos")
                return false
            }
        }
        //document.getElementById("customer-form").submit()

        $.ajax({
            data    : {
                modo    : 'ajax',
                name    : document.getElementById('cname').value,
                email   : document.getElementById('cemail').value,
                phone   : document.getElementById('cphone').value,
                cf1     : document.getElementById('cf1').value,
                cf2     : document.getElementById('cf2').value
            },
            url     : 'customers/add',
            type    : 'get',
            success : function(res){
                if(res*1 >= 0){
                    alert("Se guarda correcto el cliente.")
                }else{
                    alert("No se pudo grabar.");
                }
                
                const $select = $("#spos_customer");
                var valor = document.getElementById("cname").value
                $select.append($("<option>", {
                    value: res,
                    text: valor
                  }));
                
                $('#spos_customer').val(res).change()
                
                document.getElementById("cerrar_mijo").click()
            },
            error: function(xhr){
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            }
        })
    }

</script>

</body>
</html>