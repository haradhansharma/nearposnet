<?php
class ModelPossettingForecastReport extends Model {
	
	public function getReports($data) {
		//$sql="select * from " . DB_PREFIX . "order_product where order_id<>0 GROUP BY product_id";
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
		$implode[] = "'" . (int)$order_status_id . "'";
		}
		
		$sql="select * from " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id) WHERE o.order_status_id IN(" . implode(",", $implode) . ")";
		
		
		if (!empty($data['filter_from'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_from']) . "'";
		}

		if (!empty($data['filter_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_to']) . "'";
		}

		$sql .= " GROUP BY op.product_id";

		$sort_data = array(
			'op.order_id'
		);
		
		
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY op.order_id";
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

	public function getTotalReport($data) {
		//$sql="select * from " . DB_PREFIX . "order_product where order_id<>0 GROUP BY product_id";
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
		$implode[] = "'" . (int)$order_status_id . "'";
		}
		
		$sql="select * from " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id) WHERE o.order_status_id IN(" . implode(",", $implode) . ")";
		
		
		if (!empty($data['filter_from'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_from']) . "'";
		}

		if (!empty($data['filter_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_to']) . "'";
		}

		$sql .= " GROUP BY op.product_id";

			
		$query = $this->db->query($sql);

		return count($query->rows);
	}

	public function getproductqty($product_id) {
		$sql = "SELECT SUM(quantity) AS quantity FROM " . DB_PREFIX . "order_product where product_id='".$product_id."'";
		$query = $this->db->query($sql);
		return $query->row['quantity'];
	}

	public function getposproductqty($cproduct_id) {
		$sql = "SELECT SUM(quantity) AS posquantity FROM " . DB_PREFIX . "order_product where cproduct_id='".$cproduct_id."' ";
		$query = $this->db->query($sql);
		return $query->row['posquantity'];
	}
}