<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Createuser extends CI_Controller {
	public function index()
	{
        parent::__construct();
        $this->load->library('session');
        $name = $_POST['username'];
        $pword = $_POST['pword'];
        if ($this->session->userdata('is_admin') == 1) {
            $this->load->model('users');
            $res = null;
            if ($name && $password) {
                $res = $this->users->create($name, $pword);
            }
            $this->load->helper('url');
            if ($res) {
                redirect('/admin', 'refresh');
            } else {
                redirect('/admin/error/admin/1', 'refresh');
            }
        } else {
            show_404($page = '', $log_error = FALSE);
        }
	}
}
