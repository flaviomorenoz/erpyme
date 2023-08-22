<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller
{

    function __construct() {
        parent::__construct();


        if ( ! $this->loggedIn) {
            redirect('login');
        }

        $this->load->model('reports_model');
    }

    function daily_sales($year = NULL, $month = NULL) {
        if (!$year) { $year = date('Y'); }
        if (!$month) { $month = date('m'); }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->lang->load('calendar');
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => site_url('reports/daily_sales'),
            'month_type' => 'long',
            'day_type' => 'long'
            );
        $config['template'] = '

        {table_open}<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered table-calendar" style="min-width:522px;">{/table_open}

        {heading_row_start}<tr class="active">{/heading_row_start}

        {heading_previous_cell}<th><div class="text-center"><a href="{previous_url}">&lt;&lt;</div></a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}"><div class="text-center">{heading}</div></th>{/heading_title_cell}
        {heading_next_cell}<th><div class="text-center"><a href="{next_url}">&gt;&gt;</a></div></th>{/heading_next_cell}

        {heading_row_end}</tr>{/heading_row_end}

        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_equal"><div class="cl_wday">{week_day}</div></td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}

        {cal_row_start}<tr>{/cal_row_start}
        {cal_cell_start}<td>{/cal_cell_start}

        {cal_cell_content}{day}<br>{content}{/cal_cell_content}
        {cal_cell_content_today}<div class="highlight">{day}</div>{content}{/cal_cell_content_today}

        {cal_cell_no_content}{day}{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="highlight">{day}</div>{/cal_cell_no_content_today}

        {cal_cell_blank}&nbsp;{/cal_cell_blank}

        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}

        {table_close}</table>{/table_close}
        ';

        $this->load->library('calendar', $config);

        $sales = $this->reports_model->getDailySales($year, $month);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $sale->date = intval($sale->date);
                $daily_sale[$sale->date] = "<table class='table table-condensed table-striped' style='margin-bottom:0;'><tr><td>".lang('total').
                "</td><td style='text-align:right;'>{$this->tec->formatMoney($sale->total)}</td></tr><tr><td><span style='font-weight:normal;'>".lang('product_tax')."<br>".lang('order_tax')."</span><br>".lang('tax').
                "</td><td style='text-align:right;'><span style='font-weight:normal;'>{$this->tec->formatMoney($sale->product_tax)}<br>{$this->tec->formatMoney($sale->order_tax)}</span><br>{$this->tec->formatMoney($sale->total_tax)}</td></tr><tr><td class='violet'>".lang('discount').
                "</td><td style='text-align:right;'>{$this->tec->formatMoney($sale->discount)}</td></tr><tr><td class='violet'>".lang('grand_total').
                "</td><td style='text-align:right;' class='violet'>{$this->tec->formatMoney($sale->grand_total)}</td></tr><tr><td class='green'>".lang('paid').
                "</td><td style='text-align:right;' class='green'>{$this->tec->formatMoney($sale->paid)}</td></tr><tr><td class='orange'>".lang('balance').
                "</td><td style='text-align:right;' class='orange'>{$this->tec->formatMoney($sale->grand_total - $sale->paid)}</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['calender'] = ""; //$this->calendar->generate($year, $month, $daily_sale);

        $start = $year.'-'.$month.'-01 00:00:00';
        $end = $year.'-'.$month.'-'.days_in_month($month, $year).' 23:59:59';
        $this->data['total_purchases']  = $this->reports_model->getTotalPurchases($start, $end);
        $this->data['total_sales']      = $this->reports_model->getTotalSales($start, $end);
        $this->data['total_expenses']   = $this->reports_model->getTotalExpenses($start, $end);

        $this->data['page_title'] = $this->lang->line("daily_sales");
        $bc = array(array('link' => '#', 'page' => lang('reports')), array('link' => '#', 'page' => lang('daily_sales')));
        $meta = array('page_title' => lang('daily_sales'), 'bc' => $bc);
        $this->page_construct('reports/daily', $this->data, $meta);
    }


    function monthly_sales($year = NULL) {
        if(!$year) { $year = date('Y'); }
        $this->load->language('calendar');
        $this->lang->load('calendar');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $start = $year.'-01-01 00:00:00';
        $end = $year.'-12-31 23:59:59';
        $this->data['total_purchases'] = $this->reports_model->getTotalPurchases($start, $end);
        $this->data['total_sales'] = $this->reports_model->getTotalSales($start, $end);
        $this->data['total_expenses'] = $this->reports_model->getTotalExpenses($start, $end);
        $this->data['year'] = $year;
        $this->data['sales'] = $this->reports_model->getMonthlySales($year);
        $this->data['page_title'] = $this->lang->line("monthly_sales");
        $bc = array(array('link' => '#', 'page' => lang('reports')), array('link' => '#', 'page' => lang('monthly_sales')));
        $meta = array('page_title' => lang('monthly_sales'), 'bc' => $bc);
        $this->page_construct('reports/monthly', $this->data, $meta);
    }

    function index() {
        if ($this->input->post('customer')) {
            $start_date     = $this->input->post('start_date') ? $this->input->post('start_date') : NULL;
            $end_date       = $this->input->post('end_date') ? $this->input->post('end_date') : NULL;
            $user           = $this->input->post('user') ? $this->input->post('user') : NULL;
            $this->data['total_sales'] = $this->reports_model->getTotalCustomerSales($this->input->post('customer'), $user, $start_date, $end_date);
        }
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['customers']    = $this->reports_model->getAllCustomers();
        $this->data['users']        = $this->reports_model->getAllStaff();
        $this->data['page_title']   = $this->lang->line("sales_report");
        $bc = array(array('link' => '#', 'page' => lang('reports')), array('link' => '#', 'page' => lang('sales_report')));
        $meta = array('page_title' => lang('sales_report'), 'bc' => $bc);
        $this->page_construct('reports/sales', $this->data, $meta);
    }

    function get_sales() {
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;

        $this->load->library('datatables');
        $this->datatables
        ->select("id, date, customer_name, total, total_tax, total_discount, grand_total, paid, (grand_total-paid) as balance, status")
        ->from('sales');
        if ($this->session->userdata('store_id')) {
            $this->datatables->where('store_id', $this->session->userdata('store_id'));
        }
        $this->datatables->unset_column('id');
        if($customer) { $this->datatables->where('customer_id', $customer); }
        if($user) { $this->datatables->where('created_by', $user); }
        if($start_date) { $this->datatables->where('date >=', $start_date); }
        if($end_date) { $this->datatables->where('date <=', $end_date); }

        echo $this->datatables->generate();
    }

    function products() {
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['products'] = $this->reports_model->getAllProducts();
        $this->data['page_title'] = $this->lang->line("products_report");
        $this->data['page_title'] = $this->lang->line("products_report");
        $bc = array(array('link' => '#', 'page' => lang('reports')), array('link' => '#', 'page' => lang('products_report')));
        $meta = array('page_title' => lang('products_report'), 'bc' => $bc);
        $this->page_construct('reports/products', $this->data, $meta);
    }

    function get_products() {
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        //COALESCE(sum(".$this->db->dbprefix('sale_items').".quantity)*".$this->db->dbprefix('products').".cost, 0) as cost,
        $this->load->library('datatables');
        $this->datatables
        ->select($this->db->dbprefix('products').".id as id, ".$this->db->dbprefix('products').".name, ".$this->db->dbprefix('products').".code, COALESCE(sum(".$this->db->dbprefix('sale_items').".quantity), 0) as sold, ROUND(COALESCE(((sum(".$this->db->dbprefix('sale_items').".subtotal)*".$this->db->dbprefix('products').".tax)/100), 0), 2) as tax, COALESCE(sum(".$this->db->dbprefix('sale_items').".quantity)*".$this->db->dbprefix('sale_items').".cost, 0) as cost, COALESCE(sum(".$this->db->dbprefix('sale_items').".subtotal), 0) as income, ROUND((COALESCE(sum(".$this->db->dbprefix('sale_items').".subtotal), 0)) - COALESCE(sum(".$this->db->dbprefix('sale_items').".quantity)*".$this->db->dbprefix('sale_items').".cost, 0) -COALESCE(((sum(".$this->db->dbprefix('sale_items').".subtotal)*".$this->db->dbprefix('products').".tax)/100), 0), 2)
            as profit", FALSE)
        ->from('sale_items')
        ->join('products', 'sale_items.product_id=products.id', 'left')
        ->join('sales', 'sale_items.sale_id=sales.id', 'left');
        if ($this->session->userdata('store_id')) {
            $this->datatables->where('sales.store_id', $this->session->userdata('store_id'));
        }
        $this->datatables->group_by('products.id');

        if($product) { $this->datatables->where('products.id', $product); }
        if($start_date) { $this->datatables->where('date >=', $start_date); }
        if($end_date) { $this->datatables->where('date <=', $end_date); }
        echo $this->datatables->generate();
    }

    function profit( $income, $cost, $tax) {
        return floatval($income)." - ".floatval($cost)." - ".floatval($tax);
    }

    function top_products() {
        $this->data['topProducts'] = $this->reports_model->topProducts();
        $this->data['topProducts1'] = $this->reports_model->topProducts1();
        $this->data['topProducts3'] = $this->reports_model->topProducts3();
        $this->data['topProducts12'] = $this->reports_model->topProducts12();
        $this->data['page_title'] = $this->lang->line("top_products");
        $bc = array(array('link' => '#', 'page' => lang('reports')), array('link' => '#', 'page' => lang('top_products')));
        $meta = array('page_title' => lang('top_products'), 'bc' => $bc);
        $this->page_construct('reports/top', $this->data, $meta);
    }

    function registers() {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getAllStaff();
        $bc = array(array('link' => '#', 'page' => lang('reports')), array('link' => '#', 'page' => lang('registers_report')));
        $meta = array('page_title' => lang('registers_report'), 'bc' => $bc);
        $this->page_construct('reports/registers', $this->data, $meta);
    }

    function get_register_logs() {
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $this->load->library('datatables');
        if ($this->db->dbdriver == 'sqlite3') {
            $this->datatables->select("{$this->db->dbprefix('registers')}.id as id, date, closed_at, ({$this->db->dbprefix('users')}.first_name || ' ' || {$this->db->dbprefix('users')}.last_name || '<br>' || {$this->db->dbprefix('users')}.email) as user, cash_in_hand, (total_cc_slips || ' (' || total_cc_slips_submitted || ')') as cc_slips, (total_cheques || ' (' || total_cheques_submitted || ')') as total_cheques, (total_cash || ' (' || total_cash_submitted || ')') as total_cash, note", FALSE);
        } else {
            $this->datatables->select("{$this->db->dbprefix('registers')}.id as id, date, closed_at, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, '<br>', " . $this->db->dbprefix('users') . ".email) as user, cash_in_hand, CONCAT(total_cc_slips, ' (', total_cc_slips_submitted, ')') as cc_slips, CONCAT(total_cheques, ' (', total_cheques_submitted, ')') as total_cheques, CONCAT(total_cash, ' (', total_cash_submitted, ')') as total_cash, note", FALSE);
        }
        $this->datatables->from("registers")
        ->join('users', 'users.id=registers.user_id', 'left');

        if ($user) {
            $this->datatables->where('registers.user_id', $user);
        }
        if ($start_date) {
            $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }
        if ($this->session->userdata('store_id')) {
            $this->datatables->where('registers.store_id', $this->session->userdata('store_id'));
        }

        echo $this->datatables->generate();


    }

    function payments() {
        if ($this->input->post('customer')) {
            $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : NULL;
            $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : NULL;
            $user = $this->input->post('user') ? $this->input->post('user') : NULL;
            $this->data['total_sales'] = $this->reports_model->getTotalCustomerSales($this->input->post('customer'), $user, $start_date, $end_date);
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getAllStaff();
        $this->data['customers'] = $this->reports_model->getAllCustomers();
        $bc = array(array('link' => '#', 'page' => lang('reports')), array('link' => '#', 'page' => lang('payments_report')));
        $meta = array('page_title' => lang('payments_report'), 'bc' => $bc);
        $this->page_construct('reports/payments', $this->data, $meta);
    }

    function get_payments() {
        $user = $this->input->get('user') ? $this->input->get('user') : NULL;
        $ref = $this->input->get('payment_ref') ? $this->input->get('payment_ref') : NULL;
        $sale_id = $this->input->get('sale_no') ? $this->input->get('sale_no') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $paid_by = $this->input->get('paid_by') ? $this->input->get('paid_by') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $this->load->library('datatables');
        $this->datatables
        ->select("{$this->db->dbprefix('payments')}.id as id, {$this->db->dbprefix('payments')}.date, {$this->db->dbprefix('payments')}.reference as ref, {$this->db->dbprefix('sales')}.id as sale_no, paid_by, amount")
        ->from('payments')
        ->join('sales', 'payments.sale_id=sales.id', 'left')
        ->group_by('payments.id');

        if ($this->session->userdata('store_id')) {
            $this->datatables->where('payments.store_id', $this->session->userdata('store_id'));
        }
        if ($user) {
            $this->datatables->where('payments.created_by', $user);
        }
        if ($ref) {
            $this->datatables->where('payments.reference', $ref);
        }
        if ($paid_by) {
            $this->datatables->where('payments.paid_by', $paid_by);
        }
        if ($sale_id) {
            $this->datatables->where('sales.id', $sale_id);
        }
        if ($customer) {
            $this->datatables->where('sales.customer_id', $customer);
        }
        if ($start_date) {
            $this->datatables->where($this->db->dbprefix('payments').'.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        echo $this->datatables->generate();

    }

    function alerts() {
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('stock_alert');
        $bc = array(array('link' => '#', 'page' => lang('stock_alert')));
        $meta = array('page_title' => lang('stock_alert'), 'bc' => $bc);
        $this->page_construct('reports/alerts', $this->data, $meta);

    }

    function get_alerts() {
        $this->load->library('datatables');
        $this->datatables->select($this->db->dbprefix('products').".id as id, ".$this->db->dbprefix('products').".image as image, ".$this->db->dbprefix('products').".code as code, ".$this->db->dbprefix('products').".name as pname, type, ".$this->db->dbprefix('categories').".name as cname, (CASE WHEN psq.quantity IS NULL THEN 0 ELSE psq.quantity END) as quantity, alert_quantity, tax, tax_method, cost, (CASE WHEN psq.price > 0 THEN psq.price ELSE {$this->db->dbprefix('products')}.price END) as price", FALSE)
        ->from('products')
        ->join('categories', 'categories.id=products.category_id')
        ->join("( SELECT * from {$this->db->dbprefix('product_store_qty')} WHERE store_id = {$this->session->userdata('store_id')}) psq", 'products.id=psq.product_id', 'left')
        ->where("(CASE WHEN psq.quantity IS NULL THEN 0 ELSE psq.quantity END) < {$this->db->dbprefix('products')}.alert_quantity", NULL, FALSE)
        ->group_by('products.id');
        $this->datatables->add_column("Actions", "<div class='text-center'><a href='#' class='btn btn-xs btn-primary ap tip' data-id='$1' title='".lang('add_to_purcahse_order')."'><i class='fa fa-plus'></i></a></div>", "id");
        // $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }

    function cuadre_caja_obsoleto(){

        $fecha  = date("Y-m-d");
        $cFecha = date("d-m-Y"); 
        
        if(isset($_REQUEST["tienda"])){
            $tda    = $_REQUEST["tienda"];

            /* Ventas en efectivo de un determinado dia */
            $cSql = "select a.id, a.date, a.serie, a.correlativo, a.tipoDoc, a.envio_electronico, a.status, tp.paid_by, a.grand_total
                from tec_sales a
                left join tec_payments tp on a.id = tp.sale_id
                where a.store_id={$tda} and a.date between '{$fecha}' and date_add('{$fecha}',interval 1 day) and tp.paid_by = 'cash'";

            $query = $this->db->query($cSql);

            $result1 = $query->result_array();

            //echo $cSql . "<br>";

            $cSql = "select a.*,tec_suppliers.name, tec_suppliers.cf2 
                from tec_purchases a
                JOIN `tec_suppliers` ON a.`supplier_id` = `tec_suppliers`.`id`
                where a.store_id={$tda} and a.date between '{$fecha}' and date_add('{$fecha}',interval 1 day)";

            //echo $cSql . "<br>";
            //die();
            $query = $this->db->query($cSql);

            $result2 = $query->result_array();

            // El detalle de la tienda
            $query = $this->db->query("select id, state from tec_stores where id=$tda");
            $cTienda = "";
            foreach($query->result() as $r){
                $cTienda = $r->state;
            } 

            $this->data['result1']      = $result1; 
            $this->data['result2']      = $result2;
            $this->data['tienda']       = $tda;

            $this->data['page_title']   = "<b>Cuadre de Caja : </b>" . $cFecha . "&nbsp;&nbsp;&nbsp;&nbsp; {$cTienda}";
            $this->data['nSaldo_inicial']   = $this->get_saldo_inicial($tda);
            $this->data['nVentas']          = $this->get_ventas($tda);
            $this->data['nCompras']         = $this->get_compras($tda);
        }

        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        
        $bc = array(array('link' => '#', 'page' => lang('')));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('reports/cuadre_caja', $this->data, $meta);
    }

    function cuadre_caja_det($fecha){
    }

    function ventas_por_dia(){
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = "<b>Ventas Acumuladas por día</b>";
        
        if(isset($_REQUEST["tienda"])){
            $this->data['tienda']   = $_REQUEST["tienda"];
        }
        $bc                         = array(array('link' => '#', 'page' => lang('')));
        $meta                       = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('reports/ventas_por_dia', $this->data, $meta);
    }

    function get_ventas_por_dia(){
        
        $this->load->library('datatables');

        $tienda = "";
        if($_REQUEST['tienda']){
            $tienda = $_REQUEST['tienda'];
        }

        /* REPORTE DE VENTAS DIARIAS POR TIENDA */
        $this->datatables->select("date_format(a.date, '%Y-%m-%d') as fecha, 
            CONCAT(ELT(WEEKDAY(date) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) as dia_semana,
            round(sum(a.total),2) as total, 
            round(sum(a.product_tax),2) as igv, 
            round(sum(a.total_discount),2) as descuento, 
            round(sum(a.grand_total),2) as grand_total, 
            concat('<div style=',char(34),'width', ':', round(sum(a.grand_total)*100/700,0), 'px; background-color:red', char(34), '>&nbsp;</div>') as barras");
        $this->datatables->from("tec_sales a");
        
        if(strlen($tienda)>0){
            $this->datatables->where("a.store_id",$tienda);
        }
        
        $this->datatables->group_by("date_format(a.date, '%Y-%m-%d'), CONCAT(ELT(WEEKDAY(date) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'))");
        

        //$this->datatables->get_ordering("date_format(a.date, '%Y-%m-%d')");
        //$this->datatables->order_by("date_format(a.date, '%Y-%m-%d')");

        // fecha total igv descuento grand_total barras
        echo $this->datatables->generate();
    }

    function get_saldo_inicial($tienda=1){
        $cSql = "select cash_in_hand from tec_registers 
            where store_id = {$tienda} and date_format(date, '%Y-%m-%d') = curdate() order by id desc limit 1";
        //echo $cSql;
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
            return $r->cash_in_hand;
        }
        return "0";
    }

    function get_ventas($tienda=1){
        
        $cSql = "select round(sum(a.grand_total),2) as grand_total
            from tec_sales a
            where a.store_id={$tienda} and date_format(a.date, '%Y-%m-%d') = curdate()";
        //echo $cSql;
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
            return $r->grand_total;
        }
        return 0;
    }

    function get_compras($tienda=1){
        $cSql = "select sum(a.costo_tienda) costo_tienda
            from tec_purchases a
            JOIN `tec_suppliers` ON a.`supplier_id` = `tec_suppliers`.`id`
            where a.store_id={$tienda} and date_format(a.date, '%Y-%m-%d') = curdate()";
            //and a.date between '{$fecha}' and date_add('{$fecha}',interval 1 day)";

        //die($cSql);

        $query = $this->db->query($cSql);

        foreach($query->result() as $r){
            return $r->costo_tienda;
        }
        return 0;
    }

    function salidas_por_dia(){
        
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = "<b>Cuadre de Caja</b>";
        
        if(isset($_REQUEST["tienda"])){
            $this->data['tienda']   = $_REQUEST["tienda"];
        }else{
            $this->data['tienda'] = "1";
        }

        if(isset($_REQUEST["fec_ini"])){
            $this->data['fec_ini']   = $_REQUEST["fec_ini"];
        }else{
            $this->data['fec_ini'] = date("Y-m-01");
            $diac = date("d") * 1;
            if($diac - 5 >= 1){
                $diac = $diac * 1 - 5;
                $this->data['fec_ini'] = date("Y-m-{$diac}");
            }
        }

        if(isset($_REQUEST["fec_fin"])){
            $this->data['fec_fin']   = $_REQUEST["fec_fin"];
        }else{
            $this->data['fec_fin'] = date("Y-m-d");
        }

        $bc     = array(array('link' => '#', 'page' => lang('')));
        $meta   = array('page_title' => "<span style=\"color:rgb(60,120,190);font-weight:bold;\">Cuadre de Caja</span>", 'bc' => $bc);

        $this->data["cadena_query"]         = $this->fm->query_salidas_por_dia($this->data['tienda'], $this->data['fec_ini'], $this->data['fec_fin']);

        $this->data["cadena_query_ventas"]  = $this->fm->query_salidas_por_dia_ventas($this->data['tienda'], $this->data['fec_ini'], $this->data['fec_fin']);

        $this->page_construct('reports/salidas_por_dia', $this->data, $meta);
    }

    function get_salidas_por_dia_ant(){
        
        $this->load->library('datatables');

        $tienda = "";
        if($_REQUEST['tienda']){
            $tienda = $_REQUEST['tienda'];
        }

        /* REPORTE DE VENTAS DIARIAS POR TIENDA */
        $this->datatables->select("date_format(a.date, '%Y-%m-%d') as fecha, 
            CONCAT(ELT(WEEKDAY(a.date) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) as dia_semana,
            round(sum(if(c.paid_by='cash', b.grand_total, 0)),2) total_ventas,
            6000 as caja_inicial,
            sum(if(a.tipoDoc='F',a.costo_tienda,0)) con_factura,
            sum(if(a.tipoDoc='B',a.costo_tienda,0)) con_boleta,
            sum(if(a.tipoDoc not in ('F','B'),a.costo_tienda,0)) con_recibo,
            sum(if(a.costo_tienda is null,0,a.costo_tienda)) total_salidas,
            sum(if(a.costo_banco is null,0,a.costo_banco)) total_depositos,
            round(sum(6000 - if(a.total is null,0,a.total) - if(a.costo_banco is null,0,a.costo_banco)),2) as caja_final
        ");
        
        //    concat('<div style=',char(34),'width', ':', round(sum(a.grand_total)*100/700,0), 'px; background-color:red', char(34), '>&nbsp;</div>') as barras");
        $this->datatables->from("tec_purchases a");

        $this->datatables->join("tec_sales b","date_format(a.date, '%Y-%m-%d') = date_format(b.date, '%Y-%m-%d')","left");
        
        $this->datatables->join("tec_payments c","b.id = c.sale_id","left");

        if(strlen($tienda)>0){
            $this->datatables->where("a.store_id",$tienda);
        }
        
        $this->datatables->group_by("date_format(a.date, '%Y-%m-%d'), 
            CONCAT(ELT(WEEKDAY(a.date) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'))
        ");
        
        // fecha total igv descuento grand_total barras
        
        echo $this->datatables->generate();
    }

    function salidas_por_mes(){
        
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = "<b>Resumen Mensual de Caja</b>";
        
        if(isset($_REQUEST["tienda"])){
            $this->data['tienda']   = $_REQUEST["tienda"];
        }

        if(isset($_REQUEST["fec_ini"])){
            $this->data['fec_ini']   = $_REQUEST["fec_ini"];
        }

        if(isset($_REQUEST["fec_fin"])){
            $this->data['fec_fin']   = $_REQUEST["fec_fin"];
        }

        $bc                         = array(array('link' => '#', 'page' => lang('')));
        $meta                       = array('page_title' => "<span style=\"color:rgb(60,120,190);font-weight:bold;\">Cuadre de Caja</span>", 'bc' => $bc);

        $this->page_construct('reports/salidas_por_mes', $this->data, $meta);
    }

    function analisis_compras($desde="", $hasta="", $tienda="", $product_id=""){
        /*
        $this->db->select("tec_purchases.fec_emi_doc, tec_purchase_items.product_id, tec_products.name, tec_purchase_items.quantity, tec_purchase_items.cost, tec_purchase_items.subtotal");
        $this->db->join("tec_purchases","tec_purchase_items.purchase_id = tec_purchases.id","inner");
        $this->db->join("tec_products","tec_purchase_items.product_id = tec_products.id","inner");
        $this->db->where("tec_purchases.tipoDoc in ('F','B','G')");
        $cadena = $this->db->get_compiled_select("tec_purchase_items");
        echo $cadena;
        */
        $bc                         = array(array('link' => '#', 'page' => lang('')));
        $meta                       = array('page_title' => "Analisis de Compras", 'bc' => $bc);

        $this->data["desde"]             = $desde;
        $this->data["hasta"]             = $hasta;
        $this->data["tienda"]            = $tienda;
        $this->data["product_id"]        = $product_id;

        //$cSql = "";

        //$query_mes = $this->db->query($csql);


        $this->page_construct('reports/analisis_compras', $this->data, $meta);
    }

    function get_analisis_compras(){
        $this->load->library('datatables');
        $this->datatables->select("tec_products.name, sum(tec_purchase_items.quantity) quantity, sum(tec_purchase_items.subtotal) subtotal");

        $this->datatables->from("tec_purchase_items");
        $this->datatables->join("tec_purchases","tec_purchase_items.purchase_id = tec_purchases.id");
        $this->datatables->join("tec_products","tec_purchase_items.product_id = tec_products.id");

        $this->datatables->group_by("tec_products.name");

        if(isset($_POST["tienda"])){
            $tienda = $_POST["tienda"];
            if(strlen($tienda)>0 && $tienda!="null" && $tienda!="0"){
                $this->datatables->where("tec_purchases.store_id",$tienda);
            }
        }

        if(isset($_POST["product_id"])){
            $product_id = $_POST["product_id"];
            if(strlen($product_id) > 0 && $product_id!="null" && $product_id != '0'){
                $this->datatables->where("tec_purchase_items.product_id", $product_id);
                //die($product_id);
            }
        }

        if(isset($_POST["desde"])){
            $desde = $_POST["desde"];
            if(strlen($desde) > 0 && $desde!="null"){
                $this->datatables->where("tec_purchases.fec_emi_doc>=",$desde);
            }
        }

        if(isset($_POST["hasta"])){
            $hasta = $_POST["hasta"];
            if(strlen($hasta) > 0 && $hasta!="null"){
                $this->datatables->where("tec_purchases.fec_emi_doc<=",$hasta);
            }
        }

        echo $this->datatables->generate();    
    }

    function reporte_a_sunat(){
        $bc                         = array(array('link' => '#', 'page' => lang('')));
        $meta                       = array('page_title' => "Reporte a Sunat", 'bc' => $bc);
        $this->data["cDesde"]       = $_REQUEST["txt_desde"]; //"2021-11-01";
        $this->data["cHasta"]       = $_REQUEST["txt_hasta"]; //"2021-11-30";

        $this->page_construct('reports/reporte_a_sunat', $this->data, $meta);
    }

    function especial_productos(){
        $bc                         = array(array('link' => '#', 'page' => lang('')));
        $meta                       = array('page_title' => "Compra de Insumos", 'bc' => $bc);

        $this->data["cDesde"]       = isset($_REQUEST["txt_desde"]) ? $_REQUEST["txt_desde"] : date("Y-m-d");
        $this->data["cHasta"]       = isset($_REQUEST["txt_hasta"]) ? $_REQUEST["txt_hasta"] : date("Y-m-d");
        $this->data["producto"]     = isset($_REQUEST["txt_producto"]) ? $_REQUEST["txt_producto"] : "null";
        $this->data["proveedor"]    = isset($_REQUEST["txt_proveedor"]) ? $_REQUEST["txt_proveedor"] : "null";
        $this->data["store_id"]     = isset($_REQUEST["store_id"]) ? $_REQUEST["store_id"] : "null";

        //echo str_replace("\n","<br>",print_r($this->data,true));
        //die();
        $this->page_construct('reports/especial_productos', $this->data, $meta);
    }

    function get_especial($cDesde="",$cHasta="",$cProducto="",$cProveedor="",$cStore_id=""){  // 
        //$cDesde = substr($cDesde,0,4) . "-" . substr($cDesde,4,2) . "-" . substr($cDesde,6,2);
        //$cHasta = substr($cHasta,0,4) . "-" . substr($cHasta,4,2) . "-" . substr($cHasta,6,2);

        $this->load->library('datatables');
        
        /*$cSql = "select substr(tec_purchases.date,1,10) fecha, tec_products.name, tec_products.unidad, tec_purchases.supplier_id, d.name proveedor, round(tec_purchase_items.quantity,2) quantity, round(tec_purchase_items.precio,2) precio, truncate((tec_purchase_items.precio/tec_purchase_items.quantity),2) punit
            from tec_purchases
            inner join tec_purchase_items on tec_purchases.id = tec_purchase_items.purchase_id
            inner join tec_products on tec_purchase_items.product_id = tec_products.id
            inner join tec_suppliers d on tec_purchases.supplier_id = d.id
            where tec_purchases.date between '$cDesde' and '$cHasta'";*/
        
        $this->datatables->select("substr(tec_purchases.date,1,10) fecha, tec_products.name, tec_products.unidad, tec_purchases.supplier_id, tec_suppliers.name proveedor, round(tec_purchase_items.quantity,2) quantity, round(tec_purchase_items.precio,2) precio, truncate((tec_purchase_items.precio/tec_purchase_items.quantity),2) punit, tec_stores.state");
        $this->datatables->from("tec_purchases");
        $this->datatables->join("tec_purchase_items","tec_purchases.id = tec_purchase_items.purchase_id");
        $this->datatables->join("tec_products","tec_purchase_items.product_id = tec_products.id");
        $this->datatables->join("tec_suppliers","tec_purchases.supplier_id = tec_suppliers.id");
        $this->datatables->join("tec_stores","tec_purchases.store_id = tec_stores.id");
        $this->datatables->where("tec_purchases.date between '$cDesde' and '$cHasta 23:59:59'");

        if (strlen($cProducto)>0 && $cProducto != 'null'){
            //$this->datatables->where("tec_products.name like '%{$cProducto}%'");
            $this->datatables->where("tec_products.id",$cProducto);
        }
        if (strlen($cProveedor)>0 && $cProveedor != 'null'){
            //$this->datatables->where("tec_suppliers.name like '%{$cProveedor}%'");
            $this->datatables->where("tec_purchases.supplier_id",$cProveedor);
        }

        if (strlen($cStore_id)>0 && $cStore_id != 'null'){
            $this->datatables->where("tec_purchases.store_id", $cStore_id);
        }
        
        echo $this->datatables->generate();

    }


}
