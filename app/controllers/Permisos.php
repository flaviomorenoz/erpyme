<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Permisos extends MY_Controller
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
        $this->data['page_title'] = "Permisos x Perfil";
        
        $bc = array(array('link' => '#', 'page' => "permisos"));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('permisos/index', $this->data, $meta);
    }


    function editar($modulo_id) {
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Permisos x Perfil";
        
        $bc = array(array('link' => '#', 'page' => "permisos"));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $cSql = "select id, name, description from tec_groups order by id;";
        $this->data["query_groups"] = $this->db->query($cSql);
        $this->data["modulo_id"]    = $modulo_id;

        $this->page_construct('permisos/editar', $this->data, $meta);
    }
}