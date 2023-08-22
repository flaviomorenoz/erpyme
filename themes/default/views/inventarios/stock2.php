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

        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red;">
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

        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                
                <?php
                    //if($tipo_stock == '2'){
                        echo "<label for=\"\">Inventario 2:</label>";
                        $cSql = "select a.id, concat(a.fecha,'_',b.state) descrip from tec_maestro_inv a 
                            inner join tec_stores b on a.store_id = b.id
                            order by a.id desc";
                        $result = $this->db->query($cSql)->result_array();
                        $ar = array("");
                        foreach($result as $r){ 
                            $ar[$r["id"]] = $r["descrip"];
                        }
                        echo form_dropdown('inventario2', $ar, $inventario2, 'class="form-control tip" id="inventario2" required="required" '. ($tipo_stock == '1' ? 'readonly' : ''));
                    //}
                ?>
            </div>
        </div>    

        <div class="col-sm-3 col-md-2" style="border-style:none; border-color:red;">
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

        <div id="preparo" class="col-sm-2 col-md-1" style="border-style:none; border-color:red; margin: 20px 0px 20px 0px;">
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

    <?php if($op == '4'){ ?>
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <table class="table" style="border-style:solid; border-color:gray; border-width:1px;">
                    <theader>
                        <tr>
                            <th style="text-align:left;background-color: rgb(205,205,205);">Id</th>
                            <th style="text-align:left;background-color: rgb(205,205,205);">Insumo</th>
                            <th style="text-align:left;background-color: rgb(205,205,205);">Unidad</th>
                            <th style="text-align:center;background-color: rgb(205,205,205)">Stock<br><?= $inventario1_descrip ?></th>
                            <th style="text-align:left;background-color: rgb(205,205,205);">Comprado</th>
                            <th style="text-align:left;background-color: rgb(205,205,205);">Utilizado</th>
                            <th style="text-align:left;background-color: rgb(205,205,205);">Ingreso</th>
                            <th style="text-align:left;background-color: rgb(205,205,205);">Salida</th>
                            <th style="text-align:left;background-color: rgb(205,205,205);">Stock<br>Esperado</th>
                            <?php
                                
                                if(!is_null($inventario2) && $inventario2 != "0"){
                                    //die(gettype($inventario2));
                                    //die("Inventario2:".$inventario2);
                                    echo "<th style=\"text-align:center;background-color: rgb(205,205,205);\">Stock<br>{$inventario2_descrip}</th>";
                                    echo "<th style=\"text-align:left;color:darkred;background-color: rgb(205,205,205);\">Difiere</th>";
                                    echo "<th style=\"text-align:left;background-color: rgb(205,205,205);\">-</th>";
                                }
                            ?>
                            <th style="text-align:left;background-color:rgb(205,205,205);">&nbsp;</th>
                        </tr>
                    </theader>
                    <tbody>
                        <?php
                            $estilo_celdita = "background-color:white;";
                            if(isset($query_compara)){
                                foreach($query_compara->result() as $r){
                                    echo "<tr>";
                                    echo $this->fm->celda($r->id,0,$estilo_celdita);
                                    echo $this->fm->celda($r->name,0,"background-color:white;font-weight:bold;");
                                    echo $this->fm->celda($r->unidad,'0','background-color:white;color:rgb(210,210,210),height:15px');
                                    echo $this->fm->celda(number_format($r->contada,1),'2',$estilo_celdita.'font-weight:bold;padding-right:20px;');
                                    echo $this->fm->celda(number_format($r->total_comprado,1),2,$estilo_celdita);
                                    echo $this->fm->celda(number_format($r->total_utilizado,2),2,$estilo_celdita);
                                    echo $this->fm->celda(number_format($r->ingreso,2),2,$estilo_celdita);
                                    echo $this->fm->celda(number_format($r->salida,2),2,$estilo_celdita);

                                    echo $this->fm->celda(number_format($r->stock,2),2,$estilo_celdita);
                                    $nValor_faltante = 0;
                                    if(!is_null($inventario2) && $inventario2 != "0"){
                                        echo $this->fm->celda(number_format($r->segundo_stock,2),'2',$estilo_celdita.'font-weight:bold;padding-right:20px;');
                                        
                                        $nValor_faltante = ($r->segundo_stock*1) - ($r->stock*1);

                                        // ********************
                                        $cEsti = resaltado($nValor_faltante, $r->total_utilizado); 
                                        // ********************

                                        echo $this->fm->celda(number_format($nValor_faltante,1),2,"background-color:white;font-weight:bold;color:rgb(30,60,130);font-size:16px;{$cEsti}");

                                        echo $this->fm->celda($r->op2,1,$estilo_celdita);
                                    }
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

        if(inventario2.length == 0){
            alert("Debe elegir Inventario 2")
            return false
        }

        if(inventario1.length > 0){
            cadena = '<a href="<?= base_url() ?>inventarios/stock/4/' + inventario1 + '/' + inventario2  + '" id="enlace_grilla_compras"></a>'
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
