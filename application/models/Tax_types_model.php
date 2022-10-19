<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tax_types_model extends MY_Model {

    public $_table = 'tax_types';
    protected $primary_key = 'tax_type_id';
    public $return_type = 'array';

   

    public function getOptions(){
         $company_id=get_company_id();
       $res= $this->db->select('tax_type_id,tax_type')->from($this->_table)->where("company_id=$company_id")->get();
       
      
      $result= $res->result_array();
    
      return $result;
        
    }
     public function getdropdownOptions() {
       
        $res = $this->db->select('tax_type_id,tax_type')->from($this->_table)->get();


        $result = $res->result_array();
        $options=array(""=>"No tax");
        foreach ($result as $key => $value) {
            $options[$value['tax_type_id']] = $value['tax_type'];
        }

      

        return $options;
    }
   
}
