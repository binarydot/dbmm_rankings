<?php
class Logoff extends CI_Controller {

	public function index() {
		$userData = array(
			'firstName' => '',
			'lastName' => '',
			'userLevel' => 0,
			'currentUser' => '',
			'userID' => '',
			'loggedIn' => FALSE
		);
		$this->session->set_userdata($userData);
		$data['loginMessage'] = "Please enter your login details";
		$data['header'] = " - Login";
		$data['menuItem'] = "login";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('login', $data);
		$this->load->view('footer');
	}
	
}
	
