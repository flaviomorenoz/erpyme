<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Deliverys extends MY_Controller
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
        //$this->load->model('gastos_model');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip';

    }

    function index($cDesde=null, $cHasta=null){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Deliverys";
        
        $bc = array(array('link' => '#', 'page' => "deliverys"));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('deliverys/index', $this->data, $meta);
    }

    function view($id = NULL) {
/*
        $this->data['purchase'] = $this->gastos_model->getPurchaseByID($id);
        $this->data['items'] = $this->gastos_model->getAllPurchaseItems($id);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = 'gastos';
        $this->load->view($this->theme.'gastos/view', $this->data);
*/
    }

    function get_deliverys(){
        //$cSql = "select * from tec_deliverys order by id";
        //$query = $this->db->queries($cSql);
        //echo $query->result();

        //die("Soberano");
        $this->load->library('datatables');

        $this->datatables->select("id, nombre_delivery, active");
        
        $this->datatables->from('tec_deliverys');
        //$this->datatables->join('tec_stores','tec_purchases.store_id = tec_stores.id');
        //$this->datatables->join('tec_suppliers','tec_purchases.supplier_id = tec_suppliers.id');

        // solo datos de caja grande:
        //$this->datatables->where("tec_deliverys.",'');        

        if(!$this->Admin){
            //$this->datatables->where('store_id', $this->session->userdata('store_id'));
        }
        
        //$cDesde = $this->input->post('desde');
        //$cHasta = $this->input->post('hasta');
        
        /*if(!is_null($cDesde)){
            if(strlen($cDesde)>0){
                $this->datatables->where('tec_purchases.date>=', $cDesde);
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0){
                $this->datatables->where('tec_purchases.date<=', $cHasta);
            }
        }*/

        $cad_editar = "<a href='" . site_url('deliverys/edit/$1') . "' title='" . "Editar" . "' class='tip btn btn-warning btn-xs'>
                        <i class='fa fa-edit'></i>
                    </a>";

        $cad_eliminar = "<a href='" . site_url('deliverys/delete/$1') . "' onClick=\"return confirm('" . "eliminar?" . "')\" title='" . "delete Delivery" . "' class='tip btn btn-danger btn-xs'>
                        <i class='fa fa-trash-o'></i>
                    </a>";

        $cad_view = "<a href='".site_url('deliverys/view/$1')."' title='".lang('view_delivery')."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'>
                        <i class='fa fa-file-text-o'></i>
                    </a>";

        $this->datatables->add_column("Actions","
            <div class='text-center'>
                <div class='btn-group'>" .
                    ($this->Admin ? $cad_editar . $cad_eliminar : "") .
                "</div>
            </div>", "id");

        //$this->datatables->unset_column('tec_purchases.id');
        
        //echo $this->db->get_compiled_select("tec_deliverys");
        //die();
        echo $this->datatables->generate();

    }

    function add() {

        if (!$this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        
        $page_title = 'Deliverys'; // 
        $this->data["tipogasto"]    = 'Deliverys';

        if(!isset($_POST["nombre_delivery"])){
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            //$this->data['suppliers']    = $this->site->getAllSuppliers();
            $this->data['page_title']   = $page_title;
            $this->data['Admin']        = ($this->session->userdata["group_id"] == '1' ? true : false);

            $bc     = array(
                array('link' => site_url('deliverys'),     'page' => lang('deliverys')), 
                array('link' => '#',                       'page' => lang('add')));
            $meta   = array('page_title' => $page_title, 'bc' => $bc);
            
            $this->page_construct('deliverys/add', $this->data, $meta);
        }else{
            // Grabacion ---------------------:

            $nombre_delivery = $_POST["nombre_delivery"];

            $data = array('nombre_delivery' => $nombre_delivery, 'active' => '1');

            if ($this->db->insert('deliverys',$data)){
                $this->data["message"] = "Se graba correctamente";
            }else{
                $this->data["error"] = "No se pudo grabar";
            }    
            
            //$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = "Deliverys";
                
            $bc = array(array('link' => '#', 'page' => "deliverys"));
            $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
                
            $this->page_construct('deliverys/index', $this->data, $meta);
        }
    }

    function delete($id){

        if($id){
            $data = array("id"=>$id);
            $this->db->delete("deliverys",$data);

            $this->data['page_title'] = "Deliverys";
            $this->data["message"] = "Se elimina correctamente.";
                
            $bc = array(array('link' => '#', 'page' => "deliverys"));
            $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
                
            $this->page_construct('deliverys/index', $this->data, $meta);
        }

    }

}
