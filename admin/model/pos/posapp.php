<?php
class ModelPosPosapp extends Model {
	public function addUser($posapp_id, $data, $datas) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "pos_user` SET 
		username = '" . $this->db->escape($data['username']) . "',
		store_id = '" . (int)$this->db->escape($data['store_id']) . "',
		firstname = '" . $this->db->escape($data['firstname']) . "',
		lastname = '" . $this->db->escape($data['lastname']) . "',
		email = '" . $this->db->escape($data['email']) . "',
		image = '" . $this->db->escape($datas['f_image']) . "',
		status = '" . (int)$this->db->escape($data['status']) . "',
		commission = '".$this->db->escape($data['commission'])."',
		commission_value='".(int)$data['commission_value']."',
		date_added = NOW()");

		$user_id = $this->db->getLastId();

		$this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET posapp_id = '".$posapp_id."' WHERE user_id = '". $user_id ."' ");

		if ($datas['password'] && $datas['salt'] ) {
      $this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
      salt = '" . $this->db->escape($datas['salt']) . "',
      password = '" . $this->db->escape($datas['password']) . "' WHERE posapp_id = '" . (int)$posapp_id . "'");
    }elseif($data['password']){
      $this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
      salt = '" . $this->db->escape($salt = token(9)) . "',
      password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE posapp_id = '" . (int)$posapp_id . "'");

    }
	
