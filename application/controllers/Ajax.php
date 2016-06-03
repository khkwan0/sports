<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('oddsmodel');
        $this->load->model('betsmodel');
        $this->load->model('users');
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function deleteBet() {
        $bet_id = $this->input->post('bet_id');
        $user_id = $this->session->userdata('user_id');
        $res = $this->betsmodel->deleteBet($bet_id, $user_id);
        echo json_encode($res);
    }

    public function deactivateUser() {
        $user_id = $this->input->post('user_id');
        echo $this->users->deactivateUser($user_id);
    }

    public function activateUser() {
        $user_id = $this->input->post('user_id');
        echo $this->users->activateUser($user_id);
    }

    public function getRealizedByUser() {
        if ($this->session->userdata('is_admin') == 1) {
            $user_id = $this->input->post('user_id');
            $currentDayOfWeek = date('N', time())-1;

            $begin_date = date('Y-m-d 00:00:00', time() - ($currentDayOfWeek*24*3600));
            $end_date = date('Y-m-d H:i:s', time());
            echo json_encode($this->users->getRealizedByUser($user_id, $begin_date, $end_date));
        }
    }
}
