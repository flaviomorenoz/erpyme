<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//error_reporting(E_ALL & ~E_NOTICE & ~E_PARSE & ~E_WARNING & ~E_ERROR);
//ini_set('display_errors', '1');

class Receta extends MY_Controller{

    function __construct(){
        //die("Dua Lipa");
        parent::__construct();
        $this->load->model('receta_model');
    }

    function agregar(){
    	if(isset($_REQUEST['nombreReceta'])){

            //die("Aguila 1");
            // Se agrega la receta en una sola tabla
            $ar = array();
                 
            $ar['product_id']           = $_REQUEST['product_id']; // id del plato a hacer la receta
            $ar['nombreReceta']         = $_REQUEST['nombreReceta'];
            $ar['id_insumo']            = $_REQUEST['id_insumo'];
            $ar['cantidadReceta']       = $_REQUEST['cantidadReceta'];

            echo $this->receta_model->agregar_receta($ar);

    	}else{
            //die("Aguila 2");
            $this->data['content']         	= "receta/agregar";
	        
            //$this->_render_page('inventario/inv_templo', $this->data);

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers'] = $this->site->getAllSuppliers();
            $this->data['page_title'] = lang('agregar_receta');
            $bc = array(array('link' => site_url('recetas'), 'page' => lang('recetas')), array('link' => '#', 'page' => lang('agregar_receta')));
            $meta = array('page_title' => lang('agregar_receta'), 'bc' => $bc);
            $this->page_construct('recetas/v_agregar', $this->data, $meta);
		}
    }

    function rep_recetas(){
        $id = $_REQUEST["nomRec"];
        if(strlen($id)>0){
            $result     = $this->receta_model->rep_recetas($id);
            $data       = array('result'=>$result);
            $this->load->view($this->theme . 'recetas/v_lista_insumos_receta',$data);
        }else{
            $result     = $this->receta_model->rep_recetas();
            
            //foreach($result as $r){
            //    echo $r["id"] . $r["nombreReceta"] . $r["id_insumo"] . "<br>"; // name cantidadReceta
            //}
            
            //$data       = array('result'=>$result);
            //$this->load->view($this->theme . 'recetas/v_lista_insumos_receta',$data);
            $bc         = array(array('link' => site_url('recetas'), 'page' => lang('recetas')), array('link' => '#', 'page' => lang('agregar_receta')));
            $meta       = array('page_title' => lang('recipe'), 'bc' => $bc);
            $this->data["result"] = $result;
            $this->page_construct('recetas/v_lista_insumos_receta', $this->data, $meta);
        }
    }

    function rep_recetas2(){
/*
        $id = $_REQUEST["nomRec"];
        if(strlen($id)>0){
            $result     = $this->receta_model->rep_recetas($id);
            //$data       = array('result'=>$result);
            //$this->load->view($this->theme . 'recetas/v_lista_insumos_receta',$data);
            //echo str_replace('\n','<br>',print_r($result,true));
        }else{
            echo "No se encuentra el dato";
        }        
*/
        $product_id = $_REQUEST["nomRec"];
        $cSql = "select a.id, a.nombreReceta, a.id_insumo, b.code, b.name, if(b.unidad='KILO','gramos',b.unidad) unidad, a.cantidadReceta, a.product_id  
        from tec_recetas a
        inner join tec_products b on a.id_insumo = b.id
        where a.product_id = ?";
        $query  = $this->db->query($cSql,array($product_id));
        $result = $query->result_array();
        
        //$cSql = "select * from tec_products where id = ?";
        $cSql = "select nombreReceta name from tec_recetas where product_id = ?";
        $query = $this->db->query($cSql, array($product_id));
        foreach($query->result() as $r){
            $titulo = $r->name;    
        }

        $aru = array($titulo, $result);

        echo json_encode($aru);
    }

    function eliminar_receta(){
        $id = $_REQUEST["id"];
        if(!is_null($id)){
            $this->receta_model->eliminar_receta($id);
        }
        $this->rep_recetas();
    }

    function get_recetas(){
        
        $store_id = 1;
        
        $this->load->library('datatables');
        /*if ($this->Admin) {
            $cSubquery = "select a.product_id, c.name producto from tec_products";
            $this->datatables->select("select a.product_id, c.name producto");
            $this->datatables->from("tec_recetas a");
            $this->datatables->join("","c on a.product_id=c.id");
            $this->datatables->group_by("a.product_id, c.name");
        } else {
        }*/

        /*$this->datatables->select("tec_recetas.product_id, tec_recetas.nombreReceta, count(tec_recetas.nombreReceta) cant, concat('<a href=\'#\' onclick=\'mostrar_receta(', tec_recetas.product_id, ')\'>Ver</a>') acciones")
            ->from('tec_recetas')
            ->group_by('tec_recetas.product_id, tec_recetas.nombreReceta');*/

        /*$cSubquery = "SELECT `tec_recetas`.`product_id`, `tec_recetas`.`nombreReceta`, count(tec_recetas.nombreReceta) cant, concat('<a href=\'#\' onclick=\'mostrar_receta(', `tec_recetas`.`product_id`, ')\'>Ver</a>') acciones FROM `tec_recetas` GROUP BY `tec_recetas`.`product_id`, `tec_recetas`.`nombreReceta`";

        $this->db->select("x.`product_id`, x.`nombreReceta`, count(*) cant, concat('<a href=\'#\' onclick=\'mostrar_receta(', x.`product_id`, ')\'>Ver</a>') acciones")
            ->from("recetas as x")
            ->group_by("x.`product_id`, x.`nombreReceta`");

        $cSubquery = $this->db->get_compiled_select();
        echo $cSubquery;*/

        $this->datatables->select("product_id, nombreReceta")->from("recetas")->group_by("product_id, nombreReceta");

        echo $this->datatables->generate();
    }

    function mostrar_receta(){
        $product_id = $_REQUEST["product_id"];
        $cSql = "select a.id, a.nombreReceta, a.id_insumo, b.code, b.name, if(b.unidad='KILO','gramos',b.unidad) unidad, a.cantidadReceta, a.product_id  
        from tec_recetas a
        inner join tec_products b on a.id_insumo = b.id
        where a.product_id = ?";
        $query  = $this->db->query($cSql,array($product_id));
        $result = $query->result_array();
        
        //$cSql = "select * from tec_products where id = ?";
        $cSql = "select nombreReceta name from tec_recetas where product_id = ?";
        $query = $this->db->query($cSql, array($product_id));
        foreach($query->result() as $r){
            $titulo = $r->name;    
        }

        $aru = array($titulo, $result);

        echo json_encode($aru);
    }
}
?>