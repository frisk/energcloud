<?php 
require_once('head.php');
require_once('nav.php');

$your_profile = FALSE;
if($session['id'] == $user->id)
{
	$your_profile = TRUE;
}
?>
    <div class="container cust-container">
        <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="cust-hero-unit">
        <div class="row">
        	<div class="span9">
	        	<?php
	        	if(isset($user))
	        	{
	        	?>
					<h1><?php echo $user->first_name . ' ' . $user->last_name; ?></h1>					
					<p>Email Address: <?php echo $user->email; ?></p>
					<p>Description: <?php echo $user->description; ?></p>
					<?php 
						if($your_profile)
						{
						 echo '<a href="' . base_url('users/edit/'.$user->id) . '">Edit Profile</a>';
						}
					?>
					<a href="<?php echo base_url('emodel/'.$user->id) ?>"><h3>View Emodels</h3></a>
					<h3>Leave a message
					<?php 
						if(!$your_profile)
						{
							echo ' for '.$user->first_name; 
						}
					?>
					</h3>
	        	<?php 
	        	}

	        	echo $posts;
	        	?>
        	</div>
        </div>
      </div>
    </div> <!-- /container -->
<?php 
	require_once('footer.php');
?>
