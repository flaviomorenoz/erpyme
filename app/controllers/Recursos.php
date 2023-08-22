<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Recursos extends MY_Controller
{

    public $engrama = "";

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
        $this->load->model('recursos_model');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip';

        $this->engrama = "<a href=\"" . site_url('recursos/ver_personal') . "\">Ver personal</a> | ".
            "<a href=\"" . site_url('recursos/agregar_personal') . "\">Registrar Personal</a> | ".
            "<a href=\"" . site_url('recursos/ver_contratos') . "\">Ver contratos</a> | ".
            "<a href=\"" . site_url('recursos/agregar_contratos') . "\">Registrar Contratos</a>";

    }

    function login(){
        $bandera = true;
        if(isset($_POST["txt_pass"])){
            $pass = $_POST["txt_pass"];
            if ($pass == '5lcdls5'){
                sleep(1);
                $this->data['page_title']   = $this->engrama; //"Ver personal"; 
                $this->data['engrama']      = $this->engrama;

                $bc                     = array(array('link' => '#', 'page' => "recursos"));
                $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
                
                $this->page_construct('recursos/ver_personal', $this->data, $meta);
            }else{
                $bandera = false;
            }
        }else{
            $bandera = false;
        }
            
        if(!$bandera){
            sleep(1);
            $this->data['page_title'] = "Recursos Humanos";

            $bc                     = array(array('link' => '#', 'page' => "recursos"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            
            $this->page_construct('recursos/login', $this->data, $meta);
        }
    }

    function ver_personal(){
        
        $this->data['page_title'] = $this->engrama; //"Ver Personal";

        $bc                     = array(array('link' => '#', 'page' => "recursos"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        //$this->data["query_personal"]   = $this->recursos_model->ver_personal();
        $this->data["engrama"]          = $this->engrama;
        $this->page_construct('recursos/ver_personal', $this->data, $meta);
    }

    function get_personal(){
        $this->load->library('datatables');
        
        $this->datatables->select("tec_personal.id, tec_personal.nombres, tec_personal.apellidos, tec_personal.tip_doc, tec_personal.documento, tec_personal.phone, tec_stores.state local, tec_personal.activo");
        $this->datatables->from("tec_personal");
        $this->datatables->join("tec_stores","tec_personal.store_id=tec_stores.id","left");

        $cad_editar         = "<a href='#' title='Editar' onclick='modificar($1)' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a>&nbsp;";
        $cad_eliminar       = "<a href='#' title='Eliminar' onclick='eliminar($1)' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a>&nbsp;";

        $this->datatables->add_column("Actions",
            "<div class='text-center'>
                <div class='btn-group'>"
                    . ($this->Admin ? $cad_editar . $cad_eliminar : "") .
                "</div>
            </div>", "id");

        $cads = $this->datatables->generate();
        $cads = (substr($cads,-4) == 'null' ? substr($cads,0,strlen($cads)-4) : $cads);
        echo $cads;
    }

    function agregar_personal(){

        if(isset($_POST["modo"])){
        
            if($_POST["modo"] == "insert"){
                if($this->recursos_model->agregar_personal()){
                    $message    = "Se graba satisfactoriamente";
                    $error      = "";
                }else{
                    $message    = "";
                    $error      = "No se pudo grabar, además verifique la unicidad del documento";
                }
            }else{
                if($this->recursos_model->actualizar_personal()){
                    $message    = "Se actualiza satisfactoriamente";
                    $error      = "";
                }else{
                    $message    = "";
                    $error      = "No se pudo actualizar";
                }
            }

            $this->data["message"]  = $message;
            $this->data["error"]    = $error;

        }elseif(isset($_REQUEST["id"])){

            $id = $_REQUEST["id"];
            $query = $this->db->select('tip_doc, documento, nombres, apellidos, phone, activo')->from("tec_personal")->where("id",$id)->get();
            foreach($query->result() as $r){
                // id tip_doc nombres documento phone
                $this->data["modo"]         = "update";
                $this->data["id"]           = $id;
                $this->data["tip_doc"]      = $r->tip_doc;
                $this->data["nombres"]      = $r->nombres;
                $this->data["apellidos"]    = $r->apellidos;
                $this->data["documento"]    = $r->documento;
                $this->data["phone"]        = $r->phone;
                $this->data["activo"]        = $r->activo;
            }
        }

        $this->data['page_title']   = "Agregar Personal"; 
        $this->data['engrama']      = $this->engrama;
        
        $bc                     = array(array('link' => '#', 'page' => "recursos"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('recursos/agregar_personal', $this->data, $meta);
    }

    function eliminar_personal(){
        if(isset($_REQUEST["id"])){
            $id = $_REQUEST["id"];
            if(strlen($id)>0){
                //die($this->db->where("id",$id)->get_compiled_delete("tec_personal"));

                $this->db->where("id",$id)->delete("tec_personal");
                
                $this->data["message"] = "Se elimina correctamente.";
            }else{
                $this->data["error"] = "No se pudo eliminar";
            }
        }else{
            $this->data["error"] = "No se pudo eliminar.";
        }
        
        $this->data['page_title']   = "Ver Personal"; 
        $this->data['engrama']      = $this->engrama;
        
        $bc                     = array(array('link' => '#', 'page' => "recursos"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->data["query_personal"] = $this->recursos_model->ver_personal();
        $this->page_construct('recursos/ver_personal', $this->data, $meta);

    }

    function ver_contratos(){
        
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = "Ver contratos"; 
        $this->data['engrama']      = $this->engrama;
        
        $bc                     = array(array('link' => '#', 'page' => "recursos"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('recursos/ver_contratos', $this->data, $meta);
    }

    function get_contratos(){
        $this->load->library('datatables');
        
        $this->datatables->select("tec_contratos.id, tec_contratos.fec_ini, tec_contratos.fec_fin, tec_contratos.activo, tec_contratos.id_personal, tec_personal.nombres, tec_personal.apellidos, tec_contratos.sueldo");
        $this->datatables->from("tec_contratos");
        $this->datatables->join("tec_personal","tec_contratos.id_personal = tec_personal.id");

        $cad_editar         = "<a href='#' title='Editar' onclick='modificar($1)' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a>&nbsp;";
        $cad_eliminar       = "<a href='#' title='Eliminar' onclick='eliminar($1)' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a>&nbsp;";

        $this->datatables->add_column("Actions",
            "<div class='text-center'>
                <div class='btn-group'>"
                    . ($this->Admin ? $cad_editar . $cad_eliminar : "") .
                "</div>
            </div>", "id");

        echo $this->datatables->generate();
    }

    function agregar_contratos(){

        
        if(isset($_POST["modo"])){

            $modo = $_POST["modo"];
            
            if($modo == "insert"){
                if($this->recursos_model->agregar_contratos()){
                    $message    = "Se graba satisfactoriamente";
                    $error      = "";
                }else{
                    $message    = "";
                    $error      = "No se pudo grabar";
                }
                $this->data["message"]  = $message;
                $this->data["error"]    = $error;
            
            }elseif($modo == "update"){

                if($this->recursos_model->actualizar_contratos()){
                    $message    = "Se actualiza satisfactoriamente";
                    $error      = "";
                }else{
                    $message    = "";
                    $error      = "No se pudo grabar";
                }
                $this->data["message"]  = $message;
                $this->data["error"]    = $error;

            }

        }elseif(isset($_REQUEST["id"])){
            
            $id = $_REQUEST["id"];
            $query = $this->db->select('id, fec_ini, fec_fin, activo, id_personal, sueldo')->from("tec_contratos")->where("id",$id)->get();
            foreach($query->result() as $r){
                // a.id, a.fec_ini, a.fec_fin, a.activo, a.id_personal, b.nombres, a.sueldo
                $this->data["id"] 			= $r->id;
                $this->data["fec_ini"] 		= $r->fec_ini;
                $this->data["fec_fin"] 		= $r->fec_fin;
                $this->data["activo"] 		= $r->activo;
                $this->data["id_personal"] 	= $r->id_personal;
                $this->data["sueldo"] 		= $r->sueldo;
                //die("sueldo:" . $this->data["sueldo"]);
            }
        }

        $this->data['page_title']   = "Agregar Contratos"; 
        $this->data['engrama']      = $this->engrama;
        
        $bc                     = array(array('link' => '#', 'page' => "recursos"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('recursos/agregar_contratos', $this->data, $meta);
    }

    function getContratos(){
        $id_personal = $_REQUEST["id"];
        $cSql = "select id_personal from tec_contratos where id_personal = {$id_personal}";
        //die($cSql);
        $query = $this->db->query($cSql);
        $rpta = "0";
        foreach($query->result() as $r){
            $rpta = "1";
        }
        echo $rpta;
    }

    function eliminar_contratos(){
        if(isset($_REQUEST["id"])){
            $id = $_REQUEST["id"];
            if(strlen($id)>0){
                //die($this->db->where("id",$id)->get_compiled_delete("tec_personal"));

                $this->db->where("id",$id)->delete("tec_contratos");
                
                //$this->data["message"] = "Se elimina correctamente.";
                echo "0";
            }else{
                //$this->data["error"] = "No se pudo eliminar";
                echo "1";
            }
        }else{
            //$this->data["error"] = "No se pudo eliminar.";
            echo "1";
        }
    }
}
