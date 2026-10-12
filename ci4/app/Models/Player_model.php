<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;
class Player_model extends CI_Model {

    var $id = '';
    var $firstName = '';
    var $lastName = '';
    var $iwfId = '';
    var $rarId = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function getPlayer($id) {
        $sql = "
        	SELECT c.competitor_id
			      ,c.first_name
			      ,c.last_name
			      ,c.cr_date
			      ,c.iwf_id
			      ,c.rar_id
			      ,c.anonymous
			      ,r.rar_name 
			FROM competitor c, rar r 
			WHERE c.rar_id = r.rar_id and c.competitor_id = ?
		";
		$query = $this->db->query($sql, array($id));
		$result1 = $query->row_array();
		$sql = "
				SELECT ifnull(min(p.position),0) as best_place
				,ifnull(max(p.position),0) as worst_place
				,count(*) as num_comps
				FROM   position p
				WHERE  p.competitor_id = ?
				";
		$query = $this->db->query($sql, array($id));
		$result2 = $query->row_array();
		$result = array_merge($result1, $result2);
		return $result;
    }
    
    function getPlayerArmies($id) {
    	$sql = "
			SELECT a.name
				 , a.army_id
				 , count(*) as uses
			FROM armies a
				,position p
			WHERE a.army_id = p.army_id
			AND   p.competitor_id = ?
			GROUP BY 1,2
			ORDER BY 3 DESC
        ";
    	$query = $this->db->query($sql, array($id));
        return $query->result();
    }
    
    function getPlayerMatchStats($id) {
    	$sql = "
			 SELECT m.winner_score as my_score
			 , m.loser_score as your_score
			 , s.system_id
			 , s.system_name
			 , s.version
			 , count(*) as total_matches
			 FROM matches m, competition c, system s, match_participant p
			 WHERE m.winner_score + m.loser_score = 25
			 AND m.winner_score < 26
			 AND m.winner_score > 12
			 AND m.competition_id = c.competition_id
			 AND c.system_id = s.system_id
			 AND m.match_id = p.match_id
			 AND p.side = 'W'
			 AND p.competitor_id = ?
			 GROUP BY winner_score, loser_score, s.system_name, s.version, s.system_id
			 UNION
			 SELECT m.loser_score as my_score
			 , m.winner_score as your_score
			 , s.system_id
			 , s.system_name
			 , s.version
			 , count(*) as total_matches
			 FROM matches m, competition c, system s, match_participant p
			 WHERE m.winner_score + m.loser_score = 25
			 AND m.winner_score < 26
			 AND m.winner_score > 12
			 AND m.competition_id = c.competition_id
			 AND c.system_id = s.system_id
			 AND m.match_id = p.match_id
			 AND p.side = 'L'
			 AND p.competitor_id = ?
			 GROUP BY winner_score, loser_score, s.system_name, s.version, s.system_id
			 ORDER BY 4, 5, 1 DESC
		";
    	$query = $this->db->query($sql, array($id, $id));
    	$matchStatData = $query->result();
		$dataArray = array();
		if ($matchStatData) {
			$currSystem = $matchStatData[0]->system_id;
			$total = 0;
			$p = 0;
			$dataArray[$currSystem] = array();
			$dataArray[$currSystem]["name"] = $matchStatData[0]->system_name." ".$matchStatData[0]->version;
			$dataArray[$currSystem]["scores"] = array();
			foreach ($matchStatData as $row) {
				if ($row->system_id == $currSystem) {
					$total += $row->total_matches;
					$dataArray[$currSystem]["total"] += $row->total_matches;
					$result = number_format($row->my_score)." - ".number_format($row->your_score);
					$dataArray[$currSystem]["scores"][$result] = array();
					$dataArray[$currSystem]["scores"][$result]["number"] = $row->total_matches;
					$p++;
				} else {
					foreach ($dataArray[$currSystem]["scores"] as &$r) {
						$r["percent"] = number_format($r["number"] / $total * 100,2);
					}
					$currSystem = $row->system_id;
					$total = $row->total_matches;
					$p = 0;
					$dataArray[$currSystem] = array();
					$dataArray[$currSystem]["name"] = $row->system_name." ".$row->version;
					$dataArray[$currSystem]["scores"] = array();
					$dataArray[$currSystem]["total"] += $row->total_matches;
					$result = number_format($row->my_score)." - ".number_format($row->your_score);
					$dataArray[$currSystem]["scores"][$result] = array();
					$dataArray[$currSystem]["scores"][$result]["number"] = $row->total_matches;
					$p++;
				}
			}
			foreach ($dataArray[$currSystem]["scores"] as &$r) {
				$r["percent"] = number_format($r["number"] / $total * 100,2);
			}
			return $dataArray;
		} else {
			return FALSE;
		}
    }
    
