<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {

        parent::__construct();
        if (!is_user_logged_in()):
            redirect(site_url());
        endif;
    }

    public function gst_report() {
        $data = array();


        $data['page_title'] = "Customer Report";

        $data['result_array'] = array();
        $values = array("date_from" => date('d-m-Y'), "date_to" => date("d-m-Y"));
         $data['date_from'] = date('d-m-Y');
            $data['date_to'] = date('d-m-Y');
        if ($this->input->post(null, true)):

            $values = $this->input->post(null, true);
            $data['date_to'] = $this->input->post("date_to");
            $data['date_from'] = $this->input->post("date_from");
                $values =$data;
        endif;
        $type = $this->input->get('type');
        $this->load->model("Transactions_model");
        $res = $this->Transactions_model->getGSTStatementDetails($values, $type);
        $data['result_array'] = $res;







        $this->_loadView('billing-users/reports/gst_report', $data, 'Products');
    }

    public function customer_report() {
        $data = array();
        $data['selected_business_contact_id'] = "";
        $data['date_to'] = "";
        $data['page_title'] = "Customer Report";
        $data['date_from'] = "";
        if ($this->input->post("business_contact_id")):
            $data['selected_business_contact_id'] = $this->input->post("business_contact_id");

            $values = $this->input->post(null, true);
            $this->load->model("Transactions_model");
            $res = $this->Transactions_model->getStatementDetails($values);
            $data['result_array'] = $res['result'];
            $data['balance'] = $res['balance'];
            $data['date_to'] = $this->input->post("date_to");
            $data['date_from'] = $this->input->post("date_from");
        endif;

        $this->load->model("Business_contacts_model");
        $business_contact_options = $this->Business_contacts_model->getDropdownOptions();
        $data['business_contact_options'] = $business_contact_options;

        $this->_loadView('billing-users/reports/customer_report', $data, 'Products');
    }

    public function supplier_report() {
        $data = array();
        $data['selected_business_contact_id'] = "";
        $data['date_to'] = "";
        $data['page_title'] = "Supplier Report";
        $data['date_from'] = "";
        if ($this->input->post("business_contact_id")):
            $data['selected_business_contact_id'] = $this->input->post("business_contact_id");

            $values = $this->input->post(null, true);
            $this->load->model("Transactions_model");
            $res = $this->Transactions_model->getStatementDetails($values);
            $data['result_array'] = $res['result'];
            $data['balance'] = $res['balance'];
            $data['date_to'] = $this->input->post("date_to");
            $data['date_from'] = $this->input->post("date_from");
        endif;

        $this->load->model("Business_contacts_model");
        $business_contact_options = $this->Business_contacts_model->getDropdownOptions(2);
        $data['business_contact_options'] = $business_contact_options;

        $this->_loadView('billing-users/reports/customer_report', $data, 'Products');
    }

    public function overall_balance() {
        $data = array();
        if ($this->input->get("type")):
            $type = intval($this->input->get("type"));
        endif;
        if ($type == 1):
            $data['page_title'] = "Sales Report";
        endif;
        if ($type == 2):
            $data['page_title'] = "Purchase Report";
        endif;
        $this->load->model("Business_contacts_model");
        $business_contact_options = $this->Business_contacts_model->getDropdownOptions($type);
        $this->load->model("Transactions_model");
        $return_array = array();
        foreach ($business_contact_options as $key => $business_contact):

            $return_array[$key]['name'] = $business_contact;

            $current_balance = $this->Transactions_model->getCurrentBalance(date('Y-m-d'), $key);
            $return_array[$key]['balance'] = $current_balance;

        endforeach;

        $data['balance_list'] = $return_array;





        $this->_loadView('billing-users/reports/overall_balance', $data, 'Products');
    }

    public function products() {
        $data = array();

        $this->_loadView('billing-users/reports/products', $data, 'Products');
    }

    public function sales() {
        $data = array();

        $this->_loadView('billing-users/reports/sales', $data, 'Sales');
    }

    public function data_sales_list() {

// Check if League id is passed
        $values = $this->input->get(null, true);
        $from_date_field = $values['from_date_field'];
        $to_date_field = $values['to_date_field'];

        $this->load->library('Datatables');
        $this->datatables->select('t.transaction_id,DATE_FORMAT(transaction_date, "%d-%m -%Y") AS transaction_date,product_name,bill_no,ROUND(((tp.length*tp.width/144)*price_per_unit) * qty) AS value,length,width,qty,price_per_unit,business_contact_name', FALSE)
                ->from('transactions t')->join("transaction_products tp", "t.transaction_id=tp.transaction_id")->join("business_contacts c", "t.business_contact_id=c.business_contact_id")->join("products p", "tp.product_id=p.product_id")->where("type", 2)->where("t.is_active", 1);

        if ($from_date_field != "" && $to_date_field != ""):
            $this->datatables->where("t.transaction_date >=", date("Y-m-d", strtotime($from_date_field)));
            $this->datatables->where("t.transaction_date <=", date("Y-m-d", strtotime($to_date_field)));
        endif;
        if ($this->input->get('columns')) {
            foreach ($this->input->get('columns') as $val) {
                $column_name = "";
                if ($val['searchable'] == true && $val['search']['value'] != ""):
                    $column_name = $val['name'];
                    $search_value = $val['search']['value'];

                    $this->datatables->where("$column_name LIKE '%$search_value%'");



                endif;
            }
        }




        $data = $this->datatables->generate();
        echo $data;
        exit;
    }

    public function data_purchase() {




        $this->load->library('Datatables');
        $this->datatables->select('transaction_id,transaction_date,u.first_name,bill_no,transport_charges,due_date,payment_notes,business_contact_name', FALSE)
                ->from('transactions t')->join("users u", "t.user_id=u.user_id", "left")->join("business_contacts c", "t.business_contact_id=c.business_contact_id")->where("type", 1);


        if ($this->input->get('columns')) {
            foreach ($this->input->get('columns') as $val) {
                $column_name = "";
                if ($val['searchable'] == true && $val['search']['value'] != ""):
                    $column_name = $val['name'];
                    $search_value = $val['search']['value'];

                    $this->datatables->where("$column_name LIKE '%$search_value%'");



                endif;
            }
        }



        //  ->where("llp.LeagueID",$league_id);
//league_leagueplayers
        $data = $this->datatables->generate();

        $data_array = json_decode($data);
        $this->load->model('Transaction_products_model');
        for ($i = 0; $i < count($data_array->data); $i++) {
            $bill_value = round($this->Transaction_products_model->getTotal($data_array->data[$i]->transaction_id), 2);
            $data_array->data[$i]->bill_value = $bill_value;
        }



        echo json_encode($data_array);
        exit;
    }

}
