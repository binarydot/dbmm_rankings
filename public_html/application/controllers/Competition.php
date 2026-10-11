<?php
class Competition extends CI_Controller {

	public function index() {
		$this->competitionList();
	}

	public function competitionList() {
		$rar = $this->input->post('rar', TRUE);
		if (!$rar) {
			$rar = $this->uri->segment(3,0);
		}
		$series = $this->input->post('series', TRUE);
		if (!$series) {
			$series = $this->uri->segment(4,0);
		}
		// default to all if not specified
		if (!$series) {
			$series = 0;
		}
		$year = $this->input->post('year', TRUE);
		if (!$year) {
			$year = $this->uri->segment(5,-1);
		}
	    	// default to current year if not specified or out of range
	    	if (!$year || ($year > 0 && $year < 2007)) {
	    		$year = -1;
	    	}
		$this->load->model('Rar_model');
    		$data['rars'] = $this->Rar_model->getRars();
		$this->load->model('Competition_model');
		$data['competitionSeries'] = $this->Competition_model->getAllCompetitionSeries($rar);
		$data['competitions'] = $this->Competition_model->getCompetitionList($rar, $series, $year);
		//echo print_r($data['competitions']);
		$data['header'] = " - ".$this->lang->line("title_Competitions");
		$data['menuItem'] = "competitions";
		$data['rar'] = $rar;
		$data['series'] = $series;
		//$data['series'] = $competitionSeries;
		$data['year'] = $year;
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('competition_list', $data);
		$this->load->view('footer');
	}
	
