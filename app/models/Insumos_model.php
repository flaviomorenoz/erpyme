<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Insumos_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    public function listar_insumos($rubro=null){

        $this->load->library('datatables');

        // **** Haciendo la consulta sobre la tabla nueva ****************************

        $this->datatables->select("products.id, products.name, products.unidad, products.rubro, rubros.descrip");
        $this->datatables->from("products");
        $this->datatables->join('rubros','products.rubro = rubros.id','left');
        $this->datatables->where("products.category_id",7);
        $this->datatables->where("upper(substr(tec_products.name,1,1)) in ('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','Ñ','V','W','X','Y','Z')");
        $this->datatables->where("products.rubro!=0");

        $cadena = "<a href='#' title='Anular' class='' onclick='anular($1)'><i class='glyphicon glyphicon-remove' style='color:gray'></i></a>&nbsp;&nbsp;";

        $cadena .= "<a href='".site_url("insumos/modificar_insumos/$1")."' title='Editar' class='' data-toggle='ajax'><i class='glyphicon glyphicon-edit'></i></a>";

        if(!is_null($rubro)){
            if(strlen($rubro)>0){
                $this->datatables->where('rubros.id',$rubro);
                //die('caraxo');
            }
        }

        $this->datatables->add_column("Actions",$cadena,"id");

        $cads = $this->datatables->generate();
        //$cads = (substr($cads,-4) == 'null' ? substr($cads,0,strlen($cads)-4) : $cads);
        echo $cads;
    }

    function grabar(&$mensaje,&$error){
    	
    	
        $error = false;
    	
    	$result = $this->db->select('code')
    		->from('products')
            ->where('code<>','INS801')
            ->where('category_id','7')
    		->like('code', 'INS', 'after')
    		->order_by('code','asc')->get()->result_array();
    	
        $ultimo = "INS0000";
    	
    	if($result){
	    	foreach($result as $r){
	    		$ultimo = $r["code"];
                //echo $ultimo . "<br>";
	    	}
	    }
    	// Extraigo los numeros y le sumo 1:
    	$n = substr($ultimo,-4);
    	$n = $n + 1;

    	// Validando que no exista un producto con la misma descripcion:
        $cSql = "select * from tec_products where name = '" . $_GET["descPro"] . "' and rubro != 0";
        
        $query = $this->db->query($cSql);
        
        if($query->num_rows()==0){


            $codigo = "INS" . substr("0000" . $n,-4);

            $ar = array();
            $ar["name"] = $_GET["descPro"];
            $ar["unidad"] = $_GET["unidad"];
            $ar["code"] = $codigo;
            $ar["rubro"] = $_GET["rubro"];
            $ar["category_id"] = '7';

            //die("El rubro es ".$_GET["rubro"]);
            //echo $this->db->set($ar)->get_compiled_insert('products');

            //print_r($ar); die();
            $this->db->set($ar)->insert('products');
            //echo $this->db->set($ar)->get_compiled_insert('products');
            //die();
        	
            $item_nuevo = $this->db->insert_id();
        	
        	if($item_nuevo > 0){
        		$mensaje = "Grabacion correcta del Insumo (" . $_GET["descPro"] . ")";
                //$mensaje = $_GET["descPro"] . "," . $item_nuevo . "," . $_GET["unidad"] . ",";
        	}else{
        		$mensaje = "Ocurrio algun error en grabacion";
        		$error = true;
        	}
        }else{
            $mensaje = "Ya existe un producto con el mismo nombre";
            $error = true;
        }

    }
}