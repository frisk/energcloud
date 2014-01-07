    <!-- FOOTER -->
    <div class="container cust-container">
      <div class="navbar navbar-inverse cust-navbar">
        <div class="navbar-inner cust-navbar-inner">
          <footer>
            <p class="pull-right"><a href="#">Back to top</a></p>
            <p>&copy; 2013 Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
          </footer>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-alert.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-button.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-carousel.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-collapse.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-modal.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-popover.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-scrollspy.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-tab.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-tooltip.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-transition.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo asset_url('js/bootstrap-typeahead.js'); ?>"></script>
        <?php
    if(isset($scripts))
    {
      foreach($scripts as $script)
      {
        echo '<script type="text/javascript" src="'.$script.'"></script>';
      }
    }
    if(isset($javascript))
    {
    ?>
      <script type="text/javascript">
      $(function(){
        <?php
          echo $javascript;
        ?>
      })
      </script>
    <?php
    }
  ?>
    <script>
      $(function(){
        $(".icon").mouseover(function(){
          var src = $(this).attr('src')
          var srcHvr = src.replace(".gif","_hvr.gif");
          $(this).attr('src',srcHvr);
          $(this).css('cursor', 'pointer');
        });
        $(".icon").mouseout(function(){
          var srcHvr = $(this).attr('src');  
          var src = srcHvr.replace("_hvr.gif", ".gif");
          $(this).attr('src', src);
          $(this).css('cursor', 'default');
        })
      });
    </script>
  </body>
</html>