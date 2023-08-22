<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Inventarios extends MY_Controller
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
        $this->load->model('inventarios_model');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip';

    }

    function averigua_tienda_inv($id){
        $cSql = "select * from tec_maestro_inv where id = $id";
        $query = $this->db->query($cSql); // ,array($id)
        foreach($query->result() as $r){
            return $r->store_id;
        }
        return false;
    }

    function add($id=null){
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = 'Registrar Inventario';
        $this->data['id']           = (is_null($id) ? "" : $id);
        
        $bc                     = array(array('link' => '#', 'page' => "Agregar inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $hora_fin = $this->db->query("select hora_fin from tec_maestro_inv where id=?",array($id))->row()->hora_fin;
        $esta_abierto = is_null($hora_fin) || strlen($hora_fin)==0 ? true : false;

        $this->data["esta_abierto"] = $esta_abierto;
        $this->page_construct('inventarios/add', $this->data, $meta);
        /*}else{
            $this->data["warning"] = "El inventario está cerrado.";
            $this->page_construct('inventarios/lista_inventarios', $this->data, $meta);
        }*/
    }

    function save(){  // DEPRECADO POR SAVE_MASIVO
        $maestro_id = isset($_GET["maestro_id"]) ? $_GET["maestro_id"]*1 : 0;
        $cSql = "select * from tec_maestro_inv where id=?";
        $query = $this->db->query($cSql,array($maestro_id));
        $hora_fin = "";
        foreach($query->result() as $r){
            $fecha      = $r->fecha;
            $store_id   = $r->store_id;
            $hora_fin   = $r->hora_fin;
        }
        
        $product_id = $_GET['product_id'];
        $cantidad   = $_GET['cantidad'];
        $unidad     = $_GET['unidad'];

        // Lo convertimos a Kilos o Litros ...
        if($unidad == "GRAMO"){ $cantidad = $cantidad / 1000;}
        if($unidad == "MILILITRO"){ $cantidad = $cantidad / 1000;}

        $ar = array(
            'maestro_id'    => $maestro_id,
            'fecha'         => $fecha,
            'store_id'      => $store_id,
            'product_id'    => $product_id,
            'cantidad'      => $cantidad
        );

        // Verificando que todavia no haya sido ingresado
        $cSql = "select id from tec_inventarios where fecha = ? and product_id = ?";
        $query = $this->db->query($cSql,array($fecha,$product_id));
        $nC = 0;
        foreach($query->result() as $r){
            $nC++;
        }
        if($nC == 0 && strlen($hora_fin)==0 && $maestro_id > 0){

            $cSql = "insert into tec_inventarios(fecha, store_id, product_id, cantidad, maestro_id) values(?,?,?,?,?)";

            if($this->db->query($cSql, array($fecha, $store_id, $product_id, $cantidad, $maestro_id))){
                //echo "1";
                $this->db->select("a.id, a.fecha, a.store_id, stores.state tienda, a.product_id, tec_products.name producto, a.cantidad");
                $this->db->from("tec_inventarios as a");
                $this->db->join("tec_stores","a.store_id = tec_stores.id","left");
                $this->db->join("tec_products","a.product_id = tec_products.id", "left");
                $this->db->where("a.maestro_id", $maestro_id);
                $this->db->order_by("a.id","desc");
                $this->db->limit(1000);
                $result = $this->db->get()->result_array();

                $tabla = "";
                $nC=0;
                foreach ($result as $r){
                    $nC++;
                    if($nC==1){
                        $tabla .= "<table>";
                        $tabla .= "<tr><th style='text-align:left;width:50px'>Id</th>
                            <th style='text-align:left; width:80px'>Fecha</th>
                            <th style='text-align:left; width:90px'>Tienda</th>
                            <th style='text-align:left; width:180px'>Producto</th>
                            <th style='text-align:left; width:80px'>Cantidad</th></tr>";    
                    }
                    $tabla .= "<tr>";
                    $tabla .= $this->fm->celda($r["id"]);
                    $tabla .= $this->fm->celda($r["fecha"]);
                    $tabla .= $this->fm->celda($r["tienda"]);
                    $tabla .= $this->fm->celda($r["producto"]);
                    $tabla .= $this->fm->celda($r["cantidad"]);
                    $tabla .= "</tr>";
                }
                $tabla .= "</table>";
                $msg    = "Graba satisfactoriamente.";
                $rpta   = "success";
                $other  = $tabla;
            }else{
                $msg    = "Error al momento de grabar.";
                $rpta   = "danger";
                $other  = "";
            }
        }else{
            $msg = "<span style='color:red'>El inventario para ese producto ya fue ingresado, o ya ha cerrado el Inventario.</span>";
            $rpta = "warning";
            $other = "";
        }
        $retorno = array(
            "msg" => $msg,
            "rpta" => $rpta,
            "other" => $other
        );
        echo json_encode($retorno);
    }

    function save_masivo(){
        
        $maestro_id = isset($_POST["maestro_id"]) ? $_POST["maestro_id"]*1 : 0;
        $nLimite    = $_POST['txt_i'];

        // AVERIGUANDO FECHA, STORE_ID
        $cSql = "select * from tec_maestro_inv where id=?";
        $query = $this->db->query($cSql,array($maestro_id));
        $hora_fin = "";
        foreach($query->result() as $r){
            $fecha      = $r->fecha;
            $store_id   = $r->store_id;
            $hora_fin   = $r->hora_fin;
        }

        for($i = 0; $i < $nLimite; $i++){
            
            $product_id = $_POST["product_id_{$i}"];
            $cantidad   = trim($_POST["cantidad_{$i}"]);
            $unidad     = $_POST["unidad_{$i}"];

            //echo "Como inicia:<br>";

            // Lo convertimos a Kilos o Litros ...
            if($unidad == "GRAMO" || $unidad == "MILILITRO"){ 
                if(strlen($cantidad)>0){
                    $cantidad = $cantidad / 1000;
                }
            }
            
            $ar = array(
                'maestro_id'    => $maestro_id,
                'fecha'         => $fecha,
                'store_id'      => $store_id,
                'product_id'    => $product_id,
                'cantidad'      => $cantidad
            );

            // Verificando que todavia no haya sido ingresado
            $cSql = "select id from tec_inventarios where maestro_id = ? and product_id = ?";
            $query = $this->db->query($cSql,array($maestro_id,$product_id));
            $nC = 0;
            foreach($query->result() as $r){
                $nC++;
            }

            if(strlen($hora_fin)==0 && $maestro_id > 0 && strlen($cantidad)>0){

                if ($nC == 0){
                    $cSql = "insert into tec_inventarios(fecha, store_id, product_id, cantidad, maestro_id) values(?,?,?,?,?)";
                    $this->db->query($cSql, array($fecha, $store_id, $product_id, $cantidad, $maestro_id));
                }else{
                    $cSql = "update tec_inventarios set cantidad = ? where maestro_id = ? and product_id = ?";
                    $this->db->query($cSql, array($cantidad, $maestro_id, $product_id));
                }
                
            }

        } // FIN DEL FOR
        
        $this->add($maestro_id);
    }

    function mostrar_registros(){
        $nada = "";
    }

    function get_detalle_inventario(){
        // id fecha tienda producto cantidad //      
        $this->load->library('datatables');

        $maestro_id = $_POST["maestro_id"];
        $this->datatables->select("tec_inventarios.id, tec_inventarios.fecha, tec_inventarios.store_id, tec_stores.state tienda, tec_inventarios.product_id, tec_products.name producto, tec_inventarios.cantidad");
        $this->datatables->from("tec_inventarios");
        $this->datatables->join("tec_stores","tec_inventarios.store_id = tec_stores.id","left");
        $this->datatables->join("tec_products","tec_inventarios.product_id = tec_products.id and tec_products.category_id=7 and tec_products.rubro=1", "left");
        $this->datatables->where("tec_inventarios.maestro_id", $maestro_id);
        //$this->datatables->order_by("tec_inventarios.id","desc");
        //$this->datatables->limit(1500);
        echo $this->datatables->generate();
    }

    function view($id = NULL) {
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = 'Inventario';
       
        $this->load->view($this->theme.'inventarios/view', $this->data);
    }

    function eliminar(){
        $id = $_GET["id"];
        if($id){
            $this->db->where("id", $id)->delete("tec_inventarios");
        }
    }

    function formato_impresion(){
        $this->data["store_id"] = $_GET["store_id"];
        $this->data["fecha_inv"] = $_GET["fecha_inv"];
        $this->data["inventario1"] = $_GET["inventario1"];
        $this->load->view($this->theme."inventarios/formato_impresion", $this->data);
    }

    function save_movim(){
        $product_id     = $_GET["product_id"];
        $inv_id         = $_GET["inv_id"];
        $cantidad       = $_GET["cantidad"];
        $store_id       = $_GET["store_id"];
        $persona        = $_GET["persona"];
        $fechis         = date('Y-m-d', strtotime(date("Y-m-d") .' +1 day'));
        $cSql = "insert into tec_movim(product_id, cantidad, store_id, inv_id, persona, fechah, tipo_mov, metodo) values(?,?,?,?,?,'{$fechis}','I','3')";
        if ($this->db->query($cSql,array($product_id, $cantidad, $store_id, $inv_id ,$persona))){
            $msg = "Se graba correctamente";
            $rpta = "success";
        }else{
            $msg = "No pudo grabarse";
            $rpta = "danger";
        }
        echo json_encode(array(
            "msg"=>$msg,
            "rpta"=>$rpta,
            "other"=>""
        ));
    }

    function movimientos($store_id=""){
        
        if(strlen($store_id)==0){
            if(isset($_SESSION["store_id"])){
                $store_id = $_SESSION["store_id"];
            }else{
                die("No existe variable Tienda");
            }
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('Inventario - Movimientos');
        
        $bc                     = array(array('link' => '#', 'page' => "inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('inventarios/movimientos', $this->data, $meta);
    
    }

    function get_movimientos() {
    }

    function eliminar_movimiento(){
        $id = $_GET["id"];
        $ar["id"] = $id;
        $this->db->delete("movim", $ar);
        echo "1";
    }

    function add_movimientos(){
        if(isset($_POST["modo"])){
            
            // Modo Insert:
            //store_id tipo_mov fechah producto cantidad obs
            $store_id   = $_POST["store_id"];
            $store_id_destino   = $_POST["store_id_destino"];
            $tipo_mov   = 'S'; // Ya que ingresos a ti mismo lo deben hacer otros
            $fechah     = $_POST["fechah"];
            $producto   = $_POST["producto"];
            $cantidad   = $_POST["cantidad"];
            $obs        = $_POST["obs"];
            $metodo     = $_POST["metodo"];
            $unidad     = $_POST["unidad"];

            $this->db->set("store_id", $store_id);
            $this->db->set("store_id_destino", $store_id_destino);
            $this->db->set("tipo_mov", $tipo_mov);
            $this->db->set("fechah", $fechah);
            $this->db->set("product_id", $producto);
            
            //echo "Unidad: {$unidad}<br>";
            //echo "Cantidad: {$cantidad}<br>"; 

            if ($unidad == 'GRAMO' || $unidad == 'MILILITRO'){
                $cantidad = $cantidad / 1000;
            }

            //die("Cantidad: {$cantidad}<br>");         

            $this->db->set("cantidad", $cantidad);
            $this->db->set("obs", $obs);
            $this->db->set("metodo",$metodo);
            
            if($metodo != '1'){ // PRESTAMO
                $this->db->set("confirmado", '1'); // Confirmado (La merma la ingresa el supervisor)
            }

            //die($this->db->get_compiled_insert("movim"));
            
            if ($this->db->insert("movim")){
                $this->data["mensaje"]  = "Se graba correctamente";
                $this->data["rpta"]     = "success";
            }else{
                $this->data["mensaje"]  = "No se pudo grabar";
                $this->data["rpta"]     = "warning";
            }
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('Inventario - Agregar Otros Movimientos');
            
        $bc                     = array(array('link' => '#', 'page' => "inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            
        $this->data["tipos_mov"] = $this->db->select('*')->get("tipos_mov");

        $this->page_construct('inventarios/add_movimientos', $this->data, $meta);
    }

    function add_pases(){
        if(isset($_POST["modo"])){
            
            // Modo Insert:
            //store_id tipo_mov fechah producto cantidad obs
            $store_id   = $_POST["store_id"];
            $tipo_mov   = $_POST["tipo_mov"];
            $fechah     = $_POST["fechah"];
            $producto   = $_POST["producto"];
            $cantidad   = $_POST["cantidad"];
            $obs        = $_POST["obs"];
            $confirmado = $_POST["confirmado"];
            $store_id_destino = $_POST["store_id_destino"];
            
            $this->db->set("store_id", $store_id);
            $this->db->set("tipo_mov", $tipo_mov);
            $this->db->set("fechah", $fechah); //  '2023-02-02 10:15:16'
            $this->db->set("product_id", $producto);
            $this->db->set("cantidad", $cantidad);
            $this->db->set("obs", $obs);
            if($store_id_destino == '5'){
                $this->db->set("confirmado", '1'); // Pendiente por el momento.
            }else{
                $this->db->set("confirmado", '0'); // Pendiente
            }
            $this->db->set("store_id_destino", $store_id_destino);
            $this->db->set("inv_id", 0);
            $this->db->set("metodo",1); // PASE
            
            $mi_cSql = ""; //$this->db->get_compiled_insert("movim");

            if ($this->db->insert("movim")){
                $this->data["mensaje"]  = "Se graba correctamente";
                $this->data["rpta"]     = "success";
            }else{
                $this->data["mensaje"]  = "No se pudo grabar" . $this->db->error() . $mi_cSql;
                $this->data["rpta"]     = "warning";
            }
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('Pases entre Almacenes');
            
        $bc                     = array(array('link' => '#', 'page' => "inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            
        $this->data["tipos_mov"] = $this->db->select('*')->get("tipos_mov");

        $this->page_construct('inventarios/add_pases', $this->data, $meta);
    }

    function save_movimientos_otros(){

    }

    function nuevo_inventario($id = null){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Nuevo Inventario";
        
        $bc                     = array(array('link' => '#', 'page' => "inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        if(!is_null($id)){
            $query = $this->db->select("*")->where("id",$id)->get("tec_maestro_inv");
            foreach($query->result() as $r){
                $this->data["id"]       = $r->id;
                $this->data["store_id"] = $r->store_id;
                $this->data["fecha"]    = $r->fecha;
                $this->data["hora_ini"] = $r->hora_ini;
                $this->data["hora_fin"] = $r->hora_fin;
                $this->data["responsable"] = $r->responsable;
                $this->data["responsable_tda"] = $r->responsable_tda;
            }
        }

        $this->page_construct('inventarios/nuevo_inventario', $this->data, $meta);

    }

    function lista_inventarios(){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Inventarios";
        
        $bc                     = array(array('link' => '#', 'page' => "inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        //echo print_r($_SESSION,true);
        //die("");
        $this->page_construct('inventarios/lista_inventarios', $this->data, $meta); 
    }

    function save_nuevo_inventario(){
        $store_id       = $_POST["tienda"]; 
        $fecha          = $_POST["fecha"]; 
        $hora_ini       = $_POST["hora_ini"]; 
        $hora_fin       = $_POST["hora_fin"]; 
        $responsable    = $_POST["responsable"];
        $responsable_tda= $_POST["responsable_tda"];

        $this->db->set("store_id",$store_id);
        $this->db->set("fecha",$fecha);
        $this->db->set("hora_ini",$hora_ini);
        $this->db->set("hora_fin",$hora_fin);
        $this->db->set("responsable",$responsable);
        $this->db->set("responsable_tda",$responsable_tda);

        //die($this->db->get_compiled_update('tec_maestro_inv'));
        
        if(isset($_POST["id"]) && strlen($_POST["id"])>0){
            //die("udo");
            $this->db->where("id",$_POST["id"])->update("tec_maestro_inv");

            $this->data["message"] = "Se actualiza el Nro. de Inventario ".$this->data["id"];
            //$this->data["rpta_msg"] = "info";

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = "Lista de Inventarios";
            
            $bc                     = array(array('link' => '#', 'page' => "inventarios"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            
            $this->page_construct('inventarios/lista_inventarios', $this->data, $meta);

        }else{
            $this->db->insert("tec_maestro_inv");
            $this->data["id"] = $this->db->insert_id();
            
            $this->data["esta_abierto"] = true;

            $this->data["message"] = "Se crea el Nro. de Inventario ".$this->data["id"];
            //$this->data["rpta_msg"] = "success";

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = "Inventario";
            
            $bc                     = array(array('link' => '#', 'page' => "inventarios"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            
            $this->page_construct('inventarios/add', $this->data, $meta);
        }
    }

    function stock($op=1, $inventario1=null, $inventario2=null){ // La primera pantalla cuando van a querer sacar el stock
        
        $this->data['op']   = $op;
            
        if($op==1){ // Pantalla inicial

            $this->data['page_title'] = "Inventario";
            $bc                     = array(array('link' => '#', 'page' => "inventarios"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            $this->page_construct('inventarios/stock', $this->data, $meta);

        }elseif($op == 2){ // Generacion automatica

            // Pregunta Tda
            $this->data['page_title'] = "Generar Stock Actual";
            $bc                     = array(array('link' => '#', 'page' => "inventarios"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            $this->page_construct('inventarios/stock0', $this->data, $meta);        

        }elseif($op == '5'){  // Generacion en base a un solo inventario1 (La Generacion automatica recae aquí)
            /*
            1.- La tienda es la actual
            2.- Averigua si hay inventarios, si los hay tomar el ultimo
            3.- Proceso
            */

            // 1.-
            if($_SESSION["group_id"] == '1'){
                $store_id = $inventario1; // se grabó momentaneamente la tienda allí 
            }else{
                $store_id = $_SESSION['store_id'];
            }
            $this->data['inv_store_id']     = $store_id;

            // 2.- Averigua fecha, inventario (id) y tienda (descrip)
            $cSql = "select a.id, a.fecha, a.hora_fin, b.state tienda from tec_maestro_inv a
                left join tec_stores b on a.store_id = b.id
                where a.store_id = {$store_id} and length(a.hora_fin)>0 order by a.id";
            $query = $this->db->query($cSql);
            $fec_inv        = "2022-01-01"; // Tan sólo es por default
            $inventario1    = "0";
            $tienda         = "";
            foreach($query->result() as $r){
                $fec_inv        = $r->fecha . " " . $r->hora_fin;
                $inventario1    = $r->id;
                $tienda         = $r->tienda;
            }

            // 3.-
            $this->data['inventario1']       = $inventario1;
            
            $ar     = $this->inventarios_model->listar($inventario1, "0", $store_id);
            
            $this->data['query_compara']    = $ar[0];
            $this->data['cSql']             = $ar[1];
            $this->data['cSql_compras']     = $ar[2];
            $this->data['cSql_ventas']      = $ar[3];
            $this->data['store_id']         = $store_id;
            $this->data['ult_inventario']   = $fec_inv.'-'.$tienda;

            $this->data['page_title'] = "Generar Stock Actual";
            $bc                     = array(array('link' => '#', 'page' => "inventarios"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            $this->page_construct('inventarios/stock1', $this->data, $meta);        

        }elseif($op == '3'){ // Comparando inventarios

            $this->data['page_title'] = "Comparar 2 inventarios";
            $bc                     = array(array('link' => '#', 'page' => "inventarios"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            $this->page_construct('inventarios/stock2', $this->data, $meta);        

        }elseif($op=='4'){

            $this->data['inventario1']        = $inventario1;
            $this->data['inventario2']        = $inventario2;

            $this->data['inventario1_descrip'] = $this->db->query("select concat(a.fecha,'-',b.state) poderes, a.fecha, b.state from tec_maestro_inv a inner join tec_stores b on a.store_id=b.id where a.id = ?",array($inventario1))->row()->poderes;
            $this->data['inventario2_descrip'] = $this->db->query("select concat(a.fecha,'-',b.state) poderes, a.fecha, b.state from tec_maestro_inv a inner join tec_stores b on a.store_id=b.id where a.id = ?",array($inventario2))->row()->poderes;
            //die('inventario1_descrip:'.$this->data['inventario1_descrip']);

            $ar     = $this->inventarios_model->listar($inventario1, $inventario2);

            $this->data['query_compara']    = $ar[0];
            $this->data['cSql']             = $ar[1];
            $this->data['cSql_compras']     = $ar[2];
            $this->data['cSql_ventas']      = $ar[3];

            $this->data['page_title'] = "Comparar 2 inventarios";
            $bc                     = array(array('link' => '#', 'page' => "inventarios"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            $this->page_construct('inventarios/stock2', $this->data, $meta);        

        }elseif($op == '6'){

            $store_id = $_SESSION['store_id'];

        }else{
            die("Vitale");
        }

    }

    function confirmar_pases(){
        $id     = $_GET["id"];
        
        $cSql   = "update tec_movim set confirmado='1' where id = ?";
        $this->db->query($cSql, array($id));
        
        // Averiguando algunos datos
        $cSql = "select store_id, product_id, cantidad, fechah from tec_movim where id = ?";
        $query = $this->db->query($cSql,array($id));
        foreach($query->result() as $r){
            $store_donde    = $r->store_id;
            $product_id     = $r->product_id;
            $cantidad       = $r->cantidad;
            $fechah         = $r->fechah;
        }

        $mi_tienda = $_SESSION["store_id"];
        $cSql = "insert into tec_movim(store_id, store_id_destino, tipo_mov, metodo, fechah, product_id, cantidad, confirmado) values(?,?,'I',1,?,?,?,'1')";
        $this->db->query($cSql,array($mi_tienda, $store_donde, $fechah, $product_id, $cantidad));

        echo "OK";
    }
    
    function ajustes(){
        
        if($_POST["modo"]){
            //Proceso de ajuste : Consiste en comparar el kardex vs un inventario fisico.

            $store_id   = $_POST["store_id"];
            $proceso    = $_POST["proceso"];

            if($proceso == '1'){  // Calculo del Stock Actual

                // Query del Stock ---------------
                //$this->data['query1']     = $this->query_stock($store_id,'2023-05-12 14:01:00'); // date("Y-m-d H:i:s")
                $this->data['query1']     = "CALL calcular_kardex($store_id,'".date("Y-m-d H:i:s")."');";

                //$this->db->reset_query();

                $cStore_id = $this->db->select('state')->where('id',$store_id)->get('stores')->row()->state;
                $this->data['page_title'] = "Stock de Inventario ($cStore_id)";
                $bc                     = array(array('link' => '#', 'page'     => "inventarios"));
                $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
                $this->page_construct('inventarios/resultado_stock', $this->data, $meta);

            }

            if($proceso == '2'){ // Sinceramiento del Stock valiéndose del Inventario Físico.-+-

            }

        }else{
            $this->data['page_title'] = "Ajustes de Inventario";
            $bc                     = array(array('link' => '#', 'page' => "inventarios"));
            $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
            $this->page_construct('inventarios/ajustes', $this->data, $meta); 
        }
    }

    function sincerar($inv_id, $store_id){ // Sincera el Stock en base al ultimo inventario cerrado
        
        // Obtengo la fecha de inicio del inventario actual
        $r = $this->db->query("select * from tec_maestro_inv where id = ?",array($inv_id))->row();
        $fecha_inicio = $r->fecha . ' ' . $r->hora_ini;

        // Obteniendo el ultimo inventario terminado
        $cSql = "select a.id, a.fecha, a.hora_fin, b.state from tec_maestro_inv a 
            inner join tec_stores b on a.store_id = b.id
            where a.id < $inv_id and a.store_id = ? and length(trim(a.hora_fin)) > 0".
            " order by a.id desc limit 1";
        //die($cSql);
        $query = $this->db->query($cSql,array($store_id));
        $tienda_name = "";
        foreach ($query->result() as $r){
            $inv_id_ult = $r->id;
            $inv_fecha_ult = $r->fecha;
            $tienda_name = $r->state;
            $hora_fin = $r->hora_fin; 
            //die("ultima fecha:".$inv_fecha_ult);
            //die($hora_fin);
            $fecha_completa_ult = $inv_fecha_ult . ' ' . $hora_fin;
            //die("fecha completa:".$fecha_completa_ult);
        }
        
        if(isset($inv_id)){
            // Eliminando algun rastro de una corrida anterior del inventario actual
            $this->db->query("delete from tec_movim where inv_id = ?", array($inv_id));
            
            // Averiguo fecha y hora de cierre del ultimo inventario cerrado
            //$r = $this->db->query("select * from tec_maestro_inv where id = ?",array($inv_id_ult))->row();
            //$fec_inv_ult = trim($r->fecha) . " " . trim($r->hora_fin); 


            $cSql = "CALL sincerar_kardex($store_id,'$fecha_inicio',?)";

            $query = $this->db->query($cSql,array($inv_id));

            //$query = $this->db->query($cSql,array($maestro_id));
            $result = $query->result_array();
            $query->free_result();
            $query->next_result();
            $cols = array("id", "name", "unidad", "total_comprado", "total_utilizado","ingreso","salida","kardex","inventario","resultado");
            $titulos = array("ID", "INSUMOS", "UNIDAD", "COMPRADO", "UTILIZADO","INGRESO","SALIDA","KARDEX ACTUAL","INVENTARIO","DIFERENCIAS");
            $ar_align = array("0","0","0","1","1","1","1","1","1","1");
            
            $this->data["tabla_resultado"] = $this->fm->crea_tabla_result($result, $cols, $titulos, $ar_align, $ar_pie);
            $this->data["rptax"] = "";

            $this->data["rptax"] .= "<p>Se toma como referencia al Inventario con ID $inv_id_ult de fecha $inv_fecha_ult</p>";

            $cSql = "select concat(fecha,' ',hora_fin) fechas from tec_maestro_inv where id = ?";
            //$fechah = $this->db->query("select fechah from tec_maestro where inv_id = ?",array($inv_id))->row()->fechah;
            $fechas = $this->db->query($cSql,array($inv_id))->row()->fechas;

            // Acciones a realizar
            foreach($result as $r){
                $product_id     = $r["id"];
                $product_name   = $r["name"];
                
                $nCant_inv = $r["inventario"]*1;
                $stock_act = $r["resultado"]*1;
                $nKardex    = $r["kardex"]*1;
                
                if($r["resultado"]*1 != 0){
                    if($nCant_inv > $nKardex){
                        $tipo = 'I';
                        $cantidad = $nCant_inv - $nKardex;
                    }
                    if($nCant_inv < $nKardex){
                        $tipo = 'S';
                        $cantidad = $nKardex - $nCant_inv;
                    }
                    $this->data["rptax"] .= $this->mover($store_id, $tipo, $product_id, $cantidad, $unidad, $inv_id, $fechas);
                }

                // Guardando el kardex actual en tabla tec_inventarios
                $cSql = "update tec_inventarios set kardex = ? where maestro_id = $inv_id and product_id = $product_id";
                $this->db->query($cSql,array($nKardex));
            }
        }else{
            die("No existe algun inventario de donde comparar.");
        }

        $this->data['page_title'] = "Resultado de Ajustes de Inventario ($tienda_name)";
        $bc                     = array(array('link' => '#', 'page' => "inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('inventarios/resultado_ajustes', $this->data, $meta); 
    }
    
    function mover($store_id, $tipo, $product_id, $cantidad, $unidad, $inv_id, $fechah){
        //$fechah = date("Y-m-d H:i:s");
        $obs = "Movimiento de Ajuste";
        //$cSql = "insert into tec_inventarios(product_id, store_id, tipo_mov, cantidad, unidad, fechah, obs) values(?,?,?,?,?,?,?)";
        $cSql = "insert into tec_movim(store_id, tipo_mov, product_id, cantidad, fechah, confirmado, metodo, inv_id) values(";
        $cSql .= "$store_id, '$tipo', $product_id, $cantidad, '$fechah', '1', 7, $inv_id)"; // metodo 7 = SINCERAR
        //echo $cSql . "<br>";
        //$this->db->query($cSql,array($store_id, $tipo, $product_id, $cantidad, $unidad, $fechah, $obs));
        if($this->db->query($cSql)){
            
            $name = $this->db->query("select name from tec_products where id = ?",array($product_id))->row()->name;
            return "<p>Se realiza un ".($tipo=='I' ? 'Ingreso' : 'Salida')." del producto ".$name. " ($cantidad) como Movimiento de Ajuste</p>";
        }else{
            return "<div class='alert alert-danger'>No se pudo ajustar el producto: $name</div>";
        }
        //if ($nQ==0){ echo "<div class='alert alert-primary'>No hay necesidad de Ajuste, todo cuadra.</div>";}
    }

    function query_stock($store_id, $dt = null){  // ----DEPRECADO ------- 
        $cSql = "SELECT b.id, b.name, if(b.unidad='UNIDAD',b.unidad,b.unidad) unidad,
          if(compras.total_comprado is null,0,compras.total_comprado) total_comprado, 
          if(compras.total_cost is null,0,compras.total_cost) total_costo_comprado,
          round(if(ventas.total_utilizado is null,0,ventas.total_utilizado),2) total_utilizado,
          round(movim_i.cantidad,2) as ingreso, movim_s.cantidad as salida,
          round(if(compras.total_comprado is null,0,compras.total_comprado) - if(ventas.total_utilizado is null,0,ventas.total_utilizado) 
          + if(movim_i.cantidad is null,0,movim_i.cantidad) - if(movim_s.cantidad is null,0,movim_s.cantidad),2) as kardex
          FROM tec_products b
          left join(
            select b1.product_id, sum(b1.quantity) total_comprado, sum(b1.cost) total_cost 
            from tec_purchases a1
            inner join tec_purchase_items b1 on a1.id = b1.purchase_id
            where a1.store_id = $store_id and a1.date < '$dt'
            group by b1.product_id 
          ) compras on b.id = compras.product_id
          left join (
              select mire.id_insumo, mire.name, mire.unidad, sum(vtas.quantity) vtas_de_cantidad,
              sum(vtas.quantity * mire.cantidadReceta) as total_utilizado
              from (
                select b2.product_id, sum(b2.quantity) quantity, sum(b2.real_unit_price) monto
                from tec_sales a2
                inner join tec_sale_items b2 on a2.id = b2.sale_id
                where a2.anulado!=1 and a2.store_id = $store_id and a2.date < '$dt'
                group by b2.product_id
              ) vtas
              inner join (
                select tec_recetas.product_id, tec_recetas.id_insumo, tec_products.name, tec_products.unidad, 
                if(tec_products.unidad = 'UNIDAD', if(tec_products.peso_unidad>0, tec_recetas.cantidadreceta/tec_products.peso_unidad, tec_recetas.cantidadreceta), tec_recetas.cantidadreceta/1000) cantidadReceta
                from tec_recetas
                inner join tec_products on tec_recetas.id_insumo = tec_products.id
              ) mire on vtas.product_id = mire.product_id
              group by mire.id_insumo, mire.name, mire.unidad
          ) ventas on b.id = ventas.id_insumo
           left join (
              select tm.product_id, sum(tm.cantidad) as cantidad
              from tec_movim tm
              where tm.store_id = $store_id and tm.tipo_mov = 'I' and tm.confirmado = '1' and tm.fechah < '$dt'
              group by tm.product_id
          ) movim_i on b.id = movim_i.product_id
          left join (
              select tm.product_id, sum(tm.cantidad) as cantidad
              from tec_movim tm
              where tm.store_id = $store_id and tm.tipo_mov = 'S' and tm.confirmado = '1' and tm.fechah < '$dt'
              group by tm.product_id
          ) movim_s on b.id = movim_s.product_id
        where b.category_id = 7 and b.rubro = 1
        order by b.name";
        return $this->db->query($cSql,array($inv_id));

    }

    function vista_previa($inv_id){
        //echo "<h2>LISTADO DE INSUMOS</h2>";

        $ar = array(); 

        $cSql = "select tec_products.id, upper(tec_products.name) name, tec_products.unidad, '_______' rubro
            from tec_products
            left join tec_rubros on tec_products.rubro = tec_rubros.id
            where tec_products.category_id = 7 and tec_products.rubro = 1
            order by tec_rubros.descrip, tec_products.name";

        $result         = $this->db->query($cSql)->result_array(); 

        $cols           = array("name","unidad","rubro"); 

        $cols_titulos   = array("name","unidad","rubro"); 

        $ar_align       = array("0","0","0"); 

        $ar_pie         = $ar_align; 

        $this->data['tabla'] = $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
        $this->load->view($this->theme . 'inventarios/vista_previa',$this->data);
    }

    function autocomplete(){
        $cad = $_REQUEST["query"];
        
        $cSql = "select name from tec_products where category_id = 7 and name like '%$cad%' limit 6";
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
            echo $r->name . "<br>";
        }
    }

    /** MUESTRA EL RESULTADO DEL INVENTARIO ELEGIDO COMPARADO CON EL ANTERIOR MAS PROXIMO **/
    function resultado($maestro_id){
        $query = $this->db->select("tec_maestro_inv.store_id, tec_stores.state, tec_maestro_inv.fecha, tec_maestro_inv.hora_ini")
            ->join("tec_stores","tec_maestro_inv.store_id=tec_stores.id","left")
            ->where("tec_maestro_inv.id",$maestro_id)->get("tec_maestro_inv");
        //die($query);
        $store_id = $tienda = $fecha = "";
        
        foreach($query->result() as $r){
            $store_id   = $r->store_id;
            $tienda     = $r->state;
            //die($tienda);
            $fecha      = $r->fecha;
            $hora_hasta = $r->hora_ini;
            //die("Buda hasta:".$hora_hasta);
        }
        // --- Averiguando el inventario anterior -----
        $cSql = "select a.* from tec_maestro_inv a
        where a.store_id = $store_id and a.id < ? order by id desc limit 1";
        $query = $this->db->query($cSql,array($maestro_id));
        $maestro_id_ant = $fecha_inv_ant = "";
        foreach($query->result() as $r){
            $maestro_id_ant = $r->id;
            $fecha_inv_ant  = $r->fecha; 
            $hora_desde     = $r->hora_fin;
            //die("hora_desde:".$hora_desde);
        }
        // ----------

        $cad = "";

        $result = $this->db->select("tec_inventarios.product_id, upper(tec_products.name) name, tec_products.unidad, tec_inventarios.cantidad, tec_inventarios.store_id, tec_inventarios.maestro_id, tec_inventarios.kardex")
            ->join("tec_products","tec_inventarios.product_id=tec_products.id","left")
            ->where("tec_inventarios.maestro_id",$maestro_id)
            ->get("tec_inventarios")->result_array();
        $i=0; 

        /*$cols           = array("name","cantidad","store_id","maestro_id");
        $cols_titulos   = array("name","cantidad","store_id","maestro_id");
        $ar_align       = array("0","0","0","0");
        $ar_pie         = $ar_align;
        echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);*/

        $estilo = "";
        foreach($result as $r){
            
            if($i==0){
                $cad .= "<table class=\"table\">";
                $cad .= "<tr><th>Insumo</th><th>Inventario<br>{$fecha_inv_ant}</th><th>Compras</th><th>Ventas</th><th>Movim</th>
                <th>Kardex</th><th>Inventario<br>{$fecha}</th><th>Dif</th></tr>";
            }
            
            //echo "fecha desde:" . $fecha_inv_ant .' '. $hora_desde . "<br>";
            //echo "fecha hasta:" . $fecha .' '.$hora_hasta . "<br>";
            //die("");
            $compra = $this->explicacion_compras($store_id, $fecha_inv_ant . ' ' . $hora_desde, $fecha . ' ' . $hora_hasta, $r["product_id"]);
            $venta = $this->explicacion_ventas($store_id, $fecha_inv_ant . ' ' . $hora_desde, $fecha . ' ' . $hora_hasta, $r["product_id"]);
            $movim = $this->explicacion_movim($store_id, $fecha_inv_ant .' '. $hora_desde, $fecha .' '.$hora_hasta, $r["product_id"]);
            $cantidad_ant = $this->cantidad_inventario_buscada($maestro_id_ant,$r["product_id"]);
            $cad .= "<tr>";
            $cad .= $this->fm->celda($r["name"] . "<span style=\"font-size:12px;color:gray\"> (" . $r["unidad"] . ")</span>","0",$estilo);
            $cad .= $this->fm->celda($cantidad_ant,"2",$estilo);
            $cad .= $this->fm->celda($compra,"2",$estilo);
            $cad .= $this->fm->celda($venta,"2",$estilo);
            $cad .= $this->fm->celda($movim,"2",$estilo);
            $cad .= $this->fm->celda($r["kardex"],"2",$estilo);
            $cad .= $this->fm->celda($r["cantidad"],"2",$estilo);
            $cad .= $this->fm->celda(round($r["cantidad"]*1 - ($r["kardex"]*1),2),"2",$estilo);
            $cad .= "</tr>\n";
            $i++;
        }
        if($i>0){ $cad .= "</table>\n";}
        //echo $cad;
        $this->data['tabla'] = $cad;
        //$this->load->view($this->theme . 'inventarios/explicacion.php',$this->data);
        $this->data['page_title'] = "Explicaci&oacute;n del Inventario {$fecha}-{$tienda}";
        $bc                     = array(array('link' => '#', 'page' => "inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('inventarios/explicacion', $this->data, $meta);
    }

    function cantidad_inventario_buscada($maestro_id,$product_id){
        return $this->db->select("cantidad")->where("maestro_id",$maestro_id)->where("product_id",$product_id)->get("tec_inventarios")->row()->cantidad*1;
    }

    private function explicacion_compras($store_id, $fecha_ant, $fecha, $product_id){
        $f_ini = $fecha_ant;
        $f_fin = $fecha;
        
        $diff = intval($this->db->query("select datediff('$f_fin','$f_ini') diferencia")->row()->diferencia);

        $nAcuCant = 0;
        /*
        for($i=0; $i<$diff; $i++){
            $fechaX = ($i == 0 ? $f_ini : date('Y-m-d', strtotime($f_ini ." +{$i} day")) );
            $cSql = "call solo_compra_insumos_producto('$fechaX', $store_id, $product_id)";
            $query = $this->db->query($cSql);
            $result = $query->result_array();
            $query->free_result(); $query->next_result();
            $cantidad = 0;
            foreach($result as $r){
                $cantidad = $r["q"]*1;
            }
            $nAcuCant += $cantidad;
        }*/
        
        $cSql = "select round(sum(b1.quantity),2) cant
        from tec_purchases a1 
        inner join tec_purchase_items b1 on a1.id = b1.purchase_id 
        inner join tec_products c on b1.product_id = c.id 
        where a1.store_id = $store_id and a1.date between '$fecha_ant' and '$fecha' and b1.product_id = $product_id";
        if ($product_id = '70'){
            //die($cSql);
        }
        $nAcuCant = $this->db->query($cSql)->row()->cant;
        return $nAcuCant;
    }

    private function explicacion_ventas($store_id, $fec_i, $fec_f, $product_id){
        $cSql = "call solo_venta_insumos_producto('$fec_i', '$fec_f', $store_id, $product_id)";
        //die($cSql);
        $query = $this->db->query($cSql);
        $result = $query->result_array();
        $query->free_result(); $query->next_result();
        foreach($result as $r){
            return $r["total"];
        }
        return 0;
    }

    private function explicacion_movim($store_id, $fec_i, $fec_f, $product_id){
        $cSql = "call solo_movim_insumos_producto('$fec_i', '$fec_f', $store_id, $product_id)";
        $query = $this->db->query($cSql);
        $result = $query->result_array();
        $query->free_result(); $query->next_result();
        foreach($result as $r){
            return floatval($r["q"]);
        }
        return 0;
    }

    function cerrar_inventario($inv_id){

        $hori = date("H:i");
        $this->db->query("update tec_maestro_inv set hora_fin = '$hori' where id = $inv_id");
        $this->data["message"] = "Se cierra el inventario Nro. $inv_id";

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Inventarios";
        
        $bc                     = array(array('link' => '#', 'page' => "inventarios"));
        $meta                   = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('inventarios/lista_inventarios', $this->data, $meta); 
    }

}
