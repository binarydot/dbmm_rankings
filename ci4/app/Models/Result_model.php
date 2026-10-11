<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;
class Result_model extends CI_Model {

	var $id = '';
	var $rarId = '';

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	//*************************************************************************
	//*************************************************************************
	// LIST RESULTS BY COMPETITION
	//*************************************************************************
	//*************************************************************************
	// wow has this function got complicated...
	function getResults($id) {
		$sql = "
			SELECT max(p.position) as position
			FROM   position p
			WHERE  p.competition_id = ?
		";
		$query = $this->db->query($sql, array($id));
		$teams = $query->row()->position;
		if ($query->num_rows() == 0) {
			// there are no positions yet, so this is a runners and riders screen
			$results['fullResults'] = 0;
			$orderBy = "ORDER BY rating DESC";
		} else {
			$results['fullResults'] = 1;
			$orderBy = "ORDER BY position";
		}
		$sql = "
			SELECT cs.competition_series_id as id
			,cs.competition_series_name as name
			FROM   competition c
			,competition_series cs
			WHERE c.competition_id = ?
			AND   c.competition_series_id = cs.competition_series_id
		";
		$query = $this->db->query($sql, array($id));
		$seriesData = $query->row();
		$results['seriesID'] = $seriesData->id;
		$results['seriesName'] = $seriesData->name;
		$sql = "
			SELECT competition_name
			, competition_date
			, weighting
			FROM   competition
			WHERE  competition_id = ?
			";
		$query = $this->db->query($sql, array($id));
		$results['competitionName'] = $query->row()->competition_name;
		$results['competitionDate'] = $query->row()->competition_date;
		$results['competitionID'] = $id;
		$results['weighting'] = $query->row()->weighting;
		// changed IFNULL(max(g.rating),2000) AS rating to IFNULL(g.rating,2000) to fix a bug when someone only has one rating in the glicko table
		$sql = "SELECT p.competitor_id
			, p.first_name
			, p.last_name
			, s.position
			, s.score
			, s.position_id
			, s.army_id
			, s.bonus
			, a.name
			, IFNULL(g.rating, 2000) AS rating
			, s.pairing as pairing
			, r.rar_name
			FROM competitor p
			JOIN rar r 
			ON p.rar_id = r.rar_id
            JOIN position s 
            ON s.competitor_id = p.competitor_id
			JOIN competition c 
			ON c.competition_id = s.competition_id
			LEFT JOIN armies a 
			ON a.army_id = s.army_id
			LEFT JOIN glicko_rating g 
			ON g.competitor_id = s.competitor_id
			AND g.rating_date = (SELECT MAX(rating_date)
			FROM glicko_rating g2
			WHERE g2.competitor_id = g.competitor_id
			AND g2.match_date < c.competition_date
			)
			WHERE s.competition_id = ?
			AND s.type = 'P'
		".$orderBy;
		$query = $this->db->query($sql, array($id));
		$data = $query->result();
		/*
		$a = 0;
		foreach( $data AS $row ){
			$competitorList[$a] = array();
			$competitorList[$a]['ID'] = $row->competitor_id;
			$competitorList[$a]['FirstName'] = $row->first_name;
			$competitorList[$a]['LastName'] = $row->last_name;
			$a++;
		*/
		$results['compRating'] = 0;
		if ($query->num_rows()) {
			$results['resultsAvailable'] = 1;
		} else {
			$results['resultsAvailable'] = 0;
		}
		foreach( $data AS $row ){
			$result['competitorID'] = $row->competitor_id;
			$result['competitorName'] = $row->first_name." ".$row->last_name;
			$result['armyID'] = $row->army_id;
			$result['armyName'] = $row->name;
			$result['position'] = $row->position;
			$result['score'] = $row->score;
			$result['bonus'] = $row->bonus;
			$result['glickoRating'] = $row->rating;
			$result['positionID'] = $row->position_id;
			$result['pairing'] = $row->pairing;
			$result['rarName'] = $row->rar_name;
			if ($results['fullResults']) {
				if ($teams > 1) {
					$result['rankingPoints'] = (($teams - $row->position) / ($teams - 1) * ($results['weighting'] - 1)) + 1;
				} else {
					$result['rankingPoints'] = $results['weighting'];
				}
				$sql = "
					SELECT sum(winner_score) as winner_score
					FROM matches m
					,match_participant mp
					WHERE m.match_id = mp.match_id
					AND   mp.competitor_id = ?
					AND   m.competition_id = ?
					AND   mp.side = 'W'
					";
				$query = $this->db->query($sql,array($result['competitorID'], $id));
				$matchScoreW = $query->row()->winner_score;
				$sql = "
					SELECT sum(loser_score) as loser_score
					FROM matches m
					,match_participant mp
					WHERE m.match_id = mp.match_id
					AND   mp.competitor_id = ?
					AND   m.competition_id = ?
					AND   mp.side = 'L'
					";
				$query = $this->db->query($sql,array($result['competitorID'], $id));
				$matchScoreL = $query->row()->loser_score;
				if (($matchScoreW + $matchScoreL) != ($row->score - $row->bonus)) {
					$result['errorMessage'] = "Match scores do not match result score!";
				} else {
					$result['errorMessage'] = "";
				}
			}
			$results['results'][] = $result;
			$results['compRating'] += $row->rating;
		}
		$sql = "
			SELECT u.umpire_id
			,p.competitor_id
			,p.first_name
			,p.last_name
			FROM   competitor p
			,umpire u
			WHERE  p.competitor_id = u.competitor_id
			AND  u.competition_id = ?
		";
		$query = $this->db->query($sql, array($id));
		$umpires = $query->result();
		if ($query->num_rows()) {
			$results['umpires'] = 1;
		} else {
			$results['umpires'] = 0;
		}
		foreach ($umpires as $row) {
			$umpire['name'] = $row->first_name.' '.$row->last_name;
			$umpire['competitorID'] = $row->competitor_id;
			$umpire['umpireID'] = $row->umpire_id;
			$results['umpireList'][] = $umpire;
		}
		$sql = "
			SELECT s.stand_in_id
			      ,p.competitor_id
			      ,p.first_name
			      ,p.last_name
			      ,p1.competitor_id as stoodinid
			      ,p1.first_name as stoodinfn
			      ,p1.last_name as stoodinln
			FROM   competitor p
			      ,stand_in s
			      ,competitor p1
			WHERE  p.competitor_id = s.competitor_id
			  AND  s.competition_id = ?
			  AND  s.stood_in_for = p1.competitor_id
		";
		$query = $this->db->query($sql, array($id));
		$standins = $query->result();
		if ($query->num_rows()) {
			$results['standins'] = 1;
		} else {
			$results['standins'] = 0;
		}
		foreach ($standins as $row) {
			$standin['name'] = $row->first_name.' '.$row->last_name;
			$standin['competitorID'] = $row->competitor_id;
			$standin['standinID'] = $row->stand_in_id;
			$standin['stoodInFor'] = $row->stoodinfn.' '.$row->stoodinln;
			$standin['stoodInForID'] = $row->stoodinid;
			$results['standinList'][] = $standin;
		}
		return $results;
	}
	
	
	//insert a single result for a competition
	// this may have more than one competitor in a mixed or doubles comp
	function insertResult($competitionId, $competitorId, $armyId, $score, $bonus, $position) {
		$data = array(
			 'competition_id' => $competitionId
			 ,'competitor_id' => $competitorId
			 ,'position' => $position
			 ,'score' => $score
			 ,'army_id' => $armyId
			 ,'bonus' => $bonus
		);
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($competitionId));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Add Result']) &&
		  $this->session->userdata['permissions']['Add Result'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
			$this->db->insert('position', $data);
			if ($this->db->affected_rows() == 1) {
				return array('status' => "OK", 'ID' => $this->db->insert_id());
			} else {
				return array('status' => "Error", 'errorMessage' => "Insert failed. Query was ".$this->db->last_query());
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to insert this result.");
		}
	}
	
