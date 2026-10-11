<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;

class Rar_model extends CI_Model {
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function getRars() {
        $sql = "
        	SELECT rar_id
			      ,rar_name
			      ,rar_tag
			FROM rar r 
			ORDER BY rar_name
		";
		$query = $this->db->query($sql, array());
        return $query->result();
    	
    }

    function insert($rarName, $rarTag) {
		$data = array(
			'rar_name' => $rarName,
			'rar_tag' => $rarTag
		);
		$this->db->insert_string('rar', $data);
    }

    function update($rarID, $rarName, $rarTag) {
		$data = array(
			'rar_name' => $rarName,
			'rar_tag' => $rarTag
		);
		$this->db->where('rar_id', $rarID);
		$this->db->update_string('rar', $data);
    }
    
	function getRarsAndGroups() {
	        $sql = "
	        	SELECT rar_id   as rar_id
			      ,rar_name as rar_name
			      ,rar_tag  as rar_tag
			FROM rar r 
			UNION
			SELECT rar_group_id   as rar_id
			      ,rar_group_name as rar_name
			      ,'XX'           as rar_tag
			FROM rar_group
			ORDER BY 2
		";
		$query = $this->db->query($sql, array());
	        return $query->result();
    }
    
	function getRarsForGroup($groupId) {
	        $sql = "
	        	SELECT r.rar_id
			      ,r.rar_name
			      ,r.rar_tag
			FROM rar r, rar_group_link rgl
			WHERE r.rar_id = rgl.rar_id
			AND   rgl.rar_group_id = ? 
			ORDER BY rar_name
		";
		$query = $this->db->query($sql, array($groupId));
	        return $query->result();
	}    
}
?>
