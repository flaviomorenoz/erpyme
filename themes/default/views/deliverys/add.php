

<div class="row">

	<div class="col-sm-6" style="padding-left: 20px; padding-top:20px;">
		
		<?php echo form_open_multipart("deliverys/add", 'class="validation" id="form_deliverys"'); ?>

		<div class="form-group">
		Nombre:
		<?php
			 $ar = array(
			   "name"  =>"nombre_delivery",
			   "id"    =>"nombre_delivery",
			   "type"  =>"text",
			   "value" => "",
			   "required" => "required",
			   "class" =>"form-control tip"
			 );
			 echo form_input($ar);
		?>
		</div>

		<?php
			 $ar = array(
			   "name"  => "btn_sub",
			   "id"    => "btn_sub",
			   "type"  => "submit",
			   "value" => "Submit",
			   "class" => "btn btn-success"
			 );
			 echo form_input($ar);
		?>

		<?php echo form_close(); ?>

	</div>
	
</div>
