<?php 
	echo doctype('html5');
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php
		echo meta($meta);
		echo '<title>'.$title.'</title>';
		echo link_tag(asset_url('css/bootstrap-responsive.css'));
		echo link_tag(asset_url('css/bootstrap.css'));
		echo link_tag(asset_url('css/bootstrap-custom.css'));
	    echo link_tag(asset_url('css/style.css'));
	    echo link_tag('http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css');
        echo link_tag('http://fonts.googleapis.com/css?family=Ubuntu');
		
		if(isset($links))
		{
			foreach($links as $link)
			{
				echo link_tag($link);
			}
		}
		?>
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    	<!--[if lt IE 9]>
      	<script src="../assets/js/html5shiv.js"></script>
    	<![endif]-->
    	<script type="text/javascript" src="<?php echo asset_url('js/jquery.js'); ?>"></script>
    	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js"></script>
</head>
<body>