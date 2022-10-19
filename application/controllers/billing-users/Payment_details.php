<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_details extends MY_Controller {

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
        if(!is_user_logged_in()):
            redirect(site_url());
        endif;
    }





   

    public function fetch_transactions() {

        $values = $this->input->get(null, TRUE);
        $this->load->model('transactions_model');
        $res = $this->transactions_model->fetch_detailed($values);

        echo json_encode($res);
        exit;
    }

    public function list_view() {
        $data = array();
        if ($this->input->get("type")) {
            $trans_type = 2;
            if ($this->input->get("type") ==2):
                $data['trans_type'] = 2;
                       $data['title'] = "Sales Detail List";
                $data['by_title'] = "Sold By";
                $data['business_contact_heading'] = "Customer";
                $data['balance_label']="Receivable";
            else:
                $data['trans_type'] = 1;
       
                  $data['title'] = "Purchase Detail List";
                $data['by_title'] = "Purchased By";
                $data['business_contact_heading'] = "Supplier";
                $data['balance_label']="Payable";
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




            $this->_loadView('billing-users/payment_details/list_view', $data, 'Details list');
        }
    }

    

    

  

  

  

}
