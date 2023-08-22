<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Gastus extends MY_Controller
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
        $this->load->model('gastus_model');
        $this->allowed_types = 'gif|jpg|png|pdf|doc|docx|xls|xlsx|zip|jpeg';
        
    }

    function index(){
        // $cDesde=null, $cHasta=null, $tienda = null, $fec_emi=null, $clasifica1=null, $clasifica2=null
        //if ( ! $this->Admin) {
        //    $this->session->set_flashdata('error', lang('access_denied'));
        //    redirect('pos');
        //}
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = "Gastos";
        /*$this->data['desde'] = $cDesde;
        $this->data['hasta'] = $cHasta;
        $this->data['tienda'] = $tienda;
        $this->data['fec_emi'] = $fec_emi;
        $this->data["clasifica1"] = $clasifica1;
        $this->data["clasifica2"] = $clasifica2;*/

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
        $bc = array(array('link' => '#', 'page' => 'gastos'));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);
        $this->page_construct('gastus/index', $this->data, $meta);
    }

    function get_purchases() {
        $this->load->library('datatables');

        $this->datatables->select("tec_purchases.id, tec_purchases.store_id, tec_stores.state, DATE_FORMAT(tec_purchases.date, '%d-%m-%Y') as date, 
            if(tec_purchases.tipoDoc = 'F','Factura',if(tec_purchases.tipoDoc = 'B','Boleta',if(tec_purchases.tipoDoc = 'G','Guia',if(tec_purchases.tipoDoc='R','Recibo Hon.','')))) tipoDoc, 
            tec_purchases.nroDoc, tec_suppliers.name, 
            DATE_FORMAT(tec_purchases.fec_emi_doc,'%d-%m-%Y') fec_emi_doc, 
            DATE_FORMAT(tec_purchases.fec_venc_doc, '%d-%m-%Y') fec_venc_doc, 
            tec_purchases.cargo_servicio, tec_purchases.costo_tienda, tec_purchases.costo_banco, tec_purchases.total,
            (tec_purchases.total+tec_purchases.cargo_servicio) total_,
            tec_users.username,
            datediff(tec_purchases.fec_venc_doc, tec_purchases.fec_emi_doc) as d_a_v,
            tec_purchases.subtotal, tec_purchases.igv, tec_purchases.total, 
            concat('<span title=\'',tec_purchases.note,'\'>',tec_tipo_gastos.descrip,'</span>') as clasifica1, 
            concat('<span title=\'',tec_purchases.note,'\'>',tec_subtipo_gastos.descrip,'</span>') as clasifica2,
            tec_purchases.note"
        );

        $this->datatables->from('tec_purchases');
        $this->datatables->join('tec_stores','tec_purchases.store_id = tec_stores.id');
        $this->datatables->join('tec_suppliers','tec_purchases.supplier_id = tec_suppliers.id','left');
        $this->datatables->join('tec_tipo_gastos','tec_purchases.clasifica1 = tec_tipo_gastos.id','left');
        $this->datatables->join('tec_subtipo_gastos','tec_purchases.clasifica2 = tec_subtipo_gastos.id','left');
        $this->datatables->join('tec_users','tec_purchases.created_by = tec_users.id','left');

        // solo datos de caja grande:
        $this->datatables->where("tec_purchases.tipogasto",'gastos');        

        $cDesde = $this->input->post('desde');
        $cHasta = $this->input->post('hasta');
        
        if(!is_null($cDesde)){
            if(strlen($cDesde)>0 && $cDesde != "null"){
                $this->datatables->where('tec_purchases.date>=', $cDesde);
            }
        }

        if(!is_null($cHasta) && $cHasta != "null"){
            if(strlen($cHasta)>0){
                $this->datatables->where('tec_purchases.date<=', $cHasta);
            }
        }

        $tienda = $this->input->post('tienda');

        if(!is_null($tienda)){
            if(strlen($tienda) > 0 && $tienda != '0'){
                $this->datatables->where('tec_purchases.store_id=',$tienda);
            }
        }

        $fec_emi  = $this->input->post('fec_emi');

        if(!is_null($fec_emi)){
            if(strlen($fec_emi)>0 && $fec_emi != "null"){
                $this->datatables->where('tec_purchases.fec_emi_doc=', $fec_emi);
            }
        }

        $clasifica1 = $this->input->post('clasifica1');

        if($clasifica1 != "null"){
            if(strlen($clasifica1)>0){
                $this->datatables->where("tec_purchases.clasifica1",$clasifica1);
            }
        }

        $clasifica2 = $this->input->post('clasifica2');

        if($clasifica2 != "null"){
            if(strlen($clasifica2)>0){
                $this->datatables->where("tec_purchases.clasifica2",$clasifica2);
            }
        }

        $cad_editar = "<a href='" . site_url('gastus/edit/$1') . "' title='" . lang("edit_gastus") . "' class='tip btn btn-warning btn-xs'>
                        <i class='fa fa-edit'></i>
                    </a>";

        $cad_eliminar = "<a href='" . site_url('gastus/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_purchase') . "')\" title='" . lang("delete_gastos") . "' class='tip btn btn-danger btn-xs'>
                        <i class='fa fa-trash-o'></i>
                    </a>";

        $this->datatables->add_column("Actions","
            <div class='text-center'>
                <div class='btn-group'>
                    <a href='".site_url('gastus/view/$1')."' title='Gastos' class='tip btn btn-primary btn-xs' data-toggle='ajax-modal'>
                        <i class='fa fa-file-text-o'></i>
                    </a>" . ($this->Admin ? $cad_editar . $cad_eliminar : "") .
                "</div>
            </div>", "id");

        /*echo $cDesde . "<br>";
        echo $cHasta . "<br>";
        echo  $tienda . "<br>";
        echo  $fec_emi . "<br>";
        echo  $clasifica1 . "<br>";
        echo  $clasifica2 . "<br>";
        die();*/

        echo $this->datatables->generate();
    }

    function totalizados(){  // para ver el total debajo de los filtros
        $desde = $_REQUEST["desde"];
        $hasta = $_REQUEST["hasta"];
        $tienda = $_REQUEST["tienda"];
        //$proveedor = $_REQUEST["proveedor"];
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

        if($fec_emi != "null"){
            $cad_fec_emi = " and tec_purchases.date = '$fec_emi'";
        }

        $cSql = "SELECT sum(tec_purchases.total+tec_purchases.cargo_servicio) total_,
            sum(tec_purchases.costo_tienda) costo_tienda,
            sum(tec_purchases.costo_banco) costo_banco 
            FROM `tec_purchases` 
            JOIN `tec_stores` ON `tec_purchases`.`store_id` = `tec_stores`.`id` 
            JOIN `tec_suppliers` ON `tec_purchases`.`supplier_id` = `tec_suppliers`.`id` 
            WHERE `tec_purchases`.`tipogasto` = 'caja'" . $cad_desde . $cad_hasta . $cad_tienda . $cad_fec_emi;

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
        //$cSql   = "select nroDoc from tec_purchases where tipogasto = 'gastos' and tipoDoc = 'G' order by nroDoc desc limit 1";
        $cSql   = "select nroDoc from tec_purchases where tipogasto = 'gastos' and tipoDoc = 'G' order by convert(nroDoc, SIGNED INTEGER) desc limit 1";

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

    function view($id = NULL) {
        $this->data['purchase'] = $this->gastus_model->getPurchaseByID($id);
        $this->data['items'] = $this->gastus_model->getAllPurchaseItems($id);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = lang('view_gastus');
        $this->load->view($this->theme.'gastus/view', $this->data);
    }

    function add($tipo_gasto = null) {

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

        $page_title = lang('Gastos'); // Añadir compra

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

        if(!isset($_POST["modo_edicion"])){

            

            $page_title = "Agregar Gastos";
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers']    = $this->site->getAllSuppliers();
            $this->data['page_title']   = $page_title;
            $this->data['Admin']        = ($this->session->userdata["group_id"] == '1' ? true : false);

            $qTienda = $this->site->getStoreByID($this->session->userdata('store_id'));
            $cTie = "<span style=\"color:rgb(150,150,150)\">" . $qTienda->state . "</span>";

            $bc     = array(array('link' => site_url('gastus'), 'page' => $page_title), array('link' => '#', 'page' => $page_title));
            $meta   = array('page_title' => $page_title   . " - " . $cTie, 'bc' => $bc);
            $this->page_construct('gastus/add', $this->data, $meta);
        
        // A PARTIR DE AQUI ES MODO EDICION O SAVE
        }else{

            

            $total = 0;
            $quantity = "quantity";
            $product_id = "product_id";
            $unit_cost = "cost";
            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            $cucu = "";
            
            if(is_null($this->input->post('note', TRUE))){
                $note = "";
            }else{
                $note = $this->input->post('note', TRUE);
            }

            if ($this->input->post('tipoDoc') == 'G'){
                $nroDoc = $this->generar_nro();
            }else{
                $nroDoc = $this->input->post('nroDoc');
            }

            $data = array(
                'date' => $this->input->post('date'),
                'reference' => ($this->input->post('tipoDoc') . " " . $this->input->post('nroDoc')),
                /*'supplier_id' => (isset($_POST['supplier']) ? $_POST['supplier'] : "1"),*/
                'note' => $note,
                /*'received' => $this->input->post('received'),*/
                'subtotal'  => $this->input->post('subtotal'),
                'igv'       => $this->input->post('igv'),
                'total'     => $this->input->post('total'),
                'created_by'    => $this->session->userdata('user_id'),
                'store_id'      => $this->input->post('tienda'), // $this->session->userdata('store_id')
                'tipoDoc'       => $this->input->post('tipoDoc'),
                'nroDoc'        => $nroDoc,
                'fec_emi_doc'   => $this->input->post('fec_emi_doc'),
                //'fec_venc_doc' => $this->input->post('fec_venc_doc'),*/
                //'motivo'        => $this->session->userdata('motivo'),
                'cargo_servicio'=> $this->input->post('cargo_servicio'),
                'costo_tienda'  => $this->input->post('total'),
                //'costo_banco'   => $costo_banco,*/
                'tipogasto'     => $this->input->post('tipogasto'),
                //'texto_supplier'=> (isset($_POST["texto_supplier"]) ? $_POST["texto_supplier"] : ""),
                //'descuentos'    => $this->input->post('descuentos'),
                //'nro_cta'       => $this->input->post('nro_cta') ,
                //'nro_oper'      => $this->input->post('nro_oper'),
                //'banco'         => $this->input->post('banco'),
                //'fecha_oper'    => $this->input->post('fecha_oper')
                //'peso_caja'      => $this->input->post('peso_caja')
                'clasifica1' => $this->input->post('clasifica1'),
                'clasifica2' => $this->input->post('clasifica2')
            );

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

                // retorna al listado
                $this->session->set_userdata('remove_spo', 1);
                $this->session->set_flashdata('message', 'Gasto agregado.');
                
                

                redirect("gastus");

                /*
                $cDesde = null;
                $cHasta = null;
                $tienda = null;
                $fec_emi = null;
                $clasifica1 = null;
                $clasifica2 = null;
                $this->index($cDesde, $cHasta, $tienda, $fec_emi, $clasifica1, $clasifica2);
                */

            }else{
                

                

                if ($this->gastus_model->addPurchase($data, $products)){
                    $this->session->set_userdata('remove_spo', 1);
                    $this->session->set_flashdata('message', lang('purchase_added'));
                    
                    if($this->data["tipogasto"] == "caja"){
                        redirect("gastus");
                    }else{
                        redirect("/gastus");
                    }

                }else{
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $this->data['suppliers'] = $this->site->getAllSuppliers();
                    $this->data['page_title'] = lang('Gastos');
                    $bc = array(array('link' => site_url('gastus'), 'page' => lang('gastos')), array('link' => '#', 'page' => lang('gastos')));
                    $meta = array('page_title' => lang('add_gastos'), 'bc' => $bc);
                    $this->page_construct('gastus/add', $this->data, $meta);

                }
            }
        } // FIN DE MODO EDICION
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

            $bc     = array(array('link' => site_url('gastus'), 'page' => lang('gastus')), array('link' => '#', 'page' => 'Editar Gastos'));
            $meta   = array('page_title' => $page_title, 'bc' => $bc);
            
            $this->page_construct("gastus/add", $this->data, $meta);
        }else{
            echo lang('access_denied');
        }
    }

    function delete($id = NULL) {
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

            if ($this->gastus_model->deletePurchase($id)) {
                
                // Grabacion a tabla auditoria
                $ar_audi = array();
                $ar_audi['user']    = $this->session->userdata('username');
                $ar_audi['accion']  = "delete";
                $ar_audi['tabla']   = "tec_purchases";
                $ar_audi['id_inmerso'] = $id;
                $ar_audi['fecha_hora'] = date("Y-m-d H:i:s");

                //echo $this->db->set($ar_audi)->get_compiled_insert('auditoria');
                $this->db->insert("tec_auditoria",$ar_audi); 

                $this->session->set_flashdata('message', lang("purchase_deleted"));
                redirect('gastus');
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

/*
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
*/
    function enviar_docu(){
        $this->load->view($this->theme . "gastus/v_enviar_docu");
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

    function dependencia(){
        $elegido = $_REQUEST["elegido"];
        $cSql = "select a.id, a.descrip, b.id as id1, b.tipo_id, b.descrip as descrip1 from tec_tipo_gastos a left join tec_subtipo_gastos b on a.id = b.tipo_id".
        " where a.id = '{$elegido}' order by a.id";
        $result = null;
        $result = $this->db->query($cSql)->result_array();
        $ar_p = array();
        $ar_p[0][0] = "0";
        $ar_p[0][1] = "--- Seleccione Detalle ---";
        $i = 0;
        foreach($result as $r){
            //echo "<option value=\"" . $r["id1"] . "\">" . $r["descrip1"] . "</option>";
            $i++;
            $ar_p[$i][0] = $r["id1"];
            $ar_p[$i][1] = $r["descrip1"];
        }
        for($i=0; $i < count($ar_p); $i++){
            echo "<option value=\"" . $ar_p[$i][0] . "\">" . $ar_p[$i][1] . "</option>";
        }
    }

    function tipo_gastos(){
        //$this->load->view($this->theme . "gastus/tipo_gastos");
        $bc     = array(array('link' => site_url('gastus'), 'page' => 'Gastos'), array('link' => '#', 'page' => 'Tabla de Categorias de Gastos'));
        $meta   = array('page_title' => $page_title, 'bc' => $bc);

        $this->data["saludos"] = "hola";
        $this->page_construct('gastus/tipo_gastos', $this->data, $meta);
    }

    function agregar_subtipo(){
        $tipo_id = $_REQUEST["tipo_id"];
        $descrip = $_REQUEST["descrip"];
        $comentario = $_REQUEST["comentario"];

        $this->db->set("tipo_id",$tipo_id);
        $this->db->set("descrip",$descrip);
        $this->db->set("comentario",$comentario);
        if($this->db->insert("tec_subtipo_gastos")){
            echo "Grabacion satisfactoria.";
        }else{
            echo "No se pudo grabar...";
        }
    }

    function agregar_tipo(){
        $descrip = $_REQUEST["descrip"];
        $comentario = $_REQUEST["comentario"];

        $this->db->set("descrip",$descrip);
        $this->db->set("comentario",$comentario);
        if($this->db->insert("tec_tipo_gastos")){
            echo "Grabacion satisfactoria.";
        }else{
            echo "No se pudo grabar...";
        }
    }

    function genera_correlativos_masivos(){
        // Este metodo lo haré por única vez

        /* Nros correlativos de una guia interna de gastos */
        $cSql = "select id, reference, fec_emi_doc, note, total, tipoDoc, nroDoc, clasifica1, clasifica2 
            from tec_purchases where tipoDoc = 'G' and tipogasto = 'caja' order by fec_emi_doc";

        $query = $this->db->query($cSql);

        $nro_cor = 0;
        foreach($query->result() as $r){
            $nro_cor++;
            echo "id:" . $r->id . ", fecha:" . $r->fec_emi_doc . ",  nroDoc:" . $r->nroDoc . ", el propuesto: " . $nro_cor . "<br>";

            $cSql = "update tec_purchases set nroDoc = '{$nro_cor}' where id = {$r->id}";
            //echo $cSql . "<br><br>";
            //$this->db->query($cSql);
        }
    }
}
