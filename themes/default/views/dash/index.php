<div class="content">
	<div class="row">
		<div class="col-sm-12 col-md-7 col-lg-5">
			<h2>Facturacion Electronica</h2>
			<span>Nota.- Observados son aquellos que no han sido enviados o aceptados por Nubefact, o anulaciones incorrectas.</span>
			<?php
				echo $this->dash_model->facturacion_electronica();
			?>
		</div>
		<div class="col-sm-12 col-md-5 col-lg-7">
			<h2>Auditoria Recetas</h2>
			<?php
				echo $this->dash_model->auditoria_recetas();
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<h2>Factura Electronica Don Alejo</h2>
			<?php
				if(strpos(base_url(), "donalejo")==false){
					$ruta = "https://donalejo.com.pe/POS/dash/respuestas";

					$ch = curl_init();
				    curl_setopt($ch, CURLOPT_URL, $ruta);
				    curl_setopt($ch, CURLOPT_HEADER, false);

				    $rpta = curl_exec($ch);

				    curl_close($ch);

				    if(strlen($rpta)==0){
				    	echo "0 errores. Todo OK";
				    }else{
				    	echo $rpta;	
				    }
				}else{
					echo "";
				}
			?>
		</div>
	</div>
</div>