<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;


class Kudos_rankings_model extends CI_Model {

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
		function compareRankings($a, $b) {
			if ($a["playerData"]["ranking"] > $b["playerData"]["ranking"]) {
				//echo "<div>".$a["playerData"]["ranking"]." > ".$b["playerData"]["ranking"]."</div>";
				return -1;
			} elseif ($a["playerData"]["ranking"] == $b["playerData"]["ranking"]) {
				return 0;
			} else {
				return 1;
			}
		}
			
		date_default_timezone_set('UTC');
		// Get the data for the rankings
		// Check if the RAR is set and is valid
		if (isset($rar) && is_numeric($rar) && $rar >= 0) {
			$useRar = TRUE;
			$rarSqlWhere = " AND c.rar_id = ?";
		} else {
			$useRar = FALSE;
			$rarSqlWhere = "";
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
		// Hard code for the moment
		/*
		$sql = "
		SELECT IFNULL(comps, 6) as comps
		,IFNULL(max_score, 600) as max_score
		,IFNULL(calc_type, 'B') as calc_type
		FROM   ranking_settings
		WHERE  rar_id = ?
		AND  year   = ?
		";
		$query = $this->db->query($sql, array($year, $rar));
		$config = $query->row();
		if ($query->num_rows() > 0) {
			$minComps = $config->comps;
			$maxScore = $config->max_score;
			if ($config->calc_type == "A") {
				$calcType = "averaged";
			} else {
				$calcType = "bestof";
			}
		} else {
			$minComps = 6;
			$maxScore = 600;
			$calcType = "bestof";
		}
		*/
		$minComps = 6;
		$maxScore = 600;
		$calcType = "bestof";
		if (!isset($minComps) || !isset($maxScore) || !isset($calcType)) {
			$minComps = 6;
			$maxScore = 600;
			$calcType = "bestof";
		}

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
			WHERE  p.competition_id = c.competition_id
			AND    c.competition_date <= ?
			AND    c.competition_date >= ?
			".$rarSqlWhere."
			GROUP BY p.competition_id, competition_name, type
			ORDER BY c.competition_date
		";
		if ($useRar) {
			$query = $this->db->query($sql, array($startDate, $endDate, $rar));
		} else {
			$query = $this->db->query($sql, array($startDate, $endDate));
		}
		$competitions = $query->result();
		$totalRating = array();
		$rounds = array();
		foreach ($competitions as $ckey => $c) {
			// we need to find the total glicko rating for all the competitors at the start of the comp
			$comp_id = $c->competition_id;
			$sql =
				"SELECT  IFNULL(g.rating, 2000) AS glicko_rating
				FROM competition c
				,position s
				LEFT JOIN glicko_rating g ON g.competitor_id = s.competitor_id
				AND g.rating_date = (SELECT MAX(rating_date)
				FROM glicko_rating g2
				WHERE g2.competitor_id = g.competitor_id
				AND g2.match_date < c.competition_date
				)
				WHERE s.competition_id = ?
				AND   s.competition_id = c.competition_id
			";
			$query = $this->db->query($sql, array($comp_id));
			$ratings = $query->get_result();
			$tr = 0;
			foreach ($ratings as $rating) {
				$tr += $rating->glicko_rating;
			}
			$totalRating[$c->competition_id] = $tr;
			$sql = "SELECT IFNULL(max(round),4) as rounds FROM matches WHERE competition_id = ?";
			$query = $this->db->query($sql, array($comp_id));
			$rounds[$c->competition_id] = $query->row()->rounds;
			if ($c->worst_place < 1) {
				unset($competitions[$ckey]);
			} else {
				if ($c->type == "Singles") {
					$comp_nums[$c->competition_id] = $c->worst_place;
				} else if ($c->type == "Doubles") {
					$comp_nums[$c->competition_id] = $c->competitors / 2;
				} else {
					$comp_nums[$c->competition_id] = $c->worst_place;
				}
			}
		}
		
		$playerId = -1;
		$playerName = "";
		$sql = "
			SELECT a.competitor_id
			,a.first_name
			,a.last_name
			,c.competition_id
			,c.competition_name
			,c.weighting
			,c.protected
			,p.position
			,IFNULL(gr.rating, 2000) AS rating
			FROM   competition c
			,competitor a
			,position p
			LEFT JOIN glicko_rating gr ON gr.competitor_id = p.competitor_id
			AND gr.rating_date = (SELECT MAX(rating_date)
			FROM glicko_rating g2
			WHERE g2.competitor_id = gr.competitor_id
			AND g2.match_date < c.competition_date
			)
			WHERE  c.competition_date <= ?
			AND    c.competition_date >= ?
			AND    p.competitor_id = a.competitor_id
			AND    p.competition_id = c.competition_id
			".$rarSqlWhere."
			ORDER BY p.competitor_id
		";
		if ($useRar) {
			$query = $this->db->query($sql, array($startDate, $endDate, $rar));
		} else {
			$query = $this->db->query($sql, array($startDate, $endDate));
		}
		$players = $query->result();
		$unprotectedScores = array();
		$unprotectedComp = array();
		foreach ($players as $player) {
			if ($player->competitor_id != $playerId) {
				// we have finished with the previous player
				// if we had some data, calculate the rating
				if ($playerId != -1) {
					$rankings["Id".$playerId]["playerId"] = $playerId;
					$rankings["Id".$playerId]["playerName"] = $playerName;
					$rankings["Id".$playerId]["playerData"] = 
						 $this->calcRankings($db, $unprotectedScores, $minComps, $unprotectedComp, $playerId, $calcType, $startDate, $endDate, $rarSqlWhere, $useRar);
				}
				// reset the player data
				$playerId = $player->competitor_id;
				$unprotectedScores = array();
				$unprotectedComp = array();
				$playerName = $player->first_name." ".$player->last_name;
			}
			// get the ranking score for this competition
			$score = (($comp_nums[$player->competition_id] - $player->position) / ($comp_nums[$player->competition_id] - 1) * (sqrt(($totalRating[$player->competition_id] - $player->rating) * $rounds[$player->competition_id]) - 1)) + 1;
			$unprotectedScores[$player->competition_id] = $score;
			$unprotectedComp[$player->competition_id] = $player->competition_name." (Position: ".$player->position.")";
		}
		$unprotectedScoreString = "";
		$rankings["Id".$playerId]["playerId"] = $playerId;
		$rankings["Id".$playerId]["playerName"] = $playerName;
		$rankings["Id".$playerId]["playerData"] = 
			$this->calcRankings($db, $unprotectedScores, $minComps, $unprotectedComp, $playerId, $calcType, $startDate, $endDate, $rarSqlWhere, $useRar);
		arsort($rankings);
		$returnData["rankings"] = $rankings;
		$returnData["competitions"] = $competitions;
		$returnData["totalRating"] = $totalRating;
		$returnData["rounds"] = $rounds;
		$returnData["minComps"] = $minComps;
		$returnData["startDate"] = $startDate;
		$returnData["endDate"] = $endDate;
		return $returnData;
		
	}

