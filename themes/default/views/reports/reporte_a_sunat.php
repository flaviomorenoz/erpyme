<form name="form1" method="get" action="<?= base_url("reports/reporte_a_sunat") ?>">
	<div class="row" style="margin-top:10px; margin-bottom:20px; margin-left:10px">
		<div class="col-sm-3 col-lg-2 text-center">
			Desde:<input type="date" name="txt_desde" id="txt_desde" class="form-control" value="<?= (isset($cDesde) ? $cDesde : "") ?>">
		</div>
		<div class="col-sm-3 col-lg-2 text-center">
			Hasta:<input type="date" name="txt_hasta" id="txt_hasta" class="form-control" value="<?= (isset($cHasta) ? $cHasta : "") ?>">
		</div>
		<div class="col-sm-3 col-lg-2 text-left">
			<!--<button type="button" onclick="llenaste()">Llena</button>-->
			<br><button type="submit" class="btn btn-primary">Generar</button>
		</div>
	</div>
</form>

<script type="text/javascript">
	function llenaste(){
		document.getElementById("txt_desde").value = "2021-11-01"
		document.getElementById("txt_hasta").value = "2021-11-30"
	}
</script>

<?php
	//$cDesde = "2021-11-01"; //$cHasta = "2021-11-18";
	$cad_ruta_web = "";

	if(isset($cDesde)){
		$cSql = "select date_format(a.date,'%d/%m/%Y') fecha, 
			if(a.tipoDoc='Boleta','BOLETA DE VENTA',if(a.tipoDoc='Factura','FACTURA','')) tipoDoc, 
			a.serie, a.correlativo, 
			a.customer_name, if(a.tipoDoc = 'Boleta', cu.cf1, cu.cf2) receptorNumero, 0 descuento, a.total, 
			a.total_tax, a.grand_total, a.total gravadas, 0 inafectas, 
			0 exoneradas, 0 gratuitas, 'SOLES' moneda, a.serieDocfectado serie_destino, 
			a.numDocfectado correlativo_destino, if(a.serieDocfectado != '',date_format(a.date,'%d/%m/%Y'),'') fecha_destino, '-0' aplicado, 
			if (a.envio_electronico >= 1 , 'EMITIDO', 'DADO DE BAJA')  estado,
			if(a.envio_electronico >= 1 and a.dir_comprobante is not null, 'ACEPTADA', 'DADO DE BAJA') estado_sunat, 0 otros_cargos, 0 icbper
			from tec_sales as a
			inner join tec_payments b on a.id = b.sale_id and b.note!= 'PASE'
			inner join tec_customers cu on  a.customer_id = cu.id 
			where date(a.date) between ? and ?";


		$cSql = "select date_format(a.date,'%d/%m/%Y') fecha, 
			if(a.tipoDoc='Boleta','BOLETA DE VENTA',if(a.tipoDoc='Factura','FACTURA','')) tipoDoc, 
			a.serie, a.correlativo, 
			a.customer_name, if(a.tipoDoc = 'Boleta', cu.cf1, cu.cf2) receptorNumero, 0 descuento, 
			if (a.envio_electronico < 1,0,a.total) total, 
			if (a.envio_electronico < 1,0,a.total_tax) total_tax, 
			if (a.envio_electronico < 1,0,a.grand_total) grand_total, 
			if (a.envio_electronico < 1,0,a.total) gravadas, 
			0 inafectas, 
			0 exoneradas, 0 gratuitas, 'SOLES' moneda, a.serieDocfectado serie_destino, 
			a.numDocfectado correlativo_destino, if(a.serieDocfectado != '',date_format(a.date,'%d/%m/%Y'),'') fecha_destino, '-0' aplicado, 
			if (a.envio_electronico >= 1 , 'EMITIDO', 'DADO DE BAJA')  estado,
			if(a.envio_electronico >= 1 and a.dir_comprobante is not null, 'ACEPTADA', 'DADO DE BAJA') estado_sunat, 0 otros_cargos, 0 icbper
			from tec_sales as a
			inner join tec_payments b on a.id = b.sale_id and b.note!= 'PASE'
			inner join tec_customers cu on  a.customer_id = cu.id 
			where date(a.date) between ? and ?";

		//echo str_replace("\n","<br>",$cSql);
		/*
		FechaEmisionDoc				01/09/2021
		TipoDocDescripcion			BOLETA DE VENTA								
		SerieDescripcion			B003
		CorrelativoDocumento		476
		ReceptorRazonSocial			SIN REGISTRAR
		ReceptorNumeroDocumento		00000000

		MontoDescuento				0
		MontoSubTotal				18.64
		MontoIGV					3.36
		MontoTotal					22

		OperacionesGravadas			18.64				
		OperacionesInafectas		0
		OperacionesExoneradas		0
		OperacionesGratuitas		0

		MonedaDescripcion			SOLES
		SerieDocumentoDestino		
		CorrelativoDocumentoDestino	
		FechaEmisionDestinoFormateada	
		AplicadoEn					-0				
		EstadoDescripcion			EMITIDO
		EstadoSunatDescripcion		ACEPTADA
		OtrosCargosSumatoria		0
		SumatoriaIcbper				0 */

		$query = $this->db->query($cSql,array($cDesde,$cHasta));

		$result 		= $query->result_array();
		$cols 			= array("fecha","tipoDoc","serie","correlativo","customer_name","receptorNumero","descuento","total","total_tax",
			"grand_total","gravadas","inafectas","exoneradas","gratuitas","moneda","serie_destino","correlativo_destino","fecha_destino","aplicado","estado",
			"estado_sunat","otros_cargos","icbper");
		//$cols_titulos 	= array("fecha","tipoDoc","serie","Nro","customer name","receptor Numero","Dscto","total","total_tax",
		//	"grand_total","gravadas","inafectas","exoneradas","gratuitas","moneda","serie destino","correlativo destino","fecha destino","aplicado","estado",
		//	"estado sunat","otros cargos","icbper");

		$cols_titulos 	= array('FechaEmisionDoc','TipoDocDescripcion','SerieDescripcion','CorrelativoDocumento','ReceptorRazonSocial','ReceptorNumeroDocumento',
			'MontoDescuento','MontoSubTotal','MontoIGV','MontoTotal','OperacionesGravadas','OperacionesInafectas','OperacionesExoneradas','OperacionesGratuitas',
			'MonedaDescripcion','SerieDocumentoDestino','CorrelativoDocumentoDestino','FechaEmisionDestinoFormateada','AplicadoEn','EstadoDescripcion',
			'EstadoSunatDescripcion','OtrosCargosSumatoria','SumatoriaIcbper');

		$ar_align 		= array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ar_pie 		= array("","","","","","","","","","","","","","","","","","","","","","","");

		//echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');    

		$rt_excel = "vendor/excel/Classes/PHPExcel.php";

		//require_once("C:/xampp/htdocs/varios/qsystem/POS/" . $rt_excel);

		require_once($rt_excel);	

		$objPHPExcel  = new PHPExcel();

		//include("./estilos_excel.php");

		$objPHPExcel->setActiveSheetIndex(0);

		$result		= $query->result_array();

		$letra = "A";
		for($i=0; $i<count($cols); $i++){
			$objPHPExcel->getActiveSheet()->setCellValue("{$letra}1", $cols_titulos[$i]);
			$letra = sgte_letra($letra);
		}

		$nM = 1;
		foreach($query->result_array() as $r){
		    //if($nM == 10){ break; }
		    $nM++;
		    $letra = "A";

		    for($i=0; $i<count($cols); $i++){
		    	$objPHPExcel->getActiveSheet()->setCellValue("{$letra}{$nM}",$r[$cols[$i]]);
		    	//echo "Posicion:"."{$letra}{$nM}" . "<br>";
		    	$letra = sgte_letra($letra);
			}
		}

		$nombreArchivo = "rep_contable_" . date("Y-m-d_His") . ".xlsx";  

		// for windows:
		//$diro = "\\"; 
		
		// for Linux:
		$diro = "/";

		$rutaArchivo =  getcwd() . $diro . "temporal" . $diro . $nombreArchivo;

		$rutaWebArchivo =  base_url("temporal/" . $nombreArchivo);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		$objWriter->save($rutaArchivo);

		//echo "Ruta archivo:" . $rutaArchivo . "<br>";
		$cad_ruta_web = "<span style=\"font-size:16px\"><a href=\"" . $rutaWebArchivo . "\">" . $nombreArchivo . "</a></span>";

	}

	function sgte_letra($letra){
		$cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$nPos = strpos($cadena, $letra);
		if($nPos >= 0){
			return substr($cadena, $nPos+1, 1);
		}else{
			return "";
		}
	}
?>
<div class="row" style="margin-top:10px; margin-bottom:20px; margin-left:10px">
	<div class="col-sm-4 col-lg-3 text-center">
		<?= $cad_ruta_web ?>
	</div>
</div>