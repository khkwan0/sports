<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archives extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('oddsmodel');
        $this->load->model('betsmodel');
        $this->load->model('users');
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index() {
        if ($this->session->userdata('user_id')) {
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->load->view('header');
            $this->load->view('left_side_begin');
            $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
            $data = $this->oddsmodel->getArchives(0,50);
            $this->load->view('archives', array('res'=>$data, 'start'=>0, 'limit'=>50));
            $this->load->view('right_side_end');
            $this->load->view('footer');
        } else {
            redirect('/','refresh');
        }
    }

    public function sport($sport = '', $start = -1, $limit = 0) {
        if ($this->session->userdata('user_id')) {
            if ($limit <= 0) { $limit = 50; }
            if ($start <0) { $start = 0; }
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->load->view('header');
            $this->load->view('left_side_begin');
            $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
            if ($sport == 'all') {
                $data = $this->oddsmodel->getArchives($start,$limit);
            } else {
                $data = $this->oddsmodel->getArchives($start,$limit,$sport);
            }
            $this->load->view('archives', array('res'=>$data, 'the_sport'=>$sport,'start'=>$start,'limit'=>$limit));
            $this->load->view('right_side_end');
            $this->load->view('footer');
        } else {
            redirect('/','refresh');
        }
    }

    public function page($start = -1, $limit = 0) {
        if ($this->session->userdata('user_id')) {
            if ($limit <= 0) { $limit = 50; }
            if ($start <0) { $start = 0; }
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->load->view('header');
            $this->load->view('left_side_begin');
            $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
            $data = $this->oddsmodel->getArchives($start, $limit);
            $this->load->view('archives', array('res'=>$data, 'the_sport'=>$sport,'start'=>$start,'limit'=>$limit));
            $this->load->view('right_side_end');
            $this->load->view('footer');
        } else {
            redirect('/','refresh');
        }
    }
}
