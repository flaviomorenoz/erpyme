<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Products_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getAllProducts() {
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function products_count($category_id = NULL) {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
            return $this->db->count_all_results("products");
        } else {
            return $this->db->count_all("products");
        }
    }

    public function fetch_products($limit, $start = null, $category_id = NULL) {
        $this->db->select('name, code, barcode_symbology, price')
        ->limit($limit, $start)->order_by("code", "asc");
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        $q = $this->db->get("products");

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductByCode($code) {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    /*
        public function addProduct($data, $store_quantities, $items = array()) {
            if ($this->db->insert('products', $data)) {
                $product_id = $this->db->insert_id();
                
                if(! empty($store_quantities)) {
                    foreach ($store_quantities as $store_quantity) {
                        $store_quantity['product_id'] = $product_id;
                        $this->db->insert('product_store_qty', $store_quantity);
                    }
                }
                if(! empty($items)) {
                    foreach ($items as $item) {
                        $item['product_id'] = $product_id;
                        $this->db->insert('combo_items', $item);
                    }
                }
                return true;
            }
            return false;
        }
    */

    public function addProduct($data, $store_quantities, $items = array(), $entes = array()) {
        if ($this->db->insert('products', $data)) {
        
            $product_id = $this->db->insert_id();
            
            if(! empty($store_quantities)) {
                foreach ($store_quantities as $store_quantity) {
                    $store_quantity['product_id'] = $product_id;
                    $this->db->insert('product_store_qty', $store_quantity);
                }
            }

            if(! empty($items)) {
                foreach ($items as $item) {
                    $item['product_id'] = $product_id;
                    $this->db->insert('combo_items', $item);
                }
            }

            $entes += ['product_id' => $product_id];

            //var_dump($entes);

            for($i=0; $i < count($entes); $i++){
                $ent = array(
                    'price'         => $entes[$i]["price"],
                    'store_id'      => $entes[$i]["store_id"],
                    'tipo_id'       => $entes[$i]["tipo_id"],
                    'product_id'    => $entes["product_id"]
                );
                $this->db->set($ent)->insert("product_store_entes");
            }

            //echo $this->db->set($entes)->get_compiled_insert("product_store_entes");
            //$this->db->set($entes)->insert("product_store_entes");

            return true;
        }
        return false;
    }

    public function add_products($data = array()) {
        //echo "Price:".$data["price"]."<br>";
        //var_dump($data);
        //die();
        
        $nI = 0;
        foreach($data as $ar_fila){
            if ($this->db->insert('products', $ar_fila)) {
                $nI++;
                $producto_id = $this->db->insert_id();

                // code,name,cost,tax,price,category_id
                $cSql = "select a.* from tec_stores a where a.activo='1'";
                $query = $this->db->query($cSql);
                foreach($query->result() as $r){
                    $cSql = "insert into tec_product_store_entes(product_id,store_id,tipo_id,quantity,price) values({$producto_id}, {$r->id}, 1,999999,".$ar_fila["price"].")";
                    //echo $cSql . "<br>";
                    $this->db->query($cSql,array( $product_id, $r->id, 1, $ar_fila["price"]));
                }
                //die();
                
            }
        }
        if($nI>0){
            return true;
        }else{
            return false;
        }
    }

    public function updatePrice($data = array()) {
        if ($this->db->update_batch('products', $data, 'code')){
            return true;
        }
        return false;
    }

    public function updateProduct($id, $data = array(), $store_quantities = array(), $items = array(), $photo = NULL, $ar_s_p = array()) {
        if ($photo) { 
            $data['image'] = $photo; 
        }
        
        //echo "Antes de guardar...<br>";
        //echo str_replace("\n","<br>",print_r($data,true));
        //die("Ñato $id");

        if ($this->db->update('products', $data, array('id' => $id))) {
        
            
            if( !empty($store_quantities)) {
                
                foreach ($store_quantities as $store_quantity) {
                    $store_quantity['product_id'] = $id;
                    $this->setStoreQuantity($store_quantity);
                }
            
            }
            
            if( !empty($items)) {
                $this->db->delete('combo_items', array('product_id' => $id));
                foreach ($items as $item) {
                    $item['product_id'] = $id;
                    $this->db->insert('combo_items', $item);
                }
            }
            
            if( !empty($ar_s_p) ){
                foreach($ar_s_p as $key => $valor){
                    //echo "key:" . $key . "<br>";
                    
                    foreach($valor as $key2 => $valor2){
                        //echo "[" . $key . "] [" . $key2 . "] = " .  $valor2 . "<br>";
                        $cSql = "replace into tec_product_store_entes(product_id,store_id,tipo_id,price) values(?,?,?,?)";
                        $this->db->query($cSql,array($id, $key, $key2, $valor2));
                    }
                }
            }

            //die("Un segundo...");
            return true;
        }
        return false;
    }

    public function setStoreQuantity($data) {
        if ($this->getStoreQuantity($data['product_id'], $data['store_id'])) {
/*
            $this->db->update('product_store_qty', 
                array('quantity'        => $data['quantity'], 
                    'price'             => $data['price'],
                array('product_id' => $data['product_id'], 
                    'store_id' => $data['store_id'])
            );
*/

            $ar_ceneca = array(
                'quantity'        => $data['quantity'], 
                'price'           => $data['price']
            );

            // Agrega columnas de deliverys
            $cSql = "select * from tec_deliverys";
            $query = $this->db->query($cSql);
            foreach($query->result() as $r){
                $cads = substr("0".$r->id,-2);
                
                eval('$valore = $' . "data['price_delivery_" . $cads . '_' . $data['store_id'] . "'];");

                $ar_ceneca['price_delivery_'.$cads] = $valore;
            }

            $this->db->update('product_store_qty',
                $ar_ceneca,
                array('product_id' => $data['product_id'], 
                    'store_id' => $data['store_id'])
            );
            
        } else {
            $this->db->insert('product_store_qty', $data);
        }
    }

    public function getStoreQuantity($product_id, $store_id = NULL) {
        if(!$store_id) {
            $store_id = $this->session->userdata('store_id') ? $this->session->userdata('store_id') : 1;
        }
        $q = $this->db->get_where('product_store_qty', array('product_id' => $product_id, 'store_id' => $store_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStoresQuantity($product_id) {
        $q = $this->db->get_where('product_store_qty', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getComboItemsByPID($product_id) {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('combo_items') . '.quantity as qty, ' . $this->db->dbprefix('products') . '.name as name')
        ->join('products', 'products.code=combo_items.item_code', 'left')
        ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function deleteProduct($id) {

        //$id = $this->input->get("id");
            
        // Verificando que no exista movimiento con ese producto:
        $query = $this->db->select("product_id")
            ->from("purchase_items")
            ->where("product_id",$id)
            ->get();
            //->get_compiled_select();
        
        if($query->num_rows() > 0){
            //$data["rpta"] = "No se pudo eliminar";
            //$data["error"] = true;
            return FALSE;
        }else{
            $this->db->delete('products', array('id' => $id));

            // Borrando los precios en tabla precios
            $cSql = "delete from tec_product_store_entes where product_id = ?";
            $this->db->query($cSql, array($id));

            return true;
        }
    }

    public function getProductNames($term, $limit = 10) {
        if ($this->db->dbdriver == 'sqlite3') {
            $this->db->where("type != 'combo' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  (name || ' (' || code || ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("type != 'combo' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        }
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

}
