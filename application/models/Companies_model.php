<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Companies_model extends MY_Model {

    public $_table = 'companies';
    protected $primary_key = 'company_id';
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
          $this->db->select('company_name,company_id,company_email,company_tin,company_mobile,company_cst,company_address,company_dl_no');
        $this->db->from($this->_table . " c");
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }
       
        $res['itemsCount']= $this->db->count_all_results();
        
        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('company_name,company_id,company_email,company_tin,company_mobile,company_cst,company_address,company_dl_no');
        $this->db->from($this->_table . " c");
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

    public function getOptions(){
       $res= $this->db->select('company_id,company_name')->from($this->_table)->get();
       
       
      $result= $res->result_array();
      array_unshift($result, array('company_id'=>"","company_name"=>"Select a company"));
      return $result;
        
    }
    public function save($values) {
$values['created_on']=date('Y-m-d');
        return $this->insert($values);
    }

    public function update_data($values) {
        $id = $values['company_id'];
        unset($values['company_id']);
        return $this->update($id, $values);
    }

    public function delete_data($values) {
        $id = $values['company_id'];


        return $this->delete($id);
    }
    public function getDetails($company_id=0){
        if($company_id==0):
            $company_id=  get_company_id();
        endif;
        $this->db->select("*")->from($this->_table)->where("company_id",  $company_id);
        
        $res=$this->db->get();
        $result=$res->row_array();
        return $result;
        
    }

}
