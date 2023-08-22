<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
if(!isset($tienda)){
    $tienda = "1";
}

if(!isset($fec_ini) or $fec_ini == "null"){
    $fec_ini = date("Y-m-01");
}

if(!isset($fec_fin) or $fec_fin == "null"){
    $fec_fin = date("Y-m-d");
}

// DETALLE DE VENTAS
$cSql = "select date_format(tc.fecha,'%d-%m-%Y') as fecha, tc.dia_semana, b.cash, b.vendemas, b.transferencia, b.yape, b.plin, b.rappi, b.pedidosya, b.otros, b.total from tec_calendario tc
    left join 
    (
        select date_format(ts.date, '%d-%m-%Y') fecha, 
            sum(if(tp.paid_by='cash',ts.grand_total,0)) cash,
            sum(if(tp.paid_by='Vendemas',ts.grand_total,0)) vendemas,
            sum(if(substr(tp.paid_by,1,13)='Transferencia',ts.grand_total,0)) transferencia,
            sum(if(tp.paid_by='Yape',ts.grand_total,0)) yape,
            sum(if(tp.paid_by='Plin',ts.grand_total,0)) plin,
            sum(if(tp.paid_by='Rappi',ts.grand_total,0)) rappi,
            sum(if(tp.paid_by='PedidosYa',ts.grand_total,0)) pedidosya,
            sum(if(tp.paid_by not in ('cash','Vendemas','Yape','Plin','Rappi','PedidosYa') and substr(tp.paid_by,1,13)!='Transferencia',ts.grand_total,0)) otros,
            sum(ts.grand_total) total
        from tec_sales ts
        inner join tec_payments tp on ts.id = tp.sale_id
        where ts.store_id = $tienda
        group by date_format(ts.date, '%d-%m-%Y')
    ) b on date_format(tc.fecha,'%d-%m-%Y') = b.fecha
    where tc.fecha >= '$fec_ini' and tc.fecha <= '$fec_fin'
    order by tc.fecha";

$query2 = $this->db->query($cSql);

// DETALLE RESUMEN - CUADRE DE CAJA
$cSql = "select tc.fecha, 'nada' dia_semana, tr.cash_in_hand_adicional, ts.grand_total, a.con_factura, a.con_boleta, 
    a.con_recibo, a.total_salidas, b.total_depositos,
    if(ts.grand_total is null, 0, ts.grand_total) as total_ventas_efectivo,
        if(ts.grand_total is null, 0, ts.grand_total) 
        - if(a.total_salidas is null,0,a.total_salidas) 
        - if(b.total_depositos is null,0,b.total_depositos) as caja_final
        
    from (
        select date_format(fecha,'%Y-%m') fecha from tec_calendario group by date_format(fecha,'%Y-%m')
    ) tc
    left join (
        SELECT 
            date_format(tp.fec_emi_doc, '%Y-%m') as fecha,
            tp.store_id,
            sum(if(tp.tipoDoc='F', `costo_tienda`, 0)) con_factura, 
            sum(if(tp.tipoDoc='B', `costo_tienda`, 0)) con_boleta, 
            sum(if(tp.tipoDoc not in ('F', 'B'),tp.costo_tienda, 0)) con_recibo, 
            sum(if(tp.costo_tienda is null, 0, tp.costo_tienda)) total_salidas
        FROM `tec_purchases` tp left join tec_subtipo_gastos on tp.clasifica2 = tec_subtipo_gastos.id
        where tp.store_id = $tienda and tec_subtipo_gastos.descrip != 'Remesas'
        GROUP BY date_format(tp.fec_emi_doc, '%Y-%m'), tp.store_id
    ) a on tc.fecha = a.fecha
    left join (
        SELECT date_format(tec_purchases.fec_emi_doc, '%Y-%m') as fecha, tec_purchases.store_id, sum(tec_purchases.total) total_depositos 
            FROM `tec_purchases` left join tec_subtipo_gastos on tec_purchases.clasifica2 = tec_subtipo_gastos.id
            where tec_purchases.store_id = $tienda and tec_subtipo_gastos.descrip = 'Remesas' 
            GROUP BY date_format(tec_purchases.fec_emi_doc, '%Y-%m'), tec_purchases.store_id
    ) b on tc.fecha = b.fecha
    left join (
        select date_format(tec_sales.date, '%Y-%m') fecha, tec_sales.store_id, sum(tec_sales.grand_total) grand_total
        from tec_sales
        inner join tec_payments tp on tec_sales.id = tp.sale_id
        where tp.paid_by = 'cash' and tec_sales.store_id = $tienda
        GROUP BY date_format(tec_sales.date, '%Y-%m'), tec_sales.store_id
    ) ts on tc.fecha = ts.fecha
    order by tc.fecha";
    /*left join (
        select date_format(date, '%Y-%m') fecha, store_id, cash_in_hand, cash_in_hand_adicional, closed_at, status 
        from tec_registers
        where store_id = $tienda 
    ) tr on tc.fecha = tr.fecha order by tc.fecha";*/