		return $this->db->getLastId();
	}
	public function editUser($posapp_id, $data, $datas) {
		$this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 		
		username = '" . $this->db->escape($data['username']) . "',
		store_id = '" . (int)$this->db->escape($data['store_id']) . "',
		firstname = '" . $this->db->escape($data['firstname']) . "',
		lastname = '" . $this->db->escape($data['lastname']) . "',
		email = '" . $this->db->escape($data['email']) . "',
		image = '" . $this->db->escape($datas['f_image']) . "',
		status = '" . (int)$this->db->escape($data['status']) . "',
		commission = '".$this->db->escape($data['commission'])."',
		commission_value='".(int)$data['commission_value']."' WHERE posapp_id = '" . (int)$posapp_id . "'");

		if ($data['password']) {
      $this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
      salt = '" . $this->db->escape($salt = token(9)) . "',
      password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE posapp_id = '" . (int)$posapp_id . "'");
    }




	}

	public function getPosappCategories($posapp_id) {
    $product_category_data = array();

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "posapp_to_category WHERE posapp_id = '" . (int)$posapp_id . "'");

    foreach ($query->rows as $result) {
      $product_category_data[] = $result['category_id'];
    }

    return $product_category_data;
  }

	public function editPosapp($posapp_id, $data, $datas) {
		$this->db->query("UPDATE " . DB_PREFIX . "pos_application SET  username = '" . $this->db->escape($data['username']) . "', store_id = '" . (int)$this->db->escape($data['store_id']) . "', posname = '" . $this->db->escape($data['posname']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "',  licence_validaty = '" . $this->db->escape($data['licence_validaty']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', address = '" . $this->db->escape($data['address']) . "', city = '" . $this->db->escape($data['city']) . "', country_id = '" . $this->db->escape($data['country_id']) . "', zone_id = '" . $this->db->escape($data['zone_id']) . "', latitude = '" . $this->db->escape($data['latitude']) . "', longitude = '" . $this->db->escape($data['longitude']) . "', cost = '" . (int)$this->db->escape($data['cost']) . "', alloweddistance = '" . (int)$this->db->escape($data['alloweddistance']) . "', code = '" . $this->db->escape($data['code']) . "', status = '" . (int)$this->db->escape($data['status']) . "', commission = '" . $this->db->escape($data['commission']) . "', commission_value = '" . (int)$this->config->get('module_posapp_commission_value') . "',   ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "',  date_added = NOW() WHERE  posapp_id = '".(int)$posapp_id."' ");

   if ($data['password']) {
      $this->db->query("UPDATE `" . DB_PREFIX . "pos_application` SET 
      salt = '" . $this->db->escape($salt = token(9)) . "',
      password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE posapp_id = '" . (int)$posapp_id . "'");
    }
 


   $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET firstname = '".  $this->db->escape($data['firstname'])  . "', lastname = '". $this->db->escape($data['lastname'])  . "'  WHERE  posapp_id = '".(int)$posapp_id."'" );

   $this->db->query("DELETE FROM " . DB_PREFIX . "posapp_to_category WHERE posapp_id = '" . (int)$posapp_id . "'");

   if (isset($data['posapp_category'])) {
      foreach ($data['posapp_category'] as $category_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "posapp_to_category SET posapp_id = '" . (int)$posapp_id . "', category_id = '" . (int)$category_id . "'");
      }
    } 
   
  
   
   $directory = DIR_IMAGE ; 
   if (isset($datas['frontimage_extension']) ) {     
    $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET frontimage = '" . (int)$posapp_id ."' '_frontimage.' '". $this->db->escape($datas['frontimage_extension'])  . "'  WHERE  posapp_id = '".(int)$posapp_id."'" );
    unlink($datas['frontimage_tmp'], $directory . (int)$posapp_id .'_frontimage' . '.' . $this->db->escape($datas['frontimage_extension']) );  
    move_uploaded_file($datas['frontimage_tmp'], $directory . (int)$posapp_id .'_frontimage' . '.' . $this->db->escape($datas['frontimage_extension']) );
    }
    if (isset($datas['tradelicenceimage_extension'])) {    
    $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET tradelicenceimage = '" . (int)$posapp_id ."' '_tradelicenceimage.' '". $this->db->escape($datas['tradelicenceimage_extension'])  . "'  WHERE  posapp_id = '".(int)$posapp_id."'" );  
    unlink($datas['tradelicenceimage_tmp'], $directory . (int)$posapp_id .'_tradelicenceimage' . '.' . $this->db->escape($datas['tradelicenceimage_extension']) );    
    move_uploaded_file($datas['tradelicenceimage_tmp'], $directory . (int)$posapp_id .'_tradelicenceimage' . '.' . $this->db->escape($datas['tradelicenceimage_extension']) );
  }
    // SEO URL
    $posname = $data['posname'];    
    $this->db->query("UPDATE " . DB_PREFIX . "seo_url SET keyword = '" . $this->db->escape(strtolower(str_replace(" ", "-",$posname))) . "' WHERE query = 'posapp_id=" . (int)$posapp_id . "'");
	}

	// public function editPassword($user_id, $password) {
	// 	$this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
	// 	salt = '" . $this->db->escape($salt = token(9)) . "',
	// 	password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "',
	// 	code = '' WHERE user_id = '" . (int)$user_id . "'");
	// }

	// public function editCode($email, $code) {
	// 	$this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
	// 	code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	// }

	public function deletePosapp($posapp_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pos_application` WHERE posapp_id = '" . (int)$posapp_id . "'");
	}

	public function deleteUser($posapp_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pos_user` WHERE posapp_id = '" . (int)$posapp_id . "'");
	}
	// public function deleteposapp($posapp_id) {
	// 	$this->db->query("DELETE FROM `" . DB_PREFIX . "pos_application` WHERE posapp_id = '" . (int)$posapp_id . "'");
	// }

	public function getUser($posapp_id) {
		$sql="select * from " . DB_PREFIX . "pos_user where posapp_id='".$posapp_id."'";
		$query=$this->db->query($sql);
		return $query->row;
	}

	public function getPosappidByUserId($user_id) {
		$sql="select * from " . DB_PREFIX . "pos_user where user_id='".$user_id."'";
		$query=$this->db->query($sql);
		if($query->num_rows){
			return $query->row['posapp_id'];
		}else{
			return false;
		}
		
	}

	public function getPosappById($posapp_id) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_application` WHERE posapp_id = '" . (int)$posapp_id . "'");

    return $query->row;
  }

  public function getNotApproved() {
    $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "pos_application` WHERE LOWER(status) = 0 ");

    return $query->row['total'];
  }

  public function getHold() {
    $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "pos_application` WHERE LOWER(status) = 2 ");

    return $query->row['total'];
  }
  public function getApproved() {
    $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "pos_application` WHERE LOWER(status) = 1 ");

    return $query->row['total'];
  }

	public function getUserNameByUsername($username) {
    
    	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_user` 
		WHERE username = '" . $this->db->escape($username) . "'");

		return $query->row;   
  }
  public function getUserNameByUsername2($username) {
    
    	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_application` 
		WHERE username = '" . $this->db->escape($username) . "'");

		return $query->row;   
  }


	

	// public function getUserByCode($code) {
	// 	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_user` 
	// 	WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

	// 	return $query->row;
	// }

	public function getPosapps($data) {
		$sql="select * from " . DB_PREFIX . "pos_application where posapp_id<>0";
		if (isset($data['filter_store']))
		{
		 $sql .=" and store_id like '".$this->db->escape($data['filter_store'])."%'";
		}
		$sort_data = array(
			'posname',
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

	// public function getTotalUsersByEmail($email) {
	// 	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pos_user` 
	// 	WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

	// 	return $query->row['total'];
	// }
	public function getTotalPossappsByEmail($email) {    

    	$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "pos_user` 
		WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;   
  }
	
		public function getTotalPosapps($data) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_application where posapp_id<>0";
		if (isset($data['filter_store']))
		{
		 $sql .=" and store_id like '".$this->db->escape($data['filter_store'])."%'";
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
}