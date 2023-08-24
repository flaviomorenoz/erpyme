<?php
class Fm{
	function conectar(){
    	$usuario 	= "root";

        $mi_base = base_url("");
        if(strpos($mi_base, "POSC")==false){
            $dbname     = "lacabktv_pos";
        }else{
            $dbname     = "lacabktv_posc";
        }

    	$pass 		= "1357"; //"navarretecamara6";
    	$conn 		= new PDO("mysql:host=localhost;dbname={$dbname}", $usuario, $pass);

		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conn;
	}

	function validacion($conn, $usuario, $clave, &$tipo_usuario){
	 // ESTO ES LA VALIDACION ORIGINAL ==========================

		if($usuario == "" and $clave==""){
			$tipo_usuario = "ADMINISTRADOR";
			return true;
		}else{
			sleep(2);
			return false;
		}
	}

	function celda_simple($dato = "&nbsp;"){
		return "<td>" . $dato . "</td>";
	}

	function celda($dato="", $centrar=0, $estilo="", $cAtributo=""){
		if($dato=='0'){
			$dato = "<span style=\"color:#cccccc;\">0</span>";
		}

		$cad = "";

		$cEstilo = "";
		if(strlen($estilo)>0)
			$cEstilo = "style=\"$estilo\"";

		if($centrar==1)
			$cad .= "<td align=\"center\" $cEstilo $cAtributo>$dato</td>";
		elseif($centrar==2)
			$cad .= "<td align=\"right\" $cEstilo $cAtributo>$dato</td>";
		else
			$cad .= "<td align=\"left\" $cEstilo $cAtributo>$dato</td>";
		
		return $cad . "\n";
	}

	function  fila($cad=""){
		return "<tr>" . $cad . "</tr>";
	}

	function celda_h($dato="",$centrar=0,$estilo="",$clase=""){
		$cad = "";
		
		$cEstilo = "";
		if(strlen($estilo)>0)
			$cEstilo = "style=\"$estilo\"";

		$cad_clase = "";
		if(strlen($clase)>0){
			$cad_clase = "class=\"{$clase}\"";
		}

		if($centrar==1){
			$cad .= "<th align=\"center\" {$cad_clase} $cEstilo>$dato</th>";
		}elseif($centrar==2){
			$cad .= "<th align=\"right\" {$cad_clase} $cEstilo>$dato</th>"; // style=\"$estilo\"
		}else{
			$cad .= "<th align=\"left\" {$cad_clase} $cEstilo>$dato</th>";
		}
		
		return $cad . "\n";
	}

	function espacio($n){
		$cad = "";
		for($i=0; $i < $n; $i++){
			$cad .= "&nbsp;";
		}
		return $cad;
	}

	function mostrado($msg,$bandera){
		if($bandera){
			echo $msg . "<br>";
		}
	}

	function traer_campo($conn, $table, $campo, $where){
		$cSql = "select $campo from $table where $where";
		$pdo = $conn->prepare($cSql);
		$pdo->execute();
		$result = $pdo->fetchAll();
		foreach($result as $r){
			return $r[$campo];
		}
		return "";
	}

	function traer_campo2($conn, $cSql, $campo){
		$pdo = $conn->prepare($cSql);
		$pdo->execute();
		$result = $pdo->fetchAll();
		foreach($result as $r){
			return $r[$campo];
		}
		return "";
	}

	function result($conn, $cSql, $var1=null){
		$pdo = $conn->prepare($cSql);
		$pdo->bindParam(1,$var1);
		$pdo->execute();
		return $pdo->fetchAll();
	}

	function alertas($mensaje="",$tipo_alerta="success"){
		$mensaje = "<div class=\"alert alert-$tipo_alerta\">$mensaje</div>";
		return $mensaje;
	}

