<?php 
    echo form_open_multipart("inventarios/save_nuevo_inventario", 'id="form1"'); 
    $id             = isset($id) ? $id : "";
    $tienda         = isset($store_id) ? $store_id : "";
    $fecha          = isset($fecha) ? $fecha : "";
    $hora_ini       = isset($hora_ini) ? $hora_ini : "";
    $hora_fin       = isset($hora_fin) ? $hora_fin : "";
    $responsable    = isset($responsable) ? $responsable : "";
    $responsable_tda    = isset($responsable_tda) ? $responsable_tda : "";
?>

<section class="content">

    <div class="row" style="margin-bottom: 10px;">
        <div class="col-xs-4 col-sm-3 col-lg-2">
            <input type="hidden" name="id" id="id" value="<?= $id ?>">
            <label>Tienda</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    $q = $this->db->get('stores');

                    if ($group_id == '1'){
                        $ar[] = "Todas";
                        foreach($q->result() as $r){
                            $ar[$r->id] = $r->state;
                        }
                    }else{
                        foreach($q->result() as $r){
                            if($r->id == $this->session->userdata["store_id"]){
                                $ar[$r->id] = $r->state;
                            }
                        }
                    }
                    echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required"');
                ?>
        </div>
    </div>

    <div class="row" style="margin-bottom: 10px;">
        <div class="col-xs-4 col-sm-3 col-lg-2">
            <label>Fecha:</label>
            <?php
                $ar = array(
                   "name"  =>"fecha",
                   "id"    =>"fecha",
                   "type"  =>"date",
                   "value" => $fecha,
                   "class" =>"form-control tip"
                );
                echo form_input($ar);
            ?>
        </div>
        <?php if(isset($id)){ ?>    
            <div class="col-xs-4 col-sm-2">
                <label>Hora inicio:</label>
                <?php
                    $ar = array(
                       "name"  =>"hora_ini",
                       "id"    =>"hora_ini",
                       "type"  =>"text",
                       "value" => $hora_ini, 
                       "class" =>"form-control tip",
                       "maxlength" => "5"
                    );
                    echo form_input($ar);
                ?>
            </div>
            
            <div class="col-xs-4 col-sm-2">
                <label>Hora Fin:</label>
                <?php
                    $ar = array(
                       "name"  =>"hora_fin",
                       "id"    =>"hora_fin",
                       "type"  =>"text",
                       "value" => $hora_fin,
                       "class" =>"form-control tip",
                       "maxlength" => "5"
                    );
                    echo form_input($ar);
                ?>
            </div>
        <?php } ?>
    </div>

    <div class="row" style="margin-bottom: 10px;">
        <div class="col-xs-4 col-sm-3 col-lg-2">
            <label>Auditor:</label>
            <?php
                $ar = array(
                   "name"  =>"responsable",
                   "id"    =>"responsable",
                   "type"  =>"text",
                   "value" => $responsable,
                   "class" =>"form-control tip"
                );
                echo form_input($ar);
            ?>
        </div>

        <div class="col-xs-4 col-sm-3 col-lg-2">
            <label>Responsable Tienda:</label>
            <?php
                $ar = array(
                   "name"  =>"responsable_tda",
                   "id"    =>"responsable_tda",
                   "type"  =>"text",
                   "value" => $responsable_tda,
                   "class" =>"form-control tip"
                );
                echo form_input($ar);
            ?>
        </div>

    </div>

    <div class="row" style="margin-top: 20px ;margin-bottom: 10px;">
        <div class="col-xs-6 col-sm-4">
            <button type="submit" class="btn btn-success"><?= (strlen($id) > 0 ? "Guardar" : "Iniciar Ingreso"); ?></button>
            <input type="hidden" name="id" id="id" value="<?= $id ?>">
        </div>
    </div>

    <div class="row" style="margin-top: 20px; margin-bottom: 10px;">
        <div class="col-xs-6 col-sm-7">
            <h3 style="color:rgb(100,100,100)">Ultimos Inventarios:</h3>
            <?php
                $ruta = base_url("inventarios/nuevo_inventario/");
                $cSql = "select a.id, a.store_id, b.state tienda, a.fecha, a.hora_ini, a.hora_fin, a.responsable, a.responsable_tda
                from tec_maestro_inv a
                inner join tec_stores b on a.store_id = b.id
                order by a.id desc";
                //die($cSql);
                $result = $this->db->query($cSql)->result_array();
                $cols = array("id", "store_id", "tienda", "fecha", "hora_ini", "hora_fin", "responsable", "responsable_tda");
                echo $this->fm->crea_tabla_result($result, $cols, $cols, $ar_align = array(), $ar_pie = array());
            ?>
        </div>
    </div>

</section>

<?= form_close(); ?>

<script type="text/javascript">
    $.ajax({

    })
</script>
