<?php

class Manage extends CI_Controller {
	
	// POST data
	const POST_JSON = 'json';
	const POST_METHOD = 'method';
	const POST_TYPE = 'type';
	const POST_DATA = 'data';
	
	const TYPE_JOBS = 'jobs';
	const TYPE_REQUESTS = 'requests';
	
	const METHOD_ADD = 'add';
	const METHOD_EDIT = 'edit';
	const METHOD_REMOVE = 'remove';
	
	public function index() {
		
		$this->load->helper(array('url', 'form'));
		
		// custom library for html css,js files and session
		$this->load->library(array('html_utility', 'session'));
		
		// check user's information
		if( ! isset($_SESSION) ){
			session_start(['read_and_close' => true, 'use_strict_mode' => '1']);
		}
		$this->checkUser();
		
		// header data
		$data = array(
			'title' => 'ניהול בקשות',
			'included_files' => array(
				'uri_css' => 'css/bootstrap.min.css',
				'uri_css_theme' =>  'css/bootstrap-theme.min.css',
				'uri_css_custom' => 'css/custom.css'
			)
		);
		$this->load->view('templates/header', $data);
		
		unset($data);
		$this->load->clear_vars(); // clear cached values
		$data = array();
		
		// Main view
		$this->load->view('manage');
		
		// footer data
		$data['included_files'] =  array(
				'uri_jquery_js' =>  'js/jquery-3.2.1.min.js',
				'uri_css_js' =>  'js/bootstrap.min.js',
				'uri_jquery_validation_js' =>  'js/jquery.validate.min.js',
				'uri_jquery_validation_additional_js' => 'js/additional-methods.min.js',
				'uri_jquery_validation_js_he' =>  'js/messages_he.js',
				'uri_manage_js' => 'js/manage.js'
			);
			
		// validate the add form - footer data
		$data['scripts'] = array(
			'$("#addForm").validate({
				errorPlacement: function(error, element){
					error.appendTo(element.parents(".form-group").find(".errorLabel"));
				},
				rules: {
					description: "lettersonly_spaces_numbers"
				}
			});'
		);
		$this->load->view('templates/footer', $data);
	}
	
	public function getJobs() {
		
		$this->load->model('manage_model');
		echo json_encode($this->manage_model->get_all_jobs());
	}
	
	public function handleAction() {
		
		$data = json_decode($this->input->post(self::POST_JSON, TRUE), TRUE);
		$this->load->model("manage_model");
		$model = $this->manage_model;
		
		// check that there's data
		if( isset($data[self::POST_DATA]) && count($data[self::POST_DATA]) > 0 ){
			switch($data[self::POST_TYPE]){
				case self::TYPE_JOBS:
					switch($data[self::POST_METHOD]){
						case self::METHOD_ADD:
							
							// check if the job description is valid 
							if( preg_match('/^[א-ת0-9 ]+$/', $data[self::POST_DATA][0]['description']) ){
								echo json_encode($model->add_job($data[self::POST_DATA]));
							}else{
								echo FALSE;
							}
							break;
						case self::METHOD_EDIT:
							echo json_encode($model->edit_job($data[self::POST_DATA]));
							break;
						case self::METHOD_REMOVE:
							echo json_encode($model->remove_job($data[self::POST_DATA]));
							break;
					}
					break;
				case self::TYPE_REQUESTS:
					switch($data[self::POST_METHOD]){
						case self::METHOD_EDIT:
							echo json_encode($model->edit_request($data[self::POST_DATA]));
							break;
						case self::METHOD_REMOVE:
							echo json_encode($model->remove_request($data[self::POST_DATA]));
							break;
					}
					break;
			}
		}
		unset($model);
	}
	
	public function getRequests() {
		$this->load->model('manage_model');
		echo json_encode($this->manage_model->get_all_requests(0,20));
	}
	
	/*
		execute Session GC after a certain time to clean old sessions.
		CI tempdata is used to set a flag, after it is destroyed, call session_gc
		
		ONLY WORKS WITH PHP 7.1.0+
	*/
	public function timedEvent() {
		
		// check php version, must be 7.1.0+
		// "7.1.0" -> [7,1,0]
		$version = phpversion();
		$version = explode( '.', phpversion() );
		
		// if main version number is above 7 there's no need to check secondary number
		if( ($version[0] > 7) || ($version[0] == 7 && $version[1] >= 1) ){
			$this->load->library('session');
		
			// public function - check if a user is accessing
			$this->checkUser();
			
			// Alternative to CRON
			// This is called by the user through an AJAX get request
			// Note: session_gc() is recommended to be used by task manager (CRON etc..)

			// Used for last GC time check
			if( ! isset($_SESSION['exp']) ){
				
				// Execute GC only when tempdata $_SESSION['exp'] was destroyed by the CI driver. 
				// i.e. Calling session_gc() every request is waste of resources. 
				session_gc();
				
				$_SESSION['exp'] = time(); // time has no real meaning except for causing confusion
				$this->session->mark_as_temp('exp', 600); // used as a flag for session_gc
			}
		}
	}
	
	private function checkUser() {
		$valid = TRUE;
		
		// check if user logged in correctly (Session)
		if( ! isset($_SESSION['username']) ){
			$this->load->model('manage_model');
			
			// check if user is valid
			if( empty($this->manage_model->get_user()) ){
				$valid = FALSE;
			}
		}
		
		if( ! $valid){
			session_destroy();
			redirect('login'); // return to login view
			exit();
		}
	}
}