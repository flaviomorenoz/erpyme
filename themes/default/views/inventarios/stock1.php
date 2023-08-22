<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 

if(!isset($_SESSION["username"])){ die("Su sesion ha caducado."); }

//$store_id  : Viene desde el Controller
$nombre_tienda  = $this->db->select('state')->where('id',$store_id)->get('tec_stores')->row()->state; 

$inv_fec_inv = isset($inv_fec_inv) ? $inv_fec_inv : "";
$inv_fec_inv2 = isset($inv_fec_inv2) ? $inv_fec_inv2 : "";
$inv_store_id = isset($inv_store_id) ? $inv_store_id : "";
$inventario1 = isset($inventario1) ? $inventario1 : "";
$inventario2 = isset($inventario2) ? $inventario2 : "";
$el_csql = $cSql;

    function resaltado($nValor_faltante, $total_utilizado){
        $cEsti = ""; $nFactor = 0;
        if(abs($nValor_faltante) >= 0.5){
            $nUtilizado = floatval($total_utilizado);
            if($nUtilizado > 0){
                $nFactor = abs($nValor_faltante) / $nUtilizado;
                if($nFactor > 0.1){
                    $cEsti = "background-color:yellow;";
                }
            }else{
                $cEsti = "background-color:yellow;";
            }
        }
        return $cEsti;
    }
?>
<style type="text/css">
    .letra16{
        font-size: 16px;
        font-weight: bold;
    }
</style>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
    ?>
</script>

