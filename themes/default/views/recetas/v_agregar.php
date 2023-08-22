<script type="text/javascript" src="<?= base_url() ?>themes/default/assets/dev/js/funciones_fm.js"></script>
<script>
	<?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(8)',500);\n";
    ?>	

	function grabar_receta(){
		//console.log("inicia grabar_receta")
		var combo 			= document.getElementById("receta")
		var nombreReceta1 	= combo.options[combo.selectedIndex].text
		var unidad 			= document.getElementById("unidad").value
		var cantidad 		= $("#cantidadReceta").val()
		if(unidad == "KILO" || unidad == 'LITRO'){
			cantidad = $("#cantidadReceta").val() * 1000
		}

		parametros = {
			nombreReceta 	: nombreReceta1,
			id_insumo		: $("#idPro").val(),
			cantidadReceta 	: cantidad,
			product_id 		: $("#receta").val()
		}
		$.ajax({
			data 	:parametros,
			url 	:'<?= base_url("/receta/agregar") ?>',
			type 	:'get',
			success :function(response){
				console.log(response)
				ar = JSON.parse(response)
				if(ar['rpta']){
					document.getElementById('pizarra1').innerHTML = "<div class=\"alert alert-success\">" + ar["message"] + "</div>"
				}else{
				 	document.getElementById('pizarra1').innerHTML = "<div class=\"alert alert-danger\">" + ar["message"] + "</div>"
				}
			
				mostrar_receta($("#receta").val())
			}
		})
	}

    var objT = new tablas_fm(30)

    function mostrar_receta(product_id){
        $.ajax({
            data    : {product_id : product_id},
            url     : '<?= base_url('receta/mostrar_receta') ?>',
            type    : 'get',
            success : function(res){
                var ar = JSON.parse(res)
                var ar_t = ar[1]
                console.log(ar_t)
                var ar_tit 		= ['Id','Code','Insumo','Cantidad','Unidad']
                var ar_campos 	= ['id','code','name','cantidadReceta','unidad']
                var tabla1 = objT.tablar(ar_t, ar_tit, ar_campos)
                $('#tabla_detalle').html('<h3>'+ar[0]+'</h3>'+tabla1)
            }
        })
    }

    function limpiar_cab(){
		//document.getElementById('ruc').value = "";
	}

	function eliminar_ingrediente(nId){
		r = confirm("Desea realmente eliminar el insumo?")
		if (r == true){
			var parametros = {
				id: nId
			}
			$.ajax({
				data: parametros,
				url : "<?= base_url("receta/eliminar_receta") ?>",
				type: "get",
				success: function(response){
					ver_recetas($("#receta").val())
				}
			})
		}
	}	
</script>
<style type="text/css">
    .tistulos{
        font-weight: bold;
        background-color: rgb(230,230,0);
    }
    .table td:nth-child(3) { text-align: center;}
</style>
<div id="page-wrapper">
            
    <!-- CABECERA : -->
    <div style="border-style:solid; border-color:gray; border-width: 1px; margin:20px; padding:10px;">
        <div class="row espaciado">
	        <div class="col-sm-2">
	        	Nombre Receta :  (*)
	        </div>

	        <div class="col-sm-3">
	        	<?php
	        		$receta = "";
	        		
	        		$cSql = "SELECT `id`, `name` 
					FROM `tec_products` 
					WHERE `category_id` <> 7 order by name";
					// and id not in (select product_id from tec_recetas group by product_id)

	        		$query = $this->db->query($cSql);

	        		$ar = array("0"=>"Elija");
	        		foreach($query->result() as $r){
	        			$ar[$r->id] = $r->name;
	        		}
	        		echo form_dropdown('receta', $ar, $receta, 'class="form-control tip" id="receta" onchange="mostrar_receta(this.value)" required="required"');
	        	?>
	        </div>
	        
	    </div>

	    <div class="row espaciado">
	        <div class="col-sm-2">
	        	Costo:
	        </div>

	        <div class="col-sm-3">
	        	<input type="text" id="costo" name="costo" size="10" class="form-control" placeholder="costo">
	        </div>
	    </div>
	</div>

	<!-- DETALLE : -->
	<div style="border-style:solid; border-color:gray; border-width: 1px; margin:20px; padding:10px;">

	    <div class="row">
	        <div class="col-xs-5 col-sm-3">
	        	<label>Producto:</label>
	        	<?= $this->receta_model->combo_producto() ?>
	        </div>

	        <div class="col-xs-4 col-sm-3">
	        	<label>Unidad:</label>
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

	        <div class="col-xs-4 col-sm-2">
	        	<label>Cantidad:</label>
	        	<input type="text" id="cantidadReceta" name="cantidadReceta" size="10" class="form-control" placeholder="cantidad">
	        </div>

	        <div class="col-xs-4 col-sm-2" style="margin-top:24px">
	        	<button class="btn btn-info" type="button" onclick="grabar_receta()">Agregar Producto</button>
	        </div>

	    </div>

		<!--<div class="row" style="padding-top:18px">	    
	    	<div class="col-sm-12">
	    		<span style="color:red; font-size:16px">Nota.- En la Receta las cantidades se agregan en gramos, y en mililitros para los líquidos.</span>
	    	</div>
		</div>-->

	    <div class="row" style="padding-top:8px">
	    	<div class="col-sm-12" id="pizarra1">
	    	</div>
	    </div>

	</div>

	<div style="border-style:solid; border-color:gray; border-width: 1px; margin:20px; padding:10px;">

		<div class="row" style="padding:1px;">
	    	<div class="col-xs-12 col-sm-10 col-lg-6" id="pizarra2">

	    	</div>
		</div>

		<div class="row" style="padding:1px;">
	    	<div class="col-xs-12 col-sm-12" id="pizarra3">

	    	</div>
		</div>

		<div class="row" style="padding:1px;">
	    	<div class="col-xs-12 col-sm-10 col-md-8 col-lg-7" id="tabla_detalle">

	    	</div>
		</div>

	</div>

</div>