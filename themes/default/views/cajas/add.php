<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
//if(!isset($desde)){         $desde = "";    }
//if(!isset($hasta)){         $hasta = "";    }
if(!isset($store_id)){        $store_id = "";  }

?>
<script type="text/javascript">
    <?php
        echo "var Admin =" . ($Admin == true ? 'true' : 'false') . ";\n"; 
        //echo "setTimeout('abrir_item_menu(7)',500);\n";
    ?>
</script>

<?= form_open("cajas/save", 'class="validation" name="form1" id="form1"'); ?>

    <div class="content" style="margin-left: 10px">
        <div class="row">
        
            <div class="col-sm-3 col-lg-3" style="border-style:none; border-color:red;">
                <div class="form-group">
                    <label for="">Fecha:</label>
                    <input type="date" name="date" id="date" class="form-control">
                </div>
            </div>
            <div class="col-sm-2 col-lg-2" style="border-style:none; border-color:red;">
                <div class="form-group">
                    <label for="">Tienda:</label>
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
                        echo form_dropdown('store_id', $ar, $store_id, 'class="form-control tip" id="store_id" required="required"');
                    ?>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-sm-3 col-lg-2">
                <label for="">Efectivo Inicial:</label>
                <?php
                    echo form_input('cash_in_hand', $cash_in_hand, 'class="form-control tip" id="cash_in_hand"'); 
    			?>
            </div>

        </div>

        <div class="row" style="margin-top:25px">
            <div class="col-sm-3 col-lg-2">
            	<button type="button" onclick="validar()" class="form-control btn btn-primary">Grabar</button>
            </div>
        </div>

        <div class="row" style="margin-top:25px">
            <div id="pizarra" class="col-sm-12 col-lg-10">
            </div>
        </div>

    </div>
<?= form_close(); ?>

<div id="refresco"></div>
<script type="text/javascript">

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

    function validar(){
    	var fecha 		= document.getElementById('date').value
    	var store_id 	= document.getElementById('store_id').value
    	var cash_in_hand= document.getElementById('cash_in_hand').value

        //console.log("fecha:"+fecha)
        //console.log("tienda:"+tienda)
        //console.log("cash_in_hand:"+cash_in_hand);
        if(store_id.length > 0 && cash_in_hand.length>0 && !empty(fecha)){
            document.getElementById("form1").submit()
        }else{
            alert("Falta ingresar datos, verifique...")
        }
    }

    function limpiar(){
    	//document.getElementById('producto').value = ""
    	//document.getElementById('cantidad').value = ""
    }
</script>
