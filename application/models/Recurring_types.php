<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recurring_types extends MY_Model {

    public $_table = 'recurring_type';
    protected $primary_key = 'type_id';
    public $return_type = 'array';

   

    public function getOptions(){
         $company_id=get_company_id();
       $res= $this->db->select('type_id,')->from($this->_table)->where("company_id=$company_id")->get();
       
      
      $result= $res->result_array();
    
      return $result;
        
    }
     public function getdropdownOptions() {
       
        $res = $this->db->select('type_id,type_name')->from($this->_table)->get();


        $result = $res->result_array();
        $options=array();
        foreach ($result as $key => $value) {
            $options[$value['tax_type_id']] = $value['tax_type'];
        }

      

        return $options;
    }
    public function getJqgridOptions() {
        $res = $this->db->select("type_id,type_name")->from($this->_table)->get();
        $opt = $res->result_array();
        $lastkey=  end($opt); 
        $options = ":No;";
        foreach ($opt as $key => $value) {
            $options.=$value['type_id'] . ":" . $value['type_name'];

            if ($lastkey['type_name'] != $value['type_name']) {
                $options.=";";
            }
        }
        return trim($options);
    }
   
}
