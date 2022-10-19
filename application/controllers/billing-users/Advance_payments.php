<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Advance_payments extends MY_Controller {

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

    public function index() {
       
            $data = array();
            $this->load->model('Payment_modes_model');
            $res = $this->Payment_modes_model->getOptions();
            $data['mode_options'] = json_encode($res);
            $this->load->model('Business_contacts_model');
            $res = $this->Business_contacts_model->getOptions();
            $data['contact_options'] = json_encode($res);

            $data['title'] = "Advance Payments";

            $this->_loadView('billing-users/advance_payments/index', $data, 'Advance Payments');
       
    }

    public function add() {
        $data = array();

        $this->load->model('payments_model');

        $res = $this->paymentss_model->add();
        echo json_encode(array('id' => $res, 'msg' => 'success'));
    }

    
    public function save_payments() {
        $data = array();

        $this->load->model('paymentss_model');

        $res = $this->paymentss_model->save($this->input->post(null, true));
        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function save() {
        $data = array();
        $values = $this->input->post(null, true);
        $this->load->model('Payments_model');
        if ($values['type'] == 'update'):
            $res = $this->Payments_model->update_data($this->input->post(null, true));
        elseif ($values['type'] == 'add'):
            $res = $this->Payments_model->save($this->input->post(null, true));
        elseif ($values['type'] == 'delete'):
            $res = $this->Payments_model->delete_data($this->input->post(null, true));
        endif;



        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function fetch() {

        $values = $this->input->get(null, TRUE);
        $this->load->model('payments_model');
        $res = $this->payments_model->fetch_advance($values);

        echo json_encode($res);
        exit;
    }

    public function update(){
          $values = $this->input->post(null, TRUE);
        $this->load->model('payments_model');
        $res = $this->payments_model->update_advance($values);

        echo json_encode($res);
        exit;
    }
    public function delete() {
        $payments_id = $this->input->post('payments_id');
        $this->load->model('paymentss_model');
        $this->paymentss_model->delete_data(array('payments_id' => $payments_id));
        echo json_encode(array('msg' => 'Successfully deleted'));
        exit;
    }
 public function fetch_transactions() {
        if ($this->input->post('q')):
            $this->load->model('Transactions_model');
            $result = $this->Transactions_model->getOptionsAdjust($this->input->post(null,true));
            echo json_encode(array('items' => $result, 'total_count' => count($result)));

        endif;
    }
}
