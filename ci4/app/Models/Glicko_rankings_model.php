<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;


class Glicko_rankings_model extends CI_Model {
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function getRankings($reliability, $rar) {

     	
    	date_default_timezone_set('UTC');
		// Get the data for the rankings
		// Check if the RAR is set and is valid
		if (isset($rar) && is_numeric($rar) && $rar >= 0) {
			$useRar = TRUE;
			$rarSqlWhere = " AND p.rar_id = ? ";
		} else {
			$useRar = FALSE;
			$rarSqlWhere = "";
			$rar = -1;
		}
		$results["rarUsed"] = $rar;
		// Check the reliability limit and set a default of 'C' if not set
		if ($reliability == "A") {
			$reliabilityFilter = "AND gr.reliability <= gs.reliability_a";
		} else if ($reliability == "B") {
			$reliabilityFilter = "AND gr.reliability <= gs.reliability_b";
		} else if ($reliability == "C") {
			$reliabilityFilter = "AND gr.reliability <= gs.reliability_c";
		} else if ($reliability == "D") {
			$reliabilityFilter = "AND gr.reliability <= gs.reliability_d";
		} else if ($reliability == "E") {
			$reliabilityFilter = "AND gr.reliability <= gs.reliability_e";
		} else if ($reliability == "All") {
			$reliabilityFilter = "AND gr.reliability <= gs.reliability_e";
		} else {
			$reliability = "C";
			$reliabilityFilter = "AND gr.reliability <= gs.reliability_c";
		}
		$results["reliabilityUsed"] = $reliability;
		// Set up the config information
		// Reliability numeric thresholds from the letter codes
		$sql = "
			SELECT reliability_a
			      ,reliability_b
			      ,reliability_c
			      ,reliability_d
			      ,reliability_e
			FROM   glicko_settings
			LIMIT 0,1
		";
		$query = $this->db->query($sql, array());
		$results["reliabilityThresholdList"] = $query->row();
		// get the data from the database
		$c = 1;
		$sql = "
			SELECT p.competitor_id
			      ,p.first_name
			      ,p.last_name
			      ,gr.rating
			      ,gr.reliability
			      ,r.rar_name
			FROM   competitor p
			      ,glicko_rating gr
			      ,glicko_settings gs
			      ,rar r
			WHERE  gr.rating_date = (
			  SELECT max(rating_date)
			  FROM   glicko_rating gr2
			  WHERE  gr.competitor_id = gr2.competitor_id
			    AND  status = 'A'
			  )
			AND    p.competitor_id = gr.competitor_id
			AND    r.rar_id        = p.rar_id
			".$rarSqlWhere." ".$reliabilityFilter."
			ORDER BY rating DESC
			";
		if ($useRar) {
			$query = $this->db->query($sql, array($rar));
		} else {
			$query = $this->db->query($sql, array());
		}
		$ratings = $query->result();
		foreach ($ratings as $rating) {
			$results["glickoRankings"][$c]["rank"] = $c;
			$results["glickoRankings"][$c]["playerId"] = $rating->competitor_id;
			$results["glickoRankings"][$c]["playerName"] = $rating->first_name." ".$rating->last_name;
			$results["glickoRankings"][$c]["rarName"] = $rating->rar_name;
			$results["glickoRankings"][$c]["ranking"] = $rating->rating;
			$results["glickoRankings"][$c]["reliability"] = $rating->reliability;
			if ($rating->reliability <= $results['reliabilityThresholdList']->reliability_a) {
				$results["glickoRankings"][$c]["reliabilityName"] = "AAAAA";
			} else if ($rating->reliability <= $results['reliabilityThresholdList']->reliability_b) {
				$results["glickoRankings"][$c]["reliabilityName"] = "BBBB";
			} else if ($rating->reliability <= $results['reliabilityThresholdList']->reliability_c) {
				$results["glickoRankings"][$c]["reliabilityName"] = "CCC";
			} else if ($rating->reliability <= $results['reliabilityThresholdList']->reliability_d) {
				$results["glickoRankings"][$c]["reliabilityName"] = "DD";
			} else if ($rating->reliability <= $results['reliabilityThresholdList']->reliability_e) {
				$results["glickoRankings"][$c]["reliabilityName"] = "E";
			} else {
				$results["glickoRankings"][$c]["reliabilityName"] = "X";
			}
			$c++;
		}
		return $results;
	}
	
    
}