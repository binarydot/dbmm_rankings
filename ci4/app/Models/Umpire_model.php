<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;
class Umpire_model extends CI_Model {

	var $id = '';
	var $rarId = '';

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	
	function insertUmpire($competitionId, $competitorId) {
		$data = array(
				'competition_id' => $competitionId
				,'competitor_id' => $competitorId
		);
		$this->db->insert('umpire', $data);
		if ($this->db->affected_rows() == 1) {
			return array('status' => "OK", 'ID' => $this->db->insert_id());
		} else {
			return array('status' => "Error", 'errorMessage' => "Insert failed");
		}
	
	}

}