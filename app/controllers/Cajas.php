<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Cajas extends MY_Controller
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

    function index($cDesde=null, $cHasta=null){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = 'Cajas';
        
        $bc                     = array(array('link' => '#', 'page' => "cajas"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        //$this->data['query']    = $this->inventarios_model->listar($fec_inv, $fec_desde, $store_id);       
         
        $this->page_construct('cajas/index', $this->data, $meta);
    }

    function add(){
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = 'Agregar Cuadre';
        
        $bc                     = array(array('link' => '#', 'page' => "Agregar Cuadre"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('cajas/add', $this->data, $meta);
    }

    function save(){
        $date 			= $_REQUEST['date'];
        $store_id 		= $_REQUEST['store_id'];
        $cash_in_hand 	= $_REQUEST['cash_in_hand'];

        /*$ar = array(
            'fecha' => $fecha,
            'store_id' => $store_id,
            'cash_in_hand' => $cash_in_hand
        );*/

        // Verificando que todavia no haya sido ingresado
        $cSql = "select id from tec_registers where date(date) = ? and store_id = ?";
        $query = $this->db->query($cSql,array($date,$store_id));
        //$cSql = "select id from tec_registers where date(date) = '{$date}' and store_id = '{$store_id}'";
        //die($cSql);
        
        $nC = 0;
        foreach($query->result() as $r){
            $nC++;
        }
        if($nC > 0){

            $cSql = "update tec_registers set cash_in_hand = ? where date(date) = ? and store_id = ?";

            if($this->db->query($cSql, array($cash_in_hand, $date, $store_id))){
                
                $this->session->set_flashdata('message', 'Se actualizó los datos correctamente.');
        
            }else{
                $this->session->set_flashdata('warning','No se puede grabar...consulte con sistemas'); 
            }
        }else{
            $this->session->set_flashdata('warning', 'No han iniciado caja este dia, no hay qué modificar...');
        }

        $this->data['page_title']   = 'Agregar Cuadre';
		$bc                     = array(array('link' => '#', 'page' => "Agregar Cuadre"));
		$meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('cajas/add', $this->data, $meta);
    }

    function view($id = NULL) {
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = 'Inventario';
       
        $this->load->view($this->theme.'inventarios/view', $this->data);
    }

}
