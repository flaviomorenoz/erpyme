<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function topProducts($user_id = NULL) {
        $m = date('Y-m');
        if(!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select($this->db->dbprefix('products').".code as product_code, ".$this->db->dbprefix('products').
            ".name as product_name, sum(".$this->db->dbprefix('sale_items').".quantity) as quantity")
        ->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('sales', 'sales.id=sale_items.sale_id', 'left')
        ->order_by("sum(".$this->db->dbprefix('sale_items').".quantity)", 'desc')
        ->group_by('sale_items.product_id')
        ->limit(10)
        ->like("{$this->db->dbprefix('sales')}.date", $m, 'both');
        if($user_id) {
            $this->db->where('created_by', $user_id);
        }
        
        /*
        if ($this->session->userdata('store_id')) {
            $this->db->where('store_id', $this->session->userdata('store_id'));
        }*/
        
        //$this->fm->traza($this->db->get_compiled_select('sale_items'));

        $q = $this->db->get('sale_items');
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getChartData($user_id = NULL) {
        if(!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($this->db->dbdriver == 'sqlite3') {
            $this->db->select("strftime('%Y-%m', date) as month, SUM(total) as total, SUM(total_tax) as tax, SUM(total_discount) as discount")
            ->where("date >= datetime('now','-6 month')", NULL, FALSE)
            // ->order_by("strftime('%Y-%m', date)", 'asc')
            ->group_by("strftime('%Y-%m', date)");
        } else {

            /* PRIMERA VERSION
            $this->db->select("date_format(date, '%Y-%m') as month, SUM(total) as total, SUM(total_tax) as tax, SUM(total_discount) as discount")
            ->where("date >= date_sub( now() , INTERVAL 6 MONTH)", NULL, FALSE)
            ->group_by("date_format(date, '%Y-%m')");*/

            // SEGUNDA VERSION
            $query = $this->db->select("
                sum(if(tec_payments.paid_by = 'Vendemas', (tec_sales.total*0.955), 
                        if(tec_payments.paid_by = 'Rappi', (tec_sales.total*0.75),
                            if(tec_payments.paid_by = 'PedidosYa', (tec_sales.total*0.75), tec_sales.total)
                        )
                    )
                ) as total,     
                sum(tec_sales.total_tax) as tax, 
                sum(tec_sales.total_discount) as discount")
            ->join("tec_payments","tec_sales.id = tec_payments.sale_id")
            ->where("tec_sales.date >= date_sub( now() , INTERVAL 6 MONTH)", NULL, FALSE)
            ->group_by("date_format(tec_sales.date, '%Y-%m')");
            
            /*$query = $this->db->query("select
                date_format(date, '%Y-%m') as month,
                round(sum(v_ganancia_real_mensual.total_sin_deli),2) as total, 
                round(sum(v_ganancia_real_mensual.total_tax),2) as tax, 
                round(sum(v_ganancia_real_mensual.total_discount),2) as discount 
                from v_ganancia_real_mensual
                group by date_format(date, '%Y-%m')");
            */

            $data = $query->result();

            return $data;
        }
/*
        if($user_id) {
            $this->db->where('tec_sales.created_by', $user_id);
        }
        if ($store_id = $this->session->userdata('store_id')) {
            $this->db->where('tec_sales.store_id', $store_id);
        }
        $q = $this->db->get('tec_sales');
        //echo $this->db->get_compiled_select('tec_sales');
        //die();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
*/
        return FALSE;
    }

    public function getUserGroups() {
        $this->db->order_by('id', 'desc');
        $q = $this->db->get("users_groups");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function userGroups() {
        $ugs = $this->getUserGroups();
        if ($ugs) {
            foreach ($ugs as $ug) {
                $this->db->update('users', array('group_id' => $ug->group_id), array('id' => $ug->user_id));
            }
            return true;
        }
        return false;
    }

    public function getAllProducts() {
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function syncStoreQty() {
        $products = $this->getAllProducts();
        foreach ($products as $product) {
            $this->db->insert('product_store_qty', array('product_id' => $product->id, 'store_id' => 1, 'quantity' => $product->quantity));
        }
        $this->db->update('settings', array('version' => '4.0.6'), array('setting_id' => 1));
    }

}
