<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
if(!isset($maestro_id)){ $maestro_id = $id; }
?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        //echo "setTimeout('abrir_item_menu(7)',500);\n";
    ?>

    function cambiando_casillas(){
        // actualizando las casillas con localStorage      
        if (typeof(Storage) !== "undefined") {
            if(localStorage.getItem("gastos_filtro_desde") != "null")
                $("#desde").val(localStorage.getItem("gastos_filtro_desde"))
            if(localStorage.getItem("gastos_filtro_hasta") != "null")
                $("#hasta").val(localStorage.getItem("gastos_filtro_hasta"))
            
            if(localStorage.getItem("gastos_filtro_tienda") != "null"){
                $("#tienda").val(localStorage.getItem("gastos_filtro_tienda"))
            }
            if(localStorage.getItem("gastos_filtro_fec_emi") != "null")
                $("#fec_emi").val(localStorage.getItem("gastos_filtro_fec_emi"))
            if(localStorage.getItem("gastos_filtro_clasifica1") != "null")
                $("#clasifica1").val(localStorage.getItem("gastos_filtro_clasifica1"))
            if(localStorage.getItem("gastos_filtro_clasifica2") != "null")
                $("#clasifica2").val(localStorage.getItem("gastos_filtro_clasifica2"))
        }
    }
</script>

