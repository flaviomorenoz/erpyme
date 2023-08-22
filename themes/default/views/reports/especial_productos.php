<?php
	$cDesde = isset($cDesde) ? $cDesde : date("Y-m-d");
	$cHasta = isset($cHasta) ? $cHasta : date("Y-m-d");
	$cProducto = "null";
	if(isset($producto)){
		if($producto == "null" || $producto==''){
			$cProducto = "null";
		}else{
			$cProducto = $producto;
		}
	}
    $cProveedor = "null";
    if(isset($proveedor)){
        if($proveedor == "null" || $proveedor==''){
            $cProveedor = "null";
        }else{
            $cProveedor = $proveedor;
        }
    }
	
    $cStore_id = "null";
    if(isset($store_id)){
        if ( $store_id == "null" || $store_id == ''){
            $cStore_id = "null";
        }else{
            $cStore_id = $store_id;
        }
    }
?>
<div class="row">
	<div class="col-sm-12 col-md-12 col-lg-10">
		
        <?= form_open_multipart("reports/especial_productos", ' id="form1"'); ?>
			<div class="row" style="margin-top:10px; margin-bottom:20px; margin-left:10px">
				<div class="col-sm-3 col-lg-2 text-center">
					Desde:<input type="date" name="txt_desde" id="txt_desde" class="form-control" value="<?= (isset($cDesde) ? $cDesde : date("Y-m-d")) ?>">
				</div>
				<div class="col-sm-3 col-lg-2 text-center">
					Hasta:<input type="date" name="txt_hasta" id="txt_hasta" class="form-control" value="<?= (isset($cHasta) ? $cHasta : date("Y-m-d")) ?>">
				</div>
				<div class="col-sm-4 col-lg-3 text-center">
					Producto:
                    <?php
                        $ar         = array();
                        $result     = $this->db->query("select id, ucase(name) name from tec_products where category_id=7 and rubro<>0 order by ucase(name)")->result_array();
                        $ar         = $this->fm->conver_dropdown($result, "id", "name", array(''=>'Todas'));
                        echo form_dropdown('txt_producto',$ar,$cProducto,' id="txt_producto" class="form-control"');
                    ?>
				</div>
                <div class="col-sm-3 col-lg-2 text-center">
                    Tienda:
                    <?php
                        $ar         = array();
                        $result     = $this->db->query("select * from tec_stores order by id")->result_array();
                        $ar         = $this->fm->conver_dropdown($result, "id", "state", array(''=>'Todas'));
                        echo form_dropdown('store_id',$ar,$store_id,' id="store_id" class="form-control"');
                    ?>

                </div>

				<div class="col-sm-3 col-lg-2 text-center">
					Proveedor:
                    <?php
                        $ar         = array();
                        $result     = $this->db->query("select * from tec_suppliers order by name")->result_array();
                        $ar         = $this->fm->conver_dropdown($result, "id", "name", array(''=>'Todas'));
                        echo form_dropdown('txt_proveedor',$ar,$cProveedor,' id="txt_proveedor" class="form-control"');
                    ?>

				</div>
				
                <div class="col-sm-2 col-lg-2 text-left">
					<!--<button type="button" onclick="llenaste()">Llena</button>-->
					<br><button type="button" class="btn btn-primary" onclick="document.getElementById('form1').submit()">Generar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<section class="content">

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-11 col-lg-10">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                                <!-- BUSQUEDA GRAL -->
                                <!--<tr>
                                    <td colspan="2" class="p0" style="border-style: solid; border-color: black; border-width: 1px;">
                                        <input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;">
                                    </td>
                                </tr>-->

                                <!-- TITULOS :  "fecha","name","unidad","proveedor","quantity","precio","punit" -->
                                <tr class="active">
                                    <th class="col-xs-3 col-sm-3" style="min-width:60px;">Fecha</th>
                                    <th class="col-xs-3 col-sm-2">Tienda</th>
                                    <th class="col-xs-3 col-sm-3 text-left" style="text-align:left">Nombre</th>
                                    <th class="col-xs-2 col-sm-1">Unidad</th>
                                    <th class="col-xs-2 col-sm-2">Proveedor</th>
                                    <th class="col-xs-2 col-sm-1">Cantidad</th>
                                    <th class="col-xs-2 col-sm-1">Total</th>
                                    <th class="col-xs-2 col-sm-1">P.Unit</th>
                                </tr>

                                <!-- ORDENES COLUMNAS Y BUSQ. INDIVIDUALES -->
                                <!--<tr>
                                    <th style="max-width:25px;">                                      
                                    </th>
                                    <th class="col-xs-2 col-sm-1">
                                    </th>
                                    <th class="col-xs-3 col-sm-3">
                                    </th>
                                    <th class="col-xs-2 col-sm-1">
                                    </th>
                                    <th class="col-xs-2 col-sm-2">
                                    </th>
                                    <th class="col-xs-2 col-sm-1">
                                    </th>
                                    <th class="col-xs-2 col-sm-1">
                                    </th>
                                    <th class="col-xs-2 col-sm-1">
                                    </th>
                                </tr>-->

                            </thead>
                            <tbody>
                                <tr>
                                   <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
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
                                </tr>
                            </tfoot>

                           <tfoot>
                            
                            </tfoot>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>


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
            dom: '<"top"i>Brt<"bottom"><"clear">',
            "pageLength": 3000,
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
                url: '<?=site_url("reports/get_especial/{$cDesde}/{$cHasta}/{$cProducto}/{$cProveedor}/{$cStore_id}");?>',
                type: 'POST', 
                "data": function ( d ) {
                    d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                }
            },
            "buttons": [
                // { extend: 'copyHtml5', 'footer': true, exportOptions: { columns: [ 0, 1 ] } },
                { extend: 'excelHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7] } },
                //{ extend: 'csvHtml5', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': true, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7] } },
                //{ extend: 'colvis', text: '<i class="glyphicon glyphicon-cog"></i>'},
            ],
            "columns": [
                // TITULOS :  "fecha","name","unidad","proveedor","quantity","precio","punit"
                { "data": "fecha", "visible": true },
                { "data": "state"},
                { "data": "name"},
                { "data": "unidad"},
                { "data": "proveedor", "visible": true},
                { "data": "quantity"},
                { "data": "precio"},
                { "data": "punit"}
            ],
            
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.id = aData.id;
                return nRow;
            },
            "footerCallback": function (  tfoot, data, start, end, display ) {
                var api = this.api(), data;
                $(api.column(6).footer()).html( cf(api.column(6).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
                //$(api.column(8).footer()).html( cf(api.column(8).data().reduce( function (a, b) { return pf(a) + pf(b); }, 0)) );
            }

        });
/*
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
*/
    });
</script>
