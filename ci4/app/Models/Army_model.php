<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;
class Army_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function getActiveArmies() {
        $sql = "
			SELECT a.army_id
			      ,a.name
			      ,a.book_number
			      ,a.list_number
			      ,a.start_year
			      ,a.end_year
			      ,s.system_name
			      ,a.version
			FROM armies a
			    ,system s
			    ,army_books ab
			WHERE a.system_id = s.system_id
			AND   a.book_id = ab.book_id
			AND   ab.status = 'A'
			ORDER BY a.name, s.system_name, a.version
		";
    	$query = $this->db->query($sql, array());
        return $query->result();
    }
        
    function getArmyStats($viewType, $monthsBack, $rankingMethod) {
    	if ($monthsBack == 12){
    		$dateRestriction = "AND DATEDIFF( CURDATE(), m.match_date) < 365";
    	} else {
    		$dateRestriction = "";
    	}
    	$orderBy = ""; //just declaring it for scoping
    	$rankingMethod = "none";//the default way of ranking armies
    	if ($viewType=="mostused"){
    		$orderBy = "num_matches DESC";
    		$rankingMethod = "num_matches";
    	} else {//the default
    		$orderBy = "army_book, army_list, a.system_id DESC";
    		//rb 18/8/08 Note that I put the DESC in the above to make DBM lists appear
    		//before DBMM lists so that DBMM Bk3 lists with a higher list number would appear
    		//in the same relative position as DBM/MM lists with the same list number
    	}
    	$sql = "
    			SELECT 	DISTINCT p.army_id AS army_id
    			, COUNT(m.match_id) AS num_matches
    			,a.book_number as army_book
    			,a.list_number as army_list
    			,s.system_name as rules_system_name
    			,a.version as list_version
    			,a.name as army_name
    			FROM   matches m
    			,match_participant mp
    			,position p
    			,armies a
    			,system s
    			WHERE  m.competition_id = p.competition_id
    			AND  m.match_id = mp.match_id
    			AND  mp.competitor_id = p.competitor_id
    			AND  a.army_id = p.army_id
    			AND  s.system_id = a.system_id
    			AND  m.match_id IS NOT NULL
    			$dateRestriction
    			GROUP BY p.army_id
    			ORDER BY $orderBy
    			";
    	//rb 25/8/08 Not sure if the "NOT NULL" above is really necessary
    	$query = $this->db->query($sql, array());
    	$results = $query->result();
   		$lastRankScore = 0; //rb just initialising, use this to track the current ranking level
   		$armyRank = 0;
   		$returnData = array();
    	foreach( $results AS $row ){
    		$armyRecord = array();
    		$armyRecord['armyID'] = $row->army_id;
    		$armyRecord['armyBook'] = $row->army_book;
    		$armyRecord['armyList'] = $row->army_list;
    		$armyRecord['armyName'] = $row->army_name;
    		$armyRecord['numMatches'] = $row->num_matches;
    		if ($rankingMethod == "num_matches"){
    			if ($row->num_matches != $lastRankScore) {
    				$armyRank++;
    				$lastRankScore = $row->num_matches;
    			}
    		}
   			$armyRecord['rulesSystemName'] = $row->rules_system_name;
   			$armyRecord['armyListVersion'] = $row->list_version;
   			$sql = "
   					SELECT DISTINCT	m.winner_score
   					,m.match_id
   					FROM   matches m
   					,match_participant mp
   					,position p
   					WHERE  m.competition_id = p.competition_id
   					AND  m.match_id = mp.match_id
   					AND  mp.competitor_id = p.competitor_id
   					AND  p.army_id = ?
   					AND  mp.side = 'W'
   					AND  (m.winner_score + m.loser_score) = 25
   					ORDER BY m.winner_score DESC
   			";
   			$query = $this->db->query($sql, array($armyRecord['armyID']));
   			$winner_stats = $query->result();
   			//Note (m.winner_score + m.loser_score) = 25 above is no good for non-DBMM
   			$sql = "
    			SELECT DISTINCT
    			m.loser_score
    			,m.match_id
    			FROM   matches m
    			,match_participant mp
    			,position p
    			WHERE  m.competition_id = p.competition_id
    			AND  m.match_id = mp.match_id
    			AND  mp.competitor_id = p.competitor_id
    			AND  p.army_id = ?
    			AND  mp.side = 'L'
    			AND  (m.winner_score + m.loser_score) = 25
    			ORDER BY m.loser_score DESC
   			";
   			$query = $this->db->query($sql, array($armyRecord['armyID']));
   			$loser_stats = $query->result();
   			//Note (m.winner_score + m.loser_score) = 25 above is no good for non-DBMM
   			// get winner stats for the northern league and other 32 point systems
   			$sql = "
    			SELECT DISTINCT	(32 - m.loser_score) as winner_score
    			,m.match_id
    			FROM   matches m
    			,match_participant mp
    			,position p
    			WHERE  m.competition_id = p.competition_id
    			AND  m.match_id = mp.match_id
    			AND  mp.competitor_id = p.competitor_id
    			AND  p.army_id = ?
    			AND  mp.side = 'W'
    			AND  (m.winner_score + m.loser_score) >= 32
    			ORDER BY m.winner_score DESC
    			";
   			$query = $this->db->query($sql, array($armyRecord['armyID']));
   			$nl_winner_stats = $query->result();
   			//Note the northern league awards bonus points to the winner so we need to deduce the
   			// winner's score from the loser's score
   			// get loser stats for the northern league and other 32 point systems
   			$sql = "
    			SELECT DISTINCT
    			m.loser_score
    			,m.match_id
    			FROM   matches m
    			,match_participant mp
    			,position p
    			WHERE  m.competition_id = p.competition_id
    			AND  m.match_id = mp.match_id
    			AND  mp.competitor_id = p.competitor_id
    			AND  p.army_id = ?
    			AND  mp.side = 'L'
    			AND  (m.winner_score + m.loser_score) >= 32
    			ORDER BY m.loser_score DESC
    		";
   			$query = $this->db->query($sql, array($armyRecord['armyID']));
   			$nl_loser_stats = $query->result();
   			
   			$a = 0;
   			unset($match_stats);
   			$won_games_count = 0;
   			$drawn_games_count = 0;
   			$lost_games_count = 0;
 			if ($winner_stats) {
   				foreach ($winner_stats as $winner_stat) {
   					$match_stats[$a] = $winner_stat->winner_score;
   					if ($winner_stat->winner_score >= 16) {
   						$won_games_count++;
   					} else {
   						$drawn_games_count++;
   					}
    				$a++;
    				}
			}
			if ($loser_stats) {
  				foreach ($loser_stats as $loser_stat) {
    					$match_stats[$a] = $loser_stat->loser_score;
    					if ($loser_stat->loser_score <= 10) {
    						$lost_games_count++;
    					} else {
    						$drawn_games_count++;
    					}
   					$a++;
  				}
			}
    	
			if ($nl_winner_stats) {
   				foreach ($nl_winner_stats as $winner_stat) {
    				$match_stats[$a] = $this->convert32to25($winner_stat->winner_score);
    				$a++;
   				}
			}
			if ($nl_loser_stats) {
				foreach ($nl_loser_stats as $loser_stat) {
   					$match_stats[$a] = $this->convert32to25($loser_stat->loser_score);
					$a++;
				}
   			}
			if ($a) {
				$total_score = 0;
				foreach ($match_stats as $match_stat) {
					$total_score += $match_stat;
	    		}
				$armyRecord['avgScore'] = $total_score / $a;
				$armyRecord['avgScoreHover'] = "Based on ".$a." match results.";
				//$armyRecord['percentWins'] = round((count($winner_stats) / $a)*100);
				//$armyRecord['percentLosses'] = round((count($loser_stats) / $a)*100);
				//$armyRecord['percentDraws'] = 0;
				//rb 15/8/08 need some more sophisticated division of WDL in the future
				$armyRecord['percentWins'] = round(($won_games_count / $a)*100);
				$armyRecord['percentLosses'] = round(($lost_games_count / $a)*100);
				$armyRecord['percentDraws'] = round(($drawn_games_count / $a)*100);
    	
				//now calc top dog handicap
				$armyRecord['topDog'] = $armyRecord['avgScore'];
				if ($a < 8)
					$armyRecord['topDog'] = $armyRecord['topDog'] * 0.25;
				else if ($a < 16)
					$armyRecord['topDog'] = $armyRecord['topDog'] * 0.5;
				else if ($a < 24)
					$armyRecord['topDog'] = $armyRecord['topDog'] * 0.75;
				//note 24-99 games do nothing
				else {//note doubled the 0-10 top dog penalties below for 0-25 scoring
					$armyRecord['topDog'] = $armyRecord['topDog'] + 2*(floor($a/100) - 1);
		    	}
    	
    		} else {
    			$armyRecord['avgScore'] = "N/A";
 		   		$armyRecord['avgScoreHover'] = "";
    			$armyRecord['topDog'] = 0;
    			$armyRecord['percentWins'] = "N/A";
    			$armyRecord['percentDraws'] = "N/A";
    			$armyRecord['percentLosses'] = "N/A";
    		}
    		if ($viewType == "firstuse"){
    			$sql = "
    				SELECT min(cmp.competition_date) as date
    						, m.match_id as	match_id
    						, m.competition_id as comp
    						, cmp.competition_name as comp_name
    						, m.round as round
    						, c.first_name as first_name
    						, c.last_name as last_name
    						FROM   matches m
    						, position p
    						, competitor c
    						, competition cmp
    						WHERE  p.competition_id = m.competition_id
    						AND cmp.competition_id = p.competition_id
    						AND p.competitor_id = c.competitor_id
    						AND p.army_id = ?
    						GROUP BY army_id
				";
    			$query = $this->db->query($sql, array($army_id));
    			$first_use_stats = $query->result();
    			//rb 2/9/08 No explicit handling for multiple first use players, depending on DB
				// to just give us one back
				if ($first_use_stats){
					$armyRecord['firstDate'] = $first_use_stats->date;
					$armyRecord['firstMatchID'] = $first_use_stats->match_id;
					$armyRecord['firstComp'] = $first_use_stats->comp;
					$armyRecord['firstCompName'] = $first_use_stats->comp_name;
			   		$armyRecord['firstRound'] = $first_use_stats->round;
    				$armyRecord['firstFName'] = $first_use_stats->first_name;
   			 		$armyRecord['firstLName'] = $first_use_stats->last_name;
    			} else {
    				$armyRecord['firstDate'] = "N/A";
    				$armyRecord['firstMatchID'] = -1;
    				$armyRecord['firstComp'] = "N/A";
    				$armyRecord['firstRound'] = "N/A";
    				$armyRecord['firstFName'] = "N/A";
    				$armyRecord['firstLName'] = "N/A";
    			}
    		}
    		$returnData[] = $armyRecord;
    	} // end of main foreach
    	return $returnData;
    } // end of function
    
    function getArmyMatches($id) {
    	$returnData = array();
    	$armyName = $this->armyid2name($id);
    	$returnData['armyName'] = $armyName;
   		$sql = "
			SELECT c.competition_date
			,m.round
			,c.competition_name
			,c.competition_id
			,m.winner_score
			,m.loser_score
			,m.match_id
			FROM   position p
			,matches m
			,competition c
			WHERE  p.army_id = ?
			AND  p.competition_id = c.competition_id
			AND  m.competition_id = c.competition_id
			AND  p.competitor_id in (
				SELECT mp.competitor_id
				FROM   match_participant mp
				WHERE  mp.match_id = m.match_id
    		)
			ORDER BY c.competition_date, m.round
			";
   		$query = $this->db->query($sql, array($id));
   		$data = $query->result();
    	$matchDetails = array();
    	$matchKey = array();
    	$matchKey['winnerName'] = "";
    	$matchKey['winnerScore'] = "";
    	$matchKey['loserScore'] = "";
    	$matchKey['loserName'] = "";
    	foreach( $data as $row ){
    		$matchId = $row->match_id;
    		$sql = "
    			SELECT pl.first_name
    			,pl.last_name
    			,pl.competitor_id
    			FROM   match_participant mp
    			,competitor pl
    			WHERE  mp.match_id = ?
    			AND  mp.side = 'W'
    			AND  mp.competitor_id = pl.competitor_id
    			";
    		$query = $this->db->query($sql, array($matchId));
    		$winners = $query->result();
   		$sql = "
    			SELECT pl.first_name
    			,pl.last_name
    			,pl.competitor_id
    			FROM   match_participant mp
    			,competitor pl
    			WHERE  mp.match_id = ?
    			AND  mp.side = 'L'
    			AND  mp.competitor_id = pl.competitor_id
    			";
	   	$query = $this->db->query($sql, array($matchId));
	   	$losers = $query->result();
    		//************************************************************
    		// This works out if it was the winner or the loser
    		// who was using the army we are interested in.
    		// It gets confused if both players were using the same army
    		//************************************************************
    		$armyUsedByWinner = 0;
    		if (count($winners)) {
    			foreach ($winners as $winner) {
    				$playerId = $winner->competitor_id;
    				$sql = "
		    			SELECT count(m.match_id) as  num_matches
		    			FROM   matches m
		    			,position p
		    			WHERE  m.match_id = ?
		    			AND  m.competition_id = p.competition_id
		    			AND  p.competitor_id = ?
		    			AND  p.army_id = ?
    				";
    				$query = $this->db->query($sql, array($matchId, $playerId, $id));
    				if ($query->row()->num_matches > 0) {
		    			$armyUsedByWinner = 1;
    				}
    			}
    		}
   		if ($matchKey['winnerName'] == $winners[0]->first_name.$winners[0]->last_name
    			&& $matchKey['winnerScore'] == $row->winner_score
    			&& $matchKey['loserScore'] == $row->loser_score
    			&& $matchKey['loserName'] == $losers[0]->first_name.$losers[0]->last_name) {
    			//***********************************************************
    			// This is exactly the same as the previous row, so it is
    			// probably a pair of people with the same army playing each
    			// other - swap the winning status around
    			//***********************************************************
    			if ($armyUsedByWinner) {
    				$armyUsedByWinner = 0;
    			} else {
    				$armyUsedByWinner = 1;
    			}
   		}
   		$matchKey['winnerName'] = $winners[0]->first_name.$winners[0]->last_name;
   		$matchKey['winnerScore'] = $row->winner_score;
   		$matchKey['loserScore'] = $row->loser_score;
   		$matchKey['loserName'] = $losers[0]->first_name.$losers[0]->last_name;
   		$matchDetails['compDate'] = $row->competition_date;
    		$matchDetails['compID'] = $row->competition_id;
    		$matchDetails['compName'] = $row->competition_name;
    		$matchDetails['compRound'] = $row->round;
    		$matchDetails['winners'] = array();
    		$matchDetails['losers'] = array();
    		if ($armyUsedByWinner) {
   			$a = 0;
    			foreach ($winners as $player) {
    				//$matchDetails['winners'][$a] = array();
    				$matchDetails['winners'][$a]['winnerName'] = $player->first_name." ".$player->last_name;
    				$matchDetails['winners'][$a]['winnerID'] = $player->competitor_id;
    				$a++;
    			}
    			$matchDetails['matchScore'] = $row->winner_score." - ".$row->loser_score;
    			$a = 0;
    			foreach ($losers as $player) {
    				//$matchDetails['losers'][$a] = array();
    				$matchDetails['losers'][$a]['loserName'] = $player->first_name." ".$player->last_name;
    				$matchDetails['losers'][$a]['loserID'] = $player->competitor_id;
    				$a++;
    			}
    			$sql = "
	    			SELECT DISTINCT p.army_id
	    			,a.name
	    			FROM  position p
	    			,matches m
	    			, armies a
	    				WHERE  p.competitor_id = ?
	    				AND  p.competition_id = m.competition_id
	    				AND  m.match_id = ?
	    				AND  p.army_id = a.army_id
	    				";
    			$query = $this->db->query($sql, array($matchDetails['losers'][0]['loserID'], $matchId));
    			$otherArmy = $query->row();
    		} else {
    			$a = 0;
    			foreach ($losers as $player) {
    				//$matchDetails['winners'][$a] = array();
    				$matchDetails['winners'][$a]['winnerName'] = $player->first_name." ".$player->last_name;
    				$matchDetails['winners'][$a]['winnerID'] = $player->competitor_id;
    				$a++;
    			}
    			$matchDetails['matchScore'] = $row->loser_score." - ".$row->winner_score;
				$a = 0;
    			foreach ($winners as $player) {
    				//$matchDetails['losers'][$a] = array();
    				$matchDetails['losers'][$a]['loserName'] = $player->first_name." ".$player->last_name;
    				$matchDetails['losers'][$a]['loserID'] = $player->competitor_id;
    				$a++;
    			}
    			$sql = "
	    			SELECT DISTINCT p.army_id
	    			,a.name
	    			FROM  position p
	    			,matches m
	    			, armies a
	    				WHERE  p.competitor_id = ?
	    				AND  p.competition_id = m.competition_id
	    				AND  m.match_id = ?
	    				AND  p.army_id = a.army_id
	    				";
    			$query = $this->db->query($sql, array($matchDetails['losers'][0]['loserID'], $matchId));
    			$otherArmy = $query->row();
   			}
   			if (isset($otherArmy->name)) {
   				$matchDetails['otherArmyName'] = $otherArmy->name;
   				$matchDetails['otherArmyID'] = $otherArmy->army_id;
   			} else {
   				$matchDetails['otherArmyName'] = "N/A";
   				$matchDetails['otherArmyID'] = -1;
   			}
   			$returnData['matchDetails'][] = $matchDetails;
    	}
    	return $returnData;
    }
    	
    function armyid2name($id) {
    	$sql = "
    		SELECT a.name
    		FROM armies a
    		WHERE a.army_id = ?
    	";
    	$query = $this->db->query($sql, array($id));
    	$armyName = $query->row()->name;
    	return $armyName;
	}
	
	//***************************************************************************************
	//function to convert 32 point scores to 25 point scores using the Northern League system
	//***************************************************************************************
	function convert32to25($score32) {
		$score25 = 0;
	
		switch ($score32) {
			case 32:
				$score25 = 25;
				break;
			case 31:
				$score25 = 24;
				break;
			case 30:
				$score25 = 23;
				break;
			case 29:
				$score25 = 23;
				break;
			case 28:
				$score25 = 22;
				break;
			case 27:
				$score25 = 21;
				break;
			case 26:
				$score25 = 20;
				break;
			case 25:
				$score25 = 19;
				break;
			case 24:
				$score25 = 19;
				break;
			case 23:
				$score25 = 18;
				break;
			case 22:
				$score25 = 17;
				break;
			case 21:
				$score25 = 16;
				break;
			case 20:
				$score25 = 16;
				break;
			case 19:
				$score25 = 15;
				break;
			case 18:
				$score25 = 14;
				break;
			case 17:
				$score25 = 13;
				break;
			case 16:
				$score25 = 13;
				break;
			case 15:
				$score25 = 12;
				break;
			case 14:
				$score25 = 12;
				break;
			case 13:
				$score25 = 12;
				break;
			default:
				$score25 = $score32;
		}
		return $score25;
	}
	
}
?>
