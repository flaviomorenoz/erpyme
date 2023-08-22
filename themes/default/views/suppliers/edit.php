<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-body">
          <?php echo form_open("suppliers/edit/".$supplier->id);?>

          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="code"><?= $this->lang->line("name"); ?></label>
              <?= form_input('name', set_value('name', $supplier->name), 'class="form-control input-md" id="name"'); ?>
            </div>

            <div class="form-group">
              <label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
              <?= form_input('email', set_value('email', $supplier->email), 'class="form-control input-md" id="email_address"'); ?>
            </div>

            <div class="form-group">
              <label class="control-label" for="contact"><?= $this->lang->line("contact"); ?></label>
              <?= form_input('contact', set_value('contact', $supplier->contact), 'class="form-control input-md" id="contact"');?>
            </div>

            <div class="row">
              <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                  <label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
                  <?= form_input('phone', set_value('phone', $supplier->phone), 'class="form-control input-md" id="phone"');?>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                  <label class="control-label" for="phone2"><?= $this->lang->line("phone2"); ?></label>
                  <?= form_input('phone2', set_value('phone2', $supplier->phone2), 'class="form-control input-md" id="phone2"');?>
                </div>
              </div>
            </div>

            <!--<div class="form-group">
              <label class="control-label" for="cf1"><?= $this->lang->line("scf1"); ?></label>
              <?= form_input('cf1', set_value('cf1', $supplier->cf1), 'class="form-control input-md" id="cf1"'); ?>
            </div>-->

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="control-label" for="cf2"><?= $this->lang->line("scf2"); ?></label>
                  <?= form_input('cf2', set_value('cf2', $supplier->cf2), 'class="form-control input-md" id="cf2"');?>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label class="control-label" for="activo">Activo</label>
                  <?php
                    $ar = array('1'=>'Activo','0'=>'Inactivo');
                    echo form_dropdown('activo',$ar, set_value('activo', $supplier->activo),'class="form-control tip" id="activo" required="required"');
                  ?>
                </div>
              </div>
            </div>

            <div class="form-group">
              <?php echo form_submit('edit_supplier', "Guardar", 'class="btn btn-primary"');?>
            </div>
          </div>
          <?php echo form_close();?>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(10)',500);\n";
    ?>  
</script>
