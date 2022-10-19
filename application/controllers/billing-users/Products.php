<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {

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
        $res=$this->Categories_model->getOptions();
        $data['category_options']=  json_encode($res);
        
        
        
         $this->load->model('Unit_types_model');
        $res=$this->Unit_types_model->getOptions();
        $data['unit_type_options']=  json_encode($res);
        $this->load->model('Brands_model');
        $res=$this->Brands_model->getOptions();
        $data['brand_options']=  json_encode($res);
        
          $this->load->model('Categories_model');
        $res=$this->Categories_model->getOptions();
        $data['category_options']=  json_encode($res);
        $data['can_edit']=1;
        if(get_session_data('user_role_id')==3):
             $data['can_edit']=0;
        
        endif;
       
        $this->_loadView('billing-users/products/index', $data, 'Manage Products');
    }

    public function fetch(){
          $values = $this->input->get(null, TRUE);
        $this->load->model('Products_model');
        $res = $this->Products_model->fetch($values);
       
        echo json_encode($res);
        exit;
    }
     public function fetch_lowstock(){
          $values = $this->input->get(null, TRUE);
        $this->load->model('Products_model');
        $res = $this->Products_model->fetch_lowstock($values);
       
        echo json_encode($res);
        exit;
    }
      public function save(){
          $values = $this->input->post(null, TRUE);
        $this->load->model('Products_model');
        if($values['type']=="add"){
        $res = $this->Products_model->save($values['data']);
        }
        elseif($values['type']=="update"){
            
             $res = $this->Products_model->update_data($values['data']);
        }
        elseif(($values['type']=="delete")){
               $res = $this->Products_model->delete_data($values['data']);
               $res="Success";
        }
        echo json_encode($res);
        exit;
    }
   

}
