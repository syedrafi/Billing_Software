<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pdc extends MY_Controller {

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

        if ($this->input->get("type")):
            $type = $this->input->get('type');
            $data = array();
            $data['type'] = $type;
            $data['page_title'] = array(1 => "PDC Payable", 2 => " PDC Receivable");
            $this->load->model('Payment_modes_model');
            $res = $this->Payment_modes_model->getOptions();
            $data['mode_options'] = json_encode($res);
            $this->load->model('Business_contacts_model');
            $res = $this->Business_contacts_model->getOptions();
            $data['contact_options'] = json_encode($res);

            $data['title'] = "Advance Payments";

            $this->_loadView('billing-users/pdc/index', $data, 'PDC');
        endif;
    }

    public function fetch() {

        $values = $this->input->get(null, TRUE);
        $this->load->model('payments_model');
        $res = $this->payments_model->fetch_pdc($values);

        echo json_encode($res);
        exit;
    }

    public function pop_up() {
        $pop_up = get_session_data('show_pop_up');

        echo json_encode(array("show" => $pop_up));
    }

    public function pop_up_disable() {
        get_session_data();
        $session_data = $this->session->userdata('logged_in');

        $session_data['show_pop_up'] = 0;
        $this->session->set_userdata('logged_in', $session_data);
    }

}
