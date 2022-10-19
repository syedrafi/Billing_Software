<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Business_contacts_model extends MY_Model {

    public $_table = 'business_contacts';
    protected $primary_key = 'business_contact_id';
    public $return_type = 'array';
        public function getOptionsSelect2($type=1) {

        $this->db->select('u.business_contact_id,business_contact_name')->from($this->_table . " u")->where("is_active", 1)->where("contact_type_id",$type);

         
       

        $result = $this->db->get()->result_array();
        $options = ":Select;";
        $lastkey = end($result);


        foreach ($result as $key => $value) {
            $options .= $value['business_contact_id'] . ":" . $value['business_contact_name'];

            if ($lastkey['business_contact_id'] != $value['business_contact_id']) {
                $options .= ";";
            }
        }
        return trim($options);
    }
    
    public function fetch($values) {

        $page_index = $values['pageIndex'];
        $page_size = $values['pageSize'];
        unset($values['pageIndex']);
        unset($values['pageSize']);

        // SOrting
        if (isset($values['sortField'])) {
            $sort_field = $values['sortField'];
            $sort_order = $values['sortOrder'];
            unset($values['sortField']);
            unset($values['sortOrder']);
        }
        // Get Total rows
        $company_id = get_company_id();
        $this->db->select('business_contact_id');
        $this->db->from($this->_table . " c")->where("company_id=$company_id");
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $res['itemsCount'] = $this->db->count_all_results();

        $start_index = ($page_index * $page_size) - $page_size;

        $this->db->select('business_contact_name,business_contact_id,gstin,ref_by,business_contact_email,business_contact_mobile,type_id,is_active,address,landline,tin_no,dl_no,contact_type_id,cst_no');
        $this->db->from($this->_table . " c")->where("company_id", $company_id);
        if (isset($sort_field)):
            $this->db->order_by($sort_field, $sort_order);
        endif;

        foreach ($values as $key => $value) {

            if ($value) {
                $this->db->WHERE("$key LIKE '%$value%'");
            }
        }

        $this->db->limit($page_size, $start_index);

        $result_set = $this->db->get();
        // $this->db->last_query();


        $result = $result_set->result_array();
        $i = $start_index + 1;
        foreach ($result as $key => $value) {
            $result[$key]['sno'] = $i;
            $i++;
        }
        $res['data'] = $result;

        return $res;
    }

    public function getOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('business_contact_id,business_contact_name')->from($this->_table)->where("company_id", $company_id)->get();


        $result = $res->result_array();
        array_unshift($result, array('business_contact_id' => "", "business_contact_name" => "Select"));

        return $result;
    }

    public function getdropdownOptions($type = 1) {
        $company_id = get_company_id();
        $res = $this->db->select('business_contact_id,business_contact_name')->from($this->_table)->where("company_id=$company_id")->where("contact_type_id", $type)->get();


        $result = $res->result_array();
        $options = array("" => "Select");
        foreach ($result as $key => $value) {
            $options[$value['business_contact_id']] = $value['business_contact_name'];
        }



        return $options;
    }

    public function save($values) {
        $values['created_on'] = date('Y-m-d');
        $values['created_by'] = get_user_id();
        if (isset($values['sno'])):
            unset($values['sno']);
        endif;
        $values['company_id'] = get_company_id();
        return $this->insert($values);
    }

    public function update_data($values) {
        $id = $values['business_contact_id'];
        unset($values['business_contact_id']);
        unset($values['sno']);
        $values['updated_by'] = get_user_id();
        return $this->update($id, $values);
    }

    public function delete_data($values) {
        $id = $values['business_contact_id'];


        return $this->update($id, array('is_active' => 0));
    }

    public function getFilteredContacts($type, $business_type = 1) {
        $company_id = get_company_id();
        $res = $this->db->select('business_contact_id,business_contact_name')->from($this->_table)->where("company_id=$company_id")->where("contact_type_id", $business_type)->where("type_id", $type)->get();

        $result = $res->result_array();
        return $result;
    }

}
