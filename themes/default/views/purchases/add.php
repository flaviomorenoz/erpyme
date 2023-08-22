<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>
<?php


if(isset($purchases_id)){  // ES MODO EDICION
    $result = $this->db->select("purchases.nroDoc, purchases.cargo_servicio, purchases.tipoDoc, purchases.fec_emi_doc, purchases.fec_venc_doc,
        purchases.date, purchases.date_ingreso, purchases.costo_tienda, purchases.costo_banco, purchases.supplier_id, purchases.texto_supplier, purchases.descuentos,
        purchases.nro_cta,purchases.nro_oper,purchases.banco, purchases.fecha_oper, purchases.store_id, purchases.note, purchases.redondeo, purchases.tipo_egreso")
        ->from("purchases")
        ->where("purchases.id",$purchases_id)
        ->get()->result_array();

    foreach($result as $r){
        $nroDoc         = $r["nroDoc"];
        $cargo_servicio = $r["cargo_servicio"];
        $tipoDoc        = $r["tipoDoc"];
        $fec_emi_doc    = $r["fec_emi_doc"];
        $fec_venc_doc   = $r["fec_venc_doc"];
        $dates          = $r["date"];
        $date_ingreso   = $r["date_ingreso"];
        $costo_tienda   = $r["costo_tienda"];
        $costo_banco    = $r["costo_banco"];
        $supplier_id    = $r["supplier_id"];
        $texto_supplier = $r["texto_supplier"];
        $descuentos     = $r["descuentos"];
        $nro_cta        = $r["nro_cta"];
        $nro_oper       = $r["nro_oper"];
        $banco          = $r["banco"];
        $fecha_oper     = $r["fecha_oper"];
        $tienda         = $r["store_id"];
        $note           = $r["note"];
        $redondeo       = $r["redondeo"];
        $tipo_egreso 	= $r["tipo_egreso"];
    }

    $dates          = substr($dates,0,10) . "T" . substr($dates, 11,5);
    $date_ingreso   = substr($date_ingreso,0,10) . "T" . substr($date_ingreso, 11,5);

    $query = $this->db->query("select a.id, a.rubro_id, d.descrip rubro, a.purchase_id, a.product_id, a.cost, a.quantity, a.cost, a.subtotal, a.peso_caja, a.precio, 
        c.name, c.unidad, a.detalle
        from tec_purchase_items a
        left join tec_products c on a.product_id = c.id
        left join tec_rubros d on a.rubro_id = d.id
        where a.purchase_id = $purchases_id");

}else{

    if(strlen($tipoDoc)>0){  // MODO INVALIDO

        // nada

    }else{  // MODO NUEVO
        $nroDoc         = "";
        $cargo_servicio = 0;
        $dates           = date("Y-m-d") . "T" . date("H:i");
        $date_ingreso   = date("Y-m-d") . "T" . date("H:i");
        $tipoDoc        = "";
        $fec_emi_doc    = "";
        $fec_venc_doc   = "";
        $date           = "";
        $costo_tienda   = "0";
        $costo_banco    = "0";
        $supplier_id    = "";

        $i_ultimo = 0; // contador de items  //
        $texto_supplier = "";
        $descuentos     = 0;
        $peso_caja      = 1;
        $fecha_oper     = "";
        $tienda         = "";
        $note           = "";
        $redondeo       = 0;
        $nro_cta 		= "";
    }
}

?>

<!--<div class="container" style="margin-top: 30px;">-->
	<style type="text/css">
		/* Style the tab */
		.tab {
		  overflow: hidden;
		  border: 1px solid #ccc;
		  background-color: #f1f1f1;
		  margin-top: 15px;
		}

		/* Style the buttons inside the tab */
		.tab button {
		  background-color: inherit;
		  float: left;
		  border: none;
		  outline: none;
		  cursor: pointer;
		  padding: 5px 16px;
		  transition: 0.3s;
		  font-size: 17px;
		}

		/* Change background color of buttons on hover */
		.tab button:hover {
		  background-color: #ddd;
		}

		/* Create an active/current tablink class */
		.tab button.active {
		  background-color: orange;
		}

		/* Style the tab content */
		.tabcontent {
		  display: none;
		  padding: 6px 12px;
		  border: 1px solid #ccc;
		  border-top: none;
		}

		.leyenda{
			font-family: verdana, Arial;
			font-size:14px; font-weight:bold; font-style: italic; color:rgb(110,110,110);
		}

		.legend-especial{
			border-bottom-color:#777;
		}

		.verlo{
			border-style: solid;
			border-color: black;
			border-width: 1px;
		}

		.verlo-rojo{
			border-style: solid;
			border-color: red;
			border-width: 1px;
		}
	</style>

	<script>
		function procesos(el_proceso){
			location.href = '<?= base_url('purchases/add/') ?>' + el_proceso
		}
	</script>

	<div class="row">
		<div class="col-xs-12 col-sm-12" style="margin-left: 10px">
			<div class="tab">
			  <button id="producton" class="tablinks" onclick="openTab(event, 'tab_producto'); procesos('producto');">Producto</button>
			  <button id="serviton" class="tablinks" onclick="openTab(event, 'tab_servicio'); procesos('servicio')">Servicio</button>
			</div>

            <?php echo form_open_multipart("purchases/add", 'class="validation" id="form_compra"'); ?>

			<div id="tab_producto" class="tabcontent">
			</div> <!-- FIN DEL TAB PRODUCTO -->

			<div id="tab_servicio" class="tabcontent">
			</div>
               
                <input type="hidden" name="edicion_purchase_id" id="edicion_purchase_id" value="<?= (isset($purchases_id) ? $purchases_id : "") ?>">
                
                <fieldset style="margin-top: 15px;">
                	<legend class="leyenda legend-especial">Datos del  Documento:</legend>

	                <div class="row"  style="margin:0px;">
	                	<div class="col-xs-5 col-sm-9 text-right" style="padding-top:10px;"><label>TIENDA</label></div>
	                    <div class="col-xs-6 col-sm-3" style="padding-right:30px;">
	                        <!--<div class="form-group">-->
	                            <?php
	                                $group_id = $this->session->userdata["group_id"];
	                                $q = $this->db->where("activo","1")->get('stores');

	                                $ar = array();
	                                if ($group_id == '1'){
	                                    $ar[] = "Todas";
	                                    foreach($q->result() as $r){
	                                        //if( $r->state != "Administracion"){
	                                            $ar[$r->id] = $r->state;
	                                        //}
	                                    }
	                                }else{
	                                    foreach($q->result() as $r){
	                                        if($r->id == $this->session->userdata["store_id"]){
	                                            $ar[$r->id] = $r->state;
	                                        }
	                                    }
	                                }
	                                echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required"');
	                            ?>
	                        <!--</div>-->
	                    </div>
	                </div>

	                <div class="row">

	                    <!-- Tipo Doc -->
	                    <div class="col-xs-4 col-sm-3 col-md-2">
	                        <div class="form-group">
	                            <label for="tipodoc">Tipo Doc.</label>
	                            <?php 
	                               
	                               $ar = array('F'=>'Factura','B'=>'Boleta','G'=>'Guia Interna');
	                               $cadeno = 'class="form-control tip" id="tipoDoc" required="required" onchange="generar_nro(this);console.log(' . "'BTK'" . ');"';
	                               echo form_dropdown('tipoDoc', $ar, $tipoDoc, $cadeno);
	                                
	                            ?>
	                        </div>
	                    </div>

	                    <div class="col-xs-1 col-sm-1 col-md-1">
	                    </div>

	                    <!-- Nro Doc -->
	                    <div class="col-xs-4 col-sm-3 col-md-2">
	                        <div class="form-group">
	                            <label for="tipodoc"><?= lang('Nro Doc'); ?></label>
	                            <?= form_input('nroDoc', $nroDoc, 'class="form-control tip" id="nroDoc"'); ?>
	                        </div>
	                    </div>


	                    <!-- Fecha de Ingreso -->
	                    <div class="col-xs-5 col-sm-4 col-md-3">
	                        <div class="form-group">
	                            <label><?= $tipo_egreso == 'producto' ? "Fecha Recepcion" : "Fecha Servicio" ?></label>
	                            <?php
	                                if($group_id == '1'){
	                                    $readonly = "";
	                                }else{
	                                    $readonly = "";
	                                }

	                                $ar = array(
	                                    "name"  =>"date_ingreso",
	                                    "id"    =>"date_ingreso",
	                                    "type"  =>"datetime-local",
	                                    "value" => $date_ingreso, 
	                                    "class" =>"form-control tip",
	                                    "{$readonly}" => ""
	                                );
	                                echo form_input($ar);
	                            ?>
	                        </div>
	                    </div>

	                </div>
	                <div class="row">
	                    <?php if($tipogasto == "caja"){ ?>
	                        <div class="col-xs-10 col-sm-4 col-md-3">
	                            <div class="row">
	                            	<div class="col-xs-8 col-sm-10">
			                            <div class="form-group">
			                                <?= lang('supplier', 'supplier'); ?>
			                                <?php
			                                $sp[''] = lang("select")." ".lang("supplier");
			                                foreach($suppliers as $supplier) {
			                                    $sp[$supplier->id] = $supplier->name;
			                                }
			                                ?>
			                                <?= form_dropdown('supplier', $sp, $supplier_id, 'class="form-control select2 tip" id="supplier"  required="required" style="width:100%;"'); ?>
			                            </div>
			                        </div>
			                        <div class="col-xs-3 col-sm-2" style="margin-left:-10px; text-align:left">
			                            <div class="form-group" style="text-align:left">
			                                <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#Modal_suppliers" onclick="abrir_modal_proveedor()" style="margin-top:26px">
			                                    <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
			                                </button>
			                            </div>    
			                        </div>
			                    </div>
	                        </div>

	                    <?php }else{ ?>
	                        <div class="col-xs-10 col-sm-5 col-md-4">
	                            <div class="form-group">
	                                <label for="texto_supplier">Proveedor</label>
	                                <?= form_input('texto_supplier', $texto_supplier, 'class="form-control tip" id="texto_supplier" required="required"') ?>
	                            </div>
	                        </div>
	                    <?php } ?>

	                    <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2">
	                        <div class="form-group">
	                            <label><?= lang('Fecha Emision'); ?></label>
	                            <?php 
	                                $ar = array(
	                                    "name"  =>"fec_emi_doc",
	                                    "id"    =>"fec_emi_doc",
	                                    "type"  =>"date",
	                                    "value" =>$fec_emi_doc,
	                                    "class" =>"form-control tip"
	                                );
	                                echo form_input($ar); 
	                            ?>
	                        </div>
	                    </div>

	                    <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2">
	                        <div class="form-group">
	                            <label><?= lang('Fecha Vencim'); ?></label>
	                            <?php 
	                                $ar = array(
	                                    "name"  =>"fec_venc_doc",
	                                    "id"    =>"fec_venc_doc",
	                                    "type"  =>"date",
	                                    "value" =>$fec_venc_doc,
	                                    "class" =>"form-control tip"
	                                );
	                                echo form_input($ar); 
	                            ?>
	                        </div>
	                    </div>

	                    <div class="col-xs-3 col-sm-2 col-md-1">
	                        <div class="form-group">
	                            <label>Cargo:</label>
	                            <?php 
	                                $ar = array(
	                                    "name"  =>"cargo_servicio",
	                                    "id"    =>"cargo_servicio",
	                                    "type"  =>"text",
	                                    "value" =>$cargo_servicio,
	                                    "class" =>"form-control tip",
	                                    "placeholder" => "S/ 0.00"
	                                );
	                                echo form_input($ar);
	                            ?>
	                        </div>
	                    </div>

	                </div><!-- Fin de fila -->

                </fieldset>

                <!-- MARCO DE DETALLE DE PRODUCTOS / SERVICIOS -->
                <fieldset style="margin-top: 15px; margin-left: 0px">
                	<legend class="leyenda legend-especial">Detalle de <?= $tipo_egreso == 'producto' ? 'Productos' : 'Servicios' ?>:</legend>

	                <div class="row">
	                    
	                    <div class="col-xs-9 col-sm-4 col-md-3 col-lg-2" style="margin-left: 0px">
	                        <div class="form-group" style="margin-left: 0px">
	                            <label for="rubro_id">Rubro de Gasto</label>
	                            <?php 
	                               	if($tipo_egreso == 'producto'){
	                               		$cSql = "select id, upper(descrip) descrip from tec_rubros where productos='1' and id>0 order by descrip";
	                               	}else{
	                               		$cSql = "select id, upper(descrip) descrip from tec_rubros where servicios='1' and id>0 order by descrip";
	                               	}
	                               
	                               $result = $this->db->query($cSql)->result_array();
	                               $ar_p = array();
	                               $ar_p[""] = "--Seleccione--";
	                               foreach($result as $r){
	                                    $ar_p[ $r["id"] ] = $r["descrip"];
	                               }

	                               echo form_dropdown('rubro_id',$ar_p,'','class="form-control tip" id="rubro_id" required="required" onchange="obtener_productos()" style="margin-left: 0px"');
	                            ?>

	                        </div>
	                    </div>


	                    <div class="col-xs-9 col-sm-4 col-md-3 col-lg-3">
	                        <div class="form-group">
	                            <label for="product_id"><?= $tipo_egreso == 'producto' ? 'Productos' : 'Servicios' ?></label>
	                            <?php 
	                               	$cSql = "select id, code, upper(name) name, price, unidad from tec_products where category_id = 7 order by name";
	                               	$result = $this->db->query($cSql)->result_array();
	                               	$ar_p = array();
	                               	$ar_p[""] = "--- Seleccione Producto ---";
	                               	foreach($result as $r){
	                                    $ar_p[ $r["id"] ] = $r["name"] . " (" . $r["unidad"] . ")";
	                               	}

	                               echo form_dropdown('product_id',$ar_p,'','class="form-control tip" id="product_id" required="required"');
	                            ?>

	                        </div>
	                    </div>

	                    <!--<div class="col-md-1" style="margin-left:-10px; margin-right:-10px">
	                        <div class="form-group">
	                            <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#Modal_insumos" style="margin-top:26px">
	                                <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
	                            </button>
	                        </div>    
	                    </div>-->

	                    <div class="col-xs-5 col-sm-3 col-md-1 col-lg-1">
	                        <div class="form-group">
	                            <label><?= lang('quantity'); ?></label>
	                            <?php 
	                                $ar = array(
	                                    "name"  =>"quantity",
	                                    "id"    =>"quantity",
	                                    "type"  =>"text",
	                                    "value" =>$quantity,
	                                    "class" =>"form-control tip"
	                                );
	                                echo form_input($ar);
	                            ?>
	                        </div>
	                    </div>

	                    <div class="col-xs-5 col-sm-4 col-md-2 col-lg-2">
	                        <div class="form-group">
	                            <label>Precio U.</label>
	                            <?php 
	                                $ar = array(
	                                    "name"  =>"cost",
	                                    "id"    =>"cost",
	                                    "type"  =>"text",
	                                    "value" =>$cost,
	                                    "class" =>"form-control tip",
	                                    "placeholder" => "S/ 0.00",
	                                    "onblur"=>"document.getElementById('importex').value=$('#quantity').val()*this.value"
	                                );
	                                echo form_input($ar);
	                            ?>
	                        </div>
	                    </div>

	                    <div class="col-xs-5 col-sm-1 col-md-1">
	                    	<label>Importe</label>
	                    	<input type="text" name="importex" id="importex" class="form-control" readonly>
	                    </div>

	                    <div class="col-sm-1 col-md-1" style="padding:5px 0px 0px 0px;">
			                <br>
			                <a href="#" onclick="activar_calculadora()">
			                    <img src="<?= base_url("themes/") . $this->theme ?>images/calculadora.png" height="27px" title="Por Paquete" class=""> 
			                </a>
			                <a href="#" onclick="activar_calculadoraKilo()">
			                    <img src="<?= base_url("themes/") . $this->theme ?>images/calculadora.png" height="27px" title="Por Kilo" class=""> 
			                </a>
			            </div>
	                </div>

	                <?php 
	                	if($tipo_egreso == 'producto'){
	                		echo "<input type='hidden' name='detalle' id='detalle'>";
	                	}else{
	                ?>
	                <div class="row" style="border-style:none; border-color:red">
	                	<div class="col-sm-7 col-md-6 col-lg-5">
	                		<label>Observaciones</label><br>
	                		<textarea name="detalle" id="detalle" class="form-control" placeholder="Observaciones" rows="2">
	                			
	                		</textarea>
	                	</div>
	                </div>
	                <?php
	                	}
	                ?>

	                <!-- IGV Y CALCULADORAS Y BUTTON AGREGAR-->
	                <div class="row" style="border-style:none; border-color:red">
			            <div class="col-xs-6 col-sm-2 col-lg-1">
		                    	<br>
		                        <button class="btn btn-primary" onclick="Agregar()">Agregar</button>
		                    
			            </div>

	                    <div class="col-xs-6 col-sm-2 col-lg-1" style="">

			                    <div class="form-group">
			                            <label>IGV:</label><br>
			                            <input type="checkbox" id="chk_igv" name="chk_igv" value="1" class="form-control tip">
			                        
			                    </div>
			            </div>

			            <!--<div class="col-xs-4 col-sm-1">
			              	<label>Kilo</label><br>
			                <a href="#" onclick="activar_calculadoraKilo()">
			                    <img src="<?= base_url("themes/") . $this->theme ?>images/calculadora.png" height="30px" title="Por Kilo"> 
			                </a>
			            </div>-->
	                </div>
		                
	                <!-- SERA UN DIV CALCULADORA -->
	                <div class="row" style="display: none;" id="calculadora">
	                    <div class="col-xs-12 col-sm-9 col-lg-7" style="border-style:solid; border-radius:5px; border-width: 2px; border-color: red; padding: 10px;">
	                        <div class="row">
	                            <div class="col-sm-12"><h3 style="margin-top:0px;color:rgb(160,160,160)">Calculadora por Paquete/Unidad</h3></div>
	                        </div>
	                        
	                        <div class="row">
	                            <div class="col-xs-6 col-sm-3">
	                                <label style="height:40px">Cantidad de Paquetes:</label>                                
	                                <input type="text" name="cant_pq" id="cant_pq" class="form-control">
	                            </div>

	                            <div class="col-xs-6 col-sm-3">
	                                <label style="height:40px">Precio x Paquete:</label>                                
	                                <input type="text" name="costo_pq" id="costo_pq" class="form-control">
	                            </div>

	                            <div class="col-xs-6 col-sm-3">
	                                <label style="height:40px">Unidades en el Paquete:</label>                                
	                                <input type="text" name="un_pq" id="un_pq" class="form-control">
	                            </div>

	                            <div class="col-xs-6 col-sm-3">
	                                <div style="height:40px">&nbsp;</div>
	                                <button type="button" class="btn btn-primary" onclick="calculo_paquete()" style="margin-top:5px; padding:3px 7px;">Reemplazar</button>
	                            </div>

	                        </div>
	                    </div>
	                </div>

	                <!-- SERA UN DIV CALCULADORA KILO -->
	                <div class="row" style="display: none;margin-top: 6px;" id="calculadoraKilo">
	                    <div class="col-xs-12 col-sm-9 col-lg-7" style="border-style:solid; border-radius:5px; border-width: 2px; border-color: red; padding: 10px;">
	                        <div class="row">
	                            <div class="col-sm-12"><h3 style="margin-top:0px;color:rgb(160,160,160)">Calculadora por Kilos</h3></div>
	                        </div>
	                        <div class="row">
	                            <div class="col-xs-6 col-sm-3">
	                                <label style="height:40px">Cantidad de Paquetes:</label>                                
	                                <input type="text" name="cant_pqKilo" id="cant_pqKilo" class="form-control">
	                            </div>

	                            <div class="col-xs-6 col-sm-3">
	                                <label style="height:40px">Precio x Kilo:</label>                                
	                                <input type="text" name="costo_pqKilo" id="costo_pqKilo" class="form-control">
	                            </div>

	                            <div class="col-xs-6 col-sm-3">
	                                <label style="height:40px">Peso del Paquete:</label>                                
	                                <input type="text" name="un_pqKilo" id="un_pqKilo" class="form-control">
	                            </div>

	                            <div class="col-xs-6 col-sm-3">
	                                <div style="height:40px">&nbsp;</div>
	                                <button type="button" class="btn btn-primary" onclick="calculo_paqueteKilo()" style="margin-top:5px; padding:3px 7px;">Reemplazar</button>
	                            </div>

	                        </div>
	                    </div>
	                </div>
	                

                    <!-- ESTE ES EL DIV ALTERNATIVO DE LOS PRODUCTOS --->
                    <div class="row" style="margin-top:3px">
                        <div class="col-12 col-sm-12 col-md-10" id="taxi" style="margin-top:15px">
                        </div>
                        <div class="col-sm-2 col-md-4" style="margin-top:15px">
                        </div>
                    </div>

                    <!-- DSCTO Y REDONDEO -->
                    <div class="row">
                        <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2">
                            <div class="form-group">
                                <label>Total descuentos:</label>
                                <?php 
                                    $ar = array(
                                        "name"  =>"descuentos",
                                        "id"    =>"descuentos",
                                        "type"  =>"text",
                                        "value" =>$descuentos,
                                        "class" =>"form-control tip",
                                        "placeholder" => "S/ 0.00",
                                        "onblur" => "cargar_items()"
                                    );
                                    echo form_input($ar);
                                ?>
                            </div>
                        </div>

                        <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2">
                            <div class="form-group">
                                <label>Redondeo:</label>
                                <?php 
                                    $ar = array(
                                        "name"  =>"redondeo",
                                        "id"    =>"redondeo",
                                        "type"  =>"text",
                                        "value" =>$redondeo,
                                        "class" =>"form-control tip",
                                        "placeholder" => "S/ 0.00",
                                        "onblur" => "cargar_items()"
                                    );
                                    echo form_input($ar);
                                ?>
                            </div>
                        </div>
                    </div>


                </fieldset>
                
                <!-- REGISTRO DE PAGOS -->
                <fieldset style="margin-top: 15px">
                	<legend class="leyenda legend-especial">Registro de Pago</legend>
                	<div class="row">

	                    <!-- costo_tienda -->
	                    <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2">
	                        <div class="form-group">
	                            <label id="label_caja_tda">Pago Caja</label>
	                            <?php 
	                                $ar = array(
	                                    "name"  =>"costo_tienda",
	                                    "id"    =>"costo_tienda",
	                                    "type"  =>"text",
	                                    "value" =>$costo_tienda,
	                                    "class" =>"form-control tip",
	                                    "style" =>"font-size:15px; text-align:right;",
	                                    "onblur"=>"document.getElementById('txt_total_pagos').value= parseFloat($('#costo_banco').val()) + parseFloat(this.value)"
	                                );
	                                echo form_input($ar);
	                            ?>
	                        </div>
	                    </div>

	                    <!-- Fecha del Pago date -->
	                    <div class="col-xs-6 col-md-3 col-lg-2">
	                        <div class="form-group">
	                            <label>Fecha del pago</label>
	                            <?php
	                                if($group_id == '1'){
	                                    $readonly = "";
	                                }else{
	                                    $readonly = "";
	                                }

	                                $ar = array(
	                                    "name"  =>"date",
	                                    "id"    =>"date",
	                                    "type"  =>"datetime-local",
	                                    "value" => (isset($dates) ? $dates : (isset($date) ? $date : '') ), 
	                                    "class" =>"form-control tip",
	                                    "{$readonly}" => ""
	                                );
	                                echo form_input($ar);
	                            ?>
	                        </div>
	                    </div>

                	</div>

					<div class="row">

	                    <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2">
	                        <div class="form-group">
	                            <label id="label_caja_bco">Pago Banco</label>
	                            <?php 
	                                $atributo = "";
	                                $atributo_valor = "";
	                                if($Admin == false){
	                                    $atributo = "readonly";
	                                    $atributo_valor = "readonly";
	                                }
	                                $ar = array(
	                                    "name"  =>"costo_banco",
	                                    "id"    =>"costo_banco",
	                                    "type"  =>"text",
	                                    "value" =>$costo_banco,
	                                    "class" =>"form-control tip",
	                                    "style" =>"font-size:15px; text-align:right;",
	                                    "onblur"=>"document.getElementById('txt_total_pagos').value= parseFloat($('#costo_tienda').val()) + parseFloat(this.value)"
	                                );
	                                echo form_input($ar);
	                            ?>
	                        </div>
	                    </div>

	                    <!-- Fecha de Operacion -->
                        <div class="col-xs-6 col-md-3 col-md-2">
                            <div class="form-group">
                                <label id="label_nro_oper">Fecha Operacion</label>
                                <?php
                                        $ar = array(
                                        "name"  =>"fecha_oper",
                                        "id"    =>"fecha_oper",
                                        "type"  =>"date",
                                        "value" => substr($fecha_oper,0,10),
                                        "class" =>"form-control tip",
                                    );
                                    echo form_input($ar);
                                ?>
                            </div>
                        </div>

	                    <!-- Banco -->
	                    <div class="col-xs-4 col-md-2 col-lg-1" style="margin-left:5px">
	                        <div class="form-group">
	                            <label id="label_banco">Banco</label>
	                            
	                            <?php 
	                            	$ar = array(''=>'','BCP'=>'BCP','BBVA'=>'BBVA');
									echo form_dropdown('banco',$ar,$banco,'class="form-control tip" id="banco" onblur="obtener_cuentas()"');

	                                /*$ar = array(
	                                    "name"  =>"banco",
	                                    "id"    =>"banco",
	                                    "type"  =>"text",
	                                    "value" =>$banco,
	                                    "class" =>"form-control tip",
	                                    "style" =>"font-size:15px",
	                                    $atributo => $atributo_valor
	                                );
	                                echo form_input($ar);*/
	                            ?>
	                        </div>
	                    </div>

	                    <div class="col-xs-6 col-md-3 col-lg-2">
	                        <div class="form-group">
	                            <label id="label_nro_cta">Nro. Cuenta</label><br>
	                            <select name="nro_cta" id="nro_cta" class="form-control">
	                            </select>
	                            <?php 
	                                /*$ar = array(
	                                    "name"  =>"nro_cta",
	                                    "id"    =>"nro_cta",
	                                    "type"  =>"text",
	                                    "value" =>$nro_cta,
	                                    "class" =>"form-control tip",
	                                    "style" =>"font-size:15px",
	                                    $atributo => $atributo_valor
	                                );
	                                echo form_input($ar);*/
	                            ?>
	                        </div>
	                    </div>


					</div>

					<div class="row">

	                    <div class="col-xs-4 col-md-3 col-lg-2">
	                    	<label>Total:</label>
	                    	<input type="text" name="txt_total_pagos" id="txt_total_pagos" class="form-control" style="text-align:right" readonly>
	                    </div>

	                </div>

					<div class="row">
		                <div class="col-xs-11 col-lg-11 col-lg-11">
			                <div class="form-group">
			                    <?= lang("note", 'note'); ?>
			                    <?= form_textarea('note', $note, 'class="form-control redactor" id="note"'); ?>
			                </div>
			            </div>
		            </div>

	                <div class="row">
	                    <div class="col-xs-11 col-md-11 col-md-11">
	                        <div class="form-group">
	                            <button type="button" onclick="guardar_compra()" class="btn btn-primary">Guardar Compra</button>
	                            <button type="button" id="reset" class="btn btn-danger"><?= lang('reset'); ?></button>
	                            <a href="<?= site_url('purchases') ?>" class="btn btn-warning">Regresar</a>
	                        </div>
	                    </div>
	                    <div class="col-md-1">
	                        
	                        <?= form_submit('add_purchase', '.', 'class="" id="add_purchase"'); ?>
	                    </div>
	                    <div class="col-md-1">

	                    </div>
	                    
	                </div>


                </fieldset>
                <!-- Cuenta -->
                <script>
                	function obtener_cuentas(){
                		$.ajax({
                			data:{banco : document.getElementById('banco').value},
                			url:'<?= base_url('purchases/obtener_cuentas') ?>',
                			type:'get',
                			success:function(res){
                				var obj = JSON.parse(res)
                				const $select = $("#nro_cta");

                				// Eliminando los option actuales
                				$select.empty()
								$select.append($("<option>", {
								    value: '',
								    text: ''
								}));

                				for(registro in obj){
									$select.append($("<option>", {
									    value: obj[registro]['cuenta'],
									    text: obj[registro]['cuenta']
									}));
                				}

                				document.getElementById('nro_cta').value = '<?= $nro_cta ?>'
                			}
                		})

                	}

                	function obtener_productos(){
                		$.ajax({
                			data:{rubro_id : document.getElementById('rubro_id').value},
                			url:'<?= base_url('purchases/obtener_productos') ?>',
                			type:'get',
                			success:function(res){
                				var obj = JSON.parse(res)
                				const $select = $("#product_id");

                				// Eliminando los option actuales
                				$select.empty()
								$select.append($("<option>", {
								    value: '',
								    text: ''
								}));

                				for(registro in obj){
									$select.append($("<option>", {
									    value: obj[registro]['id'],
									    text: obj[registro]['producto']
									}));
                				}
                			}
                		})

                	}

                    function relleno_azar(){
                        document.getElementById("nroDoc").value         = "12345" 
                        document.getElementById("supplier").value       = 4 // Adidas
                        document.getElementById("fec_emi_doc").value    = "2021-09-28"
                        document.getElementById("fec_venc_doc").value   = "2021-09-28"
                        document.getElementById("product_id").focus()
                        //document.getElementById("").value = 
                    }

                    function mcq(){
                    }

                    document.getElementById("calculadora").style.display = "none"
                    document.getElementById("calculadoraKilo").style.display = "none"

                    function activar_calculadora(){
                        document.getElementById("cant_pq").value    = ""
                        document.getElementById("costo_pq").value   = ""
                        document.getElementById("un_pq").value      = ""

                        if (document.getElementById("calculadora").style.display == "none"){
                            $("#calculadora").fadeIn(800)
                        }else{
                            $("#calculadora").fadeOut(800)
                        }
                    }

                    function calculo_paquete(){
                        var cant_pq     = document.getElementById("cant_pq").value
                        var costo_pq    = document.getElementById("costo_pq").value
                        var un_pq       = document.getElementById("un_pq").value
                        var Xcantidad   = ( cant_pq * 1) * ( un_pq * 1)
                        var Xtotal      = ( cant_pq * 1) * ( costo_pq * 1)
                        var Xprecio     = Xtotal / Xcantidad
                        document.getElementById("cost").value           = Xprecio
                        document.getElementById("quantity").value       = Xcantidad
                        document.getElementById("calculadora").style.display = "none" 
                    }

                    function activar_calculadoraKilo(){
                        document.getElementById("cant_pqKilo").value    = ""
                        document.getElementById("costo_pqKilo").value   = ""
                        document.getElementById("un_pqKilo").value      = ""

                        if (document.getElementById("calculadoraKilo").style.display == "none"){
                            $("#calculadoraKilo").fadeIn(800)
                        }else{
                            $("#calculadoraKilo").fadeOut(800)
                        }                            
                    }

                    function calculo_paqueteKilo(){
                        var cant_pq     = document.getElementById("cant_pqKilo").value
                        var costo_pq    = document.getElementById("costo_pqKilo").value
                        var un_pq       = document.getElementById("un_pqKilo").value
                        var Xcantidad   = ( cant_pq * 1) * ( un_pq * 1)
                        var Xprecio     = costo_pq * 1
                        //var Xtotal      = 
                        document.getElementById("cost").value           = Xprecio
                        document.getElementById("quantity").value       = Xcantidad
                        document.getElementById("calculadoraKilo").style.display = "none" 
                    }
                </script>
                  
                <input type="hidden" name="txt_gSubtotal" id="txt_gSubtotal">
                <input type="hidden" name="txt_gIgv" id="txt_gIgv">
                <input type="hidden" name="txt_gTotal" id="txt_gTotal">
                <input type="hidden" name="modo_edicion" id="modo_edicion" value="1">
                <input type="hidden" name="tipogasto" id="tipogasto" value="<?= (isset($tipogasto) ? $tipogasto : 'caja') ?>"> 
                <!--<button onclick="verificarUnicidad()">verificarUnicidad</button>-->
                <input type="hidden" name="tipo_egreso" id="tipo_egreso" value="<?= $tipo_egreso ?>">

			<?php echo form_close();?>

		</div>	
	</div>
<!--</div>  Final de container -->

<script>
function openTab(evt, cityName) {
	var i, tabcontent, tablinks;
	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}
	tablinks = document.getElementsByClassName("tablinks");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" active", "");
	}
	document.getElementById(cityName).style.display = "block";
	evt.currentTarget.className += " active";
}
</script>

