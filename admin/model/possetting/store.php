<?php
class ModelPossettingStore extends Model {
public function addStore($data) {
		$sql="INSERT INTO " . DB_PREFIX . "pos_store set
			name='".$this->db->escape($data['name'])."',
			phone='".$this->db->escape($data['phone'])."',
			location='".$this->db->escape($data['location'])."',
			status='".(int)$data['status']."', date_added=now()";
		$this->db->query($sql);
	}   
public function editStore($store_id,$data) {
		$sql="update " . DB_PREFIX . "pos_store set
			name='".$this->db->escape($data['name'])."',
			phone='".$this->db->escape($data['phone'])."',
			location='".$this->db->escape($data['location'])."',
			status='".(int)$data['status']."',date_modified=now() where store_id='".$store_id."'";

	 	$this->db->query($sql);
 }
public function deleteStores($store_id){		
		$sql="delete  from " . DB_PREFIX . "pos_store where store_id='".$store_id."'";
		$query=$this->db->query($sql);
 } 
public function getStore($store_id){
		$sql="select * from " . DB_PREFIX . "pos_store where store_id='".$store_id."'";
		$query=$this->db->query($sql);
		return $query->row;
 }
public function getStores($data){
	$sql="select * from " . DB_PREFIX . "pos_store where store_id<>0 ";
		
		$sort_data = array(
			'name',
			'status'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY name";
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
public function getTotalStoress() {
	 $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_store");
		return $query->row['total'];
	}
}
?>