<div class="row" style="margin-left:10px">
		<div class="col-sm-3">
			<h2>Tipos de Gastos:</h2>
		</div>
		<div class="col-sm-3" style="margin-top:24px">
			<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_tipo">Agregar</button>
		</div>
</div>

<div class="row" style="margin-left:10px">
	<div class="col-xs-12 col-sm-4">
	<?php

		$result_ar = $this->gastus_model->tipo_gastos();

		$cols_titulos 	= array("Id","Descripcion","Comentario");
		$cols 			= array("id","descrip","comentario");
		$ar_align 		= array("1","1","1");
		$ar_pie 		= array("","","");

		echo $this->fm->crea_tabla_result($result_ar, $cols, $cols_titulos, $ar_align = array(), $ar_pie = array());
	?>
	</div>
</div>

<div class="row" style="margin-left:10px">
	<div class="col-sm-3">
		<h2>Sub-Tipos de gastos:</h2>
	</div>
	<div class="col-sm-3" style="margin-top:24px">
		<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_subtipo">Agregar</button>
	</div>
</div>

<div class="row" style="margin-left:10px">
	<div class="col-xs-12 col-sm-6">
	<?php
		$result_ar = $this->gastus_model->subtipo_gastos();

		$cols_titulos 	= array("Id","Tipo de gastos", "SubTipo de Gastos","Comentario");
		$cols 			= array("id","descrip","descrip1","comentario");
		$ar_align 		= array("1","1","1","1");
		$ar_pie 		= array("","","","");

		//echo "<button type=\"button\" class=\"btn btn-primary btn-sm\" data-toggle=\"modal\" data-target=\"#modal_tipo\">Agregar</button>";

		echo $this->fm->crea_tabla_result($result_ar, $cols, $cols_titulos, $ar_align = array(), $ar_pie = array());

	?>
	</div>
</div>

<!--<a href="<?= base_url("gastus/genera_correlativos_masivos") ?>">Genera Correlativo</a><br>
<a href="<?= base_url("gastus/generar_nro") ?>">Generar proximo Correlativo</a>-->

<div id="modal_subtipo" class="modal fade" role="dialog">
  <div id="oreo" class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background-color:orange">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Agregar Sub-Tipo</b></h4>
      </div>
      
      	<div class="modal-body">
	        <div class="col-md-8">

	            <div class="row">
	                <div class="col-sm-7" style="border-style:none; border-color:red;">
	                    <div class="form-group">
	                        <label for="">SubTipo de Gasto</label>
	                        <?php 
	                           $cSql = "select id, descrip, comentario from tec_tipo_gastos order by descrip";
	                           $result = $this->db->query($cSql)->result_array();
	                           $ar_p[""] = "--- Seleccione Tipo ---";
	                           foreach($result as $r){
	                                $ar_p[ $r["id"] ] = $r["descrip"];
	                           }

	                           echo form_dropdown('clasifica1',$ar_p,$clasifica1,'class="form-control tip" id="clasifica1" required="required"');
	                        ?>
	                    </div>
	                </div>
	            </div>

	            <div class="row" style="margin-top:5px;">
	                <div class="col-sm-7">
	                    <label>Nombre:</label>
	                    <input type="text" id="tipo" name="tipo" size="30" class="form-control" placeholder="Tipo">
	                </div>
	            </div>

	            <div class="row" style="margin-top:5px;">
	                <div class="col-sm-7">
	                    <label>Comentario:</label>
	                    <input type="text" id="comentario" name="comentario" size="70" class="form-control" placeholder="Comenta">
	                </div>
	            </div>

	        </div>
      	</div>
      
	    <div class="modal-footer">
	       <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="grabar_subtipo(document.getElementById('clasifica1').value,document.getElementById('tipo').value,document.getElementById('comentario').value)">Grabar</button>
	    </div>
    </div>

  </div>
</div>

<div id="modal_tipo" class="modal fade" role="dialog">
  <div id="oreo_tipo" class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background-color:orange">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Agregar Tipo</b></h4>
      </div>
      
      	<div class="modal-body">
	        <div class="col-md-8">

	            <div class="row" style="margin-top:5px;">
	                <div class="col-sm-7">
	                    <label>Nombre:</label>
	                    <input type="text" id="tipo2" name="tipo2" size="30" class="form-control" placeholder="Tipo">
	                </div>
	            </div>

	            <div class="row" style="margin-top:5px;">
	                <div class="col-sm-7">
	                    <label>Comentario:</label>
	                    <input type="text" id="comentario2" name="comentario2" size="70" class="form-control" placeholder="Comenta">
	                </div>
	            </div>

	        </div>
      	</div>
      
		    <div class="modal-footer">
		       <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="grabar_tipo(document.getElementById('tipo2').value,document.getElementById('comentario2').value)">Grabar</button>
		    </div>
      	
    </div>

  </div>
</div>

	<script>
		function grabar_subtipo(tipo_id1, descrip1, comentario1){
			$.ajax({
				data: {tipo_id: tipo_id1, descrip: descrip1, comentario: comentario1},
				type: "get",
				url : "<?= base_url("gastus/agregar_subtipo") ?>",
				success: function(res){
					alert(res)
					location.reload()
				}
			})
		}

		function grabar_tipo(descrip2, comentario2){
			$.ajax({
				data: {descrip: descrip2, comentario: comentario2},
				type: "get",
				url : "<?= base_url("gastus/agregar_tipo") ?>",
				success: function(res){
					alert(res)
					location.reload()
				}
			})
		}
	</script>
