<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'libraries/luecano/src/NumeroALetras.php';
use Luecano\NumeroALetras\NumeroALetras;
class Pos extends MY_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }
        $this->load->helper('pos');
        
        
        $this->load->model('pos_model_apisperu');
        $this->load->model('pos_model');

        $this->load->library('form_validation');
        $this->delivery = "";
        $this->Igv = 10;

    }

    function index($sid = NULL, $eid = NULL){
        
        if (!$this->Settings->multi_store){
            $this->session->set_userdata('store_id', 1);
        }
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect($this->Settings->multi_store ? 'stores' : 'welcome');
        }
        if( $this->input->get('hold') ) { $sid = $this->input->get('hold'); }
        if( $this->input->get('edit') ) { $eid = $this->input->get('edit'); }
        if( $this->input->post('eid') ) { $eid = $this->input->post('eid'); }
        if( $this->input->post('did') ) { $did = $this->input->post('did'); } else { $did = NULL; }
        
        if($eid){ //  && !$this->Admin
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'pos');
        }
        if (!$this->Settings->default_customer){
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            redirect('settings');
        }
        
        /*if (!$this->session->userdata('register_id')) {
            if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
                $register_data = array(
                    'register_id' => $register->id, 
                    'cash_in_hand' => $register->cash_in_hand, 
                    'register_open_time' => $register->date);
                $this->session->set_userdata($register_data);
            } else {
                $this->session->set_flashdata('error', lang('register_not_open'));
                redirect('pos/open_register');
            }
        }*/

        if(!$this->pos_model->apertura_caja_hoy($this->session->userdata('store_id'))){
            
            // Existe algun proximo registro anterior?
            $cSql = "select status from tec_registers where store_id = ? and date(date) < curdate() order by date desc limit 1";
            $query = $this->db->query($cSql,array($this->session->userdata('store_id')));
            $existe_ra = false;
            foreach($query->result() as $r){
                $status = $r->status;
                $existe_ra = true;
            }

            if($existe_ra){ // "Si existe el registro anterior"
                    
                    if($status == "close"){ // Esta cerrada?
                        $this->session->set_flashdata('warning', lang('register_not_open'));
                        redirect('pos/open_register');
                    }else{ // "no esta cerrada"
                        //die("por aqui voy");
                        $this->session->set_flashdata('warning', lang('register_not_open'));
                        redirect('pos/close_register2');
                    }
            }else{  // "No existe registro anterior"
                $this->session->set_flashdata('warning', lang('register_not_open'));
                redirect('pos/open_register');
            }
        }

        $suspend = $this->input->post('suspend') ? TRUE : FALSE;

        $this->form_validation->set_rules('customer', lang("customer"), 'trim|required');

        if ($this->form_validation->run() == true) {

            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";

            $date           = $eid ? $this->input->post('date') : date('Y-m-d H:i:s');
            $customer_id    = $this->input->post('customer_id');
            $customer_details = $this->pos_model->getCustomerByID($customer_id);
            $customer       = $customer_details->name;
            $note           = $this->tec->clear_tags($this->input->post('spos_note'));

            $total          = 0;
            $product_tax    = 0;
            $order_tax      = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage     = '%';
            $persona_delivery = $this->input->post('txt_persona_delivery');

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            $el_inicio = 0; // para conocer el total descontado (descuento global)
            
            for ($r = 0; $r < $i; $r++) {
                
                $item_id            = $_POST['product_id'][$r];
                $real_unit_price    = $this->tec->formatDecimal($_POST['real_unit_price'][$r]);
                $item_quantity      = $_POST['quantity'][$r];
                $item_comment       = $_POST['item_comment'][$r];
                $item_discount      = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : '0';

                if (isset($item_id) && isset($real_unit_price) && isset($item_quantity)) {
                    $product_details = $this->site->getProductByID($item_id);
                    if ($product_details) {
                        $product_name = $product_details->name;
                        $product_code = $product_details->code;
                        $product_cost = $product_details->cost;
                    } else {
                        $product_name = $_POST['product_name'][$r];
                        $product_code = $_POST['product_code'][$r];
                        $product_cost = 0;
                    }
                    
                    if (!$this->Settings->overselling) {
                        if ($product_details->type == 'standard') {
                            if ($product_details->quantity < $item_quantity) {
                                $this->session->set_flashdata('error', lang("quantity_low").' ('.
                                    lang('name').': '.$product_details->name.' | '.
                                    lang('ordered').': '.$item_quantity.' | '.
                                    lang('available').': '.$product_details->quantity.
                                    ')');
                                redirect("pos");
                            }
                        } elseif ($product_details->type == 'combo') {
                            $combo_items = $this->pos_model->getComboItemsByPID($product->id);
                            foreach ($combo_items as $combo_item) {
                                $cpr = $this->site->getProductByID($combo_item->id);
                                if ($cpr->quantity < $item_quantity) {
                                    $this->session->set_flashdata('error', lang("quantity_low").' ('.
                                        lang('name').': '.$cpr->name.' | '.
                                        lang('ordered').': '.$item_quantity.' x '.$combo_item->qty.' = '.$item_quantity*$combo_item->qty.' | '.
                                        lang('available').': '.$cpr->quantity.
                                        ') '.$product_details->name);
                                    redirect("pos");
                                }
                            }
                        }
                    }
                    $unit_price = $real_unit_price;

                    $pr_discount = 0;
                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = $this->tec->formatDecimal((($unit_price * (Float)($pds[0])) / 100), 4);
                        } else {
                            $pr_discount = $this->tec->formatDecimal($discount);
                        }
                    }
                    $unit_price = $this->tec->formatDecimal(($unit_price - $pr_discount), 4);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->tec->formatDecimal(($pr_discount * $item_quantity), 4);
                    $product_discount += $pr_item_discount;


                    $pr_item_tax = 0; $item_tax = 0; $tax = "";

                    if (isset($product_details->tax) && $product_details->tax != 0) {

                        if ($product_details && $product_details->tax_method == 1) {
                            $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / 100), 4);
                            $tax = $product_details->tax . "%";
                        } else {
                            $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / (100 + $product_details->tax)), 4);
                            $tax = $product_details->tax . "%";
                            $item_net_price -= $item_tax;
                        }

                        $pr_item_tax = $this->tec->formatDecimal(($item_tax * $item_quantity), 4);
                    }

                    $product_tax += $pr_item_tax;
                    
                    $subtotal = $this->tec->formatDecimal((($item_net_price * $item_quantity) + $pr_item_tax), 4);

                    $products[] = array(
                        'product_id'        => $item_id,
                        'quantity'          => $item_quantity,
                        'unit_price'        => $unit_price,
                        'net_unit_price'    => $item_net_price,
                        'discount'          => $item_discount,
                        'comment'           => $item_comment,
                        'item_discount'     => $pr_item_discount,
                        'tax'               => $tax,
                        'item_tax'          => $pr_item_tax,
                        'subtotal'          => $subtotal,
                        'real_unit_price'   => $real_unit_price,
                        'cost'              => $product_cost,
                        'product_code'      => $product_code,
                        'product_name'      => $product_name,
                        );


                    $total += $this->tec->formatDecimal(($item_net_price * $item_quantity), 4);

                    $el_inicio += $subtotal;

                }
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('order_discount')) {
                $order_discount_id = $this->input->post('order_discount');
                
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = $this->tec->formatDecimal(((($total + $product_tax) * (Float)($ods[0])) / 100), 4);
                } else {
                    $order_discount = $this->tec->formatDecimal($order_discount_id);
                }
            } else {
                $order_discount_id = NULL;
            }

            $total_discount = $this->tec->formatDecimal(($order_discount + $product_discount), 4);

            //echo "order_discount: " . $order_discount . "<br>";
            //echo "product_discount: " . $product_discount . "<br>";

            if($this->input->post('order_tax')) {
                $order_tax_id = $this->input->post('order_tax');
                $opos = strpos($order_tax_id, $percentage);
                if ($opos !== false) {
                    $ots = explode("%", $order_tax_id);
                    // $order_tax = $this->tec->formatDecimal(((($total + $product_tax - $order_discount) * (Float)($ots[0])) / 100), 4);
                    $order_tax =  $this->tec->formatDecimal($product_tax);
                } else {
                    $order_tax = $this->tec->formatDecimal($order_tax_id);
                }

            } else {
                $order_tax_id = NULL;
                $order_tax = 0;
            }
            
            // El cambio que haré aquí es que el total tax primero le resto el descuento (en caso de global) ****** by fmz
            $order_discount_id = $this->input->post('order_discount');
            $opos = strpos($order_discount_id, $percentage);
            if ($opos !== false){ // Existe % de descuento
                
                $mi_dscto = $ods[0] * 1 / 100;
                //echo "mi_dscto:" . $mi_dscto . "<br>";

                // (1)
                $grand_total = $this->tec->formatDecimal($el_inicio * (1 - $mi_dscto),4);
            
                // Hallando Monto Base sin Igv
                $base_sin_igv = $el_inicio/(1 + ($this->Igv/100));
                
                // (2) Hallando operaciones gravadas
                $total = $grand_total / (1 + ($this->Igv/100));

                // (3) Hallando el Igv
                $total_tax = $total * ($this->Igv/100);

                // (4) (Hallando el total inicial : total sin descuento)
                //$total = $grand_total / (1 + ($this->Igv/100));

                // (5) Hallando el famoso "descuento"
                //$total_discount = $el_inicio / (1 + ($this->Igv/100)) - $t;
                $total_discount = $base_sin_igv - $total; 

            }else{    
                $total_tax = $this->tec->formatDecimal(($product_tax), 4);
                $grand_total = $this->tec->formatDecimal(($total + $total_tax - $order_discount), 4);
            }
            
            $paid = $this->input->post('amount') ? $this->input->post('amount') : 0;
            if (!$eid) {
                $status = 'due';
                if ($grand_total > $paid && $paid > 0) {
                    $status = 'partial';
                } elseif ($grand_total <= $paid) {
                    $status = 'paid';
                }
            }
            $round_total = $this->tec->roundNumber($grand_total, $this->Settings->rounding);
            $rounding = $this->tec->formatDecimal(($round_total - $grand_total));
            if ($customer_details->id == 1 && $paid < $round_total) {
                $this->session->set_flashdata('error', lang('select_customer_for_due'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            // Grabando para despues usarlo en la impresion: 
            $this->data["tipoDoc"] = $this->input->post('tipoDoc');

            if(strlen($this->input->post('tipoDoc'))==0){
                $this->session->set_flashdata('error', "Tipo de Documento esta vacio...");
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $serie = $this->pos_model->nube_serie($this->data["tipoDoc"], $this->input->post('txt_tipoDocAfectado'), $this->session->userdata('store_id'));

            $valor_deliv = 0;
            
            if($this->input->post('delivery')=='3'){ // LaCasita o delivery_propio
                $valor_deliv = $this->input->post("valor_deliv");
            }

            $tipo_precio_id = $this->input->post('cual_delivery');

            $data = array('date'    => $date,
                'customer_id'       => $customer_id,
                'customer_name'     => $customer,
                'total'             => $this->tec->formatDecimal($total, 4),
                'product_discount'  => $this->tec->formatDecimal($product_discount, 4),
                'order_discount_id' => $order_discount_id,
                'order_discount'    => $order_discount,
                'total_discount'    => $total_discount,
                'product_tax'       => $this->tec->formatDecimal($product_tax, 4),
                'order_tax_id'      => $order_tax_id,
                'order_tax'         => $order_tax,
                'total_tax'         => $total_tax,
                'grand_total'       => $grand_total,
                'total_items'       => $this->input->post('total_items'),
                'total_quantity'    => $this->input->post('total_quantity'),
                'rounding'          => $rounding,
                'paid'              => $paid,
                'status'            => $status,
                'created_by'        => $this->session->userdata('user_id'),
                'note'              => $note,
                'hold_ref'          => $this->input->post('hold_ref'),
                'tipoDoc'           => $this->input->post('tipoDoc'),
                'serie'             => $serie,
                'delivery_propio'   => $valor_deliv,
                'persona_delivery'  => $persona_delivery,
                'tipo_precio_id' 	=> $tipo_precio_id
            );

            if (!$eid) {
                $data['store_id'] = $this->session->userdata('store_id');
            }

            if (!$eid && !$suspend && $paid) {
                if ($this->input->post('paying_gift_card_no')) {
                    $gc = $this->pos_model->getGiftCardByNO($this->input->post('paying_gift_card_no'));
                    if (!$gc || $gc->balance < $amount) {
                        $this->session->set_flashdata('error', lang("incorrect_gift_card"));
                        redirect("pos");
                    }
                }
                $amount = $this->tec->formatDecimal(($paid > $grand_total ? ($paid - $this->input->post('balance_amount')) : $paid), 4);
                
                //die("la forma de pago:" . $esto);

                /*
                $payment = array(
                    'date' => $date,
                    'amount' => $amount,
                    'customer_id' => $customer_id,
                    'paid_by' => $this->input->post('paid_by'),
                    'cheque_no' => $this->input->post('cheque_no'),
                    'cc_no' => $this->input->post('cc_no'),
                    'gc_no' => $this->input->post('paying_gift_card_no'),
                    'cc_holder' => $this->input->post('cc_holder'),
                    'cc_month' => $this->input->post('cc_month'),
                    'cc_year' => $this->input->post('cc_year'),
                    'cc_type' => $this->input->post('cc_type'),
                    'cc_cvv2' => $this->input->post('cc_cvv2'),
                    'created_by' => $this->session->userdata('user_id'),
                    'store_id' => $this->session->userdata('store_id'),
                    'note' => strtoupper($this->input->post('payment_note')),
                    'pos_paid' => $this->tec->formatDecimal($this->input->post('amount'), 4),
                    'pos_balance' => $this->tec->formatDecimal($this->input->post('balance_amount'), 4)
                    );
                */

                if($this->input->post('paid_by')){
                    $payment[0] = array(
                        'date'          => $date,
                        'customer_id'   => $customer_id,
                        'created_by'    => $this->session->userdata('user_id'),
                        'store_id'      => $this->session->userdata('store_id'),
                        'note'          => strtoupper($this->input->post('payment_note')),
                        'amount'        => $this->input->post('txt_monto_paid_by'),
                        'paid_by'       => $this->input->post('paid_by')
                    );
                }
                
                // paid_by_val txt_monto_paid_by txt_paid_by2 txt_monto_paid_by2

                if(strlen($this->input->post('txt_paid_by2'))>0){

                    $payment[1] = array(
                        'date'          => $date,
                        'customer_id'   => $customer_id,
                        'created_by'    => $this->session->userdata('user_id'),
                        'store_id'      => $this->session->userdata('store_id'),
                        'note'          => strtoupper($this->input->post('payment_note')),
                        'amount'        => $this->input->post('txt_monto_paid_by2'),
                        'paid_by'       => $this->input->post('txt_paid_by2')
                    );

                    //$this->fm->traza(print_r($payment[1],true));
                }

                //print_r($payment);
                //die();
                $data['paid'] = $amount;

            } else {
                $payment = array();
            }

            // $this->tec->print_arrays($data, $products, $payment);
        }

        if ($this->form_validation->run() == true && !empty($products))
        {
            if ($suspend) {
                unset($data['status'], $data['rounding'], $data['tipoDoc']);
        
                if ($this->pos_model_apisperu->suspendSale($data, $products, $did)) {
                    $this->session->set_userdata('rmspos', 1);
                    $this->session->set_flashdata('message', lang("sale_saved_to_opened_bill"));
                    redirect("pos");
                } else {
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos/".$did);
                }

            } elseif($eid) {

                unset($data['status'], $data['paid']);
                if (!$this->Admin) {
                    unset($data['date']);
                }
                $data['updated_at'] = date('Y-m-d H:i:s');
                $data['updated_by'] = $this->session->userdata('user_id');
        
                if($this->pos_model_apisperu->updateSale($eid, $data, $products)) {
                    $this->session->set_userdata('rmspos', 1);
                    $this->session->set_flashdata('message', lang("sale_updated"));
                    redirect("sales");
                }
                else {
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos/?edit=".$eid);
                }

            } else {

                /* UNA VEZ GRABADA SE INICIA EL FORMATO DE IMPRESION */
                
                $data["tipoDocAfectado"]    = $this->input->post("txt_tipoDocAfectado");
                $data["serieDocfectado"]    = $this->input->post("txt_serieDocfectado");
                $data["numDocfectado"]      = $this->input->post("txt_numDocfectado");
                $data["codMotivo"]          = $this->input->post("txt_codMotivo");
                $data["desMotivo"]          = $this->input->post("txt_desMotivo");
                $data["correlativo"]        = $this->input->post("correlativo");


                if($sale = $this->pos_model_apisperu->addSale($data, $products, $payment, $did)){
                    
                    $this->session->set_userdata('rmspos', 1);
                    if($this->pos_model_apisperu->get_enviada_sunat($sale["sale_id"])){
                        $msg = "Venta enviada con Exito (Aceptada)";    
                    }else{
                        $msg = "Venta agregada con Exito (sin)";
                    }
                    
                    if (!empty($sale['message'])) {
                        foreach ($sale['message'] as $m) {
                            $msg .= '<br>' . $m;
                        }
                        //$msg .= $sale['message'];
                    }
                    
                    $this->session->set_flashdata('message', $msg);
                    
                    $redirect_to = $this->Settings->after_sale_page ? "pos" : "pos/view/" . $sale['sale_id'];
                    
                    if ($this->Settings->auto_print){
                        
                        if ( ! $this->Settings->remote_printing) {
                            
                            $this->print_receipt($sale['sale_id'], true);
                            
                        } elseif ($this->Settings->remote_printing == 2) {
                            $redirect_to .= '?print='.$sale['sale_id'];
                            
                        }
                    }
                    
                    redirect($redirect_to);
                }else{
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos");
                }

            }
        }
        else
        {
            // Esto se carga al Inicio ------------------
            
            if(isset($sid) && !empty($sid)) {
                $suspended_sale     = $this->pos_model_apisperu->getSuspendedSaleByID($sid);
                $inv_items          = $this->pos_model_apisperu->getSuspendedSaleItems($sid);
                krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                        $row->id = 0;
                        $row->code = $item->product_code;
                        $row->name = $item->product_name;
                        $row->tax = 0;
                    }
                    $row->price             = $item->net_unit_price+($item->item_discount/$item->quantity);
                    $row->unit_price        = $item->unit_price+($item->item_discount/$item->quantity)+($item->item_tax/$item->quantity);
                    $row->real_unit_price   = $item->real_unit_price;
                    $row->discount          = $item->discount;
                    $row->qty               = $item->quantity;
                    $row->comment           = $item->comment;
                    $row->ordered           = $item->quantity;
                    $combo_items            = FALSE;
                    $ri             = $this->Settings->item_addition ? $row->id : $c;
                    $pr[$ri]        = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
                    $c++;
                }
                $this->data['items'] = json_encode($pr);
                $this->data['sid'] = $sid;
                $this->data['suspend_sale'] = $suspended_sale;
                $this->data['message'] = lang('suspended_sale_loaded');
            }

            // SOLO VEO QUE GUARDA ESTA INFORMACION PARA PASARLO A LA VISTA ------------------
            if(isset($eid) && !empty($eid)) {
                $sale       = $this->pos_model->getSaleByID($eid);
                $inv_items  = $this->pos_model->getAllSaleItems($eid);
                krsort($inv_items);
                $c          = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                    }
                    $row->price         = $item->net_unit_price;
                    $row->unit_price    = $item->unit_price;
                    $row->real_unit_price = $item->real_unit_price;
                    $row->discount      = $item->discount;
                    $row->qty           = $item->quantity;
                    $row->comment       = $item->comment;
                    $combo_items        = FALSE;
                    $row->quantity      += $item->quantity;
                    if ($row->type == 'combo') {
                        $combo_items    = $this->pos_model->getComboItemsByPID($row->id);
                        foreach ($combo_items as $combo_item) {
                            $combo_item->quantity += ($combo_item->qty*$item->quantity);
                        }
                    }
                    $ri             = $this->Settings->item_addition ? $row->id : $c;
                    $pr[$ri]        = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
                    $c++;
                }
                $this->data['items']    = json_encode($pr);
                $this->data['eid']      = $eid;
                $this->data['sale']     = $sale;
                $this->data['message']  = lang('sale_loaded');
            }
            
            // Verificar el status de la ultima caja
            $sss = $this->pos_model->cash($this->session->userdata('user_id'));

            foreach($sss as $r){
                $this->data['ultimo_status_caja'] = $r->status;
            }

            $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['reference_note'] = isset($sid) && !empty($sid) ? $suspended_sale->hold_ref : (isset($eid) && !empty($eid) ? $sale->hold_ref : NULL);
            $this->data['sid']          = isset($sid) && !empty($sid) ? $sid : 0;
            $this->data['eid']          = isset($eid) && !empty($eid) ? $eid : 0;
            $this->data['customers']    = $this->site->getAllCustomers();
            $this->data["tcp"]          = $this->pos_model->products_count($this->Settings->default_category);
            $this->data['products']     = $this->ajaxproducts($this->Settings->default_category, 1);
            $this->data['categories']   = $this->site->getAllCategories();
            $this->data['message']      = $this->session->flashdata('message');
            $this->data['suspended_sales'] = $this->site->getUserSuspenedSales();

            $this->data['printer']      = $this->site->getPrinterByID($this->Settings->printer);
            $printers                   = array();
            if (!empty($order_printers  = json_decode($this->Settings->order_printers))) {
                foreach ($order_printers as $printer_id) {
                    $printers[] = $this->site->getPrinterByID($printer_id);
                }
            }
            $this->data['order_printers'] = $printers;

            if ($saleid = $this->input->get('print', true)) {
                if ($inv = $this->pos_model->getSaleByID($saleid)) {
                    if ($this->session->userdata('store_id') != $inv->store_id) {
                        $this->session->set_flashdata('error', lang('access_denied'));
                        redirect('pos');
                    }
                    $this->tec->view_rights($inv->created_by, false, 'pos');
                    $this->load->helper('text');
                    $this->data['rows'] = $this->pos_model->getAllSaleItems($saleid);
                    $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
                    $this->data['store'] = $this->site->getStoreByID($inv->store_id);
                    $this->data['inv'] = $inv;
                    $this->data['print'] = $saleid;
                    $this->data['payments'] = $this->pos_model->getAllSalePayments($saleid);
                    $this->data['created_by'] = $this->site->getUser($inv->created_by);
                }
            }

            $this->data['page_title'] = lang('pos');
            $bc = array(array('link' => '#', 'page' => lang('pos')));
            $meta = array('page_title' => lang('pos'), 'bc' => $bc);
            $this->load->view($this->theme.'pos/index', $this->data, $meta);
        }
    }
    
    /*
    public function enviar_anulacion_nubefact(){
        $id = $_GET["id"];
        
        // Se verifica si es Boleta o factura o Ticket
        $tipoDoc = $this->db->select("tipoDoc")->from("tec_sales")->where("id",$id)->get()->row()->tipoDoc;
        
        if($tipoDoc == 'Boleta' || $tipoDoc == 'Factura'){
            $this->data["respuesta"] = $this->pos_model->enviar_anulacion_nubefact($id);
        }else{
            if($this->pos_model->anular_documento_simple($id)){
                $this->data["respuesta"] = "Se anula documento simple con ID:{$id}";
            }
        }
        
        echo $this->data["respuesta"];
    }*/

    public function envio_masivo_individual_index(){
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('open_registers')));
        $meta = array('page_title' => lang('open_registers'), 'bc' => $bc);
        $this->page_construct('pos/envio_masivo_individual_index', $this->data, $meta);        
    }

    public function envio_masivo_individual(){
        $dia     = $_REQUEST["dia"];
        $cSql   = "select id, grand_total, paid from tec_sales where envio_electronico = 0 and grand_total = paid and date(date) = ?";
        $query  = $this->db->query($cSql,array($dia));
        
        foreach($query->result() as $r){
            $this->pos_model->enviar_doc_sunat_nubefact_individual($r->id, true);
            sleep(1);
        }
        echo "Termina proceso.";
    }

    public function consulta_recurrente(){
        $dia     = $_REQUEST["dia"];
        $cSql = "select count(*) cantidad from tec_sales where envio_electronico = 0 and grand_total = paid and date(date) = ?";
        $query  = $this->db->query($cSql,array($dia));
        foreach($query->result() as $r){
            echo "Faltan:" . $r->cantidad;
        }
    }

    function separar_delivery(){
        if(isset($_REQUEST["delivery"])){
            $_SESSION["delivery"] = $_REQUEST["delivery"];
            echo "Se separa el delivery Mr.";
        }
    }