//$cado = str_replace("\n","<br>",$cSql);
//echo $cado;

$query = $this->db->query($cSql);

$simbolo = "<span style=\"color:red\">S/</span>&nbsp;&nbsp;&nbsp;";

?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        //echo "setTimeout('abrir_item_menu(5)',500);\n";
    ?>
</script>

<style type="text/css">
    .table td:nth-child(3) { text-align: right;}
    .table td:nth-child(4) { text-align: right;}
    .table td:nth-child(5) { text-align: right;}
    .table td:nth-child(6) { text-align: right;}
    .table td:nth-child(7) { text-align: right;}
    .table td:nth-child(8) { text-align: right;}
    .table td:nth-child(9) { text-align: right;}
    .table td:nth-child(10) { text-align: right;}
    .table td:nth-child(11) { text-align: center;}
</style>

<section class="content">

    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="display:flex;margin-bottom: 5px;">
        <div class="col-sm-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $q = $this->db->get('stores');
                    //$ar[] = "Todas";
                    $ar = array();
                    foreach($q->result() as $r){
                        $ar[$r->id] = $r->state;
                    }
                    echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required" onchange="recalcar()"');
                ?>
            </div>
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Inicio&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                <input type="date" id="fec_ini" name="fec_ini" value="<?= $fec_ini ?>" class="form-control tip">
            </div>
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Fin&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                <input type="date" id="fec_fin" name="fec_fin" value="<?= $fec_fin ?>" class="form-control tip">
            </div>
        </div>

        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: 25px 0px 20px 0px;">
            <button onclick="activo1()" class="btn btn-primary">Consultar</button>
        </div>

    </div>

    <script type="text/javascript">
        setTimeout("recalcar()",300)

        function activo1(){
            let desde       = document.getElementById("fec_ini").value
            let hasta       = document.getElementById("fec_fin").value
            if(desde == ""){ desde = "null" }
            if(hasta == ""){ hasta = "null" }
            let tienda      = document.getElementById("tienda").value
            let cadenon     = "<?= base_url() ?>reports/salidas_por_dia?tienda="+$("#tienda").val()+"&"+"fec_ini="+desde+"&"+"fec_fin="+hasta
            window.location.assign(cadenon)
        }

        function mostrar_ventas(){
            if(document.getElementById('resumen_ventas').style.display == 'none'){
                $("#resumen_ventas").show(1000)
            }else{
                $("#resumen_ventas").hide(1000)
            }
        }

        function recalcar(){
            /* Para obtener el texto */
            let combo = document.getElementById("tienda");
            let selected = combo.options[combo.selectedIndex].text;
            document.getElementById("titulo_recalcar").innerHTML = selected
        }

    </script>

    <div class="row" style="border-style: transparent; border-width:2px; border-color:red; margin: 10px;">
        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red">
            <button onclick="mostrar_ventas()" class="btn btn-primary">Ver Resumen de Ventas</button>
        </div>
        <div class="col-sm-8 col-md-9" style="border-style:none; border-color:red">
            <h2 id="titulo_recalcar" style="margin:0px"></h2>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-11">
                <div class="box box-primary" id="resumen_ventas" style="margin-bottom:20px;display:none">
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <caption style="font-weight:bold; font-size: 18px; color:rgb(30,90,150)">Ventas:</caption>
                            <tr class="active">
                                <th class="col-xs-2 col-sm-1" style="">Fecha</th>
                                <th class="col-xs-2 col-sm-1" style="">Dia</th>
                                <th class="col-sm-1">Venta en Efectivo</th>
                                <th class="col-sm-1">Venta con tarjeta</th>
                                <th class="col-sm-1">Transf. Bancaria</th>
                                <th class="col-sm-1">Venta con Yape</th>
                                <th class="col-sm-1">Venta con Plin</th>
                                <th class="col-sm-1">Venta con Rappi</th>
                                <th class="col-sm-1">PedidosYa</th>
                                <th class="col-sm-1">Consumo Personal</th>
                                <th class="col-sm-1">Total</th>
                            </tr>
                            <tr>
                            <?php
                                foreach($query2->result() as $r){
                                    echo "<tr>";
                                    echo $this->fm->celda_h($r->fecha,0,"color:rgb(60,120,190)");
                                    echo $this->fm->celda($r->dia_semana);
                                    echo $this->fm->celda(number_format($r->cash,2),2,"text-align:right");
                                    echo $this->fm->celda(number_format($r->vendemas,2));
                                    echo $this->fm->celda(number_format($r->transferencia,2));
                                    echo $this->fm->celda(number_format($r->yape,2));
                                    echo $this->fm->celda(number_format($r->plin,2));
                                    echo $this->fm->celda(number_format($r->rappi,2));
                                    echo $this->fm->celda(number_format($r->pedidosya,2));
                                    echo $this->fm->celda(number_format($r->otros,2));
                                    echo $this->fm->celda_h($simbolo . number_format($r->total,2),2,"text-align:right;padding-right:10px");
                                    echo "</tr>";
                                }
                            ?>
                            </tr>
                        </table>
                    </div>
                </div>
        </div>
    </div>
