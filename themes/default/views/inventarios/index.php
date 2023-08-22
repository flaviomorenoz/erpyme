<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 

if(!isset($_SESSION["username"])){ die("Su sesion ha caducado."); }

$store_id = $_SESSION["store_id"];

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

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Inventario 1:</label>
                <?php
                    $cSql = "select a.id, concat(a.fecha,'_',b.state) descrip from tec_maestro_inv a 
                        inner join tec_stores b on a.store_id = b.id
                        order by a.id desc";
                    $result = $this->db->query($cSql)->result_array();
                    $ar = array();
                    foreach($result as $r){ 
                        $ar[$r["id"]] = $r["descrip"];
                    }
                    echo form_dropdown('inventario1', $ar, $inventario1, 'class="form-control tip" id="inventario1" required="required"');

                ?>
            </div>    
        </div>

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Inventario 2:</label>
                <?php
                    $cSql = "select a.id, concat(a.fecha,'_',b.state) descrip from tec_maestro_inv a 
                        inner join tec_stores b on a.store_id = b.id
                        order by a.id desc";
                    $result = $this->db->query($cSql)->result_array();
                    $ar = array("");
                    foreach($result as $r){ 
                        $ar[$r["id"]] = $r["descrip"];
                    }
                    echo form_dropdown('inventario2', $ar, $inventario2, 'class="form-control tip" id="inventario2" required="required"');
                ?>
            </div>
        </div>    

        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tienda:</label>
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

        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: 20px 0px 20px 0px;">
            <div class="row">
                <div class="col-sm-5" style="padding:5px 0px 0px 0px;">
                    <button onclick="activo1()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/gastus/search.png") ?>" height="30px"></button>
                </div>
                <div class="col-sm-5" style="padding:5px 0px 0px 0px">
                    <button onclick="limpiar()" class="btn" style="background-color:white;margin:0px;padding:1px;"><img src="<?= base_url("themes/default/views/gastus/eliminar.png") ?>" height="30px"></button>
                </div>
            </div>
        </div>
    </div>

    <?php if(!is_null($inventario1)){ ?>
        <div class="row">
            <div class="col-sm-12 col-lg-9">
                <table class="table" style="border-style:solid; border-color:gray;">
                    <theader>
                        <tr>
                            <th style="text-align:left">Id</th>
                            <th style="text-align:left">Insumo</th>
                            <th style="text-align:left">Unidad</th>
                            <th style="text-align:left">Stock 1</th>
                            <th style="text-align:left">Comprado</th>
                            <th style="text-align:left">Utilizado</th>
                            <th style="text-align:left">Ingreso</th>
                            <th style="text-align:left">Salida</th>
                            <th style="text-align:left">Stock<br>Esperado</th>
                            <?php
                                
                                if(!is_null($inventario2) && $inventario2 != "0"){
                                    //die(gettype($inventario2));
                                    //die("Inventario2:".$inventario2);
                                    echo "<th style=\"text-align:left\">Stock 2</th>";
                                    echo "<th style=\"text-align:left;color:red\">Diferencias</th>";
                                    echo "<th style=\"text-align:left\">-</th>";
                                }
                            ?>
                            <th style="text-align:left">Opciones</th>
                        </tr>
                    </theader>
                    <tbody>
                        <?php
                            if(isset($query_compara)){
                                foreach($query_compara->result() as $r){
                                    echo "<tr>";
                                    echo $this->fm->celda($r->id);
                                    echo $this->fm->celda($r->name,0,"font-weight:bold;");
                                    echo $this->fm->celda($r->unidad,'0','color:rgb(210,210,210),height:15px');
                                    echo $this->fm->celda(number_format($r->contada,1),'2','font-weight:bold');
                                    echo $this->fm->celda(number_format($r->total_comprado,1),2);
                                    echo $this->fm->celda(number_format($r->total_utilizado,2),2);
                                    echo $this->fm->celda(number_format($r->ingreso,2),2);
                                    echo $this->fm->celda(number_format($r->salida,2),2);

                                    echo $this->fm->celda(number_format($r->stock,2),2);
                                    $nValor_faltante = 0;
                                    if(!is_null($inventario2) && $inventario2 != "0"){
                                        echo $this->fm->celda(number_format($r->segundo_stock,2),'2','font-weight:bold');
                                        
                                        $nValor_faltante = ($r->segundo_stock*1) - ($r->stock*1);

                                        // ********************
                                        $cEsti = resaltado($nValor_faltante, $r->total_utilizado); 
                                        // ********************

                                        echo $this->fm->celda(number_format($nValor_faltante,1),2,"font-weight:bold;color:rgb(30,60,130);font-size:16px;{$cEsti}");

                                        echo $this->fm->celda($r->op2);
                                    }
                                    echo $this->fm->celda($r->op);

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

<section id="seccion2" style="margin-left:35px">
</section>

<section>
    <div class="row">
        <div class="col-sm-12" style="margin-left:10px">
            <br><h3>Detalle de Ventas:</h3>
<?php 
    
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
        //echo $wx;
    }

    $cSql_ventas = str_replace("\n", "<br>", $cSql_ventas); 
    //echo $cSql_ventas;

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
            cadena = '<a href="<?= base_url() ?>inventarios/index/' + inventario1 + '/' + inventario2  + '" id="enlace_grilla_compras"></a>'
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
        if(empty(document.getElementById("fecha").value)){
            alert("Debe ingresar en 'Fecha del Inventario 1' la fecha del ultimo inventario y Tienda")
            return false
        }

        if(document.getElementById("store_id").value == '0'){
            alert("Debe ingresar una Tienda")
            return false
        }

        $.ajax({
            data:{ store_id : document.getElementById("store_id").value, fecha_inv : document.getElementById("fecha").value},
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
