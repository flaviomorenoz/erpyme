<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
if(!isset($tienda)){
    $tienda = "";
}
?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        //echo "setTimeout('abrir_item_menu(5)',500);\n";
    ?>
    $(document).ready(function() {

        function status(x) {
            var paid = '<?= lang('paid'); ?>';
            var partial = '<?= lang('partial'); ?>';
            var due = '<?= lang('due'); ?>';
            if (x == 'paid') {
                return '<div class="text-center"><span class="sale_status label label-success">'+paid+'</span></div>';
            } else if (x == 'partial') {
                return '<div class="text-center"><span class="sale_status label label-primary">'+partial+'</span></div>';
            } else if (x == 'due') {
                return '<div class="text-center"><span class="sale_status label label-danger">'+due+'</span></div>';
            } else {
                return '<div class="text-center"><span class="sale_status label label-default">'+x+'</span></div>';
            }
        }

        var table = $('#SLData').DataTable({
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
                url: '<?=site_url('reports/get_ventas_por_dia');?>',
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                    d.tienda = document.getElementById("tienda").value;
                }
            },
            "buttons": [
                // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1 ] } },
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5] } },
                { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5] } 
                },
                { extend: 'colvis', text: '<i class="glyphicon glyphicon-cog"></i>'},
            ],
            "columns": [
                /*{ "data": "id", "visible": false },*/
                { "data": "fecha"},
                { "data": "dia_semana"},
                { "data": "total"},
                { "data": "igv"},
                { "data": "descuento"},
                { "data": "grand_total"},
                { "data": "barras"}
            ],
            
            /* fecha total igv descuento grand_total barras */

            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.id = aData.id;
                return nRow;
            },
            "footerCallback": function (  tfoot, data, start, end, display ) {
                var api = this.api(), data;
            },
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
</script>

<style type="text/css">
    .table td:nth-child(3) { text-align: right;}
    .table td:nth-child(4) { text-align: right;}
    .table td:nth-child(5) { text-align: right;}
    .table td:nth-child(6) { text-align: right;}
</style>

<section class="content">

    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="display:flex;margin-bottom: 5px;">
        <!--<div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Desde:</label>
                <input type="date" name="desde" id="desde" value="<?= $desde ?>">
            </div>    
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Hasta:</label>
                <input type="date" name="hasta" id="hasta" value="<?= $hasta ?>">
            </div>
        </div>-->
        
        <div class="col-sm-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $q = $this->db->get('stores');
                    $ar[] = "Todas";
                    foreach($q->result() as $r){
                        $ar[$r->id] = $r->state;
                    }
                    echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required"');
                ?>
            </div>
        </div>

        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: 25px 0px 20px 0px;">
            <button onclick="activo1()" class="btn btn-primary">Consultar</button>
        </div>

    </div>

    <script type="text/javascript">
        function activo1(){
            /*let desde       = document.getElementById("desde").value
            let hasta       = document.getElementById("hasta").value
            if(desde == ""){ desde = "null" }
            if(hasta == ""){ hasta = "null" }
            let proveedor   = document.getElementById("proveedor").value
            let fec_emi     = document.getElementById("fec_emi").value
            if(fec_emi == ""){ fec_emi = "null" }
            let cadena      = ""*/
            let tienda      = document.getElementById("tienda").value
            window.location.assign("<?= base_url() ?>reports/ventas_por_dia?tienda="+$("#tienda").val())
        }
    </script>

    <div class="row">
        <div class="col-xs-12 col-sm-8">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                                <!-- BUSQUEDA GRAL -->
                                <tr>
                                    <td colspan="2" class="p0" style="border-style: solid; border-color: black; border-width: 1px;">
                                        <input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;">
                                    </td>
                                </tr>

                                <!-- TITULOS -->
                                <tr class="active">
                                    <th class="col-xs-2 col-sm-2 text-left" style="">Fecha</th>
                                    <th class="col-xs-2 col-sm-2 text-left" style="">Dia</th>
                                    <th class="col-xs-2 col-sm-1 text-left" style="text-align:left">Subtotal</th>
                                    <th class="col-xs-2 col-sm-1">Igv</th>
                                    <th class="col-xs-2 col-sm-1">Descuento</th>
                                    <th class="col-xs-2 col-sm-1">Total</th>
                                    <th class="col-xs-2 col-sm-4">Barras</th>
                                </tr>


                            </thead>
                            <tbody>
                                <tr>
                                   <td colspan="7" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                               </tr>
                           </tbody>
                           <tfoot>
                                <!-- ORDENES COLUMNAS Y BUSQ. INDIVIDUALES -->
                                <tr>
                                    <th class="col-sm-2" style="">
                                        <input type="text" class="text_filter" placeholder="[fecha]">
                                    </th>
                                    <th class="col-sm-2">
                                        <input type="text" class="text_filter" placeholder="[dia]">
                                    </th>                                    
                                    <th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[Total sin Igv]">
                                    </th>
                                    <th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[Igv]">
                                    </th>
                                    <th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[Descuento]">
                                    </th>
                                    <th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[Total]">
                                    </th>
                                    <th class="col-sm-4">
                                        <input type="text" class="text_filter" placeholder="[.]">
                                    </th>
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
