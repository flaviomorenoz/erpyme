<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
// "id", "nombres", "fec_ini", "fec_fin", "activo", "sueldo"
$id         = isset($id) ? $id : ""; 
$nombres    = isset($nombres) ? $nombres : "";
$fec_ini    = isset($fec_ini) ? $fec_ini : "";
$fec_fin    = isset($fec_fin) ? $fec_fin : "";
$activo     = isset($activo) ? $activo : "";
$sueldo     = isset($sueldo) ? $sueldo : "";
if(strlen($id)>0){ $modo = "update";}else{ $modo = "insert";}
?>

<section class="content">
<?= form_open_multipart("recursos/agregar_contratos", 'class="validation" id="form_contratos"'); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-10 col-md-9 col-lg-7">
            <p style="text-align:center;font-size:16px;">
                <?= $engrama ?>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-5 col-lg-3">
            <div class="form-group">
                <label>Personal:</label>
                <?php
                    $query  = $this->recursos_model->personal_sin_contrato();
                    $ar     = $this->fm->conver_dropdown($query->result_array(), "id", "nombres"); 
                    echo form_dropdown('id_personal', $ar, $id_personal, 'class="form-control tip" id="id_personal" required="required"');
                ?>
                <input type="hidden" name="modo" id="modo" value="<?= $modo ?>">
                <input type="hidden" name="id" id="id" value="<?= $id ?>">
            </div>
        </div>
    </div>

    <div class="row">    
        <div class="col-xs-6 col-sm-6 col-md-5 col-lg-3">
            <div class="form-group">
                <label>Fec_inicio:</label>
                <?php
                    $ar = array(
                       "name"  =>"fec_ini",
                       "id"    =>"fec_ini",
                       "type"  =>"date",
                       "value" => $fec_ini,
                       "class" =>"form-control tip"
                    );
                    echo form_input($ar);
                ?>
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-5 col-lg-3">
            <div class="form-group">
                <label>Fec_Finalizacion:</label>
                <?php
                    $ar = array(
                       "name"  =>"fec_fin",
                       "id"    =>"fec_fin",
                       "type"  =>"date",
                       "value" => $fec_fin,
                       "class" =>"form-control tip"
                    );
                    echo form_input($ar);
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-5 col-md-4 col-lg-2">
            <div class="form-group">
                <label>Sueldo</label>
                <?= form_input('sueldo', $sueldo, 'class="form-control tip" id="sueldo"'); ?>
            </div>
        </div>
        <div class="col-xs-6 col-sm-5 col-md-4 col-lg-2">
            <div class="form-group">
                <label>Activo:</label>
                <?php
                    $ar = array("1"=>"Activo","0"=>"Desactivo");
                    echo form_dropdown('activo', $ar, $activo, 'class="form-control tip" id="activo" required="required"');
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-5 col-md-4 col-lg-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Grabar</button>
            </div>
        </div>
    </div>

<?= form_close(); ?>
</section>

