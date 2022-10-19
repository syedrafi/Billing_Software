<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Company_users_model extends MY_Model {

    public $_table = 'company_users';
    protected $primary_key = 'company_user_id';
    public $return_type = 'array';

    public function save($values) {
        $user_id=$values['user_id'];
        $this->db->where("user_id=$user_id");
        $this->db->delete($this->_table);
        
        return $this->insert(array('company_id' => $values['company_id'], 'user_id' => $values['user_id'],'user_role_id'=>$values['user_role_id']));
    }

     public function getOldCompanys($user_id){
         $res=$this->db->select("company_id")->from($this->_table)->where("user_id=$user_id")->get();
         
         return $res->result_array();
    }
     public function getUserCompany($user_id){
         $res=$this->db->select("company_id,user_role_id")->from($this->_table)->where("user_id=$user_id")->get();
         
         return $res->row_array();
    }
}
