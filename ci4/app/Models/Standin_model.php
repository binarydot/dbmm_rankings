<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;
class Standin_model extends CI_Model {

	var $id = '';
	var $rarId = '';

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	// insert a stand in for a player at a competition
	function insertStandIn($competitionId, $standInID, $standInForID) {
		$data = array(
				'competition_id' => $competitionId
				,'competitor_id' => $standInID
				,'stood_in_for' => $standInForID
		);
		$this->db->insert('stand_in', $data);
		if ($this->db->affected_rows() == 1) {
			return array('status' => "OK", 'ID' => $this->db->insert_id());
		} else {
			return array('status' => "Error", 'errorMessage' => "Insert failed");
		}
	
	}
	
	
}