<?php
require_once('head.php');
require_once('nav.php');

$chart = "$('#container').highcharts({
            chart: {
                type: 'line',
                marginRight: 130,
            },
            title: {
                text: '".$graph_title."',
                x: -20 //center
            },
            subtitle: {
                text: '".$subtitle."',
                x: -20
            },".$chart_info."
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [".$series."]
        });";
?>
<div class="container cust-container">
	<div class="btn-group cust-btn-group">
        <button id="sun_path" class="btn"><a href="<?php echo base_url('emodel/chart/'.$model_id.'/sunpath'); ?>">Sun Path Diagram</a></button>
		<button id="temperature" class="btn"><a href="<?php echo base_url('emodel/chart/'.$model_id.'/temperature'); ?>">Temperature</a></button>
		<button id="degree_days" class="btn"><a href="<?php echo base_url('emodel/chart/'.$model_id.'/degree_days'); ?>">Degree Days</a></button>
		<button id="kwh" class="btn"><a href="<?php echo base_url('emodel/chart/'.$model_id.'/kwh'); ?>">kWh Consumption</a></button>
	</div>
	<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
</div><!-- end container -->
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<script>
$(function () {
    <?php
        echo $chart; 
    ?>
});
</script>
<?php 
require_once('footer.php');
?>