<?php
class Competition_model extends CI_Model {

	var $id = '';
	var $rarId = '';

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	//*********************************************************************
	//*********************************************************************
	//
	// insertCompetitionSeries - create a new series if you have permission
	//
	//*********************************************************************
	//*********************************************************************

	function insertCompetitionSeries($name, $rar) {
		if (isset($this->session->userdata['permissions']['Add Competition']) &&
		  $this->session->userdata['permissions']['Add Competition'] == 1) {
			$data = array(
				'competition_series_name' => $name
			);
			$this->db->insert('competition_series', $data);
			if ($this->db->affected_rows() > 0) {
				//now we need to grant this user permission to create comps in this comp series
				//$query = $this->db->query("SELECT max(competition_series_id) as last_id FROM competition_series");
				//if ($query->num_rows() > 0) {
				//	$row = $query->row();
				//	$competitionSeriesId = $row->last_id;
				$competitionSeriesId = $this->db->insert_id();
					//$adminUser = $_SESSION['user_id'];
				$adminUser = $this->session->userdata('userID');
				$data = array(
						'user_id' => $adminUser,
						'competition_series_id' => $competitionSeriesId,
						'value' => 1
				);
				$this->db->insert('user_series', $data);
				// and update the user permissions so any subsequent competition insert will work
				// who knows what this is doing ? - it at least now matches login.php
				$seriesPermissions = $this->session->userdata('seriesPermissions');
				$seriesPermissions[$competitionSeriesId] = 1;
				$seriesPermData = array('seriesPermissions' => $seriesPermissions);
				$this->session->set_userdata($seriesPermData);	
				//}
				return array('status' => 'OK', 'ID' => $competitionSeriesId, 'name' => $name);
			} else {
				//$errorMessage = 'Insert failed. ';
				$errorMessage = $this->db->last_query();
				return array('status' => 'Error', 'errorMessage' => $errorMessage);
			}
		} else {
			return array('status' => 'Error', 'errorMessage' => 'You do not have permission to create competition series');
		}
	}
	
	//*********************************************************************
	//*********************************************************************
	//
	// getAllCompetitionSeries - get a list of all competition series,
	//                           optionally filtered on rar 
	//
	//*********************************************************************
	//*********************************************************************
	function getAllCompetitionSeries($rar) {
		if ($rar > 0) {
			$rarsqlwhere = "";
		} else {
			$rarsqlwhere = "";
		}
		$sql = "
				SELECT c.competition_series_id as id
				,c.competition_series_name as name
				FROM competition_series c
				$rarsqlwhere
				ORDER BY c.competition_series_name
				";
		$query = $this->db->query($sql, array());
		return $query->result();
	}
	
	
	//*********************************************************************
	//*********************************************************************
	//
	// getCompetitionList - List all the competitions, filterable by:
	//                      - year
	//                      - rar
	//                      - series
	//
	//*********************************************************************
	//*********************************************************************
	function getCompetitionList($rar, $series, $year) {
		if ($rar > 0) {
			$rarsqlwhere = "AND c.rar_id = ".$rar;
		} else {
			$rarsqlwhere = "";
		}
		if ($series > 0) {
			$seriessqlwhere = "AND c.competition_series_id = ".$series;
		} else {
			$seriessqlwhere = "";
		}
		if ($year > 0) {
			$yearsqlwhere = "AND YEAR(c.competition_date) = ".$year;
		} else if ($year == -1) {
			$yearsqlwhere = "AND YEAR(c.competition_date) >= ".date("Y");
		} else {
			$yearsqlwhere = "";
		}
		$sql = "
			SELECT c.competition_id
			     , c.competition_name
			     , c.competition_date
			     , c.type
			     , c.weighting
			     , c.protected
			     , glicko_eligible
			     , cs.competition_series_name 
			     , cs.competition_series_id
			     , r.rar_name
			     , max(p.position) as max_pos
			     , count(p.position) as tot_players
			FROM competition c
			LEFT JOIN position p ON c.competition_id = p.competition_id
			   , competition_series cs 
			   , rar r
			WHERE c.competition_series_id = cs.competition_series_id 
			AND   c.rar_id = r.rar_id
			$rarsqlwhere
			$seriessqlwhere
			$yearsqlwhere
			GROUP BY 1,2,3,4,5,6,7,8,9,10
			ORDER BY c.competition_date DESC
		";
		//echo $sql."\n";
		$query = $this->db->query($sql, array());
		$data = $query->result();
		$returnData = array();
		foreach( $data AS $row ){
			$compRow = array();
			$compRow['competition_name'] = $row->competition_name;
			$date = new DateTime($row->competition_date);
			$compRow['competition_date'] = $date->format('jS F Y');
			$compRow['competition_id'] = $row->competition_id;
			$compRow['competition_series_name'] = $row->competition_series_name;
			$compRow['series_id'] = $row->competition_series_id;
			$compRow['type'] = $row->type;
			$compRow['protected'] = $row->protected;
			$compRow['rar_name'] = $row->rar_name;
			if ($row->protected) {
				$compRow['protectedname'] = "Yes";
			} else {
				$compRow['protectedname'] = "No";
			}
			$compRow['weighting'] = $row->weighting;
			if ($row->max_pos > 0) {
				$compRow['runners'] = 0;
			} else {
				$compRow['runners'] = 1;
			}
			$compRow['entrants'] = $row->max_pos;
			$compRow['players'] = $row->tot_players;
			$compRow['notes'] = "";
			$this->competitionValidation($row->competition_id, $row->type, $compRow);
			//echo print_r($compRow)."\n";
			$returnData[] = $compRow;
		}
		return $returnData;
	}
	
