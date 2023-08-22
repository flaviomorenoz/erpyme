<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receta_model extends CI_Model{

    public function __construct(){
        parent::__construct();
        $this->load->database(); 
    }

    function rep_recetas($id=""){
    		
        $query = $this->db->select("recetas.id, recetas.nombreReceta, recetas.id_insumo, products.name, recetas.cantidadReceta")
            ->from("recetas")
            ->join("products","recetas.id_insumo = products.id")
            ->order_by("nombreReceta","asc");

        if(strlen($id)>0){
            $this->db->where("product_id =",$id);
        }
        
        $result = $this->db->get()->result_array();

    	return $result;
    }

    function rep_recetas_json($nomRec){
        return json_encode($this->rep_recetas($nomRec));
    }

    function combo_producto(){
        $cSql = "select id, concat(name,' (',unidad,')') name from tec_products where category_id = 7 order by name";
        $result = $this->db->query($cSql)->result();
        $cad = "<select id=\"idPro\" name=\"idPro\" class=\"form-control\" placeholder=\"producto\">";
        foreach($result as $r){
            $cad .= $this->fm->option($r->id, $r->name);
        }
        $cad .= "</select>";
        return $cad;
    }

    function agregar_receta($ar){
        //$cad = str_replace("\n", "<br>", print_r($_SESSION,true));
        //die($cad);
        
        if(!$this->db->insert("recetas", $ar)){
            $ar_error = $this->db->error();
            $ar_rpta["rpta"]    = false;
            $ar_rpta["message"] = $ar_error["message"];

        }else{
            $id_inmerso = $this->db->insert_id();
            
            $query = $this->db->select("name")->from("products")->where("id", $ar["id_insumo"])->get();
            foreach($query->result() as $r){ $name = $r->name; }

            $this->db->set("user", $_SESSION["username"]);
            $this->db->set("accion", "insert");
            $this->db->set("id_inmerso", $id_inmerso);
            $this->db->set("fecha_hora", date("Y-m-d H:i:s"));
            $nombreReceta = $ar["nombreReceta"];
            $this->db->set("obs", "se agrega un item {$name} en Receta {$nombreReceta}");
            $this->db->insert("tec_audi_recetas");
  
            $ar_rpta["rpta"]    = true;
            $ar_rpta["message"] = "Grabaccion Correcta de ingrediente de receta!";
        }
        return json_encode($ar_rpta);

    }

    function eliminar_receta($id){
        $query = $this->db->select("recetas.nombreReceta, recetas.id_insumo, recetas.cantidadReceta, products.name")->from("recetas")->join("products","recetas.id_insumo = products.id","left")->where("recetas.id",$id)->get();
        foreach($query->result() as $r){ 
            $nombreReceta   = $r->nombreReceta;
            $id_insumo      = $r->id_insumo;
            $cantidadReceta = $r->cantidadReceta;
            $name           = $r->name;
        }

        $this->db->delete('recetas', array('id' => $id));

        $this->db->set("user", $_SESSION["username"]);
        $this->db->set("accion", "delete");
        $this->db->set("id_inmerso", $id);
        $this->db->set("fecha_hora", date("Y-m-d H:i:s"));
        $this->db->set("obs", "Se elimina el item {$id_insumo} {$name} de la receta {$nombreReceta}");
        $this->db->insert("tec_audi_recetas");

    }
}
?>