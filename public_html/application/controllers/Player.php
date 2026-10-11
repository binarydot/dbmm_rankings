<?php
class Player extends CI_Controller {

	public function index() {
		$this->playerList();
	}
	
	public function details($id) {
		if ($id > 0) {
			$this->load->model('Player_model');
			$data['playerData'] = $this->Player_model->getPlayer($id);
			$data['armyData'] = $this->Player_model->getPlayerArmies($id);
			$data['header'] = " - ".$this->lang->line("title_Player_Details");
			$data['menuItem'] = "players";
			$data['competitorId'] = $id;
			$data['subMenuItem'] = 'Details';
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('player_submenu', $data);
			$this->load->view('player_data', $data);
			$this->load->view('footer');
		} else {
			echo 'Player does not exist';
		}
	}
	
	public function playerList($initial = 'A') {
		$this->load->model('Player_model');
		$data['initialList'] = $this->Player_model->getInitialLetterList();
		$data['playerList'] = $this->Player_model->getPlayersByInitial($initial);
		$data['currentInitial'] = $initial;
		$data['header'] = " - ".$this->lang->line("title_Player_List");
		$data['menuItem'] = "players";
		$data['subMenuItem'] = 'List';
		$data['competitorId'] = 0;
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('player_submenu', $data);
		$this->load->view('player_list', $data);
		$this->load->view('footer');
			}
	
	public function playerListFull() {
		$rar = $this->input->post('rar', TRUE);
		if (!$rar) {
			$rar = $this->uri->segment(3,0);
		}
		$pageNumber = $this->input->post('pageNum', TRUE);
		if (!$pageNumber) {
			$pageNumber = $this->uri->segment(4,1);
		}
		// default to page 1 if less than page 1
		if ($pageNumber < 1) {
			$pageNumber = 1;
		}
		$pageSize = $this->input->post('pageSize', TRUE);
		if (!$pageSize) {
			$pageSize = $this->uri->segment(5,20);
		}
    	// default  20 players per page if not specified
    	if ($pageSize < 1) {
    		$pageSize = 20;
    	}
    	$this->load->model('Rar_model');
    	$data['rars'] = $this->Rar_model->getRars();
		$this->load->model('Player_model');
		$data['totalPlayers'] = $this->Player_model->getTotalPlayers($rar);
		$data['playerList'] = $this->Player_model->getPlayers($rar, $pageNumber, $pageSize);
		$data['rar'] = $rar;
		$data['pageNum'] = $pageNumber;
		$data['pageSize'] = $pageSize;
		$data['header'] = " - ".$this->lang->line("title_Player_List");
		$data['menuItem'] = "players";
		$data['subMenuItem'] = 'List';
		$data['competitorId'] = 0;
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('player_submenu', $data);
		$this->load->view('player_list_full', $data);
		$this->load->view('footer');
		
	}
	
	/*
	 * Show the page for entering a new player into the database.
	 * We should probably check that you have the relevant permission here.
	 */
	public function add() {
		if (isset($this->session->userdata['permissions']['Add Player']) &&
				$this->session->userdata['permissions']['Add Player'] == 1) {
			$this->load->model('Rar_model');
			$data['rars'] = $this->Rar_model->getRars();
			$data['header'] = " - ".$this->lang->line("title_Add_Player");
			$data['menuItem'] = "players";
			$data['errorMessage'] = "";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('player_add', $data);
			$this->load->view('footer');
		} else {
			$this->playerList();
		}
	}
	
	public function edit($id) {
		if ($id > 0 && isset($this->session->userdata['permissions']['Update Player']) &&
				$this->session->userdata['permissions']['Update Player'] == 1) {
			$this->load->model('Rar_model');
			$data['rars'] = $this->Rar_model->getRars();
			$this->load->model('Player_model');
			$data['playerData'] = $this->Player_model->getPlayer($id);
			$data['header'] = " - ".$this->lang->line("title_Edit_Player");
			$data['menuItem'] = "players";
			$data['errorMessage'] = "";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('player_edit', $data);
			$this->load->view('footer');
		} else {
			$this->playerList();
		}
	}
	
