<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
if(!isset($desde)){         $desde = "";    }
if(!isset($hasta)){         $hasta = "";    }
if(!isset($tienda)){        $tienda = "0";  }
if(!isset($proveedor)){     $proveedor = "0";}
if(!isset($fec_emi)){       $fec_emi = "";  }
if(!isset($clasifica1)){    $clasifica1 = "";}
if(!isset($clasifica2)){    $clasifica2 = "";}
?>
<style type="text/css">
    .agorero{
        background-color: yellow;
        text-align: left;
        padding-right: 10px;
        margin-right: 20px;
    }
</style>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(7)',500);\n";
    ?>

    $(document).ready(function(){

        if (get('remove_spo')) {
            if (get('spoitems')) {
                remove('spoitems');
            }
            remove('remove_spo');
        }
        <?php

        if ($this->session->userdata('remove_spo')) {
            ?>
            if (get('spoitems')) {
                remove('spoitems');
            }
            <?php
            $this->tec->unset_data('remove_spo');
        }
        ?>
        function attach(x) {
            if (x !== null) {
                return '<a href="<?=base_url();?>uploads/'+x+'" target="_blank" class="btn btn-primary btn-block btn-xs"><i class="fa fa-chain"></i></a>';
            }
            return '';
        }

        var opcion_almacenaje = true
        if (typeof(Storage) == "undefined"){
            opcion_almacenaje = false
        }

        var table = $('#purData').DataTable({
            'language': {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            'ajax' : { 
                url: '<?=site_url('gastus/get_purchases');?>', 
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                    d.desde = (opcion_almacenaje) ? localStorage.getItem("gastos_filtro_desde") :document.getElementById('desde').value; 
                    d.hasta = (opcion_almacenaje) ? localStorage.getItem("gastos_filtro_hasta") :document.getElementById('hasta').value; 
                    d.tienda = (opcion_almacenaje) ? localStorage.getItem("gastos_filtro_tienda") :document.getElementById('tienda').value;
                    d.fec_emi = (opcion_almacenaje) ? localStorage.getItem("gastos_filtro_fec_emi") :document.getElementById('fec_emi').value;
                    d.clasifica1 = (opcion_almacenaje) ? localStorage.getItem("gastos_filtro_clasifica1") :document.getElementById('clasifica1').value;
                    d.clasifica2 = (opcion_almacenaje) ? localStorage.getItem("gastos_filtro_clasifica2") :document.getElementById('clasifica2').value;
                }
            },
            "buttons": [
                // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] } },
                { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
                exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] } },
                { extend: 'colvis', text: 'Filtro'},
            ],
            "columns": [
                { "data": "id", "visible": true },
                { "data": "state" },
                { "data": "date"}, /* , "render": hrld */
                { "data": "fec_emi_doc"},
                { "data": "tipoDoc","className": "text-center"},
                { "data": "nroDoc"},
                { "data": "clasifica1"},
                { "data": "clasifica2"},
                { "data": "subtotal"},
                { "data": "igv"},
                { "data": "total", "render": function ( data, type, row, meta ){ 
                    let datin = data * 1;
                    return datin.toFixed(2); } },
                { "data": "username"},
                { "data": "Actions", "searchable": false, "orderable": false }
            ],
            "footerCallback": function (tfoot, data, start, end, display){
                var api = this.api(), data;
                /*$(api.column(9).footer()).html( cf(api.column(9).data().reduce( function (a, b) { 
                    paz = pf(a) + pf(b)
                    return paz;
                }, 0)) );*/

                $(api.column(10).footer()).html( cf(api.column(10).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                //$(api.column(11).footer()).html( cf(api.column(11).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
            },
            "scrollX": true,
            "rowCallback": function(Row, Data){
                $('td', Row).css('background-color', Data['colores']);
            }
        });
        
        $('#search_table').on( 'keyup change', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (((code == 13 && table.search() !== this.value) || (table.search() !== '' && this.value === ''))) {
                table.search( this.value ).draw();
            }
        });

        table.columns().every(function () {
            var self = this;
            $( 'input.datepicker', this.footer() ).on('dp.change', function (e) {
                self.search( this.value ).draw();
            });
            $( 'input:not(.datepicker)', this.footer() ).on('keyup change', function (e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (((code == 13 && self.search() !== this.value) || (self.search() !== '' && this.value === ''))) {
                    self.search( this.value ).draw();
                }
            });
            $( 'select', this.footer() ).on( 'change', function (e) {
                self.search( this.value ).draw();
            });
        });

        cambiando_casillas()

    });

    function cambiando_casillas(){
        // actualizando las casillas con localStorage      
        if (typeof(Storage) !== "undefined") {
            if(localStorage.getItem("gastos_filtro_desde") != "null")
                $("#desde").val(localStorage.getItem("gastos_filtro_desde"))
            if(localStorage.getItem("gastos_filtro_hasta") != "null")
                $("#hasta").val(localStorage.getItem("gastos_filtro_hasta"))
            
            if(localStorage.getItem("gastos_filtro_tienda") != "null"){
                $("#tienda").val(localStorage.getItem("gastos_filtro_tienda"))
            }
            if(localStorage.getItem("gastos_filtro_fec_emi") != "null")
                $("#fec_emi").val(localStorage.getItem("gastos_filtro_fec_emi"))
            if(localStorage.getItem("gastos_filtro_clasifica1") != "null")
                $("#clasifica1").val(localStorage.getItem("gastos_filtro_clasifica1"))
            if(localStorage.getItem("gastos_filtro_clasifica2") != "null")
                $("#clasifica2").val(localStorage.getItem("gastos_filtro_clasifica2"))
        }
    }
