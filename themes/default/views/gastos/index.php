<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
if(!isset($desde)){ $desde = ""; }
if(!isset($hasta)){ $hasta = ""; }

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

    $(document).ready(function() {

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

        //function contrarestar(){    
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
                url: '<?=site_url('gastos/get_gastos');?>', 
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                    d.desde = document.getElementById('desde').value; //'<?= $desde ?>'; //
                    d.hasta = document.getElementById('hasta').value; //'<?= $hasta ?>'; //
                }
            },
            "buttons": [
                // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
                { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
                exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } },
                { extend: 'colvis', text: 'Filtro'},
            ],
            "columns": [
                { "data": "id", "visible": false },
                { "data": "state" },
                { "data": "date"}, /* , "render": hrld */
                { "data": "tipoDoc","className": "text-center"},
                { "data": "nroDoc"},
                { "data": "name"},
                { "data": "texto_supplier"},
                { "data": "fec_emi_doc" },
                { "data": "fec_venc_doc" },
                { "data": "d_a_v" },
                /*{ "data": "cargo_servicio", "render": currencyFormat},
                { "data": "total", "render": currencyFormat },*/
                { "data": "total_", "render": currencyFormat },
                //{ "data": "attachment", "render": attach, "searchable": false, "orderable": false },
                { "data": "Actions", "searchable": false, "orderable": false }
            ],
            "footerCallback": function (tfoot, data, start, end, display){
                var api = this.api(), data;
                $(api.column(10).footer()).html( cf(api.column(10).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
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
        //}

        //contrarestar();
        
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


    });
</script>

<style type="text/css">
    .table td:nth-child(3) { text-align: center;}
    .table td:nth-child(4) { text-align: left;}
</style>

<section class="content">
    <div class="row" style="display:flex;margin-bottom: 5px;">
        <div class="col-sm-2" style="border-style:none; border-color:red;">
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
        </div>
        
        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: auto;">
            <!--<a href="#" onclick="activo1()"><b>Consultar</b></a>-->
            <button onclick="activo1()" class="btn btn-primary">Consultar</button>
        </div>
        
        <div id="refresco" class="col-sm-1"></div>
        <div class="col-sm-5"></div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        
                        <table id="purData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
                            <thead>
                                <tr>
                                    <td colspan="11" class="p0" style="border-style: solid; border-color: black; border-width: 1px;">
                                        <input type="text" class="form-control b0" name="search_table" id="search_table" 
                                        placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;">
                                    </td>
                                </tr>
                                <tr class="active">
                                    <th style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Tda'); ?></th>
                                    <th class="col-xs-2 col-sm-1"><?= lang('date'); ?></th>
                                    <th class=""><?= lang('Tipo'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Nro.Doc'); ?></th>
                                    <th class="col-xs-2 col-sm-1"><?= lang('Proveedor'); ?></th>
                                    <th class="col-xs-2 col-sm-2"><?= lang('Proveedor'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Fec_Emision'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('Fec_Vcmto'); ?></th>
                                    <th class=""><?= lang('Dias'); ?></th>
                                    <!--<th class="col-xs-1 col-sm-1"><?= lang('Cargo'); ?></th>
                                    <th class="col-xs-1 col-sm-1"><?= lang('total'); ?></th>-->
                                    <th class="col-xs-1 col-sm-1"><?= lang('total.'); ?></th>
                                    <th style="width:75px;"><?= lang('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th style="max-width:30px;"><input type="text" class="text_filter" placeholder="[<?= lang('id'); ?>]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Tda'); ?>]"></th>
                                    <th class="col-xs-2 col-sm-1">
                                        <!--<span class="datepickercon">
                                            <input type="text" class="text_filter datepicker" placeholder="[<?= lang('date'); ?>]">
                                        </span>-->
                                    </th>
                                    <th class=""><input type="text" class="text_filter" placeholder="[<?= lang('tipo'); ?>]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Nro'); ?>]"></th>
                                    <th class="col-sm-2"><input type="text" class="text_filter" placeholder="[<?= lang('proveedor'); ?>]"></th>
                                    <th class="col-sm-2"><input type="text" class="text_filter" placeholder="[<?= lang('proveedor'); ?>]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Fec Emi'); ?>]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Fec Vcmto'); ?>]"></th>
                                    <th class=""><input type="text" class="text_filter" placeholder="[<?= lang('Dias'); ?>]"></th>
                                    <!--<th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Cargo'); ?>]"></th>
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="[<?= lang('Op'); ?>]"></th>-->
                                    <th class="col-sm-1"><input type="text" class="text_filter" placeholder="total"></th>
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
        let desde = document.getElementById("desde").value
        let hasta = document.getElementById("hasta").value
        if(desde.length > 0){
            document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>purchases/index/' + desde + '/' + hasta + '" id="enlace_grilla_compras">Ejecutar</a>'
            document.getElementById('preparo').style.display = "none"
            document.getElementById('enlace_grilla_compras').click()
        }

    }
</script>
