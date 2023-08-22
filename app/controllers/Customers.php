<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('customers_model');
    }

    function index() {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('customers');
        $bc = array(array('link' => '#', 'page' => lang('customers')));
        $meta = array('page_title' => lang('customers'), 'bc' => $bc);
        $this->page_construct('customers/index', $this->data, $meta);
    }

    function get_customers() {

        $this->load->library('datatables');
        $this->datatables
        ->select("id, name, phone, email, cf1, cf2")
        ->from("customers")
        ->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='" . site_url('customers/edit/$1') . "' class='tip btn btn-warning btn-xs' title='".$this->lang->line("edit_customer")."'><i class='fa fa-edit'></i></a> <a href='" . site_url('customers/delete/$1') . "' onClick=\"return confirm('". $this->lang->line('alert_x_customer') ."')\" class='tip btn btn-danger btn-xs' title='".$this->lang->line("delete_customer")."'><i class='fa fa-trash-o'></i></a></div></div>", "id")
        ->unset_column('id');

        echo $this->datatables->generate();

    }

    function add() {

        if(!isset($_GET["modo"])){
            
            if(!isset($_POST["name"])){
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->data['page_title'] = lang('add_customer');
                $bc = array(array('link' => site_url('customers'), 'page' => lang('customers')), array('link' => '#', 'page' => lang('add_customer')));
                $meta = array('page_title' => lang('add_customer'), 'bc' => $bc);
                $this->page_construct('customers/add', $this->data, $meta);
            }else{

                $this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
                $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'valid_email');

                $cf1 = $this->input->post('cf1');
                $cf2 = $this->input->post('cf2');

                $flag_red = false;
                $len_cf1 = strlen($cf1);
                $len_cf2 = strlen($cf2);
                if ($len_cf1 > 0){
                    if($len_cf1 != 8){
                        $flag_red = true;
                    }
                }
                if ($len_cf2 > 0){
                    if($len_cf2 != 11){
                        $flag_red = true;
                    }
                }

                if ($this->form_validation->run() == true) {

                    $data = array('name' => $this->input->post('name'),
                        'email' => $this->input->post('email'),
                        'phone' => $this->input->post('phone'),
                        'cf1' => $this->input->post('cf1'),
                        'cf2' => $this->input->post('cf2'),
                    );
                }

                if ( $this->form_validation->run() == true && $flag_red == false){ 
                    if($cid = $this->customers_model->addCustomer($data)) {

                        if($this->input->is_ajax_request()) {
                            echo json_encode(array('status' => 'success', 'msg' =>  $this->lang->line("customer_added"), 'id' => $cid, 'val' => $data['name']));
                            die();
                        }
                        $this->session->set_flashdata('message', $this->lang->line("customer_added"));
                        redirect("customers");

                    } else {
                        /*
                        if($this->input->is_ajax_request()) {
                            echo json_encode(array('status' => 'failed', 'msg' => validation_errors())); die();
                        }

                        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                        $this->data['page_title'] = lang('add_customer');
                        $bc = array(array('link' => site_url('customers'), 'page' => lang('customers')), array('link' => '#', 'page' => lang('add_customer')));
                        $meta = array('page_title' => lang('add_customer'), 'bc' => $bc);
                        $this->page_construct('customers/add', $this->data, $meta);
                        */
                    }
                }else{
                    //if($this->input->is_ajax_request()) {
                    //    echo json_encode(array('status' => 'failed', 'msg' => 'Corrija datos..DNI 8 digitos, RUC 11 digitos')); die();
                    //}

                    //$this->data['error'] = (validation_errors()) ?  : $this->session->flashdata('error');
                    $this->data['error'] = (validation_errors()) ? validation_errors() : 'Corrija datos..DNI 8 digitos, RUC 11 digitos';
                    $this->data['page_title'] = lang('add_customer');
                    $bc = array(array('link' => site_url('customers'), 'page' => lang('customers')), array('link' => '#', 'page' => lang('add_customer')));
                    $meta = array('page_title' => lang('add_customer'), 'bc' => $bc);
                    $this->page_construct('customers/add', $this->data, $meta);
                }
            }    
        }else{
            $name   = $_GET["name"];
            $email  = $_GET["email"];
            $phone  = $_GET["phone"];
            $cf1    = $_GET["cf1"];
            $cf2    = $_GET["cf2"];

            $data = array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'cf1' => $cf1,
                'cf2' => $cf2
            );

            if($cid = $this->customers_model->addCustomer($data)) {
                echo $cid;
            } else {
                echo "0";
            }
        }
    }

    function edit($id = NULL) {
        //if (!$this->Admin) {
        //    $this->session->set_flashdata('error', $this->lang->line('access_denied'));
        //    redirect('pos');
        //}
        if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        $this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'valid_email');

        if ($this->form_validation->run() == true) {

            $data = array('name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2')
            );

        }

        if ( $this->form_validation->run() == true && $this->customers_model->updateCustomer($id, $data)) {

            $this->session->set_flashdata('message', $this->lang->line("customer_updated"));
            redirect("customers");

        } else {

            $this->data['customer'] = $this->customers_model->getCustomerByID($id);
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('edit_customer');
            $bc = array(array('link' => site_url('customers'), 'page' => lang('customers')), array('link' => '#', 'page' => lang('edit_customer')));
            $meta = array('page_title' => lang('edit_customer'), 'bc' => $bc);
            $this->page_construct('customers/edit', $this->data, $meta);

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

        if ( $this->customers_model->deleteCustomer($id) )
        {
            $this->session->set_flashdata('message', lang("customer_deleted"));
            redirect("customers");
        }

    }


}
