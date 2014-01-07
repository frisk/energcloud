<?php

class Energ_model extends CI_Model{
	
	var $orientation = array('north' => 1, 'south' => 2, 'east' => 3, 'west' => 4);
	

	function get_locations($id='')
	{
		$sql = "SELECT locations.id, locations.city, locations.state 
		 		FROM locations
		 		JOIN temperatures on locations.id = temperatures.location_id
		 		AND locations.city != ''
		 		GROUP BY locations.city";
		
		return $this->db->query($sql);
	}

	function get_location($id)
	{
		$sql = "SELECT CONCAT(locations.city, ', ', locations.full_state) location, locations.latitude, locations.longitude
				FROM emodels
				JOIN locations on locations.id = emodels.location_id
				WHERE emodels.id = $id";

		$query = $this->db->query($sql);

		return $query->row();
	}

	function get_degree_days($id)
	{
		$sql = "SELECT temperatures.hdd, temperatures.cdd, CONCAT(locations.city, ', ', locations.full_state) location 
				FROM temperatures
				JOIN locations ON locations.id = temperatures.location_id
				JOIN emodels ON emodels.location_id = locations.id
				WHERE emodels.id = $id";
		$query = $this->db->query($sql);

		return $query->result();
	}

	function get_kwh_consumption($id)
	{
		$sql = "SELECT CONCAT(locations.city, ', ', locations.full_state) location, ((emodels.ua_metric*ABS(18-(temperatures.average-32)*(5/9)))*24*DAY(LAST_DAY(temperatures.time))/1000) kwh65, ((emodels.ua_metric*ABS(21-(temperatures.average-32)*(5/9)))*24*DAY(LAST_DAY(temperatures.time))/1000) kwh70, ((emodels.ua_metric*ABS(16-(temperatures.average-32)*(5/9)))*24*DAY(LAST_DAY(temperatures.time))/1000) kwh60
				FROM emodels
				LEFT JOIN locations ON emodels.location_id = locations.id
				LEFT JOIN temperatures ON locations.id = temperatures.location_id
				WHERE emodels.id = $id";

		$query = $this->db->query($sql);

		return $query->result();
	}

	function get_temperatures($id)
	{
		$sql = "SELECT temperatures.high, temperatures.low, temperatures.average, CONCAT(locations.city, ', ', locations.full_state) location 
				FROM temperatures
				JOIN locations ON locations.id = temperatures.location_id
				JOIN emodels ON emodels.location_id = locations.id
				WHERE emodels.id = $id";
		$query = $this->db->query($sql);

		return $query->result();
	}

	function _get_curl_data($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
			
		return $data;
	}

	function get_zip()
	{
		$sql = "SELECT locations.id, locations.city, locations.full_state FROM temperatures 
				JOIN locations ON temperatures.location_id = locations.id
				WHERE locations.zip = ? LIMIT 1";
		$query = $this->db->query($sql, $this->input->post('new_location'));
		
		if($query->num_rows() > 0)
		{
			return array('message' => '<div class="alert alert-success">Location Found</div>','row' => $query->row());		
		}
		else
		{
			$sql = "SELECT id, zip FROM locations WHERE zip = ?";
			$query = $this->db->query($sql, $this->input->post('new_location'));

			if($query->num_rows() != 1)
			{
				return $this->input->post('new_location').' is Invalid';
			}
			else
			{
				$row = $query->row();
				$id = $row->id;
				$zip = $row->zip;
				$returned_content = $this->_get_curl_data('http://www.melissadata.com/lookups/ZipWeather.asp?ZipCode='.$zip.'&submit1=Submit');

				libxml_use_internal_errors(true);

				$dom = new DOMDocument;
				$dom->validateOnParse = true;
				$dom->loadHTML($returned_content);

				$dom_xpath = new DOMXpath($dom);
				$table = $dom_xpath->query("//*[@class='Tableresultborder']")->item(0);

				$months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
				$data = array();
				$first = false;
				$count = 0;
				if (!is_null($table)) {

					$rows = $table->getElementsByTagName("tr");

				  foreach ($rows as $row) {
				    $cells = $row->getElementsByTagName('td');
				    foreach ($cells as $cell) {
				    	if(in_array($cell->nodeValue, $months))
				      	{
				      		$sql = "INSERT INTO temperatures (location_id, time, high, average, low, cdd, hdd, created, modified) VALUES(".$id.", '".date('Y-m-d', strtotime($cell->nodeValue.' 1 2012'))."'";
				      		$first = true;
				      		$current_month = $cell->nodeValue;
				      		$count = 0;
				      	}
				      	else
				      	{
				      		if($first && $count < 5)
				      		{
				      			$value = str_replace(',', '', $cell->nodeValue);
				      			$sql .= ", ".$value;
				      			$count++;
				      		}
				      		else if($count == 5)
				      		{
				      			$sql .= ", NOW(), NOW())";
								$this->db->query($sql);
								if($this->db->affected_rows())
								{
									$sql = '';
								}
								else
								{
									return $zip.' '.$current_month.' Failed';
									exit;
								}
								$count++;
				      		}
				      	}
				    }
				  }
				}
				else
				{
					return $zip.' Not Found';
				}
			}
		}

		$sql = "SELECT locations.id, locations.city, locations.full_state 
				FROM locations 
				WHERE locations.zip = ?";
		$query = $this->db->query($sql, $this->input->post('new_location'));

		return array('message' => '<div class="alert alert-success">Location Found</div>','row' => $query->row());
	}

