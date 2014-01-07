<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
</head>
<body>
	
	<h1>Welcome 
		<?php 
				echo $session['first_name'];
		?>!
	</h1>
	<h2>User Information</h2>
	<div id="user_info">
		<?
				echo '<p>First Name: '. $session['first_name'] .'</p>';
				echo '<p>Last Name: '. $session['last_name'] .'</p>';
				echo '<p>Email Address: '. $session['email'] .'</p>';
				echo '<a href="' . base_url() . 'main/logout">Logout</a>';
		?>
	</div>
</body>
</html>