<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transaction_product_details extends MY_Model {

    public $_table = 'transaction_product_details';
    protected $primary_key = 'product_detail_id';
    public $return_type = 'array';

    public function save($values, $id) {
        try {
            $expiry_date = null;
            $mfg_date = null;
            $values['expiry_date'] = null;
            $values['mfg_date'] = null;
            if ($values['mfg_date'] != ""):
                $mfg_date = date('Y-m-d', strtotime($values['mfg_date']));
            else:
                $values['mfg_date'] = null;
            endif;
            $values['batch_no'] = null;
            $data = array(
                'batch_no' => $values['batch_no'],
                'expiry_date' => $expiry_date,
                'mfg_date' => $mfg_date,
                'transaction_product_id' => $id
            );

            $res = $this->db->select("COUNT(*) as total")->from($this->_table)->where("transaction_product_id", $id)->get();
            $result = $res->row_array();
            if ($result['total'] == 0):
                return $this->insert($data);
            elseif ($result['total'] == 1):
                $this->db->where('transaction_product_id', $id);
                $this->db->update($this->_table, $data);
                return true;
            else:
                $this->db->where('transaction_product_id', $id);
                $this->db->delete($this->_table);
                return $this->insert($data);
            endif;
        } catch (Exception $e) {
            
        }
    }

}
