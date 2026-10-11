<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;
class Glicko_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function getPlayerHistory($id) {
    	$returnData = array();
    	// get this player's details
	$sql = "
		SELECT c.first_name
		,c.last_name
		,gs.starting_rating
		,gs.starting_reliability
		FROM   competitor c
		,glicko_settings gs
		WHERE  c.competitor_id = ?
	";
	$query = $this->db->query($sql, array($id));
	$const = $query->row();
	$returnData['playerName'] = $const->first_name." ".$const->last_name;
	// get all of his matches
	$sql = "
		SELECT gr.rating
		,gr.reliability
		,m.match_id
		,c.competition_date
		,m.round
		,c.competition_name
		,c.competition_id
		,m.winner_score
		,m.loser_score
		FROM   glicko_rating gr
		,matches m
		,competition c
		WHERE  gr.competitor_id = ?
		AND  gr.match_id = m.match_id
		AND  gr.status = 'A'
		AND  m.competition_id = c.competition_id
		ORDER BY rating_date DESC
	";
	$query = $this->db->query($sql, array($id));
	$data = $query->result();
		// iterate through each match
		foreach ($data as $rating) {
			$matchData = array();
			$matchData['date'] = $rating->competition_date;
			$matchData['competitionID'] = $rating->competition_id;
			$matchData['competitionName'] = $rating->competition_name;
			$matchData['round'] = $rating->round;
			$matchData['rating'] = $rating->rating;
			$matchData['reliability'] = $rating->reliability;
			$matchId = $rating->match_id;
			// find out whether he won or lost the match
			$sql = "
				SELECT mp.side
				FROM   match_participant mp
				WHERE  mp.match_id = ?
				AND  mp.competitor_id = ?
				";
			$query = $this->db->query($sql, array($matchId, $id));
			$side = $query->row()->side;
			// assign the scores in order depending on whether he won or lost
			if ($side == 'W') {
				$matchData['myScore'] = $rating->winner_score;
				$matchData['yourScore'] = $rating->loser_score;
			} else {
				$matchData['myScore'] = $rating->loser_score;
				$matchData['yourScore'] = $rating->winner_score;
			}
			// get the details of the opponents - there might be two if its
			// a doubles match
			$sql = "
				SELECT c.first_name
				,c.last_name
				,c.competitor_id
				,gr.rating
				,gr.reliability
				,gr.rating_date
				FROM   match_participant mp
				,competitor c
				,glicko_rating gr
				WHERE  mp.match_id = ?
				AND  mp.side != ?
				AND  c.competitor_id = mp.competitor_id
				AND  c.competitor_id = gr.competitor_id
				AND  gr.status = 'A'
				AND  gr.match_id = mp.match_id
			";
			$query = $this->db->query($sql, array($matchId, $side));
			$opponents = $query->result();
			$matchData['numOpponents'] = $query->num_rows();
			if ($matchData['numOpponents'] < 1) {
				// set it to 1 because we use this to do the rowspanning in the view
				// obviously this should never happen though
				$matchData['numOpponents'] == 1;
				// create a dummy opponents record as well to avoid breaking the view
				$matchData['opponents'][0]['playerName'] = "Cannot be found";
				$matchData['opponents'][0]['ID'] = -1;
				$matchData['opponents'][0]['rating'] = "N/A";
				$matchData['opponents'][0]['reliability'] = 0;
				$matchData['opponents'][0]['previousRating'] = "N/A";
				$matchData['opponents'][0]['previousReliability'] = "N/A";
			}
			// iterate through the opponents
			foreach ($opponents as $opponent) {
				// get the previous glicko rating for that opponent so we
				// can show the comparison
				$sql = "SELECT IFNULL(gr1.rating,2000) as rating
					,IFNULL(gr1.reliability,300) as reliability
					FROM   glicko_rating gr1
					WHERE  gr1.rating_date = (SELECT MAX(gr2.rating_date)
					FROM   glicko_rating gr2
					WHERE  gr1.competitor_id = gr2.competitor_id
					AND    gr2.rating_date   < ?
					)
					AND gr1.competitor_id = ?
				";
				$query = $this->db->query($sql, array($opponent->rating_date, $opponent->competitor_id));
				$oppPrev = $query->row();
				$oppoData = array();
				$oppoData['playerName'] = $opponent->first_name." ".$opponent->last_name;
				$oppoData['ID'] = $opponent->competitor_id;
				$oppoData['rating'] = $opponent->rating;
				$oppoData['reliability'] = $opponent->reliability;
				if ($query->num_rows() > 0) {
					$oppoData['previousRating'] = $oppPrev->rating;
					$oppoData['previousReliability'] = $oppPrev->reliability;
				} else {
					$oppoData['previousRating'] = "N/A";
					$oppoData['previousReliability'] = "N/A";
				}
				$matchData['opponents'][] = $oppoData;
			}
			$returnData['record'][$rating->competition_date.$rating->round] = $matchData;			
		}
		$returnData['ID'] = $id;
		return $returnData;
    }
    
    function resetMatchesGlickoFlag() {
    	$updateValue = array('glicko_processed' => 0);
	$this->db->update('matches', $updateValue); 
    }
    
    function clearGlickoRatingTable() {
	$this->db->empty_table('glicko_rating');
    }
    
    function getMatches() {
   	 $sql = "
                  SELECT m.match_id
                        ,c.competition_date
                        ,m.round
                        ,m.winner_score
                        ,m.loser_score
                  FROM   matches m
                  		,competition c
                  WHERE  m.glicko_eligible = 1
                    AND  m.glicko_processed = 0
                    AND  m.competition_id = c.competition_id
                  ORDER BY c.competition_date
                          ,m.round
                          ";
	$query = $this->db->query($sql, array());
	return $query->result();
    }
    
	function processMatch($match, $p, $q) {
		//echo "In processMatch.\n";
		$match_id = $match["match_id"];
		$winner_score = $match["winner_score"];
		$loser_score = $match["loser_score"];
		$rating_date = $match["match_date"];
		$match_date = $match["date"];
		$haveWinningSide = 0;
		$haveLosingSide = 0;
		// clear down all the arrays that hold winner and loser data
		$winner_idx = 0;
		$loser_idx = 0;
		$winner_rating = array();
		$winner_reliability = array();
		$winner_id = array();
		$winner_name = array();
		$loser_rating = array();
		$loser_reliability = array();
		$loser_id = array();
		$loser_name = array();
		//echo "Breakpoint 1.\n";
		//***************************************************************
		// we are using 2000 as the default rating for an unrated player
		// and 999.00 as the default reliability for an unrated player
		//***************************************************************
		
		$sql = "
                  SELECT mp.competitor_id
                        ,c.first_name
                        ,c.last_name
                        ,mp.side
                        ,IFNULL(gr.rating, 2000) as rating
                        ,IFNULL(gr.reliability, 300.00) as reliability
                        ,IFNULL(gr.rating_date, 0) as rating_date
                  FROM   competitor c
                        ,match_participant mp
                  LEFT OUTER JOIN glicko_rating gr
                     ON  mp.competitor_id = gr.competitor_id
                    AND  gr.rating_date = 
                       (SELECT MAX(gr2.rating_date)
                        FROM   glicko_rating gr2
                        WHERE  rating_date <= ?
                        AND    gr2.competitor_id = gr.competitor_id)
                  WHERE  c.competitor_id = mp.competitor_id
                    AND  mp.match_id = ?
                  ORDER BY side DESC
                  ";
		$query = $this->db->query($sql, array($rating_date, $match_id));
		$players = $query->result();
		//echo "Breakpoint 2.\n";
		
		if ($query->num_rows()) {
			foreach ($players as $player) {
				// at the moment, this is assuming that we don't have more than one player per side,
				// which is convenient but untrue
				echo $player->first_name." ".$player->last_name."  ";
				if ($player->side == "W") {
					echo "WINNER ".$winner_score." ".$player->rating." ".$player->reliability."\n";
					$winner_rating[$winner_idx] = $player->rating;
					$winner_reliability[$winner_idx] = $player->reliability;
					$winner_rating_date[$winner_idx] = $player->rating_date;
					$winner_id[$winner_idx] = $player->competitor_id;
					$winner_name[$winner_idx] = $player->first_name." ".$player->last_name;
					if ($winner_id[$winner_idx] == -1) {
						// this is a BYE and shouldn't be counted
						$haveWinningSide = 0;
					} else {
						$haveWinningSide++;
					}
					$winner_idx++;
				} else {
					echo "LOSER ".$loser_score." ".$player->rating." ".$player->reliability."\n";
					$loser_rating[$loser_idx] = $player->rating;
					$loser_reliability[$loser_idx] = $player->reliability;
					$loser_rating_date[$loser_idx] = $player->rating_date;
					$loser_id[$loser_idx] = $player->competitor_id;
					$loser_name[$loser_idx] = $player->first_name." ".$player->last_name;
					if ($loser_id[$loser_idx] == -1) {
						// this is a BYE and shouldn't be counted
						$haveLosingSide = 0;
					} else {
						$haveLosingSide++;
					}
					$loser_idx++;
				}
			}
		//echo "Breakpoint 3.\n";
			if ($haveWinningSide && $haveLosingSide) {
				// echo "Winner rating: ".$winner_rating.", reliability ".$winner_reliability;
				// echo ": Loser rating ".$loser_rating.", reliability ".$loser_reliability."\n";
				// ************************************************
				// Calculate L, the difference in rating 
				// between the winner and loser
				// This is capped at +/- 450 to prevent wild swings
				// This capping was introduced by DY and 
				// has been commented out on 		
				// advice from LG
				// *************************************************
				// what do we do if there is more than one player on a side?
				// lets assume that we take the average 
				// of their rating and reliability:
				$overall_winner_rating = 0;
				$overall_winner_reliability = 0;
				$overall_loser_rating = 0;
				$overall_loser_reliability = 0;
				foreach ($winner_rating as $wra) {
					$overall_winner_rating += $wra;
				}
				$average_winner_rating = $overall_winner_rating / $haveWinningSide;
				foreach ($loser_rating as $lra) {
					$overall_loser_rating += $lra;
				}
				$average_loser_rating = $overall_loser_rating / $haveLosingSide;
		//echo "Breakpoint 4.\n";
				// we need to remember to degrade the 
				// reliability for each player
				// based on the amount of time since they last played
				// the formula is R(t) = sqrt(R(0)^2+t*V^2)
				// t will be measured in days
				// V will be 20 (initially)
				// THIS BIT DOESN'T WORK>>>>
				/*
				foreach ($winner_reliability as $idx=>$wre) {
					$prevDate = $winner_rating_date($idx);
					if ($prevDate > 0) {
						$degraded_rel = sqrt(pow($wre,2) + (($match_date - $prevDate) * pow(20,2)));
					} else {
						$degraded_rel = $wre;
					}
					$overall_winner_reliability += $degraded_rel;
				}
				$average_winner_reliability = $overall_winner_reliability / $haveWinningSide;
				foreach ($loser_reliability as $idx=>$lre) {
					$prevDate = $loser_rating_date($idx);
					if ($prevDate > 0) {
						$degraded_rel = sqrt(pow($lre,2) + (($match_date - $prevDate) * pow(20,2)));
					} else {
						$degraded_rel = $lre;
					}
					$overall_loser_reliability += $degraded_rel;
				}
				$average_loser_reliability = $overall_loser_reliability / $haveLosingSide;
				*/
				// crude way of working out team reliability:
				foreach ($winner_reliability as $wre) {
					$overall_winner_reliability += $wre;
				}
				$average_winner_reliability = $overall_winner_reliability/ $haveWinningSide;
				foreach ($loser_reliability as $lre) {
					$overall_loser_reliability += $lre;
				}
				$average_loser_reliability = $overall_loser_reliability / $haveLosingSide;
		//echo "Breakpoint 5.\n";
				// now we can use this:
				$winner_L = $average_winner_rating - $average_loser_rating;
				$loser_L = $average_loser_rating - $average_winner_rating;
				// now work out the actual Glicko ratings for the winner
				$winner_f = 1 / sqrt(1 + ($p * pow($average_loser_reliability,2)));
				$winner_X = 1 / (1 + pow(10,(-$winner_L * $winner_f / 400)));
				//$winner_expected_score = 10 * $winner_X;
				// DY young capped this at 16, which we will remove
				$winner_K = ($winner_f * $q / (1 / pow($average_winner_reliability,2) + pow($q,2) * pow($winner_f,2) * $winner_X * (1 - $winner_X)));
				$winner_rating_delta = round($winner_K * (($winner_score)-$winner_X),0);
				// Replace DY formula with LG formula
				//$winner_reliability_delta = sqrt(1 / (1 / pow($winner_reliability,2) + 1 / pow(d,2)))
				// now how do we apportion the increase?					
				for ($a = 0; $a < $haveWinningSide; $a++) {
					// this next line is a problem - it means that both partners in
					// a doubles game end up with the same reliability
					$winner_new_reliability[$a] = sqrt($winner_K / ($q * $winner_f));
					$winner_new_rating[$a] = $winner_rating[$a] + ($winner_rating_delta * ($haveWinningSide * $winner_rating[$a]) / $overall_winner_rating);
				}
		//echo "Breakpoint 6.\n";
				//***************************
				// Now the same for the loser
				//***************************
				$loser_f = 1 / sqrt(1 + ($p * pow($average_winner_reliability,2)));
				$loser_X = 1 / (1 + pow(10,(-$loser_L * $loser_f / 400)));
				//$loser_expected_score = 10 * $loser_X;
				// DY young capped this at 16, which we will remove
				$loser_K = ($loser_f * $q / (1 / pow($average_loser_reliability,2) + pow($q,2) * pow($loser_f,2) * $loser_X * (1 - $loser_X)));
				$loser_rating_delta = round($loser_K * (($loser_score)-$loser_X),0);
				//$loser_reliability_delta = min(0.125,1-(1/($loser_reliability*sqrt((1/(pow($loser_reliability,2))+pow($q,2)*pow($loser_f,2)*$loser_X*(1-$loser_X))))));
				// now how do we apportion the increase?
				for ($a = 0; $a < $haveLosingSide; $a++) {
					// again this next line has an issue
					$loser_new_reliability[$a] = sqrt($loser_K / ($q * $loser_f));
					$loser_new_rating[$a] = $loser_rating[$a] + ($loser_rating_delta * ($haveLosingSide * $loser_rating[$a]) / $overall_loser_rating);
				}
		//echo "Breakpoint 7.\n";
				// Now log out the results:
				//echo key($main)."::\n";
				for ($a = 0; $a < $haveWinningSide; $a++) {
					echo "Winner ".$winner_name[$a]." ".$winner_score." ".$winner_rating[$a].">".$winner_new_rating[$a]."\n";
				}
				for ($a = 0; $a < $haveLosingSide; $a++) {
					echo "Loser  ".$loser_name[$a]." ".$loser_score." ".$loser_rating[$a].">".$loser_new_rating[$a]."\n";
				}
				//$match_total = $winner_score + $loser_score;
				$match_total = 25;
				$pred_winner_score = round(($overall_winner_rating / ($overall_winner_rating + $overall_loser_rating)) * $match_total,0);
				$pred_loser_score = round(($overall_loser_rating / ($overall_winner_rating + $overall_loser_rating)) * $match_total,0); 
				echo "Predicted score from WR=".$overall_winner_rating.",LR=".$overall_loser_rating."::PWS=".$pred_winner_score.",PLS=".$pred_loser_score."\n";
				//*************************************************
				// Start a transaction for the following queries
				// Made more difficult because mySql on MyIsam doesn't support 
				// transactions:)
				//*************************************************
	
				//*********************************************
				// Suspend any existing ranking for this game
				//*********************************************
				/*
				foreach($winner_id as $wid) {
					$updateValue = array('status' => 'S');
					$this->db->where('competitor_id', $wid, 'match_id', $updateValue);
					$this->db->update('glicko_rating', $updateValue); 
				}
				foreach($loser_id as $lid) {
					$updateValue = array('status' => 'S');
					$this->db->where('competitor_id', $lid, 'match_id', $updateValue);
					$this->db->update('glicko_rating', $updateValue); 
				}
				*/
				// why were we doing that - delete the old records instead - there are now 10,000 suspended records and only 374 active ones
				// in fact - we have cleared down the whole table before anyway - why delete them - they shouldn't be there
				/*
				foreach($winner_id as $wid) {
					echo "Deleting rating record for Competitor ".$wid." in match ".$match_id."\n";
					$this->db->where('competitor_id', $wid, 'match_id', $match_id);
					$this->db->delete('glicko_rating'); 
				}
				foreach($loser_id as $lid) {
					echo "Deleting rating record for Competitor ".$lid." in match ".$match_id."\n";
					$this->db->where('competitor_id', $lid, 'match_id', $match_id);
					$this->db->delete('glicko_rating'); 
				}
				*/
				
				//****************************************
				// Insert the new rating in the database
				//****************************************
				
				for ($a = 0; $a < $haveWinningSide; $a++) {
					$wid = $winner_id[$a];
					$wra = $winner_new_rating[$a];
					$wre = $winner_new_reliability[$a];
					$wrb = $winner_rating[$a];
					$data = array(
						'competitor_id' => $wid,
						'match_id' => $match_id,
						'rating' => $wra,
						'reliability' => $wre,
						'rating_date' => $rating_date,
						'match_date' => $match_date,
						'rating_before' => $wrb
					);
					echo "Inserting row for ".$wid." in match ".$match_id." on ".$match_date." [".$rating_date."]\n";
	            			$this->db->insert('glicko_rating', $data);
	            			if ($this->db->affected_rows() == 0) {
	            				print_r($this->db->error());
	            			}
				}
				for ($a = 0; $a < $haveLosingSide; $a++) {
					$lid = $loser_id[$a];
					$lra = $loser_new_rating[$a];
					$lre = $loser_new_reliability[$a];
					$lrb = $loser_rating[$a];
					$data = array(
						'competitor_id' => $lid,
						'match_id' => $match_id,
						'rating' => $lra,
						'reliability' => $lre,
						'rating_date' => $rating_date,
						'match_date' => $match_date,
						'rating_before' => $lrb
					);
					echo "Inserting row for ".$lid." in match ".$match_id." on ".$match_date." [".$rating_date."]\n";
	            			$this->db->insert('glicko_rating', $data);
	            			if ($this->db->affected_rows() == 0) {
	            				print_r($this->db->error());
	            			}
				}
				
			} else {
				echo "Match not processed as only one side - must be a bye.\n";
				$pred_winner_score = 25;
				$pred_loser_score = 0; 
			}
			//*******************************************************************
			// Mark the match as processed so we don't reprocess it
			// also set the predicted scores
			//*******************************************************************
			
			$updateValue = array('glicko_processed' => '1'
				,'pred_winner_score' => $pred_winner_score
				,'pred_loser_score' => $pred_loser_score);
			$this->db->where('match_id', $match_id);
			$this->db->update('matches', $updateValue); 
	            	if ($this->db->affected_rows() == 0) {
	            		print_r($this->db->error());
	            	}
			
			//*******************************************************************
			// Commit the transaction
			//*******************************************************************
			// we can't because mySql doesn't do transactions
			//*******************************************************************
			// Delete this match from the main array so we don't reprocess it
			//*******************************************************************
			//unset($main[key($main)]); -- now done in the calling proc
			//*******************************************************************
			// Work out which other matches are affected by this rating change
			// and insert them into the main array if they are not already there
			// Mark them as unprocessed in the database
			//
			// ONE DAY we will do this, but at the moment we will just recalculate
			// from scratch every run, so this code is just a placeholder
			//*******************************************************************
		
		} else {
			//echo $db->debug();
			echo "Match ".$match_id." not found or had no players\n";
			//unset($main[key($main)]);
		}
		
	}
    
}

?>