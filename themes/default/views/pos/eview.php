<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>
<?php
if ($modal) {
?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <?php
            } else {
            ?><!doctype html>
            <html<?= $Settings->rtl ? ' dir="rtl"' : ''; ?>>
            <head>
                <meta charset="utf-8">
                <title><?= $page_title . " " . lang("no") . " " . $inv->id; ?></title>
                <base href="<?= base_url() ?>"/>
                <meta http-equiv="cache-control" content="max-age=0"/>
                <meta http-equiv="cache-control" content="no-cache"/>
                <meta http-equiv="expires" content="0"/>
                <meta http-equiv="pragma" content="no-cache"/>
                <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
                <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
                <style type="text/css" media="all">
                    body { color: #000; }
                    #wrapper { max-width: 350px; margin: 0 auto; padding-top: 20px; }
                    .btn { margin-bottom: 5px; }
                    .table { border-radius: 3px; }
                    .table th { background: #f5f5f5; }
                    .table th, .table td { vertical-align: middle !important; }
                    h3 { margin: 5px 0; }

                    @media print {
                        .no-print { display: none; }
                        #wrapper { width: 100%; min-width: 250px; margin: 0 auto; }
                    }
                    <?php if($Settings->rtl) { ?>
                    .text-right { text-align: left; }
                    .text-left { text-align: right; }
                    tfoot tr th:first-child { text-align: left; }
                    <?php } else { ?>
                    tfoot tr th:first-child { text-align: right; }
                    <?php } ?>
                </style>
            </head>
            <body>
            <?php
            }
            ?>
            <div id="wrapper">
                <div id="receiptData" style="width: auto; max-width: 580px; min-width: 250px; margin: 0 auto;">
                    <div class="no-print">
                        <?php if ($message) { ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= is_array($message) ? print_r($message, true) : $message; ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div id="receipt-data">
                        <div>
                            <div style="text-align:center;text-transform: uppercase;">
                                <?php if ($store) { ?>
                                    <br>
                                    <p style="text-align: center;">
                                        <strong><?php echo $store->name; ?></strong><br>
                                        <?= $store->address2; ?><br>
                                        <?= $store->address1; ?><br>
                                        <?= $store->state.' - '.$store->city; ?><br>
                                        <?= 'RUC : '.$store->code; ?><br>
                                        <?= 'TLF. '.$store->phone; ?><br>
                                    </p>
                                    <p><?php echo nl2br($store->receipt_header); ?></p>
                                    <p style="padding: 0.5rem;border-top: 1px dashed; border-bottom: 1px dashed;">
                                        <!--<?= lang('blv'); ?>-->
                                        <?php 
                                            if ($tipoDoc == 'Factura'){
                                                echo 'FACTURA ELECTRONICA';
                                            }else{
                                                echo 'BOLETA DE VENTA';
                                            }
                                        ?> 
                                    </p>
                                <?php } ?>
                            </div>
                            <div style="text-transform: uppercase;">
                                <span><?= $this->tec->hrld($inv->date); ?></span>
                                <span style="float: right">
                                    B001 - <?php echo str_pad($inv->id, '4', '0',STR_PAD_LEFT); ?>
                                </span>
                            </div>
                            <div style="text-transform: uppercase; border-bottom: 1px dashed;">
                                <?= lang("sale_no_ref").' : '; ?><?= $inv->customer_id != '2' ? $inv->customer_name : '';?>
                                <br>
                                <?= lang("ccf1").'        : '.$customer->cf1; ?>
                            </div>
                            <div style="clear:both;"></div>
                            <?php if ($modal) { ?>
                            <table class="table table-striped table-condensed" style="text-transform: uppercase;">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 50%; border-bottom: 2px solid #ddd;"><?=lang('description');?></th>
                                    <th class="text-center" style="width: 12%; border-bottom: 2px solid #ddd;"><?=lang('quantity');?></th>
                                    <th class="text-center" style="width: 24%; border-bottom: 2px solid #ddd;"><?=lang('price');?></th>
                                    <th class="text-center" style="width: 26%; border-bottom: 2px solid #ddd;"><?=lang('subtotal');?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $tax_summary = array();
                                foreach ($rows as $row) {
                                    echo '<tr><td>' . $row->product_name .'</td>';
                                    echo '<td style="text-align:center;">' . $this->tec->formatQuantity($row->quantity) . '</td>';
                                    echo '<td class="text-right">';
                                    echo $this->tec->formatMoney($row->net_unit_price + ($row->item_tax / $row->quantity)) . '</td><td class="text-right">' . $this->tec->formatMoney($row->subtotal) . '</td></tr>';
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right">OP. EXONERADAS</td>
                                    <td colspan="2" class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">OP. INAFECTAS</td>
                                    <td colspan="2" class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right"><?= lang("total"); ?></td>
                                    <td colspan="2" class="text-right"><?= $this->tec->formatMoney($inv->total - $inv->product_tax); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">OP. GRATUITAS</td>
                                    <td colspan="2" class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">I.S.C</td>
                                    <td colspan="2" class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">OTROS CARGOS</td>
                                    <td colspan="2" class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                </tr>
                                <?php
                                if ($inv->order_tax != 0) {
                                    echo '<tr><td colspan="2"  class="text-right">' . lang("order_tax") . '</td><td colspan="2" class="text-right">' . $this->tec->formatMoney($inv->order_tax) . '</td></tr>';
                                }
                                if ($inv->total_discount != 0) {
                                    echo '<tr><td colspan="2"  class="text-right">' . lang("order_discount") . '</td><td colspan="2" class="text-right">' . $this->tec->formatMoney($inv->total_discount) . '</td></tr>';
                                }

                                if ($Settings->rounding) {
                                    $round_total = $this->tec->roundNumber($inv->grand_total, $Settings->rounding);
                                    $rounding = $this->tec->formatDecimal($round_total - $inv->grand_total);
                                    ?>
                                    <tr>
                                        <td colspan="2"  class="text-right"><?= lang("rounding"); ?></td>
                                        <td colspan="2" class="text-right"><?= $this->tec->formatMoney($rounding); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"  class="text-right"><?= lang("grand_total"); ?></td>
                                        <td colspan="2" class="text-right"><?= $this->tec->formatMoney($inv->grand_total + $rounding); ?></td>
                                    </tr>
                                <?php
                                } else {
                                    $round_total = $inv->grand_total;
                                ?>
                                    <tr>
                                        <td colspan="2"  class="text-right"><?= lang("grand_total"); ?></td>
                                        <td colspan="2" class="text-right"><?= $this->tec->formatMoney($inv->grand_total); ?></td>
                                    </tr>
                                    <?php
                                }
                                if ($inv->paid < $round_total) { ?>
                                    <tr>
                                        <td colspan="2"  class="text-right"><?= lang("paid_amount"); ?></td>
                                        <td colspan="2" class="text-right"><?= $this->tec->formatMoney($inv->paid); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right"><?= lang("due_amount"); ?></td>
                                        <td colspan="2" class="text-right"><?= $this->tec->formatMoney($inv->grand_total - $inv->paid); ?></td>
                                    </tr>
                                <?php } ?>
                                </tfoot>
                            </table>
                            <?php }else{ ?>
                                <style type="text/css">
                                    table tfoot tr th {
                                        background: white !important;
                                        border-top: none !important;
                                    }
                                    .table-condensed>tfoot>tr>td {padding: 2px}
                                    .table>tfoot>tr>td, .table>tbody>tr>td {border: none}
                                    .table>tfoot {border-top: 1px dashed;}
                                </style>
                            <table class="table table-striped table-condensed" style="text-transform: uppercase; margin-bottom: 1rem">
                                <tbody>
                                <?php
                                    $tax_summary = array();
                                    foreach ($rows as $row) { ?>
                                        <tr style="background: white">
                                            <td>
                                                <?= $row->product_name.'<br>'; ?>
                                                <span style="padding-left: 5rem;"><?= $this->tec->formatQuantity($row->quantity).'x'.$this->tec->formatMoney($row->net_unit_price + ($row->item_tax / $row->quantity)) ; ?></span>
                                            </td>
                                            <td class="text-right">
                                                <br>
                                                <?= $this->tec->formatMoney($row->subtotal); ?>
                                            </td>
                                        </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>OP. EXONERADAS</td>
                                        <td class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">OP. INAFECTAS</td>
                                        <td class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left" ><?= lang("total"); ?></td>
                                        <td class="text-right"><?= $this->tec->formatMoney($inv->total - $inv->product_tax); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left" >OP. GRATUITAS</td>
                                        <td class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left" >I.S.C</td>
                                        <td class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">OTROS CARGOS</td>
                                        <td class="text-right"><?= $this->tec->formatMoney(0.00) ?></td>
                                    </tr>
                                    <?php
                                    if ($inv->order_tax != 0) {
                                        echo '<tr><td  style="text-align: left">' . lang("order_tax") . '</td><td class="text-right">' . $this->tec->formatMoney($inv->order_tax) . '</td></tr>';
                                    }
                                    if ($inv->total_discount != 0) {
                                        echo '<tr><td style="text-align: left">' . lang("order_discount") . '</td><td  class="text-right">' . $this->tec->formatMoney($inv->total_discount) . '</td></tr>';
                                    }

                                    if ($Settings->rounding) {
                                        $round_total = $this->tec->roundNumber($inv->grand_total, $Settings->rounding);
                                        $rounding = $this->tec->formatDecimal($round_total - $inv->grand_total);
                                        ?>
                                        <tr>
                                            <td style="text-align: left"><?= lang("grand_total"); ?></td>
                                            <td class="text-right"><?= $this->tec->formatMoney($inv->grand_total + $rounding); ?></td>
                                        </tr>
                                    <?php
                                    } else {
                                        $round_total = $inv->grand_total;
                                    ?>
                                        <tr>
                                            <td style="text-align: left"><?= lang("grand_total"); ?></td>
                                            <td class="text-right"><?= $this->tec->formatMoney($inv->grand_total); ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tfoot>
                            </table>
                            <p style="margin: 0; border-top: 1px dashed;padding: 0.5rem;"><?= 'SON: '.$format->toInvoice($inv->grand_total + $rounding,2,'soles')?></p>
                            <p style="border-bottom: 1px dashed; border-top: 1px dashed;padding: 0.5rem;">COBRADO POR: CAJA</p>
                            <?php } ?>
                            <?php
                            if ($payments && $modal) {
                                echo '<table class="table table-striped table-condensed" style="margin-top:10px;"><tbody>';
                                foreach ($payments as $payment) {
                                    echo '<tr>';
                                    if (($payment->paid_by == 'cash' || $payment->paid_by == 'Yape' || $payment->paid_by == 'Plin') && $payment->pos_paid) {

                                        echo '<th>' . lang("amount") . ':<br>' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</th>';
                                        echo '<th>' . lang("paid_by") . ':<br>' . lang($payment->paid_by) . '</th>';
                                        echo '<th>' . lang("amount") . ':<br>' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</th>';
                                        echo '<th>' . lang("change") . ':<br>' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</th>';
                                    }
                                    if ($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe')  {
                                        echo '<th>' . lang("paid_by") . ':<br>' . lang($payment->paid_by) . '</th>';
                                        echo '<th>' . lang("amount") . ':<br>' . $this->tec->formatMoney($payment->pos_paid) . '</th>';
                                        echo '<th>' . lang("no") . ':<br>' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</th>';
                                        echo '<th>' . lang("name") . ':<br>' . $payment->cc_holder . '</th>';
                                    }
                                    if ($payment->paid_by == 'Cheque' || $payment->paid_by == 'cheque' && $payment->cheque_no) {
                                        echo '<th class="text-right">' . lang("paid_by") . ' :</th><td>' . lang($payment->paid_by) . '</td>';
                                        echo '<th class="text-right">' . lang("amount") . ' :</th><td>' . $this->tec->formatMoney($payment->pos_paid) . '</td>';
                                        echo '<th class="text-right">' . lang("cheque_no") . ' :</th><td>' . $payment->cheque_no . '</td>';
                                    }
                                    if ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                                        echo '<th>' . lang("paid_by") . ':<br>' . lang($payment->paid_by) . '</th>';
                                        echo '<th>' . lang("no") . ':<br>' . $payment->gc_no . '</th>';
                                        echo '<th>' . lang("amount") . ':<br>' . $this->tec->formatMoney($payment->pos_paid) . '</th>';
                                        echo '<th>' . lang("balance") . ':<br>' . ($payment->pos_balance > 0 ? $this->tec->formatMoney($payment->pos_balance) : 0) . '</th>';
                                    }
                                    if ($payment->paid_by == 'other' && $payment->amount) {
                                        echo '<th class="text-right">' . lang("paid_by") . ':<br>' . lang($payment->paid_by) . '</th>';
                                        echo '<th class="text-right">' . lang("amount") . ':<br>' . $this->tec->formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</th>';
                                        echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ' :</td><td>' . $payment->note . '</td>' : '';
                                    }
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                            }

                            ?>

                            <?= $inv->note ? '<p style="margin-top:10px; text-align: center;">' . $this->tec->decode_html($inv->note) . '</p>' : ''; ?>
                            <?php if (!empty($store->receipt_footer)) { ?>
                                <div class="well well-sm"  style="margin-top:10px;">
                                    <div style="text-align: center;"><?= nl2br($store->receipt_footer); ?></div>
                                </div>
                            <?php } ?>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <!-- start -->
                    <div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
                        <hr>
                        <?php if ($modal) { ?>
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group">
                                    <?php
                                    if ( ! $Settings->remote_printing) {
                                        echo '<a href="'.site_url('pos/print_receipt/'.$inv->id.'/0').'" id="print" class="btn btn-block btn-primary">'.lang("print").'</a>';
                                    } elseif ($Settings->remote_printing == 1) {
                                        echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                    } else {
                                        echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                    }
                                    ?>
                                </div>
                                <div class="btn-group" role="group">
                                    <a style="text-transform: initial;" class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                                </div>
                            </div>
                        <?php } else { ?>
                            <span class="pull-right col-xs-12">
                                <?php
                                if ( ! $Settings->remote_printing) {
                                    echo '<a href="'.site_url('pos/print_receipt/'.$inv->id.'/1').'" id="print" class="btn btn-block btn-primary">'.lang("print").'</a>';
                                    // echo '<a href="'.site_url('pos/open_drawer/').'" class="btn btn-block btn-default">'.lang("open_cash_drawer").'</a>';
                                } elseif ($Settings->remote_printing == 1) {
                                    echo '<button onclick="window.print();" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                } else {
                                    echo '<button onclick="return printReceipt()" class="btn btn-block btn-primary">'.lang("print").'</button>';
                                    // echo '<button onclick="return openCashDrawer()" class="btn btn-block btn-default">'.lang("open_cash_drawer").'</button>';
                                }
                                ?>
                            </span>
                            <span class="pull-left col-xs-12">
                                <a style="text-transform: initial;" class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a>
                            </span>
                            <span class="col-xs-12">
                                <a style="text-transform: initial;" class="btn btn-block btn-warning" href="<?= site_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
                            </span>
                        <?php } ?>
                        <div style="clear:both;"></div>
                    </div>
                    <!-- end -->
                </div>
            </div>
            <!-- start -->
            <?php
            if (!$modal) {
                ?>
                <script type="text/javascript">
                    var base_url = '<?=base_url();?>';
                    var site_url = '<?=site_url();?>';
                    var dateformat = '<?=$Settings->dateformat;?>', timeformat = '<?= $Settings->timeformat ?>';
                    <?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
                    var Settings = <?= json_encode($Settings); ?>;
                </script>
                <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
                <script src="<?= $assets ?>dist/js/libraries.min.js" type="text/javascript"></script>
                <script src="<?= $assets ?>dist/js/scripts.min.js" type="text/javascript"></script>
                <?php
            }
            ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#print').click(function (e) {
                        e.preventDefault();
                        var link = $(this).attr('href');
                        $.get(link);
                        return false;
                    });
                    $('#email').click(function () {
                        bootbox.prompt({
                            title: "<?= lang("email_address"); ?>",
                            inputType: 'email',
                            value: "<?= $customer->email; ?>",
                            callback: function (email) {
                                if (email != null) {
                                    $.ajax({
                                        type: "post",
                                        url: "<?= site_url('pos/email_receipt') ?>",
                                        data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: <?= $inv->id; ?>},
                                    dataType: "json",
                                        success: function (data) {
                                        bootbox.alert({message: data.msg, size: 'small'});
                                    },
                                    error: function () {
                                        bootbox.alert({message: '<?= lang('ajax_request_failed'); ?>', size: 'small'});
                                        return false;
                                    }
                                });
                                }
                            }
                        });
                        return false;
                    });
                });
            </script>
            <?php /* include FCPATH.'themes'.DIRECTORY_SEPARATOR.$Settings->theme.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'pos'.DIRECTORY_SEPARATOR.'remote_printing.php'; */ ?>
            <?php include 'remote_printing.php'; ?>
            <?php
            if ($modal) {
            ?>
        </div>
    </div>
</div>
<?php
} else {
    ?>
    <!-- end -->
    </body>
    </html>
    <?php
}
?>