	function obtener_ip(){
		if(getenv('HTTP_CLIENT_IP')){
			$ip = getenv('HTTP_CLIENT_IP');
		}elseif(getenv('HTTP_X_FORWARDED_FOR')){
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}elseif(getenv('HTTP_X_FORWARDED')){
			$ip = getenv('HTTP_X_FORWARDED');
		}elseif(getenv('HTTP_FORWARDED_FOR')){
			$ip = getenv('HTTP_FORWARDED_FOR');
		}elseif(getenv('HTTP_FORWARDED')){
			$ip = getenv('HTTP_FORWARDED');
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	function guardar_ip($conn, $mi_ip, $padre, $hijo){
		$mi_fecha = date("Y-m-d H:i:s");
		$cSql = "insert into visitas(padre, hijo, ip, fecha) values('$padre','$hijo','$mi_ip','$mi_fecha')";
		$pdo = $conn->prepare($cSql);
		$pdo->execute();
	}

	function casilla($nombre, $valor_default, $size=10){
		$cad = "<input type='text' id='" . $nombre . "' name='" . $nombre . "' value='" . $valor_default . "' size='" . $size . "'>";
		return $cad;
	}

	function query_a_array($result,$key,$valor){
	    $ar = array();
	    foreach($result as $r){
	        $ar[$r[$key]] = $r[$valor];
	    }
	    return $ar;
	}

	function option($id, $cad="vacio", $valor=""){
		$selected = ""; //$codo = "";
		if(strlen($valor)>0){
			//$codo .= "valor: $valor = id: $id";
			if($valor == $id){
				$selected = " selected";
			}
		}
		return "<option value=\"$id\" " . $selected . ">" . $cad . "</option>";
	}

	function message($cad="", $alerta=0){
		if ($alerta == 0){
			$class = "success";
			$color = "rgb(250,255,230)";
		}elseif($alerta == 1){
			$class = "warning";
			$color = "rgb(255,255,225)";
		}elseif($alerta == 2){
			$class = "danger";
			$color = "rgb(255,160,140)";
		}else{
			$class = "cualquiera";
			$color = "rgb(240,240,255)";
		}
		return "<div class=\"alert-$class\" style=\"height:40px;background-color:$color;padding:9px\"><strong>" . $cad . "</strong></div>";
	}

    function ymd_dmy($cad=""){
        $n = strlen($cad);
        if($n >= 10){
            return substr($cad,8,2) . "-" . substr($cad,5,2) . "-" . substr($cad,0,4) . substr($cad,10);
        }else{
            return "vacio";
        }
    }

    function floor_dec($nU,$precision=0){
	    $cU = $nU . "";
	    $nLim = strlen($cU);
	    for($n=0; $n<$nLim; $n++){
	        if(substr($cU,$n,1)=="."){
	           $nDecimales = $nLim - $n - 1;
	           $nPos = $n;
	           
	           // Extrayendo o mejor dicho truncando.
	           $nQuitar = $nDecimales - $precision;

	           $nU = substr($cU,0,$nLim-$nQuitar)*1;
	           return $nU;
	        }
	    }
	    return $nU;
	}

	function basico($numero) {
		$valor = array ('uno','dos','tres','cuatro','cinco','seis','siete','ocho',
		'nueve','diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciseis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidos', 'veintitres', 'veinticuatro','veinticinco',
		'veintiséis','veintisiete','veintiocho','veintinueve');
		return $valor[$numero - 1];
	}

	function decenas($n) {
		$decenas = array (30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',
		70=>'setenta',80=>'ochenta',90=>'noventa');
		if( $n <= 29) return $this->basico($n);
		$x = $n % 10;
		if ( $x == 0 ) {
		return $decenas[$n];
		} else return $decenas[$n - $x].' y '. $this->basico($x);
	}

	function centenas($n) {
		$cientos = array (100 =>'cien',200 =>'doscientos',300=>'trecientos',
		400=>'cuatrocientos', 500=>'quinientos',600=>'seiscientos',
		700=>'setecientos',800=>'ochocientos', 900 =>'novecientos');
		if( $n >= 100) {
		if ( $n % 100 == 0 ) {
		return $cientos[$n];
		} else {
		$u = (int) substr($n,0,1);
		$d = (int) substr($n,1,2);
		return (($u == 1)?'ciento':$cientos[$u*100]).' '.$this->decenas($d);
		}
		} else return decenas($n);
	}

	function miles($n) {
		if($n > 999) {
		if( $n == 1000) {return 'mil';}
		else {
		$l = strlen($n);
		$c = (int)substr($n,0,$l-3);
		$x = (int)substr($n,-3);
		if($c == 1) {$cadena = 'mil '. $this->centenas($x);}
		else if($x != 0) {$cadena = $this->centenas($c).' mil '. $this->centenas($x);}
		else $cadena = $this->centenas($c). ' mil';
		return $cadena;
		}
		} else return $this->centenas($n);
	}

	function millones($n) {
		if($n == 1000000) {return 'un millón';}
		else {
		$l = strlen($n);
		$c = (int)substr($n,0,$l-6);
		$x = (int)substr($n,-6);
		if($c == 1) {
		$cadena = ' millón ';
		} else {
		$cadena = ' millones ';
		}
		return $this->miles($c).$cadena.(($x > 0)? $this->miles($x):'');
		}
	}

	function convertir($n){
		switch (true) {
			case ($n >= 1 && $n <= 29) : return $this->basico($n); break;
			case ($n >= 30 && $n < 100) : return $this->decenas($n); break;
			case ($n >= 100 && $n < 1000) : return $this->centenas($n); break;
			case ($n >= 1000 && $n <= 999999): return $this->miles($n); break;
			case ($n >= 1000000): return $this->millones($n);
		}
	}

	function traza($msg){
	    $nombre_file = "traza.txt";
        $gestor = fopen($nombre_file,"a+");
        $msg .= "\n";
        fputs($gestor,$msg);
        fclose($gestor);
    }




	function crea_tabla_result($result, $cols, $cols_titulos, $ar_align = array(), $ar_pie = array()){
		
		$cad = "<table class=\"table table-hover\" style=\"border-style:solid; border-color:gray; border-width:1px;\"><tr>";
		
		// titulos ===============
		for($i=0; $i< count($cols); $i++){
			$cad .= "<th id=\"cabeza\" style=\"background-color:rgb(200,200,200);margin:0px;padding:10px\">" . $cols_titulos[$i] . "</th>";
			//echo "cabeza";
		}

		// Añado operaciones
		//$cad .= "<th style=\"background-color:rgb(200,200,200);margin:0px;padding:10px\">Op.</th>";

		$cad .= "</tr>";

		// body ===============
		$totals = array();
		foreach($result as $r){
			
			$cad .= "<tr>";
			
			$color = "";

			for($i=0; $i < count($cols); $i++){
				$cad .= $this->celda($r[$cols[$i]], $ar_align[$i]);

				if(strtolower($ar_pie[$i]) == "suma"){
					$totals[$i] += $r[$cols[$i]] * 1;
					//echo "Mi suma es :" . $totals[$i] . " _ " . $r[$cols[$i]] . "<br>";
					//print_r($totals);
				}
			}
			
			// Añado operaciones
			/*$cad .= "<td style=\"$color\">";

			if($this->session->userdata["first_name"] == "Administrador"){ 
				$cad .= "<a href=\"" . base_url("insumos/modificar_insumos/") . $r["id"] . "\" alt=\"Editar\"><span class=\"glyphicon glyphicon-edit iconos\"></span></a>\n&nbsp;&nbsp;";
				$cad .= "<a href=\"#\" onclick=\"eliminar_insumo(" . $r["id"] . ")\"><span class=\"glyphicon glyphicon-remove iconos\"></span></a>";
			}
			$cad .= "</td>";*/		

			$cad .= "</tr>";
		}

		if (count($totals) > 0){
			for($i=0; $i<count($cols); $i++){
				if($totals[$i] > 0){
					$cad .= $this->celda_h(number_format($totals[$i],2));
				}else{
					if($i>0){
						$cad .= $this->celda_h($totals[$i]);
					}else{
						$cad .= $this->celda_h("Totales");
					}
				}
			}
		}

		$cad .= "</table>";
		return $cad;
	}

	function obtener_nombre_doc($cod=""){
		$cod = substr($cod,0,1);
		if(strlen($cod)>0){
			if($cod == "F"){
				return "Factura";
			}elseif($cod == "B"){
				return "Boleta";
			}elseif($cod == "G"){
				return "Guia";
			}else{
				return "clip";
			}
		}else{
			return "";
		}
	}

	function obtener_codigo_doc_sunat($cod=""){
		//Tipo de COMPROBANTE que desea generar:
		//1 = FACTURA
		//2 = BOLETA
		//3 = NOTA DE CRÉDITO
		//4 = NOTA DE DÉBITO
		if(strlen($cod)>0){
			if($cod == "Factura"){
				return "01";
			}elseif($cod == "Boleta"){
				return "02";
			}elseif($cod == "Nota_de_credito"){
				return "03";
			}elseif($cod == "Nota_de_debito"){
				return "04";
			}else{
				return "00";
			}
		}else{
			return "";
		}
	}

	function casilla_graf($valor=0){
		$ancho_casilla = 120;
		if($valor > 0){
			if($valor > 2000){
			    $ancho = 110;
			}else{
			    $ancho = (110 * $valor)/2000;
			}
		}else{
			$valor = "0.00";
			$ancho = "0";
		}
		
		// Viendo de alinear a la derecha
		$nCifras = strlen($valor . "");
		$tam_cifra = 7;
		$punto_partida = $ancho_casilla - ($tam_cifra * $nCifras);
		
		$cad = '<svg width="' . $ancho_casilla . '" height="27" transform="scale(1,1)" style="background-color:white;border-style: solid; border-width:1px; border-color:rgb(200,200,200);">
		        <rect x="1" y="9" width="'.$ancho.'" height="13" style="fill:rgb(150,250,150)" />
		        <text fill="#333" font-size="14" x="' . $punto_partida . '" y="20" font-family="Arial">' . $valor . '</text></svg>';
		return $cad;
	}

	function query_salidas_por_dia($tienda, $fec_ini, $fec_fin){
        // DETALLE RESUMEN - CUADRE DE CAJA
        $por_tarjeta = 0.95;
        $por_delivery = 0.75; 
        $cSql = "select date_format(tc.fecha,'%d-%m-%Y') as fecha, tc.dia_semana, tr.cash_in_hand, tr.cash_in_hand_adicional, ts.grand_total, a.con_factura, a.con_boleta, 
            a.con_recibo, a.total_salidas, 
            if(ts.grand_total is null, 0, ts.grand_total) as total_ventas_efectivo,
            ts.vendemas + ts.transferencia + ts.yape + ts.plin + ts.rappi + ts.pedidosya + ts.otros as total_otras_ventas,
            remesas.remesa,
            
			if(tr.cash_in_hand is null,0,tr.cash_in_hand) + if(tr.cash_in_hand_adicional is null,0,tr.cash_in_hand_adicional) 
			+ if(ts.grand_total is null, 0, ts.grand_total) 
			+ if(ts.vendemas is null,0,ts.vendemas) + if(ts.transferencia is null,0,ts.transferencia) + if(ts.yape is null,0,ts.yape) + if(ts.plin is null,0,ts.plin) + if(ts.rappi is null,0,ts.rappi) 
			+ if(ts.pedidosya is null,0,ts.pedidosya) + if(ts.otros is null,0,ts.otros) 
			- if(a.total_salidas is null,0,a.total_salidas) as caja_final,
			 
			if(tr.cash_in_hand is null,0,tr.cash_in_hand) + if(tr.cash_in_hand_adicional is null,0,tr.cash_in_hand_adicional)
			+ if(ts.grand_total is null, 0, ts.grand_total) 
			- (if(a.con_factura is null,0,a.con_factura) + if(a.con_boleta is null,0,a.con_boleta) + if(a.con_recibo is null,0,a.con_recibo)) as caja_final_efectivo,

                tr.status as cierre,
                if(tr.note is null,'',tr.note) note
            from tec_calendario tc
            left join (
                SELECT 
                    date_format(tp.date, '%d-%m-%Y') as fecha,
                    tp.store_id,
                    sum(if(tp.tipoDoc='F', `costo_tienda`, 0)) con_factura, 
                    sum(if(tp.tipoDoc='B', `costo_tienda`, 0)) con_boleta, 
                    sum(if(tp.tipoDoc not in ('F', 'B'),tp.costo_tienda, 0)) con_recibo, 
                    sum(if(tp.costo_tienda is null, 0, tp.costo_tienda)) total_salidas
                FROM `tec_purchases` tp left join tec_subtipo_gastos on tp.clasifica2 = tec_subtipo_gastos.id and tec_subtipo_gastos.descrip != 'Remesas'
                where tp.store_id = $tienda
                GROUP BY date_format(tp.date, '%d-%m-%Y'), tp.store_id
            ) a on date_format(tc.fecha,'%d-%m-%Y') = a.fecha
            left join (
                select date_format(tec_sales.date, '%d-%m-%Y') fecha, tec_sales.store_id, 
                sum(if(tp.paid_by = 'cash',tp.amount,0)) grand_total,
                sum(if(tp.paid_by = 'Vendemas',tp.amount * $por_tarjeta,0)) vendemas, 
                sum(if(substr(tp.paid_by,1,6)='Transf',tp.amount,0)) transferencia,
                sum(if(tp.paid_by = 'Yape',tp.amount,0)) yape,
                sum(if(tp.paid_by = 'Plin',tp.amount,0)) plin,
                sum(if(tp.paid_by = 'Rappi',tp.amount * $por_delivery,0)) rappi,
                sum(if(tp.paid_by = 'PedidosYa',tp.amount * $por_delivery,0)) pedidosya,
                sum(if(tp.paid_by not in ('cash','Vendemas','Yape','Plin','Rappi','PedidosYa') and substr(tp.paid_by,1,6)!='Transf',tp.amount,0)) otros
                from tec_sales
                inner join tec_payments tp on tec_sales.id = tp.sale_id and tp.note != 'PASE'
                where tec_sales.store_id = $tienda
                GROUP BY date_format(tec_sales.date, '%d-%m-%Y'), tec_sales.store_id
            ) ts on date_format(tc.fecha,'%d-%m-%Y') = ts.fecha
            left join (
                select date_format(date, '%d-%m-%Y') fecha, store_id, cash_in_hand, cash_in_hand_adicional, closed_at, status, note 
                from tec_registers
                where store_id = $tienda 
            ) tr on date_format(tc.fecha,'%d-%m-%Y') = tr.fecha
            left join (
                select date_format(a.date,'%d-%m-%Y') fecha, a.store_id, b.product_id, sum(b.quantity*b.cost) remesa 
                from tec_purchases a
                inner join tec_purchase_items b on a.id = b.purchase_id
                inner join tec_products c on b.product_id = c.id    
                where c.name = 'REMESA' and a.store_id = $tienda
                group by date_format(a.date,'%d-%m-%Y'), a.store_id, b.product_id 
            ) remesas on date_format(tc.fecha,'%d-%m-%Y') = remesas.fecha    
            where tc.fecha >= '$fec_ini' and tc.fecha <= '$fec_fin'
            order by tc.fecha";

        //$this->data['query'] = $this->db->query($cSql);
        return $cSql;
    }

    function query_salidas_por_dia_ventas($tienda, $fec_ini, $fec_fin){
		// DETALLE DE VENTAS
		// Nota.- Estos % tambien estan en libreria FM
		$por_tarjeta = 0.95;
		$por_delivery = 0.75; // pedidosYa, Yape
		$por_culqi = (100 - 2.8)/100; // Culqi

		$cSql = "select date_format(tc.fecha,'%d-%m-%Y') as fecha, tc.dia_semana, b.cash, b.vendemas, b.transferencia, b.yape, b.plin, b.rappi, b.pedidosya, b.didi, b.culqi, b.otros, 
		    b.cash + b.vendemas + b.transferencia + b.yape + b.plin + b.rappi + b.pedidosya + b.didi + b.culqi + b.otros as total
		    from tec_calendario tc
		    left join 
		    (
		        select date_format(ts.date, '%d-%m-%Y') fecha, 
		            sum(tp.cash) cash,
		            sum(tp.vendemas) vendemas,
		            sum(tp.transferencia) transferencia,
		            sum(tp.yape) yape,
		            sum(tp.plin) plin,
		            sum(tp.rappi) rappi,
		            sum(tp.pedidosya) pedidosya,
		            sum(tp.didi) didi,
		            sum(tp.culqi) culqi,
		            sum(tp.otros) otros
		        from tec_sales ts
		        inner join 
		        (
		            select sale_id, 
		            sum(if(paid_by = 'cash',amount,0)) cash,
		            sum(if(paid_by = 'Vendemas',amount* $por_tarjeta,0)) vendemas,
		            sum(if(substr(paid_by,1,6)='Transf',amount,0)) transferencia,
		            sum(if(paid_by = 'Yape',amount,0)) yape,
		            sum(if(paid_by = 'Plin',amount,0)) plin,
		            sum(if(paid_by = 'Rappi',amount * $por_delivery,0)) rappi,
		            sum(if(paid_by = 'PedidosYa',amount * $por_delivery,0)) pedidosya,
		            sum(if(paid_by = 'Didi',amount * $por_delivery,0)) didi,
		            sum(if(paid_by = 'CULQI',amount * $por_culqi,0)) culqi,
		            sum(if(paid_by not in ('cash','Vendemas','Yape','Plin','Rappi','PedidosYa','Didi','CULQI') and substr(paid_by,1,6)!='Transf',amount,0)) otros
		            from tec_payments where note != 'PASE'
		            group by sale_id
		        ) tp on ts.id = tp.sale_id      
		        where ts.store_id = $tienda
		        group by date_format(ts.date, '%d-%m-%Y')
		    ) b on date_format(tc.fecha,'%d-%m-%Y') = b.fecha
		    where tc.fecha >= '$fec_ini' and tc.fecha <= '$fec_fin'
		    order by tc.fecha";
		return $cSql;
	}

    function dias_de_la_semana($num){
        switch ($num){
            case 0:
                return "D";
                break;
            case 1:
                return "L";
                break;
            case 2:
                return "M";
                break;
            case 3:
                return "X";
                break;
            case 4:
                return "J";
                break;
            case 5:
                return "V";
                break;
            case 6:
                return "S";
                break;
            
        }
    }

    function grafico_jp($datay1, $filename){
		//require_once ('./jpgraph-4.3.5/src/jpgraph.php');
		//require_once ('./jpgraph-4.3.5/src/jpgraph_line.php');

		//$datay1 = array(20,15,23,15,80,20,45,10,5,45,60);
		 
		// Setup the graph
		$graph = new Graph(900,350);
		$graph->SetScale("textlin");
		 
		$theme_class=new UniversalTheme;
		 
		$graph->SetTheme($theme_class);
		$graph->img->SetAntiAliasing(false);
		$graph->title->Set('Grafico');
		$graph->SetBox(false);
		 
		$graph->img->SetAntiAliasing();
		 
		$graph->yaxis->HideZeroLabel();
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		 
		$graph->xgrid->Show();
		$graph->xgrid->SetLineStyle("solid");
		$graph->xaxis->SetTickLabels(array('Ene','Feb','Mar','Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Nov', 'Oct', 'Dic'));
		$graph->xgrid->SetColor('#E3E3E3');
		 
		// Create the first line
		$p1 = new LinePlot($datay1);
		$graph->Add($p1);
		$p1->SetColor("#6495ED");
		$p1->SetLegend('Tienda 1');
		 
		// Create the second line
		$graph->legend->SetFrameWeight(1);
		 
		$graph->legend->SetPos(0.5,0.98,'center','bottom');
		 
		// Output line
		$graph->Stroke();

		$graph->img->Stream($fileName);
	}

    function estilo_dt($dato){
        // Cambio de color rojo la parte de la hora en un datetime
        return substr($dato,0,11) . '<span style="color:red;font-style:italic;">' . substr($dato,11) . '</span>';
    }

	function conver_dropdown($result, $indice, $descrip, $agrega=null){
		
		$ar = array();
		if($agrega !== null){
			foreach($agrega as $key => $valor){
				$ar[$key] = $valor;
			}
		}
		
		foreach($result as $r){
			$ar[$r[$indice]] = $r[$descrip];
		}
		return $ar;
	}

	function contra_inyeccion($dato=""){
		if(strlen($dato)>0){
			$dato = str_replace(";","",$dato);
		}
		return $dato;
	}
}
?>