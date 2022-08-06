<?php
class ModelPossettingProduct extends Model {
public function addProduct($data) {
		
		if(isset($this->session->data['store_id']))
		{
			$store_id=$this->session->data['store_id'];
		}
		else if(isset($data['store_id'] ))
		{
			$store_id=$data['store_id'];
		}
		else
		{
			$store_id=0;
		}
		if(!isset($data['status']))
		{
		$data['status']=1;
		}
		if(!isset($data['image']))
		{
		$data['image']='';
		}
		if(!isset($data['price']))
		{
		$data['price']='';
		}

		////sharma for pos
		if(isset($this->session->data['posapp_id']))
		{
			$posapp_id= $this->session->data['posapp_id'] ;
		}else{
			$posapp_id= 0 ;

		}
          
/////sharma for pos edited tp add posapp_id
		
		$sql="INSERT INTO " . DB_PREFIX . "pos_product set
		    posapp_id='".(int)$posapp_id."',
			name='".$this->db->escape($data['name'])."',
			model='".$this->db->escape($data['model'])."',
			store_id = '" . (int)$store_id. "',
			price = '" . (float)$data['price']. "',
			shipping = '" . (int)$data['shipping']. "',
			tax_class_id = '" . (int)$data['tax_class_id']. "',
			quantity = '" . (int)$data['quantity']. "',
			image = '" . $this->db->escape($data['image']) . "',
			user_id='".$this->user->getId()."',
			status='".(int)$data['status']."', date_added=now()";
		$this->db->query($sql);
		$cproduct_id = $this->db->getLastId();
		return $cproduct_id;
	}   
	public function editProduct($product_id,$data) {		

		////edited for posapp_id
		$sql="update " . DB_PREFIX . "pos_product set
		    posapp_id='". (int)$this->cart->getProductPosPosapp($product_id) ."',
			name='".$this->db->escape($data['name'])."',
			store_id = '" . (int)$data['store_id'] . "',
			model='".$this->db->escape($data['model'])."',
			price = '" . (float)$data['price']. "',
			shipping = '" . (int)$data['shipping']. "',
			tax_class_id = '" . (int)$data['tax_class_id']. "',
			image = '" . $this->db->escape($data['image']) . "',
			quantity='".(int)$data['quantity']."',
			status='".(int)$data['status']."',date_modified=now() where product_id='".$product_id."'";

	 	$this->db->query($sql);
 	}
	public function deleteProducts($product_id){		
		$sql="delete  from " . DB_PREFIX . "pos_product where product_id='".$product_id."'";
		$query=$this->db->query($sql);
 	} 
	public function getProduct($product_id){		
		////sharma for pos edited to add posapp_id
		if(isset($this->session->data['posuser'])){
		$sql="select * from " . DB_PREFIX . "pos_product where product_id='".$product_id."' AND posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql="select * from " . DB_PREFIX . "pos_product where product_id='".$product_id."' ";


	}
		$query=$this->db->query($sql);
		return $query->row;
 	}
	public function getProducts($data){		  
		////sharma for pos edited to add posapp_id
		if(isset($this->session->data['posuser'])){
		$sql="select * from " . DB_PREFIX . "pos_product where product_id<>0 AND posapp_id='". (int)$this->session->data['posapp_id'] ."' ";
	    }else{
	    	$sql="select * from " . DB_PREFIX . "pos_product where product_id<>0 ";

	    }
		
		if (isset($data['filter_store']))
		{
		 $sql .=" and store_id like '".$this->db->escape($data['filter_store'])."%'";
		}
		
		if (isset($data['filter_name']))
		{
		 $sql .=" and name like '".$this->db->escape($data['filter_name'])."%'";
		}
	
		$sort_data = array(
			'name',
			'store_id',
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
	public function getTotalProductss($data) {
		
		////sharma for pos edited to add posapp_id
		if(isset($this->session->data['posuser'])){
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_product where product_id<>0 AND posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_product where product_id<>0 ";

	}
		if (isset($data['filter_store']))
		{
		 $sql .=" and store_id like '".$this->db->escape($data['filter_store'])."%'";
		}
	
		if (isset($data['filter_name']))
		{
		 $sql .=" and name like '".$this->db->escape($data['filter_name'])."%'";
		}
	
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
}
?>