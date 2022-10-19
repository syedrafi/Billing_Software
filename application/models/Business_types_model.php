<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Business_types_model extends MY_Model {

    public $_table = 'business_contacts_type';
    protected $primary_key = 'type_id';
    public $return_type = 'array';

   

    public function getOptions(){
         $company_id=get_company_id();
       $res= $this->db->select('type_id,type_name')->from($this->_table)->get();
       
      
      $result= $res->result_array();
      array_unshift($result, array('type_id'=>"","type_name"=>"Select a  type"));
      return $result;
        
    }
      public function getdropdownOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('type_id,type_name')->from($this->_table . " u")->get();


        $result = $res->result_array();
        $options = array("" => "Select");
        foreach ($result as $key => $value) {
            $options[$value['type_id']] = $value['type_name'];
        }



        return $options;
    }
     public function getdropdownOptionsCustom() {
        $company_id = get_company_id();
        $res = $this->db->select('type_id,type_name')->from($this->_table . " u")->get();


        $result = $res->result_array();
        $options = array();
        foreach ($result as $key => $value) {
            $options[$value['type_id']] = $value['type_name'];
        }



        return $options;
    }
   
}
