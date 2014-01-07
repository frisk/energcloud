<?php 
require_once('head.php');
require_once('nav.php');
?>
    <div class="container cust-container">
        <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="cust-hero-unit">
        <?php
        if(isset($admin))
        {
        ?>
        <div class="row">
          <div class="span6">
            <h2>Manage Users</h2>
          </div>
          <div class="span6">
            <a class="btn btn-success cust-btn-space-top pull-right" href="<?php echo base_url('users/new_user'); ?>">Add new</a>
          </div>
        </div>
        <?php
        }
          echo $table
        ?>
      </div>
    </div> <!-- /container -->
<?php 
require_once('footer.php')
?>