    function getTotalPlayers($rar) {
    	if ($rar > 0) {
			$rarsqlwhere = "
				,rar r
				WHERE  r.rar_id = c.rar_id
				AND c.rar_id = ?";
		} else {
			$rarsqlwhere = "";
		}
    	$sql = "
			SELECT count(c.competitor_id) as total_players
			FROM competitor c
			".$rarsqlwhere."
    	";
    	if ($rar > 0) {
			$query = $this->db->query($sql, array($rar));
    	} else {
			$query = $this->db->query($sql, array());
    	}
    	$data = $query->row();
    	return $data->total_players;
    }

    // utility function to get a full list of players
    function getPlayers() {
    	$sql = "
    	SELECT c.competitor_id
    	,c.first_name
    	,c.last_name
    	,c.iwf_id
    	,c.cr_date
    	,c.anonymous
    	,r.rar_name
    	FROM competitor c
    	,rar r
    	WHERE  r.rar_id = c.rar_id
    	ORDER BY c.first_name, c.last_name
    	";
   		$query = $this->db->query($sql, array());
    	return $query->result();
    }
    
    // pageNum is the number of the page. The first page is 1, not 0.
    function getPlayersPaged($rar, $pageNumber, $pageSize) {
		// decrement the pageNum so we are zero-based
		$pageNumber--; 
		$startOffset = $pageNumber * $pageSize;
		// restrict by rar if specified
    	if ($rar > 0) {
			$rarsqlwhere = "AND c.rar_id = ?";
		} else {
			$rarsqlwhere = "";
		}
    	$sql = "
			SELECT c.competitor_id
			      ,c.first_name
			      ,c.last_name
			      ,c.iwf_id
			      ,c.cr_date
			      ,c.anonymous
			      ,r.rar_name
			FROM competitor c
			    ,rar r
			WHERE  r.rar_id = c.rar_id
			".$rarsqlwhere."
			ORDER BY c.last_name, c.first_name
			LIMIT ?, ?
    	";
    	if ($rar > 0) {
			$query = $this->db->query($sql, array($rar, $startOffset, $pageSize));
    	} else {
			$query = $this->db->query($sql, array($startOffset, $pageSize));
    	}
    	$data = $query->result();
    	$pageData = array();
		foreach( $data AS $row ){
			$sql = "
				SELECT ifnull(min(p.position),0) as best_place
				      ,ifnull(max(p.position),0) as worst_place
				      ,count(*) as num_comps
				FROM   position p
				WHERE  p.competitor_id = ?
				  AND  p.position > 0
			";
			$query = $this->db->query($sql, array($row->competitor_id));
			$compRow = $query->row();
			$sql = "
				SELECT count(distinct p.army_id) as armies
				FROM position p
				WHERE p.competitor_id = ?
			";
			$query = $this->db->query($sql, array($row->competitor_id));
			$armyRow = $query->row();
			$playerItem = array(
				'firstName' => $row->first_name,
				'lastName' => $row->last_name,
				'competitorID' => $row->competitor_id,
				'iwfID' => $row->iwf_id,
				'createDate' => $row->cr_date,
				'anonymous' => $row->anonymous,
				'rarName' => $row->rar_name,
				'bestPlace' => $compRow->best_place,
				'worstPlace' => $compRow->worst_place,
				'numComps' => $compRow->num_comps,
				'numArmies' => $armyRow->armies
			);
			$pageData[] = $playerItem;
		}
		return $pageData;
    }
    
    //get all initial letters from player surnames that are in use
    function getInitialLetterList() {
    	$sql = "
    		SELECT DISTINCT LEFT(last_name,1) AS initial
    		FROM competitor
    		ORDER BY 1
    	";
    	$query = $this->db->query($sql, array());
    	return $query->result();
    }

