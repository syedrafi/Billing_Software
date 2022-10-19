<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categories_model extends MY_Model {

    public $_table = 'categories';
    protected $primary_key = 'category_id';
    public $return_type = 'array';

   

    public function getOptions(){
         $company_id=get_company_id();
       $res= $this->db->select('category_id,category_name')->from($this->_table)->where("company_id",$company_id)->get();
       
      
      $result= $res->result_array();
      array_unshift($result, array('category_id'=>"","category_name"=>"Select a category"));
      return $result;
        
    }
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
        $company_id=  get_company_id();
        $this->db->select('category_id');
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

        $this->db->select('category_name,category_id');
        $this->db->from($this->_table . " c")->where("company_id",$company_id);
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
        $res['data'] = $result;

        return $res;
    }

   

    public function getdropdownOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('category_id,category_name')->from($this->_table)->where("company_id=$company_id")->get();


        $result = $res->result_array();
        $options=array(""=>"Select");
        foreach ($result as $key => $value) {
            $options[$value['category_id']] = $value['category_name'];
        }

      

        return $options;
    }

    public function save($values) {
      
        $values['company_id'] = get_company_id();
        return $this->insert($values);
    }

    public function update_data($values) {
        $id = $values['category_id'];
        unset($values['category_id']);
      
        return $this->update($id, $values);
    }

     public function delete_data($values) {
        $id = $values['category_id'];

$this->db->where("category_id",$id);
$this->db->delete($this->_table);
     return true;
    }
}