	// insert a doubles result
	function insertDoublesResult($competitionId, $competitorId1, $competitorId2, $armyId, $score, $bonus, $position) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($competitionId));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Add Result']) &&
		  $this->session->userdata['permissions']['Add Result'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
			$sql = "
				SELECT MAX(p.pairing) as max_pairing_value
				FROM   position p
				WHERE  p.competition_id = ?
			";
			$query = $this->db->query($sql, array($competitionId));
			$results = $query->result();
			$max_pairing_value = $results[0]->max_pairing_value + 1;
			$data = array(
					'competition_id' => $competitionId
					,'competitor_id' => $competitorId1
					,'position' => $position
					,'score' => $score
					,'army_id' => $armyId
					,'bonus' => $bonus
					,'pairing' => $max_pairing_value
			);
			$this->db->insert('position', $data);
			$data = array(
					'competition_id' => $competitionId
					,'competitor_id' => $competitorId2
					,'position' => $position
					,'score' => $score
					,'army_id' => $armyId
					,'bonus' => $bonus
					,'pairing' => $max_pairing_value
			);
			$this->db->insert('position', $data);
			if ($this->db->affected_rows() == 1) {
				return array('status' => "OK", 'ID' => $this->db->insert_id());
			} else {
				return array('status' => "Error", 'errorMessage' => "Insert failed. Query was ".$this->db->last_query());
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to insert this result.");
		}
	}
	
	// insert a runner for a competition
	function insertRunner($competitionId, $competitorId, $armyId) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($competitionId));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Add Result']) &&
		  $this->session->userdata['permissions']['Add Result'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
			$data = array(
					'competition_id' => $competitionId
					,'competitor_id' => $competitorId
					,'army_id' => $armyId
			);
			$this->db->insert('position', $data);
			if ($this->db->affected_rows() == 1) {
				return array('status' => "OK", 'ID' => $this->db->insert_id());
			} else {
				return array('status' => "Error", 'errorMessage' => "Insert failed");
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to insert this result.");
		}
	
	}
	
	function insertDoublesRunner($competitionId, $competitorId1, $competitorId2, $armyId) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($competitionId));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Add Result']) &&
		  $this->session->userdata['permissions']['Add Result'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
			$sql = "
				SELECT MAX(p.pairing) as max_pairing_value
				FROM   position p
				WHERE  p.competition_id = ?
			";
			$query = $this->db->query($sql, array($competitionId));
			$results = $query->result();
			$max_pairing_value = $results[0]->max_pairing_value + 1;
			$data = array(
					'competition_id' => $competitionId
					,'competitor_id' => $competitorId1
					,'army_id' => $armyId
					,'pairing' => $max_pairing_value
			);
			$this->db->insert('position', $data);
			$data = array(
					'competition_id' => $competitionId
					,'competitor_id' => $competitorId2
					,'army_id' => $armyId
					,'pairing' => $max_pairing_value
			);
			$this->db->insert('position', $data);
			if ($this->db->affected_rows() == 1) {
				return array('status' => "OK", 'ID' => $this->db->insert_id());
			} else {
				return array('status' => "Error", 'errorMessage' => "Insert failed");
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to insert this result.");
		}
	
	}
	

	function getResultDetails($id) {
		$sql = "SELECT p.competitor_id
		, p.first_name
		, p.last_name
		, s.position
		, s.score
		, s.position_id
		, s.army_id
		, s.bonus
		, a.name
		, s.pairing
		FROM competitor p
		, position s
		LEFT JOIN armies a ON a.army_id = s.army_id
		WHERE s.competitor_id = p.competitor_id
		AND s.position_id = ?
		AND s.type = 'P'
		";
		$query = $this->db->query($sql, array($id));
		return $query->row();
		
	} 
	
	//update a single result for a competition
	function updateResult($positionID, $competitorId, $armyId, $score, $bonus, $position) {
		$sql = "SELECT competition_series_id FROM competition c, position p WHERE p.competition_id = c.competition_id AND p.position_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($positionID));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Update Result']) &&
		  $this->session->userdata['permissions']['Update Result'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
			$data = array(
				  'competitor_id' => $competitorId
				 ,'position' => $position
				 ,'score' => $score
				 ,'army_id' => $armyId
				 ,'bonus' => $bonus
			);
			$this->db->update('position', $data, array('position_id' => $positionID));
			if ($this->db->affected_rows() == 1) {
				return array('status' => "OK", 'ID' => $positionID);
			} else {
				return array('status' => "Error", 'errorMessage' => "Update failed. Query was ".$this->db->last_query());
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to update this result.");
		}
		
	}

	function deleteResult($id) {
		$sql = "SELECT competition_series_id FROM competition c, position p WHERE p.competition_id = c.competition_id AND p.position_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($positionId));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Update Result']) &&
		  $this->session->userdata['permissions']['Update Result'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
			$this->db->where('position_id', $id);
			$this->db->delete('position');
			if ($this->db->affected_rows() == 1) {
				return array('status' => "OK");
			} else {
				return array('status' => "Error", 'errorMessage' => "Delete failed");
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to update this result.");
		}
	}
	
	function validateResultData($resultData) {
		$dataOK = true;
		$errorMessage = "";
		$numCols = 0;
		//if (FALSE == empty($_FILES['csvfile']['tmp_name'])) {
		if (isset($resultData) && $resultData['full_path'] != "") {
			$row = 1;
			//$handle = fopen($_FILES['csvfile']['tmp_name'], "r");
			$handle = fopen($resultData['full_path'], "r");
			$returnData = array();
			$returnData['results'] = array();
			while (($data = fgetcsv($handle, 1000, ","))!== FALSE) {
				$num = count($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				if ($row == 1) {
					$rounds = ($num - 5) / 2;
				}
				if (!is_int($rounds)) {
					$dataOK = false;
					$errorMessage .= "Uneven number of opponent and score columns. ";
					break;
				} else {
					$r = $row - 1;
					//echo "<p>";
					//print_r($data);
					//echo "</p>";
					if (isset($data[1])) {
						$returnData['results'][$r] = array();
						$returnData['results'][$r]['position'] = $data[0];
						$returnData['results'][$r]['id'] = $data[1];
						$returnData['results'][$r]['name'] = $data[2];
						$returnData['results'][$r]['score'] = $data[$num - 2];
						$returnData['results'][$r]['army'] = $data[$num - 1];
						$returnData['results'][$r]['opponent'] = array();
						for ($c = 3; $c < $rounds + 3; $c++) {
							$returnData['results'][$r]['opponent'][($c - 2)] = $data[$c];
						}
						$returnData['results'][$r]['matchScore'] = array();
						for ($c = $rounds + 3; $c < ($rounds * 2) + 3; $c++) {
							$returnData['results'][$r]['matchScore'][($c - $rounds - 2)] = $data[$c];
						}
					}
				}
				$row++;
			}
			$returnData['rounds'] = $rounds;
			fclose($handle);
			if ($dataOK) {
				return array('status' => "OK", 'data' => $returnData, 'errorMessage' => $errorMessage);
			} else {
				return array('status' => "Error", 'errorMessage' => $errorMessage);
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "No file for processing");
		}
		
	}
	
	function buildRoundByRoundMatches($returnData, $errorMessage, $errorsFounds) {
		// this will only work for singles.
		// we will build this into the $returnData['matches'] array
		$playersDone = array();
		//$roundData = array();
		$returnData['matches'] = array();
		for ($round = 1; $round <= $returnData['rounds']; $round++) {
			$playersDone[$round] = array();
			//$roundData[$a] = array();
			$returnData['matches']['round'.$round] = array();
			$returnData['matches']['round'.$round]['number'] = $round;
			$returnData['matches']['round'.$round]['matches'] = array();
			//$matchCounter = 0;
			//for ($b = 0; $b < $returnData['results'].length; $b++) {
			foreach ($returnData['results'] as $result) {
				$alreadyDone = 0;
				for ($c = 0; $c < count($playersDone[$round]); $c++) {
					if ($playersDone[$round][$c] == $result['id']) {
						$alreadyDone = 1;
					}
				}
				if ($alreadyDone) {
					//we have already processed this player as the opponent of someone else
				} else {
					//first time we have encountered this player this round
					// did he actually play someone - if the opponent is X, then the match did not take place
					// if B, it was a Bye
					if (strtoupper($result['opponent'][$round]) == "X") {
						//ignore the line
						array_push($playersDone[$round],$result['id']);
					} else if (strtoupper($result['opponent'][$round]) == "B") {
						// record it as a bye
						// the opponent is the BYE
						$oppName = "BYE";
						$oppScore = 0;
						array_push($playersDone[$round],$result['id']);
						$tempData = array();
						$tempData["winnerId"] = $result['id'];
						$tempData["loserId"] = -1;
						$tempData["winnerName"] = $result['name'];
						$tempData["loserName"] = $oppName;
						$tempData["winningScore"] = $result['matchScore'][$round];
						$tempData["losingScore"] = $oppScore;
						array_push($returnData['matches']['round'.$round]['matches'],$tempData);						
						//$matchCounter++;
					} else {
						//find the opponent
						$oppId = $result['opponent'][$round];
						$oppName = "";
						//for (c = 0; c < res.length; c++) {
						$tempData = array();
						foreach ($returnData['results'] as $opponent) {
							if ($opponent['id'] == $oppId) {
								array_push($playersDone[$round],$result['id']);
								array_push($playersDone[$round],$oppId);
								$oppName = $opponent['name'];
								if ($result['matchScore'][$round] >= $opponent['matchScore'][$round]) {
									$tempData['winnerId'] = $result['id'];
									$tempData['loserId'] = $opponent["id"];
									$tempData['winnerName'] = $result['name'];
									$tempData['loserName'] = $opponent['name'];
									$tempData['winningScore'] = $result['matchScore'][$round];
									$tempData['losingScore'] = $opponent['matchScore'][$round];
								} else {
									$tempData['winnerId'] = $opponent['id'];
									$tempData['loserId'] = $result['id'];
									$tempData['winnerName'] = $opponent['name'];
									$tempData['loserName'] = $result['name'];
									$tempData['winningScore'] = $opponent['matchScore'][$round];
									$tempData['losingScore'] = $result['matchScore'][$round];	
								}
								break;
							}
						}
						if ($oppName == "") {
							$errorMessage.=" No opponent found for ".$result['name']." in round ".$round.". ";
						} else {
							array_push($returnData['matches']['round'.$round]['matches'],$tempData);
						}
						//$matchCounter++;
					}
				}
			}
		}
	}

	
	function checkUploadData($returnData) {
		//echo "<p>";
		//print_r($returnData);
		//echo "</p>";
		//for ($a = 0; $a < count($returnData['results']); $a++) {
		$errorMessage = "";
		$dataOK = true;
		$errorsFound = false;
		foreach ($returnData['results'] as $result) {
			//echo "Checking line ".$a;
			$result['name'] = trim($result['name']);
			$firstName = addslashes(substr($result['name'],0,strpos($result['name']," ")));
			$lastName = addslashes(substr($result['name'],strrpos($result['name']," ") + 1,strlen($result['name'])));
			$army = addslashes($result['army']);
			$sql = "
					SELECT competitor_id
					,iwf_id
					FROM   competitor
					WHERE  first_name = ?
					AND    last_name  = ?
					";
			$query = $this->db->query($sql, array($firstName, $lastName));
			$queryResults = $query->result();
			if (count($queryResults) < 1) {
				$dataOK = false;
				$errorMessage .= "Player |".$firstName."| |".$lastName."| does not exist in the database. ";
				$result['competitorId'] = -2;
				$result['iwfId'] = "";
				$result['nameValidation'] = "Not Found";
			} else if (count($queryResults) > 1) {
				$dataOK = false;
				$errorMessage .= "Player |".$firstName."| |".$lastName."| matches more than once in the database. ";
				$result['competitorId'] = -2;
				$result['iwfId'] = "";
				$result['nameValidation'] = "Duplicates Found";
				$result['nameOptions'] = array();
				for ($b = 0; $b < count($queryResults); $b++) {
					$result['nameOptions'][$b] = array();
					$result['nameOptions'][$b]['iwfId'] = $queryResults[$b]->iwf_id;
					$result['nameOptions'][$b]['competitorId'] = $queryResults[$b]->competitor_id;
				}
			} else {
				$result['competitorId'] = $queryResults[0]->competitor_id;
				$result['iwfId'] = $queryResults[0]->iwf_id;
				$result['nameValidation'] = "OK";
			}
			$sql = "
				SELECT a.army_id
				,a.name
				,s.system_name
				,a.version
				FROM   armies a
				,system s
				,army_books ab
				WHERE  a.name = ?
				AND    a.system_id = s.system_id
				AND a.book_id = ab.book_id
				AND ab.status = 'A'
				ORDER BY a.army_id desc
			";
			$query = $this->db->query($sql, array($army));
			$queryResults = $query->result();
			if (count($queryResults) < 1) {
				$dataOK = false;
				$errorMessage .= "Army |".$army."| does not exist in the database. ";
				$result['army_id'] = -1;
				$result['armyValidation'] = "Not Found";
			} else if (count($queryResults) > 1) {
				$dataOK = false;
				$errorMessage .= "Army |".$army."| matches more than once in the database. ";
				$result['army_id'] = -1;
				$result['armyValidation'] = "Duplicates Found";
				$result['armyOptions'] = array();
				for ($b = 0; $b < count($queryResults); $b++) {
					$result['armyOptions'][$b] = array();
					$result['armyOptions'][$b]['armyId'] = $queryResults[$b]->army_id;
					$result['armyOptions'][$b]['version'] = $queryResults[$b]->system_name." v".$queryResults[$b]->version;
				}
			} else {
				$result['army_id'] = $queryResults[0]->army_id;
				$result['armyValidation'] = "OK";
			}
		}
		// right - that's the results array built.
		// now we need to work out the matches as well.
		$this->buildRoundByRoundMatches($returnData, $errorMessage, $errorsFound);
		if (!$errorsFound) {
			return array('status' => "OK", 'data' => $returnData, 'errorMessage' => $errorMessage);
		} else {
			return array('status' => "Error", 'errorMessage' => $errorMessage);
		}

	}
	

	function deleteAllResultsForComp($compId) {
		$sql = "SELECT competition_series_id FROM competition WHERE competition_id = ? LIMIT 1";
		$query = $this->db->query($sql, array($compId));
		$competitionSeriesID = $query->row()->competition_series_id;
		if (isset($this->session->userdata['permissions']['Add Result']) &&
		  $this->session->userdata['permissions']['Add Result'] == 1 && 
		  (isset($this->session->userdata['seriesPermissions'][$competitionSeriesID]))) {
			$this->db->where('competition_id', $compId);
			$this->db->delete('position');
			if ($this->db->affected_rows() == 1) {
				return array('status' => "OK");
			} else {
				return array('status' => "Error", 'errorMessage' => "Delete failed");
			}
		} else {
			return array('status' => "Error", 'errorMessage' => "You do not have permission to update this result.");
		}
	}
	
	function getPlayerHistory($id) {
		$sql = "SELECT c.competition_id
				, c.competition_name
				, c.competition_date
				, c.competition_series_id
				, p.competitor_id
				, p.first_name
				, p.last_name
				, s.position
				, s.score
				, s.position_id
				, s.army_id
				, c.weighting
				, a.name
				, s.bonus
				FROM competition c
				, competitor p
				, position s
				LEFT JOIN armies a ON a.army_id = s.army_id
				WHERE c.competition_id = s.competition_id
				AND s.competitor_id = p.competitor_id
				AND s.competitor_id = ?
				AND s.position > 0
				ORDER BY competition_date
				";
		$query = $this->db->query($sql, array($id));
		return $query->result();
	}
	
	//*************************************************************************
	//*************************************************************************
	// Submarine Data - who was at what position in each round
	//*************************************************************************
	//*************************************************************************
	function getSubmarineData($id) {
		$sql = "
			SELECT cs.competition_series_id as id
			,cs.competition_series_name as name
			FROM   competition c
			,competition_series cs
			WHERE c.competition_id = ?
			AND   c.competition_series_id = cs.competition_series_id
		";
		$query = $this->db->query($sql, array($id));
		$seriesData = $query->row();
		$results['seriesID'] = $seriesData->id;
		$results['seriesName'] = $seriesData->name;
		$sql = "
			SELECT competition_name
			, competition_date
			FROM   competition
			WHERE  competition_id = ?
			";
		$query = $this->db->query($sql, array($id));
		$results['competitionName'] = $query->row()->competition_name;
		$results['competitionDate'] = $query->row()->competition_date;
		$results['competitionID'] = $id;
		$sql = "SELECT p.competitor_id
			, p.first_name
			, p.last_name
			, s.position
			, s.score
			, s.position_id
			, s.army_id
			, s.bonus
			, s.pairing as pairing
			, r.rar_name
			FROM competitor p
			, rar r
			, competition c
			, position s
			WHERE s.competitor_id = p.competitor_id
			AND c.competition_id = s.competition_id
			AND s.competition_id = ?
			AND s.type = 'P'
			AND p.rar_id = r.rar_id
			ORDER BY position
		";
		$query = $this->db->query($sql, array($id));
		$data = $query->result();
		/*
		$a = 0;
		foreach( $data AS $row ){
			$competitorList[$a] = array();
			$competitorList[$a]['ID'] = $row->competitor_id;
			$competitorList[$a]['FirstName'] = $row->first_name;
			$competitorList[$a]['LastName'] = $row->last_name;
			$a++;
		*/
		if ($query->num_rows()) {
			$results['resultsAvailable'] = 1;
		} else {
			$results['resultsAvailable'] = 0;
		}
		$rounds = 0;
		foreach( $data AS $row ){
			$result['competitorID'] = $row->competitor_id;
			$result['competitorName'] = $row->first_name." ".$row->last_name;
			$result['armyID'] = $row->army_id;
			$result['position'] = $row->position;
			$result['score'] = $row->score;
			$result['bonus'] = $row->bonus;
			$result['pairing'] = $row->pairing;
			$result['rarName'] = $row->rar_name;
			$result['matchScore'] = array();
			$sql = "
				SELECT winner_score
				      ,round
				FROM matches m
				,match_participant mp
				WHERE m.match_id = mp.match_id
				AND   mp.competitor_id = ?
				AND   m.competition_id = ?
				AND   mp.side = 'W'
				";
			$query = $this->db->query($sql,array($result['competitorID'], $id));
			$matchScoresW = $query->result();
			foreach ($matchScoresW as $matchScoreW) {
				$result['matchScore'][$matchScoreW->round] = $matchScoreW->winner_score;
			}
			$sql = "
				SELECT loser_score
				      ,round
				FROM matches m
				,match_participant mp
				WHERE m.match_id = mp.match_id
				AND   mp.competitor_id = ?
				AND   m.competition_id = ?
				AND   mp.side = 'L'
				";
			$query = $this->db->query($sql,array($result['competitorID'], $id));
			$matchScoresL = $query->result();
			foreach ($matchScoresL as $matchScoreL) {
				$result['matchScore'][$matchScoreL->round] = $matchScoreL->loser_score;
			}
			//sort the array by key - very important
			//if (isset($result['matchScore'])) {
				ksort($result['matchScore']);	
			//}
			// now work out the cumulative score for each player
			$cumScore = 0;
			foreach ($result['matchScore'] as $round=>$score) {
				$cumScore += $score;
				$result['cumScore'][$round] = $cumScore;
				if ($round > $rounds) {
					$rounds = $round;
				}
			}
			$results['results'][] = $result;
		}
		//print_r($results['results']);
		//now we need to create the matrix of position by round (for all except the last round)
		// which we already have. Use arsort to sort an associative array maintaing the key association.
		// In fact we should end up with an array that the Graph API is expecting, which is:
		// Round | Player A | Player B
		//    1  |    3        |   4
		//    2   |     2     | 1
		// First we need an array for each round, rather than the array for each player that we have:
		for ($i = 1; $i < $rounds; $i++) {
			foreach ($results['results'] as $result) {
				$roundScores[$i][$result['competitorName']] = $result['cumScore'][$i];
			}
			// now sort each round
			arsort($roundScores[$i]);
			// now create an array of position rather than score
			$prevScore = -1;
			$currPos = 1;
			$places = 1;
			foreach ($roundScores[$i] as $name=>$roundScore) {
				if ($prevScore == -1) {
					//this is the first person, but might be first equal
					$roundPos[$i][$name] = $currPos;
					$prevScore = $roundScore;
				} else if ($roundScore == $prevScore) {
					// this person is at the same position as the previous person
					$roundPos[$i][$name] = $currPos;
					$places++;
				} else {
					$currPos += $places;
					$roundPos[$i][$name] = $currPos;
					$places = 1;
					$prevScore = $roundScore;
				}
			}
		}
		//print_r($roundPos);
		//now create the array of the positions in the correct format
		// order the players by their position in the final round
		$arrayLine[] = 'Round';
		if (isset($results['results'])) {
			foreach ($results['results'] as $player) {
				$arrayLine[] = $player['competitorName'];
			}
		}
		$positions[] = $arrayLine;
		unset($arrayLine);
		//now create an array of positions in ecah round
		for ($i = 1; $i < $rounds; $i++) {
			$arrayLine[] = (string)$i;
			foreach ($results['results'] as $player) {
				$arrayLine[] = $roundPos[$i][$player['competitorName']];
			}
			$positions[] = $arrayLine;
			unset($arrayLine);
		}
		$arrayLine[] = (string)$rounds;
		if (isset($results['results'])) {
			foreach ($results['results'] as $player) {
				$arrayLine[] = (int)$player['position'];
			}
		}
		$positions[] = $arrayLine;
		$results['positions'] = json_encode($positions);
		$results['rounds'] = $rounds;
		return $results;
	}

}