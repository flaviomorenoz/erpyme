<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
    if(isset($rubro)){
        if(is_null($rubro)){
            $rubro = '';
        }
    }else{
        $rubro = '';
    }
    //die("Mi rubro:".$rubro);
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
            paging:false,
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
                url: '<?=site_url("insumos/get_insumos/$rubro");?>',
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                }
            },
            "buttons": [
                // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4] } },
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 4] } },
                { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 4] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 4] } }
                //{ extend: 'colvis', text: '<i class="glyphicon glyphicon-cog"></i>'},
            ],
            "columns": [
                //a.id, a.name, a.rubro, b.descrip, a.activo 
                { "data": "id", "visible": true },
                { "data": "name"},
                { "data": "unidad"},
                { "data": "rubro", "visible": false},
                { "data": "descrip"},
                //{ "data": "sum_qc"},
                //{ "data": "stock"},
                //{ "data": "inventariable"},
                { "data": "Actions"}
            ],
            
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.id = aData.id;
                return nRow;
            },
            "footerCallback": function (  tfoot, data, start, end, display ) {
                var api = this.api(), data;
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
</script>

<style type="text/css">
    /*.table td:nth-child(1) { text-align: left;}
    .table td:nth-child(6) { text-align: center;}*/
</style>

<section class="content">

<?= form_open('insumos/listar_insumos') ?>
    <div class="row" style="display:flex;margin-bottom: 5px;">
        
        <div class="col-sm-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Rubro</label>
                <?php
                    //$group_id = $this->session->userdata["group_id"];
                    $q = $this->db->query("select * from tec_rubros where productos='1' order by id");
                    
                    /*$ar = array();
                    $ar['0'] = "--Todas--";
                    foreach($q->result() as $r){
                        $ar[$r->id] = $r->descrip;
                    }
                    echo form_dropdown('rubro', $ar, $rubro, 'class="form-control tip" id="rubro" required="required"');*/

                    $result     = $this->db->query("select * from tec_rubros where productos='1' order by id")->result_array();
                    $ar         = $this->fm->conver_dropdown($result, "id", "descrip", array(''=>'--Seleccione--'));
                    echo form_dropdown('rubro',$ar,$rubro,'class="form-control tip" id="rubro" required="required" onclick=""');
                ?>
            </div>
        </div>
        
        <div id="preparo" class="col-sm-2" style="border-style:none; border-color:red; padding:5px;">
            <span style="margin-bottom:5px"></span><br>
            <!--<a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>&nbsp;&nbsp;&nbsp;&nbsp;-->
            <button type="submit" class="btn btn-primary">Consulta</button>
        </div>
<?= form_close() ?>

        <div id="refresco" class="col-sm-1"></div>
        <div class="col-sm-3">
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-11 col-lg-9">
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
                                    <th class="col-xs-2 col-sm-1" style="max-width:10px;"><?= lang("id"); ?></th>
                                    <th class="col-xs-2 col-sm-3 text-left" style="text-align:left"><?= lang("Insumo"); ?></th>
                                    <th class="col-xs-2 col-sm-1">Unidad</th>
                                    <th class="col-xs-2 col-sm-1">Cod. Rubro</th>
                                    <th class="col-xs-3 col-sm-2">Rubro</th>
                                    <th class="col-xs-2 col-sm-1">Actions</th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                   <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                               </tr>
                           </tbody>
                           <tfoot>
                            
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
            url     :'<?= base_url('insumos/eliminar') ?>',
            type    :'get',
            success :function(response){
                let ar = JSON.parse(response)
                console.log(ar)
                alert(ar['rpta'])
                location.reload()
            }
        })

    }

    function anular(id){
        var parametros = {
            id : id
        }
        console.log("inicia Eliminar producto")
        $.ajax({
            data    : parametros,
            url     :'<?= base_url('insumos/anular') ?>',
            type    :'get',
            success :function(res){
                alert('Se anula el insumo.')
                location.reload()
            }
        })
    }

    function activo1(){
        $.ajax({
            data    : {rubro : document.getElementById('rubro').value},
            url     : '<?= base_url("insumos/listar_insumos") ?>',
            type    : 'get',
            success : function(res){
                //alert("llega")
            }
        })
    }
</script>
