<?php
class Form extends CI_Controller {

	const FNAME = 'firstname';
	const LNAME = 'lastname';
	const CITY = 'city';
	const ADDRESS = 'address';
	const EMAIL = 'email';
	const PHONE_NUM = 'phone_number';
	const JOB_TYPE = 'job_type';
	const DESCRIPTION = 'description';
	const IMAGES = 'images';
	
	public function index() {
		
		//$this->output->enable_profiler(TRUE);
		$this->load->helper(array('url', 'form'));
		$this->load->library('form_validation');
		
		// custom library for html css,js files
		$this->load->library('html_utility');
		
		// form validation rules
		$this->form_validation->set_rules(
			self::FNAME,
			'Firstname', 
			'min_length[1]|max_length[40]|required|regex_match[/^[א-ת ]+$/]',
			array('required' => 'שם פרטי לא תקין')
		);
		$this->form_validation->set_rules(
			'lastname', 
			'Lastname', 
			'min_length[1]|max_length[40]|required|regex_match[/^[א-ת ]+$/]',
			array('required' => 'שם משפחה לא תקין')
		);
		$this->form_validation->set_rules(
			self::CITY,
			'City', 
			'min_length[1]|max_length[40]|required|regex_match[/^[א-ת ]+$/]',
			array('required' => 'שם עיר לא תקין')
		);
		$this->form_validation->set_rules(
			self::ADDRESS,
			'Address', 
			'min_length[1]|max_length[60]|required|regex_match[/^[א-ת0-9 ]+$/]',
			array('required' => 'כתובת לא תקינה')
		);
		$this->form_validation->set_rules(
			self::EMAIL,
			'Email', 
			'min_length[3]|max_length[100]|required|valid_email',
			array('required' => 'כתובת אימייל לא תקינה')
		);
		$this->form_validation->set_rules(
			self::PHONE_NUM,
			'Phone Number', 
			'min_length[9]|max_length[10]|required|numeric',
			array('required' => 'מספר לא תקין')
		);
		$this->form_validation->set_rules(
			self::JOB_TYPE,
			'Job Type', 
			'min_length[1]|max_length[60]|required|regex_match[/^[א-ת ]+$/]',
			array('required' => 'סוג עבודה לא תקין')
		);
		$this->form_validation->set_rules(
			self::DESCRIPTION,
			'Description', 
			'min_length[1]|max_length[255]|required|regex_match[/^[א-תa-zA-Z,.\'-()! ]+$/]',
			array('required' => 'תיאור לא תקין')
		);
		
		if($this->form_validation->run()) {
			
			// run formSubmission()
			$this->formSubmit();
			return;
		}
		
		// header data
		$data = array(
			'title' => 'טופס בקשה לשירות',
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
		
		// load main data
		$this->load->model('client_form_model');
		$data = array(
			'jobs' => $this->client_form_model->get_all_jobs()
		);
		$this->load->view('client_form', $data);
		
		// clear the data and prepare for new data
		unset($data);
		$this->load->clear_vars(); // clear cached values
		$data = array();
		
		// validate the form (client side) - footer data
		// Rules: file upload: only images, max 2 files (each 2MB max [default])
		$data['scripts'] = array(
			'$("#client_form").validate({
				errorPlacement: function(error, element){
					error.appendTo(element.parents(".form-group").find(".errorLabel"));
				},
				rules: {
					firstname: "lettersonly",
					lastname: "lettersonly",
					city: "lettersonly_spaces",
					address: "lettersonly_spaces_numbers",
					phone_number: "digitsonly ilphone",
					description: "lettersonly_spaces_punctuation",
					"images[]": {accept: "image/*", maxFilesToSelect: [3], maxFileSize: true}
				}
			});'
		);
		
