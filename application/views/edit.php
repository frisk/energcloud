<?php 
// die(var_dump($user));
require_once('head.php');
require_once('nav.php');
?>
	    <div class="container cust-container">
	        <!-- Main hero unit for a primary marketing message or call to action -->
	        <div class="hero-unit cust-hero-unit">
	     		<div class="row">
	        		<div class="span5">
	          		<h3>Edit Profile</h3>
	          		</div>
	          		<div class="span4 pull-right">
	        				<a class="btn btn-primary" href="<?php echo base_url('dashboard'); ?>">Return to Dashboard</a>
	       			</div>
	       		</div>  
	          <?php 
	            if(isset($success))
	            {
	              echo '<div class="alert alert-success">' . $success . '</div>';
	            } 
	            elseif(isset($failure))
	            {
	            	echo '<div class="alert alert-error">' . $failure . '</div>';
	            }
	          ?>
	          	<div class="row">
	          		<div class="boxborder span4">
	          			<h5>Edit Information</h5>
						<?php echo form_open(base_url('process/edit_information/'.$user[0]->id)); ?>
			            <div class="control-group">
			              <?php echo form_error('email'); ?>
			              <label class="control-label" for="email">Email Address:</label>
			              <div class="controls">
			                <input type="text" name="email" value="<?php echo $user[0]->email;?>">
			              </div>
			            </div>
			            <div class="control-group">
			              <?php echo form_error('first_name'); ?>
			              <label class="control-label" for="first_name">First Name:</label>
			              <div class="controls">
			                <input type="text" name="first_name" value="<?php echo $user[0]->first_name;?>">
			              </div>
			            </div>
			            <div class="control-group">
			              <?php echo form_error('last_name'); ?>
			              <label class="control-label" for="last_name">Last Name:</label>
			              <div class="controls">
			                <input type="text" name="last_name" value="<?php echo $user[0]->last_name;?>">
			              </div>
		            	</div>
		            	<?php 
		            	if($session['user_level'] == 'admin' || $session['user_level'] == 'owner') 
		            	{
		            	?>
		          		<div class="control-group">
			              <?php echo form_error('user_level'); ?>
			              <label class="control-label" for="user_level">User Level:</label>
			              <div class="controls">
			                <select name="user_level">
			                	<?php
			                		if($session['user_level'] == 'owner')
			                		{
			                	 ?>
			                	 <option>Owner</option>
			                	 <?php 
			                	 	}
			                	 	else
			                	 	{
			                	 ?>
			                	<option value="normal" <?php if($user[0]->user_level == 'normal') { echo 'selected="selected"'; } ?>>Normal</option>
			                	<option value="admin" <?php if($user[0]->user_level == 'admin') { echo 'selected="selected"'; } ?>>Admin</option>
								<?php
									}
								?>
			               	</select>
			              </div>
		            	</div>
		            	<?php 
		            	} 
		            	?>
			            <div class="control-group">
			              <div class="controls">
			                <button type="submit" class="btn btn-success">Save</button>
			              </div>
			            </div>
			        	</form>
			         </div><!-- end span4 -->
			         <div class="boxborder span4 offset1">
			         	<h5>Change Password</h5>	            	
	        			<?php echo form_open(base_url('process/change_password/'.$user[0]->id)); ?> 
			           	<div class="control-group">
			              <?php echo form_error('password'); ?>
			              <label class="control-label" for="password">Password:</label>
			              <div class="controls">
			                <input type="password" name="password">
			              </div>
			            </div>
			            <div class="control-group">
			              <?php echo form_error('passconf'); ?>
			              <label class="control-label" for="passconf">Password Confirmation:</label>
			              <div class="controls">
			                <input type="password" name="passconf">
			              </div>
			            </div>
			            <div class="control-group">
			              <div class="controls">
			                <button type="submit" class="btn btn-success">Update Password</button>
			              </div>
			            </div>
		          		</form>
		          	</div><!-- end span4 offset1 -->
			  	</div><!-- end row -->
			  	<?php 
			  		if($session['user_level'] != 'admin')
			  		{
			  			echo form_open(base_url('process/edit_description/'.$user[0]->id)); 
			  	?>
				<div class="row">
					<div class="boxborder span9">
						<h5>Change Description</h5>	
						<div class="row">
							<div class="span9">
								<?php echo form_error('description'); ?>
								<textarea class="span9" name="description" id="description"></textarea>
							</div>
						</div>
						<div class="row">
							<div class="span1 offset8">
								<button class="btn btn-success" type="submit">Save</button>
							</div>
						</div>
					</div>
				</form>
				<?php 
					}
				?>
	      	</div><!-- hero-unit -->
	    </div> <!-- /container -->
<?php
require_once('footer.php');
?>