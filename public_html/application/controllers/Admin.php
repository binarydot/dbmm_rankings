<?php
class Admin extends CI_Controller {

	public function index() {
		if ($this->session->userdata('loggedIn') == TRUE) {
			$this->load->model('User_model');
			$data['userId'] = $this->session->userdata('userID');
			$data['firstName'] = $this->session->userdata('firstName');
			$data['lastName'] = $this->session->userdata('lastName');
			$data['permissions'] = $this->session->userdata('permissions');
			$data['seriesPermissions'] = $this->User_model->getSeriesPermissions($this->session->userdata('userID'));;
			$data['header'] = " - Admin";
			$data['menuItem'] = "admin";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('admin_submenu', $data);
			$this->load->view('welcome', $data);
			$this->load->view('footer');
		} else {
			$data['loginMessage'] = "Please enter your login details";
			$data['header'] = " - Login";
			$data['menuItem'] = "login";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('login', $data);
			$this->load->view('footer');
		}
	}

	public function changePassword() {
		if ($this->session->userdata('loggedIn') == TRUE) {
			$data['userId'] = $this->session->userdata('userID');
			$data['id'] = $this->session->userdata('userID');
			$data['header'] = " - Change Password";
			$data['menuItem'] = "admin";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('admin_submenu', $data);
			$this->load->view('change_password', $data);
			$this->load->view('footer');
		} else {
			$this->index();
		}
	}
	
	public function updatePassword() {
		if ($this->session->userdata('loggedIn') == TRUE) {
			$userId = $this->session->userdata('userID');
			$password = $this->input->post('password1', TRUE);
			$oldPassword = $this->input->post('oldPassword', TRUE);
			$this->load->model("User_model");
			$result = $this->User_model->checkPassword($userId, $oldPassword);
			if ($result['status'] == "OK") {
				$this->User_model->setPassword($userId, $password);
				$data['permissions'] = $this->session->userdata('permissions');
				$data['header'] = " - Admin";
				$data['menuItem'] = "admin";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('admin_submenu', $data);
				$this->load->view('welcome', $data);
				$this->load->view('footer');
			} else {
				$this->index();
			}
		} else {
			$this->index();
		}
	}
	
	public function setPassword($id) {
		if (isset($this->session->userdata['permissions']['Create User'])
				&& $this->session->userdata['permissions']['Create User'] == 1) {
			$data['userId'] = $this->session->userdata('userID');
			$data['id'] = $id;
			$data['header'] = " - Set Other User's Password";
			$data['menuItem'] = "admin";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('admin_submenu', $data);
			$this->load->view('set_password', $data);
			$this->load->view('footer');
		} else {
			$this->index();
		}
	}
	
	public function updatePasswordOther() {
		if (isset($this->session->userdata['permissions']['Create User'])
				&& $this->session->userdata['permissions']['Create User'] == 1) {
			$userId = $this->session->userdata('userID');
			$id = $this->input->post('id', TRUE);
			$password = $this->input->post('password1', TRUE);
			$this->load->model("User_model");
			$result = $this->User_model->setPassword($id, $password);
			if ($result['status'] == "OK") {
				$this->userDetails($id);
			} else {
				$data['errorMessage'] = $result['errorMessage'];
				$data['header'] = " - Update User";
				$data['menuItem'] = "admin";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('error_page', $data);
				$this->load->view('footer');
			}
		} else {
			$this->index();
		}
	}
	
	public function addUser() {
		if (isset($this->session->userdata['permissions']['User Menu'])
				&& $this->session->userdata['permissions']['User Menu'] == 1) {
			$this->load->model('User_model');
			$data['userPerms'] = $this->User_model->getAllPermissions();
			$data['seriesPerms'] = $this->User_model->getAllSeriesPermissions();
			$data['userId'] = $this->session->userdata('userID');
			$data['header'] = " - Add User";
			$data['menuItem'] = "admin";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('admin_submenu', $data);
			$this->load->view('user_add', $data);
			$this->load->view('footer');
		} else {
			$this->index();
		}
	}
	
	public function insertUser() {
		$userName = $this->input->post('username', TRUE);
		$firstName = $this->input->post('firstname', TRUE);
		$lastName = $this->input->post('lastname', TRUE);
		$password = $this->input->post('password1', TRUE);
		$parms = $this->input->post(NULL, TRUE);
		if (isset($this->session->userdata['permissions']['User Menu'])
				&& $this->session->userdata['permissions']['User Menu'] == 1) {
			$this->load->model('User_model');
			$result = $this->User_model->insertUser($userName, $firstName, $lastName, $password, $parms);
			if ($result['status'] == "OK") {
				$this->userDetails($result['ID']);
			} else {
				$data['errorMessage'] = $result['errorMessage'];
				$data['header'] = " - Insert User";
				$data['menuItem'] = "admin";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('error_page', $data);
				$this->load->view('footer');
			}
		} else {
			$this->index();
		}
	}
	
	public function listUsers() {
		if (isset($this->session->userdata['permissions']['User Menu'])
				&& $this->session->userdata['permissions']['User Menu'] == 1) {
			$this->load->model('User_model');
			$data['users'] = $this->User_model->listUsers();
			$data['userId'] = $this->session->userdata('userID');
			$data['header'] = " - User List";
			$data['menuItem'] = "admin";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('admin_submenu', $data);
			$this->load->view('user_list', $data);
			$this->load->view('footer');
		} else {
			$this->index();
		}
	}
	
	public function userDetails($id) {
		if (isset($this->session->userdata['permissions']['User Menu'])
				&& $this->session->userdata['permissions']['User Menu'] == 1) {
			$this->load->model('User_model');
			$data['userDetails'] = $this->User_model->getUserDetails($id);
			$data['userPerms'] = $this->User_model->getUserPermissionSettings($id);
			$data['seriesPerms'] = $this->User_model->getUserSeriesSettings($id);
			$data['userId'] = $this->session->userdata('userID');
			$data['header'] = " - User Details";
			$data['menuItem'] = "admin";
			$this->load->view('header', $data);
			$this->load->view('menu', $data);
			$this->load->view('admin_submenu', $data);
			$this->load->view('user_details', $data);
			$this->load->view('footer');
		} else {
			$this->index();
		}
	} 
	
	public function updateUser($id) {
		$userName = $this->input->post('username', TRUE);
		$firstName = $this->input->post('firstname', TRUE);
		$lastName = $this->input->post('lastname', TRUE);
		$parms = $this->input->post(NULL, TRUE);
		if (isset($this->session->userdata['permissions']['User Menu'])
				&& $this->session->userdata['permissions']['User Menu'] == 1) {
			$this->load->model('User_model');
			$result = $this->User_model->updateUser($id, $userName, $firstName, $lastName, $parms);
			if ($result['status'] == "OK") {
				$this->userDetails($id);
			} else {
				$data['errorMessage'] = $result['errorMessage'];
				$data['header'] = " - Update User";
				$data['menuItem'] = "admin";
				$this->load->view('header', $data);
				$this->load->view('menu', $data);
				$this->load->view('error_page', $data);
				$this->load->view('footer');
			}
		} else {
			$this->index();
		}
	}
}
	