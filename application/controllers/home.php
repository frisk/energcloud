<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('main.php');

class Home extends Main {

	function index()
	{
		// Add meta tags for this page here 
		$this->view_data['meta'] = array(
									array('description', 'content' => 'This is the Home Page'),
									array('keywords', 'content' => 'home, page, coding, dojo, mvc')
									);

		// Add title to be displayed in the browser tab. 
		$this->view_data['title'] = 'Home Page';

		// Add any additinoal stylesheets here.
		// $this->view_data['links'] = array(asset_url('css/style.css'));

		// Add any additional javascript files here.
		// $this->view_data['scripts'] = array(asset_url('js/sample_js.js'));

		// load the view and pass the view data
		$this->load->view('home', $this->view_data);

	}
}