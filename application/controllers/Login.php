<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

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
         session_destroy();
        $data = array();
        $this->template->set_template("login");
        $this->_loadView('login/index', $data, 'Billing');
       
       
    }



    public function check_login() {
         header('Access-Control-Allow-Origin: *');  
$data=array();

        if ($this->input->post('email') && $this->input->post('password')) {
            //This method will have the credentials validation
            $this->load->library('form_validation');

            $this->form_validation->set_rules('email', 'email', 'trim|required');
            $this->form_validation->set_rules('password', 'password', 'trim|required|callback_check_database');
            if ($this->form_validation->run() == FALSE) {
                $data['msg'] = validation_errors();
                $data['success'] = 0;
                //Field validation failed.  User redirected to login page
                //  $this->_loadView('login/index', $data, 'Login | iApple Academy');
            } else {

                $data['msg'] = "Successfully Logged in. Redirecting...";
                $data['success'] = 1;

                //Go to private area
            }
            echo json_encode($data);
            exit;
        }
        echo json_encode(array('success' => 0, 'msg' => 'Invalid Access'));
        exit;
    }

    function check_database($pwd) {
        //Field validation succeeded.Validate against database
        $email = $this->input->post('email');
        $pwd = $this->input->post('password');


        //query the database
        $this->load->model('users_model');
        $result = $this->users_model->login($email, $pwd);

        if ($result) {

            if ($result['is_super_admin'] == 1):
                $result['redirect_url'] = site_url('masters/companies/index');
            $result['user_role_id']=1;
            if(isset($res['company_id'])):
                   $result['company_id'] = $res['company_id'];
        
            else:
                    $result['company_id'] = null;
            endif;
            else:
                $this->load->model('Company_users_model');
                $res = $this->Company_users_model->getUserCompany($result['user_id']);
                $result['company_id'] = $res['company_id'];
                $result['user_role_id'] = $res['user_role_id'];
                $result['redirect_url'] = site_url('billing-users/home');
                 $result['show_pop_up'] =1;
                
            endif;
            $this->session->set_userdata('logged_in', $result);
            $result['msg'] = "Successfully Logged in. Redirecting...";
            $result['success'] = 1;
            
            echo json_encode($result);
            exit;
        } else {
            $this->form_validation->set_message('check_database', 'Your login attempt was not successful.  Please check the username and password and try again.');
            return false;
        }
    }

    function logout() {
        $this->session->unset_userdata('logged_in');

        redirect(site_url('login/index'));
    }
  public function forgot_password() {
         session_destroy();
        $data = array();
        $this->template->set_template("login");
        $this->_loadView('login/forgot_password', $data, 'ABS');
    }
}
