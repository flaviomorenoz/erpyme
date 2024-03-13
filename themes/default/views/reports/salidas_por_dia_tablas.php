    <!-- ********************************************** VENTAS ********************************************* -->
    <div class="row">
        <div class="col-xs-12 col-sm-11">
                <div class="box box-primary" id="resumen_ventas" style="margin-bottom:20px;display:block">
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <caption style="font-weight:bold; font-size: 18px; color:rgb(30,90,150)">Ventas L&iacute;quidas:<br>
                                <span style="color:rgb(200,0,0)">Nota.- Dscto 5% para Tarjetas y 25% para Deliverys Externos.</span>
                            </caption>
                            <tr class="active">
                                <th class="col-xs-2 col-sm-1" style="">Fecha</th>
                                <th class="col-xs-2 col-sm-1" style="">Dia</th>
                                <th class="col-sm-1">Efectivo</th>
                                <th class="col-sm-1">Vendem&aacute;s</th>
                                <th class="col-sm-1">Transf. Bancaria</th>
                                <th class="col-sm-1">Yape</th>
                                <th class="col-sm-1">Plin</th>
                                <th class="col-sm-1">Rappi</th>
                                <th class="col-sm-1">PedidosYa</th>
                                <th class="col-sm-1">Didi</th>
                                <th class="col-sm-1">Culqi</th>
                                <th class="col-sm-1">Total</th>
                            </tr>
                            <tr>
                            <?php

                                $nAcu_cash = $nAcu_vendemas = $nAcu_transferencia = $nAcu_yape = $nAcu_plin = $nAcu_rappi = $nAcu_pedidosya = $nAcu_otros = $nAcu_total = $nAcu_didi = $nAcu_culqui = 0;
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
                                    echo $this->fm->celda(number_format($r->didi,2));
                                    //echo $this->fm->celda(number_format($r->otros,2));
                                    echo $this->fm->celda(number_format($r->culqi,2));
                                    echo $this->fm->celda_h($simbolo . number_format($r->total,2),2,"text-align:right;padding-right:10px");
                                    echo "</tr>";
                                    $nAcu_cash              += $r->cash*1;
                                    $nAcu_vendemas          += $r->vendemas*1;
                                    $nAcu_transferencia     += $r->transferencia*1;
                                    $nAcu_yape              += $r->yape*1;
                                    $nAcu_plin              += $r->plin*1;
                                    $nAcu_rappi             += $r->rappi*1;
                                    $nAcu_pedidosya         += $r->pedidosya*1;
                                    $nAcu_didi              += $r->didi*1;
                                    $nAcu_otros             += $r->otros*1;
                                    $nAcu_total             += $r->total*1;
                                    $nAcu_culqi            += $r->culqi*1;
                                }
                                echo "</tr>";
                                echo "<tr><td></td><td></td>";
                                echo $this->fm->celda_h(number_format($nAcu_cash,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_vendemas,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_transferencia,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_yape,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_plin,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_rappi,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_pedidosya,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_didi,2),"","text-align:right");
                                //echo $this->fm->celda_h(number_format($nAcu_otros,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_culqi,2),"","text-align:right");
                                echo $this->fm->celda_h(number_format($nAcu_total,2),"","text-align:right");
                                echo "</tr>";
                            ?>
                            
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
            background-color: rgb(30,70,100);
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

        #cabeza{
            background-color:rgb(200,200,200);
            margin:0px;
            padding:4px!important;
        }
    </style>
    
    <!-- ********************************************** Cuadre de Caja General ********************************************* -->
    <div class="row">
        <div class="col-xs-12 col-sm-11">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table">
                            <table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
                                <caption style="font-weight:bold; font-size: 18px; color:rgb(30,90,150); text-align:center">
                                    Cuadre de Caja General:
                                </caption>
                                <thead>
                                    <!-- TITULOS -->
                                    <tr class="titulon">
                                        <th class="col-xs-2 col-sm-1 text-left" style="">Fecha</th>
                                        <th class="col-xs-2 col-sm-1 text-left" style="">Dia</th>
                                        <th class="col-xs-2 col-sm-1 text-left">Caja<br>Inicial</th>
                                        
                                        <th class="col-xs-2 col-sm-1">Total Ventas<br>Efectivo</th>

                                        <th class="col-xs-2 col-sm-1">Total Otras<br>Ventas</th>                                        
                                        
                                        <th class="col-xs-2 col-sm-1">TOTAL<BR>VENTAS</th>
                                        <th class="col-xs-2 col-sm-1">Salidas con_factura</th>
                                        
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
                                        $nTotalVentas = $nCon_factura = $nCon_boleta = $nCon_recibo = $nTotal_salidas = $nTotal_depositos = $nTotal_otras_ventas = 0;
                                        foreach($query_cuadre->result() as $r){
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
                                            echo "<td style=\"border-style:solid; border-color:brown; border-left-width:0px; border-top-width:0px; border-right-width:2px; border-bottom-width:0px;\">" . number_format($r->total_ventas_efectivo + $r->total_otras_ventas,2) . "</td>";

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
                                            $nTotal_otras_ventas += $r->total_otras_ventas * 1;
                                            $nTotalIngresos   += ($r->total_ventas_efectivo * 1) + ($r->total_otras_ventas*1); // +$r->cash_in_hand + $r->cash_in_hand_adicional + 
                                            $nCon_factura     += $r->con_factura * 1;
                                            $nCon_boleta      += $r->con_boleta * 1;
                                            $nCon_recibo      += $r->con_recibo * 1;
                                            $nTotal_salidas   += $r->con_factura+$r->con_boleta+$r->con_recibo;
                                            $nTotal_depositos += $r->total_depositos * 1;
                                            
                                        }
                                   
                                        $cTotalVentas = number_format($nTotalVentas,2);
                                        $cTotal_otras_ventas = number_format($nTotal_otras_ventas,2);
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
                                        echo $this->fm->celda_h($cTotal_otras_ventas,"","","titulon_pie"); // Total otras ventas
                                        echo $this->fm->celda_h($cTotalIngresos,"","","titulon_pie"); // 
                                        echo $this->fm->celda_h($cCon_factura,"","","titulon_pie"); // con factura
                                        echo $this->fm->celda_h($cCon_boleta,"","","titulon_pie"); // con boleta
                                        echo $this->fm->celda_h($cCon_recibo,"","","titulon_pie"); // con recibo
                                        echo $this->fm->celda_h($cTotal_salidas,"","","titulon_pie"); // salidas
                                        echo $this->fm->celda_h($cTotal_depositos,"","","titulon_pie"); // remesas
                                        echo $this->fm->celda_h("-","","","titulon_pie"); // caja final efectivo
                                        //echo $this->fm->celda_h("-","","","titulon_pie"); // caja final
                                        echo $this->fm->celda_h("&nbsp;","","","titulon_pie"); // cierre
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
                        
                        <h3>Rentabilidad : <?= $cRentabilidad ?> %</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div><!-- FIN DEL COL -->
        </div>    
    </div>

    <!-- ********************************************** DETALLE HORIZONTAL DE GASTOS DE CAJA X RUBRO *************************************-->
    <!--
    <div class="row" style="border-style:solid; border-width:2px; border-color:gray; margin-top:20px;overflow-x: scroll;">
        <p style="font-weight:bold; font-size: 18px; color:rgb(30,90,150)">Detalle de Gastos x D&iacute;a:</p>
        <?php
        /*
            $cSql = "
            select az.dia, az.r1 r1, az.r2 r2, az.r3 r3, az.r4 r4, az.r5 r5, az.r6 r6, az.r7 r7, az.r8 r8, az.r9 r9, az.r10 r10, az.r11 r, az.r12 r12, az.r13 r13, az.r14 r14, az.r15 r15, az.r16 r16, az.r17 r17, az.r_mas r_mas,
                az.r1+ az.r2+ az.r3+ az.r4+ az.r5+ az.r6+ az.r7+ az.r8+ az.r9+ az.r10+ az.r11+ az.r12+ az.r13+ az.r14+ az.r15+ az.r16+ az.r17+ az.r_mas as total
                from (
                select ay.dia, 
                round(sum(if(ay.rubro_id = 1, ay.monto_total*1.18, 0)),2) r1,
                round(sum(if(ay.rubro_id = 2, ay.monto_total*1.18, 0)),2) r2,
                round(sum(if(ay.rubro_id = 3, ay.monto_total*1.18, 0)),2) r3,
                round(sum(if(ay.rubro_id = 4, ay.monto_total*1.18, 0)),2) r4,
                round(sum(if(ay.rubro_id = 5, ay.monto_total*1.18, 0)),2) r5,
                round(sum(if(ay.rubro_id = 6, ay.monto_total*1.18, 0)),2) r6,
                round(sum(if(ay.rubro_id = 7, ay.monto_total*1.18, 0)),2) r7,
                round(sum(if(ay.rubro_id = 8, ay.monto_total*1.18, 0)),2) r8,
                round(sum(if(ay.rubro_id = 9, ay.monto_total*1.18, 0)),2) r9,
                round(sum(if(ay.rubro_id = 10, ay.monto_total*1.18, 0)),2) r10,
                round(sum(if(ay.rubro_id = 11, ay.monto_total*1.18, 0)),2) r11,
                round(sum(if(ay.rubro_id = 12, ay.monto_total*1.18, 0)),2) r12,
                round(sum(if(ay.rubro_id = 13, ay.monto_total*1.18, 0)),2) r13,
                round(sum(if(ay.rubro_id = 14, ay.monto_total*1.18, 0)),2) r14,
                round(sum(if(ay.rubro_id = 15, ay.monto_total*1.18, 0)),2) r15,
                round(sum(if(ay.rubro_id = 16, ay.monto_total*1.18, 0)),2) r16,
                round(sum(if(ay.rubro_id = 17, ay.monto_total*1.18, 0)),2) r17,
                round(sum(if(ay.rubro_id > 17, ay.monto_total*1.18, 0)),2) r_mas
                from
                (
                    select ax.dia, ax.rubro_id, ax.rubro, sum(ax.subtotal) monto_total from (
                        select date(a.date) dia, b.product_id, p.name, b.rubro_id, c.descrip rubro, b.quantity, b.cost, b.subtotal
                        from tec_purchases a
                        inner join tec_purchase_items b on a.id = b.purchase_id
                        inner join tec_products p on b.product_id = p.id
                        inner join tec_rubros c on b.rubro_id = c.id
                        where date(a.date) between ? and ? and a.store_id = ?
                    ) ax
                    group by ax.dia, ax.rubro_id, ax.rubro
                ) ay
                group by ay.dia
            ) az"; 

            $result         = $this->db->query($cSql, array($fec_ini, $fec_fin, $tienda))->result_array();
            $cols           = array("dia","r1","r2","r3","r4","r5","r6","r7","r8","r9","r10","r11","r12","r13","r14","r15","r16","r17","r_mas","total");
            $cols_titulos   = array("Dia", "Insumos", "Servicios", "Representacion", "Infraestructura", "Legal", "Contable", "Publicidad", "Movilidad_I", "Movilidad_D", "Pago_acreedores"              , "Pago_Creditos_Bcos" , "Prestamo_a_Don_Alejo", "Prestamo_a_San_Miguel", "Prestamo_a_Surco", "Compra Activos", "Gastos Bancos", "Otros", "Totales");
            $cols_titulos   = array("Dia", "Insumos", "Servi", "Repres", "Infra", "Legal", "Contable", "Publi", "Movil_I","Movil_D", "acreedores", "Creditos_Bcos" ,
                                "Prestamo_Alejo", "Prestamo_Miguel", "Prestamo_Surco", "C.Activos", "G.Bancos", "Planilla", "Otros", "Totales");
            $ar_align       = array("1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1");
            $ar_pie         = array("","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma","suma");
            echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
        */
        ?>
        <p>Nota.- Refleja los montos en base a lo registrado en Compras, sea este documento pagado o aun no pagado.</p>        
    </div>-->


    <div class="row">
        <div class="col-xs-12 col-sm-10 col-md-9 col-lg-8">

                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table">
                            <table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
                                <caption style="font-weight:bold; font-size: 18px; color:rgb(30,90,150); text-align:center">
                                    Resumen por Canal de Venta
                                </caption>
                                <thead>
                                    <!-- TITULOS -->
                                    <tr class="titulon">
                                        <th class="text-left" style="">Fecha</th>
                                        <th class="text-right" style="">Lobby</th>
                                        <th class="text-right" style="">PedidosYa</th>
                                        <th class="text-right" style="">Rappi</th>
                                        <th class="text-right" style="">Didi</th>
                                        <th class="text-right" style="">Delivery Propio</th>
                                        <th class="text-right" style="">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $nDirecto   = $nPedidosya = $nRappi = $nPropio = $nDidi = $nTotal = 0;
                                    $result      = $this->db->query($cadena_canales)->result_array();
                                    foreach($result as $r){
                                        echo "<tr>";
                                        echo celda($r["fecha"]);
                                        echo celda(number_format($r["directo"],2),2 );
                                        echo celda(number_format($r["pedidosya"],2),2 );
                                        echo celda(number_format($r["rappi"],2),2 );
                                        echo celda(number_format($r["didi"],2),2 );
                                        echo celda(number_format($r["propio"],2),2 );

                                        $nTotal_f = 0 + $r["directo"] + $r["pedidosya"] + $r["rappi"] + $r["didi"] + $r["propio"];
                                        echo celda(number_format($nTotal_f,2));

                                        echo "</tr>";
                                        $nDirecto   += $r["directo"] * 1;
                                        $nPedidosya += $r["pedidosya"] * 1; 
                                        $nRappi     += $r["rappi"] * 1; 
                                        $nDidi      += $r["didi"] * 1; 
                                        $nPropio    += $r["propio"] * 1; 
                                        $nTotal     += $nTotal_f * 1;
                                    }                                    
                                ?>
                                </tbody>
                                <tfooter>
                                <?php
                                    echo "<tr>";
                                    echo celda("Totales");
                                    echo celda(number_format($nDirecto,2),2 );
                                    echo celda(number_format($nPedidosya,2),2 );
                                    echo celda(number_format($nRappi,2),2 );
                                    echo celda(number_format($nDidi,2),2 );
                                    echo celda(number_format($nPropio,2),2 );
                                    echo celda(number_format($nTotal,2),2 );
                                    echo "</tr>";
                                ?>
                                </tfooter>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>
