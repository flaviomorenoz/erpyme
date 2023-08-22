<div class="row">
	<div class="col-xs-10 col-sm-8 col-md-6 col-lg-4" style="padding-left:30px;">
	
		<?php
			$cSql = "select * from tec_moduls where id = $modulo_id";
			$modulo = trim($this->db->query($cSql)->row()->name);
			echo "<h3 style=\"text-align:center\">MODULO DE $modulo</h3>\n";
		?>

		<table class="table table-responsive">
			<header>
				<tr>
					<th>Perfiles</th>
					<th>Accion</th>
				</tr>
			</header>
			<tbody>
				<?php
					foreach($query_groups->result() as $r){
						$group = trim($r->id);
						echo "<tr>";
						echo $this->fm->celda($r->name,1);
						
						// ,array( trim($r->name) )
						
						$cSql 		= "select * from tec_permisos where modulo='{$modulo}' and group_id = {$group}";
						//echo $cSql . "<br>";
						$permiso 	= $this->db->query($cSql)->row()->permiso;
						$permiso 	= ($permiso=="" ? "APROBADO" : $permiso);

						//echo "Permiso:" . $permiso . "<br>";
						
						$ar 		= array('APROBADO'=>'APROBADO', 'DENEGADO'=>'DENEGADO');
						//$result 	= $this->db->query("select a.* from proveedores a order by a.nombre")->result_array();
						//$ar 		= $this->fm->conver_dropdown($result, "dni", "nombre", array(''=>'Seleccione'));
						$obj 		= form_dropdown('permi',$ar,$permiso,'class="form-control tip" id="permi" required="required" onclick=""');

						echo $this->fm->celda($obj);
						echo "</tr>";
					}
				?>
			</tbody>
		</table>

	</div>
</div>
<div class="row">
	<div class="col-xs-10 col-sm-8 col-md-6 col-lg-4" style="padding-left:30px; text-align: center;">
		<button type="submit" class="btn btn-danger">Guardar</button>
	</div>
</div>