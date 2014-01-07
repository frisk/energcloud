<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('main.php');

class Signin extends Main {
	
	function index()
	{
		if(isset($this->view_data['session']['logged_in']))
		{
			$admin = '';

			if($this->view_data['session']['user_level'] == 'admin')
			{
				$admin = '/admin';
			}
				redirect(base_url('dashboard'.$admin));
		}
		// Add meta tags for this page here 
		$this->view_data['meta'] = array(
									array('description', 'content' => 'Signin Page'),
									array('keywords', 'content' => 'signin')
									);

		// Add title to be displayed in the browser tab. 
		$this->view_data['title'] = 'Signin Page';

		// Add any additinoal stylesheets here.
		// $this->view_data['links'] = array(asset_url('css/style.css'));

		// Add any additional javascript files here.
		// $this->view_data['scripts'] = array(asset_url('js/sample_js.js'));

		// load the view and pass the view data
		$this->load->view('signin', $this->view_data);

	}
}