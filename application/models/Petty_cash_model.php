<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Petty_cash_model extends MY_Model {

    public $_table = 'petty_cash';
    protected $primary_key = 'petty_cash_id';
    public $return_type = 'array';

    public function getBalance($date, $petty_cash_id) {
        try {
            $receipts_sum = $this->getSum($date, 2, $petty_cash_id);
            $payments_sum = $this->getSum($date, 1, $petty_cash_id);

            $balance = $receipts_sum - $payments_sum;
            return round($balance);
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo $e->getLine();
            echo $e->getFile();
            // Other code to recover from the error
        }
    }

    public function getSum($date = null, $transaction_type = 1, $petty_cash_id) {
        // Transaction type=1 is PAyments 2 is Receipts

        $this->db->select('SUM(amt) as total')->from($this->_table)->where("transaction_type", $transaction_type)->where("petty_cash_id <=", $petty_cash_id)->where("company_id", get_company_id());

        if ($date != null):
            $this->db->where("date <= '$date'");
        endif;

        $res = $this->db->get();
        $opt = $res->row_array();
        if (count($opt) == 1) {
            return $opt['total'];
        } else {
            return 0;
        }
    }

    public function operations($values) {
        try {

            if ($values['oper'] == 'del') {
                echo "Deleting.. ";

                $id = explode(",", $values['id']);

//$ids=implode(",",$ids);
                $this->db->where_in("petty_cash_id", $id);
                $this->db->delete($this->_table);
                echo $this->db->last_query();
                exit;
                echo "Deleted Successfully";
                return true;
            }
            $date = date('Y-m-d', strtotime($values['date']));




            $values['transaction_type'] = 1;
            if ($values['oper'] == 'add') {  // Add Event
                $data = array(
                    'date' => $date,
                    'amt' => $values['amt'],
                    'transaction_type' => $values['transaction_type'],
                    'created_on' => date('Y-m-d H:i:s'),
                    'user_id' => get_session_data('user_id'),
                    'company_id' => get_company_id()
                );

                $insert = $this->insert($data);

                if ($values['transaction_type'] == 1):



                    $this->load->model('Petty_cash_details_model');

                    $res = $this->Petty_cash_details_model->save($values, $insert);
                endif;
                if ($insert) {
                    echo "Successfully Inserted";
                } else {
                    echo "Error In Adding. Try Again.";
                }
            } elseif ($values['oper'] == 'edit') {  // Edit Task Details
                $id = $values['id'];

                $data = array(
                    'date' => $date,
                    'amt' => $values['amt'],
                    'transaction_type' => $values['transaction_type'],
                    'user_id' => get_session_data('user_id')
                );

                $update = $this->update($id, $data);

                if ($values['transaction_type'] == 1):
                    // Get Paid To Details


                    $this->load->model('Petty_cash_details_model');

                    $res = $this->Petty_cash_details_model->save($values, $values['id']);

                endif;

                echo "Successfully Edited";
            }

            elseif (isset($values['_search']) && $values['_search'] == true) {
                $search_field = $values['searchField'];
                $searchString = $values['searchString'];

                if ($values['searchOper'] == 'cn') {
                    echo "Search is under development";
                }
            }
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo $e->getLine();
            echo $e->getFile();
            // Other code to recover from the error
        }
    }

    public function fetch($values) {

        try {
            $page = $values['page']; // get the requested page
            $limit = $values['rows']; // get how many rows we want to have into the grid
            $sidx = $values['sidx']; // get index row - i.e. user click to sort
            $sord = $values['sord']; // get the direction
            if ($values['from_date'] != "" && $values['to_date'] != ""):
                $from_date = date('Y-m-d', strtotime($values['from_date']));
                $to_date = date('Y-m-d', strtotime($values['to_date']));
            endif;

            if (!$sidx):
                $sidx = 1;
            endif;





            $this->db->select('COUNT(*) AS total')->from($this->_table . " p")
                    ->join("petty_cash_paid_details pc", 'p.petty_cash_id=pc.petty_cash_id', 'left')
                    ->join("users u", 'p.user_id=u.user_id', 'left')
                    ->where("company_id", get_company_id());


            if (isset($values['_search']) && $values['_search'] != false && isset($values['filters'])):

                $items = json_decode($values['filters'], true);
                $rules = $items['rules'];
                if (is_array($rules)):
                    foreach ($rules as $value_search) {

                        $fields = $value_search['field'];
                        $field = str_replace('_view', '', $fields);
                        if ($field == "event_type"):

                        else:
                            $data = $value_search['data'];
                            if (!is_numeric($data)):
                                $this->db->like($field, $data);
                            //   $count->where("$field like '%$data%'");
                            else:
                                $this->db->where($field, $data);
                            endif;
                        endif;
                    }

                endif;
            endif;
            if (isset($from_date)):
                $this->db->where("date >= '$from_date'");
                $this->db->where("date <= '$to_date'");

            endif;

            $res = $this->db->get();
            $result_array = $res->row_array();
            $no_of_rows_array = $result_array['total'];
            $no_of_rows = $no_of_rows_array;

            if ($no_of_rows > 0) {
                $total_pages = round($no_of_rows / $limit, 2);
            } else {
                $total_pages = 0;
            }

            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit; // do not put $limit*($page - 1)
            if ($start < 0):
                $start = 0;
            endif;

            // Check User Dept in petty cash  
            $this->db->select('p.*,contact_id,head_id,contact_id,notes,doc_no,first_name')->from($this->_table . " p")
                    ->join("petty_cash_paid_details pc", 'p.petty_cash_id=pc.petty_cash_id', 'left')
                    ->join("users u", 'p.user_id=u.user_id', 'left')
                    ->where("company_id", get_company_id())->limit($limit, $start)->order_by("$sidx $sord");

            if (isset($values['_search']) && $values['_search'] != false && isset($values['filters'])):

                $items = json_decode($values['filters'], true);
                $rules = $items['rules'];
                if (is_array($rules)):
                    foreach ($rules as $value_search) {

                        $fields = $value_search['field'];
                        $field = str_replace('_view', '', $fields);
                        if ($field == "event_type"):

                        else:
                            $data = $value_search['data'];
                            if (!is_numeric($data)):
                                $this->db->like($field, $data);
                            //   $count->where("$field like '%$data%'");
                            else:
                                $this->db->where($field, $data);
                            endif;
                        endif;
                    }

                endif;
            endif;
            if (isset($from_date)):
                $this->db->where("date >= '$from_date'");
                $this->db->where("date <= '$to_date'");

            endif;
            $res = $this->db->get();
            $com_details = $res->result_array();

            $responce = new stdClass();
            $responce->page = $page;
            $responce->total = $total_pages;
            $responce->records = $no_of_rows;
            $i = 0;
            $prev_date = "";
            $total = 0;
            foreach ($com_details as $row) {




                // $receipts
                $receipts = "";
                $paid_rs = "";
                if ($row['transaction_type'] == 1):
                    $paid_rs = $row['amt'];

                elseif ($row['transaction_type'] == 2):
                    $receipts = $row['amt'];
                endif;

                // get Balance


                $total = $total + $paid_rs;
                $responce->rows[$i]['id'] = $row['petty_cash_id'];
                $responce->rows[$i]['cell'] = array($row['petty_cash_id'], date("d-M-y", strtotime($row['date'])), $paid_rs, $row['head_id'], $row['contact_id'], $row['notes'], $row['first_name']);
                $prev_date = $row['date'];
                $i++;
            }
            $responce->rows[$i]['cell'] = array("", "Total", $total, "", "", "", "");

            return json_encode($responce);
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo "Message: " . $e->getLine() . "\n";

            // Other code to recover from the error
        }
    }

}
