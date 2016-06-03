<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parlay extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('oddsmodel');
        $this->load->model('users');
        $this->load->model('betsmodel');
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
            $this->load->view('parlay_menu');
            $this->load->view('right_side_end');
            $this->load->view('footer');
        } else {
            redirect('/','refresh');
        }
    }

    private function getCodeSwitch($sport = '') {
        $code = -1;
        switch ($sport) {
            case 'nfl': $code = 4;break;
            case 'nba': $code = 1;break;
            case 'mlb': $code = 0;break;
            case 'ncaaf': $code = 3;break;
            case 'ncaab': $code = 2;break;
            case 'soccer': $code = 7;break;
            case 'nhl': $code = 5;break;
            case 'mma': $code = 11;break;
            case 'tennis': $code = 9;break;
            default:break;
        }
        return $code;
    }

    private function getCode($sport = '') {
        $sport_code = array();
        if ($sport && isset($sport) && (strlen($sport)>0) && (strlen($sport)<10) && $sport!='multi') {
            $sport_code[$sport] = $this->getCodeSwitch($sport);
        } else {
            $sports = $this->input->post('sport');
            foreach ($sports as $sport) {
                $sport_code[$sport] = $this->getCodeSwitch($sport);
            }
        }
        return $sport_code;
    }

    public function showOdds() {
        if ($this->session->userdata('user_id')) {
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->load->view('header');
            $this->load->view('left_side_begin');
            $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
            $this->load->view('parlay_menu');
            $this->load->view('parlay_form_begin');
            $sport_code = $this->getCode();
            foreach ($sport_code as $the_sport=>$code) {
                $odds = $this->oddsmodel->getLatestOdds($code);
                $this->load->view('parlay_odds', array('odds'=>$odds,'sport'=>$the_sport));
            }
            $this->load->view('parlay_form_end');
            $this->load->view('right_side_end');
            $this->load->view('footer');
        } else {
            redirect('/','refresh');
        }
    }
/*
	public function showOdds($sport = '') {
        if ($this->session->userdata('user_id')) {
            $balance = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->load->view('header');
            $this->load->view('left_side_begin');
            $this->load->view('balances', array('balance'=>$balance,'pending'=>$pending));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
            $this->load->view('btb');
            $this->load->view('form_begin');
            $sport_code = $this->getCode($sport);
            foreach ($sport_code as $the_sport=>$code) {
                $odds = $this->oddsmodel->getLatestOdds($code);
                $this->load->view('odds', array('odds'=>$odds,'sport'=>$the_sport));
            }
            $this->load->view('form_end');
            $this->load->view('right_side_end');
            $this->load->view('footer');
        } else {
            redirect('/','refresh');
        }
	}
    */
}
