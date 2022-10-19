<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_challan extends MY_Controller {

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
        $data['return_options']=json_encode(array(0=>array("return_id"=>"","return_name"=>"Select"),1=>array('return_id' => "1", "return_name" => "Yes"),2=>array('return_id' => "0", "return_name" => "No")));
        $data['transaction_id'] = "";
        if ($this->uri->segment(4)):
            $data['challan_id'] = $this->uri->segment(4);
            $this->load->model('Delivery_challan_model');
            $res = $this->Delivery_challan_model->getDetails($data['challan_id']);
            $data['challan_details'] = $res;

        endif;
  
       $this->load->model('Business_contacts_model');
        $res = $this->Business_contacts_model->getdropdownOptions(1);
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

        $this->load->model('Payment_modes_model');
        $res = $this->Payment_modes_model->getOptionsDropdown();
        $data['mode_options'] = $res;
        // Get bill No
        $this->load->model('Delivery_challan_model');
        $bill_no = $this->Delivery_challan_model->getBillNo();
        $data['title'] = "Sales";
        $data['dc_no'] = $bill_no;
       
        $data['by_title'] = "Given by";
        $data['business_contact_heading'] = "Customer";
        $this->_loadView('billing-users/delivery_challan/index', $data, $data['title']);
    }

    public function add() {
        $data = array();

        $this->load->model('Delivery_challan_model');

        $res = $this->Delivery_challan_model->add($this->input->post(null, true));
        echo json_encode(array('id' => $res, 'msg' => 'success'));
    }

    public function fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Challan_products_model');
        $res = $this->Challan_products_model->fetch($values);
        echo json_encode($res);
        exit;
    }

    public function single_fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Challan_products_model');
        $res = $this->Challan_products_model->fetch_single($values);

        echo json_encode($res);
        exit;
    }

    public function save_challan() {
        $data = array();

        $this->load->model('Delivery_challan_model');

        $res = $this->Delivery_challan_model->save($this->input->post(null, true));
        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function save() {
        $data = array();
        $values = $this->input->post(null, true);
        $this->load->model('Challan_products_model');
        if ($values['type'] == 'update'):
            $res = $this->Challan_products_model->update_data_purchase($this->input->post(null, true));
        elseif ($values['type'] == 'add'):
            $res = $this->Challan_products_model->save($this->input->post(null, true));
        elseif ($values['type'] == 'delete'):
            $res = $this->Challan_products_model->delete_data($this->input->post(null, true));
        endif;



        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function save_products() {
        $data = array();
        $values = $this->input->post(null, true);
        $this->load->model('Challan_products_model');
        if ($values['type'] == 'update'):
            $res = $this->Challan_products_model->update_data($this->input->post(null, true));
        elseif ($values['type'] == 'add'):
            $res = $this->Challan_products_model->save_sales($this->input->post(null, true));
        elseif ($values['type'] == 'delete'):
            $res = $this->Challan_products_model->delete_data($this->input->post(null, true));
        endif;



        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function fetch_challans() {

        $values = $this->input->get(null, TRUE);
        $this->load->model('Delivery_challan_model');
        $res = $this->Delivery_challan_model->fetch($values);

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

    public function challan_list() {
      
        $data = array();
      
           
           
              
                $data['title'] = "DC List";
               
                $data['by_title'] = "Given By";
                $data['business_contact_heading'] = "Customer";
           
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
            $this->load->model('Users_model');
            $res = $this->Users_model->getOptions();
            $data['user_options'] = json_encode($res);

            $this->_loadView('billing-users/delivery_challan/challan_list', $data, 'Challan list');
       
    }

    public function delete() {
        $challan_id_data = $this->input->post(null, true);

        if (isset($challan_id_data['data']['challan_id'])):
            $challan_id = $challan_id_data['data']['challan_id'];
        else:
            $challan_id = $challan_id_data['challan_id'];
        endif;
        $this->load->model('Delivery_challan_model');
        $this->Delivery_challan_model->delete_data(array('challan_id' => $challan_id));
        echo json_encode(array('msg' => 'Successfully deleted'));
        exit;
    }

    public function print_receipt() {

        if ($this->uri->segment(4)):
            $data = array();
            $transactioin_id = $this->uri->segment(4);
            $this->load->model('Delivery_challan_model');
            $data['challan_details'] = $this->Delivery_challan_model->getDetails($transactioin_id);
          
            $this->load->model('Companies_model');
            $data['company_details'] = $this->Companies_model->getDetails(get_company_id());


            $this->load->model('Challan_products_model');
            $data['transaction_products'] = $this->Challan_products_model->getDetails($transactioin_id);

            $this->_loadView('billing-users/delivery_challan/print', $data, 'Print');

        endif;
    }



  

    private function deleteInactive() {
        $this->load->model('Delivery_challan_model');
        $res = $this->Delivery_challan_model->deleteInactive();
    }

}
