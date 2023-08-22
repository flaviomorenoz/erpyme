<?php
	//if(isset($inventariable)){ echo "Inventariable:" . $inventariable;}else{ echo "No hay la variable";}
?>
<div class="row">
	<div class="col-xs-12 col-sm-12" style="padding: 20px 40px">

	    <div class="row" style="margin-top:5px;">
	        <div class="col-sm-2">
	        	Nombre Producto:
	        </div>

	        <div class="col-sm-3">
	        	<input type="text" id="descPro" name="descPro" value="<?= $name ?>" size="30" class="form-control" placeholder="Nombre Producto">
	        	<input type="hidden" id="idPro" name="idPro" value="<?= $id ?>">
	        </div>
	    </div>

	    <div class="row" style="margin-top:5px;">
	        <div class="col-sm-2">
	        	Unidad de medida:
	        </div>

	        <div class="col-sm-3">
	        	<?php 
	        		$ar_unidad = array('UNIDAD','GRAMO','KILO','LITRO');
	        		echo "<select class=\"form-control\" name=\"unidad\" id=\"unidad\">";
	        		$selected = "";
	        		for($i=0; $i<count($ar_unidad); $i++){
	        			if($ar_unidad[$i] == $unidad){
	        				$selected = " selected";
	        			}
	        			echo "<option value=\"" . $ar_unidad[$i] . "\"$selected>" . $ar_unidad[$i] . "</option>";
	        			$selected = "";
	        		}
	        		echo "</select>";
	        	?>
	        </div>
	    </div>
	    
	    <div class="row" style="margin-top:5px;">
	        <div class="col-sm-2">
	        	Para Inventario:
	        </div>
	        <div class="col-sm-3">
				
				<?php
					$ar = array('1'=>'Inventariable','0'=>'No inventariable');
					echo form_dropdown('inventariable', $ar, $inventariable,'class="form-control tip" id="inventariable" required="required"');	
				?>
				
			</div>
		</div>

	    <div class="row" style="margin-top:5px;">
	        <div class="col-sm-2">
	        	Tipo de Item:
	        </div>
	        <div class="col-sm-3">
				
				<?php
					$result = $this->db->query("SELECT * FROM `tec_rubros` WHERE productos = '1'")->result_array();
					$ar 	= $this->fm->conver_dropdown($result,"id","descrip",array(''=>'Seleccione'));
					echo form_dropdown('rubro2', $ar, $rubro,'class="form-control tip" id="rubro2" required="required"');	
				?>
				
			</div>
		</div>

	    <div class="row" style="margin-top:5px;">
	        <div class="col-sm-2">
					<div class="col-sm-2" style="margin:20px;">
					<button class="btn btn-success" onclick="actualizar_insumo()">Grabar</button>
				</div>
			</div>
		</div>

	    <div class="row" style="margin-top:5px;">
	        <div id="pizarra1" class="col-sm-12">
	        	.
	        </div>
	    </div>
		<a id="listado_i" href="<?= site_url('insumos/listar_insumos'); ?>"></a>	    
	</div>
</div>

<script type="text/javascript">
	<?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(4)',500);\n";
    ?>	

	function actualizar_insumo(){
		//alert("Antes de enviar:" + document.getElementById('rubro2').value)
		var parametros = {
			name 		: document.getElementById('descPro').value,
			unidad 		: document.getElementById('unidad').value,
			id 			: document.getElementById('idPro').value,
			inventariable : document.getElementById('inventariable').value,
			rubro 		: document.getElementById('rubro2').value
		}
		$.ajax({
			data 	: parametros,
			url 	:'<?= base_url('insumos/update_insumos') ?>',
			type 	:'get',
			success :function(response){
				alert(response)
				//document.getElementById('listado_i').click()
				location.reload()
			}
		})
	}
</script>