<?php
class ModelPossettingSaleReport extends Model {

	public function getSellTaxs($data) {
		$sql="select * from " . DB_PREFIX . "tax_rate where tax_rate_id<>0";
		$query = $this->db->query($sql);
		return $query->rows;
	}


	////sharma for pos

	public function getPartialPaid($order_id){	
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "partial_payment` WHERE `order_id` = '". (int)$order_id ."' ");
		if($query->num_rows){
			return $query->row['partial_amount'];
		}else{
			return false;
		}
	}

	public function getTotalTaxs($order_id,$name) {
		$query = $this->db->query("SELECT value,order_id FROM " . DB_PREFIX . "order_total WHERE order_id='".(int)$order_id."' and code = 'tax' and title='".$name."'");
		return $query->row;
	}

	public function getTaxReports($data) {
		//sharma for pos edited to add posapp
		if(isset($this->request->get['posapp_id'])) {
			$checkout_posapp_id = $this->request->get['posapp_id'];
		} elseif(isset($this->session->data['posapp_id'])){
			$checkout_posapp_id = $this->session->data['posapp_id'];
		} else{
			$checkout_posapp_id = 0; 
		}

		$sql="SELECT * FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_total ot ON (o.order_id = ot.order_id) ";

		if (!empty($data['filter_order_id'])) {
			$sql .=" and o.order_id like '".(int)$data['filter_order_id']."%'";
		}

		if (!empty($data['filter_payment_method'])) {
			$sql .=" and o.payment_method like '".$this->db->escape($data['filter_payment_method'])."%'";
		}
		
		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		$sql .= " WHERE o.order_status_id IN(" . implode(",", $implode) . ") AND o.posapp_id = '". (int)$checkout_posapp_id ."' AND ot.code = 'total' group by ot.order_id";

		$sort_data = array( 
			'o.order_id',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
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

	public function getSellProducts($order_id) {
		$sql="select * from " . DB_PREFIX . "order_product where order_id='".$order_id."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalTaxReport($data) {
		//sharma for pos edited to add posapp
		if(isset($this->request->get['posapp_id'])) {
			$checkout_posapp_id = $this->request->get['posapp_id'];
		} elseif(isset($this->session->data['posapp_id'])){
			$checkout_posapp_id = $this->session->data['posapp_id'];
		} else{
			$checkout_posapp_id = 0; 
		}

		$sql="SELECT COUNT(DISTINCT ot.order_id) AS total FROM " . DB_PREFIX . "order o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) ";
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		$sql .= " WHERE o.order_status_id IN(" . implode(",", $implode) . ") AND o.posapp_id = '". (int)$checkout_posapp_id ."' AND ot.code = 'total'";

		if (!empty($data['filter_order_id'])) {
			$sql .=" and o.order_id like '".(int)$data['filter_order_id']."%'";
		}

		if (!empty($data['filter_payment_method'])) {
			$sql .=" and o.payment_method like '".$this->db->escape($data['filter_payment_method'])."%'";
		}

		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}


		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getSumTaxs($order_id) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "order_total WHERE order_id='".$order_id."' and code='tax' group by order_id");
		return $query->row['value'];
	}

}