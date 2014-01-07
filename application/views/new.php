<?php 
require_once('head.php');
require_once('nav.php');
?>
    <div class="container">
        <!-- Main hero unit for a primary marketing message or call to action -->
        <div class="hero-unit">
          <h3>Register</h3>
          <?php 
            if(isset($success))
            {
              echo $success;
            }
            echo form_open(base_url('process/registration')); 
          ?>
            <div class="control-group">
              <?php echo form_error('email'); ?>
              <label class="control-label" for="email">Email Address:</label>
              <div class="controls">
                <input type="text" name="email">
              </div>
            </div>
            <div class="control-group">
              <?php echo form_error('first_name'); ?>
              <label class="control-label" for="first_name">First Name:</label>
              <div class="controls">
                <input type="text" name="first_name">
              </div>
            </div>
            <div class="control-group">
              <?php echo form_error('last_name'); ?>
              <label class="control-label" for="last_name">Last Name:</label>
              <div class="controls">
                <input type="text" name="last_name">
              </div>
            </div>
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
                <button type="submit" class="btn btn-success">Register</button>
              </div>
            </div>
          </form>

          <a href="<?php echo base_url('signin'); ?>">Already have an account? Login</a>
        </div>
      </div>
    </div> <!-- /container -->
	</body>
</html>