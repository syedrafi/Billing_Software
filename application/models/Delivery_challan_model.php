<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Delivery_challan_model extends MY_Model {

    public $_table = 'delivery_challan';
    protected $primary_key = 'challan_id';
    public $return_type = 'array';

    public function add($values = array()) {
        try {
            $user_id = get_user_id();
            $company_id = get_company_id();
            $data = array('challan_date' => date('Y-m-d'), 'created_by' => $user_id, 'company_id' => $company_id, 'created_on' => date('Y-m-d'));
            $data['dc_no'] = $this->getBillNo();
            $res = $this->insert($data);
            return $res;
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }
    }

    public function getDetails($challan_id) {
        try {
            $this->db->select('c.challan_id,dc_no,dispatched_to,order_no,c.created_on,challan_date,business_contact_name,tin_no,cst_no,business_contact_email,business_contact_mobile,dc_no,b.business_contact_id,user_id,address,dl_no,gstin,ref_by');
            $this->db->from($this->_table . " c")->join('business_contacts b', 'c.business_contact_id=b.business_contact_id');
            $this->db->where("c.challan_id", $challan_id);
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
            $this->db->where("challan_date >=", date("Y-m-d", strtotime($from_date_field)));
            $this->db->where("challan_date <=", date("Y-m-d", strtotime($to_date_field)));
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
            $this->db->order_by("challan_id", "desc");
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
        $this->load->model("Challan_products_model");
        $total_value = 0;
        if ($start_index == 0):
            $start_index = 1;
        endif;
        $sn_no = $start_index;
        foreach ($result as $key => $value) {

            $business_contact_id = $value['business_contact_id'];
            $result[$key]['challan_date'] = date('d-m-Y', strtotime($result[$key]['challan_date']));
            $challan_id = $value['challan_id'];
            
            $link_url = "<a href='" . base_url('index.php/billing-users/delivery_challan/index/') . "/" . $challan_id . "' class='btn btn-success btn-sm edit-btn'><i class='fa fa-pencil '></i></a>";

            $link_url .= "<a href='" . base_url('index.php/billing-users/delivery_challan/print_receipt/') . "/" . $challan_id . "' class='btn btn-primary btn-sm edit-btn'><i class='fa fa-print '></i></a>";

            $result[$key]['challan_value'] = round($this->Challan_products_model->getTotal($challan_id), 2);
            $result[$key]['sno'] = $sn_no;
            $total_value = $total_value + $result[$key]['challan_value'];
            $result[$key]['edit_btn'] = $link_url;
            $sn_no++;
        }
        $res['data'] = $result;
        $res['total_dc_value'] = round($total_value, 2);

        return $res;
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
            'dc_no' => $values['dc_no'],
            'challan_date' => date('Y-m-d', strtotime($values['challan_date'])),
            'business_contact_id' => $values['business_contact_id'],
            'is_active' => 1,
        );

        if (isset($values['dispatched_to'])):
            $data['dispatched_to'] = $values['dispatched_to'];
        endif;
        if (isset($values['order_no'])):
            $data['order_no'] = $values['order_no'];
        endif;


        if (isset($values['user_id'])):
            if ($values['user_id'] > 0):
                $data['user_id'] = $values['user_id'];
            endif;
        endif;



        $this->update($values['challan_id'], $data);


        return $values['challan_id'];
    }

    public function update_data($values) {
        $id = $values['product_id'];
        unset($values['product_id']);

        return $this->update($id, $values);
    }

    public function delete_data($values) {
        $id = $values['challan_id'];

        $this->db->where("challan_id", $id);
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
            $this->db->select("COUNT(*)as total")->from($this->_table)->where("is_active", 1);


            $res = $this->db->get();
            $res_array = $res->row_array();
            return $res_array['total'] + 1;
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
