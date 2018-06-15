<?php
class Error_c extends CI_Controller  {
    public function __construct() {
        parent::__construct();
    }

    public function index(){
        $this->output->set_status_header('404');
		$this->load->helper('url');
        // Make sure you actually have some view file named 404.php
        $this->load->view('error');
    }
}
