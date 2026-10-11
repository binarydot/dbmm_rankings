<?php
class Xml extends CI_Controller {

	public function index() {
		// do nothing
	}

	public function addStandin() {
		header("HTTP/1.0 200 OK");
		header("Content-type: text/xml");
		$permissions = $this->session->userdata('permissions');
		if ($permissions['Add Result'] == 1) {
			$standInID = $this->input->get('id', TRUE);
			$competitionID = $this->input->get('compid', TRUE);
			$standInForID = $this->input->get('infor', TRUE);
	   		$this->load->model('Competition_model');
	    	$data['affectedRows'] = $this->Competition_model->addStandin($standInID, $competitionID, $standInForID);
			$this->load->view('xml/add_standin_response', $data);
		} else {
			$this->load->view('xml/no_permission_error', null);
		}
	}
	
	public function addUmpire() {
		
	}
	
	public function createCompSeries() {
		header("HTTP/1.0 200 OK");
		header("Content-type: text/xml");
		$permissions = $this->session->userdata('permissions');
		if ($permissions['Add Competition'] == 1) {
			$name = $this->input->get('name', TRUE);
			$rar = $this->input->get('rar', TRUE);
			$this->load->model('Competition_model');
			$data['response'] = $this->Competition_model->insertCompetitionSeries($name, $rar);
			$this->load->view('xml/create_comp_series_response', $data);
		} else {
			$this->load->view('xml/no_permission_error', null);
		}
	}
	
	public function createMatch() {
		
	}
	
	public function createPlayer() {
		
	}
	
	public function deleteMatch() {
		
	}
	
	public function deletePlayer() {
		
	}
	
	public function deleteStandin() {
		
	}
	
	public function deleteUmpire() {
		
	}
	
	public function getAllCompSeries() {
		
	}
	
	public function getArmies() {
		
	}
	
	public function getMatchesByComp() {
		
	}
	
	public function getPlayer() {
		
	}
	
	public function getPlayers() {
		
	}
	
	public function getPlayersInComp() {
		
	}
	
	public function getRars() {
		header("HTTP/1.0 200 OK");
		header("Content-type: text/xml");
		$this->load->model('Rar_model');
		$data['data'] = $this->Rar_model->addStandin($standInID, $competitionID, $standInForID);
		$this->load->view('xml/get_rars_response', $data);
	}
	
	public function getStandinsForComp() {
		
	}
	
	public function getUmpiresForComp() {
		
	}
	
	public function updateMatch() {
		
	}
	
	public function updatePlayer() {
		
	}
	
	public function updateResults() {
		
	}
}