<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
        $this->Igv = 10;
    }

    public function getProductNames($term, $limit = 10) {
        $store_id = $this->session->userdata('store_id');
        $this->db->select("{$this->db->dbprefix('products')}.*, COALESCE(psq.quantity, 0) as quantity, COALESCE(psq.price, 0) as store_price")
        ->join("( SELECT * from {$this->db->dbprefix('product_store_qty')} WHERE store_id = {$store_id}) psq", 'products.id=psq.product_id', 'left');
        if ($this->db->dbdriver == 'sqlite3') {
            $this->db->where("(name LIKE '%{$term}%' OR code LIKE '%{$term}%' OR  (name || ' (' || code || ')') LIKE '%{$term}%')");
        } else {
            $this->db->where("(name LIKE '%{$term}%' OR code LIKE '%{$term}%' OR  concat(name, ' (', code, ')') LIKE '%{$term}%')");
        }
        $this->db->where("category_id!=",7); // Insumos
        $this->db->group_by('products.id')->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTodaySales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getTodayCCSales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'CC');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashSales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayRefunds() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('return_sales', 'return_sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayExpenses() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE)
            ->where('date >', $date);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayCashRefunds() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('return_sales', 'return_sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'cash');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayChSales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->group_start()->where("{$this->db->dbprefix('payments')}.paid_by", 'Cheque')->or_where("{$this->db->dbprefix('payments')}.paid_by", 'cheque')->group_end();

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayYaSales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->group_start()->where("{$this->db->dbprefix('payments')}.paid_by", 'Yape')->or_where("{$this->db->dbprefix('payments')}.paid_by", 'Yape')->group_end();

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayPlSales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->group_start()->where("{$this->db->dbprefix('payments')}.paid_by", 'Plin')->or_where("{$this->db->dbprefix('payments')}.paid_by", 'Plin')->group_end();

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayOtherSales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'other');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayGCSales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'gift_card');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTodayStripeSales() {
        $date = date('Y-m-d 00:00:00');
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'stripe');

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getRegisterCCSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->where("{$this->db->dbprefix('payments')}.paid_by", 'CC');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCashSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }

        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'cash');
        $this->db->where('payments.created_by', $user_id);
        $q = $this->db->get('payments');

        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function getRegisterYapeSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'Yape');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterPlinSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'Plin');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterRefunds($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('return_sales', 'return_sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date);
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterCashRefunds($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS returned', FALSE)
            ->join('return_sales', 'return_sales.id=payments.return_id', 'left')
            ->where('type', 'returned')->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'cash');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterExpenses($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE)
            ->where('date >', $date);
        $this->db->where('created_by', $user_id);

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterChSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->group_start()->where("{$this->db->dbprefix('payments')}.paid_by", 'Cheque')->or_where("{$this->db->dbprefix('payments')}.paid_by", 'cheque')->group_end();
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterYaSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->group_start()->where("{$this->db->dbprefix('payments')}.paid_by", 'Yape')->or_where("{$this->db->dbprefix('payments')}.paid_by", 'Yape')->group_end();
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterPlSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)
            ->group_start()->where("{$this->db->dbprefix('payments')}.paid_by", 'Yape')->or_where("{$this->db->dbprefix('payments')}.paid_by", 'Yape')->group_end();
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function getRegisterOtherSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'other');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterGCSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'gift_card');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getRegisterStripeSales($date = NULL, $user_id = NULL) {
        if (!$date) {
            $date = $this->session->userdata('register_open_time');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->select('COUNT(' . $this->db->dbprefix('payments') . '.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid', FALSE)
            ->join('sales', 'sales.id=payments.sale_id', 'left')
            ->where('payments.date >', $date)->where("{$this->db->dbprefix('payments')}.paid_by", 'stripe');
        $this->db->where('payments.created_by', $user_id);

        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function products_count($category_id) {
        if ($category_id) {
           $this->db->where('category_id', $category_id);
        }
        return $this->db->count_all_results('products');
    }

    public function fetch_products($category_id, $limit, $start) {
        $this->db->limit($limit, $start);
        if ($category_id) {
           $this->db->where('category_id', $category_id);
        }
        $this->db->order_by("code", "asc");
        
        $query = $this->db->get("products");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function registerData($user_id = NULL) {
        /*
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('registers', array('user_id' => $user_id, 'status' => 'open'), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;*/
        $cSql = "select * from tec_registers a where date(a.date) = curdate()";
        $query = $this->db->query($cSql);
        if($query->num_rows() > 0){
            return $query->num_rows();
        }else{
            return false;
        }
    }

    public function openRegister($data) {
        $la_fecha = $data["date"];
        $la_fecha = substr($data["date"], 0, 10);
        
        $query = $this->db->query("select * from tec_registers where date(date) = ? and store_id = ?",array($la_fecha, $data["store_id"]));
        if($query->num_rows() > 0){
            return false;
        }

        if ($this->db->insert('registers', $data)) {
            return true;
        }
        return FALSE;
    }

    public function getOpenRegisters() {
        $this->db->select("date, user_id, cash_in_hand, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' - ', " . $this->db->dbprefix('users') . ".email) as user", FALSE)
            ->join('users', 'users.id=pos_register.user_id', 'left');
        $q = $this->db->get_where('registers', array('status' => 'open'));
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;

    }

    public function closeRegister($rid, $user_id, $data) {
        if (!$rid) {
            $rid = $this->session->userdata('register_id');
        }
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($data['transfer_opened_bills'] == -1) {
            $this->db->delete('suspended_sales', array('created_by' => $user_id));
        } elseif ($data['transfer_opened_bills'] != 0) {
            $this->db->update('suspended_sales', array('created_by' => $data['transfer_opened_bills']), array('created_by' => $user_id));
        }
        if ($this->db->update('registers', $data, array('id' => $rid, 'user_id' => $user_id))) {
            return true;
        }
        return FALSE;
    }

    public function getCustomerByID($id) {
        $q = $this->db->get_where('customers', array('id' => $id), 1);
          if( $q->num_rows() > 0 ) {
            return $q->row();
          }
          return FALSE;
    }

    public function getProductByCode($code) {
        $jpsq = "( SELECT product_id, quantity, price from {$this->db->dbprefix('product_store_qty')} WHERE store_id = {$this->session->userdata('store_id')} ) AS PSQ";
        $this->db->select("{$this->db->dbprefix('products')}.*, COALESCE(PSQ.quantity, 0) as quantity, COALESCE(PSQ.price, {$this->db->dbprefix('products')}.price) as store_price", FALSE)
        ->join($jpsq, 'PSQ.product_id=products.id', 'left');
        
        //die($this->db->get_compiled_select('products'));

        $q = $this->db->get_where('products', array('code' => $code), 1);
          if( $q->num_rows() > 0 )
          {
            return $q->row();
          }
          return FALSE;
    }

    public function getProductByCode2($code,$delivery="0") {

        if($delivery == '1'){
            $campo = "price_delivery_01";
        }elseif($delivery == '2'){
            $campo = "price_delivery_02";
        }else{
            $campo = "price";
        }

        $cSql = "SELECT a.*, COALESCE(PSQ.quantity, 0) as quantity, COALESCE(PSQ.price, a.price) as store_price
            FROM `tec_products` a
            LEFT JOIN ( SELECT product_id, quantity, coalesce({$campo},price) as price from tec_product_store_qty WHERE store_id = {$this->session->userdata('store_id')} ) AS PSQ ON `PSQ`.`product_id`=a.`id`
            where a.code = '{$code}'";

        $q = $this->db->query($cSql);

        if( $q->num_rows() > 0 )
        {
           return $q->row();
        }
        return FALSE;
    }

    public function obtener_correlativo($store_id, $tipoDocAfectado = null){
        $ar = array();
        
        // Averiguando el nro correlativo:
        for($n=0; $n<3; $n++){
            if($n==0){$tipoDoc = 'Boleta';}
            if($n==1){$tipoDoc = 'Factura';}
            if($n==2){$tipoDoc = 'Ticket';}
            
            $this->db->select_max('correlativo');
    
            if(substr($tipoDoc,0,8) == 'Nota_de_'){
                
                if($tipoDocAfectado == '1'){
                    $this->db->like('tipoDoc', 'Factura');    
                }elseif($tipoDocAfectado == '2'){
                    $this->db->like('tipoDoc', 'Boleta');    
                }
                
            }else{
                $this->db->where("tipoDoc",$tipoDoc);
            }
            $this->db->where("tipoDoc is not null");
            $this->db->where("store_id",$store_id);
            
            //echo $this->db->get_compiled_select('sales');
            //die();
            
            $result = $this->db->get('sales')->result();
    
            $maximo = 0;
            foreach($result as $r){
                if(is_null($r->correlativo)){
                    if($tipoDoc == 'Boleta'){
                        $maximo = 0;   
                    }elseif($tipoDoc == 'Factura'){
                        $maximo = 0; 
                    }elseif($tipoDoc == 'Nota_de_debito'){
                        $maximo = 0;
                    }elseif($tipoDoc == 'Nota_de_credito'){
                        $maximo = 0;
                    }elseif($tipoDoc == 'Ticket'){
                        $maximo = 0;
                    }
                }else{
                    $maximo = $r->correlativo * 1;
                }
            }
            
            if(is_null($maximo)){
                $maximo = 1;
            }else{
                $maximo = $maximo + 1;
            }
            
            if($n==0){$ar["maximo_boleta"] = $maximo;}
            if($n==1){$ar["maximo_factura"] = $maximo;}
            if($n==2){$ar["maximo_ticket"] = $maximo;}
        }

        return json_encode($ar);
        
    }
/*
    public function addSale($data, $items, $payment = array(), $did = NULL) {

        $this->db->where('serie',$data['serie']);
        $this->db->where('correlativo',$data['correlativo']);
        $query = $this->db->get('sales');
        
        $n=0;
        foreach($query->result() as $r){
            $n++;
            //echo("Algo sale mal...FFD serie:".$data['serie']);
            //die("correlativo:".$data['correlativo']);
        }
        
        
        if ($n==0){
            $this->db->trans_begin();
            $bandera_valida = true;

            $this->db->insert('sales', $data);
            //echo $this->db->set($data)->get_compiled_insert('sales');
            //die('Black Mirror');
            //print_r($data);
            //die("");

            if($this->db->insert('sales', $data)){
    
                //die("Flag 6");
                $sale_id = $this->db->insert_id();
    
                foreach ($items as $item){
                    $item['sale_id'] = $sale_id;
                    if($this->db->insert('sale_items', $item)) {
                        if ($item['product_id'] > 0 && $product = $this->site->getProductByID($item['product_id'])) {
                            if ($product->type == 'standard') {
                                $this->db->update('product_store_qty', array('quantity' => ($product->quantity-$item['quantity'])), array('product_id' => $product->id, 'store_id' => $data['store_id']));
                            } elseif ($product->type == 'combo') {
                                $combo_items = $this->getComboItemsByPID($product->id);
                                foreach ($combo_items as $combo_item) {
                                    $cpr = $this->site->getProductByID($combo_item->id);
                                    if($cpr->type == 'standard') {
                                        $qty = $combo_item->qty * $item['quantity'];
                                        $this->db->update('product_store_qty', array('quantity' => ($cpr->quantity-$qty)), array('product_id' => $cpr->id, 'store_id' => $data['store_id']));
                                    }
                                }
                            }
                        }
                    }
                }
    
                if($did) {
                    $this->db->delete('suspended_sales', array('id' => $did));
                    $this->db->delete('suspended_items', array('suspend_id' => $did));
                }
                
                $msg = array();
                if(! empty($payment)) {
                    $nLimites = count($payment);
                    for($i=0; $i<$nLimites; $i++){
                        
                        $payment[$i]['sale_id'] = $sale_id;
                        $ar_pay = $payment[$i];
                        if ($this->db->insert('payments', $ar_pay)){

                        }else{
                            //die("No ha podido grabar Payments !!!");
                        }

                    }
                }
                
                //if($this->db->trans_status() === FALSE){ die("Algo ya falló"); }
                //die("Flag 7");
                
                // A estas alturas ya debe estar registrado un pago en la db
                
                usleep(400000);
                $query = $this->db->select('id')->where('sale_id',$sale_id)->get("payments");
                $nix = 0;
                foreach($query->result() as $r){ 
                    $nix++;
                }
                if($nix == 0){
                    $bandera_valida = false;
                }

                if (strtoupper($payment[0]["note"])!='PASE'){
                    // Generando la BV/FA electronica
                    $this->el_json  = "";
     
                    $this->enviar_doc_sunat_nubefact_individual($sale_id, false);
                    //if($this->db->trans_status() === FALSE){ die("Algo ya falló"); }
                }else{
                    $this->marcar_envio_sunat($sale_id,'2');  // para el caso de PASE
                    //if($this->db->trans_status() === FALSE){ die("Algo ya falló x"); }
                }
                
            }

            if ($this->db->trans_status() === FALSE || $bandera_valida == false){
                $this->db->trans_rollback();
                return false;
            }else{
                $this->db->trans_commit();
                return array('sale_id' => $sale_id, 'message' => $msg);
            }
        }
        return false;
    }
*/
    function enviar_doc_sunat_nubefact_individual($sale_id, $activar=false){
        
        $result = $this->db->select("tipoDoc, customer_id, store_id, serie, total, total_tax, grand_total, correlativo, total_discount, tipoDocAfectado, serieDocfectado, numDocfectado, codMotivo")
            ->where("id",$sale_id)->get("sales")->result();

        foreach($result as $r){
            $tipo_comprobante = $r->tipoDoc;
            $customer_id    = $r->customer_id;
            $store_id       = $r->store_id;
            $correlativo    = $r->correlativo;
            $serie          = $r->serie;
            $cod_motivo     = ""; //$r->cod_motivo;
            $total_descuento = $r->total_discount;
            $total          = $r->total;
            $total_tax      = $r->total_tax;
            $grand_total    = $r->grand_total;
            $tipoDocAfectado = $r->tipoDocAfectado;
            $serieDocfectado = $r->serieDocfectado;
            $numDocfectado  = $r->numDocfectado;
            $codMotivo      = $r->codMotivo;
        }

        //Averiguando los datos de la empresa
        $result     = $this->db->select("code, city, state, ubigeo, address1, address2")->where("id",$store_id)->get("stores")->result_array();

        foreach($result as $r){
            $this->COMPANY_DIRECCION      = $r["address1"]; 
            $this->COMPANY_PROV           = $r["city"];
            $this->COMPANY_DPTO           = "LIMA";
            $this->COMPANY_DISTRITO       = $r["state"];
            $this->COMPANY_UBIGEO         = $r["ubigeo"];
            $this->COMPANY_RAZON_SOCIAL   = $r["address2"]; 
            $this->COMPANY_RUC            = $r["code"]; 
        }

        $cSql = "select cf1, cf2, name, direccion, email from tec_customers where id = {$customer_id}";
        
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
            $dni                = $r->cf1;
            $ruc                = $r->cf2;
            $cliente_name       = $r->name;
            $cliente_direccion  = $r->direccion;
            $cliente_email      = (is_null($r->email) ? "" : $r->email);
        }

        $data                       = array();
        $data["tipo_comprobante"]   = $tipo_comprobante;
        $data["customer_id"]        = $customer_id;
        $data["dni"]                = $dni;
        $data["ruc"]                = $ruc;
        $data["cliente_name"]       = $cliente_name;
        $data["cliente_direccion"]  = $cliente_direccion;
        $data["cliente_email"]      = $cliente_email;
        $data["serie"]              = $serie;
        $data["cod_motivo"]         = $cod_motivo;
        $data["total_discount"]     = $total_descuento;
        $data["correlativo"]        = $correlativo;
        $data["total"]              = $total;
        $data["total_tax"]          = $total_tax;
        $data["grand_total"]        = $grand_total;
        $data["tipoDocAfectado"]    = $tipoDocAfectado;
        $data["serieDocfectado"]    = $serieDocfectado;
        $data["numDocfectado"]      = $numDocfectado;
        $data["codMotivo"]          = $codMotivo;

        $items                          = array();
        
        $query = $this->db->select("*")->where("sale_id",$sale_id)->get("sale_items");
        $i = -1;
        foreach($query->result() as $r){
            $i++;
            $items[$i]["product_code"]      = $r->product_code;
            $items[$i]["product_name"]      = $r->product_name;
            $items[$i]["quantity"]          = $r->quantity;
            $items[$i]["net_unit_price"]    = $r->net_unit_price;
            $items[$i]["unit_price"]        = $r->unit_price;
            $items[$i]["discount"]          = $r->discount;
            $items[$i]["net_unit_price"]    = $r->net_unit_price;
            $items[$i]["quantity"]          = $r->quantity;
            $items[$i]["item_tax"]          = $r->item_tax;
            $items[$i]["subtotal"]          = $r->subtotal;
        }

        $msg_sunat      = $this->enviar_doc_sunat_nubefact($sale_id, $data, $items, $activar);
        $msg_           = "";
        
        //$this->fm->traza("Antes de ver msg sunat: " . $msg_sunat);

        if (strlen($msg_sunat) > 0){

            // Grabando solo la respuesta tal cual:
            $gestor_solo    = fopen("log/solo_respuesta.txt","w");
            fputs($gestor_solo,$msg_sunat);
            fclose($gestor_solo);


            $nombre_file    = "rpta_sunat" . date("Y-m-d-His") . "_" . $sale_id . ".txt";
            $gestor         = fopen("log/" . $nombre_file,"a+");
            fputs($gestor, "\n\n");

            if($this->nube_analizar_rpta_sunat($msg_sunat, $sale_id, $msg_)){

                //$this->fm->traza("Pasa nube_analizar... $msg_ ");
            
                $msg[]          = $msg_;
                
                $ar_j = json_decode($msg_sunat);
                $cas  = print_r($ar_j, true);

                fputs($gestor, $cas);
                fclose($gestor);
                return "OK";
            }else{
                fputs($gestor, $msg_);
                fclose($gestor);

                //$this->fm->traza("No pasa nube_analizar");
                return "No se pudo, por analisis";
            }
        }else{
            //$this->fm->traza("No pasa nube_analizar...");
            return "No se pudo, por vacío";
        }
    }

    function analizar_rpta_sunat($msg_sunat, $sale_id){
        //{"code":401,"message":"Expired JWT Token"}
        //$msg_sunat = 'description":"La Boleta ha sido aceptada en Sunat."';

        $msg_original = $msg_sunat;
        $pos_inicial = strrpos($msg_sunat, 'description"');

        if($pos_inicial > 0){

            $msg_sunat = substr($msg_sunat,$pos_inicial+1);

            // buscando la 3ra comilla doble
            $posx = strpos($msg_sunat,'"');
            $putx = substr($msg_sunat,$posx+1);

            $posx = strpos($putx,'"');
            $putx = $pucho = substr($putx,$posx+1);

            $posx = strpos($putx,'"');
            $putx = substr($putx,$posx+1);

            $msg_sunat = substr($pucho,0,$posx);

            // Grabando el correcto envio a Sunat:
            if(strpos($msg_sunat,"ha sido aceptada") > -1){
                $this->marcar_envio_sunat($sale_id,'1');
            }
        }else{
            $posx = strpos($msg_sunat, "message");

            $msg_sunat = "<div style=\"background-color:red;font-weight:bold\">Documento no pudo pasar a la Sunat.</div>";

            $nombre_file = "rpta_sunat" . date("Y-m-d-His") . "_" . $sale_id . ".txt";
            $gestor = fopen("log/" . $nombre_file,"a+");
            fputs($gestor,$msg_original);
            fclose($gestor);

        }

        return $msg_sunat;
    }

    function nube_analizar_rpta_sunat($msg_sunat, $sale_id, &$mensaje){
        
        $respuesta          = $msg_sunat;
        
        $leer_respuesta     = json_decode($respuesta, true);
        
        if (isset($leer_respuesta['errors'])) {
        
            $mensaje = "<div style=\"background-color:red;font-weight:bold\"> $msg_sunat </div>";
            
            return false;
        } else {
        
            $dir_comprobante    = $leer_respuesta["enlace"];
            $codigo_hash        = $leer_respuesta["codigo_hash"];

            // Grabando el correcto envio a Sunat:
            //if(strpos($leer_respuesta['aceptada_por_sunat'],"ha sido aceptada") > -1){
            if(strpos($leer_respuesta['aceptada_por_sunat'],"1") > -1){
                $this->marcar_envio_sunat($sale_id, '1', $dir_comprobante, $codigo_hash);
                return true;
            }else{
                $this->marcar_envio_sunat($sale_id, '2', $dir_comprobante, $codigo_hash);
                $mensaje = "No encuentra aceptada_por_sunat = 1,";
                $this->fm->traza($mensaje . " ; " . $msg_sunat);
                return false;
            }
        }
    }

    function nube_analizar_rpta_anulacion($msg_sunat, $sale_id, &$mensaje){
        
        $respuesta          = $msg_sunat;
        
        $leer_respuesta     = json_decode($respuesta, true);
        
        /*if (isset($leer_respuesta['errors'])) {
        
            $mensaje = $msg_sunat;
            
            return false;
        } else {*/
        
            $ticket             = $leer_respuesta["sunat_ticket_numero"];
            $enlace             = $leer_respuesta["enlace"];

            if(strlen($enlace) > 8){
                $cSql = "update tec_sales set anulado = 1, grand_total = 0, paid = 0 where id = ?";
                $this->db->query($cSql, array($sale_id));
                
                $this->db->where("sale_id",$sale_id);
                $this->db->delete("payments");

                $mensaje = "Se anula con Nro. Ticket Nubefact:" . $ticket;
                $mensaje = "Se anula el documento en Nubefact.";
                return true;
            }else{
                $mensaje = "No arroja Ticket";
                return false;
            }

        //}
    }

    function marcar_envio_sunat($sale_id, $valor='0', $dir_comprobante='', $codigo_hash=''){
        $datos = array(
                "envio_electronico" =>$valor,
                "dir_comprobante"   =>$dir_comprobante,
                "codigo_hash"       =>$codigo_hash
                );
        
        $this->db->set($datos);
        $this->db->where("id",$sale_id);
        $this->db->update("sales",$datos);

        return true;
    }

    public function enviar_doc_sunat_nubefact($sale_id, $data, $items, $activar=true){

        if($data["tipo_comprobante"] == "Boleta"){
            $cDocumento = $data["dni"];
            $el_tipo = '1'; // dni
        }else{
            $cDocumento = $data["ruc"];
            $el_tipo = '6'; // ruc
        }

        // INICIANDO EL ARRAY DE ITEMS PARA EL JSON *******************
        $total_gravada  = $total_igv = $total = 0;
        $limiteI        = count($items);
        for($i = 0; $i < $limiteI; $i++){
            $items_[] = array(
                "unidad_de_medida"          => "NIU",
                "codigo"                    => $items[$i]["product_code"],    //"001",
                "descripcion"               => $items[$i]["product_name"],    //"DETALLE DEL PRODUCTO",
                "cantidad"                  => $items[$i]["quantity"],        // Cantidad
                "valor_unitario"            => $items[$i]["net_unit_price"],  // Precio unitario sin IGV 
                "precio_unitario"           => $items[$i]["unit_price"],      // Precio unitario con IGV
                "descuento"                 => $items[$i]["discount"],        // Descuento
                "subtotal"                  => ($items[$i]["net_unit_price"] * $items[$i]["quantity"] * 1)."",
                "tipo_de_igv"               => "1",
                "igv"                       => $items[$i]["item_tax"],         // Precio_sin_IGV * Cantidad * 0.18
                "total"                     => $items[$i]["subtotal"],         // Precio_con_IGV * Cantidad
                "anticipo_regularizacion"   => "false",
                "anticipo_documento_serie"  => "",
                "anticipo_documento_numero" => ""
            );
        };

        // INICIANDO EL JSON EN SI **********************************
        $tipo   = $this->nube_tipo_de_comprobante($data["tipo_comprobante"]);  // $tipo_documento
        
        $serie = $data["serie"];

        $tipo_de_nota_de_credito            = "";
        $tipo_de_nota_de_debito             = "";

        if($tipo == '3' || $tipo == '4'){ // de credito y debito respectivamente
            $documento_que_se_modifica_tipo     = $data['tipoDocAfectado'];
            $documento_que_se_modifica_serie    = $data['serieDocfectado']; 
            $documento_que_se_modifica_numero   = $data['numDocfectado'];
            
            if($tipo == '3'){ $tipo_de_nota_de_credito            = $data['codMotivo'];}
            if($tipo == '4'){ $tipo_de_nota_de_debito             = $data['codMotivo'];}
            
        }else{
            $documento_que_se_modifica_tipo     = "";
            $documento_que_se_modifica_serie    = "";
            $documento_que_se_modifica_numero   = "";
        }

        $numero = $data["correlativo"];  // 

        $sunat_transaction           = "1"; // venta interna.
        $cliente_tipo_de_documento   = $el_tipo;  // 1 DNI, 6 RUC
        $cliente_numero_de_documento = $cDocumento;
        
        $cliente_denominacion   = $data["cliente_name"];
        $cliente_direccion      = $data["cliente_direccion"];
        $cliente_email          = $data["cliente_email"];
        $cliente_email_1        = "";
        $cliente_email_2        = "";
        $fecha_de_emision       = date("Y-m-d");
        $fecha_de_vencimiento   = date("Y-m-d");
        $moneda                 = "1"; // soles
        $tipo_de_cambio         = "";
        $porcentaje_de_igv      = $this->Igv;       // ES SOLO EL PORCENTAJE DEL IGV
        $descuento_global       = "";
        $total_descuento        = $data["total_discount"];
        $total_anticipo         = "";
        $total_gravada          = $data["total"];      // TOTAL DE LA FACTURA SIN IGV
        $total_inafecta         = "";

        $total_exonerada        = "";
        $total_igv              = $data["total_tax"];  // TOTAL IMPUESTO
        $total_gratuita         = "";
        $total_otros_cargos     = "";
        $total                  = $data["grand_total"]; // TOTAL DE LA FACTURA (INCLUYE IGV)
        $percepcion_tipo        = "";
        $percepcion_base_imponible = "";
        $total_percepcion       = "";
        $total_incluido_percepcion = "";
        $detraccion             = "false";
        $observaciones          = "";
        
        $enviar_automaticamente_a_la_sunat = "true";
        $enviar_automaticamente_al_cliente = "false";
        $codigo_unico                      = "";
        $condiciones_de_pago               = "";
        $medio_de_pago                     = "";
        $placa_vehiculo                    = "";
        $orden_compra_servicio             = "";
        $tabla_personalizada_codigo        = "";
        $formato_de_pdf                    = "";

        $respuesta = "";
        
        //require_once("paquete_json.php"); // aqui esta la variable respuesta


        $ruta = "https://api.nubefact.com/api/v1/ee047059-16bb-4595-adb6-fc5e559ee23f";

        //TOKEN para enviar documentos
        //$token = "f8011ff38c484ec4ac05a21d011d2b4b1ee2d9dca8164213a25252c32124987d";
        $token = "1ebd60ff9fcb4411b26b9cc192bb480156e131c38fcf4a18b7b3636ac1d4fdec";

        /*
        #########################################################
        #### PASO 2: GENERAR EL ARCHIVO PARA ENVIAR A NUBEFACT ####
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++
        # - MANUAL para archivo JSON en el link: https://goo.gl/WHMmSb
        # - MANUAL para archivo TXT en el link: https://goo.gl/Lz7hAq
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++
         */

        $ar_obj = array(
            "operacion"                         => "generar_comprobante",
            "tipo_de_comprobante"               => $tipo, /*"1",*/
            "serie"                             => $serie, /*"FFF1",*/
            "numero"                            => $numero, /*"1",*/
            "sunat_transaction"                 => "1",
            "cliente_tipo_de_documento"         => $cliente_tipo_de_documento,
            "cliente_numero_de_documento"       => $cliente_numero_de_documento,
            "cliente_denominacion"              => $cliente_denominacion,
            "cliente_direccion"                 => $cliente_direccion,
            "cliente_email"                     => "",
            "cliente_email_1"                   => "",
            "cliente_email_2"                   => "",
            "fecha_de_emision"                  => $fecha_de_emision,
            "fecha_de_vencimiento"              => $fecha_de_vencimiento, 
            "moneda"                            => $moneda,
            "tipo_de_cambio"                    => "",
            "porcentaje_de_igv"                 => $porcentaje_de_igv."",
            "descuento_global"                  => $total_descuento."",
            "total_descuento"                   => $total_descuento."",
            "total_anticipo"                    => "",
            "total_gravada"                     => $total_gravada,
            "total_inafecta"                    => "",
            "total_exonerada"                   => "",
            "total_igv"                         => $total_igv,
            "total_gratuita"                    => "",
            "total_otros_cargos"                => "",
            "total"                             => $total,
            "percepcion_tipo"                   => "",
            "percepcion_base_imponible"         => "",
            "total_percepcion"                  => "",
            "total_incluido_percepcion"         => "",
            "detraccion"                        => "false",
            "observaciones"                     => "",
            "documento_que_se_modifica_tipo"    => $documento_que_se_modifica_tipo,
            "documento_que_se_modifica_serie"   => $documento_que_se_modifica_serie,
            "documento_que_se_modifica_numero"  => $documento_que_se_modifica_numero,
            "tipo_de_nota_de_credito"           => $tipo_de_nota_de_credito,
            "tipo_de_nota_de_debito"            => $tipo_de_nota_de_debito,
            "enviar_automaticamente_a_la_sunat" => "true",
            "enviar_automaticamente_al_cliente" => "false",
            "codigo_unico"                      => "",
            "condiciones_de_pago"               => "",
            "medio_de_pago"                     => "",
            "placa_vehiculo"                    => "",
            "orden_compra_servicio"             => "",
            "tabla_personalizada_codigo"        => "",
            "formato_de_pdf"                    => "",
            "items" => $items_
        );

        $this->el_json = print_r($ar_obj, true) . "\n\n";  // Para colocarlo en un archivo de texto
        $data_json = json_encode($ar_obj);

        $gis       = fopen("log/antes_de" . date("Y-m-d-His") . "_" . $sale_id . ".txt","a+");
        fputs($gis, $data_json);
        fclose($gis);

        $respuesta = "";
        
        //Invocamos el servicio de NUBEFACT
        if($activar){
            /*
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: Token token="'.$token.'"',
                    'Content-Type: application/json',
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            
            
            if($respuesta === false){
                $this->fm->traza('Curl error: ' . curl_error($ch));
            }

            curl_close($ch);
            */
        }


        // poner el contenido de un fichero en una cadena
        /*
            $nombre_fichero = "log/ejemplo_aceptado.txt";
            $gestor = fopen($nombre_fichero, "r");
            $respuesta = fread($gestor, filesize($nombre_fichero));
            fclose($gestor);
            
            $g = fopen("log/" . "archivo.txt","a+");
            fputs($g,print_r($ar_obj,true));
            fputs($g, "\n\n");
            fputs($g,print_r($items_,true));
            fclose($g);
        */

        return $respuesta;
    }

    public function enviar_anulacion_nubefact($sale_id, $activar=true){

        // INICIANDO EL JSON EN SI **********************************

        $cSql = "select id, tipoDoc, serie, correlativo from tec_sales where id = ?";
        
        $query = $this->db->query($cSql,array($sale_id));

        foreach($query->result() as $r){
            $tipoDoc    = $r->tipoDoc;
            $serie      = $r->serie;
            $numero     = $r->correlativo;
            $motivo     = "ERROR DEL SISTEMA";
            $codigo_unico = "";
            //echo("tipoDoc:".$tipoDoc."\n");
        }

        $tipo="";
        if($tipoDoc == 'Factura'){ $tipo='1';}
        if($tipoDoc == 'Boleta'){ $tipo='2';}

        //die("tipo:".$tipo."\n");

        if($tipo == '1' || $tipo == '2'){
            $respuesta = "";
            
            $ruta = "https://api.nubefact.com/api/v1/ee047059-16bb-4595-adb6-fc5e559ee23f";

            //TOKEN para enviar documentos
            $token = "1ebd60ff9fcb4411b26b9cc192bb480156e131c38fcf4a18b7b3636ac1d4fdec";

            /*
            #########################################################
            #### PASO 2: GENERAR EL ARCHIVO PARA ENVIAR A NUBEFACT ####
            +++++++++++++++++++++++++++++++++++++++++++++++++++++++
            # - MANUAL para archivo JSON en el link: https://goo.gl/WHMmSb
            # - MANUAL para archivo TXT en el link: https://goo.gl/Lz7hAq
            +++++++++++++++++++++++++++++++++++++++++++++++++++++++
             */

            $ar_obj = array(
                "operacion"                         => "generar_anulacion",
                "tipo_de_comprobante"               => $tipo, 
                "serie"                             => $serie, 
                "numero"                            => $numero, 
                "motivo"                            => $motivo,
                "codigo_unico"                      => $codigo_unico
            );

            $this->el_json = print_r($ar_obj, true) . "\n\n";  // Para colocarlo en un archivo de texto
            $data_json = json_encode($ar_obj);

            $gis       = fopen("log/antes_de" . date("Y-m-d-His") . "_" . $sale_id . "_anulacion.txt","a+");
            fputs($gis, $data_json);
            fclose($gis);

            $respuesta = "";
            
            //Invocamos el servicio de NUBEFACT
            if($activar){
                /*
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta);
                curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, array(
                        'Authorization: Token token="'.$token.'"',
                        'Content-Type: application/json',
                    )
                );
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
                
                
                if($respuesta === false){
                    $this->fm->traza('Curl error: ' . curl_error($ch));
                }

                curl_close($ch);
                */
            }
            
            
            $respuesta = '{
                "numero": 1,
                "enlace": "https://www.nubefact.com/anulacion/b7fc0c001-b31a",
                "sunat_ticket_numero": "1494358661332",
                "aceptada_por_sunat": false,
                "sunat_description": null,
                "sunat_note": null,
                "sunat_responsecode": null,
                "sunat_soap_error": "",
                "enlace_del_pdf":
                "https://www.nubefact.com/anulacion/b7fc0c001-b31a.pdf",
                "enlace_del_xml":
                "https://www.nubefact.com/anulacion/b7fc0c001-b31a.xml",
                "enlace_del_cdr":
                "https://www.nubefact.com/anulacion/b7fc0c001-b31a.cdr"
                }';
            

            $gn  = fopen("log/anulacion_" . date("Y-m-d-His") . "_" . $sale_id . ".txt","a+");
            fputs($gn, $respuesta);
            fclose($gn);
            $gn = null;

            $un_msg = "";
            $rpta2 = $this->nube_analizar_rpta_anulacion($respuesta, $sale_id, $un_msg);

            return $un_msg;
        }

    }

    public function nube_tipo_de_comprobante($tipo){
        // 1 = FACTURA  2 = BOLETA  3 = NOTA DE CRÉDITO  4 = NOTA DE DÉBITO
        if($tipo == 'Boleta'){ // Boleta
            return '2';
        }elseif($tipo == 'Factura'){ // Factura
            return '1';
        }elseif($tipo == 'Nota_de_credito'){
            return '3';
        }elseif($tipo == 'Nota_de_debito'){
            return '4';
        }else{
            return '99';
        }
    }

    public function nube_serie($tipo, $tipoDocAfectado="", $tienda=0){

        //die("Tienda:".gettype($tienda). " - ".$tienda);

        if($tienda * 1 == 2){
            if($tipo == "Boleta"){
                return "BBB1"; // BOLETA DE VENTA B003 - 00002471 08/11/2021 
            }elseif($tipo == "Factura"){
                return "FFF1"; // Nº FACTURA F002 - 00000022 07/11/2021
            }elseif($tipo == 'Ticket'){
                return "TK1";
            }else{
                if(strlen($tipoDocAfectado) > 0){
                    if($tipoDocAfectado == '1'){ return "FFF1"; } // FFF1
                    if($tipoDocAfectado == '2'){ return "BBB1"; } // BBB1
                }                
            }
        }

        if($tienda * 1 == 3){
            if($tipo == "Boleta"){
                return "BBB2"; // BOLETA DE VENTA B003 - 00002471 08/11/2021 
            }elseif($tipo == "Factura"){
                return "FFF2"; // Nº FACTURA F002 - 00000022 07/11/2021
            }elseif($tipo == 'Ticket'){
                return "TK2";
            }else{
                if(strlen($tipoDocAfectado) > 0){
                    if($tipoDocAfectado == '1'){ return "FFF2"; } // FFF1
                    if($tipoDocAfectado == '2'){ return "BBB2"; } // BBB1
                }                
            }
        }

        if($tienda * 1 == 1){
            if($tipo == "Boleta"){
                return "BBB3"; // BOLETA DE VENTA B003 - 00002471 08/11/2021 
            }elseif($tipo == "Factura"){
                return "FFF3"; // Nº FACTURA F002 - 00000022 07/11/2021
            }elseif($tipo == 'Ticket'){
                return "TK3";
            }else{
                if(strlen($tipoDocAfectado) > 0){
                    if($tipoDocAfectado == '1'){ return "FFF3"; } // FFF1
                    if($tipoDocAfectado == '2'){ return "BBB3"; } // BBB1
                }                
            }
        }

    }

    public function nube_consultas($tipo_comprobante, $serie, $numero){
        $ar = array(
            "operacion"             => "consultar_comprobante",
            "tipo_de_comprobante"   => $tipo_comprobante,
            "serie"                 => $serie,
            "numero"                => $numero
        );
        return json_encode($ar);
    }

    function stripe($amount = 0, $card_info = array(), $desc = '') {
        $this->load->model('stripe_payments');
        // $card_info = array( "number" => "4242424242424242", "exp_month" => 1, "exp_year" => 2016, "cvc" => "314" );
        // $amount = $amount ? $amount*100 : 3000;
        $amount = $amount * 100;
        if ($amount && !empty($card_info)) {
            $token_info = $this->stripe_payments->create_card_token($card_info);
            if (!isset($token_info['error'])) {
                $token = $token_info->id;
                $data = $this->stripe_payments->insert($token, $desc, $amount, $this->Settings->currency_prefix);
                if (!isset($data['error'])) {
                    $result = array('transaction_id' => $data->id,
                        'created_at' => date('Y-m-d H:i:s', $data->created),
                        'amount' => ($data->amount / 100),
                        'currency' => strtoupper($data->currency)
                    );
                    return $result;
                } else {
                    return $data;
                }
            } else {
                return $token_info;
            }
        }
        return false;
    }

    public function updateSale($id, $data, $items) {
        $osale = $this->getSaleByID($id);
        $oitems = $this->getAllSaleItems($id);
        foreach ($oitems as $oitem) {
            $product = $this->site->getProductByID($oitem->product_id, $osale->store_id);
            if ($product->type == 'standard') {
                $this->db->update('product_store_qty', array('quantity' => ($product->quantity+$oitem->quantity)), array('product_id' => $product->id, 'store_id' => $osale->store_id));
            } elseif ($product->type == 'combo') {
                $combo_items = $this->getComboItemsByPID($product->id);
                foreach ($combo_items as $combo_item) {
                    $cpr = $this->site->getProductByID($combo_item->id, $osale->store_id);
                    if($cpr->type == 'standard') {
                        $qty = $combo_item->qty * $oitem->quantity;
                        $this->db->update('product_store_qty', array('quantity' => ($cpr->quantity+$qty)), array('product_id' => $cpr->id, 'store_id' => $osale->store_id));
                    }
                }
            }
        }

        $data['status'] = $osale->paid > 0 ? 'partial' : ($data['grand_total'] <= $osale->paid ? 'paid' : 'due');

        if($this->db->update('sales', $data, array('id' => $id)) && $this->db->delete('sale_items', array('sale_id' => $id))) {

            foreach ($items as $item) {
                $item['sale_id'] = $id;
                if($this->db->insert('sale_items', $item)) {
                    $product = $this->site->getProductByID($item['product_id'], $osale->store_id);
                    if ($product->type == 'standard') {
                        $this->db->update('product_store_qty', array('quantity' => ($product->quantity-$item['quantity'])), array('product_id' => $product->id, 'store_id' => $osale->store_id));
                    } elseif ($product->type == 'combo') {
                        $combo_items = $this->getComboItemsByPID($product->id);
                        foreach ($combo_items as $combo_item) {
                            $cpr = $this->site->getProductByID($combo_item->id, $osale->store_id);
                            if($cpr->type == 'standard') {
                                $qty = $combo_item->qty * $item['quantity'];
                                $this->db->update('product_store_qty', array('quantity' => ($cpr->quantity-$qty)), array('product_id' => $cpr->id, 'store_id' => $osale->store_id));
                            }
                        }
                    }
                }
            }

            return TRUE;
            }

        return false;
    }

    public function suspendSale($data, $items, $did = NULL) {

        if($did) {
            if($this->db->update('suspended_sales', $data, array('id' => $did)) && $this->db->delete('suspended_items', array('suspend_id' => $did))) {
                foreach ($items as $item) {
                    unset($item['cost']);
                    $item['suspend_id'] = $did;
                    $this->db->insert('suspended_items', $item);
                }
                return TRUE;
            }

        } else {
            if($this->db->insert('suspended_sales', $data)) {
                
                $suspend_id = $this->db->insert_id();
                foreach ($items as $item) {
                    unset($item['cost']);
                    $item['suspend_id'] = $suspend_id;
                    $this->db->insert('suspended_items', $item);
                }
                return $suspend_id;
            }
        }
        return false;
    }

    public function getDatos($sale_id,$var){
        
        $result = $this->db
            ->select("sales.tipoDoc, customers.cf1, customers.cf2")
            ->from('sales')
            ->join('customers','sales.customer_id = customers.id')
            ->where('sales.id', $sale_id)
            ->get()->result_array();
        
        foreach($result as $r){
            if ($var == 'tipoDoc'){
                return $r["tipoDoc"];
            }elseif($var == 'cf1'){
                return $r['cf1'];
            }elseif($var == 'cf2'){
                return $r['cf2'];
            }
        }
        return false;
    }

    public function getSaleByID($sale_id) {
        $q = $this->db->get_where('sales', array('id' => $sale_id), 1);
          if( $q->num_rows() > 0 ) {
            return $q->row();
          }
          return FALSE;
    }

    public function getAllSaleItems($sale_id) {
        $j = "(SELECT id, code, name, tax_method from {$this->db->dbprefix('products')}) P";
        $this->db->select("sale_items.*,
            (CASE WHEN {$this->db->dbprefix('sale_items')}.product_code IS NULL THEN {$this->db->dbprefix('products')}.code ELSE {$this->db->dbprefix('sale_items')}.product_code END) as product_code,
            (CASE WHEN {$this->db->dbprefix('sale_items')}.product_name IS NULL THEN {$this->db->dbprefix('products')}.name ELSE {$this->db->dbprefix('sale_items')}.product_name END) as product_name,
            {$this->db->dbprefix('products')}.tax_method as tax_method", FALSE)
        ->join('products', 'products.id=sale_items.product_id', 'left outer')
        ->order_by('sale_items.id');
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllSalePayments($sale_id) {
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSuspendedSaleByID($id) {
        $q = $this->db->get_where('suspended_sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSuspendedSaleItems($id) {
        $q = $this->db->get_where('suspended_items', array('suspend_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSuspendedSales($user_id = NULL) {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->db->order_by('date', 'desc');
        $q = $this->db->get_where('suspended_sales', array('created_by' => $user_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getGiftCardByNO($no) {
        $q = $this->db->get_where('gift_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getComboItemsByPID($product_id) {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('combo_items') . '.quantity as qty, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('products') . '.quantity as quantity')
        ->join('products', 'products.code=combo_items.item_code', 'left')
        ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

//    public function enviar_doc_sunat_nubefact_masivo($dia=date('Y-m-d')){
//        return "Pruebas...";
//    }


    public function Igv(){
        return $this->Igv;
    }


/*    public function correlativo($tipoDoc="Boleta"){
        
        $query = $this->db->select_max('correlativo')->where("tipoDoc",$tipoDoc)->where("tipoDoc is not null")->get('sales');
        //echo($this->db->select_max('correlativo')->where("tipoDoc",$tipoDoc)->where("tipoDoc is not null")->get_compiled_select('sales'));
        
        $maximo = 0;
        foreach($query->result() as $r){
            //$maximo = (is_null($r->correlativo) ? 1 : $r->correlativo*1);
            if(is_null($r->correlativo)){
                $maximo = 1;
            }else{
                $maximo = $r->correlativo * 1;
            }
        }
        if(is_null($maximo)){
            $maximo = 1;
        }else{
            $maximo = $maximo + 1;
        }
        return $maximo;
    }*/

    public function notas_varias($sale_id){
        // Subvariables aun por definir:
        $tip_forma  = "Contado";
        $numDoc     = ""; // normalmente es el dni del cliente, pero en caso de empresa, no se
        $icbper     = 0;
        $porcentajeIgv = $this->Igv;

        $query = $this->db->query("select a.id, a.date, a.customer_id, a.customer_name, a.total, a.status, a.tipoDoc, a.grand_total, a.correlativo, 
            a.serie, a.tipoDocAfectado, a.numDocfectado, a.guia_nroDoc, a.codMotivo, a.desMotivo,
            c.cf1, c.cf2, c.direccion,
            b.id id_items, 
            b.product_id, 
            b.product_name,
            b.quantity,
            b.net_unit_price,
            b.tax,
            b.real_unit_price,
            b.subtotal,
            d.codProdSunat 
            from tec_sales a
            inner join tec_sale_items b on a.id = b.sale_id
            inner join tec_customers c on a.customer_id = c.id
            inner join tec_products d on b.product_id = d.id
            where a.id = $sale_id");

        foreach($query->result() as $r){
            $total          = $r->total;
            $tax            = $r->tax;
            $Cliente        = $r->customer_name;
            $codProdSunat   = $r->codProdSunat;
            $serie          = $r->serie;
            $tipoDocAfectado = $r->tipoDocAfectado;
            $numDocfectado  = $r->numDocfectado;
            $guia_nroDoc    = $r->guia_nroDoc;
            $codMotivo      = $r->codMotivo;
            $desMotivo      = $r->desMotivo;

            if($r->tipoDoc == 'Boleta'){
                $numDoc     = $r->cf1;
                $tipoDocClient = "1"; // DNI
            }elseif($r->tipoDoc == 'Factura'){
                $numDoc     = $r->cf2;
                $tipoDocClient = "6"; // RUC
            }elseif($r->tipoDoc == 'Nota_de_credito'){
                $numDoc     = $r->cf2;
                $tipoDocClient = "6";    // RUC
                $tipoDoc        = '07';  //NOTA DE CREDITO
            }elseif($r->tipoDoc == 'Nota_de_debito'){
                $numDoc     = $r->cf2;
                $tipoDocClient = "6";   // RUC
                $tipoDoc        = '08';  //NOTA DE DEBITO
            }
            
            $fecha_emi      = substr($r->date,0,10) . "T" . substr($r->date,11);
            $Cliente        = $r->customer_name; // Razon Social
            $direccion_cliente = (is_null($r->direccion) ? "sin direccion" : $r->direccion);
            $correlativo    = $r->correlativo;
        }
        
        $nTotal             = $total * (1 + ($tax/100)) * 1;
        $nTotal             = round($nTotal,2);

        // Variables segun la API:
        
        $mtoOperGravadas    = round($total, 2); //200.2       
        //$mtoIGV           = round($total*$product_tax/100, 2); //36.04
        $mtoIGV             = round($total * $porcentajeIgv / 100,2);
        $icbper             = round($icbper * 1, 2); //0.8
        $valorVenta         = round($total, 2); //200.2
        $totalImpuestos     = $mtoIGV; // 36.84
        
        $subTotal           = $nTotal; // 237.04
        $redondeo           = 0; // 0.04
        $mtoImpVenta        = $nTotal; // 237

        $campos = "{
          \"tipDocAfectado\": \"$tipoDocAfectado\",
          \"numDocfectado\": \"$numDocfectado\",
          \"codMotivo\": \"$codMotivo\",
          \"desMotivo\": \"$desMotivo\",  
          \"tipoDoc\": \"$tipoDoc\",
          \"serie\": \"$serie\",
          \"fechaEmision\": \"" . $fecha_emi . "-05:00\",
          \"correlativo\": \"$correlativo\",
          \"tipoMoneda\": \"PEN\",";
          
        if ($tipoDoc == '07'){
            $campos .= "\"guias\": [
                    {
                        \"tipoDoc\": \"09\",
                        \"nroDoc\": \"$guia_nroDoc\"
                    }
                ],";
        }

        $campos .="\"client\": {
            \"tipoDoc\": \"$tipoDocClient\",
            \"numDoc\": $numDoc,
            \"rznSocial\": \"$Cliente\",
            \"address\": {
              \"direccion\": \"$direccion_cliente\",
              \"provincia\": \"".$this->COMPANY_PROV."\",
              \"departamento\": \"".$this->COMPANY_DPTO."\",
              \"distrito\": \"".$this->COMPANY_DISTRITO."\",
              \"ubigueo\": \"".$this->COMPANY_UBIGEO."\"
            }
        },";

        $campos .= "\"company\": {
            \"ruc\": ".$this->COMPANY_RUC.",
            \"razonSocial\": \"".$this->COMPANY_RAZON_SOCIAL."\",
            \"address\": {
              \"direccion\": \"".$this->COMPANY_DIRECCION."\",
              \"provincia\": \"".$this->COMPANY_PROV."\",
              \"departamento\": \"".$this->COMPANY_DPTO."\",
              \"distrito\": \"".$this->COMPANY_DISTRITO."\",
              \"ubigueo\": \"".$this->COMPANY_UBIGEO."\"
            }
          },";

        $campos .= "\"mtoOperGravadas\": $mtoOperGravadas,
          \"mtoIGV\": $mtoIGV,
          \"totalImpuestos\": $totalImpuestos,
          \"mtoImpVenta\": $mtoImpVenta,
          \"ublVersion\": \"2.1\",
          \"details\": [";
           
        //  \"icbper\": $icbper,
        //  \"valorVenta\": $valorVenta,
        //  \"subTotal\": $subTotal,
        //  \"redondeo\": $redondeo,
        
        foreach ($query->result() as $r){
            
            $codProducto        = "P" . $r->product_id; //$r->codProdSunat;
            //$unidad             = $items[0];
            $descripcion        = $r->product_name;
            $cantidad           = round($r->quantity,0);
            $mtoValorUnitario   = round($r->net_unit_price,2)*1;
            $mtoValorVenta      = round($r->net_unit_price * $cantidad * 1,2);
            $mtoBaseIgv         = round($cantidad * $mtoValorUnitario,2);
            $porcentajeIgv      = $r->tax*1;
            $igv                = round($mtoBaseIgv * ($porcentajeIgv/100),2); // round($r->subtotal - round($r->net_unit_price,2),2);
            $tipAfeIgv          = 10;
            $totalImpuestos     = $igv;
            
            $igvX               = 1 + ($porcentajeIgv/100);
            $mtoPrecioUnitario  = $this->fm->floor_dec($r->net_unit_price * $igvX, 2);           

            $campos .= "{
              \"codProducto\": \"$codProducto\",
              \"unidad\": \"NIU\",
              \"descripcion\": \"$descripcion\",
              \"cantidad\": $cantidad,
              \"mtoValorUnitario\": $mtoValorUnitario,
              \"mtoValorVenta\": $mtoValorVenta,
              \"mtoBaseIgv\": $mtoBaseIgv,
              \"porcentajeIgv\": $porcentajeIgv,
              \"igv\": $igv,
              \"tipAfeIgv\": $tipAfeIgv,
              \"totalImpuestos\": $totalImpuestos,
              \"mtoPrecioUnitario\": $mtoPrecioUnitario
            },";
        }    
        
        // Se asume que si o si hay items, por tanto se quita la ultima coma:
        $campos = substr($campos,0,strlen($campos)-1);

        $cValor         = $mtoImpVenta . "";
        $pos            = strpos($cValor, ".");
        $valor_entero   = substr($cValor,0,$pos);
        $valor_dec      = substr($cValor,$pos+1);

        $valor_dec = substr($valor_dec . "00",0,2);

        $en_letras =  "Son " . $this->fm->convertir($valor_entero) . " y $valor_dec/100 Soles";

        $campos .= "],
          \"legends\": [
            {
              \"code\": \"1000\",
              \"value\": \"$en_letras\"
            }
          ]
        }";

        return $campos;
    }

    public function obtener_sale_id($series){
        $serie          = substr($series,0,4);
        $correlativo    = substr($series,4);
        $query          = $this->db->select("id")->where("correlativo",$correlativo)->where("serie",$serie)->get("sales");

        //$sentencia = $this->db->select("id")->where("correlativo",$correlativo)->where("serie",$serie)
        //    ->get_compiled_select("sales"); echo $sentencia;

        foreach($query->result() as $r){
            $sale_id = $r->id;
        }
        return $sale_id;
    }

    public function total_final_cash_anterior($tienda){
        
        // Averiguando el ultimo inicio o cierre de caja.
        $cSql = "select status, date(date) fecha, cash_in_hand, cash_in_hand_adicional, monto_final_cash as totales 
            from tec_registers a 
            where date(a.date) < curdate() and store_id = ?
            order by a.date desc limit 1";

        $query = $this->db->query($cSql,array($tienda));
        foreach($query->result() as $r){
            $fec_ini = $fec_fin = $r->fecha;
        }
        
        if(isset($fec_ini)){
            //die("datos: $tienda, $fec_ini, $fec_fin");
            $cSql = $this->fm->query_salidas_por_dia($tienda, $fec_ini, $fec_fin);
            //echo "<br><br><br>".$cSql;
            $query = $this->db->query($cSql);

            $retorno = 0;
            foreach($query->result() as $r){
                
                $retorno = $r->caja_final_efectivo;
                //die("retorno: {$retorno}");
            }
        }else{
            $retorno = 0;
        }
        
        return $retorno;
    }

    public function cash($tienda){
        $cSql = "select status, date, cash_in_hand, cash_in_hand_adicional, cash_in_hand + cash_in_hand_adicional as totales from tec_registers a 
            where store_id = {$tienda}
            order by a.date desc limit 1";

        $query = $this->db->query($cSql);

        /*
        $retorno = 0;
        foreach($query->result() as $r){
            $retorno = $r->cash_in_hand;
        }
        return $retorno;
        */

        return $query->result();
    }

    public function apertura_caja_hoy($tienda){
        $cSql = "select status from tec_registers where store_id = ? and date(date) = ?";

        //echo $cSql;

        $query = $this->db->query($cSql, array($tienda,date("Y-m-d")));

        $bandera = false;
        foreach($query->result() as $r){
            $bandera = true;
        }
        //die("Bandera:" . $bandera);
        return $bandera;
    }

    public function anular_documento_simple($id){
        // por ejemplo anular un Ticket
        $cSql = "update tec_sales set anulado = 1, grand_total = 0, paid = 0 where id = ?";
        $this->db->query($cSql, array($id));
        
        $this->db->where("sale_id",$id);
        $this->db->delete("payments");

        //$mensaje = "Se anula Documento simple con ID:{$id}";
        return true;
    }
}
