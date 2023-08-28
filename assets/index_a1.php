<?php
error_reporting(-1);  // -1
ini_set('display_errors', 1); // 1

// Datos de conexión a la base de datos
define('CADENA_CONEXION_DB','postgre://cubilxsp:OoTKxn2Puc6v@localhost/cubilxsp_empresa05'); //'dbdriver://username:password@hostname/database'

$host       = "localhost";
$user       = "lacabktv_root";
$password   = "navarretecamara6";
$dbname     = "lacabktv_pos";

$odbc = new Odbc($host, $user, $password, $dbname);

$odbc->consulta($_GET["param1"]);

class Odbc{

    public $host;
    public $user;
    public $password;
    public $dbname;

    function __construct($host1, $user1, $password1, $dbname1){
        //parent::__construct();
        $this->host = $host1;
        $this->user = $user1;
        $this->password = $password1;
        $this->dbname = $dbname1;
    }

    public function consulta($consulta){ // 
        
        $cSql = $this->descriptar($consulta);

        try {
            $host = $this->host;
            $user = $this->user;
            $password = $this->password;
            $dbname = $this->dbname;
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo = $conn->prepare($cSql);
            $pdo->execute();

            $conn = null;

            $result = $pdo->fetchAll(PDO::FETCH_ASSOC);

            $cInicia = strtolower(substr($cSql,0,6));

            $data = array();

            //die($cInicia."<br>");
            if($cInicia == "select" || $cInicia == "show t"  || $cInicia == "descri"){
                // Obtener los nombres de los campos del resultado

                $field_names = array();
                for ($i = 0; $i < $pdo->columnCount(); $i++) {
                    $meta = $pdo->getColumnMeta($i);
                    $field_names[] = $meta['name'];
                }
                //echo "Los nombres de los campos son: ";
                //echo implode(", ", $field_names);

                $ar_fields = array();
                $nCant_campos = count($field_names);
                for($x=0; $x < $nCant_campos; $x++){
                    $ar_fields[$x][0] = $field_names[$x];
                    $ar_fields[$x][1] = 0;
                }

                //---- Obteniendo el Tamaño maximo
                foreach($result as $r){
                    //echo "La fila:" . $this->print_array($r) . "<br>";
                    //echo "Tamaño de r:".count($r)."<br>";

                    for($x=0; $x < $nCant_campos; $x++){ // count($r) **************************************************** falta hacer la longitud
                        //echo "ar_fields[$x][0]:".$ar_fields[$x][0].'<br>';
                        //echo "sancho:".$r["id"];
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
                //foreach($fields as $field){
                for($i=0; $i<count($ar_fields); $i++){
                    $ar_campo["a$i"] = $ar_fields[$i][1];  // $ar_fields[$i][0]
                }
                $data[] = $ar_campo;

                //echo $this->print_array($ar_campo);

                $ar_campo = array();
                //if ($cInicia != "show t"){
                    $i = 0;
                    //echo "Cantidad de campos:" . count($ar_fields) . "<br>";
                    for($i=0; $i<count($ar_fields); $i++){
                        $ar_campo["a$i"] = $ar_fields[$i][0];
                    }

                    $data[] = $ar_campo;
                    
                    foreach($result as $r){
                        $s = array();
                        for($i=0; $i<count($ar_fields); $i++){
                            $campo = $r[$ar_campo["a$i"]];
                            $s[$ar_campo["a$i"]] = $this->decodi($campo);
                            /*echo $s[$ar_campo["a$i"]] . " : ";
                            for($j=0; $j<strlen($campo); $j++){
                                $letra = substr($campo, $j, 1);
                                echo ord($letra) . ", ";
                            }
                            echo "<br>";*/
                        }
                        $data[] = $s;
                    }
                    //$tension = str_replace("\n","<br>",print_r($data,true));
                    //$tension = str_replace(" ","&nbsp;",$tension);
                    //die($tension);

                /*}else{
                    $i = 0;
                    $ar_campo["a$i"]    = "campos";
                    $data[]             = $ar_campo;
                    
                    foreach($result as $r){
                        $data[] = array("campos"=>$r["Tables_in_qsysthoo_coreqs"]);
                        $i++;
                    }
                }*/
                //echo $this->print_array($data);
                echo json_encode($data);
            }elseif($cInicia == "update" || $cInicia == "delete"){
                //die($cSql);
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
            $pdo = null;
        
        }catch(PDOException $e){
            //die("La conexión a la base de datos falló: " . $e->getMessage());
            echo '[{"a0":255},{"a0":"campo_error"},{"campo_error":"' . substr($e->getMessage(),0,255) . '"}]';
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

    function print_array($ar_fields){
        $tension = str_replace("\n","<br>",print_r($ar_fields,true));
        $tension = str_replace(" ","&nbsp;",$tension);
        return $tension;        
    }

    function decodi($palabra){
        $cad = $palabra;
        $cad = str_replace(chr(241),"n",$cad);
        $cad = str_replace(chr(209),"N",$cad);
        $cad = str_replace(chr(225),"a",$cad);
        $cad = str_replace(chr(233),"e",$cad);
        $cad = str_replace(chr(237),"i",$cad);
        $cad = str_replace(chr(243),"o",$cad);
        $cad = str_replace(chr(250),"u",$cad);
        $cad = str_replace(chr(193),"A",$cad);
        $cad = str_replace(chr(201),"E",$cad);
        $cad = str_replace(chr(205),"I",$cad);
        $cad = str_replace(chr(211),"O",$cad);
        $cad = str_replace(chr(218),"U",$cad);
        return $cad;
    }
}
?>