<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
        $this->por_tarjeta = 0.95;
        $this->por_delivery = 0.75;
    }

    public function getAllProducts() {
        $q = $this->db->get('products');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCustomers() {
        $q = $this->db->get('customers');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function topProducts() {
        $m = date('Y-m');
        $this->db->select($this->db->dbprefix('products').".code as product_code, ".$this->db->dbprefix('products').".name as product_name, sum(".$this->db->dbprefix('sale_items').".quantity) as quantity")
        ->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('sales', 'sales.id=sale_items.sale_id', 'left')
        ->order_by("sum(".$this->db->dbprefix('sale_items').".quantity)", 'desc')
        ->group_by('sale_items.product_id')
        ->limit(10)
        ->like($this->db->dbprefix('sales').'.date', $m, 'both');
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('sale_items');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function topProducts1() {
        $m = date('Y-m', strtotime('first day of last month'));
        $this->db->select($this->db->dbprefix('products').".code as product_code, ".$this->db->dbprefix('products').".name as product_name, sum(".$this->db->dbprefix('sale_items').".quantity) as quantity")
        ->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('sales', 'sales.id=sale_items.sale_id', 'left')
        ->order_by("sum(".$this->db->dbprefix('sale_items').".quantity)", 'desc')
        ->group_by('sale_items.product_id')
        ->limit(10)
        ->like($this->db->dbprefix('sales').'.date', $m, 'both');
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('sale_items');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function topProducts3() {
        $this->db->select($this->db->dbprefix('products').".code as product_code, ".$this->db->dbprefix('products').".name as product_name, sum(".$this->db->dbprefix('sale_items').".quantity) as quantity")
        ->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('sales', 'sales.id=sale_items.sale_id', 'left')
        ->order_by("sum(".$this->db->dbprefix('sale_items').".quantity)", 'desc')
        ->group_by('sale_items.product_id')
        ->limit(10);
        if ($this->db->dbdriver == 'sqlite3') {
            // ->where("date >= datetime('now','-6 month')", NULL, FALSE)
            $this->db->where("{$this->db->dbprefix('sales')}.date >= datetime(date('now','start of month','+1 month','-1 day'), '-3 month')", NULL, FALSE);
        } else {
            $this->db->where($this->db->dbprefix('sales').'.date >= last_day(now()) + interval 1 day - interval 3 month', NULL, FALSE);
        }
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('sale_items');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function topProducts12() {
        $this->db->select($this->db->dbprefix('products').".code as product_code, ".$this->db->dbprefix('products').".name as product_name, sum(".$this->db->dbprefix('sale_items').".quantity) as quantity")
        ->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('sales', 'sales.id=sale_items.sale_id', 'left')
        ->order_by("sum(".$this->db->dbprefix('sale_items').".quantity)", 'desc')
        ->group_by('sale_items.product_id')
        ->limit(10);
        if ($this->db->dbdriver == 'sqlite3') {
            // ->where("date >= datetime('now','-6 month')", NULL, FALSE)
            $this->db->where("{$this->db->dbprefix('sales')}.date >= datetime(date('now','start of month','+1 month','-1 day'), '-12 month')", NULL, FALSE);
        } else {
            $this->db->where($this->db->dbprefix('sales').'.date >= last_day(now()) + interval 1 day - interval 12 month', NULL, FALSE);
        }

        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('sale_items');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getDailySales($year, $month) {
        if ($this->db->dbdriver == 'sqlite3') {
            $this->db->select("strftime('%d', date) AS date, COALESCE(sum(product_tax), 0) as product_tax, COALESCE(sum(order_tax), 0) as order_tax, COALESCE(sum(total), 0) as total, COALESCE(sum(grand_total), 0) as grand_total, COALESCE(sum(total_tax), 0) as total_tax, COALESCE(sum(total_discount), 0) as discount, COALESCE(sum(paid), 0) as paid", FALSE);
        } else {
            $this->db->select("DATE_FORMAT( date,  '%d' ) AS date, COALESCE(sum(product_tax), 0) as product_tax, COALESCE(sum(order_tax), 0) as order_tax, COALESCE(sum(total), 0) as total, COALESCE(sum(grand_total), 0) as grand_total, COALESCE(sum(total_tax), 0) as total_tax, COALESCE(sum(total_discount), 0) as discount, COALESCE(sum(paid), 0) as paid", FALSE);
        }
        $this->db->like('date', "{$year}-{$month}", 'after');
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('sales');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getMonthlySales($year) {
        if ($this->db->dbdriver == 'sqlite3') {
            $this->db->select("strftime('%m', date) AS date, COALESCE(sum(product_tax), 0) as product_tax, COALESCE(sum(order_tax), 0) as order_tax, COALESCE(sum(total), 0) as total, COALESCE(sum(grand_total), 0) as grand_total, COALESCE(sum(total_tax), 0) as tax, COALESCE(sum(total_discount), 0) as discount, COALESCE(sum(paid), 0) as paid", FALSE)
            ->group_by("strftime('%m', date)");
        } else {
            $this->db->select("DATE_FORMAT( date,  '%m' ) AS date, COALESCE(sum(product_tax), 0) as product_tax, COALESCE(sum(order_tax), 0) as order_tax, COALESCE(sum(total), 0) as total, COALESCE(sum(grand_total), 0) as grand_total, COALESCE(sum(total_tax), 0) as tax, COALESCE(sum(total_discount), 0) as discount, COALESCE(sum(paid), 0) as paid", FALSE)
            ->group_by("DATE_FORMAT(date,  '%m')")
            ->order_by("DATE_FORMAT(date,  '%m') ASC");
        }

        $this->db->like('date', "{$year}", 'after');
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('sales');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTotalCustomerSales($customer_id, $user = NULL, $start_date = NULL, $end_date = NULL) {
        $this->db->select('COUNT(id) as number, sum(grand_total) as amount, sum(paid) as paid');
        if ($start_date && $end_date) {
            $this->db->where('date >=', $start_date);
            $this->db->where('date <=', $end_date);
        }
        if ($user) {
            $this->db->where('created_by', $user);
        }
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get_where('sales', array('customer_id' => $customer_id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalSalesforCustomer($customer_id, $user = NULL, $start_date = NULL, $end_date = NULL) {
        if ($start_date && $end_date) {
            $this->db->where('date >=', $start_date);
            $this->db->where('date <=', $end_date);
        }
        if ($user) {
            $this->db->where('created_by', $user);
        }
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q=$this->db->get_where('sales', array('customer_id' => $customer_id));
        return $q->num_rows();
    }

    public function getTotalSalesValueforCustomer($customer_id, $user = NULL, $start_date = NULL, $end_date = NULL) {
        $this->db->select('sum(grand_total) as total');
        if($start_date && $end_date) {
            $this->db->where('date >=', $start_date);
            $this->db->where('date <=', $end_date);
        }
        if($user) {
            $this->db->where('created_by', $user);
        }
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q=$this->db->get_where('sales', array('customer_id' => $customer_id));
        if( $q->num_rows() > 0 ) {
            $s = $q->row();
            return $s->total;
        }
        return FALSE;
    }

    public function getAllStaff() {

        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTotalSales($start, $end) {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', FALSE)
            ->where("date >= '{$start}' and date <= '{$end}'", NULL, FALSE);
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalPurchases($start, $end) {
        $this->db->select('count(id) as total, sum(COALESCE(total, 0)) as total_amount', FALSE)
            ->where("date >= '{$start}' and date <= '{$end}'", NULL, FALSE);
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalExpenses($start, $end) {
        $this->db->select('count(id) as total, sum(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where("date >= '{$start}' and date <= '{$end}'", NULL, FALSE);
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }
        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    function query_salidas_por_dia_ventas($tienda, $fec_ini, $fec_fin){
        // DETALLE DE VENTAS
        // Nota.- Estos % tambien estan en libreria FM
        $por_tarjeta    = $this->por_tarjeta;
        $por_delivery   = $this->por_delivery;


        $cSql = "select date_format(tc.fecha,'%d-%m-%Y') as fecha, tc.dia_semana, b.cash, b.vendemas, b.transferencia, b.yape, b.plin, b.rappi, b.pedidosya, b.didi, b.culqi, 
            b.cash + b.vendemas + b.transferencia + b.yape + b.plin + b.rappi + b.pedidosya + b.didi + b.culqi + b.otros as total
            from tec_calendario tc
            left join 
            (
                select date(ts.date) fecha, 
                    sum(tp.cash) cash,
                    sum(tp.vendemas) vendemas,
                    sum(tp.transferencia) transferencia,
                    sum(tp.yape) yape,
                    sum(tp.plin) plin,
                    sum(tp.rappi) rappi,
                    sum(tp.pedidosya) pedidosya,
                    sum(tp.didi) didi,
                    sum(tp.culqi) culqi,
                    sum(tp.otros) otros
                from tec_sales ts
                inner join 
                (
                    select date(date) fecha,sale_id, 
                    sum(if(paid_by = 'cash',amount,0)) cash,
                    sum(if(paid_by = 'Vendemas',amount* $por_tarjeta,0)) vendemas,
                    sum(if(substr(paid_by,1,6)='Transf',amount,0)) transferencia,
                    sum(if(paid_by = 'Yape',amount,0)) yape,
                    sum(if(paid_by = 'Plin',amount,0)) plin,
                    sum(if(paid_by = 'Rappi',amount * $por_delivery,0)) rappi,
                    sum(if(paid_by = 'PedidosYa',amount * $por_delivery,0)) pedidosya,
                    sum(if(paid_by = 'Didi',amount * $por_delivery,0)) didi,
                    sum(if(paid_by = 'Culqi',amount * $por_tarjeta,0)) culqi,
                    sum(if(paid_by not in ('cash','Vendemas','Yape','Plin','Rappi','PedidosYa','Didi','Culqi') and substr(paid_by,1,6)!='Transf',amount,0)) otros
                    from tec_payments where note != 'PASE' and date(date) between '$fec_ini' and '$fec_fin'
                    group by date(date), sale_id
                ) tp on ts.id = tp.sale_id      
                where ts.store_id = $tienda
                group by date(ts.date)
            ) b on date(tc.fecha) = b.fecha
            where tc.fecha between '$fec_ini' and '$fec_fin'
            order by tc.fecha";
        //die($cSql);
        return $cSql;
    }

    function query_salidas_por_dia($tienda, $fec_ini, $fec_fin){
        // DETALLE RESUMEN - CUADRE DE CAJA
        $por_tarjeta    = $this->por_tarjeta;
        $por_delivery   = $this->por_delivery;
        $cSql = "select date_format(tc.fecha,'%d-%m-%Y') as fecha, tc.dia_semana, tr.cash_in_hand, tr.cash_in_hand_adicional, ts.grand_total, a.con_factura, a.con_boleta, 
            a.con_recibo, a.total_salidas, 
            if(ts.grand_total is null, 0, ts.grand_total) as total_ventas_efectivo,
            ts.vendemas + ts.transferencia + ts.yape + ts.plin + ts.culqi + ts.rappi + ts.pedidosya + ts.didi + ts.otros as total_otras_ventas,
            remesas.remesa,
            
            if(tr.cash_in_hand is null,0,tr.cash_in_hand) + if(tr.cash_in_hand_adicional is null,0,tr.cash_in_hand_adicional) 
            + if(ts.grand_total is null, 0, ts.grand_total) 
            + if(ts.vendemas is null,0,ts.vendemas) + if(ts.culqi is null,0,ts.culqi) + if(ts.transferencia is null,0,ts.transferencia) + if(ts.yape is null,0,ts.yape) + if(ts.plin is null,0,ts.plin) + if(ts.rappi is null,0,ts.rappi) 
            + if(ts.pedidosya is null,0,ts.pedidosya) + if(ts.didi is null,0,ts.didi) + if(ts.otros is null,0,ts.otros) 
            - if(a.total_salidas is null,0,a.total_salidas) as caja_final,
             
            if(tr.cash_in_hand is null,0,tr.cash_in_hand) + if(tr.cash_in_hand_adicional is null,0,tr.cash_in_hand_adicional)
            + if(ts.grand_total is null, 0, ts.grand_total) 
            - (if(a.con_factura is null,0,a.con_factura) + if(a.con_boleta is null,0,a.con_boleta) + if(a.con_recibo is null,0,a.con_recibo)) as caja_final_efectivo,

                tr.status as cierre,
                if(tr.note is null,'',tr.note) note
            from tec_calendario tc
            left join (
                SELECT 
                    date_format(tp.date, '%d-%m-%Y') as fecha,
                    tp.store_id,
                    sum(if(tp.tipoDoc='F', `costo_tienda`, 0)) con_factura, 
                    sum(if(tp.tipoDoc='B', `costo_tienda`, 0)) con_boleta, 
                    sum(if(tp.tipoDoc not in ('F', 'B'),tp.costo_tienda, 0)) con_recibo, 
                    sum(if(tp.costo_tienda is null, 0, tp.costo_tienda)) total_salidas
                FROM `tec_purchases` tp left join tec_subtipo_gastos on tp.clasifica2 = tec_subtipo_gastos.id and tec_subtipo_gastos.descrip != 'Remesas'
                where tp.store_id = $tienda
                GROUP BY date_format(tp.date, '%d-%m-%Y'), tp.store_id
            ) a on date_format(tc.fecha,'%d-%m-%Y') = a.fecha
            left join (
                select date_format(tec_sales.date, '%d-%m-%Y') fecha, tec_sales.store_id, 
                sum(if(tp.paid_by = 'cash',tp.amount,0)) grand_total,
                sum(if(tp.paid_by = 'Vendemas',tp.amount * $por_tarjeta,0)) vendemas, 
                sum(if(substr(tp.paid_by,1,6)='Transf',tp.amount,0)) transferencia,
                sum(if(tp.paid_by = 'Yape',tp.amount,0)) yape,
                sum(if(tp.paid_by = 'Plin',tp.amount,0)) plin,
                sum(if(tp.paid_by = 'CULQI',tp.amount * $por_tarjeta,0)) culqi, 
                sum(if(tp.paid_by = 'Rappi',tp.amount * $por_delivery,0)) rappi,
                sum(if(tp.paid_by = 'PedidosYa',tp.amount * $por_delivery,0)) pedidosya,
                sum(if(tp.paid_by = 'Didi',tp.amount * $por_delivery,0)) didi,
                sum(if(tp.paid_by not in ('cash','Vendemas','Yape','Plin','Rappi','PedidosYa','Didi','CULQI') and substr(tp.paid_by,1,6)!='Transf',tp.amount,0)) otros
                from tec_sales
                inner join tec_payments tp on tec_sales.id = tp.sale_id and tp.note != 'PASE'
                where tec_sales.store_id = $tienda
                GROUP BY date_format(tec_sales.date, '%d-%m-%Y'), tec_sales.store_id
            ) ts on date_format(tc.fecha,'%d-%m-%Y') = ts.fecha
            left join (
                select date_format(date, '%d-%m-%Y') fecha, store_id, cash_in_hand, cash_in_hand_adicional, closed_at, status, note 
                from tec_registers
                where store_id = $tienda 
            ) tr on date_format(tc.fecha,'%d-%m-%Y') = tr.fecha
            left join (
                select date_format(a.date,'%d-%m-%Y') fecha, a.store_id, b.product_id, sum(b.quantity*b.cost) remesa 
                from tec_purchases a
                inner join tec_purchase_items b on a.id = b.purchase_id
                inner join tec_products c on b.product_id = c.id    
                where c.name = 'REMESA' and a.store_id = $tienda
                group by date_format(a.date,'%d-%m-%Y'), a.store_id, b.product_id 
            ) remesas on date_format(tc.fecha,'%d-%m-%Y') = remesas.fecha    
            where tc.fecha >= '$fec_ini' and tc.fecha <= '$fec_fin'
            order by tc.fecha";

        //$this->data['query'] = $this->db->query($cSql);
        return $cSql;
    }

    function query_canales($store_id, $cDesde='', $cHasta=''){
        $por_tarjeta    = $this->por_tarjeta;
        $por_delivery   = $this->por_delivery;
        
         $cSql = "select date(a.date) fecha, 
            sum(case when b.paid_by = 'Rappi' then b.amount * $por_delivery else 0 end) rappi,
            sum(case when b.paid_by = 'PedidosYa' then b.amount * $por_delivery else 0 end) pedidosya,
            sum(case when (b.paid_by = 'Didi' or a.tipo_precio_id = 5) then b.amount * $por_delivery else 0 end) didi,
            sum(case when (b.paid_by not in ('Rappi','PedidosYa','Didi') and a.tipo_precio_id != 4)
                then if(b.paid_by='CULQI', b.amount * $por_tarjeta, b.amount) else 0 end) directo,
            sum(case when b.paid_by not in ('Rappi','PedidosYa','Didi') and a.tipo_precio_id = 4
                then if(b.paid_by='CULQI', b.amount * $por_tarjeta, b.amount) 
                else 0 end) propio
            from tec_sales a
            left join (
                select sale_id, paid_by, amount from tec_payments where date(date) between '$cDesde' and '$cHasta'
            ) b on a.id = b.sale_id
            where a.store_id = $store_id and date(a.date) between '$cDesde' and '$cHasta' and a.anulado != '1'
            group by date(a.date)
            order by date(a.date)";

        return $cSql;
    }

}