<!-- Modal Nuevo Insumo -->
<div id="Modal_insumos" class="modal fade" role="dialog">
  <div id="oreo" class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background-color:orange">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Agregar Insumos</b></h4>
      </div>
      
      <div class="modal-body">
        <div class="col-md-8">

            <div class="row" style="margin-top:5px;">
                <div class="col-sm-5">
                    Nombre Producto:
                </div>

                <div class="col-sm-7">
                    <input type="text" id="descPro" name="descPro" size="30" class="form-control" placeholder="Nombre Producto">
                    <input type="hidden" id="idPro" name="idPro">
                </div>
            </div>

            <div class="row" style="margin-top:5px;">
                <div class="col-sm-5">
                    Unidad de medida:
                </div>

                <div class="col-sm-7">
                    <?php 
                        $ar_unidad = array('UNIDAD','GRAMO','KILO','LITRO');
                        echo "<select class=\"form-control\" name=\"unidad\" id=\"unidad\">";
                        for($i=0; $i<count($ar_unidad); $i++){
                            echo "<option value=\"" . $ar_unidad[$i] . "\">" . $ar_unidad[$i] . "</option>";
                        }
                        echo "</select>";
                    ?>
                </div>
            </div>

        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="grabar_insumos()">Grabar</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal SUPPLIERS -->
