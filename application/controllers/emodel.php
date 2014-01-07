<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('main.php');

class Emodel extends Main {
	
	function __construct()
	{
		parent::__construct();
		
		if(!$this->logged_in)
		{
			redirect(base_url('signin'));
		}
		$this->load->model('User_model');
		$this->load->model('Energ_model');
		$this->load->model('Construction_model');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
	}

	function index($id = '')
	{
		// Add meta tags for this page here 
		$this->view_data['meta'] = array(
									array('description', 'content' => 'Create a business or residential energy model.'),
									array('keywords', 'content' => 'energy, model, saving')
									);

		// Add title to be displayed in the browser tab. 
		$this->view_data['title'] = 'eModel';
		$this->view_data['emodels'] = '';
		$this->view_data['my_page'] = false;
		if($id)
		{
			$this->view_data['user'] = $this->User_model->user($id);
			$result = $this->Energ_model->get_emodels($id);

			if($this->view_data['user']->id == $this->view_data['session']['id']) 
			{
				$this->view_data['my_page'] = true;
			}
		}
		else
		{
			$result = $this->Energ_model->get_emodels($this->view_data['session']['id']);	
			$this->view_data['my_page'] = true;
		}
		
		if($result)
		{
			foreach($result as $row)
			{
				$this->view_data['emodels'] .= $this->display_emodel($row->model_id, $row->name, $row->location, $row->type);
			}	
		}
		
		// Add any additinoal stylesheets here.
		// $this->view_data['links'] = array(asset_url('css/style.css'));

		// Add any additional javascript files here.
		// $this->view_data['scripts'] = array(asset_url('js/sample_js.js'));

		// load the view and pass the view data
		$this->load->view('emodel', $this->view_data);

	}

	private function _get_construction($component)
	{
		$q_const = $this->Construction_model->construction_list($component);
		$options = '';
		foreach($q_const->result() as $construction)
		{
			$options .= '<option value="'.$construction->id.'">'.$construction->construction.'</option>';
		}

		return $options;
	}

	private function return_wall($id)
	{
		$direction = array('North' => 'north', 'South' => 'south', 'East' => 'east', 'West' => 'west');

		$wall = '';
		
		foreach($direction as $key => $orientation)
		{
			$wall .= '<div id="'.$orientation.'">';
			$wall .= '<form id="'.$orientation.'_wall" action="'.base_url('emodel/update_wall/'.$id[$orientation]).'" method="post">
					<h4>' . $key . ' Wall</h4>
					<div class="cust-wall">
							<label class="cust-area" for="area">Area</label>
							<input type="text" name="area" placeholder="total sqft">
							<label class="cust-area" for="construction_type">Construction Type</label>
							<input type="hidden" name="orientation" value="' . $orientation . '">';

			// here we would query the database
			// to get all the current wall construction
			// types. Eventually we want to add the ability
			// to add additional construction types.					

			$wall .=		'<select name="construction_type">';
			$wall .= $this->_get_construction('wall');
			$wall .=	'</select>
					</div>
					<h4>Windows</h4>
					</form>
					<div class="cust-windows">
						<form id="add_window_input_'.$orientation.'" action="' . base_url('emodel/display_window_input/'.$id[$orientation]) . '" method="post">
							<input type="hidden" name="number" value="1">
							<input type="hidden" name="orientation" value="'.$orientation.'">
						</form>
						<p class="add_window">Add Window <img class="cust-add" src="' . asset_url('img/add_icn.gif') . '"></p>						
					</div>
					<h4>Doors</h4>
					<div class="cust-doors">
						<form id="add_door_input_'.$orientation.'" action="' . base_url('emodel/display_door_input/'.$id[$orientation]) . '" method="post">
							<input type="hidden" name="number" value="1">
							<input type="hidden" name="orientation" value="'.$orientation.'">
						</form>
						<p class="add_door">Add Door <img class="cust-add" src="' . asset_url('img/add_icn.gif') . '"></p>
					</div>
					<div class="cust-update-btn">
						<input id="'.$orientation.'_wall_save" class="btn" value="Update '.$key.' Wall">
					</div>';
			$wall .= '</div>';
		}

		return $wall;
	}

