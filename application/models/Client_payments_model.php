<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Client_payments_model extends MY_Model {

    public $_table = 'client_payments';
    protected $primary_key = 'client_payment_id';
    public $return_type = 'array';

    public function getPayments($values) {
        try {
            $business_contact_id = $values['business_contact_id'];
            $from_date = date("Y-m-d", strtotime($values['date_from']));
            $to_date = date("Y-m-d", strtotime($values['date_to']));

            $this->db->select("business_contact_id,paid_amt,paid_date,transaction_id AS payment_transaction_id")->from($this->_table)->where("business_contact_id", $business_contact_id)->where("paid_date >=", $from_date)->where("paid_date <=", $to_date)->order_by("paid_date","desc")->order_by("client_payment_id","asc");

            $result = $this->db->get()->result_array();
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function save($values) {
        try {
            
            $data = array(
                'business_contact_id' => $values['business_contact_id'],
                'paid_amt' => $values['paid_amt'],
                'paid_date' => date("Y-m-d", strtotime($values['paid_date'])),
                'payment_mode_id' => $values['payment_mode_id'],
                'transaction_id' => trim($values['transaction_id']),
                'created_on' => date('Y-m-d'),
                'created_by' => get_session_data('user_id'),
            );

            $res = $this->insert($data);
            if ($res):
                echo "Successfully Added"; exit;
                $return_data['msg'] = "Successfully Added";
                return $return_data;
            endif;
              echo "Error Occured. Contact Admin"; exit;
            $return_data['msg'] = "Error Occured. Contact Admin";
            return $return_data;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function update_data($values) {
        try {
            $id = $values['id'];
            $data = array(
                'business_contact_id' => $values['business_contact_id'],
                'paid_amt' => $values['paid_amt'],
                'paid_date' => date("Y-m-d", strtotime($values['paid_date'])),
                'payment_mode_id' => $values['payment_mode_id'],
                'transaction_id' => trim($values['transaction_id']),
                'updated_by' => get_session_data('user_id'),
            );

            $res = $this->update($id, $data);
            if ($res):
                $return_data['msg'] = "Successfully Updated";
                return $return_data;
            endif;

            $return_data['msg'] = "Error Occured. Contact Admin";
            return $return_data;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function delete_data($values) {
        $id = $values['id'];


        return $this->delete($id);
    }

    public function fetch($values) {
        try {
            $page = $values['page']; // get the requested page
            $limit = $values['rows']; // get how many rows we want to have into the grid
            $sidx = $values['sidx']; // get index row - i.e. user click to sort
            $sord = $values['sord']; // get the direction
            
            $business_contact_type=$values['type'];
            if (!$sidx):
                $sidx = 1;
            endif;
            $is_client = false;
            if (!get_session_data('user_id') && get_session_data('business_contact_id')):
                $is_client = true;
                $business_contact_id = get_session_data('business_contact_id');
            endif;

            if ($values['from_date'] != ""):
                $from_date = date("Y-m-d", strtotime($values['from_date']));
                $to_date = date("Y-m-d", strtotime($values['to_date']));
            endif;
            if (!get_session_data('user_id') && !get_session_data('business_contact_id')):
                exit;
            endif;
            $count = $this->db->select('COUNT(*) as total');
            $this->db->from($this->_table . " u")->join("business_contacts bc","u.business_contact_id=bc.business_contact_id")->where("contact_type_id",$business_contact_type);

            if ($is_client):
                $this->db->where("business_contact_id", $business_contact_id);
            endif;

            if (isset($from_date)):
                $this->db->where("paid_date >=", $from_date)->where("paid_date <=", $to_date);
            endif;
            if (isset($values['_search']) && $values['_search'] != false && isset($values['filters'])):

                $items = json_decode($values['filters'], true);
                $rules = $items['rules'];
                if (is_array($rules)):
                    foreach ($rules as $value_search) {

                        $fields = $value_search['field'];
                        $field = str_replace('_view', '', $fields);

                        $data = $value_search['data'];

                        $this->db->like($field, $data);
                    }

                endif;
            endif;


            $res = $this->db->get()->row_array();
            $no_of_rows = $res['total'];

            if ($no_of_rows > 0) {
                $total_pages = ceil($no_of_rows / $limit);
            } else {
                $total_pages = 0;
            }

            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit; // do not put $limit*($page - 1)
            if ($start < 0):
                $start = 0;
            endif;
            $this->db->select('*');
            $this->db->from($this->_table . " u")->join("business_contacts bc","u.business_contact_id=bc.business_contact_id")->where("contact_type_id",$business_contact_type);
            $this->db->limit($limit, $start)
                    ->where("contact_type_id",$business_contact_type)
                    ->order_by($sidx, $sord);
            if ($is_client):
                $this->db->where("business_contact_id", $business_contact_id);
            endif;
            if (isset($from_date)):
                $this->db->where("paid_date >=", $from_date)->where("paid_date <=", $to_date);
            endif;
            if (isset($values['_search']) && $values['_search'] != false && isset($values['filters'])):

                $items = json_decode($values['filters'], true);
                $rules = $items['rules'];
                if (is_array($rules)):
                    foreach ($rules as $value_search) {

                        $fields = $value_search['field'];
                        $field = str_replace('_view', '', $fields);

                        $data = $value_search['data'];

                        $this->db->like($field, $data);
                    }

                endif;
            endif;
            $com_details = $this->db->get()->result_array();
            $responce = new stdClass();
            $responce->page = $page;
            $responce->total = $total_pages;
            $responce->records = $count;
            $i = 0;


            foreach ($com_details as $row) {

                $responce->rows[$i]['id'] = $row['client_payment_id'];
                $paid_date = date("d-m-Y", strtotime($row['paid_date']));

                $responce->rows[$i]['cell'] = array($row['client_payment_id'], $paid_date, $row['paid_amt'], $row['business_contact_id'], $row['payment_mode_id'], $row['transaction_id']);
                $i++;
            }
            return $responce;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo "Message: " . $e->getLine() . "\n";
            // Other code to recover from the error
        }
    }

    public function getTotalPayment($business_contact_id) {
        try {
            $res = $this->db->select("SUM(paid_amt) as total_paid")->from($this->_table . " p")->where("business_contact_id", $business_contact_id)->get()->row_array();

            return $res;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo "Message: " . $e->getLine() . "\n";
            // Other code to recover from the error
        }
    }

}
