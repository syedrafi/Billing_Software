<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends MY_Controller {

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
        
          
            $this->load->model('Users_model');
            $res = $this->Users_model->getJqgridOptions();
            $data['user_options'] = $res;
               $this->load->model('Recurring_types');
            $res = $this->Recurring_types->getJqgridOptions();
            $data['recurring_types'] = $res;

            $data['title'] = "Tasks";

            $this->_loadView('billing-users/tasks/index', $data, 'Tasks');
       
    }
   public function fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Tasks_model');
        $res = $this->Tasks_model->fetch($values);

        echo $res;
        exit;
    }

    public function operations() {
        $values = $this->input->post(null, TRUE);
        $this->load->model('Tasks_model');
    
            $res = $this->Tasks_model->operations($values);
       
        echo json_encode($res);
        exit;
    }
  
}
