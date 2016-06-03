<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('users');
        $this->load->model('betsmodel');
        $this->load->helper('url');
    }

    public function Account($error = '') {
        if ($this->session->userdata('user_id')) {
            $this->load->view('header');
            $this->load->view('left_side_begin');
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
            $this->load->view('account', array('error_msg'=>($error)));
            $this->load->view('footer');
        } else {
            show_404($page = '', $log_error = FALSE);
        }
    }

    public function changePassword() {
        if ($this->session->userdata('user_id')) {
            $old = $this->input->post('old');
            $new = $this->input->post('new');
            $confirm = $this->input->post('confirm');
            if ($new == $confirm) {
                $res = $this->users->changePassword($old, $new, $this->session->userdata('user_id'));  
                if ($res) {
                    redirect('/user/account/2', 'refresh'); 
                } else {
                    redirect('/user/account/3', 'refresh'); 
                }
            } else {
               redirect('/user/account/1', 'refresh'); 
            }
        } else {
            show_404($page = '', $log_error = FALSE);
        }
    }
}
