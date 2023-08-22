<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 

if(!isset($desde)){         $desde = "";    }
if(!isset($hasta)){         $hasta = "";    }
if(!isset($tienda)){        $tienda = "0";  }
if(!isset($product_id)){    
    $product_id = "";  
}else{
    //echo($product_id);
}
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
        echo "setTimeout('abrir_item_menu(6)',500);\n";
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
                url: '<?=site_url('reports/get_analisis_compras');?>', 
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                    d.desde = '<?= $desde ?>'; 
                    d.hasta = '<?= $hasta ?>';
                    d.tienda = '<?= $tienda ?>';
                    d.product_id = '<?= $product_id ?>';
                }
            },
            "buttons": [
                // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2] } },
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2] } },
                { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
                exportOptions: { columns: [ 0, 1, 2] } },
                { extend: 'colvis', text: 'Filtro'},
            ],
            "columns": [
                { "data": "name" },
                { "data": "quantity", "render": function( data, type, row, meta ){
                        let datin = data * 1
                        return datin.toFixed(0)
                    }
                }, /* , "render": hrld */
                { "data": "subtotal", "render": function ( data, type, row, meta ){ 
                    let datin = data * 1;
                    return datin.toFixed(2); } 
                }
            ],
            "footerCallback": function (tfoot, data, start, end, display){
                var api = this.api(), data;
                //$(api.column(1).footer()).html( cf(api.column(1).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                $(api.column(2).footer()).html( cf(api.column(2).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
            },
            "scrollX": true,
            "rowCallback": function(Row, Data){
                //$('td', Row).css('background-color', Data['colores']);
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
    .table td:nth-child(1) { text-align: left; padding-right: 10px}
    .table td:nth-child(2) { text-align: right; padding-right: 10px}
    .table td:nth-child(3) { text-align: right; padding-right: 10px}
</style>

<section class="content">
    
    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="display:flex;margin-bottom: 5px;">
        <div class="col-sm-2" style="border-style:none; border-color:red">
            <div class="form-group">
                <label for="">F. Emision - Desde:</label>
                <input type="date" name="desde" id="desde" value="<?= $desde ?>" class="form-control" style="font-size:16px">
            </div>    
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">F. Emision - Hasta:</label>
                <input type="date" name="hasta" id="hasta" value="<?= $hasta ?>" class="form-control" style="font-size:16px">
            </div>
        </div>
        
        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    $q = $this->db->get('stores');

                    if ($group_id == '1'){
                        $ar = array();
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
                    echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required" style="font-size:16px"');
                ?>
            </div>
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Producto:</label>
                <?php
                    
                    $this->db->where("category_id",7);
                    $q = $this->db->get('products');
                    $ar_p = array();
                    $ar_p[] = "Todas";
                    foreach($q->result() as $r){
                        $ar_p[ $r->id ] = $r->name;
                    }
                    echo form_dropdown('product_id', $ar_p, $product_id, 'class="form-control tip" id="product_id" required="required" style="font-size:16px"');
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

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        
                        <table id="purData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;" data-page-length='25'>
                            <thead>
                                <tr class="active">
                                    <th class="col-xs-2 col-sm-2">Producto</th>
                                    <th class="col-xs-1 col-sm-1">Cantidad</th>
                                    <th class="col-xs-2 col-sm-1">Monto</th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th class="col-xs-2 col-sm-2"></th>
                                    <th class="col-xs-1 col-sm-1"></th>
                                    <th class="col-xs-2 col-sm-1"></th>
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
    });

    function activo1(){
        let desde       = document.getElementById("desde").value
        let hasta       = document.getElementById("hasta").value
        let tienda      = document.getElementById("tienda").value
        let product_id  = document.getElementById("product_id").value
        
        if(desde == ""){ desde = "null" }
        if(hasta == ""){ hasta = "null" }
        if(product_id == ""){ product_id = "null"}
        
        let cadena      = ""
        
        if(desde.length > 0 || tienda.length > 0 || proveedor.length > 0 || product_id.length){
            cadena = '<a href="<?= base_url() ?>reports/analisis_compras/' + desde + '/' + hasta + '/' + tienda + '/' + product_id  + '" id="enlace_grilla_compras"></a>'
            //alert(cadena)
            document.getElementById('refresco').innerHTML = cadena
            setTimeout("document.getElementById('enlace_grilla_compras').click()",100)
        }

        /*
        if (typeof(Storage) !== "undefined") {
          localStorage.setItem("gastos_filtro_desde", desde)
          localStorage.setItem("gastos_filtro_hasta", hasta)
          localStorage.setItem("gastos_filtro_tienda", tienda)
          localStorage.setItem("gastos_filtro_fec_emi", fec_emi)
          localStorage.setItem("gastos_filtro_clasifica1", clasifica1)
          localStorage.setItem("gastos_filtro_clasifica2", clasifica2)
        }*/
    }

    function limpiar(){
        $("#desde").val("")
        $("#hasta").val("")
        $("#tienda").val("0")
        $("#product_id").val("")
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

</script>