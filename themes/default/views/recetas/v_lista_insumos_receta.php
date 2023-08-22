<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<script type="text/javascript">
    $(document).ready(function() {

        function ptype(x) {
            if (x == 'standard') {
                return '<?= lang('standard'); ?>';
            } else if (x == 'combo') {
                return '<?= lang('combo'); ?>';
            } else if (x == 'service') {
                return '<?= lang('service'); ?>';
            } else {
                return x;
            }
        }

        function image(n) {
            if (n !== null) {
                return '<div style="width:32px; margin: 0 auto;"><a href="<?=base_url();?>uploads/'+n+'" class="open-image"><img src="<?=base_url();?>uploads/thumbs/'+n+'" alt="" class="img-responsive"></a></div>';
            }
            return '';
        }

        function method(n) {
            return (n == 0) ? '<span class="label label-primary"><?= lang('inclusive'); ?></span>' : '<span class="label label-warning"><?= lang('exclusive'); ?></span>';
        }

        //let cRut = "<?= site_url("products/get_products/{$store->id}/{$tienda}") ?>"
        //console.log(cRut)

        var table = $('#prTables').DataTable({
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
            'ajax' : { url: '<?=site_url("receta/get_recetas") ?>', 
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                }
            },
            "buttons": [
                { extend: 'excelHtml5', 'footer': false, exportOptions: { columns: [ 0, 1] } },
                { extend: 'csvHtml5', 'footer': false, exportOptions: { columns: [ 0, 1] } },
                { extend: 'pdfHtml5', orientation: 'portrait', pageSize: 'A4', 'footer': false,
                exportOptions: { columns: [ 0, 1] } },
                
            ],
            "columns": [
                { "data": "product_id"},
                { "data": "nombreReceta", "visible": true },
                //{ "data": "cant"}
                //{ "data": "acciones"}
                //{ "data": "Actions", "searchable": false, "orderable": false }
            ]

        });

        $('#prTables tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            //carga_tabla_detalle(data.nombreReceta)
            mostrar_receta(data.product_id)
        } );

        $('#search_table').on( 'keyup change', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (((code == 13 && table.search() !== this.value) || (table.search() !== '' && this.value === ''))) {
                table.search( this.value ).draw();
            }
        });

        table.columns().every(function () {
            var self = this;
            $( 'input', this.footer() ).on( 'keyup change', function (e) {
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

    function activo1(){
        let tienda      = document.getElementById("tienda").value
        let categoria   = document.getElementById("categoria").value
        //location.load("<?= base_url() ?>products/?tienda=2")

        cadena = '<a href="<?= base_url() ?>receta/rep_recetas/' + tienda + '/' + categoria + '" id="enlace_grilla_compras"></a>'
        
        document.getElementById('refresco').innerHTML = cadena

        setTimeout("document.getElementById('enlace_grilla_compras').click()",100)
    }

    /*
        function carga_tabla_detalle(cod){
            $.ajax({
                data    : {cod : cod},
                url     : '<?= base_url('receta/detalle_de_receta') ?>',
                type    : 'get',
                success : function(res){
                    $('#tabla_detalle').html(res)
                }
            })
        }
    */

    function mostrar_receta(product_id){
        $.ajax({
            data    : {product_id : product_id},
            url     : '<?= base_url('receta/mostrar_receta') ?>',
            type    : 'get',
            success : function(res){
                var ar = JSON.parse(res)
                var ar_tit = ar[0]
                var ar_data = ar[1]

                $('#tabla_detalle').html('<h3>'+ar_tit+'</h3>'+ tablar(ar_data))
            }
        })
    }

    
    function tablar(ari){
        var cad = "<table border='1' class='table'>"

        cad += "<tr>"
        cad += casillar('Id','tistulos')
        cad += casillar('Code','tistulos')
        cad += casillar('Insumo','tistulos')
        cad += casillar('Cantidad','tistulos')
        cad += "</tr>"

        for(let i = 0; i < ari.length; i++){
            cad += "<tr>"
            cad += "<td>" + ari[i]["id_insumo"] + "</td>"
            cad += "<td>" + ari[i]["code"] + "</td>"
            cad += "<td>" + ari[i]["name"] + "</td>"
            cad += "<td>" + ari[i]["cantidadReceta"] + " " + ari[i]["unidad"] + "</td>"
            cad += "</tr>"
        }
        cad += "</table>"
        return cad
    }

    function casillar(cado,clase1=''){
        return '<td class=\'' + clase1 + '\'>' + cado + '</td>'
    }


</script>
<style type="text/css">
    .tistulos{
        font-weight: bold;
        background-color: rgb(230,230,0);
    }
    .table td:nth-child(3) { text-align: center;}
</style>
<section class="content">
    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="display:flex;margin-bottom: 5px;">
        
        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div id="refresco"></div>
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    $q = $this->db->get('stores');

                    $ar = array();
                    if ($group_id == '1'){
                        
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
                    echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required" style="font-size:16px; font-weight:bold;"');
                ?>
            </div>
        </div>
    
        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Categoria:</label>
                <?php
                    //$group_id = $this->session->userdata["group_id"];
                    $this->db->select('id, name');
                    $this->db->from('categories');
                    $q = $this->db->get();

                    $ar = array();
                    $ar[] = "";
                    foreach($q->result() as $r){
                        //if($r->id == $this->session->userdata["store_id"]){
                            $ar[$r->id] = $r->name;
                        //}
                    }
                    echo form_dropdown('categoria', $ar, $categoria, 'class="form-control tip" id="categoria" required="required" style="font-size:16px; font-weight:bold;"');
                ?>
            </div>
        </div>
        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: 20px 0px 20px 0px;">
            <div class="row">
                <div class="col-sm-5" style="padding:5px 0px 0px 0px; text-align: center;">
                    <button onclick="activo1()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/gastus/search.png") ?>" height="30px"></button>
                </div>
                <div class="col-sm-5" style="padding:5px 0px 0px 0px; text-align: center">
                    <button onclick="limpiar()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/gastus/eliminar.png") ?>" height="30px"></button>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-11 col-md-10 col-lg-8">
            <div class="box box-primary">
                
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="prTables" class="table table-striped table-bordered table-hover" style="margin-bottom:5px;" data-page-length="9">
                        <thead>
                            <tr>
                                <td colspan="2" class="p0">
                                    <input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;">
                                </td>
                            </tr>
                            <tr class="active">
                                <th style="max-width:30px;"><?= lang("id"); ?></th>
                                <th class="col-xs-2 col-sm-1">Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="max-width:30px;"><input type="text" class="text_filter" placeholder="[]"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="modal fade" id="picModal" tabindex="-1" role="dialog" aria-labelledby="picModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                                <button type="button" class="close mr10" onclick="window.print();"><i class="fa fa-print"></i></button>
                                <h4 class="modal-title" id="myModalLabel">title</h4>
                            </div>
                            <div class="modal-body text-center">
                                <img id="product_image" src="" alt="" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-10 col-md-8" id="tabla_detalle">

        </div>
    </div>
</div>
</section>