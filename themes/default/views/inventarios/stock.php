<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
?>
<div class="content" style="margin-left: 10px">

    

        <div class="row">
            <div class="col-sm-4 col-lg-3" style="border-style:none; border-color:red;">
                <select name="tipo_stock" id="tipo_stock" class="form-control">
                    <option value="2">Generar Stock actual</option>
                    <option value="3">Comparar 2 Inventarios</option>
                </select>
            </div>
        </div>

        <div class="row" style="margin-top:25px">
            <div class="col-sm-3 col-lg-2">
            	<button type="submit" class="btn btn-primary" onclick="envio_stock()">Continuar</button>
            </div>
        </div>

        <div class="row" style="margin-top:25px">
            <div id="pizarra" class="col-sm-12 col-lg-6">
            </div>
        </div>
</div>

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

    function envio_stock(){
        var op = document.getElementById('tipo_stock').value
        window.location.href = '<?= base_url() ?>inventarios/stock/' + op
    }
</script>
