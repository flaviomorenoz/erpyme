<?php
  $metodo       = "";
  $unidad       = "";
  $store_id_destino = "";
  $fechah       = "";
  $inv_store_id = "";
  $tipo_mov     = "";
  $cantidad     = "";
  $obs          = "";
  $store_id = $_SESSION["store_id"];
  
  error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
  //echo "Display errors: ". ini_get('display_errors')."<br>";
  //echo "Error_reporting : " . ini_get('error_reporting')."<br>";
?>
<style type="text/css">
    .mostrada{
        border-style: none;
        border-width: 1px;
        border-color: gray;
        margin-top: 10px;
    }
</style>
<section class="content">

    <?php if(isset($mensaje)){ ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="alert alert-<?= $rpta ?>"><?= $mensaje ?></div>
        </div>
    </div>
    <?php } ?>

    <?= form_open_multipart("inventarios/add_movimientos", 'class="validation" id="form_compra"'); ?>
    <input type="hidden" name="modo" id="modo" value="insert">
    <div class="row">

        <div class="col-xs-6 col-md-4 col-lg-2 mostrada">
            <label>Fecha:</label>
            <?php
                $ar = array(
                    "name"  =>"fechah",
                    "id"    =>"fechah",
                    "type"  =>"datetime-local",
                    "value" => $fechah,
                    "class" =>"form-control"
                );
                echo form_input($ar);
            ?>
        </div>

        <div class="col-xs-6 col-sm-4 mostrada">
            <div class="form-group">
                <label for="">Origen:</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    $q = $this->db->get('stores');

                    $ar = array();
                    if ($group_id == '1'){
                        $ar[] = "Todas";
                        foreach($q->result() as $r){
                            $ar[$r->id] = $r->state;
                        }
                    }else{
                        foreach($q->result() as $r){
                            if($r->id == $store_id){
                                $ar[$r->id] = $r->state;
                            }
                        }
                    }
                    echo form_dropdown('store_id', $ar, $inv_store_id, 'class="form-control tip" id="store_id" required="required"');
                    
                ?>
            </div>
        </div>

        <div class="col-xs-6 col-sm-4 mostrada">
            <div class="form-group">
                <label for="">Destino:</label>
                <?php
                    $group_id = $this->session->userdata["group_id"];
                    $q = $this->db->get('stores');

                    $ar = array();
                    $ar[] = "Todas";
                    foreach($q->result() as $r){
                        $ar[$r->id] = $r->state;
                    }
                    echo form_dropdown('store_id_destino', $ar, $store_id_destino, 'class="form-control tip" id="store_id_destino" required="required"');
                ?>
            </div>
        </div>

    </div>

    <div class="row">

        <!--<div class="col-xs-6 col-sm-2 mostrada">
            <label>Tipo Mov:</label>
            <?php
                $ar = array();
                foreach($tipos_mov->result() as $r){
                    $ar[$r->tipo_mov] = $r->descrip;
                }
                echo form_dropdown('tipo_mov', $ar, $tipo_mov, 'class="form-control tip" id="tipo_mov" required="required"');
            ?>
        </div>-->

        <div class="col-xs-6 col-sm-2 mostrada">
            <label>Metodo:</label>
            <?php
                $ar = array();
                $tipo_metodos = $this->db->select('id, metodo')->where('id<>',3)->get("tec_metodos_inv");
                foreach($tipo_metodos->result() as $r){
                    if($r->metodo != 'PASE'){
                        $ar[$r->id] = $r->metodo;
                    }
                }
                echo form_dropdown('metodo', $ar, $metodo, 'class="form-control tip" id="metodo" required="required"');
            ?>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-4 mostrada">
            <label>Producto:</label>
            <?php
                $ar = $this->inventarios_model->productos();
                echo form_dropdown('producto',$ar,'','class="form-control tip" id="producto" required="required"');
            ?>
        </div>
        <div class="col-xs-4 col-sm-2 mostrada">
            <label>Unidad:</label>
            <?php
                $ar = $this->inventarios_model->unidades();
                echo form_dropdown('unidad',$ar,$unidad,'class="form-control tip" id="unidad" required="required"');
            ?>
        </div>
        <div class="col-xs-6 col-sm-2 mostrada">
            <label>Cantidad:</label>
            <?php
                $ar = array(
                    "name"  =>"cantidad",
                    "id"    =>"cantidad",
                    "type"  =>"text",
                    "value" => $cantidad,
                    "class" =>"form-control tip",
                    "required" => "required"
                );
                echo form_input($ar);
            ?>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-8 mostrada">
            <label>Observaciones:</label>
            <?php
                $ar = array(
                    "name"  =>"obs",
                    "id"    =>"obs",
                    "type"  =>"text",
                    "value" => $obs,
                    "class" =>"form-control tip"
                );
                echo form_input($ar);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-4 mostrada">
            <button type="submit" class="btn btn-success" onclick="validar()">Grabar</button>
        </div>
    </div>

    <?= form_close(); ?>

    <div class="row">
        <div class="col-sm-12 mostrada">
            <h2>Pases por Confirmar hacia tu Tienda:</h2>
            <?php
                // $store_id
                $cSql = "select tm.id, tm.product_id, tp.name producto, tm.cantidad, tm.fechah, tov.descrip tipo_mov, tm.store_id_destino, ts2.state tienda_origen, ts.state tienda_destino 
                    from tec_movim tm
                    inner join tec_products tp on tm.product_id = tp.id
                    inner join tec_tipos_mov tov on tm.tipo_mov = tov.tipo_mov
                    left join tec_stores ts on tm.store_id_destino = ts.id
                    left join tec_stores ts2 on tm.store_id = ts2.id
                    where 1=1 and tm.store_id_destino = {$store_id} and tm.confirmado = '0'
                    order by tm.id desc limit 30";
                //echo $cSql;
                $query = $this->db->query($cSql);

                $mi_estilo = "padding:5px 10px;";
                $nC = 0;
                foreach($query->result() as $r){
                    $nC++;
                    if($nC == 1){
                        echo "<table border='1'>";
                        echo "<tr><th class='mi_estilo'>Producto</th><th class='mi_estilo'>Cantidad</th><th class='mi_estilo'>Fecha</th><th class='mi_estilo'>Tipo_mov</th>".
                            "<th class='mi_estilo'>Tienda<br> Origen</th><th class='mi_estilo'>Tienda<br> Destino</th><th class='mi_estilo'>.</th></tr>";
                    }

                    echo "<tr>";
                    echo $this->fm->celda($r->producto,0,$mi_estilo);
                    echo $this->fm->celda($r->cantidad,0,$mi_estilo);
                    echo $this->fm->celda($r->fechah,0,$mi_estilo);
                    echo $this->fm->celda($r->tipo_mov,0,$mi_estilo);
                    echo $this->fm->celda($r->tienda_origen,0,$mi_estilo);
                    echo $this->fm->celda($r->tienda_destino,0,$mi_estilo);
                    echo $this->fm->celda("<a href='#' onclick='confirmar_pase(" . $r->id . ")'>Confirmar</a>",0,$mi_estilo);
                    echo "</tr>";
                }
                if($nC>0){ echo "</table>";}else{ echo "No hay pases por confirmar.";}
            ?>            
        </div>
    </div>

</section>

<script type="text/javascript">
    function validar(){
        //document.getElementById("form_compra").submit()
    }

    function confirmar_pase(nId){
        console.log("A enviar:" + nId)
        $.ajax({
            data    :{id:nId},
            url     :'<?= base_url("inventarios/confirmar_pases") ?>',
            type    :'get',
            success :function (res){
                if(res == 'OK'){
                    location.reload()
                    alert("Se confirma el Pase.")
                }else{
                    alert("!No se pudo confirmar el Pase!")
                }
            }
        })
    }
</script>
