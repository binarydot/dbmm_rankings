<?php

namespace App\Controllers;

use App\Libraries\LegacyController as CI_Controller;
class Matches extends CI_Controller {

	public function index() {
		$this->load->model('Matches_model');
		$data['resultsData'] = $this->Matches_model->matchesByResult();
		$data['header'] = " - Match Stats";
		$data['menuItem'] = "stats";
		$data['subMenuItem'] = "Match Results";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('matches_submenu', $data);
		$this->load->view('matches_results_totals', $data);
		$this->load->view('footer');
	}
	
	public function matchesDetails($year) {
		//$year = $this->input->post('year', TRUE);
		if (is_null($year)) {
			$year = date("Y");
		}
		$this->load->model('Matches_model');
		$data['matches'] = $this->Matches_model->allMatchDetailsByYear($year);
		$data['header'] = " - Match Stats";
		$data['menuItem'] = "stats";
		$data['subMenuItem'] = "All Matches";
		$data['yearUsed'] = $year;
		//echo "Data loaded";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('matches_submenu', $data);
		$this->load->view('matches_all_details', $data);
		$this->load->view('footer');
	}
	
	
	public function matchesDetailsCSV($year) {
		$this->load->model('Matches_model');
		$data['matches'] = $this->Matches_model->allMatchDetailsByYear($year);
		foreach ($data['matches'] as &$match) {
			if ($match->side == 'W') {
				$match->score = $match->winner_score." - ".$match->loser_score;
			} else {
				$match->score = $match->loser_score." - ".$match->winner_score;
			}
		}
		header("Content-type: application/csv");
    		header("Content-Disposition: attachment; filename=\"matchdata.csv\"");
    		header("Pragma: no-cache");
    		header("Expires: 0");
		$output = fopen('php://output', 'w');
		//$this->output->set_content_type('text/csv');
		// output the column headings
		fputcsv($output, array('System Name'
			, 'System Version'
			, 'Competition Date'
			, 'Round'
			, 'Competition ID'
			, 'Competition Name'
			, 'Player ID'
			, 'First Name'
			, 'Last Name'
			, 'Army ID'
			, 'Army Name'
			, 'Army Version'
			, 'Army System'
			, 'Side'
			, 'Winner Score'
			, 'Loser Score'
			, 'Score'
			));
		foreach ($data['matches'] as $match) {
			fputcsv($output, (array) $match);
		}
		fclose($output);
	}

	public function annualTrends() {
		$this->load->model('Matches_model');
		$data['trends'] = $this->Matches_model->statsByYear();
		$data['header'] = " - Annual Trends";
		$data['menuItem'] = "stats";
		$data['subMenuItem'] = "Annual Trends";
		//echo "Data loaded";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('matches_submenu', $data);
		$this->load->view('annual_trends', $data);
		$this->load->view('footer');
	}
	
	
}
?>