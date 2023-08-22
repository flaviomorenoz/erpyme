<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
    $tipogasto = 'gastos';
?>
<?php

if(isset($purchases_id)){  // ES MODO EDICION
    $result = $this->db->select("purchases.nroDoc, purchases.cargo_servicio, purchases.tipoDoc, purchases.fec_emi_doc, purchases.fec_venc_doc,
        purchases.date, purchases.costo_tienda, purchases.costo_banco, purchases.supplier_id, purchases.texto_supplier")
        ->from("purchases")
        ->where("purchases.id",$purchases_id)
        ->get()->result_array();

    $query = $this->db->query("select a.id, a.purchase_id, a.product_id, a.cost, a.quantity, a.cost, a.subtotal, c.name, c.unidad 
        from tec_purchase_items a
        left join tec_products c on a.product_id = c.id
        where a.purchase_id = $purchases_id");


    foreach($result as $r){
        $nroDoc         = $r["nroDoc"];
        $cargo_servicio = $r["cargo_servicio"];
        $tipoDoc        = $r["tipoDoc"];
        $fec_emi_doc    = $r["fec_emi_doc"];
        $fec_venc_doc   = $r["fec_venc_doc"];
        $dates          = $r["date"];
        $costo_tienda   = $r["costo_tienda"];
        $costo_banco    = $r["costo_banco"];
        $supplier_id    = $r["supplier_id"];
        $texto_supplier = $r["texto_supplier"];
    }
}else{

    if(strlen($tipoDoc)>0){  // MODO INVALIDO
        // nada
    }else{  // MODO NUEVO
        $nroDoc         = "";
        $cargo_servicio = 0;
        $dates           = date("Y-m-d H:i:s");
        $tipoDoc        = "";
        $fec_emi_doc    = "";
        $fec_venc_doc   = "";
        $date           = "";
        $costo_tienda   = "";
        $costo_banco    = "";
        $supplier_id    = "";

        $i_ultimo = 0; // contador de items  //
        $texto_supplier = "";
    }
}

?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
    
                <div class="box-body">
                    <div class="col-lg-12">
                        <?php echo form_open_multipart("gastos/add", 'class="validation" id="form_compra"'); ?>
                        <input type="hidden" name="edicion_purchase_id" id="edicion_purchase_id" value="<?= (isset($purchases_id) ? $purchases_id : "") ?>">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('date', 'date'); ?>
                                    <?php
                                        //die($dates);
                                            $ar = array(
                                            "name"  =>"date",
                                            "id"    =>"date",
                                            "type"  =>"date",
                                            "value" => substr($dates,0,10),
                                            "class" =>"form-control tip"
                                        );
                                        echo form_input($ar);
                                    ?>
                                </div>
                            </div>
                            
                            <!--<div class="col-md-1">
                                <div class="form-group">
                                    <?= lang('Hours','Hours'); ?>
                                    <?php
                                        //die($dates);
                                            $ar = array(
                                            "name"  =>"date2",
                                            "id"    =>"date2",
                                            "type"  =>"text",
                                            "value" =>"",
                                            "class" =>"form-control tip"
                                        );
                                        echo form_input($ar);
                                    ?>
                                </div>
                            </div>-->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipodoc">Tipo Doc.</label>
                                    <?php 
                                       //echo $this->purchases_model->combo_TipoDoc($tipo_doc); 
                                       $ar = array('F'=>'Factura','B'=>'Boleta','G'=>'Guia Interna');
                                       echo form_dropdown('tipoDoc', $ar, $tipoDoc, 'class="form-control tip" id="tipoDoc" required="required"');
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tipodoc"><?= lang('Nro Doc'); ?></label>
                                    <?= form_input('nroDoc', $nroDoc, 'class="form-control tip" id="nroDoc" required="required"'); ?>
                                </div>
                            </div>

                            <?php if($tipogasto == "caja"){ ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('supplier', 'supplier'); ?>
                                        <?php
                                        $sp[''] = lang("select")." ".lang("supplier");
                                        foreach($suppliers as $supplier) {
                                            $sp[$supplier->id] = $supplier->name;
                                        }
                                        ?>
                                        <?= form_dropdown('supplier', $sp, $supplier_id, 'class="form-control select2 tip" id="supplier"  required="required" style="width:100%;"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="" style="color:white">&nbps;&nbps;&nbps;&nbps;&nbps;</label>
                                        <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#Modal_suppliers">
                                            <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
                                        </button>
                                    </div>    
                                </div>
                            <?php }else{ ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="texto_supplier">Proveedor</label>
                                        <?= form_input('texto_supplier', $texto_supplier, 'class="form-control tip" id="texto_supplier" required="required"') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?= lang('Fecha Emision'); ?></label>
                                    <?php 
                                        $ar = array(
                                            "name"  =>"fec_emi_doc",
                                            "id"    =>"fec_emi_doc",
                                            "type"  =>"date",
                                            "value" =>$fec_emi_doc,
                                            "class" =>"form-control tip"
                                        );
                                        echo form_input($ar); 
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?= lang('Fecha Vencim'); ?></label>
                                    <?php 
                                        $ar = array(
                                            "name"  =>"fec_venc_doc",
                                            "id"    =>"fec_venc_doc",
                                            "type"  =>"date",
                                            "value" =>$fec_venc_doc,
                                            "class" =>"form-control tip"
                                        );
                                        echo form_input($ar); 
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><?= lang('Cargo por Servicio'); ?></label>
                                    <?php 
                                        $ar = array(
                                            "name"  =>"cargo_servicio",
                                            "id"    =>"cargo_servicio",
                                            "type"  =>"text",
                                            "value" =>$cargo_servicio,
                                            "class" =>"form-control tip",
                                            "placeholder" => "S/ 0.00"
                                        );
                                        echo form_input($ar);
                                    ?>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <!--<input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">-->
                                    <label for="product_id">Producto</label>
                                    <?php 
                                       
                                       $cSql = "select id, code, name, price, unidad from tec_products where category_id = 7 order by name";
                                       $result = $this->db->query($cSql)->result_array();
                                       $ar_p[""] = "--- Seleccione Producto ---";
                                       foreach($result as $r){
                                            $ar_p[ $r["id"] ] = $r["name"] . " (" . $r["unidad"] . ")";
                                       }

                                       //$ar = array('F'=>'Factura','B'=>'Boleta','G'=>'Guia Interna');
                                       echo form_dropdown('product_id',$ar_p,'','class="form-control tip" id="product_id" required="required"');
                                    ?>

                                </div>
                            </div>

                            <div class="col-sm-4 col-md-2">
                                <div class="form-group">
                                    <label><?= lang('quantity'); ?></label>
                                    <?php 
                                        $ar = array(
                                            "name"  =>"quantity",
                                            "id"    =>"quantity",
                                            "type"  =>"text",
                                            "value" =>$quantity,
                                            "class" =>"form-control tip"
                                        );
                                        echo form_input($ar);
                                    ?>
                                </div>
                            </div>

                            <div class="col-sm-4 col-md-2">
                                <div class="form-group">
                                    <label><?= lang('unit_Price'); ?></label>
                                    <?php 
                                        $ar = array(
                                            "name"  =>"cost",
                                            "id"    =>"cost",
                                            "type"  =>"text",
                                            "value" =>$cost,
                                            "class" =>"form-control tip",
                                            "placeholder" => "S/ 0.00"
                                        );
                                        echo form_input($ar);
                                    ?>
                                </div>
                            </div>

                            <div class="col-sm-4 col-md-2">
                                <br>
                                <button class="btn btn-primary" onclick="Agregar()">Agregar</button>
                            </div>

                        </div>

                        <!-- ESTE ES EL DIV ALTERNATIVO DE LOS PRODUCTOS --->
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-8" id="taxi">
                            </div>
                            <div class="col-sm-2 col-md-4">
                            </div>
                        </div>

                        
                        
                        <div class="row">
                        <?php if($tipogasto == "gastos"){ echo "<!--"; } ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label id="label_caja_tda"><?= lang('Monto pagado desde caja Tda'); ?></label>
                                    <?php 
                                        $ar = array(
                                            "name"  =>"costo_tienda",
                                            "id"    =>"costo_tienda",
                                            "type"  =>"text",
                                            "value" =>$costo_tienda,
                                            "class" =>"form-control tip"
                                        );
                                        echo form_input($ar);
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label id="label_caja_bco"><?= lang('Monto pagado desde Cta. Banco'); ?></label>
                                    <?php 
                                        $ar = array(
                                            "name"  =>"costo_banco",
                                            "id"    =>"costo_banco",
                                            "type"  =>"text",
                                            "value" =>$costo_banco,
                                            "class" =>"form-control tip"
                                        );
                                        echo form_input($ar);
                                    ?>
                                </div>
                            </div>
                        <?php if($tipogasto == "gastos"){ echo "-->"; } ?>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= lang('received', 'received'); ?>
                                    <?php $sts = array(1 => lang('received'), 0 => lang('not_received_yet')); ?>
                                    <?= form_dropdown('received', $sts, set_value('received'), 'class="form-control select2 tip" id="received"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <?= lang('attachment', 'attachment'); ?>
                            <input type="file" name="userfile" class="form-control tip" id="attachment">
                        </div>
                        
                        <div class="form-group">
                            <?= lang("note", 'note'); ?>
                            <?= form_textarea('note', set_value('note'), 'class="form-control redactor" id="note"'); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group">
                                    <button type="button" onclick="guardar_compra()" class="btn btn-primary">Guardar Compra</button>
                                    <button type="button" id="reset" class="btn btn-danger"><?= lang('reset'); ?></button>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <?= form_submit('add_purchase', '.', 'class="" id="add_purchase"'); ?>
                                <!--<button onclick="donald()">Donald</button>-->
                            </div>
                            
                        </div>
                          
                        <input type="hidden" name="txt_gSubtotal" id="txt_gSubtotal">
                        <input type="hidden" name="txt_gIgv" id="txt_gIgv">
                        <input type="hidden" name="txt_gTotal" id="txt_gTotal">
                        modo_edicion:<input type="text" name="modo_edicion" id="modo_edicion" value="1">
                        tipogasto:<input type="text" name="tipogasto" id="tipogasto" value="<?= (isset($tipogasto) ? $tipogasto : 'caja') ?>"> 
                        <!--<button onclick="verificarUnicidad()">verificarUnicidad</button>-->
                        <?php echo form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div id="Modal_suppliers" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar Proveedor</h4>
      </div>
      <div class="modal-body">


            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="code"><?= $this->lang->line("name"); ?></label>
                    <?= form_input('name', set_value('name'), 'class="form-control input-sm" id="name"'); ?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
                    <?= form_input('email', set_value('email'), 'class="form-control input-sm" id="email_address"'); ?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="contact"><?= $this->lang->line("contact"); ?></label>
                    <?= form_input('contact', set_value('contact'), 'class="form-control input-sm" id="contact"');?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
                    <?= form_input('phone', set_value('phone'), 'class="form-control input-sm" id="phone"');?>
                </div>

                <!--<div class="form-group">
                    <label class="control-label" for="cf1"><?= $this->lang->line("scf1"); ?></label>
                    <?= form_input('cf1', set_value('cf1'), 'class="form-control input-sm" id="cf1"'); ?>
                </div>-->

                <div class="form-group">
                    <label class="control-label" for="cf2"><?= $this->lang->line("scf2"); ?></label>
                    <?= form_input('cf2', set_value('cf2'), 'maxlength="11" class="form-control input-sm" id="cf2"');?>
                </div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="grabar_suppliers()">Grabar</button>

      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
/*
    var spoitems = {};
    if (localStorage.getItem('remove_spo')) {
        if (localStorage.getItem('spoitems')) {
            localStorage.removeItem('spoitems');
        }
        localStorage.removeItem('remove_spo');
    }
*/
</script>

<script type="text/javascript">
    var ar_items = new Array();
    var gIgv = 18;

    <?php 
        //echo $tipogasto;
        //die();
        if($tipogasto == "gastos"){ 
            echo "document.getElementById('tipogasto').value = 'gastos';\n";
        }
    ?>

    <?php 
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(7)',500);\n";

        // PARA EL CASO DE MODO EDICION
        if(isset($purchases_id)){
            $i=0;
            foreach($query->result() as $row){ 
                echo "ar_items[$i]={";
                echo "id:'" . $row->product_id . "',";
                echo "name:'" . $row->name . "',";
                echo "quantity:" . $row->quantity . ",";
                echo "cost:" . round($row->cost,2) . ",";
                echo "subtotal:'" . round($row->subtotal,2) . "'";
                echo "}\n";
                $i++;
            }
        }else{

            if(strlen($tipoDoc)>0){  // MODO INVALIDO
            
                $i=0;
                //foreach($query->result() as $row){ 
                for($i=0; $i < count($productos); $i++){
                    echo "ar_items[$i]={";
                    echo "id:'" .       $productos[$i]["product_id"] . "',";
                    echo "name:'" .     $productos[$i]["name"] . "',";
                    echo "quantity:" .  $productos[$i]["quantity"] . ",";
                    echo "cost:" .      round($productos[$i]["cost"],2) . ",";
                    echo "subtotal:'" . round($productos[$i]["subtotal"],2) . "'";
                    echo "}\n";
                }
                //var_dump($productos);
                //die();

            }else{ // CASO NUEVO

                // NO VA NADA

            }
        }

    ?>

    function grabar_suppliers(){
        let parametros = {
            name:   $("#name").val(),
            email:  $("#email").val(),
            contact:$("#contact").val(),
            phone:  $("#phone").val(),
            cf1:    $("#cf1").val(),
            cf2:    $("#cf2").val(),
            modal_sup: "modal"
        }
        $.ajax({
            data    : parametros,
            type    : 'get',
            url     : '<?php echo base_url('suppliers/add'); ?>',
            success: function(response){
                //alert(response)
                let objS = JSON.parse(response)

                const $select = $("#supplier");
                let valor = objS.valor
                let texto = objS.texto
                $select.append($("<option>", {
                    value: valor,
                    text: texto
                }));

            },
            error: function(){
                alert("No hay respuesta del Servidor.")
            }
        })
    }

    function Agregar(){
        // previa validacion:
        if ($("#quantity").val() <= 0){
            alert("Cantidad no puede ser negativo")
            return false
        }

        if ($("#cost").val() <= 0){
            alert("Costo no puede ser negativo")
            return false
        }

        agregar_item();
        cargar_items();

        // Borrando valores casillas
        $("#quantity").val(0)
        $("#cost").val(0)
        $("#product_id").val("")
    }

    function cargar_items(){
        let Limite = ar_items.length
        let gsubTotal = 0
        let cad = "<table>"

        cad += "<div class='table-responsive'>"
        cad += '<table id="clasico" class="table table-striped table-bordered">'
        cad += '<thead>'
        cad += '    <tr class="active">'
        cad += '        <th class="col-xs-4 col-sm-4"><?= lang("product"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-2"><?= lang("quantity"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-2"><?= lang("unit_cost"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-2" style="text-align:right"><?= lang("subtotal"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-1" style="width:25px;"><i class="fa fa-trash-o"></i></th>'
        cad += '    </tr>'
        cad += '</thead>'
        cad += '<tbody>'
        console.log("flow")
        console.log(JSON.stringify(ar_items))
        for(let i=0; i<Limite; i++){
            cad += "<tr>"
            cad += '<td style="text-align: left">' + ar_items[i]["name"] + '<input type="hidden" name="product_id[]" value="'+ar_items[i]['id'] + '"></td>'
            cad += '<td style="text-align: center"><input type="text" name="quantity[]" value="' + ar_items[i]["quantity"] + '" readonly></td>'
            cad += '<td style="text-align: center"><input type="text" name="cost[]" value="' + ar_items[i]["cost"] + '" readonly></td>'
            cad += '<td style="text-align: right">' + ar_items[i]["subtotal"] + "</td>"
            cad += '<td style="text-align: center"><a href="#" onclick="quitar_item(ar_items,'+i+')"><i class="fa fa-trash-o"></i></a></td>'
            cad += "</tr>"
            gParcial = 1 * ar_items[i]["quantity"] * ar_items[i]["cost"]
            
            gsubTotal += gParcial

            console.log("vamos ....[i]:" + i +  ", quantity:" + ar_items[i]["quantity"] + ", cost:" + ar_items[i]["cost"] + ", gsubTotal:" + gsubTotal)
        }
        console.log("flow2")
        cad += '</tbody>'

        cad += '<tfoot>'

        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"><?= lang('subtotal'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)" class="col-xs-2"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)" class="col-xs-2"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)" class="col-xs-2 text-right"><span id="gsubtotal">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;width:25px;height:17px;background-color:rgb(200,200,200)"></th>'
        cad += '    </tr>'

        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"><?= lang('igv'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"class="col-xs-2"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"class="col-xs-2"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"class="col-xs-2 text-right"><span id="gIgv">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;width:25px;height:17px;background-color:rgb(190,190,190)"></th>'
        cad += '    </tr>'

        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"><?= lang('total_1'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"class="col-xs-2 text-right"><span id="gTotal">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);width:25px;"></th>'
        cad += '    </tr>'
        cad += '</tfoot>'
        cad += "</table>"
        
        document.getElementById("taxi").innerHTML = cad
        //document.getElementById("lista_items").value = JSON.stringify(ar_items)
        
        let nIgv    = gsubTotal * (gIgv/100)
        let gTotal  = gsubTotal + nIgv

        let cSubTotal   = gsubTotal.toFixed(2);
        let cIgv        = nIgv.toFixed(2);
        let cTotal      = gTotal.toFixed(2);
        console.log("flow3")

        document.getElementById("gsubtotal").innerHTML      = cSubTotal
        document.getElementById("gIgv").innerHTML           = cIgv
        console.log(gsubTotal)
        //console.log(valor_total)
        document.getElementById("gTotal").innerHTML         = cTotal
        
        $("#txt_gSubtotal").val($("#gsubtotal").html())
        $("#txt_gIgv").val($("#gIgv").html())
        $("#txt_gTotal").val($("#gTotal").html())
    }

    cargar_items()

    function empty(data){
      if(typeof(data) == 'number' || typeof(data) == 'boolean')
      { 
        return false; 
      }
      if(typeof(data) == 'undefined' || data === null)
      {
        return true; 
      }
      if(typeof(data.length) != 'undefined')
      {
        return data.length == 0;
      }
      var count = 0;
      for(var i in data)
      {
        if(data.hasOwnProperty(i))
        {
          count ++;
        }
      }
      return count == 0;
    }

    function validar_gral(){
       
        // La fecha
        if(empty($("#date").val())){
            alert("Debe ingresar la Fecha")
            return false
        }

        let tipoDoc = document.getElementById("tipoDoc").value
        //if(tipoDoc != 'G'){
            if(empty(document.getElementById("fec_emi_doc").value)){
                alert("Falta Fec_emi_doc")
                document.getElementById("fec_emi_doc").focus()
                return false
            }
            
            if(empty(document.getElementById("fec_venc_doc").value)){
                alert("Falta Fec_venc_doc")
                document.getElementById("fec_venc_doc").focus()
                return false
            }

            if(document.getElementById("fec_emi_doc").value > document.getElementById("fec_venc_doc").value){
                alert("Fecha de Emision no puede ser mayor a fecha de vencimiento")
                document.getElementById("fec_venc_doc").focus()
                return false
            }
        //}

        nCargo = document.getElementById("cargo_servicio").value
        if(!empty(nCargo)){
            nCargo = nCargo * 1
            if (nCargo < 0){
                alert("Cargo es menor a cero")
                return false
            }
        }else{
            nCargo = 0
        }


        if(document.getElementById('tipogasto').value == 'caja'){

            let Proveedor = document.getElementById("supplier").value
            if(empty(Proveedor)){
                alert("Debe ingresar Proveedor.")
                document.getElementById("supplier").focus()
                return false
            }

            // Verificando que los montos no sean negativos
            let ct = document.getElementById("costo_tienda").value
            let cb = document.getElementById("costo_banco").value

            if (!empty(ct)){
                if(ct<0){
                    alert("Costo Tienda No puede ser menor a cero")
                    return false
                }
            }

            if (!empty(cb)){
                if(cb<0){
                    alert("Costo Banco No puede ser menor a cero")
                    return false
                }
            }

            let nTotal = (ct * 1) + (cb * 1)
            if (nTotal > 0){
                
                if (!Admin){
                    alert("No puede colocar Montos Pagados ni de Cajas, ni Tiendas, esto lo hace el Administrador")
                    return false
                }
            }

            // NUEVO:

            // Total productos:
            var gTotal = $("#gTotal").html() * 1

            // cargo_servicio:
            // nCargo

            // Total gral:
            var nTotalP = gTotal + nCargo
            //alert("gTotal:" + gTotal + ", nCargo:"+nCargo + ",nTotalP:" + nTotalP)

            // Total de cajas:
            // nTotal

            // Compraciones:
            if (nTotalP < nTotal){
                alert("Los total de Pago Tienda y Banco exceden al total del Documento (Total productos:"+nTotalP + ", Total cajas:" + nTotal + ")")
                return false
            }
        
            // Verificar que este documento no haya sido ingresado anteriormente
            <?php if(!isset($purchases_id)){ ?>
                /*if(!verificarUnicidad()){
                    alert("Este nro. de Documento con el mismo Proveedor ya existe")
                    return false
                }*/
            <?php } ?>
        }

        if(document.getElementById('tipogasto').value == 'gastos'){
            let Proveedor = document.getElementById("texto_supplier").value
            if(empty(Proveedor)){
                alert("Debe ingresar Proveedor.")
                document.getElementById("texto_supplier").focus()
                return false
            }
        }

        return true
    }

    function guardar_compra(){
        if(validar_gral()){
            //document.getElementById("add_purchase").click()
            document.getElementById("form_compra").submit();
        }
    }

    function marcar(i){
        document.getElementById("marca" + i).value = 2
    }

    function agregar_item(){
        let x1 = document.getElementById("product_id").value
        
        let combo = document.getElementById("product_id");
        let x1_name = combo.options[combo.selectedIndex].text;
        let x2 = document.getElementById("quantity").value
        let x3 = document.getElementById("cost").value
        let x4 = 1 * x2 * x3
        x4 = x4.toFixed(2)
        x4 = x4 * 1
        ar_items.push({id:x1, name:x1_name, quantity:x2, cost:x3, subtotal: x4})
    }

    function quitar_item(aro,pid){
        quitar_elemento(aro,pid)
        cargar_items()
    }

    function quitar_elemento(aro,pid){  // function OK
        aro.splice(pid,1)
    }

    function cadenar(are){
        return JSON.parse(are)
    }

    function agrega_fila(j){
        document.getElementById("column"+j).style.display = "block"
    }

    function verificarUnicidad(){
        //alert("adentro verificarUnicidad")
        let parametros = {
            nroDoc: $("#nroDoc").val(),
            supplier: $("#supplier").val()
        }
        $.ajax({
            data: parametros,
            type: 'get',
            url: '<?= base_url("purchases/verificarUnicidad") ?>',
            success: function(response){

                if(response * 1 > 0){
                    //alert("aqui en success false")
                    return false
                }
                //alert("aqui en success true")
                return true
            },
            error: function(){
                alert("Ocurre un Problema con VerificarUnicidad")
                return false
            }
        })
    }

    
</script>