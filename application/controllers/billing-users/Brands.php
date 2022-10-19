<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Brands extends MY_Controller {

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
     $data['can_edit']=1;
        if(get_session_data('user_role_id')==3):
             $data['can_edit']=0;
        
        endif;
       
        
       
        $this->_loadView('billing-users/brands/index', $data, 'Manage Brands');
    }

    public function fetch(){
          $values = $this->input->get(null, TRUE);
        $this->load->model('Brands_model');
        $res = $this->Brands_model->fetch($values);
       
        echo json_encode($res);
        exit;
    }
      public function save(){
          $values = $this->input->post(null, TRUE);
        $this->load->model('Brands_model');
        if($values['type']=="add"){
        $res = $this->Brands_model->save($values['data']);
        }
        elseif($values['type']=="update"){
            
             $res = $this->Brands_model->update_data($values['data']);
        }
        elseif(($values['type']=="delete")){
               $res = $this->Brands_model->delete_data($values['data']);
               $res="Success";
        }
        echo json_encode($res);
        exit;
    }
   

}
