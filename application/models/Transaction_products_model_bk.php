<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transaction_products_model extends MY_Model {

    public $_table = 'transaction_products';
    protected $primary_key = 'transaction_product_id';
    public $return_type = 'array';

    public function getDetails($transaction_id) {
        try {

            $this->db->select('tp.product_id,product_name,qty,price_per_unit,brand_name,category_name,specifications,serial_nos,mrp');
            $this->db->from($this->_table . " tp")->join('products p', 'tp.product_id=p.product_id')->join("categories c", "p.category_id=c.category_id", 'left')->join("brands b", "p.brand_id=b.brand_id", 'left');
            $this->db->where("transaction_id", $transaction_id);

            $res = $this->db->get();

            $res_array = $res->result_array();


            return $res_array;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getOptions($search_term) {
        try {
            /*
              $this->db->select("qty,transaction_product_id,specifications,price_per_unit,category_name,brand_name,product_name,business_contact_name")->from($this->_table . " tp")->join("products p", "tp.product_id=p.product_id")->join("companies cm", "p.company_id=cm.company_id")->join("transactions t", "tp.transaction_id=t.transaction_id")->join("business_contacts b", "t.business_contact_id=b.business_contact_id")->join("categories ca", "p.category_id=ca.category_id", 'left')->join("brands br", "p.brand_id=br.brand_id", "left")->like("product_name", $search_term)->or_like("category_name", $search_term)->or_like("brand_name", $search_term)->or_like("specifications", $search_term)->where("cm.company_id", get_company_id())->where("t.is_active", 1)->where('type', 1);
             * 
             */
            $company_id = get_company_id();
            $query = " SELECT `qty`, `tp`.`transaction_product_id`, `price_per_unit`, `brand_name`, `product_name`, `business_contact_name`
FROM `transaction_products` `tp`
JOIN `products` `p` ON `tp`.`product_id`=`p`.`product_id`
JOIN `companies` `cm` ON `p`.`company_id`=`cm`.`company_id`
JOIN `transactions` `t` ON `tp`.`transaction_id`=`t`.`transaction_id`
JOIN `business_contacts` `b` ON `t`.`business_contact_id`=`b`.`business_contact_id`
LEFT JOIN `transaction_product_details` `td` ON `tp`.`transaction_product_id`=`td`.`transaction_product_id`

LEFT JOIN `brands` `br` ON `p`.`brand_id`=`br`.`brand_id`
WHERE (`expiry_date` IS NULL  OR CURDATE() < `expiry_date`) AND (`product_name` LIKE '%$search_term%' ESCAPE '!'

OR  `brand_name` LIKE '%$search_term%' ESCAPE '!' OR `batch_no` LIKE '%$search_term%')

AND `cm`.`company_id` = $company_id
AND `t`.`is_active` = 1
AND `type` = 1";
            $res = $this->db->query($query);



            $result = $res->result_array();

            $options = array();
            $i = 0;
            foreach ($result as $key => $value) {
                $stock = $this->getBatchStock($result[$key]['transaction_product_id']);
                if ($value['qty'] > $stock['total']):
                    $stock_in_hand = $value['qty'] - $stock['total'];
                    $options[$i]['id'] = $value['transaction_product_id'];
                    $options[$i]['text'] = $value['brand_name'] . " | " . $value['product_name'] . " | " . $stock_in_hand;
                    $i++;

                endif;
            }
            return $options;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getOptionsPurchase($search_term) {
        try {
            $this->db->select("specifications,category_name,brand_name,product_name,p.product_id")->from("products p", "tp.product_id=p.product_id")->join("companies cm", "p.company_id=cm.company_id")->join("categories ca", "p.category_id=ca.category_id", 'left')->join("brands br", "p.brand_id=br.brand_id", "left")->where("cm.company_id", get_company_id())->like("product_name", $search_term)->or_like("brand_name", $search_term);
            $res = $this->db->get();
            $result = $res->result_array();
            $options = array();
            foreach ($result as $key => $value) {
                //     $stock = $this->getBatchStock($result[$key]['transaction_product_id']);
                //
                 
                    $options[$key]['id'] = $value['product_id'];
                $options[$key]['text'] = "" . $value['brand_name'] . "|" . $value['product_name'] . "";
            }
            return $options;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getBatchStock($transaction_product_id) {
        try {
            $this->db->select("SUM(qty) as total")->from($this->_table . " tp")->join("transactions t", "tp.transaction_id=t.transaction_id")->where("t.is_active", 1)->where("purchase_transaction_product_id", $transaction_product_id)->where("t.type", 2);
            $res = $this->db->get();
            $result = $res->row_array();
            return $result;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getNamesAndSum($transaction_id) {
        try {

            $this->db->select('c.product_id,product_name,qty,price_per_unit');
            $this->db->from($this->_table . " c")->join('products p', 'c.product_id=p.product_id');
            $this->db->where("transaction_id", $transaction_id);

            $res = $this->db->get();

            $res_array = $res->result_array();
            $total = 0;
            $prod_names = array();
            foreach ($res_array as $key => $value) {
                $total = $total + ($value['price_per_unit'] * $value['qty']);
                $prod_names[] = $value['product_name'];
            }
            $discount_total = $this->calculateDiscount($transaction_id, $total);
            $total = $total - $discount_total;
            $tax_total = $this->calculateTax($transaction_id, $total);


            $total = round($total + $tax_total, 2);
            $result = array('total' => $total, 'product_names' => implode(",", $prod_names));
            return $result;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function calculateTax($transaction_id, $total) {
        try {
            $this->load->model('Transaction_taxes');
            $percent = $this->Transaction_taxes->getPercent($transaction_id);

            $tax = ($total / 100) * $percent;
            return $tax;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function calculateDiscount($transaction_id, $total) {
        try {
            $this->load->model('Transactions_model');
            $percent = $this->Transactions_model->getDiscountPercent($transaction_id);
            if ($percent > 0):
                $discount = ($total / 100) * $percent;
                return $discount;
            endif;
            return 0;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function fetch($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);
        $transaction_id = $values['transaction_id'];
        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('transaction_product_id,p.product_id,qty,price_per_unit,tax_percent');
        $this->db->from($this->_table . " c")->join("products p", "c.product_id=p.product_id")->where("transaction_id", $transaction_id)->where("transaction_id", $transaction_id);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('c.transaction_product_id,p.product_id,qty,price_per_unit,(qty*price_per_unit) AS value,product_name,specifications,brand_name,batch_no,mfg_date,expiry_date');
        $this->db->from($this->_table . " c")->join("products p", "c.product_id=p.product_id")->join("brands b", "p.brand_id=b.brand_id", 'left')->join("transaction_product_details pd", 'c.transaction_product_id=pd.transaction_product_id', 'left')->where("transaction_id", $transaction_id);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();
        // $this->db->last_query();

        $result = $result_set->result_array();

        $total_value = 0;
        foreach ($result as $key => $value) {
            $result[$key]['product_name'] = $value['brand_name'] . "-" . $value['specifications'] . $value['product_name'];
            $result[$key]['value'] = round($value['value'], 2);
            $total_value = $total_value + $result[$key]['value'];
        }
        $res['data'] = $result;
        $res['total_value'] = $total_value;
        return $res;
    }

    public function fetch_replacements($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        $exp_date = date('Y-m-d');
        if (isset($values['expiry_date'])):
            $exp_date = date('Y-m-d', strtotime($values['expiry_date']));
        endif;

        unset($values['pageIndex']);
        unset($values['pageSize']);
        unset($values['expiry_date']);

        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('transaction_product_id,p.product_id,qty,price_per_unit,transaction_id');
        $this->db->from($this->_table . " c")->join('transactions t', 'c.transaction_id=t.transaction_id')->join("products p", "c.product_id=p.product_id")->join("transaction_product_details tpd", "c.transaction_product_id=tpd.transaction_product_id")->where("type", 1)->where("expiry_date <=", "$exp_date");
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('c.transaction_product_id,p.product_id,qty,price_per_unit,(qty*price_per_unit) AS value,product_name,specifications,brand_name,batch_no,mfg_date,expiry_date,c.transaction_id');
        $this->db->from($this->_table . " c")->join('transactions t', 'c.transaction_id=t.transaction_id')->join("products p", "c.product_id=p.product_id")->join("brands b", "p.brand_id=b.brand_id", 'left')->join("transaction_product_details tpd", "c.transaction_product_id=tpd.transaction_product_id", 'left')->where("type", 1)->where("expiry_date <=", "$exp_date");
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();
        // $this->db->last_query();

        $result = $result_set->result_array();
        $i = $start_index + 1;
        $total_value = 0;
        foreach ($result as $key => $value) {
            $result[$key]['product_name'] = $value['brand_name'] . "-" . $value['product_name'];
            $result[$key]['value'] = round($value['value'], 2);
            $result[$key]['sno'] = $i;
            $i++;
            $total_value = $total_value + $result[$key]['value'];
            if ($value['mfg_date'] != ""):
                $result[$key]['mfg_date'] = date('d-m-Y', strtotime($value['mfg_date']));
            endif;
            if ($value['expiry_date'] != ""):
                $result[$key]['expiry_date'] = date('d-m-Y', strtotime($value['expiry_date']));
            endif;
        }
        $res['data'] = $result;
        $res['total_value'] = $total_value;
        return $res;
    }

    public function fetch_sales($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);
        $transaction_id = $values['transaction_id'];
        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('transaction_product_id,product_id,qty,price_per_unit');
        $this->db->from($this->_table . " c")->where("transaction_id", $transaction_id);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('tp.transaction_product_id,specifications,product_name,brand_name,tp.product_id,tp.qty,tp.price_per_unit,(tp.qty*tp.price_per_unit) AS value,tp.price_per_unit ,td.batch_no,td.mfg_date,td.expiry_date');
        $this->db->from($this->_table . " tp")->join("products p", "tp.product_id=p.product_id")->join("brands b", "p.brand_id=b.brand_id", 'left')->join("transaction_product_details  td", "tp.purchase_transaction_product_id=td.transaction_product_id", 'left')->where("tp.transaction_id", $transaction_id);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                //      $this->db->WHERE("'tp.'.$key LIKE '%$value%'");
            }
        }

        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();
        // $this->db->last_query();

        $result = $result_set->result_array();

        $total_value = 0;
        $product_options = array();
        foreach ($result as $key => $value) {

            $product_options[$key]['transaction_product_id'] = $value['transaction_product_id'];
            $result[$key]['product_name'] = $value['brand_name'] . "-" . $value['product_name'];
            if ($value['mfg_date'] != ""):
                $result[$key]['mfg_date'] = date('d-m-Y', strtotime($value['mfg_date']));
            endif;
            if ($value['expiry_date'] != ""):
                $result[$key]['expiry_date'] = date('d-m-Y', strtotime($value['expiry_date']));
            endif;


            $result[$key]['value'] = round($value['value'], 2);
            $total_value = round($total_value + $value['value'], 2);
        }
        $res['data'] = $result;
        $res['total_value'] = $total_value;
        $res['product_options'] = $product_options;
        return $res;
    }

    public function save($values) {

        $other_values = $values['data'];
        unset($values['data']['batch_no']);
        unset($values['data']['mfg_date']);
        unset($values['data']['expiry_date']);

        //    unset($values['value']);
        $id = $this->insert($values['data']);

        $this->load->model('Transaction_product_details');
        $res = $this->Transaction_product_details->save($other_values, $id);

        return true;
    }

    public function save_sales($values) {

        $data['purchase_transaction_product_id'] = $values['data']['product_id'];
        $product_id = $this->getProductID($values['data']['product_id']);
        $data['product_id'] = $product_id['product_id'];
        $data['price_per_unit'] = $values['data']['price_per_unit'];
        $data['transaction_id'] = $values['data']['transaction_id'];
        // $data['serial_nos'] = $values['data']['serial_nos'];
        $data['qty'] = $values['data']['qty'];

        return $this->insert($data);
    }

    public function getProductID($transaction_product_id) {
        try {
            $this->db->select("product_id")->from($this->_table)->where("transaction_product_id", $transaction_product_id);
            $res = $this->db->get();
            $result = $res->row_array();
            return $result;
        } catch (Exception $e) {
            $e->getMessage();
            exit;
        }
    }

    public function update_data($data) {
        $values = $data['data'];
        $id = $values['transaction_product_id'];
        $transaction_id = $values['transaction_id'];
        unset($values['transaction_product_id']);
        unset($values['value']);
        if (isset($values['product_name'])):
            unset($values['product_name']);
        endif;

        if (isset($values['specifications'])):
            unset($values['specifications']);
        endif;
        if (isset($values['sno'])):
            unset($values['sno']);
        endif;
        if (isset($values['brand_name'])):
            unset($values['brand_name']);
        endif;
        if (isset($values['category_name'])):
            unset($values['category_name']);
        endif;
        if (isset($values['category_id'])):
            unset($values['category_id']);
        endif;
        if (isset($values['cost_price'])):
            unset($values['cost_price']);
        endif;
        if (isset($values['percentage'])):
            unset($values['percentage']);
        endif;
        if (isset($values['batch_no'])):
            unset($values['batch_no']);
        endif;
        if (isset($values['expiry_date'])):
            unset($values['expiry_date']);
        endif;
        if (isset($values['mfg_date'])):
            unset($values['mfg_date']);
        endif;
        // Check editrted after one day
        $this->load->model('Transactions_model');
        $this->Transactions_model->checkEdited($transaction_id);


        $res = $this->update($id, $values);
        if (isset($data['data']['batch_no'])):
            $this->load->model('Transaction_product_details');
            $this->Transaction_product_details->save($data['data'], $id);
            return $id;
        endif;
    }

    public function update_replacement_data($data) {

        $values = $data['data'];
        $id = $values['transaction_product_id'];

        $data_details = $this->getProductDetails($id);
        $transaction_id = $data_details['transaction_id'];
        $this->load->model('Replaced_products_model');
        $save = $this->Replaced_products_model->save($data_details);
        if ($save):
            $data['data']['transaction_id'] = $transaction_id;
            $this->update_data($data);
        endif;
    }

    public function getProductDetails($id) {
        $this->db->select("*")->from($this->_table . " tp")->join("transaction_product_details tpd", "tp.transaction_product_id=tpd.transaction_product_id")->where("tp.transaction_product_id", $id);

        $res = $this->db->get();
        $result = $res->row_array();
        return $result;
    }

    public function delete_data($data) {
        $values = $data['data'];
        $id = $values['transaction_product_id'];


        return $this->delete($id);
    }

    public function getTotal($transaction_id) {
        $this->db->select('SUM((price_per_unit * qty)) AS bill_value')->from($this->_table)->where("transaction_id=$transaction_id");
        $res = $this->db->get();
        $result = $res->row_array();

        $this->load->model('Transaction_taxes');
        $tax_percent = $this->Transaction_taxes->getPercent($transaction_id);
        $this->load->model('Transactions_model');
        $dis_percent = $this->Transactions_model->getDiscountPercent($transaction_id);

        $bill_value = $result['bill_value'] - (($result['bill_value'] / 100) * $dis_percent);
        return $bill_value + (($bill_value / 100) * $tax_percent);
    }

    public function getStock($product_id, $date = null) {

        $total_purchase = $this->getSum($product_id, 1, $date);
        $total_sales = $this->getSum($product_id, 2, $date);

        return $total_purchase - $total_sales;
    }

    public function getLowStock($product_id, $date = null) {

        $total_purchase = $this->getSum($product_id, 1, $date);
        if ($total_purchase > 0):
            $total_sales = $this->getSum($product_id, 2, $date);
            $percentage = ($total_sales / $total_purchase) * 100;
            if ($percentage > 75):
                return array("total_purchase" => $total_purchase, "total_sales" => $total_sales, "stock" => $total_purchase - $total_sales);
            endif;
        endif;


        return false;
    }

    public function getStockWithValue($product_id, $date = null) {

        $stock = $this->getSumWithValue($product_id);


        return $stock;
    }

    public function getSumWithValue($product_id) {
        $this->db->select('qty,price_per_unit,t.transaction_id,transaction_product_id')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("type", 1)->where("product_id", $product_id)->where("is_active", 1);


        $res = $this->db->get();

        $result = $res->result_array();
        $stock_value = 0;
        $qty = 0;
        $total_purchase = 0;
        $total_sales = 0;
        foreach ($result as $key => $value) {
            $res = $this->getSalesSum($value['transaction_product_id'], $product_id);

            $in_stock = $value['qty'] - $res;
            $total_purchase = $total_purchase + $value['qty'];
            $total_sales = $total_sales + $res;
            if ($in_stock < 0):
            //  $in_stock = 0;
            endif;
            $result[$key]['stock_value'] = $in_stock * $value['price_per_unit'];
            $stock_value = $stock_value + ($in_stock * $value['price_per_unit']);
            $qty = $qty + ($in_stock);
        }

        return array("stock_value" => $stock_value, 'total_sales' => $total_sales, 'total_purchase' => $total_purchase, 'qty' => $qty);
    }

    public function getSalesSum($transaction_product_id, $product_id) {
        $this->db->select('SUM(qty) AS total')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("product_id", $product_id)->where("purchase_transaction_product_id", $transaction_product_id);

        $res = $this->db->get();
        $result = $res->row_array();
        return $result['total'];
    }

    public function getSum($product_id, $type, $date) {
        $this->db->select('SUM(qty) AS total')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("type", $type)->where("product_id", $product_id)->where("is_active", 1);
        if ($date):
            $date = date('Y-m-d', strtotime($date));
            $this->db->where("transaction_date<", $date);
        endif;
        $res = $this->db->get();
        $result = $res->row_array();
        return $result['total'];
    }

    public function getMonthSum($product_id, $type, $date) {
        $this->db->select('SUM(qty) AS total')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("type", $type)->where("product_id", $product_id)->where("is_active", 1);
        if ($date):
            $date = date('Y-m-d', strtotime($date));
            $to_date = strtotime($date);
            $to_date = strtotime("+1 day", $to_date);
            $to_date = date('Y-m-d', $to_date);
            $this->db->where("transaction_date", $date);

        endif;
        $res = $this->db->get();
        $result = $res->row_array();
        if ($result['total']):
            return $result['total'];
        else:
            return 0;
        endif;
    }

    public function getPrice($values) {

        try {
            $type = $values['customer_type'];
            $transaction_prouct_id = $values['transaction_product_id'];
            if ($type == 1) {
                $column_name = "ptd";
            } elseif ($type == 2) {
                $column_name = "pti";
            } else {
                $column_name = "mrp";
            }

            $res = $this->db->select("$column_name AS price,batch_no,expiry_date,mfg_date")->from("products p")->join("transaction_products tp", "p.product_id=tp.product_id")->join("transaction_product_details td", "tp.transaction_product_id=td.transaction_product_id", 'left')->where('tp.transaction_product_id', $transaction_prouct_id)->get();
            $result = $res->row_array();
            if ($result['mfg_date'] != "") {
                $result['mfg_date'] = date('d-m-Y', strtotime($result['mfg_date']));
            }
            if ($result['expiry_date'] != "") {
                $result['expiry_date'] = date('d-m-Y', strtotime($result['expiry_date']));
            }
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

}
