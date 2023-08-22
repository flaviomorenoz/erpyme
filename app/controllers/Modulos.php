<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Modulos extends MY_Controller
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
        //$this->load->model('inventarios_model');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip';

    }

    function index(){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Módulos del Sistema";
        
        $bc = array(array('link' => '#', 'page' => "modulos"));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('modulos/index', $this->data, $meta);
    }


    function add() {
        echo "En proceso...";
    }
}