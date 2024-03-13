<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends MY_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        $this->load->library('form_validation');
        $this->load->model('sales_model');

        $this->digital_file_types = 'zip|pdf|doc|docx|xls|xlsx|jpg|png|gif';
        //error_reporting(E_ALL);
        //ini_set('display_errors', '1');

    }

    function index($cDesde=null, $cHasta=null, $tienda='0', $metodo=null) {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('sales');
        $this->data['desde'] = is_null($cDesde) ? date("Y-m-d") : $cDesde;
        $this->data['hasta'] = is_null($cHasta) ? date("Y-m-d") : $cHasta; //$cHasta;
        $this->data['tienda'] = $tienda;
        $this->data["metodo"] = $metodo;
        $bc = array(array('link' => '#', 'page' => lang('sales')));
        $meta = array('page_title' => lang('sales'), 'bc' => $bc);
        $this->page_construct('sales/index', $this->data, $meta);
    }

    function get_sales() {

        $this->load->library('datatables');
        
        $base_x = base_url("sales/envio_individual");

        $metodo = $this->input->post('metodo');

        if(!is_null($metodo) and strlen($metodo)>0 and $metodo!='0'){
            $this->db->select("sale_id, amount, paid_by");
            $this->db->from("payments");
            $this->db->where("paid_by",$metodo);
            //$this->db->group_by("sale_id");
            $subQuery = $this->db->get_compiled_select();
        }else{
            $this->db->select("sale_id, sum(amount) amount,group_concat(concat(paid_by,':',round(amount,1) ),' ') paid_by");
            $this->db->from("payments");
            $this->db->group_by("sale_id");
            $subQuery = $this->db->get_compiled_select();
        }

        //die($subQuery);
        $botone = "'<button onclick=\"reenvio_a_sunat(', sales.id, ')\">Reenviar</button>'";
            
        $this->datatables->select("sales.id, stores.state, DATE_FORMAT(date, '%d/%m/%Y %H:%i:%s') as date, customer_name, total, total_tax, total_discount, grand_total, if(tp.amount is null,0,tp.amount) amount, status,
            sales.tipoDoc, concat(serie,'-',correlativo) recibo, tp.paid_by,
            if(envio_electronico = 0, if(tec_sales.tipoDoc != 'Ticket',concat({$botone}),''), concat('<a href=\'',dir_comprobante,'\' target=\'_blank\'>Ticket</a>')) as dir_comprobante");

        $this->datatables->from('sales');
        $this->datatables->join('stores','sales.store_id=stores.id');

        $this->datatables->join("($subQuery) as tp",'sales.id = tp.sale_id','left');
        $this->datatables->where("sales.date>='2022-07-01'");

        $cDesde = $this->input->post('desde');
        $cHasta = $this->input->post('hasta');
        $tienda = $this->input->post('tienda');
        
        
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 and $cDesde != "null"){
                $this->datatables->where('sales.date>=', $cDesde);
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 and $cHasta != "null"){
                $this->datatables->where("sales.date<date_add('$cHasta',interval 1 day)");
            }
        }
        
        if(!is_null($tienda) and strlen($tienda)>0 and $tienda!='0'){
            $this->datatables->where("sales.store_id",$tienda);
        }

        if(!is_null($metodo) and strlen($metodo)>0 and $metodo!='0'){
            $this->datatables->like("tp.paid_by",$metodo);
        }

        if($this->Admin){

            $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . 
                site_url('pos/view/$1/1') . "' title='".lang("view_invoice")."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'><i class='fa fa-list'></i></a> <a href='".       
                site_url('sales/payments/$1')."' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a> <a href='".        
                site_url('sales/add_payment/$1')."' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a>".
                "<a href='" . site_url('pos/?edit=$1') . "' title='".lang("edit_invoice")."' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a>".
                /*"<a href='" . site_url('sales/delete/$1') . "' onClick=\"return confirm('". lang('alert_x_sale') ."')\" title='".lang("delete_sale")."' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a>" .*/
                "<a href=\"#\" onclick='anular_doc($1)' class='tip btn btn-primary btn-xs' title='Anular'><i class='fa fa-trash-o'></i></a></div></div>", "id");
                //site_url('pos/enviar_anulacion_nubefact/$1')."' title='" . "Anular" . "' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'><i class='fa fa-briefcase'></i></a></div></div>", "id");
        
        }else{

            //if (!$this->Admin && !$this->session->userdata('view_right')) {
            //    $this->datatables->where('created_by', $this->session->userdata('user_id'));
            //}
            
            if($_SESSION["group_id"] == 2){
                $this->datatables->where('store_id', $this->session->userdata('store_id'));
            }
            
            $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('pos/view/$1/1') . "' title='".lang("view_invoice")."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'><i class='fa fa-list'></i></a> <a href='".site_url('sales/payments/$1')."' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a> <a href='".site_url('sales/add_payment/$1')."' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a> <a href='" . site_url('pos/?edit=$1') . "' title='".lang("edit_invoice")."' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('sales/delete/$1') . "' onClick=\"return confirm('". lang('alert_x_sale') ."')\" title='".lang("delete_sale")."' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id");

        }

        echo $this->datatables->generate();

    }

    function opened() {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('opened_bills');
        $bc = array(array('link' => '#', 'page' => lang('opened_bills')));
        $meta = array('page_title' => lang('opened_bills'), 'bc' => $bc);
        $this->page_construct('sales/opened', $this->data, $meta);
    }

    function get_opened_list(){

        $this->load->library('datatables');
        if ($this->db->dbdriver == 'sqlite3') {
            $this->datatables->select("id, date, customer_name, hold_ref, (total_items || ' (' || total_quantity || ')') as items, grand_total", FALSE);
        } else {
            $this->datatables->select("id, date, customer_name, hold_ref, CONCAT(total_items, ' (', total_quantity, ')') as items, grand_total", FALSE);
        }
        $this->datatables->from('suspended_sales');
        if(!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
            $this->datatables->where('created_by', $user_id);
        }
        $this->datatables->where('store_id', $this->session->userdata('store_id'));
        $this->datatables->add_column("Actions",
            "<div class='text-center'><div class='btn-group'><a href='" . site_url('pos/?hold=$1') . "' title='".lang("click_to_add")."' class='tip btn btn-info btn-xs'><i class='fa fa-th-large'></i></a>
            <a href='" . site_url('sales/delete_holded/$1') . "' onClick=\"return confirm('". lang('alert_x_holded') ."')\" title='".lang("delete_sale")."' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id")
        ->unset_column('id');

        echo $this->datatables->generate();

    }


    function delete($id = NULL) {
        /*
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }*/

        if($this->input->get('id')){ $id = $this->input->get('id'); }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect('sales');
        }

        //if ( $this->sales_model->deleteInvoice($id) ) {
        
        // Elimino Payments
        $this->db->where("sale_id",$id);
        $this->db->delete("payments");

        // Elimino sale_items
        $this->db->where("sale_id",$id);
        $this->db->delete("sale_items");

        // Elimino sales
        $this->db->where("id",$id);
        $this->db->delete("sales");

        /*
        $this->db->set("grand_total",0);
        $this->db->set("paid",0);
        $this->db->where("id",$id);
        $this->db->update("sales");
        */

        if($this->db->affected_rows()>0){
            $this->session->set_flashdata('message', lang("invoice_deleted"));
        }
        redirect('sales');

    }

    function delete_holded($id = NULL) {

        if($this->input->get('id')){ $id = $this->input->get('id'); }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect('sales/opened');
        }

        if ( $this->sales_model->deleteOpenedSale($id) ) {
            $this->session->set_flashdata('message', lang("opened_bill_deleted"));
            redirect('sales/opened');
        }

    }

    /* -------------------------------------------------------------------------------- */

    function payments($id = NULL) {
        $this->data['payments'] = $this->sales_model->getSalePayments($id);
        $this->load->view($this->theme . 'sales/payments', $this->data);
    }

    function payment_note($id = NULL) {
        $payment = $this->sales_model->getPaymentByID($id);
        $inv = $this->sales_model->getSaleByID($payment->sale_id);
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['page_title'] = $this->lang->line("payment_note");

        $this->load->view($this->theme . 'sales/payment_note', $this->data);
    }

    function add_payment($id = NULL, $cid = NULL) {
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $date = $this->input->post('date');
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = array(
                'date' => $date,
                'sale_id' => $id,
                'customer_id' => $cid,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'gc_no' => $this->input->post('gift_card_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
                'store_id' => $this->session->userdata('store_id'),
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = 2048;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            // $this->tec->print_arrays($payment);

        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            $this->tec->dd();
        }


        if ($this->form_validation->run() == true && $this->sales_model->addPayment($payment)) {
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $sale = $this->sales_model->getSaleByID($id);
            $this->data['inv'] = $sale;

            $this->load->view($this->theme . 'sales/add_payment', $this->data);
        }
    }

    function edit_payment($id = NULL, $sid = NULL) {

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            $payment = array(
                'sale_id' => $sid,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'gc_no' => $this->input->post('gift_card_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'updated_by' => $this->session->userdata('user_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            );

            if ($this->Admin) {
                $payment['date'] = $this->input->post('date');
            }

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = 2048;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->tec->print_arrays($payment);

        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            $this->tec->dd();
        }


        if ($this->form_validation->run() == true && $this->sales_model->updatePayment($id, $payment)) {
            $this->session->set_flashdata('message', lang("payment_updated"));
            redirect("sales");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $payment = $this->sales_model->getPaymentByID($id);
            if($payment->paid_by != 'cash') {
                //$this->session->set_flashdata('error', lang('only_cash_can_be_edited'));
                //$this->tec->dd();
            }
            $this->data['payment'] = $payment;
            $this->load->view($this->theme . 'sales/edit_payment', $this->data);
        }
    }

    function delete_payment($id = NULL) {

        if($this->input->get('id')){ $id = $this->input->get('id'); }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ( $this->sales_model->deletePayment($id) ) {
            $this->session->set_flashdata('message', lang("payment_deleted"));
            redirect('sales');
        }
    }

    public function status() {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect('sales');
        }
        $this->form_validation->set_rules('sale_id', lang('sale_id'), 'required');
        $this->form_validation->set_rules('status', lang('status'), 'required');

        if ($this->form_validation->run() == true) {

            $this->sales_model->updateStatus($this->input->post('sale_id', TRUE), $this->input->post('status', TRUE));
            $this->session->set_flashdata('message', lang('status_updated'));
            redirect('sales');

        } else {

            $this->session->set_flashdata('error', validation_errors());
            redirect('sales');

        }
    }

    public function envio_individual(){
        $this->load->model('pos_model');
        
        $sale_id = $_REQUEST["sale_id"];
        
        $this->pos_model->enviar_doc_sunat_nubefact_individual($sale_id, true);

        $query = $this->db->select("envio_electronico")->where("id",$sale_id)->get("sales");

        foreach($query->result() as $r){
            $rpta = $r->envio_electronico;
        }
        if ($rpta != '0'){
            echo "OK";
        }else{
            echo "No se pudo";
        }
    }

    public function reenvio_individual_apisperu(){

        $this->load->model('pos_model_apisperu');
        
        $sale_id = $_REQUEST["sale_id"];
        
        $this->pos_model_apisperu->envio_masivo_individual($sale_id);

        $query = $this->db->select("envio_electronico")->where("id",$sale_id)->get("sales");

        foreach($query->result() as $r){
            $rpta = $r->envio_electronico;
        }
        if ($rpta != '0'){
            echo "OK";
        }else{
            echo "No se pudo";
        }

    }

    public function acumulado($tienda='0',$cDesde="null", $cHasta="null", $producto="null"){
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = "Ventas - Acumulado Diario";
        $this->data['tienda'] = $tienda;
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['producto'] = $producto;
        $bc = array(array('link' => '#', 'page' => lang('sales')));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('sales/acumulado', $this->data, $meta);
    }

    function get_acumulado() {

        $this->load->library('datatables');
        
        $this->db->reset_query();
        $this->datatables->select("stores.state, date_format(tec_sales.date,'%Y-%m-%d') fecha, products.name, round(sum(ts.quantity),0) cantidad, round(sum(ts.subtotal),2) total");

        $this->datatables->from('sales');
        $this->datatables->join('sale_items as ts','sales.id = ts.sale_id');
        $this->datatables->join('stores','sales.store_id=stores.id','left');
        $this->datatables->join('products','ts.product_id = products.id','left');
        $this->datatables->group_by(array('stores.state', "date_format(tec_sales.date,'%Y-%m-%d')", 'tec_products.name'));

        //$this->datatables->join("($subQuery) as tp",'sales.id = tp.sale_id','left');

        $tienda = $this->input->post('tienda');
        $cDesde = $this->input->post('desde');
        $cHasta = $this->input->post('hasta');
        $producto = $this->input->post('producto');
        
        if(!is_null($tienda) and strlen($tienda)>0 and $tienda!='0'){
            $this->datatables->where("sales.store_id",$tienda);
        }

        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 and $cDesde != "null"){
                $this->datatables->where('sales.date>=', $cDesde);
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 and $cDesde != "null"){
                $this->datatables->where("sales.date<date_add('$cHasta',interval 1 day)");
            }
        }

        if(!is_null($producto)){
            if(strlen($producto)>0 and $producto != "null"){
                $this->datatables->where("products.id", $producto);
            }
        }

