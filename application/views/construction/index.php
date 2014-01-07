<div class="container cust-container">
    <!-- Main hero unit for a primary marketing message or call to action -->
  <div class="cust-hero-unit">
    <?php
    if(isset($admin))
    {
    ?>
    <div class="row">
      <div class="span6">
        <h2>Manage Materials</h2>
      </div>
      <div class="span6">
        <a class="btn btn-success cust-btn-space-top pull-right" href="<?php echo base_url('construction/new_construction'); ?>">Add new</a>
      </div>
    </div>
    <?php
    }
      echo $table
    ?>
  </div>
</div> <!-- /container -->