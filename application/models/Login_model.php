<?php
class Login_model extends CI_Model {
	
	const TABLE = 'Manage';
	const USER = 'username';
	const PASS = 'password';
	
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	public function get_user() {
		
		 // fetch the user specified from the table
		$query = $this->db->get_where(self::TABLE, array(
				self::USER => $this->input->post(self::USER, TRUE)
			)
		);
		return $query->row_array();
	}
}