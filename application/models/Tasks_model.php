<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tasks_model extends MY_Model {

    public $_table = 'tasks';
    protected $primary_key = 'task_id';
    public $return_type = 'array';

    public function operations($values) {
        try {

            if ($values['oper'] == 'del') {
                echo "Deleting.. ";

                $id = explode(",", $values['id']);

//$ids=implode(",",$ids);
                $this->db->where_in("task_id", $id);
                $this->db->delete($this->_table);
                echo $this->db->last_query();
                exit;
                echo "Deleted Successfully";
                return true;
            }
          





            if ($values['oper'] == 'add') {  // Add Event
                $data = array(
                    'task_name' => $values['task_name'],
                    'task_desc' => $values['task_desc'],
                    'task_created_time' => date('Y-m-d H:i:s'),
                    'task_created_user' => get_session_data('user_id'),
                    'task_score' => $values['task_score'],
                    'recurring_times' => $values['recurring_times'],
                    'recurring_type' => $values['recurring_type'],
                    'send_sms' => $values['send_sms']
                );

                $task_id = $this->insert($data);

                if ($task_id) {
                    $this->saveTaskDates($values, $task_id);
                } else {
                    echo "Error In Adding. Try Again.";
                }
            } elseif ($values['oper'] == 'edit') {  // Edit Task Details
                $id = intval($values['id']);

                $data = array(
                    'date' => $date,
                    'amt' => $values['amt'],
                    'transaction_type' => $values['transaction_type'],
                );

                $update = $this->update($id, $data);

                if ($values['transaction_type'] == 1):
                    // Get Paid To Details
                    $contact_id = $values['contact_id'][0]['id'];

                    if (!intval($contact_id) && !is_null($contact_id)):
                        $this->load->model('Company_contacts_model');
                        $contact_id = $this->Company_contacts_model->getContactID($contact_id);
                    endif;
                    if (isset($values['head_id'][0]['id'])):
                        $head_id = $values['head_id'][0]['id'];
                    else:
                        $head_id = null;
                    endif;
                    if (!intval($head_id) && !is_null($head_id)):
                        $this->load->model('Heads_model');

                        $head_id = $this->Heads_model->getHeadID($head_id, 1);
                    endif;
                    if (isset($values['sub_head_id'][0]['id'])):
                        $sub_head_id = $values['sub_head_id'][0]['id'];
                    else:
                        $sub_head_id = null;
                    endif;
                    if (!intval($sub_head_id) && !is_null($sub_head_id)):
                        $this->load->model('Heads_model');

                        $head_id = $this->Heads_model->getHeadID($head_id, 2);
                    endif;

                    // CHeck If Debit and Add in Details table
                    $data = array(
                        'contact_id' => $contact_id,
                        'head_id' => $head_id,
                        'sub_head_id' => $sub_head_id,
                        'notes' => $values['notes'],
                        'doc_no' => $values['doc_no']
                    );

                    $this->load->model('Petty_cash_details_model');
                    $res = $this->Petty_cash_details_model->save($data, $id);

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

    public function saveTaskDates($values, $task_id) {
        $from_date = date('Y-m-d', strtotime($values['task_from']));
        $to_date = date('Y-m-d', strtotime($values['task_to']));
          $this->load->model('Task_from_to');
        if ($values['recurring_type'] == ""):
         
            $this->Task_from_to->save($values, $task_id);
            
        else:
            if ($values['recurring_type'] == 1) {
                $type = "day";
            } elseif ($values['recurring_type'] == 2) {
                $type = "months";
            } elseif ($values['recurring_type'] == 3) {
                $type = "years";
            }
            for ($i = 1; $i < $values['recurring_times']; $i++):
                if ($values['recurring_type'] == 1):
                    $num =  7;

                else:
                    $num = $i;
                endif;
                $rec_from_date = strtotime("+$num $type", strtotime($from_date));
                $rec_to_date = strtotime("+$num $type", strtotime($to_date));
                $from_date=date('Y-m-d',$rec_from_date);
                 $to_date=date('Y-m-d',$rec_to_date);
               
                if ($rec_from_date > strtotime(date('Y-m-d'))):
                    $data = array(
                        'task_from' => date("Y-m-d",$rec_from_date),
                        'task_to' => date("Y-m-d",$rec_to_date)
                    );
                 $this->Task_from_to->save($data, $task_id);
                endif;
            endfor;

        endif;
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





            $this->db->select('COUNT(*) AS total')->from($this->_table . " p")->join("petty_cash_paid_details pc", 'p.task_id=pc.task_id', 'left');


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
                $total_pages = round($no_of_rows / $limit,2);
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
            $this->db->select('p.*,contact_id,head_id,sub_head_id,notes,doc_no')->from($this->_table . " p")->join("petty_cash_paid_details pc", 'p.task_id=pc.task_id', 'left')->limit($limit, $start)->order_by("$sidx $sord");

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
            foreach ($com_details as $row) {

                $contact_id = $row['contact_id'];
                $contact_id = "$contact_id";
                $head_id = $row['head_id'];
                $head_id = $head_id;
                $sub_head_id = $row['sub_head_id'];
                $sub_head_id = "$sub_head_id";

                // $receipts
                $receipts = "";
                $paid_rs = "";
                if ($row['transaction_type'] == 1):
                    $paid_rs = $row['amt'];

                elseif ($row['transaction_type'] == 2):
                    $receipts = $row['amt'];
                endif;

                // get Balance

                $balance = $this->getBalance($row['date'], $row['task_id']);

                $responce->rows[$i]['id'] = intval($row['task_id']);
                $responce->rows[$i]['cell'] = array($row['task_id'], date("d-M-y", strtotime($row['date'])), $row['dummy_id'], $paid_rs, $receipts, $row['amt'], $row['transaction_type'], $row['doc_no'], $contact_id, $row['head_id'], $sub_head_id, $row['notes'], $row['is_approved'], $balance);
                $prev_date = $row['date'];
                $i++;
            }

            return json_encode($responce);
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo "Message: " . $e->getLine() . "\n";

            // Other code to recover from the error
        }
    }

}
