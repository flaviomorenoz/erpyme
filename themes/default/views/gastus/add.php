<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<script>
    var gIgv = 18;
</script>

<?php

if(isset($purchases_id)){  // ES MODO EDICION
    $result = $this->db->select("purchases.nroDoc, purchases.cargo_servicio, purchases.tipoDoc, purchases.fec_emi_doc, purchases.fec_venc_doc,
        purchases.date, purchases.costo_tienda, purchases.costo_banco, purchases.supplier_id, purchases.texto_supplier, purchases.descuentos,
        purchases.nro_cta,purchases.nro_oper,purchases.banco, purchases.fecha_oper, purchases.store_id, purchases.clasifica1, purchases.clasifica2, purchases.subtotal, purchases.igv, purchases.total, purchases.note")
        ->from("purchases")
        ->where("purchases.id",$purchases_id)
        ->get()->result_array();

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
        $descuentos     = $r["descuentos"];
        $nro_cta        = $r["nro_cta"];
        $nro_oper       = $r["nro_oper"];
        $banco          = $r["banco"];
        $fecha_oper     = $r["fecha_oper"];
        $tienda         = $r["store_id"];
        $clasifica1     = $r["clasifica1"];
        $clasifica2     = $r["clasifica2"];
        $subtotal       = $r["subtotal"];
        $igv            = $r["igv"];
        $total          = $r["total"];
        $note           = $r["note"];
    }

    $query = $this->db->query("select a.id, a.purchase_id, a.product_id, a.cost, a.quantity, a.cost, a.subtotal, a.peso_caja, 
        c.name, c.unidad 
        from tec_purchase_items a
        left join tec_products c on a.product_id = c.id
        where a.purchase_id = $purchases_id");

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

        $i_ultimo       = 0; // contador de items  //
        $texto_supplier = "";
        $descuentos     = 0;
        $peso_caja      = 1;
        $fecha_oper     = "";
        $tienda         = "";
        $clasifica1     = "";
        $clasifica2     = "";
        $subtotal       = "";
        $igv            = "";
        $total          = "";
        $note           = "";
    }
}

