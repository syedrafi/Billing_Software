<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transaction_products_model extends MY_Model {

    public $_table = 'transaction_products';
    protected $primary_key = 'transaction_product_id';
    public $return_type = 'array';

    public function getDetails($transaction_id) {
        try {

            $this->db->select('tp.product_id,tt.transaction_date,product_name, hsn_no,qty,price_per_unit,brand_name,discount_percentage,category_name,specifications,serial_nos,igst_percent,sgst_percent,cgst_percent,purchase_transaction_product_id,size');
            $this->db->from($this->_table . " tp")->join('products p', 'tp.product_id=p.product_id')->join("categories c", "p.category_id=c.category_id", 'left')->join("brands b", "p.brand_id=b.brand_id", 'left')->join("transactions tt", "tt.transaction_id=tp.transaction_id");
            $this->db->where("tp.transaction_id", $transaction_id);

            $res = $this->db->get();

            $res_array = $res->result_array();

            foreach ($res_array as $key => $value):
                $purchase_transaction_date = $this->getPurchaseTransactionDate($value['purchase_transaction_product_id']);


                if (is_numeric($value['discount_percentage'])):
                    $res_array[$key]['discounted_price_per_unit'] = $value['price_per_unit'] - (($value['price_per_unit'] / 100) * $value['discount_percentage']);
                else:
                    $res_array[$key]['discounted_price_per_unit'] = $value['price_per_unit'];
                endif;

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
              $this->db->select("qty,transaction_product_id,specifications,price_per_unit,category_name,brand_name,product_name,business_contact_name")->from($this->_table . " tp")->join("products p", "tp.product_id=p.product_id")->join("companies cm", "p.company_id=cm.company_id")->join("transactions t", "tp.transaction_id=t.transaction_id")->join("business_contacts b", "t.business_contact_id=b.business_contact_id")->join("categories ca", "p.category_id=ca.category_id", 'left')->join("brands br", "p.brand_id=br.brand_id", "left")->like("product_name", $search_term)->or_like("category_name", $search_term)->or_like("brand_name", $search_term)->or_like("specifications", $search_term)->where("cm.company_id", get_company_id())->where("t.is_active", 1)->where('type', 1);
             * 
             */
            $company_id = get_company_id();


            $res = $this->db->select("product_id,product_name,brand_name,product_code")->join("brands b", "p.brand_id=b.brand_id")->from("products p")->where("is_active", 1)->like("product_code", $search_term)->or_like("product_name", $search_term)->get();


            $result = $res->result_array();

            $options = array();
            $i = 0;
            foreach ($result as $key => $value) {
                $stock = $this->getStock($value['product_id']);
                if ($stock > 0):
                    $stock_in_hand = $stock;
                    $options[$i]['id'] = $value['product_id'];
                    $options[$i]['text'] = $value['product_code'] . " | " . $value['product_name'] . " | " . $stock_in_hand;
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
                //     $stock = $this->getBatchStock($result[$key]['transaction_product_id']);
                //
                 
                    $options[$key]['id'] = $value['product_id'];
                $options[$key]['text'] = "" . $value['brand_name'] . "|" . $value['product_name'] . "|" . $value['hsn_no'];
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


            $return_array = array("total" => $result['total']);
            return $return_array;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getBatchStockPurchase($transaction_product_id) {
        try {
            $this->db->select("SUM(qty) as total")->from($this->_table . " tp")->join("transactions t", "tp.transaction_id=t.transaction_id")->where("t.is_active", 1)->where("transaction_product_id", $transaction_product_id)->where("t.type", 1);
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
        $this->db->select('transaction_product_id,p.product_id,qty,price_per_unit,tax_percent,c.mrp');
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

        $this->db->select('c.transaction_product_id,hsn_no,cgst_percent,size,sgst_percent,igst_percent,p.product_id,qty,price_per_unit,(qty*price_per_unit) AS value,product_name,specifications,brand_name,batch_no,mfg_date,expiry_date,c.mrp');
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

        $this->db->select('tp.transaction_product_id,tp.discount_percentage,specifications,product_name,brand_name,tp.product_id,tp.qty,tp.price_per_unit,(tp.qty*tp.price_per_unit) AS value,tp.price_per_unit ,td.batch_no,td.mfg_date,td.expiry_date,cgst_percent,igst_percent,sgst_percent');
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
            if (is_numeric($value['discount_percentage'])):
                $value['value'] = $value['value'] - (($value['value'] / 100) * $value['discount_percentage']);

            endif;

            $result[$key]['cgst_percent'] = floatval($value['cgst_percent']);
            $result[$key]['cgst_amt'] = get_tax_value($value['cgst_percent'], $value['value']);
            $result[$key]['sgst_percent'] = floatval($value['sgst_percent']);
            $result[$key]['sgst_amt'] = get_tax_value($value['sgst_percent'], $value['value']);
            $result[$key]['igst_percent'] = floatval($value['igst_percent']);
            $result[$key]['igst_amt'] = get_tax_value($value['igst_percent'], $value['value']);
            $result[$key]['taxable_value'] = $value['value'];
            $result[$key]['value'] = round($value['value'] + $result[$key]['cgst_amt'] + $result[$key]['sgst_amt'] + $result[$key]['igst_amt'], 2);
            $total_value = round($total_value + $result[$key]['value'], 2);
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
        unset($values['data']['cgst_amt']);
        unset($values['data']['igst_amt']);
        unset($values['data']['sgst_amt']);


        if ($values['data']['mrp'] == 0 || $values['data']['mrp'] == ""):
            unset($values['data']['mrp']);
        endif;
        if ($values['data']['cgst_percent'] == 2 || $values['data']['cgst_percent'] == "2"):
            $values['data']['cgst_percent'] = 2.5;
            $values['data']['sgst_percent'] = 2.5;
        endif;
        //    unset($values['value']);
        $id = $this->insert($values['data']);

        $this->load->model('Transaction_product_details');
        $res = $this->Transaction_product_details->save($other_values, $id);

        return true;
    }

    public function save_sales($values) {


        $data['product_id'] = $values['data']['product_id'];
        $data['price_per_unit'] = $values['data']['price_per_unit'];
        $data['transaction_id'] = $values['data']['transaction_id'];
        $data['igst_percent'] = $values['data']['igst_percent'];
        $data['cgst_percent'] = $values['data']['cgst_percent'];
        $data['sgst_percent'] = $values['data']['sgst_percent'];
        // $data['serial_nos'] = $values['data']['serial_nos'];
        $data['qty'] = $values['data']['qty'];
        $stock = $this->getStock($data['product_id']);

        if ($data['qty'] > $stock):
            return false;
        endif;

        $data['discount_percentage'] = floatval($values['data']['discount_percentage']);

        if ($data['cgst_percent'] == 2 || $data['cgst_percent'] == "2"):
            $data['cgst_percent'] = 2.5;
            $data['sgst_percent'] = 2.5;
        endif;
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
        if ($values['cgst_percent'] == 2 || $values['cgst_percent'] == "2"):
            $values['cgst_percent'] = 2.5;
            $values['sgst_percent'] = 2.5;
        endif;
        $res = $this->update($id, $values);
        if (isset($data['data']['batch_no'])):
            $this->load->model('Transaction_product_details');
            $this->Transaction_product_details->save($data['data'], $id);
            return $id;
        endif;
    }

    public function update_data_purchase($data) {
        $values = $data['data'];
        $id = $values['transaction_product_id'];
        $transaction_id = $values['transaction_id'];
        unset($values['transaction_product_id']);
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
        if ($values['cgst_percent'] == 2 || $values['cgst_percent'] == "2"):
            $values['cgst_percent'] = 2.5;
            $values['sgst_percent'] = 2.5;
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
        $this->db->select('price_per_unit,qty,(cgst_percent+sgst_percent+igst_percent) AS gst_percent')->from($this->_table)->where("transaction_id=$transaction_id");
        $res = $this->db->get();
        $result = $res->result_array();
        $bill_value = 0;
        foreach ($result as $key => $value):
            $result[$key]['bill_value'] = ($value['price_per_unit'] * $value['qty']) + (($value['price_per_unit'] * $value['qty'] / 100) * $value['gst_percent']);
            $bill_value = $bill_value + $result[$key]['bill_value'];
        endforeach;
        $this->load->model('Transaction_taxes');
        $tax_percent = $this->Transaction_taxes->getPercent($transaction_id);
        $this->load->model('Transactions_model');
        $dis_percent = $this->Transactions_model->getDiscountPercent($transaction_id);
        $result['bill_value'] = $bill_value;
        $bill_value = $bill_value - (($result['bill_value'] / 100) * $dis_percent);
        return round($bill_value + (($bill_value / 100) * $tax_percent), 2);
    }

    public function getTotalGST($transaction_id) {
        $this->db->select('price_per_unit,qty,(cgst_percent+sgst_percent+igst_percent) AS gst_percent,cgst_percent,igst_percent,sgst_percent')->from($this->_table)->where("transaction_id", $transaction_id);
        $res = $this->db->get();
        $result = $res->result_array();
        $bill_value = 0;
        $without_tax = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        foreach ($result as $key => $value):

            $result[$key]['bill_value'] = ($value['price_per_unit'] * $value['qty']) + (($value['price_per_unit'] * $value['qty'] / 100) * $value['gst_percent']);

            $bill_value = $bill_value + $result[$key]['bill_value'];

            $result[$key]['without_tax'] = ($value['price_per_unit'] * $value['qty']);
            $without_tax = $without_tax + $result[$key]['without_tax'];

            if ($value['cgst_percent'] > 0):
                $cgst = $cgst + ( (($value['price_per_unit'] * $value['qty'] / 100) * $value['cgst_percent']));

            endif;
            if ($value['igst_percent'] > 0):
                $igst = $igst + (($value['price_per_unit'] * $value['qty'] / 100) * $value['igst_percent']);
            endif;
            if ($value['sgst_percent'] > 0):
                $sgst = $sgst + (($value['price_per_unit'] * $value['qty'] / 100) * $value['sgst_percent']);
            endif;
        endforeach;
        $this->load->model('Transaction_taxes');

        $this->load->model('Transactions_model');

        $result['bill_value'] = $bill_value;
        $return_array = array("bill_value" => $bill_value, "sgst" => $sgst, "cgst" => $cgst, "igst" => $igst, "without_tax" => $without_tax, "total_tax" => $sgst + $cgst + $igst);
        return $return_array;
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
        $this->db->select('SUM(qty) AS total')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("type", 1)->where("product_id", $product_id)->where("is_active", 1);


        $res_pur = $this->db->get()->row_array();
        $stock_array = array("purchase" => $res_pur['total']);

        $this->db->select('SUM(qty) AS total')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("type", 2)->where("product_id", $product_id)->where("is_active", 1);


        $res_sales = $this->db->get()->row_array();
        $stock_array['sales'] = $res_sales['total'];

        return array("stock_value" => 0, 'total_sales' => $stock_array['sales'], 'total_purchase' => $stock_array['purchase'], 'qty' => $stock_array['purchase'] - $stock_array['sales']);
    }

    public function getSalesSum($transaction_product_id, $product_id) {
        $this->db->select('SUM(qty) AS total')->from($this->_table . " tp")->join('transactions t', 'tp.transaction_id=t.transaction_id')->where("product_id", $product_id)->where("t.is_active", 1);

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
//            $type = $values['customer_type'];
            $transaction_prouct_id = $values['transaction_product_id'];


            // After GST

            $res = $this->db->select("tp.mrp,transaction_date,batch_no,expiry_date,mfg_date")
                            ->from("products p")->join("transaction_products tp", "p.product_id=tp.product_id")
                            ->join("transaction_product_details td", "tp.transaction_product_id=td.transaction_product_id", 'left')
                            ->join("transactions tt", "tt.transaction_id=tp.transaction_id")
                            ->where('tp.transaction_product_id', $transaction_prouct_id)->get();
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
