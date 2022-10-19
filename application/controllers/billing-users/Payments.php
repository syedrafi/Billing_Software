<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends MY_Controller {

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

    public function purchase() {
        if ($this->uri->segment(4)) {
            $data = array();
            $this->load->model('Payment_modes_model');
        $res=$this->Payment_modes_model->getOptions();
        $data['mode_options']=  json_encode($res);
            $transactioin_id = $this->uri->segment(4);
            $this->load->model('Transactions_model');
            $data['details'] = $this->Transactions_model->getDetails($transactioin_id);
            $data['title']="Purchase Payments";
            $this->load->model('Transaction_products_model');
            $data['product_details'] = $this->Transaction_products_model->getNamesAndSum($transactioin_id);
            $this->_loadView('billing-users/payments/purchase', $data, 'Purchase Payments');
        }
    }
     public function sales() {
        if ($this->uri->segment(4)) {
            $data = array();
            $this->load->model('Payment_modes_model');
        $res=$this->Payment_modes_model->getOptions();
        $data['mode_options']=  json_encode($res);
            $transactioin_id = $this->uri->segment(4);
            $this->load->model('Transactions_model');
            $data['details'] = $this->Transactions_model->getDetails($transactioin_id);
            $data['title']="Sales Payments";
            $this->load->model('Transaction_products_model');
            $data['product_details'] = $this->Transaction_products_model->getNamesAndSum($transactioin_id);
            $this->_loadView('billing-users/payments/sales', $data, 'Purchase Payments');
        }
    }

    public function add() {
        $data = array();

        $this->load->model('paymentss_model');

        $res = $this->paymentss_model->add();
        echo json_encode(array('id' => $res, 'msg' => 'success'));
    }

    public function fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Transaction_products_model');
        $res = $this->Transaction_products_model->fetch($values);

        echo json_encode($res);
        exit;
    }

    public function save_payments() {
        $data = array();

        $this->load->model('paymentss_model');

        $res = $this->paymentss_model->save($this->input->post(null, true));
        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

    public function purchase_save() {
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

    public function purchase_fetch() {

        $values = $this->input->get(null, TRUE);
        $this->load->model('payments_model');
        $res = $this->payments_model->fetch($values);

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
    
    public function adjust(){
        
    }

}
