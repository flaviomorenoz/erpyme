<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 

if(!isset($_SESSION["username"])){ die("Su sesion ha caducado."); }

$store_id = $_SESSION["store_id"];

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
                    <button onclick="activo1()" class="btn" style="background-color:white;margin:0px;padding:1px;">
                        <img src="<?= base_url("themes/default/views/gastus/search.png") ?>" height="30px">
                    </button>
                </div>
                <div class="col-sm-5" style="padding:5px 0px 0px 0px">
                    <button onclick="limpiar()" class="btn" style="background-color:white;margin:0px;padding:1px;">
                        <img src="<?= base_url("themes/default/views/gastus/eliminar.png") ?>" height="30px">
                    </button>
                </div>
            </div>
        </div>
    </div>

</section>

<div id="refresco"></div>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">

    function activo1(){

        cadena = '<a href="<?= base_url() ?>inventarios/stock/5/'+ document.getElementById('store_id').value + '" id="enlace_grilla_compras">x</a>'
        document.getElementById('refresco').innerHTML = cadena
        setTimeout("document.getElementById('enlace_grilla_compras').click()",100)

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
