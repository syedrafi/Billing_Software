<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contact_types_model extends MY_Model {

    public $_table = 'contact_type';
    protected $primary_key = 'contact_type_id';
    public $return_type = 'array';

   

    public function getOptions(){
         $company_id=get_company_id();
       $res= $this->db->select('contact_type_id,contact_type')->from($this->_table)->get();
      $result= $res->result_array();
      array_unshift($result, array('contact_type_id'=>"","contact_type"=>"Select a  Contact Type"));
      return $result;
        
    }
     public function getdropdownOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('contact_type_id,contact_type')->from($this->_table . " u")->get();


        $result = $res->result_array();
        $options = array();
        foreach ($result as $key => $value) {
            $options[$value['contact_type_id']] = $value['contact_type'];
        }



        return $options;
    }
   
}
