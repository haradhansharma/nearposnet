<?php

class ModelAccountPosapp extends Model { 

  public function addPosapp($customer_id, $data, $datas) {

    $this->db->query("INSERT INTO " . DB_PREFIX . "pos_application SET customer_id = '" . (int)$customer_id . "', customer_group_id = '" . (int)$this->config->get('module_posapp_customer_group') . "', username = '" . $this->db->escape($data['username']) . "', store_id = '" . (int)$this->db->escape($data['store_id']) . "', posname = '" . $this->db->escape($data['posname']) . "', firstname = '". $this->db->escape($data['firstname'])  . "', lastname = '".$this->db->escape($data['lastname']) . "',  email = '" . $this->db->escape($data['email']) . "',  licence_validaty = '" . $this->db->escape($data['licence_validaty']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', address = '" . $this->db->escape($data['address']) . "', city = '" . $this->db->escape($data['city']) . "', country_id = '" . $this->db->escape($data['country_id']) . "', zone_id = '" . $this->db->escape($data['zone_id']) . "', latitude = '" . $this->db->escape($data['latitude']) . "', longitude = '" . $this->db->escape($data['longitude']) . "', cost = '" . (int)$this->db->escape($data['cost']) . "', alloweddistance = '" . (int)$this->db->escape($data['alloweddistance']) . "', code = '" . $this->db->escape($data['code']) . "', status = '" . (int)$this->db->escape($datas['status']) . "', agent_code = '" . $this->db->escape($data['agent_code']) . "', commission = '" . $this->db->escape($data['commission']) . "', commission_value = '" . (int)$this->db->escape($datas['commission_value']) . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "',  date_added = NOW()");

    $posapp_id = $this->db->getLastId(); 

    if($this->customer->isLogged()){
      $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET firstname = '". $this->db->escape($this->customer->getFirstName())  . "', lastname = '".$this->db->escape($this->customer->getLastName())  . "'  WHERE  posapp_id = '".(int)$posapp_id."'" );
    }
 
    if (isset($data['posapp_category'])) {
      foreach ($data['posapp_category'] as $category_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "posapp_to_category SET posapp_id = '" . (int)$posapp_id . "', category_id = '" . (int)$category_id . "'");
      }
    } 

    
   $directory = DIR_IMAGE ;

    $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET frontimage = '". (int)$posapp_id ."' '_frontimage.' '". $this->db->escape($datas['frontimage_extension'])  . "', tradelicenceimage = '". (int)$posapp_id ."' '_tradelicenceimage.' '". $this->db->escape($datas['tradelicenceimage_extension'])  . "'  WHERE  posapp_id = '".(int)$posapp_id."'" );

    move_uploaded_file($datas['frontimage_tmp'], $directory . (int)$posapp_id .'_frontimage' . '.' . $this->db->escape($datas['frontimage_extension']) );
    move_uploaded_file($datas['tradelicenceimage_tmp'], $directory . (int)$posapp_id .'_tradelicenceimage' . '.' . $this->db->escape($datas['tradelicenceimage_extension']) );



    // SEO URL
    $posname = $data['posname'];    
    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$this->db->escape($data['store_id']) . "', language_id = '1' , query = 'posapp_id=" . (int)$posapp_id . "', keyword = '" . $this->db->escape(strtolower(str_replace(" ", "-",$posname))) . "'");

    
    return $posapp_id; 
  }

  public function editPosapp($posapp_id, $data, $datas){
   $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET  username = '" . $this->db->escape($data['username']) . "', store_id = '" . (int)$this->db->escape($data['store_id']) . "', posname = '" . $this->db->escape($data['posname']) . "',  email = '" . $this->db->escape($data['email']) . "',  licence_validaty = '" . $this->db->escape($data['licence_validaty']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', address = '" . $this->db->escape($data['address']) . "', city = '" . $this->db->escape($data['city']) . "', country_id = '" . $this->db->escape($data['country_id']) . "', zone_id = '" . $this->db->escape($data['zone_id']) . "', latitude = '" . $this->db->escape($data['latitude']) . "', longitude = '" . $this->db->escape($data['longitude']) . "', cost = '" . (int)$this->db->escape($data['cost']) . "', alloweddistance = '" . (int)$this->db->escape($data['alloweddistance']) . "', code = '" . $this->db->escape($data['code']) . "', status = '" . (int)$this->db->escape($datas['status']) . "', commission = '" . $this->db->escape($data['commission']) . "', commission_value = '" . (int)$this->db->escape($datas['commission_value']) . "',   ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "',  date_added = NOW() WHERE  posapp_id = '".(int)$posapp_id."' ");