<div class="content" style="margin-left: 10px">
<?php echo form_open_multipart("inventarios/save_masivo", 'class="validation" id="form_inv"'); ?>
    <div class="row">
    
        <div class="col-sm-4 col-lg-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Inventario:</label>
                <?php
                    $cSql = "select a.id, concat(a.fecha,'_',b.state) descrip from tec_maestro_inv a 
                        inner join tec_stores b on a.store_id = b.id
                        order by a.id desc";
                    $result = $this->db->query($cSql)->result_array();
                    $ar = array();
                    foreach($result as $r){ 
                        $ar[$r["id"]] = $r["descrip"];
                    }
                    echo form_dropdown('maestro_id', $ar, $maestro_id, 'class="form-control tip" id="maestro_id" required="required"');
                ?>
            </div>
        </div>

        <div class="col-sm-4 col-lg-3" style="border-style:none; border-color:red;">
            <br>
            <button type="button" onclick="vista_previa()" class="btn btn-primary" style="margin-top:5px">Vista Previa</button>
            <button type="button" onclick="rellenar_con_ceros()" class="btn btn-primary" style="margin-top:5px">Rellenar vacios con Ceros</button>
        </div>

        <!--<div class="col-sm-2 col-lg-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Fecha Inventario:</label>
                <input type="date" name="fecha" id="fecha" class="form-control">
            </div>
        </div>
        <div class="col-sm-3 col-lg-3" style="border-style:none; border-color:red;">
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
        </div>-->
    </div>

    <div class="row" style="color: rgb(100,100,100); font-weight: bold; font-size: 16px; margin-bottom:10px">
        <div class="hidden-xs col-sm-3 col-md-3 col-lg-3 text-left">Insumo</div>
        <div class="hidden-xs col-sm-3 col-md-3 col-lg-2 text-center">Unidad</div>
        <div class="hidden-xs col-sm-3 col-md-2 col-lg-2 text-center">Cantidad</div>
    </div>

    <?php 
        $cSql = "select tec_products.id, tec_products.name, tec_products.unidad, tec_products.rubro, tec_rubros.descrip
            from tec_products
            left join tec_rubros on tec_products.rubro = tec_rubros.id
            where tec_products.category_id = 7 and tec_products.rubro = 1 and tec_products.inventariable = '1'
            order by tec_rubros.descrip, tec_products.name";
        $query = $this->db->query($cSql);
        $i = 0;
        foreach($query->result() as $fila){
            $unidad = $fila->unidad;
            $cantidad = "";
            $cSql = "select a.* from tec_inventarios a 
                left join tec_products b on a.product_id = b.id and b.category_id = 7 and b.rubro=1 and b.inventariable = '1'
                where a.product_id = ? and a.maestro_id = ?";
            $q1 = $this->db->query($cSql,array($fila->id, $maestro_id));
            foreach($q1->result() as $r){
                $cantidad   = $r->cantidad;
            }
    ?>
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <?php
                        echo "<span style=\"color:rgb(200,200,200)\">".$fila->id."</span>&nbsp;&nbsp;";
                    	echo $fila->name;
        			?>
                    <input type="hidden" name="product_id_<?php echo $i; ?>" id="product_id_<?php echo $i; ?>" value="<?php echo $fila->id; ?>">
                </div>
                <div class="col-sm-3 col-md-3 col-lg-2" style="border-style:none; border-color:red; padding-left:35px; padding-right:35px;">
                    <div class="form-group">
                        <?php
                            $this->db->reset_query();
                            $result = $this->db->select("codigo, descrip")->get("unidades")->result();
                            $ar = array("0"=>"Elija");
                            foreach($result as $r){
                                $ar[$r->codigo] = $r->descrip;
                            }
                            echo form_dropdown("unidad_".$i, $ar, $unidad, 'class="form-control tip" id="unidad_'.$i.'" required="required"');
                        ?>
                    </div>
                </div>

                <div class="col-sm-3 col-md-2 col-lg-2">
                    <input type="text" name="cantidad_<?php echo $i; ?>" id="cantidad_<?php echo $i; ?>" value="<?php echo $cantidad; ?>" class="form-control">
                </div>

            </div>
    <?php
            $i++; 
        }
        echo "<input type=\"hidden\" id=\"nro_insumos\" value=\"{$i}\">"; 
    ?>

    <div class="row" style="margin-top:25px">
        <div class="col-sm-3 col-lg-2">
        	<!--<button type="submit" class="form-control btn btn-primary">Grabar</button>-->
            <?php 
                if(isset($esta_abierto)){
                    if($esta_abierto){
                        echo form_submit('add_', "Grabar", 'class="btn btn-primary"');
                    }else{
                        $nada = 0;
                    }
                }
             ?>
        </div>
    </div>

    <div class="row" style="margin-top:25px">
        <div id="pizarra" class="col-sm-12 col-lg-8" style="border-style:none; border-color:red; border-width:3px;">

            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="catData" class="table table-striped table-bordered table-condensed table-hover" style="margin-bottom:5px;">
                            <thead>
                                <tr>
                                    <td colspan="7" class="p0"><input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;"></td>
                                </tr>
                                <tr class="active">
                                    <th style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th>Fecha</th>
                                    <th>Store_id</th>
                                    <th>Tienda</th>
                                    <th>product_id</th>
                                    <th>producto</th>
                                    <th style="width:75px;">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                            <!-- <tfoot>
                                <tr>
                                    <td colspan="5" class="p0"><input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;"></td>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

        </div>
    </div>
    <input type="hidden" name="txt_i" id="txt_i" value="<?= $i ?>">
<?php 
    echo form_close(); 
?>
</div>

