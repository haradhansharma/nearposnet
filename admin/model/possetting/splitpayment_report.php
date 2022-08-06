<?php
class ModelPossettingSplitPaymentReport extends Model {
	public function getSplitPaymentMethod($order_id){
		$sql="SELECT * FROM " . DB_PREFIX . "pos_split_payment WHERE order_id='".$order_id."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getTotalAmount($order_id){
		$sql="SELECT SUM(amount) as total FROM " . DB_PREFIX . "pos_split_payment WHERE order_id='".$order_id."'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	public function getSplitReports($data=array()){
		$sql="SELECT * FROM " . DB_PREFIX . "pos_split_payment WHERE split_id<>0";
		
		$sort_data = array(
			'order_id',
			'method',
			'order_status_id',
			'date_added',
		);
		if (!empty($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND order_status_id LIKE '" . $this->db->escape($data['filter_order_status_id']) . "%'";
		}
		
		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$sql.=" GROUP By order_id";

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY order_id";
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

	
	public function getTotalSplitReports($data=array()) {
		$sql="SELECT COUNT(*) as total FROM " . DB_PREFIX . "pos_split_payment WHERE split_id<>0";

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND order_status_id = '" . $this->db->escape($data['filter_order_status_id']) . "'";
		}
		
		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}
		$sql.=" GROUP By order_id";
		$query = $this->db->query($sql);
		if (!empty($query->row)) {
			return $query->num_rows;
		}else{
			return 0;
		}
		
	}
}