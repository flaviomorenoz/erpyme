<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-11" style="margin-left:30px; margin-top:10px; border-style:solid; border-color:rgb(70,120,180); border-width:2px; border-radius:10px;">
		<?= $tbl_calidad ?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-11" style="margin-left:30px; margin-top:10px; border-style:solid; border-color:rgb(70,120,180); border-width:2px; border-radius:10px;">
		<?= $tbl_detalles ?>
	</div>
</div>
<!-- Button trigger modal --> 

<button type="button" id="ver_datelle_id" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong"> 
  Launch demo modal 
</button> 

<button type="button" id="ver_rpta_id" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong"> 
  Launch demo modal 
</button> 
 
		<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="background-color: white!important;"> 

		  <div class="modal-dialog" role="document"> 
		    <div class="modal-content"> 
		      <div class="modal-header"> 
		        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5> 
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
		          <span aria-hidden="true">&times;</span> 
		        </button> 
		      </div> 

		      <div class="modal-body" id="modal_21" style="background-color: white;"> 
		      </div> 

		      <div class="modal-footer"> 
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> 
		        <button type="button" class="btn btn-primary">Save changes</button> 
		      </div> 

		    </div> 
		  </div> 
		</div>
	
		<div class="modal fade" id="exampleModalLong2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="background-color: white!important;"> 

		  <div class="modal-dialog" role="document"> 
		    <div class="modal-content"> 
		      <div class="modal-header"> 
		        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5> 
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
		          <span aria-hidden="true">&times;</span> 
		        </button> 
		      </div> 

		      <div class="modal-body" id="modal_22" style="background-color: white;"> 
		      </div> 

		      <div class="modal-footer"> 
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> 
		        <button type="button" class="btn btn-primary">Save changes</button> 
		      </div> 

		    </div> 
		  </div> 
		</div>

<script type="text/javascript">
	function ver(id){
		$.ajax({
			url 	: "<?= base_url("permisos/ver_det/") ?>" + id,
			type 	: "get",
			success : function(res){
				document.getElementById("modal_21").innerHTML = res
				document.getElementById('ver_datelle_id').click()
			}
		})
	}

	function ver_rpta(id){
		$.ajax({
			url 	: "<?= base_url("permisos/ver_rpta/") ?>" + id,
			type 	: "get",
			success : function(res){
				document.getElementById("modal_22").innerHTML = res
				document.getElementById('ver_rpta_id').click()
			}
		})
	}

	function ejecutar(id){
		$.ajax({
			url 	: "<?= base_url("permisos/envio_masivo_tarea_unica/") ?>" + id,
			type 	: "get",
			success : function(res){
				alert("Procesado "+id);
			}
		})
	}

</script>

