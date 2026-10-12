<?php
class User_model extends CI_Model {

    var $id = '';
    var $firstName = '';
    var $lastName = '';

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }
    
    function loginUser($username, $password) {
		// Convert password to md5 hash
		// Password is now md5 hashed 
		//$password = md5($password);
		// check if the user info validates the db
		//echo($username." ".$password);
		$sql = "
			SELECT first_name
			      ,last_name
			      ,user_name
			      ,user_id 
			FROM users 
			WHERE user_name = ? 
			AND password_enc = ?
			AND activated = '1'
		";
		$query = $this->db->query($sql, array($username, $password));
		return $query->row();
	}
		
	function getUserPermissions($userId) {
		$sql = "
			SELECT p.permission_id
				  ,p.name
			      ,up.value
			FROM   permissions p
			      ,user_permissions up
			WHERE  p.permission_id = up.permission_id
			  AND  up.user_id = ?
			  AND  up.value = 1
		";
		$query = $this->db->query($sql, array($userId));
		return $query->result();
	}
	
	function getAllPermissions() {
		$sql = "
			SELECT p.permission_id
				  ,p.name
				  ,p.description
			FROM   permissions p
		";
		$query = $this->db->query($sql, array());
		return $query->result();
	}
	
	function getSeriesPermissions($userId) {
		$sql = "
			SELECT us.competition_series_id
			      ,cs.competition_series_name
			FROM   user_series us
			      ,competition_series cs
			WHERE  us.user_id = ?
			  AND  us.value = 1
			  AND  us.competition_series_id = cs.competition_series_id
		";
		$query = $this->db->query($sql, array($userId));
		return $query->result();
	}

	function getAllSeriesPermissions() {
		$sql = "
		SELECT s.competition_series_id as id
		      ,s.competition_series_name as name
		FROM   competition_series s
		";
		$query = $this->db->query($sql, array());
		return $query->result();
	}
	
	function getUserDetails($id) {
		$sql = "
			SELECT user_id
			,user_name
			,first_name
			,last_name
			,cr_date
			FROM   users
			WHERE  user_id = ?
			";
		$query = $this->db->query($sql, array($id));
		return $query->row();
	}
	
	function listUsers() {
		$sql = "
			SELECT u.user_id
			,u.first_name
			,u.last_name
			,u.cr_date
			FROM users u
			";
		$query = $this->db->query($sql, array());
		return $query->result();
	}
	
	function getUserPermissionSettings($id) {
		$sql = "
			SELECT p.permission_id
			,p.name
			,p.description
			,IFNULL(up.value,0) as value
			FROM   permissions p
			LEFT JOIN
			user_permissions up
			ON p.permission_id = up.permission_id
			AND up.user_id = ?
			ORDER BY name
			";
		$query = $this->db->query($sql, array($id));
		return $query->result();
	}
	
	function getUserSeriesSettings($id) {
		$sql = "
				SELECT cs.competition_series_id as id
				,cs.competition_series_name as name
				,IFNULL(us.value,0) as value
				FROM   competition_series cs
				LEFT JOIN
				user_series us
				ON us.competition_series_id = cs.competition_series_id
				AND us.user_id = ?
				ORDER BY name
				";
		$query = $this->db->query($sql, array($id));
		return $query->result();
	}
	
	function updateUser($id, $userName, $firstName, $lastName, $parms) {
		// get the permissions
		//print_r($parms);
		$perms = array();
		$series = array();
		foreach ($parms as $parmName => $parmValue) {
			if (substr($parmName,0,4) == "perm") {
				$perms[substr($parmName,4)] = $parmValue;
			} else if (substr($parmName,0,6) == "series") {
				$series[substr($parmName,6)] = $parmValue;
			}
		}
		//update base details
		$data = array(
				'user_name' => $userName
				,'first_name' => $firstName
				,'last_name' => $lastName
		);
		$this->db->update('users', $data, array('user_id' => $id));
		// update the permissions
		foreach ($perms as $permKey => $permValue) {
			// this is a MySQL specific bit of SQL - need to change it if we change 
			// databases
			$sql = "INSERT INTO user_permissions (user_id, permission_id, value)
				VALUES (?, ?, ?)
				ON DUPLICATE KEY UPDATE
				user_id = VALUES(user_id),
				permission_id = VALUES(permission_id),
				value = VALUES(value)";
			$query = $this->db->query($sql, array($id, $permKey, $permValue));
		}
		foreach ($series as $seriesKey => $seriesValue) {
			// this is a MySQL specific bit of SQL - need to change it if we change 
			// databases
			$sql = "INSERT INTO user_series (user_id, competition_series_id, value)
				VALUES (?, ?, ?)
				ON DUPLICATE KEY UPDATE
				user_id = VALUES(user_id),
				competition_series_id = VALUES(competition_series_id),
				value = VALUES(value)";
			$query = $this->db->query($sql, array($id, $seriesKey, $seriesValue));
		}
		return array('status' => "OK", 'ID' => $id);
	}

	function insertUser($userName, $firstName, $lastName, $password, $parms) {
		// get the permissions
		$perms = array();
		$series = array();
		foreach ($parms as $parmName => $parmValue) {
			if (substr($parmName,0,4) == "perm") {
				$perms[substr($parmName,4)] = $parmValue;
			} else if (substr($parmName,0,6) == "series") {
				$series[substr($parmName,6)] = $parmValue;
			}
		}
		//update base details
		// password should already be encrypted now
		$data = array(
				'user_name' => $userName
				,'first_name' => $firstName
				,'last_name' => $lastName
				,'password_enc' => $password
		);
		$this->db->insert('users', $data);
		if ($this->db->affected_rows() == 1) {
			$id = $this->db->insert_id();
			// update the permissions
			foreach ($perms as $permKey => $permValue) {
				// this is a MySQL specific bit of SQL - need to change it if we change
				// databases
				$sql = "INSERT INTO user_permissions (user_id, permission_id, value)
				VALUES (?, ?, ?)
				ON DUPLICATE KEY UPDATE
				user_id = VALUES(user_id),
				permission_id = VALUES(permission_id),
				value = VALUES(value)";
				$query = $this->db->query($sql, array($id, $permKey, $permValue));
			}
			foreach ($series as $seriesKey => $seriesValue) {
				// this is a MySQL specific bit of SQL - need to change it if we change
				// databases
				$sql = "INSERT INTO user_series (user_id, competition_series_id, value)
				VALUES (?, ?, ?)
				ON DUPLICATE KEY UPDATE
				user_id = VALUES(user_id),
				competition_series_id = VALUES(competition_series_id),
				value = VALUES(value)";
				$query = $this->db->query($sql, array($id, $seriesKey, $seriesValue));
			}
			return array('status' => "OK", 'ID' => $id);
		} else {
			return array('status' => "Error", 'errorMessage' => "Insert failed. Query was ".$this->db->last_query());
		}
	
	}
	
	// password should now be encrypted
	function setPassword($id, $password) {
			$data = array(
					'password_enc' => $password
					);
			$this->db->update('users', $data, array('user_id' => $id));
			if ($this->db->affected_rows() > 0) {
				return array('status' => "OK");
			} else {
				return array('status' => "Error");
			}
	}
	
	function checkPassword($id, $password) {
		// Convert password to md5 hash
		// password should now be hashed already
		//$password = md5($password);
		// check if the user info validates the db
		$sql = "
			SELECT first_name
			,last_name
			,user_name
			,user_id
			FROM users
			WHERE user_id = ?
			AND password_enc = ?
			AND activated = '1'
		";
		$query = $this->db->query($sql, array($id, $password));
		if ($query->num_rows() > 0) {
			return array('status' => "OK");
		} else {
			return array('status' => "Invalid");
		}
	}
	
}