	function add_location()
	{
		$result = $this->Energ_model->get_zip();

		if(is_array($result))
		{
			
			echo json_encode($result);
		}
		else
		{
			$data['message'] = '<div class="alert alert-error">'.$result.'</div>'; 
			echo json_encode($data);
		}
	}

	function emod($id = '')
	{
		// insert an empty row into the emodel
		// table and return the insert id

		// if the id is empty then display a new model
		// otherwise populate the model with all the 
		if(!$id)
		{
			
			$insert_id = $this->Energ_model->new_emodel($this->view_data['session']['id']);
		}
		$option = '';
		$query = $this->Energ_model->get_locations();
		foreach($query->result() as $row)
		{
			$option .= '<option value="'.$row->id.'">'.$row->city.', '.$row->state.'</option>';
		}

		$display_emodel = '<div id="main-tabs" class="cust-tabs">
		<ul>
			<li><a href="#model_info">Model Info</a></li>
			<li><a href="#walls">Walls</a></li>
			<li><a href="#floor">Floor</a></li>
			<li><a href="#roof">Roof</a></li>
			<li class="cust-save-btn"><button id="save_model" data-model-id="'.$insert_id["model_id"].'" class="btn btn-primary">Save Model</button></li>
		</ul>
		<div id="model_info">
			<form id="model" action="' . base_url('emodel/update_model_info/'.$insert_id["model_id"]).'" method="post">
				<label class="cust-model" for="model_name">Model Name:</label>
				<input type="text" name="model_name" placeholder="Enter Model Name">
				<label class="cust-model" for="location">Location: <span id="city_state"></span></label>
				<div>
					<label class="cust-model" for="location">Enter your zip to see if it is available:</label>
					<input type="text" name="new_location" id="new_location">
					<input type="hidden" name="location" id="location">
					<button type="button" id="search_location" class="btn btn-success">Search</button>
				</div>
				<div class="cust-building-type">
					<label for="type">Select Building Type:</label>
					<input type="radio" name="type" value="1" checked><p>Residential</p>
					<input type="radio" name="type" value="2"><p>Business</p>
				</div>
				<div>
					<input id="model_update" type="submit" class="btn" value="Update Model Info">
				</div>
			</form>
		</div>
		<div id="walls">
			<div id="orientation-tabs">
				<ul>
					<li><a href="#north">North</a></li>
					<li><a href="#south">South</a></li>
					<li><a href="#east">East</a></li>
					<li><a href="#west">West</a></li>
				</ul>
				
				'. $this->return_wall($insert_id['wall_id']) .'
			</div>
		</div>
		<div id="floor">
			<form id="floor_form" action="' . base_url('emodel/update_floor/'.$insert_id["model_id"]) .'" method="post">
				<label class="cust-model" for="area">Area:</label>
				<input type="text" name="area" placeholder="sqft">
				<label class="cust-model" for="construction_type">Construction Type:</label>
				<select name="construction_type">'.$this->_get_construction('floor').'</select>
				<div class="cust-update-btn">
					<input id="floor_update" type="submit" class="btn" value="Update Floor Info">
				</div>
			</form>
		</div>
		<div id="roof">
			<p>Note: Area of roof is determined by the floor area</p>
			<form id="roof_form" action="' . base_url('emodel/update_roof/'.$insert_id["model_id"]) .'" method="post">
				<label class="cust-model" for="height">Ceiling Height:</label>
				<input type="text" name="height" placeholder="ft">
				<label class="cust-model" for="construction_type">Insulation:</label>
				<select name="construction_type">'.$this->_get_construction('roof').'</select>
				<div class="cust-update-btn">
					<input id="roof_update" type="submit" class="btn" value="Update Roof">
				</div>
			</form>
		</div>
	</div>';

		echo json_encode($display_emodel);
	}

