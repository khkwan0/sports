<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('users');
        $this->load->model('betsmodel');
        $this->load->model('Logit');
        $this->load->library('session');
    }

    private function showStandardHeader() {
        $this->load->view('header');
        $this->load->view('left_side_begin');
        $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
        $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
        $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
        $this->load->view('left_side_end');
        $this->load->view('right_side_begin');
        $this->load->view('right_header');
        $this->load->view('admin_nav');
    }

    private function showStandardFooter() {
        $this->load->view('right_side_end');
        $this->load->view('footer');
    }

	public function index() {
        if ($this->session->userdata('is_admin')==1) {
            $users = $this->users->getUsers();
            if ($this->session->userdata('user_id')) {
                $this->showStandardHeader();
                $all_pending = $this->betsmodel->getAllPending();
                $this->load->view('admin_page', array('users_raw'=>$users, 'pending'=>$all_pending));
                $this->showStandardFooter();
            }
        } else {
            show_404($page = '', $log_error = FALSE);
        }
	}

    public function net() {
        if ($this->session->userdata('is_admin')==1) {
            $users = $this->users->getUsers();
            if ($this->session->userdata('user_id')) {
                $this->showStandardHeader();
                $weekly_balances = $this->users->getWeeklyBalances();
                $this->load->view('admin_net', array('balances'=>$weekly_balances));
                $this->showStandardFooter();
            }
        } else {
            show_404($page = '', $log_error = FALSE);
        }
    }
    public function error($url = '', $msg = '') {
        $this->load->view('error', array('url'=>$url,'msg'=>$msg));
    }

    public function showBets($user_id = 0) {
        if ($this->session->userdata('user_id') && $this->session->userdata('is_admin') && $user_id) {
            $this->showStandardHeader();
            $balances = $this->users->getBalancebyUserID($user_id);
            $bets = $this->betsmodel->getAllBetsByUserID($user_id, false, false,false);
            $this->load->view('bets', array('bets'=>$bets,'default_balance'=>$balances['default_balance']));
            $this->showStandardFooter();
        }
    }

    public function AllPending() {
        if ($this->session->userdata('is_admin')==1) {
            $this->showStandardHeader();
            $bets = $this->betsmodel->getAllBetsByUserID(0, true, false, false);
            $this->load->view('bets_with_user_id', array('bets'=>$bets));
            $this->showStandardFooter();
        } else {
            show_404($page = '', $log_error = FALSE);
        }
    }

    public function edit($user_id = 0) {
        if ($this->session->userdata('is_admin') == 1) {
            $this->showStandardHeader();
            $user_info = $this->users->getUser($user_id);
            $this->load->view('admin_user_edit', array('user'=>$user_info));
            $this->showStandardFooter();
        } else {
            show_404($page = '', $log_error = FALSE);
        }
    }

    public function update_user() {
        $this->load->helper('url');
        if ($this->session->userdata('is_admin') ==1 ) {
            $this->users->updateUser($this->input->post());
            redirect('/admin', 'refresh'); 
        } else {
            show_404($page = '', $log_error = FALSE);
        }
    }

    public function recent($start = 0, $limit = 50) {
        if ($this->session->userdata('user_id')) {
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->showStandardHeader();
            $data = $this->Logit->getRealized($start, $limit);
            $this->load->view('realized', array('res'=>$data, 'start'=>0, 'limit'=>50));
            $this->showStandardFooter();
        } else {
            redirect('/','refresh');
        }
    }
}