<div id="refresco"></div>
<script type="text/javascript">

    function empty(data){
      if(typeof(data) == 'number' || typeof(data) == 'boolean')
      { 
        return false; 
      }
      if(typeof(data) == 'undefined' || data === null)
      {
        return true; 
      }
      if(typeof(data.length) != 'undefined')
      {
        return data.length == 0;
      }
      var count = 0;
      for(var i in data)
      {
        if(data.hasOwnProperty(i))
        {
          count ++;
        }
      }
      return count == 0;
    }

    function grabar(){
    	//var fecha 		= document.getElementById('fecha').value
    	//var tienda 		= document.getElementById('tienda').value
    	var producto 	= document.getElementById('producto').value
    	var cantidad 	= document.getElementById('cantidad').value
        var unidad      = document.getElementById('unidad').value
        var maestro_id  = document.getElementById('maestro_id').value

        /*if(empty(fecha)){
            alert("Ingrese fecha")
            return false
        }

    	if(tienda.length == 0){
            alert('Ingrese tienda')
            return false
        }*/

        if(unidad == '0'){
            alert('Debe ingresar unidad')
            return false
        }

        if(producto.length == 0){
            alert("Ingrese producto")
            return false
        }

        if(cantidad.length == 0){
            alert("Ingrese cantidad")
            return false
        }

        $.ajax({
    		data: {maestro_id: maestro_id, product_id: producto, cantidad: cantidad, unidad: unidad}, /*  fecha: fecha, store_id: tienda, */
    		type: 'get',
    		url: '<?= base_url('inventarios/save') ?>',
    		success: function(res){
                //console.log(res)
                var obj = JSON.parse(res)
                if(obj.rpta == "success"){
    				document.getElementById("producto").value = "" 
                    document.getElementById("unidad").value = ""
                    document.getElementById("cantidad").value = ""
    			}
                document.getElementById("pizarra").innerHTML = "<div class=\"alert alert-" + obj.rpta + "\">" + obj.msg + "</div>" + obj.other
    		}
    	})
    }

    function limpiar(){
    	document.getElementById('producto').value = ""
    	document.getElementById('cantidad').value = ""
    }

    function mostrar_registros(maestro_id){
        //console.log("Carijo:"+maestro_id)
        $.ajax({
            data : {maestro_id : maestro_id},
            url  : '<?= base_url('inventarios/mostrar_registros') ?>',
            type : 'get',
            success : function(res){
                document.getElementById("pizarra").innerHTML = res
            }
        })
    }

</script>

<script type="text/javascript">
    $(document).ready(function() {

        //console.log(document.getElementById('maestro_id').value)
        //mostrar_registros(document.getElementById('maestro_id').value)  
        //alert(document.getElementById('maestro_id').value)

        function image(n) {
            if (n !== null) {
                return '<div style="width:32px; margin: 0 auto;"><a href="<?=base_url();?>uploads/'+n+'" class="open-image"><img src="<?=base_url();?>uploads/thumbs/'+n+'" alt="" class="img-responsive"></a></div>';
            }
            return '';
        }

        var table = $('#catData').DataTable({
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
            'ajax' : { url: '<?=site_url('inventarios/get_detalle_inventario');?>', type: 'POST', "data": function ( d ) {
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
                d.maestro_id = document.getElementById('maestro_id').value;
            }},
            "buttons": [
                // { extend: 'copyHtml5', 'footer': false, exportOptions: { columns: [ 0, 1, 2, 3 ] } },
                { extend: 'excelHtml5', 'footer': false, exportOptions: { columns: [ 1, 3, 5, 6 ] } },
                { extend: 'csvHtml5', 'footer': false, exportOptions: { columns: [ 1, 3, 5, 6 ] } },
                { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': false, exportOptions: { columns: [ 1, 3, 5, 6 ] } },
                { extend: 'colvis', text: 'Filtro'},
            ],
            "columns": [
                { "data": "id", "visible": false},
                { "data": "fecha"},
                { "data": "store_id", "visible":false},
                { "data": "tienda" },
                { "data": "product_id", "visible":false},
                { "data": "producto"},
                { "data": "cantidad"}
            ]

        });

        $('#search_table').on( 'keyup change', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (((code == 13 && table.search() !== this.value) || (table.search() !== '' && this.value === ''))) {
                table.search( this.value ).draw();
            }
        });

        $('#catData').on('click', '.image', function() {
            var a_href = $(this).attr('href');
            var code = $(this).attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src',a_href);
            $('#picModal').modal();
            return false;
        });
        $('#catData').on('click', '.open-image', function() {
            var a_href = $(this).attr('href');
            var code = $(this).closest('tr').find('.image').attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src',a_href);
            $('#picModal').modal();
            return false;
        });


    });

    function vista_previa(){
        window.open('<?= base_url() ?>inventarios/vista_previa/'+document.getElementById('maestro_id').value,'_blank');
    }

    function rellenar_con_ceros(){
        var nI = document.getElementById("nro_insumos").value * 1
        var casilla = ""
        for(var i=0; i<nI; i++){
            casilla = document.getElementById("cantidad_"+i).value
            if (casilla.length == 0){
                document.getElementById("cantidad_"+i).value = 0
            }
        }
    }
</script>

