<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Replaced_products_model extends MY_Model {

    public $_table = 'replaced_products';
    protected $primary_key = 'replacement_product_id';
    public $return_type = 'array';

    public function save($values) {
        $data = array(
            'transaction_id' => $values['transaction_id'],
            'product_id' => $values['product_id'],
            'qty' => $values['qty'],
            'price_per_unit' => $values['price_per_unit'],
            'batch_no' => $values['batch_no'],
            'exp_date' => $values['expiry_date'],  
            'mfg_date' => $values['mfg_date'],
            'created_on' => date('Y-m-d H:i:s'),
            'created_by' => get_session_data('user_id'),
            'transaction_product_id'=>$values['transaction_product_id']
        );
        return $this->insert($data);
    }

}
