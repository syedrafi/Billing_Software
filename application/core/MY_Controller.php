<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public $_template = 'default';
    public $_template_user = 'user';

    /*
     * for SEO optimized routes
     */

    public function _remap($method) {
        $method = str_replace('-', '_', $method);
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    public function __construct() {
        parent::__construct();
 
            if (is_super_admin()):
                $this->template->set_template($this->_template);
     
        else:
            $this->template->set_template($this->_template_user);
        endif;
    }

    public function _loadView($view, $data = array(), $title = '') {
        $this->template->write('title', $title);
        $this->template->write_view('content', $view, $data);
        $this->template->render();
    }

}
