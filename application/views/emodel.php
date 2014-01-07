<?php
require_once('head.php');
require_once('nav.php');
?>
<style>
  .ui-tabs-vertical { width: 55em; }
  .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
  .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
  .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}
 </style>
<div class="container cust-container">
	<h1><?=(!$my_page ? $user->first_name.' '.$user->last_name.'\'s' : 'My') ?> eModel's</h1>
	<h3><a href="<?=base_url('users/show/'.($my_page ? $session['id'] : $user->id)) ?>">View Profile</a></h3>
	<?php 
		if($my_page)
		{
	?>
		<h2 class="cust-new-model">Add a New eModel</h2><img id="add_model" class="cust-plus" src="<?php echo asset_url('img/add_icn.gif'); ?>">
		<form id="new_emodel" action="<?php echo base_url('emodel/emod'); ?>" method="post">
			<input type="hidden" name="new_emodel" value="1">
		</form>
	<?php
			if(!$emodels)
			{
				?>
				<p>You haven't added any eModel's yet! Add a model and start reducing your energy consumption!</p>
				<?php
			}
		}
	
		if($emodels)
		{
			echo $emodels;
		}
		else if(!$my_page)
		{
		?>
			<p>This user has not created any models yet. Go back to their profile and encourage them to add one!</p>
		<?php
		}
	?>
</div><!-- end container -->
<script>

  $(function(){
  	var loc;
  	var frmId;

  	var addComponent = [];
  	var count = 0;
  	var orientation_count = {'north':1, 'south':1, 'east':1, 'west':1};
  	
  	$('')

  	function removeError(){
  		$('.alert').remove();
  	}

  	function removeTempInput()
  	{
  		$('.temp_input').remove();
  	}

  	$('#add_model').click(function(){
  		$('#new_emodel').submit();
  	})
	$('#new_emodel').submit(function(){
		$.post(
				$(this).attr('action'),
				$(this).serialize(),
				function(data){
					$('#new_emodel').after(data);
					$('#main-tabs').tabs();
					$( "#orientation-tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
					$( "#orientation-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
					$('#north_wall_save, #south_wall_save, #east_wall_save, #west_wall_save').click(function(){
						removeTempInput();
						var str_array = new Array();
						var formId = $(this).attr('id');
						var str_array = formId.split('_');
						var orientation = str_array[0]
						$.each($('.append_form_'+orientation), function(index){
							if($(this).get(0).tagName != "SELECT")
							{
								$('<input class="temp_input" type="hidden">').attr("name",$(this).attr('name')).attr("value",$(this).val()).appendTo('#'+orientation+'_wall');
							}
							else
							{
								$('<input class="temp_input" type="hidden">').attr("name",$(this).attr('name')).attr("value",$(this).find(":selected").val()).appendTo('#'+orientation+'_wall');
							}
						});
						$('#'+orientation+'_wall').submit();
					});
					$('.add_door, .add_window').click(function(){
						var loc = $(this).prev();
						// console.log(loc);
					
						var frmId = loc.attr('id');
						if(addComponent.indexOf(frmId) < 0 || count == 0)
						{	
								addComponent.push(frmId);
								count++;
							$('#'+frmId).submit(function(){
								$.post(
									$(this).attr('action'),
									$(this).serialize(),
									function(stuff){
										loc.before(stuff);
									}, 
									"json"
								);
								return false;
							});

							$('#'+frmId).submit();
						}
						else
						{
							counter = $('#'+frmId+' input[name=number]');
							counter_value = parseInt(counter.val())+1; 
							counter.val(counter_value);
							
							$('#'+frmId).submit();
						}
					});
				  	$('#model, #north_wall, #south_wall, #east_wall, #west_wall, #floor_form, #roof_form').submit(function(){
				  		removeError();
				  		var formId = $(this).attr('id');
				  		$.post(
				  			$(this).attr('action'),
				  			$(this).serialize(),
				  			function(data){
				  				$('#'+formId).before(data);
				  			},
				  			"json"
				  			);
				  		return false;
				  	});
				  	$('#save_model').click(function(){
				  		var model_id = $(this).attr('data-model-id');
				  		removeError();
				  		$('#model, #north_wall, #south_wall, #east_wall, #west_wall, #floor_form, #roof_form').submit();
				  		$.post('<?php echo base_url("emodel/save_model") ?>/'+model_id,
				  				function(data){
				  					$('#new_emodel').after(data.message);
				  					
				  					if(typeof data.model !== 'undefined')
				  					{
				  						$('#main-tabs').after(data.model);
				  						$('#main-tabs').remove();	
				  					}
				  				},'json');
				  	});
				  	$('#search_location').click(function(){
				  		removeError();
				  		var loc_data = $('#new_location').serialize();
				  		var p = $(this).parent();
				  		$.post('<?php echo base_url("emodel/add_location") ?>',
				  				loc_data,
				  				function(data){
				  					p.prepend(data.message);
				  					if(typeof data.row !== 'undefined')
				  					{
				  						$('#location').val(data.row.id);
				  						$('#city_state').text(data.row.city+', '+data.row.full_state);	
				  					}
				  				},'json');
				  	})

				},
				"json"
			);
		return false;
	})
  });
</script>
<?php 
require_once('footer.php');
?>