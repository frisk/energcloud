    <div class="container cust-container">
      <div id="cust-header">
        <a href="<?php echo base_url('home'); ?>">
          <img id="cust-logo" src="<?php echo asset_url('img/logo.gif'); ?>" width="200">
          <h1 id="cust-company-name">nerGcloud</h1>
        </a>
        <div id="signin">
            <?php 
              if(!isset($session['logged_in'])) 
              {
            ?>
            Don't have an account? <a href="<?php echo base_url('register'); ?>">Sign up!</a>
            <?php echo form_open(base_url('process/login'), 'class="pull-right"'); ?>
                <input class="span2 cust-input" type="text" name="email" placeholder="Email">
                <input class="span2 cust-input" type="password" name="password" placeholder="Password">
                <button type="submit" class="btn btn-primary">Signin</button>
            </form>
            <?php 
              }
              else
              {
                echo '<a class="btn btn-danger cust-btn-space-top pull-right" href="' .base_url('main/logout').'">Logout</a>';
                echo '<a class="btn btn-primary cust-btn-space-top pull-right" href="'.base_url('users/show/'.$session['id']).'">My Profile</a>';
              }
            ?>
        </div>
      </div>
      <div class="clear"></div>
    <!-- NAVBAR
    ================================================== -->
      <div class="navbar navbar-inverse cust-navbar">
        <div class="navbar-inner cust-navbar-inner">
          <!-- Responsive Navbar Part 1: Button for triggering responsive navbar (not covered in tutorial). Include responsive CSS to utilize. -->
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <!-- Responsive Navbar Part 2: Place all navbar contents you want collapsed withing .navbar-collapse.collapse. -->
          <div class="nav-collapse collapse cust-collapse">
            <?php
            if(isset($admin))
            {
            ?>
            <a href="<?php echo base_url('construction') ?>"><img class="icon" src="<?php echo asset_url('/img/materials_wht.gif'); ?>"></a>
            <?php
            }
            ?>
            <a href="<?php echo base_url('dashboard') ?>"><img class="icon" src="<?php echo asset_url('/img/user_wht.gif'); ?>"></a>
            <img class="icon" src="<?php echo asset_url('/img/finance_icon_wht.gif'); ?>">
            <a href="<?php echo base_url('emodel') ?>"><img class="icon" src="<?php echo asset_url('/img/emodel_icon_wht.gif'); ?>"></a>
          </div><!--/.nav-collapse -->
        </div><!-- /.navbar-inner -->
      </div><!-- /.navbar -->