	public function details($id) {
		$this->load->model('Competition_model');
		if ($id > 0) {
			$data['competitionDetails'] = $this->Competition_model->getCompetitionDetails($id);
			$data['header'] = " - ".$this->lang->line("title_Competition_Details");
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Details";
			$data['competitionId'] = $id;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_details', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - ".$this->lang->line("title_Competition Details");
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function add() {
		$this->load->model('Competition_model');
		$data['competitionSeries'] = $this->Competition_model->getAllCompetitionSeries(0);
		$this->load->model('Rar_model');
    		$data['rars'] = $this->Rar_model->getRars();
    		$data['systems'] = $this->Competition_model->getSystems();
    		$data['scales'] = $this->Competition_model->getScales();
    		$data['scoringSystems'] = $this->Competition_model->getScoringSystems();
    		$data['formats'] = $this->Competition_model->getFormats();
    		$data['themes'] = $this->Competition_model->getThemes();
		$data['header'] = " - ".$this->lang->line("title_Add_Competition");
		$data['menuItem'] = "competitions";
		//$data['subMenuItem'] = "Details";
    		//$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
    		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		//$this->load->view('competition_submenu', $data);
		$this->load->view('competition_add', $data);
		$this->load->view('footer');
		
	}

	
	public function insert() {
		//$this->output->enable_profiler(TRUE);
		$competitionName = $this->input->post('name', TRUE);
		$competitionDate = $this->input->post('date', TRUE);
		$type = $this->input->post('type', TRUE);
		$weighting = $this->input->post('weighting', TRUE);
		$protected = $this->input->post('protected', TRUE);
		$glickoEligible = $this->input->post('glicko_eligible', TRUE);
		$competitionSeriesId = $this->input->post('series', TRUE);
		$rarId = $this->input->post('rar', TRUE);
		$systemId = $this->input->post('system', TRUE);
		$scaleId = $this->input->post('scale', TRUE);
		$armyPoints = $this->input->post('army_points', TRUE);
		$location = $this->input->post('location', TRUE);
		$listsAllowed = $this->input->post('lists_allowed', TRUE);
		$rounds = $this->input->post('rounds', TRUE);
		$numCompetitors = $this->input->post('num_competitors', TRUE);
		$scoringSystemId = $this->input->post('scoring_system_id', TRUE);
		$formatId = $this->input->post('format_id', TRUE);
		$themeId = $this->input->post('theme_id', TRUE);
		$notes = $this->input->post('notes', TRUE);
		$this->load->model('Competition_model');
		$result = $this->Competition_model->insert(
			$competitionName,
			$competitionDate,
			$type,
			$weighting,
			$protected,
			$glickoEligible,
			$competitionSeriesId,
			$rarId,
			$systemId,
			$scaleId,
			$armyPoints,
			$location,
			$listsAllowed,
			$rounds,
			$numCompetitors,
			$scoringSystemId,
			$formatId,
			$themeId,
			$notes
		);
		if ($result['status'] == "OK") {
			$data['header'] = " - ".$this->lang->line("title_Competition_Results");
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Details";
			$data['competitionId'] = $result['ID'];
			$data['competitionDetails'] = $this->Competition_model->getCompetitionDetails($result['ID']);
			//$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_details', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = $result['errorMessage'];
			$data['header'] = " - ".$this->lang->line("title_Competition_Details");
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function edit($id) {
		$this->load->model('Competition_model');
		if ($id > 0) {
			$this->load->model('Competition_model');
			$data['competitionSeries'] = $this->Competition_model->getAllCompetitionSeries(0);
			$this->load->model('Rar_model');
	    		$data['rars'] = $this->Rar_model->getRars();
	    		$data['systems'] = $this->Competition_model->getSystems();
	    		$data['scales'] = $this->Competition_model->getScales();
	    		$data['scoringSystems'] = $this->Competition_model->getScoringSystems();
	    		$data['formats'] = $this->Competition_model->getFormats();
	    		$data['themes'] = $this->Competition_model->getThemes();
			$data['competitionDetails'] = $this->Competition_model->getCompetitionDetails($id);
			$data['header'] = " - Edit Competition Details";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Details";
			$data['competitionId'] = $id;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_edit', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Competition Details";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}

	public function update($id) {
		//$this->output->enable_profiler(TRUE);
		$competitionName = $this->input->post('name', TRUE);
		$competitionDate = $this->input->post('date', TRUE);
		$type = $this->input->post('type', TRUE);
		$weighting = $this->input->post('weighting', TRUE);
		$protected = $this->input->post('protected', TRUE);
		$glickoEligible = $this->input->post('glicko_eligible', TRUE);
		$competitionSeriesId = $this->input->post('series', TRUE);
		$rarId = $this->input->post('rar', TRUE);
		$systemId = $this->input->post('system', TRUE);
		$scaleId = $this->input->post('scale', TRUE);
		$armyPoints = $this->input->post('army_points', TRUE);
		$location = $this->input->post('location', TRUE);
		$listsAllowed = $this->input->post('lists_allowed', TRUE);
		$rounds = $this->input->post('rounds', TRUE);
		$numCompetitors = $this->input->post('num_competitors', TRUE);
		$scoringSystemId = $this->input->post('scoring_system_id', TRUE);
		$formatId = $this->input->post('format_id', TRUE);
		$themeId = $this->input->post('theme_id', TRUE);
		$notes = $this->input->post('notes', TRUE);
		$this->load->model('Competition_model');
		$result = $this->Competition_model->update(
			$id,
			$competitionName,
			$competitionDate,
			$type,
			$weighting,
			$protected,
			$glickoEligible,
			$competitionSeriesId,
			$rarId,
			$systemId,
			$scaleId,
			$armyPoints,
			$location,
			$listsAllowed,
			$rounds,
			$numCompetitors,
			$scoringSystemId,
			$formatId,
			$themeId,
			$notes
		);
		if ($result['status'] == "OK") {
			$this->details($id);
		} else {
			$data['errorMessage'] = $result['errorMessage'];
			$data['header'] = " - Competition Details";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function delete($id) {
		//$this->output->enable_profiler(TRUE);
		$this->load->model('Competition_model');
		$result = $this->Competition_model->delete($id);
		if ($result['status'] == "OK") {
			//$this->competitonList();
			$this->load->model('Rar_model');
	    		$data['rars'] = $this->Rar_model->getRars();
			$this->load->model('Competition_model');
			$data['competitionSeries'] = $this->Competition_model->getAllCompetitionSeries(-1);
			$data['competitions'] = $this->Competition_model->getCompetitionList(-1, -1, -1);
			//echo print_r($data['competitions']);
			$data['header'] = " - Competitions";
			$data['menuItem'] = "competitions";
			$data['rar'] = -1;
			$data['series'] = -1;
			//$data['series'] = $competitionSeries;
			$data['year'] = -1;
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_list', $data);
			$this->load->view('footer');

		} else {
			$data['errorMessage'] = $result['errorMessage'];
			$data['header'] = " - Competition Details";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
		
	}
	
	public function results($id) {
		$this->load->model('Result_model');
		if ($id > 0) {
			$data['competitionResults'] = $this->Result_model->getResults($id);
			if ($data['competitionResults']['fullResults']) {
				$data['submarine'] = $this->Result_model->getSubmarineData($id);
			}
			$this->load->model('Matches_model');
			$data['matchResults'] = $this->Matches_model->getMatches($id);
			$data['header'] = " - Competition Results";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$data['competitionId'] = $id;
			$this->load->model('Competition_model');
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_results', $data);
			if ($data['competitionResults']['resultsAvailable']) {
				$this->load->view('competition_results_chart', $data);
			}
			if ($data['competitionResults']['fullResults']) {
				$this->load->view('submarine_chart', $data);
			}
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Results";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function matches($id) {
		$this->load->model('Matches_model');
		if ($id > 0) {
			$data['competitionMatches'] = $this->Matches_model->getMatches($id);
			$data['header'] = " - Competition Matches";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Matches";
			$data['competitionID'] = $id;
			$data['competitionId'] = $id;
			$this->load->model('Competition_model');
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_matches', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Matches";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	// need to have ['competitionResults']series
	public function addResult($id) {
		if ($id > 0) {
			$this->load->model('Player_model');
			$data['playerList'] = $this->Player_model->getPlayers();
			$this->load->model('Army_model');
			$data['armyList'] = $this->Army_model->getActiveArmies();
			$data['competitionId'] = $id;
			$data['competitionResults']['competitionID'] = $id;
			$this->load->model('Competition_model');
			$tempData = $this->Competition_model->getCompetitionDetails($id);
			$data['competitionResults']['seriesID'] = $tempData->competition_series_id;
			$data['competitionResults']['type'] = $tempData->type;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$data['header'] = " - Add Competition Result";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_result_add', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function insertResult($id) {
		//$this->output->enable_profiler(TRUE);
		$competitorId = $this->input->post('competitorId', TRUE);
		$competitorId2 = $this->input->post('competitorId2', TRUE);
		$score = $this->input->post('score', TRUE);
		$bonus = $this->input->post('bonus', TRUE);
		$position = $this->input->post('position', TRUE);
		$armyId = $this->input->post('army', TRUE);
		if ($position < 1) {
			$this->addResult($id);
		}
		$this->load->model('Result_model');
		if ($competitorId2 < 0) {
			$response = $this->Result_model->insertResult($id, $competitorId, $armyId, $score, $bonus, $position);
		} else {
			$response = $this->Result_model->insertDoublesResult($id, $competitorId, $competitorId2, $armyId, $score, $bonus, $position);
		}
		if ($response['status'] = 'OK') {
			$this->results($id);
		} else {
			$data['errorMessage'] = $response['errorMessage'];
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
				
		}
	}
	
	public function addAllResults($compId) {
		if ($compId > 0) {
			$this->load->model('Competition_model');
			$data['competitionDetails'] = $this->Competition_model->getCompetitionDetails($compId);
			$data['competitionName'] = $data['competitionDetails']->competition_name;
			$data['competitionId'] = $compId;
			$data['competitionResults']['competitionID'] = $compId;
			$data['competitionResults']['seriesID'] = $data['competitionDetails']->competition_series_id;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$data['header'] = " - Add All Competition Results";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('result_add_all', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}

	public function validateAllResults($compId) {
		if ($compId > 0) {
			$config['upload_path'] = './resfiles/';
			$config['allowed_types'] = 'csv';
			$config['max_size']	= '100';
			$config['encrypt_name'] = TRUE;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload("csvfile")) {
				$data['errorMessage'] = $this->upload->display_errors();
				$data['header'] = " - Add Result";
				$data['menuItem'] = "competitions";
				$data['subMenuItem'] = "Results";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('error_page', $data);
				$this->load->view('footer');
			} else {
				//echo "<p>Upload sucessede</p>";
				$this->load->model('Competition_model');
				$data['competitionDetails'] = $this->Competition_model->getCompetitionDetails($compId);
				$data['competitionResults']['competitionID'] = $compId;
				$data['competitionResults']['seriesID'] = $data['competitionDetails']->competition_series_id;	
				$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
				$this->load->model('Player_model');
				$data['playerList'] = $this->Player_model->getPlayers();
				$this->load->model('Army_model');
				$data['armyList'] = $this->Army_model->getActiveArmies();
				$this->load->model('Result_model');
				$data['resultData'] = $this->Result_model->validateResultData($this->upload->data());
				// OK - we should have all the data in the array. Now we need to process it into $res2
				// First check all the players and armies exist. Note if they don't - we will allow the user
				// to correct the data on-screen.
				if ($data['resultData']['status'] == "OK") {
					//echo "<p>Data is validated</p>";
					$data['resultData'] = $this->Result_model->checkUploadData($data['resultData']['data']);
					if ($data['resultData']['status'] == "OK") {
						//echo "<p>Data is checked</p>";
						//print_r($data['resultData']);
						$data['competitionId'] = $compId;
						$data['header'] = " - Add All Competition Results";
						$data['menuItem'] = "competitions";
						$data['subMenuItem'] = "Results";
						$data['results'] = $data['resultData']['data'];
						$this->load->view('header', $data);
						$this->load->view('menu', $data);
						$this->load->view('competition_submenu', $data);
						$this->load->view('result_show_all', $data);
						$this->load->view('footer');
					} else {
						//echo "<p>Data check failed</p>";
						$data['header'] = " - Add Result";
						$data['menuItem'] = "competitions";
						$data['subMenuItem'] = "Results";
						$data['errorMessage'] = $data['resultData']['errorMessage'];
						$this->load->view('header', $data);
						$this->load->view('menu', $data);
						$this->load->view('error_page', $data);
						$this->load->view('footer');
					}
				} else {
					//echo "<p>data validation failed</p>";
					$data['header'] = " - Add Result";
					$data['menuItem'] = "competitions";
					$data['subMenuItem'] = "Results";
					$data['errorMessage'] = $data['resultData']['errorMessage'];
					$this->load->view('header', $data);
					$this->load->view('menu', $data);
					$this->load->view('error_page', $data);
					$this->load->view('footer');
				}
			}
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function insertAllResults($compId) {
		$this->load->model('Result_model');
		// delete all the existing results for the competition
		$this->Result_model->deleteAllResultsForComp($compId);
		$competitors = $this->input->post('c', TRUE);
		$rounds = $this->input->post('r', TRUE);
		$matchDate = $this->input->post('competition_date', TRUE);
		// now the horrible bit of parsing the request comes
		// we need to insert these as results, and also build an array for the next step
		$playerData = array();
		$errorMessage = "";
		for ($a = 0; $a < $competitors; $a++) {
			$competitorId = $this->input->post('p'.$a, TRUE);
			$position = $this->input->post('o'.$a, TRUE);
			$score = $this->input->post('s'.$a, TRUE);
			$armyId = $this->input->post('a'.$a, TRUE);
			$fileId = $this->input->post('f'.$a, TRUE);
			$response = $this->Result_model->insertResult($compId, $competitorId, $armyId, $score, 0, $position);
			$tempData = array();
			$tempData['fileId'] = $fileId;
			$tempData['competitorId'] = $competitorId;
			array_push($playerData, $tempData);
			if ($response['status'] == "Error") {
				$errorMesagge.="<p>".$response['errorMessage']." For results at position ".$position.".</p>";
			}
		}
		// now  we need to recompile the round by round matchups with the correct player IDs.
		
		$this->load->model('Matches_model');
		// delete all the existing matches for the competition
		$this->Matches_model->deleteAllMatchesForComp($compId);
		for ($b = 1; $b <= $rounds; $b++) {
			$round = $b;
			//echo "<p>Round ".$b."</p>";
			$numMatches = $this->input->post('r'.$b.'c', TRUE);
			//$match[$b]["matches"] = $this->input->post('r'.$b.'c', TRUE);
			for ($c = 0; $c < $numMatches; $c++) {
				//echo "<p>Match ".$c."</p>";
				$winnerFileId = $this->input->post('r'.$b.'w'.$c, TRUE);
				$loserFileId= $this->input->post('r'.$b.'l'.$c, TRUE);
				$winnerScore = $this->input->post('r'.$b.'ws'.$c, TRUE);
				$loserScore = $this->input->post('r'.$b.'ls'.$c, TRUE);
				$winnerFound = false;
				$loserFound = false;
				if ($loserFileId == -1) {
					//the opponent was a BYE
					$loserFound = TRUE;
					$loserId = -1;
				}
				foreach ($playerData as $player) {
					//echo ".";
					if (!$winnerFound && $player['fileId'] == $winnerFileId) {
						$winnerFound = TRUE;
						$winnerId = $player['competitorId'];
						//echo "<P>Winner found</p>";
					}
					if (!$loserFound && $player['fileId'] == $loserFileId) {
						$loserFound = TRUE;
						$loserId = $player['competitorId'];
						//echo "<p>Loser found</p>";
					}
					if ($winnerFound && $loserFound) {
						//echo "<p>Calling Match insert</p>";
						$response = $this->Matches_model->insertMatch($compId
							,$matchDate
							,1
							,$round
							,$winnerId
							,-1
							,-1
							,$winnerScore
							,$loserScore
							,$loserId
							,-1
							,-1
						);
						if ($response['status'] == "Error") {
							$errorMessage.="<p>".$response['errorMessage']." For match number ".$c." in round ".$b.".</p>";
						}
						break;
					}
				}
			}
		}
		if ($errorMessage == "") {
			$this->results($compId);
		} else {
			$data['errorMessage'] = $errorMessage;
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function addRunner($id) {
		if ($id > 0) {
			$this->load->model('Player_model');
			$data['playerList'] = $this->Player_model->getPlayers();
			$this->load->model('Army_model');
			$data['armyList'] = $this->Army_model->getActiveArmies();
			$data['competitionId'] = $id;
			$data['competitionResults']['competitionID'] = $id;
			$this->load->model('Competition_model');
			$tempData = $this->Competition_model->getCompetitionDetails($id);
			$data['competitionResults']['seriesID'] = $tempData->competition_series_id;
			$data['competitionResults']['type'] = $tempData->type;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$data['header'] = " - Add Competition Runner";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_runner_add', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Competition Details";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function insertRunner($id) {
		$competitorId = $this->input->post('competitorId', TRUE);
		$competitorId2 = $this->input->post('competitorId2', TRUE);
		$armyId = $this->input->post('army', TRUE);
		$this->load->model('Result_model');
		if ($competitorId2 < 0) {
			$response = $this->Result_model->insertRunner($id, $competitorId, $armyId);
		} else {
			$response = $this->Result_model->insertDoublesRunner($id, $competitorId, $competitorId2, $armyId);
		}
		if ($response[0] = 'OK') {
			$this->results($id);
		} else {
			$data['errorMessage'] = $results[1];
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
		
	}
	
	public function editResult($compID, $posID) {
		if ($posID > 0) {
			$this->load->model('Player_model');
			$data['playerList'] = $this->Player_model->getPlayers();
			$this->load->model('Army_model');
			$data['armyList'] = $this->Army_model->getActiveArmies();
			$data['resultId'] = $posID;
			$data['competitionId'] = $compID;
			$this->load->model('Result_model');
			$data['resultData'] = $this->Result_model->getResultDetails($posID);
			$data['competitionResults']['competitionID'] = $compID;
			$this->load->model('Competition_model');
			$tempData = $this->Competition_model->getCompetitionDetails($compID);
			$data['competitionResults']['seriesID'] = $tempData->competition_series_id;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($compID);
			$data['header'] = " - Edit Competition Result";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_result_edit', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Result does not exist.";
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	// TODO update doubles result
	public function updateResult($compID, $posID) {
		//$this->output->enable_profiler(TRUE);
		$competitorId = $this->input->post('competitorId', TRUE);
		$score = $this->input->post('score', TRUE);
		$bonus = $this->input->post('bonus', TRUE);
		$position = $this->input->post('position', TRUE);
		$armyId = $this->input->post('army', TRUE);
		$this->load->model('Result_model');
		$response = $this->Result_model->updateResult($posID, $competitorId, $armyId, $score, $bonus, $position);
		if ($response['status'] = 'OK') {
			$this->results($compID);
		} else {
			$data['errorMessage'] = $response['errorMessage'];
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function deleteResult($compID, $id) {
		$this->load->model('Result_model');
		$response = $this->Result_model->deleteResult($id);
		if ($response['status'] = 'OK') {
			$this->results($compID);
		} else {
			$data['errorMessage'] = $response['errorMessage'];
			$data['header'] = " - Delete Result";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}

	public function addMatch($id) {
		if ($id > 0) {
			$this->load->model('Competition_model');
			$data['compData'] = $this->Competition_model->getCompetitionDetails($id);
			$this->load->model('Player_model');
			$data['compPlayerList'] = $this->Player_model->listPlayersInCompetition($id);
			$this->load->model('Army_model');
			$data['armyList'] = $this->Army_model->getActiveArmies();
			$data['competitionMatches']['competitionSeriesID'] = $data['compData']->competition_series_id;
			$data['competitionId'] = $id;
			$data['header'] = " - Add Match";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Matches";
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('match_add', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Add Match";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function insertMatch($id) {
		//$this->output->enable_profiler(TRUE);
		$matchDate = $this->input->post('matchdate', TRUE);
		$glickoEligible = $this->input->post('glicko_eligible', TRUE);
		$round = $this->input->post('round', TRUE);
		$winnerId0 = $this->input->post('winnerid0', TRUE);
		$winnerId1 = $this->input->post('winnerid1', TRUE);
		$winnerArmy = $this->input->post('winnerarmy', TRUE);
		$winnerScore = $this->input->post('winnerscore', TRUE);
		$loserScore = $this->input->post('loserscore', TRUE);
		$loserId0 = $this->input->post('loserid0', TRUE);
		$loserId1 = $this->input->post('loserid1', TRUE);
		$loserArmy = $this->input->post('loserarmy', TRUE);
		$this->load->model('Matches_model');
		$response = $this->Matches_model->insertMatch($id
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
				);
		if ($response['status'] = 'OK') {
			$this->matches($id);
		} else {
			$data['errorMessage'] = $response['errorMessage'];
			$data['header'] = " - Add Match";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		
		}
	}
	
	public function editMatch($compID, $id) {
		if ($id > 0) {
			$this->load->model('Competition_model');
			$data['compData'] = $this->Competition_model->getCompetitionDetails($compID);
			$this->load->model('Player_model');
			$data['compPlayerList'] = $this->Player_model->listPlayersInCompetition($compID);
			$this->load->model('Army_model');
			$data['armyList'] = $this->Army_model->getActiveArmies();
			$this->load->model('Matches_model');
			$data['matchDetails'] = $this->Matches_model->getMatchDetails($compID, $id);
			$data['competitionId'] = $compID;
			$data['competitionMatches']['competitionSeriesId'] = $data['compData']->competition_series_id;
			$data['matchId'] = $id;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$data['header'] = " - Edit Match";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Matches";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('match_edit', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Edit Match";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function updateMatch($compID, $id) {
		//$this->output->enable_profiler(TRUE);
		$matchDate = $this->input->post('matchdate', TRUE);
		$glickoEligible = $this->input->post('glicko_eligible', TRUE);
		$round = $this->input->post('round', TRUE);
		$winnerId0 = $this->input->post('winnerid0', TRUE);
		$winnerId1 = $this->input->post('winnerid1', TRUE);
		$winnerArmy = $this->input->post('winnerarmy', TRUE);
		$winnerScore = $this->input->post('winnerscore', TRUE);
		$loserScore = $this->input->post('loserscore', TRUE);
		$loserId0 = $this->input->post('loserid0', TRUE);
		$loserId1 = $this->input->post('loserid1', TRUE);
		$loserArmy = $this->input->post('loserarmy', TRUE);
		$this->load->model('Matches_model');
		$response = $this->Matches_model->updateMatch($compID
				,$id
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
		);
		if ($response['status'] = 'OK') {
			$this->matches($compID);
		} else {
			$data['errorMessage'] = $response['errorMessage'];
			$data['header'] = " - Edit Match";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		
		}
	}
	
	public function deleteMatch($compID, $id) {
		$this->load->model('Matches_model');
		$response = $this->Matches_model->deleteMatch($compID, $id);
		if ($response['status'] = 'OK') {
			$this->matches($compID);
		} else {
			$data['errorMessage'] = $response['errorMessage'];
			$data['header'] = " - Delete Match";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function addUmpire($id) {
		if ($id > 0) {
			$this->load->model('Player_model');
			$data['playerList'] = $this->Player_model->getPlayers();
			$data['competitionId'] = $id;
			$data['competitionResults']['competitionID'] = $id;
			$this->load->model('Competition_model');
			$tempData = $this->Competition_model->getCompetitionDetails($id);
			$data['competitionResults']['seriesID'] = $tempData->competition_series_id;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$data['header'] = " - Add Umpire";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_umpire_add', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Add Umpire";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function insertUmpire($id) {
		$competitorId = $this->input->post('competitorId', TRUE);
		$this->load->model('Umpire_model');
		$response = $this->Umpire_model->insertUmpire($id, $competitorId);
		if ($response[0] = 'OK') {
			$this->results($id);
		} else {
			$data['errorMessage'] = $results[1];
			$data['header'] = " - Add Umpire";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function addStandIn($id) {
		if ($id > 0) {
			$this->load->model('Player_model');
			$data['playerList'] = $this->Player_model->getPlayers();
			$data['compPlayerList'] = $this->Player_model->listPlayersInCompetition($id);
			$data['competitionId'] = $id;
			$data['competitionResults']['competitionID'] = $id;
			$this->load->model('Competition_model');
			$tempData = $this->Competition_model->getCompetitionDetails($id);
			$data['competitionResults']['seriesID'] = $tempData->competition_series_id;
			$data['competitionResults']['fullResults'] = $this->Competition_model->getCompetitionStatus($id);
			$data['header'] = " - Add Stand In";
			$data['menuItem'] = "competitions";
			$data['subMenuItem'] = "Results";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('competition_submenu', $data);
			$this->load->view('competition_standin_add', $data);
			$this->load->view('footer');
		} else {
			$data['errorMessage'] = "Competition does not exist.";
			$data['header'] = " - Add Result";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function insertStandIn($id) {
		$standInID = $this->input->post('standInId', TRUE);
		$stoodInForID = $this->input->post('stoodInForId', TRUE);
		$this->load->model('Standin_model');
		$response = $this->Standin_model->insertStandIn($id, $standInID, $stoodInForID);
		if ($response[0] = 'OK') {
			$this->results($id);
		} else {
			$data['errorMessage'] = $results[1];
			$data['header'] = " - Add Stand In";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		
		}
	}
	
	public function deleteUmpire($compID, $id) {
		$this->load->model('Umpire_model');
		$response = $this->Umpire_model->deleteUmpire($id);
		if ($response['status'] = 'OK') {
			$this->results($compID);
		} else {
			$data['errorMessage'] = $response['errorMessage'];
			$data['header'] = " - Delete Umpire";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
	public function deleteStandIn($compID, $id) {
		$this->load->model('Standin_model');
		$response = $this->Standin_model->deleteStandin($id);
		if ($response['status'] = 'OK') {
			$this->results($compID);
		} else {
			$data['errorMessage'] = $response['errorMessage'];
			$data['header'] = " - Delete Stand In";
			$data['menuItem'] = "competitions";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('error_page', $data);
			$this->load->view('footer');
		}
	}
	
}