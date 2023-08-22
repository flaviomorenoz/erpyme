<section style="margin:10px 0px 10px 10px">

	<?php echo form_open_multipart("products/print_barcodes", 'class="validation" id="form_compra"'); ?>

	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<label>Forma de Impresion:</label><br>
			<select name="eleccion" id="eleccion" class="form-control" onchange="mostrar(this.value)" required>
				<option value="">------Elija------</option>
				<option value="1">Codigo Individual</option>
				<option value="2">Todos los Codigos</option>
			</select>
		</div>

	</div>

	<div class="row" id="cod_individual" style="display:block; margin-top: 20px;">
		<div class="col-sm-3 col-lg-2" id="col_codigo" style="display:none">
			<label>Codigo a Imprimir</label>
			<?php
				$ar = $this->fm->conver_dropdown($query_codigos->result_array(), 'id', 'descrip');
				echo form_dropdown('codigo',$ar,'','class="form-control tip" id="codigo" required="required"');
			?>
		</div>
		<div class="col-sm-2 col-lg-1">
			<label>Nro Filas:</label>
			<input type="text" name="cantidad" id="cantidad" value="10" class="form-control">
		</div>
		<div class="col-sm-2 col-lg-1">
			<label>Nro Cols:</label>
			<input type="text" name="cantidad_cols" id="cantidad_cols" value="1" class="form-control">
		</div>
		<div class="col-sm-2 col-lg-1">
			<label>Ancho(px):</label>
			<input type="text" name="ancho" id="ancho" class="form-control" value="180">
		</div>
		<div class="col-sm-2 col-lg-1">
			<label>Alto(px):</label>
			<input type="text" name="alto" id="alto" class="form-control" value="60">
		</div>
		<div class="col-sm-3 col-lg-2">
			<label>Margin Top (px):</label>
			<input type="text" name="margin_top" id="margin_top" class="form-control" value="0">
		</div>
	</div>

	<div class="row" style="margin-top: 20px;">
		<div class="col-sm-2 col-lg-1">
			<label>&nbsp;</label><br>
			<button type="submit" class="btn btn-primary">Vista Previa</button>
		</div>
	</div>

	<?php echo form_close() ?>

</section>

<script type="text/javascript">
	function mostrar(rpta){
		if(rpta == '1'){
			//document.getElementById("cod_individual").style.display = "block"
			//document.getElementById("cod_individual2").style.display = "none"
			document.getElementById("col_codigo").style.display = "block"
		}else{
			//document.getElementById("cod_individual").style.display = "none"
			//document.getElementById("cod_individual2").style.display = "block"
			document.getElementById("col_codigo").style.display = "none"
		}
	}
</script>