<?php defined('BASEPATH') OR exit('No direct script access allowed');
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors","1");

class Insumos extends MY_Controller
{
    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->model("insumos_model");
    }

    public function listar_insumos(){
    	$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
    	$bc 	= array(array('link' => '#', 'page' => lang('Insumos')));
    	$meta 	= array('page_title' => lang('Insumos / Stock'), 'bc' => $bc);
        if (isset($_REQUEST['rubro'])){
            $this->data["rubro"] = $_REQUEST["rubro"];
            //die("rubro:".$_REQUEST["rubro"]);
        }
    	$this->page_construct('insumos/index', $this->data, $meta);
    }

    public function get_insumos($rubro=null){
        $result_json      = $this->insumos_model->listar_insumos($rubro);
        echo $result_json;
    }

    public function agregar_insumos_(){
    	//$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
    	$bc = array(array('link' => '#', 'page' => lang('Insumos')));
    	$meta = array('page_title' => lang('Agregar Insumos'), 'bc' => $bc);
   		$this->page_construct('insumos/agregar_insumos', $this->data, $meta);
    }

    public function grabar_insumos(){

        $this->data["descPro"] = strtoupper($_REQUEST["descPro"]);
        $this->data["unidad"] = $_REQUEST["unidad"];
        $mensaje = "";
        $error = false;
        $this->insumos_model->grabar($mensaje, $error);
        $ar = array();
        $ar["rpta"] = $mensaje;
        $ar["error"] = $error;
    		
    	echo json_encode($ar);
    }

    public function eliminar(){
        $data = array();
        
        if (strlen($this->input->get("id"))>0){
            $id = $this->input->get("id");
            
            $bandera = false;
            // Verificando que no exista movimiento con ese insumo:
            $query = $this->db->select("product_id")->from("purchase_items")->where("product_id",$id)->get();

            if($query->num_rows() > 0){
                $bandera = true;
            }

            // Verifico que no exista en movimientos
            $query = $this->db->select("product_id")->from("movim")->where("product_id",$id)->get();

            if($query->num_rows() > 0){
                $bandera = true;
            }

            if($bandera){
                $data["rpta"] = "No se pudo eliminar, existen movimientos en Compras u otros";
                $data["error"] = true;

            }else{
                
                $query_i = $this->db->select('*')->from('recetas')->where('id_insumo',$id)->get();
                
                //echo $this->db->select('*')->from('recetas')->where('id_insumo',$id)->get_compiled_select();

                if($query_i->num_rows() > 0){
                    $data["rpta"] = "No se pudo eliminar, existen datos en Insumos";
                    $data["error"] = true;
                }else{
                    $this->db->delete('products', array('id' => $id));
                    $data["rpta"] = "Se elimina correctamente";
                    $data["error"] = false;
                }
            }
            echo json_encode($data);
        }
        return false;

    }

    public function modificar_insumos($id){
        $bc = array(array('link' => '#', 'page' => lang('Insumo')));
        $meta = array('page_title' => lang('Modificar Insumo'), 'bc' => $bc);
        $result = $this->db->select("id, name, unidad, inventariable, rubro")->where("id",$id)->get("products")->result_array();
        
        $this->data["id"]       = $id;

        foreach($result as $r){
            $this->data["name"]     = $r["name"];
            $this->data["unidad"]   = $r["unidad"];
            $this->data["inventariable"] = $r["inventariable"];
            $this->data["rubro"]    = $r["rubro"];
        }

        $this->page_construct('insumos/modificar_insumo', $this->data, $meta);
    }

    public function update_insumos(){
        $bc = array(array('link' => '#', 'page' => lang('Insumo')));
        $meta = array('page_title' => lang('Modificar Insumo'), 'bc' => $bc);

        $ar         = array();
        
        $ar["name"]     = $this->input->get("name");
        $ar["unidad"]   = $this->input->get("unidad");
        $ar["inventariable"]   = $this->input->get("inventariable");
        $ar["rubro"]    = $this->input->get("rubro");
        //var_dump($ar);
        //die($this->input->get("id"));

        $this->db->where("id",$this->input->get("id"));
        $this->db->update("products",$ar);
        
        $error = false;

        if($error){
            $this->data["message"] = "<div class='alert alert-danger'>" . lang("No se pudo grabar.") . "</div>";
            echo lang("No se pudo grabar.");
        }else{
            $this->data["message"] = "<div class='alert alert-success'>" . lang("Se grabó correctamente.") . "</div>";
            echo lang("Se grabó correctamente.");
        }

        //echo $this->data["message"];
    }

    public function anular(){  // significa colocar rubro = 0
        $id = $_REQUEST["id"];
        if(!is_null($id)){
            $ar = array("rubro"=>'0');
            $this->db->set($ar)->where("id",$id)->update("tec_products");
            echo "Se elimina correctamente. <a href=\"" . base_url("insumos/listar_insumos") . "\">Regresar</a>";
        }
        
        /*
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc     = array(array('link' => '#', 'page' => lang('Insumos')));
        $meta   = array('page_title' => lang('Insumos / Stock'), 'bc' => $bc);
        $this->page_construct('insumos/index', $this->data, $meta);
        */
    }

}