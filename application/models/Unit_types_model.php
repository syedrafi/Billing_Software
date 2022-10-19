<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Unit_types_model extends MY_Model {

    public $_table = 'unit_types';
    protected $primary_key = 'unit_type_id';
    public $return_type = 'array';

   

    public function getOptions(){
         $company_id=get_company_id();
       $res= $this->db->select('unit_type_id,unit_type')->from($this->_table)->where("company_id=$company_id")->get();
       
      
      $result= $res->result_array();
      array_unshift($result, array('unit_type_id'=>"","unit_type"=>"Select a Unit type"));
      return $result;
        
    }
   
}
