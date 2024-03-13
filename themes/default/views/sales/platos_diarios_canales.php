<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
    
    if(!isset($tienda)){    
        die("No existe tienda relacionada con el usuario (*)");
        //$tienda = '1';
    }else{
        if($tienda == '0' || $tienda == 'null' || is_null($tienda) ){
            $tienda = '1';
            //die("No existe tienda relacionada con el usuario (**)");
        }else{
            //$tienda = $_SESSION["store_id"];
        }        
    }
    if(!isset($anno)){
        $anno = date("Y"); 
    }else{
        if(is_null($anno) || $anno == 'null'){
            $anno = date("Y");
        }
    }
    if(!isset($mes)){       
        $mes = date("m"); 
    }else{
        if($mes == 'null' || is_null($mes)){
            $mes = date("m");
        }
    }
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
            'ajax' : {
                url: '<?=site_url('sales/get_platos_diarios_canales');?>/<?= $tienda ?>/<?= $anno ?>/<?= $mes ?>',
                type:'POST',
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                    d.tienda = '<?= $tienda ?>';
                    d.desde = '<?= $desde ?>'; //
                    d.hasta = '<?= $hasta ?>'; //
                }
            }, 
            "buttons": [
            { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124] } },
            { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124 ] } },
            { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
            exportOptions: { columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124] } },
            { extend: 'colvis', text: 'Filtro'},
            ]/*,
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
                
            }*/

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
                <!--Tienda: <?= $tienda ?><br>
                Año: <?= $anno ?><br>
                Mes: <?= $mes ?><br>-->
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
                <label for="">Año:</label>
                <input type="number" name="anno" id="anno" class="form-control" value="<?= $anno ?>">
            </div>    
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Mes:</label>
                <?php
                    $ar = array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Setiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
                    echo form_dropdown('mes',$ar,$mes,'class="form-control tip" id="mes"');
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
                                    <th class="">Id</th>
                                    <th style="max-width:60px; text-align:left">Producto</th>
                                    <th class="">Directo.Tda</th>
                                    <th class="">PedidosYa</th>
                                    <th class="">Rappi</th>

                                    <th class="">Didi</th>
                                    <th class="">Dia1_Directo</th>
                                    <th class="">Dia1_PedidosYa</th>
                                    <th class="">Dia1_Rappi</th>
                                    <th class="">Dia1_Didi</th>

                                    <th class="">Dia2_Directo</th><!-- 10 -->
                                    <th class="">Dia2_PedidosYa</th>
                                    <th class="">Dia2_Rappi</th>
                                    <th class="">Dia2_Didi</th>
                                    <th class="">Dia3_Directo</th>

                                    <th class="">Dia3_PedidosYa</th>
                                    <th class="">Dia3_Rappi</th>
                                    <th class="">Dia3_Didi</th>
                                    <th class="">Dia4_Directo</th>
                                    <th class="">Dia4_PedidosYa</th>

                                    <th class="">Dia4_Rappi</th><!-- 20 Dia_Directo Dia_PedidosYa Dia_Rappi Dia_Didi -->
                                    <th class="">Dia4_Didi</th>
                                    <th class="">Dia5_Directo</th>
                                    <th class="">Dia5_PedidosYa</th>
                                    <th class="">Dia5_Rappi</th>

                                    <th class="">Dia5_Didi</th>
                                    <th class="">Dia6_Directo</th>
                                    <th class="">Dia6_PedidosYa</th>
                                    <th class="">Dia6_Rappi</th>
                                    <th class="">Dia6_Didi</th>

                                    <th class="">Dia7_Directo</th><!-- 30 -->
                                    <th class="">Dia7_PedidosYa</th>
                                    <th class="">Dia7_Rappi</th>
                                    <th class="">Dia7_Didi</th>
                                    <th class="">Dia8_Directo</th>

                                    <th class="">Dia8_PedidosYa</th>
                                    <th class="">Dia8_Rappi</th>
                                    <th class="">Dia8_Didi</th>
                                    <th class="">Dia9_Directo</th>
                                    <th class="">Dia9_PedidosYa</th>

                                    <th class="">Dia9_Rappi</th><!-- 40 -->
                                    <th class="">Dia9_Didi</th>
                                    <th class="">Dia10_Directo</th>
                                    <th class="">Dia10_PedidosYa</th>
                                    <th class="">Dia10_Rappi</th>

                                    <th class="">Dia10_Didi</th>
                                    <th class="">Dia11_Directo</th>
                                    <th class="">Dia11_PedidosYa</th>
                                    <th class="">Dia11_Rappi</th>
                                    <th class="">Dia11_Didi</th>

                                    <th class="">Dia12_Directo</th><!-- 50 -->
                                    <th class="">Dia12_PedidosYa</th>
                                    <th class="">Dia12_Rappi</th>
                                    <th class="">Dia12_Didi</th>
                                    <th class="">Dia13_Directo</th>

                                    <th class="">Dia13_PedidosYa</th>
                                    <th class="">Dia13_Rappi</th>
                                    <th class="">Dia13_Didi</th>
                                    <th class="">Dia14_Directo</th>
                                    <th class="">Dia14_PedidosYa</th>

                                    <th class="">Dia14_Rappi</th><!-- 60 -->
                                    <th class="">Dia14_Didi</th>
                                    <th class="">Dia15_Directo</th>
                                    <th class="">Dia15_PedidosYa</th>
                                    <th class="">Dia15_Rappi</th>

                                    <th class="">Dia15_Didi</th>
                                    <th class="">Dia16_Directo</th>
                                    <th class="">Dia16_PedidosYa</th>
                                    <th class="">Dia16_Rappi</th>
                                    <th class="">Dia16_Didi</th>

                                    <th class="">Dia17_Directo</th><!-- 70 -->
                                    <th class="">Dia17_PedidosYa</th>
                                    <th class="">Dia17_Rappi</th>
                                    <th class="">Dia17_Didi</th>
                                    <th class="">Dia18_Directo</th>

                                    <th class="">Dia18_PedidosYa</th>
                                    <th class="">Dia18_Rappi</th>
                                    <th class="">Dia18_Didi</th>
                                    <th class="">Dia19_Directo</th>
                                    <th class="">Dia19_PedidosYa</th>

                                    <th class="">Dia19_Rappi</th><!-- 80 -->
                                    <th class="">Dia19_Didi</th>
                                    <th class="">Dia20_Directo</th>
                                    <th class="">Dia20_PedidosYa</th>
                                    <th class="">Dia20_Rappi</th>

                                    <th class="">Dia20_Didi</th>
                                    <th class="">Dia21_Directo</th>
                                    <th class="">Dia21_PedidosYa</th>
                                    <th class="">Dia21_Rappi</th>
                                    <th class="">Dia21_Didi</th>

                                    <th class="">Dia22_Directo</th><!-- 90 -->
                                    <th class="">Dia22_PedidosYa</th>
                                    <th class="">Dia22_Rappi</th>
                                    <th class="">Dia22_Didi</th>
                                    <th class="">Dia23_Directo</th>

                                    <th class="">Dia23_PedidosYa</th>
                                    <th class="">Dia23_Rappi</th>
                                    <th class="">Dia23_Didi</th>
                                    <th class="">Dia24_Directo</th>
                                    <th class="">Dia24_PedidosYa</th>

                                    <th class="">Dia24_Rappi</th><!-- 100 -->
                                    <th class="">Dia24_Didi1</th>
                                    <th class="">Dia25_Directo</th>
                                    <th class="">Dia25_PedidosYa</th>
                                    <th class="">Dia25_Rappi</th>

                                    <th class="">Dia25_Didi</th>
                                    <th class="">Dia26_Directo</th>
                                    <th class="">Dia26_PedidosYa</th>
                                    <th class="">Dia26_Rappi</th>
                                    <th class="">Dia26_Didi</th>

                                    <th class="">Dia27_Directo</th><!-- 110 -->
                                    <th class="">Dia27_PedidosYa</th>
                                    <th class="">Dia27_Rappi</th>
                                    <th class="">Dia27_Didi</th>
                                    <th class="">Dia28_Directo</th>

                                    <th class="">Dia28_PedidosYa</th>
                                    <th class="">Dia28_Rappi</th>
                                    <th class="">Dia28_Didi</th>
                                    <th class="">Dia29_Directo</th>
                                    <th class="">Dia29_PedidosYa</th>

                                    <th class="">Dia29_Rappi</th><!-- 120 -->
                                    <th class="">Dia29_Didi</th>
                                    <th class="">Dia30_Directo</th>
                                    <th class="">Dia30_PedidosYa</th>
                                    <th class="">Dia30_Rappi</th>

                                    <th class="">Dia30_Didi</th>
                                    <th class="">Dia31_Directo</th>
                                    <th class="">Dia31_PedidosYa</th>
                                    <th class="">Dia31_Rappi</th>
                                    <th class="">Dia31_Didi</th>

                                </tr>
   
                            </thead>
                            <tbody>
                                <tr>
                                   <td colspan="125" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                               </tr>
                           </tbody>
                            <tfoot>
                                <tr>
                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th><!-- -->
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
<script type="text/javascript">
    $(document).ready(function() {
        //$('.datepicker').datetimepicker({format: 'YYYY-MM-DD', showClear: true, showClose: true, useCurrent: false, widgetPositioning: {horizontal: 'auto', vertical: 'bottom'}, widgetParent: $('.dataTable tfoot')});
    });

    function activo1(){
        //alert("Que pasa chochera")
        var tienda = document.getElementById("tienda").value
        var anno = document.getElementById("anno").value
        var mes = document.getElementById("mes").value
        var cRutin = '<a href="<?= base_url() ?>sales/platos_diarios_canales/' + tienda + '/' + anno + '/' + mes + '" id="enlace_grilla">Ejecutar</a>'
        //alert(cRutin)
        document.getElementById('refresco').innerHTML = cRutin
        document.getElementById('preparo').style.display = "none"
        document.getElementById('enlace_grilla').click()
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