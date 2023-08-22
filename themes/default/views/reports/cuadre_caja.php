<form action="<?= base_url("reports/cuadre_caja") ?>" method="get">
<div class="row" style="margin-bottom:0px; padding-bottom:0px;">
    <div class="col-sm-3" style="border-style:none; border-color:red; margin:20px 10px 5px 20px;">
        <div class="form-group">
            <label for="">Tienda:</label>
            <?php
                $q = $this->db->get('stores');
                $ar[] = "Todas";
                foreach($q->result() as $r){
                    $ar[$r->id] = $r->state;
                }
                echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required"');
            ?>
        </div>
    </div>

    <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin:43px 20px 5px 20px;">
        <button type="submit" class="btn btn-primary">Consultar</button>
    </div>
</div>
</form>

<?php if(isset($tienda)){ ?>
<div class="row">
	<div class="col-sm-11" style="margin: 10px">
		<!--<h2>Tienda: <?= $tienda ?></h2>-->

		<h3 style="margin-top:0px">Ventas en Efectivo</h3>

	    <?php
	    	$cols 			= array("id","date","serie","correlativo","tipoDoc","envio_electronico","status","paid_by","grand_total");
			$cols_titulos 	= array("id","date","serie","correlativo","tipoDoc","envio_electronico","status","paid_by","grand_total"); 
			$ar_align 		= array(1, 1, 1, 1, 1, 1, 1, 1, 1);
			$ar_pie 		= array("","","","","","","","","suma");

			echo $this->fm->crea_tabla_result($result1, $cols, $cols_titulos, $ar_align, $ar_pie);
		?>

		<h3 style="margin-top:0px">Gastos de Compras (Caja-tienda)</h3>

	    <?php
	    	$cols 			= array("id","date", "tipoDoc", "nroDoc", "name", "total", "costo_tienda");
			$cols_titulos 	= array("id","Fecha", "Tipo Docu", "nroDoc", "Proveedor", "Total", "Monto");
			$ar_pie 		= array("","","","","","","suma"); 

			echo $this->fm->crea_tabla_result($result2, $cols, $cols_titulos, $ar_align, $ar_pie);
		
			$simbolo = "<span style=\"color:red;font-weight:bold\">S/&nbsp;&nbsp;</span>";
			
			$cSaldo_inicial 	= $simbolo . number_format($nSaldo_inicial, 2);
			$cVentas 			= $simbolo . number_format($nVentas,2);
			$cCompras 			= $simbolo . number_format($nCompras,2);
			
			$nSaldo 			= $nSaldo_inicial + $nVentas - $nCompras;
			$cSaldo 			= $simbolo . number_format($nSaldo,2);
		?>

	<style type="text/css">
		.resumen-cuadre-caja{
			border-style: solid;
			font-size: 14px;
			font-family: arial;
		}
		.resumen-cuadre-caja th{
			font-size: 14px;
			font-family: arial;
			margin: 10px 10px 10px 10px;
			padding: 10px 10px 10px 10px;
		}
	</style>
		
		<h3>Cuadre de Caja Diario</h3>

		<div class="row">
			<div class="col-sm-4 col-md-3">
				<table class="resumen-cuadre-caja">
					<tr style="background-color:orange">
						<th class="col-sm-1 text-left">Saldo Inicial</th>
						<th class="col-sm-1 text-right"><?= $cSaldo_inicial ?></td>
					</tr>

					<tr>
						<th class="col-sm-1 text-left">Ventas</th>
						<th class="col-sm-1 text-right"><?= $cVentas ?></td>
					</tr>

					<tr>
						<th class="col-sm-1 text-left">Compras</th>
						<th class="col-sm-1 text-right"><?= $cCompras ?></td>
					</tr>

					<tr style="background-color:yellow">
						<th class="col-sm-1 text-left">Saldo</th>
						<th class="col-sm-1 text-right"><?= $cSaldo ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<?php } ?>