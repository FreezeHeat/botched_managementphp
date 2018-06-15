<?php
class Manage_model extends CI_Model {
	
	// Manage table
	const TABLE_MANAGE = 'Manage';
	const MANAGE_USER = 'username';
	const MANAGE_PASS = 'password';
	
	// Clients table
	const TABLE_CLIENTS = 'Clients';
	const CLIENT_CID = 'client_id';
	const CLIENT_FNAME = 'firstname';
	const CLIENT_LNAME = 'lastname';
	const CLIENT_CITY = 'city';
	const CLIENT_ADDRESS = 'address';
	const CLIENT_EMAIL = 'email';
	const CLIENT_PHONE_NUM = 'phone_number';
	const CLIENT_JOB_TYPE = 'job_type';
	const CLIENT_DESCRIPTION = 'description';
	const CLIENT_STATUS = 'status';
	const CLIENT_REQUEST_DATE = 'request_date';
	
	// Image table
	const TABLE_IMG = 'Images';
	const IMG_CID = 'client_id';
	const IMG_IMAGES = 'images_path';
	
	// Jobs table
	const TABLE_JOBS = 'Jobs';
	const JOB_ID = 'job_id';
	const JOB_DESCRIPTION = 'description';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	// the $limit and $offset are used for pagination
	public function get_all_requests($offset=0, $limit = NULL) {
		
		 // fetch all client requests with image paths
		 $array = ($this->db->get(self::TABLE_CLIENTS, $limit, $offset))->result_array();
		 
		 // get all, but with formatted timestamp
		 $array = ($this->db->query(
			'SELECT '.self::CLIENT_CID.' ,'.
			self::CLIENT_FNAME.' ,'.
			self::CLIENT_LNAME.' ,'.
			self::CLIENT_CITY.' ,'.
			self::CLIENT_ADDRESS.' ,'.
			self::CLIENT_EMAIL.' ,'.
			self::CLIENT_PHONE_NUM.' ,'.
			self::CLIENT_JOB_TYPE.' ,'.
			self::CLIENT_DESCRIPTION.' ,'.
			self::CLIENT_STATUS.
			', DATE_FORMAT('.self::CLIENT_REQUEST_DATE.', \'%d/%m/%Y %H:%i:%S\') AS '.self::CLIENT_REQUEST_DATE.' '.
			'FROM '. $this->db->protect_identifiers(self::TABLE_CLIENTS).' WHERE '.self::CLIENT_CID.' BETWEEN '.$offset.' AND '.$limit.';'
		 ))->result_array();
		 
		 //get only the images from the specified client range
		 $images = ($this->db->query(
			'SELECT * FROM '. $this->db->protect_identifiers(self::TABLE_IMG).' WHERE '.self::IMG_CID.' BETWEEN '.$offset.' AND '.$limit.';'
		 ))->result_array();
		 
		 // The whole process below unifies the clients array
		 // with the images array data based on client_id
		 $client_id_array = array_column($images, self::CLIENT_CID);
		foreach($array as &$client){
			
			//get the image position based on client_id
			$img_position = array_search($client[self::CLIENT_CID], $client_id_array);
			if($img_position !== FALSE){
				
				// append the image paths to the client array so a unified array will be sent
				$client[self::IMG_IMAGES] = array();
				
				//There can be a maximum of 3 pictures per client_id
				for(; $img_position !== count($images); $img_position++){
					
					if($client[self::CLIENT_CID] === $images[$img_position][self::CLIENT_CID]){
						$client[self::IMG_IMAGES][] = strstr($images[$img_position][self::IMG_IMAGES], '/kobi/');
					}else{
						break;
					}
				}
			}
		}
		return $array;
	}
	
	
	// Client requests methods
	// public function add_request($data){
		// if( ! $this->db->insert('table', 
		// array('column' => 'value')) ){
			// return 'שגיאה';
		// }else{
			// return TRUE;
		// }
	// }
	
	public function remove_request($data) {
		$where_clause_clients = self::CLIENT_CID." IN (".$data[0][self::CLIENT_CID];
		$where_clause_images = self::CLIENT_CID." IN (".$data[0][self::CLIENT_CID];
		foreach($data as $job){
			$where_clause_clients .= ', '.$job[self::CLIENT_CID];
			$where_clause_images .= ', '.$job[self::CLIENT_CID];
		}
		$where_clause_clients .= ")";
		$where_clause_images .=")";		
		
		// Both the images and the client are removed from the DB
		if( ! ( $this->db->delete(self::TABLE_CLIENTS, $where_clause_clients) && $this->db->delete(self::TABLE_IMG, $where_clause_images) ) ){
			return 'שגיאה';
		}else{
			return TRUE;
		}
	}
	
	public function edit_request($data) {
		
		// prepare data for update batch
		$data_array = array();
		foreach($data as $client){
			$data_array[] = array(
				self::CLIENT_CID => $client[self::CLIENT_CID],
				self::CLIENT_FNAME => $client['values'][0], 
				self::CLIENT_LNAME => $client['values'][1],
				self::CLIENT_CITY => $client['values'][2],
				self::CLIENT_ADDRESS => $client['values'][3],
				self::CLIENT_EMAIL => $client['values'][4],
				self::CLIENT_PHONE_NUM => $client['values'][5],
				self::CLIENT_JOB_TYPE => $client['values'][6],
				self::CLIENT_DESCRIPTION => $client['values'][7],
				self::CLIENT_STATUS  => $client['values'][8]
			);
		}
		
		if( ! $this->db->update_batch(self::TABLE_CLIENTS, $data_array, self::CLIENT_CID) ){
			return 'שגיאה';
		}else{
			return TRUE;
		}
	}
	
	
	// Jobs (Services) methods
	public function get_all_jobs(){
		$query = $this->db->get(self::TABLE_JOBS);
		return $query->result_array();
	}
	
	public function add_job($data) {
		if( ! 
		$this->db->insert(self::TABLE_JOBS, 
		array(self::JOB_DESCRIPTION => $data[0][self::JOB_DESCRIPTION])) ) {
			return 'שגיאה';
		}else{
			return TRUE;
		}
	}
	
	public function remove_job($data) {
		$where_clause = self::JOB_ID." IN (".$data[0][self::JOB_ID];
		foreach($data as $job){
			$where_clause .= ', '.$job[self::JOB_ID];
		}
		$where_clause .= ")";
		if( ! $this->db->delete(self::TABLE_JOBS, $where_clause) ) {
			return 'שגיאה';
		}else{
			return TRUE;
		}
	}
	
	public function edit_job($data) {
		if( ! $this->db->update_batch(self::TABLE_JOBS, $data, self::JOB_ID) ){
			return 'שגיאה';
		}else{
			return TRUE;
		}
	}
	
	public function get_user() {
	
		 // fetch the user specified from the table
		$query = $this->db->get_where(self::TABLE_MANAGE, array(
				self::MANAGE_USER => $this->security->xss_clean($_SESSION[self::MANAGE_USER])
			)
		);
		return $query->row_array();
	}
}