	function display_window_input()
	{
		
		$orientation = $this->input->post('orientation');
		$number = $this->input->post('number');
		
		$window_form = '<label for="area">' . ucfirst($orientation) . ' Window ' . $number . ':</label>
						<input class="append_form_'.$orientation.'" type="text" name="window[area][]" placeholder="sqft">
						<label for="const">Construction Type</label>
						<select class="append_form_'.$orientation.'" name="window[construction_type][]">'.$this->_get_construction('window').'</select>';
		
		echo json_encode($window_form);
	}

	function display_door_input()
	{
		$orientation = $this->input->post('orientation');
		$number 	 = $this->input->post('number');

		$door_form = '<label for="area">' . ucfirst($orientation) . ' Door ' . $number . ':</label>
					<input class="append_form_'.$orientation.'" type="text" name="door[area][]" placeholder="sqft">
					<label for="const">Construction Type</label>
					<select class="append_form_'.$orientation.'" name="door[construction_type][]">'.$this->_get_construction('door').'</select>';

		echo json_encode($door_form);
	}

	private function get_post_validate($post, $array=false)
	{
		foreach($post as $post_key => $post_value)
		{
			if($post_key == "window" || $post_key == "door")
			{
				foreach($post_value as $key => $value)
				{
					for($i=0;$i<count($value);$i++)
					{
						$this->view_data[$post_key][$key][$i] = $value[$i];
						$pretty_key = str_replace('_',' ',$post_key.' '.($i+1).' '.$key);
						$pretty_key = ucwords($pretty_key);
						$key_field = $post_key.'['.$key.']['.$i.']';
						$this->form_validation->set_rules($key_field, $pretty_key, 'trim|required');
					}
				}
			}
			else if($post_key != 'new_location')
			{
				$this->view_data[$post_key] = $post_value;
				$pretty_key = str_replace('_', ' ',$post_key);
				$pretty_key = ucwords($pretty_key);
				$this->form_validation->set_rules($post_key, $pretty_key, 'trim|required');
			}
		}

		if($this->form_validation->run() == FALSE)
		{
			$error = validation_errors();
		}
		else
		{
			$error = false;	
		}

		return $error;
	}

	function save_model($id)
	{
		$row = $this->Energ_model->save_emodel($id);
		if($row)
		{
			$data['message'] = '<div class="alert alert-success">Your Model has been Saved.</div>';
			$data['model'] = $this->display_emodel($id, $row->model_name, $row->location, $row->building_type);
			echo json_encode($data);
		}
		else
		{
			$data['message'] = '<div class="alert alert-error">Error saving model. Please correct the errors below.</div>';
			echo json_encode($data);
		}
	}

	function display_emodel($model_id, $name, $location, $type)
	{
		$model = '<div id="show-model-'.$model_id.'" class="row cust-row">
			<div class="span9">
				<div class="cust-model-info">
					<form id action="'.base_url('emodel/emod/'.$model_id).'" method="post">
						<input type="hidden" name="emodel_edit" value="1">
					</form>
					<img class="icon edit-model" src="'.asset_url('img/edit_icn.gif').'">
					<p>Edit</p>
				</div>
				<div class="cust-model-info">
					<h3 class="cust-model-name">'.$name.'</h3>
					<p class="cust-location">'.$location.'</p>
					<img src="'.asset_url(($type == 1 ? 'img/residential_icn.gif' : 'img/business_icn.gif')).'">
				</div>
				<div class="cust-model-info">
					<a href="'.base_url('emodel/chart/'.$model_id).'/sunpath"><img class="icon" src="'.asset_url('img/view_icn.gif').'"></a>
					<p class="cust-model-view">View</p>
				</div>
			</div>
		</div><!-- end row -->';

		return $model;
	}
	

