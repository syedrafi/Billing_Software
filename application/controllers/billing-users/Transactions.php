<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends MY_Controller {

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
    }

    public function index() {
        $data = array();
        $this->deleteInactive();
        $data['can_edit'] = 1;
        if (get_session_data('user_role_id') == _VIEWER_ROLE_ID):
            $data['can_edit'] = 0;

        endif;
        $data['transaction_id'] = "";
        if ($this->uri->segment(5)):
            $data['transaction_id'] = $this->uri->segment(5);
            $this->load->model('transactions_model');
            $res = $this->transactions_model->getDetails($data['transaction_id']);
            $data['transaction_details'] = $res;

        endif;

        $this->load->model('Business_contacts_model');
        $res = $this->Business_contacts_model->getdropdownOptions(2);
        $data['contact_options'] = $res;
        /*
          $this->load->model('Business_contacts_model');
          $res = $this->Business_contacts_model->getdropdownOptions();
          $data['contact_options'] = $res;
         */

        $this->load->model('Users_model');
        $res = $this->Users_model->getdropdownOptions();
        $data['user_options'] = $res;
        $this->load->model('Business_types_model');
        $res = $this->Business_types_model->getdropdownOptionsCustom();
        $data['contact_type_options'] = $res;
        $this->load->model('Tax_types_model');
        $res = $this->Tax_types_model->getdropdownOptions();
        $data['tax_options'] = $res;

        $data['cgst_percent_options'] = json_encode(array(0 => array("percent" => "0%", "value" => floatval(0)), 1 => array("percent" => "2.5%", "value" => 2.5), 2 => array("percent" => "6%", "value" => 6), 3 => array("percent" => "9%", "value" => 9), 4 => array("percent" => "14%", "value" => 14)));
        $data['igst_percent_options'] = json_encode(array(0 => array("percent" => "0%", "value" => floatval(0)), 1 => array("percent" => "5%", "value" => floatval(5)), 2 => array("percent" => "12%", "value" => 12), 3 => array("percent" => "18%", "value" => 18), 4 => array("percent" => "28%", "value" => 28)));
        if ($this->uri->segment(4) == "purchase"):
            $this->load->model('Products_model');
            $res = $this->Products_model->getOptions();
            $data['product_options'] = json_encode($res);
            $data['title'] = "Purchase";
            $data['by_title'] = "Purchased by";
            $data['type'] = 1;
            $data['business_contact_heading'] = "Supplier";
            $this->_loadView('billing-users/transactions/index', $data, $data['title']);
        elseif ($this->uri->segment(4) == "sales"):
            $this->load->model('Business_contacts_model');
            $res = $this->Business_contacts_model->getdropdownOptions(1);
            $data['contact_options'] = $res;
            $this->load->model('Payment_modes_model');
            $res = $this->Payment_modes_model->getOptionsDropdown();
            $data['mode_options'] = $res;
            // Get bill No
            $this->load->model('Transactions_model');
            $bill_no = $this->Transactions_model->getBillNo();
            $data['title'] = "Sales";
            $data['bill_no'] = $bill_no;
            $data['type'] = 2;
            $data['by_title'] = "Sold by";
            $data['business_contact_heading'] = "Customer";
            $this->_loadView('billing-users/transactions/sales', $data, $data['title']);
        else:
            echo "Invalid";
            exit;
        endif;
    }

    public function add() {
        $data = array();

        $this->load->model('transactions_model');

        $res = $this->transactions_model->add($this->input->post(null, true));
        echo json_encode(array('id' => $res, 'msg' => 'success'));
    }

    public function fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Transaction_products_model');
        $res = $this->Transaction_products_model->fetch($values);
        echo json_encode($res);
        exit;
    }

    public function sales_fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Transaction_products_model');
        $res = $this->Transaction_products_model->fetch_sales($values);

        echo json_encode($res);
        exit;
    }

    public function save_transaction() {
        $data = array();

        $this->load->model('transactions_model');

        $res = $this->transactions_model->save($this->input->post(null, true));
        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function save() {
        $data = array();
        $values = $this->input->post(null, true);
        $this->load->model('Transaction_products_model');
        if ($values['type'] == 'update'):
            $res = $this->Transaction_products_model->update_data_purchase($this->input->post(null, true));
        elseif ($values['type'] == 'add'):
            $res = $this->Transaction_products_model->save($this->input->post(null, true));
        elseif ($values['type'] == 'delete'):
            $res = $this->Transaction_products_model->delete_data($this->input->post(null, true));
        endif;



        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function save_sales() {
        $data = array();
        $values = $this->input->post(null, true);
        $this->load->model('Transaction_products_model');
        if ($values['type'] == 'update'):
            $res = $this->Transaction_products_model->update_data($this->input->post(null, true));
        elseif ($values['type'] == 'add'):
            $res = $this->Transaction_products_model->save_sales($this->input->post(null, true));
        elseif ($values['type'] == 'delete'):
            $res = $this->Transaction_products_model->delete_data($this->input->post(null, true));
        endif;



        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function fetch_transactions() {

        $values = $this->input->get(null, TRUE);
        $this->load->model('transactions_model');
        $res = $this->transactions_model->fetch($values);

        echo json_encode($res);
        exit;
    }

    public function get_contacts() {
        if ($this->input->post('type_id')):
            //Hospital or Dealer
            $type_id = $this->input->post('type_id');

            // Supplier or Customer
            $business_type_id = $this->input->post('business_type');
            $values = $this->input->get(null, TRUE);
            $this->load->model('Business_contacts_model');
            $res = $this->Business_contacts_model->getFilteredContacts($type_id);

            echo json_encode($res);
            exit;
        endif;
    }

    public function transaction_list() {
        $data = array();
        if ($this->uri->segment(4)) {
            $trans_type = $this->uri->segment(4);
            if ($trans_type == "purchase"):
                $data['trans_type'] = 1;
                $data['title'] = "Purchase List";
                $data['by_title'] = "Purchased By";

                $data['freight_visible'] = true;
                $data['transport_visible'] = false;

                $data['business_contact_heading'] = "Supplier";
            else:
                $data['trans_type'] = 2;
                $data['title'] = "Sales List";
                $data['freight_visible'] = false;
                $data['transport_visible'] = true;
                $data['by_title'] = "Sold By";
                $data['business_contact_heading'] = "Customer";
            endif;
            $data['can_edit'] = 1;
            if (get_session_data('user_role_id') == _VIEWER_ROLE_ID):
                $data['can_edit'] = 0;

            endif;


            $this->load->model('Business_contacts_model');
            $res = $this->Business_contacts_model->getOptions();
            $data['business_contact_options'] = json_encode($res);

            $this->load->model('Business_contacts_model');
            $res = $this->Business_contacts_model->getOptions();
            $data['contact_options'] = json_encode($res);

            $this->load->model('Payment_modes_model');
            $res = $this->Payment_modes_model->getOptions();
            $data['mode_options'] = json_encode($res);

            $this->load->model('Users_model');
            $res = $this->Users_model->getOptions();
            $data['user_options'] = json_encode($res);




            $this->_loadView('billing-users/transactions/transaction_list', $data, 'Transactions list');
        }
    }

    public function delete() {
        $transaction_id_data = $this->input->post(null, true);

        if (isset($transaction_id_data['data']['transaction_id'])):
            $transaction_id = $transaction_id_data['data']['transaction_id'];
        else:
            $transaction_id = $transaction_id_data['transaction_id'];
        endif;
        $this->load->model('transactions_model');
        $this->transactions_model->delete_data(array('transaction_id' => $transaction_id));
        echo json_encode(array('msg' => 'Successfully deleted'));
        exit;
    }

    public function print_receipt() {

        if ($this->uri->segment(4)):
            $data = array();
            $transactioin_id = $this->uri->segment(4);
            $this->load->model('Transactions_model');
            $data['transaction_details'] = $this->Transactions_model->getDetails($transactioin_id);
            if ($data['transaction_details']['type'] == 1):
                echo "Invalid";
                exit;
            endif;
            $this->load->model('Companies_model');
            $data['company_details'] = $this->Companies_model->getDetails(get_company_id());


            $this->load->model('Transaction_products_model');
            $data['transaction_products'] = $this->Transaction_products_model->getDetails($transactioin_id);

            $this->_loadView('billing-users/transactions/print', $data, 'Print');

        endif;
    }

    public function fetch_sales_products() {
        if ($this->input->post('q')):
            $this->load->model('Transaction_products_model');
            $result = $this->Transaction_products_model->getOptions($this->input->post('q'));
            echo json_encode(array('items' => $result, 'total_count' => count($result)));

        endif;
    }

    public function fetch_purchase_products() {
        if ($this->input->post('q')):
            $this->load->model('Transaction_products_model');
            $result = $this->Transaction_products_model->getOptionsPurchase($this->input->post('q'));
            echo json_encode(array('items' => $result, 'total_count' => count($result)));

        endif;
    }

    public function get_price() {
        if ($this->input->post('transaction_product_id')):
            $transaction_product_id = $this->input->post('transaction_product_id');
            $this->load->model('Transaction_products_model');
            $result = $this->Transaction_products_model->getPrice($this->input->post(null, true));
            echo json_encode(array('batch_no' => $result['batch_no'], 'mfg_date' => $result['mfg_date'], 'expiry_date' => $result['expiry_date'], 'mrp' => $result['mrp']));
            exit;
        endif;
    }

    private function deleteInactive() {
        $this->load->model('Transactions_model');
        $res = $this->Transactions_model->deleteInactive();
    }

}
