<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
    $cSql   = "select a.*, c.descrip as forma_pago from tec_sales a 
        left join tec_payments b on a.id = b.sale_id
        left join tec_forma_pagos c on b.paid_by = c.forma_pago where a.id={$inv->id}";
    $query  = $this->db->query($cSql);
    $result = $query->result();
    $forma_pago = "";
    foreach($result as $r){
        $total          = $r->grand_total;
        $product_tax    = $r->product_tax;
        $forma_pago     .= $r->forma_pago . ", ";
        $gravadas       = $r->total;
        $total_tax      = $r->total_tax;
        
    }
    //echo "total : $total, product_tax: $product_tax <br>";
    //$data_qr = "RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION | TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE | CODIGO HASH |";

    $cSql = "select c.descrip as forma_pago, 
    '20551286429' as ruc,
    a.tipoDoc, 
    a.serie, a.correlativo, a.total_tax as total_igv, 
    a.grand_total as monto_total_comprobante,
    date_format(a.date, '%d/%m/%Y') fecha,
    if(a.tipoDoc = 'Boleta','1','6') tipo_doc_cus,
    if(a.tipoDoc = 'Boleta',d.cf1,d.cf2) ruc_cus,
    a.codigo_hash
    from tec_sales a 
    left join tec_payments b on a.id = b.sale_id
    left join tec_forma_pagos c on b.paid_by = c.forma_pago 
    left join tec_customers d on a.customer_id = d.id 
    where a.id={$inv->id}";

    // DNI = 1, RUC = 6 (en tip_doc_cus)

    $query = $this->db->query($cSql);
    $result = $query->result();
    foreach($result as $r){
        $RUC            = $r->ruc;
        $TIPO           = $this->fm->obtener_codigo_doc_sunat($r->tipoDoc);
        $SERIE          = $r->serie;
        $NUMERO         = substr("000000" . $r->correlativo,-6);
        $MTO_IGV        = number_format($r->total_igv,2,".","");
        $MTO_COMPRO     = number_format($r->monto_total_comprobante,2,".","");
        $FECHA          = $r->fecha;
        $TIPO_DOC       = $r->tipo_doc_cus;
        $NRO_DOC        = $r->ruc_cus;
        $COD_HASH       = $r->codigo_hash;
    }

    $data_qr = "{$RUC}|{$TIPO}|{$SERIE}|{$NUMERO}|{$MTO_IGV}|{$MTO_COMPRO}|{$FECHA}|{$TIPO_DOC}|{$NRO_DOC}|{$COD_HASH}";
?>
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
                                    </p>
                                    <p style="font-size:14px;font-weight:bold;">
                                        WhatsApp 970 343 471
                                    </p>
                                    <p><?php echo nl2br($store->receipt_header); ?></p>
                                    <p style="padding: 0.5rem;border-top: 1px dotted; margin-bottom:0px;">
                                        <!--<?= lang('blv'); ?>-->
                                        <?php 
                                            if ($tipoDoc == 'Factura'){
                                                echo '<b>FACTURA ELECTRONICA</b>';
                                            }elseif($tipoDoc == 'Boleta'){
                                                echo '<b>BOLETA ELECTRONICA</b>';
                                            }elseif($tipoDoc == 'Nota_de_credito'){
                                                echo '<b>NOTA DE CREDITO</b>';
                                            }elseif($tipoDoc == 'Nota_de_debito'){
                                                echo '<b>NOTA DE DEBITO</b>';
                                            }
                                        ?> 
                                    </p>
                                <?php } ?>
                            </div>
                            <div style="text-transform: uppercase;">
                                <span><?= $this->tec->hrld($inv->date); ?></span>
                                <span style="float: right">
                                    <?php 
                                        $result = $this->db->select("serie, correlativo")
                                            ->where("id",$inv->id)
                                            ->get('sales')->result();

                                        foreach($result as $r){
                                            $serie      = $r->serie;
                                            $correlativo = $r->correlativo;
                                            echo $serie . "-" . str_pad($correlativo, '4', '0', STR_PAD_LEFT); 
                                        }
                                    ?>
                                </span>
                            </div>
                            <div style="text-transform: uppercase; border-bottom: 1px dashed;">
                                
                                <?php 
                                    if($tipoDoc == 'Factura'){
                                        echo "RUC : " . $nombre_docu;
                                    }else{
                                        //echo lang("ccf1").'        : '; = $inv->customer_id != '2' ? $inv->cf1 : '';
                                        echo "DNI : " . $nombre_docu;
                                    }
                                ?>
                                <?=  "&nbsp;&nbsp;" . substr($inv->customer_name,0,23) ?>
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
                                    <td colspan="2" class="text-right"><?= $this->tec->formatMoney($gravadas); ?></td>
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
                                if ($total_tax != 0) {
                                    echo '<tr><td colspan="2" style="text-align: left">' . lang("order_tax") . '</td><td colspan="2" class="text-right">' . $this->tec->formatMoney($inv->total_tax) . '</td></tr>';
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
                                        <td class="text-right"><?= $this->tec->formatMoney($gravadas); ?></td>
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
                                    if ($total_tax != 0) {
                                        echo '<tr><td  style="text-align: left">' . lang("order_tax") . '</td><td class="text-right">' . $this->tec->formatMoney($inv->total_tax) . '</td></tr>';
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
                        </div>
                        <p style="border-bottom: 1px dashed; border-top: 1px dashed;padding: 0.5rem;">COBRADO POR: CAJA</p>
                        <p>FORMA DE PAGO: <?= $forma_pago ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>