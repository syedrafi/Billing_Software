<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Petty_cash_details_model extends MY_Model {

    public $_table = 'petty_cash_paid_details';
    protected $primary_key = 'id';
    public $return_type = 'array';

    public function save($values, $petty_cash_id) {

        $this->db->where("petty_cash_id", $petty_cash_id);
        $this->db->delete($this->_table);
        $data = array(
            'head_id' => $values['head_id'],
            'contact_id' => $values['contact_id'],
            'notes' => $values['notes'],
            'petty_cash_id' => $petty_cash_id
        );

        return $this->insert($data);
    }

}
