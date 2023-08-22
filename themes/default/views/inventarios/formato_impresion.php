<?php
    $this->db->reset_query();
    $query = $this->db->select("state")->from("stores")->where("id",$store_id)->get();
    $descrip_tda = "";
    foreach($query->result() as $r){
        $descrip_tda = $r->state;
    }

    // Averiguo la fecha del inventario
    if(isset($inventario1)){
        $maestro_id     = $inventario1;
        $fecha_inv      = $this->db->select("a.fecha")->from("tec_inventarios a")->where("a.id",$maestro_id)->get()->row()->fecha;
    }else{
        $maestro_id     = "";
        $fecha_inv      = "";
    }
?>
<style type="text/css">
    .celda_impre{
        border-spacing: 10px 20px;
        border-collapse: separate;
    }
</style>
    <div class="row" style="margin-bottom:20px">
        <div class="col-sm-12"><h2 class="text-center">Hoja de Inventario</h2></div>
    </div>
    <div class="row" style="margin-bottom:20px">
        <div class="col-sm-4 text-center letra16">Tienda : <?= $descrip_tda ?></div>
        <div class="col-sm-4 text-center letra16">Fecha : _____________</div>
        <div class="col-sm-4 text-center letra16">Hora : _____________</div>
    </div>
    <div class="row" style="margin-bottom:20px">
        <div class="col-sm-12 text-center letra16">Persona que realiza Inventario:_______________________</div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <table style="">
                <tr>
                    <th class="celda_impre" style="width:70px; ">Codigo</th>
                    <th class="celda_impre" style="width:240px; ">Producto</th>
                    <th class="celda_impre" style="width:80px; ">Stock<br>Esperado</th>
                    <th class="celda_impre" style="width:80px; ">Nuevo<br>Stock</th>
                </tr>
                <?php
                    if(strlen($maestro_id."")>0 && $maestro_id != '0'){
                        //die("Tiene inventario...");
                        $cSql = "select compras.code, compras.id, compras.name, compras.unidad, if(ti.cantidad is null,0,ti.cantidad) + round(if(compras.cantidad is null,0,compras.cantidad) - (if(ventas.sum_qc is null,0,ventas.sum_qc)/1000),1) stock 
                            from 
                            (
                                select a.id, a.code, a.name, a.unidad, a.inventariable, sum(compri.quantity) cantidad 
                                from tec_products a 
                                left join (
                                    select xb.product_id, xb.quantity  
                                    from tec_purchase_items xb 
                                    inner join tec_purchases xc on xb.purchase_id = xc.id and xc.store_id = ? and xc.date_ingreso >= '{$fecha_inv}'
                                ) compri on a.id = compri.product_id
                                where a.category_id = 7 and a.rubro = 1
                                group by a.id, a.code, a.name, a.unidad, a.inventariable
                            ) compras
                            left join
                            (
                                select tr.id_insumo, tr.name, sum(b.quantity*tr.cantidadReceta) sum_qc 
                                from (
                                    select tec_sale_items.product_id, tec_sale_items.quantity from tec_sale_items
                                    inner join tec_sales on tec_sale_items.sale_id = tec_sales.id
                                    where tec_sales.store_id = ? and tec_sales.date >= '{$fecha_inv}'
                                ) b
                                inner join 
                                (
                                 select tec_recetas.product_id plato_id, tec_recetas.nombreReceta, tec_recetas.id_insumo, tec_products.name,
                                    tec_recetas.cantidadReceta from tec_recetas
                                    inner join tec_products on tec_recetas.id_insumo = tec_products.id
                                ) tr on b.product_id = tr.plato_id
                                group by tr.id_insumo, tr.name
                            ) ventas on compras.id = ventas.id_insumo
                            left join tec_inventarios ti on ti.product_id = compras.id and ti.maestro_id = {$maestro_id}
                            order by compras.name";

                        //die($cSql);
                        $query = $this->db->query($cSql, array($store_id, $store_id));
                    }else{
                        //die("No tiene");
                        $cSql = "select compras.code, compras.id, compras.name, compras.unidad, 
                            round(if(compras.cantidad is null,0,compras.cantidad) - (if(ventas.sum_qc is null,0,ventas.sum_qc)/1000),1) stock 
                            from 
                            (
                                select a.id, a.code, a.name, a.unidad, a.inventariable, sum(compri.quantity) cantidad 
                                from tec_products a 
                                left join (
                                    select xb.product_id, xb.quantity  
                                    from tec_purchase_items xb 
                                    inner join tec_purchases xc on xb.purchase_id = xc.id and xc.store_id = ?
                                ) compri on a.id = compri.product_id
                                where a.category_id = 7 and a.rubro = 1
                                group by a.id, a.code, a.name, a.unidad, a.inventariable
                            ) compras
                            left join
                            (
                                select tr.id_insumo, tr.name, sum(b.quantity*tr.cantidadReceta) sum_qc 
                                from (
                                    select tec_sale_items.product_id, tec_sale_items.quantity from tec_sale_items
                                    inner join tec_sales on tec_sale_items.sale_id = tec_sales.id
                                    where tec_sales.store_id = ?
                                ) b
                                inner join 
                                (
                                 select tec_recetas.product_id plato_id, tec_recetas.nombreReceta, tec_recetas.id_insumo, tec_products.name,
                                    tec_recetas.cantidadReceta from tec_recetas
                                    inner join tec_products on tec_recetas.id_insumo = tec_products.id
                                ) tr on b.product_id = tr.plato_id
                                group by tr.id_insumo, tr.name
                            ) ventas on compras.id = ventas.id_insumo 
                            where compras.inventariable = '1'
                            order by compras.name";

                        $query = $this->db->query($cSql, array($store_id, $store_id));
                    }
                    foreach($query->result() as $r){
                        echo "<tr style=\"height:38px\">";
                        echo $this->fm->celda($r->code);
                        echo $this->fm->celda($r->name);
                        echo $this->fm->celda($r->stock,1);
                        echo $this->fm->celda("_______",1);
                        echo "</tr>";
                    }
                ?>
            </table>
            
        </div>
    </div>
