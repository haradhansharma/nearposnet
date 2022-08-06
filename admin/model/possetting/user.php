<?php
class ModelPossettingUser extends Model {
	public function addUser($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "pos_user` SET 
		username = '" . $this->db->escape($data['username']) . "',
		store_id = '" . (int)$data['store_id'] . "',user_store_id = '" . (int)$data['user_store_id'] . "',
		salt = '" . $this->db->escape($salt = token(9)) . "',
		password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "',
		firstname = '" . $this->db->escape($data['firstname']) . "',
		lastname = '" . $this->db->escape($data['lastname']) . "',
		email = '" . $this->db->escape($data['email']) . "',
		image = '" . $this->db->escape($data['image']) . "',
		status = '" . (int)$data['status'] . "',
		commission = '".$this->db->escape($data['commission'])."',
		commission_value='".(int)$data['commission_value']."',
		date_added = NOW()");
	
		return $this->db->getLastId();
	}

	public function editUser($user_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
		username = '" . $this->db->escape($data['username']) . "',
		store_id = '" . (int)$data['store_id'] . "',user_store_id = '" . (int)$data['user_store_id'] . "',
		firstname = '" . $this->db->escape($data['firstname']) . "',
		lastname = '" . $this->db->escape($data['lastname']) . "',
		email = '" . $this->db->escape($data['email']) . "',
		image = '" . $this->db->escape($data['image']) . "',
		status = '" . (int)$data['status'] . "',
		commission = '".$this->db->escape($data['commission'])."',
		commission_value='".(int)$data['commission_value']."' WHERE user_id = '" . (int)$user_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
			salt = '" . $this->db->escape($salt = token(9)) . "',
			password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE user_id = '" . (int)$user_id . "'");
		}
	}

	public function editPassword($user_id, $password) {
		$this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
		salt = '" . $this->db->escape($salt = token(9)) . "',
		password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "',
		code = '' WHERE user_id = '" . (int)$user_id . "'");
	}

	public function editCode($email, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
		code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function deleteUser($user_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pos_user` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function getUser($user_id) {
		$sql="select * from " . DB_PREFIX . "pos_user where user_id='".$user_id."'";
		$query=$this->db->query($sql);
		return $query->row;
	}

	public function getUserByUsername($username) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_user` 
		WHERE username = '" . $this->db->escape($username) . "'");

		return $query->row;
	}

	public function getUserByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "pos_user` 
		WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getUserByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_user` 
		WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}

	public function getUsers($data) {
		$sql="select * from " . DB_PREFIX . "pos_user where user_id<>0";
		if (isset($data['filter_store']))
		{
		 $sql .=" and store_id like '".$this->db->escape($data['filter_store'])."%'";
		}
		$sort_data = array(
			'username',
			'status',
			'store_id',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY username";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		if (isset($data['start']) || isset($data['limit'])) {
		 if ($data['start'] < 0) {
		 $data['start'] = 0;
		 }
		 if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalUsersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pos_user` 
		WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}
	
		public function getTotalUsers($data) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_user where user_id<>0";
		if (isset($data['filter_store']))
		{
		 $sql .=" and store_id like '".$this->db->escape($data['filter_store'])."%'";
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
}