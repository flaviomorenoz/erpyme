<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
defined('BASEPATH') OR exit('No direct script access allowed');

class Permisos extends MY_Controller
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
        $this->load->model('pos_model_apisperu_mejorado');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip';

    }

    function index(){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Permisos x Perfil";
        
        $bc = array(array('link' => '#', 'page' => "permisos"));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $this->page_construct('permisos/index', $this->data, $meta);
    }


    function editar($modulo_id) {
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Permisos x Perfil";
        
        $bc = array(array('link' => '#', 'page' => "permisos"));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        
        $cSql = "select id, name, description from tec_groups order by id;";
        $this->data["query_groups"] = $this->db->query($cSql);
        $this->data["modulo_id"]    = $modulo_id;

        $this->page_construct('permisos/editar', $this->data, $meta);
    }

    function calidad($dia){
        $cSql = "select a.id, date(a.date) fecha, a.total, a.total_tax, a.grand_total grand, a.order_discount_id disco, 
            a.store_id, a.tipoDoc, concat(a.serie,'-',a.correlativo) corre, 
            round(a.total*10/100,2) mtoIGV, 
            round(a.total*10/100,2) totalImpuestos, 
            a.total * (1+(10/100)) * 1 subtotal, 
             concat('doc_', lpad(a.id,7,'0') ,'_', date(a.date), '_envio.txt') archivo, 
            envio_electronico envio
            from tec_sales a
            where a.tipoDoc in ('Boleta','Factura') and date(a.date) = '$dia' order by a.id desc";
        //die($cSql);
        $result          = $this->db->query($cSql)->result_array();
        $cols           = array("id","fecha",'total', 'total_tax', 'grand', 'disco', 'store_id', 'tipoDoc', "corre","detalle","envio","envio2","tarea");
        $cols_titulos   = $cols;
        $ar_align       = $this->queso(count($cols));
        $ar_pie         = $ar_align;

        $cad = "";
        foreach($result as &$r){
            $cad = "";
            $rpta = $this->verifica_comprobante($r["archivo"],$dia);
            if($rpta=='OK'){
                $cad .= 'OK';
            }else{
                $cad .= "<a href='#' title='$rpta'>msg</a>";
            }

            $r["envio2"] = "<a href=\"#\" onclick=\"ver(" . $r['id'] . ")\">Detalle</a>";
            $r["tarea"] = "<a href=\"#\" onclick=\"ejecutar(" . $r['id'] . ")\">Ejecuta</a> {$cad}";

            $cSql = "select b.* from tec_sales a inner join tec_sale_items b on a.id=b.sale_id where a.id = ".$r["id"];
            $query = $this->db->query($cSql);
            $detalle = "";
            foreach($query->result() as $fila){
                $detalle .= round($fila->quantity,0) . "x" . round($fila->unit_price,2) . ", ";
            }

            $r['detalle'] = $detalle;
            
        }
        $this->data['tbl_detalles'] = "";

        $this->data["tbl_calidad"] = $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
        $this->data['page_title'] = "Calidad";
        
        $bc = array(array('link' => '#', 'page' => "permisos"));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('permisos/calidad', $this->data, $meta);
    }

    function verifica_comprobante($nombre_file,$dia){
        $cad = "";
        $ruta = base_url("comprobantes/" . substr($dia,0,7). "/" . $nombre_file);
        
        $gn = fopen($ruta,"r");
        
        //$cad .= "<h2>".$nombre_file."</h2>";
        
        $lineas = "";
        while (!feof($gn)) {
            $linea = fgets($gn);
            $lineas .= $linea;
        }
        if($this->analizar_rpta_sunat($lineas)){
            $cad .=  "OK";
        }else{
            $pos = strpos($lineas, '"error":');
            if($pos === false){
                $cad = "";
            }else{
                $cad .= substr($lineas, $pos);
            }
        }
        
        fclose($gn);
        return $cad;
        //echo $cad;
    }

    function analizar_rpta_sunat($bloque){
        $rpta1 = strpos($bloque, 'ha sido aceptada');
        $rpta2 = strpos($bloque, 'ha sido aceptado"');
        $rpta3 = strpos($bloque, "1033 - El comprobante fue registrado previamente con otros datos");
        
        if($rpta1 === false && $rpta2 === false && $rpta3 === false){
            $rpta = false;
        }else{
            $rpta = true;
        }

        if($rpta){
            return true;
        }else{ 
            return false;
        }
    }

    function queso($nCant){
        $ar = array();
        for($i=0; $i<$nCant; $i++){
            $ar[] = '1';
        }
        return $ar;
    }

    function ver_det($id){
        $cSql = "select a.id, a.sale_id, product_id, quantity, unit_price, net_unit_price, discount, tax, subtotal, real_unit_price, product_name from tec_sale_items a 
            where a.sale_id in ($id)";
        //die($cSql);
        $query      = $this->db->query($cSql);
        $result     = $query->result_array();
        $cols       = array("id","sale_id",'product_id','quantity','unit_price','net_unit_price','discount','tax', 'subtotal','real_unit_price','product_name');
        
        $cols_titulos   = $cols;
        $campos = "";
        $cantidad_campos = $this->nro_campos($query);
        $ar_align       = $this->queso($cantidad_campos);
        $ar_pie         = $ar_align;

        echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
    }

    function nro_campos($query){
        $result     = $query->result_array();
        $n=0;
        foreach($result as $r){
            $n = count($r);
            break;
        }

        /*foreach($result as $key => $value){
            $campos = $key;
        }*/
        return $n;
    }

    function ver_antes_de($id){
        
        $cad = "";
        $cSql = "select date(a.date) fecha from tec_sales a 
            where a.id in ($id)";
        $query      = $this->db->query($cSql);

        $fecha = "";
        foreach($query->result() as $r){
            $fecha = $r->fecha;
        }

        $archivo = "doc_" . substr("0000000" . $id, -7) . "_" . $fecha . "_antes_de.txt";

        $carpeta = substr($fecha,0,7);

        $ruta = "comprobantes/{$carpeta}/" . $archivo;

        // Verifica si el archivo existe
        if (!file_exists($ruta)) {
            $cad .= "El archivo no existe<br>";
        }

        // Abre el archivo para lectura
        $archivo = fopen($ruta, 'r');
        if ($archivo === FALSE) {
            $cad .= "El archivo no se pudo abrir";
        }

        $cad .= $ruta."<br>";

        $fp = fopen($ruta, "r");

        if ($fp) {
            while (($buffer = fgets($fp, 4096)) !== false) {
                $cad .= $buffer;
            }
            if (!feof($fp)){
                $cad .= "Error: unexpected fgets() fail\n";
            }
            fclose($fp);
        }else{
            $cad .= "No haremos tratos<br>";
        }
        $fp = null;
        return $cad;
    }

    function ver_rpta($id){
        
        $cSql = "select date(a.date) fecha from tec_sales a 
            where a.id in ($id)";
        $query      = $this->db->query($cSql);

        $fecha = "";
        foreach($query->result() as $r){
            $fecha = $r->fecha;
        }

        $archivo = "doc_" . substr("0000000" . $id, -7) . "_" . $fecha . "_envio.txt";

        $carpeta = substr($fecha,0,7);

        $ruta = "comprobantes/{$carpeta}/" . $archivo;

        // Verifica si el archivo existe
        if (!file_exists($ruta)) {
            echo "El archivo no existe";
        }

        // Abre el archivo para lectura
        $archivo = fopen($ruta, 'r');
        if ($archivo === FALSE) {
            echo "El archivo no se pudo abrir";
        }

        echo $ruta."<br>";

        $fp = fopen($ruta, "r");

        if ($fp) {
            while (($buffer = fgets($fp, 4096)) !== false) {
                echo $buffer;
            }
            if (!feof($fp)){
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($fp);
        }else{
            echo "No haremos tratos";
        }
        $fp = null;
    }

    public function envio_masivo_tarea_unica($id){
        
        $cSql   = "select id, grand_total, paid from tec_sales 
            where tipoDoc != 'Ticket' and anulado!=1 and id = $id";
        
        $query  = $this->db->query($cSql);
        
        $n = 0;
        foreach($query->result() as $r){
            $this->pos_model_apisperu_mejorado->envio_masivo_individual($r->id);
            usleep(800000);
            $n++;
        }        

        if($n > 0){ 
            echo "Hubo proceso unico<br>\n";
            
            echo str_replace(",","<br>",$this->ver_antes_de($id));
            
            echo "<br><br>\n\n";
            
            $this->ver_rpta($id);
        
        }else{
            echo "Ocurrió un error";
        }
    }

}