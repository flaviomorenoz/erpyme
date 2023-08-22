<div class="row">
	<div class="col-xs-12 col-sm-12" style="padding: 20px 40px">

	    <div class="row" style="margin-top:5px;">
	        <div class="col-sm-2">
	        	Nombre Producto:
	        </div>

	        <div class="col-sm-3">
	        	<input type="text" id="descPro" name="descPro" size="30" class="form-control" placeholder="Nombre Producto">
	        	<div id="suggestions"></div>
	        	<input type="hidden" id="idPro" name="idPro">
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
	        		for($i=0; $i<count($ar_unidad); $i++){
	        			echo "<option value=\"" . $ar_unidad[$i] . "\">" . $ar_unidad[$i] . "</option>";
	        		}
	        		echo "</select>";
	        	?>
	        </div>
	    </div>
	    
	    <div class="row" style="margin-top:5px;">
	        <div class="col-sm-2">
	        	Rubro:
	        </div>

	        <div class="col-sm-3">
	        	<?php 
				//$cSql = "select id,concat(if(productos='1','Producto',if(servicios='1','Servicio','Nulo')),'-',descrip) descrip_caja, productos, servicios from tec_rubros order by concat(if(productos='1','Producto',if(servicios='1','Servicio','Nulo')),'-',descrip), id";
				
	        	$cSql = "select id, descrip descrip_caja, productos, servicios from tec_rubros where productos='1' order by descrip";
				$result = $this->db->query($cSql)->result_array();
				$ar = array();
				//$ar[0] = "--seleccione--";
				$aru = $this->fm->conver_dropdown($result, 'id', 'descrip_caja', "--seleccione--");
				foreach($aru as $key => $value){
					$ar[$key] = $value;
				}
				echo form_dropdown('rubro',$ar,'','class="form-control tip" id="rubro" required="required"');
	        	?>
	        </div>
	    </div>

	    <div class="row" style="margin-top:5px;">
	        <div class="col-sm-2">
					<div class="col-sm-2" style="margin:20px;">
					<button class="btn btn-success" onclick="grabar_producto()">Grabar</button>
				</div>
			</div>
		</div>

	    <div class="row" style="margin-top:5px;">
	        <div id="pizarra1" class="col-sm-12">
	        	.
	        </div>
	        <a id="listado_i" href="<?= site_url('insumos/listar_insumos'); ?>" style="color:white">Listar</a>
	    </div>

	</div>
</div>
<script type="text/javascript">
	<?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        //echo "setTimeout('abrir_item_menu(4)',500);\n";
    ?>

	function grabar_producto(){
		var parametros = {
			descPro 	: document.getElementById('descPro').value,
			unidad 		: document.getElementById('unidad').value,
			idPro 		: document.getElementById('idPro').value,
			rubro		: document.getElementById('rubro').value
		}
		$.ajax({
			data 	: parametros,
			url 	:'<?= base_url('insumos/grabar_insumos') ?>',
			type 	:'get',
			success :function(response){
				
				var ar = JSON.parse(response)
				console.log(ar)
				var cad = ""
				
				if(ar.error == true){
					cad = "<div class=\"alert alert-danger\">"+ar.rpta+"</div>"
				}else{
					cad = "<div class='alert alert-success'>"+ar.rpta+"</div>"
					document.getElementById('unidad').value = ""
					document.getElementById('descPro').value = ""
				}
				//document.getElementById('pizarra1').innerHTML = cad
				
				toastr.options = {
				  "debug": false,
				  "positionClass": "toast-bottom-right",
				  "onclick": null,
				  "fadeIn": 200,
				  "fadeOut": 70,
				  "timeOut": 2000,
				  "extendedTimeOut": 700
				}

				//toastr.success("<br>" + ar.rpta)
				alert(ar.rpta)
				//setTimeout("document.getElementById('listado_i').click()",3000)
			}
		})
	}

    $('#descPro').keyup(function() {
        var query = $(this).val();
        if (query !== '' && query.length>1) {
			$.ajax({
				url: '<?= base_url("inventarios/autocomplete") ?>',
				method: 'get',
				data: { query: query },
				success: function(data) {
			  		$('#suggestions').html(data);
				}
			});
        } else {
        	$('#suggestions').empty();
        }
    });

</script>