	function get_emodels($user_id)
	{
		$sql = "SELECT emodels.id model_id, emodels.name, CONCAT(locations.city, ', ', locations.full_state) location, emodels.type 
		FROM emodels
		LEFT JOIN locations ON locations.id = emodels.location_id 
		WHERE emodels.user_id = $user_id
		AND emodels.saved = 1
		ORDER BY emodels.created DESC";

		$query = $this->db->query($sql);

		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		else
		{
			return false;
		}

	}

	function new_emodel($id)
	{
		$ids = array();

		$sql_model = "INSERT INTO emodels (user_id, location_id, created, modified) 
				VALUES ($id, 1, NOW(),NOW())";
		$this->db->query($sql_model);
		
		$ids['model_id'] = $this->db->insert_id();
		$direction = array('north', 'south', 'east', 'west');
		foreach($direction as $orientation)
		{
			$ids['wall_id'][$orientation] = $this->_new_wall($ids['model_id'], 1, $orientation);
		}

		return $ids;
	}

	function save_emodel($id)
	{
		$sql = "SELECT emodels.name model_name, emodels.type building_type, floors.area floor_area, roof_construction.r_value roof_construction, walls.area wall_area, walls.orientation wall_orientation, CONCAT(locations.city, ', ', locations.full_state) location
				FROM emodels
				LEFT JOIN users ON users.id = emodels.user_id
				LEFT JOIN locations ON locations.id = emodels.location_id
				LEFT JOIN floors ON floors.emodel_id = emodels.id
				LEFT JOIN constructions floor_construction ON floors.construction_id = floor_construction.id
				LEFT JOIN roofs ON roofs.emodel_id = emodels.id
				LEFT JOIN constructions roof_construction ON roofs.construction_id = roof_construction.id
				LEFT JOIN walls ON walls.emodel_id = emodels.id
				LEFT JOIN constructions wall_construction ON walls.construction_id = wall_construction.id
				WHERE emodels.id = $id
				GROUP BY walls.orientation
				ORDER BY emodels.id DESC";

		$query = $this->db->query($sql);
		$result = $query->row();
		if($query->num_rows() > 0)
		{
			$row_count = 0;
			$orientation = 0;
			foreach($query->result_array() as $row)
			{
				foreach($row as $field => $value)
				{
					if($row_count == 0)
					{
						if($value)
						{
							if($field == 'wall_orientation')
								$orientation += $value;
							continue;
						}
						else
						{
							return false;
						}
					}
				}
			}

			if($orientation == 10)
			{
				$sql = "SELECT emodels.name model_name, emodels.type building_type, 
						SUM(floors.ua_metric+roofs.ua_metric+infiltration.ua_metric+IFNULL(walls.ua_metric,0)+IFNULL(windows.ua_metric,0)+IFNULL(doors.ua_metric,0)) UA_metric,
						SUM(floors.ua_english+roofs.ua_english+infiltration.ua_english+IFNULL(walls.ua_english,0)+IFNULL(windows.ua_english,0)+IFNULL(doors.ua_english,0)) UA_english
						FROM emodels
						LEFT JOIN floors ON floors.emodel_id = emodels.id
						LEFT JOIN roofs ON roofs.emodel_id = emodels.id
						LEFT JOIN infiltration ON infiltration.emodel_id = emodels.id
						LEFT JOIN walls ON walls.emodel_id = emodels.id
						LEFT JOIN windows ON walls.id = windows.wall_id
						LEFT JOIN doors ON walls.id = doors.wall_id
						WHERE emodels.id = $id";

				$query = $this->db->query($sql);
				$row = $query->row();

				$sql = "UPDATE emodels SET saved = 1, ua_english = ".$row->UA_english.", ua_metric = ".$row->UA_metric." WHERE id = ".$id;
				$this->db->query($sql);
				if($this->db->affected_rows())
				{
					return $result;
				}
			}
		} 

		return false;
	}

