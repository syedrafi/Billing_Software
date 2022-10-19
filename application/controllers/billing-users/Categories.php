<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller {

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
    
        
       
        $this->_loadView('billing-users/categories/index', $data, 'Manage Categories');
    }

    public function fetch(){
          $values = $this->input->get(null, TRUE);
        $this->load->model('Categories_model');
        $res = $this->Categories_model->fetch($values);
       
        echo json_encode($res);
        exit;
    }
      public function save(){
          $values = $this->input->post(null, TRUE);
        $this->load->model('Categories_model');
        if($values['type']=="add"){
        $res = $this->Categories_model->save($values['data']);
        }
        elseif($values['type']=="update"){
            
             $res = $this->Categories_model->update_data($values['data']);
        }
        elseif(($values['type']=="delete")){
               $res = $this->Categories_model->delete_data($values['data']);
               $res="Success";
        }
        echo json_encode($res);
        exit;
    }
   

}