    // get all the players whose surname start with a specific letter
    function getPlayersByInitial($firstLetter) {
    	$sql = "
    	SELECT c.competitor_id
    	,c.first_name
    	,c.last_name
    	,c.iwf_id
    	,c.cr_date
    	,c.anonymous
    	,r.rar_name
    	FROM competitor c
    	    ,rar r
    	WHERE  c.last_name like ?
    	  AND c.rar_id = r.rar_id
    	ORDER BY c.last_name, c.first_name
    	";
    	$firstLetterSearch = $firstLetter.'%';
   		$query = $this->db->query($sql, array($firstLetterSearch));
    	$data = $query->result();
    	$pageData = array();
    	foreach( $data AS $row ){
    		$sql = "
    		SELECT ifnull(min(p.position),0) as best_place
    		,ifnull(max(p.position),0) as worst_place
    		,count(*) as num_comps
    		FROM   position p
    		WHERE  p.competitor_id = ?
    		AND  p.position > 0
    		";
    		$query = $this->db->query($sql, array($row->competitor_id));
    		$compRow = $query->row();
    		$sql = "
    		SELECT count(distinct p.army_id) as armies
    		FROM position p
    		WHERE p.competitor_id = ?
    		";
    		$query = $this->db->query($sql, array($row->competitor_id));
    		$armyRow = $query->row();
    		if ($compRow->best_place > 0) {
	    		$sql = "
    				SELECT max(p.position) as best_place_from
    				FROM position p
    				WHERE p.competition_id IN (
    					SELECT p1.competition_id
    					FROM position p1
    					WHERE p1.position = ?
    					and p1.competitor_id = ?
    				)
    			";
    			$query = $this->db->query($sql, array($compRow->best_place, $row->competitor_id));
    			$bestFrom = $query->row();
	    		$sql = "
    				SELECT max(p.position) as worst_place_from
    				FROM position p
    				WHERE p.competition_id IN (
    					SELECT p1.competition_id
    					FROM position p1
    					WHERE p1.position = ?
    					and p1.competitor_id = ?
    				)
    			";
    			$query = $this->db->query($sql, array($compRow->worst_place, $row->competitor_id));
    			$worstFrom = $query->row();
    		}
    		$playerItem = array(
    				'firstName' => $row->first_name,
    				'lastName' => $row->last_name,
    				'competitorID' => $row->competitor_id,
    				'iwfID' => $row->iwf_id,
    				'createDate' => $row->cr_date,
    				'anonymous' => $row->anonymous,
    				'rarName' => $row->rar_name,
    				'bestPlace' => $compRow->best_place,
    				'worstPlace' => $compRow->worst_place,
    				'numComps' => $compRow->num_comps,
    				'numArmies' => $armyRow->armies,
    				'bestPlaceFrom' => $bestFrom->best_place_from,
    				'worstPlaceFrom' => $worstFrom->worst_place_from
    		);
    		$pageData[] = $playerItem;
    	}
    	return $pageData;
    }
    
    function insert($firstName, $lastName, $iwfID, $rarID, $anonymous) {
		$data = array(
			'first_name' => $firstName,
			'last_name' => $lastName,
			'iwf_id' => $iwfID,
			'rar_id' => $rarID,
			'anonymous' => $anonymous
		);
		$this->db->insert('competitor', $data);
    	if ($this->db->affected_rows() == 1) {	
			return array('status' => "OK", 'ID' => $this->db->insert_id());
		} else {
			return array('status' => "Error", 'errorMessage' => "Insert failed.  Query was ".$this->db->last_query());
		}
    }

	function update($id, $firstName, $lastName, $iwfID, $rarID, $anonymous) {
    	if (isset($this->session->userdata['permissions']['Update Player']) &&
		    $this->session->userdata['permissions']['Update Player'] == 1) { 
	    	if (!isset($iwfID)) {
	    		$iwfID = "";
	    	}
	    	if (!isset($anonymous)) {
	    		$anonymous = 0;
	    	}
			$data = array(
				'first_name' => $firstName,
				'last_name' => $lastName,
				'iwf_id' => $iwfID,
				'rar_id' => $rarID,
				'anonymous' => $anonymous
			);
			//$this->db->where('competitor_id', $id);
			$this->db->update('competitor', $data, array('competitor_id' => $id));
			if ($this->db->affected_rows() == 1) {
				return array('status' => "OK", 'errorMessage' => "Row updated.");
			} else {
				//return array('status' => "Error", 'errorMessage' => "Update failed. Query was ".$this->db->last_query());
				return array('status' => "Error", 'errorMessage' => "The player was not updated.");
			}
		} else {
		    return array('status' => "Error", 'errorMessage' => "You do not have permission to update this player.");
		}
	}
    
	function delete($id) {
    	if (isset($this->session->userdata['permissions']['Update Player']) &&
		    $this->session->userdata['permissions']['Update Player'] == 1) { 
	    		$this->db->where('competitor_id', $id);
    			$this->db->delete('competitor');
    			if ($this->db->affected_rows() == 1) {
				return "OK";
			} else {
				return array('status' => "Error", 'errorMessage' => "Delete failed");
			}
		} else {
		    return array('status' => "Error", 'errorMessage' => "You do not have permission to delete this player.");
		}
	}

    function listPlayersInCompetition($competitionID) {
    	$sql = "
    			SELECT c.competitor_id
    			,c.first_name
    			,c.last_name
    			,p.army_id
    			FROM   competitor c
    			,position p
    			WHERE  c.competitor_id = p.competitor_id
    			AND  p.competition_id = ?
    			AND  p.type = 'P'
    			UNION
    			SELECT s.competitor_id
    			,c.first_name
    			,c.last_name
    			,p.army_id
    			FROM   competitor c
    			,stand_in s
    			,position p
    			WHERE  c.competitor_id = s.competitor_id
    			AND    s.competition_id = ?
    			AND    p.competitor_id = s.stood_in_for
    			AND    s.competition_id = p.competition_id
    			ORDER BY first_name, last_name
    			";
    	$query = $this->db->query($sql, array($competitionID, $competitionID));
    	$results = $query->result();
    	return $results;
   	}
    
}
?>