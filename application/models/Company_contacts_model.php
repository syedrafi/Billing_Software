<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Company_contacts_model extends MY_Model {

    public $_table = 'company_contacts';
    protected $primary_key = 'contact_id';
    public $return_type = 'array';

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
        $company_id=  get_company_id();
        $this->db->select('contact_id');
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

        $this->db->select('business_contact_name,contact_id,business_contact_email,business_contact_mobile,type_id,is_active,address,landline,tin_no,cst_no');
        $this->db->from($this->_table . " c")->where("company_id",$company_id);
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
        $res['data'] = $result;

        return $res;
    }

    public function getOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('contact_id,contact_name')->from($this->_table)->where("company_id",$company_id)->get();


        $result = $res->result_array();
         $lastkey=  end($result); 
       
          $options=":Select;";
          foreach ($result as $key =>$value) {
              $options.=$value['contact_id'].":".$value['contact_name'];
              
              if($lastkey['contact_id']!=$value['contact_id']){
                  $options.=";";
              }
              
              
          }
         return  trim($options);
        return $options;
    }

    public function getdropdownOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('contact_id,business_contact_name')->from($this->_table)->where("company_id=$company_id")->get();


        $result = $res->result_array();
        $options=array(""=>"Select");
        foreach ($result as $key => $value) {
            $options[$value['contact_id']] = $value['business_contact_name'];
        }

      

        return $options;
    }

    public function save($values) {
        $values['created_on'] = date('Y-m-d');
        $values['created_by'] = get_user_id();
        $values['company_id'] = get_company_id();
        return $this->insert($values);
    }

    public function update_data($values) {
        $id = $values['contact_id'];
        unset($values['contact_id']);
        $values['updated_by'] = get_user_id();
        return $this->update($id, $values);
    }

    public function delete_data($values) {
        $id = $values['contact_id'];


        return $this->update($id, array('is_active' => 0));
    }
    
 public function getContactID($contact_id) {
    
        $contact_id = trim($contact_id);
        $check_duplicates = $this->db->select('contact_id')->from($this->_table)->where("contact_name",$contact_id)->get();
        $count=$check_duplicates->row_array();
       
        if (count($count)==1):
            $check_duplicates = $check_duplicates->row_array();
            $contact_id = $check_duplicates['contact_id'];

        else:
           
            if ($contact_id!=""):
                $contact_id = $this->insert(array(
                    'contact_name' => $contact_id,
                    'created_on'=>date('Y-m-d')
                ));
            else:
                $contact_id = null;
            endif;
        endif;
        
        return $contact_id;
    }
}