/*
    function get_product($code = NULL,$delivery = NULL) {

        if(!is_null($delivery)){
            if ($this->input->get('code')){ 
                $code = $this->input->get('code'); 
            }
            $combo_items = FALSE;
            
            if($delivery * 1 == 3){ // Directo de Tienda
                $product = $this->pos_model->getProductByCode($code);
            }elseif(($delivery*1)>0){
                $product = $this->pos_model->getProductByCode2($code,$delivery);
            }
    
            if($product) {
                unset($product->cost, $product->details);
                $product->qty = 1;
                $product->comment = '';
                $product->discount = '0';
                $product->price             = $product->store_price > 0 ? $product->store_price : $product->price;
                $product->real_unit_price   = $product->price;
                $product->unit_price        = $product->tax ? ($product->price+(($product->price*$product->tax)/100)) : $product->price;
                
                if ($product->type == 'combo') {
                    $combo_items = $this->pos_model->getComboItemsByPID($product->id);
                }
                echo json_encode(array(
                    'id' => str_replace(".", "", microtime(true)), 
                    'item_id' => $product->id, 
                    'label' => $product->name . " (" . $product->code . ")", 
                    'row' => $product, 
                    'combo_items' => $combo_items));
            } else {
                echo NULL;
            }
        }else{
            echo NULL;
        }
    }
*/

    function get_product($code = NULL,$tipo = NULL) {

        $store_id = $this->session->userdata('store_id');

        if(!is_null($tipo)){
            //if ($this->input->get('code')){ 
            //    $code = $this->input->get('code'); 
            //}
            
            $combo_items = FALSE;

            $cSql = "select a.store_id, a.quantity, a.price/1.10 price, a.price store_price, a.product_id, b.code, b.name, b.category_id, b.tax, b.cost, b.tax_method, b.alert_quantity, b.unidad, b.inventariable, b.rubro, b.details, b.`type` 
                from tec_product_store_entes a
                inner join tec_products b on a.product_id=b.id
                where b.code = '$code' and a.store_id = $store_id and (a.tipo_id = '$tipo' or a.tipo_id = '1') order by a.tipo_id desc limit 1";
        
            //die($cSql);
            $query = $this->db->query($cSql);
            $precio = 0;
            foreach($query->result() as $r){
                $precio = $r->store_price * 1;
            }

            $product = $query->row();

            //$cSql = "SELECT a.*, COALESCE(PSQ.quantity, 0) as quantity, COALESCE(PSQ.price, a.price) as store_price
            //    FROM `tec_products` a
            //    LEFT JOIN ( SELECT product_id, quantity, coalesce({$campo},price) as price from tec_product_store_qty WHERE store_id = {$this->session->userdata('store_id')} ) AS PSQ ON `PSQ`.`product_id`=a.`id`
            //    where a.code = '{$code}'";

            //$q = $this->db->query($cSql);
        
            /*$product = $this->pos_model->getProductByCode2($code,$tipo)

            if($delivery * 1 == 3){ // Directo de Tienda
                $product = $this->pos_model->getProductByCode($code);
            }elseif(($delivery*1)>0){
                $product = $this->pos_model->getProductByCode2($code,$delivery);
            }*/
    
            if($product) {
                unset($product->cost, $product->details);
                $product->qty = 1;
                $product->comment = '';
                $product->discount = '0';
                
                //$product->price             = $product->store_price > 0 ? $product->store_price : $product->price;
                
                // codigo por fmz:
                $product->real_unit_price   = $product->store_price;
                //$product->unit_price        = $product->tax ? $product->store_price/(1+($product->tax/100)) : $product->store_price;
                
                // Codigo encontrado:
                $product->unit_price          = $product->tax ? ($product->price+(($product->price*$product->tax)/100)) : $product->price;
                //$product->unit_price        = $product->tax ? ($product->price+(($product->price*$product->tax)/100)) : $product->price;
                
                if ($product->type == 'combo') {
                    $combo_items = $this->pos_model->getComboItemsByPID($product->product_id);
                }
                echo json_encode(array(
                    'id' => str_replace(".", "", microtime(true)), 
                    'item_id' => $product->product_id, 
                    'label' => $product->name . " (" . $product->code . ")", 
                    'row' => $product, 
                    'combo_items' => $combo_items));
            } else {
                echo NULL;
            }
        }else{
            echo NULL;
        }
    }

