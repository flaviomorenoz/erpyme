<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
if(!isset($tienda)){ $tienda = "";}
if(!isset($desde)){ 
    $desde = date("Y-m-d"); 
}else{
    if($desde == "" || $desde == "null" || is_null($desde)){
        $desde = date("Y-m-d");
    }
}
if(!isset($hasta)){ 
    $hasta = date("Y-m-d"); 
}else{
    if($hasta == "" || $hasta == "null" || is_null($hasta)){
        $hasta = date("Y-m-d");
    }
}

if(!isset($producto)){ $producto = "";}
?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(5)',500);\n";
    ?>
    

    $(document).ready(function(){

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
            'ajax' : { url: '<?=site_url('sales/get_vtas_platos_hora');?>', type: 'POST', "data": function ( d ) {
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                d.tienda = '<?= $tienda ?>';
                d.desde = '<?= $desde ?>'; //
                d.hasta = '<?= $hasta ?>'; //
                d.producto = '<?= $producto ?>';
            }},
            "buttons": [
            { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,11,12,13] } },
            { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,11,12,13 ] } },
            { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
            exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,11,12,13] } },
            { extend: 'colvis', text: 'Filtro'},
            ],
            "columns": [
            { "data": "state" },
            { "data": "fecha"},
            { "data": "name"},
            { "data": "h7"},
            { "data": "h9"},
            { "data": "h11"},
            { "data": "h13"},
            { "data": "h15"},
            { "data": "h17"},
            { "data": "h19"},
            { "data": "h21"},
            { "data": "h23"},
            { "data": "cantidad"},
            { "data": "total"}
            ],
            
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.id = aData.id;
                return nRow;
            },
            "footerCallback": function (  tfoot, data, start, end, display ) {
                var api = this.api(), data;
                //$(api.column(3).footer()).html( cf(api.column(3).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                //$(api.column(4).footer()).html( cf(api.column(4).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                
            }

        });

        $('#search_table').on( 'keyup change', function (e){
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
    .table td:nth-child(4) { text-align: center;}
    .table td:nth-child(5) { text-align: center;}
    .table td:nth-child(6) { text-align: center;}
    .table td:nth-child(7) { text-align: center;}
    .table td:nth-child(8) { text-align: center;}
    .table td:nth-child(9) { text-align: center;}
    .table td:nth-child(10) { text-align: center;}
    .table td:nth-child(11) { text-align: center;}
    .table td:nth-child(12) { text-align: center;}
    .table td:nth-child(13) { text-align: center;}
</style>

<section class="content">

    <div class="row" style="display:flex;margin-bottom: 5px;">

        <div class="col-sm-2" style="border-style:none; border-color:red;">
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

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Desde:</label>
                <input type="date" name="desde" id="desde" value="<?= $desde ?>" class="form-control">
            </div>    
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Hasta:</label>
                <input type="date" name="hasta" id="hasta" value="<?= $hasta ?>" class="form-control">
            </div>
        </div>
        
        <div class="col-sm-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Producto:</label>
                <?php 
                   $cSql = "select id, code, name, price, unidad from tec_products where category_id != 7 order by name";
                   $result = $this->db->query($cSql)->result_array();
                   $ar_p[""] = "---Todos---";
                   foreach($result as $r){
                        $ar_p[ $r["id"] ] = $r["name"] . " (" . $r["unidad"] . ")";
                   }
                   echo form_dropdown('product_id', $ar_p, $producto, 'class="form-control tip" id="product_id"');
                ?>
            </div>
        </div>
        
        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; padding:5px;">
            <span style="margin-bottom:5px"></span><br>
            <a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        
        <div id="refresco" class="col-sm-1"></div>
        <div class="col-sm-5">
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="SLData" class="table table-striped table-bordered table-condensed table-hover" data-page-length='50'>
                            <thead>
                                <tr>
                                    <td colspan="14" class="p0" style="border-style: solid; border-color: black; border-width: 1px;">
                                        <input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;">
                                    </td>
                                </tr>

                                <tr class="active">
                                    <th style="max-width:60px; text-align:left"><?= lang("Tda"); ?></th>
                                    <th class="">Fecha</th>
                                    <th class="">Producto</th>
                                    <th class="">7</th>
                                    <th class="">9</th>
                                    <th class="">11</th>
                                    <th class="">13</th>
                                    <th class="">15</th>
                                    <th class="">17</th>
                                    <th class="">19</th>
                                    <th class="">21</th>
                                    <th class="">23</th>
                                    <th class="">Q</th>
                                    <th class="">Total</th>
                                </tr>
   
                            </thead>
                            <tbody>
                                <tr>
                                   <td colspan="14" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                               </tr>
                           </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
<?php if ($Admin) { ?>
    <div class="modal fade" id="stModal" tabindex="-1" role="dialog" aria-labelledby="stModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                    <h4 class="modal-title" id="stModalLabel"><?= lang('update_status'); ?> <span id="status-id"></span></h4>
                </div>
                <?= form_open('sales/status'); ?>
                <div class="modal-body">
                    <input type="hidden" value="" id="sale_id" name="sale_id" />
                    <div class="form-group">
                        <?= lang('status', 'status'); ?>
                        <?php $opts = array('paid' => lang('paid'), 'partial' => lang('partial'), 'due' => lang('due'))  ?>
                        <?= form_dropdown('status', $opts, set_value('status'), 'class="form-control select2 tip" id="status" required="required" style="width:100%;"'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?= lang('update'); ?></button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', '.sale_status', function() {
                var sale_id = $(this).closest('tr').attr('id');
                var curr_status = $(this).text();
                var status = curr_status.toLowerCase();
                $('#status-id').text('( <?= lang('sale_id'); ?> '+sale_id+' )');
                $('#sale_id').val(sale_id);
                $('#status').val(status);
                $('#status').select2('val', status);
                $('#stModal').modal()
            });
        });
    </script>
<?php } ?>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.datepicker').datetimepicker({format: 'YYYY-MM-DD', showClear: true, showClose: true, useCurrent: false, widgetPositioning: {horizontal: 'auto', vertical: 'bottom'}, widgetParent: $('.dataTable tfoot')});
    });

    function activo1(){
        var tienda = document.getElementById("tienda").value
        var desde = document.getElementById("desde").value
        var hasta = document.getElementById("hasta").value
        var producto = document.getElementById("product_id").value
        
        if(desde.length > 0){}else{desde = "null";}
        if(hasta.length > 0){}else{hasta = "null";}
        if(producto.length >0){}else{producto = "null";}
        
        var cRutin = '<a href="<?= base_url() ?>sales/vtas_platos_hora/' + tienda + '/' + desde + '/' + hasta + '/' + producto + '" id="enlace_grilla_compras">Ejecutar</a>'
        console.log(cRutin)
        document.getElementById('refresco').innerHTML = cRutin
        document.getElementById('preparo').style.display = "none"
        document.getElementById('enlace_grilla_compras').click()
    }

    function anular_doc(id){
        
        var respeta = confirm("Confirma que desea Elimanar?")

        if(respeta){
            $.ajax({
                data : {id : id},
                url  : '<?= base_url('pos/enviar_anulacion_nubefact') ?>',
                type : 'get',
                success : function(response){
                    alert(response)
                }
            })
        }
    }
</script>