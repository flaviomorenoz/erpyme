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

            </nav>
        </header>

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

        </div>
    </div>
    <div class="control-sidebar-bg sb"></div>
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