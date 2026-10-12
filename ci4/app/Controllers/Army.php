<?php

namespace App\Controllers;

use App\Libraries\LegacyController as CI_Controller;
class Army extends CI_Controller {

	public function index() {
		$this->armyStats('booklistorder', 12, 'none');
	}

	public function armyStats($viewType, $monthsBack, $rankingMethod) {
		$data['viewType'] = 'booklistorder';
		$data['monthsBack'] = '12';
		$data['rankingMethod'] = 'none';
		
		$this->load->model('Army_model');
		$data['allArmies'] = $this->Army_model->getArmyStats($viewType, $monthsBack, $rankingMethod);
		$data['header'] = " - ".$this->lang->line("title_Armies");
		$data['menuItem'] = "armies";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('army_submenu', $data);
		$this->load->view('army_list', $data);
		$this->load->view('footer');
	}
	
	public function topdog($viewType, $monthsBack, $rankingMethod) {
		$data['viewType'] = 'booklistorder';
		$data['monthsBack'] = '12';
		$data['rankingMethod'] = 'none';
		$data['header'] = " - ".$this->lang->line("title_Armies");
		$data['menuItem'] = "topdog";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('army_submenu', $data);
		$this->load->view('top_dog', $data);
		$this->load->view('footer');	
	}

	public function listArmies() {
		$this->load->model('Army_model');
		$data['allArmies'] = $this->Army_model->getActiveArmies();
		$data['header'] = " - ".$this->lang->line("title_Armies");
		$data['menuItem'] = "armies";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('army_submenu', $data);
		$this->load->view('army_list', $data);
		$this->load->view('footer');
	}
	
	public function details($id) {
		$this->load->model('Army_model');
		if ($id > 0) {
			$data['armyData'] = $this->Army_model->getDetails($id);
			$data['header'] = " - ".$this->lang->line("title_Army_Details");
			$data['menuItem'] = "armies";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('army_details', $data);
			$this->load->view('footer');
		} else {
			$id = $this->input->get_post('id', TRUE);
			if ($id > 0) {
				$data['armyData'] = $this->Army_model->getDetails($id);
				$data['header'] = " - ".$this->lang->line("title_Army_Details");
				$data['menuItem'] = "armies";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('army_details', $data);
				$this->load->view('footer');
			} else {
				echo "Army does not exist";
			}
		}
	}
	
	public function listMatches($id) {
		$this->load->model('Army_model');
		$data['armyMatches'] = $this->Army_model->getArmyMatches($id);
		$data['header'] = " - ".$this->lang->line("title_Army_Matches");
		$data['menuItem'] = "armies";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('army_matches', $data);
		$this->load->view('footer');
		
	}
}