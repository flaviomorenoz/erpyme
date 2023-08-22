<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('products_model');
    }

    function index($tienda="",$categoria="") {
        
        $stores = $this->site->getAllStores();
        if ($this->input->get('store_id') && !$this->session->userdata('has_store_id')) {
            $this->data['store'] = $this->site->getStoreByID($this->input->get('store_id', TRUE));
        } elseif ($this->session->userdata('store_id')) {
            $this->data['store'] = $this->site->getStoreByID($this->session->userdata('store_id'));
        } else {
            $this->data['store'] = current($stores);
        }
        $this->data['stores'] = $stores;
        $this->data['tienda'] = $cTienda; 
        
        if(strlen($tienda)>0){
            $this->data["tienda"] = $tienda;
        }else{
            $this->data["tienda"] = $this->input->get('store_id');
        }

        if(strlen($categoria)>0){
            $this->data["categoria"] = $categoria;
        }else{
            $this->data["categoria"] = "";
        }

        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('products');
        $bc = array(array('link' => '#', 'page' => lang('products')));
        $meta = array('page_title' => lang('products'), 'bc' => $bc);
        $this->page_construct('products/index', $this->data, $meta);
    }

    function get_products($store_id, $cTienda="",$categoria="") {

        if (strlen($cTienda)>0){
            $store_id = $cTienda;
        }
        
        $this->load->library('datatables');
        if ($this->Admin) {
            /*
            $this->datatables->select($this->db->dbprefix('products').".id as pid, ".$this->db->dbprefix('products').".image as image, ".$this->db->dbprefix('products').".code as code, ".$this->db->dbprefix('products').".name as pname, type, ".$this->db->dbprefix('categories').".name as cname, psq.quantity, tax, tax_method, cost, (CASE WHEN psq.price > 0 THEN psq.price ELSE {$this->db->dbprefix('products')}.price END) as price, barcode_symbology", FALSE);*/

            $this->datatables->select("tec_products.id as pid, tec_products.image as image, tec_products.code as code, tec_products.name as pname, tec_products.type, ".$this->db->dbprefix('categories').".name as cname, psq.quantity, tax, tax_method, cost, (CASE WHEN psq.price > 0 THEN psq.price ELSE tec_products.price END) as price, barcode_symbology, psq.price_delivery_01, psq.price_delivery_02",FALSE);
        } else {
            $this->datatables->select($this->db->dbprefix('products').".id as pid, ".$this->db->dbprefix('products').".image as image, ".$this->db->dbprefix('products').".code as code, ".$this->db->dbprefix('products').".name as pname, type, ".$this->db->dbprefix('categories').".name as cname, psq.quantity, tax, tax_method, (CASE WHEN psq.price > 0 THEN psq.price ELSE {$this->db->dbprefix('products')}.price END) as price, barcode_symbology, psq.price_delivery_01, psq.price_delivery_02", FALSE);
        }

        $this->datatables->from('products')
        ->join('categories', 'categories.id=products.category_id', 'left')
        // ->join('product_store_qty', 'product_store_qty.product_id=products.id', 'left')
        ->join("( SELECT * from {$this->db->dbprefix('product_store_qty')} WHERE store_id = {$store_id}) psq", 'products.id=psq.product_id', 'left')
        // ->where('product_store_qty.store_id', $store_id)
        
        ->where($this->db->dbprefix('products').".category_id!=7");

        if(strlen($categoria)>0 && $categoria != "0"){
            $this->datatables->where("products.category_id",$categoria);
        }

        $this->datatables->group_by('products.id');

        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='".site_url('products/view/$1')."' title='" . lang("view") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'><i class='fa fa-file-text-o'></i></a><a href='".site_url('products/single_barcode/$1')."' title='".lang('print_barcodes')."' class='tip btn btn-default btn-xs' data-toggle='ajax-modal'><i class='fa fa-print'></i></a> <a href='".site_url('products/single_label/$1')."' title='".lang('print_labels')."' class='tip btn btn-default btn-xs' data-toggle='ajax-modal'><i class='fa fa-print'></i></a> <a class='tip image btn btn-primary btn-xs' id='$4 ($3)' href='" . base_url('uploads/$2') . "' title='" . lang("view_image") . "'><i class='fa fa-picture-o'></i></a> <a href='" . site_url('products/modificar/$1') . "' title='modificar' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('products/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_product') . "')\" title='" . lang("delete_product") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "pid, image, code, pname, barcode_symbology");

        $this->datatables->unset_column('pid')->unset_column('barcode_symbology');
        
        //echo $this->db->get_compiled_select("tec_products");
        //die();

        echo $this->datatables->generate();

    }

    function view($id = NULL) {
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $product = $this->site->getProductByID($id);
        $this->data['product'] = $product;
        $this->data['category'] = $this->site->getCategoryByID($product->category_id);
        $this->data['combo_items'] = $product->type == 'combo' ? $this->products_model->getComboItemsByPID($id) : NULL;
        $this->load->view($this->theme.'products/view', $this->data);

    }

    function barcode($product_code = NULL) {
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }
        $data['product_details'] = $this->products_model->getProductByCode($product_code);
        $data['img'] = "<img src='" . base_url() . "index.php?products/gen_barcode&code={$product_code}' alt='{$product_code}' />";
        $this->load->view('barcode', $data);

    }

    function product_barcode($product_code = NULL, $bcs = 'code128', $height = 60) {
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }
        return $this->tec->barcode($product_code, $bcs, $height);
    }

    function gen_barcode($product_code = NULL, $bcs = 'code128', $height = 60, $text = 1) {
        return $this->tec->barcode($product_code, $bcs, $height, $text);
    }

    /*
    function print_barcodes() {
        $limit = 10;
        $this->load->helper('pagination');
        $page = $this->input->get('page');
        $total = $this->products_model->products_count();
        $info = ['page' => $page, 'total' => ceil($total/$limit)];
        $pagination = pagination('products/print_barcodes', $total, $limit, true);
        $products = $this->products_model->fetch_products($limit, (!empty($page) ? (($page-1)*$limit) : 0));
        $r = 1;
        $html = "";
        $html .= '<table class="table table-bordered table-centered mb0">
        <tbody><tr>';
        foreach ($products as $pr) {
            if ($r != 1) {
                $rw = (bool)($r & 1);
                $html .= $rw ? '</tr><tr>' : '';
            }
            $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $pr->name . '</strong><br>' . $this->product_barcode($pr->code, $pr->barcode_symbology, 60) . '<br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($pr->price) . '</span></td>';
            $r++;
        }
        $html .= '</tr></tbody>
        </table>';
        $this->data['links'] = $pagination;
        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_barcodes");
        $this->load->view($this->theme.'products/print_barcodes', $this->data);
    }*/

    function print_barcodes() {

        $eleccion   = $_POST["eleccion"];

        //$this->load->helper('pagination');

        if($eleccion == '2'){
            $nro_filas  = $_POST["cantidad"]*1;
            $nro_cols   = $_POST["cantidad_cols"]*1;
            $codigo     = $_POST["codigo"];
            $ancho      = $_POST["ancho"]*1; 
            $alto       = $_POST["alto"]*1;
            $margin_top = $_POST["margin_top"]*1; 

            //$products   = $this->db->query("select * from tec_products where id = ?",array($codigo))->result();
            $products   = $this->db->select('*')->get('tec_products')->result();

            $html       = "<div style=\"height:{$margin_top}px\"></div>";
            $html       .= '<table class="table table-bordered table-centered mb0">
            <tbody>';
            
            foreach ($products as $pr) {

                for($i=0; $i<$nro_filas; $i++){
                    $html .= "<tr>";
                    for($j=0; $j<$nro_cols; $j++){
                        
                        //$rutin = base_url("themes/default/assets/codigo_barras/barcode.php");
                        //die($rutin);
                        $direc = base_url("themes/default/assets/codigo_barras/barcode.php?text=" . $pr->code . "&size=50&codetype=". $pr->barcode_symbology ."&print=true");

                        $celda = '<td style="width:'.$ancho.'px; height:'.$alto.'px; border-style:solid; border-width:1px; border-color:rgb(220,220,220); text-align:center; font-size:14px;"><strong>' . 
                            substr($pr->name,0,19) . '</strong><br>' . "<img src=" . $direc . "\">" . '</td>'; 
                        // ($pr->code, $pr->barcode_symbology, 30) 
                        
                        $html .= $celda;
                    }
                    $html .= "</tr>";
                }
            }

            $html .= '</tbody>
            </table>';

            //$this->data['html'] = $html;
            //$this->load->view('products/print_barcodes_a', $this->data);

            $this->data['links'] = $pagination;
            $this->data['html'] = $html;
            $this->data['page_title'] = lang("print_barcodes");
            $this->load->view($this->theme.'products/print_barcodes', $this->data);

        }elseif($eleccion == '1'){ // Codigo individual
            $nro_filas  = $_POST["cantidad"]*1;
            $nro_cols   = $_POST["cantidad_cols"]*1;
            $codigo     = $_POST["codigo"];
            $ancho      = $_POST["ancho"]*1; 
            $alto       = $_POST["alto"]*1;
            $margin_top = $_POST["margin_top"]*1; 

            //$products   = $this->db->query("select * from tec_products where id = ?",array($codigo))->result();
            $products   = $this->db->select('*')->where('id',$codigo)->get('tec_products')->result();
            

            $html       = "<div style=\"height:{$margin_top}px\"></div>";
            $html       .= '<table class="table table-bordered table-centered mb0">
            <tbody>';
            
            foreach ($products as $pr) {

                for($i=0; $i<$nro_filas; $i++){
                    $html .= "<tr>";
                    for($j=0; $j<$nro_cols; $j++){
                        
                        //$rutin = base_url("themes/default/assets/codigo_barras/barcode.php");
                        //die($rutin);
                        $direc = base_url("themes/default/assets/codigo_barras/barcode.php?text=" . $pr->code . "&size=50&codetype=". $pr->barcode_symbology ."&print=true");

                        $celda = '<td style="width:'.$ancho.'px; height:'.$alto.'px; border-style:solid; border-width:1px; border-color:rgb(220,220,220); text-align:center; font-size:14px;"><strong>' . 
                            substr($pr->name,0,19) . '</strong><br>' . "<img src=" . $direc . "\">" . '</td>'; 
                        // ($pr->code, $pr->barcode_symbology, 30) 
                        
                        $html .= $celda;
                    }
                    $html .= "</tr>";
                }
            }

            $html .= '</tbody>
            </table>';

            //$this->data['html'] = $html;
            //$this->load->view('products/print_barcodes_a', $this->data);

            $this->data['links'] = $pagination;
            $this->data['html'] = $html;
            $this->data['page_title'] = lang("print_barcodes");
            $this->load->view($this->theme.'products/print_barcodes', $this->data);
        }       

    }


    function print_labels() {
        $limit = 10;
        $this->load->helper('pagination');
        $page = $this->input->get('page');
        $total = $this->products_model->products_count();
        $info = ['page' => $page, 'total' => ceil($total/$limit)];
        $pagination = pagination('products/print_labels', $total, $limit, true);
        $products = $this->products_model->fetch_products($limit, (!empty($page) ? (($page-1)*$limit) : 0));
        $html = "";
        foreach ($products as $pr) {
            $html .= '<div class="text-center labels break-after"><strong>' . $pr->name . '</strong><br>' . $this->product_barcode($pr->code, $pr->barcode_symbology, 25) . '<br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($pr->price) . '</span></div>';
        }
        $this->data['links'] = $pagination;
        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_labels");
        $this->load->view($this->theme.'products/print_labels', $this->data);
    }

    function single_barcode($product_id = NULL) {

        $product = $this->site->getProductByID($product_id);

        $html = "";
        $html .= '<table class="table table-bordered table-centered mb0">
        <tbody><tr>';
        if($product->quantity > 0) {
            for ($r = 1; $r <= $product->quantity; $r++) {
                if ($r != 1) {
                    $rw = (bool)($r & 1);
                    $html .= $rw ? '</tr><tr>' : '';
                }
                $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 60) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($product->price) . '</span></td>';
            }
        } else {
            for ($r = 1; $r <= 10; $r++) {
            if ($r != 1) {
                $rw = (bool)($r & 1);
                $html .= $rw ? '</tr><tr>' : '';
            }
            $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 60) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($product->price) . '</span></td>';
        }
        }
        $html .= '</tr></tbody>
        </table>';

        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_barcodes").' ('.$product->name.')';
        $this->load->view($this->theme . 'products/single_barcode', $this->data);
    }

    function single_label($product_id = NULL, $warehouse_id = NULL) {

        $product = $this->site->getProductByID($product_id);
        $html = "";
        if($product->quantity > 0) {
            for ($r = 1; $r <= $product->quantity; $r++) {
                $html .= '<div class="text-center labels"><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 25) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($product->price) . '</span></div>';
            }
        } else {
            for ($r = 1; $r <= 10; $r++) {
                $html .= '<div class="text-center labels"><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 25) . ' <br><span class="price">'.lang('price') .': ' .$this->Settings->currency_prefix. ' ' . $this->tec->formatMoney($product->price) . '</span></div>';
            }
        }
        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_labels").' ('.$product->name.')';
        $this->load->view($this->theme . 'products/single_label', $this->data);

    }

    function add() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('code', lang("product_code"), 'trim|is_unique[products.code]|min_length[2]|max_length[50]|required|alpha_numeric');
        $this->form_validation->set_rules('name', lang("product_name"), 'required');
        $this->form_validation->set_rules('category', lang("category"), 'required');
        //$this->form_validation->set_rules('price', lang("product_price"), 'required|is_numeric');
        if ($this->input->post('type') != 'service') {
            $this->form_validation->set_rules('cost', lang("product_cost"), 'required|is_numeric');
        }
        $this->form_validation->set_rules('product_tax', lang("product_tax"), 'required|is_numeric');
        $this->form_validation->set_rules('alert_quantity', lang("alert_quantity"), 'is_numeric');

        if ($this->form_validation->run() == true) {

            $data = array(
                'type' => $this->input->post('type'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'category_id' => $this->input->post('category'),
                //'price' => $this->input->post('price'),
                'cost' => $this->input->post('cost'),
                'tax' => $this->input->post('product_tax'),
                'tax_method' => $this->input->post('tax_method'),
                'alert_quantity' => $this->input->post('alert_quantity'),
                'details' => $this->input->post('details'),
                'barcode_symbology' => $this->input->post('barcode_symbology'),
                );

            if ($this->Settings->multi_store) {
                $stores = $this->site->getAllStores();
                foreach ($stores as $store) {
                    $store_quantities[] = array(
                        'store_id'          => $store->id,
                        'quantity'          => $this->input->post('quantity'.$store->id),
                        'price'             => $this->input->post('price'.$store->id),
                        'price_delivery_01' => $this->input->post('price_delivery_01'.$store->id),
                        'price_delivery_02' => $this->input->post('price_delivery_02'.$store->id)
                    );
                }

                // Creando la nueva matriz bidimensional de precios
                foreach ($stores as $store) {
                    if($store->id == 1 || $store->id == 2){

                        $cSql = "select * from tec_tipo_precios where activo='1' order by id";
                        $tipuis = $this->db->query($cSql);
                        foreach($tipuis->result() as $tipui){
                            $entes[] = array(
                                'price'         => $_POST["cas_{$store->id}_{$tipui->id}"],
                                'store_id'      => $store->id,
                                'tipo_id'       => $tipui->id
                            );
                        }
                    }
                }

            } else {
                $store_quantities[] = array(
                    'store_id' => 1,
                    'quantity' => $this->input->post('quantity'),
                    'price' => $this->input->post('price'),
                    );
            }

            if ($this->input->post('type') == 'combo') {
                $c = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                        $items[] = array(
                            'item_code' => $_POST['combo_item_code'][$r],
                            'quantity' => $_POST['combo_item_quantity'][$r]
                        );
                    }
                }
            } else {
                $items = array();
            }

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '800';
                $config['max_height'] = '800';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/add");
                }

                $photo = $this->upload->file_name;
                $data['image'] = $photo;

                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/' . $photo;
                $config['new_image'] = 'uploads/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 110;
                $config['height'] = 110;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    $this->session->set_flashdata('error', $this->image_lib->display_errors());
                    redirect("products/add");
                }

            }
            // $this->tec->print_arrays($data, $items);
        }

        //if($this->form_validation->run()){
        //    $cadu = print_r($store_quantities,true);
        //    echo str_replace("\n","<br>",$cadu);
        //    die();
        //}

        if ($this->form_validation->run() == true && $this->products_model->addProduct($data, $store_quantities, $items, $entes)) {

            $this->session->set_flashdata('message', lang("product_added"));
            $this->session->set_flashdata('message', lang("product_added_receta"));
            redirect('products');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stores'] = $this->site->getAllStores();
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['page_title'] = lang('add_product');

            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_product')));
            $meta = array('page_title' => lang('add_product'), 'bc' => $bc);
            $this->page_construct('products/add', $this->data, $meta);

        }
    }

    function edit($id = NULL) {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $pr_details = $this->site->getProductByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang("product_code"), 'is_unique[products.code]');
        }
        $this->form_validation->set_rules('code', lang("product_code"), 'trim|min_length[2]|max_length[50]|required|alpha_numeric');
        $this->form_validation->set_rules('name', lang("product_name"), 'required');
        $this->form_validation->set_rules('category', lang("category"), 'required');
        $this->form_validation->set_rules('price', lang("product_price"), 'required|is_numeric');
        $this->form_validation->set_rules('cost', lang("product_cost"), 'required|is_numeric');
        $this->form_validation->set_rules('product_tax', lang("product_tax"), 'required|is_numeric');
        $this->form_validation->set_rules('alert_quantity', lang("alert_quantity"), 'is_numeric');

        if ($this->form_validation->run() == true) {

            $data = array(
                'type' => $this->input->post('type'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'category_id' => $this->input->post('category'),
                'price' => $this->input->post('price'),
                'cost' => $this->input->post('cost'),
                'tax' => $this->input->post('product_tax'),
                'tax_method' => $this->input->post('tax_method'),
                'alert_quantity' => $this->input->post('alert_quantity'),
                'details' => $this->input->post('details'),
                'barcode_symbology' => $this->input->post('barcode_symbology'),
                );

            if ($this->Settings->multi_store) {
                $stores = $this->site->getAllStores();
                $nI = 0;
                foreach ($stores as $store) {
                    $nI++;
                    $cI = substr("0" . $nI,-2);

                    /*
                    $ar_ceneca = array(
                        'store_id' => $store->id,
                        'quantity' => $this->input->post('quantity'.$store->id),
                        'price' => $this->input->post('price'.$store->id)
                    );

                    // Agrega columnas de deliverys
                    $cSql = "select * from tec_deliverys";
                    $query = $this->db->query($cSql);

                    //print_r($_POST);

                    foreach($query->result() as $r){
                        $cads = substr("0".$r->id,-2);

                        $mejora = '$valore = $this->input->post(' . "'" . 'price_delivery_' . $cads . "_" . $store->id . "');";

                        //echo $mejora . "<br>";

                        eval($mejora);

                        $ar_ceneca['price_delivery_'.$cads.'_'.$store->id] = $valore;
                    }

                    $store_quantities[] = $ar_ceneca;
                    */

                    $cSql = "select * from tec_tipo_precios where activo='1' order by id";
                    $tipuis = $this->db->query($cSql);
                    foreach($tipuis->result() as $tipui){
                        $price = "";
                        $cad = "cas_" . $store->id . "_" . $tipui->id;
                        if( isset($_POST[$cad]) ){
                            $ar_store_precios[$store->id][$tipui->id] = $_POST[$cad];
                        }

                    }
                }

                //print_r($store_quantities);
                //die();
            } else {
                $store_quantities[] = array(
                    'store_id'      => 1,
                    'quantity'      => $this->input->post('quantity'),
                    'price'         => $this->input->post('price')
                    );

                $cSql = "select * from tec_deliverys order by id";
                $query = $this->db->query($cSql);
                foreach($query->result() as $r){
                    $nD = $r->id;
                    $cD = substr("0" . $nD,-2);

                    $manejo = '$valore = $sq->price_delivery_' . $cD . ';';
                    eval($manejo);
                    $ar_precio[$r->id]       = (is_null($valore) ? "" : $valore); 
                }
            }

            if ($this->input->post('type') == 'combo') {
                $c = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                        $items[] = array(
                            'item_code' => $_POST['combo_item_code'][$r],
                            'quantity' => $_POST['combo_item_quantity'][$r]
                        );
                    }
                }
            } else {
                $items = array();
            }

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '800';
                $config['max_height'] = '800';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/edit/" . $id);
                }

                $photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/' . $photo;
                $config['new_image'] = 'uploads/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 110;
                $config['height'] = 110;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    $this->session->set_flashdata('error', $this->image_lib->display_errors());
                    redirect("products/edit/" . $id);
                }

            } else {
                $photo = NULL;
            }

        }

        if ($this->form_validation->run() == true && $this->products_model->updateProduct($id, $data, $store_quantities, $items, $photo, $ar_store_precios)) {

            $this->session->set_flashdata('message', lang("product_updated"));
            redirect("products");

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            
            $product = $this->site->getProductByID($id);
            
            if($product->type == 'combo') {
                $combo_items = $this->products_model->getComboItemsByPID($id);
                foreach ($combo_items as $combo_item) {
                    $cpr = $this->site->getProductByID($combo_item->id);
                    $cpr->qty = $combo_item->qty;
                    $items[] = array('id' => $cpr->id, 'row' => $cpr);
                }
                $this->data['items'] = $items;
            }
            
            $this->data['product']          = $product;
            $this->data['stores']           = $this->site->getAllStores();
            $this->data['stores_quantities'] = $this->Settings->multi_store ? $this->products_model->getStoresQuantity($id) : $this->products_model->getStoreQuantity($id);
            $this->data['categories']       = $this->site->getAllCategories();
            $this->data['page_title']       = lang('edit_product');
            
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('edit_product')));
            $meta = array('page_title' => lang('edit_product'), 'bc' => $bc);
            $this->page_construct('products/edit', $this->data, $meta);

        }
    }

    function modificar($id = NULL) {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $pr_details = $this->site->getProductByID($id);
/*
        $this->form_validation->set_rules('code', lang("product_code"), 'trim|is_unique[products.code]|min_length[2]|max_length[50]|required|alpha_numeric');
        $this->form_validation->set_rules('name', lang("product_name"), 'required');
        $this->form_validation->set_rules('category', lang("category"), 'required');
        //$this->form_validation->set_rules('price', lang("product_price"), 'required|is_numeric');
        if ($this->input->post('type') != 'service') {
            $this->form_validation->set_rules('cost', lang("product_cost"), 'required|is_numeric');
        }
        $this->form_validation->set_rules('product_tax', lang("product_tax"), 'required|is_numeric');
        $this->form_validation->set_rules('alert_quantity', lang("alert_quantity"), 'is_numeric');

        if ($this->form_validation->run() == true) {

            $data = array(
                'type' => $this->input->post('type'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'category_id' => $this->input->post('category'),
                'price' => $this->input->post('price'),
                'cost' => $this->input->post('cost'),
                'tax' => $this->input->post('product_tax'),
                'tax_method' => $this->input->post('tax_method'),
                'alert_quantity' => $this->input->post('alert_quantity'),
                'details' => $this->input->post('details'),
                'barcode_symbology' => $this->input->post('barcode_symbology'),
                'unidad'=> $this->input->post('unidad')
                );

            if ($this->Settings->multi_store) {
                $stores = $this->site->getAllStores();
                $nI = 0;
                foreach ($stores as $store) {
                    $nI++;
                    $cI = substr("0" . $nI,-2);

                    $cSql = "select * from tec_tipo_precios where activo='1' order by id";
                    $tipuis = $this->db->query($cSql);
                    foreach($tipuis->result() as $tipui){
                        $price = "";
                        $cad = "cas_" . $store->id . "_" . $tipui->id;
                        if( isset($_POST[$cad]) ){
                            $ar_store_precios[$store->id][$tipui->id] = $_POST[$cad];
                        }

                    }
                }

            } else {
                $store_quantities[] = array(
                    'store_id'      => 1,
                    'quantity'      => $this->input->post('quantity'),
                    'price'         => $this->input->post('price')
                    );

                $cSql = "select * from tec_deliverys order by id";
                $query = $this->db->query($cSql);
                foreach($query->result() as $r){
                    $nD = $r->id;
                    $cD = substr("0" . $nD,-2);

                    $manejo = '$valore = $sq->price_delivery_' . $cD . ';';
                    eval($manejo);
                    $ar_precio[$r->id]       = (is_null($valore) ? "" : $valore); 
                }
            }

            if ($this->input->post('type') == 'combo') {
                $c = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                        $items[] = array(
                            'item_code' => $_POST['combo_item_code'][$r],
                            'quantity' => $_POST['combo_item_quantity'][$r]
                        );
                    }
                }
            } else {
                $items = array();
            }

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '800';
                $config['max_height'] = '800';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/edit/" . $id);
                }

                $photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/' . $photo;
                $config['new_image'] = 'uploads/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 110;
                $config['height'] = 110;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    $this->session->set_flashdata('error', $this->image_lib->display_errors());
                    redirect("products/edit/" . $id);
                }

            } else {
                $photo = NULL;
            }

        }

        if ($this->form_validation->run() == true && $this->products_model->updateProduct($id, $data, $store_quantities, $items, $photo, $ar_store_precios)) {

            $this->session->set_flashdata('message', lang("product_updated"));
            redirect("products");

        } else {
*/
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            
            $cSql = "select type, name, code, barcode_symbology, category_id, tax, tax_method, alert_quantity, unidad".
                " from tec_products where id = ?";

            //$cSql = "select * from tec_products where id = ? limit 1";

            $product = $this->db->query($cSql,array($id)); // ,//$this->site->getProductByID($id);

            foreach($product->result() as $r){
                $product = $r;
            }

            if($product->type == 'combo') {
                $combo_items = $this->products_model->getComboItemsByPID($id);
                foreach ($combo_items as $combo_item) {
                    $cpr = $this->site->getProductByID($combo_item->id);
                    $cpr->qty = $combo_item->qty;
                    $items[] = array('id' => $cpr->id, 'row' => $cpr);
                }
                $this->data['items'] = $items;
            }
            
            $this->data['id'] = $id;

            // Obteniendo sus precios de Tabla tec_product_store_entes
            $cSql = "select a.id, a.product_id, a.store_id, c.state, a.tipo_id, b.descrip descrip_tipo, a.quantity, a.price
                from tec_product_store_entes a
                left join tec_tipo_precios b on a.tipo_id = b.id
                left join tec_stores c on a.store_id = c.id
                where a.product_id = $id";
            
            $this->data['precios']          = $this->db->query($cSql)->result_array();

            $this->data['product']          = $product;
            
            $this->data['stores']           = $this->site->getAllStores();
            $this->data['stores_quantities'] = $this->Settings->multi_store ? $this->products_model->getStoresQuantity($id) : $this->products_model->getStoreQuantity($id);
            $this->data['categories']       = $this->site->getAllCategories();
            $this->data['page_title']       = lang('edit_product');
            $this->data['modo']             = "U";
            
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('edit_product')));
            $meta = array('page_title' => lang('edit_product'), 'bc' => $bc);
            $this->page_construct('products/agregar', $this->data, $meta);

