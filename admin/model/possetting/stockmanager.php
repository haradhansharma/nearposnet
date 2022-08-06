<?php
class ModelPossettingStockmanager extends Model {
public function addStock($data) {
		$sql="INSERT INTO " . DB_PREFIX . "pos_stockmanager set
			store_id = '" . (int)$data['store_id'] . "',
			product_id = '" . (int)$data['product_id'] . "',
			quantity='".(int)$data['quantity']."', date_added=now()";
		$this->db->query($sql);
	}   
public function editStock($stock_id,$data) {
		$sql="update " . DB_PREFIX . "pos_stockmanager set
			store_id = '" . (int)$data['store_id'] . "',
			product_id = '" . (int)$data['product_id'] . "',
			quantity='".(int)$data['quantity']."',date_modified=now() where stock_id='".$stock_id."'";

	 	$this->db->query($sql);
 }
public function deleteStocks($stock_id){		
		$sql="delete  from " . DB_PREFIX . "pos_stockmanager where stock_id='".$stock_id."'";
		$query=$this->db->query($sql);
 } 
public function getStock($stock_id){
		$sql="select * from " . DB_PREFIX . "pos_stockmanager where stock_id='".$stock_id."'";
		$query=$this->db->query($sql);
		return $query->row;
 }
public function getStocks($data){
	$sql="select * from " . DB_PREFIX . "pos_stockmanager where stock_id<>0 ";
		if (isset($data['filter_store']))
		{
		 $sql .=" and store_id like '".$this->db->escape($data['filter_store'])."%'";
		}
	
		if (isset($data['filter_product']))
		{
		 $sql .=" and product_id like '".$this->db->escape($data['filter_product'])."%'";
		}
		
		$sort_data = array(
			'product_id',
			'store_id',
			'status'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY store_id";
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
public function getTotalStockss($data) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_stockmanager where stock_id<>0";
		if (isset($data['filter_store']))
		{
		 $sql .=" and store_id like '".$this->db->escape($data['filter_store'])."%'";
		}
	
		if (isset($data['filter_product']))
		{
		 $sql .=" and product_id like '".$this->db->escape($data['filter_product'])."%'";
		}	
	
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
}
?>