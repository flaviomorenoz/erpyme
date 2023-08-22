<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
$nPos = strpos($cash_in_hand, ".");
$nLim = strlen($cash_in_hand . "");
$nCant_dec = $nLim - $nPos;

if($nCant_dec > 2){
    // Dejando 2 decimales
    $cash_in_hand = substr($cash_in_hand . "",0,$nLim - ($nCant_dec - 3));
}
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="well well-sm col-xs-12 col-sm-3">
                                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'open-register-form');
                                echo form_open_multipart("pos/open_register", $attrib); ?>
                                    <div class="form-group">
                                        <?= lang('cash_in_hand', 'cash_in_hand') ?>
                                        <?= form_input('cash_in_hand', $cash_in_hand, 'id="cash_in_hand" class="form-control" readonly'); ?>
                                    </div>

                                    <div class="form-group">
                                        <b><?= "Agrega un monto si deseas" ?></b>
                                        <?= form_input('cash_in_hand_adicional', 0, 'id="cash_in_hand_adicional" class="form-control" readonly'); ?>
                                    </div>

                                    <?php echo form_submit('open_register', lang('open_register'), 'class="btn btn-primary"'); ?>
                                <?php echo form_close(); ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>