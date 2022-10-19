<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Business_contacts extends MY_Controller {

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
        if (!is_user_logged_in()):
            redirect(site_url());
        endif;
    }

    public function index() {
        $data = array();

        $this->load->model('Companies_model');
        $res = $this->Companies_model->getOptions();
        $data['company_options'] = json_encode($res);

        $this->load->model('Business_types_model');
        $res = $this->Business_types_model->getOptions();
        $data['contact_type_options'] = json_encode($res);

        $this->load->model('Contact_types_model');
        $res = $this->Contact_types_model->getOptions();
        $data['business_type_options'] = json_encode($res);
        $data['can_edit'] = 1;
        if (get_session_data('user_role_id') == 3):
            $data['can_edit'] = 0;

        endif;


        $this->_loadView('billing-users/business-contacts/index', $data, 'Manage Business Contacts');
    }

    public function fetch() {
        $values = $this->input->get(null, TRUE);
        $this->load->model('Business_contacts_model');
        $res = $this->Business_contacts_model->fetch($values);

        echo json_encode($res);
        exit;
    }

    public function save() {
        $values = $this->input->post(null, TRUE);
        $this->load->model('Business_contacts_model');
        if ($values['type'] == "add") {
            $res = $this->Business_contacts_model->save($values['data']);
        } elseif ($values['type'] == "update") {

            $res = $this->Business_contacts_model->update_data($values['data']);
        } elseif (($values['type'] == "delete")) {
            $res = $this->Business_contacts_model->delete_data($values['data']);
            $res = "Success";
        }
        echo json_encode($res);
        exit;
    }

}
