<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users_model extends MY_Model {

    public $_table = 'users';
    protected $primary_key = 'user_id';
    public $return_type = 'array';

    public function save($values) {
        try {
            $company_id = $values['company_id'];
            $user_role_id = $values['user_role_id'];
            unset($values['company_id']);
            unset($values['user_role_id']);
            $values['created_on'] = date('Y-m-d');
            if ($values['password'] == ""):
                $values['password'] = "p_891011";
            endif;
            $values['password'] = md5($values['password']);
            $user_id = $this->insert($values);

            $this->load->model('Company_users_model');
            $send_values = array('company_id' => $company_id, 'user_id' => $user_id, 'user_role_id' => $user_role_id);
            $res = $this->Company_users_model->save($send_values);
            return $res;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function update_data($values) {
        try {
            // Get old company id
            $this->load->model('Company_users_model');
            $this->Company_users_model->getOldCompanys($values['user_id']);


            $id = $values['user_id'];
            $updated_company_id = $values['company_id'];
            $user_role_id = $values['user_role_id'];
            unset($values['user_role_id']);
            unset($values['company_id']);
            if ($values['password'] != ""):
                $values['password'] = md5($values['password']);
            else:
                unset($values['password']);
            endif;
            $res = $this->update($id, $values);

            $this->load->model('Company_users_model');
            $is_new_company = $this->Company_users_model->getOldCompanys($id);
            if (!in_array($updated_company_id, $is_new_company)):
                $send_values = array('company_id' => $updated_company_id, 'user_id' => $id, 'user_role_id' => $user_role_id);
                $res = $this->Company_users_model->save($send_values);
            endif;
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function delete_data($values) {
        $id = $values['user_id'];


        return $this->delete($id);
    }

    public function login($email, $password) {

        // Username is Email of the Player
        $this->db->select('u.*');
        $this->db->from($this->_table . " u")
                ->where('user_email', $email)->where('is_active', 1)->where("password", md5($password));

        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $result = $query->row_array();
            return $result;
        } else {
            return false;
        }
    }

    public function usernamecheck($username) {
        $this->db->select('p.Salt');
        $this->db->from($this->_table . " p")
                ->where('Username', $username);



        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
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
        $this->db->select('u.user_id');
        $this->db->from($this->_table . " u")->join("company_users cu", "u.user_id=cu.user_id", 'left')->join("companies c", "cu.company_id=c.company_id", 'left');
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

        $this->db->select('u.user_id,first_name,middle_name,last_name,user_email,is_active,cu.company_id,cu.user_role_id,user_mobile');
        $this->db->from($this->_table . " u")->join("company_users cu", "u.user_id=cu.user_id", 'left')->join("companies c", "cu.company_id=c.company_id", 'left');
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
        $res = $this->db->select('u.user_id,CONCAT(first_name,middle_name) AS uname')->from($this->_table . " u")->join('company_users cu', "u.user_id=cu.user_id")->where("company_id=$company_id")->get();


        $result = $res->result_array();
        array_unshift($result, array('user_id' => "", "uname" => "Select"));
        return $result;
    }

    public function getdropdownOptions() {
        $company_id = get_company_id();
        $res = $this->db->select('u.user_id,CONCAT(first_name,middle_name) AS uname')->from($this->_table . " u")->join('company_users cu', "u.user_id=cu.user_id")->where("company_id=$company_id")->get();


        $result = $res->result_array();
        $options = array("" => "Select");
        foreach ($result as $key => $value) {
            $options[$value['user_id']] = $value['uname'];
        }



        return $options;
    }

    public function update_passwords($values) {
        try {
            $data['password'] = trim(md5($values['password']));
            if ($values['forgot_password_key'] == "") {
                $id = get_session_data('user_id');
                $res = $this->update($id, $data);
            } else {
                $this->db->where('forgot_password_key', trim($values['forgot_password_key']));
                $this->db->update($this->_table, $data);
            }
            return true;




            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function checkEmail($email) {
        try {
            $res = $this->db->select("user_id")->from($this->_table)->where("user_email", trim($email))->get();

            $result_array = $res->row_array();
            if (!$result_array):
                return false;
            endif;
            $this->update($result_array['user_id'], array("forgot_password_key" => md5($email)));
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function getUserID($key) {
        try {

            if ($key != ""):
                $res = $this->db->select("user_id")->from($this->_table)->where("forgot_password_key", trim($key))->get();
                $result = $res->row_array();

                return $result;
            else:
                return get_user_id();
            endif;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function getJqgridOptions() {
        $res = $this->db->select("user_id,first_name,middle_name,last_name")->from($this->_table)->where("is_active", 1)->get();
        $opt = $res->result_array();
         $lastkey=  end($opt); 
        $options = "";
        foreach ($opt as $key => $value) {
            $options.=$value['user_id'] . ":" . $value['first_name']." ".$value['middle_name']." ".$value['last_name'];

            if ($lastkey['user_id'] != $value['user_id']) {
                $options.=";";
            }
        }
        return trim($options);
    }

}
