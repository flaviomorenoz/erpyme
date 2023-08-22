<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					
					<?php echo form_open("customers/add");?>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="code"><?= $this->lang->line("name"); ?></label>
							<?= form_input('name', set_value('name'), 'class="form-control input-sm" id="name"'); ?>
						</div>

						<div class="form-group">
							<label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
							<?= form_input('email', set_value('email'), 'class="form-control input-sm" id="email_address"'); ?>
						</div>

						<div class="form-group">
							<label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
							<?= form_input('phone', set_value('phone'), 'class="form-control input-sm" id="phone"');?>
						</div>

						<div class="form-group">
							<label class="control-label" for="cf1"><?= $this->lang->line("ccf1"); ?></label>
							<?= form_input('cf1', set_value('cf1'), 'class="form-control input-sm" id="cf1"'); ?>
						</div>

						<div class="form-group">
							<label class="control-label" for="cf2"><?= $this->lang->line("ccf2"); ?></label>
							<?= form_input('cf2', set_value('cf2'), 'class="form-control input-sm" id="cf2"');?>
						</div>


						<div class="form-group">
							<?php echo form_submit('add_customer', $this->lang->line("add_customer"), 'class="btn btn-primary"');?>
						</div>
					</div>
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	function validar_customers(){
		var cf1 = document.getElementById("cf1").value
		var cf2 = document.getElementById("cf2").value
		var len_cf1 = cf1.length
		var len_cf2 = cf2.length
		if (len_cf1 > 0){
			if(len_cf1 != 8){
				alert("Dni debe tener 8 digitos")
				return false
			}
		}
		if (len_cf2 > 0){
			if(len_cf2 != 11){
				alert("Ruc debe tener 11 digitos")
				return false
			}
		}
		document.getElementById("form1").submit()
	}
</script>