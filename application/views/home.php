<?php 
require_once('head.php');
require_once('nav.php');
?>
      <!-- Carousel
      ================================================== -->
      <div id="myCarousel" class="carousel slide cust-carousel">
        <div class="carousel-inner">
          <div class="item active">
            <img src="<?php echo asset_url('img/clouds-14v.jpg'); ?>" alt="">
            <div class="container">
              <div class="carousel-caption cust-carousel-caption">
                <h1 class="cust-emodel">eModel</h1>
                <p class="lead cust-lead">Create a baseline energy model of your new building or existing home.</p>
                <a class="btn btn-large btn-primary" href="<?php echo base_url('/register'); ?>">Sign up today</a>
              </div>
            </div>
          </div>
          <div class="item">
            <img src="<?php echo asset_url('img/lightning.jpg'); ?>" alt="">
            <div class="container">
              <div class="carousel-caption cust-carousel-caption">
                <h1 class="cust-data">Data</h1>
                <p class="lead cust-lead">See the monthly temperature, heating and cooling degree days, kwh consumption and CO<sub>2</sub> In a nicely formatted graph.</p>
              </div>
            </div>
          </div>
          <div class="item">
            <img src="<?php echo asset_url('img/forestblog.jpg'); ?>" alt="">
            <div class="container">
              <div class="carousel-caption cust-carousel-caption">
                <h1 class="cust-finance">Finance</h1>
                <p class="lead cust-lead">See the estimated monthly cost of your kWh consumption. Also see a breakdown of where you can improve energy losses.</p>
              </div>
            </div>
          </div>
        </div>
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
      </div><!-- /.carousel -->
    </div><!-- /.container -->
    <script>
      !function ($) {
        $(function(){
          // carousel demo
          $('#myCarousel').carousel()
        })
      }(window.jQuery)
    </script>
<?php 
  require_once('footer.php');
?>