//        }
    }

    function import() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang("disabled_in_demo"));
                redirect('pos');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '500';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/import");
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("uploads/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                array_shift($arrResult);

                $keys = array('code', 'name', 'cost', 'tax', 'price', 'category');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                    //echo "key: $key -> $value <br>";
                    //var_dump($value);
                }
                //die();

                if (sizeof($final) > 1001) {
                    $this->session->set_flashdata('error', lang("more_than_allowed"));
                    redirect("products/import");
                }

                foreach ($final as $csv_pr) {
                    if ($this->products_model->getProductByCode($csv_pr['code'])) {
                        $this->session->set_flashdata('error', lang("check_product_code") . " (" . $csv_pr['code'] . "). " . lang("code_already_exist"));
                        redirect("products/import");
                    }
                    if (!is_numeric($csv_pr['tax'])) {
                        $this->session->set_flashdata('error', lang("check_product_tax") . " (" . $csv_pr['tax'] . "). " . lang("tax_not_numeric"));
                        redirect("products/import");
                    }
                    if(! ($category = $this->site->getCategoryByCode($csv_pr['category']))) {
                        $this->session->set_flashdata('error', lang("check_category") . " (" . $csv_pr['category'] . "). " . lang("category_x_exist"));
                        redirect("products/import");
                    }
                    $data[] = array(
                        'type' => 'standard',
                        'code' => $csv_pr['code'],
                        'name' => $csv_pr['name'],
                        'cost' => $csv_pr['cost'],
                        'tax' => $csv_pr['tax'],
                        'price' => $csv_pr['price'],
                        'category_id' => $category->id
                    );
                }
                //print_r($data); die();
            }

        }

        if ($this->form_validation->run() == true && $this->products_model->add_products($data)) {

            $this->session->set_flashdata('message', lang("products_added"));
            redirect('products');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['page_title'] = lang('import_products');
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('import_products')));
            $meta = array('page_title' => lang('import_products'), 'bc' => $bc);
            $this->page_construct('products/import', $this->data, $meta);

        }
    }


    function delete($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        if ($this->products_model->deleteProduct($id)) {
            $this->session->set_flashdata('message', lang("product_deleted"));
            redirect('products');
        }else{
            $this->session->set_flashdata('error', lang("not_deleted_consistence"));
            redirect('products');
        }

    }

    function suggestions() {
         $term = $this->input->get('term', TRUE);

         $rows = $this->products_model->getProductNames($term);
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

    function print_inicial(){

        $this->data['page_title'] = "Impresion de Codigos de Barra";

        $this->data["query_codigos"] = $this->db->query("select id, code, name descrip from tec_products where category_id != 7 order by name");

        //die($this->theme.'products/print_inicial');
        //$this->load->view($this->theme.'products/print_inicial', $this->data);

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['page_title'] = "C&oacute;digos de barras";
        $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('import_products')));
        $meta = array('page_title' => $this->data['page_title'], 'bc' => $bc);

        $this->page_construct('products/print_inicial', $this->data, $meta);
    }

    function agregar() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        /*if(isset($_SESSION["modo"])){
            if(strlen($_SESSION["modo"])>0){
                $modo = $_SESSION["modo"];
            }else{
                $modo = "I";
            }
        }else{
            $modo = "I";
        }*/

        if(isset($_POST["modo"])){
            if(strlen($_POST["modo"])>0){
                $modo = $_POST["modo"];
            }else{
                $modo = "I";
            }
        }else{
            $modo = "I";
        }        
        
        //die("El modo es $modo");
        if ($modo == 'I'){
            $this->form_validation->set_rules('code', lang("product_code"), 'trim|is_unique[products.code]|min_length[2]|max_length[50]|required|alpha_numeric');    
        }else{
            $this->form_validation->set_rules('code', lang("product_code"), 'trim|min_length[2]|max_length[50]|required|alpha_numeric');
        }
        
        $this->form_validation->set_rules('name', lang("product_name"), 'required');
        $this->form_validation->set_rules('category', lang("category"), 'required');

        //if ($this->input->post('type') != 'service') {
        //    $this->form_validation->set_rules('cost', lang("product_cost"), 'required|is_numeric');
        //}

        $this->form_validation->set_rules('product_tax', lang("product_tax"), 'required|is_numeric');
        //$this->form_validation->set_rules('alert_quantity', lang("alert_quantity"), 'is_numeric');

        if ($this->form_validation->run() == true){

            //die("Pasa la validacion...");
            if($modo == "I"){


                $this->data['modo']       = "I";

                $data = array(
                    'type' => $this->input->post('type'),
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'category_id' => $this->input->post('category'),
                    'price' => 1,
                    'cost' => $this->input->post('cost'),
                    'tax' => $this->input->post('product_tax'),
                    'tax_method' => $this->input->post('tax_method'),
                    'unidad' => $this->input->post('unidad'),
                    'details' => $this->input->post('details'),
                    'barcode_symbology' => $this->input->post('barcode_symbology'),
                    'unidad' => $this->input->post('unidad'),
                );


                if ($this->Settings->multi_store) {

                    $stores = $this->site->getAllStores();
                    foreach ($stores as $store) {
                        $store_quantities[] = array(
                            'store_id'          => $store->id,
                            'quantity'          => $this->input->post('quantity'.$store->id),
                            'price'             => 1,
                        );
                    }

                    // Creando la nueva matriz bidimensional de precios
                    foreach ($stores as $store) {
                        if($store->id == 1 || $store->id == 2){

                            $cSql = "select * from tec_tipo_precios where activo='1' order by id";
                            $tipuis = $this->db->query($cSql);
                            foreach($tipuis->result() as $tipui){
                                $entes[] = array(
                                    'price'         => $_POST["cas_{$store->id}_{$tipui->id}"],
                                    'store_id'      => $store->id,
                                    'tipo_id'       => $tipui->id
                                );
                            }
                        }
                    }

                    if ($this->products_model->addProduct($data, $store_quantities, $items, $entes)) {
                        
                        $this->session->set_flashdata('message', lang("product_added"));
                        $this->session->set_flashdata('message', lang("product_added_receta"));
                        //die("sale bien");

                    } else {
                        
                        $this->session->set_flashdata("message", "Hubo un error al grabar los datos");
                        //die("Error al grabar");

                    }

                    redirect('products');

                }

            
            }else{ // ====================== UPDATE =============================

                $this->data['modo']       = "U";

                $data = array(
                    'type' => $this->input->post('type'),
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'category_id' => $this->input->post('category'),
                    'price' => 1,
                    'cost' => $this->input->post('cost'),
                    'tax' => $this->input->post('product_tax'),
                    'tax_method' => $this->input->post('tax_method'),
                    //'alert_quantity' => $this->input->post('alert_quantity'),
                    'details' => $this->input->post('details'),
                    'barcode_symbology' => $this->input->post('barcode_symbology'),
                    'unidad' => $this->input->post('unidad')
                );

                // Averiguando el id del producto
                $code = $this->input->post('code');
                //echo "select id from tec_products where code = $code";
                //die("");
                $qu = $this->db->query("select id from tec_products where code = '$code'");
                foreach($qu->result() as $r){
                    $id = $r->id;
                }

                //echo str_replace("\n","<br>",print_r($data,true));
                //die("");
                if ($this->Settings->multi_store) {
                    $stores = $this->site->getAllStores();
                    $nI = 0;
                    foreach ($stores as $store) {
                        $nI++;
                        $cI = substr("0" . $nI,-2);

                        $cSql = "select * from tec_tipo_precios where activo='1' order by id";
                        $tipuis = $this->db->query($cSql);
                        foreach($tipuis->result() as $tipui){
                            $price = "";
                            $cad = "cas_" . $store->id . "_" . $tipui->id;
                            if( isset($_POST[$cad]) ){
                                $ar_store_precios[$store->id][$tipui->id] = $_POST[$cad];
                            }

                        }
                    }

                }
                
                if ($this->input->post('type') == 'combo') {
                    $c = sizeof($_POST['combo_item_code']) - 1;
                    for ($r = 0; $r <= $c; $r++) {
                        if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                            $items[] = array(
                                'item_code' => $_POST['combo_item_code'][$r],
                                'quantity' => $_POST['combo_item_quantity'][$r]
                            );
                        }
                    }
                } else {
                    $items = array();
                }

                if ($_FILES['userfile']['size'] > 0) {

                    $this->load->library('upload');

                    $config['upload_path'] = 'uploads/';
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['max_size'] = '500';
                    $config['max_width'] = '800';
                    $config['max_height'] = '800';
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        redirect("products/edit/" . $id);
                    }

                    $photo = $this->upload->file_name;

                    $this->load->helper('file');
                    $this->load->library('image_lib');
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = 'uploads/' . $photo;
                    $config['new_image'] = 'uploads/thumbs/' . $photo;
                    $config['maintain_ratio'] = TRUE;
                    $config['width'] = 110;
                    $config['height'] = 110;

                    $this->image_lib->clear();
                    $this->image_lib->initialize($config);

                    if (!$this->image_lib->resize()) {
                        $this->session->set_flashdata('error', $this->image_lib->display_errors());
                        redirect("products/edit/" . $id);
                    }

                } else {
                    $photo = NULL;
                }

                if ($this->form_validation->run() == true && $this->products_model->updateProduct($id, $data, $store_quantities, $items, $photo, $ar_store_precios)) {

                    $this->session->set_flashdata('message', lang("product_updated"));
                    redirect("products");

                }

            }
        }else{
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stores'] = $this->site->getAllStores();
            $this->data['page_title'] = lang('add_product');
            
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_product')));
            $meta = array('page_title' => lang('add_product'), 'bc' => $bc);
            $this->page_construct('products/agregar', $this->data, $meta);
        }

    }

}
