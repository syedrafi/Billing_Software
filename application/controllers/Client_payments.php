<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Client_payments extends MY_Controller {

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

    public function index() {


        $data = array();
          $data['business_contact_type']=1;
        $this->load->model("Payment_modes_model");
        $data['payment_mode_options'] = $this->Payment_modes_model->getOptionsSelect2();

        $this->load->model("Business_contacts_model");
        $data['business_contact_options'] = $this->Business_contacts_model->getOptionsSelect2();
         $data['page_title'] = "Client Payments";
        $this->_loadView('client_payments/index', $data, 'Qikink Order Proccessing');
    }

    public function checkusername() {
        $username = trim($this->input->post('username', true));
        $this->load->model('Clients_model');
        $res = $this->Clients_model->checkAvailability($username);
        echo json_encode($res);
        exit;
    }

    public function operations() {
        $values = $this->input->post(null, true);
        $this->load->model('Client_payments_model');

        switch ($values['oper']) {
            case "add":
                $res = $this->Client_payments_model->save($values);

                break;
            case "edit":
                $res = $this->Client_payments_model->update_data($values);
                break;
            case "del":
                $res = $this->Client_payments_model->delete_data($values['id']);
                break;
            default:
                $res = array('msg' => "InvalidAccess");

                echo json_encode($res);
                exit;
        }
    }

    public function fetch() {
        $values = $this->input->get(null, true);
        $this->load->model('Client_payments_model');

        $res = $this->Client_payments_model->fetch($values);
        echo json_encode($res);
        exit;
    }

}
