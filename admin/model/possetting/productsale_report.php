<?php
class ModelPossettingProductsalereport extends Model {
	public function getProductSales($data=array()){

		//sharma for pos edited to add posapp
		    if(isset($this->request->get['posapp_id'])) {
				$checkout_posapp_id = $this->request->get['posapp_id'];
			} elseif(isset($this->session->data['posapp_id'])){
				$checkout_posapp_id = $this->session->data['posapp_id'];
			} else{
				$checkout_posapp_id = 0; 
			}
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		$sql="SELECT * FROM " . DB_PREFIX . "order_product op LEFT JOIN " .DB_PREFIX. "order o ON (op.order_id = o.order_id) WHERE o.order_status_id IN(" . implode(",", $implode) . ") AND op.quantity > 0 AND o.posapp_id = '". (int)$checkout_posapp_id ."' ";
		
		$sort_data = array(
			'op.product_id',
			'totalsale'
		);
		if (!empty($data['filter_productid'])) {
			$sql .= " AND op.product_id LIKE '" . $this->db->escape($data['filter_productid']) . "%'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND op.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND op.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}
		
		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}
		$sql.=" GROUP By op.product_id";
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY op.product_id";
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

	
	public function getTotalProductSales($data=array()) {

		//sharma for pos edited to add posapp
		    if(isset($this->request->get['posapp_id'])) {
				$checkout_posapp_id = $this->request->get['posapp_id'];
			} elseif(isset($this->session->data['posapp_id'])){
				$checkout_posapp_id = $this->session->data['posapp_id'];
			} else{
				$checkout_posapp_id = 0; 
			}

		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		$sql="SELECT * FROM " . DB_PREFIX . "order_product op LEFT JOIN " .DB_PREFIX. "order o ON (op.order_id = o.order_id) WHERE o.order_status_id IN(" . implode(",", $implode) . ") and op.quantity > 0 AND o.posapp_id = '". (int)$checkout_posapp_id ."'";


		if (!empty($data['filter_productid'])) {
			$sql .= " AND op.product_id LIKE '" . $this->db->escape($data['filter_productid']) . "%'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND op.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND op.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}
		
		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}
		$sql.=" GROUP By op.product_id";
		$query = $this->db->query($sql);
		if (!empty($query->row)) {
			return $query->num_rows;
		}else{
			return 0;
		}
	}

	public function getTotalSales($data,$product_id) {

		//sharma for pos edited to add posapp
		    if(isset($this->request->get['posapp_id'])) {
				$checkout_posapp_id = $this->request->get['posapp_id'];
			} elseif(isset($this->session->data['posapp_id'])){
				$checkout_posapp_id = $this->session->data['posapp_id'];
			} else{
				$checkout_posapp_id = 0; 
			}
			
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		$sql="select sum(quantity) AS total from " . DB_PREFIX . "order_product op LEFT JOIN " .DB_PREFIX. "order o ON (op.order_id = o.order_id) WHERE o.order_status_id IN(" . implode(",", $implode) . ") and op.product_id='".$product_id."' AND o.posapp_id = '". (int)$checkout_posapp_id ."'";
		
		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}	
		$query = $this->db->query($sql);
		if(isset($query->row['total'])){
			return $query->row['total'];
		} else {
			return 0;
		}
	}
	
}