	function update_wall($id)
	{
		$error = $this->get_post_validate($_POST);
		if($error)
		{
			echo json_encode($error);
		}
		else
		{
			$this->Energ_model->update_wall_info($id);

			$window = $this->input->post('window');
			$door = $this->input->post('door');
			if(!empty($window['area']))
			{
				$windows = count($window['area']);
				for($i = 0; $i < $windows; $i++)
				{	$number_windows = $i + 1;
					if(!$this->Energ_model->check_fenestration($id, $number_windows, 'windows'))
					{
						$this->Energ_model->new_fenestration($id, $window['construction_type'][$i], $window['area'][$i], $number_windows, 'windows');
					} 
					else
					{
						$this->Energ_model->update_fenestration($id, $window['construction_type'][$i], $window['area'][$i], $number_windows, 'windows');	
					}
					
				}
			}
			if(!empty($door['area']))
			{
				$doors = count($door['area']);
				for($i = 0; $i < $doors; $i++)
				{	$number_doors = $i + 1;
					if(!$this->Energ_model->check_fenestration($id, $number_doors, 'doors'))
					{
						$this->Energ_model->new_fenestration($id, $door['construction_type'][$i], $door['area'][$i], $number_doors, 'doors');
					} 
					else
					{
						$this->Energ_model->update_fenestration($id, $door['construction_type'][$i], $door['area'][$i], $number_doors, 'doors');	
					}
				}
			}
			echo json_encode('<div class="alert alert-success">Updated!</div>');
		}
	}

	function update_model_info($id)
	{
		// this function will update the
		// database with the modified model information
		// and return errors if any exist.
		$error = $this->get_post_validate($_POST);
		if($error)
		{
			$message = $error;
		}
		else
		{
			$this->Energ_model->update_model_info($id);

			$message = '<div class="alert alert-success">Updated!</div>';
		}
		echo json_encode($message);
	}

	function update_roof($id='')
	{
		$error = $this->get_post_validate($_POST);
		if($error)
		{
			echo json_encode($error);
		}
		else
		{
			$update = $this->Energ_model->update_roof($id);
			$message = '<div class="alert alert-success">Updated!</div>';
			if(!$update)
			{
				$message = '<div class="alert alert-error">You must input floor area before updating roof.</div>';
			}

			echo json_encode($message);
		}
	}

	function update_floor($id='')
	{
		$error = $this->get_post_validate($_POST);
		if($error)
		{
			echo json_encode($error);
		}
		else
		{
			if(!$this->Energ_model->check_model($id, 'floors'))
			{
				$this->Energ_model->new_floor_roof($id);
			}
			else
			{
				$this->Energ_model->update_floor($id);
			}

			echo json_encode('<div class="alert alert-success">Updated!</div>');
		}
	}

	function _sun_path($longitude, $latitude)
	{
	    $months = array('dec' => 355, 'jan' => 21, 'feb' => 51, 'mar' => 79, 'apr' => 110, 'may' => 141, 'jun' => 172);

	    foreach($months as $month => $day_of_year)
	    {
	         $current_hour = 0;
	         $data_plot[$month] = '';
	         $first_item = true;
	         for($i=0;$i<24;$i++)
	         {

	            $d = deg2rad(round(360*($day_of_year -81)/365, 1));
	            $equation_of_time = 9.87*sin(2*$d) - 7.53*cos($d)-1.5*sin($d);
	            $local_longitude = 15*round($longitude/15);
	            $apparent_solar_time = round($current_hour*60 + (4)*($local_longitude - $longitude) + $equation_of_time);
	            $hour_angle = ($apparent_solar_time -720)/4;
	            $declination = round(23.45*sin(deg2rad((($day_of_year+284)/365)*360)), 2);
	            
	            $cos_dec = cos(deg2rad($declination));
	            $sin_dec = sin(deg2rad($declination));
	            $cos_latitude = cos(deg2rad($latitude));
	            $sin_latitude = sin(deg2rad($latitude));
	            $cos_hour_angle = cos(deg2rad($hour_angle));
	            $sa1 = $cos_latitude*$cos_dec*$cos_hour_angle;
	            $sa2 = $sin_latitude*$sin_dec;
	            $sa_ex = round($sa1+$sa2, 2);
	            
	            $solar_altitude = rad2deg(asin($sa_ex));

	            $sin_sa = sin(deg2rad($solar_altitude));
	            $saz1 = ($sin_sa*$sin_latitude)-$sin_dec;
	            $cos_sa = cos(deg2rad($solar_altitude));
	            $saz2 = $cos_sa*$cos_latitude;
	            $sin_hour_angle = sin(deg2rad($hour_angle));
	            $saz3 = $saz1/$saz2;
	            
	            if($apparent_solar_time > 12*60)
	            {
	                $solar_azimuth = 180 + rad2deg(acos($saz3));
	            }
	            else
	            {
	                $solar_azimuth = 180 - rad2deg(acos($saz3));    
	            }

	            if($solar_altitude >= 0 && !is_nan($solar_azimuth))
	            {
	                
	                if($first_item)
	                {
	                    $data_plot[$month] .= '['.$solar_azimuth.','.$solar_altitude.']';
	                    $first_item = false;
	                }
	                else
	                {
	                    // echo $solar_azimuth.'<br>';
	                    $data_plot[$month] .= ',['.$solar_azimuth.','.$solar_altitude.']';
	                }
	            }
	            $current_hour++;
	        }
	    }

	    $series = "{
                name: 'Dec 21',
                data: [".$data_plot['dec']."]
            },
            		{
                name: 'Jan 21',
                data: [".$data_plot['jan']."]
            },
            		{
                name: 'Feb 20',
                data: [".$data_plot['feb']."]
            },
            		{
                name: 'Mar 20',
                data: [".$data_plot['mar']."]
            },
            		{
                name: 'Apr 20',
                data: [".$data_plot['apr']."]
            },
            		{
                name: 'May 21',
                data: [".$data_plot['may']."]
            },
                    {
                name: 'Jun 21',
                data: [".$data_plot['jun']."]
            }";

