<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transaction_Taxes extends MY_Model {

    public $_table = 'transaction_taxes';
    protected $primary_key = 'transaction_tax_id';
    public $return_type = 'array';

    public function save($values, $transaction_id) {


        try {
          
            $this->db->where("transaction_id", $transaction_id);
            $this->db->delete($this->_table);

            $data['tax_percent'] = $values['tax_percent'];
            $data['tax_type_id'] = $values['tax_type_id'];
            $data['transaction_id'] = intval($transaction_id);

            return $this->insert($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
    public function getPercent($transaction_id) {

        

        try {
          
         $res=$this->db->select("tax_percent")->from($this->_table)->where("transaction_id",$transaction_id)->get();
         $result=$res->row_array();
         return $result['tax_percent'];
          
          
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

}
