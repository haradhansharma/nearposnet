<?php
class ModelPossettingBarcode extends Model {
	////sharma for pos edited to add posapp_id in full file where have product_id
	public function getProduct($product_id){
	if(isset($this->session->data['posuser'])){		
		$sql="select * from " . DB_PREFIX . "product where product_id='".$product_id."' AND posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql="select * from " . DB_PREFIX . "product where product_id='".$product_id."' ";
	}
		$query = $this->db->query($sql);
		return $query->row;
	} 
	
	public function getProductBarcodes($data=array()){

		if(isset($this->session->data['posuser'])){	
		$sql="select * from " . DB_PREFIX . "product p LEFT JOIN " .DB_PREFIX. "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."' AND p.product_id<>0 AND p.posapp_id='". (int)$this->session->data['posapp_id'] ."' ";
	}else{
		$sql="select * from " . DB_PREFIX . "product p LEFT JOIN " .DB_PREFIX. "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."' AND p.product_id<>0 ";
	}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		$sort_data = array(
			'p.product_id',
			'p.model',
			'p.quantity',
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY p.product_id";
		}
		if (isset($data['order']) && ($data['order'] == 'ASC')) {
		 $sql .= " ASC";
		} else {
		 $sql .= " DESC";
		}
		
		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		
		$query = $this->db->query($sql);
		return $query->rows;	
 	}

	public function getTotalBarcodes($data=array()) {
		if(isset($this->session->data['posuser'])){	
		$sql="SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " .DB_PREFIX. "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."' AND p.product_id<>0 AND p.posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql="SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " .DB_PREFIX. "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."' AND p.product_id<>0";

	}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}
		
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_id'    		=> $product_option['product_id'],
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductOptionUpc($product_option_value_id){		
		$sql="select * from " . DB_PREFIX . "product_option_value_upc where product_option_value_id='".$product_option_value_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	} 

	public function updateOpionQTY($data) {
		if (isset($data['product'])) {
			foreach ($data['product'] as $product_id => $product_options) {
				
				if(!empty($product_options['option'])){
					$qty=0;
					foreach ($product_options['option'] as $product_option_value_id => $product_option) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$product_option['quantity'] . "' where product_id='".(int)$product_id."' and product_option_value_id='".(int)$product_option_value_id."'");				
						
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value_upc WHERE product_id = '" . (int)$product_id . "' and product_option_value_id='".(int)$product_option_value_id."'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value_upc SET product_option_value_id='".(int)$product_option_value_id."',product_id='".(int)$product_id."',upc='".$this->db->escape($product_option['upc'])."'");
						$qty +=$product_option['quantity'];
					}
					if(!empty($qty)){
					$this->db->query("UPDATE " . DB_PREFIX . "product SET upc = '" . $this->db->escape($product_options['upc']) . "',quantity='".$qty."' where product_id = '" . (int)$product_id . "'");
					}
				}
				else {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET upc = '" . $this->db->escape($product_options['upc']) . "',quantity='".(int)$product_options['quantity']."' where product_id = '" . (int)$product_id . "'");
				}


			}
		}

	}

	public function getProductUpc($product_id){	
	if(isset($this->session->data['posuser'])){		
		$sql="select * from " . DB_PREFIX . "product where product_id='".$product_id."' AND posapp_id='". (int)$this->session->data['posapp_id'] ."'";
	}else{
		$sql="select * from " . DB_PREFIX . "product where product_id='".$product_id."'";

	}
		$query = $this->db->query($sql);
		return $query->row;
	} 

}
?>