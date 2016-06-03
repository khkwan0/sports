<?php

class Users extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->model('Logit');
    }

    function GetUsers($num = 0, $date_order = 'desc') {
        $this->db->from('users');
        $order = ($date_order=='desc')?'desc':'asc';
        $this->db->order_by('datetime_created',$order);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $res[$row->user_id] = $row;
        }
        return $res;
    }

    function getUser($user_id = 0) {
        $res = array();
        if ($user_id) {
            $this->db->from('users');
            $this->db->where('user_id', $user_id);
            $query = $this->db->get();
            if ($query && $query->result()) {
                $temp = $query->result();
                $res = $temp[0];
            }
        }
        return $res;
    }

    function create($name = '', $pword = '') {
        $name = stripslashes($name);
        $pword = stripslashes($pword);
        $data = array('username'    =>  $name,
                      'password'    =>  $pword,
                      'datetime_created'    =>  date('Y-m-d H:i:s', time()),
                );
        $this->db->insert('users', $data);
        $res = $this->db->insert_id();
        return $res;
    }

    function verify($name = '', $pword = '') {
        $row = null;
        $name = stripslashes($name);
        $pword = stripslashes($pword);
        $this->db->from('users');
        $this->db->where('username', $name);
        $this->db->where('password', $pword);
        $this->db->where('is_active', 1);
        $query = $this->db->get();
        if ($query && $query->result()) {
            $res = $query->result();
            $row = $res[0];
        }
        return $row;
    }

    function getBalanceByUserID($user_id = 0) {
        $balances = array();
        $this->db->from('users');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        if ($query && $query->result()) {
            $res = $query->result();
            $balances['balance'] = $res[0]->balance;
            $balances['default_balance'] = $res[0]->default_balance;
        }
        return $balances;
    }

    function deductBalanceByUserID($user_id = 0, $amt = 0.00) {
        $res = 0;
        if (is_numeric($user_id) && is_numeric($amt)) {
            $query = $this->db->query('update users set balance=balance-'.$amt.' where user_id='.$user_id);
            $res = $this->db->affected_rows();
        }
    }

    function getMaxBet($user_id = 0) {
        $max_bet = 0.00;
        $this->db->from('users');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        if ($query && $query->result()) {
            $res = $query->result();
            $max_bet = $res[0]->max_bet;
        }
        return $max_bet;
    }

    function getParlayJuice($user_id = 0) {
        $juice = 0.93;
        $this->db->from('users');
        $this->db->where('user_id',$user_id);
        $this->db->select('parlay_factor');
        $query = $this->db->get();
        if ($query && $query->result()) {
            $res = $query->result();
            $row = $res[0];
            $juice = $row->parlay_factor;
        }
        return $juice;
    }

    function changePassword($old = '', $new = '', $user_id = 0) {
        if ($old && $new) {
            $data = array(
                    'password'      => $new,
                    );
            $this->db->where('password', $old);
            $this->db->where('user_id', $user_id);
            $this->db->update('users', $data);
            return $this->db->affected_rows();
        }
        return 0;
    }

    function getWeeklyBalances() {
        $balances = array();
        $this->db->select('users.user_id');
        $this->db->select('users.username');
        $this->db->select('users.is_admin');
        $this->db->select('weekly_log.beginning_balance');
        $this->db->select('weekly_log.balance');
        $this->db->select('date');
        $this->db->from('weekly_log');
        $this->db->where('date >', date('Y-m-d',time()-3024000));
        $this->db->join('users','weekly_log.user_id=users.user_id');
        $this->db->order_by('date','desc');
        $query = $this->db->get();
        if ($query && $query->result()) {
            foreach ($query->result() as $row) {
                $balances[$row->date][$row->user_id] = $row;
            }
        }
        return $balances;
    }

    function deactivateUser($user_id = 0) {
        $data = array('is_active'=>0);
        $this->db->where('user_id',$user_id);
        $this->db->update('users',$data);
        return $this->db->affected_rows(); 
    }

    function activateUser($user_id = 0) {
        $data = array('is_active'=>1);
        $this->db->where('user_id',$user_id);
        $this->db->update('users',$data);
        return $this->db->affected_rows(); 
    }

    function getDefaultBalance($user_id = 0) {
        if ($user_id) {
            $this->db->from('users');
            $this->db->where('user_id', $user_id);
            $this->db->select('default_balance');
            $query = $this->db->get();
            if ($query && $query->result()) {
                $temp = $query->result();
                $res = $temp[0];
                return $res->default_balance;
            }
        }
        return null;
    }

    function updateUser($user_info = array()) {
        if (isset($user_info['user_id']) && is_numeric($user_info['user_id'])) {
            $user_id = $user_info['user_id'];

            $old_info = $this->getUser($user_id);

            $balances =  $this->getBalanceByUserID($user_id);
            $balance_diff = $user_info['default_balance'] - $balances['default_balance'];
            $user_info['balance'] = $balances['balance'] + $balance_diff;

            $this->db->where('user_id', $user_id);
            $this->db->update('users', $user_info);

            $log_raw = array(
                    'old_info'  => $old_info,
                    'new_info'  =>  json_decode(json_encode($user_info), false),
                    );
            $this->Logit->saveLog('update user', json_encode($log_raw));
        }
    }

    function getRealizedByUser($user_id, $begin, $end) {
        $res = null;
        if ($user_id && $begin && $end) {
            $this->db->from('realized');
            $this->db->where('user_id', $user_id);
            $this->db->where('valid', 1);
            $this->db->where('datetime >', $begin);
            $this->db->where('datetime <=', $end);
            $this->db->order_by('datetime','asc');
            $query = $this->db->get();
            if ($query && $query->result()) {
                foreach ($query->result() as $row) {
                    $res[] = $row;
                }
            }
        }
        return $res;
    }
}