<style type="text/css">
    .titulon{
        background-color: rgb(80,80,80);
        color:  white;
        font-weight: bold;
    }

    .titulon_pie{
        background-color: rgb(80,80,80);
        color:  white;
        font-weight: bold;
        text-align: right;
    }

    .celda_titulon{
        border-color: white;
    }
    .resu{
        background-color: rgb(80,80,80);
        color:  white;
        font-weight: bold;
    }
</style>
    <div class="row">
        <div class="col-xs-12 col-sm-9">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table">
                            <table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
                                <caption style="font-weight:bold; font-size: 18px; color:rgb(30,90,150)">Resumen:</caption>
                                <thead>
                                    <!-- TITULOS -->
                                    <tr class="titulon">
                                        <th class="col-xs-2 col-sm-1 text-left" style="">Fecha</th>
                                        <th class="col-xs-2 col-sm-1 text-left" style="">Dia</th>
                                        <!--<th class="col-xs-2 col-sm-1 text-left">Caja Inicial</th>-->
                                        <th class="col-xs-2 col-sm-1">Total Ventas Efectivo</th>
                                        <th class="col-xs-2 col-sm-1">Salidas con_factura</th>
                                        <th class="col-xs-2 col-sm-1">Salidas con_boleta</th>
                                        <th class="col-xs-2 col-sm-1">Salidas con_recibo</th>
                                        <th class="col-xs-2 col-sm-1">total Salidas</th>
                                        <th class="col-xs-2 col-sm-1">Remesas</th>
                                        <th class="col-xs-2 col-sm-1">Caja final</th>
                                        <th class="col-xs-2 col-sm-1">Cierre</th>
                                    </tr>
                                </thead>
                                
                                <tbody id="traveler">
                                   <?php
                                        $nTotalVentas = $nCon_factura = $nCon_boleta = $nCon_recibo = $nTotal_salidas = $nTotal_depositos = 0;
                                        foreach($query->result() as $r){
                                            echo "<tr>";
                                            
                                            // Fecha
                                            echo $this->fm->celda_h($r->fecha,0,"color:rgb(60,120,190)"); // tc.fecha, a.store_id, tc.dia_semana, ts.grand_total, a.con_factura, a.con_boleta, a.con_recibo, a.total_salidas, a.total_depositos
                                            
                                            // Dia
                                            echo $this->fm->celda(($r->dia_semana == 'DOMINGO' ? "<span style=\"color:red;font-weight:bold;\">{$r->dia_semana}</span>": $r->dia_semana));
                                            
                                            // Caja_inicial
                                            //echo $this->fm->celda(number_format($r->cash_in_hand*1 + $r->cash_in_hand_adicional*1,2));
                                            
                                            // Total ventas efectivo
                                            echo "<td style=\"border-style:solid; border-color:brown; border-left-width:0px; border-top-width:0px; border-right-width:2px; border-bottom-width:0px;\">" . number_format($r->total_ventas_efectivo,2) . "</td>";
                                            
                                            // Salida con factura                                            
                                            echo $this->fm->celda(number_format($r->con_factura,2));
                                            
                                            echo $this->fm->celda(number_format($r->con_boleta,2));
                                            
                                            echo $this->fm->celda(number_format($r->con_recibo,2));
                                            
                                            echo $this->fm->celda($this->fm->casilla_graf(number_format($r->total_salidas,2)));

                                            echo $this->fm->celda(number_format($r->total_depositos,2));
                                            
                                            echo $this->fm->celda_h($simbolo . number_format($r->caja_final,2),2,"text-align: right;padding-right:10px;");
                                            
                                            echo $this->fm->celda($r->cierre);
                                            
                                            echo "</tr>";
                                        
                                            $nTotalVentas     += $r->total_ventas_efectivo * 1;
                                            $nCon_factura     += $r->con_factura * 1;
                                            $nCon_boleta      += $r->con_boleta * 1;
                                            $nCon_recibo      += $r->con_recibo * 1;
                                            $nTotal_salidas   += $r->total_salidas * 1;
                                            $nTotal_depositos += $r->total_depositos * 1;
                                            
                                        }
                                   
                                        $cTotalVentas = number_format($nTotalVentas,2);
                                        $cCon_factura = number_format($nCon_factura,2);
                                        $cCon_boleta = number_format($nCon_boleta,2);
                                        $cCon_recibo = number_format($nCon_recibo,2);
                                        $cTotal_salidas = number_format($nTotal_salidas,2);
                                        $cTotal_depositos = number_format($nTotal_depositos,2);

                                        echo "<tr>";
                                        echo "<td colspan=\"2\" class=\"titulon_pie\">Totales</td>";
                                        echo $this->fm->celda_h("-","","","titulon_pie");
                                        echo $this->fm->celda_h($cTotalVentas,"","","titulon_pie"); // Total ventas efectivo
                                        echo $this->fm->celda_h($cCon_factura,"","","titulon_pie"); // con factura
                                        echo $this->fm->celda_h($cCon_boleta,"","","titulon_pie"); // con boleta
                                        echo $this->fm->celda_h($cCon_recibo,"","","titulon_pie"); // con recibo
                                        echo $this->fm->celda_h($cTotal_salidas,"","","titulon_pie"); // salidas
                                        echo $this->fm->celda_h($cTotal_depositos,"","","titulon_pie"); // remesas
                                        echo $this->fm->celda_h("-","","","titulon_pie"); // caja final
                                        echo $this->fm->celda_h("-","","","titulon_pie"); // cierre
                                        echo "</tr>";
                                   
                                        // Hallando la rentabilidad:
                                        if($nTotal_salidas > 0){
                                            $rentabilidad = 100 * (($nTotalVentas - $nTotal_salidas)/$nTotal_salidas);
                                        }else{
                                            $rentabilidad = 0;
                                        }
                                        $cRentabilidad = number_format($rentabilidad,1);
                                   ?>
                               </tbody>
                        </table>

                        <h3>Rentabilidad : <?= $cRentabilidad ?> %</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div><!-- FIN DEL COL -->
        </div>    
    </div>

</div>
</section>
