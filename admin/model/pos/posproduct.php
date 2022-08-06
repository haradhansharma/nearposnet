<?php
class ModelPosPosProduct extends Controller {

	////sharma for pos edited to add posapp_id in full file where have product_id

	public function getProducts($data){
		$sql="select * from " . DB_PREFIX . "pos_product";
		if(isset($this->session->data['posuser'])){

		$sql .= " WHERE product_id<>0 AND posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql .= " WHERE product_id<>0 ";

	}

		if (!empty($data['search'])) {
			$sql .=" and name like '".$this->db->escape($data['search'])."%'";
		}

		$sort_data = array(
			'product_id'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY product_id";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
		 $sql .= " ASC";
		} else {
		 $sql .= " DESC";
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

 	public function getProduct($product_id){
 		if(isset($this->session->data['posuser'])){
		$sql="select * from " . DB_PREFIX . "pos_product where product_id='".(int)$product_id."' AND posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql="select * from " . DB_PREFIX . "pos_product where product_id='".(int)$product_id."' ";

	}
		$query = $this->db->query($sql);
		return $query->row;	
 	}

	public function getTotalProductss($data) {
		if(isset($this->session->data['posuser'])){
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_product where product_id<>0 AND posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_product where product_id<>0";

	}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
}