		// footer data
		$data['included_files'] =  array(
				'uri_jquery_js' =>  'js/jquery-3.2.1.min.js',
				'uri_css_js' =>  'js/bootstrap.min.js',
				'uri_jquery_validation_js' =>  'js/jquery.validate.min.js',
				'uri_jquery_validation_additional_js' => 'js/additional-methods.min.js',
				'uri_jquery_validation_js_he' =>  'js/messages_he.js',
				'uri_fileselect_js' => 'js/fileselect.js'
			);
		$this->load->view('templates/footer', $data);
	}
	
	// Update the database and upload pictures when form submission is valid
	private function formSubmit(){
		
		$valid = TRUE; // boolean if ALL images were uploaded with no errors
		
		// How many files were uploaded
		$filesCount = ( (empty($_FILES[self::IMAGES]['name'][0])) ? 0 : count($_FILES[self::IMAGES]['name']) );
		
		// check if files were uploaded, and no more than 3 of them
		if($filesCount >= 1 && $filesCount <= 3){
			
			// prepare for file upload
			$this->load->library('upload');

			// Make a directory if it doesn't exist (for the images, based on email)
			$temp_email = $this->input->post(self::EMAIL, TRUE);
			if( ! file_exists('./uploads/'.$temp_email) ){
				mkdir('./uploads/'.$temp_email, 0777, true);
			}
			
			// uploaded files configuration
			$config['upload_path'] = './uploads/'.$temp_email; //upload path based on email in DB
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_size'] = 2048;
			$config['max_filename_increment'] = 300;
			$config['file_ext_tolower'] = TRUE;
			$this->upload->initialize($config);
			
			// set the filepaths array, later uploaded to DB if successful
			$filesData = array();
			
			/* CodeIgniter's upload library cannot handle multiple files upload.
			*	The $this->upload->do_upload() function, can only handle one 1D associative array,
			*	therefore forcing to build an array for each file individually, 
			*	then passing it to the aforementioned function
			*/
			for($i = 0; $i < $filesCount; $i++){
				
				// prepare an individual array for each file in the 2D multiple files array
				$_FILES['image']['name'] = $this->security->sanitize_filename($_FILES[self::IMAGES]['name'][$i]);
				$_FILES['image']['type'] = $_FILES[self::IMAGES]['type'][$i];
				$_FILES['image']['tmp_name'] = $_FILES[self::IMAGES]['tmp_name'][$i];
				$_FILES['image']['error'] = $_FILES[self::IMAGES]['error'][$i];
				$_FILES['image']['size'] = $_FILES[self::IMAGES]['size'][$i];
				
				// when upload is succesful, store the image
				if( $this->upload->do_upload('image') ){
					
					// append the filepath to the filepath array, later uploaded to the DB
					$filesData[] = $this->upload->data();
				}else{
					$valid = FALSE;
					break;
				}
			}
		}
		
		//array for holding the results
		$result_array = array();
		
		// if image upload has failed, remove the client's folder and its contents
		if( ! $valid){
			
			// delete files associated with the email (unlink - possible delete, NOT certain)
			foreach($filesData as $file) {
				unlink($file['full_path']);
			}
			
			 // cancels the uploading of the filepaths array
			unset($filesData);
			
			// remove user's directory if empty
			// scandir returns '..' and '.' as default for empty directories (2 items in array)
			if( count(scandir($config['upload_path'])) == 2 ){
				rmdir($config['upload_path']);
			}
			
			// add an error to the results array, later viewed by the user
			$result_array[] = 'שגיאה: העלאת התמונות נכשלה';
		}else{
			
			// upload the client's data to the database
			$this->load->model('client_form_model');
			
			// get the next unique client ID
			$client_id = $this->client_form_model->next_client_id();
			
			if( ! $this->client_form_model->upload_client_details($client_id)){
				$result_array[] = 'שגיאת שרת: שליחת פרטיך נכשלו';
			}else{
				$result_array[] = 'פרטיך נשלחו בהצלחה';
			}
			
			// if image upload was succesful OR no images were uploaded
			if( isset($filesData) ){
				
				// upload the filepaths to the DB
				if(! $this->client_form_model->upload_images($client_id, $filesData)){
					$result_array[] = 'שגיאת שרת: העלאת התמונות נכשלה';
				}else{
					$result_array[] = 'התמונות נשלחו בהצלחה';
				}
			}
		}
		
		// prepare for new view
		$this->load->clear_vars(); // clear cached values
		$data = array();
		
		// header data
		$data = array(
			'title' => 'טופס בקשה לשירות',
			'included_files' => array(
				'uri_css' => 'css/bootstrap.min.css',
				'uri_css_theme' =>  'css/bootstrap-theme.min.css',
				'uri_css_custom' => 'css/custom.css'
			)
		);
		$this->load->view('templates/header', $data);
		
		// load result page
		$this->load->view('form_submit', array('messages' =>$result_array));
		
		// clear the data and prepare for new data
		unset($data);
		$this->load->clear_vars(); // clear cached values
		$data = array();
		
		// footer data
		$data['included_files'] =  array(
				'uri_jquery_js' =>  'js/jquery-3.2.1.min.js',
				'uri_css_js' =>  'js/bootstrap.min.js'
			);
		$this->load->view('templates/footer', $data);
	}
}