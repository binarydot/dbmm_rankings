<?php

namespace App\Models;

use App\Libraries\LegacyModel as CI_Model;
class User extends CI_Model {

    var $id = '';
    var $firstName = '';
    var $lastName = '';

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }
    
    function loginUser($username, $password) {
		// Convert password to md5 hash
		$password = md5($password);
		// check if the user info validates the db
		$sql = "
			SELECT * 
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
		";
		$query = $this->db->query($sql, array($userId));
		return $query->result();
	}
	
	function getSeriesPermissions($userId) {
		$sql = "
			SELECT us.competition_series_id
			FROM   user_series us
			WHERE  us.user_id = ?
			  AND  us.value = 1
		";
		$query = $this->db->query($sql, array($userId));
		return $query->result();
	}
}
