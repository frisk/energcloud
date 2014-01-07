<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('main.php');

class Register extends Main {
	
	function index()
	{
		// Add meta tags for this page here 
		$this->view_data['meta'] = array(
									array('description', 'content' => 'Registration Page'),
									array('keywords', 'content' => 'register, account, new')
									);

		// Add title to be displayed in the browser tab. 
		$this->view_data['title'] = 'Register';

		// Add any additinoal stylesheets here.
		// $this->view_data['links'] = array(asset_url('css/style.css'));

		// Add any additional javascript files here.
		// $this->view_data['scripts'] = array(asset_url('js/sample_js.js'));

		// load the view and pass the view data
		$this->load->view('register', $this->view_data);

	}
}