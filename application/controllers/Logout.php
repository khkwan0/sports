<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
	public function index()
	{
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->session->sess_destroy();
        redirect('/', 'refresh');
	}
}
