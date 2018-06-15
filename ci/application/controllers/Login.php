<?php

class Login extends CI_Controller {

	const USER = 'username';
	const PASS = 'password';
	
	public function index() {
		
		// destroy session if available
		// if(isset($_SESSION)){
			// session_destroy();
		// }
		
		$this->load->helper(array('url', 'form'));
		$this->load->library('form_validation');
		
		// custom library for html css,js files
		$this->load->library('html_utility');
		
		// form validation rules
		$this->form_validation->set_rules(
			self::USER,
			'Username', 
			'min_length[1]|max_length[72]|required|trim',
			array('required' => 'יש להקליד שם משתמש')
		);
		$this->form_validation->set_rules(
			self::PASS, 
			'Password', 
			'min_length[1]|max_length[72]|required|trim',
			array('required' => 'יש להקליד סיסמא')
		);
		
		if($this->form_validation->run()) {
			
			// if POST data is available
			if($this->input->post(array(self::USER, self::PASS))) {
				
				// return user info from DB
				$this->load->model('login_model');
				$result = $this->login_model->get_user();
				
				// if the user exists
				if($result) {
				
					// Check password hash - compare POST vs DB
					if( password_verify($this->input->post(self::PASS), $result[self::PASS]) ) {
					
						// Save to SESSION
						$this->load->library('session');
						if( ! isset($_SESSION) ){
							session_start(['use_strict_mode' => '1']);
						}
						session_regenerate_id();
						if ( ! isset($_SESSION[self::USER]) ){
							$_SESSION[self::USER] = $this->input->post(self::USER, true);
						}
						$_SESSION['exp'] = time(); // time has no real meaning except for causing confusion
						$this->session->mark_as_temp('exp', 600); // used as a flag for session_gc
						
						// redirect to the manage controller
						redirect('manage');
						exit();
					}
				}
			}
		}
		
		// header data
		$data = array(
			'title' => 'התחברות למערכת',
			'included_files' => array(
				'uri_css' => 'css/bootstrap.min.css',
				'uri_css_theme' =>  'css/bootstrap-theme.min.css',
				'uri_css_custom' => 'css/custom.css'
			)
		);
		$this->load->view('templates/header', $data);
		
		// clear the data and prepare for new data
		unset($data);
		$this->load->clear_vars(); // clear cached values
		$data = array();
		
		// login form at first or when invalid credentials are entered
		$this->load->view('login');
		
		// validate the form (client side) - footer data
		$data['scripts'] = array(
			'$("#login_form").validate();'
		);
		
		// footer data
		$data['included_files'] =  array(
				'uri_jquery_js' =>  'js/jquery-3.2.1.min.js',
				'uri_css_js' =>  'js/bootstrap.min.js',
				'uri_jquery_validation_js' =>  'js/jquery.validate.min.js',
				'uri_jquery_validation_js_he' =>  'js/messages_he.js'
			);
		$this->load->view('templates/footer', $data);
	}
}