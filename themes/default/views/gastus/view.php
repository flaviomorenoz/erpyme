<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header modal-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <button type="button" class="close mr10" onclick="window.print();"><i class="fa fa-print"></i></button>
            <h4 class="modal-title" id="myModalLabel">
                <?= lang('purchase').' # '.$purchase->id; ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="col-xs-2"><?= lang('date'); ?></td>
                                    <td class="col-xs-10"><?= $purchase->date; ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2"><?= lang('reference'); ?></td>
                                    <td class="col-xs-10"><?= $this->fm->obtener_nombre_doc($purchase->reference) . " " . $purchase->nroDoc; ?></td>
                                </tr>
                                <?php
                                if ($purchase->attachment) {
                                    ?>
                                    <tr>
                                        <td class="col-xs-2"><?= lang('attachment'); ?></td>
                                        <td class="col-xs-10"><a href="<?=base_url('uploads/'.$purchase->attachment);?>"><?= $purchase->attachment; ?></a></td>
                                    </tr>
                                    <?php
                                }
                                if ($purchase->note) {
                                    ?>
                                    <tr>
                                        <td class="col-xs-2"><?= lang('note'); ?></td>
                                        <td class="col-xs-10"><?= $purchase->note; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="table-responsive">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
