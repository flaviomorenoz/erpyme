<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
?>

<section class="content">

    <div class="row">
        <div class="col-xs-12 col-sm-10 col-md-9 col-lg-7">
            <p style="text-align:center;font-size:16px;">
                <h2>Ver Personal</h2>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-11 col-md-10 col-lg-9">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="SLData" class="table table-striped table-bordered table-condensed table-hover" data-page-length='50'>
                            <thead>
                                <tr>
                                    <td colspan="9" class="p0" style="border-style: solid; border-color: black; border-width: 1px;">
                                        <input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;">
                                    </td>
                                </tr>

                                <tr class="active">
                                    <!-- "id", "nombres", "apellidos", "tip_doc", "documento", "phone", "activo","op" -->
                                    <th style="max-width:30px;">Id</th>
                                    <th class="col-xs-1 col-sm-1 text-left" style="text-align:left">Nombres</th>
                                    <th class="col-xs-2 col-sm-2">Apellidos</th>
                                    <th class="col-sm-2">Tip_doc</th>
                                    <th class="col-xs-1 col-sm-1">Documento</th>
                                    <th class="col-xs-1 col-sm-1">Phone</th>
                                    <th class="col-xs-1 col-sm-1">Local</th>
                                    <th class="col-xs-1 col-sm-1">Activo</th>
                                    <th style="min-width:75px; max-width:115px; text-align:center;">Op</th>
                                </tr>

                                <tr>
                                    <th style="max-width:30px;">
                                        <input type="text" class="text_filter" placeholder="Id">
                                    </th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-2"></th>
                                    <th class="col-sm-2"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                    <th class="col-sm-1"></th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                   <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
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
                                </tr>
                            
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>



</section>
<script type="text/javascript">
    function modificar(id){
        window.location.href = "<?= base_url() ?>recursos/agregar_personal?id=" + id
    }

    function eliminar(id){
        if(confirm("Confirme que desea eliminar?")){
            $.ajax({
                data : {id:id},
                type : 'get',
                url  : '<?= base_url('recursos/getContratos') ?>',
                success:function(res){
                    if(res=='0'){
                        window.location.href = "<?= base_url() ?>recursos/eliminar_personal?id=" + id            
                    }else{
                        alert("No se puede eliminar debido a que existe contratos con este Personal")
                    }
                }
            })
            
                    
        }
    }

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
                url: '<?= base_url('recursos/get_personal');?>', 
                type: 'POST', 
                "data": function ( d ) {
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                }
            },
            "buttons": [
            // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ] } },
            { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true,
            exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7] } },
            { extend: 'colvis', text: 'Filtro'},
            ],
            "columns": [
                // "id", "nombres", "apellidos", "tip_doc", "documento", "phone", "activo","op"
                { "data": "id", "visible": true },
                { "data": "nombres"},
                { "data": "apellidos"},
                { "data": "tip_doc" },
                { "data": "documento"},
                { "data": "phone"},
                { "data": "local"}, 
                { "data": "activo"},
                { "data": "Actions"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.id = aData.id;
                return nRow;
            },
            "footerCallback": function (  tfoot, data, start, end, display ) {
                var api = this.api(), data;
                //$(api.column(7).footer()).html( cf(api.column(7).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
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