<section id="seccion1" class="content">
    
    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="display:flex;margin-bottom: 5px;">

        <div class="col-sm-8 col-md-7" style="border-style:none; border-color:red;">
            <h3>Stock Actual (Tda. <?= $nombre_tienda ?>)</h3>
            <p>Basado en Inventario <?= $ult_inventario ?></p>
        </div>

        <!--<div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: 20px 0px 20px 0px;">
            <div class="row">
                <div class="col-sm-5" style="padding:5px 0px 0px 0px;">
                    <button onclick="activo1()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/gastus/search.png") ?>" height="30px"></button>
                </div>
                <div class="col-sm-5" style="padding:5px 0px 0px 0px">
                    <button onclick="limpiar()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/gastus/eliminar.png") ?>" height="30px"></button>
                </div>
            </div>
        </div>-->
    </div>

    <?php if(!is_null($inventario1)){ 
        $estilo_celdita = "background-color:white;padding-right: 16px;";
        $estilo_tit = "background-color:rgb(205,205,205);text-align:center;";
    ?>
        <div class="row">
            <div class="col-sm-12 col-lg-9">
                <table class="table" style="border-style:solid; border-color:gray;">
                    <theader>
                        <tr>
                            <th style="<?= $estilo_tit ?>">Id</th>
                            <th style="<?= $estilo_tit ?>">Product_id</th>
                            <th style="<?= $estilo_tit ?>">Insumo</th>
                            <th style="<?= $estilo_tit ?>">Unidad</th>
                            <th style="<?= $estilo_tit ?>">Stock 1</th>
                            <th style="<?= $estilo_tit ?>">Comprado</th>
                            <th style="<?= $estilo_tit ?>">Utilizado</th>
                            <th style="<?= $estilo_tit ?>">Ingreso</th>
                            <th style="<?= $estilo_tit ?>">Salida</th>
                            <th style="<?= $estilo_tit ?>">Stock<br>Actual</th>
                            <?php
                                if(!is_null($inventario2) && $inventario2 != "0"){
                                    //echo "<th style=\"text-align:left\">-</th>";
                                }
                            ?>
                            <th style="<?= $estilo_tit ?>">Opciones</th>
                        </tr>
                    </theader>
                    <tbody>
                        <?php
                            if(isset($query_compara)){
                                foreach($query_compara->result() as $r){
                                    echo "<tr>";
                                    echo $this->fm->celda($r->id,1,$estilo_celdita);
                                    echo $this->fm->celda($r->product_id,1,$estilo_celdita);
                                    echo $this->fm->celda($r->name,0,$estilo_celdita."font-weight:bold;");
                                    echo $this->fm->celda($r->unidad,'0',$estilo_celdita.'color:rgb(210,210,210),height:15px');
                                    echo $this->fm->celda(number_format($r->contada,1),'2',$estilo_celdita.'font-weight:bold');
                                    echo $this->fm->celda(number_format($r->total_comprado,1),2,$estilo_celdita);
                                    echo $this->fm->celda(number_format($r->total_utilizado,2),2,$estilo_celdita);
                                    echo $this->fm->celda(number_format($r->ingreso,2),2,$estilo_celdita);
                                    echo $this->fm->celda(number_format($r->salida,2),2,$estilo_celdita);

                                    echo $this->fm->celda(number_format($r->stock,2),2,$estilo_celdita);
                                    $nValor_faltante = 0;
                                    echo $this->fm->celda($r->op,1,$estilo_celdita);

                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                    <tfooter>
                        
                    </tfooter>
                </table>
            </div>
        </div>
    <?php } ?>
        <div class="row">
            <div class="col-sm-12 col-lg-6">
                <?php
                    $cSql = "SELECT fecha, count(*) cantidad ".
                        " FROM tec_inventarios".
                        " where store_id = $inv_store_id".
                        " group by fecha".
                        " order by fecha desc limit 4";
                    $query = $this->db->query($cSql);
                    $nJ = 0;
                    foreach($query->result() as $r){
                        $nJ++;
                        if($nJ==1){
                            echo "<table class=\"table\" style=\"width:50%; border-style:solid; border-color:gray;\">".
                                "<caption>Nro. de Registros Ingresados:</caption>".
                                "<tr><th>Fecha</th><th>Cantidad</th></tr>";
                        }
                        echo "<tr>";
                        echo $this->fm->celda($r->fecha,1);
                        echo $this->fm->celda($r->cantidad,1);
                        echo "</tr>";
                    }
                    if($nJ > 0){
                        echo "</table>";
                    }
                    if($nJ == 0){
                        echo $this->fm->message("No se encontraron datos...",3);
                    }
                ?>
            </div>
        </div>
</section>

<section style="margin-left:25px">
    <button id="btn_formato" onclick="imprimir()" class="btn btn-warning">Mostrar Formato Impresion</button>
</section>

<section id="seccion2" style="margin-left:35px; border-style: solid; border-color:green;">
</section>

<section>
    <div class="row">
        <div class="col-sm-12" style="margin-left:10px">
            <br><h3>Detalle de Ventas:</h3>
<?php 
/*    
    if(isset($query_compara)){
        $query = $this->db->query($cSql_ventas); //->result_array();
        
        //$ar_cols = array("id", "numero", "date", "id_insumo", "name", "quantity", "cantidadReceta", "platillos");
        
        echo "<table border=\"1\">";
        echo "<tr style=\"background-color:white\"><th style=\"padding:3px\">id</th>
            <th style=\"padding:3px\">numero</th>
            <th style=\"padding:3px\">date</th>
            <th style=\"padding:3px\">id_insumo</th>
            <th style=\"padding:3px\">name</th>
            <th style=\"padding:3px\">Cantidad</th>
            <th style=\"padding:3px\">cantidadReceta</th>
            <th style=\"padding:3px\">platillos</th></tr>";
        
        $name_anterior = $valor_anterior = "";
        $acu_cant = $acu_cant_receta = $acu_cant_total = 0;

        foreach($query->result() as $r){
            echo "<tr style=\"\">";
            
            echo celda_adicional($r->id_insumo, $r->name, $valor_anterior, $name_anterior, $acu_cant, $acu_cant_total);

            echo $this->fm->celda($r->id,0,"padding:3px");
            echo $this->fm->celda($r->numero,0,"padding:3px");
            echo $this->fm->celda($r->date,0,"padding:3px");
            echo $this->fm->celda($r->id_insumo,0,"padding:3px");
            echo $this->fm->celda($r->name,0,"padding:3px");
            echo $this->fm->celda(number_format($r->quantity,0),1,"padding:3px");
            echo $this->fm->celda(number_format($r->cantidadReceta,2),1,"padding:3px");
            echo $this->fm->celda($r->platillos,0,"padding:3px");
            $valor_anterior = $r->id_insumo;
            $name_anterior = $r->name;
            $acu_cant           += $r->quantity * 1;
            $acu_cant_receta    += $r->cantidadReceta * 1;
            $acu_cant_total     += ($r->quantity * 1)*($r->cantidadReceta * 1);
            echo "</tr>";
        }
        echo "</table>";

        $wx = str_replace("\n", "<br>", $el_csql); 
        $wx = str_replace("\t", "&nbsp;&nbsp;", $wx);
    }

    $cSql_ventas = str_replace("\n", "<br>", $cSql_ventas); 

    function celda_adicional($id_insumo, $name, $valor_anterior, $name_anterior, &$acu_cant, &$acu_cant_total){
        if($id_insumo != $valor_anterior && strlen($valor_anterior."")>0 ){
            $cad = "<tr>";
            $cad .= "<th colspan=5 style=\"background-color:yellow;text-align:right\">Total {$name_anterior}:" . "</th>";
            $cad .= "<th style=\"background-color:yellow\">{$acu_cant}</th>";
            $cad .= "<th style=\"background-color:yellow\">{$acu_cant_total}</th>";
            $cad .= "<th style=\"background-color:yellow\">" . "</th>";
            $cad .= "</tr>";
            $acu_cant = $acu_cant_total = 0;
            return $cad;
        }
        return "";
    }
*/    
?>
        </div>
    </div>

    <?php if(isset($cSql_compras)){ ?>
    <div class="row">
        <div class="col-sm-12" style="margin-left:10px">
            <br><h3>Detalle de Compras:</h3>
            <table border="1">
                <tr>
                    <th style="padding:6px;background-color: white;">Id</th>
                    <th style="padding:6px;background-color: white;">Fecha Pago</th>
                    <th style="padding:6px;background-color: white;">fecha Ingreso</th>
                    <th style="padding:6px;background-color: white;">Proveedor</th>
                    <th style="padding:6px;background-color: white;">ID Producto</th>
                    <th style="padding:6px;background-color: white;">Producto</th>
                    <th style="padding:6px;background-color: white;">Cantidad</th>
                </tr>
            <?php
            /*    
                $this->db->reset_query();
                
                $query = $this->db->query($cSql_compras);
                $formato = "padding:6px";
                foreach($query->result() as $r){
                    echo "<tr>";
                    echo $this->fm->celda($r->id,0,$formato);
                    echo $this->fm->celda($r->date,0,$formato);
                    echo $this->fm->celda($r->date_ingreso,0,$formato);
                    echo $this->fm->celda($r->proveedor,0,$formato);
                    echo $this->fm->celda($r->product_id,0,$formato);
                    echo $this->fm->celda($r->name,0,$formato);
                    echo $this->fm->celda(number_format($r->quantity,0),1,$formato);
                    echo "</tr>";
                }
            */ 
            ?>
            </table>

        </div>
    </div>
    <?php
        //echo($cSql_compras);
        } 
    ?>
</section>

<div id="refresco"></div>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.datepicker').datetimepicker(
            {
                format: 'YYYY-MM-DD', 
                showClear: true, 
                showClose: true, 
                useCurrent: false, 
                widgetPositioning: 
                    {
                        horizontal: 'auto', 
                        vertical: 'bottom'
                    }, 
                widgetParent: $('.dataTable tfoot')
            }
        );

        setTimeout('$("#seccion2").hide()',200)

    });

    function activo1(){
        var inventario1 = document.getElementById("inventario1").value
        var inventario2 = document.getElementById("inventario2").value
        if(inventario1 == ""){ inventario1 = "null"}
        if(inventario2 == ""){ inventario2 = "null"}
        let cadena      = ""
        
        if(inventario1.length == 0){
            alert("Debe elegir Inventario 1")
            return false
        }

        if(inventario1.length > 0){
            cadena = '<a href="<?= base_url() ?>inventarios/stock/' + inventario1 + '/' + inventario2  + '" id="enlace_grilla_compras"></a>'
            document.getElementById('refresco').innerHTML = cadena
            setTimeout("document.getElementById('enlace_grilla_compras').click()",100)
        }

    }

    function limpiar(){
        $("#inventario1").val("")
        $("#inventario2").val("")
        //activo1()
    }

    function empty(data){
      if(typeof(data) == 'number' || typeof(data) == 'boolean')
      { 
        return false; 
      }
      if(typeof(data) == 'undefined' || data === null)
      {
        return true; 
      }
      if(typeof(data.length) != 'undefined')
      {
        return data.length == 0;
      }
      var count = 0;
      for(var i in data)
      {
        if(data.hasOwnProperty(i))
        {
          count ++;
        }
      }
      return count == 0;
    }

    function eliminar(id){
        if(Admin){
            if (confirm("Confirma que desea Eliminar?")){
                $.ajax({
                    data    :{id:id},
                    url     :'<?= base_url("inventarios/eliminar") ?>',
                    type    :"get",
                    success :function(res){
                        alert("se elimina correctamente");
                        location.reload()
                    }
                })
            }
        }else{
            alert("Este servicio es solo para Administradores.")
        }
    }

    function imprimir(){
        /*if(empty(document.getElementById("fecha").value)){
            alert("Debe ingresar en 'Fecha del Inventario 1' la fecha del ultimo inventario y Tienda")
            return false
        }

        if(document.getElementById("store_id").value == '0'){
            alert("Debe ingresar una Tienda")
            return false
        }*/

        $.ajax({
            data:{ 
                store_id : '<?= $store_id ?>', 
                fecha_inv : '',
                inventario1 : '<?= $inventario1 ?>'
            },
            type:'get',
            url:'<?= base_url("inventarios/formato_impresion") ?>',
            success: function(res){
                //console.log(res)
                var seccion = document.getElementById("seccion1")
                var seccion2 = document.getElementById("seccion2")
                if(seccion.style.display == 'block' || seccion.style.display == ''){
                    seccion.style.display = "none"
                    seccion2.style.display = "block"
                    seccion2.innerHTML = res
                    document.getElementById("btn_formato").innerHTML = "Ocultar Formato"
                }else{
                    //console.log(seccion.style.display)
                    seccion.style.display = "block"
                    seccion2.style.display = "none"
                    document.getElementById("btn_formato").innerHTML = "Mostrar Formato Impresion"
                }
            }
        })

    }

    function reconoce(id, product_id){
        if(Admin){
            let canti = prompt("Cantidad a reconocer en Kg/Litros")
            let Nombre = prompt("Persona que reconoce:")
            let tienda = document.getElementById("store_id").value
            
            if(empty(tienda)){
                alert("Debe ingresar tienda");
                return false
            }
            if (canti != null) {
                $.ajax({
                    data    :{product_id:product_id, inv_id:id, cantidad:canti, store_id: tienda, persona: Nombre},
                    url     :'<?= base_url("inventarios/save_movim") ?>',
                    type    :"get",
                    success :function(res){
                        var obj = JSON.parse(res)
                        location.reload()
                        if(obj.rpta == "success"){
                            alert("se graba correctamente");
                        }else{
                            alert("No se pudo grabar")
                        }
                        
                    }
                })
            }
        }else{
            alert("Este servicio es solo para Administradores.")
        }
    }

</script>
