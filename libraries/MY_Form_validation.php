<?php

class MY_Form_validation extends CI_Form_validation {

    protected $_error_prefix = '<p class="text-error">';
    protected $_error_suffix = '</p>';

    /**
     * Validate URL format
     *
     * @access  public
     * @param   string
     * @return  string
     */
    function valid_url_format($str) {
        $pattern = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";
        if (!preg_match($pattern, $str)) {
            $this->set_message('valid_url_format', 'The URL you entered is not correctly formatted.');
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Validates that a URL is accessible. Also takes ports into consideration. 
     * Note: If you see "php_network_getaddresses: getaddrinfo failed: nodename nor servname provided, or not known" 
     *          then you are having DNS resolution issues and need to fix Apache
     *
     * @access  public
     * @param   string
     * @return  string
     */
    function url_exists($url) {
        $url_data = parse_url($url); // scheme, host, port, path, query
        if (!fsockopen($url_data['host'], isset($url_data['port']) ? $url_data['port'] : 80)) {
            $this->set_message('url_exists', 'The URL you entered is not accessible.');
            return FALSE;
        }

        return TRUE;
    }

    function edit_unique($value, $params) {
        $CI = & get_instance();
        $CI->load->database();

        $CI->form_validation->set_message('edit_unique', "%s field must contain a unique value.");

        list($table, $field, $current_id, $primarykey) = explode(".", $params);

        $query = $CI->db->select()->from($table)->where($field, $value)->limit(1)->get();

        if ($query->row() && $query->row()->$primarykey != $current_id) {
            return FALSE;
        }
    }
     function error_array()
  {
    if (count($this->_error_array) === 0)
      return FALSE;
    else
      return $this->_error_array;
  }

}