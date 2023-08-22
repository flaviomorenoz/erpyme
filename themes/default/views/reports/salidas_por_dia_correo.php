<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
        <?= $Settings->rtl ? '<link href="'.$assets.'dist/css/rtl.css" rel="stylesheet" />' : ''; ?>
        
        <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
        
        <script type="text/javascript" src="<?= $assets ?>toastr-master/toastr.js"></script>
        
        <link href="<?= $assets ?>toastr-master/build/toastr.css" rel="stylesheet"/>
    </head>
    <body>
<?php
// DETALLE DE VENTAS

$query2         = $this->db->query($cadena_query_ventas);

$madre          = $cadena_query_ventas; 

$query2     = $this->db->query($cadena_query_ventas);
$query2a    = $this->db->query($cadena_query_ventas);
$query2b    = $this->db->query($cadena_query_ventas); // Para el grafico

$bandera_r2 = 0;
foreach($query2a->result() as $r){
    $bandera_r2++; 
}

$query      = $this->db->query($cadena_query);

$simbolo    = "<span style=\"color:red\">S/</span>&nbsp;&nbsp;&nbsp;";

?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
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

    </script>

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

    <!-- ********************************************** VENTAS ********************************************* -->
    <?php if($bandera_r2>0){ ?>
    <div class="row">
        <div class="col-xs-12 col-sm-11">
                <div class="box box-primary" id="resumen_ventas" style="margin-bottom:20px;display:block">
                    <div class="box-body">
                        <h2 style="font-weight:bold; font-size: 18px; color:rgb(30,90,150); text-align:left">Ventas <?= $tienda_descrip ?>:</h2>
                        <table class="table table-striped table-bordered table-condensed table-hover" style="border-style:solid; border-color:gray; border-width:1px" border="1">
                            
                            <tr class="titulon">
                                <th class="col-xs-2 col-sm-1" style="">Fecha</th>
                                <th class="col-xs-2 col-sm-1" style="">Dia</th>
                                <th class="col-sm-1">Venta<br> en Efectivo</th>
                                <th class="col-sm-1">Venta<br> con tarjeta</th>
                                <th class="col-sm-1">Transf.<br> Bancaria</th>
                                <th class="col-sm-1">Venta<br> con Yape</th>
                                <th class="col-sm-1">Venta<br> con Plin</th>
                                <th class="col-sm-1">Venta<br> con Rappi</th>
                                <th class="col-sm-1">PedidosYa</th>
                                <th class="col-sm-1">Consumo<br> Personal</th>
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
                                    echo $this->fm->celda_h($simbolo . number_format($r->total,2),2,"text-align:right;padding-right:10px;background-color:yellow");
                                    echo "</tr>";
                                }
                            ?>
                            </tr>
                        </table>
                    </div>
                </div>
        </div>
    </div>
    <?php } ?>

    
    <!-- ********************************************** CIERRE DE CAJA ********************************************* -->
    <div class="row">
        <div class="col-xs-12 col-sm-11">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table">
                            <h2 style="font-weight:bold; font-size: 18px; color:rgb(30,90,150); text-align:left">Cuadre de Caja General <?= $tienda_descrip ?>:</h2>
                            <table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
                                <thead>
                                    <!-- TITULOS -->
                                    <tr class="titulon">
                                        <th class="col-xs-2 col-sm-1 text-left" style="">Fecha</th>
                                        <th class="col-xs-2 col-sm-1 text-left" style="">Dia</th>
                                        <th class="col-xs-2 col-sm-1 text-left">Caja<br>Inicial</th>
                                        
                                        <th class="col-xs-2 col-sm-1">Total Ventas<br>Efectivo</th>

                                        <th class="col-xs-2 col-sm-1">Total Otras<br>Ventas</th>                                        
                                        
                                        <th class="col-xs-2 col-sm-1">TOTAL<BR>VENTAS</th>
                                        <th class="col-xs-2 col-sm-1">Salidas<BR> con_factura</th>
                                        
                                        <th class="col-xs-2 col-sm-1">Salidas<br>con_boleta</th>
                                        <th class="col-xs-2 col-sm-1">Salidas<br>con_recibo</th>
                                        <th class="col-xs-2 col-sm-1">TOTAL<br>SALIDAS</th>
                                        <th class="col-xs-2 col-sm-1">Remesas</th>
                                        <th class="col-xs-2 col-sm-1">Caja final<br>Efectivo</th>
                                        <!--<th class="col-xs-2 col-sm-1">Caja final</th>-->
                                        <th class="col-xs-2 col-sm-1">Cierre</th>
                                        <!--<th class="col-xs-2 col-sm-2">Notas</th>-->
                                    </tr>

                                </thead>
                                
                                <tbody id="traveler">
                                   <?php
                                        $nTotalVentas = $nCon_factura = $nCon_boleta = $nCon_recibo = $nTotal_salidas = $nTotal_depositos = 0;
                                        foreach($query->result() as $r){
                                            echo "<tr>";
                                            
                                            // Fecha
                                            echo $this->fm->celda_h(substr($r->fecha,0,5),0,"color:rgb(60,120,190)"); // tc.fecha, a.store_id, tc.dia_semana, ts.grand_total, a.con_factura, a.con_boleta, a.con_recibo, a.total_salidas, a.total_depositos
                                            
                                            // Dia
                                            echo $this->fm->celda(($r->dia_semana == 'DOMINGO' ? "<span style=\"color:red;font-weight:bold;\">{$r->dia_semana}</span>": $r->dia_semana));
                                            
                                            // Caja_inicial
                                            echo $this->fm->celda(number_format($r->cash_in_hand*1 + $r->cash_in_hand_adicional*1,2));
                                            
                                            // Total ventas efectivo
                                            echo $this->fm->celda(number_format($r->total_ventas_efectivo,2));

                                            // Total otras ventas
                                            echo "<td style=\"border-style:solid; border-color:brown; border-left-width:0px; border-top-width:0px; border-right-width:2px; border-bottom-width:0px;\">" . number_format($r->total_otras_ventas,2) . "</td>";

                                            // Total ingresos
                                            echo "<td style=\"border-style:solid; border-color:brown; border-left-width:0px; border-top-width:0px; border-right-width:2px; border-bottom-width:0px; background-color:yellow\">" . number_format($r->total_ventas_efectivo + $r->total_otras_ventas,2) . "</td>";

                                            // Salida con factura                                            
                                            echo $this->fm->celda(number_format($r->con_factura,2));
                                            
                                            echo $this->fm->celda(number_format($r->con_boleta,2));
                                            
                                            //echo $this->fm->celda(number_format($r->con_recibo,2));
                                            echo "<td style=\"border-style:solid; border-color:brown; border-left-width:0px; border-top-width:0px; border-right-width:2px; border-bottom-width:0px;\">" . number_format($r->con_recibo,2) . "</td>";
                                            
                                            //echo $this->fm->celda($this->fm->casilla_graf(number_format($r->total_salidas,2)));
                                            echo "<td style=\"border-style:solid; border-color:brown; border-left-width:0px; border-top-width:0px; border-right-width:2px; border-bottom-width:0px;\">" . number_format($r->con_factura+$r->con_boleta+$r->con_recibo,2) . "</td>";

                                            // Remesas
                                            echo $this->fm->celda(number_format($r->remesa,2));
                                            
                                            echo $this->fm->celda_h(number_format($r->caja_final_efectivo,2));

                                            // $r->cash_in_hand + $r->cash_in_hand_adicional + $r->total_ventas_efectivo - ($r->con_factura + $r->con_boleta + $r->con_recibo),2)
                                            //echo $this->fm->celda_h($simbolo . number_format($r->caja_final,2),2,"text-align: right;padding-right:10px;");
                                            
                                            echo $this->fm->celda($r->cierre);
                                            
                                            //echo $this->fm->celda($r->note);

                                            echo "</tr>";
                                        
                                            $nTotalVentas     += $r->total_ventas_efectivo * 1;
                                            $nTotalIngresos   += $r->cash_in_hand + $r->cash_in_hand_adicional + ($r->total_ventas_efectivo * 1) + ($r->total_otras_ventas*1);
                                            $nCon_factura     += $r->con_factura * 1;
                                            $nCon_boleta      += $r->con_boleta * 1;
                                            $nCon_recibo      += $r->con_recibo * 1;
                                            $nTotal_salidas   += $r->con_factura+$r->con_boleta+$r->con_recibo;
                                            $nTotal_depositos += $r->total_depositos * 1;
                                            
                                        }
                                   
                                        $cTotalVentas = number_format($nTotalVentas,2);
                                        $cTotalIngresos = number_format($nTotalIngresos,2);
                                        $cCon_factura = number_format($nCon_factura,2);
                                        $cCon_boleta = number_format($nCon_boleta,2);
                                        $cCon_recibo = number_format($nCon_recibo,2);
                                        $cTotal_salidas = number_format($nTotal_salidas,2);
                                        $cTotal_depositos = number_format($nTotal_depositos,2);

                                        echo "<tr>";
                                        echo "<td colspan=\"2\" class=\"titulon_pie\">Totales</td>";
                                        echo $this->fm->celda_h("-","","","titulon_pie");
                                        echo $this->fm->celda_h($cTotalVentas,"","","titulon_pie"); // Total ventas efectivo
                                        echo $this->fm->celda_h(0,"","","titulon_pie"); // Total otras ventas
                                        echo $this->fm->celda_h($cTotalIngresos,"","","titulon_pie"); // 
                                        echo $this->fm->celda_h($cCon_factura,"","","titulon_pie"); // con factura
                                        echo $this->fm->celda_h($cCon_boleta,"","","titulon_pie"); // con boleta
                                        echo $this->fm->celda_h($cCon_recibo,"","","titulon_pie"); // con recibo
                                        echo $this->fm->celda_h($cTotal_salidas,"","","titulon_pie"); // salidas
                                        echo $this->fm->celda_h($cTotal_depositos,"","","titulon_pie"); // remesas
                                        echo $this->fm->celda_h("-","","","titulon_pie"); // caja final efectivo
                                        //echo $this->fm->celda_h("-","","","titulon_pie"); // caja final
                                        echo $this->fm->celda_h("-","","","titulon_pie"); // cierre
                                        //echo $this->fm->celda_h("&nbsp;");
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

                        <!--<h3>Rentabilidad : <?= $cRentabilidad ?> %</h3>-->
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div><!-- FIN DEL COL -->
        </div>    
    </div>