<script type="text/javascript">
function abrir_modal_proveedor(){
    document.getElementById("name").value = ""
    document.getElementById("email").value = ""
    document.getElementById("contact").value = ""
    document.getElementById("phone").value = ""
    //document.getElementById("cf1").value = ""
    document.getElementById("cf2").value = ""
}

</script>

<div id="Modal_suppliers" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background-color:orange">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Agregar Proveedor</b></h4>
      </div>
      <div class="modal-body">


            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="code"><?= $this->lang->line("name"); ?></label>
                    <?= form_input('name', set_value('name'), 'class="form-control input-sm" id="name"'); ?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
                    <?= form_input('email', set_value('email'), 'class="form-control input-sm" id="email"'); ?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="contact"><?= $this->lang->line("contact"); ?></label>
                    <?= form_input('contact', set_value('contact'), 'class="form-control input-sm" id="contact"');?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
                    <?= form_input('phone', set_value('phone'), 'class="form-control input-sm" id="phone"');?>
                </div>

                <!--<div class="form-group">
                    <label class="control-label" for="cf1"><?= $this->lang->line("scf1"); ?></label>
                    <?= form_input('cf1', set_value('cf1'), 'class="form-control input-sm" id="cf1"'); ?>
                </div>-->

                <div class="form-group">
                    <label class="control-label" for="cf2"><?= $this->lang->line("scf2"); ?></label>
                    <?= form_input('cf2', set_value('cf2'), 'maxlength="11" class="form-control input-sm" id="cf2"');?>
                </div>
            </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="grabar_suppliers()">Grabar</button>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        echo "setTimeout('abrir_item_menu(6)',500);\n";
    ?>

    document.getElementById('chk_igv').checked = true;
    var ar_items = new Array();
    var gIgv = 18;

    var ar_cambios_tdc = new Array(); // arreglo para ver si es que hace cambio de tipo de comprobante.
    ar_cambios_tdc[0] = document.getElementById("tipoDoc").value

    <?php 
        if($tipogasto == "gastos"){ 
            echo "document.getElementById('tipogasto').value = 'gastos';\n";
        }
    ?>

    <?php
        // Variable Admin
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . "\n";

        // PARA EL CASO DE MODO EDICION
        if(isset($purchases_id)){
            $i=0;
            
            foreach($query->result() as $row){ 
                //die("bandera blancas:" . $row->rubro_id);
                echo "ar_items[$i]={";
                echo "id:'" . 		$row->product_id . "',";
                echo "rubro_id:'" . $row->rubro_id . "',";
                echo "rubro:'"   .  $row->rubro . "',";
                echo "name:'" . 	$row->name . "',";
                echo "quantity:" . 	$row->quantity . ",";
                echo "cost:" . 		$row->cost . ",";
                echo "peso_caja:" . round($row->peso_caja,4) . ",";
                echo "subtotal:'" . round($row->subtotal,4) . "',";
                echo "precio:'" . 	round($row->precio,4) . "',";
                echo "detalle:'" . 	$row->detalle . "'";
                echo "}\n";
                $i++;
            }
        }else{

            if(strlen($tipoDoc)>0){  // MODO INVALIDO
            
                $i=0;
                for($i=0; $i < count($productos); $i++){
                    echo "ar_items[$i]={";
                    echo "id:'" .       $productos[$i]["product_id"] . "',";
                    echo "rubro_id:'". 	$productos[$i]["rubro_id"] . "',";
                    echo "name:'" .     $productos[$i]["name"] . "',";
                    echo "quantity:" .  $productos[$i]["quantity"] . ",";
                    echo "cost:" .      round($productos[$i]["cost"],2) . ",";
                    echo "subtotal:'" . round($productos[$i]["subtotal"],2) . "'";
                    echo "}\n";
                }

            }else{ // CASO NUEVO

                // NO VA NADA

            }
        }

    ?>

    function grabar_suppliers(){
        let parametros = {
            name:   $("#name").val(),
            email:  $("#email").val(),
            contact:$("#contact").val(),
            phone:  $("#phone").val(),
            cf1:    $("#cf1").val(),
            cf2:    $("#cf2").val(),
            modal_sup: "modal"
        }
        $.ajax({
            data    : parametros,
            type    : 'get',
            url     : '<?php echo base_url('suppliers/add'); ?>',
            success: function(response){
                //alert(response)
                let objS = JSON.parse(response)

                const $select = $("#supplier");
                let rpta = objS.rpta 
                if (rpta == 1){
                    let valor = objS.valor
                    let texto = objS.texto
                    $select.append($("<option>", {
                        value: valor,
                        text: texto
                    }));
                }else{
                    alert("Corrija, el proveedor no se grabó, talvez ya exista ese nombre")
                }

            },
            error: function(){
                alert("No hay respuesta del Servidor.")
            }
        })
    }

    function Agregar(){
        // previa validacion:
        if ($("#quantity").val() <= 0){
            alert("Cantidad no puede ser 0 negativo")
            return false
        }

        if ($("#cost").val() <= 0){
            alert("Costo no puede ser negativo")
            return false
        }

        if( empty($("#rubro_id").val()) ){
            alert("Rubro no puede estar vacio")
            return false
        }

        agregar_item(); // lo ingresa al array ar_items
        
        cargar_items();

        // Borrando valores casillas
        $("#quantity").val('')
        $("#cost").val('')
        $("#product_id").val("")
        $("#peso_caja").val(1)
    }

    function cambiar_a_guia(){
        let limite = ar_items.length
        let costo = 0
        let i=0
        console.log("limite" + limite)
        for(i=0; i<limite; i++){
            cost = ar_items[i]["cost"] * (1 + (gIgv / 100))
            console.log("cost:" + cost)
            ar_items[i]["cost"] = cost
            let nSubtotal = ar_items[i]["quantity"] * cost
            ar_items[i]["subtotal"] = nSubtotal
        }
        cargar_items()
    }

    function cargar_items(){
        let Limite = ar_items.length
        let gsubTotal = 0
        let cad = ""

        cad += "<div class='table-responsive'>"
        cad += '<table id="clasico" class="table table-striped table-bordered">'
        cad += '<thead>'
        cad += '    <tr class="active">'
        cad += '        <th class="col-xs-4 col-sm-2 col-md-2" style="max-width:60px !important"><?= lang("product"); ?></th>'
        cad += '        <th class="col-xs-4 col-sm-3 col-md-3" >Observaciones</th>'
        cad += '        <th class="col-xs-2 col-sm-1"><?= lang("quantity"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-2">P.U sin Igv</th>'
        //cad += '        <th class="col-xs-2 col-sm-1">Peso_caja</th>'
        cad += '        <th class="col-xs-2 col-sm-2" style="min-width:60px; width:25px;color:red;font-style:normal;">P.U</th>'
        cad += '        <th class="col-xs-2 col-sm-2" style="text-align:right"><?= lang("subtotal"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-1" style="max-width:25px;"><i class="fa fa-trash-o"></i></th>'
        
        cad += '    </tr>'
        cad += '</thead>'
        cad += '<tbody>'
        
        console.log("funcion cargar_items : ar_items:"+JSON.stringify(ar_items))

        for(let i=0; i<Limite; i++){
            cad += "<tr>"
            
            // Nombre
            cad += '<td style="text-align: left">' + ar_items[i]["name"] + '<input type="hidden" name="product_id[]" value="'+ar_items[i]['id'] + '" class="form-control"></td>'
            
            cad += '<td style="text-align: left">' + ar_items[i]["detalle"] 

            cad += '<input type="hidden" name="rubro_id[]" value="' + ar_items[i]['rubro_id'] + '">'
            cad += '<input type="hidden" name="detalle[]" value="' + ar_items[i]['detalle'] + '"></td>'

            
            // Quantity
            cad += '<td><input size="4" style="text-align: right" type="text" name="quantity[]" value="' + ar_items[i]["quantity"] + '"  class="form-control" readonly></td>'
            
            // Costo
            cad += '<td><input size="9" style="text-align: right" type="text" name="cost[]" value="' + ar_items[i]["cost"] + '"  class="form-control" readonly></td>'
            
            // con Igv
            cad += '<td style="color:red"><input size="9" style="text-align: right" type="text" name="precio[]" value="' + ar_items[i]["precio"] + '"  class="form-control" readonly>' + '</td>'

            // Subtotal
            let nSubTotalx = ar_items[i]["subtotal"] * 1
            // .toLocaleString('es-PE',{ style: 'currency', currency: 'PEN' })
            cad += '<td style="text-align: right">' + nSubTotalx.toLocaleString('es-PE') + "</td>"
            
            // Trash
            cad += '<td style="text-align: center"><a href="#" onclick="quitar_item(ar_items,'+i+')"><i class="fa fa-trash-o"></i></a></td>'
            
            cad += "</tr>"
            gParcial = 1 * ar_items[i]["quantity"] * ar_items[i]["cost"]
            
            gsubTotal += gParcial

            //console.log("vamos ....[i]:" + i +  ", quantity:" + ar_items[i]["quantity"] + ", cost:" + ar_items[i]["cost"] + ", gsubTotal:" + gsubTotal)
        }
        
        // Tema de descuentos .......
        let nDscto = cDscto = 0
        if($("#descuentos").length > 0){
            nDscto = $("#descuentos").val() * 1
            nDscto = nDscto.toFixed(2)
            cDscto = "Dscto. = " + nDscto
        }

        cad += '</tbody>'

        cad += '<tfoot>'

        // *** FILA SUBTOTAL **********
        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"><?= lang('subtotal'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)" class="text-right"><span id="gsubtotal">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;width:25px;height:17px;background-color:rgb(200,200,200)"></th>'
        cad += '    </tr>'

        // *** FILA IGV **********
        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"><?= lang('igv'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(190,190,190)" class="text-right"><span id="gIgv">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;width:25px;height:17px;background-color:rgb(190,190,190)" ></th>'
        cad += '    </tr>'

        
        // *** FILA DSCTO **********
        if(nDscto > 0){
            cad += '    <tr class="active">'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);color:rgb(200,0,0);font-weight:bold">Dscto.</th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"></th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
            //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);color:rgb(200,0,0)" class="text-right">-'+nDscto+'</th>'
            cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);width:25px;"></th>'
            cad += '    </tr>'
        }

        // *** FILA TOTAL **********
        cad += '    <tr class="active">'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"><?= lang('total_1'); ?></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(200,200,200)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)"></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="text-right"><span id="gTotal">0.00</span></th>'
        cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185);width:25px;"></th>'
        cad += '    </tr>'
        cad += '</tfoot>'
        cad += "</table>"

        cad += "</div>"
        
        document.getElementById("taxi").innerHTML = cad

        var nIgv = 0
        var gTotal = 0

        var cSubTotal   = gsubTotal.toFixed(2) * 1;
        cSubTotal       = cSubTotal.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})  // ,{ style: 'currency', currency: 'PEN' }

        var redondeo    = document.getElementById("redondeo").value * 1

        if(document.getElementById("tipoDoc").value != 'G'){
            nIgv    = gsubTotal * (gIgv/100)

            var cIgv        = nIgv.toFixed(2) * 1;
            cIgv            = cIgv.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})

            gTotal          = (gsubTotal.toFixed(2) * 1) + (nIgv.toFixed(2) * 1) - (nDscto * 1) + redondeo
            var cTotal      = gTotal.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})

        }else{
            var cSubTotal       = 0.00
            var cIgv            = 0.00
            var nTotal_2        = (gsubTotal * 1) + redondeo
            var cTotal          = Math.round(nTotal_2*100)/100
            gTotal              = gsubTotal 
            gsubTotal           = 0.00
        }

        document.getElementById("gsubtotal").innerHTML      = cSubTotal
        document.getElementById("gIgv").innerHTML           = cIgv
        document.getElementById("gTotal").innerHTML         = cTotal
        
        $("#txt_gSubtotal").val(gsubTotal.toFixed(2))
        $("#txt_gIgv").val(nIgv.toFixed(2))
        $("#txt_gTotal").val(gTotal.toFixed(2)) // Number.parseFloat
    }

    cargar_items()

    function empty(data){
      if(typeof(data) == 'number' || typeof(data) == 'boolean')
      { 
        return false; 
      }
      if(typeof(data) == 'undefined' || data === null)
      {
        return true; 
      }
      if(typeof(data.length) != 'undefined')
      {
        return data.length == 0;
      }
      var count = 0;
      for(var i in data)
      {
        if(data.hasOwnProperty(i))
        {
          count ++;
        }
      }
      return count == 0;
    }

    function verifica_unicidad_items(un_id){
        let Limite = ar_items.length
        for(let i=0; i<Limite; i++){
            if(un_id == ar_items[i]["id"]){
                return false
            }
        }
        return true
    }

    function validar_gral(){
       
        // La fecha
        if(empty($("#date").val())){
            alert("Debe ingresar la Fecha")
            return false
        }

        const months = ["01","02","03","04","05","06","07","08","09","10","11","12"]
        var grupo = '<?= $_SESSION["group_id"] ?>'
        if (grupo != '1'){
            var fes = new Date()
            var fDia = '0' + fes.getDate() + ""
            var cFes = fes.getFullYear() + "-" + months[fes.getMonth()] + "-" + fDia.substr(-2)
            var cinth = $("#date").val()
            cinth = cinth.substr(0,10)
/*
            if (cinth != cFes){
                alert("La fecha debe ser de hoy día:" + cinth + " vs " + cFes)
                return false
            }

            var cinth2 = $("#date_ingreso").val()
            cinth2 = cinth2.substr(0,10)

            if (cinth2 != cFes){
                alert("La fecha de Ingreso a Almacen debe ser de hoy día")
                return false
            }
*/
        }

        let tipoDoc = document.getElementById("tipoDoc").value
        
        if(empty(document.getElementById("fec_emi_doc").value)){
            alert("Falta Fec_emi_doc")
            document.getElementById("fec_emi_doc").focus()
            return false
        }
        
        if(empty(document.getElementById("fec_venc_doc").value)){
            alert("Falta Fec_venc_doc")
            document.getElementById("fec_venc_doc").focus()
            return false
        }

        if(document.getElementById("fec_emi_doc").value > document.getElementById("fec_venc_doc").value){
            alert("Fecha de Emision no puede ser mayor a fecha de vencimiento")
            document.getElementById("fec_venc_doc").focus()
            return false
        }
        
        if(document.getElementById("tienda").value == '0'){
            alert("Debe escoger una Tienda")
            document.getElementById("tienda").focus()
            return false
        }


        var nroDoc = document.getElementById("nroDoc").value
        
        if(tipoDoc != 'G'){
            if (nroDoc.length == 0){
                alert("Debe ingresar el Nro. de Documento.")
                document.getElementById("nroDoc").focus()
                return false
            }
        }

        nCargo = document.getElementById("cargo_servicio").value
        if(!empty(nCargo)){
            nCargo = nCargo * 1
            if (nCargo < 0){
                alert("Cargo es menor a cero")
                return false
            }
        }else{
            nCargo = 0
        }

        if(document.getElementById('tipogasto').value == 'caja'){

            let Proveedor = document.getElementById("supplier").value
            if(empty(Proveedor)){
                alert("Debe ingresar Proveedor.")
                document.getElementById("supplier").focus()
                return false
            }

            // Verificando que los montos no sean negativos
            let ct = document.getElementById("costo_tienda").value
            let cb = document.getElementById("costo_banco").value

            if (!empty(ct)){
                if(ct<0){
                    alert("Costo Tienda No puede ser menor a cero")
                    return false
                }
            }

            if (!empty(cb)){
                if(cb<0){
                    alert("Costo Banco No puede ser menor a cero")
                    return false
                }
            }

            let nTotal = (ct * 1) + (cb * 1)
            if (nTotal > 0){
                
                if (!Admin){
                    //alert("No puede colocar Montos Pagados ni de Cajas, ni Tiendas, esto lo hace el Administrador")
                    //return false
                }
            }

            // NUEVO:

            // Total productos:
            let gTotal = $("#gTotal").html() * 1

            // cargo_servicio:
            // nCargo

            // Total gral:
            var nTotalP = gTotal + nCargo
            //alert("gTotal:" + gTotal + ", nCargo:"+nCargo + ",nTotalP:" + nTotalP)

            // Total de cajas:
            // nTotal

            // Compraciones:
            if (nTotalP < nTotal){
                alert("Los total de Pago Tienda y Banco exceden al total del Documento (Total productos:"+nTotalP + ", Total cajas:" + nTotal + ")")
                return false
            }
        
            // Verificar que este documento no haya sido ingresado anteriormente
            <?php if(!isset($purchases_id)){ ?>
                let obj1 = document.getElementById("nroDoc")
                let obj2 = document.getElementById("supplier")
                if(obj1.length > 0 && obj2.length > 0){
                    if(!verificarUnicidad()){
                        alert("Este nro. de Documento con el mismo Proveedor ya existe")
                        return false
                    }else{
                        console.log("Todo correcto con la Unicidad.")
                    }
                }
            <?php } ?>
        }
        if(document.getElementById('tipogasto').value == 'gastos'){
            let Proveedor = document.getElementById("texto_supplier").value
            if(empty(Proveedor)){
                alert("Debe ingresar Proveedor.")
                document.getElementById("texto_supplier").focus()
                return false
            }

            // EN EL CASO DE GASTOS (BORRAR SUBTOTAL E IGV)
            document.getElementById("txt_gSubtotal").value = 0
            document.getElementById("txt_gIgv").value = 0
        }

        var nro_cta     = document.getElementById("nro_cta").value
        //var nro_oper    = document.getElementById("nro_oper").value
        var banco       = document.getElementById("banco").value
        let fecha_oper  = document.getElementById("fecha_oper").value
        let cb1 = document.getElementById("costo_banco").value
        if(!empty(cb1) && cb1*1 > 0){
            if(empty(nro_cta)){
                alert("Ingrese Nro_cta")
                document.getElementById("nro_cta").focus()
                return false
            }

            //if(empty(nro_oper)){
            //    alert("Ingrese Nro_operacion")
            //    document.getElementById("nro_oper").focus()
            //    return false
            //}

            if(empty(banco)){
                alert("Ingrese Banco")
                document.getElementById("banco").focus()
                return false
            }

            if(empty(fecha_oper)){
                alert("Ingrese Fecha de operacion")
                document.getElementById("fecha_oper").focus()
                return false
            }
        }

        return true
    }

    function guardar_compra(){
        if(validar_gral()){
            document.getElementById("form_compra").submit();
        }
    }

    function marcar(i){
        document.getElementById("marca" + i).value = 2
    }

    function agregar_item(){
        let x1      = document.getElementById("product_id").value
        let combo   = document.getElementById("product_id");
        let x1_name = combo.options[combo.selectedIndex].text;
        let x2      = document.getElementById("quantity").value
        let x3      = document.getElementById("cost").value
        var x5      = parseFloat(x3)
        var x6      = document.getElementById("rubro_id").value
        var x7 		= document.getElementById("detalle").value

        if(document.getElementById("tipoDoc").value != 'G'){
            if(document.getElementById("chk_igv").checked == true){
                x3 = x3 / (1+(gIgv/100))
                x3 = x3.toFixed(4)
            }else{
            	x5 = x5 * (1+(gIgv/100))
            	x5 = x5.toFixed(4)
            }

            var x4      = 1 * x2 * x3
            x4          = x4.toFixed(2)
        }else{
            // el igv es como si fuera 0%
            x3      = x3 * 1
            x3      = x3.toFixed(4)
            var x4  = 1 * x2 * x3
            x4      = x4.toFixed(2)
        }

        ar_items.push({id:x1, 
            name:       x1_name, 
            quantity:   x2, 
            cost:       x3, 
            subtotal:   x4,
            precio:     x5,
            rubro_id:   x6,
            detalle: 	x7
        })
        console.log("Se agrega Item...")
    }

    function quitar_item(aro,pid){
        quitar_elemento(aro,pid)
        cargar_items()
    }

    function quitar_elemento(aro,pid){  // function OK
        aro.splice(pid,1)
    }

    function cadenar(are){
        return JSON.parse(are)
    }

    function agrega_fila(j){
        document.getElementById("column"+j).style.display = "block"
    }

    function verificarUnicidad(){
        let parametros = {
            nroDoc: $("#nroDoc").val(),
            supplier: $("#supplier").val()
        }
        $.ajax({
            data: parametros,
            type: 'get',
            url: '<?= base_url("purchases/verificarUnicidad") ?>',
            success: function(response){
                if(response * 1 > 0){
                    return false
                }
                return true
            },
            error: function(){
                alert("Ocurre un Problema con VerificarUnicidad")
                return false
            }
        })
    }

    var valor_anterior_td = ""

    function el_foco(mensaje){
        valor_anterior_td = document.getElementById("tipoDoc").value
        //console.log(mensaje + "  " + valor_anterior_td)
    }

    function generar_nro(obj){
        if((obj.value == "G" && (valor_anterior_td == "F" || valor_anterior_td == "B" )) || ( valor_anterior_td == "G" && ( obj.value == 'F' || obj.value == 'B' ) ) ){    
            if(ar_items.length > 0){
                let rpta = confirm("deberá volver a ingresar los productos ... confirme?")
                if(rpta){
                    ar_items.splice(0,ar_items.length)
                    cargar_items()
                    valor_anterior_td = obj.value
                }else{
                    setTimeout('document.getElementById("tipoDoc").value = valor_anterior_td; document.getElementById("supplier").focus(); el_foco("en setTime")',700)
                }
            }
        }

        if(obj.value == "G"){
            document.getElementById("nroDoc").value = ""        
            document.getElementById("nroDoc").readOnly = true
        }else{
            document.getElementById("nroDoc").readOnly = false
        }

    }

    function grabar_insumos(){
        var parametros = {
            descPro     : document.getElementById('descPro').value,
            unidad      : document.getElementById('unidad').value,
            idPro       : document.getElementById('idPro').value
        }
        $.ajax({
            data    : parametros,
            url     :'<?= base_url('insumos/grabar_insumos') ?>',
            type    :'get',
            success :function(response){
                
                var ar = JSON.parse(response)
                var cad = ""
                
                if(ar.error == true){
                    alert(ar.rpta)
                }
                
                const $select = $("#product_id");
                
                let texto = ar.rpta 
                
                // mejorando
                let nin     = texto.indexOf(",")
                let cDescrip  = texto.substr(0,nin)
                
                ubi2 = texto.indexOf(",",nin+1)

                let cId = texto.substr(nin+1,ubi2-(nin+1))

                ubi3 = texto.indexOf(",",ubi2)

                let cUnidad = texto.substr(ubi2+1,texto.length-(ubi2+1)-1)
                
                $select.append($("<option>", {
                    value: cId,
                    text: cDescrip + " (" + cUnidad + ")"
                }));

            }
        })
    }

    function peka(opcion=0){
        console.log("peka")
    }

    $(document).ready( function(){
        <?php if($tipo_egreso == 'producto'){ $elemento = "producton"; }else{ $elemento = "serviton";} ?>
        document.getElementById("<?= $elemento ?>").style.backgroundColor = "orange";
        //$('#banco').change()

        //var event = new Event('onblur');  // Create a new 'change' event
		//document.getElementById('banco').dispatchEvent(event); // Dispatch it.
		obtener_cuentas()
		
    })
    
</script>