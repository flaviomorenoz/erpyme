<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>
<?php
	// type, name, code, barcode_symbology, category, product_tax, tax as tax_method, alert_quantity
	$type = isset($type) ? $type : 'standard';
	$name = isset($product) ? $product->name : '';
	$code = isset($product) ? $product->code : '';
	$category_id = isset($product) ? $product->category_id : '';
	$product_tax  = isset($product) ? $product->tax : 10;
    $unidad         = isset($product) ? $product->unidad : '';
    //echo("unidad:".$product->unidad."<br>");
    //die("Unidad:".$unidad);
    $modo       = isset($modo) ? $modo : 'I';
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="col-lg-12">
                        <?= form_open_multipart("products/agregar", 'class="validation"');?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-8" style="border-style:solid;border-color:gray;border-radius: 10px">
                                <div class="form-group">
                                    <?= lang('type', 'type'); ?>
                                    <?php $opts = array('standard' => lang('standard'), 'combo' => lang('combo'), 'service' => lang('service')); ?>
                                    <?= form_dropdown('type', $opts, $type, 'class="form-control tip select2" id="type"  required="required" style="width:100%;"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('name', 'name'); ?>
                                    <?= form_input('name', $name, 'class="form-control tip" id="name"  required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('code', 'code'); ?> <?= lang('can_use_barcode'); ?>
                                    <?= form_input('code', $code, 'class="form-control tip" id="code"  required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('Unidad', 'Unidad'); ?>
                                    <?php  
                                        $ar         = array(); 
                                        $result     = $this->db->query("select * from tec_unidades where comunes='1'")->result_array(); 
                                        $ar         = $this->fm->conver_dropdown($result, "codigo", "codigo", array(''=>'Seleccione')); 
                                        echo form_dropdown('unidad',$ar,$unidad,'class="form-control tip" id="unidad" required="required" onclick=""'); 
                                    ?>
                                </div>                                
                                <div class="form-group all">
                                    <?= lang("barcode_symbology", "barcode_symbology") ?>
                                    <?php
                                    $bs = array('code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca ' => 'UPC-A', 'upce' => 'UPC-E');
                                    echo form_dropdown('barcode_symbology', $bs, set_value('barcode_symbology', 'code128'), 'class="form-control select2" id="barcode_symbology" required="required" style="width:100%;"');
                                    ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('category', 'category'); ?>
                                    <?php
                                    $cat[''] = lang("select")." ".lang("category");
                                    foreach($categories as $category) {
                                        $cat[$category->id] = $category->name;
                                    }
                                    ?>
                                    <?= form_dropdown('category', $cat, $category_id, 'class="form-control select2 tip" id="category"  required="required" style="width:100%;"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('product_tax', 'product_tax'); ?> <?= lang('external_percentage'); ?>
                                    <?= form_input('product_tax', $product_tax, 'class="form-control tip" id="product_tax"  required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= lang('tax_method', 'tax_method'); ?>
                                    <?php $tm = array(0 => lang('inclusive'), 1 => lang('exclusive')); ?>
                                    <?= form_dropdown('tax_method', $tm, set_value('tax_method'), 'class="form-control tip select2" id="tax_method"  required="required" style="width:100%;"'); ?>
                                </div>

                                <!--<div class="form-group st">
                                    <?= lang('alert_quantity', 'alert_quantity'); ?>
                                </div>-->

                                <div class="form-group">
                                    <?= lang('image', 'image'); ?>
                                    <input type="file" name="userfile" id="image">
                                </div>
                                <input type="hidden" name="modo" id="modo" value="<?= $modo ?>">
                            </div>
                            
                        </div>

                        <!-- LISTA DE PRODUCTOS DEL COMBO ------->
                        
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-8" style="border-style:solid;border-color:gray;border-radius: 10px; margin-top:10px;">
                                <?php
                                $ar         = array();
                                $result     = $this->db->query("select a.id, a.name, b.name nom_cat from tec_products a left join tec_categories b on a.category_id=b.id where a.category_id != 7 and a.type='standard' order by a.name")->result_array();
                                $ar         = $this->fm->conver_dropdown($result, "id", "name", array(''=>'Seleccione'));

                                for($i=0;$i<4;$i++){
                                    echo "<div class=\"row\">";
                                    echo "<div class=\"col-xs-12 col-sm-12 col-md-4\" style=\"margin:5px\">";        
                                    echo form_dropdown("p$i",$ar,'','class="form-control tip" id="p$i" onclick=""');
                                    echo "</div></div>";        
                                }
                                ?>
                            </div>
                        </div>

                        <!-- INICIO PRECIOS POR TIENDA ------->

                        <?php if ($Settings->multi_store) { ?>
                            <div class="row">
                                <div class="col-sm-12 col-md-10 col-lg-12">
                                    <h2 style="color:red">Precios</h2>
                                </div>
                            </div>
                            <div id="china" class="row" style="border-style:solid; border-color: gray; padding:5px;">
                            <?php
                                //if(!isset($id)){ // Es nuevo producto
                                    foreach($stores as $store) { 
                                        //echo date("H:i:s") . '<br>';
                                        if($store->id == 1 || $store->id == 2){
                                        
                            ?>
                                        <div class="col-sm-3" style="margin-right:10px;">
                                            <h4><?= $store->state; ?></h4>
                                            
                                            <!-- QUANTITY --->
                                            <div class="form-group st">
                                                <?= lang('quantity', 'quantity'.$store->id); ?>:
                                                <?php
                                                	$cSql = "select * from tec_product_store_qty where product_id = ? and store_id = ?";
                                                	//die($cSql);
                                                	$r = $this->db->query($cSql, array($id, $store->id));
                                                	foreach($r->result() as $r){
                                                		$Qa = $r->quantity;
                                                	}
                                                ?>
                                                <?= form_input('quantity'.$store->id, $Qa, 'class="form-control tip" id="quantity'.$store->id.'"'); ?>
                                            </div>
                                            
                                            <?php 
	                                            $cSql = "select * from tec_tipo_precios where activo='1' order by id";
	                                            $tipuis = $this->db->query($cSql);
	                                            foreach($tipuis->result() as $tipui){
	                                                $resultX = $this->db->query("select * from tec_product_store_entes where store_id = ? and tipo_id = ?", array($store->id, $tipui->id));
	                                                echo "<label>".$tipui->descrip.":</label>";
	                                                echo casilli($store->id, $tipui->id, buca2($precios, "store_id", "tipo_id", $store->id, $tipui->id, "price"));
	                                            }
                                            ?>
                                        </div>
                            <?php
                                        } // fin if 
                                    } // for
                            //} // if
                        }
                        ?>

                        <div class="row">
                            <div class="col-sm-12 col-md-10">
                                <div class="form-group">
                                    <?= lang('details', 'details'); ?>
                                    <?= form_textarea('details', set_value('details'), 'class="form-control tip redactor" id="details"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= form_submit('add_product', 'Guardar', 'class="btn btn-primary"'); ?>
                                    <!--<button type="button" onclick="rellenar()">R</button>-->
                                </div>
                            </div>
                        </div>
                        <?= form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" charset="utf-8">
    var price = 0; cost = 0; items = {};
    $(document).ready(function() {
        /*
            $('#type').change(function(e) {
                var type = $(this).val();
                if (type == 'combo') {
                    $('.st').slideUp();
                    $('#ct').slideDown();
                    //$('#cost').attr('readonly', true);
                } else if (type == 'service') {
                    $('.st').slideUp();
                    $('#ct').slideUp();
                    //$('#cost').attr('readonly', false);
                } else {
                    $('#ct').slideUp();
                    $('.st').slideDown();
                    //$('#cost').attr('readonly', false);
                }
            });

            $("#add_item").autocomplete({
                source: '<?= site_url('products/suggestions'); ?>',
                minLength: 1,
                autoFocus: false,
                delay: 200,
                response: function (event, ui) {
                    if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                        bootbox.alert('<?= lang('no_product_found') ?>', function () {
                            $('#add_item').focus();
                        });
                        $(this).val('');
                    }
                    else if (ui.content.length == 1 && ui.content[0].id != 0) {
                        ui.item = ui.content[0];
                        $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                        $(this).removeClass('ui-autocomplete-loading');
                    }
                    else if (ui.content.length == 1 && ui.content[0].id == 0) {
                        bootbox.alert('<?= lang('no_product_found') ?>', function () {
                            $('#add_item').focus();
                        });
                        $(this).val('');

                    }
                },
                select: function (event, ui) {
                    event.preventDefault();
                    if (ui.item.id !== 0) {
                        var row = add_product_item(ui.item);
                        if (row) {
                            $(this).val('');
                        }
                    } else {
                        bootbox.alert('<?= lang('no_product_found') ?>');
                    }
                }
            });
            $('#add_item').bind('keypress', function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    $(this).autocomplete("search");
                }
            });

            $(document).on('click', '.del', function () {
                var id = $(this).attr('id');
                delete items[id];
                $(this).closest('#row_' + id).remove();
            });


            $(document).on('change', '.rqty', function () {
                var item_id = $(this).attr('data-item');
                items[item_id].row.qty = (parseFloat($(this).val())).toFixed(2);
                add_product_item(null, 1);
            });

            $(document).on('change', '.rprice', function () {
                var item_id = $(this).attr('data-item');
                items[item_id].row.price = (parseFloat($(this).val())).toFixed(2);
                add_product_item(null, 1);
            });

            function add_product_item(item, noitem) {
                if (item == null && noitem == null) {
                    return false;
                }
                if (noitem != 1) {
                    item_id = item.row.id;
                    if (items[item_id]) {
                        items[item_id].row.qty = (parseFloat(items[item_id].row.qty) + 1).toFixed(2);
                    } else {
                        items[item_id] = item;
                    }
                }
                price = 0;
                cost = 0;

                $("#prTable tbody").empty();
                $.each(items, function () {
                    var item = this.row;
                    var row_no = item.id;
                    var newTr = $('<tr id="row_' + row_no + '" class="item_' + item.id + '"></tr>');
                    tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + item.id + '"><input name="combo_item_code[]" type="hidden" value="' + item.code + '"><input name="combo_item_name[]" type="hidden" value="' + item.name + '"><input name="combo_item_cost[]" type="hidden" value="' + item.cost + '"><span id="name_' + row_no + '">' + item.name + ' (' + item.code + ')</span></td>';
                    tr_html += '<td><input class="form-control text-center rqty" name="combo_item_quantity[]" type="text" value="' + formatDecimal(item.qty) + '" data-id="' + row_no + '" data-item="' + item.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                    //tr_html += '<td><input class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(item.price) + '" data-id="' + row_no + '" data-item="' + item.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
                    tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                    newTr.html(tr_html);
                    newTr.prependTo("#prTable");
                    //price += formatDecimal(item.price*item.qty);
                    cost += formatDecimal(item.cost*item.qty);
                });
                $('#cost').val(cost);
                return true;

            }
        */
        <?php
        /*
            if ($this->input->post('type') == 'combo') {
                $c = sizeof($_POST['combo_item_code']);
                $items = array();
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                        $items[] = array('id' => $_POST['combo_item_id'][$r], 'row' => array('id' => $_POST['combo_item_id'][$r], 'name' => $_POST['combo_item_name'][$r], 'code' => $_POST['combo_item_code'][$r], 'qty' => $_POST['combo_item_quantity'][$r], 'cost' => $_POST['combo_item_cost'][$r]));
                    }
                }
                echo '
                var ci = '.json_encode($items).';
                $.each(ci, function() { add_product_item(this); });
                ';
            }
            if ($this->input->post('type')) {
        */
            ?>
            /*
                var type = '<?= $this->input->post('type'); ?>';
                if (type == 'combo') {
                    $('.st').slideUp();
                    $('#ct').slideDown();
                    //$('#cost').attr('readonly', true);
                } else if (type == 'service') {
                    $('.st').slideUp();
                    $('#ct').slideUp();
                    //$('#cost').attr('readonly', false);
                } else {
                    $('#ct').slideUp();
                    $('.st').slideDown();
                    //$('#cost').attr('readonly', false);
                }
            */
<?php /* } */
        ?>
    });

    function rellenar(){
        //document.getElementById('name').value = "PYE DE MANZANA"
        //document.getElementById('code').value = "PYE01"
        document.getElementById("quantity1").value = "9999999"
        document.getElementById("quantity2").value = "9999999"
        //document.getElementById("cost").value = "23"
        //document.getElementById("price").value = "23"
        document.getElementById("product_tax").value = "10"
        //document.getElementById("category").value = "26"
        for(var j=1; j<6; j++){
            document.getElementById("cas_1_"+j).value = 40+j;
            document.getElementById("cas_2_"+j).value = 40+j;
        }
    }

</script>
<?php
    function casilli($store, $tipui, $rpta){
        //$price = "0";
        /*if($query){
            $price = $query->price;
        }*/
        $cad = "<div class=\"\">";  // form-group st
        $cad .= "<input type=\"text\" name=\"cas_{$store}_{$tipui}\" id=\"cas_{$store}_{$tipui}\" class=\"form-control\" value=\"$rpta\">";
        //$cad .= "store:". $store. ", tipui:" . $tipui;
        $cad .= "</div>";
        return $cad;
    }

    function buca($ar, $c1, $v1, $rpta){

        foreach($ar as $r){
            if($r[$c1] == $v1){
                return $r[$rpta];
            }
        }
        return "";
    }

    function buca2($ar, $c1, $c2, $v1, $v2, $rpta){
        foreach($ar as $r){
            if($r[$c1] == $v1 && $r[$c2] == $v2){
                return $r[$rpta];
            }
        }
    }
?>