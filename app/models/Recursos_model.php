<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Recursos_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function ver_personal(){
        $query = $this->db->select("id, tip_doc, documento, nombres, apellidos, phone, activo") 
            ->from("tec_personal")
            ->order_by(array("apellidos"))->get();
        return $query;
        //             concat('<a href=\'#\' onclick=\'modificar(',id,')\'><i class=\'glyphicon glyphicon-edit\'></i></a>&nbsp;&nbsp;
        //    <a href=\'#\' onclick=\'eliminar(',id,')\'><i class=\'glyphicon glyphicon-remove\'></i></a>')
    }

    function agregar_personal(){
        $tip_doc   = $_POST["tip_doc"];
        $nombres    = $_POST["nombres"];
        $apellidos  = $_POST["apellidos"];
        $documento  = $_POST["documento"];
        $phone      = $_POST["phone"];
        $activo     = $_POST["activo"];
        $store_id   = $_POST["store_id"];

        $ar = array();
        $ar["tip_doc"]     = $tip_doc;
        $ar["nombres"]      = $nombres;
        $ar["apellidos"]    = $apellidos;
        $ar["documento"]    = $documento;
        $ar["phone"]        = $phone;
        $ar["activo"]       = $activo;
        $ar["store_id"]     = $store_id;
        
        //echo($this->db->set($ar)->get_compiled_insert("tec_personal"));
        //die();

        // Verificando la unicidad del documento
        $cSql = "select id from tec_personal where documento = ?";
        $query = $this->db->query($cSql, array($documento));
        $canti=0;
        foreach($query->result() as $r){
            $canti++;
        }

        if($canti == 0){
            if($this->db->set($ar)->insert("tec_personal")){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function actualizar_personal(){
        $id         = $_POST["id"];
        $tip_doc    = $_POST["tip_doc"];
        $nombres    = $_POST["nombres"];
        $apellidos  = $_POST["apellidos"];
        $documento  = $_POST["documento"];
        $phone      = $_POST["phone"];
        //$sueldo     = $_POST["sueldo"];
        $activo     = $_POST["activo"];
        $store_id   = $_POST["store_id"];

        $ar = array();
        //$ar["id"]           = $id;
        $ar["tip_doc"]      = $tip_doc;
        $ar["nombres"]      = $nombres;
        $ar["apellidos"]    = $apellidos;
        $ar["documento"]    = $documento;
        $ar["phone"]        = $phone;
        $ar["activo"]       = $activo;
        $ar["store_id"]     = $store_id;
        
        //echo($this->db->set($ar)->where("id",$id)->get_compiled_update("tec_personal"));
        //die();

        if($this->db->set($ar)->where("id",$id)->update("tec_personal")){
            return true;
        }else{
            return false;
        }
    }

    public function ver_contratos(){
        
        // Actualmente no se va a utilizar
        $query = $this->db->select("a.id, a.fec_ini, a.fec_fin, a.activo, a.id_personal, b.nombres, b.apellidos, a.sueldo, 
            concat('<a href=\'#\' onclick=\'modificar(',a.id,')\'><i class=\'glyphicon glyphicon-edit\'></i></a>&nbsp;&nbsp;
            <a href=\'#\' onclick=\'eliminar(',a.id,')\'><i class=\'glyphicon glyphicon-remove\'></i></a>') op")
            ->from("tec_contratos a")
            ->join("tec_personal b","a.id_personal = b.id","left")
            ->order_by(array("b.nombres"))->get();


        return $query;
    }

    function agregar_contratos(){
        
        $ar = array();
        $fec_ini        =$_POST["fec_ini"];
        $fec_fin        =$_POST["fec_fin"];
        $activo         =$_POST["activo"];
        $id_personal    =$_POST["id_personal"];
        $sueldo         =$_POST["sueldo"];

        $ar["fec_ini"]  = $fec_ini;
        $ar["fec_fin"]  = $fec_fin;
        $ar["activo"]   = $activo;
        $ar["id_personal"] = $id_personal;
        $ar["sueldo"]   = $sueldo;

        //die($this->db->set($ar)->get_compiled_insert("tec_contratos"));

        if($this->db->set($ar)->insert("tec_contratos")){
            return true;
        }else{
            return false;
        }
    }

    function actualizar_contratos(){
        
        $ar = array();
        $id             = $_POST["id"];
        $fec_ini        = $_POST["fec_ini"];
        $fec_fin        = $_POST["fec_fin"];
        $activo         = $_POST["activo"];
        $id_personal    = $_POST["id_personal"];
        $sueldo         = $_POST["sueldo"];

        $ar["fec_ini"]  = $fec_ini;
        $ar["fec_fin"]  = $fec_fin;
        $ar["activo"]   = $activo;
        $ar["id_personal"] = $id_personal;
        $ar["sueldo"]   = $sueldo;

        //print_r($ar);
        //die($this->db->set($ar)->where("id", $id)->get_compiled_update("tec_contratos"));

        if($this->db->set($ar)->where("id", $id)->update("tec_contratos")){
            return true;
        }else{
            return false;
        }
        
    }

    function personal_sin_contrato(){
        $query = $this->db->query("select a.id, concat(a.nombres,' ',a.apellidos) nombres, a.phone, a.tip_doc, a.documento from tec_personal a
            left join (
              select id_personal from tec_contratos
              where fec_fin > curdate()
            ) b on a.id = b.id_personal
            where b.id_personal is null");
        return $query;
    }

}
