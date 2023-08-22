<div class="row">
	<div class="col-xs-12 col-sm-8 col-md-6 col-lg-4" style="margin-left:30px; margin-top:10px; border-style:none; border-color:rgb(70,120,180); border-width:2px; border-radius:10px;">
		
<?php
	$ar = array();
	$cSql = "select a.id, a.name, if(a.activo='1','Activo','-') activo
		from tec_moduls a 
		order by a.id";

	/*
	$cSql = "select x.id, x.name, 
		if(sum(x.admin)>0,'Aprobado','-') admin, 
		if(sum(x.staff)>0,'Aprobado','-') staff, 
		if(sum(x.supervisor),'Aprobado','-') supervisor,
		concat('<a href=\'permisos/editar/',x.id,'\'><i class=\'fa fa-edit\' title=\'Editar\'></i></a>') acciones
		from
		(
			select a.id, a.name, if(upper(b.name) = 'ADMIN', 1, 0) admin,
			if(upper(b.name) = 'STAFF', 1, 0) staff,
			if(upper(b.name) = 'SUPERVISOR', 1, 0) supervisor
			from tec_moduls a 
			left join tec_groups b on a.group_id = b.id
			where a.activo='1'
		) x
		group by x.name";*/

	// <i class='fa fa-edit'></i>

	$result 		= $this->db->query($cSql)->result_array();

	//$cols 			= array("name","admin","staff","supervisor","acciones");
	//$cols_titulos 	= array("Modulo","ADMIN","STAFF","SUPERVISOR","Acciones");
	//$ar_align 		= array("0","0","0","0","0");

	$cols 			= array("id","name","activo");
	$cols_titulos 	= array("id","name","activo");
	$ar_align 		= array("0","0","0");

	$ar_pie 		= $ar_align;
	echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
?>

	</div>
	
</div>
