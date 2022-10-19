<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Petty extends MY_Controller {

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

        $this->load->model('Categories_model');
       

     

       
      
        
         

        $this->_loadView('billing-users/petty/index', $data, 'Manage Petty');
    }

    public function fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Petty_cash_model');
        $res = $this->Petty_cash_model->fetch($values);

        echo $res;
        exit;
    }

    public function operations() {
        $values = $this->input->post(null, TRUE);
        $this->load->model('Petty_cash_model');
    
            $res = $this->Petty_cash_model->operations($values);
       
        echo json_encode($res);
        exit;
    }

    public function get() {
        if ($this->db->get('type') == 'contact_options'):
            $this->load->model('Company_contacts_model');
            $res = $this->Company_contacts_model->getOptions();
            echo json_encode($res);
            exit;
        endif;
          if ($this->db->get('type') == 'head_options'):
            $this->load->model('Head_options');
            $res = $this->Head_options->getOptions(1);
            echo json_encode($res);
            exit;
        endif;
        if ($this->db->get('type') == 'sub_head_options'):
            $this->load->model('Head_options');
            $res = $this->Head_options->getOptions(2);
            echo json_encode($res);
            exit;
        endif;
    }

}
