<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transactions_model extends MY_Model {

    public $_table = 'transactions';
    protected $primary_key = 'transaction_id';
    public $return_type = 'array';

    public function getGSTStatementDetails($values,$type) {
        try {
          $this->load->model('Transaction_products_model');
            if ($values['date_from'] != ""):
                $from_date = date('Y-m-d', strtotime($values['date_from']));
                $to_date = date('Y-m-d', strtotime($values['date_to']));
            endif;
            $this->db->select('c.transaction_date,bill_no,business_contact_name,gstin,c.transaction_id');
            $this->db->from($this->_table . " c")->join("business_contacts bc","c.business_contact_id=bc.business_contact_id")->where("c.type",$type)->where("c.is_active",1);

            $this->db->order_by("transaction_id", "desc");

            if ($from_date != "" && $to_date != ""):
                $this->db->where("transaction_date >=", date("Y-m-d", strtotime($from_date)));
                $this->db->where("transaction_date <=", date("Y-m-d", strtotime($to_date)));
            endif;
            $res=$this->db->get()->result_array();
         
            foreach ($res as $key=> $transaction):
                
                  $res[$key]['bill_details'] = $this->Transaction_products_model->getTotalGST($transaction['transaction_id']);
                 
                 
            endforeach;
            return $res;
            
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function getStatementDetails($values) {
        $business_contact_id = $values['business_contact_id'];
        $values['from_date'] = date("Y-m-d", strtotime($values['date_from']));
        $values['to_date'] = date("Y-m-d", strtotime($values['date_to']));
        $current_balance = $this->getCurrentBalance($values['date_from'], $business_contact_id);
        $this->load->model("Client_payments_model");
        $result_payments = $this->Client_payments_model->getPayments($values);
        $return_array = array();
        $index = 0;
        foreach ($result_payments as $payment):
            $payment['date'] = strtotime($payment['paid_date'] . " 00:01:00");

            $return_array[$index] = $payment;
            $index++;
        endforeach;


        $this->load->model("Transactions_model");
        $result_sales = $this->getSales($values);

        foreach ($result_sales as $payment):

            $payment['date'] = strtotime($payment['transaction_date'] . " 00:02:00");

            $return_array[$index] = $payment;
            $index++;
        endforeach;
        $sort = array();

        foreach ($return_array as $key => $row) {

            $sort[$key] = $row['date'];
        }
        array_multisort($sort, SORT_ASC, $return_array);

        return array("result" => $return_array, "balance" => $current_balance);
    }

    public function getCurrentBalance($to_date, $client_id) {
        try {
            $business_contact_id = $client_id;

            $this->db->select('t.transaction_id,transaction_date', FALSE)
                    ->from('transactions t')->where("type", 2)->where("t.business_contact_id", $client_id)->where("t.is_active", 1);
            $this->db->where("t.transaction_date <=", date("Y-m-d", strtotime($to_date)));


            $res = $this->db->get()->result_array();
            $this->load->model("Transaction_products_model");
            $total = 0;
            foreach ($res as $sales):
                $transaction_id = $sales['transaction_id'];
                $total = $total + round($this->Transaction_products_model->getTotal($transaction_id), 2);
            endforeach;










            $this->db->select("SUM(paid_amt) AS paid_total")->from("client_payments")->where("business_contact_id", $business_contact_id)->where("paid_date <=", $to_date);

            $res_paid = $this->db->get()->row_array();
            return $total - $res_paid['paid_total'];
        } catch (Exception $e) {
            
        }
    }

    public function add($values = array()) {
        try {
            $user_id = get_user_id();
            $company_id = get_company_id();
            $data = array('transaction_date' => date('Y-m-d'), 'created_by' => $user_id, 'company_id' => $company_id, 'created_on' => date('Y-m-d'));
            if ($values['type'] == 2):
                $data['bill_no'] = $this->getBillNo();
                $data['type'] = 2;
            endif;
            $res = $this->insert($data);
            return $res;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getDetails($transaction_id) {
        try {

            $this->db->select('c.transaction_id,dc_no,dispatched_to,order_no,discount_per,likely_payment_mode_id,freight_charges,transport_charges,payment_type,c.created_on,type_id,tax_type,transaction_date,tt.tax_type_id,tax_percent,business_contact_name,tin_no,cst_no,type,business_contact_email,business_contact_mobile,bill_no,due_date,payment_notes,b.business_contact_id,user_id,address,mode_name,dl_no,gstin,ref_by');
            $this->db->from($this->_table . " c")->join('business_contacts b', 'c.business_contact_id=b.business_contact_id')->join("transaction_taxes tt", "c.transaction_id=tt.transaction_id", "left")->join("tax_types ty", "tt.tax_type_id=ty.tax_type_id", "left")->join("payment_modes pm", "c.likely_payment_mode_id=pm.mode_id", "left");
            $this->db->where("c.transaction_id", $transaction_id);

            $res = $this->db->get();

            $res_array = $res->row_array();
            $res_array['created_on'] = date('d-m-Y', strtotime($res_array['created_on']));
            return $res_array;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function fetch($values) {
        $company_id = get_company_id();
        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        $from_date_field = $values['from_date_field'];
        $to_date_field = $values['to_date_field'];

        unset($values['pageIndex']);
        unset($values['pageSize']);
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
        $this->db->select('COUNT(*) as total');
        $this->db->from($this->_table . " c");
        $this->db->where("is_active", 1)->where("company_id=$company_id");
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;
        if ($from_date_field != "" && $to_date_field != ""):
            $this->db->where("transaction_date >=", date("Y-m-d", strtotime($from_date_field)));
            $this->db->where("transaction_date <=", date("Y-m-d", strtotime($to_date_field)));
        endif;
        foreach ($values as $key => $value) {

            if ($value) {
                if ($key == "business_contact_id" || $key == "created_by" || $key == "user_id"):
                    $this->db->where($key, $value);
                else:
                    $this->db->WHERE("$key LIKE '%$value%'");
                endif;
            }
        }
        $count = $this->db->get();
        $count_array = $count->row_array();

        $res['itemsCount'] = $count_array['total'];
        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('c.*');
        $this->db->from($this->_table . " c");
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        else:
            $this->db->order_by("transaction_id", "desc");
        endif;
        if ($from_date_field != "" && $to_date_field != ""):
            $this->db->where("transaction_date >=", date("Y-m-d", strtotime($from_date_field)));
            $this->db->where("transaction_date <=", date("Y-m-d", strtotime($to_date_field)));
        endif;
        foreach ($values as $key => $value) {
            if ($value) {
                if ($key == "business_contact_id" || $key == "created_by" || $key == "user_id"):
                    $this->db->where($key, $value);
                else:
                    $this->db->where("$key LIKE '%$value%'");
                endif;
            }
        }
        $this->db->where("is_active", 1)->where("company_id=$company_id");
        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();
        // $this->db->last_query();

        $result = $result_set->result_array();
        $this->load->model("Transaction_products_model");
        $total_value = 0;
        if ($start_index == 0):
            $start_index = 1;
        endif;
        $sn_no = $start_index;
        foreach ($result as $key => $value) {
            if (date('Y', strtotime($result[$key]['due_date'])) > 2015) {
                $result[$key]['due_date'] = date('d-m-Y', strtotime($result[$key]['due_date']));
            } else {
                $result[$key]['due_date'] = "";
            }
            $business_contact_id = $value['business_contact_id'];
            $result[$key]['transaction_date'] = date('d-m-Y', strtotime($result[$key]['transaction_date']));
            if ($value['type'] == 1):
                $type = "purchase";
            elseif ($value['type'] == 2):
                $type = "sales";
            endif;
            $transaction_id = $value['transaction_id'];
            // $status = $this->getPaymentStatus($transaction_id);
            $status = 1;
            $result[$key]['payment_status'] = $status;
            $link_url = "<a href='" . base_url('index.php/billing-users/transactions/index/') . "/$type/" . $transaction_id . "' class='btn btn-success btn-sm edit-btn'><i class='fa fa-pencil '></i></a>";
            if ($type == "sales"):
                $link_url .= "<a href='" . base_url('index.php/billing-users/transactions/print_receipt/') . "/" . $transaction_id . "' class='btn btn-primary btn-sm edit-btn'><i class='fa fa-print '></i></a>";

            //  $link_url .= "<a href='" . base_url('index.php/billing-users/payments/sales') . "/" . $transaction_id . "' class='btn btn-primary btn-sm edit-btn'><i class='fa fa-credit-card '></i></a>";
            endif;
            if ($type == "purchase"):
            //    $link_url .= "<a href='" . base_url('index.php/billing-users/payments/purchase') . "/" . $transaction_id . "' class='btn btn-primary btn-sm edit-btn'><i class='fa fa-credit-card '></i></a>";
            endif;

            if ($status != 1) {
                
            }
            $result[$key]['bill_value'] = round($this->Transaction_products_model->getTotal($transaction_id), 2);
            $result[$key]['sno'] = $sn_no;
            $total_value = $total_value + $result[$key]['bill_value'];
            $result[$key]['edit_btn'] = $link_url;
            $sn_no++;
        }
        $res['data'] = $result;
        $res['total_bill_value'] = round($total_value, 2);

        return $res;
    }

    public function getSales($values) {
        try {
            $business_contact_id = $values['business_contact_id'];
            $from_date_field = date("Y-m-d", strtotime($values['date_from']));
            $to_date_field = date("Y-m-d", strtotime($values['date_to']));

            $this->db->select('t.transaction_id,transaction_date,bill_no', FALSE)
                    ->from('transactions t')->where("type", 2)->where("t.business_contact_id", $business_contact_id)->where("t.is_active", 1)->order_by("transaction_date", "desc")->order_by("transaction_id", "asc");

            if ($from_date_field != "" && $to_date_field != ""):
                $this->db->where("t.transaction_date >=", date("Y-m-d", strtotime($from_date_field)));
                $this->db->where("t.transaction_date <=", date("Y-m-d", strtotime($to_date_field)));
            endif;

            $res = $this->db->get()->result_array();
            $this->load->model("Transaction_products_model");
            foreach ($res as $key => $sales):
                $transaction_id = $sales['transaction_id'];
                $res[$key]['bill_value'] = round($this->Transaction_products_model->getTotal($transaction_id), 2);
            endforeach;
            return $res;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function fetch_detailed($values) {
        $company_id = get_company_id();
        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        $from_date_field = $values['from_date_field'];
        $to_date_field = $values['to_date_field'];

        unset($values['pageIndex']);
        unset($values['pageSize']);
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
        $this->db->select('COUNT(*) as total');
        $this->db->from($this->_table . " c");
        $this->db->where("is_active", 1)->where("company_id=$company_id");
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;
        if ($from_date_field != "" && $to_date_field != ""):
            $this->db->where("transaction_date >=", date("Y-m-d", strtotime($from_date_field)));
            $this->db->where("transaction_date <=", date("Y-m-d", strtotime($to_date_field)));
        endif;
        foreach ($values as $key => $value) {

            if ($value) {
                if ($key == "business_contact_id" || $key == "created_by" || $key == "user_id"):
                    $this->db->where($key, $value);
                else:
                    $this->db->WHERE("$key LIKE '%$value%'");
                endif;
            }
        }
        $count = $this->db->get();

        $count_array = $count->row_array();

        $res['itemsCount'] = $count_array['total'];
        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('c.*');
        $this->db->from($this->_table . " c");
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;
        if ($from_date_field != "" && $to_date_field != ""):
            $this->db->where("transaction_date >=", date("Y-m-d", strtotime($from_date_field)));
            $this->db->where("transaction_date <=", date("Y-m-d", strtotime($to_date_field)));
        endif;
        foreach ($values as $key => $value) {
            if ($value) {
                if ($key == "business_contact_id" || $key == "created_by" || $key == "user_id"):
                    $this->db->where($key, $value);
                else:
                    $this->db->where("$key LIKE '%$value%'");
                endif;
            }
        }
        $this->db->where("is_active", 1)->where("company_id=$company_id");
        $this->db->limit($page_size, $start_index);




        $result_set = $this->db->get();
        // $this->db->last_query();

        $result = $result_set->result_array();
        $this->load->model("Transaction_products_model");
        $total_value = 0;
        if ($start_index == 0):
            $start_index = 1;
        endif;
        $sn_no = $start_index;
        foreach ($result as $key => $value) {
            $transaction_id = $value['transaction_id'];
            $status = $this->getPaymentStatusDetailed($transaction_id);
            $payment_list = $this->getPaymentList($transaction_id);
            $payment_list = $this->formatList($payment_list, $status['bill_value']);
            $result[$key]['due_date'] = date('d-m-Y', strtotime($result[$key]['due_date']));
            $business_contact_id = $value['business_contact_id'];
            $result[$key]['transaction_date'] = date('d-m-Y', strtotime($result[$key]['transaction_date']));
            if ($value['type'] == 1):
                $type = "purchase";
            elseif ($value['type'] == 2):
                $type = "sales";
            endif;


            $result[$key]['payment_status'] = $status['payment_status'];
            $link_url = "<a href='" . base_url('index.php/billing-users/transactions/index/') . "/$type/" . $transaction_id . "' class='btn btn-success btn-sm edit-btn'><i class='fa fa-pencil '></i></a>";
            if ($type == "sales"):
                $link_url .= "<a href='" . base_url('index.php/billing-users/transactions/print_receipt/') . "/" . $transaction_id . "' class='btn btn-primary btn-sm edit-btn'><i class='fa fa-print '></i></a>";

                $link_url .= "<a href='" . base_url('index.php/billing-users/payments/sales') . "/" . $transaction_id . "' class='btn btn-primary btn-sm edit-btn'><i class='fa fa-credit-card '></i></a>";
            endif;
            if ($type == "purchase"):
                $link_url .= "<a href='" . base_url('index.php/billing-users/payments/purchase') . "/" . $transaction_id . "' class='btn btn-primary btn-sm edit-btn'><i class='fa fa-credit-card '></i></a>";
            endif;
            if ($status['payment_status'] != 1):
                $link_url .= "<a class='btn btn-primary btn-sm edit-btn adjust_advance' data-contact-id=" . $business_contact_id . " data-transaction-id=" . $transaction_id . "><i class='fa fa-briefcase '></i></a>";
            endif;
            $result[$key]['bill_value'] = $status['bill_value'];
            $result[$key]['receivable'] = round(($status['bill_value'] - $status['total_paid_checked']));
            $result[$key]['payment_details'] = $payment_list;
            $result[$key]['sno'] = $sn_no;
            $total_value = $total_value + $result[$key]['bill_value'];
            $result[$key]['edit_btn'] = $link_url;
            $sn_no++;
        }
        $res['data'] = $result;
        $res['total_bill_value'] = round($total_value, 2);

        return $res;
    }

    public function formatList($payment_list, $bill_value) {
        foreach ($payment_list as $key => $value) {
            $value['amount'] = round($value['amount'], 2);
            $bill_value = round($bill_value - $value['amount'], 2);
            $payment_list[$key]['balance'] = $bill_value;
            if ($value['pdc_dated'] != "") {
                $payment_list[$key]['pdc_dated'] = date('d-m-Y', strtotime($value['pdc_dated']));
            }
            if ($value['issued_date'] != "") {
                $payment_list[$key]['issued_date'] = date('d-m-Y', strtotime($value['issued_date']));
            }


            if ($value['stmt_checked'] == 1):
                $payment_list[$key]['stmt_checked'] = "Yes";
            else:
                $payment_list[$key]['stmt_checked'] = "No";
            endif;
            if ($value['mgmt_checked'] == 1):
                $payment_list[$key]['mgmt_checked'] = "Yes";
            else:
                $payment_list[$key]['mgmt_checked'] = "No";
            endif;
        }
        return $payment_list;
    }

    public function getPaymentList($transaction_id) {
        $this->load->model('Payments_model');
        $res = $this->Payments_model->getPaidDetails($transaction_id);
        return $res;
    }

    public function getPaymentStatus($transaction_id) {
        try {

            $this->load->model('Payments_model');
            $res = $this->Payments_model->getPaid($transaction_id);
            $res_checked = $this->Payments_model->getPaid($transaction_id, 0, 1);

            if ($res > 0):
                $this->load->model('Transaction_products_model');
                $total = $this->Transaction_products_model->getNamesAndSum($transaction_id);

                if ($res >= $total['total']):

                    if ($res_checked >= $total['total']):
                        return 1;
                    endif;
                    return 2;
                else:
                    return 2;
                endif;

            endif;
            return 0;
        } catch (Exception $e) {
            
        }
    }

    public function getPaymentStatusDetailed($transaction_id) {
        try {
            $this->load->model('Payments_model');
            $res = $this->Payments_model->getPaid($transaction_id);
            $res_checked = $this->Payments_model->getPaid($transaction_id, 0, 1);
            $this->load->model('Transaction_products_model');
            $total = $this->Transaction_products_model->getNamesAndSum($transaction_id);
            if ($res > 0):


                if ($res >= $total['total']):

                    if ($res_checked >= $total['total']):
                        return array("payment_status" => 1, "total_paid_checked" => $res_checked, "total_paid" => $res, "bill_value" => $total['total']);
                    endif;
                    return array("payment_status" => 2, "total_paid_checked" => $res_checked, "total_paid" => $res, "bill_value" => $total['total']);
                else:
                    return array("payment_status" => 2, "total_paid_checked" => $res_checked, "total_paid" => $res, "bill_value" => $total['total']);
                endif;

            endif;
            return array("payment_status" => 0, "total_paid_checked" => $res_checked, "total_paid" => $res, "bill_value" => $total['total']);
        } catch (Exception $e) {
            
        }
    }

    public function getOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('product_id,product_name')->from($this->_table)->where("company_id=$company_id")->get();


        $result = $res->result_array();
        array_unshift($result, array('product_id' => "", "product_name" => "Select a company"));
        return $result;
    }

    public function save($values) {

        $data = array(
            'bill_no' => $values['bill_no'],
            'payment_notes' => $values['payment_notes'],
            'transaction_date' => date('Y-m-d', strtotime($values['transaction_date'])),
            'business_contact_id' => $values['business_contact_id'],
            'is_active' => 1,
            'type' => $values['type'],
            'transport_charges' => $values['transport_charges'],
            'freight_charges' => $values['freight_charges']
        );
        if (isset($values['dc_no'])):
            $data['dc_no'] = $values['dc_no'];
        endif;
        if (isset($values['dispatched_to'])):
            $data['dispatched_to'] = $values['dispatched_to'];
        endif;
        if (isset($values['order_no'])):
            $data['order_no'] = $values['order_no'];
        endif;
        if (isset($values['due_date'])):
            if ($values['due_date'] != ""):
                $data['due_date'] = date('Y-m-d', strtotime($values['due_date']));
            endif;
        else:
            if (isset($values['payment_type'])):
                $payment_type = intval($values['payment_type']);
                $due_date = date('Y-m-d', strtotime("+$payment_type days"));

                $data['payment_type'] = $values['payment_type'];
                $data['due_date'] = $due_date;
            endif;
            if (isset($values['discount_percentage'])):

                $data['discount_per'] = $values['discount_percentage'];
            endif;
        endif;
        if (isset($values['likely_payment_mode_id'])):
            $data['likely_payment_mode_id'] = intval($values['likely_payment_mode_id']);

        endif;
        if (isset($values['user_id'])):
            if ($values['user_id'] > 0):
                $data['user_id'] = $values['user_id'];
            endif;
        endif;


        if ($values['type'] == 2):
            unset($data['bill_no']);
        endif;
        $this->update($values['transaction_id'], $data);

        if (isset($values['tax_type_id']) && isset($values['tax_percent'])):
            if ($values['tax_percent'] != ""):
                $this->load->model('Transaction_taxes');
                $this->Transaction_taxes->save($values, $values['transaction_id']);
            endif;
        endif;
        return $values['transaction_id'];
    }

    public function update_data($values) {
        $id = $values['product_id'];
        unset($values['product_id']);

        return $this->update($id, $values);
    }

    public function delete_data($values) {
        $id = $values['transaction_id'];

        $this->db->where("transaction_id", $id);
        return $this->db->delete($this->_table);
    }

    public function checkEdited($transactioin_id) {
        try {
            $this->db->select('t.created_on,t.company_id,transaction_type,company_mobile,transaction_id')->from($this->_table . " t")->join("transaction_types ty", "t.type=ty.transaction_type")->join('companies c', "t.company_id=c.company_id")->where("transaction_id", $transactioin_id);

            $res = $this->db->get();
            $result = $res->row_array();
            $date_diff = date_diff(date_create($result['created_on']), date_create(date('Y-m-d')));
            if ($date_diff->d > 1) {
                return $this->sendNotification($result);
            }
            return true;
        } catch (Exception $e) {
            
        }
    }

    public function getDiscountPercent($transaction_id) {
        try {
            $res = $this->db->select("discount_per")->from($this->_table)->where("transaction_id", $transaction_id)->get();

            $result = $res->row_array();

            return $result['discount_per'];
        } catch (Exception $e) {
            
        }
    }

    public function sendNotification($values) {
        $sname = get_session_data('first_name');
        $msg = "Your " . $values['transaction_type'] . " #" . " " . $values['transaction_id'] . " has been edited by " . $sname;
        $this->sendSMS(array($values['company_mobile']), $msg);
    }

    public function sendSMS($mobile_nos, $msg) {
        $mob_num = implode(",", $mobile_nos);

        $mob_msg = urlencode($msg);

        $url = "http://203.212.70.200/smpp/sendsms?username=manchester&password=Manche@123&to=$mob_num&from=MISCBE&text=$mob_msg";

// Get cURL resource
        $curl = curl_init();
// Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'MIS'
        ));
// Send the request & save response to $resp
        //
          
          if (!curl_exec($curl)) {
            die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }

        curl_close($curl);

        return true;
    }

    public function getBillNo() {
        try {
            $this->db->select("COUNT(*)as total")->from($this->_table)->where("is_active", 1)->where("type", 2);

            $res = $this->db->get();
            $res_array = $res->row_array();
            $b= $res_array['total'] + 1-297-674;
$num=sprintf('%03d', $b);
return $num;

        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getOptionsAdjust($search_term) {
        try {
            $this->db->select("transaction_id,b.business_contact_name")->from($this->_table . " t")->join("business_contacts b", "t.business_contact_id=b.business_contact_id")->where("t.business_contact_id", $search_term['bcid'])->like("transaction_id", $search_term['q'])->or_like("b.business_contact_name", $search_term['q']);
            $res = $this->db->get();
            $result = $res->result_array();
            $options = array();
            foreach ($result as $key => $value) {
                //     $stock = $this->getBatchStock($result[$key]['transaction_product_id']);
                //
                 
                    $options[$key]['id'] = $value['transaction_id'];
                $options[$key]['text'] = $value['transaction_id'] . "-" . $value['business_contact_name'];
            }
            return $options;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function deleteInactive() {
        $this->db->where("is_active", 0);
        $this->db->where("created_by", get_session_data('user_id'));
        $this->db->delete($this->_table);
    }

}