	function _new_wall($model_id, $construction_id, $orientation)
	{
		$sql_walls ="INSERT INTO walls (emodel_id, construction_id, orientation, created, modified)
					 VALUES(".$model_id.", ".$construction_id.", '".$orientation."', NOW(), NOW())";
		$this->db->query($sql_walls);
		return $this->db->insert_id();
	}

	function _get_construction($id)
	{
		$sql = "SELECT r_value from constructions WHERE id = $id";
		$query = $this->db->query($sql);

		return $query->row();
	}

	function _english_to_metric($value, $metric)
	{
		switch ($metric) {
			case 'r_value':
				$converted = (($value)*3.41*0.09)/1.8;
			break;
			case 'area':
				$converted = $value*0.09;
			break;
			case 'temperature':
				$converted = ($value-32)*(5/9);
			break;
		}

		return $converted;
	}

	function _calculate_ua($r_value, $area)
	{
		$english_ua = $area*(1/$r_value);

		$sqmeters = $this->_english_to_metric($area, 'area');
		$watts_celsius = $this->_english_to_metric($r_value, 'r_value');

		$metric_ua = $sqmeters*(1/$watts_celsius);

		return array('english' => $english_ua, 'metric' => $metric_ua);
	}

	function new_construction($id)
	{
		$sql = "INSERT INTO constructions (component_id, name, r_value, created, modified) VALUES($id, ?, ?, NOW(), NOW())";
		$this->db->query($sql, array($this->input->post('name'),$this->input->post('r_value')));
	}

	function all_components()
	{
		$sql = "SELECT * FROM components";
		return $this->db->query($sql);
	}

	function new_fenestration($wall_id, $construction_id, $area, $number, $type)
	{
		$construction = $this->_get_construction($construction_id);
		$ua = $this->_calculate_ua($construction->r_value, $area);

		$sql = "INSERT INTO $type (wall_id, construction_id, area, ua_english, ua_metric, number, created, modified) VALUES($wall_id, $construction_id, $area, ".$ua['english'].", ".$ua['metric'].", $number, NOW(), NOW())";
		$this->db->query($sql);
	}

	function new_floor_roof($id)
	{
		$floor_construction = $this->_get_construction($this->input->post('construction_type'));
		$ua = $this->_calculate_ua($floor_construction->r_value, $this->input->post('area'));

		$sql = "INSERT INTO floors (emodel_id, construction_id, area, ua_english, ua_metric, created, modified) VALUES(".$id.", ".$this->input->post('construction_type').", ".$this->input->post('area').", ".$ua['english'].", ".$ua['metric'].", NOW(), NOW())";
		$this->db->query($sql);
		$floor_id = $this->db->insert_id();

		$sql = "SELECT constructions.r_value FROM constructions 
				JOIN components ON constructions.component_id = components.id
				WHERE components.name = 'roof' LIMIT 1";

		$query = $this->db->query($sql);
		$roof_construction = $query->row();
		$ua = $this->_calculate_ua($roof_construction->r_value, $this->input->post('area'));

		$sql = "INSERT INTO roofs (emodel_id, construction_id, floor_id, ua_english, ua_metric, created, modified) VALUES(".$id.", 1, ".$floor_id.", ".$ua['english'].", ".$ua['metric'].", NOW(), NOW())";
		$this->db->query($sql);
	}

	function update_floor($id)
	{
		$construction = $this->_get_construction($this->input->post('construction_type'));
		$ua = $this->_calculate_ua($construction->r_value, $this->input->post('area'));

		$sql = "UPDATE floors SET area = ".$this->input->post('area').", construction_id = ".$this->input->post('construction_type').", ua_english = ".$ua['english'].", ua_metric = ".$ua['metric'].", modified = NOW() WHERE emodel_id = ".$id;
		$this->db->query($sql);

		$sql = "SELECT constructions.r_value, roofs.height
				FROM roofs
				LEFT JOIN constructions ON roofs.construction_id = constructions.id
				WHERE roofs.emodel_id = $id";
		
		$query = $this->db->query($sql);
		$row = $query->row();
		$ua = $this->_calculate_ua($row->r_value, $this->input->post('area'));
	
		$sql = "UPDATE roofs SET ua_english = ".$ua['english'].", ua_metric = ".$ua['metric'].", modified = NOW() WHERE emodel_id = ".$id;
		$this->db->query($sql);

		$this->_update_infiltration($id, $this->input->post('area'), $row->height);
	}

