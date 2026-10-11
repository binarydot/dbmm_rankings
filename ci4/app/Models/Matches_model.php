<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;
class Matches_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function matchesByResult() {
    	$sql = "SELECT m.winner_score
    			, m.loser_score
    			, s.system_id
    			, s.system_name
    			, s.version
    			, count(*) as total_matches
    			FROM matches m, competition c, system s
    			WHERE m.winner_score + m.loser_score = 25
    			AND m.winner_score < 26
    			AND m.winner_score > 12
    			AND m.competition_id = c.competition_id
    			AND c.system_id = s.system_id
    			GROUP BY winner_score, loser_score, s.system_name, s.version, s.system_id
    			ORDER BY s.system_name, s.version, m.winner_score DESC
    			";
    	$query = $this->db->query($sql, array());
    	$data = $query->result();
    	$dataArray = array();
   		$currSystem = -1;
   		foreach ($data as $row) {
			if ($row->system_id != $currSystem) {
				if ($currSystem > -1) {
	   				foreach ($dataArray[$currSystem]["scores"] as &$r) {
    					$r["percent"] = number_format($r["number"] / $total * 100,2);
    				}
				}
				$currSystem = $row->system_id;
    			$dataArray[$currSystem] = array();
    			$dataArray[$currSystem]["name"] = $row->system_name." ".$row->version;
    			$dataArray[$currSystem]["scores"] = array();
    			$dataArray[$currSystem]["total"] = 0;
		   		$total = 0;
		   		$p = 0;
			}
			$total += $row->total_matches;
    		$dataArray[$currSystem]["total"] += $row->total_matches;
    		$result = number_format($row->winner_score)." - ".number_format($row->loser_score);
    		$dataArray[$currSystem]["scores"][$result] = array();
			$dataArray[$currSystem]["scores"][$result]["number"] = $row->total_matches;
			$p++;
   		}
   		foreach ($dataArray[$currSystem]["scores"] as &$r) {
   			$r["percent"] = number_format($r["number"] / $total * 100,2);
   		}
   		//print_r($dataArray);
   		return $dataArray;
    }
    
	function deleteMatch($compID, $matchID) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($compID));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Update Matches']) &&
		  $this->session->userdata['permissions']['Update Matches'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
		    	$sql = "DELETE FROM matches WHERE match_id = ?";
		    	$this->db->query($sql, array($matchID));
		    	if ($this->db->affected_rows() == 1) {
		    		$sql = "DELETE FROM match_participant WHERE  match_id = ?";
		    		$this->db->query($sql, array($matchID));
		    		if ($this->db->affected_rows() == 1) {
		    			return array('status' => "OK", 'ID' => $matchID);
		    		} else {
		    			return array('status' => "Error", 'errorMessage' => "Delete failed");
		    		}
		    	} else {
		    		return array('status' => "Error", 'errorMessage' => "Delete failed");
		    	}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to delete this match.");
		}
	}
    
    // get the matches for a competition
    function getMatches($id) {
    	$returnData = array();
    	$returnData['errorMessage'] = "";
    	$sql = "SELECT c.competition_name
	    	, c.competition_date
	    	, c.competition_series_id
	    	, cs.competition_series_name
	    	FROM competition c
	    	    ,competition_series cs
	    	WHERE c.competition_id = ?
	    	  AND c.competition_series_id = cs.competition_series_id
    	";
    	$query = $this->db->query($sql, array($id));
    	$row = $query->row();
    	$returnData['competitionName'] = $row->competition_name;
    	$returnData['competitionDate'] = $row->competition_date;
    	$returnData['competitionSeriesID'] = $row->competition_series_id;
    	$returnData['competitionSeriesName'] = $row->competition_series_name;
    	$sql = "SELECT m.match_id
	    	, m.round
	    	, m.winner_score
	    	, m.loser_score
	    	FROM matches m
	    	WHERE m.competition_id = ?
	    	ORDER BY m.round, m.winner_score DESC
    	";
    	$query = $this->db->query($sql, array($id));
    	$data = $query->result();
    	foreach( $data AS $row ){
    		$matchId = $row->match_id;
    		$returnData['rounds'][$row->round][$matchId]['errorMessage'] = "";
    		$returnData['rounds'][$row->round][$matchId]['ID'] = $matchId;
    		$returnData['rounds'][$row->round][$matchId]['winnerScore'] = $row->winner_score;
    		$returnData['rounds'][$row->round][$matchId]['loserScore'] = $row->loser_score;
    		$returnData['rounds'][$row->round][$matchId]['score'] = $row->winner_score.' - '.$row->loser_score;
    		$sql = "SELECT c.first_name
	    		,c.last_name
	    		,c.competitor_id
	    		,IFNULL(mp.army_id, p.army_id) AS army_id
	    		,IFNULL(a.name, a1.name) AS name
	    		FROM   competitor c
	    		,match_participant mp
	    		LEFT JOIN (position p, armies a)
	    		ON (mp.competitor_id = p.competitor_id
	    		AND  p.competition_id = ?
	    		AND a.army_id = IFNULL(mp.army_id, p.army_id)
	    		)
	    		LEFT JOIN (stand_in s, armies a1)
	    		ON (mp.competitor_id = s.competitor_id
	    		AND a1.army_id = mp.army_id)
	    		WHERE  mp.competitor_id = c.competitor_id
	    		AND  mp.match_id = ?
	    		AND  mp.side = 'W'
    		";
    		$query = $this->db->query($sql, array($id, $matchId));
    		$winnerData = $query->result();
    		$numWinners = $query->num_rows();
    		foreach ($winnerData as $winnerRow) {
    			$winner['ID'] = $winnerRow->competitor_id;
    			$winner['name'] = $winnerRow->first_name.' '.$winnerRow->last_name;
    			$returnData['rounds'][$row->round][$matchId]['winners'][] = $winner;
    			$returnData['rounds'][$row->round][$matchId]['winnerArmy']['ID'] = $winnerRow->army_id;
    			$returnData['rounds'][$row->round][$matchId]['winnerArmy']['name'] = $winnerRow->name;
    		}
    		$sql = "
    		SELECT c.first_name
    		,c.last_name
    		,c.competitor_id
    		,IFNULL(mp.army_id, p.army_id) AS army_id
    		,IFNULL(a.name, a1.name) AS name
    		FROM   competitor c
    		,match_participant mp
    		LEFT JOIN (position p, armies a)
    		ON (mp.competitor_id = p.competitor_id
    		AND  p.competition_id = ?
    		AND a.army_id = IFNULL(mp.army_id, p.army_id)
    		)
    		LEFT JOIN (stand_in s, armies a1)
    		ON (mp.competitor_id = s.competitor_id
    		AND a1.army_id = mp.army_id)
    		WHERE  mp.competitor_id = c.competitor_id
    		AND  mp.match_id = ?
    		AND  mp.side = 'L'
    		";
    		$query = $this->db->query($sql, array($id, $matchId));
    		$loserData = $query->result();
    		$numLosers = $query->num_rows();
    		foreach ($loserData as $loserRow) {
    			$loser['ID'] = $loserRow->competitor_id;
    			$loser['name'] = $loserRow->first_name.' '.$loserRow->last_name;
    			$returnData['rounds'][$row->round][$matchId]['losers'][] = $loser;
    			$returnData['rounds'][$row->round][$matchId]['loserArmy']['ID'] = $loserRow->army_id;
    			$returnData['rounds'][$row->round][$matchId]['loserArmy']['name'] = $loserRow->name;
    		}
    		if ($row->winner_score + $row->loser_score != 25) {
    			$returnData['rounds'][$row->round][$matchId]['errorMessage'] .= "Scores do not add up to 25. ";
    		}
    
    	}
    	return $returnData;
    }

    // This function is used for the old style matches where there were an unlimited number of
    // players per side. This is no longer used and so this function is deprecated, but it remains
    // in case we want to implement similar functionality again.
    /*
    function createMatch($compID, $winnerList, $loserList, $round, $winnerScore, $loserScore, $matchDate, $glickoEligible, $winnerArmy, $loserArmy) {
    	$data = array(
    			'competition_id' => $compID,
    			'round' => $round,
    			'winner_score' => $winnerScore,
    			'loser_score' => $loserScore,
    			'match_date' => $matchDate,
    			'glicko_eligible' => $glickoEligible,
    			'glicko_processed' => 0
    	);
    	$this->db->insert_string('matches', $data);
    	if ($this->db->affected_rows > 0) {
    		//$query = $this->db->query("SELECT max(match_id) as match_id FROM matches");
    		//if ($query->num_rows() > 0) {
    		//	$row = $query->row();
    		//	$matchID = $row->match_id;
    		$matchID = $this->db->insert_id();
    		foreach($winnerList as $winner) {
    			$data = array(
    					'match_id' => $matchID,
    					'competitor_id' => $winner,
    					'side' => 'W',
    					'army_id' => $winnerArmy
    			);
    			$this->db->insert_string('match_participant', $data);
    		}
    		foreach($loserList as $loser) {
    			$data = array(
    					'match_id' => $matchID,
    					'competitor_id' => $loser,
    					'side' => 'L',
    					'army_id' => $loserArmy
    			);
    			$this->db->insert_string('match_participant', $data);
    		}
    		return array('status' => 'OK', 'ID' => $matchID);
    		//}
    	} else {
    		return array('status' => "Error", 'errorMessage' => "Insert failed");
    	}
    }
    */
    
	function insertMatch($id
				,$matchDate
				,$glickoEligible
				,$round
				,$winnerId0
				,$winnerId1
				,$winnerArmy
				,$winnerScore
				,$loserScore
				,$loserId0
				,$loserId1
				,$loserArmy
				) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($id));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Add Matches']) &&
		  $this->session->userdata['permissions']['Add Matches'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
	    	// save the match
	    	echo "Saving the match";
	    	$data = array(
	    			'competition_id' => $id,
	    			'round' => $round,
	    			'winner_score' => $winnerScore,
	    			'loser_score' => $loserScore,
	    			'glicko_eligible' => $glickoEligible,
	    			'glicko_processed' => 'N',
	    			'match_date' => $matchDate,
	    			'match_seqno' => substr('$matchdate',1,4).substr('$matchdate',5,2).substr('$matchdate',9,2).$round,
	    			'pred_winner_score' => 0,
	    			'pred_loser_score' => 0
	    	);
	    	$this->db->insert('matches', $data);
	    	if ($this->db->affected_rows() == 1) {
	    		$matchId = $this->db->insert_id();
	    		$participantErrors = 0;
	    		if ($winnerArmy != -1) {
			    	$data = array(
			    			'match_id' => $matchId,
			    			'competitor_id' => $winnerId0,
			    			'side' => 'W',
			    			'army_id' => $winnerArmy
			    	);
			    	$this->db->insert('match_participant', $data);
			    	if ($this->db->affected_rows() < 1) {
			    		$participantErrors++;
			    	}
			    	if ($winnerId1 > -1) {
		    		    $data = array(
			    			'match_id' => $matchId,
			    			'competitor_id' => $winnerId1,
			    			'side' => 'W',
			    			'army_id' => $winnerArmy
			    		);
			    		$this->db->insert('match_participant', $data);
			    		if ($this->db->affected_rows() < 1) {
			    			$participantErrors++;
			    		}
			    	}
			} else {
			    	$data = array(
			    			'match_id' => $matchId,
			    			'competitor_id' => $winnerId0,
			    			'side' => 'W'
			    	);
			    	$this->db->insert('match_participant', $data);
			    	if ($this->db->affected_rows() < 1) {
			    		$participantErrors++;
			    	}
			    	if ($winnerId1 > -1) {
		    		    $data = array(
			    			'match_id' => $matchId,
			    			'competitor_id' => $winnerId1,
			    			'side' => 'W'
			    		);
			    		$this->db->insert('match_participant', $data);
			    		if ($this->db->affected_rows() < 1) {
			    			$participantErrors++;
			    		}
			    	}
			}
			if ($loserArmy != -1) {
			    	$data = array(
			    			'match_id' => $matchId,
			    			'competitor_id' => $loserId0,
			    			'side' => 'L',
			    			'army_id' => $loserArmy
			    	);
			    	$this->db->insert('match_participant', $data);
			    	if ($this->db->affected_rows() < 1) {
			    		$participantErrors++;
			    	}
		    		if ($loserId1 > -1) {
		    		    $data = array(
			    			'match_id' => $matchId,
			    			'competitor_id' => $loserId1,
			    			'side' => 'L',
			    			'army_id' => $loserArmy
			    		);
			    		$this->db->insert('match_participant', $data);
			    		if ($this->db->affected_rows() < 1) {
			    			$participantErrors++;
			    		}
			    	}
			} else {
			    	$data = array(
			    			'match_id' => $matchId,
			    			'competitor_id' => $loserId0,
			    			'side' => 'L'
			    	);
			    	$this->db->insert('match_participant', $data);
			    	if ($this->db->affected_rows() < 1) {
			    		$participantErrors++;
			    	}
		    		if ($loserId1 > -1) {
		    		    $data = array(
			    			'match_id' => $matchId,
			    			'competitor_id' => $loserId1,
			    			'side' => 'L'
			    		);
			    		$this->db->insert('match_participant', $data);
			    		if ($this->db->affected_rows() < 1) {
			    			$participantErrors++;
			    		}
			    	}
			}
			if ($participantErrors > 0) {
		    		return array('status' => "OK", 'ID' => $matchId);
		    	} else {
	    			return array('status' => "Error", 'errorMessage' => "Insert of ".$participantErrors." match participants failed. Query was ".$this->db->last_query().". ");
		    	}
	    	} else {
	    		return array('status' => "Error", 'errorMessage' => "Insert of match failed.  Query was ".$this->db->last_query());
	       	}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to insert this match.");
		}
	}

    function updateMatch($compID
    			,$matchId
				,$matchDate
				,$glickoEligible
				,$round
				,$winnerId0
				,$winnerId1
				,$winnerArmy
				,$winnerScore
				,$loserScore
				,$loserId0
				,$loserId1
				,$loserArmy
				) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($compID));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Update Matches']) &&
		  $this->session->userdata['permissions']['Update Matches'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
    	// save the match
    	$data = array(
    			'competition_id' => $compID,
    			'round' => $round,
    			'winner_score' => $winnerScore,
    			'loser_score' => $loserScore,
    			'glicko_eligible' => $glickoEligible,
    			'glicko_processed' => 'N',
    			'match_date' => $matchDate,
    			'match_seqno' => substr('$matchdate',1,4).substr('$matchdate',5,2).substr('$matchdate',9,2).$round,
    			'pred_winner_score' => 0,
    			'pred_loser_score' => 0
    	);
    	$this->db->update('matches', $data, array('match_id' => $matchId));
    	// now we need to check the match participants
    	// its crude, but the easiest way to do this is to delete all of them and then
    	// reinsert them
    	$this->db->delete('match_participant', array('match_id' => $matchId));
   		$participantErrors = 0;
    	$data = array(
    			'match_id' => $matchId,
    			'competitor_id' => $winnerId0,
    			'side' => 'W',
    			'army_id' => $winnerArmy
    	);
    	$this->db->insert('match_participant', $data);
    	if ($this->db->affected_rows() < 1) {
    		$participantErrors++;
    	}
   		$data = array(
    			'match_id' => $matchId,
    			'competitor_id' => $loserId0,
    			'side' => 'L',
    			'army_id' => $loserArmy
    	);
    	$this->db->insert('match_participant', $data);
    	if ($this->db->affected_rows() < 1) {
    		$participantErrors++;
    	}
    	if ($winnerId1 > -1) {
   		    $data = array(
    			'match_id' => $matchId,
    			'competitor_id' => $winnerId1,
    			'side' => 'W',
    			'army_id' => $winnerArmy
    		);
    		$this->db->insert('match_participant', $data);
    		if ($this->db->affected_rows() < 1) {
    			$participantErrors++;
    		}
    	}
   		if ($loserId1 > -1) {
   		    $data = array(
    			'match_id' => $matchId,
    			'competitor_id' => $loserId1,
    			'side' => 'L',
    			'army_id' => $loserArmy
    		);
    		$this->db->insert('match_participant', $data);
    		if ($this->db->affected_rows() < 1) {
    			$participantErrors++;
    		}
    	}
    	if ($participantErrors) {
    		return array('status' => "OK", 'ID' => $matchId);
    	} else {
   		return array('status' => "Error", 'errorMessage' => "Insert of ".$participantErrors." match participants failed. Query was ".$this->db->last_query().". ");
    	}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to update this match.");
		}
    }
    
    // get the details for a single match
    function getMatchDetails($compId, $id) {
    	$returnData = array();
    	$returnData['errorMessage'] = "";
     	$sql = "SELECT m.match_id
    	, m.round
    	, m.match_date
    	, m.winner_score
    	, m.loser_score
    	, m.glicko_eligible
    	FROM matches m
    	WHERE m.match_id = ?
    	ORDER BY m.round, m.winner_score DESC
    	";
    	$query = $this->db->query($sql, array($id));
    	$data = $query->row();
    	$returnData['round'] = $data->round;
    	$returnData['matchDate'] = $data->match_date;
    	$returnData['glickoEligible'] = $data->glicko_eligible;
    	$returnData['winnerScore'] = $data->winner_score;
    	$returnData['loserScore'] = $data->loser_score;
   		$sql = "SELECT c.first_name
    		,c.last_name
    		,c.competitor_id
    		,IFNULL(mp.army_id, p.army_id) AS army_id
    		,IFNULL(a.name, a1.name) AS name
    		FROM   competitor c
    		,match_participant mp
    		LEFT JOIN (position p, armies a)
    		ON (mp.competitor_id = p.competitor_id
    		AND  p.competition_id = ?
    		AND a.army_id = IFNULL(mp.army_id, p.army_id)
    		)
    		LEFT JOIN (stand_in s, armies a1)
    		ON (mp.competitor_id = s.competitor_id
    		AND a1.army_id = mp.army_id)
    		WHERE  mp.competitor_id = c.competitor_id
    		AND  mp.match_id = ?
    		AND  mp.side = 'W'
    		";
   		$query = $this->db->query($sql, array($compId, $id));
   		$winnerData = $query->result();
   		$numWinners = $query->num_rows();
   		foreach ($winnerData as $winnerRow) {
   			$winner['ID'] = $winnerRow->competitor_id;
   			$winner['name'] = $winnerRow->first_name.' '.$winnerRow->last_name;
   			$returnData['winners'][] = $winner;
   			$returnData['winnerArmy']['ID'] = $winnerRow->army_id;
   			$returnData['winnerArmy']['name'] = $winnerRow->name;
   		}
   		$sql = "
    		SELECT c.first_name
    		,c.last_name
    		,c.competitor_id
    		,IFNULL(mp.army_id, p.army_id) AS army_id
    		,IFNULL(a.name, a1.name) AS name
    		FROM   competitor c
    		,match_participant mp
    		LEFT JOIN (position p, armies a)
    		ON (mp.competitor_id = p.competitor_id
    		AND  p.competition_id = ?
    		AND a.army_id = IFNULL(mp.army_id, p.army_id)
    		)
    		LEFT JOIN (stand_in s, armies a1)
    		ON (mp.competitor_id = s.competitor_id
    		AND a1.army_id = mp.army_id)
    		WHERE  mp.competitor_id = c.competitor_id
    		AND  mp.match_id = ?
    		AND  mp.side = 'L'
    		";
   		$query = $this->db->query($sql, array($compId, $id));
   		$loserData = $query->result();
   		$numLosers = $query->num_rows();
   		foreach ($loserData as $loserRow) {
   			$loser['ID'] = $loserRow->competitor_id;
   			$loser['name'] = $loserRow->first_name.' '.$loserRow->last_name;
   			$returnData['losers'][] = $loser;
   			$returnData['loserArmy']['ID'] = $loserRow->army_id;
   			$returnData['loserArmy']['name'] = $loserRow->name;
   		}
    	return $returnData;
    }

    function delete($id) {
		$sql = "SELECT competition_series_id FROM competition c, matches m WHERE c.competition_id = m.competition_id and m.match_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($id));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Update Matches']) &&
		  $this->session->userdata['permissions']['Update Matches'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
    	$this->db->where('match_id', $id);
    	$this->db->delete('matches');
    	if ($this->db->affected_rows() == 1) {
    	    $this->db->where('match_id', $id);
    		$this->db->delete('match_participants');
    		if ($this->db->affected_rows() == 1) {
    			return "OK";
    		} else {
    			return array('status' => "Error", 'errorMessage' => "Match participant delete failed");
    		}
    	} else {
    		return array('status' => "Error", 'errorMessage' => "Match delete failed");
    	}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to delete this match.");
		}
    }
    
    function deleteAllMatchesForComp($compId) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($compId));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Update Matches']) &&
		  $this->session->userdata['permissions']['Update Matches'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
    	$this->db->where('competition_id', $compId);
    	$this->db->delete('matches');
    	if ($this->db->affected_rows() == 1) {
    		$sql = "DELETE FROM match_participants
    			WHERE match_id IN (
    			SELECT match_id
    			FROM matches
    			WHERE competition_id = ?
    			)
    		";
    		$query = $this->db->query($sql, array($compId));
    		$results = $query->result();
    		return array('status' => "OK");
    	} else {
    		return array('status' => "Error", 'errorMessage' => "Delete failed");
    	}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to delete this match.");
		}
    }
    
    function getPlayerHistory($id) {
    	$sqlX = "
	    	SELECT c.first_name
		    	,c.last_name
		    	,c.competitor_id
		    	,IFNULL(mp.army_id, p.army_id) AS army_id
		    	,IFNULL(a.name, a1.name) AS name
		FROM   competitor c
		    	,match_participant mp
	    	LEFT JOIN (position p, armies a)
	    	ON (mp.competitor_id = p.competitor_id
	    	-- AND  p.competition_id = 
	    	AND a.army_id = IFNULL(mp.army_id, p.army_id)
	    	)
	    	LEFT JOIN (stand_in s, armies a1)
	    	ON (mp.competitor_id = s.competitor_id
	    	AND a1.army_id = mp.army_id)
	    	WHERE  mp.competitor_id = c.competitor_id
	    	AND  mp.competitor_id = ?
	    	AND  mp.side = 'L'
	    	";
	$sql = "SELECT c.competition_name
		 , c.competition_date
		 , c.competition_id
		 , m.match_id
		 , m.round
		 , m.winner_score
		 , m.loser_score
		 , mp.side
		 , a.name as army
		 , a.army_id
		 FROM competition c
	     , matches m
	     , match_participant mp
	     , position p
		 LEFT JOIN armies a ON a.army_id = p.army_id
		 WHERE c.competition_id = m.competition_id
		 AND mp.competitor_id = ?
		 AND mp.match_id = m.match_id
		 AND p.competitor_id = mp.competitor_id
		 AND p.competition_id = m.competition_id
		 AND p.type = 'P'
		UNION
		 SELECT c.competition_name
		 , c.competition_date
		 , c.competition_id
		 , m.match_id
		 , m.round
		 , m.winner_score
		 , m.loser_score
		 , mp.side
		 , a.name as army
		 , a.army_id
		 FROM competition c
	     , matches m
	     , match_participant mp
	     , stand_in s
	     , position p
		 LEFT JOIN armies a ON a.army_id = p.army_id
		 WHERE c.competition_id = m.competition_id
		 AND mp.competitor_id = ?
		 AND mp.match_id = m.match_id
		 AND s.competitor_id = mp.competitor_id
		 AND p.competition_id = m.competition_id	
		 AND s.competition_id = m.competition_id
		 AND s.stood_in_for = p.competitor_id						  
	ORDER BY competition_date, round";
			 
    	$query = $this->db->query($sql, array($id, $id));
    	$results = $query->result();
    	foreach ($results as &$result) {
    		$winnerDataSql = "
		   SELECT c.first_name
		   ,c.last_name
		   ,c.competitor_id
		   ,a.name
		   ,a.army_id
		   FROM   competitor c
		   ,match_participant mp
	       LEFT JOIN armies a ON a.army_id = mp.army_id
		   WHERE  mp.competitor_id = c.competitor_id
		   AND  mp.match_id = ?
		   AND  mp.side = 'W'
			   ";
		$winnerQuery = $this->db->query($winnerDataSql, array($result->match_id));
		$winners = $winnerQuery->result();
		if  ($winnerQuery->num_rows() > 0) {
			$result->winner1FirstName = $winners[0]->first_name;
			$result->winner1LastName = $winners[0]->last_name;
			$result->winner1CompetitorId = $winners[0]->competitor_id;
			$result->winner1ArmyName = $winners[0]->name;
			$result->winner1ArmyId = $winners[0]->army_id;
		}
		if  ($winnerQuery->num_rows() > 1) {
			$result->winner2FirstName = $winners[1]->first_name;
			$result->winner2LastName = $winners[1]->last_name;
			$result->winner2CompetitorId = $winners[1]->competitor_id;
			$result->winner2ArmyName = $winners[1]->name;
			$result->winner2ArmyId = $winners[1]->army_id;
		}
    		$loserDataSql = "
		   SELECT c.first_name
		   ,c.last_name
		   ,c.competitor_id
		   ,a.name
		   ,a.army_id
		   FROM   competitor c
		   ,match_participant mp
	       LEFT JOIN armies a ON a.army_id = mp.army_id
		   WHERE  mp.competitor_id = c.competitor_id
		   AND  mp.match_id = ?
		   AND  mp.side = 'L'
			   ";
		$loserQuery = $this->db->query($loserDataSql, array($result->match_id));
		$losers = $loserQuery->result();
		if  ($loserQuery->num_rows() > 0) {
			$result->loser1FirstName = $losers[0]->first_name;
			$result->loser1LastName = $losers[0]->last_name;
			$result->loser1CompetitorId = $losers[0]->competitor_id;
			$result->loser1ArmyName = $losers[0]->name;
			$result->loser1ArmyId = $losers[0]->army_id;
		}
		if  ($loserQuery->num_rows() > 1) {
			$result->loser2FirstName = $losers[1]->first_name;
			$result->loser2LastName = $losers[1]->last_name;
			$result->loser2CompetitorId = $losers[1]->competitor_id;
			$result->loser2ArmyName = $losers[1]->name;
			$result->loser2ArmyId = $losers[1]->army_id;
		}
    	}
    	return $results;
    }

    function allMatchDetails() {
    	$sql = "    SELECT s.system_name
    	              ,s.version
    	              ,c.competition_date
    	              ,m.round
    	              ,c.competition_id
    	              ,c.competition_name
    	              ,p.competitor_id
    	              ,pp.first_name
    	              ,pp.last_name
	,p.army_id
	,a.name as army_name
	,a.version as army_version
	,s2.system_name as army_system
	,mp.side 
	,m.winner_score
	,m.loser_score
	from   competition c, position p, competitor pp, armies a, matches m, system s, match_participant mp, system s2
	where c.system_id = s.system_id
	and c.competition_id = p.competition_id
	and p.competitor_id = pp.competitor_id
	and p.army_id = a.army_id
	and p.competition_id = m.competition_id
	and p.competitor_id = mp.competitor_id
	and mp.match_id = m.match_id
	and a.system_id = s2.system_id
	";
    	$query = $this->db->query($sql, array());
    	return $query->result();
    }

    function allMatchDetailsByYear($year) {
    	$sql = "    SELECT s.system_name
    	              ,s.version
    	              ,c.competition_date
    	              ,m.round
    	              ,c.competition_id
    	              ,c.competition_name
    	              ,p.competitor_id
    	              ,pp.first_name
    	              ,pp.last_name
	,p.army_id
	,a.name as army_name
	,a.version as army_version
	,s2.system_name as army_system
	,mp.side 
	,m.winner_score
	,m.loser_score
	from   competition c, position p, competitor pp, armies a, matches m, system s, match_participant mp, system s2
	where c.system_id = s.system_id
	and c.competition_id = p.competition_id
	and p.competitor_id = pp.competitor_id
	and p.army_id = a.army_id
	and p.competition_id = m.competition_id
	and p.competitor_id = mp.competitor_id
	and mp.match_id = m.match_id
	and a.system_id = s2.system_id
	and year(c.competition_date) = ?
	";
    	$query = $this->db->query($sql, array($year));
    	return $query->result();
    }
    
    function statsByYear() {
    	$sql=" SELECT year(match_date) as year
    			,COUNT(*) as matches
    		FROM matches
    		GROUP BY 1
    		ORDER BY 1
    		";
    	$query = $this->db->query($sql, array());
    	$data = $query->result();
    	foreach ($data as $item) {
    		$returnData[$item->year]['matches'] = $item->matches;
    	}
    	$sql=" SELECT year(c.competition_date) as year
    			,COUNT(DISTINCT p.competitor_id) as players
    		FROM competition c
    		    ,position p
    		WHERE c.competition_id = p.competition_id
    		GROUP BY 1
    		ORDER BY 1
    		";
    	$query = $this->db->query($sql, array());
    	$data = $query->result();
    	foreach ($data as $item) {
    		$returnData[$item->year]['players'] = $item->players;
    	}
    	$sql=" SELECT year(c.competition_date) as year
    			,COUNT(DISTINCT p.competitor_id) as players
    		FROM competition c
    		    ,position p
    		WHERE year(c.competition_date) = (
    		    SELECT min(year(c2.competition_date))
    		    FROM competition c2
    		    WHERE c2.competition_id = p.competition_id
    		    )
    		GROUP BY 1
    		ORDER BY 1
    		";
    	$query = $this->db->query($sql, array());
    	$data = $query->result();
    	foreach ($data as $item) {
    		$returnData[$item->year]['newbies'] = $item->players;
    	}
    	return $returnData;
    }

}
?>