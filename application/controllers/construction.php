<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('main.php');

class Construction extends Main {
	
	function __construct()
	{
		parent::__construct();
		
		if(!$this->logged_in)
		{
			redirect(base_url('signin'));
		}

		$this->load->library('table');
		$this->load->model('Construction_model');
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
		$tmpl = array('table_open' => '<table class="table table-striped">');
		$this->table->set_template($tmpl);
	}

	function index()
	{
		// Add meta tags for this page here 
		$this->view_data['meta'] = array(
									array('description', 'content' => 'Create new component construction'),
									array('keywords', 'content' => 'energy, model, saving, construction')
									);

		// Add title to be displayed in the browser tab. 
		$this->view_data['title'] = 'eModel Construction';

		// Add any additinoal stylesheets here.
		// $this->view_data['links'] = array(asset_url('css/style.css'));

		// Add any additional javascript files here.
		// $this->view_data['scripts'] = array(asset_url('js/sample_js.js'));

		$this->view_data['table'] = $this->table->generate($this->Construction_model->constructions());

		// load the view and pass the view data
		$this->load->view('head', $this->view_data);
		$this->load->view('nav', $this->view_data);
		$this->load->view('construction/index', $this->view_data);
		$this->load->view('footer', $this->view_data);

	}

	function new_construction()
	{
		$query = $this->Construction_model->components();
		$this->view_data['options'] = '';

		foreach($query->result() as $results)
		{
			$this->view_data['options'] .= '<option value="'.$results->id.'">'.$results->name.'</option>';
		}
		$this->load->view('head', $this->view_data);
		$this->load->view('nav', $this->view_data);
		$this->load->view('construction/new', $this->view_data);
		$this->load->view('footer', $this->view_data);
	}

	function create()
	{
		$this->Construction_model->new_construction();

		redirect(base_url('construction'));
	}
}