/*
    function suggestions() {
        $term = $this->input->get('term', TRUE);

        $rows = $this->pos_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                unset($row->cost, $row->details);
                $row->qty = 1;
                $row->comment = '';
                $row->discount = '0';
                $row->price = $row->store_price > 0 ? $row->store_price : $row->price;
                $row->real_unit_price = $row->price;
                $row->unit_price = $row->tax ? ($row->price+(($row->price*$row->tax)/100)) : $row->price;
                $combo_items = FALSE;
                if ($row->type == 'combo') {
                    $combo_items = $this->pos_model->getComboItemsByPID($row->id);
                }
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }
*/

    function suggestions() {
        $tipo       = $_SESSION["tipo_id"];
        $store_id   = $_SESSION["store_id"]; //$_REQUEST["store_id"];
        
        $term = $this->input->get('term', TRUE);

        $rows = $this->pos_model->getProductNames($term);
        
        if ($rows) {
            foreach ($rows as $row) {
                unset($row->cost, $row->details);
                $row->qty = 1;
                $row->comment = '';
                $row->discount = '0';
                
                //$row2                   = $this->entes($row->id, $store_id, $tipo);
                $row2                   = $this->entes($row->id, $store_id, $tipo);
                $row->price             = $row2->price;
                $row->real_unit_price   = $row2->price;
                $row->unit_price        = $row2->price;
                //$row->price = $row->store_price > 0 ? $row->store_price : $row->price;
                //$row->real_unit_price = $row->price;
                //$row->unit_price = $row->tax ? ($row->price+(($row->price*$row->tax)/100)) : $row->price;
                $combo_items = FALSE;
                if ($row->type == 'combo') {
                    $combo_items = $this->pos_model->getComboItemsByPID($row->id);
                }
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function entes($product_id, $store_id, $tipo){
        //$cSql = "select * from tec_product_store_entes where product_id = $product_id and store_id = $store_id and tipo_id = $tipo";
        //$gn = fopen("trazu.txt","a+");
        //fputs($gn,$cSql."\n");
        //fclose($gn);

        $cSql = "select * from tec_product_store_entes where product_id = ? and store_id = ? and (tipo_id = ? or tipo_id=1) order by tipo_id desc limit 1";
        $row = $this->db->query($cSql, array($product_id, $store_id, $tipo))->row();
        return $row;
    }

    function registers() {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['registers'] = $this->pos_model->getOpenRegisters();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('open_registers')));
        $meta = array('page_title' => lang('open_registers'), 'bc' => $bc);
        $this->page_construct('pos/registers', $this->data, $meta);
    }

    function open_register() {
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        $this->form_validation->set_rules('cash_in_hand', lang("cash_in_hand"), 'trim|required|numeric');

        if ($this->form_validation->run() == true) {
            
            $cash_in_hand_adicional = 0;
            
            /*
            if(isset($_POST['cash_in_hand_adicional'])){
                if(!is_null($_POST['cash_in_hand_adicional'])){
                    $cash_in_hand_adicional = $_POST['cash_in_hand_adicional'];
                }
            }*/   

            $data = array('date'        => date('Y-m-d H:i:s'),
                'cash_in_hand'          => $this->input->post('cash_in_hand'),
                'cash_in_hand_adicional'=> $this->input->post('cash_in_hand_adicional'),
                'user_id'               => $this->session->userdata('user_id'),
                'store_id'              => $this->session->userdata('store_id'),
                'status'                => 'open'
            );
        }

        if ($this->form_validation->run() == true && $this->pos_model->openRegister($data)) {
            $this->session->set_flashdata('message', lang("welcome_to_pos"));
            redirect("pos");
        }else{
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['cash_in_hand'] = $this->pos_model->total_final_cash_anterior($this->session->userdata('store_id'));

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('open_register')));
            $meta = array('page_title' => lang('open_register'), 'bc' => $bc);
            $this->page_construct('pos/open_register', $this->data, $meta);
        }
    }

    function close_register2($user_id = NULL) {
        //if (!$this->Admin) {
            $user_id    = $this->session->userdata('user_id');
            $tienda     = $this->session->userdata('store_id');
            $this->data["user_id"] = $user_id;
            $this->data["tienda"] = $tienda;
        //}

        //$this->load->view($this->theme . 'pos/v_close_register2', $this->data);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('close_register')));
        $meta = array('page_title' => lang('cierre de Caja'), 'bc' => $bc);
        $this->page_construct('pos/v_close_register2', $this->data, $meta);
    }

    function close_register($user_id = NULL) {
        if (!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->form_validation->set_rules('total_cash', lang("total_cash"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cheques', lang("total_cheques"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cc_slips', lang("total_cc_slips"), 'trim|required|numeric');

        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : NULL;
                $rid = $user_register ? $user_register->id : $this->session->userdata('register_id');
                $user_id = $user_register ? $user_register->user_id : $this->session->userdata('user_id');
                $register_open_time = $user_register ? $user_register->date : $this->session->userdata('register_open_time');
                $cash_in_hand = $user_register ? $user_register->cash_in_hand : $this->session->userdata('cash_in_hand');
                $ccsales = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
                $cashsales = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
                $yapesales = $this->pos_model->getRegisterYapeSales($register_open_time, $user_id);
                $plinsales = $this->pos_model->getRegisterPlinSales($register_open_time, $user_id);
                $expenses = $this->pos_model->getRegisterExpenses($register_open_time, $user_id);
                $chsales = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
                $total_cash = ($cashsales->paid ? ($cashsales->paid + $cash_in_hand) : $cash_in_hand);
                $total_cash -= ($expenses->total ? $expenses->total : 0);
            } else {
                $rid = $this->session->userdata('register_id');
                $user_id = $this->session->userdata('user_id');
                $register_open_time = $this->session->userdata('register_open_time');
                $cash_in_hand = $this->session->userdata('cash_in_hand');
                $ccsales = $this->pos_model->getRegisterCCSales($register_open_time);
                $cashsales = $this->pos_model->getRegisterCashSales($register_open_time);
                $yapesales = $this->pos_model->getRegisterYapeSales($register_open_time);
                $plinsales = $this->pos_model->getRegisterPlinSales($register_open_time);
                $expenses = $this->pos_model->getRegisterExpenses($register_open_time);
                $chsales = $this->pos_model->getRegisterChSales($register_open_time);
                $total_cash = ($cashsales->paid ? ($cashsales->paid + $cash_in_hand) : $cash_in_hand);
                $total_cash -= ($expenses->total ? $expenses->total : 0);
            }

            $data = array('closed_at' => date('Y-m-d H:i:s'),
                'total_cash' => $total_cash,
                'total_cheques' => $chsales->total_cheques,
                'total_cc_slips' => $ccsales->total_cc_slips,
                'total_cash_submitted' => $this->input->post('total_cash_submitted'),
                'total_cheques_submitted' => $this->input->post('total_cheques_submitted'),
                'total_cc_slips_submitted' => $this->input->post('total_cc_slips_submitted'),
                'note' => $this->input->post('note'),
                'status' => 'close',
                'transfer_opened_bills' => $this->input->post('transfer_opened_bills'),
                'closed_by' => $this->session->userdata('user_id'),
                );

            // $this->tec->print_arrays($data);

        } elseif ($this->input->post('close_register')) {
            $this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
            redirect("pos");
        }

        if ($this->form_validation->run() == true && $this->pos_model->closeRegister($rid, $user_id, $data)) {
            $this->session->unset_userdata('register_id');
            $this->session->unset_userdata('cash_in_hand');
            $this->session->unset_userdata('register_open_time');
            $this->session->set_flashdata('message', lang("register_closed"));
            redirect("welcome");
        } else {
            if ($this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : NULL;
                $register_open_time = $user_register ? $user_register->date : $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = $user_register ? $user_register->cash_in_hand : NULL;
                $this->data['register_open_time'] = $user_register ? $register_open_time : NULL;
            } else {
                $register_open_time = $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = NULL;
                $this->data['register_open_time'] = NULL;
            }
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
            $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
            $this->data['yapesales'] = $this->pos_model->getRegisterYapeSales($register_open_time, $user_id);
            $this->data['plinsales'] = $this->pos_model->getRegisterPlinSales($register_open_time, $user_id);
            $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
            $this->data['other_sales'] = $this->pos_model->getRegisterOtherSales($register_open_time, $user_id);
            $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time, $user_id);
            $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time, $user_id);
            $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time, $user_id);
            $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);
            $this->data['users'] = $this->tec->getUsers($user_id);
            $this->data['suspended_bills'] = $this->pos_model->getSuspendedsales($user_id);
            $this->data['user_id'] = $user_id;
            $this->load->view($this->theme . 'pos/close_register', $this->data);
        }
    }

    function close_caja(){  // by fmz

        $dia        = $_GET["dia"];

        $cash_final = $_GET["cash_final"];
        $usuario    = $_GET["usuario"];
        $store_id   = $_GET["tienda"];
        $status     = "close";
        $total_tarjeta      = $_GET["total_tarjeta"];
        $total_transf       = $_GET["total_transf"];
        $total_yape         = $_GET["total_yape"];
        $total_plin         = $_GET["total_plin"];
        $total_deli1        = $_GET["total_deli1"];
        $total_deli2        = $_GET["total_deli2"];
        $total_personal     = $_GET["total_personal"];
        $monto_final_cash   = $_GET["monto_final_cash"];
        $total_total        = $_GET["total_total"];
        $nota               = (strlen($_GET["nota"]) > 0 ? $_GET["nota"] : "");

        $this->db->where('date(date)', $dia);
        $this->db->where('store_id', $store_id);

        $this->db->set("status", $status);
        $this->db->set("closed_at",date("Y-m-d H:i:s"));
        $this->db->set("closed_by",$usuario);
        $this->db->set("total_cash",$cash_final);
        $this->db->set("total_tarjeta",$total_tarjeta);
        $this->db->set("total_transf",$total_transf);
        $this->db->set("total_yape",$total_yape);
        $this->db->set("total_plin",$total_plin);
        $this->db->set("total_deli1",$total_deli1);
        $this->db->set("total_deli2",$total_deli2);
        $this->db->set("total_personal",$total_personal);
        $this->db->set("monto_final_cash",$monto_final_cash);
        $this->db->set("total_total",$total_total);
        $this->db->set("note",$nota);

        //echo $this->db->get_compiled_update('tec_registers');

        $this->db->update('tec_registers');

        $fecha_del_cierre = $dia;
        if(date("H") * 1 <= 5){
            $fecha_del_cierre = date('Y-m-d', strtotime($fecha_del_cierre .' -1 day'));
        }
        //$cSql       = $this->fm->query_salidas_por_dia($store_id, $fecha_del_cierre, $fecha_del_cierre);
        //$result     = $this->db->query($cSql)->result();

        //if($this->salidas_por_dia_correo($store_id, $fecha_del_cierre)){
        //    echo "Se cerró caja y envió correo automático.";
        //}else{
            echo "Se cerró caja";    
        //}
        
    }

    function ajaxproducts( $category_id = NULL, $return = NULL) {

        if($this->input->get('category_id')) { $category_id = $this->input->get('category_id'); } elseif(!$category_id) { $category_id = $this->Settings->default_category; }
        if($this->input->get('per_page') == 'n' ) { $page = 0; } else { $page = $this->input->get('per_page'); }
        if($this->input->get('tcp') == 1 ) { $tcp = TRUE; } else { $tcp = FALSE; }

        $products = $this->pos_model->fetch_products($category_id, $this->Settings->pro_limit, $page);
        $pro = 1;
        $prods = "<div>";
        if($products) {
            if($this->Settings->bsty == 1) {
                foreach($products as $product) {
                    $count = $product->id;
                    if($count < 10) { $count = "0".($count /100) *100;  }
                    if($category_id < 10) { $category_id = "0".($category_id /100) *100;  }
                    $prods .= "<button type=\"button\" data-name=\"".$product->name."\" id=\"product-".$category_id.$count."\" type=\"button\" value='".$product->code."' class=\"btn btn-name btn-default btn-flat product\">".$product->name."</button>";
                    $pro++;
                }
            } elseif($this->Settings->bsty == 2) {
                foreach($products as $product) {
                    $count = $product->id;
                    if($count < 10) { $count = "0".($count /100) *100;  }
                    if($category_id < 10) { $category_id = "0".($category_id /100) *100;  }
                    $prods .= "<button type=\"button\" data-name=\"".$product->name."\" id=\"product-".$category_id.$count."\" type=\"button\" value='".$product->code."' class=\"btn btn-img btn-flat product\"><img src=\"".base_url()."uploads/thumbs/".$product->image."\" alt=\"".$product->name."\" style=\"width: 110px; height: 110px;\"></button>";
                    $pro++;
                }
            } elseif($this->Settings->bsty == 3) {
                foreach($products as $product) {
                    $count = $product->id;
                    if($count < 10) { $count = "0".($count /100) *100;  }
                    if($category_id < 10) { $category_id = "0".($category_id /100) *100;  }
                    $prods .= "<button type=\"button\" data-name=\"".$product->name."\" id=\"product-".$category_id.$count."\" type=\"button\" value='".$product->code."' class=\"btn btn-both btn-flat product\"><span class=\"bg-img\"><img src=\"".base_url()."uploads/thumbs/".$product->image."\" alt=\"".$product->name."\" style=\"width: 100px; height: 100px;\"></span><span><span>".$product->name."</span></span></button>";
                    $pro++;
                }
            }
        } else {
            $prods .= '<h4 class="text-center text-info" style="margin-top:50px;">'.lang('category_is_empty').'</h4>';
        }

        $prods .= "</div>";

        if(!$return) {
            if(!$tcp) {
                echo $prods;
            } else {
                $category_products = $this->pos_model->products_count($category_id);
                header('Content-Type: application/json');
                echo json_encode(array('products' => $prods, 'tcp' => $category_products)); // $prods
            }
        } else {
            return $prods;
        }

    }

    function view($sale_id = NULL, $noprint = NULL) {
        $formatter = new NumeroALetras();

        if($this->input->get('id')){ $sale_id = $this->input->get('id'); }
        
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        
        $this->data['message'] = $this->session->flashdata('message');
        
        $inv = $this->pos_model->getSaleByID($sale_id);
        
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        } 
        /*elseif ($this->session->userdata('store_id') != $inv->store_id) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('welcome');
        }*/
        
        $this->tec->view_rights($inv->created_by);
        $this->load->helper('text');
        $this->data['format']       = $formatter;
        $this->data['rows']         = $this->pos_model->getAllSaleItems($sale_id);
        $this->data['customer']     = $this->pos_model->getCustomerByID($inv->customer_id);
        $this->data['store']        = $this->site->getStoreByID($inv->store_id);
        $this->data['inv']          = $inv;
        $this->data['sid']          = $sale_id;
        $this->data['noprint']      = $noprint;
        $this->data['modal']        = $noprint ? true : false;
        $this->data['payments']     = $this->pos_model->getAllSalePayments($sale_id);
        $this->data['created_by']   = $this->site->getUser($inv->created_by);
        $this->data['printer']      = $this->site->getPrinterByID($this->Settings->printer);
        $this->data['store']        = $this->site->getStoreByID($inv->store_id);
        $this->data['page_title']   = lang("invoice");
        $this->data['tipoDoc']      = $this->pos_model->getDatos($sale_id,'tipoDoc');

        if($this->data['tipoDoc'] == "Factura"){
            $this->data["nombre_docu"] = $this->pos_model->getDatos($sale_id,'cf2');
        }else{
            $this->data["nombre_docu"] = $this->pos_model->getDatos($sale_id,'cf1');
        }

        //$this->fm->traza(($this->Settings->print_img ? 'eview' : 'view'));

        $this->load->view($this->theme.'pos/'.($this->Settings->print_img ? 'eview' : 'view'), $this->data);

    }

    function email_receipt($sale_id = NULL, $to = NULL) {
        
        
        if($this->input->post('id')) { $sale_id = $this->input->post('id'); }
        if($this->input->post('email')){ $to = $this->input->post('email'); }
        if(!$sale_id || !$to) { die(); }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv = $this->pos_model->getSaleByID($sale_id);
        $this->tec->view_rights($inv->created_by);
        $this->load->helper('text');
        $this->data['rows'] = $this->pos_model->getAllSaleItems($sale_id);
        $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['noprint'] = NULL;
        $this->data['page_title'] = lang('invoice');
        $this->data['modal'] = false;
        $this->data['payments'] = $this->pos_model->getAllSalePayments($sale_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);

        $receipt = $this->load->view($this->theme.'pos/view_para_correo', $this->data, TRUE);
        $message = preg_replace('#\<!-- start -->(.+)\<!-- end -->#Usi', '', $receipt);
        $subject = lang('email_subject').' - '.$this->Settings->site_name;
        
        
        try {
            //if ($this->tec->send_email($to, $subject, $message)) {
            $to         = "flaviomorenoz@gmail.com";
            $subject    = "Comprobante LaCasita de las Salchipapas";
            $cuerpo     = $message; //"Este es el cuerpo del correo";

            if($this->envio_correo_generico($to, $subject, $cuerpo)){
                echo json_encode(array('msg' => lang("email_success")));
            } else {
                echo json_encode(array('msg' => lang("email_failed")));
            }
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }

    }

    function register_details() {

        $register_open_time = $this->session->userdata('register_open_time');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales']      = $this->pos_model->getRegisterCCSales($register_open_time);
        $this->data['yapesales']    = $this->pos_model->getRegisterYaSales($register_open_time);
        $this->data['plinsales']    = $this->pos_model->getRegisterPlSales($register_open_time);
        $this->data['cashsales']    = $this->pos_model->getRegisterCashSales($register_open_time);
        $this->data['chsales']      = $this->pos_model->getRegisterChSales($register_open_time);
        $this->data['other_sales']  = $this->pos_model->getRegisterOtherSales($register_open_time);
        $this->data['gcsales']      = $this->pos_model->getRegisterGCSales($register_open_time);
        $this->data['stripesales']  = $this->pos_model->getRegisterStripeSales($register_open_time);
        $this->data['totalsales']   = $this->pos_model->getRegisterSales($register_open_time);
        $this->data['expenses']     = $this->pos_model->getRegisterExpenses($register_open_time);
        $this->load->view($this->theme . 'pos/register_details', $this->data);
    }

    function agregar_monto_adicional(){
        //$this->load->view($this->theme . 'pos/register_details', $this->data);

        $tienda     = $_REQUEST["tienda"];
        $monto      = $_REQUEST["monto"];

        $cSql = "select id from tec_registers where store_id = {$tienda} and status != 'close' order by id desc limit 1";
        $query = $this->db->query($cSql);
        $existe = false;
        foreach($query->result() as $r){
            if ($r->id * 1 > 0){ 
                $existe = true;
                $id = $r->id;
            }
        }

        if($existe){
            $cSql = "update tec_registers set cash_in_hand_adicional = {$monto} where store_id={$tienda} and id={$id}";
            if($query = $this->db->query($cSql)){
                echo "Actualizacion correcta.";
            }else{
                echo "Existe el dia, mas no pudo actualizarse los datos...";
            }
        }else{
            echo "No se pudo grabar, Debe Iniciar Caja por primera vez!";
        }
    }

    function today_sale() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getTodayCCSales();
        $this->data['cashsales'] = $this->pos_model->getTodayCashSales();
        $this->data['chsales'] = $this->pos_model->getTodayChSales();
        $this->data['yapesales'] = $this->pos_model->getTodayYaSales();
        $this->data['plinsales'] = $this->pos_model->getTodayPlSales();
        $this->data['other_sales'] = $this->pos_model->getTodayOtherSales();
        $this->data['gcsales'] = $this->pos_model->getTodayGCSales();
        $this->data['stripesales'] = $this->pos_model->getTodayStripeSales();
        $this->data['totalsales'] = $this->pos_model->getTodaySales();
        // $this->data['expenses'] = $this->pos_model->getTodayExpenses();
        $this->load->view($this->theme . 'pos/today_sale', $this->data);
    }

    function shortcuts() {
        $this->load->view($this->theme . 'pos/shortcuts', $this->data);
    }

    function view_bill() {
        $this->load->view($this->theme . 'pos/view_bill', $this->data);
    }

    function promotions() {
        $this->load->view($this->theme . 'promotions', $this->data);
    }

    function stripe_balance() {
        if (!$this->Owner) {
            return FALSE;
        }
        $this->load->model('stripe_payments');
        return $this->stripe_payments->get_balance();
    }

    function language($lang = false) {
        if ($this->input->get('lang')) {
            $lang = $this->input->get('lang');
        }
        //$this->load->helper('cookie');
        $folder = 'app/language/';
        $languagefiles = scandir($folder);
        if (in_array($lang, $languagefiles)) {
            $cookie = array(
                'name' => 'language',
                'value' => $lang,
                'expire' => '31536000',
                'prefix' => 'spos_',
                'secure' => false
            );

            $this->input->set_cookie($cookie);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    function validate_gift_card($no) {
        if ($gc = $this->pos_model->getGiftCardByNO(urldecode($no))) {
            if ($gc->expiry) {
                if ($gc->expiry >= date('Y-m-d')) {
                    echo json_encode($gc);
                } else {
                    echo json_encode(false);
                }
            } else {
                echo json_encode($gc);
            }
        } else {
            echo json_encode(false);
        }
    }

    function print_register($re = NULL) {


        if ($this->session->userdata('register_id')) {

            $register = $this->pos_model->registerData();
            $ccsales = $this->pos_model->getRegisterCCSales();
            $cashsales = $this->pos_model->getRegisterCashSales();
            $chsales = $this->pos_model->getRegisterChSales();
            $other_sales = $this->pos_model->getRegisterOtherSales();
            $gcsales = $this->pos_model->getRegisterGCSales();
            $stripesales = $this->pos_model->getRegisterStripeSales();
            $totalsales = $this->pos_model->getRegisterSales();
            $expenses = $this->pos_model->getRegisterExpenses();
            $user = $this->site->getUser();

            $total_cash = $cashsales->paid ? ($cashsales->paid + $register->cash_in_hand) : $register->cash_in_hand;
            $total_cash -= ($expenses->total ? $expenses->total : 0);
            $info = array(
                (object) array('label' => lang('opened_at'), 'value' => $this->tec->hrld($register->date)),
                (object) array('label' => lang('cash_in_hand'), 'value' => $register->cash_in_hand),
                (object) array('label' => lang('user'), 'value' => $user->first_name.' '.$user->last_name.' ('.$user->email.')'),
                (object) array('label' => lang('printed_at'),  'value' => $this->tec->hrld(date('Y-m-d H:i:s')))
                );

            $reg_totals = array(
                (object) array('label' => lang('cash_sale'), 'value' => $this->tec->formatMoney($cashsales->paid ? $cashsales->paid : '0.00') . ' (' . $this->tec->formatMoney($cashsales->total ? $cashsales->total : '0.00') . ')'),
                (object) array('label' => lang('ch_sale'), 'value' => $this->tec->formatMoney($chsales->paid ? $chsales->paid : '0.00') . ' (' . $this->tec->formatMoney($chsales->total ? $chsales->total : '0.00') . ')'),
                (object) array('label' => lang('gc_sale'),  'value' => $this->tec->formatMoney($gcsales->paid ? $gcsales->paid : '0.00') . ' (' . $this->tec->formatMoney($gcsales->total ? $gcsales->total : '0.00') . ')'),
                (object) array('label' => lang('cc_sale'),  'value' => $this->tec->formatMoney($ccsales->paid ? $ccsales->paid : '0.00') . ' (' . $this->tec->formatMoney($ccsales->total ? $ccsales->total : '0.00') . ')'),
                (object) array('label' => lang('stripe'),  'value' => $this->tec->formatMoney($stripesales->paid ? $stripesales->paid : '0.00') . ' (' . $this->tec->formatMoney($stripesales->total ? $stripesales->total : '0.00') . ')'),
                (object) array('label' => lang('other_sale'),  'value' => $this->tec->formatMoney($other_sales->paid ? $other_sales->paid : '0.00') . ' (' . $this->tec->formatMoney($other_sales->total ? $other_sales->total : '0.00') . ')'),
                (object) array('label' => 'line',  'value' => ''),
                (object) array('label' => lang('total_sales'),  'value' => $this->tec->formatMoney($totalsales->paid ? $totalsales->paid : '0.00') . ' (' . $this->tec->formatMoney($totalsales->total ? $totalsales->total : '0.00') . ')'),
                (object) array('label' => lang('cash_in_hand'),  'value' => $this->tec->formatMoney($register->cash_in_hand)),
                (object) array('label' => lang('expenses'),  'value' => $this->tec->formatMoney($expenses->total ? $expenses->total : '0.00')),
                (object) array('label' => 'line',  'value' => ''),
                (object) array('label' => lang('total_cash'),  'value' => $this->tec->formatMoney($total_cash))
                );

            $data = (object) array(
                'printer' => $this->Settings->local_printers ? '' : json_encode($printer),
                'logo' => !empty($store->logo) ? base_url('uploads/'.$store->logo) : '',
                'heading' => lang('register_details'),
                'info' => $info,
                'totals' => $reg_totals
                );

            // $this->tec->print_arrays($data);
            if ($re == 1) {
                return $data;
            } elseif ($re == 2) {
                echo json_encode($data);
            } else {
                $printer = $this->site->getPrinterByID($this->Settings->printer);
                $this->load->library('escpos');
                $this->escpos->load($printer);
                $this->escpos->print_data($data);
                echo json_encode(true);
            }

        } else {
            echo json_encode(false);
        }
    }

    function print_receipt($id, $open_drawer = false) {

        $sale = $this->pos_model->getSaleByID($id);
        $items = $this->pos_model->getAllSaleItems($id);
        $payments = $this->pos_model->getAllSalePayments($id);
        $store = $this->site->getStoreByID($sale->store_id);
        $created_by = $this->site->getUser($sale->created_by);
        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->print_receipt($store, $sale, $items, $payments, $created_by, $open_drawer);

    }

    function receipt_img() {

        $data = $this->input->post('img', TRUE);
        $filename = date('Y-m-d-H-i-s-').uniqid().'.png';
        $cd = !empty($this->input->post('cd')) ? true : false;
        $imgData = str_replace(' ', '+', $data);
        $imgData = base64_decode($imgData);
        file_put_contents('files/receipts/'.$filename, $imgData);
        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->print_img($filename, $cd);

    }

    function open_drawer() {

        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->open_drawer();

    }

    function p($bo = 'order') {

        $date = date('Y-m-d H:i:s');
        $customer_id = $this->input->post('customer_id');
        $customer_details = $this->pos_model->getCustomerByID($customer_id);
        $customer = $customer_details->name;
        $note = $this->tec->clear_tags($this->input->post('spos_note'));

        $total = 0;
        $product_tax = 0;
        $order_tax = 0;
        $product_discount = 0;
        $order_discount = 0;
        $percentage = '%';
        $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
        for ($r = 0; $r < $i; $r++) {
            $item_id = $_POST['product_id'][$r];
            $real_unit_price = $this->tec->formatDecimal($_POST['real_unit_price'][$r]);
            $item_quantity = $_POST['quantity'][$r];
            $item_comment = $_POST['item_comment'][$r];
            $item_ordered = $_POST['item_was_ordered'][$r];
            $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : '0';

            if (isset($item_id) && isset($real_unit_price) && isset($item_quantity)) {
                $product_details = $this->site->getProductByID($item_id);
                if ($product_details) {
                    $product_name = $product_details->name;
                    $product_code = $product_details->code;
                    $product_cost = $product_details->cost;
                } else {
                    $product_name = $_POST['product_name'][$r];
                    $product_code = $_POST['product_code'][$r];
                    $product_cost = 0;
                }
                if (!$this->Settings->overselling) {
                    if ($product_details->type == 'standard') {
                        if ($product_details->quantity < $item_quantity) {
                            $this->session->set_flashdata('error', lang("quantity_low").' ('.
                                lang('name').': '.$product_details->name.' | '.
                                lang('ordered').': '.$item_quantity.' | '.
                                lang('available').': '.$product_details->quantity.
                                ')');
                            redirect("pos");
                        }
                    } elseif ($product_details->type == 'combo') {
                        $combo_items = $this->pos_model->getComboItemsByPID($product->id);
                        foreach ($combo_items as $combo_item) {
                            $cpr = $this->site->getProductByID($combo_item->id);
                            if ($cpr->quantity < $item_quantity) {
                                $this->session->set_flashdata('error', lang("quantity_low").' ('.
                                    lang('name').': '.$cpr->name.' | '.
                                    lang('ordered').': '.$item_quantity.' x '.$combo_item->qty.' = '.$item_quantity*$combo_item->qty.' | '.
                                    lang('available').': '.$cpr->quantity.
                                    ') '.$product_details->name);
                                redirect("pos");
                            }
                        }
                    }
                }
                $unit_price = $real_unit_price;

                $pr_discount = 0;
                if (isset($item_discount)) {
                    $discount = $item_discount;
                    $dpos = strpos($discount, $percentage);
                    if ($dpos !== false) {
                        $pds = explode("%", $discount);
                        $pr_discount = $this->tec->formatDecimal((($unit_price * (Float)($pds[0])) / 100), 4);
                    } else {
                        $pr_discount = $this->tec->formatDecimal($discount);
                    }
                }
                $unit_price = $this->tec->formatDecimal(($unit_price - $pr_discount), 4);
                $item_net_price = $unit_price;
                $pr_item_discount = $this->tec->formatDecimal(($pr_discount * $item_quantity), 4);
                $product_discount += $pr_item_discount;

                $pr_item_tax = 0; $item_tax = 0; $tax = "";
                if (isset($product_details->tax) && $product_details->tax != 0) {

                    if ($product_details && $product_details->tax_method == 1) {
                        $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / 100), 4);
                        $tax = $product_details->tax . "%";
                    } else {
                        $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / (100 + $product_details->tax)), 4);
                        $tax = $product_details->tax . "%";
                        $item_net_price -= $item_tax;
                    }

                    $pr_item_tax = $this->tec->formatDecimal(($item_tax * $item_quantity), 4);

                }

                $product_tax += $pr_item_tax;
                $subtotal = (($item_net_price * $item_quantity) + $pr_item_tax);

                $products[] = (object) array(
                    'product_id' => $item_id,
                    'quantity' => $item_quantity,
                    'unit_price' => $unit_price,
                    'net_unit_price' => $item_net_price,
                    'discount' => $item_discount,
                    'comment' => $item_comment,
                    'item_discount' => $pr_item_discount,
                    'tax' => $tax,
                    'item_tax' => $pr_item_tax,
                    'subtotal' => $subtotal,
                    'real_unit_price' => $real_unit_price,
                    'cost' => $product_cost,
                    'product_code' => $product_code,
                    'product_name' => $product_name,
                    'ordered' => $item_ordered,
                    );

                $total += $subtotal * $item_quantity;

            }
        }
        if (empty($products)) {
            $this->form_validation->set_rules('product', lang("order_items"), 'required');
        } else {
            krsort($products);
        }

        if ($this->input->post('order_discount')) {
            $order_discount_id = $this->input->post('order_discount');
            $opos = strpos($order_discount_id, $percentage);
            if ($opos !== false) {
                $ods = explode("%", $order_discount_id);
                $order_discount = $this->tec->formatDecimal(((($total + $product_tax) * (Float)($ods[0])) / 100), 4);
            } else {
                $order_discount = $this->tec->formatDecimal($order_discount_id);
            }
        } else {
            $order_discount_id = NULL;
        }
        $total_discount = $this->tec->formatDecimal(($order_discount + $product_discount), 4);

        if($this->input->post('order_tax')) {
            $order_tax_id = $this->input->post('order_tax');
            $opos = strpos($order_tax_id, $percentage);
            if ($opos !== false) {
                $ots = explode("%", $order_tax_id);
                // $order_tax = $this->tec->formatDecimal(((($total + $product_tax - $order_discount) * (Float)($ots[0])) / 100), 4);
                $order_tax = $this->tec->formatDecimal(($product_tax), 4);
            } else {
                $order_tax = $this->tec->formatDecimal($order_tax_id);
            }

        } else {
            $order_tax_id = NULL;
            $order_tax = 0;
        }

        // $total_tax = $this->tec->formatDecimal(($product_tax + $order_tax), 4);
        $total_tax = $this->tec->formatDecimal(($product_tax), 4);
        $grand_total = $this->tec->formatDecimal(($this->tec->formatDecimal($total) + $total_tax - $order_discount), 4);
        $paid = 0;
        $round_total = $this->tec->roundNumber($grand_total, $this->Settings->rounding);
        $rounding = $this->tec->formatDecimal(($round_total - $grand_total));

        $data = (object) array('date' => $date,
            'customer_id' => $customer_id,
            'customer_name' => $customer,
            'total' => $this->tec->formatDecimal($total),
            'product_discount' => $this->tec->formatDecimal($product_discount, 4),
            'order_discount_id' => $order_discount_id,
            'order_discount' => $order_discount,
            'total_discount' => $total_discount,
            'product_tax' => $this->tec->formatDecimal($product_tax, 4),
            'order_tax_id' => $order_tax_id,
            'order_tax' => $order_tax,
            'total_tax' => $total_tax,
            'grand_total' => $grand_total,
            'total_items' => $this->input->post('total_items'),
            'total_quantity' => $this->input->post('total_quantity'),
            'rounding' => $rounding,
            'paid' => $paid,
            'created_by' => $this->session->userdata('user_id'),
            'note' => $note,
            'hold_ref' => $this->input->post('hold_ref'),
            );

        // $this->tec->print_arrays($data, $products);
        $store = $this->site->getStoreByID($this->session->userdata('store_id'));
        $created_by = $this->site->getUser($this->session->userdata('user_id'));

        if ($bo == 'bill') {
            $printer = $this->site->getPrinterByID($this->Settings->printer);
            $this->load->library('escpos');
            $this->escpos->load($printer);
            $this->escpos->print_receipt($store, $data, $products, false, $created_by, false, true);
        } else {
            $order_printers = json_decode($this->Settings->order_printers);
            $this->load->library('escpos');
            foreach ($order_printers as $printer_id) {
                $printer = $this->site->getPrinterByID($printer_id);
                $this->escpos->load($printer);
                $this->escpos->print_order($store, $data, $products, $created_by);
            }
        }

    }

    function verifica_datos_cliente(){
        //ini_set("display_errors",1);
        //error_reporting(E_ALL ^ E_NOTICE);
        
        $message = "";
        //if ($this->pedir_token($message)){
            $tipoDoc    = $_REQUEST["tipoDoc"]; //$this->input->post("tipoDoc");
            $idCustomer = $_REQUEST["idCustomer"]; //$this->input->post("idCustomer");
            
            //$this->fm->traza($this->db->select("cf1, cf2")->from("customers")->where("id",$idCustomer)->get_compiled_select());
            
            $result = $this->db->select("cf1, cf2")
                ->from("customers")
                ->where("id",$idCustomer)
                ->get()->result();

            $n=0;
            foreach($result as $r){
                $cf1 = $r->cf1;
                $cf2 = $r->cf2;
                $n++;
            }
            if(strlen($tipoDoc)>0 && !is_null($tipoDoc)){
                if($n>0){
                    if($tipoDoc == 'Boleta'){
                        if(!is_null($cf1) and strlen($cf1)>0){
                            echo 1;
                        }else{
                            echo 0;
                        }
                    }elseif($tipoDoc == 'Factura'){
                        if(!is_null($cf2) and strlen($cf2)>0){
                            echo 1;
                        }else{
                            echo 0;
                        }
                    }elseif($tipoDoc == 'Ticket'){
                        echo 1;
                    }else{
                        echo 0;
                    }
                }else{
                    echo 0;
                }
            }else{
                echo 0;
            }
    }

    function pedir_token(&$message=""){
        
        $this->db->select("fec_reg");
        $this->db->where("name","TOKEN");
        $result = $this->db->get("variables")->result();

        foreach($result as $r){
            $fecha_ = $r->fec_reg;
        }
        if($fecha_ != date("Y-m-d")){

            $campos = array();
            $campos = array(
                "username"=> "flaviomorenoz",
                "password"=> "2468"
            );

            $jCampos = json_encode($campos); 

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, "https://facturacion.apisperu.com/api/v1/auth/login");

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 

            curl_setopt($curl, CURLOPT_POST, true);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $jCampos);

            curl_setopt($curl, CURLOPT_HTTPHEADER,
                array(
                    "content-type: application/json"
                )
            );

            $response = curl_exec($curl);

            curl_close($curl);

            // Verificando la respuesta
            $ar_rpta = json_decode($response);

            if(isset($ar_rpta->error)){
                $message = $ar_rpta->message;
                echo false;
            }else{
                $message = $ar_rpta->token;
                $datos = array();
                $datos = array("dato" => $message,"fec_reg"=>date("Y-m-d"));
                $this->db->where("name","TOKEN");
                $this->db->update("variables",$datos);

                echo true;
            }
        }else{
            echo true;
        }
    }

    function doc_sunat($sale_id, $data, $items){
        //ini_set("display_errors",1);
        //ini_set("error_reporting",E_ALL);
        $this->pos_model->enviar_doc_sunat($sale_id, $data, $items);
    }

    function obtener_valor_metodo_pago(){
        $cSql = "select * from tec_metodos_pago where id = {$_REQUEST["metodo_pago"]}";
        $query = $this->db->query($cSql);
        foreach($query->result() as $r){
            echo $r->valor;
            exit;
        }
    }

    function buscar_precios($id="", $tienda=""){
        $cSql = "select * from tec_product_store_qty where product_id = $id and store_id = $tienda";

        $query = $this->db->query($cSql);

        echo json_encode($query->result());
    }

    function cual_delivery($id, $delivery, $tienda=1){
        // funcion que retorna el precio del producto cuando es por delivery

        if($delivery * 1 == 1){
            //$campo = "price_rappi";
            $campo = "price_delivery_01";
        }elseif($delivery * 1 == 2){
            //$campo = "price_pedidosya";
            $campo = "price_delivery_02";
        }

        $cSql = "select $campo as precio from tec_product_store_qty where product_id = $id and store_id = $tienda";

        $query = $this->db->query($cSql);

        foreach($query->result() as $r){
            if(!is_null($r->precio)){
                return $r->precio;
            }
        }
        return 0;
    }

    function obtener_monto_caja(){
        $store_id = $_REQUEST["tienda"];
        echo json_encode($this->pos_model->cash($store_id));
    }
    
    function obtener_correlativo(){
        //$tipoDoc    = $_GET["tipoDoc"];
        $store_id   = $_GET["store_id"];
        $tipoDocAfectado = $_GET["tipoDocAfectado"];
        echo $this->pos_model->obtener_correlativo($store_id, $tipoDocAfectado);
    }

    function salidas_por_dia_correo($tienda, $fec_ini){
        
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = "<b>Cuadre de Caja</b>";
        
        $this->data['tienda']       = $tienda;
        $this->data['fec_ini']      = $fec_ini;

        $bc     = array(array('link' => '#', 'page' => lang('')));
        $meta   = array('page_title' => "<span style=\"color:rgb(60,120,190);font-weight:bold;\">Cuadre de Caja</span>", 'bc' => $bc);

        $this->data["cadena_query"]         = $this->fm->query_salidas_por_dia($this->data['tienda'], $this->data['fec_ini'], $this->data['fec_ini']);

        $this->data["cadena_query_ventas"]  = $this->fm->query_salidas_por_dia_ventas($this->data['tienda'], $this->data['fec_ini'], $this->data['fec_ini']);

        $rutin = "reports/salidas_por_dia_correo";
        
        $this->db->reset_query();
        
        $result = $this->db->select("state")->from("stores")->where("id",$this->data['tienda'])->get()->result();

        foreach($result as $r){ $this->data['tienda_descrip'] = $r->state; }

        $subject    = "Resumen Diario - Tienda {$this->data['tienda_descrip']} - {$this->data['fec_ini']}";

        $cad_salida = $this->load->view($this->theme . $rutin, $this->data, TRUE);

        if($this->correo_por_cierre_diario($subject, $cad_salida, $this->data['tienda'])){
            return true;
        }else{
            return false;
        }
    }

    function salidas_por_dia_prueba($tienda, $fec_ini){
        $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title']   = "<b>Cuadre de Caja</b>";
        
        $this->data['tienda']       = $tienda;
        $this->data['fec_ini']      = $fec_ini;

        $bc     = array(array('link' => '#', 'page' => lang('')));
        $meta   = array('page_title' => "<span style=\"color:rgb(60,120,190);font-weight:bold;\">Cuadre de Caja</span>", 'bc' => $bc);

        $this->data["cadena_query"] = $this->fm->query_salidas_por_dia($this->data['tienda'], $this->data['fec_ini'], $this->data['fec_ini']);
        $rutin = "reports/salidas_por_dia_correo";
        
        $this->db->reset_query();
        $result = $this->db->select("state")->from("stores")->where("id",$this->data['tienda'])->get()->result();

        foreach($result as $r){ $this->data['tienda_descrip'] = $r->state; }

        $subject    = "Resumen Diario - Tienda {$this->data['tienda_descrip']} - {$this->data['fec_ini']}";

        $this->load->view($this->theme . $rutin, $this->data);
    }

    function correo_por_cierre_diario($subject, $cuerpo="", $store_id){
    
        //$to         = "gerardo.guzman@qsystem.com.pe, ferdan.lopez01@gmail.com, flavio.moreno@qsystem.com.pe, pameliux-351@hotmail.com, judithcatherina@gmail.com, chiosalazar20@gmail.com";
        $to         = "flavio.moreno@qsystem.com.pe";
        
        //if($store_id*1 == 1){ $to .= ",carlosyari18@gmail.com";} // 
        //if($store_id*1 == 2){ $to .= ",pamelita062503@gmail.com";} // 

        //$from       = "flaviomorenoz@hotmail.com";
        //$subject    = "Cuadre de Caja "; 
        //$cuerpo     = "1854 soles peruanos";
        //$mensaje    = "Línea 1\r\nLínea 2\r\nLínea 3";
        //$mensaje    = wordwrap($mensaje, 70, "\r\n");

        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Enviarlo
        $success = mail($to, $subject, $cuerpo, $cabeceras);

        if(strlen($cuerpo)==0){ return false; }

        if (!$success){
            $errorMessage = error_get_last()['message'];
            //die($errorMessage);
            return false;
        }else{
            //die("Pasa la prueba");
            return  true;
        }
    }

    function envio_correo_generico($to, $subject, $cuerpo=""){
    
        $to         = "flavio.moreno@qsystem.com.pe";

        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Enviarlo
        $success = mail($to, $subject, $cuerpo, $cabeceras);

        if(strlen($cuerpo)==0){ return false; }

        if (!$success){
            $errorMessage = error_get_last()['message'];
            //die($errorMessage);
            return false;
        }else{
            //die("Pasa la prueba");
            return  true;
        }
    }

    function set_session(){
        $valor  = $_GET["valor"];
        $var1   = $_GET["var1"];
        $_SESSION[$var1] = $valor;
        echo $_SESSION[$var1];
    }

    function edit($sid = NULL){
        
        if (!$this->Settings->multi_store){
            $this->session->set_userdata('store_id', 1);
        }
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect($this->Settings->multi_store ? 'stores' : 'welcome');
        }

        $this->data['page_title'] = lang('pos');
        $bc = array(array('link' => '#', 'page' => lang('pos')));
        $meta = array('page_title' => lang('pos'), 'bc' => $bc);

        $this->load->view($this->theme . "pos/editar", $this->data);
        
    }

    function anular_doc(){
        $sale_id = $_REQUEST["id"];
        $this->db->where('id',$sale_id)->delete('sales');
        $this->db->where('sale_id',$sale_id)->delete('payments');
        $ar["rpta"] = "OK";
        $ar["mensaje"] = "Se elimina correctamente la Venta";
        echo json_encode($ar);
    }

    function index_apiperu(){
        if (!$this->Settings->multi_store){
            $this->session->set_userdata('store_id', 1);
        }
        if( $this->input->get('hold') ) { $sid = $this->input->get('hold'); }
        if( $this->input->get('edit') ) { $eid = $this->input->get('edit'); }
        if( $this->input->post('eid') ) { $eid = $this->input->post('eid'); }
        if( $this->input->post('did') ) { $did = $this->input->post('did'); } else { $did = NULL; }
        
        if($eid){ //  && !$this->Admin
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'pos');
        }
        if (!$this->Settings->default_customer){
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            redirect('settings');
        }
        
        if(!$this->pos_model->apertura_caja_hoy($this->session->userdata('store_id'))){
            
            // Existe algun proximo registro anterior?
            $cSql = "select status from tec_registers where store_id = ? and date(date) < curdate() order by date desc limit 1";
            $query = $this->db->query($cSql,array($this->session->userdata('store_id')));
            $existe_ra = false;
            foreach($query->result() as $r){
                $status = $r->status;
                $existe_ra = true;
            }

            if($existe_ra){ // "Si existe el registro anterior"
                    
                    if($status == "close"){ // Esta cerrada?
                        $this->session->set_flashdata('warning', lang('register_not_open'));
                        redirect('pos/open_register');
                    }else{ // "no esta cerrada"
                        //die("por aqui voy");
                        $this->session->set_flashdata('warning', lang('register_not_open'));
                        redirect('pos/close_register2');
                    }
            }else{  // "No existe registro anterior"
                $this->session->set_flashdata('warning', lang('register_not_open'));
                redirect('pos/open_register');
            }
        }

        $suspend = $this->input->post('suspend') ? TRUE : FALSE;

        $this->form_validation->set_rules('customer', lang("customer"), 'trim|required');

        if ($this->form_validation->run() == true) {

            $ar_cliente["date"]         = substr($eid ? $this->input->post('date') : date('Y-m-d H:i:s'),0,10);

            $ar_cliente["tipoDoc_"]     = $this->input->post('tipoDoc');

            $ar_cliente["serie"]        = "";

            $ar_cliente["correlativo"]  = "";

            $ar_cliente["fecha_emi"]    = $ar_cliente["date"];

            $ar_cliente["tip_forma"]    = "";

            $ar_cliente["tipoMoneda"]   = "PEN";

            $ar_cliente["tipoDoc_client"]   = "";

            $ar_cliente["numDoc"]       = "";

            $ar_cliente["rznSocial"]    = ""; // $this->input->post('customer_id'); // $customer_details->name;

            $ar_cliente["direccion_cliente"] = "";

            /*$campus1 = "{
                \"ublVersion\": \"2.1\",
              \"fecVencimiento\": \"" . $date . "-05:00\",
              \"tipoOperacion\": \"0101\", 
              \"tipoDoc\": \"{$tipoDoc_}\",
              \"serie\": \"$serie\",
              \"correlativo\": \"{$correlativo}\",
              \"fechaEmision\": \"" . $fecha_emi . "-05:00\",
              \"formaPago\": {
                \"moneda\": \"PEN\",
                \"tipo\": \"$tip_forma\"
              },
              \"tipoMoneda\": \"PEN\",
              \"client\": {
                \"tipoDoc\": \"{$tipoDoc_client}\",
                \"numDoc\": \"$numDoc\",
                \"rznSocial\": \"{$Cliente}\",
                \"address\": {
                  \"direccion\": \"{$direccion_cliente}\",
                  \"provincia\": \"LIMA\",
                  \"departamento\": \"LIMA\",
                  \"distrito\": \"LIMA\",
                  \"ubigueo\": \"150101\"
                }
            },";*/

            $ar_empresa = array();

            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";
            $ar_empresa["a"] = "";


            $campus4 = "";

            $campus3 = "";

        }
        else
        {
            // Esto se carga al Inicio ------------------
            
            if(isset($sid) && !empty($sid)) {
                $suspended_sale = $this->pos_model->getSuspendedSaleByID($sid);
                $inv_items = $this->pos_model->getSuspendedSaleItems($sid);
                krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                        $row->id = 0;
                        $row->code = $item->product_code;
                        $row->name = $item->product_name;
                        $row->tax = 0;
                    }
                    $row->price = $item->net_unit_price+($item->item_discount/$item->quantity);
                    $row->unit_price = $item->unit_price+($item->item_discount/$item->quantity)+($item->item_tax/$item->quantity);
                    $row->real_unit_price = $item->real_unit_price;
                    $row->discount = $item->discount;
                    $row->qty = $item->quantity;
                    $row->comment = $item->comment;
                    $row->ordered = $item->quantity;
                    $combo_items = FALSE;
                    $ri = $this->Settings->item_addition ? $row->id : $c;
                    $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
                    $c++;
                }
                $this->data['items'] = json_encode($pr);
                $this->data['sid'] = $sid;
                $this->data['suspend_sale'] = $suspended_sale;
                $this->data['message'] = lang('suspended_sale_loaded');
            }

            if(isset($eid) && !empty($eid)) {
                $sale = $this->pos_model->getSaleByID($eid);
                $inv_items = $this->pos_model->getAllSaleItems($eid);
                krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                    }
                    $row->price         = $item->net_unit_price;
                    $row->unit_price    = $item->unit_price;
                    $row->real_unit_price = $item->real_unit_price;
                    $row->discount      = $item->discount;
                    $row->qty           = $item->quantity;
                    $row->comment       = $item->comment;
                    $combo_items        = FALSE;
                    $row->quantity      += $item->quantity;
                    if ($row->type == 'combo') {
                        $combo_items    = $this->pos_model->getComboItemsByPID($row->id);
                        foreach ($combo_items as $combo_item) {
                            $combo_item->quantity += ($combo_item->qty*$item->quantity);
                        }
                    }
                    $ri = $this->Settings->item_addition ? $row->id : $c;
                    $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
                    $c++;
                }
                $this->data['items']    = json_encode($pr);
                $this->data['eid']      = $eid;
                $this->data['sale']     = $sale;
                $this->data['message']  = lang('sale_loaded');
            }
            
            // Verificar el status de la ultima casa
            $sss = $this->pos_model->cash($this->session->userdata('user_id'));

            foreach($sss as $r){
                $this->data['ultimo_status_caja'] = $r->status;
            }

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['reference_note'] = isset($sid) && !empty($sid) ? $suspended_sale->hold_ref : (isset($eid) && !empty($eid) ? $sale->hold_ref : NULL);
            $this->data['sid'] = isset($sid) && !empty($sid) ? $sid : 0;
            $this->data['eid'] = isset($eid) && !empty($eid) ? $eid : 0;
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data["tcp"] = $this->pos_model->products_count($this->Settings->default_category);
            $this->data['products'] = $this->ajaxproducts($this->Settings->default_category, 1);
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['message'] = $this->session->flashdata('message');
            $this->data['suspended_sales'] = $this->site->getUserSuspenedSales();

            $this->data['printer'] = $this->site->getPrinterByID($this->Settings->printer);
            $printers = array();
            if (!empty($order_printers = json_decode($this->Settings->order_printers))) {
                foreach ($order_printers as $printer_id) {
                    $printers[] = $this->site->getPrinterByID($printer_id);
                }
            }
            $this->data['order_printers'] = $printers;

            if ($saleid = $this->input->get('print', true)) {
                if ($inv = $this->pos_model->getSaleByID($saleid)) {
                    if ($this->session->userdata('store_id') != $inv->store_id) {
                        $this->session->set_flashdata('error', lang('access_denied'));
                        redirect('pos');
                    }
                    $this->tec->view_rights($inv->created_by, false, 'pos');
                    $this->load->helper('text');
                    $this->data['rows'] = $this->pos_model->getAllSaleItems($saleid);
                    $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
                    $this->data['store'] = $this->site->getStoreByID($inv->store_id);
                    $this->data['inv'] = $inv;
                    $this->data['print'] = $saleid;
                    $this->data['payments'] = $this->pos_model->getAllSalePayments($saleid);
                    $this->data['created_by'] = $this->site->getUser($inv->created_by);
                }
            }

            $this->data['page_title'] = lang('pos');
            $bc = array(array('link' => '#', 'page' => lang('pos')));
            $meta = array('page_title' => lang('pos'), 'bc' => $bc);
            $this->load->view($this->theme.'pos/index', $this->data, $meta);
        }

    }

}
