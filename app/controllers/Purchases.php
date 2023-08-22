<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Purchases extends MY_Controller
{

    function __construct() {
        
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        $this->load->library('form_validation');
        $this->load->model('purchases_model');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip|jpeg';
    }

    function index($cDesde=null, $cHasta=null, $tienda = null, $proveedor = null, $fec_emi=null, $estado=null, $tipo_egreso=null){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = 'Egresos';
        /*
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['tienda'] = $tienda;
        $this->data['proveedor'] = $proveedor;
        $this->data['fec_emi'] = $fec_emi;
        $this->data['estado'] = $estado;
        */

        /*
        [userdata] => Array
                (
                    [__ci_last_regenerate] => 1633963297
                    [identity] => surco
                    [username] => surco
                    [email] => surco@lcdls.com.pe
                    [user_id] => 3
                    [first_name] => Empleado
                    [last_name] => Usuario
                    [created_on] => 08/05/2121 12:33:04 PM
                    [old_last_login] => 1633723432
                    [last_ip] => ::1
                    [avatar] => 
                    [gender] => male
                    [group_id] => 2
                    [store_id] => 2
                    [has_store_id] => 2
                    [register_id] => 24
                    [cash_in_hand] => 8000.0000
                    [register_open_time] => 2021-09-30 12:36:53
                )
        */
        $bc = array(array('link' => '#', 'page' => lang('purchases')));
        $meta = array('page_title' => 'Egresos', 'bc' => $bc);
        $this->page_construct('purchases/index', $this->data, $meta);
    }

    function joka(){
        $bc = array(array('link' => '#', 'page' => lang('purchases')));
        $meta = array('page_title' => lang('purchases'), 'bc' => $bc);
        $this->page_construct('purchases/joka', $this->data, $meta);
    }

    function get_purchases() {
        $this->load->library('datatables');

        if($_POST['agrupado'] == ""){
            
            $this->datatables->select("tec_purchases.id, tec_purchases.store_id, tec_stores.state, tec_purchases.date as date, 
                if(tec_purchases.tipoDoc = 'F','Factura',if(tec_purchases.tipoDoc = 'B','Boleta','Guia')) tipoDoc, 
                tec_purchases.nroDoc, tec_suppliers.name, 
                tec_purchases.fec_emi_doc, 
                tec_purchases.fec_venc_doc, 
                tec_purchases.cargo_servicio, tec_purchases.costo_tienda, tec_purchases.costo_banco, tec_purchases.total,
                (tec_purchases.total+tec_purchases.cargo_servicio) total_,
                datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc) as d_a_v,
                if(tec_purchases.costo_tienda + tec_purchases.costo_banco < tec_purchases.total+tec_purchases.cargo_servicio,
                    if(datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc)>=4,
                        'rgb(250,250,150)',
                        if(datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc) in (2,3),
                            'orange',
                            if(tec_purchases.fec_emi_doc = '0000-00-00','lightgrey','Tomato')
                        )
                    ),
                    'lightgreen'
                ) as colores,
                if(tec_purchases.costo_tienda + tec_purchases.costo_banco < tec_purchases.total + tec_purchases.cargo_servicio, 
                    'Pendiente', 'Pagado') as estado"
            );
            
            $this->datatables->from('tec_purchases');
            $this->datatables->join('tec_stores','tec_purchases.store_id = tec_stores.id');
            $this->datatables->join('tec_suppliers','tec_purchases.supplier_id = tec_suppliers.id');

            // solo datos de caja grande:
            $this->datatables->where("tec_purchases.tipogasto",'caja');        

            $cDesde = $this->input->post('desde');
            $cHasta = $this->input->post('hasta');
            
            if(!is_null($cDesde)){
                if(strlen($cDesde)>0 && $cDesde != "null"){
                    $this->datatables->where('tec_purchases.date>=', $cDesde);
                }
            }

            if(!is_null($cHasta)){
                if(strlen($cHasta)>0 && $cHasta != "null"){
                    $this->datatables->where('tec_purchases.date<=', $cHasta . " 23:59:59");
                }
            }

            $tienda = $this->input->post('tienda');

            if(!is_null($tienda)){
                if(strlen($tienda) > 0 && $tienda != '0'){
                    $this->datatables->where('tec_purchases.store_id=',$tienda);
                }
            }

            $proveedor = $this->input->post('proveedor');

            if(!is_null($proveedor)){
                if(strlen($proveedor) > 0 && $proveedor != '0'){
                    $this->datatables->where('tec_purchases.supplier_id=',$proveedor);
                }
            }

            $fec_emi  = $this->input->post('fec_emi');

            if(!is_null($fec_emi)){
                if(strlen($fec_emi)>0 && $fec_emi != "null"){
                    $this->datatables->where('tec_purchases.fec_emi_doc=', $fec_emi);
                }
            }

            $estado  = $this->input->post('estado');

            if(!is_null($estado)){
                if(strlen($estado)>0){
                    $this->datatables->where("if(tec_purchases.costo_tienda + tec_purchases.costo_banco < tec_purchases.total + tec_purchases.cargo_servicio,'Pendiente','Pagado')=", $estado);
                }
            }

            $tipo_egreso  = $this->input->post('tipo_egreso');

            if(!is_null($tipo_egreso)){
                if(strlen($tipo_egreso) > 0 && $tipo_egreso != '0'){
                    $this->datatables->where('tec_purchases.tipo_egreso=', $tipo_egreso);    
                }
            }

            $cad_editar = "<a href='" . site_url('purchases/edit/$1') . "' title='" . lang("edit_purchase") . "' class='tip btn btn-warning btn-xs'>
                            <i class='fa fa-edit'></i>
                        </a>";

            $cad_eliminar = "<a href='" . site_url('purchases/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_purchase') . "')\" title='" . lang("delete_purchase") . "' class='tip btn btn-danger btn-xs'>
                            <i class='fa fa-trash-o'></i>
                        </a>";

            $this->datatables->add_column("Actions","
                <div class='text-center'>
                    <div class='btn-group'>
                        <a href='".site_url('purchases/view/$1')."' title='".lang('view_purchase')."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'>
                            <i class='fa fa-file-text-o'></i>
                        </a>" . ($this->Admin ? $cad_editar . $cad_eliminar : "") .
                    "</div>
                </div>", "id");

            echo $this->datatables->generate();
        
        }else{ // ****************** OPCION DE AGRUPACION POR PROVEEDOR : *******************
        
            $this->datatables->select("'' id, tec_purchases.store_id, tec_stores.state, '' as date, 
                '' tipoDoc, 
                '' nroDoc, tec_suppliers.name, 
                '' fec_emi_doc, 
                '' fec_venc_doc, 
                '' cargo_servicio, '' costo_tienda, '' costo_banco, sum(tec_purchases.total) total,
                sum(tec_purchases.total+tec_purchases.cargo_servicio) total_,
                '' as d_a_v,
                'white' as colores,
                '' as estado"
            );
            
            $this->datatables->from('tec_purchases');
            $this->datatables->join('tec_stores','tec_purchases.store_id = tec_stores.id');
            $this->datatables->join('tec_suppliers','tec_purchases.supplier_id = tec_suppliers.id');

            // solo datos de caja grande:
            $this->datatables->where("tec_purchases.tipogasto",'caja');        

            $cDesde = $this->input->post('desde');
            $cHasta = $this->input->post('hasta');
            
            if(!is_null($cDesde)){
                if(strlen($cDesde)>0 && $cDesde != "null"){
                    $this->datatables->where('tec_purchases.date>=', $cDesde);
                }
            }

            if(!is_null($cHasta)){
                if(strlen($cHasta)>0 && $cHasta != "null"){
                    $this->datatables->where('tec_purchases.date<=', $cHasta . " 23:59:59");
                }
            }

            $tienda = $this->input->post('tienda');

            if(!is_null($tienda)){
                if(strlen($tienda) > 0 && $tienda != '0'){
                    $this->datatables->where('tec_purchases.store_id=',$tienda);
                }
            }

            $proveedor = $this->input->post('proveedor');

            if(!is_null($proveedor)){
                if(strlen($proveedor) > 0 && $proveedor != '0'){
                    $this->datatables->where('tec_purchases.supplier_id=',$proveedor);
                }
            }

            $fec_emi  = $this->input->post('fec_emi');

            if(!is_null($fec_emi)){
                if(strlen($fec_emi)>0 && $fec_emi != "null"){
                    $this->datatables->where('tec_purchases.fec_emi_doc=', $fec_emi);
                }
            }

            $estado  = $this->input->post('estado');

            if(!is_null($estado)){
                if(strlen($estado)>0){
                    $this->datatables->where("if(tec_purchases.costo_tienda + tec_purchases.costo_banco < tec_purchases.total + tec_purchases.cargo_servicio,'Pendiente','Pagado')=", $estado);
                }
            }

            $tipo_egreso  = $this->input->post('tipo_egreso');

            if(!is_null($tipo_egreso)){
                if(strlen($tipo_egreso) > 0 && $tipo_egreso != '0'){
                    $this->datatables->where('tec_purchases.tipo_egreso=', $tipo_egreso);    
                }
            }

            $this->datatables->group_by('tec_purchases.supplier_id');

            $cad_editar = "<a href='" . site_url('purchases/edit/$1') . "' title='" . lang("edit_purchase") . "' class='tip btn btn-warning btn-xs'>
                            <i class='fa fa-edit'></i>
                        </a>";

            $cad_eliminar = "<a href='" . site_url('purchases/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_purchase') . "')\" title='" . lang("delete_purchase") . "' class='tip btn btn-danger btn-xs'>
                            <i class='fa fa-trash-o'></i>
                        </a>";

            $this->datatables->add_column("Actions","
                <div class='text-center'>
                    <div class='btn-group'>
                        <a href='".site_url('purchases/view/$1')."' title='".lang('view_purchase')."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'>
                            <i class='fa fa-file-text-o'></i>
                        </a>" . ($this->Admin ? $cad_editar . $cad_eliminar : "") .
                    "</div>
                </div>", "id");

            echo $this->datatables->generate();

        }
    }

    function totalizados(){  // para ver el total debajo de los filtros
        $desde = $_REQUEST["desde"];
        $hasta = $_REQUEST["hasta"];
        $tienda = $_REQUEST["tienda"];
        $proveedor = $_REQUEST["proveedor"];
        $fec_emi = $_REQUEST["fec_emi"];
            
        $cad_desde = $cad_hasta = $cad_tienda = $cad_proveedor = $cad_fec_emi = "";

        if($desde != "null"){
            $cad_desde = " and tec_purchases.fec_emi_doc >= '$desde'";
        }

        if($hasta != "null"){
            $cad_hasta = " and tec_purchases.fec_venc_doc <= '$hasta'";
        }

        if($tienda){
            $cad_tienda = " and tec_purchases.store_id = '$tienda'";
        }

        if($proveedor){
            $cad_proveedor = " and tec_purchases.supplier_id = '$proveedor'";
        }

        if($fec_emi != "null"){
            $cad_fec_emi = " and tec_purchases.date = '$fec_emi'";
        }

        $cSql = "SELECT sum(tec_purchases.total+tec_purchases.cargo_servicio) total_,
            sum(tec_purchases.costo_tienda) costo_tienda,
            sum(tec_purchases.costo_banco) costo_banco 
            FROM `tec_purchases` 
            JOIN `tec_stores` ON `tec_purchases`.`store_id` = `tec_stores`.`id` 
            JOIN `tec_suppliers` ON `tec_purchases`.`supplier_id` = `tec_suppliers`.`id` 
            WHERE `tec_purchases`.`tipogasto` = 'caja'" . $cad_desde . $cad_hasta . $cad_tienda . $cad_proveedor . $cad_fec_emi;

        $query = $this->db->query($cSql);

        $simbolo = "<span style=\"color:red;font-weight:bold\">S/&nbsp;&nbsp;</span>";
        foreach($query->result() as $r){
            echo "Total: $simbolo" . number_format($r->total_,2);
            echo "&nbsp;&nbsp;&nbsp;&nbsp;Caja-Tienda: $simbolo" . number_format($r->costo_tienda,2);
            echo "&nbsp;&nbsp;&nbsp;&nbsp;Caja-Banco: $simbolo" . number_format($r->costo_banco,2);
        }

        //echo $cSql;
    }

    function generar_nro(){
        //$cSql   = "select nroDoc from tec_purchases where tipogasto = 'caja' and tipoDoc = 'G' order by nroDoc desc limit 1";
        $cSql   = "select nroDoc from tec_purchases where tipogasto = 'caja' and tipoDoc = 'G' order by convert(nroDoc, SIGNED INTEGER) desc limit 1";
        $query  = $this->db->query($cSql);
        $nDato  = 0;
        foreach($query->result() as $r){
            $nDato = $r->nroDoc;
            if(is_numeric($nDato)){
                $nDato = $nDato + 1;
            }else{
                $nDato = 1;
            }
        }
        return $nDato;
    }

    function view($id = NULL){
        $this->data['purchase'] = $this->purchases_model->getPurchaseByID($id);
        $this->data['items'] = $this->purchases_model->getAllPurchaseItems($id);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('view_purchase');
        $this->load->view($this->theme.'purchases/view', $this->data);
    }

    function add($tipo_egreso = 'producto'){

        $tipo_gasto = null;
        //print_r(str_replace("\n", "<br>", $_SESSION));
        //die();

        if (!$this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        
        // SI ES ADMIN ENTONCES QUE TOME LA TIENDA POR DEFAULT EN SU TABLA
        
        if(isset($_SESSION["edicion"])){
            if ($_SESSION["edicion"] == true){
                $_SESSION["edicion"] = false;
            }else{
                if($this->Admin){
                    $result = $this->db->select("store_id")->where("username",'admin')->get("users")->result_array();
                    foreach($result as $r){
                        $_SESSION['store_id'] = $r["store_id"];
                    }
                }
            }
        }else{
            if($this->Admin){
                $result = $this->db->select("store_id")->where("username",'admin')->get("users")->result_array();
                foreach($result as $r){
                    $_SESSION['store_id'] = $r["store_id"];
                }
            }
        }

        $page_title = "A&ntilde;adir Egreso";

        if(!isset($this->data["tipogasto"])){
            if(!is_null($tipo_gasto)){
                $page_title             = lang("add_expense");  // gastos
                $this->data["tipogasto"] = $tipo_gasto;
            
            }else{

                if(isset($_POST["tipogasto"])){
                    $this->data["tipogasto"] = $_POST["tipogasto"];
                }else{
                    $this->data["tipogasto"] = "caja";
                }
            }
        }else{
            $page_title             = lang("add_expense");  // gastos
            echo($this->data["tipogasto"]); 
            die();
        }

        $this->data['tipo_egreso']  = $tipo_egreso;
        $this->data['Admin']        = ($this->session->userdata["group_id"] == '1' ? true : false);

        if(!isset($_POST["modo_edicion"])){

            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            //$this->data['suppliers']    = $this->site->getAllSuppliers();
            
            if($tipo_egreso == 'producto'){ $this->db->where('activo','1'); }
            $this->data['suppliers']    = $this->db->select('*')->from('suppliers')->get()->result();
            
            $this->data['page_title']   = $page_title;

            $qTienda = $this->site->getStoreByID($this->session->userdata('store_id'));
            $cTie = "<span style=\"color:rgb(150,150,150)\">" . $qTienda->state . "</span>";

            $bc     = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('add_purchase')));
            $meta   = array('page_title' => $page_title   . " - " . $cTie, 'bc' => $bc);
            $this->page_construct('purchases/add', $this->data, $meta);
        
        // A PARTIR DE AQUI ES MODO EDICION O SAVE
        }else{

            if (!$this->data['Admin']){
                $this->form_validation->set_rules('fec_emi_doc', "Fecha de Emisión", 'callback_fecha_valida');
            }

            $total          = 0;
            $quantity       = "quantity";
            $product_id     = "product_id";
            $unit_cost      = "cost";
            $i              = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            $cucu           = "";
            
            for ($r = 0; $r < $i; $r++) {
                $item_id    = $_POST['product_id'][$r];
                $rubro_id   = $_POST['rubro_id'][$r];
                $item_qty   = $_POST['quantity'][$r];
                $item_cost  = $_POST['cost'][$r];
                $peso_caja  = $_POST['peso_caja'][$r];
                $precio     = $_POST['precio'][$r];
                $detalle    = $_POST['detalle'][$r];
            
                $cucu.= "item:" . $item_id . "<br>";
                $cucu.= "cantidad:".$item_qty . "<br>";
                $cucu.= "costo:".$item_cost . "<br><br>";
                
                if($item_id && $item_qty && $item_cost){

                    //echo "item_qty: $item_qty<br>";
                    if(!$this->purchases_model->getProductByID($item_id)){
                        //echo $item_id;
                        $this->session->set_flashdata('error', $this->lang->line("product_not_found")." ( ".$item_id." ).");
                        //$this->session->set_flashdata('error', $cucu ." ( ".$item_id." ).");
                        
                        redirect('purchases/add');
                    }

                    // Este arreglo va a entrar al metodo grabar_producto (addPurchase)
                    $products[] = array(
                        'product_id' => $item_id,
                        'rubro_id' =>   $rubro_id,
                        'cost' =>       $item_cost,
                        'quantity' =>   $item_qty,
                        'subtotal' =>   ($item_cost*$item_qty),
                        'peso_caja' =>  $peso_caja,
                        'precio' =>     $precio,
                        'detalle' =>    $detalle
                    );

                }
            }

            $total = (1 * $this->input->post('txt_gTotal')) + $this->input->post('cargo_servicio');
            
            if($this->data["tipogasto"] == "gastos"){
                $costo_tienda   = "0";
                $costo_banco    = "0";
            }else{
                $costo_tienda   = $this->input->post('costo_tienda');
                $costo_banco    = $this->input->post('costo_banco');
            }

            // Validacion de los costos:
            $ct = $costo_tienda * 1;
            $cb = $costo_banco * 1;
            $cargo = $this->input->post('cargo_servicio');

            if(strlen($cargo)>0){
                $cargo = $cargo * 1;
            }else{
                $cargo = 0;
            }

            $el_total = $ct + $cb + 0;

            $datin = array('tipoDoc'=>$this->input->post('tipoDoc'));
            
            if (!isset($products) || empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if(is_null($this->input->post('note'))){
                $note = "";
            }else{
                $note = $this->input->post('note');
            }

            if ($this->input->post('tipoDoc') == 'G'){
                $nroDoc = $this->generar_nro();
            }else{
                $nroDoc = $this->input->post('nroDoc');
            }

            // Se guarda para en caso la validacion sea falsa
            $this->data["date"]             = $this->input->post('date');
            $this->data["date_ingreso"]     = $this->input->post('date_ingreso');
            $this->data["reference"]        =  ($this->input->post('tipoDoc') . " " . $this->input->post('nroDoc'));
            $this->data["supplier_id"]      = (isset($_POST['supplier']) ? $_POST['supplier'] : "1");
            $this->data["note"]             = $note;
            $this->data["received"]         = $this->input->post('received');
            $this->data["subtotal"]         = $this->input->post('txt_gSubtotal');
            $this->data["igv"]              = $this->input->post('txt_gIgv');
            $this->data["total"]            = $this->input->post('txt_gTotal');
            $this->data["created_by"]       = $this->session->userdata('user_id');
            $this->data["store_id"]         = $this->input->post('tienda');
            $this->data["tipoDoc"]          = $this->input->post('tipoDoc');
            $this->data["nroDoc"]           = $nroDoc;
            $this->data["fec_emi_doc"]      = $this->input->post('fec_emi_doc');
            $this->data["fec_venc_doc"]     = $this->input->post('fec_venc_doc');
            $this->data["cargo_servicio"]   = $cargo;
            $this->data["costo_tienda"]     = $costo_tienda;
            $this->data["costo_banco"]      = $costo_banco;
            $this->data["tipogasto"]        = $this->input->post('tipogasto');
            $this->data["texto_supplier"]   = (isset($_POST["texto_supplier"]) ? $_POST["texto_supplier"] : "");
            $this->data["descuentos"]       = $this->input->post('descuentos');
            $this->data["nro_cta"]          = $this->input->post('nro_cta');
            $this->data["nro_oper"]         = $this->input->post('nro_oper');
            $this->data["banco"]            = $this->input->post('banco');
            $this->data["fecha_oper"]       = $this->input->post('fecha_oper');
            $this->data["redondeo"]         = $this->input->post('redondeo');
            $this->data["tipo_egreso"]      = $this->input->post('tipo_egreso');
            //$this->data["fecha_oper"]     = ;

            $data = array(        
                'date'          => $this->input->post('date'),
                'date_ingreso'  => $this->input->post('date_ingreso'),
                'reference'     => ($this->input->post('tipoDoc') . " " . $this->input->post('nroDoc')),
                'supplier_id'   => (isset($_POST['supplier']) ? $_POST['supplier'] : "1"),
                'note'          => $note,
                'received' => $this->input->post('received'),
                'subtotal'  => $this->input->post('txt_gSubtotal'),
                'igv'       => $this->input->post('txt_gIgv'),
                'total'     => $this->input->post('txt_gTotal'),
                'created_by'    => $this->session->userdata('user_id'),
                'store_id'      => $this->input->post('tienda'), // $this->session->userdata('store_id')
                'tipoDoc'       => $this->input->post('tipoDoc'),
                'nroDoc'        => $nroDoc,
                'fec_emi_doc'   => $this->input->post('fec_emi_doc'),
                'fec_venc_doc' => $this->input->post('fec_venc_doc'),
                //'motivo'        => $this->session->userdata('motivo'),
                'cargo_servicio'=> $cargo,
                'costo_tienda'  => $costo_tienda,
                'costo_banco'   => $costo_banco,
                'tipogasto'     => $this->input->post('tipogasto'),
                'texto_supplier'=> (isset($_POST["texto_supplier"]) ? $_POST["texto_supplier"] : ""),
                'descuentos'    => $this->input->post('descuentos'),
                'nro_cta'       => $this->input->post('nro_cta') ,
                'nro_oper'      => $this->input->post('nro_oper'),
                'banco'         => $this->input->post('banco'),
                'fecha_oper'    => $this->input->post('fecha_oper'),
                'redondeo'      => $this->input->post('redondeo'),
                'tipo_egreso'   => $this->input->post('tipo_egreso')
            );

            if ($this->form_validation->run() == true){

                /*
                    if ($_FILES['userfile']['size'] > 0) {

                        $this->load->library('upload');
                        $config['upload_path']      = 'uploads/';
                        $config['allowed_types']    = $this->allowed_types;
                        $config['max_size']         = '2000';
                        $config['overwrite']        = FALSE;
                        $config['encrypt_name']     = TRUE;
                        
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload()) {
                            $error = $this->upload->display_errors();
                            $this->upload->set_flashdata('error', $error);
                            redirect("purchases/add");
                        }

                        $data['attachment'] = $this->upload->file_name;

                    }
                */

                //if ($this->form_validation->run() == true){

                if(strlen($_REQUEST["edicion_purchase_id"])>0){
                
                    // Agregando el purchase_id para el insert del detalle
                    for($i=0; $i<count($products); $i++){
                        $products[$i]['purchase_id'] = $_REQUEST["edicion_purchase_id"];
                    }

                    $this->db->where("id",$_REQUEST["edicion_purchase_id"]);
                    
                    // En la cabecera:
                    $this->db->set($data);
                    $this->db->update("purchases",$data);
                    
                    // En el detalle
                    $this->db->where('purchase_id',$_REQUEST["edicion_purchase_id"]);
                    $this->db->delete('purchase_items');

                    for($i=0; $i<count($products); $i++){
                        $this->db->insert("purchase_items",$products[$i]);
                    }
                    //print_r($products);
                    //die("Esto es modo edicion...");

                    /*******************************/
                    //$this->actualiza_saldos($data);
                    /*******************************/                   

                    // retorna al listado
                    $this->session->set_userdata('remove_spo', 1);
                    $this->session->set_flashdata('message', lang('purchase_added'));
                    redirect("purchases");

                }else{
                    
                    if ($this->purchases_model->addPurchase($data, $products)){

                        /*******************************/
                        //$this->actualiza_saldos($data);
                        /*******************************/                   

                        $this->session->set_userdata('remove_spo', 1);
                        $this->session->set_flashdata('message', lang('purchase_added'));
                        
                        if($this->data["tipogasto"] == "caja"){
                            redirect("purchases");
                        }else{
                            redirect("purchases/gastos");
                        }

                    }else{
                        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                        $this->data['suppliers'] = $this->site->getAllSuppliers();
                        $this->data['page_title'] = lang('add_purchase');
                        $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('add_purchase')));
                        $meta = array('page_title' => lang('add_purchase'), 'bc' => $bc);
                        $this->page_construct('purchases/add', $this->data, $meta);
                    }
                }

            }else{
                
                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['suppliers'] = $this->site->getAllSuppliers();
                $this->data['page_title'] = lang('add_purchase');
                $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('add_purchase')));
                $meta = array('page_title' => lang('add_purchase'), 'bc' => $bc);
                $this->page_construct('purchases/add', $this->data, $meta);
            }

        } // FIN DE MODO EDICION
    }

    function fecha_valida($fecha){ // callback (puedes ingresarlo hasta antes de la 1pm del dia siguiente)
        
        $fechaM = date('Y-m-d', strtotime($fecha .' 1 day'));

        if($fecha == null){
            return false;
        }elseif($fecha==''){
            return false;
        }else{
            if($fechaM <= date("Y-m-d")){
                if($fechaM == date("Y-m-d")){
                    if( intval(date("H")) > 13 ){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }
    }


    function actualiza_saldos($data){ // Actualiza saldos iniciales de una fecha en adelante
        if($data["date"] < date("Y-m-d")){
            $fx = $data["date"];
            
            $feus = $fx;
            $next_date = date('Y-m-d', strtotime($feus .' +1 day'));

            while($next_date <= date("Y-m-d")){
                
                // Tanto feus como next_date deben ser fechas con open/close
                $cSql = "select status from tec_registers where store_id = ? and date(date) = ? and status in ('open','close')";
                $query = $this->db->query($cSql, array($data["store_id"], $feus));
                $bandera1 = $bandera2 = false;
                foreach($query->result() as $r){
                    $bandera1 = true;
                }

                $cSql = "select status from tec_registers where store_id = ? and date(date) = ? and status in ('open','close')";
                $query = $this->db->query($cSql, array($data["store_id"], $next_date));
                foreach($query->result() as $r){
                    $bandera2 = true;
                }

                //echo "========================================== next_date: $next_date <br>";
                if($bandera2){
                    if($this->calcular_cuadre($feus, $next_date, $data["store_id"])){
                        //echo("OK mackein  $feus  $next_date<br>");
                    }else{
                        //echo("No pasa nada.... $feus  $next_date<br>");
                    }
                    // Muy correcto, el saldo final se retrasa a propósito.
                    $feus = date('Y-m-d', strtotime($feus .' +1 day'));    
                }
                $next_date = date('Y-m-d', strtotime($next_date .' +1 day'));
            }
            //die("Fin");
        }
    }

    function edit($id = NULL){
        $acceso = false;
        $_SESSION["edicion"] = true;
        if(!is_null($id)){
            $result = $this->db->select("date, tipogasto, store_id")->where(id,$id)->get("purchases")->result_array();
            foreach($result as $r){
                $la_fecha = substr($r["date"],0,10);
                $tipogasto = $r["tipogasto"];

                // CON ESTE CAMBIO SE PUEDE MODIFICAR SIN PROBLEMA DIFERENTES DOCUMENTOS DE DIFERENTES TIENDAS
                if($this->Admin){
                    $_SESSION['store_id'] = $r["store_id"];
                }
            }
            
            $hoy = date("Y-m-d");

            if($la_fecha >= $hoy){
                $acceso = true;
            }

            if($tipogasto == "caja"){
                $page_title = lang('add_purchase');
            }                
            
            if($tipogasto == "gastos"){
                $page_title = lang('add_expense');
            }                

            $this->data["tipogasto"] = $tipogasto;

        }

        if($this->Admin){
            $acceso = true;
        }

        if ($acceso) {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers']    = $this->site->getAllSuppliers();
            $this->data['page_title']   = $page_title;
            $this->data['purchases_id'] = $id;     
            
            $qTienda = $this->site->getStoreByID($this->session->userdata('store_id'));
            $cTie = "<span style=\"color:rgb(150,150,150)\">" . $qTienda->state . "</span>";

            $bc     = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('add_purchase')));
            $meta   = array('page_title' => $page_title . "-" . $cTie, 'bc' => $bc);
            
            $this->page_construct("purchases/add", $this->data, $meta);
        }else{
            echo lang('access_denied');
        }
    }

    function delete($id = NULL){
        $acceso = false;
        if(!is_null($id)){
            $result = $this->db->select("date")->where(id,$id)->get("purchases")->result_array();
            foreach($result as $r){
                $la_fecha = substr($r["date"],0,10);
            }
            $hoy = date("Y-m-d");

            if($la_fecha >= $hoy){
                $acceso = true;
            }
        }

        if($this->Admin){
            $acceso = true;
        }

        if ($acceso) {
        
            if(DEMO) {
                $this->session->set_flashdata('error', lang('disabled_in_demo'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
            }
            if ( ! $this->Admin) {
                $this->session->set_flashdata('error', lang('access_denied'));
                redirect('pos');
            }
            if ($this->input->get('id')) {
                $id = $this->input->get('id');
            }

            // averiguando date y store_id
            $this->db->select("date, store_id");
            $this->db->from("tec_purchases");
            $this->db->where("id", $id);
            $result = $this->db->get()->result();
            
            foreach($result as $r){
                $date       = $r->date;
                $store_id   = $r->store_id;
            }

            if ($this->purchases_model->deletePurchase($id)) {
                
                // Grabacion a tabla auditoria
                $ar_audi = array();
                $ar_audi['user']    = $this->session->userdata('username');
                $ar_audi['accion']  = "delete";
                $ar_audi['tabla']   = "tec_purchases";
                $ar_audi['id_inmerso'] = $id;
                $ar_audi['fecha_hora'] = date("Y-m-d H:i:s");

                //echo $this->db->set($ar_audi)->get_compiled_insert('auditoria');
                $this->db->insert("tec_auditoria",$ar_audi); 

                $datus = array("date"=>$date,"store_id"=>$store_id);
                /*******************************/
                //$this->actualiza_saldos($datus);
                /*******************************/                   

                $this->session->set_flashdata('message', lang("purchase_deleted"));
                redirect('purchases');
            }
        }else{
            echo lang('access_denied');
        }
    }

    function suggestions($id = NULL) {
        if($id) {
            $row = $this->site->getProductByID($id);
            $row->qty = 1;
            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            echo json_encode($pr);
        }
        $term = $this->input->get('term', TRUE);
        $rows = $this->purchases_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

     /* ----------------------------------------------------------------- */

    function expenses($id = NULL) {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('expenses');
        $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => '#', 'page' => lang('expenses')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('purchases/expenses', $this->data, $meta);

    }

    function gastos($cDesde=null, $cHasta=null){
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('purchases');
        $this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        //echo $cDesde . " " . $cHasta . "<br>";
        
        $bc = array(array('link' => '#', 'page' => lang('purchases')));
        $meta = array('page_title' => lang('expenses'), 'bc' => $bc);
        $this->page_construct('gastos/index', $this->data, $meta);
    }

    function get_gastos() {
        $this->load->library('datatables');

        /* OPCION INICIAL
        $this->datatables->select("tec_purchases.id, tec_purchases.store_id, DATE_FORMAT(date, '%Y-%m-%d') as date, tipoDoc, nroDoc, name, fec_emi_doc, fec_venc_doc, cargo_servicio, total_, _");
        $this->datatables->from('tec_purchases');
        $this->datatables->join('tec_suppliers', 'tec_suppliers.id=tec_purchases.supplier_id');
        */

        $this->datatables->select("tec_purchases.id, tec_purchases.store_id, tec_stores.state, DATE_FORMAT(tec_purchases.date, '%Y-%m-%d') as date, tec_purchases.tipoDoc, tec_purchases.nroDoc, tec_suppliers.name, tec_purchases.fec_emi_doc, tec_purchases.fec_venc_doc, 
            tec_purchases.cargo_servicio, tec_purchases.costo_tienda, tec_purchases.costo_banco, tec_purchases.total,
            (tec_purchases.total+tec_purchases.cargo_servicio) total_, tec_purchases.texto_supplier,
            datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc) as d_a_v,
            if(tec_purchases.costo_tienda + tec_purchases.costo_banco < tec_purchases.total+tec_purchases.cargo_servicio,
                if(datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc)>=4,
                    'yellow',
                    if(datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc) in (2,3),
                        'orange',
                        if(tec_purchases.fec_emi_doc = '0000-00-00','lightgrey','red')
                    )
                ),
                'lightgreen'
            ) as colores");
        
        $this->datatables->from('tec_purchases');
        $this->datatables->join('tec_stores','tec_purchases.store_id = tec_stores.id');
        $this->datatables->join('tec_suppliers','tec_purchases.supplier_id = tec_suppliers.id');

        // solo datos de caja grande:
        $this->datatables->where("tec_purchases.tipogasto",'gastos');        

        if(!$this->Admin){
            $this->datatables->where('store_id', $this->session->userdata('store_id'));
        }
        
        $cDesde = $this->input->post('desde');
        $cHasta = $this->input->post('hasta');
        
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0){
                $this->datatables->where('tec_purchases.date>=', $cDesde);
            }
        }

        if(!is_null($cHasta)){
            if(strlen($cHasta)>0){
                $this->datatables->where('tec_purchases.date<=', $cHasta);
            }
        }

        $cad_editar = "<a href='" . site_url('purchases/edit/$1') . "' title='" . lang("edit_purchase") . "' class='tip btn btn-warning btn-xs'>
                        <i class='fa fa-edit'></i>
                    </a>";

        $cad_eliminar = "<a href='" . site_url('purchases/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_purchase') . "')\" title='" . lang("delete_purchase") . "' class='tip btn btn-danger btn-xs'>
                        <i class='fa fa-trash-o'></i>
                    </a>";

        $this->datatables->add_column("Actions","
            <div class='text-center'>
                <div class='btn-group'>
                    <a href='".site_url('purchases/view/$1')."' title='".lang('view_purchase')."' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'>
                        <i class='fa fa-file-text-o'></i>
                    </a>" . ($this->Admin ? $cad_editar . $cad_eliminar : "") .
                "</div>
            </div>", "id");

        //$this->datatables->unset_column('tec_purchases.id');
        
        //echo $this->db->get_compiled_select("tec_purchases");
        echo $this->datatables->generate();
    }

    function expense_note($id = NULL) {
        if ( ! $this->Admin) {
            if($expense->created_by != $this->session->userdata('user_id')) {
                $this->session->set_flashdata('error', lang('access_denied'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'pos');
            }
        }

        $expense = $this->purchases_model->getExpenseByID($id);
        $this->data['user'] = $this->site->getUser($expense->created_by);
        $this->data['expense'] = $expense;
        $this->data['page_title'] = $this->lang->line("expense_note");
        $this->load->view($this->theme . 'purchases/expense_note', $this->data);

    }

    function add_expense() {
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        $this->load->helper('security');

        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $date = trim($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference') ? $this->input->post('reference') : $this->site->getReference('ex'),
                'amount' => $this->input->post('amount'),
                'created_by' => $this->session->userdata('user_id'),
                'store_id' => $this->session->userdata('store_id'),
                'note' => $this->input->post('note', TRUE)
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->tec->print_arrays($data);

        } elseif ($this->input->post('add_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->purchases_model->addExpense($data)) {

            $this->session->set_flashdata('message', lang("expense_added"));
            redirect('purchases/gastos');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = lang('add_expense');
            $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => site_url('purchases/expenses'), 'page' => lang('expenses')), array('link' => '#', 'page' => lang('add_expense')));
            $meta = array('page_title' => lang('add_expense'), 'bc' => $bc);
            $this->page_construct('purchases/add_expense', $this->data, $meta);

        }
    }

    function edit_expense($id = NULL) {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference', lang("reference"), 'required');
        $this->form_validation->set_rules('amount', lang("amount"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $date = trim($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $data = array(
                'date' => $date,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount'),
                'note' => $this->input->post('note', TRUE)
            );
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            //$this->tec->print_arrays($data);

        } elseif ($this->input->post('edit_expense')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }


        if ($this->form_validation->run() == true && $this->purchases_model->updateExpense($id, $data)) {
            $this->session->set_flashdata('message', lang("expense_updated"));
            redirect("purchases/expenses");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['expense'] = $this->purchases_model->getExpenseByID($id);
            $this->data['page_title'] = lang('edit_expense');
            $bc = array(array('link' => site_url('purchases'), 'page' => lang('purchases')), array('link' => site_url('purchases/expenses'), 'page' => lang('expenses')), array('link' => '#', 'page' => lang('edit_expense')));
            $meta = array('page_title' => lang('edit_expense'), 'bc' => $bc);
            $this->page_construct('purchases/edit_expense', $this->data, $meta);

        }
    }

    function delete_expense($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $expense = $this->purchases_model->getExpenseByID($id);
        if ($this->purchases_model->deleteExpense($id)) {
            if ($expense->attachment) {
                unlink($this->upload_path . $expense->attachment);
            }
            $this->session->set_flashdata('message', lang("expense_deleted"));
            redirect('purchases/expenses');
        }
    }

    function enviar_docu(){
        $this->load->view($this->theme . "purchases/v_enviar_docu");
    }

    function verificarUnicidad(){
        $supplier_id    = $_REQUEST["supplier"];
        $nroDoc         = $_REQUEST["nroDoc"];
        $this->db->select("id");
        $this->db->from("purchases");
        $this->db->where("supplier_id",$supplier_id);
        $this->db->where("nroDoc",$nroDoc);
        $query = $this->db->get();
        echo $query->num_rows();
    }

    //function calcular_cuadre($fecha, $fecha_cinicial, $store_id){
    //    $this->fm->calcular_cuadre($fecha, $fecha_cinicial, $store_id);        
    //}

    function calcular_cuadre($fecha, $fecha_cinicial, $store_id=null){

        if(!is_null($store_id) && strlen($store_id."")>0){
            // El calculo del cuadre se realiza siempre y cuando exista apertura/cierre de este dia (fecha_cinicial).
            //$conn       = $this->conectar();
            $cSql       = $this->fm->query_salidas_por_dia($store_id, $fecha, $fecha);
            $query      = $this->db->query($cSql);

            $bandera = false;
            foreach($query->result() as $r){
                $bandera = true;
                $caja_final_efectivo = $r->caja_final_efectivo;
            }

            if($bandera){
                $cSql = "update tec_registers set cash_in_hand = ? where date(date) = ? and store_id = ?";
                //$pdo = $conn->prepare($cSql);
                //$pdo->bindParam(1,$caja_final_efectivo);
                //$pdo->bindParam(2,$fecha_cinicial);
                //$pdo->bindParam(3,$store_id);
                //$pdo->execute();
                
                $this->db->query($cSql,array($caja_final_efectivo, $fecha_cinicial, $store_id));
                return true;
            }else{
                return false;
            }
        }
    }

    function obtener_cuentas(){
        $cod_banco = $_REQUEST["banco"];
        $cSql = "select * from tec_banco_cuentas where banco = ? and activo = '1'";
        $result = $this->db->query($cSql,array($cod_banco))->result_array();
        echo json_encode($result);
    }

    function obtener_productos(){
        $rubro_id   = $_REQUEST["rubro_id"];
        $cSql       = "select id, upper(name) producto from tec_products where category_id = 7 and rubro = ? order by name";
        $result     = $this->db->query($cSql,array($rubro_id))->result_array();
        echo json_encode($result);
    }

}
