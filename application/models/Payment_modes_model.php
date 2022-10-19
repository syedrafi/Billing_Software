<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment_modes_model extends MY_Model {

    public $_table = 'payment_modes';
    protected $primary_key = 'mode_id';
    public $return_type = 'array';

            public function getOptionsSelect2() {

        $this->db->select('u.mode_id,mode_name')->from($this->_table . " u");

         
       

        $result = $this->db->get()->result_array();
        $options = ":Select;";
        $lastkey = end($result);


        foreach ($result as $key => $value) {
            $options .= $value['mode_id'] . ":" . $value['mode_name'];

            if ($lastkey['mode_id'] != $value['mode_id']) {
                $options .= ";";
            }
        }
        return trim($options);
    }
    
    
    public function getOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('mode_id,mode_name')->from($this->_table)->get();


        $result = $res->result_array();
        array_unshift($result, array('mode_id' => "", "mode_name" => "Select a Mode"));
        return $result;
    }

    public function getOptionsDropdown() {
        $company_id = get_company_id();
        $res = $this->db->select('mode_id,mode_name')->from($this->_table)->get();


        $result = $res->result_array();
        $options = array("" => "Select");
        foreach ($result as $key => $value) {
            $options[$value['mode_id']] = $value['mode_name'];
        }
        return $options;
    }

}
