<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('main.php');

class Process extends Main {

	protected $id;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
	}

	public function login()
	{
		$this->view_data['meta'] = array(
					array('description', 'content' => 'Signin Page'),
					array('keywords', 'content' => 'signin')
					);

		$result = $this->User_model->authenticate();
		
		if($this->form_validation->run() === false)
		{
			$this->view_data['title'] = 'Login Failed';
			$this->load->view('signin', $this->view_data);
		}
		elseif(count($result) == 1)
		{
			if($result[0]->active == 1)
			{
				$this->load->view('signin');

				$user = array('id' => $result[0]->id,
								'email' => $result[0]->email,
							  'first_name' => $result[0]->first_name,
							  'last_name' => $result[0]->last_name,
							  'user_level' => $result[0]->user_level,
							  'logged_in' => true);

				$this->session->set_userdata('user_session', $user);
				
				$login_redirect = 'dashboard';
				if($result[0]->user_level == 'admin')
				{
					$login_redirect = 'dashboard/admin';
				}

				redirect(base_url($login_redirect));
			}
			else
			{
				$this->view_data['title'] = 'Login Failed';
				$this->view_data['login_error'] = 'Your account is no longer active';

				$this->load->view('signin', $this->view_data);
			}
		}
		else
		{
			$this->view_data['title'] = 'Login Failed';
			$this->view_data['login_error'] = 'Incorrect Login or Password';

			$this->load->view('signin', $this->view_data);
		}
	}

	private function user_validate($page_values)
	{
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
		
		$this->view_data['meta'] = array(
									array('description', 'content' => $page_values['meta']['description']),
									array('keywords', 'content' => $page_values['meta']['keywords'])
									);	

		if($this->form_validation->run('user_validate') === false)
		{		
			$this->view_data['title'] = $page_values['title']['fail'];
		}
		else
		{
			$this->User_model->register();
			
			$this->view_data['title'] = $page_values['title']['success'];
			$this->view_data['success'] = $page_values['success']['message'];
		}
		$this->view_data['new_user'] = $page_values['success']['new_user'];

		$this->load->view('register', $this->view_data);
	}

	public function registration()
	{
		$register_array = array(
									'meta' => array(
													'description' => 'Registration Page',
													'keywords' => 'register'
													),
									'title' => array(
													'fail' => 'Registration Failed',
													'success' => 'Registration Success'
													),
									'success' => array(
														'message' => 'You have successfully registered! Please login to see your page!',
														'new_user' => FALSE
														)
									);

		$this->user_validate($register_array);
	}

	public function new_user()
	{
		$new_user_array = array(
							'meta' => array(
											'description' => 'New User Page',
											'keywords' => 'new, users'
											),
							'title' => array(
											'fail' => 'Create New User Failed',
											'success' => 'Created New User'
											),
							'success' => array(
												'message' => 'User has been successfully added!',
												'new_user' => TRUE
												)
							);

		$this->user_validate($new_user_array);
	}

	private function edit($type)
	{
		$this->view_data['meta'] = array(
											array('description', 'content' => 'Editing Users Page'),
											array('keywords', 'content' => 'editing users')
										);
		if($this->form_validation->run($type) !== FALSE)
		{
			if(!$this->admin && $this->view_data['session']['id'] != $this->id)
			{
				$this->view_data['title'] = 'User Edit Failed';
				$this->view_data['failure'] = "You are not authorized to make changes to this account";
			}
			else
			{
				$this->User_model->update_user($this->id, $type, $this->admin, $this->user_access);
				$this->view_data['title'] = 'User Edit Success';
				$this->view_data['success'] = "You have successfully updated your information.";
			}
			
		}
		else
		{
			$this->view_data['title'] = 'User Edit Failed';
		}

		$this->view_data['user'] = $this->User_model->user($this->id);

		$this->load->view('edit', $this->view_data);
	}

	function remove($id)
	{
		if($this->admin)
		{
			$this->User_model->update_user($id, 'remove', $this->admin, $this->user_access);
			redirect(base_url('dashboard/admin'));
		}
		else
		{
			redirect(base_url('dashboard'));
		}
	}

	function edit_information($id)
	{
		$this->id = $id;
		$this->edit('edit_information');
	}

	function change_password($id)
	{
		$this->id = $id;
		$this->edit('change_password');
	}

	function edit_description($id)
	{
		$this->id = $id;
		$this->edit('edit_description');
	}

	function message($id)
	{
		$this->User_model->message($id, $this->view_data['session']['id'], 'add');
		redirect(base_url('users/show/'.$id));
	}

	function message_delete($id)
	{
		$this->User_model->message($id, '', 'remove');
		redirect(base_url('users/show/'.$this->input->post('user_id')));
	}	

	function comment($id)
	{
		$this->User_model->comment($id, $this->view_data['session']['id'], 'add');
		redirect(base_url('users/show/'.$this->input->post('user_id')));
	}

	function comment_delete($id)
	{
		$this->User_model->comment($id, '', 'remove');
		redirect(base_url('users/show/'.$this->input->post('user_id')));
	}
}

?>