	function _update_infiltration($id, $area, $height)
	{
		$volume_f3 = $area*$height;
		
		$area_meters = $this->_english_to_metric($area, 'area');
		$height_meters = $this->_english_to_metric($height, 'area');
		$volume_m3 = $area_meters*$height_meters;

		$ach = .5;
		
		$ua_english = $volume_f3*$ach*.018;
		$ua_metric = $volume_m3*$ach*.018;

		if(!$this->check_model($id, 'infiltration'))
		{
			$sql = "INSERT INTO infiltration (emodel_id, volume, ach, ua_english, ua_metric, created, modified) VALUES($id, $volume_f3, $ach, $ua_english, $ua_metric, NOW(), NOW())";
			$this->db->query($sql);
		}
		else
		{
			$sql = "UPDATE infiltration SET volume = $volume_f3, ua_english = $ua_english, ua_metric = $ua_metric, modified = NOW()";
			$this->db->query($sql);
		}
	}

	function update_roof($id)
	{
		$construction = $this->_get_construction($this->input->post('construction_type'));
		$sql = "SELECT area FROM floors WHERE emodel_id = $id";
		$query = $this->db->query($sql);
		$row = $query->row();

		$ua = $this->_calculate_ua($construction->r_value, $row->area);

		$sql = "UPDATE roofs SET height = ".$this->input->post('height').", construction_id = ".$this->input->post('construction_type').", ua_english = ".$ua['english'].", ua_metric = ".$ua['metric'].", modified = NOW() WHERE emodel_id = ".$id;
		$this->db->query($sql);

		$this->_update_infiltration($id, $row->area, $this->input->post('height'));

		return $this->db->affected_rows();
	}

	function check_fenestration($wall_id, $number, $type)
	{
		$sql = "SELECT * FROM $type WHERE wall_id = ".$wall_id." AND number = ".$number;
		$query = $this->db->query($sql);

		return $query->num_rows();
	}

	function check_model($id, $type)
	{
		$sql = "SELECT * FROM $type WHERE emodel_id = ".$id;
		$query = $this->db->query($sql);

		return $query->num_rows();	
	}

	function update_model_info($model_id)
	{
		$sql = "UPDATE emodels SET location_id = ".$this->input->post('location').", name = ?, type = ".$this->input->post('type').", modified = NOW() WHERE id = ".$model_id;
		$this->db->query($sql,array($this->input->post('model_name')));
	}

	function update_wall_info($wall_id)
	{
		$construction = $this->_get_construction($this->input->post('construction_type'));
		
		$sql = "SELECT SUM(windows.area) window_area, SUM(doors.area) door_area
				FROM walls 
				LEFT JOIN windows ON windows.wall_id = walls.id
				LEFT JOIN doors ON doors.wall_id = walls.id 
				WHERE walls.id = $wall_id";

		$query = $this->db->query($sql);
		$row = $query->row();
		$window_area = ($row->window_area ? $row->window_area : 0);
		$door_area = ($row->door_area ? $row->door_area : 0);

		$wall_area = $this->input->post('area') - ($window_area + $door_area);

		$ua = $this->_calculate_ua($construction->r_value, $wall_area);

		$sql = "UPDATE walls SET construction_id = ".$this->input->post('construction_type').", orientation = ?, area = ?, ua_english = ".$ua['english'].", ua_metric = ".$ua['metric'].", modified = NOW() WHERE id = ".$wall_id;
		$this->db->query($sql, array($this->orientation[$this->input->post('orientation')], $this->input->post('area')));
	}

	function update_fenestration($wall_id, $construction_id, $area, $number, $type)
	{
		$construction = $this->_get_construction($construction_id);
		$ua = $this->_calculate_ua($construction->r_value, $area);

		$sql = "UPDATE $type SET construction_id = ".$construction_id.", area = ".$area.", ua_english = ".$ua['english'].", ua_metric = ".$ua['metric'].", modified = NOW() WHERE wall_id = ".$wall_id." AND number = ".$number;
		$this->db->query($sql);
	}

}
?>