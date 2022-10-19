<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_model extends MY_Model {

    public $_table = 'products';
    protected $primary_key = 'product_id';
    public $return_type = 'array';

    public function fetch($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);

        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('product_name,product_id,unit_type_id,category_id');
        $this->db->from($this->_table . " c")->where("company_id", get_company_id());
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

        $this->db->select('product_name,product_code,product_id,brand_id,size,category_id,hsn_no,unit_type_id,is_active');
        $this->db->from($this->_table . " c")->where("company_id", get_company_id());
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
        $this->load->model('Transaction_products_model');
        $i = $start_index + 1;
        foreach ($result as $key => $value) {
            $result[$key]['sno'] = $i;
            $result[$key]['stock'] = $this->Transaction_products_model->getStock($value['product_id']);
            $i++;
        }
        $res['data'] = $result;

        return $res;
    }

    public function fetch_lowstock($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);

        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }



        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('product_name,product_code,brand_id,product_id,brand_id,unit_type_id');
        $this->db->from($this->_table . " c")->where("company_id", get_company_id());
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
        $this->load->model('Transaction_products_model');
        $i = $start_index + 1;
        $index = 0;
        foreach ($result as $key => $value) {
            $result[$key]['sno'] = $i;
            $stock_result = $this->Transaction_products_model->getLowStock($value['product_id']);

            if ($stock_result):
                $result_array[$index]['stock'] = $stock_result['stock'];
                $result_array[$index]['total_sales'] = $stock_result['total_sales'];
                $result_array[$index]['total_purchase'] = $stock_result['total_purchase'];
                $result_array[$index]['product_id'] = $value['product_id'];
                $result_array[$index]['product_name'] = $value['product_name'];
                $result_array[$index]['brand_id'] = $value['brand_id'];
                $result_array[$index]['product_code'] = $value['product_code'];
                $result_array[$index]['sno'] = $i;
                $i++;
                $index++;
            else:
                unset($result[$key]);
            endif;
        }
        $res['itemsCount'] = count($result_array);
        $res['data'] = $result_array;

        return $res;
    }

    public function fetch_stock($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        // $from_date = $values['date'];
        unset($values['date']);
        unset($values['pageIndex']);
        unset($values['pageSize']);

        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('product_name,product_code,brand_name,category_name,specifications,product_id,unit_type_id,c.category_id');
        $this->db->from($this->_table . " c")->join("brands b", "c.brand_id=b.brand_id", 'left')->join("categories ca", "c.category_id=ca.category_id", 'left')->where("c.company_id", get_company_id());
        if (isset($sort_field)):
            if ($sort_field == "product_name" && $sort_field == "product_id"):
                $this->db->order_by($sort_field, $sort_order);
            endif;
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->like($key, $value)->or_like("brand_name", $value)->or_like("specifications", $value)->or_like("category_name", $value);
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('product_name,product_code,brand_name,category_name,specifications,product_id,unit_type_id,c.category_id');
        $this->db->from($this->_table . " c")->join("brands b", "c.brand_id=b.brand_id", 'left')->join("categories ca", "c.category_id=ca.category_id", 'left')->where("c.company_id", get_company_id());
        if (isset($sort_field)):
            if ($sort_field == "product_name" && $sort_field == "product_id"):
                $this->db->order_by($sort_field, $sort_order);
            endif;
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->like($key, $value)->or_like("brand_name", $value)->or_like("specifications", $value)->or_like("category_name", $value);
            }
        }

        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();
        // $this->db->last_query();

        $result = $result_set->result_array();
        $this->load->model('Transaction_products_model');
        $i = $start_index + 1;
        foreach ($result as $key => $value) {
            $result[$key]['product_name'] = $value['brand_name'] . "-" . $value['product_name'];

            $res = $this->Transaction_products_model->getStockWithValue($value['product_id']);
            $result[$key]['stock'] = round($res['qty'], 2);
            $result[$key]['value'] = round($res['stock_value'], 2);
            $result[$key]['total_purchase'] = round($res['total_purchase'], 2);
            $result[$key]['total_sales'] = round($res['total_sales'], 2);
            $result[$key]['sno'] = $i;
            $i++;
        }

        if (isset($sort_field)):
            if ($sort_field != "product_name" && $sort_field != "product_id"):
                $stock = array();
                foreach ($result as $key => $row) {
                    $stock[$key] = $row[$sort_field];
                }
                if ($sort_order == "desc"):
                    array_multisort($stock, SORT_DESC, $result);
                else:
                    array_multisort($stock, SORT_ASC, $result);
                endif;



            endif;
        endif;
        $res['data'] = $result;

        return $res;
    }

    public function getOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('product_id,product_name')->from($this->_table)->where("company_id=$company_id")->get();


        $result = $res->result_array();

        array_unshift($result, array('product_id' => "", "product_name" => "Select"));
        return $result;
    }

    public function save($values) {
        $values['created_on'] = date('Y-m-d');
        $values['created_by'] = get_user_id();
        $values['company_id'] = get_company_id();
        unset($values['stock']);
        unset($values['sno']);
        if ($values['size'] == ""):
            unset($values['size']);
        endif;
        if ($values['product_code'] == ""):
            unset($values['product_code']);
        endif;
        if ($values['hsn_no'] == ""):
            unset($values['hsn_no']);
        endif;
        if ($values['brand_id'] == ""):
            unset($values['brand_id']);
        endif;
        return $this->insert($values);
    }

    public function update_data($values) {
        $id = $values['product_id'];
        unset($values['product_id']);
        unset($values['stock']);
        unset($values['sno']);
        unset($values['category_id']);
        if ($values['product_code'] == ""):
            $values['product_code'] = null;
        endif;
        if ($values['hsn_no'] == ""):
            $values['hsn_no'] = null;
        endif;
        if ($values['brand_id'] == ""):
            $values['brand_id'] = null;
        endif;
        if ($values['size'] == ""):
            $values['size'] = null;
        endif;
        return $this->update($id, $values);
    }

    public function delete_data($values) {
        $id = $values['product_id'];


        return $this->delete($id);
    }

}
