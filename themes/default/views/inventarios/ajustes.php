<?php 
	echo form_open_multipart("inventarios/ajustes", 'class="validation" id="form1"');
	$inv1 = "";
?>
<div class="row">

    <div class="col-sm-5 col-md-4 col-lg-3" style="padding-left:50px; padding-top:15px;">
    	<?php
	    	/*$ar = array();
	    	$cSql = "select a.id, concat(b.state,'_',a.fecha) descrip
                from tec_maestro_inv a
                inner join tec_stores b on a.store_id = b.id
                order by a.id desc";
			$result = $this->db->query($cSql)->result_array();
			$ar = $this->fm->conver_dropdown($result, "id", "descrip", array(''=>'Seleccione'));
			echo form_dropdown('inv1',$ar,$inv1,'class="form-control tip" id="inv1" required="required" onclick=""');
			*/
			$ar = array();
	    	$cSql = "select a.id, concat(a.state) descrip
                from tec_stores a
                order by a.id desc";
			$result = $this->db->query($cSql)->result_array();
			$ar = $this->fm->conver_dropdown($result, "id", "descrip", array(''=>'---Tienda---'));
			echo form_dropdown('store_id',$ar,$store_id,'class="form-control tip" id="store_id" required="required" onclick=""');
		?>
    </div>

    <div class="col-sm-5 col-md-4 col-lg-3" style="padding-left:50px; padding-top:15px;">
    	<?php
			$ar = array();
			$ar = array("1"=>"Generar Stock"); // ,"2"=>"Sincerar Stock"
			//$ar = $this->fm->conver_dropdown($result, "id", "descrip", array(''=>'Seleccione'));
			echo form_dropdown('proceso',$ar,'1','class="form-control tip" id="proceso" required="required"');
		?>
    </div>

    <div class="col-sm-4 col-md-3 col-lg-2" style="padding-left:50px; padding-top:15px;">
        <button type="submit" class="btn btn-success">Ejecutar</button>
        <input type="hidden" name="modo" value="ejecutar">
    </div>

</div>
<?= form_close() ?>