	//*********************************************************************
	//*********************************************************************
	//
	// competitionValidation - A utility function to validate details
	//                          about the competition and create the
	//                          messages that admins see
	//
	//*********************************************************************
	//*********************************************************************
	function competitionValidation($competitionId, $type, &$compRow) {
		$notes = "";
		$sql = "
			SELECT count(*) as entrants
			FROM   position
			WHERE  competition_id = ?
			";
		$query = $this->db->query($sql, array($competitionId));
		$entrants = $query->row();
		$sql = "
			SELECT max(position) as last_place
			FROM   position
			WHERE  competition_id = ?
			";
		$query = $this->db->query($sql, array($competitionId));
		$lastPlace = $query->row();
		if (($lastPlace->last_place <> $entrants->entrants && $type == "Singles")
				|| ($lastPlace->last_place <> $entrants->entrants/2 && $type == "Doubles")) {
			$notes = "Results missing. ";
		}
		if ($entrants->entrants == 0) {
			$notes = "No results. ";
		}
		$sql = "
			SELECT count(*) as matches
			FROM matches
			WHERE competition_id = ?
			";
		$query = $this->db->query($sql, array($competitionId));
		$matches = $query->row();
		if ($matches->matches > 0 ) {
			if ($matches->matches%($entrants->entrants/2) <> 0) {
				$notes .= "Matches may be missing. ";
			}
		}else {
			$notes .= "No matches. ";
		}
		$compRow['notes'] = $notes;
	}
	
	//*********************************************************************
	//*********************************************************************
	//
	// getCompetitionStatus - A utility function to tell you if the 
	//                        competition already has results or not
	//
	//*********************************************************************
	//*********************************************************************
	function getCompetitionStatus($competitionId) {
		$sql = "
			SELECT count(*) as results
			FROM   position
			WHERE  competition_id = ?
			  AND  position > 0
			";
		$query = $this->db->query($sql, array($competitionId));
		return $query->row()->results;
	}
	
	//*********************************************************************
	//*********************************************************************
	//
	// getCompetitionDetails - get all the parameters for a competition 
	//
	//*********************************************************************
	//*********************************************************************
	function getCompetitionDetails($competitionId) {
		$sql = "
			SELECT c.competition_id
			, c.competition_name
			, c.competition_date
			, c.type
			, c.weighting
			, c.protected
			, c.glicko_eligible
			, c.competition_series_id
			, cs.competition_series_name
			, c.rar_id
			, r.rar_name
			, c.system_id
			, y.system_name
			, y.version
			, c.scale_id
			, s.desc as scale_desc
			, army_points
			, location
			, lists_allowed
			, rounds
			, num_competitors
			, c.scoring_system_id
			, ss.desc as scoring_system_desc
			, c.format_id
			, f.desc as format_desc
			, c.theme_id
			, t.desc as theme_desc
			, notes
			FROM competition c
			    ,competition_series cs
			    ,rar r
			    ,system y
			    ,scale s
			    ,scoring_system ss
			    ,theme t
			    ,format f
			WHERE c.competition_id = ?
			  AND c.rar_id = r.rar_id
			  AND cs.competition_series_id = c.competition_series_id
			  AND c.system_id = y.system_id
			  AND c.scale_id = s.scale_id
			  AND c.scoring_system_id = ss.scoring_system_id
			  AND c.theme_id = t.theme_id
			  AND c.format_id = f.format_id
		";
		$query = $this->db->query($sql, array($competitionId));
		return $query->row();
	}
	
