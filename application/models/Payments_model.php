<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payments_model extends MY_Model {

    public $_table = 'payments';
    protected $primary_key = 'payment_id';
    public $return_type = 'array';

    public function fetch($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);
        $transaction_id = $values['transaction_id'];
        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('payment_id');
        $this->db->from($this->_table . " c")->where("transaction_id", $transaction_id);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('payment_id,amount,DATE_FORMAT(issued_date,"%d-%m-%Y") AS issued_date,DATE_FORMAT(pdc_dated,"%d-%m-%Y") AS pdc_dated,stmt_checked,mgmt_checked,ref_no,mode_id,bank_name');
        $this->db->from($this->_table . " c")->where("transaction_id", $transaction_id);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;
        $this->db->order_by("pdc_dated", "asc");
        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();
        // $this->db->last_query();

        $result = $result_set->result_array();
        $this->load->model('Transaction_products_model');
        $sum = $this->Transaction_products_model->getTotal($transaction_id);
        foreach ($result as $key => $value) {
            $paid = $this->getPaid($transaction_id, $value['pdc_dated']);
            $result[$key]['balance'] = round($sum - $paid, 2);

            if ($result[$key]['stmt_checked'] == 1):
                $result[$key]['stmt_checked'] = true;
            else:
                $result[$key]['stmt_checked'] = false;
            endif;

            if ($result[$key]['mgmt_checked'] == 1):
                $result[$key]['mgmt_checked'] = true;
            else:
                $result[$key]['mgmt_checked'] = false;
            endif;
        }
        $res['data'] = $result;
        return $res;
    }

    public function getPaid($transaction_id, $issued_date = 0, $should_check = 0) {
        if ($issued_date != 0):
            $issued_date = date('Y-m-d', strtotime($issued_date));
        endif;
        $this->db->select('SUM(amount) AS total')->from($this->_table . " tp")->where('transaction_id', $transaction_id);
        if ($issued_date != 0):
            $this->db->where("pdc_dated <=", $issued_date);
        endif;
        if ($should_check == 1):
            $this->db->where("stmt_checked", 1);
        endif;

        $res = $this->db->get();

        $result = $res->row_array();


        return $result['total'];
    }

    public function getPaidDetails($transaction_id) {
        $this->db->select('p.*,pm.mode_name')->from($this->_table . " p")->join("payment_modes pm", "p.mode_id=pm.mode_id")->where("transaction_id=$transaction_id")->order_by("p.issued_date asc");

        $res = $this->db->get();
        $result = $res->result_array();
        return $result;
    }

    public function save($values) {


        $values['data']['issued_date'] = date('Y-m-d', strtotime($values['data']['issued_date']));
        $values['data']['pdc_dated'] = date('Y-m-d', strtotime($values['data']['pdc_dated']));
        if ($values['data']['stmt_checked']=="true"):
            $values['data']['stmt_checked'] = 1;
        else:
            $values['data']['stmt_checked'] = 0;
        endif;
        if ($values['data']['mgmt_checked']=="true"):
            $values['data']['mgmt_checked'] = 1;
        else:
            $values['data']['mgmt_checked'] = 0;
        endif;
        $values['data']['created_on'] = date('Y-m-d');
        //    unset($values['value']);
        return $this->insert($values['data']);
    }

    public function update_data($data) {
        $values = $data['data'];
        $values['issued_date'] = date('Y-m-d', strtotime($values['issued_date']));
        $values['pdc_dated'] = date('Y-m-d', strtotime($values['pdc_dated']));

        if ($values['stmt_checked'] == "true"):
            $values['stmt_checked'] = 1;
        else:
            $values['stmt_checked'] = 0;
        endif;
        if ($values['mgmt_checked'] == "true"):
            $values['mgmt_checked'] = 1;
        else:
            $values['mgmt_checked'] = 0;
        endif;
        $id = $values['payment_id'];
        unset($values['payment_id']);

        unset($values['adjust']);
        unset($values['value']);
        if (isset($values['balance'])):
            unset($values['balance']);
        endif;
        if (isset($values['sno'])):
            unset($values['sno']);
        endif;
        if (isset($values['transaction_id'])):
            if ($values['transaction_id'] == "" || $values['transaction_id'] == 0):
                unset($values['transaction_id']);
            endif;
        endif;

        return $this->update($id, $values);
    }

    public function update_advance($data) {

        $this->load->model('Transaction_products_model');
        $id = $data['payment_id'];
        $transaction_id = $data['transaction_id'];
        $paid = $this->getPaid($transaction_id);
        $advance_result = $this->getRow($id);
        $advance_paid = $advance_result['amount'];
        $res = $this->Transaction_products_model->getNamesAndSum($transaction_id);
        $bill_value = $res['total'];
        if ($bill_value < $advance_paid):
            $balance = $advance_paid - $bill_value;
        else:
            $balance = 0;
        endif;
        if ($paid < $bill_value):
            // Less Already paid for bill
            $bill_value = $bill_value - $paid;
            if ($bill_value >= $advance_paid):
                $amt = $advance_paid;
            else:
                $amt = $bill_value;
            endif;
            $this->addPaymentFromAdvance($advance_result, $amt, $transaction_id);

            $values = array('amount' => $balance);
            $this->update($id, $values);

            return array('msg' => "Successfully Updated");
        endif;
        return array('msg' => "Bill Already Paid");



        return array('msg' => "Successfully Updated");
    }

    public function addPaymentFromAdvance($result, $amount, $trasactioin_id) {

        try {
            $values = array();
            $values['data'] = $result;
            $values['data']['amount'] = $amount;
            $values['data']['transaction_id'] = $trasactioin_id;


            unset($values['data']['payment_id']);
            unset($values['data']['updated_on']);
            return $this->save($values);
        } catch (Exception $e) {
            echo $e->getCode();
            echo $e->getLine();
            echo $e->getFile();
        }
    }

    public function getRow($payment_id) {
        $this->db->select('*')->from($this->_table)->where("payment_id=$payment_id");
        $res = $this->db->get();
        $result = $res->row_array();

        return $result;
    }

    public function delete_data($data) {
        $values = $data['data'];
        $id = $values['payment_id'];


        return $this->delete($id);
    }

    public function getTotal($transaction_id) {
        $this->db->select('(price_per_unit * qty) AS bill_value')->from($this->_table)->where("transaction_id=$transaction_id");
        $res = $this->db->get();
        $result = $res->row_array();

        return $result['bill_value'];
    }

    public function getStock($product_id) {
        $total_purchase = $this->getSum($product_id, 1);
        $total_sales = $this->getSum($product_id, 2);

        return $total_purchase - $total_sales;
    }

    public function getSum($product_id, $type) {
        
    }

    public function fetch_advance($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);

        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('payment_id');
        $this->db->from($this->_table . " c")->join("business_contacts b", "c.business_contact_id=b.business_contact_id")->where("transaction_id IS NULL")->where("c.business_contact_id !=", "")->where("b.company_id", get_company_id())->where("amount !=", 0);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                if ($key = "business_contact_id"):
                    $key = "b.business_contact_id";
                endif;
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('c.business_contact_id,payment_id,amount,DATE_FORMAT(issued_date,"%d-%m-%Y") AS issued_date,DATE_FORMAT(pdc_dated,"%d-%m-%Y") AS pdc_dated,stmt_checked,mgmt_checked,ref_no,mode_id,bank_name');
        $this->db->from($this->_table . " c")->join("business_contacts b", "c.business_contact_id=b.business_contact_id")->where("transaction_id IS NULL")->where("c.business_contact_id !=", "")->where("b.company_id", get_company_id())->where("amount !=", 0);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;
        $this->db->order_by("pdc_dated", "asc");
        foreach ($values as $key => $value) {

            if ($value) {
                if ($key = "business_contact_id"):
                    $key = "b.business_contact_id";
                endif;
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();
        // $this->db->last_query();

        $result = $result_set->result_array();
        $i = $start_index;
        foreach ($result as $key => $value) {
            $i++;
            $result[$key]['adjust'] = "<a class='btn btn-success btn-sm adjust_btn' data-payment-id=" . $value['payment_id'] . ">Adjust</a>";
            $result[$key]['sno'] = $i;
        }

        $res['data'] = $result;
        return $res;
    }

    public function fetch_pdc($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        $from_date_field = $values['from_date_field'];
        $to_date_field = $values['to_date_field'];
        unset($values['pageIndex']);
        unset($values['pageSize']);
        $t_tpye = $values['t_type'];
        unset($values['t_type']);



        unset($values['from_date_field']);
        unset($values['to_date_field']);
        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $this->db->select('payment_id');
        $this->db->from($this->_table . " c")->join("transactions t", "t.transaction_id=c.transaction_id")->where("type", $t_tpye);
        if ($from_date_field != "" && $to_date_field != ""):
            $this->db->where("pdc_dated >=", date("Y-m-d", strtotime($from_date_field)));
            $this->db->where("pdc_dated <=", date("Y-m-d", strtotime($to_date_field)));
        endif;
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        else:
            $this->db->order_by("pdc_dated", "desc");
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                if ($key == "business_contact_id"):
                    $key = "t.business_contact_id";
                endif;
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('payment_id,business_contact_name,mode_name,amount,issued_date,pdc_dated,stmt_checked,mgmt_checked,ref_no,pm.mode_id,bank_name,t.business_contact_id');
        $this->db->from($this->_table . " c")
                ->join("transactions t", "t.transaction_id=c.transaction_id")
                ->join("business_contacts bt", "t.business_contact_id=bt.business_contact_id")
                ->join("payment_modes pm", "c.mode_id=pm.mode_id")
                ->where("type", $t_tpye);
        if ($from_date_field != "" && $to_date_field != ""):
            $this->db->where("pdc_dated >=", date("Y-m-d", strtotime($from_date_field)));
            $this->db->where("pdc_dated <=", date("Y-m-d", strtotime($to_date_field)));
        endif;
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        else:
            $this->db->order_by("pdc_dated", "desc");
        endif;
        foreach ($values as $key => $value) {

            if ($value) {
                if ($key == "business_contact_id"):
                    $key = "t.business_contact_id";
                endif;
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();

        // $this->db->last_query();

        $result = $result_set->result_array();
        $this->load->model('Transaction_products_model');
        foreach ($result as $key => $value) {
            $result[$key]['issued_date'] = date('d-m-Y', strtotime($value['issued_date']));
            $result[$key]['pdc_dated'] = date('d-m-Y', strtotime($value['pdc_dated']));

            if ($value['mgmt_checked'] == 1):
                $result[$key]['mgmt_checked'] = true;
            else:
                $result[$key]['mgmt_checked'] = false;
            endif;
            if ($value['stmt_checked'] == 1):
                $result[$key]['stmt_checked'] = true;
            else:
                $result[$key]['stmt_checked'] = false;
            endif;
        }

        $res['data'] = $result;
        return $res;
    }

}
