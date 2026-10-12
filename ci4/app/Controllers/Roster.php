<?php

namespace App\Controllers;

use App\Libraries\LegacyController as CI_Controller;
class Roster extends CI_Controller {

	public function index() {
		$data['header'] = " - Roster Editor";
		$data['menuItem'] = "roster";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('roster_editor', $data);
		$this->load->view('footer');
	}

}