<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
        <?= $Settings->rtl ? '<link href="'.$assets.'dist/css/rtl.css" rel="stylesheet" />' : ''; ?>
        
        <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
        
        <script type="text/javascript" src="<?= $assets ?>toastr-master/toastr.js"></script>
        
        <link href="<?= $assets ?>toastr-master/build/toastr.css" rel="stylesheet"/>
    </head>
    <body>
<?php
// DETALLE DE VENTAS

$query2         = $this->db->query($cadena_query_ventas);

$madre          = $cadena_query_ventas; 

$query2     = $this->db->query($cadena_query_ventas);
$query2a    = $this->db->query($cadena_query_ventas);
//die("Perse : $cadena_query_ventas");
$query2b    = $this->db->query($cadena_query_ventas); // Para el grafico

$query_cuadre   = $this->db->query($cadena_query);

$bandera_r2 = 0;
foreach($query2a->result() as $r){
    $bandera_r2++; 
}

//$query      = $this->db->query($cadena_query);

$simbolo    = "<span style=\"color:red\">S/</span>&nbsp;&nbsp;&nbsp;";

?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
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

    </script>

    <style type="text/css">
        .titulon{
            background-color: rgb(80,80,80);
            color:  white;
            font-weight: bold;
        }

        .titulon_pie{
            background-color: rgb(80,80,80);
            color:  white;
            font-weight: bold;
            text-align: right;
        }

        .celda_titulon{
            border-color: white;
        }
        .resu{
            background-color: rgb(80,80,80);
            color:  white;
            font-weight: bold;
        }
    </style>

    <?php include("salidas_por_dia_tablas.php"); ?>



</section>

    <style type="text/css">
        .etiq_x{
            font-family: arial;
            font-weight: bold;
        }

        .titulo_graf{
            font-family: arial;
            color: red;
            font-weight: bold;
            font-size: 18px;
        }

        .titulo_acumulado{
            font-family: arial;
            color: rgb(100,130,160);
            font-weight: bold;
            font-size: 16px;
            
        }

    </style>

    <section style="margin-left:20px">
        <div class="row">
            <div class="col-sm-10 col-md-6">
            <?php
                $cSql = "select sum(grand_total) totalisimo from tec_sales".
                    " where extract(month from date) = extract(month from curdate()) and extract(year from date) = extract(year from curdate()) and store_id = ?";

                $query = $this->db->query($cSql,array($tienda));

                foreach($query->result() as $r){
                    $totalisimo = $r->totalisimo;
                }
                
                echo "<br><div class=\"titulo_acumulado\" style=\"font-weight:bold\"><span style=\"background-color: yellow;\">Total acumulado del Mes para la Tienda $tienda_descrip : &nbsp;&nbsp;&nbsp; S/ " . number_format($totalisimo,2) . "</span><div>";
            ?>
            </div>
        </div>
    </section>

    </body>
</html>
