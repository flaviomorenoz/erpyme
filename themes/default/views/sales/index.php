<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
if(!isset($desde)){ $desde = ""; }
if(!isset($hasta)){ $hasta = ""; }
if(!isset($tienda)){ $tienda = "";}
if(!isset($metodo)){ $metodo = "";}
?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(5)',500);\n";
    ?>
    
    function reenvio_a_sunat(sale_id1){
        $.ajax({
            data : {sale_id : sale_id1},
            url  : '<?= base_url("sales/envio_individual") ?>',
            type : 'get',
            success: function(response){
                if(response == "OK"){
                    alert("Se envía satisfactoriamente a Sunat.")
                }else{
                    alert("Hubo problemas en el envío.")
                }
            },
            beforeSend: function(){
                console.log("Por favor espere....")
            }
        })
    }

    $(document).ready(function(){

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
            'ajax' : { url: '<?=site_url('sales/get_sales');?>', type: 'POST', "data": function ( d ) {
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                d.desde = '<?= $desde ?>'; //
                d.hasta = '<?= $hasta ?>'; //
                d.tienda = '<?= $tienda ?>';
                d.metodo = '<?= $metodo ?>';
            }},
            "buttons": [
            // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ] } },
            { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] } },
            { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ] } },
            { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
            exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] } },
            { extend: 'colvis', text: 'Filtro'},
            ],
            "columns": [
            { "data": "id", "visible": true },
            { "data": "state"},
            { "data": "date"},
            { "data": "customer_name" },
            { "data": "tipoDoc"},
            { "data": "recibo"}, /* total_discount */
            //{ "data": "total", "render": currencyFormat },
            //{ "data": "total_tax", "render": currencyFormat },
            { "data": "paid_by"},
            { "data": "grand_total", "render": currencyFormat },
            { "data": "amount", "render": currencyFormat },
            { "data": "status", "render": status },
            { "data": "dir_comprobante"},
            { "data": "Actions",  }
            ],
            
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.id = aData.id;
                return nRow;
            },
            "footerCallback": function (  tfoot, data, start, end, display ) {
                var api = this.api(), data;
                //$(api.column(4).footer()).html( cf(api.column(4).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                //$(api.column(6).footer()).html( cf(api.column(5).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                //$(api.column(7).footer()).html( cf(api.column(6).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                $(api.column(7).footer()).html( cf(api.column(7).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                $(api.column(8).footer()).html( cf(api.column(8).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
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
            $( 'input.datepicker', this.footer() ).on('dp.change', function (e){
                self.search( this.value ).draw();
            });
            $( 'input:not(.datepicker)', this.footer() ).on('keyup change', function (e){
                var code = (e.keyCode ? e.keyCode : e.which);
                if (((code == 13 && self.search() !== this.value) || (self.search() !== '' && this.value === ''))){
                    self.search( this.value ).draw();
                }
            });
            $( 'select', this.footer() ).on( 'change', function (e){
                self.search( this.value ).draw();
            });
        });

    });
</script>

<style type="text/css">
    .table td:nth-child(1) { text-align: left;}
</style>

<section class="content">

    <div class="row" style="display:flex;margin-bottom: 5px;">
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
        
        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    $q = $this->db->get('stores');

                    $ar = array();
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
        
        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Metodo de Pago</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    
                    $q = $this->db->query("select * from tec_forma_pagos where activo='1'");
                    
                    $ar = array();
                    $ar[] = "Todas";
                    foreach($q->result() as $r){
                        $ar[$r->forma_pago] = $r->descrip;
                    }
                    echo form_dropdown('metodo', $ar, $metodo, 'class="form-control tip" id="metodo" required="required"');
                ?>
            </div>
        </div>
        
        
        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; padding:5px;">
            <span style="margin-bottom:5px"></span><br>
            <a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        
        <div id="refresco" class="col-sm-1"></div>
        <div class="col-sm-3">
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="SLData" class="table table-striped table-bordered table-condensed table-hover" data-page-length='50'>
                            <thead>
                                <tr>
                                    <td colspan="10" class="p0" style="border-style: solid; border-color: black; border-width: 1px;">
                                        <input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;">
                                    </td>
                                </tr>

                                <tr class="active">
                                    <th style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th class="col-xs-1 col-sm-1 text-left" style="text-align:left"><?= lang("Tda"); ?></th>
                                    <th class="col-xs-2 col-sm-2"><?= lang("date"); ?></th>
                                    <th class="col-sm-2"><?= lang("customer"); ?></th>
                                    <th class="col-xs-1 col-sm-1">Tipo Doc</th>
                                    <th class="col-xs-1 col-sm-2" style="min-width:60px;">Nro</th>
                                    <!--<th class="col-xs-1 col-sm-1"><?= lang("total"); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang("tax"); ?></th>-->
                                    <th class="col-xs-1 col-sm-1">Metodo</th>
                                    <th class="col-xs-1 col-sm-1"><?= lang("grand_total"); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang("paid"); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang("status"); ?></th>
                                    <th class="col-xs-1 col-sm-1">Env&iacute;o Sunat</th>
                                    <th style="min-width:115px; max-width:115px; text-align:center;"><?= lang("actions"); ?></th>
                                </tr>

                                <tr>
                                    <th style="max-width:30px;"><input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]"></th>
                                    <th class="col-sm-1">
                                        <input type="text" class="text_filter" placeholder="[<?= lang('Tda'); ?>]">
                                    </th>
                                    <th class="col-sm-2">
                                        <span class="datepickercon">
                                            <input type="text" class="text_filter datepicker" placeholder="[<?= lang('date'); ?>]">
                                        </span>
                                    </th>
                                    <th class="col-sm-2">
                                        <input type="text" class="text_filter" placeholder="[<?= lang('customer'); ?>]">
                                    </th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-2"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1">
                                        <select class="select2 select_filter">
                                            <option value=""><?= lang("all"); ?></option>
                                            <option value="paid"><?= lang("paid"); ?></option>
                                            <option value="partial"><?= lang("partial"); ?></option>
                                            <option value="due"><?= lang("due"); ?></option>
                                        </select>
                                    </th>
                                    <th class="col-sm-1"></th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                   <td colspan="12" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                               </tr>
                           </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th><input type="text"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><input type="text"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            
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
        var desde = document.getElementById("desde").value
        var hasta = document.getElementById("hasta").value
        var tienda = document.getElementById("tienda").value
        var metodo = document.getElementById("metodo").value
        
        if(desde.length > 0){}else{desde = "null";}
        if(hasta.length > 0){}else{hasta = "null";}
        
        document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>sales/index/' + desde + '/' + hasta + '/' + tienda + '/' + metodo + '" id="enlace_grilla_compras">Ejecutar</a>'
        document.getElementById('preparo').style.display = "none"
        document.getElementById('enlace_grilla_compras').click()
    }

    /*function anular_doc(id){
        console('en anular_doc')
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
    }*/

    function anular_doc(id){
        var respeta = confirm("Confirma que desea Elimanar?")

        if(respeta){
            $.ajax({
                data:{id:id},
                url:'<?= base_url('pos/anular_doc') ?>',
                type:'get',
                success:function(res){
                    var respuesta = JSON.parse(res)
                    if(respuesta.rpta == 'OK'){
                        alert(respuesta.mensaje)    
                    }else{
                        alert("Hubo un inconveniente...no se pudo borrar")
                    }
                    window.location.reload()
                },
                error: function(xhr, status, error){
                    // Ocurrió un error durante la solicitud AJAX
                    console.log('Error: ' + error);
                }
            })
        }
    }

</script>