/*        
        if($this->Admin){

            $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . 
                site_url('pos/view/$1/1') . "' title='".lang("view_invoice")."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'><i class='fa fa-list'></i></a> <a href='".       
                site_url('sales/payments/$1')."' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a> <a href='".        
                site_url('sales/add_payment/$1')."' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a>".
                "<a href='" . site_url('pos/?edit=$1') . "' title='".lang("edit_invoice")."' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a>".
                "<a href=\"#\" onclick='anular_doc($1)' class='tip btn btn-primary btn-xs' title='Anular'><i class='fa fa-trash-o'></i></a></div></div>", "id");
                //site_url('pos/enviar_anulacion_nubefact/$1')."' title='" . "Anular" . "' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'><i class='fa fa-briefcase'></i></a></div></div>", "id");
        
        }else{

            if (!$this->Admin && !$this->session->userdata('view_right')) {
                $this->datatables->where('created_by', $this->session->userdata('user_id'));
            }
            
            $this->datatables->where('store_id', $this->session->userdata('store_id'));
            $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('pos/view/$1/1') . "' title='".lang("view_invoice")."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'><i class='fa fa-list'></i></a> <a href='".site_url('sales/payments/$1')."' title='" . lang("view_payments") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-money'></i></a> <a href='".site_url('sales/add_payment/$1')."' title='" . lang("add_payment") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-briefcase'></i></a> <a href='" . site_url('pos/?edit=$1') . "' title='".lang("edit_invoice")."' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('sales/delete/$1') . "' onClick=\"return confirm('". lang('alert_x_sale') ."')\" title='".lang("delete_sale")."' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id");

        }
*/
        echo $this->datatables->generate();

    }

    public function vtas_platos_hora($tienda='0',$cDesde="null", $cHasta="null", $producto="null"){
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = "Ventas Diarias de Platos x Hora";
        $this->data['tienda'] = $tienda;
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['producto'] = $producto;
        $bc = array(array('link' => '#', 'page' => lang('sales')));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('sales/vtas_platos_hora', $this->data, $meta);
    }

    function get_vtas_platos_hora() {

        $this->load->library('datatables');
        
        $this->db->reset_query();
        $this->datatables->select("stores.state, date_format(tec_sales.date,'%Y-%m-%d') fecha, products.name,
            round(sum(if(substr(tec_sales.date,12,2) in ('07','08'),ts.quantity,0)),0) h7,
            round(sum(if(substr(tec_sales.date,12,2) in ('09','10'),ts.quantity,0)),0) h9,
            round(sum(if(substr(tec_sales.date,12,2) in ('11','12'),ts.quantity,0)),0) h11,
            round(sum(if(substr(tec_sales.date,12,2) in ('13','14'),ts.quantity,0)),0) h13,
            round(sum(if(substr(tec_sales.date,12,2) in ('15','16'),ts.quantity,0)),0) h15,
            round(sum(if(substr(tec_sales.date,12,2) in ('17','18'),ts.quantity,0)),0) h17,
            round(sum(if(substr(tec_sales.date,12,2) in ('19','20'),ts.quantity,0)),0) h19,
            round(sum(if(substr(tec_sales.date,12,2) in ('21','22'),ts.quantity,0)),0) h21,
            round(sum(if(substr(tec_sales.date,12,2) in ('23'),ts.quantity,0)),0) h23,
            round(sum(ts.quantity),0) cantidad, round(sum(ts.subtotal),2) total");
        $this->datatables->from('sales');
        $this->datatables->join('sale_items as ts','sales.id = ts.sale_id');
        $this->datatables->join('stores','sales.store_id=stores.id','left');
        $this->datatables->join('products','ts.product_id = products.id','left');
        $this->datatables->group_by(array('stores.state', "date_format(tec_sales.date,'%Y-%m-%d')", 'tec_products.name'));

        //$this->datatables->join("($subQuery) as tp",'sales.id = tp.sale_id','left');

        $tienda = $this->input->post('tienda');
        $cDesde = $this->input->post('desde');
        $cHasta = $this->input->post('hasta');
        $producto = $this->input->post('producto');
        
        if(!is_null($tienda) and strlen($tienda)>0 and $tienda!='0'){
            $this->datatables->where("sales.store_id",$tienda);
        }

        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 and $cDesde != "null"){
                $this->datatables->where('sales.date>=', $cDesde);
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0 and $cDesde != "null"){
                $this->datatables->where("sales.date<date_add('$cHasta',interval 1 day)");
            }
        }

        if(!is_null($producto)){
            if(strlen($producto)>0 and $producto != "null"){
                $this->datatables->where("products.id", $producto);
            }
        }

        echo $this->datatables->generate();

    }

    function platos_diarios_canales($tienda='0', $anno="null", $mes="null"){
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = "Venta Diaria de Platos x Canal de Venta";
        $this->data['tienda'] = $tienda;
        $this->data['anno'] = $anno;
        $this->data['mes'] = $mes;
        //$this->data['producto'] = $producto;
        $bc     = array(array('link' => '#', 'page' => lang('sales')));
        $meta   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('sales/platos_diarios_canales', $this->data, $meta);
    }

    function get_platos_diarios_canales($tienda='0', $anno="null", $mes="null"){
        /*$tienda     = $this->input->post("tienda");
        $anno       = $this->input->post("anno");
        $mes        = $this->input->post("mes");*/
    
        $cSql = "CALL platos_diarios_canales(?,?,?)";
        $result = $this->db->query($cSql,array($tienda, $anno, $mes))->result_array();

        // Armando el rompecabezas
        $ar_g = array();

        $cado ="product_id, producto, dt, py, ra, dd, dias_01_dt, dias_01_py, dias_01_ra, dias_01_dd, dias_02_dt, dias_02_py, dias_02_ra, dias_02_dd, dias_03_dt, dias_03_py, dias_03_ra, dias_03_dd, dias_04_dt, dias_04_py, dias_04_ra, dias_04_dd, dias_05_dt, dias_05_py, dias_05_ra, dias_05_dd, dias_06_dt, dias_06_py, dias_06_ra, dias_06_dd, dias_07_dt, dias_07_py, dias_07_ra, dias_07_dd, dias_08_dt, dias_08_py, dias_08_ra, dias_08_dd, dias_09_dt, dias_09_py, dias_09_ra, dias_09_dd, dias_10_dt, dias_10_py, dias_10_ra, dias_10_dd, dias_11_dt, dias_11_py, dias_11_ra, dias_11_dd, dias_12_dt, dias_12_py, dias_12_ra, dias_12_dd, dias_13_dt, dias_13_py, dias_13_ra, dias_13_dd, dias_14_dt, dias_14_py, dias_14_ra, dias_14_dd, dias_15_dt, dias_15_py, dias_15_ra, dias_15_dd, dias_16_dt, dias_16_py, dias_16_ra, dias_16_dd, dias_17_dt, dias_17_py, dias_17_ra, dias_17_dd, dias_18_dt, dias_18_py, dias_18_ra, dias_18_dd, dias_19_dt, dias_19_py, dias_19_ra, dias_19_dd, dias_20_dt, dias_20_py, dias_20_ra, dias_20_dd, dias_21_dt, dias_21_py, dias_21_ra, dias_21_dd, dias_22_dt, dias_22_py, dias_22_ra, dias_22_dd, dias_23_dt, dias_23_py, dias_23_ra, dias_23_dd, dias_24_dt, dias_24_py, dias_24_ra, dias_24_dd, dias_25_dt, dias_25_py, dias_25_ra, dias_25_dd, dias_26_dt, dias_26_py, dias_26_ra, dias_26_dd, dias_27_dt, dias_27_py, dias_27_ra, dias_27_dd, dias_28_dt, dias_28_py, dias_28_ra, dias_28_dd, dias_29_dt, dias_29_py, dias_29_ra, dias_29_dd, dias_30_dt, dias_30_py, dias_30_ra, dias_30_dd, dias_31_dt, dias_31_py, dias_31_ra, dias_31_dd";

        $ar_col = explode(",", $cado);
        $limite = count($ar_col);
        for($i=0; $i<$limite; $i++){ // Por cada producto
            $ar_col[$i] = trim($ar_col[$i]);
        }
        echo $this->fm->json_datatable($ar_col,$result);
    }

}
