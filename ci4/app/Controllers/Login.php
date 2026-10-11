<?php

namespace App\Controllers;

use App\Libraries\LegacyController as CI_Controller;
class Login extends CI_Controller {

	public function index() {
		$data['loginMessage'] = "Please enter your login details";
		$data['header'] = " - Login";
		$data['menuItem'] = "login";
		$this->load->view('header', $data);
		$this->load->view('menu', $data);
		$this->load->view('login', $data);
		$this->load->view('footer');
	}
	
	public function submit() {
		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', TRUE);
		if((!$username) || (!$password)){
			$data['loginMessage'] = "Please enter ALL of the information.";
			$data['header'] = " - Login";
			$data['menuItem'] = "login";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('login', $data);
			$this->load->view('footer');
		} else {
			$this->load->model('User_model');
			// try and log in the user with the database
			$row = $this->User_model->loginUser($username, $password);
			if (isset($row) && $row != FALSE) {
				// user is logged in OK - set up session credentials for them
				$userData = array(
					'firstName' => $row->first_name,
					'lastName' => $row->last_name,
					'currentUser' => $row->user_name,
					'userID' => $row->user_id,
					'loggedIn' => TRUE
				);
				$this->session->set_userdata($userData);
				// we now have the following items in the session cookie:
				// [firstName]
				// [lastName]
				// [currentUser]
				// [userId]
				// [loggedIn]
				// get their permissions from the database and store them in the session
				$permissionData = $this->User_model->getUserPermissions($row->user_id);
				foreach ($permissionData as $perm) {
					$permissions[$perm->name] = $perm->value;
				}
				$permData = array('permissions' => $permissions);
				$this->session->set_userdata($permData);
				// we have now added a permission array to the session cookie:
				// [permissions]['permissionName1'] => numericValue
				// [permissions]['permissionName2'] => numericValue
				// etc	
				// we access them using $this->session->userdata('User Menu') for example
				// get their series permissions from the database and store them in the session
				$seriesPermissionData = $this->User_model->getSeriesPermissions($row->user_id);
				foreach ($seriesPermissionData as $seriesPerm) {
					$seriesPermissions[$seriesPerm->competition_series_id] = 1;
				}
				$seriesPermData = array('seriesPermissions' => $seriesPermissions);
				$this->session->set_userdata($seriesPermData);	
				// we have now added a permission array to the session cookie:
				// [seriesPermissions]['competitionSeriesId1'] => 1
				// [seriesPermissions]['competitionSeriesId2'] => 1
				// etc	
				$data['userId'] = $row->user_id;
				$data['firstName'] = $row->first_name;
				$data['lastName'] = $row->last_name;
				$data['permissions'] = $permissions;
				$data['seriesPermissions'] = $seriesPermissionData;
				$data['header'] = " - Welcome";
				$data['menuItem'] = "login";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('welcome', $data);
				$this->load->view('footer');
			} else {
				// failed login attempt - clear all the user data in the session just in case
				$userData = array(
					'firstName' => '',
					'lastName' => '',
					'userLevel' => 0,
					'currentUser' => '',
					'userID' => '',
					'loggedIn' => FALSE
				);
				$this->session->set_userdata($userData);
				$data['loginMessage'] = "Userid not found or userid and password do not match.";
				$data['header'] = " - Login";
				$data['menuItem'] = "login";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('login', $data);
				$this->load->view('footer');
			}
		}		
	}
}
	
