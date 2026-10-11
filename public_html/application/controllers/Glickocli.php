<?php
class Glickocli extends CI_Controller {

	public function index() {
		echo "In Glickocli\n";
		$this->calc();
	}
	
	public function calc() {
	
		echo "Glicko Rankings Engine\n";
		echo "======================\n";
		echo "Run date: ".date("Y/m/d h:m",time())."\n";
		$q = log(10)/400;
		$p = 3 * pow($q,2) / pow(pi(),2);
		echo "q is ".$q.", p is ".$p."\n";
		$rows = 0;
		$main = array();
		$startTime = time();
		
		// select matches that need to be processed in order of processing
		//
		//HACK: for simplicity, we will select everything and reprocess the 
		// whole lot every run, as we don't have much data at the moment
		//
		$this->load->model('Glicko_model');
		$this->Glicko_model->resetMatchesGlickoFlag();
		$this->Glicko_model->clearGlickoRatingTable();
    		$matches = $this->Glicko_model->getMatches();
		echo count($matches)." rows of match data extracted from DB.\n";
	        $records_processed = 0;
		if (count($matches)) {
		
			//*****************************************************
	    		//populate the processing array with the initial data
			//*****************************************************
			$rows = 0;
			$main = array();
			
			foreach ($matches as $match) {
				$this->populateMain($match, $main);
				$rows++;
			}
			
			echo $rows." match rows added to \$main array.\n";
//			print_r($main);
			ksort($main);
			$match = reset($main);
			// hmmm - we have an infinite loop bug which is upsetting 
			// Lunarpages - let's set a safety net
			$max_loops = $rows + 100;
			$curr_loop = 0;
			//*****************************************************
	    		//process each entry in the processing array in turn
			//*****************************************************
			print_r($match);
			while (!is_null($match) && $curr_loop < $max_loops) {
//				print_r($match);
				$key = key($main);
				echo "Match key is: ".$key."\n";
				//echo '.';
				$this->Glicko_model->processMatch($match, $p, $q);
				//*******************************************************************
				// Delete this match from the main array so we don't reprocess it
				//*******************************************************************
				unset($main[$key]);
				//***********************************************
				// Then resort the array and grab the first record again
				//***********************************************
				
				ksort($main);
				$match = reset($main);
				print_r($match);
				$records_processed++;
				if (($records_processed % 100) == 0) {
					echo $records_processed." processed, ".count($main)." left to go\n";
				}
				$curr_loop++;
			}
			echo "Completed main loop.\n";
		} else {
			echo "No match results to process.\n";
		}
		
		echo "Run finished - ".$records_processed." matches processed.\n";
		echo "Finish time: ".date("Y/m/d h:m",time()).".\n";
		echo "Processing time was ".(time() - $startTime)." seconds.\n";
		
	}
	
	function populateMain(&$match, &$main) {	
		//echo "About to process match on ".$match->competition_date." R";
		//echo $match->round."\n";
		
		$matchID = $match->match_id;
		$totalScore = $match->winner_score + $match->loser_score;
		if ($totalScore > 0) {
			$winnerScore = $match->winner_score / $totalScore;
			$loserScore = $match->loser_score / $totalScore;
		} else {
			$winnerScore = 0;
			$loserScore = 0;
		}
		//*********************************************************************
		// $match_date is a combination of the date of the match and the
		// round, to make sure that we always order matches chronologically
		// Two matches in the same round will have the same $match_date
		// but that shouldn't matter as you can't be playing in two matches
		// simultaneously (can you?).
		//*********************************************************************
		$date = $match->competition_date;
		$matchDate = substr($date,0,4).substr($date,5,2).substr($date,8,2);
		if ($match->round < 10) {
			$matchDate .= "0".$match->round;
		} else {
			$matchDate .= $match->round;
		}
		//*********************************************************************
		// the match_date will ensure that matches are sequentially ordered,
		// but won't uniquely identify the match, so we will suffix the 
		// match id to the end.
		// Since the match_date will always be the most significant 
		// part of the key, it will always force the sorting to be 
		// correct.
		// We also add an X to the front to force the sorting to be
		// string rather than numeric
		//*********************************************************************
		$mainkey = "X".$matchDate.$matchID;
		//echo $mainkey." is key for match ".$matchDate." ".$matchID."\n";
		$main[$mainkey] = array("match_id" => $matchID
		                      ,"winner_score" => $winnerScore
		                      ,"loser_score" => $loserScore
		                      ,"match_date" => $matchDate
		                      ,"date" => $date
		                      );
		                      
	}
	
}