?>
<section class="content" style="background-color: rgb(210,210,210);">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
    
                <div class="box-body">
                    <div class="col-sm-10 col-lg-12">
                        
                        <div class="row">
                            <div class="col-sm-10">

                                <?php echo form_open_multipart("gastus/add", 'class="validation" id="form_compra"'); ?>
                                <input type="hidden" name="edicion_purchase_id" id="edicion_purchase_id" value="<?= (isset($purchases_id) ? $purchases_id : "") ?>">

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Fecha de Registro</label>
                                            <?php
                                                $ar = array(
                                                    "name"  =>"date",
                                                    "id"    =>"date",
                                                    "type"  =>"date",
                                                    "value" => substr($dates,0,10),
                                                    "class" =>"form-control tip",
                                                    "readonly" => "readonly"
                                                );
                                                echo form_input($ar);
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Fecha de Emision</label>
                                            <?php
                                                $ar = array(
                                                    "name"  =>"fec_emi_doc",
                                                    "id"    =>"fec_emi_doc",
                                                    "type"  =>"date",
                                                    "value" => $fec_emi_doc,
                                                    "class" =>"form-control tip"
                                                );
                                                echo form_input($ar);
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-sm-3" style="border-style:none; border-color:red;">
                                        <div class="form-group">
                                            <label for="">Tienda:</label>
                                            <?php
                                                $group_id = $this->session->userdata["group_id"];
                                                $q = $this->db->get('stores');

                                                $ar = array();
                                                if ($group_id == '1'){
                                                    $ar[] = "Todas";
                                                    foreach($q->result() as $r){
                                                        $ar[$r->id] = $r->state;
                                                    }
                                                }else{
                                                    foreach($q->result() as $r){
                                                        if($r->id == $this->session->userdata["store_id"]){
                                                            $ar[$r->id] = $r->state;
                                                        }
                                                    }
                                                }
                                                echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required"');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- AQUI VAN LOS COMBOS NUEVOS -->
                                <div class="row">
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <!--<input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">-->
                                            <label for="product_id">Tipo de Gasto</label>
                                            <?php 
                                               $cSql = "select id, descrip, comentario from tec_tipo_gastos order by descrip";
                                               $result = $this->db->query($cSql)->result_array();
                                               $ar_p[""] = "--- Seleccione Tipo ---";
                                               foreach($result as $r){
                                                    $ar_p[ $r["id"] ] = $r["descrip"];
                                               }

                                               echo form_dropdown('clasifica1',$ar_p,$clasifica1,'class="form-control tip" id="clasifica1" required="required"');
                                            ?>

                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <!--<input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">-->
                                            <label for="product_id">Detalle Gasto</label>
                                            <?php 
                                               
                                               //$cSql = "select a.id, a.descrip, b.id as id1, b.tipo_id, b.descrip as descrip1 from tec_tipo_gastos a left join tec_subtipo_gastos b on a.id = b.tipo_id order by a.id";
                                               

                                               $this->db->select("a.id, a.descrip, b.id as id1, b.tipo_id, b.descrip as descrip1");
                                               $this->db->from("tec_tipo_gastos as a");
                                               $this->db->join("tec_subtipo_gastos as b","a.id = b.tipo_id","left");
                                               $this->db->where("a.id",$clasifica1);
                                               
                                               //echo $this->db->get_compiled_select();
                                               //die();

                                               $result = $this->db->get()->result_array();

                                               //$result = null;
                                               //$result = $this->db->query($cSql)->result_array();
                                               $ar_p = array();
                                               $ar_p[""] = "--- Seleccione Detalle ---";
                                               foreach($result as $r){
                                                    $ar_p[ $r["id1"] ] = $r["descrip"] . " - " . $r["descrip1"];
                                               }

                                               echo form_dropdown('clasifica2',$ar_p, $clasifica2,'class="form-control tip" id="clasifica2" required="required"');
                                            ?>

                                        </div>
                                    </div>

                                </div>


                                <!-- ==== TIPO DE DOCUMENTO =========-->
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tipodoc">Tipo Doc.</label>
                                            <?php 
                                               //echo $this->purchases_model->combo_TipoDoc($tipo_doc); 
                                               $ar = array('F'=>'Factura','B'=>'Boleta','G'=>'Guia Interna','R'=>'Recibo Honorarios');
                                               echo form_dropdown('tipoDoc', $ar, $tipoDoc, 'class="form-control tip" id="tipoDoc" required="required" onchange="generar_nro(this)"');
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="tipodoc"><?= lang('Nro Doc'); ?></label>
                                            <?= form_input('nroDoc', $nroDoc, 'class="form-control tip" id="nroDoc"'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <!--<button type="button" onclick="relleno_azar()">Relleno</button>-->
                                    </div>

                                </div>
                                
                                <div class="row">

                                    <div class="col-md-2">
                                        
                                    </div>

                                </div>

                                <script>
                                    function relleno_azar(){
                                        document.getElementById("date").value           = "<?= date("Y-m-d") ?>"
                                        document.getElementById("tienda").value         = "1"
                                        document.getElementById("nroDoc").value         = "12345" 
                                        document.getElementById("clasifica1").value     = "10"
                                        document.getElementById("clasifica2").value     = "25"
                                        document.getElementById("subtotal").value       = "100"
                                        document.getElementById("igv").value            = "18"
                                        document.getElementById("total").value          = "118"
                                        document.getElementById("note").innerHTML       = "Enrique Sileri"
                                    }

                                    function distribuye(obj){
                                        let valor       = obj.value
                                        let nIgv        = valor * (gIgv/100) / (1 + (gIgv/100))
                                        let nSubtotal   = valor / (1 + (gIgv/100))
                                        let nTotal      = valor

                                        if(document.getElementById("tipoDoc").value != "G" && document.getElementById("tipoDoc").value != "R"){
                                            document.getElementById("subtotal").value   = nSubtotal.toFixed(2)
                                            document.getElementById("igv").value        = nIgv.toFixed(2)
                                        }
                                        //console.log("Pasó por distribuye")
                                    }
                                </script>

                                <!-- AQUI IRA SUBTOTAL, IGV Y TOTAL --->
                                <div class="row">

                                    <div class="col-sm-2" style="margin-top:10px">
                                        <div class="form-group">
                                            <label><?= lang('Subtotal'); ?></label>
                                            <?php
                                                //die($dates);
                                                    $ar = array(
                                                    "name"  =>"subtotal",
                                                    "id"    =>"subtotal",
                                                    "type"  =>"text",
                                                    "value" => $subtotal,
                                                    "class" =>"form-control tip",
                                                    "onfocusout" => "",
                                                    "readonly" => "readonly"
                                                );
                                                echo form_input($ar);
                                            ?>
                                        </div>

                                    </div>
                                    
                                    <div class="col-sm-2" style="margin-top:10px">
                                        <div class="form-group">
                                            <label><?= lang('Igv'); ?></label>
                                            <?php
                                                //die($dates);
                                                    $ar = array(
                                                    "name"  =>"igv",
                                                    "id"    =>"igv",
                                                    "type"  =>"text",
                                                    "value" => $igv,
                                                    "class" =>"form-control tip",
                                                    "readonly" => "readonly"
                                                );
                                                echo form_input($ar);
                                            ?>
                                        </div>

                                    </div>

                                    <div class="col-sm-2" style="margin-top:10px">
                                        <div class="form-group">
                                            <label><?= lang('Total'); ?></label>
                                            <?php
                                                //die($dates);
                                                    $ar = array(
                                                    "name"  =>"total",
                                                    "id"    =>"total",
                                                    "type"  =>"text",
                                                    "value" => number_format($total*1,2,".",""),
                                                    "class" =>"form-control tip",
                                                    "onblur" =>"distribuye(this)"
                                                );
                                                echo form_input($ar);
                                            ?>
                                        </div>

                                    </div>

                                    <div class="col-sm-2" style="margin-top:18px">
                                        <br>
                                        <label>Incluye IGV:</label><input type="checkbox" id="chk_igv" name="chk_igv" value="1">
                                    </div>

                                </div>

                                <!-- CASILLA OTROS DATOS ---->
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>Otros</label>
                                            <input type="text" name="cargo_servicio" id="cargo_servicio" class="form-control tip">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <?= lang("note", 'note'); ?>
                                    <?= form_textarea('note', $note, 'class="form-control redactor" id="note"'); ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="form-group">
                                            <button type="button" onclick="guardar_compra()" class="btn btn-primary">Guardar Compra</button>
                                            <button type="button" id="reset" class="btn btn-danger"><?= lang('reset'); ?></button>
                                            <a href="<?= site_url('gastus') ?>" class="btn btn-warning">Regresar</a>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <?= form_submit('add_purchase', '.', 'class="" id="add_purchase"'); ?>
                                    </div>
                                </div>
                                  
                                <input type="hidden" name="txt_gSubtotal" id="txt_gSubtotal">
                                <input type="hidden" name="txt_gIgv" id="txt_gIgv">
                                <input type="hidden" name="txt_gTotal" id="txt_gTotal">
                                <input type="hidden" name="modo_edicion" id="modo_edicion" value="1">
                                <input type="hidden" name="tipogasto" id="tipogasto" value="gastos"> 
                                <?php echo form_close();?>

                            </div>
                            <div  class="col-sm-4">

                                <div id="confirmaciones" class="alert alert-warning alert-dismissable" style="display: none;">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <h4><i class="icon fa fa-check"></i>Confirme</h4>
                                        Confirma que desea guardar los datos?<br>
                                    <button class="btn btn-success" onclick="document.getElementById('form_compra').submit();">Aceptar</button>&nbsp;&nbsp;&nbsp;
                                    <button class="btn btn-danger" onclick="document.getElementById('confirmaciones').style.display = 'none';">Cancelar</button>
                                </div>

                            </div>
                        </div>
                    </div>
                            
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Nuevo Insumo -->
<div id="Modal_insumos" class="modal fade" role="dialog">
  <div id="oreo" class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background-color:orange">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Agregar Insumos</b></h4>
      </div>
      
      <div class="modal-body">
        <div class="col-md-8">

            <div class="row" style="margin-top:5px;">
                <div class="col-sm-5">
                    Nombre Producto:
                </div>

                <div class="col-sm-7">
                    <input type="text" id="descPro" name="descPro" size="30" class="form-control" placeholder="Nombre Producto">
                    <input type="hidden" id="idPro" name="idPro">
                </div>
            </div>

            <div class="row" style="margin-top:5px;">
                <div class="col-sm-5">
                    Unidad de medida:
                </div>

                <div class="col-sm-7">
                    <?php 
                        $ar_unidad = array('UNIDAD','GRAMO','KILO','LITRO');
                        echo "<select class=\"form-control\" name=\"unidad\" id=\"unidad\">";
                        for($i=0; $i<count($ar_unidad); $i++){
                            echo "<option value=\"" . $ar_unidad[$i] . "\">" . $ar_unidad[$i] . "</option>";
                        }
                        echo "</select>";
                    ?>
                </div>
            </div>

        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="grabar_insumos()">Grabar</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal SUPPLIERS -->
<script type="text/javascript">

function abrir_modal_proveedor(){
    document.getElementById("name").value       = ""
    document.getElementById("email").value      = ""
    document.getElementById("contact").value    = ""
    document.getElementById("phone").value      = ""
    document.getElementById("cf2").value        = ""
}

const $total            = document.querySelector("#total");
const $nroDoc           = document.querySelector("#nroDoc");
const $cargo_servicio   = document.querySelector("#cargo_servicio");
const $subtotal        = document.querySelector("#subtotal");
const $igv              = document.querySelector("#igv");
const $note             = document.querySelector("#note");
const $date             = document.querySelector("#date"); 
const $fec_emi_doc      = document.querySelector("#fec_emi_doc");

// Escuchamos el keydown y prevenimos el evento
$total.addEventListener("keydown", (evento)             => { if (evento.key == "Enter") { evento.preventDefault(); return false; }});
$nroDoc.addEventListener("keydown", (evento)            => { if (evento.key == "Enter") { evento.preventDefault(); return false; }});
$cargo_servicio.addEventListener("keydown", (evento)    => { if (evento.key == "Enter") { evento.preventDefault(); return false; }});
$subtotal.addEventListener("keydown", (evento)         => { if (evento.key == "Enter") { evento.preventDefault(); return false; }});
$igv.addEventListener("keydown", (evento)               => { if (evento.key == "Enter") { evento.preventDefault(); return false; }});
$note.addEventListener("keydown", (evento)              => { if (evento.key == "Enter") { evento.preventDefault(); return false; }});
$date.addEventListener("keydown", (evento)              => { if (evento.key == "Enter") { evento.preventDefault(); return false; }});
$fec_emi_doc.addEventListener("keydown", (evento)       => { if (evento.key == "Enter") { evento.preventDefault(); return false; }});

</script>
<div id="Modal_suppliers" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background-color:orange">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Agregar Proveedor</b></h4>
      </div>
      <div class="modal-body">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="code"><?= $this->lang->line("name"); ?></label>
                    <?= form_input('name', set_value('name'), 'class="form-control input-sm" id="name"'); ?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
                    <?= form_input('email', set_value('email'), 'class="form-control input-sm" id="email"'); ?>
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="grabar_suppliers()">Grabar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(7)',500);\n";
    ?>

    function es_numerico(cValor){
        //let cOtros = document.getElementById("cargo_servicio").value
        if(is_numeric(cValor)){
            return true
        }else{
            return false
        }
    }

    document.getElementById('chk_igv').checked = true;
    var ar_items = new Array();

    <?php 
        if($tipogasto == "gastos"){ 
            echo "document.getElementById('tipogasto').value = 'gastos';\n";
        }
    ?>

    <?php
        // Variable Admin
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . "\n";
    ?>

    $(document).ready(function(){
        $("#clasifica1").on('change', function () {
            $("#clasifica1 option:selected").each(function () {
                elegido=$(this).val();
                $.get("<?= base_url("gastus/dependencia") ?>", { elegido: elegido }, function(data){
                    console.log(data)
                    $("#clasifica2").html(data);
                });         
            });
       });
    });

    //setTimeout("let mi_clasifica = document.getElementById('clasifica1');var event = new Event('change'); mi_clasifica.dispatchEvent(event);",350)

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
                let rpta = objS.rpta 
                if (rpta == 1){
                    let valor = objS.valor
                    let texto = objS.texto
                    $select.append($("<option>", {
                        value: valor,
                        text: texto
                    }));
                }else{
                    alert("Corrija, el proveedor no se grabó, talvez ya exista ese nombre")
                }

            },
            error: function(){
                alert("No hay respuesta del Servidor.")
            }
        })
    }

    function Agregar(){
        // previa validacion:
        if ($("#quantity").val() <= 0){
            alert("Cantidad no puede ser 0 negativo")
            return false
        }

        if ($("#cost").val() <= 0){
            alert("Costo no puede ser negativo")
            return false
        }

        agregar_item(); // lo ingresa al array ar_items
        
        cargar_items();

        // Borrando valores casillas
        $("#quantity").val(0)
        $("#cost").val(0)
        $("#product_id").val("")
        $("#peso_caja").val(1)
    }

    function cargar_items(){
        let Limite = ar_items.length
        let gsubTotal = 0
        let cad = "<table>"

        cad += "<div class='table-responsive'>"
        cad += '<table id="clasico" class="table table-striped table-bordered">'
        cad += '<thead>'
        cad += '    <tr class="active">'
        cad += '        <th class="col-xs-4 col-sm-5"><?= lang("product"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-1"><?= lang("quantity"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-2"><?= lang("unit_cost"); ?></th>'
        //cad += '        <th class="col-xs-2 col-sm-1">Peso_caja</th>'
        cad += '        <th class="col-xs-2 col-sm-2" style="text-align:right"><?= lang("subtotal"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-1" style="width:25px;"><i class="fa fa-trash-o"></i></th>'
        cad += '    </tr>'
        cad += '</thead>'
        cad += '<tbody>'
        
        for(let i=0; i<Limite; i++){
            cad += "<tr>"
            cad += '<td style="text-align: left" class="col-xs-5 col-sm-5">' + ar_items[i]["name"] + '<input type="hidden" name="product_id[]" value="'+ar_items[i]['id'] + '" class="form-control"></td>'
            cad += '<td class="col-xs-2 col-sm-1"><input size="4" style="text-align: right" type="text" name="quantity[]" value="' + ar_items[i]["quantity"] + '"  class="form-control" readonly></td>'
            cad += '<td class="col-xs-2 col-sm-2"><input size="9" style="text-align: right" type="text" name="cost[]" value="' + ar_items[i]["cost"] + '"  class="form-control" readonly></td>'
            let nSubTotalx = ar_items[i]["subtotal"] * 1
            // .toLocaleString('es-PE',{ style: 'currency', currency: 'PEN' })
            cad += '<td style="text-align: right" class="col-xs-3 col-sm-2">' + nSubTotalx.toLocaleString('es-PE') + "</td>"
            cad += '<td style="text-align: center" class="col-xs-2 col-sm-1"><a href="#" onclick="quitar_item(ar_items,'+i+')"><i class="fa fa-trash-o"></i></a></td>'
            cad += "</tr>"
            gParcial = 1 * ar_items[i]["quantity"] * ar_items[i]["cost"]
            
            gsubTotal += gParcial

            //console.log("vamos ....[i]:" + i +  ", quantity:" + ar_items[i]["quantity"] + ", cost:" + ar_items[i]["cost"] + ", gsubTotal:" + gsubTotal)
        }
        
        // Tema de descuentos .......
        let nDscto = cDscto = 0
        if($("#descuentos").length > 0){
            nDscto = $("#descuentos").val() * 1
            nDscto = nDscto.toFixed(2)
            cDscto = "Dscto. = " + nDscto
        }

        cad += '</tbody>'

        cad += '<tfoot>'

        // *** FILA SUBTOTAL **********
        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)" class="col-xs-5 col-sm-5"><?= lang('subtotal'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)" class="col-xs-2 col-sm-1"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)" class="col-xs-2 col-sm-2"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)" class="col-xs-2 col-sm-2 text-right"><span id="gsubtotal">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;width:25px;height:17px;background-color:rgb(200,200,200)" class="col-xs-2 col-sm-1"></th>'
        cad += '    </tr>'

        // *** FILA IGV **********
        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)" class="col-xs-5 col-sm-5"><?= lang('igv'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"class="col-xs-2 col-sm-1"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"class="col-xs-2 col-sm-2"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)" class="col-xs-2 col-sm-2 text-right"><span id="gIgv">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;width:25px;height:17px;background-color:rgb(190,190,190)" class="col-xs-2 col-sm-1"></th>'
        cad += '    </tr>'

        
        // *** FILA DSCTO **********
        if(nDscto > 0){
            cad += '    <tr class="active">'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);color:rgb(200,0,0);font-weight:bold" class="col-xs-5 col-sm-5">Dscto.</th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-2"></th>'
            //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);color:rgb(200,0,0)" class="col-xs-2 col-sm-2 text-right">-'+nDscto+'</th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);width:25px;" class="col-xs-2 col-sm-1"></th>'
            cad += '    </tr>'
        }

        // *** FILA TOTAL **********
        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-5 col-sm-5"><?= lang('total_1'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-2"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 text-right"><span id="gTotal">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);width:25px;" class="col-xs-2 col-sm-1"></th>'
        cad += '    </tr>'
        cad += '</tfoot>'
        cad += "</table>"
        
        document.getElementById("taxi").innerHTML = cad

        let nIgv = 0
        let gTotal = 0

        nIgv    = gsubTotal * (gIgv/100)

        let cSubTotal   = gsubTotal.toFixed(2) * 1;
        cSubTotal       = cSubTotal.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})  // ,{ style: 'currency', currency: 'PEN' }

        let cIgv        = nIgv.toFixed(2) * 1;
        cIgv            = cIgv.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})

        gTotal          = (gsubTotal.toFixed(2) * 1) + (nIgv.toFixed(2) * 1) - (nDscto * 1)
        let cTotal      = gTotal.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})

        document.getElementById("gsubtotal").innerHTML      = cSubTotal
        document.getElementById("gIgv").innerHTML           = cIgv
        document.getElementById("gTotal").innerHTML         = cTotal
        
        //$("#txt_gSubtotal").val($("#gsubtotal").html())
        //$("#txt_gIgv").val($("#gIgv").html())
        //$("#txt_gTotal").val($("#gTotal").html())

        $("#txt_gSubtotal").val(gsubTotal.toFixed(2))
        $("#txt_gIgv").val(nIgv.toFixed(2))
        $("#txt_gTotal").val(gTotal.toFixed(2)) // Number.parseFloat
    }

    //cargar_items()

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

        if(empty($("#fec_emi_doc").val())){
            alert("Debe ingresar la Fecha de Emisión")
            document.getElementById("fec_emi_doc").focus()
            return false
        }

        let tipoDoc = document.getElementById("tipoDoc").value
        
        //console.log("valida 1")
        
        if(document.getElementById("tienda").value == '0'){
            alert("Debe escoger una Tienda")
            document.getElementById("tienda").focus()
            return false
        }

        var nroDoc = document.getElementById("nroDoc").value
        
        if(tipoDoc != 'G'){
            if (nroDoc.length == 0){
                alert("Debe ingresar el Nro. de Documento.")
                document.getElementById("nroDoc").focus()
                return false
            }
        }

        let clasifica1 = document.getElementById('clasifica1').value

        if(empty(clasifica1)){
            alert("Debe ingresar Tipo de Gasto.")
            document.getElementById("clasifica1").focus()
            return false
        }

        let clasifica2 = document.getElementById('clasifica2').value

        if(empty(clasifica2) || clasifica2 == "0"){
            alert("Debe ingresar detalle de Gasto.")
            document.getElementById("clasifica2").focus()
            return false
        }

        if(!es_numerico(document.getElementById('cargo_servicio').value)){
            let valore = document.getElementById('cargo_servicio').value
            if (valore.length > 0){
                alert("No es un valor numérico en 'Otros'.")
                document.getElementById("cargo_servicio").focus()
                return false
            }
        }

        if(!es_numerico(document.getElementById('total').value)){
            alert("No es un valor numérico en 'Total'.")
            document.getElementById("total").focus()
            return false
        }

        return true
    }

    function guardar_compra(){
        if(validar_gral()){
            //rpta = confirm("¿Confirme que desea grabar Documento?")
            //if (rpta){ document.getElementById("form_compra").submit(); }
            document.getElementById("confirmaciones").style.display = "block"
        }
    }

    function marcar(i){
        document.getElementById("marca" + i).value = 2
    }

    function agregar_item(){
        let x1      = document.getElementById("product_id").value
        let combo   = document.getElementById("product_id");
        let x1_name = combo.options[combo.selectedIndex].text;
        let x2      = document.getElementById("quantity").value
        let x3      = document.getElementById("cost").value

        if(document.getElementById("chk_igv").checked == true){
            x3 = x3 / (1+(gIgv/100))
            x3 = x3.toFixed(4)
        }

        let x4      = 1 * x2 * x3
        x4          = x4.toFixed(2)
        ar_items.push({id:x1, 
            name:x1_name, 
            quantity:x2, 
            cost:x3, 
            subtotal: x4})
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
                    return false
                }
                return true
            },
            error: function(){
                alert("Ocurre un Problema con VerificarUnicidad")
                return false
            }
        })
    }

    function generar_nro(obj){

        // Se borra las casillas posteriores
        document.getElementById("nroDoc").value         = ""
        document.getElementById("subtotal").value       = ""
        document.getElementById("igv").value            = ""
        document.getElementById("total").value          = ""

        if(obj.value == "G"){
            document.getElementById("nroDoc").readOnly = true
        }else{
            document.getElementById("nroDoc").readOnly = false
        }
        if(document.getElementById("txt_tipoDoc").value == ""){
            document.getElementById("txt_tipoDoc").value = obj.value
        }
    }

    function grabar_insumos(){
        var parametros = {
            descPro     : document.getElementById('descPro').value,
            unidad      : document.getElementById('unidad').value,
            idPro       : document.getElementById('idPro').value
        }
        $.ajax({
            data    : parametros,
            url     :'<?= base_url('insumos/grabar_insumos') ?>',
            type    :'get',
            success :function(response){
                
                var ar = JSON.parse(response)
                var cad = ""
                
                if(ar.error == true){
                    alert(ar.rpta)
                }
                
                /*
                toastr.options = {
                  "debug": false,
                  "positionClass": "toast-bottom-right",
                  "onclick": null,
                  "fadeIn": 300,
                  "fadeOut": 100,
                  "timeOut": 3000,
                  "extendedTimeOut": 1000
                }

                toastr.success("<br>" + ar.rpta)
                setTimeout("document.getElementById('listado_i').click()",4000)
                */

                const $select = $("#product_id");
                
                let texto = ar.rpta 
                
                // mejorando
                let nin     = texto.indexOf(",")
                let cDescrip  = texto.substr(0,nin)
                
                ubi2 = texto.indexOf(",",nin+1)

                let cId = texto.substr(nin+1,ubi2-(nin+1))

                ubi3 = texto.indexOf(",",ubi2)

                let cUnidad = texto.substr(ubi2+1,texto.length-(ubi2+1)-1)
                
                $select.append($("<option>", {
                    value: cId,
                    text: cDescrip + " (" + cUnidad + ")"
                }));

            }
        })
    }
</script>