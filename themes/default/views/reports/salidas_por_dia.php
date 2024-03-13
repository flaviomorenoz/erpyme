<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

// DETALLE DE VENTAS

//die($cadena_query_ventas);
$query2         = $this->db->query($cadena_query_ventas);

$query_cuadre   = $this->db->query($cadena_query);

$simbolo        = "<span style=\"color:red\">S/</span>&nbsp;&nbsp;&nbsp;";

?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        //echo "setTimeout('abrir_item_menu(5)',500);\n";
    ?>
</script>

<style type="text/css">
    .table td:nth-child(3) { text-align: right;}
    .table td:nth-child(4) { text-align: right;}
    .table td:nth-child(5) { text-align: right;}
    .table td:nth-child(6) { text-align: right;}
    .table td:nth-child(7) { text-align: right;}
    .table td:nth-child(8) { text-align: right;}
    .table td:nth-child(9) { text-align: right;}
    .table td:nth-child(10) { text-align: right;}
    .table td:nth-child(11) { text-align: center;}
</style>

<section class="content">

    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="display:flex;margin-bottom: 5px;">
        <div class="col-sm-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $q = $this->db->get('stores');
                    //$ar[] = "Todas";
                    $ar = array();
                    foreach($q->result() as $r){
                        $ar[$r->id] = $r->state;
                    }
                    echo form_dropdown('tienda', $ar, $tienda, 'class="form-control tip" id="tienda" required="required" onchange="recalcar()"');
                ?>
            </div>
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Inicio&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                <input type="date" id="fec_ini" name="fec_ini" value="<?= $fec_ini ?>" class="form-control tip">
            </div>
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Fin&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                <input type="date" id="fec_fin" name="fec_fin" value="<?= $fec_fin ?>" class="form-control tip">
            </div>
        </div>

        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: 25px 0px 20px 0px;">
            <button onclick="activo1()" class="btn btn-primary">Consultar</button>
        </div>

    </div>

    <script type="text/javascript">
        setTimeout("recalcar()",300)

        function activo1(){
            let desde       = document.getElementById("fec_ini").value
            let hasta       = document.getElementById("fec_fin").value
            if(desde == ""){ desde = "null" }
            if(hasta == ""){ hasta = "null" }
            let tienda      = document.getElementById("tienda").value
            let cadenon     = "<?= base_url() ?>reports/salidas_por_dia?tienda="+$("#tienda").val()+"&"+"fec_ini="+desde+"&"+"fec_fin="+hasta
            window.location.assign(cadenon)
        }

        function mostrar_ventas(){
            if(document.getElementById('resumen_ventas').style.display == 'none'){
                $("#resumen_ventas").show(1000)
            }else{
                $("#resumen_ventas").hide(1000)
            }
        }

        function recalcar(){
            /* Para obtener el texto */
            let combo = document.getElementById("tienda");
            let selected = combo.options[combo.selectedIndex].text;
            document.getElementById("titulo_recalcar").innerHTML = selected
        }

    </script>

    <div class="row" style="border-style: transparent; border-width:2px; border-color:red; margin: 10px;">
        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red">
            
        </div>
        <div class="col-sm-8 col-md-9" style="border-style:none; border-color:red">
            <h2 id="titulo_recalcar" style="margin:0px"></h2>
        </div>
    </div>

    <?php include("salidas_por_dia_tablas.php"); ?>

</section>
