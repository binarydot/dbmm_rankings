<?php

namespace App\Models;

use CodeIgniter\Model;

class CompRankingsModel extends Model {
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    // Note that for the large array of results that we return,
    // we prepend the main index of playerId with "Id" because
    // array_multisort will renumber the indexes of a numeric 
    // key, but not one with strings.
    // We are using PlayerId as the primary key because it 
    // should always be unique while we could have two players
    // with the same name.
    function getRankings($year, $rar) {

    	//inner function needed for the sort later on
    	$compareRankings = static function ($a, $b) {
		if ($a["playerData"]["ranking"] > $b["playerData"]["ranking"]) {
			return -1;
		} elseif ($a["playerData"]["ranking"] == $b["playerData"]["ranking"]) {
			return 0;
		} else {
			return 1;
		}
	};
    	
    	date_default_timezone_set('UTC');
		// Get the data for the rankings
		// Check if the RAR is set and is valid
		if (isset($rar) && is_numeric($rar) && $rar >= 0 && $rar < 10000) {
			$useRar = TRUE;
			$rarSqlWhere = " AND c.rar_id = ?";
			$rarSqlFrom = "";
		} else if (isset($rar) && is_numeric($rar) && $rar >= 10000) {
			$useRar = TRUE;
			$rarSqlWhere = " AND g.rar_group_id = ? AND c.rar_id = g.rar_id";
			$rarSqlFrom = ", rar_group_link g";
		} else {
			$useRar = FALSE;
			$rarSqlWhere = "";
			$rarSqlFrom = "";
			$rar = -1;
		}
		// sort out start and end date
		if (is_numeric($year) && $year > 2000) {
			$startDate = date("Y-m-d",mktime(0,0,0,1,1,$year));
			$endDate = date("Y-m-d",mktime(0,0,0,12,31,$year));
		} else {
			$startDate = date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")-1));
			$endDate = date("Y-m-d");
			$year = date("Y");
		}
    	    	// Set up the config information
    
		$sql = "
			SELECT IFNULL(comps, 6) as comps
			      ,IFNULL(max_score, 600) as max_score
			      ,IFNULL(calc_type, 'B') as calc_type
			FROM   ranking_settings
			WHERE  rar_id = ?
			AND    ?   >= (SELECT MAX(YEAR) FROM ranking_settings WHERE rar_id = ?)
		";
		$query = $this->db->query($sql, array($rar, $year, $rar));
		$config = $query->getRow();
		if ($query->getNumRows() > 0) {
			$minComps = $config->comps;
			$maxScore = $config->max_score;
			if ($config->calc_type == "A") {
				$calcType = "averaged";
			} else {
				$calcType = "bestof";
			}
		} else {
			$minComps = 5;
			$maxScore = 500;
			$calcType = "bestof";
		}
		if (!isset($minComps) || !isset($maxScore) || !isset($calcType)) {
			$minComps = 5;
			$maxScore = 500;
			$calcType = "bestof";
		}
		
		// get the data from the database
		$sql = "
			SELECT p.competition_id
			      ,c.competition_name
			      ,c.competition_date
			      ,c.type
			      ,max(p.position) as worst_place
			      ,count(p.position) as competitors
			      ,c.protected
			      ,c.weighting
			FROM   position p
			      ,competition c
			".$rarSqlFrom."
			WHERE  p.competition_id = c.competition_id
			AND    c.competition_date >= ?
			AND    c.competition_date <= ?
			".$rarSqlWhere."
			GROUP BY p.competition_id, competition_name, type
			ORDER BY c.competition_date
		";
		if ($useRar) {
			$query = $this->db->query($sql, array($startDate, $endDate, $rar));
		} else {
			$query = $this->db->query($sql, array($startDate, $endDate));
		}
		$competitions = $query->getResult();
		foreach ($competitions as $ckey => $c) {
			if ($c->worst_place < 1) {
				unset($competitions[$ckey]);
			} else {
				if ($c->type == "Singles") {
					$compNums[$c->competition_id] = $c->worst_place;
				} else if ($c->type == "Doubles") {
					$compNums[$c->competition_id] = $c->competitors / 2;
				} else {
					$compNums[$c->competition_id] = $c->worst_place;
				}
			}
		}
		$playerId = -1;
		// p.position > 0 is added in order to exclude competitions where there are no results, just runners, otherwise we get an index not
		// found later on when it tried to calculate the rankings for a competition with no results.
		$sql = "
			SELECT a.competitor_id
			      ,a.first_name
			      ,a.last_name
			      ,c.competition_id
			      ,c.competition_name
			      ,c.weighting
			      ,c.protected
			      ,p.position
			      ,r.rar_name
			FROM   competition c
			      ,rar r
			      ,competitor a
			      ,position p
			".$rarSqlFrom."
			WHERE  c.competition_date >= ?
			AND    c.competition_date <= ?
			AND    p.competitor_id = a.competitor_id
			AND    p.competition_id = c.competition_id
			AND    p.position > 0
			AND    a.rar_id = r.rar_id
			".$rarSqlWhere."
			ORDER BY p.competitor_id
		";
		if ($useRar) {
			$query = $this->db->query($sql, array($startDate, $endDate, $rar));
		} else {
			$query = $this->db->query($sql, array($startDate, $endDate));
		}
		$players = $query->getResult();
		if (count($players) === 0) {
			return array(
				"rankings" => array(),
				"competitions" => $competitions,
				"minComps" => $minComps,
				"startDate" => $startDate,
				"endDate" => $endDate,
				"maxScore" => $maxScore
			);
		}
		$protectedScores = array();
		$unprotectedScores = array();
		$protectedComp = array();
		$unprotectedComp = array();
		$playerName = "N/A";
		foreach ($players as $player) {
			if ($player->competitor_id != $playerId) {
				if ($playerId != -1) {
					$rankings["Id".$playerId]["playerId"] = $playerId;
					$rankings["Id".$playerId]["playerName"] = $playerName;
					$rankings["Id".$playerId]["rarName"] = $rarName;
					$rankings["Id".$playerId]["playerData"] = 
						$this->calcRankings(
							$protectedScores, 
							$unprotectedScores, 
							$minComps, 
							$protectedComp, 
							$unprotectedComp, 
							$playerId, 
							$maxScore, 
							$calcType, 
							$startDate, 
							$endDate, 
							$useRar, 
							$rarSqlWhere,
							$rarSqlFrom,
								$rar
					);
				}
				$playerId = $player->competitor_id;
				//$num_protected = 0;
				//$num_unprotected = 0;
				//$protected_total = 0;
				//$unprotected_total = 0;
				$protectedScores = array();
				$unprotectedScores = array();
				$protectedComp = array();
				$unprotectedComp = array();
				$playerName = $player->first_name." ".$player->last_name;
				$rarName = $player->rar_name;
			}
			// get the ranking score for this competition
			$score = (($compNums[$player->competition_id] - $player->position) / ($compNums[$player->competition_id] - 1) * ($player->weighting - 1)) + 1;
			if ($player->protected == '1') {
				//$num_protected++;
				//$protected_total += $score;
				$protectedScores[$player->competition_id] = $score; 
				$protectedComp[$player->competition_id] = $player->competition_name;
			} else {
				//$num_unprotected++;
				//$unprotected_total += $score;
				$unprotectedScores[$player->competition_id] = $score;
				$unprotectedComp[$player->competition_id] = $player->competition_name;
			}
		}
		$rankings["Id".$playerId]["playerId"] = $playerId;
		$rankings["Id".$playerId]["playerName"] = $playerName;
		$rankings["Id".$playerId]["rarName"] = $rarName;
		$rankings["Id".$playerId]["playerData"] = 
			$this->calcRankings(
				$protectedScores, 
				$unprotectedScores, 
				$minComps, 
				$protectedComp, 
				$unprotectedComp, 
				$playerId, 
				$maxScore, 
				$calcType, 
				$startDate, 
				$endDate, 
				$useRar, 
				$rarSqlWhere,
				$rarSqlFrom,
					$rar
			);
		//arsort($rankings);
		//array_multisort($rankings["playerData"], 
		//	$rankings["playerData"]["ranking"], 
		//	SORT_DESC);
		// Sort the rankings array by score descending:
		//print_r($rankings);
		uasort($rankings, $compareRankings);
		//print_r($rankings);
		// we now create a mega return array of:
		// $returnData
		//   ["rankings"]
		//     ["Id".playerId]
		//       ["playerId"]
		//       ["playerName"]
		//       ["playerData"]
    	//         [ranking] => ranking value
	    //         [numComps] => number of competitions
	    //         [compData] => list of ranking scores for each competiton
	    //           [] => the comp number in order
	    //             [score]
	    //             [type] => protected or unprotected
	    //             [notes] => for a hover over or pop-up 
		//   ["competitions"] => a query results object
		//   ["minComps"]
		//   ["startDate"]
		//   ["endDate"]
		//   ["maxScore"]
		$returnData["rankings"] = $rankings;
		$returnData["competitions"] = $competitions;
		$returnData["minComps"] = $minComps;
		$returnData["startDate"] = $startDate;
		$returnData["endDate"] = $endDate;
		$returnData["maxScore"] = $maxScore;
		return $returnData;
    }
    
