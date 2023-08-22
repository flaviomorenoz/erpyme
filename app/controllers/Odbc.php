<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

class Odbc extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->helper('url');
    }

    public function consulta($consulta){
        $cSql = $this->descriptar($consulta);

        //die($cSql);
        $query = $this->db->query($cSql);
        
        $cInicia = strtolower(substr($cSql,0,6));

        $data = array();

        //echo $cInicia."<br>";
        if($cInicia == "select" || $cInicia == "show t"  || $cInicia == "descri"){
            $result = $query->result_array();

            // ----- Obteniendo el nombre de los campos
            $fields = $query->list_fields();
            $ar_fields = array();
            $x = 0;
            foreach($fields as $field){
                $ar_fields[$x][0] = $field;
                $ar_fields[$x][1] = 0;
                //echo "field:" . $field . "<br>";
                $x++;
            }

            //---- Obteniendo el Tamaño maximo
            foreach($result as $r){
                for($x=0; $x < count($r); $x++){
                    $valor = $r[$ar_fields[$x][0]];
                    //echo $ar_fields[$x][0] . ":" . gettype($campo) . '<br>';
                    if( strlen($valor) > $ar_fields[$x][1]){
                        $ar_fields[$x][1] = intval(strlen($valor));
                    }
                    if ($ar_fields[$x][1] == 0){
                        $ar_fields[$x][1] = 1;
                    }
                }
            }
            
            // Guardando la longitud en el 1er arreglo
            $i = 0;
            foreach($fields as $field){
                $ar_campo["a$i"] = $ar_fields[$i][1];  // $ar_fields[$i][0]
                $i++;
            }
            $data[] = $ar_campo;
            
            // Guardando los nombres en el 2do arreglo
            if ($cInicia != "show t"){
                $i = 0;
                foreach($fields as $field){
                    $ar_campo["a$i"] = $ar_fields[$i][0];
                    //echo "Seria: " . $ar_fields[$i][0] . "<br>";
                    $i++;
                }

                $data[] = $ar_campo;
                foreach($result as $r){
                    $data[] = $r;
                }

            }else{
                $i = 0;

                // Esto seria lo real 
                //$data[] = array("a0"=>$ar_fields[0][0]);
                $data[] = array("a0"=>"campos");
                
                foreach($result as $r){
                    //$data[] = $r;
                    
                    //echo "Nombre real campo:" . $ar_fields[0][0] . "<br>";
                    $nombre_campo = $ar_fields[0][0];
                    //echo "Valor:" . $r[$nombre_campo] . "<br>";

                    $data[] = array("campos"=>$r[$nombre_campo]);
                    $i++;
                }
            }
            echo json_encode($data);
        }elseif($cInicia == "update" || $cInicia == "delete"){
            //$this->db->insert_id();
            $ar = array();
            $ar["a0"] = 60;
            $data[] = $ar;
            
            $ar = array();
            $ar["a0"] = "Resultado";
            $data[] = $ar;
            
            $ar = array();
            $ar["Resultado"] = "Se logra ingresar el dato.";
            $data[] = $ar;
            
            echo json_encode($data);
        }else{
            $ar = array();
            $ar["a0"] = 60;
            $data[] = $ar;
            
            $ar = array();
            $ar["a0"] = "Resultado";
            $data[] = $ar;
            
            $ar = array();
            $ar["Resultado"] = "Se ejecuta el Comando.";
            $data[] = $ar;
            
            echo json_encode($data);
        }
    }

    public function llaves($cad = ""){
        return "{" . $cad . "},";
    }

    function descriptar($cSql){
        $cSql = str_replace("wMundano", "select", $cSql);
        $cSql = str_replace("wrisco", "*", $cSql);
        $cSql = str_replace("wdesde", "from", $cSql);
        $cSql = str_replace("_ier__", "inner", $cSql);
        $cSql = str_replace("_lef__", "left", $cSql);
        $cSql = str_replace("_donde__", "where", $cSql);
        $cSql = str_replace("_j__", "-", $cSql);
        $cSql = str_replace("Mundiali", "%", $cSql);
        $cSql = str_replace("megusta_", "like", $cSql);
        $cSql = str_replace("j_eb_epu", "group", $cSql);
        $cSql = str_replace("j6k9_", " ", $cSql);
        $cSql = str_replace("j1k9_", "limit", $cSql);
        $cSql = str_replace("ljv8_", ",", $cSql);
        $cSql = str_replace("w0v3_","'", $cSql);
        $cSql = str_replace("g0v7_","=", $cSql);

        $cSql = str_replace("s0v8_","insert", $cSql);
        $cSql = str_replace("s0v9_","into", $cSql);
        $cSql = str_replace("s0v6_","values", $cSql);

        $cSql = str_replace("3ntr_","(", $cSql);
        $cSql = str_replace("s4le_",")", $cSql);

        $cSql = str_replace("s0v5_",">", $cSql);
        $cSql = str_replace("s0v4_","<", $cSql);

        return $cSql;
    }
}