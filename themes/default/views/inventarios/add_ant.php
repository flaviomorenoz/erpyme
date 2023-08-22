<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
/*if(!isset($desde)){         $desde = "";    }
if(!isset($hasta)){         $hasta = "";    }
if(!isset($tienda)){        $tienda = "0";  }
*/
if(!isset($maestro_id)){ $maestro_id = $id;}
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

    <div class="row">

        <div class="col-sm-4 col-lg-3">
            <label for="">Producto:</label>
            <?php
            	$ar = $this->inventarios_model->productos();
                echo form_dropdown('producto',$ar,'','class="form-control tip" id="producto" required="required"');
			?>
        </div>
        <div class="col-sm-2 col-lg-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Unidades:</label>
                <?php
                    $this->db->reset_query();
                    $result = $this->db->select("codigo, descrip")->get("unidades")->result();
                    $ar = array("0"=>"Elija");
                    foreach($result as $r){
                        $ar[$r->codigo] = $r->descrip;
                    }
                    echo form_dropdown('unidad', $ar, $unidad, 'class="form-control tip" id="unidad" required="required"');
                ?>
            </div>
        </div>

        <div class="col-sm-2 col-lg-1">
            <label for="">Cantidad:</label>
            <input type="text" name="cantidad" id="cantidad" class="form-control">
        </div>

    </div>

    <div class="row" style="margin-top:25px">
        <div class="col-sm-3 col-lg-2">
        	<button type="button" onclick="grabar()" class="form-control btn btn-primary">Grabar</button>
        </div>
    </div>

    <div class="row" style="margin-top:25px">
        <div id="pizarra" class="col-sm-12 col-lg-6">
        </div>
    </div>

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
        console.log("Carijo:"+maestro_id)
        $.ajax({
            data : {maestro_id : maestro_id},
            url  : '<?= base_url('inventarios/mostrar_registros') ?>',
            type : 'get',
            success : function(res){
                document.getElementById("pizarra").innerHTML = res
            }
        })
    }

    $("document").ready(function(){
        console.log(document.getElementById('maestro_id').value)
        mostrar_registros(document.getElementById('maestro_id').value)    
    })
</script>