	    return $series;
	}

	function _degree_days($id)
	{
		$rows = $this->Energ_model->get_degree_days($id);
		$HDD = "{ name: 'Heating DD', data: [";
		$CDD = "{ name: 'Cooling DD', data: ["; 
		$count = 0;
		foreach($rows as $row)
		{
			if($count == 0)
			{
				$location = $row->location;
				$CDD .= $row->cdd;
				$HDD .= $row->hdd;
			}
			else
			{
				$CDD .= ', '.$row->cdd;
				$HDD .= ', '.$row->hdd;
			}
			$count++;	
		}
		$CDD .= "], color: 'blue' },";
		$HDD .= "], color: 'red' }";
		$series = $CDD.$HDD;

		$data['series'] = $series;
		$data['location'] = $location;

		return $data;
	}

	function _temperature($id)
	{
		$rows = $this->Energ_model->get_temperatures($id);
		$high = "[";
		$average = "[";
		$low = "["; 
		
		$count = 0;
		foreach($rows as $row)
		{
			if($count == 0)
			{
				$location = $row->location;
				$high .= '['.$row->average.', '.$row->high.']';
				$low .= '['.$row->low.', '.$row->average.']';
				$average .= '['.$row->average.']';
			}
			else
			{
				$high .= ', ['.$row->average.', '.$row->high.']';
				$low .= ', ['.$row->low.', '.$row->average.']';
				$average .= ', ['.$row->average.']';
			}
			$count++;	
		}
		$high .= ']';
		$low .= ']';
		$average .= ']';
		$series = "{
			    	name: 'Average',
			    	data: ".$average.",
			    	zIndex: 1,
	                color: 'green',
			    	marker: {
			    		fillColor: 'white',
			    		lineWidth: 2,
			    		lineColor: 'green'
			    	}
			    }, {
			        name: 'High Range',
			        data: ".$high.",
			        type: 'arearange',
			        lineWidth: 0,
			    	linkedTo: ':previous',
			    	color: Highcharts.getOptions().colors[3],
			    	fillOpacity: 0.3,
			    	zIndex: 0
			    }, {
			        name: 'Low Range',
			        data: ".$low.",
			        type: 'arearange',
			        lineWidth: 0,
			    	linkedTo: ':previous',
			    	color: Highcharts.getOptions().colors[0],
			    	fillOpacity: 0.5,
			    	zIndex: 0
			    }";

		$data['series'] = $series;
		$data['location'] = $location;

		return $data;
	}

	function _kwh($id)
	{
		$rows = $this->Energ_model->get_kwh_consumption($id);
		$kwh60 = "{ name: 'Base Temp 60', data: [";
		$kwh65 = "{ name: 'Base Temp 65', data: ["; 
		$kwh70 = "{ name: 'Base Temp 70', data: [";

		$count = 0;
		foreach($rows as $row)
		{
			if($count == 0)
			{
				$location = $row->location;
				$kwh60 .= $row->kwh60;
				$kwh65 .= $row->kwh65;
				$kwh70 .= $row->kwh70;
			}
			else
			{
				$kwh60 .= ', '.$row->kwh60;
				$kwh65 .= ', '.$row->kwh65;
				$kwh70 .= ', '.$row->kwh70;
			}
			$count++;	
		}
		$kwh60 .= "], color: 'green' },";
		$kwh65 .= "], color: 'blue' },";
		$kwh70 .= "], color: 'red' }";
		$series = $kwh60.$kwh65.$kwh70;

		$data['series'] = $series;
		$data['location'] = $location;

		return $data;
	}

	function chart($id, $chart)
	{
		switch ($chart) {
			case 'sunpath':
				$row = $this->Energ_model->get_location($id);
				$xAxis = "West -- Solar Azimuth -- East";
				$yAxis = "Solar Elevation";
				$y_units = "°";
				$x_units = "°";            
				$this->view_data['chart_info'] = "xAxis: {
				                reversed: false,
				                title: {
				                    enabled: true,
				                    text: '".$xAxis."'
				                },
				                labels: {
				                    formatter: function() {
				                        return this.value +'".$x_units."';
				                    }
				                },
				                maxPadding: 0.05,
				                showLastLabel: true
				            },
				            yAxis: {
				                title: {
				                    text: '".$yAxis."'
				                },
				                labels: {
				                    formatter: function() {
				                        return this.value +'".$y_units."';
				                    }
				                },
				                plotLines: [{
				                    value: 0,
				                    width: 1,
				                    color: '#808080'
				                }]
				            },
				            tooltip: {
				                headerFormat: '<b>{series.name}</b><br/>',
				                pointFormat: '{point.x}".$x_units.": {point.y}".$y_units."'
				            },";
				$this->view_data['graph_title'] = "Sun Path Diagram";
				$this->view_data['subtitle'] = $row->location;
				$this->view_data['series'] = $this->_sun_path($row->longitude, $row->latitude);
			break;
			case 'degree_days':
				$this->view_data['chart_info'] = "xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
	            },
	            yAxis: {
	                title: {
	                    text: 'Degree Days'
	                },
	                plotLines: [{
	                    value: 0,
	                    width: 1,
	                    color: '#808080'
	                }]
	            },
	            legend: {
	                layout: 'vertical',
	                align: 'right',
	                verticalAlign: 'middle',
	                borderWidth: 0
	            },";
	            $data = $this->_degree_days($id);
	            $this->view_data['graph_title'] = "Degree Days";
				$this->view_data['subtitle'] = $data['location'];
				$this->view_data['series'] = $data['series'];
			break;
			case 'temperature':
			    $this->view_data['chart_info'] = "xAxis: {
			        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
	                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
			    },
			    yAxis: {
			        title: {
			            text: null
			        }
			    },
			    tooltip: {
			        crosshairs: true,
			        shared: true,
			        valueSuffix: '°F'
			    },";
				 
	            $data = $this->_temperature($id);
	            $this->view_data['graph_title'] = "Temperatures";
				$this->view_data['series'] = $data['series'];
				$this->view_data['subtitle'] = $data['location'];
			break;
			case 'kwh':
				$this->view_data['chart_info'] = "xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
	            },
	            yAxis: {
	                title: {
	                    text: 'kWh'
	                },
	                plotLines: [{
	                    value: 0,
	                    width: 1,
	                    color: '#808080'
	                }]
	            },
	            legend: {
	                layout: 'vertical',
	                align: 'right',
	                verticalAlign: 'middle',
	                borderWidth: 0
	            },";
	            $data = $this->_kwh($id);
	            $this->view_data['graph_title'] = "kWh Consumption";
				$this->view_data['subtitle'] = $data['location'];
				$this->view_data['series'] = $data['series'];
			break;
		}

		$this->view_data['model_id'] = $id;
		$this->load->view('chart', $this->view_data);
	}
}