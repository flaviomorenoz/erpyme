<div class="row">

    <div class="col-xs-12 col-sm-10 col-md-8" style="padding-left:50px; padding-top:15px;">
    	<?php
            $query1 = $this->db->query($query1);

            $ar = array(); 

            $result         = $query1->result_array(); 

            //$cols         = array("id","name","unidad","resultado"); 
            $cols           = array('id', 'name', 'unidad', 'total_comprado', 'total_utilizado', 'ingreso', 'salida', 'kardex');

            $cols_titulos   = $cols;

            $ar_align       = array('1','0','0','1','1','1','1','1'); 

            $ar_pie         = $ar_align; 

            echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie); 	    	
		?>
    </div>

    <div class="col-sm-12" style="padding-left:50px; padding-top:15px;">
        <?php
            
        ?>
    </div>

</div>