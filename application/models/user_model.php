<?php

class User_model extends CI_Model{

	public function authenticate()
	{
		$sql = "SELECT users.*, access.user_level
				FROM users 
				JOIN access ON users.access_id = access.id
				WHERE users.email = ? AND users.password = ?";

		return $this->db->query($sql, array($this->input->post('email'), md5($this->input->post('password'))))->result();
	}

	public function register($admin = FALSE)
	{
		$user_level = 2;

		if($admin)
		{
			$user_level = 575;
		}

		$sql = "INSERT INTO users (email, first_name, last_name, password, access_id, created, modified) VALUES(?, ?, ?, ?, $user_level, NOW(), NOW())";
		$this->db->query($sql, array('email' => $this->input->post('email'), 'first_name' => $this->input->post('first_name'), 'last_name' => $this->input->post('last_name'), 'password' => $this->input->post('password')));
	}

	public function user($id)
	{
		$sql = "SELECT users.*, access.user_level 
				FROM users
				JOIN access ON users.access_id = access.id 
				WHERE users.id = ? AND active = 1";

		return $this->db->query($sql, array($id))->row(); 
	}

	public function users()
	{
		$user_link_start = '<a href="' . base_url() . 'users/show/';
		$user_link_mid = '">';

		$sql = "SELECT users.id ID, CONCAT('" . $user_link_start . "',users.id, '" . $user_link_mid . "',users.first_name,' ',users.last_name,'</a>') Name, users.email, DATE_FORMAT(users.created, '%b %D %Y') created, access.user_level 
				FROM users 
				JOIN access ON users.access_id = access.id
				WHERE active = 1";

		return $this->db->query($sql);
	}

	public function admin_users()
	{
		$user_link_start = '<a href=\"' . base_url() . 'users/show/';
		$user_link_mid = '\">';

		$edit_link_start = '<a href=\"' . base_url() . 'users/edit/';
		$edit_link_mid = '\">';

		$remove_link_start = '<a id="remove" href=\"' . base_url() . 'process/remove/';
		$remove_link_mid = '\">';

		$sql = "SELECT users.id ID, CONCAT('" . $user_link_start . "',users.id, '" . $user_link_mid . "',users.first_name,' ',users.last_name,'</a>') Name, users.email, DATE_FORMAT(users.created, '%b %D %Y') created, access.user_level, CONCAT('" . $edit_link_start . "',users.id, '" . $edit_link_mid . "','edit</a>',' ','" . $remove_link_start . "',users.id, '" . $remove_link_mid . "','remove</a>') actions  
				FROM users 
				JOIN access ON users.access_id = access.id
				WHERE users.active = 1";

		return $this->db->query($sql);
	}

	public function show_user($id)
	{
		$sql = "SELECT messages.id message_id, messages.user_sent_id message_sender_id, messages.user_reciever_id message_recieved_id, messages.content message, messages.created message_created, comments.id comment_id, comments.users_id comment_recieved_id, comments.content comment, comments.display_flag display_comment, comments.created comment_created, users.first_name message_first_name, users.last_name message_last_name, u2.first_name comment_first_name, u2.last_name comment_last_name
				FROM users 
				LEFT JOIN messages ON users.id = messages.user_sent_id 
				LEFT JOIN comments ON messages.id = comments.messages_id
				LEFT JOIN users u2 ON comments.users_id = u2.id 
				WHERE messages.user_reciever_id = ?
				AND messages.display_flag = 1
				ORDER BY messages.id DESC, messages.created DESC, comments.created ASC";
		// die(htmlentities($sql));
		return $this->db->query($sql, array($id));
	}

	function update_user($id, $update, $admin = FALSE, $access)
	{
		switch($update)
		{
			case 'edit_information':
				$sql = "UPDATE users SET email = ?, first_name = ?, last_name = ?";
				$posts = array($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'));
				if($admin && !empty($access[$this->input->post('user_level')]))
				{
					$sql .= ", access_id = ?";
					$posts[] = $access[$this->input->post('user_level')];
				}
				$posts[] = $id;
				$sql .= ", modified = NOW() WHERE id = ?";
			break;
			case 'change_password':
				$sql = "UPDATE users SET password = ?, modified = NOW()
						WHERE id = ?";
				$posts = array($this->input->post('password'), $id);
			break;
			case 'edit_description':
				$sql = "UPDATE users SET description = ?, modified = NOW()
						WHERE id = ?";
				$posts = array($this->input->post('description'), $id);
			break;
			case 'remove':
				$sql = "UPDATE users SET active = 0, modified = NOW()
						WHERE id = ?";
				$posts = array($id);
			break;
		}

		if(!$this->db->query($sql, $posts))
		{
			return FALSE;
		}

		return TRUE;
	}

	function message($id, $user_sent_id, $mode)
	{
		if($mode == 'add')
		{
			$sql = "INSERT INTO messages (user_sent_id, user_reciever_id, content, created, modified) 
					VALUES(?,?,?, NOW(), NOW())";
			$posts = array($user_sent_id, $id, $this->input->post('new_message'));

			if(!$this->db->query($sql, $posts))
			{
				return FALSE;
			}
		}
		elseif($mode == 'remove')
		{
			$sql_message_remove = "UPDATE messages SET display_flag = 0, modified = NOW() 
									WHERE id = ?";
			$message_posts = array($id);

			if(!$this->db->query($sql_message_remove, $message_posts))
			{
				return FALSE;
			}

			$sql_comment_remove = "UPDATE comments SET display_flag = 0, modified = NOW()
									WHERE messages_id = ?";
			$comment_posts = array($id);

			if(!$this->db->query($sql_comment_remove, $comment_posts))
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	function comment($id, $user_id, $mode)
	{
		if($mode == 'add')
		{
			$sql = "INSERT INTO comments (messages_id, users_id, content, created, modified) 
					VALUES(?,?,?, NOW(), NOW())";
			$posts = array($id, $user_id, $this->input->post('new_comment'));

			if(!$this->db->query($sql, $posts))
			{
				return FALSE;
			}
		}
		elseif($mode == 'remove')
		{
			$sql_comment_remove = "UPDATE comments SET display_flag = 0, modified = NOW()
									WHERE id = ?";
			$comment_posts = array($id);

			if(!$this->db->query($sql_comment_remove, $comment_posts))
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}

?>