	//*********************************************************************
	//*********************************************************************
	//
	// insert - insert a competition into the database, if you have permission 
	//
	//*********************************************************************
	//*********************************************************************
	function insert(
			$competitionName,
			$competitionDate,
			$type,
			$weighting,
			$protected,
			$glickoEligible,
			$competitionSeriesID,
			$rarId,
			$systemId,
			$scaleId,
			$armyPoints,
			$location,
			$listsAllowed,
			$rounds,
			$numCompetitors,
			$scoringSystemId,
			$formatId,
			$themeId,
			$notes
			) {
		if (isset($this->session->userdata['permissions']['Add Competition']) &&
		  $this->session->userdata['permissions']['Add Competition'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
			$data = array(
				'competition_name' => $competitionName,
				'competition_date' => $competitionDate,
				'type' => $type,
				'weighting' => $weighting,
				'protected' => $protected,
				'glicko_eligible' => $glickoEligible,
				'competition_series_id' => $competitionSeriesID,
				'rar_id' => $rarId,
				'system_id' => $systemId,
				'scale_id' => $scaleId,
				'army_points' => $armyPoints,
				'location' => $location,
				'lists_allowed' => $listsAllowed,
				'rounds' => $rounds,
				'num_competitors' => $numCompetitors,
				'scoring_system_id' => $scoringSystemId,
				'format_id' => $formatId,
				'theme_id' => $themeId,
				'notes' => $notes
			);
			$this->db->insert('competition', $data);
		   	if ($this->db->affected_rows() == 1) {	
				return array('status' => "OK", 'ID' => $this->db->insert_id());
			} else {
				return array('status' => "Error", 'errorMessage' => "Insert failed. Query was ".$this->db-last_query());
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to add a competition to competition series ".$competitionSeriesID);
		}
	}
	
	//*********************************************************************
	//*********************************************************************
	//
	// update - update a competition, if you have permission 
	//
	//*********************************************************************
	//*********************************************************************
	function update($id,
			$competitionName,
			$competitionDate,
			$type,
			$weighting,
			$protected,
			$glickoEligible,
			$competitionSeriesId,
			$rarId,
			$systemId,
			$scaleId,
			$armyPoints,
			$location,
			$listsAllowed,
			$rounds,
			$numCompetitors,
			$scoringSystemId,
			$formatId,
			$themeId,
			$notes
			) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($id));
		$oldCompetitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Add Competition']) &&
		  $this->session->userdata['permissions']['Add Competition'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesId])) &&
		  (isset($this->session->userdata['seriesPermissions'][$oldCompetitionSeriesID]))) {
			$data = array(
					'competition_name' => $competitionName,
					'competition_date' => $competitionDate,
					'type' => $type,
					'weighting' => $weighting,
					'protected' => $protected,
					'glicko_eligible' => $glickoEligible,
					'competition_series_id' => $competitionSeriesId,
					'rar_id' => $rarId,
					'system_id' => $systemId,
					'scale_id' => $scaleId,
					'army_points' => $armyPoints,
					'location' => $location,
					'lists_allowed' => $listsAllowed,
					'rounds' => $rounds,
					'num_competitors' => $numCompetitors,
					'scoring_system_id' => $scoringSystemId,
					'format_id' => $formatId,
					'theme_id' => $themeId,
					'notes' => $notes
					);
			$this->db->where('competition_id', $id);
			$this->db->update('competition', $data);
			return array('status' => "OK", 'ID' => $id);
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to edit this competition.");
		}
	}	

	function getSystems() {
		$sql = "
		SELECT system_id, system_name, version FROM system ORDER BY system_name, version
		";
		$query = $this->db->query($sql, array());
		return $query->result();
		
	}
	
	function getScales() {
		$sql = "SELECT s.desc, s.scale_id FROM scale s ORDER BY s.desc, s.scale_id";
		$query = $this->db->query($sql, array());
		return $query->result();
	}
	
	function getScoringSystems() {
		$sql = "SELECT scoring_system_id, s.desc FROM scoring_system s ORDER BY scoring_system_id";
		$query = $this->db->query($sql, array());
		return $query->result();
	}
	
	function getFormats() {
		$sql = "SELECT format_id, f.desc FROM format f ORDER BY f.desc";
		$query = $this->db->query($sql, array());
		return $query->result();
	}
	
	function getThemes() {
		$sql = "SELECT theme_id, t.desc FROM theme t ORDER BY theme_id";
		$query = $this->db->query($sql, array());
		return $query->result();
	}
	
	function delete($id) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($id));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Update Competition']) &&
		  $this->session->userdata['permissions']['Update Competition'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
		    	$this->db->where('competition_id', $id);
		    	$this->db->delete('competition');
		    	if ($this->db->affected_rows() == 1) {
				return array('status' => "OK");
			} else {
				return array('status' => "Error", 'errorMessage' => "Delete failed");
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to delete this competition.");
		}
	}
}