<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inventarios_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function listar($inventario1, $inventario2, $store_id=""){
        
        $cad2 = $cad3 = "";
        $inventario2 = $inventario2 != "null" ? $inventario2 : ""; 
        $inventario2 = $inventario2 != "0" ? $inventario2 : ""; 

        // Para inventario1 usaremos fec_inv como datetime
        //$store_id = "";
        $cSql = "select * from tec_maestro_inv where id = $inventario1";
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
            $fec_inv = $r->fecha . " " . $r->hora_fin;
            $store_id = $r->store_id;
        }

        if($inventario2!='0' && $inventario2 != ''){  // Compara 2 inventarios

            // Para inventario1 usaremos fec_inv como datetime
            $cSql = "select * from tec_maestro_inv where id = $inventario2";
            $query = $this->db->query($cSql);
            foreach($query->result() as $r){
                $fec_inv2 = $r->fecha . " " . $r->hora_fin;
            }

            // Para compras:
            $cad2 = " and a1.date_ingreso <= '{$fec_inv2}'";
            $cad5 = " and a.date_ingreso <= '{$fec_inv2}'";

            // Para ventas:
            $cad3 = " and a2.date <= '{$fec_inv2}'";

            $cSql = "SELECT a.id, a.fecha, a.product_id, b.name, if(b.unidad='UNIDAD',b.unidad,b.unidad) unidad, a.store_id, c.state as local, a.cantidad contada, 
                if(compras.total_comprado is null,0,compras.total_comprado) total_comprado, 
                if(compras.total_cost is null,0,compras.total_cost) total_costo_comprado,
                if(ventas.total_utilizado is null,0,ventas.total_utilizado) total_utilizado,
                movim_i.cantidad as ingreso, movim_s.cantidad as salida,
                a.cantidad + if(compras.total_comprado is null,0,compras.total_comprado) - if(ventas.total_utilizado is null,0,ventas.total_utilizado) 
                + if(movim_i.cantidad is null,0,movim_i.cantidad) - if(movim_s.cantidad is null,0,movim_s.cantidad) as stock,
                inv2.cantidad segundo_stock, concat('<a href=\"#\" onclick=\"eliminar(',a.id,')\"><span class=\"glyphicon glyphicon-remove iconos\"></span></a>') op,
                if(tec_movim.cantidad > 0, concat('Reconoce ',tec_movim.cantidad), concat('<button class=\"btn btn-primary btn-sm\" onclick=\"reconoce(',a.id,',',a.product_id,')\">Reconoce</button>')) op2
                FROM `tec_inventarios` a 
                left join tec_products b on a.product_id = b.id
                left join tec_stores c on a.store_id = c.id
                left join(
                  select b1.product_id, sum(b1.quantity) total_comprado, sum(b1.cost) total_cost 
                  from tec_purchases a1
                  inner join tec_purchase_items b1 on a1.id = b1.purchase_id
                  where a1.date_ingreso >'{$fec_inv}' {$cad2} and a1.store_id = {$store_id}
                  group by b1.product_id 
                ) compras on a.product_id = compras.product_id
                left join (
                    select mire.id_insumo, mire.name, mire.unidad, sum(vtas.quantity) vtas_de_cantidad,
                    sum(vtas.quantity * mire.cantidadReceta) as total_utilizado
                    from (
                      select b2.product_id, sum(b2.quantity) quantity, sum(b2.real_unit_price) monto
                      from tec_sales a2
                      inner join tec_sale_items b2 on a2.id = b2.sale_id
                      where a2.date>'{$fec_inv}' {$cad3} and a2.store_id = {$store_id}
                      group by b2.product_id
                    ) vtas
                    inner join (
                      select tec_recetas.product_id, tec_recetas.id_insumo, tec_products.name, tec_products.unidad, 
                      if(tec_products.unidad = 'UNIDAD', tec_recetas.cantidadReceta, tec_recetas.cantidadReceta / 1000) cantidadReceta
                      from tec_recetas
                      inner join tec_products on tec_recetas.id_insumo = tec_products.id
                    ) mire on vtas.product_id = mire.product_id
                    group by mire.id_insumo, mire.name, mire.unidad
                ) ventas on a.product_id = ventas.id_insumo
                left join (
                    select tm.product_id, sum(tm.cantidad) as cantidad
                    from tec_movim tm
                    where tm.store_id = {$store_id} and tm.tipo_mov = 'I' and tm.fechah between '{$fec_inv}' and '{$fec_inv2}' and tm.confirmado = '1'
                    group by tm.product_id
                ) movim_i on a.product_id = movim_i.product_id
                left join (
                    select tm.product_id, sum(tm.cantidad) as cantidad
                    from tec_movim tm
                    where tm.store_id = {$store_id} and tm.tipo_mov = 'S' and tm.fechah between '{$fec_inv}' and '{$fec_inv2}' and tm.confirmado = '1'
                    group by tm.product_id
                ) movim_s on a.product_id = movim_s.product_id
                left join(
                    select product_id, cantidad from tec_inventarios where maestro_id = '{$inventario2}' and store_id = {$store_id}
                ) inv2 on a.product_id = inv2.product_id 
                left join tec_movim on a.id = tec_movim.inv_id
                WHERE a.maestro_id = '{$inventario1}' and a.store_id = {$store_id}
                order by b.name";

            //die($cSql);

            $cSql_ventas = "select vtas.id, vtas.numero, vtas.date, mire.id_insumo, mire.name, mire.unidad, vtas.quantity, mire.cantidadReceta, vtas.name platillos
                    from (
                      select a2.id, a2.date, concat(a2.serie, '-', a2.correlativo) numero, b2.product_id, tp.name, b2.quantity, b2.real_unit_price monto
                      from tec_sales a2
                      inner join tec_sale_items b2 on a2.id = b2.sale_id
                      left join tec_products tp on b2.product_id = tp.id
                      where a2.date>'{$fec_inv}' {$cad3} and a2.store_id = {$store_id}
                    ) vtas
                    inner join (
                      select tec_recetas.product_id, tec_recetas.id_insumo, tec_products.name, tec_products.unidad, 
                      if(tec_products.unidad = 'UNIDAD', tec_recetas.cantidadReceta, tec_recetas.cantidadReceta / 1000) cantidadReceta
                      from tec_recetas
                      inner join tec_products on tec_recetas.id_insumo = tec_products.id
                    ) mire on vtas.product_id = mire.product_id order by mire.name, vtas.id";
                    
            $cSql_compras = "select a.id, a.date, a.date_ingreso, ts.name proveedor, b.product_id, tp.name, b.quantity 
                from tec_purchases a 
                inner join tec_purchase_items b on a.id = b.purchase_id
                left join tec_products tp on b.product_id = tp.id
                left join tec_suppliers ts on a.supplier_id = ts.id
                where a.date_ingreso>'{$fec_inv}' {$cad5}  and a.store_id = {$store_id}";
                
        }else{

            // Hay 2 opciones: 1)Que tenga al menos un primer inventario, 2)Que no tenga ninguno
        
            if($inventario1!='0' && $inventario1 != ''){  // CASO SI TIENE INVENTARIO INICIAL
                $cSql = "SELECT a.id, a.fecha, b.id product_id, b.name, if(b.unidad='UNIDAD',b.unidad,b.unidad) unidad, a.store_id, c.state as local, a.cantidad contada, 
                    if(compras.total_comprado is null,0,compras.total_comprado) total_comprado, 
                    if(compras.total_cost is null,0,compras.total_cost) total_costo_comprado,
                    if(ventas.total_utilizado is null,0,ventas.total_utilizado) total_utilizado,
                    movim_i.cantidad as ingreso, movim_s.cantidad as salida,
                    a.cantidad + if(compras.total_comprado is null,0,compras.total_comprado) - if(ventas.total_utilizado is null,0,ventas.total_utilizado) 
                    + if(movim_i.cantidad is null,0,movim_i.cantidad) - if(movim_s.cantidad is null,0,movim_s.cantidad) as stock,
                    concat('<a href=\"#\" onclick=\"eliminar(',a.id,')\"><span class=\"glyphicon glyphicon-remove iconos\"></span></a>') op
                    from tec_products b
                    left join tec_inventarios a on a.product_id = b.id and a.maestro_id = '{$inventario1}' and a.store_id = {$store_id}
                    left join(
                      select a1.store_id, b1.product_id, sum(b1.quantity) total_comprado, sum(b1.cost) total_cost 
                      from tec_purchases a1
                      inner join tec_purchase_items b1 on a1.id = b1.purchase_id
                      where a1.date >'{$fec_inv}' and a1.store_id = {$store_id}
                      group by a1.store_id, b1.product_id 
                    ) compras on b.id = compras.product_id
                    left join tec_stores c on compras.store_id = c.id
                    left join (
                    	select mire.id_insumo, mire.name, mire.unidad, sum(vtas.quantity) vtas_de_cantidad,
                    	sum(vtas.quantity * mire.cantidadReceta) as total_utilizado
                    	from (
                    	  select b2.product_id, sum(b2.quantity) quantity, sum(b2.real_unit_price) monto
                    	  from tec_sales a2
                    	  inner join tec_sale_items b2 on a2.id = b2.sale_id
                    	  where a2.date>'{$fec_inv}' and a2.store_id = {$store_id}
                    	  group by b2.product_id
                    	) vtas
                    	inner join (
                    	  select tec_recetas.product_id, tec_recetas.id_insumo, tec_products.name, tec_products.unidad, 
                    	    if(tec_products.unidad = 'UNIDAD', tec_recetas.cantidadReceta, tec_recetas.cantidadReceta / 1000) cantidadReceta
                    	  from tec_recetas
                    	  inner join tec_products on tec_recetas.id_insumo = tec_products.id
                    	) mire on vtas.product_id = mire.product_id
                    	group by mire.id_insumo, mire.name, mire.unidad
                    ) ventas on b.id = ventas.id_insumo
                    left join (
                        select tm.product_id, sum(tm.cantidad) as cantidad
                        from tec_movim tm
                        where tm.store_id = {$store_id} and tm.tipo_mov = 'I' and tm.fechah > '{$fec_inv}'
                        group by tm.product_id
                    ) movim_i on b.id = movim_i.product_id
                    left join (
                        select tm.product_id, sum(tm.cantidad) as cantidad
                        from tec_movim tm
                        where tm.store_id = {$store_id} and tm.tipo_mov = 'S' and tm.fechah > '{$fec_inv}'
                        group by tm.product_id
                    ) movim_s on b.id = movim_s.product_id
                    WHERE b.category_id = 7 and b.rubro = 1
                    order by b.name";
                //die($cSql);
            }else{
                
                // PARA EL CASO QUE NO TENGA INVENTARIO INICIAL
                
                if($store_id == ''){
                    $store_id = $_SESSION['store_id'];
                }
                $fec_inv = '2022-04-01'; // Solo se aplica a un archivo extra que esta al ultimo de los queries
                

                $cSql = "SELECT b.name, if(b.unidad='UNIDAD',b.unidad,'') unidad, {$store_id} store_id, c.state as local, 0 contada, 
                    if(compras.total_comprado is null,0,compras.total_comprado) total_comprado, 
                    if(compras.total_cost is null,0,compras.total_cost) total_costo_comprado,
                    if(ventas.total_utilizado is null,0,ventas.total_utilizado) total_utilizado,
                    movim_i.cantidad as ingreso, movim_s.cantidad as salida,
                    if(compras.total_comprado is null,0,compras.total_comprado) - if(ventas.total_utilizado is null,0,ventas.total_utilizado) 
                    + if(movim_i.cantidad is null,0,movim_i.cantidad) - if(movim_s.cantidad is null,0,movim_s.cantidad) as stock,
                    concat('x') op
                    FROM tec_products b
                    left join tec_stores c on {$store_id} = c.id
                    left join(
                      select b1.product_id, sum(b1.quantity) total_comprado, sum(b1.cost) total_cost 
                      from tec_purchases a1
                      inner join tec_purchase_items b1 on a1.id = b1.purchase_id
                      where a1.store_id = {$store_id}
                      group by b1.product_id 
                    ) compras on b.id = compras.product_id
                    left join (
                        select mire.id_insumo, mire.name, mire.unidad, sum(vtas.quantity) vtas_de_cantidad,
                        sum(vtas.quantity * mire.cantidadReceta) as total_utilizado
                        from (
                          select b2.product_id, sum(b2.quantity) quantity, sum(b2.real_unit_price) monto
                          from tec_sales a2
                          inner join tec_sale_items b2 on a2.id = b2.sale_id
                          where a2.store_id = {$store_id}
                          group by b2.product_id
                        ) vtas
                        inner join (
                          select tec_recetas.product_id, tec_recetas.id_insumo, tec_products.name, tec_products.unidad, 
                            if(tec_products.unidad = 'UNIDAD', tec_recetas.cantidadReceta, tec_recetas.cantidadReceta / 1000) cantidadReceta
                          from tec_recetas
                          inner join tec_products on tec_recetas.id_insumo = tec_products.id
                        ) mire on vtas.product_id = mire.product_id
                        group by mire.id_insumo, mire.name, mire.unidad
                    ) ventas on b.id = ventas.id_insumo
                    left join (
                        select tm.product_id, sum(tm.cantidad) as cantidad
                        from tec_movim tm
                        where tm.store_id = {$store_id} and tm.tipo_mov = 'I' 
                        group by tm.product_id
                    ) movim_i on b.id = movim_i.product_id
                    left join (
                        select tm.product_id, sum(tm.cantidad) as cantidad
                        from tec_movim tm
                        where tm.store_id = {$store_id} and tm.tipo_mov = 'S'
                        group by tm.product_id
                    ) movim_s on b.id = movim_s.product_id
                    where b.category_id = 7 and b.rubro = 1
                    order by b.name";

                //die($cSql);
            }


            $cSql_ventas = "select vtas.id, vtas.numero, vtas.date, mire.id_insumo, mire.name, mire.unidad, vtas.quantity, mire.cantidadReceta, vtas.name platillos
                from (
                  select a2.id, a2.date, concat(a2.serie, '-', a2.correlativo) numero, b2.product_id, tp.name, b2.quantity, b2.real_unit_price monto
                  from tec_sales a2
                  inner join tec_sale_items b2 on a2.id = b2.sale_id
                  left join tec_products tp on b2.product_id = tp.id
                  where a2.date>'{$fec_inv}' and a2.store_id = {$store_id}
                ) vtas
                inner join (
                  select tec_recetas.product_id, tec_recetas.id_insumo, tec_products.name, tec_products.unidad, 
                  if(tec_products.unidad = 'UNIDAD', tec_recetas.cantidadReceta, tec_recetas.cantidadReceta / 1000) cantidadReceta
                  from tec_recetas
                  inner join tec_products on tec_recetas.id_insumo = tec_products.id
                ) mire on vtas.product_id = mire.product_id order by mire.name, vtas.id";

            $cSql_compras = "select a.id, a.date, a.date_ingreso, ts.name proveedor, b.product_id, tp.name, b.quantity 
                from tec_purchases a 
                inner join tec_purchase_items b on a.id = b.purchase_id
                left join tec_products tp on b.product_id = tp.id
                left join tec_suppliers ts on a.supplier_id = ts.id
                where date(a.date_ingreso)>'{$fec_inv}' and a.store_id = {$store_id}";
        }
        
        $query = $this->db->query($cSql);
        
        //return $query;
        $ar[0] = $query;
        $ar[1] = $cSql;
        $ar[2] = $cSql_compras;
        $ar[3] = $cSql_ventas;
        return $ar;
    }

    function kardex(){
      $cSql = "SELECT a.id, a.fecha, a.product_id, b.name, if(b.unidad='UNIDAD',b.unidad,b.unidad) unidad, a.store_id, c.state as local, a.cantidad contada, 
          if(compras.total_comprado is null,0,compras.total_comprado) total_comprado, 
          if(compras.total_cost is null,0,compras.total_cost) total_costo_comprado,
          if(ventas.total_utilizado is null,0,ventas.total_utilizado) total_utilizado,
          movim_i.cantidad as ingreso, movim_s.cantidad as salida,
          a.cantidad + if(compras.total_comprado is null,0,compras.total_comprado) - if(ventas.total_utilizado is null,0,ventas.total_utilizado) 
          + if(movim_i.cantidad is null,0,movim_i.cantidad) - if(movim_s.cantidad is null,0,movim_s.cantidad) as stock,
          inv2.cantidad segundo_stock, concat('<a href=\"#\" onclick=\"eliminar(',a.id,')\"><span class=\"glyphicon glyphicon-remove iconos\"></span></a>') op,
          if(tec_movim.cantidad > 0, concat('Reconoce ',tec_movim.cantidad), concat('<button class=\"btn btn-primary btn-sm\" onclick=\"reconoce(',a.id,',',a.product_id,')\">Reconoce</button>')) op2
          FROM `tec_inventarios` a 
          left join tec_products b on a.product_id = b.id
          left join tec_stores c on a.store_id = c.id
          left join(
            select b1.product_id, sum(b1.quantity) total_comprado, sum(b1.cost) total_cost 
            from tec_purchases a1
            inner join tec_purchase_items b1 on a1.id = b1.purchase_id
            where a1.date_ingreso >'{$fec_inv}' {$cad2} and a1.store_id = {$store_id}
            group by b1.product_id 
          ) compras on a.product_id = compras.product_id
          left join (
              select mire.id_insumo, mire.name, mire.unidad, sum(vtas.quantity) vtas_de_cantidad,
              sum(vtas.quantity * mire.cantidadReceta) as total_utilizado
              from (
                select b2.product_id, sum(b2.quantity) quantity, sum(b2.real_unit_price) monto
                from tec_sales a2
                inner join tec_sale_items b2 on a2.id = b2.sale_id
                where a2.date>'{$fec_inv}' {$cad3} and a2.store_id = {$store_id}
                group by b2.product_id
              ) vtas
              inner join (
                select tec_recetas.product_id, tec_recetas.id_insumo, tec_products.name, tec_products.unidad, 
                if(tec_products.unidad = 'UNIDAD', tec_recetas.cantidadReceta, tec_recetas.cantidadReceta / 1000) cantidadReceta
                from tec_recetas
                inner join tec_products on tec_recetas.id_insumo = tec_products.id
              ) mire on vtas.product_id = mire.product_id
              group by mire.id_insumo, mire.name, mire.unidad
          ) ventas on a.product_id = ventas.id_insumo
          left join (
              select tm.product_id, sum(tm.cantidad) as cantidad
              from tec_movim tm
              where tm.store_id = {$store_id} and tm.tipo_mov = 'I' and tm.fechah between '{$fec_inv}' and '{$fec_inv2}' and tm.confirmado = '1'
              group by tm.product_id
          ) movim_i on a.product_id = movim_i.product_id
          left join (
              select tm.product_id, sum(tm.cantidad) as cantidad
              from tec_movim tm
              where tm.store_id = {$store_id} and tm.tipo_mov = 'S' and tm.fechah between '{$fec_inv}' and '{$fec_inv2}' and tm.confirmado = '1'
              group by tm.product_id
          ) movim_s on a.product_id = movim_s.product_id
          WHERE a.store_id = {$store_id}
          order by b.name";

    }

    public function productos(){
        $cSql = "select id, name from tec_products where category_id in (7) order by name";
        $result = $this->db->query($cSql)->result_array();
        $ar = array('0'=>'Seleccione');
        foreach($result as $r){
            $ar[$r["id"]] = strtoupper($r["name"]);
        }
        return $ar;
    }

    public function unidades(){
        $cSql = "select codigo, descrip from tec_unidades";
        $result = $this->db->query($cSql)->result_array();
        $ar = array('0'=>'Seleccione');
        foreach($result as $r){
            $ar[$r["codigo"]] = strtoupper($r["descrip"]);
        }
        return $ar;
    }

}
