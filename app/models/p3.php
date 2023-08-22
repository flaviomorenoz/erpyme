<?php
	$ar_s_p["tda1"]["a"] = "bla";
	$ar_s_p["tda1"]["b"] = "ble";
	$ar_s_p["tda1"]["c"] = "bli";
	$ar_s_p["tda2"]["a"] = "blo";
	$ar_s_p["tda2"]["b"] = "blu";
	$ar_s_p["tda2"]["c"] = "blk";
	

    $nLim = count($ar_s_p);
    foreach($ar_s_p as $key => $valor){
        echo "key:" . $key . "<br>";
        echo "val:" . $valor . "<br><br>";
    }
?>