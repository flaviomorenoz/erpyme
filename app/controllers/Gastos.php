<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Gastos extends MY_Controller
{

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
        $this->load->model('gastos_model');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip';

    }

    function index($cDesde=null, $cHasta=null){
        //if ( ! $this->Admin) {
        //    $this->session->set_flashdata('error', lang('access_denied'));
        //    redirect('pos');
        //}
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('Gastos');
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        
        $bc = array(array('link' => '#', 'page' => "gastos"));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('gastos/index', $this->data, $meta);
    }

    function view($id = NULL) {
        $this->data['purchase'] = $this->gastos_model->getPurchaseByID($id);
        $this->data['items'] = $this->gastos_model->getAllPurchaseItems($id);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = 'gastos';
        $this->load->view($this->theme.'gastos/view', $this->data);

    }

    function add($tipo_gasto = null) {

        if (!$this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        
        $page_title = 'gastos'; // Añadir compra
        $this->data["tipogasto"]    = 'gastos';

        if(!isset($_POST["modo_edicion"])){
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers']    = $this->site->getAllSuppliers();
            $this->data['page_title']   = $page_title;
            $this->data['Admin']        = ($this->session->userdata["group_id"] == '1' ? true : false);

            $bc     = array(
                array('link' => site_url('gastos'),     'page' => lang('gastos')), 
                array('link' => '#',                    'page' => lang('add_expense')));
            $meta   = array('page_title' => $page_title, 'bc' => $bc);
            
            $this->page_construct('gastos/add', $this->data, $meta);
        
        // A PARTIR DE AQUI ES MODO EDICION O SAVE
        }else{

            //$this->form_validation->set_rules('date', lang('date'), 'required');

            //if ($this->form_validation->run() == true){
                
                $total = 0;
                $quantity = "quantity";
                $product_id = "product_id";
                $unit_cost = "cost";
                $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
                $cucu = "";
                
                for ($r = 0; $r < $i; $r++) {
                    $item_id = $_POST['product_id'][$r];
                    $item_qty = $_POST['quantity'][$r];
                    $item_cost = $_POST['cost'][$r];
                
                    $cucu.= "item:" . $item_id . "<br>";
                    $cucu.= "cantidad:".$item_qty . "<br>";
                    $cucu.= "costo:".$item_cost . "<br><br>";
                    
                    if($item_id && $item_qty && $item_cost){

                        //echo $item_id;
                        //die();
                        if(!$this->gastos_model->getProductByID($item_id)){
                            die("Barbaro");
                            $this->session->set_flashdata('error', $this->lang->line("product_not_found")." ( ".$item_id." ).");
                            //$this->session->set_flashdata('error', $cucu ." ( ".$item_id." ).");
                            
                            redirect('gastos/add');
                        }

                        $products[] = array(
                            'product_id' => $item_id,
                            'cost' => $item_cost,
                            'quantity' => $item_qty,
                            'subtotal' => ($item_cost*$item_qty)
                            );

                        //$total += ($item_cost * $item_qty);

                    }
                    
                }
                $total = (1 * $this->input->post('txt_gTotal')) + $this->input->post('cargo_servicio');

                
                if($this->data["tipogasto"] == "gastos"){
                    $costo_tienda   = "0";
                    $costo_banco    = "0";
                }else{
                    $costo_tienda   = $this->input->post('costo_tienda');
                    $costo_banco    = $this->input->post('costo_banco');
                }

                // Validacion de los costos:
                $ct = $costo_tienda * 1;
                $cb = $costo_banco * 1;
                $cargo = $this->input->post('cargo_servicio');

                if(strlen($cargo)>0){
                    $cargo = $cargo * 1;
                }else{
                    $cargo = 0;
                }


                //$total_inc_cargo = $total + $cargo + 0;
                $el_total = $ct + $cb + 0;

                $datin = array('tipoDoc'=>$this->input->post('tipoDoc'));
                
                if (!isset($products) || empty($products)) {
                    $this->form_validation->set_rules('product', lang("order_items"), 'required');
                } else {
                    krsort($products);
                }


                if(is_null($this->input->post('note', TRUE))){
                    $note = "";
                }else{
                    $note = $this->input->post('note', TRUE);
                }


                $data = array(
                            'date' => $this->input->post('date'),
                            'reference' => ($this->input->post('tipoDoc') . " " . $this->input->post('nroDoc')),
                            'supplier_id' => (isset($_POST['supplier']) ? $_POST['supplier'] : "1"),
                            'note' => $note,
                            'received' => $this->input->post('received'),
                            'subtotal'  => $this->input->post('txt_gSubtotal'),
                            'igv'       => $this->input->post('txt_gIgv'),
                            'total'     => $this->input->post('txt_gTotal'),
                            'created_by'    => $this->session->userdata('user_id'),
                            'store_id'      => $this->session->userdata('store_id'),
                            'tipoDoc'       => $this->input->post('tipoDoc'),
                            'nroDoc'        => $this->input->post('nroDoc'),
                            'fec_emi_doc'   => $this->input->post('fec_emi_doc'),
                            'fec_venc_doc' => $this->input->post('fec_venc_doc'),
                            //'motivo'        => $this->session->userdata('motivo'),
                            'cargo_servicio'=> $cargo,
                            'costo_tienda'  => $costo_tienda,
                            'costo_banco'   => $costo_banco,
                            'tipogasto'     => $this->input->post('tipogasto'),
                            'texto_supplier' => (isset($_POST["texto_supplier"]) ? $_POST["texto_supplier"] : "")
                        );

                if ($_FILES['userfile']['size'] > 0) {

                    $this->load->library('upload');
                    $config['upload_path'] = 'uploads/';
                    $config['allowed_types'] = $this->allowed_types;
                    $config['max_size'] = '2000';
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors();
                        $this->upload->set_flashdata('error', $error);
                        redirect("gastos/add");
                    }

                    $data['attachment'] = $this->upload->file_name;

                }

                //if ($this->form_validation->run() == true){

                    if(strlen($_REQUEST["edicion_purchase_id"])>0){
                    
                        // Agregando el purchase_id para el insert del detalle
                        for($i=0; $i<count($products); $i++){
                            $products[$i]['purchase_id'] = $_REQUEST["edicion_purchase_id"];
                        }

                        $this->db->where("id",$_REQUEST["edicion_purchase_id"]);
                        
                        // En la cabecera:
                        $this->db->set($data);
                        $this->db->update("purchases",$data);
                        
                        // En el detalle
                        $this->db->where('purchase_id',$_REQUEST["edicion_purchase_id"]);
                        $this->db->delete('purchase_items');

                        for($i=0; $i<count($products); $i++){
                            $this->db->insert("purchase_items",$products[$i]);
                        }

                        // retorna al listado
                        //die("En edicion, si valida");
                        $this->session->set_userdata('remove_spo', 1);
                        $this->session->set_flashdata('message', "gastos agregados");
                        redirect("gastos");

                    }else{
                        
                        //var_dump($data);
                        //die();
                        if ($this->gastos_model->addPurchase($data, $products)){
                            //die("trebol");
                            $this->session->set_userdata('remove_spo', 1);
                            $this->session->set_flashdata('message', "gastos agregados");
                            
                            redirect("gastos");

                        }else{

                            //die("espada");
                            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                            $this->data['suppliers'] = $this->site->getAllSuppliers();
                            $this->data['page_title'] = "gastos";
                            $bc = array(array('link' => site_url('gastos'), 'page' => "gastos"), array('link' => '#', 'page' => "gastos agregados"));
                            $meta = array('page_title' => lang('add_expense'), 'bc' => $bc);
                            $this->page_construct('gastos/add', $this->data, $meta);

                        }
                    }

        } // FIN DE MODO EDICION

    }

    function edit($id = NULL){

        $acceso = false;
        if(!is_null($id)){
            $result = $this->db->select("date, tipogasto")->where(id,$id)->get("purchases")->result_array();
            foreach($result as $r){
                $la_fecha = substr($r["date"],0,10);
                $tipogasto = $r["tipogasto"];
            }
            
            $hoy = date("Y-m-d");

            if($la_fecha >= $hoy){
                $acceso = true;
            }

            $page_title = 'gastos';
            
            $this->data["tipogasto"] = $tipogasto;
        }

        if($this->Admin){
            $acceso = true;
        }

        if ($acceso) {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers']    = $this->site->getAllSuppliers();
            $this->data['page_title']   = $page_title;
            $this->data['purchases_id'] = $id;     
            
            $bc     = array(array('link' => site_url('gastos'), 'page' => 'gastos'), array('link' => '#', 'page' => 'gastos'));
            $meta   = array('page_title' => $page_title, 'bc' => $bc);
            
            $this->page_construct("gastos/add", $this->data, $meta);
        }else{
            echo lang('access_denied');
        }
    }

    function delete($id = NULL) {
        $acceso = false;
        if(!is_null($id)){
            $result = $this->db->select("date")->where(id,$id)->get("purchases")->result_array();
            foreach($result as $r){
                $la_fecha = substr($r["date"],0,10);
            }
            $hoy = date("Y-m-d");

            if($la_fecha >= $hoy){
                $acceso = true;
            }
        }

        if($this->Admin){
            $acceso = true;
        }

        if ($acceso) {
        
            if(DEMO) {
                $this->session->set_flashdata('error', lang('disabled_in_demo'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
            }
            if ( ! $this->Admin) {
                $this->session->set_flashdata('error', lang('access_denied'));
                redirect('pos');
            }
            if ($this->input->get('id')) {
                $id = $this->input->get('id');
            }

            if ($this->gastos_model->deletePurchase($id)) {
                $this->session->set_flashdata('message', "Gastos eliminados");
                redirect('gastos');
            }
        }else{
            echo lang('access_denied');
        }
    }

    function suggestions($id = NULL) {
        if($id) {
            $row = $this->site->getProductByID($id);
            $row->qty = 1;
            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            echo json_encode($pr);
        }
        $term = $this->input->get('term', TRUE);
        $rows = $this->purchases_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

     /* ----------------------------------------------------------------- */

    function expenses($id = NULL) {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('expenses');
        $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('expenses')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('purchases/expenses', $this->data, $meta);

    }

    function gasto($cDesde=null, $cHasta=null){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('purchases');
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        
        $bc = array(array('link' => '#', 'page' => lang('purchases')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('gastos/index', $this->data, $meta);
    }

    function get_gastos(){
        $this->load->library('datatables');

        $this->datatables->select("tec_purchases.id, tec_purchases.store_id, tec_stores.state, DATE_FORMAT(tec_purchases.date, '%Y-%m-%d') as date, tec_purchases.tipoDoc, tec_purchases.nroDoc, tec_suppliers.name, tec_purchases.fec_emi_doc, tec_purchases.fec_venc_doc, 
            tec_purchases.cargo_servicio, tec_purchases.costo_tienda, tec_purchases.costo_banco, tec_purchases.total,
            (tec_purchases.total+tec_purchases.cargo_servicio) total_, tec_purchases.texto_supplier,
            datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc) as d_a_v,
            if(tec_purchases.costo_tienda + tec_purchases.costo_banco < tec_purchases.total+tec_purchases.cargo_servicio,
                if(datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc)>=4,
                    'yellow',
                    if(datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc) in (2,3),
                        'orange',
                        if(tec_purchases.fec_emi_doc = '0000-00-00','lightgrey','red')
                    )
                ),
                'lightgreen'
            ) as colores");
        
        $this->datatables->from('tec_purchases');
        $this->datatables->join('tec_stores','tec_purchases.store_id = tec_stores.id');
        $this->datatables->join('tec_suppliers','tec_purchases.supplier_id = tec_suppliers.id');

        // solo datos de caja grande:
        $this->datatables->where("tec_purchases.tipogasto",'gastos');        

        if(!$this->Admin){
            $this->datatables->where('store_id', $this->session->userdata('store_id'));
        }
        
        $cDesde = $this->input->post('desde');
        $cHasta = $this->input->post('hasta');
        
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0){
                $this->datatables->where('tec_purchases.date>=', $cDesde);
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0){
                $this->datatables->where('tec_purchases.date<=', $cHasta);
            }
        }

        $cad_editar = "<a href='" . site_url('gastos/edit/$1') . "' title='" . lang("edit_purchase") . "' class='tip btn btn-warning btn-xs'>
                        <i class='fa fa-edit'></i>
                    </a>";

        $cad_eliminar = "<a href='" . site_url('gastos/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_purchase') . "')\" title='" . lang("delete_purchase") . "' class='tip btn btn-danger btn-xs'>
                        <i class='fa fa-trash-o'></i>
                    </a>";

        $this->datatables->add_column("Actions","
            <div class='text-center'>
                <div class='btn-group'>
                    <a href='".site_url('gastos/view/$1')."' title='".lang('view_purchase')."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'>
                        <i class='fa fa-file-text-o'></i>
                    </a>" . ($this->Admin ? $cad_editar . $cad_eliminar : "") .
                "</div>
            </div>", "id");

        //$this->datatables->unset_column('tec_purchases.id');
        
        //echo $this->db->get_compiled_select("tec_purchases");
        echo $this->datatables->generate();
    }

    function expense_note($id = NULL) {
        if ( ! $this->Admin) {
            if($expense->created_by != $this->session->userdata('user_id')) {
                $this->session->set_flashdata('error', lang('access_denied'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'pos');
            }
        }

        $expense = $this->purchases_model->getExpenseByID($id);
        $this->data['user'] = $this->site->getUser($expense->created_by);
        $this->data['expense'] = $expense;
        $this->data['page_title'] = $this->lang->line("expense_note");
        $this->load->view($this->theme . 'purchases/expense_note', $this->data);

    }

    function add_expense() {
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        $this->load->helper('security');

        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $date = trim($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference') ? $this->input->post('reference') : $this->site->getReference('ex'),
                'amount' => $this->input->post('amount'),
                'created_by' => $this->session->userdata('user_id'),
                'store_id' => $this->session->userdata('store_id'),
                'note' => $this->input->post('note', TRUE)
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->tec->print_arrays($data);

        } elseif ($this->input->post('add_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addExpense($data)) {

            $this->session->set_flashdata('message', lang("expense_added"));
            redirect('purchases/gastos');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = lang('add_expense');
            $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => site_url('purchases/expenses'), 'page' => lang('expenses')), array('link' => '#', 'page' => lang('add_expense')));
            $meta = array('page_title' => lang('add_expense'), 'bc' => $bc);
            $this->page_construct('purchases/add_expense', $this->data, $meta);

        }
    }

/*
    function edit_expense($id = NULL) {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference', lang("reference"), 'required');
        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $date = trim($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount'),
                'note' => $this->input->post('note', TRUE)
            );
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->tec->print_arrays($data);

        } elseif ($this->input->post('edit_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->purchases_model->updateExpense($id, $data)) {
            $this->session->set_flashdata('message', lang("expense_updated"));
            redirect("purchases/expenses");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['expense'] = $this->purchases_model->getExpenseByID($id);
            $this->data['page_title'] = lang('edit_expense');
            $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => site_url('purchases/expenses'), 'page' => lang('expenses')), array('link' => '#', 'page' => lang('edit_expense')));
            $meta = array('page_title' => lang('edit_expense'), 'bc' => $bc);
            $this->page_construct('purchases/edit_expense', $this->data, $meta);

        }
    }
*/
    function delete_expense($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $expense = $this->purchases_model->getExpenseByID($id);
        if ($this->purchases_model->deleteExpense($id)) {
            if ($expense->attachment) {
                unlink($this->upload_path . $expense->attachment);
            }
            $this->session->set_flashdata('message', lang("expense_deleted"));
            redirect('purchases/expenses');
        }
    }

    function enviar_docu(){
        $this->load->view($this->theme . "purchases/v_enviar_docu");
    }

    function verificarUnicidad(){
        $supplier_id    = $_REQUEST["supplier"];
        $nroDoc         = $_REQUEST["nroDoc"];
        $this->db->select("id");
        $this->db->from("purchases");
        $this->db->where("supplier_id",$supplier_id);
        $this->db->where("nroDoc",$nroDoc);
        $query = $this->db->get();
        echo $query->num_rows();
    }

}
