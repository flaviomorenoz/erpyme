<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }
        
        /*if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }*/

        $this->load->library('form_validation');
        $this->load->model('suppliers_model');
    }

    function index() {

        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title']   = lang('suppliers');
        $bc     = array(array('link'    => '#', 'page' => lang('suppliers')));
        $meta   = array('page_title'    => lang('suppliers'), 'bc' => $bc);
        $this->page_construct('suppliers/index', $this->data, $meta);
    }

    function get_suppliers() {

        $this->load->library('datatables');
        $this->datatables
        ->select("id, name, contact, phone, email, cf1, cf2, activo")
        ->from("suppliers")
        ->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('suppliers/edit/$1') . "' class='tip btn btn-warning btn-xs' title='".$this->lang->line("edit_supplier")."'><i class='fa fa-edit'></i></a> <a href='" . site_url('suppliers/delete/$1') . "' onClick=\"return confirm('". $this->lang->line('alert_x_supplier') ."')\" class='tip btn btn-danger btn-xs' title='".$this->lang->line("delete_supplier")."'><i class='fa fa-trash-o'></i></a></div></div>", "id")
        ->unset_column('id');

        echo $this->datatables->generate();

    }

    function add() {

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        if(!isset($_REQUEST["modal_sup"])){

            $this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
            $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'valid_email');
            $this->form_validation->set_rules('cf2',    $this->lang->line("cf2"), 'trim|required|min_length[11]|max_length[11]');

            $nCan = 0;
            if ($this->form_validation->run() == true){
                $data = array('name' => $this->input->post('name'),
                    'email'     => $this->input->post('email'),
                    'phone'     => $this->input->post('phone'),
                    'cf2'       => $this->input->post('cf2'),
                    'contact'   => $this->input->post('contact'),
                    'phone2'     => $this->input->post('phone2')
                );

                $name = $this->input->post('name');
            
                // Verificando que su descripcion es unica
                $cSql = "select count(*) cantidad from tec_suppliers where name = '{$name}'";
                
                $query = $this->db->query($cSql);

                foreach($query->result() as $r){
                    $nCan = $r->cantidad * 1;
                }
            }

            if($nCan == 0){

                if ( $this->form_validation->run() == true && $cid = $this->suppliers_model->addSupplier($data)) {

                    if($this->input->is_ajax_request()) {
                        echo json_encode(array('status' => 'success', 'msg' =>  $this->lang->line("supplier_added"), 'id' => $cid, 'val' => $data['name']));
                        die();
                    }
                    $this->session->set_flashdata('message', $this->lang->line("supplier_added"));
                    redirect("suppliers");

                } else {
                    if($this->input->is_ajax_request()) {
                        echo json_encode(array('status' => 'failed', 'msg' => validation_errors())); die();
                    }

                    $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                    $this->data['page_title'] = lang('add_supplier');
                    $bc = array(array('link' => site_url('suppliers'), 'page' => lang('suppliers')), array('link' => '#', 'page' => lang('add_supplier')));
                    $meta = array('page_title' => lang('add_supplier'), 'bc' => $bc);
                    $this->page_construct('suppliers/add', $this->data, $meta);

                }
            }else{

                $this->data['error'] = "Ya existe un proveedor con la misma descripcion";
                $this->data['page_title'] = lang('add_supplier');
                $bc = array(array('link' => site_url('suppliers'), 'page' => lang('suppliers')), array('link' => '#', 'page' => lang('add_supplier')));
                $meta = array('page_title' => lang('add_supplier'), 'bc' => $bc);
                $this->page_construct('suppliers/add', $this->data, $meta);

            }
        }else{  // grabacion opcion (formulario modal)

            $name       = $_REQUEST["name"];
            $email      = $_REQUEST["email"];
            $contact    = $_REQUEST["contact"];
            $phone      = $_REQUEST["phone"];
            $cf1        = $_REQUEST["cf1"];
            $cf2        = $_REQUEST["cf2"];
            $phone2      = $_REQUEST["phone2"];

            $data       =array(
                "name"=>$name,
                "email"=>(is_null($email)?"":$email),
                "contact"=>(is_null($contact)?"":$contact),
                "phone"=>(is_null($phone)?"":$phone),
                "cf1"=>(is_null($cf1)?"":$cf1),
                "cf2"=>(is_null($cf2)?"":$cf2),
                "phone2"=>(is_null($phone2)?"":$phone2)
            );

            // Verificando que su descripcion es unica
            $cSql = "select count(*) cantidad from tec_suppliers where name = '{$name}'";
            
            $query = $this->db->query($cSql);

            foreach($query->result() as $r){
                $nCan = $r->cantidad * 1;
            }

            if($nCan == 0){
                if ($this->db->insert("tec_suppliers",$data)){
                    
                    //echo "Se guardó el Proveedor";
                    $ar_rpta = array(
                        "valor" => $this->db->insert_id(),
                        "texto" => $name,
                        "rpta"  => 1
                    );
                    
                }else{
                    //$ar_error = $this->db->error();
                    //echo $ar_error["message"];
                    $ar_rpta = array(
                        "rpta" => 0
                    );
                }
                
            }else{
                $ar_rpta = array(
                    "rpta" => 0
                );
            }
            echo json_encode($ar_rpta);
        }
    }

    function edit($id = NULL) {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', $this->lang->line('access_denied'));
            redirect('pos');
        }
        if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        $this->form_validation->set_rules('name',   $this->lang->line("name"), 'required');
        $this->form_validation->set_rules('email',  $this->lang->line("email_address"), 'valid_email');
        $this->form_validation->set_rules('cf2',    $this->lang->line("cf2"), 'required');

        if ($this->form_validation->run() == true) {
            $data = array('name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'cf2'   => $this->input->post('cf2'),
                'contact'=> $this->input->post('contact'),
                'phone2' => $this->input->post('phone2'),
                'activo' => $this->input->post('activo')
            );
        }

        if ( $this->form_validation->run() == true && $this->suppliers_model->updateSupplier($id, $data)) {

            $this->session->set_flashdata('message', $this->lang->line("supplier_updated"));
            redirect("suppliers");

        } else {

            $this->data['supplier']     = $this->suppliers_model->getSupplierByID($id);
            $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title']   = lang('edit_supplier');
            $bc = array(array('link' => site_url('suppliers'), 'page' => lang('suppliers')), array('link' => '#', 'page' => lang('edit_supplier')));
            $meta = array('page_title' => lang('edit_supplier'), 'bc' => $bc);
            $this->page_construct('suppliers/edit', $this->data, $meta);
        }
    }

    function delete($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', $this->lang->line("disabled_in_demo"));
            redirect('pos');
        }

        if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        if (!$this->Admin)
        {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect('pos');
        }

        if ( $this->suppliers_model->deleteSupplier($id) )
        {
            $this->session->set_flashdata('message', lang("supplier_deleted"));
            
        }else{
            $this->session->set_flashdata('error', lang("No se puede eliminar, está asociado con un documento."));
        }
        redirect("suppliers");

    }


}
