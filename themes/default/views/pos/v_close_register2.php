<?php

//echo "Se termina esto $user_id $tienda";

if(isset($tienda)){

	if(!isset($fec_ini) or $fec_ini == "null"){
	    $fec_ini = date("Y-m-01");
	}

	if(!isset($fec_fin) or $fec_fin == "null"){
	    $fec_fin = date("Y-m-d");
	}

	$result = $this->db->query("select * from tec_stores where id = ?",array($tienda))->result();
	foreach($result as $r){
		$tienda_descrip = $r->state;
	}

	// DETALLE DE VENTAS
	$cSql = "select date_format(tc.fecha,'%d-%m-%Y') as fecha, date(tc.fecha) fecha_ymd, tc.dia_semana, 
	tr.cash_in_hand cash_inicial, tr.cash_in_hand_adicional cash_inicial_adicional, 
	b.cash, b.vendemas, b.transferencia, b.yape, b.plin, b.rappi, b.pedidosya, b.otros, 
    b.cash + b.vendemas + b.transferencia + b.yape + b.plin + b.rappi + b.pedidosya + b.otros as total, 
    if(remesas.remesa is null,0,remesas.remesa) remesa,
    tr.cash_in_hand + tr.cash_in_hand_adicional + if(b.cash is null,0,b.cash) - if(remesas.remesa is null, 0, remesas.remesa) as cash_final,
    tr.cash_in_hand + tr.cash_in_hand_adicional + if(b.cash is null,0,b.cash) + if(b.vendemas is null,0,b.vendemas) + if(b.transferencia is null,0,b.transferencia) + if(b.yape is null,0,b.yape) + if(b.plin is null,0,b.plin) + if(b.rappi is null,0,b.rappi) + if(b.pedidosya is null,0,b.pedidosya) + if(b.otros is null,0,b.otros) as total_total,
    if(tr.status is null,'',tr.status) status
    from tec_calendario tc
    left join 
    (
        select date_format(ts.date, '%d-%m-%Y') fecha, 
            sum(tp.cash) cash,
            sum(tp.vendemas) vendemas,
            sum(tp.transferencia) transferencia,
            sum(tp.yape) yape,
            sum(tp.plin) plin,
            sum(tp.rappi) rappi,
            sum(tp.pedidosya) pedidosya,
            sum(tp.otros) otros
        from tec_sales ts
        inner join 
        (
            select sale_id, 
            sum(if(paid_by = 'cash',amount,0)) cash,
            sum(if(paid_by = 'Vendemas',amount,0)) vendemas,
            sum(if(substr(paid_by,1,6)='Transf',amount,0)) transferencia,
            sum(if(paid_by = 'Yape',amount,0)) yape,
            sum(if(paid_by = 'Plin',amount,0)) plin,
            sum(if(paid_by = 'Rappi',amount,0)) rappi,
            sum(if(paid_by = 'PedidosYa',amount,0)) pedidosya,
            sum(if(paid_by not in ('cash','Vendemas','Yape','Plin','Rappi','PedidosYa') and substr(paid_by,1,6)!='Transf',amount,0)) otros
            from tec_payments where note != 'PASE'
            group by sale_id
        ) tp on ts.id = tp.sale_id      
        where ts.store_id = $tienda
        group by date_format(ts.date, '%d-%m-%Y')
    ) b on date_format(tc.fecha,'%d-%m-%Y') = b.fecha
    left join (
    	select date_format(a.date,'%d-%m-%Y') fecha, a.store_id, b.product_id, sum(b.quantity*b.cost) remesa 
		from tec_purchases a
		inner join tec_purchase_items b on a.id = b.purchase_id
		inner join tec_products c on b.product_id = c.id 	
	  	where c.name = 'REMESA' and a.store_id = $tienda
	    group by date_format(a.date,'%d-%m-%Y'), a.store_id, b.product_id 
	) remesas on date_format(tc.fecha,'%d-%m-%Y') = remesas.fecha
    inner join tec_registers tr on tc.fecha = date(tr.date)
    where tr.store_id = $tienda
    order by tc.fecha desc limit 1";

	//echo $cSql;

	$query2 = $this->db->query($cSql);
	$simbolo = "<span style=\"color:red\">S/</span>&nbsp;&nbsp;&nbsp;";
?>
	<div class="row" style="margin-left:10px; background: white;">
	    <div class="col-xs-12 col-sm-11">
	    	<h2><?= $tienda_descrip ?></h2>
	    </div>
	</div>

	<div class="row" style="margin-left:10px; background: white;">
	    <div class="col-xs-12 col-sm-11">
	            <div class="box box-primary" id="resumen_ventas" style="margin-bottom:20px;display:block">
	                <div class="box-body">
	                    <table class="table table-striped table-bordered table-condensed table-hover">
	                        <caption style="font-weight:bold; font-size: 18px; color:rgb(30,90,150)"></caption>
	                        <tr class="active" style="color:rgb(255,100,100)">
	                            <th class="col-xs-2 col-sm-2"></th>
	                            <th class="col-xs-2 col-sm-1"></th>
	                            <th class="col-sm-1">A</th>
	                            <th class="col-sm-1">B</th>
	                            <th class="col-sm-1">C</th>
	                            <th class="col-sm-1">D</th>
	                            <th class="col-sm-1">E</th>
	                            <th class="col-sm-1">F</th>
	                            <th class="col-sm-1">G</th>
	                            <th class="col-sm-1">H</th>
	                            <th class="col-sm-1">I</th>
	                            <th class="col-sm-1">J</th>
	                            <th class="col-sm-1">K</th>
	                            <th class="col-sm-1">L</th>
	                            <th class="col-sm-1">M</th>
	                            <th class="col-sm-1">N</th>
	                        </tr>
	                        <tr class="active">
	                            <th class="col-xs-2 col-sm-2" style="">Fecha</th>
	                            <th class="col-xs-2 col-sm-1" style="">Dia</th>
	                            <th class="col-sm-1" style="color:blue">Efectivo Inicio</th>
	                            <th class="col-sm-1" style="color:blue">Efectivo inicio Ad.</th>
	                            <th class="col-sm-1">Venta en Efectivo</th>
	                            <th class="col-sm-1">Venta con tarjeta</th>
	                            <th class="col-sm-1">Transf. Bancaria</th>
	                            <th class="col-sm-1">Venta con Yape</th>
	                            <th class="col-sm-1">Venta con Plin</th>
	                            <th class="col-sm-1">Venta con Rappi</th>
	                            <th class="col-sm-1">PedidosYa</th>
	                            <th class="col-sm-1">Consumo Personal</th>
	                            <th class="col-sm-1" style="color:blue">Remesas</th>
	                            <th class="col-sm-1">Efectivo Final</th>
	                            <th class="col-sm-1">Cuadre Final</th>
	                            <th class="col-sm-1" style="color:blue">Status</th>
	                        </tr>
	                        
	                        <?php
	                            foreach($query2->result() as $r){
	                                echo "<tr>";
	                                echo $this->fm->celda_h($r->fecha,0,"color:rgb(60,120,190)");
	                                echo $this->fm->celda($r->dia_semana);
	                                echo $this->fm->celda(number_format($r->cash_inicial,2),2,"text-align:right");
	                                echo $this->fm->celda(number_format($r->cash_inicial_adicional,2),2,"text-align:right");
	                                echo $this->fm->celda(number_format($r->cash,2),2,"text-align:right");
	                                echo $this->fm->celda(number_format($r->vendemas,2));
	                                echo $this->fm->celda(number_format($r->transferencia,2));
	                                echo $this->fm->celda(number_format($r->yape,2));
	                                echo $this->fm->celda(number_format($r->plin,2));
	                                echo $this->fm->celda(number_format($r->rappi,2));
	                                echo $this->fm->celda(number_format($r->pedidosya,2));
	                                echo $this->fm->celda(number_format($r->otros,2));
	                                echo $this->fm->celda(number_format($r->remesa,2));
	                                echo $this->fm->celda_h($simbolo . number_format($r->cash_final,2),2,"text-align:right;padding-right:10px");
	                                echo $this->fm->celda_h($simbolo . number_format($r->total_total,2),2,"text-align:right;padding-right:10px");
	                                
	                                if($r->status == "open"){
	                                	$lo_demas =  "'" . $r->fecha_ymd . "'," . (is_null($r->cash) ? "0" : $r->cash) .",". 
	                                		(is_null($r->vendemas) ? "0" : $r->vendemas) .",". 
	                                		(is_null($r->transferencia) ? "0" : $r->transferencia) .",". 
	                                		(is_null($r->yape) ? "0" : $r->yape) .",". 
	                                		(is_null($r->plin) ? "0" : $r->plin) .",". 
	                                		(is_null($r->rappi) ? "0" : $r->rappi) .",". 
	                                		(is_null($r->pedidosya) ? "0" : $r->pedidosya) .",". 
	                                		(is_null($r->otros) ? "0" : $r->otros) . "," . 
	                                		(is_null($r->cash_final) ? "0" : $r->cash_final) . "," . 
	                                		(is_null($r->total_total) ? "0" : $r->total_total);
	                                	$status = "<button type=\"button\" onclick=\"cerrar_caja(" . $lo_demas . ")\" class='btn btn-danger'>Cerrar</button>";
	                                	
	                                	//$status = $lo_demas;
	                                }else{
	                                	$status = $r->status;
	                                }

	                                echo $this->fm->celda($status);
	                                echo "</tr>";
	                            }
	                        ?>
	                        
	                    </table>
	                </div>
	            </div>
	            <span style="color:rgb(250,100,100);font-weight:bold">Nota.- Para calcular el Efectivo Final = A + B + C - K</span>
	    </div>
	</div>

	<div class="row" style=" margin-left:10px; background: white;">
	    <div class="form-group col-sm-6" style="">
		    <label>Observaciones</label>
		    <textarea class="form-control" name="obs1" id="obs1"></textarea>
	    </div>
	</div>

	
	<!-- *********************************************************** Cierre de Caja Anteriores ************************************************* -->
	
	<div class="row" style="margin-left:10px; background: white;">
	    <div class="col-xs-12 col-sm-11">
	            <div class="box box-primary" id="resumen_ventas" style="margin-bottom:20px;display:block">
	                <div class="box-body">
	                    <table class="table table-striped table-bordered table-condensed table-hover">
	                        <caption style="font-weight:bold; font-size: 18px; color:rgb(30,90,150)"></caption>

<?php 
	// Mostrando Cierre de Caja de dias anteriores (OJO: Muestra la informacion grabada en la tabla tec_registers)
	$cSql = "select date_format(tc.fecha,'%d-%m-%Y') as fecha, tc.dia_semana,
		date(tr.date) fecha, tr.cash_in_hand, tr.cash_in_hand_adicional, tr.total_cash, tr.store_id, tr.total_tarjeta, tr.total_transf, tr.total_yape, tr.total_plin, tr.total_deli1, tr.total_deli2, tr.total_personal, tr.monto_final_cash, tr.total_total, if(tr.note is null,'',tr.note) note, tr.status 
		from tec_calendario tc
		inner join tec_registers tr on tc.fecha = date(tr.date)
		where tr.store_id = ? and date(tr.date) < curdate() order by tr.date desc limit 2";

	//echo $cSql;

	$query = $this->db->query($cSql,array($this->session->userdata('store_id')));

	echo "<tr>";
	echo $this->fm->celda_h("Fecha");
	echo $this->fm->celda_h("Dia");
	echo $this->fm->celda_h("Efectivo<br>Inicio",0,"color:blue");
	echo $this->fm->celda_h("Efectivo<br>Inicio Adi",0,"color:blue");
	echo $this->fm->celda_h("Efectivo<br>del dia");
	echo $this->fm->celda_h("Venta<br>Tarjeta");
	echo $this->fm->celda_h("Venta<br>Transf");
	echo $this->fm->celda_h("Venta<br>Yape");
	echo $this->fm->celda_h("Venta<br>Plin");
	echo $this->fm->celda_h("Venta<br>Rappi");
	echo $this->fm->celda_h("Venta<br>PedidosYa");
	echo $this->fm->celda_h("Consumo<br>personal");
	echo $this->fm->celda_h("Efectivo<br>Final");
	echo $this->fm->celda_h("Cuadre<br>Final");
	echo $this->fm->celda_h("Status",0,"color:blue");
	echo "</tr>";

	foreach($query->result() as $r){
		echo "<tr>";
		echo $this->fm->celda($r->fecha);
		echo $this->fm->celda($r->dia_semana);
		echo $this->fm->celda(number_format($r->cash_in_hand,2));
		echo $this->fm->celda(number_format($r->cash_in_hand_adicional,2));
		echo $this->fm->celda(number_format($r->total_cash,2));
		echo $this->fm->celda(number_format($r->total_tarjeta,2));
		echo $this->fm->celda(number_format($r->total_transf,2));
		echo $this->fm->celda(number_format($r->total_yape,2));
		echo $this->fm->celda(number_format($r->total_plin,2));
		echo $this->fm->celda(number_format($r->total_deli1,2));
		echo $this->fm->celda(number_format($r->total_deli2,2));
		echo $this->fm->celda(number_format($r->total_personal,2));
		echo $this->fm->celda(number_format($r->monto_final_cash,2));
		echo $this->fm->celda(number_format($r->total_total,2));
		echo $this->fm->celda("<span title='". $r->note ."'>".$r->status."</span>");
		echo "</tr>";
	}
} 
?>
	                    </table>
	                </div>
	            </div>
	            
	    </div>
	</div>



<script type="text/javascript">
    function cerrar_caja(diat, cash_finalx, total_tarjeta1,	total_transf1, total_yape1, total_plin1, total_deli1_, total_deli2_, total_personal1, monto_final_cash1, total_total1){
        $.ajax({
            data:{
            	dia: diat, 
            	cash_final:cash_finalx, 
            	usuario: '<?= $this->session->userdata('user_id') ?>', 
            	tienda: '<?= $this->session->userdata('store_id') ?>',
            	total_tarjeta: total_tarjeta1,
            	total_transf: total_transf1,
            	total_yape: total_yape1,
            	total_plin: total_plin1,
            	total_deli1: total_deli1_,
            	total_deli2: total_deli2_,
            	total_personal: total_personal1,
            	monto_final_cash : monto_final_cash1,
            	total_total: total_total1,
            	nota: document.getElementById("obs1").value
            },
            url: "<?= base_url("pos/close_caja") ?>",
            type:"get",
            success: function(res){
                alert(res)
                location.reload()
            }
        })
    }
</script>