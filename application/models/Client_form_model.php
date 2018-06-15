<?php
class Client_form_model extends CI_Model {
	
	// Clients table
	const TABLE_CLIENTS = 'Clients';
	const CID = 'client_id';
	const FNAME = 'firstname';
	const LNAME = 'lastname';
	const CITY = 'city';
	const ADDRESS = 'address';
	const EMAIL = 'email';
	const PHONE_NUM = 'phone_number';
	const JOB_TYPE = 'job_type';
	const DESCRIPTION = 'description';
	const STATUS = 'status';
	const REQUEST_DATE = 'request_date';
	
	// Image table
	const TABLE_IMG = 'Images';
	const IMAGES = 'images_path';
	
	// Jobs table
	const TABLE_JOBS = 'Jobs';
	const JOB_DESCRIPTION = 'description';
	
	public function __construct() {
		
		parent::__construct();
		$this->load->database();
	}
	
	// insert the clients' details into the Clients table
	public function upload_client_details($client_id) {
		
		$data = array(
			self::CID => $client_id,
			self::FNAME => $this->input->post(self::FNAME, TRUE),
			self::LNAME => $this->input->post(self::LNAME, TRUE),
			self::CITY => $this->input->post(self::CITY, TRUE),
			self::ADDRESS => $this->input->post(self::ADDRESS, TRUE),
			self::EMAIL => $this->input->post(self::EMAIL, TRUE),
			self::PHONE_NUM => $this->input->post(self::PHONE_NUM, TRUE),
			self::JOB_TYPE => $this->input->post(self::JOB_TYPE, TRUE),
			self::DESCRIPTION => $this->input->post(self::DESCRIPTION, TRUE),
			self::STATUS => 0
		);
		
		return $this->db->insert(self::TABLE_CLIENTS, $data, TRUE);
	}
	
	// if the user uploaded images, store in a folder and specify paths in Images table
	public function upload_images($client_id, $filesData){
		
		// insert each file into the array, then upload to DB
		$data = array();
		foreach($filesData as $file){
			$data[] = array(
				self::CID => $client_id,
				self::IMAGES => $file['full_path']
			);
		}
		
		return $this->db->insert_batch(self::TABLE_IMG, $data, NULL, 3);
	}
	
	// get the next client ID based on the available numbers
	public function next_client_id() {
		$this->db->protect_identifiers(self::TABLE_CLIENTS);
		$result = $this->db->query('SELECT MAX('.self::CID.') AS '.self::CID.'_MAX, '.
												'MIN('.self::CID.') AS '.self::CID.'_MIN '.
												'FROM '.self::TABLE_CLIENTS.';'
												);
		$result = $result->row_array();
		
		// this is not perfect, but it's better than AUTO_INCREMENT
		// any number bigger than one means you can use a lower number
		// if and ID of one is present, just use MAX ID + 1 as the next ID
		if( $result[self::CID.'_MIN'] > 1 ){
			return $result[self::CID.'_MIN'] - 1;
		}else{
			return $result[self::CID.'_MAX'] + 1;
		}
	}
	
	// Return jobs available for the clients to choose from (HTML select)
	public function get_all_jobs(){
		$query = $this->db->get(self::TABLE_JOBS);
		return $query->result_array();
	}
}