   if ($data['password']) {
      $this->db->query("UPDATE `" . DB_PREFIX . "pos_application` SET 
      salt = '" . $this->db->escape($salt = token(9)) . "',
      password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE posapp_id = '" . (int)$posapp_id . "'");
    }
   
   if(!$posapp_id){
   $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET firstname = '". $this->db->escape($this->customer->getFirstName())  . "', lastname = '".$this->db->escape($this->customer->getLastName())  . "'  WHERE  posapp_id = '".(int)$posapp_id."'" );
   }

   $this->db->query("DELETE FROM " . DB_PREFIX . "posapp_to_category WHERE posapp_id = '" . (int)$posapp_id . "'");

   if (isset($data['posapp_category'])) {
      foreach ($data['posapp_category'] as $category_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "posapp_to_category SET posapp_id = '" . (int)$posapp_id . "', category_id = '" . (int)$category_id . "'");
      }
    } 
   
   $directory = DIR_IMAGE ;
   if (isset($datas['frontimage_extension'])) {     
    $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET frontimage = '". (int)$posapp_id ."' '_frontimage.' '". $this->db->escape($datas['frontimage_extension'])  . "'  WHERE customer_id = '" . (int)$this->customer->getId() . "' AND customer_group_id = '" . (int)$this->config->get('module_posapp_customer_group') . "' AND posapp_id = '".(int)$posapp_id."'" );
    unlink($datas['frontimage_tmp'], $directory . (int)$posapp_id .'_frontimage' . '.' . $this->db->escape($datas['frontimage_extension']) );  
    move_uploaded_file($datas['frontimage_tmp'], $directory . (int)$posapp_id .'_frontimage' . '.' . $this->db->escape($datas['frontimage_extension']) );
    }
    if (isset($datas['tradelicenceimage_extension'])) {    
    $this->db->query("UPDATE " . DB_PREFIX . "pos_application SET tradelicenceimage = '" . (int)$posapp_id ."' '_tradelicenceimage.' '". $this->db->escape($datas['tradelicenceimage_extension'])  . "'  WHERE customer_id = '" . (int)$this->customer->getId() . "' AND customer_group_id = '" . (int)$this->config->get('module_posapp_customer_group') . "' AND posapp_id = '".(int)$posapp_id."'" );  
    unlink($datas['tradelicenceimage_tmp'], $directory . (int)$posapp_id .'_tradelicenceimage' . '.' . $this->db->escape($datas['tradelicenceimage_extension']) );    
    move_uploaded_file($datas['tradelicenceimage_tmp'], $directory . (int)$posapp_id .'_tradelicenceimage' . '.' . $this->db->escape($datas['tradelicenceimage_extension']) );
  }
    // SEO URL
    $posname = $data['posname'];    
    $this->db->query("UPDATE " . DB_PREFIX . "seo_url SET keyword = '" . $this->db->escape(strtolower(str_replace(" ", "-",$posname))) . "' WHERE query = 'posapp_id=" . (int)$posapp_id . "'");   

  }

  public function getUser($posapp_id) {
    $sql="select * from " . DB_PREFIX . "pos_user where posapp_id='".$posapp_id."'";
    $query=$this->db->query($sql);
    return $query->row;
  }

  public function editUser($posapp_id, $data, $datas) {
    $this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET    
    username = '" . $this->db->escape($data['username']) . "',
    store_id = '" . (int)$this->db->escape($data['store_id']) . "',
    firstname = '". $this->db->escape($this->customer->getFirstName())  . "', 
    lastname = '".$this->db->escape($this->customer->getLastName())  . "',
    email = '" . $this->db->escape($data['email']) . "',
    image = '" . $this->db->escape($datas['f_image']) . "',
    status = '" . (int)$this->db->escape($datas['status']) . "',
    commission = '".$this->db->escape($data['commission'])."',
    commission_value='".(int)$datas['commission_value']."' WHERE posapp_id = '" . (int)$posapp_id . "'");

    if ($data['password']) {
      $this->db->query("UPDATE `" . DB_PREFIX . "pos_user` SET 
      salt = '" . $this->db->escape($salt = token(9)) . "',
      password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE posapp_id = '" . (int)$posapp_id . "'");
    }

  }

  public function getTotalPossappsByEmail($email) {
    $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "pos_application` 
    WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

    return $query->row;
  }

  public function getUserNameByUsername($username) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_application` 
    WHERE username = '" . $this->db->escape($username) . "'");

    return $query->row;
  }
  public function getUserNameByUsername2($username) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_user` 
    WHERE username = '" . $this->db->escape($username) . "'");

    return $query->row;
  }

  public function getPosappCategories($posapp_id) {
    $product_category_data = array();

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "posapp_to_category WHERE posapp_id = '" . (int)$posapp_id . "'");

    foreach ($query->rows as $result) {
      $product_category_data[] = $result['category_id'];
    }

    return $product_category_data;
  }
  
  public function getPosapps($data = array()) {
    $sql = "SELECT * FROM `" . DB_PREFIX . "pos_application` WHERE customer_id = '" . (int)$this->customer->getId() . "'";

    $sort_data = array(
      'posapp_id',      
      'date_added'
    );

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
      $sql .= " ORDER BY " . $data['sort'];
    } else {
      $sql .= " ORDER BY date_added";
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

      $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    $query = $this->db->query($sql);

    return $query->rows;
  }

  public function getTotalPosapps() {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pos_application` WHERE customer_id = '" . (int)$this->customer->getId() . "'");

    return $query->row['total'];
  }
   public function getNotApproved() {
    $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "pos_application` WHERE LOWER(status) = 0 AND customer_id = '" . (int)$this->customer->getId() . "'");

    return $query->row['total'];
  }

  public function getHold() {
    $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "pos_application` WHERE LOWER(status) = 2 AND customer_id = '" . (int)$this->customer->getId() . "'");

    return $query->row['total'];
  }
  public function getApproved() {
    $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "pos_application` WHERE LOWER(status) = 1 AND customer_id = '" . (int)$this->customer->getId() . "'");

    return $query->row['total'];
  }
  public function getTotalSaleOfPos(){   

    $query = $this->db->query("SELECT `posapp_id` from `" . DB_PREFIX . "pos_application` WHERE customer_id = '" . (int)$this->customer->getId() . "' ");
    if($query->num_rows){
    $implode = array();
    foreach ($query->rows as $posapp_ids) {
      $implode[] = $posapp_ids['posapp_id'];
    }     

      $total_sale = $this->db->query("SELECT sum(total) as totals FROM `" . DB_PREFIX . "order` WHERE `posapp_id` IN (" . implode(',',  $implode) . ") AND order_status_id > '0' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
  
     return $total_sale->row['totals'];
   }else{
    return false;

   }
  
  }
  public function getSaleOrdersNumber(){   

    $query = $this->db->query("SELECT `posapp_id` from `" . DB_PREFIX . "pos_application` WHERE customer_id = '" . (int)$this->customer->getId() . "' ");

    $implode = array();

    foreach ($query->rows as $posapp_ids) {

      $implode[] = $posapp_ids['posapp_id'];
    }       

      $total_sale = $this->db->query("SELECT count(*) as totals FROM `" . DB_PREFIX . "order` WHERE `posapp_id` IN (" . implode(',',  $implode) . ") AND order_status_id > '0' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
  
     return $total_sale->row['totals'];
  
  }
  public function getPosappById($posapp_id) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_application` WHERE posapp_id = '" . (int)$posapp_id . "'");

    return $query->row;
  }



}