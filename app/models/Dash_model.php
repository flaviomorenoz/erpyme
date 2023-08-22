<?php 
//if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dash_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function facturacion_electronica(){
        $cSql = "select date(a.date) as fecha, sum(if(envio_electronico > 0,1,0)) as envio, count(*) - sum(if(envio_electronico > 0,1,0)) obs, count(*) cantidad, obss.obis, sin_pagos.obos
            from tec_sales a
            left join (
              select date(date) fecha, GROUP_CONCAT(id) obis from tec_sales 
              where envio_electronico = 0 and grand_total > 0 and tipoDoc in ('Boleta','Factura','')
              group by date(date)
            ) obss on date(date) = obss.fecha   
            left join (
                select sin_mon.fecha, group_concat(sin_mon.id) obos from (
                    select tec_sales.id, date(tec_sales.date) fecha
                    from tec_sales
                    left join tec_payments on tec_sales.id = tec_payments.sale_id
                    where tec_sales.date > '2022-01-01' and tec_sales.grand_total > 0
                    group by tec_sales.id, date(tec_sales.date)
                    having sum(if(tec_payments.amount is null,0,tec_payments.amount)) = 0
                ) as sin_mon
                group by sin_mon.fecha
            ) sin_pagos on date(a.date) = sin_pagos.fecha
            where a.grand_total > 0 and date(a.date) < curdate()
            group by date(a.date)
            order by date(a.date) desc limit 8";

        $result = $this->db->query($cSql)->result_array();

        $cols           = array("fecha", "envio", "obs", "cantidad", "obis", "obos");
        $cols_titulos   = array("Fecha", "envio", "Obs", "Cantidad", "Id detalle", "Sin Pagos");

        return $this->fm->crea_tabla_result($result, $cols, $cols_titulos);
    }

    public function auditoria_recetas(){
        $result = $this->db->select("id, user, accion, id_inmerso, fecha_hora, obs")->from("tec_audi_recetas")->order_by("id","desc")->limit(10)->get()->result_array();

        $cols           = array("id", "user", "accion", "id_inmerso", "fecha_hora", "obs");
        $cols_titulos   = array("id", "user", "accion", "id_inmerso", "fecha_hora", "obs");

        return $this->fm->crea_tabla_result($result, $cols, $cols_titulos);
    }
}
