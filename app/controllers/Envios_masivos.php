<?php 
$timezone = "America/Lima";
date_default_timezone_set($timezone);

require_once APPPATH.'libraries/luecano/src/NumeroALetras.php';
use Luecano\NumeroALetras\NumeroALetras;

class Envios_masivos extends MY_Controller {

    function __construct() {
        parent::__construct();
       $this->load->helper('pos');
        $this->load->model('pos_model');
        $this->load->library('form_validation');
        $this->delivery = "";
    }

    public function envio_masivo_tarea_programada(){
        
        $tiempo     = strtotime(date("Y-m-d") . " -1 day");
        $dia        = date("Y-m-d", $tiempo);
        //$dia           = date("Y-m-d");
        $cSql   = "select id, grand_total, paid from tec_sales".
            " where envio_electronico = 0 and tipoDoc != 'Ticket' and anulado!=1 and date(date) >= '{$dia}' order by id";
        
        $query  = $this->db->query($cSql,array($dia));
        
        $n = 0;
        foreach($query->result() as $r){
            $this->pos_model->enviar_doc_sunat_nubefact_individual($r->id, true);
            sleep(1);
            $n++;
        }

        $gn = fopen("./tarea_programada.txt","a+");
        $msg = date("Y-m-d H:i:s") . " Se procesaron $n comprobantes.\n";
        fputs($gn, $msg);
        fclose($gn);

        echo $msg;
        
    }

    public function envio_masivo_t_p_fecha($fecha){
        
        //$tiempo     = strtotime($fecha . " +1 day");
        //$dia        = date("Y-m-d", $tiempo);
        
        $cSql   = "select id, grand_total, paid from tec_sales".
            " where envio_electronico = 0 and tipoDoc != 'Ticket' and anulado!='1' and date(a.date) = '{$fecha}' order by id limit 3";
        
        $query  = $this->db->query($cSql,array($dia));
        
        $n = 0;
        foreach($query->result() as $r){
            $this->pos_model->enviar_doc_sunat_nubefact_individual($r->id, true);
            sleep(1);
            $n++;
        }

        $gn = fopen("./tarea_programada.txt","a+");
        $msg = date("Y-m-d H:i:s") . " Se procesaron $n comprobantes.\n";
        fputs($gn, $msg);
        fclose($gn);

        echo $msg;
        
    }

}
?>