<?php
class ModelPossettingStockOut extends Model {

	////sharma for pos edited to add posapp_id in full file where have product_id

	public function getOptionValue($option_value_id){
		$sql="select * from " . DB_PREFIX . "option_value_description WHERE language_id = '".(int)$this->config->get('config_language_id')."' and option_value_id='".(int)$option_value_id."'";
		
		$query = $this->db->query($sql);
		return $query->row;	
 	}

	public function getProductOptionValue($product_option_value_id){
		//$sql="select * from " . DB_PREFIX . "product_option_value WHERE product_option_value_id='".(int)$product_option_value_id."'";
		$sql="SELECT * FROM `" . DB_PREFIX . "product_option_value` pov LEFT JOIN `" . DB_PREFIX . "option_value` ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN `" . DB_PREFIX . "option_value_description` ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);
		return $query->row;	
 	}
 	
	public function getStockinOptionUpcs($data=array()){
		$sql="select * from " . DB_PREFIX . "product_option_value_upc WHERE product_option_value_id<>0";
		
		if (!empty($data['filter_search'])) {
			$sql .= " AND upc='" . $this->db->escape($data['filter_search']) . "'";
		}
		
	    $sort_data = array(
			'product_option_value_id',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY product_option_value_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
				
		$query = $this->db->query($sql);
		return $query->rows;	
 	}
	public function getStockinUpcs($data=array()){
		if(isset($this->session->data['posuser'])){		
		$sql="select * from " . DB_PREFIX . "product WHERE product_id<>0 AND posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql="select * from " . DB_PREFIX . "product WHERE product_id<>0";


	}
		
		if (!empty($data['filter_search'])) {
			$sql .= " AND upc='" . $this->db->escape($data['filter_search']) . "'";
		}
		
	    $sort_data = array(
			'product_id',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY product_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
				
		$query = $this->db->query($sql);
		return $query->rows;	
 	}

 	public function getStockinProductOption($option_id){
	    $options_data = array();
	    $product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE o.option_id = '" . (int)$option_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

	    foreach ($product_option_query->rows as $option) {
	      $options_data[] = array(
	      	'name' 		=> $option['name']
	      );
	    }
    	return $options_data;
  	}

  	public function getStockinProduct($product_id){
	    $products_data = array();
	    if(isset($this->session->data['posuser'])){	
	    $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.posapp_id='". (int)$this->session->data['posapp_id'] ."'");
	}else{
		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ");

	}

	    foreach ($product_query->rows as $product) {
	      $products_data[] = array(
	      	'product_id' => $product['product_id'],
	      	'name' 		 => $product['name'],
	      ); 
	    }
    	return $products_data;
  	}

	/* 27 -12-2019 */
	  	public function updateOpionQty($data){
	    if (isset($data['stockoutproduct'])) {
	      foreach ($data['stockoutproduct'] as $key => $stockout) {
	      	if(empty($stockout['product_option_value_id'])){
			 $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$stockout['quantity'] . "),date_modified = NOW() where product_id='".(int)$stockout['product_id']."' "); 
			 
			}
			else
			{
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$stockout['quantity'] . ") where product_id='".(int)$stockout['product_id']."' and product_option_value_id='".(int)$stockout['product_option_value_id']."'");  
			}			
	         	         	                
	      }
	    }

	}
	/* 27 -12-2019 */
	
}