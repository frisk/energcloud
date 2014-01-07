<?php

class Construction_model extends CI_Model{

	public function constructions()
	{
		$user_link_start = '<a href="' . base_url() . 'constructions/show/';
		$user_link_mid = '">';

		$sql = "SELECT constructions.name Construction, constructions.r_value R_Value, components.name Component, DATE_FORMAT(constructions.created, '%b %D %Y') Created
				FROM constructions
				JOIN components ON constructions.component_id = components.id
				ORDER BY components.id";

		return $this->db->query($sql);
	}

	public function construction_list($component)
	{
		$sql = "SELECT constructions.id, constructions.name construction, constructions.r_value
				FROM constructions
				JOIN components ON constructions.component_id = components.id
				WHERE components.name = '$component'";

		return $this->db->query($sql);
	}

	public function components()
	{
		$sql = "SELECT id, name FROM components";

		return $this->db->query($sql);
	}

	public function new_construction()
	{
		$r_value = $this->input->post('r_value');

		if($this->input->post('units'))
		{
			// convert metric r-value to english
			$r_value = (($r_value/3.41)/0.09)*1.8;
		}	
		
		$sql = "INSERT INTO constructions (component_id, name, r_value, created, modified) VALUES(".$this->input->post('component_id').", ?, ?, NOW(), NOW())";
		$this->db->query($sql, array($this->input->post('name'), $r_value));
	}
	
	// r-value conversion from (((((K30*1055)/60)/60)/0.09)*1.8)
	// r-value conversion from m^2 - C / W to ft^2-h-F / Btu (((m^2 - C / W)/3.41)/0.09)*1.8,0)
	// r-value conversion from ft^2-h-F / Btu to m^2 - C / W = ((ft^2-h-F)*3.41*0.09)/1.8,0)
	// infiltration = building volume * air changes per hour * 0.018
}

?>