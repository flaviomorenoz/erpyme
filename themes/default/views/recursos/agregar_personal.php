<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
$modo       = isset($modo) ? $modo : "insert";
$id         = isset($id) ? $id : ""; 
$tip_doc    = isset($tipo_doc) ? $tipo_doc : "";
$nombres    = isset($nombres) ? $nombres : "";
$apellidos  = isset($apellidos) ? $apellidos : "";
$documento  = isset($documento) ? $documento : "";
$phone      = isset($phone) ? $phone : "";
$store_id   = isset($store_id) ? $store_id : "";
$activo     = isset($activo) ? $activo : "1";
?>

<section class="content">
<?= form_open_multipart("recursos/agregar_personal", 'class="validation" id="form_compra"'); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-10 col-md-9 col-lg-7">
            <p style="text-align:center;font-size:16px;">
                <?= $engrama ?>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
            <div class="form-group">
                <label>Dni:</label>
                <?php
                    $ar = array('DNI'=>'DNI','PTP'=>'PTP','RUC'=>'RUC');
                    echo form_dropdown('tip_doc', $ar, $tip_doc, 'class="form-control tip" id="tip_doc" required="required"');
                ?>
                
            </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
            <div class="form-group">
                <label>Documento:</label>
                <?= form_input('documento', $documento, 'class="form-control tip" id="documento"'); ?>
            </div>
        </div>

        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
            <div class="form-group">
                <label>Tienda:</label>
                <?php 
                    $result = $this->db->query("select id, state from tec_stores where activo = '1'")->result_array();
                    $ar = $this->fm->conver_dropdown($result, "id", "state"); // , array(''=>'seleccione Tda')
                    echo form_dropdown('store_id', $ar, $store_id, 'class="form-control tip" id="store_id" required="required"'); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-5 col-lg-3">
            <div class="form-group">
                <label>Nombres</label>
                <?= form_input('nombres', $nombres, 'class="form-control tip" id="nombres"'); ?>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-5 col-lg-3">
            <div class="form-group">
                <label>Apellidos:</label>
                <?= form_input('apellidos', $apellidos, 'class="form-control tip" id="apellidos"'); ?>
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-5 col-lg-3">
            <div class="form-group">
                <label>Fono:</label>
                <?= form_input('phone', $phone, 'class="form-control tip" id="phone"'); ?>
                <input type="hidden" name="id" id="id" value="<?= $id ?>">
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
        <div class="col-xs-8 col-sm-6 col-md-5 col-lg-3">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Grabar</button>
            </div>
        </div>
    </div>
    <input type="hidden" name="modo" id="modo" value="<?= $modo ?>">

<?= form_close(); ?>
</section>

