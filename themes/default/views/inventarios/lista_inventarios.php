<?php 
    echo form_open_multipart("inventarios/save_nuevo_inventario", 'id="form1"'); 
/*  $id             = isset($id) ? $id : "";
    $tienda         = isset($store_id) ? $store_id : "";
    $fecha          = isset($fecha) ? $fecha : "";
    $hora_ini       = isset($hora_ini) ? $hora_ini : "";
    $hora_fin       = isset($hora_fin) ? $hora_fin : "";
    $responsable    = isset($responsable) ? $responsable : "";
    $responsable_tda    = isset($responsable_tda) ? $responsable_tda : "";
*/
?>

<section class="content">

    <div class="row" style="margin-top: 20px; margin-bottom: 10px;">
        <div class="col-xs-12 col-sm-10" style="background-color: white;padding:5px;">
            <?php
                $ruta   = base_url("inventarios/nuevo_inventario/");
                $ruta1  = base_url("inventarios/cerrar_inventario/");
                $ruta2  = base_url("inventarios/add/");
                $ruta3  = base_url("inventarios/sincerar/");
                $ruta4  = base_url("inventarios/resultado/");
                $cSql = "select a.id, a.store_id, b.state tienda, a.fecha, a.hora_ini, a.hora_fin, a.responsable, a.responsable_tda, c.cant,
                if(a.hora_fin='','Abierto','Cerrado') estado,
                '' as op
                from tec_maestro_inv a
                inner join tec_stores b on a.store_id = b.id
                left join(
                  select inv_id, count(*) cant from tec_movim group by inv_id 
                ) c on a.id=c.inv_id
                order by a.id desc limit 10";
                $result = $this->db->query($cSql)->result_array();
                
                // le añado un botoncito
                //if($_SESSION["username"] == 'admin'){
                    $i=0;
                    foreach($result as &$r){
                        $i++;

                        if(trim($r["hora_fin"]) == ""){
                            $r["op"] .= "<a href=\"{$ruta1}" . $r["id"] . "\" title=\"Cerrar\"><span class=\"glyphicon glyphicon-log-out iconos\" style=\"color:red\"></span></a>&nbsp;&nbsp;";
                        }

                        if($_SESSION["username"] == 'admin'){
                            $r["op"] .= "<a href=\"{$ruta}" . $r["id"] . "\" title=\"Editar\"><span class=\"glyphicon glyphicon-edit iconos\"></span></a>&nbsp;&nbsp;";
                        }

                        $r["op"] .= "<a href=\"{$ruta2}" . $r["id"] . "\" title=\"Registrar\"><span class=\"glyphicon glyphicon-ok-sign iconos\"></span></a>&nbsp;&nbsp;";

                        if( ($r["cant"]=="0" || is_null($r["cant"])) && a.hora_fin!=''){
                            $r["op"] .= "<a href=\"{$ruta3}" . $r["id"] . "/" . $r["store_id"] . '" title="Sincerar\"><span class="glyphicon glyphicon-cog iconos" style="color:red"></span></a>&nbsp;&nbsp;';
                        }else{
                            $r["op"] .= "<a href=\"$ruta3" . $r["id"] . "/" . $r["store_id"] . '" title="Sincerar"><span class="glyphicon glyphicon-cog iconos" style="color:lightgray"></span></a>&nbsp;&nbsp;';
                        }

                        $r["op"] .= "<a href=\"$ruta4" . $r["id"] . "\" title=\"Ver\" target=\"_blank\"><span class=\"glyphicon glyphicon-eye-open iconos\" style=\"color:orange\"></span></a>";
                    }
                //}
                $cols = array("id", "tienda", "fecha", "hora_ini", "hora_fin", "responsable", "responsable_tda", "estado", "op");
                $ar_align = array('0','0','0','0','0','0','0','0','2');
                echo $this->fm->crea_tabla_result($result, $cols, $cols, $ar_align, $ar_pie = array());
            ?>
        </div>
    </div>

</section>

<?= form_close(); ?>

<script type="text/javascript">
    $.ajax({

    })
</script>