	function calcRankings(&$db, $unprotectedScores, $min_comps, $unprotectedComp, $playerId, $calc_type, $startDate, $endDate, $rarSqlWhere, $useRar) {
		//was this player also an umpire at any point?
		// BUG FIX - only pick up umpire scores in the specific time period
		$sql = "
			SELECT c.competition_name
			      ,c.competition_id
			FROM   competition c
			      ,umpire u
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
		$umpire = $query->result();
		$comps = count($unprotectedScores);
		$compData = array();
		if ($query->num_rows() > 0) {
			$totalScore = 0;
			foreach ($unprotectedScores as $u) {
				$totalScore += $u;
			}
			$avgScore = $totalScore/$comps;
			foreach ($umpire as $umpireComp) {
				if (!is_null($unprotectedScores[$umpireComp->competition_id])) {
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
		$unprotectedScoreString = "";
		if  (count($unprotectedScores) > $min_comps) {
			$ranking = 0;
			arsort($unprotectedScores);
			reset($unprotectedScores);
			if ($calc_type == "bestof") {
				for ($b = 0; $b < ($min_comps); $b++) {
					$currKey = key($unprotectedScores);
					next($unprotectedScores);
					$ranking += $unprotectedScores[$currKey];
					$unprotectedScoreString[$currKey] .= "<span class=\"green helpy\" title=\"".$unprotectedComp[$currKey]."\">".number_format($unprotectedScores[$currKey],2)." </span>";
				}
				for ($c = $min_comps; $c < count($unprotectedScores); $c++) {
					$currKey = key($unprotectedScores);
					next($unprotectedScores);
					$unprotectedScoreString[$currKey] .= "<span class=\"red helpy\" title=\"".$unprotectedComp[$currKey]."\">".number_format($unprotectedScores[$currKey],2)." </span>";
				}
				$thisCompData = array(
					"score" => number_format($unprotectedScores[$currKey],2),
					"type" => "Unprotected/Used",
					"notes" => $unprotectedComp[$currKey]
				);
				$compData[] = $thisCompData;
			} else if ($calc_type == "averaged") {
				$unprotectedTotal = 0;
				for ($b = 0; $b < count($unprotectedScores); $b++) {
					$currKey = key($unprotectedScores);
					next($unprotectedScores);
					$unprotectedTotal += $unprotectedScores[$currKey];
					$unprotectedScoreString[$currKey] .= "<span class=\"blue helpy\" title=\"".$unprotectedComp[$currKey]."\">".number_format($unprotectedScores[$currKey],2)." </span>";
				}
				$unprotectedScoreString .= "<span> averaged to </span>";
				for ($z = 0; $z < $min_comps; $z++) {
					$unprotectedScoreString[$currKey] .= "<span class=\"green helpy\" title=\"Average of unprotected scores\">".number_format($unprotectedTotal / count($unprotectedScores),2)." </span>";
				}
				$ranking += $unprotectedTotal / count($unprotectedScores) * ($min_comps);
				$thisCompData = array(
					"score" => number_format($unprotectedScores[$currKey],2),
					"type" => "Unprotected/Used",
					"notes" => $unprotectedComp[$currKey]
				);
				$compData[] = $thisCompData;
			}
		} else {
			$ranking = 0;
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
				//$unprotectedScoreString[$currKey] .= "<span class=\"green helpy\" title=\"".$unprotectedComp[$currKey]."\">".number_format($unprotectedScores[$currKey],2)." </span>";
			}
		}
		$ranking = $ranking / $min_comps;
		$r_data['ranking'] = $ranking;
		$r_data['numComps'] = $comps;
		$r_data['compData'] = $compData;
		$r_data[2] = $unprotectedScoreString;
		return $r_data;
	}
	

}