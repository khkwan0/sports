<?php

class Logit extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    function saveLog($action = '', $value = '') {
        $data = array('user_id' =>  $this->session->userdata('user_id'),
                      'action'  =>  $action,
                      'value'   =>  $value
                );
        $this->db->insert('log', $data);
    }

    function getRealized($start = 0, $number = 50) {
        $res = null;
        if (!is_numeric($start)) $start = 0;
        if (!is_numeric($number)) $number = 50;
        if ($number > 100) $number = 100;
        $this->db->where('valid', 1);
        $this->db->order_by('datetime','desc');
        $query = $this->db->get('realized', $number, $start);
        if ($query  && $query->result()) {
            foreach ($query->result() as $row) {
                $res[] = $row;
            }
        }
        return $res;
    }
}
