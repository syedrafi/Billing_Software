<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes., it's displayed at http://example.com/
     *php
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {
        parent::__construct();
    }
   public function index() {
        $data = array();
      if(!is_user_logged_in()):
          redirect(base_url("index.php/login"));
      endif;
        $this->_loadView('home/index', $data, 'Billing');
    }

    public function reset() {
        $data = array();
        $data['forgot_password_key'] = "";
        if ($this->uri->segment(3)|| is_user_logged_in()):
            $this->load->model('Users_model');
            $result = $this->Users_model->getUserID($this->uri->segment(3));
            if ($result):
                $data['forgot_password_key'] = $this->uri->segment(3);
            else:
                $this->session->set_flashdata('success', 'Invalid Key');
                redirect(site_url('login/index'));
            endif;
        else:
               $this->session->set_flashdata('success', 'Invalid Access');
            redirect(site_url('login/index'));
        endif;
        $this->_loadView('home/reset', $data, 'ABS');
    }
public function reset_password() {

        if ($this->input->post()) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('password', 'Password', 'required|matches[password_conf]');
            $this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required');

            if ($this->form_validation->run()) {
                $this->load->model('Users_model');
                $res = $this->Users_model->update_passwords($this->input->post(null, true));
                echo json_encode(array('msg' => "Successfully Saved"));
            } else {
                echo json_encode(array('msg' => validation_errors()));
            }
        }
    }
     public function forgot_password() {

        if ($this->input->post()) {
            $this->load->library('form_validation');


            $this->form_validation->set_rules('email', 'Email', 'required|callback_check_database');






            if ($this->form_validation->run()) {
                $this->load->model('Users_model');
                $res = $this->Users_model->update_passwords($this->input->post(null, true));
                echo json_encode(array('msg' => "Successfully Saved"));
            } else {
                echo json_encode(array('msg' => validation_errors()));
            }
        }
    }
     function check_database($pwd) {
        //Field validation succeeded.Validate against database
        $email = $this->input->post('email');



        //query the database
        $this->load->model('Users_model');
        $result = $this->Users_model->checkEmail($email);

        if ($result) {


            $result = $this->sendPasswordResetMail($email);

            echo json_encode($result);
            exit;
        } else {
            $this->form_validation->set_message('check_database', 'Email ID not registered with us.');
            return false;
        }
    }
    
    public function sendPasswordResetMail($email) {


        $message = "Hello User \n\n";
        $message.="Click the below link to reset the password\n\n";
        $message.=site_url() . "/home/reset/" . md5($email) . " \n\n";
        $message.="Regards,\n\n ABS Team";
        $config['mailtype'] = 'html';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['newline'] = "\r\n"; //use double quotes to comply with RFC 822 standard

        $this->load->library('email'); // load email library
        $this->email->from('no_reply@aviv.co.in', 'Aviv Business Solutions');
        $this->email->to($email);
        $this->email->cc('samuelchristopher29@gmail.com');
        $this->email->subject('Password Reset Request');
        $this->email->message($message);
       
        if ($this->email->send()) {
            return array('msg' => 'We have sent you an email with the password reset instructions');
        }
        return array('msg' => 'Email Server error!!!');
    }
}