	public function update($id) {
		//$this->output->enable_profiler(TRUE);
		$errors = false;
		$errorMessage = "";
		if ($id > 0 && isset($this->session->userdata['permissions']['Update Player']) &&
				$this->session->userdata['permissions']['Update Player'] == 1) {
			$rarID = $this->input->post('rar', TRUE);
			if (!isset($rarID) || $rarID < 0) {
				$errorMessage .= 'RAR must be specified. ';
				$errors = true;
			}
			$firstName = $this->input->post('first_name', TRUE);
			if (!isset($firstName)) {
				$errorMessage .= 'First Name must be specified. ';
				$errors = true;
			}
			$lastName = $this->input->post('last_name', TRUE);
			if (!isset($lastName)) {
				$errorMessage .= 'Last Name must be specified. ';
				$errors = true;
			}
			$iwfID = $this->input->post('iwf_id', TRUE);
			$anonymous = $this->input->post('anonymous', TRUE);
			if ($errors) {
				$this->load->model('Rar_model');
				$data['rars'] = $this->Rar_model->getRars();
				$this->load->model('Player_model');
				$data['playerData'] = $this->Player_model->getPlayer($id);
				$data['errorMessage'] = $errorMessage;
				$data['header'] = " - ".$this->lang->line("title_Edit_Player");
				$data['menuItem'] = "players";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('error_page', $data);
				$this->load->view('footer');
			} else {
				$this->load->model('Player_model');
				$result = $this->Player_model->update($id, $firstName, $lastName, $iwfID, $rarID, $anonymous);
				if ($result['status'] == "OK") {
					$this->details($id);
				} else {
					$this->load->model('Rar_model');
					$data['rars'] = $this->Rar_model->getRars();
					$this->load->model('Player_model');
					$data['playerData'] = $this->Player_model->getPlayer($id);
					$data['errorMessage'] = $result['errorMessage'];
					$data['header'] = " - ".$this->lang->line("title_Edit_Player");
					$data['menuItem'] = "players";
					$this->load->view('header', $data);
					$this->load->view('menu', $data);
					$this->load->view('player_edit', $data);
					$this->load->view('footer');
				}
			}
		} else {
			$this->playerList();
		}
	}
	
	public function insert() {
		$errors = false;
		$errorMessage = "";
		if (isset($this->session->userdata['permissions']['Add Player']) &&
				$this->session->userdata['permissions']['Add Player'] == 1) {
			$rarID = $this->input->post('rar', TRUE);
			if (!isset($rarID) || $rarID < 0) {
				$errorMessage .= 'RAR must be specified. ';
				$errors = true;
			}
			$firstName = $this->input->post('first_name', TRUE);
			if (!isset($firstName)) {
				$errorMessage .= 'First Name must be specified. ';
				$errors = true;
			}
			$lastName = $this->input->post('last_name', TRUE);
			if (!isset($lastName)) {
				$errorMessage .= 'Last Name must be specified. ';
				$errors = true;
			}
			$iwfID = $this->input->post('iwf_id', TRUE);
			$anonymous = $this->input->post('anonymous', TRUE);
			if ($errors) {
				$this->load->model('Rar_model');
				$data['rars'] = $this->Rar_model->getRars();
				$data['header'] = " - ".$this->lang->line("title_Add_Player");
				$data['menuItem'] = "players";
				$data['errorMessage'] = $errorMessage;
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('player_add', $data);
				$this->load->view('footer');
			} else {
				$this->load->model('Player_model');
				$result = $this->Player_model->insert($firstName, $lastName, $iwfID, $rarID, $anonymous);
				if ($result['status'] == "OK") {
					$this->details($result['ID']);
				} else {
					$data['errorMessage'] = $result['errorMessage'];
					$data['header'] = " - ".$this->lang->line("title_Add_Player");
					$data['menuItem'] = "players";
					$this->load->view('header', $data);
					$this->load->view('menu', $data);
					$this->load->view('error_page', $data);
					$this->load->view('footer');
				}
			}
		} else {
			$this->playerList();
		}
	}
	
	public function delete($id) {
		if ($id > 0 && isset($this->session->userdata['permissions']['Update Player']) &&
				$this->session->userdata['permissions']['Update Player'] == 1) {
			$this->load->model('Player_model');
			$result = $this->Player_model->delete($id);
			$this->playerList();
		} else {
			$this->playerList();
		}
		
	}
	
	public function glicko($id) {
		if ($id > 0) {
			$this->load->model('Glicko_model');
			$data['ratings'] = $this->Glicko_model->getPlayerHistory($id);
			$data['header'] = " - ".$this->lang->line("title_Player_Glicko");
			$data['menuItem'] = "players";
			$data['competitorId'] = $id;
			$data['subMenuItem'] = 'Glicko';
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('player_submenu', $data);
			$this->load->view('player_glicko', $data);
			$this->load->view('player_glicko_chart', $data);
			$this->load->view('footer');
		} else {
			$this->playerList();
		}
				
	}

	public function matches($id) {
		if ($id > 0) {
			$this->load->model('Matches_model');
			$data['matches'] = $this->Matches_model->getPlayerHistory($id);
			$data['header'] = " - ".$this->lang->line("title_Player_Matches");
			$data['menuItem'] = "players";
			$data['competitorId'] = $id;
			$data['subMenuItem'] = 'Matches';
			
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('player_submenu', $data);
			//var_dump($data['matches']);
			$this->load->view('player_matches', $data);
			$this->load->view('footer');
		} else {
			$this->playerList();
		}
				
	}
	
	public function results($id) {
		if ($id > 0) {
			$this->load->model('Result_model');
			$data['results'] = $this->Result_model->getPlayerHistory($id);
			$data['header'] = " - ".$this->lang->line("title_Player_Matches");
			$data['menuItem'] = "players";
			$data['competitorId'] = $id;
			$data['subMenuItem'] = 'Results';
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('player_submenu', $data);
			$this->load->view('player_results', $data);
			$this->load->view('footer');
		} else {
			$this->playerList();
		}
	
	}
	
}
?>