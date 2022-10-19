<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_roles_model extends MY_Model {

    public $_table = 'user_roles';
    protected $primary_key = 'user_role_id';
    public $return_type = 'array';

   

    public function getOptions(){
         $company_id=get_company_id();
         $res= $this->db->select('user_role_id,user_role')->from($this->_table)->get();
       
      
         $result= $res->result_array();
         array_unshift($result, array('user_role_id'=>"","user_role"=>"Select "));
         return $result;
        
    }
   
}
