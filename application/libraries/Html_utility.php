<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This class is used to echo css and js scripts for HTML header/footer pages
class Html_utility {
	public $str = '';
	
		// only js files, string with <script>
		public function str_js($included_files){
			
			// Include all Javascript files
			if(isset($included_files) && count($included_files)){
				$this->str = '';
				
				while(current($included_files) !== FALSE){
					$this->str .= '<script src="'.base_url().current($included_files).'"></script>';
					
					// HTML readability
					if(next($included_files)){
						$this->str .= "\r\n\t";
					}else{
						$this->str .= "\r\n";
					}
				}
				return $this->str;
			}
		}
		
		// only css files string with <link rel
        public function str_css($included_files){
			
			// Include files in head based on it's extension
			if(isset($included_files) && count($included_files)){
				$this->str = '';
				
				while(current($included_files) !== FALSE){
					$this->str .= '<link rel="stylesheet" type="text/css" href="'.base_url().current($included_files).'">'; // CSS
					
					// HTML readability
					if(next($included_files)){
						$this->str .= "\r\n\t";
					}else{
						$this->str .= "\r\n";
					}
				}
				return $this->str;
			}
		}
		
		// both file types (js files must have 'js' in filename and same with 'css')
		public function str_js_css($included_files){
			
			// Include files in head based on it's extension
			if(isset($included_files) && count($included_files)){
				$this->str = '';
				
				while(current($included_files) !== FALSE){
					
					// Check if the suffix is '.js'
					if( strrpos(current($included_files), '.js') === (strlen(current($included_files)) - 3) ){
						$this->str .= '<script src="'.base_url().current($included_files).'"></script>'; // Javascript
					}else{
						
						$this->str .= '<link rel="stylesheet" type="text/css" href="'.base_url().current($included_files).'">'; // CSS
					}
					
					// HTML readability
					if(next($included_files)){
						$this->str .= "\r\n\t";
					}else{
						$this->str .= "\r\n";
					}
				}
				return $this->str;
			}
		}
		
		// execute short scripts in html page
		public function str_js_scripts($scripts){
			
			// Run short Javascript scripts
			if(isset($scripts) && count($scripts)){
				$this->str = '';
				
				while(current($scripts) !== FALSE){
					
					$this->str .= '<script>'.current($scripts).'</script>';
					
					// HTML readability
					if(next($scripts)){
						$this->str .= "\r\n\t";
					}else{
						$this->str .= "\r\n";
					}
				}
				
				return $this->str;
			}
		}
}