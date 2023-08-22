<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        //echo "setTimeout('abrir_item_menu(5)',500);\n";
    ?>
    $(document).ready(function() {

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
                url: '<?=site_url('inventarios/get_movimientos');?>',
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
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
                { "data": "id"},
                { "data": "fechah"}/*,
                { "data": "persona"},
                { "data": "product_id"},
                { "data": "cantidad"},
                { "data": "store_id"}
                { "data": "Actions"}*/
            ],
            
            /*"fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.id = aData.id;
                return nRow;
            },
            "footerCallback": function (  tfoot, data, start, end, display ) {
                var api = this.api(), data;
            }*/

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
    /*.table td:nth-child(1) { text-align: left;}*/
</style>

<section class="content">

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
                                    <th style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th class="col-xs-2 col-sm-3">Fecha</th>
                                    <!--<th class="col-xs-2 col-sm-1">Persona</th>
                                    <th class="col-xs-2 col-sm-1">Producto</th>
                                    <th class="col-xs-2 col-sm-1">Cantidad</th>
                                    <th class="col-xs-2 col-sm-1">Tienda</th>
                                    <th class="col-xs-2 col-sm-1"><?= lang("actions"); ?></th>-->
                                </tr>

                                <!-- ORDENES COLUMNAS Y BUSQ. INDIVIDUALES -->
                                <tr>
                                    <th style="max-width:30px;">
                                        <input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]">
                                    </th>
                                    <th class="col-sm-3">
                                        <input type="text" class="text_filter" placeholder="[<?= lang('Insumo'); ?>]">
                                    </th>
                                    <!--<th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[<?= lang('Unidad'); ?>]">
                                    </th>
                                    <th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[<?= lang('Cantidad'); ?>]">
                                    </th>
                                    <th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[<?= lang('Sum_qc'); ?>]">
                                    </th>
                                    <th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[<?= lang('Stock'); ?>]">
                                    </th>

                                    <th class="col-sm-1"></th>-->
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                   <td colspan="2" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                               </tr>
                           </tbody>
                           <tfoot>
                            <!-- <tr>
                                <td colspan="10" class="p0"><input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;"></td>
                            </tr> -->
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
<?php } ?>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    function eliminar_insumo(id){
        var parametros = {
            id : id,
        }
        console.log("inicia Eliminar producto")
        $.ajax({
            data    : parametros,
            url     :'<?= base_url('insumos/eliminar_insumo') ?>',
            type    :'get',
            success :function(response){
                let ar = JSON.parse(response)
                console.log(ar)
                alert(ar['rpta'])
                location.reload()
            }
        })

    }
</script>