</script>

<style type="text/css">
    .table td:nth-child(9) { text-align: right; padding-right: 10px}
    .table td:nth-child(10) { text-align: right; padding-right: 10px}
    .table td:nth-child(11) { text-align: right; padding-right: 10px}
</style>

<section class="content">
    
    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="display:flex;margin-bottom: 5px;">
        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">F. Registro - Desde:</label>
                <input type="date" name="desde" id="desde" value="<?= $desde ?>" class="form-control">
            </div>    
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">F. Registro - Hasta:</label>
                <input type="date" name="hasta" id="hasta" value="<?= $hasta ?>" class="form-control">
            </div>
        </div>
        
        <div class="col-sm-1" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    $q = $this->db->get('stores');

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

        <!--<div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Proveedor:</label>
                <?php
                    $q = $this->db->order_by('name')->get('suppliers');
                    $ar = array();
                    $ar[] = "Todas";
                    foreach($q->result() as $r){
                        $ar[$r->id] = $r->name;
                    }
                    echo form_dropdown('proveedor', $ar, $proveedor, 'class="form-control tip" id="proveedor" required="required"');
                ?>
            </div>
        </div>-->

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Fecha de Emisión:</label>
                <input type="date" name="fec_emi" id="fec_emi" value="<?= $fec_emi ?>" class="form-control">
            </div>
        </div>

        <div class="col-sm-6 col-sm-2">
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

                   echo form_dropdown('clasifica1', $ar_p, $clasifica1,'class="form-control tip" id="clasifica1" required="required"');
                ?>

            </div>
        </div>

        <div class="col-sm-2">
            <div class="form-group">
                <!--<input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">-->
                <label for="product_id">Detalle Gasto</label>
                <?php 
                   
                   $cSql = "select a.id, a.descrip, b.id as id1, b.tipo_id, b.descrip as descrip1 from tec_tipo_gastos a left join tec_subtipo_gastos b on a.id = b.tipo_id order by a.id";
                   $result = null;
                   $result = $this->db->query($cSql)->result_array();
                   $ar_p = array();
                   $ar_p[""] = "--- Seleccione Detalle ---";
                   foreach($result as $r){
                        $ar_p[ $r["id1"] ] = $r["descrip1"];
                   }

                   echo form_dropdown('clasifica2', $ar_p, $clasifica2,'class="form-control tip" id="clasifica2" required="required"');
                ?>
            </div>
        </div>

        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: 20px 0px 20px 0px;">
            <div class="row">
                <div class="col-sm-5" style="padding:5px 0px 0px 0px;">
                    <button onclick="activo1()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/gastus/search.png") ?>" height="30px"></button>
                </div>
                <div class="col-sm-5" style="padding:5px 0px 0px 0px">
                    <button onclick="limpiar()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/gastus/eliminar.png") ?>" height="30px"></button>
                </div>
            </div>
        </div>

    </div>

    <!--<div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-6">
            <b><span id="txt_total" style="font-size:16px; font-weight:bold;"></span></b>
        </div>        
        <div class="col-sm-3">
        </div>        
    </div>-->

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        
                        <!--<a href="<?= base_url("purchases/index/2021-05-26/2021-06-01") ?>">Refresh</a><br>-->
                        <table id="purData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;" data-page-length='25'>
                            <thead>
                                <!--<tr>
                                    <td colspan="11" class="p0" style="border-style: solid; border-color: black; border-width: 1px;">
                                        <input type="text" class="form-control b0" name="search_table" id="search_table" 
                                        placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;">
                                    </td>
                                </tr>-->
                                <tr class="active">
                                    <th style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Tienda'); ?></th>
                                    <th class="col-xs-2 col-sm-1">F. Registro</th>
                                    <th class="col-xs-2 col-sm-1">F. Emision</th>
                                    <th class=""><?= lang('Tipo'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Nro.Doc'); ?></th>
                                    <th class="col-xs-1 col-sm-1">Tipo Gasto</th>
                                    <th class="col-xs-1 col-sm-1">Detalle</th>
                                    <th class="col-xs-1 col-sm-1">SubTotal</th>
                                    <th class="col-xs-1 col-sm-1">Igv</th>
                                    <th class="col-xs-1 col-sm-1">Total</th>
                                    <th class="col-xs-1 col-sm-1">User</th>
                                    <th style="width:75px;"><?= lang('actions'); ?></th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="12" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th style="max-width:30px;"><input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Tda'); ?>]"></th>
                                    <th class="col-xs-2 col-sm-1"></th>
                                    <th class="col-xs-2 col-sm-1"></th>
                                    <th class="col-xs-1 col-sm-1"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Nro'); ?>]"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[Total]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[User]"></th>
                                    <th style="width:75px;"><?= lang('actions'); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="refresco"></div>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.datepicker').datetimepicker(
            {
                format: 'YYYY-MM-DD', 
                showClear: true, 
                showClose: true, 
                useCurrent: false, 
                widgetPositioning: 
                    {
                        horizontal: 'auto', 
                        vertical: 'bottom'
                    }, 
                widgetParent: $('.dataTable tfoot')
            }
        );

        setTimeout("cambiando_casillas_gastos()",700);
    });

    function activo1(){
        let desde       = document.getElementById("desde").value
        let hasta       = document.getElementById("hasta").value
        if(desde == ""){ desde = "null" }
        if(hasta == ""){ hasta = "null" }
        let tienda      = document.getElementById("tienda").value
        let fec_emi     = document.getElementById("fec_emi").value
        if(fec_emi == ""){ fec_emi = "null" }
        let cadena      = ""
        
        let clasifica1 = document.getElementById("clasifica1").value

        if (clasifica1.length == 0){
            clasifica1 = "null"
        }

        let clasifica2 = document.getElementById("clasifica2").value

        if (clasifica2.length == 0){
            clasifica2 = "null"
        }

        if(desde.length > 0 || hasta.length > 0 || tienda.length > 0 || fec_emi.length > 0 || clasifica1.length > 0 || clasifica2.length > 0){
            cadena = '<a href="<?= base_url() ?>gastus/index/' + desde + '/' + hasta + '/' + tienda + '/' + fec_emi + '/' + clasifica1 + '/' + clasifica2 + '" id="enlace_grilla_compras"></a>'
            document.getElementById('refresco').innerHTML = cadena
            setTimeout("document.getElementById('enlace_grilla_compras').click()",100)
        }

        if (typeof(Storage) !== "undefined") {
          localStorage.setItem("gastos_filtro_desde", desde)
          localStorage.setItem("gastos_filtro_hasta", hasta)
          localStorage.setItem("gastos_filtro_tienda", tienda)
          localStorage.setItem("gastos_filtro_fec_emi", fec_emi)
          localStorage.setItem("gastos_filtro_clasifica1", clasifica1)
          localStorage.setItem("gastos_filtro_clasifica2", clasifica2)
        }
    }

    function limpiar(){
        $("#desde").val("")
        $("#hasta").val("")
        //$("#tienda").val("0")
        $("#proveedor").val("0")
        $("#fec_emi").val("")
        activo1()
    }

    function activar_consulta_total(){
        let desde       = document.getElementById("desde").value
        let hasta       = document.getElementById("hasta").value
        if(desde == ""){ desde = "null" }
        if(hasta == ""){ hasta = "null" }
        let tienda      = document.getElementById("tienda").value
        //let proveedor   = document.getElementById("proveedor").value
        let fec_emi     = document.getElementById("fec_emi").value
        if(fec_emi == ""){ fec_emi = "null" }
        
        let parametros = {
            desde : desde,
            hasta : hasta,
            tienda : tienda,
            //proveedor : proveedor,
            fec_emi : fec_emi
        }
        $.ajax({
            data: parametros,
            url : "<?= base_url("gastus/totalizados") ?>",
            type: "get",
            success: function(response){
                //alert("llegamos hasta aqui totalizados:" + response)
                //document.getElementById("txt_total").style.display = "block"
                //document.getElementById("txt_total").innerHTML = response
            }
        })
    }        
    
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

    function cambiando_casillas_gastos(){
        // actualizando las casillas con localStorage      
        if (typeof(Storage) !== "undefined"){
            if(localStorage.getItem("gastos_filtro_desde") != "null"){
                $("#desde").val(localStorage.getItem("gastos_filtro_desde"))
            }

            if(localStorage.getItem("gastos_filtro_hasta") != "null"){
                $("#hasta").val(localStorage.getItem("gastos_filtro_hasta"))
            }

            if(localStorage.getItem("gastos_filtro_tienda") != "null"){    
                $("#tienda").val(localStorage.getItem("gastos_filtro_tienda"))
            }

            if(localStorage.getItem("gastos_filtro_fec_emi") != "null"){
                $("#fec_emi").val(localStorage.getItem("gastos_filtro_fec_emi"))
            }

            if(localStorage.getItem("clasifica1") != "null"){
                $("#clasifica1").val(localStorage.getItem("gastos_filtro_clasifica1"))
            }

            if(localStorage.getItem("clasifica2") != "null"){
                $("#clasifica2").val(localStorage.getItem("gastos_filtro_clasifica2"))
            }
        }
    }
</script>
