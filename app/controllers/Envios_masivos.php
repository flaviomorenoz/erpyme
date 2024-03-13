<?php 
$timezone = "America/Lima";
date_default_timezone_set($timezone);
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once APPPATH.'libraries/luecano/src/NumeroALetras.php';
use Luecano\NumeroALetras\NumeroALetras;

class Envios_masivos extends MY_Controller {

    function __construct() {
        parent::__construct();
       $this->load->helper('pos');
        $this->load->model('pos_model');
        $this->load->model('pos_model_apisperu');
        $this->load->library('form_validation');
        $this->delivery = "";
    }

    public function envio_masivo_t_p_fecha($fecha){
        
        //$tiempo     = strtotime($fecha . " +1 day");
        //$dia        = date("Y-m-d", $tiempo);
        
        $cSql   = "select id, grand_total, paid, date, tipoDoc from tec_sales".
            " where envio_electronico = 0 and tipoDoc != 'Ticket' and anulado!='1' and date(date) = ? order by id limit 70";  // ESTO NO ES +++++++++++++++++
        
        $query  = $this->db->query($cSql,array($fecha));
        
        $n = 0;
        $cad = "";
        foreach($query->result() as $r){
            $cad .= $fecha . " [" . $r->id . "] " . $r->date . " " . $r->tipoDoc . " ";
            if ($this->pos_model_apisperu->envio_masivo_individual($r->id) ){
                $cad .= "Correcto\n";
            }else{
                $cad .= "Mal\n";
            }
            usleep(1000000);
            $n++;
        }

        $gn = fopen("./tarea_programada.txt","a+");
        $msg = date("Y-m-d H:i:s") . " Se procesaron $n comprobantes.\n";
        fputs($gn, $msg);
        fclose($gn);

        $cad .= $msg;
        $gn = fopen("procesado_".$fecha.".txt","a");
        fputs($gn,$cad);
        fclose($gn);
    }

    public function envio_masivo_tarea_programada(){
        /*
        $tiempo     = strtotime(date("Y-m-d") . " -1 day");
        $dia        = date("Y-m-d", $tiempo);
        
        $cSql   = "select id, grand_total, paid from tec_sales 
            where envio_electronico = 0 and tipoDoc != 'Ticket' and anulado!=1 and date(date) >= curdate()-3 and mensaje_sunat=''";
        
        //$cSql   = "select id, grand_total, paid from tec_sales where envio_electronico = 0 and tipoDoc != 'Ticket' and anulado!=1 and date(date) >= '{$dia}' order by id";

        $query  = $this->db->query($cSql,array($dia));
        
        $n = 0;
        foreach($query->result() as $r){
            $this->pos_model_apisperu->envio_masivo_individual($r->id);
            usleep(800000);
            $n++;
        }

        $gn = fopen("./tarea_programada.txt","a+");
        $msg = date("Y-m-d H:i:s") . " Se procesaron $n comprobantes.\n";
        fputs($gn, $msg);
        fclose($gn);

        echo $msg;
        */
    }

}
?>