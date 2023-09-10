<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos_model_apisperu extends CI_Model
{

    public function __construct() {
        parent::__construct();
        $this->Igv = 10;
    }

    public function addSale($data, $items, $payment = array(), $did = NULL) {

        $this->fm->traza("ADDSALE-APISPERU");

        $this->db->where('serie',$data['serie']);
        $this->db->where('correlativo',$data['correlativo']);
        $query = $this->db->get('sales');
        
        $n=0;
        foreach($query->result() as $r){
            $n++;
        }
        
        if ($n==0){
            $this->db->trans_begin();
            $bandera_valida = true;

            //$this->db->insert('sales', $data);

            if($this->db->insert('sales', $data)){
    
                $sale_id = $this->db->insert_id();
    
                foreach ($items as $item){
                    $item['sale_id'] = $sale_id;
                    if($this->db->insert('sale_items', $item)) {
                        if ($item['product_id'] > 0 && $product = $this->site->getProductByID($item['product_id'])) {
                            if ($product->type == 'standard') {
                                $this->db->update('product_store_qty', array('quantity' => ($product->quantity-$item['quantity'])), array('product_id' => $product->id, 'store_id' => $data['store_id']));
                            } elseif ($product->type == 'combo') {
                                $combo_items = $this->getComboItemsByPID($product->id);
                                foreach ($combo_items as $combo_item) {
                                    $cpr = $this->site->getProductByID($combo_item->id);
                                    if($cpr->type == 'standard') {
                                        $qty = $combo_item->qty * $item['quantity'];
                                        $this->db->update('product_store_qty', array('quantity' => ($cpr->quantity-$qty)), array('product_id' => $cpr->id, 'store_id' => $data['store_id']));
                                    }
                                }
                            }
                        }
                    }
                }
    
                if($did) {
                    $this->db->delete('suspended_sales', array('id' => $did));
                    $this->db->delete('suspended_items', array('suspend_id' => $did));
                }
                
                $msg = array();
                if(! empty($payment)) {
                    $nLimites = count($payment);
                    for($i=0; $i<$nLimites; $i++){
                        
                        $payment[$i]['sale_id'] = $sale_id;
                        $ar_pay = $payment[$i];
                        if ($this->db->insert('payments', $ar_pay)){

                        }else{
                            //die("No ha podido grabar Payments !!!");
                        }

                    }
                }
                
                // A estas alturas ya debe estar registrado un pago en la db
                
                usleep(400000);
                $query = $this->db->select('id')->where('sale_id',$sale_id)->get("payments");
                $nix = 0;
                foreach($query->result() as $r){ 
                    $nix++;
                }
                if($nix == 0){
                    $bandera_valida = false;
                }

                // Generando la BV/FA electronica
                $this->el_json  = "";
     
                // ****************************************************
                $this->enviar_doc_sunat($sale_id, $data, $items);
                // ****************************************************
                
                
            }

            if ($this->db->trans_status() === FALSE || $bandera_valida == false){
                $this->db->trans_rollback();
                return false;
            }else{
                $this->db->trans_commit();
                return array('sale_id' => $sale_id, 'message' => $msg);
            }
        }
        return false;
    }


    public function enviar_doc_sunat($sale_id, $data, $items){  // IMPLEMENTACION DE APISPERU

        // Token que sale del Loguin de la Empresa.
        $cToken = "Bearer ";

        $result = $this->db->select("dato")->where("name","TOKEN")->get("variables")->result();
        foreach($result as $r){
            $cToken .= $r->dato;
        }

        $result = $this->db->select("tipoDoc")->where("id",$sale_id)->get("sales")->result();
        foreach($result as $r){
            $tipo_documento = $r->tipoDoc;
        }

        //Averiguando los datos de la empresa
        $store_id   = $data["store_id"];
        $result     = $this->db->select("code, city, state, ubigeo, address1, address2")->where("id",$store_id)->get("stores")->result_array();
        $correlativo = $data["correlativo"];

        foreach($result as $r){
            $this->COMPANY_DIRECCION      = $r["address1"]; // "Las casuarinas 666"
            $this->COMPANY_PROV           = $r["city"];
            $this->COMPANY_DPTO           = "LIMA";
            $this->COMPANY_DISTRITO       = $r["state"];
            $this->COMPANY_UBIGEO         = $r["ubigeo"];
            $this->COMPANY_RAZON_SOCIAL   = $r["address2"]; //"DAVID MORENO PLETS"
            $this->COMPANY_RUC            = $r["code"]; //"10075047946";
        }

        if($tipo_documento == 'Boleta' || $tipo_documento == 'Factura'){

            // Subvariables aun por definir:
            if($tipo_documento == 'Boleta'){
                $tipoDoc = '03';
            }else{
                $tipoDoc = '01';
            }
            $serie      = $data["serie"]; //"B001";
            $tip_forma  = "Contado";
            $fecha_emi  = date("Y-m-d") . "T" . date("H:i:s");
            $numDoc     = ""; // normalmente es el dni del cliente, pero en caso de empresa, no se
            $icbper     = 0;
            $porcentajeIgv = $this->Igv;

            // tipoDoc : 01: Factura, 03: BV
            //$correlativo        = $this->correlativo($tipo_documento);

            $query = $this->db->query("select a.id, a.date, a.customer_id, a.customer_name, a.total, a.status, a.tipoDoc, a.grand_total,
                c.cf1, c.cf2, 
                b.id id_items, 
                b.product_id, 
                b.product_name,
                b.quantity,
                b.net_unit_price,
                b.tax,
                b.real_unit_price,
                b.subtotal,
                d.codProdSunat 
                from tec_sales a
                inner join tec_sale_items b on a.id = b.sale_id
                inner join tec_customers c on a.customer_id = c.id
                inner join tec_products d on b.product_id = d.id
                where a.id = $sale_id");

            foreach ($query->result() as $r){
                $numDoc         = $r->cf1;
                //$grand_total  = $r["grand_total"];
                $total          = $r->total;
                $tax            = $r->tax;
                $Cliente        = $r->customer_name;
                $codProdSunat   = $r->codProdSunat;
            }
            
            $nTotal             = $total * (1 + ($tax/100)) * 1;
            $nTotal             = round($nTotal,2);

            // Variables segun la API:
            $Cliente            = $data["customer_name"];
            $direccion_cliente  = "sin direccion"; 

            $mtoOperGravadas    = round($total, 2); //200.2       
            //$mtoIGV           = round($total*$product_tax/100, 2); //36.04
            $mtoIGV             = round($total * $porcentajeIgv / 100,2);
            $icbper             = round($icbper * 1, 2); //0.8
            $valorVenta         = round($total, 2); //200.2
            $totalImpuestos     = $mtoIGV; // 36.84
            
            $subTotal           = $nTotal; // 237.04
            $redondeo           = 0; // 0.04
            $mtoImpVenta        = $nTotal; // 237

            //tipoOperacion: 0101: venta interna
            $campos = "{
              \"ublVersion\": \"2.1\",
              \"tipoOperacion\": \"0101\", 
              \"tipoDoc\": \"{$tipoDoc}\",
              \"serie\": \"$serie\",
              \"correlativo\": \"$correlativo\",
              \"fechaEmision\": \"" . $fecha_emi . "-05:00\",
              \"formaPago\": {
                \"moneda\": \"PEN\",
                \"tipo\": \"$tip_forma\"
              },
              \"tipoMoneda\": \"PEN\",
              \"client\": {
                \"tipoDoc\": \"1\",
                \"numDoc\": \"$numDoc\",
                \"rznSocial\": \"$Cliente\",
                \"address\": {
                  \"direccion\": \"$direccion_cliente\",
                  \"provincia\": \"LIMA\",
                  \"departamento\": \"LIMA\",
                  \"distrito\": \"LIMA\",
                  \"ubigueo\": \"150101\"
                }
              },";


            $campos .= "\"company\": {
                \"ruc\": $this->COMPANY_RUC,
                \"razonSocial\": \"$this->COMPANY_RAZON_SOCIAL\",
                \"address\": {
                  \"direccion\": \"$this->COMPANY_DIRECCION\",
                  \"provincia\": \"$this->COMPANY_PROV\",
                  \"departamento\": \"$this->COMPANY_DPTO\",
                  \"distrito\": \"$this->COMPANY_DISTRITO\",
                  \"ubigueo\": \"$this->COMPANY_UBIGEO\"
                }
              },";

            $campos .= "\"mtoOperGravadas\": $mtoOperGravadas,
              \"mtoIGV\": $mtoIGV,
              \"icbper\": $icbper,
              \"valorVenta\": $valorVenta,
              \"totalImpuestos\": $totalImpuestos,
              \"subTotal\": $subTotal,
              \"redondeo\": $redondeo,
              \"mtoImpVenta\": $mtoImpVenta,
              \"details\": [";
            //   
            
            foreach ($query->result() as $r){
                
                $codProducto        = "P" . $r->product_id; //$r->codProdSunat;
                //$unidad             = $items[0];
                $descripcion        = $r->product_name;
                $cantidad           = round($r->quantity,0);
                $mtoValorUnitario   = round($r->net_unit_price,2)*1;
                $mtoValorVenta      = round($r->net_unit_price * $cantidad * 1,2);
                $mtoBaseIgv         = round($cantidad * $mtoValorUnitario,2);
                $porcentajeIgv      = $r->tax*1;
                $igv                = round($mtoBaseIgv * ($porcentajeIgv/100),2); // round($r->subtotal - round($r->net_unit_price,2),2);
                $tipAfeIgv          = 10;
                $totalImpuestos     = $igv;
                
                $igvX               = 1 + ($porcentajeIgv/100);
                $mtoPrecioUnitario  = $this->fm->floor_dec($r->net_unit_price * $igvX, 2);           

                $campos .= "{
                  \"codProducto\": \"$codProducto\",
                  \"unidad\": \"NIU\",
                  \"descripcion\": \"$descripcion\",
                  \"cantidad\": $cantidad,
                  \"mtoValorUnitario\": $mtoValorUnitario,
                  \"mtoValorVenta\": $mtoValorVenta,
                  \"mtoBaseIgv\": $mtoBaseIgv,
                  \"porcentajeIgv\": $porcentajeIgv,
                  \"igv\": $igv,
                  \"tipAfeIgv\": $tipAfeIgv,
                  \"totalImpuestos\": $totalImpuestos,
                  \"mtoPrecioUnitario\": $mtoPrecioUnitario
                },";
            }    
            
            // Se asume que si o si hay items, por tanto se quita la ultima coma:
            $campos = substr($campos,0,strlen($campos)-1);

            $cValor         = $mtoImpVenta . "";
            $pos            = strpos($cValor, ".");
            if($pos==false){
                $valor_entero   = $cValor;
                $valor_dec      = "00";
            }else{
                $valor_entero   = substr($cValor,0,$pos);
                $valor_dec      = substr($cValor,$pos+1);
                $valor_dec      = substr($valor_dec . "00",0,2);
            }
            $en_letras =  "Son " . $this->fm->convertir($valor_entero) . " y $valor_dec/100 Soles";

            $campos .= "],
              \"legends\": [
                {
                  \"code\": \"1000\",
                  \"value\": \"$en_letras\"
                }
              ]
            }";

            $url = "https://facturacion.apisperu.com/api/v1/invoice/send";

        }
        /*
            elseif($tipo_documento == 'Factura'){

                // Subvariables aun por definir:
                $serie      = $data["serie"];;
                $tip_forma  = "Contado";
                $fecha_emi  = date("Y-m-d") . "T" . date("H:i:s");
                $numDoc     = ""; // normalmente es el dni del cliente, pero en caso de empresa, no se
                $icbper     = 0;
                $porcentajeIgv = $this->Igv;

                // tipoDoc : 01: Factura, 03: BV

                //$correlativo        = $this->correlativo($tipo_documento);

                $query = $this->db->query("select a.id, a.date, a.customer_id, a.customer_name, a.total, a.status, a.tipoDoc, a.grand_total,
                    c.cf1, c.cf2, 
                    b.id id_items, 
                    b.product_id, 
                    b.product_name,
                    b.quantity,
                    b.net_unit_price,
                    b.tax,
                    b.real_unit_price,
                    b.subtotal,
                    d.codProdSunat 
                    from tec_sales a
                    inner join tec_sale_items b on a.id = b.sale_id
                    inner join tec_customers c on a.customer_id = c.id
                    inner join tec_products d on b.product_id = d.id
                    where a.id = $sale_id");

                
                foreach ($query->result() as $r){
                    $numDoc         = $r->cf2;
                    //$grand_total    = $r["grand_total"];
                    $total          = $r->total;
                    $tax            = $r->tax;
                    $Cliente        = $r->customer_name;
                    $codProdSunat   = $r->codProdSunat;
                    //$fecha_venc     = $r->fec 
                }

                $nTotal             = $total * (1 + ($tax/100)) * 1;
                $nTotal             = round($nTotal,2);

                // Variables segun la API:
                $Cliente            = $data["customer_name"];
                $direccion_cliente  = "sin direccion"; 

                $mtoOperGravadas    = round($total, 2); //200.2       
                //$mtoIGV             = round($total*$product_tax/100, 2); //36.04
                $mtoIGV             = round($total * $porcentajeIgv / 100,2);
                $icbper             = round($icbper * 1, 2); //0.8
                $valorVenta         = round($total, 2); //200.2
                $totalImpuestos     = $mtoIGV; // 36.84
                
                $subTotal           = $nTotal; // 237.04
                $redondeo           = 0; // 0.04
                $mtoImpVenta        = $nTotal; // 237
                $mtoOperExoneradas  = 0;

                //tipoOperacion: 0101: venta interna
                $campos = "{
                  \"ublVersion\": \"2.1\",
                  \"fecVencimiento\": \"" . $fecha_emi . "-05:00\",
                  \"tipoOperacion\": \"0101\", 
                  \"tipoDoc\": \"01\",
                  \"serie\": \"$serie\",
                  \"correlativo\": \"$correlativo\",
                  \"fechaEmision\": \"" . $fecha_emi . "-05:00\",
                  \"formaPago\": {
                    \"moneda\": \"PEN\",
                    \"tipo\": \"$tip_forma\"
                  },
                  \"tipoMoneda\": \"PEN\",
                  \"client\": {
                    \"tipoDoc\": \"6\",
                    \"numDoc\": $numDoc,
                    \"rznSocial\": \"$Cliente\",
                    \"address\": {
                      \"direccion\": \"$direccion_cliente\",
                      \"provincia\": \"LIMA\",
                      \"departamento\": \"LIMA\",
                      \"distrito\": \"LIMA\",
                      \"ubigueo\": \"150101\"
                    }
                  },";


                $campos .= "\"company\": {
                    \"ruc\": $this->COMPANY_RUC,
                    \"razonSocial\": \"$this->COMPANY_RAZON_SOCIAL\",
                    \"address\": {
                      \"direccion\": \"$this->COMPANY_DIRECCION\",
                      \"provincia\": \"$this->COMPANY_PROV\",
                      \"departamento\": \"$this->COMPANY_DPTO\",
                      \"distrito\": \"$this->COMPANY_DISTRITO\",
                      \"ubigueo\": \"$this->COMPANY_UBIGEO\"
                    }
                  },";

                $campos .= "\"mtoOperGravadas\": $mtoOperGravadas,
                  \"mtoOperExoneradas\": $mtoOperExoneradas,
                  \"mtoIGV\": $mtoIGV,
                  \"icbper\": $icbper,
                  \"valorVenta\": $valorVenta,
                  \"totalImpuestos\": $totalImpuestos,
                  \"subTotal\": $subTotal,
                  \"redondeo\": $redondeo,
                  \"mtoImpVenta\": $mtoImpVenta,
                  \"details\": [";
                
                foreach ($query->result() as $r){
                    
                    $codProducto        = "P" . $r->product_id; //$r->codProdSunat;
                    //$unidad             = $items[0];
                    $descripcion        = $r->product_name;
                    $cantidad           = round($r->quantity,0);
                    $mtoValorUnitario   = round($r->net_unit_price,2)*1;
                    $mtoValorVenta      = round($r->net_unit_price * $cantidad * 1,2);
                    $mtoBaseIgv         = round($cantidad * $mtoValorUnitario,2);
                    $porcentajeIgv      = $r->tax*1;
                    $igv                = round($mtoBaseIgv * ($porcentajeIgv/100),2); // round($r->subtotal - round($r->net_unit_price,2),2);
                    $tipAfeIgv          = 10;
                    $totalImpuestos     = $igv;
                    
                    $igvX               = 1 + ($porcentajeIgv/100);
                    $mtoPrecioUnitario  = $this->fm->floor_dec($r->net_unit_price * $igvX, 2);           

                    $campos .= "{
                      \"codProducto\": \"$codProducto\",
                      \"unidad\": \"NIU\",
                      \"descripcion\": \"$descripcion\",
                      \"cantidad\": $cantidad,
                      \"mtoValorUnitario\": $mtoValorUnitario,
                      \"mtoValorVenta\": $mtoValorVenta,
                      \"mtoBaseIgv\": $mtoBaseIgv,
                      \"porcentajeIgv\": $porcentajeIgv,
                      \"igv\": $igv,
                      \"tipAfeIgv\": $tipAfeIgv,
                      \"totalImpuestos\": $totalImpuestos,
                      \"mtoPrecioUnitario\": $mtoPrecioUnitario
                    },";
                }    
                
                // Se asume que si o si hay items, por tanto se quita la ultima coma:
                $campos = substr($campos,0,strlen($campos)-1);

                $cValor         = $mtoImpVenta . "";
                $pos            = strpos($cValor, ".");
                $valor_entero   = substr($cValor,0,$pos);
                $valor_dec      = substr($cValor,$pos+1);

                $valor_dec = substr($valor_dec . "00",0,2);

                $en_letras =  "Son " . $this->fm->convertir($valor_entero) . " y $valor_dec/100 Soles";
                //$en_letras = "Son Ciento noventa y nueve solesssss";

                $campos .= "],
                  \"legends\": [
                    {
                      \"code\": \"1000\",
                      \"value\": \"$en_letras\"
                    }
                  ]
                }";
                $url = "https://facturacion.apisperu.com/api/v1/invoice/send";
        */

        /*elseif($tipo_documento == 'Nota_de_credito' || $tipo_documento == 'Nota_de_debito'){

            $campos = $this->Notas_varias($sale_id);

            $url = "https://facturacion.apisperu.com/api/v1/note/send";
        }
        */

        //echo($campos);

        $nombre_file    = "ultimo.txt";
        $gestor         = fopen($nombre_file,"w");
        fputs($gestor, $campos);
        fclose($gestor);
        
        // ***********************************
        $rpta_sunat = $this->rulo($campos, $cToken, $url);
        // ***********************************
        
        $rpta_analizada = $this->analizar_rpta_sunat($rpta_sunat);

        // Guarda la respuesta:
        if($rpta_analizada){
            $cSql = "update tec_sales set envio_electronico=1 where id = ?";
            $this->db->query($cSql, array($sale_id));
        }

        if ($rpta_analizada){
            return true;
        }else{
            return false;
        }

    }

    function rulo($campos, $cToken, $url){ 
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 

        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $campos);

        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "content-type: application/json",
                "Authorization: $cToken"
            )
        );

        //$response = curl_exec($curl);
        $response = "Bloqueado por el momento";

        curl_close($curl);

        return $response;
    }


    public function updateSale($id, $data, $items) {
        $osale = $this->getSaleByID($id);
        $oitems = $this->getAllSaleItems($id);
        foreach ($oitems as $oitem) {
            $product = $this->site->getProductByID($oitem->product_id, $osale->store_id);
            if ($product->type == 'standard') {
                $this->db->update('product_store_qty', array('quantity' => ($product->quantity+$oitem->quantity)), array('product_id' => $product->id, 'store_id' => $osale->store_id));
            } elseif ($product->type == 'combo') {
                $combo_items = $this->getComboItemsByPID($product->id);
                foreach ($combo_items as $combo_item) {
                    $cpr = $this->site->getProductByID($combo_item->id, $osale->store_id);
                    if($cpr->type == 'standard') {
                        $qty = $combo_item->qty * $oitem->quantity;
                        $this->db->update('product_store_qty', array('quantity' => ($cpr->quantity+$qty)), array('product_id' => $cpr->id, 'store_id' => $osale->store_id));
                    }
                }
            }
        }

        $data['status'] = $osale->paid > 0 ? 'partial' : ($data['grand_total'] <= $osale->paid ? 'paid' : 'due');

        if($this->db->update('sales', $data, array('id' => $id)) && $this->db->delete('sale_items', array('sale_id' => $id))) {

            foreach ($items as $item) {
                $item['sale_id'] = $id;
                if($this->db->insert('sale_items', $item)) {
                    $product = $this->site->getProductByID($item['product_id'], $osale->store_id);
                    if ($product->type == 'standard') {
                        $this->db->update('product_store_qty', array('quantity' => ($product->quantity-$item['quantity'])), array('product_id' => $product->id, 'store_id' => $osale->store_id));
                    } elseif ($product->type == 'combo') {
                        $combo_items = $this->getComboItemsByPID($product->id);
                        foreach ($combo_items as $combo_item) {
                            $cpr = $this->site->getProductByID($combo_item->id, $osale->store_id);
                            if($cpr->type == 'standard') {
                                $qty = $combo_item->qty * $item['quantity'];
                                $this->db->update('product_store_qty', array('quantity' => ($cpr->quantity-$qty)), array('product_id' => $cpr->id, 'store_id' => $osale->store_id));
                            }
                        }
                    }
                }
            }

            return TRUE;
            }

        return false;
    }

    public function suspendSale($data, $items, $did = NULL) {

        if($did) {
            if($this->db->update('suspended_sales', $data, array('id' => $did)) && $this->db->delete('suspended_items', array('suspend_id' => $did))) {
                foreach ($items as $item) {
                    unset($item['cost']);
                    $item['suspend_id'] = $did;
                    $this->db->insert('suspended_items', $item);
                }
                return TRUE;
            }

        } else {
            if($this->db->insert('suspended_sales', $data)) {
                
                $suspend_id = $this->db->insert_id();
                foreach ($items as $item) {
                    unset($item['cost']);
                    $item['suspend_id'] = $suspend_id;
                    $this->db->insert('suspended_items', $item);
                }
                return $suspend_id;
            }
        }
        return false;
    }

    public function getSuspendedSaleByID($id) {
        $q = $this->db->get_where('suspended_sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSuspendedSaleItems($id) {
        $q = $this->db->get_where('suspended_items', array('suspend_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    function get_enviada_sunat($sale_id){
        $cSql = "select envio_electronico from tec_sales where id=$sale_id";
        $query = $this->db->query($cSql);
        $envio_electronico = "0";
        foreach($query->result() as $r){
            $envio_electronico = $r->envio_electronico;
        }
        if($envio_electronico != "0"){
            return true;
        }else{
            return false;
        }
    }
/*
    function delete(){
        
        if(isset($_GET["id"])){
           $id = $_GET["id"];
           // Tec_sales
           $this->db->set(array("anulado"=>"1","grand_total"=>"0"));
           $this->db->where("id",$id);
           $this->db->update("tec_sales");

           // tec_payments:
           //$this->db->where("sale_id",$id);
           //$this->db->delete("tec_payments");

           //Se resta del stock
           //$this->sales_model->restar_stock($id, $_SESSION["store_id"]);

           $objDoc = $this->db->select("*")->where("id",$id)->get("tec_sales")->row();

            $cad_a = "";
            if($objDoc->tipoDoc == "1" || $objDoc->tipoDoc == "2"){  // Factura, boleta
                $this->sales_model->enviar_anulacion($id);
                $cad_a = " con envio a Sunat.";
            }

           $ar["rpta"] = "1";
           $ar["message"] = "Se anula el Documento {$id} {$cad_a}";
        }else{
            $ar["rpta"] = "0";
            $ar["message"] = "No se pudo anular";
        }

        echo json_encode($ar);
    }
*/
    function enviar_anulacion($id){

        /*
        $result = $this->db->select("b.codigo_sunat tipoDoc, a.date, a.serie, a.correlativo, a.store_id")
            ->from("tec_sales a")
            ->join("tec_tipos_doc b","a.tipoDoc=b.id","left")
            ->where("a.id",$id)->get()->result();
        */

        $result = $this->db->select("tec_sales.tipoDoc, tec_sales.date, tec_sales.serie, tec_sales.correlativo, tec_sales.store_id");
        $this->db->where("tec_sales.id", $id)->get()->result();
        foreach($result as $r){
            $tipo_documento = $r->tipoDoc; // 1 Factura, 2 Boleta
            
            if($r->tipoDoc == "Boleta"){
                $tipo_documento = 2;
            }else{
                if($r->tipoDoc == "Factura"){
                    $tipo_documento = 1;    
                }else{
                    echo "KO";
                }
            }

            $fec_gen        = substr($r->date,0,10) . 'T' . substr($r->date,11,8) . '-05:00';
            $serie          = $r->serie;
            $correlativo    = $r->correlativo;
            $store_id       = $r->store_id;
        }

        $maximon        = 1;
        $fec_hoy        = date("Y-m-d") . "T" . "00:00:00-05:00";
 
        //Averiguando los datos de la empresa
        $result     = $this->db->select("code, city, state, ubigeo, address1, address2, address2 nombre_empresa, code ruc")
                    ->where("id",$store_id)->get("tec_stores")->result_array();
        
        foreach($result as $r){
            $this->COMPANY_DIRECCION      = $r["address1"]; 
            $this->COMPANY_PROV           = $r["city"];
            $this->COMPANY_DPTO           = "LIMA";
            $this->COMPANY_DISTRITO       = $r["state"];
            $this->COMPANY_UBIGEO         = $r["ubigeo"];
            $this->COMPANY_RAZON_SOCIAL   = $r["nombre_empresa"]; 
            $this->COMPANY_RUC            = $r["ruc"]; 
        }

        $campus2 = 
            "\"company\": {".
            "    \"ruc\":\"" . $this->COMPANY_RUC . '",' .
                "\"razonSocial\":\"" .  $this->COMPANY_RAZON_SOCIAL . '",' .
                "\"address\": {".
                    "\"direccion\": \"" . $this->COMPANY_DIRECCION . '",'.
                    "\"provincia\": \"" . $this->COMPANY_PROV . '",'.
                    "\"departamento\": \"" .$this->COMPANY_DPTO . '",'.
                    "\"distrito\": \"" . $this->COMPANY_DISTRITO . '",'.
                    "\"ubigueo\": \"" . $this->COMPANY_UBIGEO . '"' .
                "}".
            "},";
        
        $cad = '';
        $cad .= '{';
        $cad .= '  "correlativo": "' . $maximon . '",';
        $cad .= '  "fecGeneracion": "' . $fec_gen . '",';
        $cad .= '  "fecComunicacion": "' . $fec_hoy . '",';
        
        $cad .= $campus2;
        
        $cad .= '  "details": [';
        $cad .= '    {';
        $cad .= '      "tipoDoc": "' . $tipo_documento . '",';
        $cad .= '      "serie": "' . $serie . '",';
        $cad .= '      "correlativo": "' . $correlativo . '",';
        $cad .= '      "desMotivoBaja": "ERROR EN CÁLCULOS"';
        $cad .= '    }';
        $cad .= '  ]';
        $cad .= '} '; 

        $datos = $cad;

        //echo $datos . "<br><br>";

        $url = "https://facturacion.apisperu.com/api/v1/voided/send";

        // ***********************************
        $respuesta = $this->rulo($campos, $cToken, $url);
        // ***********************************
        //$respuesta = $this->rulo($url, $datos);
        
        $gn             = null;
        $nombre_file    = "comprobantes/doc_{$id}_anulacion.txt";
        $gn             = fopen($nombre_file,"w");
        fputs($gn, $respuesta);
        fclose($gn);

        echo $respuesta;
    }

    function analizar_rpta_sunat($bloque){
        $rpta = strpos($bloque, "ha sido aceptada");
        if($rpta != false && $rpta > 0){
            return true;
        }else{ 
            return false;
        }
    }

}
