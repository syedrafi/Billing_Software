<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Brands_model extends MY_Model {

    public $_table = 'brands';
    protected $primary_key = 'brand_id';
    public $return_type = 'array';

    public function getOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('brand_id,brand_name')->from($this->_table)->where("company_id=$company_id")->get();


        $result = $res->result_array();
        array_unshift($result, array('brand_id' => "", "brand_name" => "Select"));
        return $result;
    }

    public function fetch($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);
        if (isset($values['sno'])):
            unset($values['sno']);
        endif;
        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $company_id = get_company_id();
        $this->db->select('brand_id');
        $this->db->from($this->_table . " c")->where("company_id=$company_id");
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

        $this->db->select('brand_name,brand_id');
        $this->db->from($this->_table . " c")->where("company_id", $company_id);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $this->db->limit($page_size, $start_index);


        $i = $start_index + 1;


        $result_set = $this->db->get();
        // $this->db->last_query();


        $result = $result_set->result_array();

        foreach ($result as $key => $value) {
            $result[$key]['sno'] = $i;
            $i++;
        }
        $res['data'] = $result;

        return $res;
    }

    public function getdropdownOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('brand_id,brand_name')->from($this->_table)->where("company_id=$company_id")->get();


        $result = $res->result_array();
        $options = array("" => "Select");
        foreach ($result as $key => $value) {
            $options[$value['brand_id']] = $value['brand_name'];
        }



        return $options;
    }

    public function save($values) {

        $values['company_id'] = get_company_id();
        if(isset($values['sno'])):
            unset($values['sno']);
        endif;
        return $this->insert($values);
    }

    public function update_data($values) {
        $id = $values['brand_id'];
        unset($values['brand_id']);
  if(isset($values['sno'])):
            unset($values['sno']);
        endif;
        return $this->update($id, $values);
    }

    public function delete_data($values) {
        $id = $values['brand_id'];

        $this->db->where("brand_id", $id);
        $this->db->delete($this->_table);
        return true;
    }

}