    // this function does the hard bit of calculating the ranking for a given player.
    // it will then return an array that contains the following elements:
    // [ranking] => ranking value
    // [numComps] => number of competitions
    // [compData] => list of ranking scores for each competiton
    //   [] => the comp number in order
    //    [score]
    //    [type] => protected or unprotected
    //    [notes] => for a hover over or pop-up 
    function calcRankings(
    	$protectedScores, 
    	$unprotectedScores, 
    	$minComps, 
    	$protectedComp, 
    	$unprotectedComp, 
    	$playerId, 
    	$maxScore, 
    	$calcType, 
    	$startDate, 
    	$endDate, 
    	$useRar, 
    	$rarSqlWhere,
    	$rarSqlFrom,
    	$rar
    	) {
		//was this player also an umpire at any point?
		// BUG FIX - only pick up umpire scores in the specific time period
		$sql = "
			SELECT c.competition_name
			      ,c.competition_id
			FROM   competition c
			      ,umpire u
			".$rarSqlFrom."
			WHERE  c.competition_id = u.competition_id
			  AND  u.competitor_id = ?
			  AND    c.competition_date >= ?
			  AND    c.competition_date <= ?
			  ".$rarSqlWhere
		;
		if ($useRar) {
			$query = $this->db->query($sql, array($playerId, $startDate, $endDate, $rar));
		} else {
			$query = $this->db->query($sql, array($playerId, $startDate, $endDate));
		}
		$umpire = $query->getResult();
		$comps = count($protectedScores) + count($unprotectedScores);
		if ($query->getNumRows() > 0) {
			$totalScore = 0;
			foreach ($protectedScores as $p) {
				$totalScore += $p;
			}
			foreach ($unprotectedScores as $u) {
				$totalScore += $u;
			}
			$avgScore = $totalScore/$comps;
			foreach ($umpire as $umpireComp) {
				if (isset($protectedScores[$umpireComp->competition_id])) {
					if ($protectedScores[$umpireComp->competition_id] < $avgScore) {
						$protectedComp[$umpireComp->competition_id] = $umpireComp->competition_name." (UMPIRE) replaces original score of ".$protectedScores[$umpireComp->competition_id];
						$protectedScores[$umpireComp->competition_id] = $avgScore;
					}
				} else if (isset($unprotectedScores[$umpireComp->competition_id])) {
					if ($unprotectedScores[$umpireComp->competition_id] < $avgScore) {
						$unprotectedComp[$umpireComp->competition_id] = $umpireComp->competition_name." (UMPIRE) replaces original score of ".$unprotectedScores[$umpireComp->competition_id];
						$unprotectedScores[$umpireComp->competition_id] = $avgScore;
					}
				} else {
					$unprotectedScores[$umpireComp->competition_id] = $avgScore;
					$unprotectedComp[$umpireComp->competition_id] = $umpireComp->competition_name." (UMPIRE)";
					// add 1 to $comps if we were an umpire and didn't play
					$comps++;
				}
			}
		}
		$compData = array();
		if (count($protectedScores) > $minComps) {
			arsort($protectedScores);
			$ranking = 0;
			reset($protectedScores);
			for ($a = 0; $a < $minComps; $a++) {
				$currKey = key($protectedScores);
				next($protectedScores);
				$ranking += $protectedScores[$currKey];
				$thisCompData = array(
					"score" => number_format($protectedScores[$currKey],2),
					"type" => "Protected/Used",
					"notes" => $protectedComp[$currKey]
				);
				$compData[] = $thisCompData;
			}
			for ($b = $minComps; $b < count($protectedScores); $b++) {
				$currKey = key($protectedScores);
				next($protectedScores);
				$thisCompData = array(
					"score" => number_format($protectedScores[$currKey],2),
					"type" => "Protected/Unused",
					"notes" => $protectedComp[$currKey]
				);
				$compData[] = $thisCompData;
			}
		} else if ((count($protectedScores) + count($unprotectedScores)) > $minComps) {
			$ranking = 0;
			arsort($protectedScores);
			reset($protectedScores);
			for ($a = 0; $a < count($protectedScores); $a++) {
				$currKey = key($protectedScores);
				next($protectedScores);
				$ranking += $protectedScores[$currKey];
				$thisCompData = array(
					"score" => number_format($protectedScores[$currKey],2),
					"type" => "Protected/Used",
					"notes" => $protectedComp[$currKey]
				);
				$compData[] = $thisCompData;
			}
			arsort($unprotectedScores);
			reset($unprotectedScores);
			if ($calcType == "bestof") {
				for ($b = 0; $b < ($minComps - count($protectedScores)); $b++) {
					$currKey = key($unprotectedScores);
					next($unprotectedScores);
					$ranking += $unprotectedScores[$currKey];
					$thisCompData = array(
						"score" => number_format($unprotectedScores[$currKey],2),
						"type" => "Unprotected/Used",
						"notes" => $unprotectedComp[$currKey]
					);
					$compData[] = $thisCompData;
				}
				for ($c = ($minComps - count($protectedScores)); $c < count($unprotectedScores); $c++) {
					$currKey = key($unprotectedScores);
					next($unprotectedScores);
					$thisCompData = array(
						"score" => number_format($unprotectedScores[$currKey],2),
						"type" => "Unprotected/Unused",
						"notes" => $unprotectedComp[$currKey]
					);
					$compData[] = $thisCompData;
				}
			} else if ($calcType == "averaged") {
				$unprotectedTotal = 0;
				for ($b = 0; $b < count($unprotectedScores); $b++) {
					$currKey = key($unprotectedScores);
					next($unprotectedScores);
					$unprotectedTotal += $unprotectedScores[$currKey];
					$thisCompData = array(
						"score" => number_format($unprotectedScores[$currKey],2),
						"type" => "Unprotected/Used",
						"notes" => $unprotectedComp[$currKey]
					);
					$compData[] = $thisCompData;
				}
				for ($z = 0; $z < $minComps - count($protectedScores); $z++) {
					$thisCompData = array(
						"score" => " averaged to ".number_format($unprotectedTotal / count($unprotectedScores),2),
						"type" => "Unprotected/Average",
						"notes" => "Average of unprotected scores"
					);
					$compData[] = $thisCompData;
				}
				$ranking += $unprotectedTotal / count($unprotectedScores) * ($minComps - count($protectedScores));
			}
		} else {
			$ranking = 0;
			arsort($protectedScores);
			reset($protectedScores);
			for ($a = 0; $a < count($protectedScores); $a++) {
				$currKey = key($protectedScores);
				next($protectedScores);
				$ranking += $protectedScores[$currKey];
				$thisCompData = array(
					"score" => number_format($protectedScores[$currKey],2),
					"type" => "Protected/Used",
					"notes" => $protectedComp[$currKey]
				);
				$compData[] = $thisCompData;
			}
			arsort($unprotectedScores);
			reset($unprotectedScores);
			for ($b = 0; $b < count($unprotectedScores); $b++) {
				$currKey = key($unprotectedScores);
				next($unprotectedScores);
				$ranking += $unprotectedScores[$currKey];
				$thisCompData = array(
					"score" => number_format($unprotectedScores[$currKey],2),
					"type" => "Unprotected/Used",
					"notes" => $unprotectedComp[$currKey]
				);
				$compData[] = $thisCompData;
			}
		}
		$ranking = $ranking / $maxScore * 100;
		$r_data['ranking'] = $ranking;
		$r_data['numComps'] = $comps;
		$r_data['compData'] = $compData;
		//$r_data[3] = $unprotectedScoreString;
		return $r_data;
	}
	
    
}