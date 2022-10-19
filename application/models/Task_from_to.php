<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Task_from_to extends MY_Model {

    public $_table = 'task_from_to';
    protected $primary_key = 'task_from_to_id';
    public $return_type = 'array';

    public function save($values,$task_id) {


        try {

            $data = array();
            $data['from_date'] = date("Y-m-d", strtotime($values['task_from']));
            $data['to_date'] = date("Y-m-d", strtotime($values['task_to']));
              $data['task_id'] = $task_id;
            
            return $this->insert($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

   
}
