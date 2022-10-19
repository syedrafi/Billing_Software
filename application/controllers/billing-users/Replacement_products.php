<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Replacement_products extends MY_Controller {

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
      $data['title']="Replacement Products";

      
    $this->_loadView('billing-users/replacement_products/index', $data, $data['title']);
     
        

      
    


        
    }

    

    public function fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Transaction_products_model');
        $res = $this->Transaction_products_model->fetch_replacements($values);
        echo json_encode($res);
        exit;
    }

   

 
    public function save() {
        $data = array();
        $values = $this->input->post(null, true);
        $this->load->model('Transaction_products_model');
        if ($values['type'] == 'update'):
            $res = $this->Transaction_products_model->update_replacement_data($this->input->post(null, true));
        elseif ($values['type'] == 'add'):
           
        elseif ($values['type'] == 'delete'):
           // $res = $this->Transaction_products_model->delete_data($this->input->post(null, true));
        endif;
        


        echo json_encode(array('id' => $res, 'msg' => 'Successfully Saved'));
    }

  

   

  


    public function delete() {
        $transaction_id_data = $this->input->post(null, true);


        $transaction_id = $transaction_id_data['data']['transaction_id'];
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

  
    public function fetch_purchase_products() {
        if ($this->input->post('q')):
            $this->load->model('Transaction_products_model');
            $result = $this->Transaction_products_model->getOptionsPurchase($this->input->post('q'));
            echo json_encode(array('items' => $result, 'total_count' => count($result)));

        endif;
    }

   

   

}
