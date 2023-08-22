<!-- Versión compilada y comprimida del CSS de Bootstrap -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">

<!-- Tema opcional -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap-theme.min.css">

<!-- Versión compilada y comprimida del JavaScript de Bootstrap -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
if(!isset($desde)){     $desde = "";    }
if(!isset($hasta)){     $hasta = "";    }
if(!isset($tienda)){    $tienda = "0";  }
if(!isset($proveedor)){ $proveedor = "0";}
if(!isset($fec_emi)){   $fec_emi = "";  }
if(!isset($estado)){    $estado = "";}
if(!isset($tipo_egreso)){    $tipo_egreso = "";}
$agrupado = "";
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

    var opcion_almacenaje = true

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
                url: '<?=site_url('purchases/get_purchases');?>', 
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                    
                    if(opcion_almacenaje) {
                        d.desde = localStorage.getItem("compras_filtro_desde");
                        console.log("Creo Yo:"+d.desde);
                        //d.desde = '2022-07-01'
                    }else{
                        d.desde = document.getElementById('desde').value;
                        console.log("Caray");
                    }

                    d.hasta     = (opcion_almacenaje) ? localStorage.getItem("compras_filtro_hasta") : document.getElementById('hasta').value; 
                    d.tienda    = (opcion_almacenaje) ? localStorage.getItem("compras_filtro_tienda") : document.getElementById('tienda').value;
                    d.proveedor = (opcion_almacenaje) ? localStorage.getItem("compras_filtro_proveedor") : document.getElementById('proveedor').value;
                    d.fec_emi   = (opcion_almacenaje) ? localStorage.getItem("compras_filtro_fec_emi") : document.getElementById('fec_emi').value;
                    d.estado    = (opcion_almacenaje) ? localStorage.getItem("compras_filtro_estado") : document.getElementById('estado').value;
                    d.tipo_egreso = (opcion_almacenaje) ? localStorage.getItem("compras_filtro_tipo_egreso") : document.getElementById('tipo_egreso').value;
                    d.agrupado  = (opcion_almacenaje) ? localStorage.getItem("agrupado") : document.getElementById('tipo_egreso').value;
                }
            },
            "buttons": [
                // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] } },
                { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
                exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] } },
                { extend: 'colvis', text: 'Filtro'},
            ],
            "columns": [
                { "data": "id", "visible": true },
                { "data": "state" },
                { "data": "date"}, /* , "render": hrld */
                { "data": "tipoDoc","className": "text-center"},
                { "data": "nroDoc"},
                { "data": "name"},
                { "data": "fec_emi_doc" },
                { "data": "fec_venc_doc" },
                { "data": "d_a_v" },
                /*{ "data": "cargo_servicio", "render": currencyFormat},
                { "data": "total", "render": currencyFormat },*/
                { "data": "total_", "render": currencyFormat },
                { "data": "costo_tienda"},
                { "data": "costo_banco"},
                { "data": "estado"},
                //{ "data": "attachment", "render": attach, "searchable": false, "orderable": false },
                { "data": "Actions", "searchable": false, "orderable": false }
            ],
            "footerCallback": function (tfoot, data, start, end, display){
                var api = this.api(), data;
                $(api.column(9).footer()).html( cf(api.column(9).data().reduce( function (a, b) { 
                    paz = pf(a) + pf(b)
                    return paz;
                }, 0)) );

                $(api.column(10).footer()).html( cf(api.column(10).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                $(api.column(11).footer()).html( cf(api.column(11).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
            },
            "scrollX": true,
            "rowCallback": function(Row, Data){
                //if(Data['colores']=='B'){
                //if ( Data[2] == "Excelente" ){
                    $('td', Row).css('background-color', Data['colores']);
                //    console.log(Data["colores"])
                //}
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
            //console.log("Hola amigos")
        });

    
        setTimeout("cambiando_casillas()",700);
    });

    function cambiando_casillas(){
        // actualizando las casillas con localStorage      
        if (typeof(Storage) !== "undefined"){
            if(localStorage.getItem("compras_filtro_desde") != "null"){
                $("#desde").val(localStorage.getItem("compras_filtro_desde"))
            }

            if(localStorage.getItem("compras_filtro_hasta") != "null"){
                $("#hasta").val(localStorage.getItem("compras_filtro_hasta"))
            }

            if(localStorage.getItem("compras_filtro_tienda") != "null"){    
                $("#tienda").val(localStorage.getItem("compras_filtro_tienda"))
                //console.log("En cambiando casillas")
            }

            if(localStorage.getItem("compras_filtro_proveedor") != "null"){
                $("#proveedor").val(localStorage.getItem("compras_filtro_proveedor"))
            }

            if(localStorage.getItem("compras_filtro_fec_emi") != "null"){
                $("#fec_emi").val(localStorage.getItem("compras_filtro_fec_emi"))
            }

            if(localStorage.getItem("compras_filtro_estado") != "null"){
                $("#estado").val(localStorage.getItem("compras_filtro_estado"))
            }

            if(localStorage.getItem("compras_filtro_tipo_egreso") != "null"){
                $("#tipo_egreso").val(localStorage.getItem("compras_filtro_tipo_egreso"))
            }

            if(localStorage.getItem("agrupado") != "null" && localStorage.getItem("agrupado") != ''){
                $("#agrupado").val(localStorage.getItem("agrupado"))
                document.getElementById("agrupado").style.backgroundColor = "yellow"
            }
        }
    }
</script>

<style type="text/css">
    .table td:nth-child(3) { text-align: center;}
    .table td:nth-child(4) { text-align: left;}
    .table td:nth-child(7) { text-align: center;}
    .table td:nth-child(8) { text-align: center;}
    .table td:nth-child(9) { text-align: center;}
    .table td:nth-child(10) { text-align: right; padding-right: 10px}
    .table td:nth-child(11) { text-align: right; padding-right: 10px}
    .table td:nth-child(12) { text-align: right; padding-right: 10px}
</style>


<section class="content">
    
    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="margin: 0px 0px 0px 5px;">
        <div class="col-xs-4 col-sm-4 col-md-2" style="border-style:none; border-color:red; padding-left:0px;">
            <div class="form-group">
                <label for="">Desde:</label>
                <input type="date" name="desde" id="desde" value="<?= $desde ?>" class="form-control">
            </div>    
        </div>

        <div class="col-xs-4 col-sm-4 col-md-2" style="border-style:none; border-color:red; padding-left:0px;">
            <div class="form-group">
                <label for="">Hasta:</label>
                <input type="date" name="hasta" id="hasta" value="<?= $hasta ?>" class="form-control">
            </div>
        </div>
        
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2" style="border-style:none; border-color:red; padding-left:0px;">
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    $q = $this->db->get('stores');

                    if ($group_id == '1' || $group_id == '3'){
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

        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Proveedor:</label>
                <?php
                    //$q = $this->db->order_by('name')->get('suppliers');
                    $cSql   = "select a.* from tec_suppliers a inner join (select supplier_id from tec_purchases where date >= '2023-04-01' group by supplier_id) b on a.id = b.supplier_id";
                    $q      = $this->db->query($cSql); 
                    $ar = array();
                    $ar[] = "Todas";
                    foreach($q->result() as $r){
                        $ar[$r->id] = $r->name;
                    }
                    echo form_dropdown('proveedor', $ar, $proveedor, 'class="form-control tip" id="proveedor" required="required"');
                ?>
            </div>
        </div>

        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Fecha de Emisión:</label>
                <input type="date" name="fec_emi" id="fec_emi" value="<?= $fec_emi ?>" class="form-control">
            </div>
        </div>
    </div>
    <div class="row" style="margin: 0px 0px 10px 5px;">
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2" style="padding:0px 10px 0px 0px;">
            <div class="form-group">
                <label for="">Estado:</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="">Todo</option>
                    <option value="Pendiente" <?= ($estado == "Pendiente" ? "selected" : "") ?>>Pendiente</option>
                    <option value="Pagado" <?= ($estado == "Pagado" ? "selected" : "") ?>>Pagado</option>
                </select>
            </div>
        </div>

        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2" style="padding:0px 10px 0px 0px;">
            <div class="form-group">
                <label for="">Tipo Egreso:</label>
                <select name="tipo_egreso" id="tipo_egreso" class="form-control">
                    <option value="">Todo</option>
                    <option value="producto" <?= ($tipo_egreso == "producto" ? "selected" : "") ?>>Producto</option>
                    <option value="servicio" <?= ($tipo_egreso == "servicio" ? "selected" : "") ?>>Servicio</option>
                </select>
            </div>
        </div>

        <style type="text/css">
            .resalta{ background-color: yellow !important; }
        </style>
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2" style="padding:0px 10px 0px 0px;">
            <div class="form-group">
                <label for="">Agrupar por:</label>
                <select name="agrupado" id="agrupado" class="form-control">
                    <option value="">-- Seleccione --</option>
                    <option value="proveedor" <?= ($agrupado == "proveedor" ? "selected" : "") ?>>Proveedor</option>
                </select>
            </div>
        </div>
        
        <div id="preparo" class="col-xs-5 col-sm-3 col-md-2 col-lg-1" style="border-style:none; border-color:red; margin: 20px 0px 20px 0px;">
            <div class="row">
                <div class="col-xs-3 col-sm-4 col-lg-5" style="padding:5px 0px 0px 0px;">
                    <button onclick="activo1()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/purchases/search.png") ?>" height="30px"></button>
                </div>
                <div class="col-xs-3 col-sm-4 col-lg-5" style="padding:5px 0px 0px 0px;">
                    <button onclick="limpiar()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/purchases/eliminar.png") ?>" height="30px"></button>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="hidden-sm-3 hidden-md col-lg-3">
        </div>
        <div class="col-sm-10 col-md-10 col-lg-8">
            <b><span id="txt_total" style="font-size:16px; font-weight:bold;"></span></b>
        </div>        
        <div class="hidden-sm-3 hidden-md col-lg-3">
        </div>        
    </div>

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
                                    <th class="col-xs-2 col-sm-1"><?= lang('date'); ?></th>
                                    <th class=""><?= lang('Tipo'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Nro.Doc'); ?></th>
                                    <th class="col-xs-2 col-sm-2"><?= lang('Proveedor'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Fec_Emision'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Fec_Vcmto'); ?></th>
                                    <th class=""><?= lang('Dias'); ?></th>
                                    <!--<th class="col-xs-1 col-sm-1"><?= lang('Cargo'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('total'); ?></th>-->
                                    <th class="col-xs-1 col-sm-1"><?= lang('total.'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Caja Tda'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Caja Bco'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Estado'); ?></th>
                                    <th style="width:75px;"><?= lang('actions'); ?></th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="14" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th style="max-width:30px;"><input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Tda'); ?>]"></th>
                                    <th class="col-xs-2 col-sm-1">
                                    </th>
                                    <th class="col-xs-1 col-sm-1">
                                    </th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Nro'); ?>]"></th>
                                    <th class="col-sm-2"><input type="text" class="text_filter" placeholder="[<?= lang('proveedor'); ?>]"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Dias'); ?>]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="total"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
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
    });

    function activo1(){
        let desde       = document.getElementById("desde").value
        let hasta       = document.getElementById("hasta").value
        if(desde == ""){ desde = "null" }
        if(hasta == ""){ hasta = "null" }
        let tienda      = document.getElementById("tienda").value
        let proveedor   = document.getElementById("proveedor").value
        let fec_emi     = document.getElementById("fec_emi").value
        if(fec_emi == ""){ fec_emi = "null" }
        let estado      = document.getElementById("estado").value
        let cadena      = ""
        let tipo_egreso = document.getElementById("tipo_egreso").value
        let agrupado    = document.getElementById("agrupado").value

        if (typeof(Storage) !== "undefined"){
            
            localStorage.setItem("compras_filtro_desde", desde)
            localStorage.setItem("compras_filtro_hasta", hasta)
            localStorage.setItem("compras_filtro_tienda", tienda )
            localStorage.setItem("compras_filtro_proveedor", proveedor)
            localStorage.setItem("compras_filtro_fec_emi", fec_emi)
            localStorage.setItem("compras_filtro_estado", estado)
            localStorage.setItem("compras_filtro_tipo_egreso", tipo_egreso)
            localStorage.setItem("agrupado", agrupado)
            
        }  

        if(desde.length > 0 || hasta.length > 0 || tienda.length > 0 || proveedor.length > 0 || fec_emi.length > 0 || estado.length > 0){
            cadena = '<a href="<?= base_url() ?>purchases/index/' + desde + '/' + hasta + '/' + tienda + '/' + proveedor + '/' + fec_emi + '/' + estado + '" id="enlace_grilla_compras"></a>'
            //console.log(cadena)
            document.getElementById('refresco').innerHTML = cadena
            //document.getElementById('preparo').style.display = "none"
            setTimeout("document.getElementById('enlace_grilla_compras').click()",100)
        }

    }

    function limpiar(){
        $("#desde").val("")
        $("#hasta").val("")
        //$("#tienda").val("0")
        $("#proveedor").val("0")
        $("#fec_emi").val("")
        $("#agrupado").val("")
        activo1()
    }

    function activar_consulta_total(){
        let desde       = document.getElementById("desde").value
        let hasta       = document.getElementById("hasta").value
        if(desde == ""){ desde = "null" }
        if(hasta == ""){ hasta = "null" }
        let tienda      = document.getElementById("tienda").value
        let proveedor   = document.getElementById("proveedor").value
        let fec_emi     = document.getElementById("fec_emi").value
        if(fec_emi == ""){ fec_emi = "null" }
        
        let parametros = {
            desde : desde,
            hasta : hasta,
            tienda : tienda,
            proveedor : proveedor,
            fec_emi : fec_emi
        }
        $.ajax({
            data: parametros,
            url : "<?= base_url("purchases/totalizados") ?>",
            type: "get",
            success: function(response){
                //alert("llegamos hasta aqui totalizados:" + response)
                //document.getElementById("txt_total").style.display = "block"
                document.getElementById("txt_total").innerHTML = response
            }
        })
    }        
    
    activar_consulta_total()
</script>

