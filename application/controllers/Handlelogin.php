<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Handlelogin extends CI_Controller {
	public function index()
	{
        parent::__construct();
        $this->load->library('session');
        if ($this->session->userdata('user_id')) {
            $this->session->sess_destroy();
        }
        $name = $_POST['login_id'];
        $pword = $_POST['pword'];
        if ($name && $pword) {  // do admin check here...
            $this->load->model('users');
            $user = $this->users->verify($name, $pword);
            $this->load->helper('url');
            if ($user) {
                $this->session->set_userdata('user_id', $user->user_id);
                if ($user->is_admin == 1) {
                    $this->session->set_userdata('is_admin', 1);
                    redirect('/admin','refresh');
                }
                redirect('/odds','refresh');
            } else {
                $this->load->view('login_page');
            }
        } else {
            show_404($page = '', $log_error = FALSE);
        }
	}
}
