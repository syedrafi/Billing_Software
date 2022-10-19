<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Challan_products_model extends MY_Model {

    public $_table = 'delivery_challan_products';
    protected $primary_key = 'challan_product_id';
    public $return_type = 'array';

    public function getDetails($challan_id) {
        try {
            $this->db->select('tp.product_id,tt.challan_date,product_name,qty,price_per_unit,hsn_no,brand_name,category_name,specifications,purchase_transaction_product_id,size');
            $this->db->from($this->_table . " tp")->join('delivery_challan tt', 'tp.challan_id=tt.challan_id')->join('products p', 'tp.product_id=p.product_id')->join("categories c", "p.category_id=c.category_id", 'left')->join("brands b", "p.brand_id=b.brand_id", 'left');
            $this->db->where("tp.challan_id", $challan_id);

            $res = $this->db->get();

            $res_array = $res->result_array();

            foreach ($res_array as $key => $value):
                $purchase_transaction_date = $this->getPurchaseTransactionDate($value['purchase_transaction_product_id']);

                $res_array[$key]['batch_no'] = $purchase_transaction_date['batch_no'];
                $res_array[$key]['expiry_date'] = $purchase_transaction_date['expiry_date'];
                $res_array[$key]['mfg_date'] = $purchase_transaction_date['mfg_date'];


            endforeach;

            return $res_array;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getPurchaseTransactionDate($purchase_transaction_product_id) {

        try {
            $res = $this->db->select("transaction_date,mrp,batch_no,expiry_date,mfg_date")->from("transactions t")->join("transaction_products tp", "t.transaction_id=tp.transaction_id")
                            ->join("transaction_product_details tdp", "tp.transaction_product_id=tdp.transaction_product_id", 'left')
                            ->where("tp.transaction_product_id", $purchase_transaction_product_id)->get()->row_array();
            if ($res):

                return $res;
            endif;
            return false;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getOptions($search_term) {
        try {
            /*
              $this->db->select("qty,challan_product_id,specifications,price_per_unit,category_name,brand_name,product_name,business_contact_name")->from($this->_table . " tp")->join("products p", "tp.product_id=p.product_id")->join("companies cm", "p.company_id=cm.company_id")->join("transactions t", "tp.transaction_id=t.transaction_id")->join("business_contacts b", "t.business_contact_id=b.business_contact_id")->join("categories ca", "p.category_id=ca.category_id", 'left')->join("brands br", "p.brand_id=br.brand_id", "left")->like("product_name", $search_term)->or_like("category_name", $search_term)->or_like("brand_name", $search_term)->or_like("specifications", $search_term)->where("cm.company_id", get_company_id())->where("t.is_active", 1)->where('type', 1);
             * 
             */
            $company_id = get_company_id();
            $query = " SELECT `qty`, `tp`.`challan_product_id`, `price_per_unit`, `brand_name`, `product_name`, `business_contact_name`
FROM `transaction_products` `tp`
JOIN `products` `p` ON `tp`.`product_id`=`p`.`product_id`
JOIN `companies` `cm` ON `p`.`company_id`=`cm`.`company_id`
JOIN `transactions` `t` ON `tp`.`transaction_id`=`t`.`transaction_id`
JOIN `business_contacts` `b` ON `t`.`business_contact_id`=`b`.`business_contact_id`
LEFT JOIN `transaction_product_details` `td` ON `tp`.`challan_product_id`=`td`.`challan_product_id`

LEFT JOIN `brands` `br` ON `p`.`brand_id`=`br`.`brand_id`
WHERE (`expiry_date` IS NULL  OR CURDATE() < `expiry_date`) AND (`product_name` LIKE '%$search_term%' ESCAPE '!'

OR  `brand_name` LIKE '%$search_term%' ESCAPE '!' OR `batch_no` LIKE '%$search_term%' OR `product_code` LIKE '%$search_term%')

AND `cm`.`company_id` = $company_id
AND `t`.`is_active` = 1
AND `type` = 1";
            $res = $this->db->query($query);



            $result = $res->result_array();

            $options = array();
            $i = 0;
            foreach ($result as $key => $value) {
                $stock = $this->getBatchStock($result[$key]['challan_product_id']);
                if ($value['qty'] > $stock['total']):
                    $stock_in_hand = $value['qty'] - $stock['total'];
                    $options[$i]['id'] = $value['challan_product_id'];
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
            $this->db->select("specifications,category_name,brand_name,hsn_no,product_name,p.product_id")->from("products p", "tp.product_id=p.product_id")->join("companies cm", "p.company_id=cm.company_id")->join("categories ca", "p.category_id=ca.category_id", 'left')->join("brands br", "p.brand_id=br.brand_id", "left")->where("cm.company_id", get_company_id())->like("product_name", $search_term)->or_like("brand_name", $search_term)->or_like("product_code", $search_term)->or_like("hsn_no", $search_term);
            $res = $this->db->get();
            $result = $res->result_array();
            $options = array();
            foreach ($result as $key => $value) {
                //     $stock = $this->getBatchStock($result[$key]['challan_product_id']);
                //
                 
                    $options[$key]['id'] = $value['product_id'];
                $options[$key]['text'] = "" . $value['brand_name'] . "|" . $value['product_name'] . "|" . $value['hsn_no'];
            }
            return $options;
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
        $this->db->select('challan_product_id,p.product_id,qty,price_per_unit,tax_percent,c.mrp');
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

        $this->db->select('c.challan_product_id,hsn_no,cgst_percent,size,sgst_percent,igst_percent,p.product_id,qty,price_per_unit,(qty*price_per_unit) AS value,product_name,specifications,brand_name,batch_no,mfg_date,expiry_date,c.mrp');
        $this->db->from($this->_table . " c")->join("products p", "c.product_id=p.product_id")->join("brands b", "p.brand_id=b.brand_id", 'left')->join("transaction_product_details pd", 'c.challan_product_id=pd.challan_product_id', 'left')->where("transaction_id", $transaction_id);
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
            $result[$key]['product_name'] = $value['brand_name'] . "-" . $value['product_name'] . "-" . $value['hsn_no'] . " " . $value['size'];

            /*
             * GST Alteration
             */
            $result[$key]['cgst_percent'] = floatval($value['cgst_percent']);
            $result[$key]['cgst_amt'] = get_tax_value($value['cgst_percent'], $value['value']);
            $result[$key]['sgst_percent'] = floatval($value['sgst_percent']);
            $result[$key]['sgst_amt'] = get_tax_value($value['sgst_percent'], $value['value']);
            $result[$key]['igst_percent'] = floatval($value['igst_percent']);
            $result[$key]['igst_amt'] = get_tax_value($value['igst_percent'], $value['value']);
            $result[$key]['value'] = round($value['value'] + $result[$key]['cgst_amt'] + $result[$key]['sgst_amt'] + $result[$key]['igst_amt'], 2);
            $result[$key]['value'] = round($result[$key]['value'], 2);
            $total_value = round($total_value + $result[$key]['value'], 2);
            /*
             * End
             */
        }
        $res['data'] = $result;
        $res['total_value'] = $total_value;
        return $res;
    }

    public function fetch_single($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);
        $challan_id = $values['challan_id'];
        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('challan_product_id,product_id,qty,price_per_unit');
        $this->db->from($this->_table . " c")->where("challan_id", $challan_id);


        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('tp.challan_product_id,specifications,is_returned,product_name,brand_name,tp.product_id,tp.qty,tp.price_per_unit,(tp.qty*tp.price_per_unit) AS value,tp.price_per_unit ,td.batch_no,td.mfg_date,td.expiry_date');
        $this->db->from($this->_table . " tp")->join("products p", "tp.product_id=p.product_id")->join("brands b", "p.brand_id=b.brand_id", 'left')->join("transaction_product_details  td", "tp.purchase_transaction_product_id=td.transaction_product_id", 'left')->where("tp.challan_id", $challan_id);

        $this->db->order_by("challan_product_id", "ASC");


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

            $product_options[$key]['challan_product_id'] = $value['challan_product_id'];
            $result[$key]['product_name'] = $value['brand_name'] . "-" . $value['product_name'];
            if ($value['mfg_date'] != ""):
                $result[$key]['mfg_date'] = date('d-m-Y', strtotime($value['mfg_date']));
            endif;
            if ($value['expiry_date'] != ""):
                $result[$key]['expiry_date'] = date('d-m-Y', strtotime($value['expiry_date']));
            endif;



            $result[$key]['value'] = $value['value'];
            $total_value = round($total_value + $result[$key]['value'], 2);
        }
        $res['data'] = $result;
        $res['total_value'] = $total_value;
        $res['product_options'] = $product_options;
        return $res;
    }

    public function save_sales($values) {

        $data['purchase_transaction_product_id'] = $values['data']['product_id'];
        $product_id = $this->getProductID($values['data']['product_id']);
        $data['product_id'] = $product_id['product_id'];
        $data['price_per_unit'] = $values['data']['price_per_unit'];
        $data['challan_id'] = $values['data']['challan_id'];

        // $data['serial_nos'] = $values['data']['serial_nos'];
        $data['qty'] = $values['data']['qty'];
        $this->load->model("Transaction_products_model");
        $stock = $this->Transaction_products_model->getBatchStock($values['data']['product_id']);
     
        $purchase_stock = $this->Transaction_products_model->getBatchStockPurchase($values['data']['product_id']);
           
        if ($data['qty'] > ($purchase_stock['total'] - $stock['total'])):
            return false;
        endif;


        return $this->insert($data);
    }

    public function getProductID($challan_product_id) {
        try {
            $this->db->select("product_id")->from($this->_table)->where("challan_product_id", $challan_product_id);
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
        $id = $values['challan_product_id'];

        unset($values['challan_product_id']);
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
        $res = $this->update($id, $values);
        return true;
    }

    public function update_data_purchase($data) {
        $values = $data['data'];
        $id = $values['challan_product_id'];
        $transaction_id = $values['transaction_id'];
        unset($values['challan_product_id']);
        unset($values['value']);
        if (isset($values['product_name'])):
            unset($values['product_name']);
        endif;
        if (isset($values['hsn_no'])):
            unset($values['hsn_no']);
        endif;
        if (isset($values['size'])):
            unset($values['size']);
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
        if (isset($values['discount_percentage'])):
            if ($values['discount_percentage'] == ""):
                unset($values['percentage']);
            endif;
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
        //   $this->load->model('Transactions_model');
        //  $this->Transactions_model->checkEdited($transaction_id);
        if (isset($values['mrp'])):
            if ($values['mrp'] == ""):
                $values['mrp'] = null;
            endif;
        endif;
        if (isset($values['cgst_amt'])):
            unset($values['cgst_amt']);
            unset($values['igst_amt']);
            unset($values['sgst_amt']);
        endif;
        if (isset($values['taxable_value'])):
            unset($values['taxable_value']);

        endif;
        $res = $this->update($id, $values);
        if (isset($data['data']['batch_no'])):
            $this->load->model('Transaction_product_details');
            $this->Transaction_product_details->save($data['data'], $id);
            return $id;
        endif;
    }

    public function update_replacement_data($data) {

        $values = $data['data'];
        $id = $values['challan_product_id'];

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
        $this->db->select("*")->from($this->_table . " tp")->join("transaction_product_details tpd", "tp.challan_product_id=tpd.challan_product_id")->where("tp.challan_product_id", $id);

        $res = $this->db->get();
        $result = $res->row_array();
        return $result;
    }

    public function delete_data($data) {
        $values = $data['data'];
        $id = $values['challan_product_id'];


        return $this->delete($id);
    }

    public function getTotal($challan_id) {
        $this->db->select('price_per_unit,qty')->from($this->_table)->where("challan_id=$challan_id");
        $res = $this->db->get();
        $result = $res->result_array();
        $challan_value = 0;
        foreach ($result as $key => $value):
            $result[$key]['challan_value'] = $value['price_per_unit'] * $value['qty'];
            $challan_value = $challan_value + $result[$key]['challan_value'];
        endforeach;

        $result['bill_value'] = $challan_value;
        return $challan_value;
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
        $this->db->select('qty,price_per_unit,t.transaction_id,challan_product_id')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("type", 1)->where("product_id", $product_id)->where("is_active", 1);


        $res = $this->db->get();

        $result = $res->result_array();
        $stock_value = 0;
        $qty = 0;
        $total_purchase = 0;
        $total_sales = 0;
        foreach ($result as $key => $value) {
            $res = $this->getSalesSum($value['challan_product_id'], $product_id);

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

    public function getSalesSum($challan_product_id, $product_id) {
        $this->db->select('SUM(qty) AS total')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("product_id", $product_id)->where("purchase_challan_product_id", $challan_product_id)->where("t.is_active", 1);

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
            $transaction_prouct_id = $values['challan_product_id'];


            // After GST

            $res = $this->db->select("tp.mrp,transaction_date,batch_no,expiry_date,mfg_date")
                            ->from("products p")->join("transaction_products tp", "p.product_id=tp.product_id")
                            ->join("transaction_product_details td", "tp.challan_product_id=td.challan_product_id", 'left')
                            ->join("transactions tt", "tt.transaction_id=tp.transaction_id")
                            ->where('tp.challan_product_id', $transaction_prouct_id)->get();
            $result = $res->row_array();
            if (strtotime($result['transaction_date']) >= strtotime("2017-07-01")):

            endif;
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