</section>

    <style type="text/css">
        .etiq_x{
            font-family: arial;
            font-weight: bold;
        }

        .titulo_graf{
            font-family: arial;
            color: red;
            font-weight: bold;
            font-size: 18px;
        }

        .titulo_acumulado{
            font-family: arial;
            color: rgb(100,130,160);
            font-weight: bold;
            font-size: 16px;
            
        }

    </style>

    <section style="margin-left:20px">
        <div class="row">
            <div class="col-sm-10 col-md-6">
            <?php
                $cSql = "select sum(grand_total) totalisimo from tec_sales".
                    " where extract(month from date) = extract(month from curdate()) and extract(year from date) = extract(year from curdate()) and store_id = ?";

                $query = $this->db->query($cSql,array($tienda));

                foreach($query->result() as $r){
                    $totalisimo = $r->totalisimo;
                }
                
                echo "<br><div class=\"titulo_acumulado\" style=\"font-weight:bold\"><span style=\"background-color: yellow;\">Total acumulado del Mes para la Tienda $tienda_descrip : &nbsp;&nbsp;&nbsp; S/ " . number_format($totalisimo,2) . "</span><div>";
            ?>
            </div>
        </div>
    </section>

    <!-- ************************** GRAFICO **************************-->
    <br><br>
    <section style="margin:10px 0px 5px 30px">
        <svg width="525" height="360" x="5" y="5" style="background-color:rgb(235,235,235);border-style: solid; border-width:2px; border-color:black;">
            
        <?php
        
            //$ar_fecha = array("17/01","18/02","19/03","20/04","21/05","22/05","23/05");

            // Averiguando Montos
            $desfase        = 8;
            $desfase_real   = $desfase - 1;
            $la_fase        = date('Y-m-d', strtotime(date("Y-m-d")." -{$desfase_real} day"));

            $j=0;
            $ar_montos[0] = 0; $ar_montos[1] = 0; $ar_montos[2] = 0; $ar_montos[3] = 0; $ar_montos[4] = 0; $ar_montos[5] = 0;
            $ar_montos[6] = 0; 

            for($is=0; $is < $desfase; $is++){

                $fec_ini = $la_fase;
                $cSql = "select date_format(tc.fecha,'%d-%m-%Y') as fecha, tc.dia_semana, b.cash, b.vendemas, b.transferencia, b.yape, b.plin, b.rappi, b.pedidosya, b.otros, 
                    b.cash + b.vendemas + b.transferencia + b.yape + b.plin + b.rappi + b.pedidosya + b.otros as total
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
                    where tc.fecha = '$fec_ini'
                    order by tc.fecha";

                //echo str_replace("\n","<br>",$cSql) . "<br>";

                $this->db->reset_query();
                $query = $this->db->query($cSql);

                foreach($query->result() as $r){
                    $ar_montos[$j] = $r->total * 1;
                    $ar_fecha[$j] = substr($r->fecha,0,5);
                    //echo "El monto es: " . $ar_montos[$j] . " y el dia es: {$la_fase}<br>";
                    //$ar_sem[$j] = date("D", strtotime($r->fecha));
                    $ar_sem[$j] = $this->fm->dias_de_la_semana(date("w", strtotime($r->fecha)));
                }
                $j++;
                $la_fase = date('Y-m-d', strtotime($la_fase." +1 day"));

            }

            $limiteX = $desfase;
            $x = -20;
            for($i=0; $i<$limiteX; $i++){

                $x = $x + 60;
                $pos_letra = $x;
                $pos_monto = $x;

                $altura = $ar_montos[$i] * 0.05;

                $complemento = 240 - $altura;

                // Barras
                echo "<rect x=\"{$x}\" y=\"{$complemento}\" width=\"30\" height=\"{$altura}\" style=\"fill:rgb(70,100,150);stroke-width:1;stroke:rgb(10,10,10)\" />\n";
                
                // Dias
                echo "<text x=\"{$pos_letra}\" y=\"290\" fill=\"rgb(60,60,60)\" class=\"etiq_x\">{$ar_sem[$i]}</text>\n";

                // Montos:
                echo "<text x=\"{$pos_monto}\" y=\"260\" fill=\"rgb(60,60,60)\" class=\"etiq_x\">{$ar_montos[$i]}</text>\n";

                // Fecha:
                echo "<text x=\"{$pos_monto}\" y=\"313\" fill=\"rgb(120,120,120)\" class=\"etiq_x\">{$ar_fecha[$i]}</text>\n";
            }
            
            // Titulo
            echo "<text x=\"170\" y=\"30\" fill=\"red\" class=\"titulo_graf\">Local : {$tienda_descrip}</text>\n";
            
            echo "<line x1=\"30\" y1=\"240\" x2=\"500\" y2=\"240\" style=\"stroke:rgb(255,0,0);stroke-width:2\" />";
            echo "<line x1=\"30\" y1=\"240\" x2=\"30\" y2=\"40\" style=\"stroke:rgb(255,0,0);stroke-width:2\" />";

            // Linea de referencia 1:
            echo "<line x1=\"30\" y1=\"188\" x2=\"500\" y2=\"188\" style=\"stroke:rgb(80,80,80);stroke-width:1\" />";
            echo "<text x=\"10\" y=\"188\" fill=\"rgb(120,120,120)\">1</text>";

            // Linea de referencia 2:
            echo "<line x1=\"30\" y1=\"138\" x2=\"500\" y2=\"138\" style=\"stroke:rgb(80,80,80);stroke-width:1\" />";
            echo "<text x=\"10\" y=\"138\" fill=\"rgb(120,120,120)\">2</text>";
        ?>
        </svg>
    </section>

    </body>
</html>
