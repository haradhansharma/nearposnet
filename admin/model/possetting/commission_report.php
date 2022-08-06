<?php
class ModelPossettingCommissionReport extends Model {
	public function deleteBarcodes($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pos_order` WHERE order_id = '" . (int)$order_id . "'");
	}	

	public function getPosapp($posapp_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_application WHERE posapp_id = '" . (int)$posapp_id . "' AND status<>0");

		return $query->row;
	}

	public function genarateComissionProduct($data){
		$this->db->query(" INSERT INTO " . DB_PREFIX . "pos_commission_order SET invoice_prefix =  '" . $this->db->escape($data['invoice_prefix']) . "', posapp_id =  '" . $this->db->escape($data['posapp_id']) . "', posname =  '" . $this->db->escape($data['posname']) . "', pos_url =  '" . $this->db->escape($data['pos_url']) . "', store_id =  '" . $this->db->escape($data['store_id']) . "', store_name =  '" . $this->db->escape($data['store_name']) . "', store_url =  '" . $this->db->escape($data['store_url']) . "', firstname =  '" . $this->db->escape($data['firstname']) . "', lastname =  '" . $this->db->escape($data['lastname']) . "', email =  '" . $this->db->escape($data['email']) . "', telephone =  '" . $this->db->escape($data['telephone']) . "', payment_firstname =  '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname =  '" . $this->db->escape($data['payment_lastname']) . "', payment_address_1 =  '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 =  '" . $this->db->escape($data['payment_address_2']) . "', payment_city =  '" . $this->db->escape($data['payment_city']) . "', payment_postcode =  '" . $this->db->escape($data['payment_postcode']) . "', payment_country =  '" . $this->db->escape($data['payment_country']) . "', payment_country_id =  '" . $this->db->escape($data['payment_country_id']) . "', payment_zone =  '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id =  '" . $this->db->escape($data['payment_zone_id']) . "', payment_address_format =  '" . $this->db->escape($data['payment_address_format']) . "', payment_method =  '" . $this->db->escape($data['payment_method']) . "', payment_code =  '" . $this->db->escape($data['payment_code']) . "', total =  '" . (float)$data['total'] . "', payable =  '" . (float)$data['payable'] . "', commission_value =  '" . $this->db->escape($data['commission_value']) . "', commission =  '" . $this->db->escape($data['commission']) . "', language_id =  '" . $this->db->escape($data['language_id']) . "', currency_id =  '" . $this->db->escape($data['currency_id']) . "', currency_code =  '" . $this->db->escape($data['currency_code']) . "', currency_value =  '" . $this->db->escape($data['currency_value']) . "', ip =  '" . $this->db->escape($data['ip']) . "', forwarded_ip =  '" . $this->db->escape($data['forwarded_ip']) . "', user_agent =  '" . $this->db->escape($data['user_agent']) . "', accept_language =  '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()  ");

		$commission_order_id = $this->db->getLastId();

		if (isset($data['commissions'])) {
			foreach ($data['commissions'] as $selected_order) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "pos_commission_details SET commission_order_id = '" . (int)$commission_order_id . "', order_id = '" . (int)$selected_order['order_id'] . "',  posapp_id = '" . (int)$selected_order['posapp_id'] . "', posname = '" . $this->db->escape($selected_order['posname']) . "', commission_value = '" . (float)$selected_order['commission_value'] . "', commission = '" . $this->db->escape($selected_order['commission']) . "', total = '" . (float)$selected_order['total'] . "', payable = '" . (float)$selected_order['payable'] . "', tax = '" . (float)$selected_order['tax'] . "', creat_date = NOW()");
			}
		}


		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "pos_commission_order_total SET commission_order_id = '" . (int)$commission_order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}

		return $commission_order_id;
	}

	public function getCommissionOrder($commission_order_id){
		$query = $this->db->query(" SELECT * FROM " . DB_PREFIX . "pos_commission_order WHERE commission_order_id = '". (int)$commission_order_id ."' ");

		return $query->row;
	}

	public function getExistingOrderDetails($order_id){
		$query = $this->db->query(" SELECT DISTINCT order_id FROM " . DB_PREFIX . "pos_commission_details WHERE order_id = '". (int)$order_id ."' ");

		return $query->rows;

	}

	public function getExistingOrderHistory($order_id){	

		$query = $this->db->query(" SELECT pco.order_status_id FROM `" . DB_PREFIX . "pos_commission_order` pco RIGHT JOIN `" . DB_PREFIX . "pos_commission_details` pcd ON (pco.commission_order_id = pcd.commission_order_id) WHERE pcd.order_id = '". (int)$order_id ."' ");		

		return $query->row;
	}

	public function checkoutPending(){
		$query = $this->db->query("SELECT DISTINCT SUM(cd.payable) as checkout_pending FROM `" . DB_PREFIX . "pos_commission_details` cd LEFT JOIN `" . DB_PREFIX . "pos_commission_order_history` coh ON (cd.commission_order_id = coh.commission_order_id) LEFT JOIN `" . DB_PREFIX . "pos_commission_order` co ON (co.commission_order_id = cd.commission_order_id) WHERE co.order_status_id = 0");
		return $query->row['checkout_pending'];

	}

	public function checkoutInitializing(){
		$query = $this->db->query("SELECT DISTINCT SUM(cd.payable) as checkout_initializing FROM `" . DB_PREFIX . "pos_commission_details` cd LEFT JOIN `" . DB_PREFIX . "pos_commission_order_history` coh ON (cd.commission_order_id = coh.commission_order_id) LEFT JOIN `" . DB_PREFIX . "pos_commission_order` co ON (co.commission_order_id = cd.commission_order_id) WHERE co.order_status_id > 0");
		return $query->row['checkout_initializing'];

	}

	

	public function getStatusName($order_status_id){
		$query = $this->db->query(" SELECT name FROM `" . DB_PREFIX . "order_status` WHERE`order_status_id` = '". (int)$order_status_id ."' ");

		return $query->row['name'];
	}

	public function getCommissionOrderDetails($commission_order_id){
		$query = $this->db->query(" SELECT * FROM " . DB_PREFIX . "pos_commission_details WHERE commission_order_id = '". (int)$commission_order_id ."' ");

		return $query->rows;

	}

	public function getCommissionOrderTotal($commission_order_id){
		$query = $this->db->query(" SELECT * FROM " . DB_PREFIX . "pos_commission_order_total WHERE commission_order_id = '". (int)$commission_order_id ."' ");

		return $query->rows;

	}

	public function addOrderHistory($commission_order_id, $order_status_id, $comment = '', $notify = false, $override = false) {
		$order_info = $this->getCommissionOrder($commission_order_id);
		
		if ($order_info) {				

			// Update the DB with the new statuses
			$this->db->query("UPDATE `" . DB_PREFIX . "pos_commission_order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE commission_order_id = '" . (int)$commission_order_id . "'");

			$this->db->query("INSERT INTO " . DB_PREFIX . "pos_commission_order_history SET commission_order_id = '" . (int)$commission_order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");			
			
		}
	}


	public function getReports($data) {
		// $sql="select * from " . DB_PREFIX . "pos_order po left join " . DB_PREFIX . "pos_user pu on (pu.user_id = po.user_id) LEFT JOIN " .DB_PREFIX. "order o ON (o.order_id = po.order_id)";

		$sql="SELECT o.order_id, o.order_status_id, o.posapp_id, pu.username, pu.user_id, o.payment_method, o.total, pu.commission_value FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "pos_order` po ON (po.order_id = o.order_id) LEFT JOIN " . DB_PREFIX . "pos_user pu ON (pu.user_id = po.user_id) ";


		

		// if (!empty($data['filter_payment_method'])) {
		// 	$sql .=" LEFT JOIN " .DB_PREFIX. "order o ON (o.order_id = po.order_id)";
		// }

		// $sql .=" WHERE pu.commission_value<>0 AND po.posapp_id = '". $this->session->data['posapp_id'] ."' ";

		$sql .=" WHERE  o.posapp_id = '". $this->session->data['posapp_id'] ."' ";

		if (isset($data['filter_payment_method'])) { 
			$sql .=" and o.payment_method = '".$this->db->escape($data['filter_payment_method'])."'";
		}

		if (isset($data['filter_username'])) { 
			$sql .=" and po.user_id = '".$this->db->escape($data['filter_username'])."'";
		}

		if (isset($data['filter_order_id'])) {
			$sql .=" and po.order_id = '".$this->db->escape($data['filter_order_id'])."'";
		}

		if (isset($data['filter_payment_method'])) {
			$sql .=" and o.payment_method like '".$this->db->escape($data['filter_payment_method'])."%'";
		}

		$sort_data = array(
			'po.user_id',
			'po.order_id',
			'po.amount',
			'po.commission'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
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
		// $sql="select count(*) as total from " . DB_PREFIX . "pos_order po left join " . DB_PREFIX . "pos_user pu on (pu.user_id=po.user_id)";

		$sql="SELECT count(*) as total FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "pos_order` po ON (po.order_id = o.order_id) LEFT JOIN " . DB_PREFIX . "pos_user pu ON (pu.user_id = po.user_id) ";


		// if (!empty($data['filter_payment_method'])) {
		// 	$sql .=" LEFT JOIN " .DB_PREFIX. "order o ON (o.order_id = po.order_id)";
		// }

		// $sql .=" WHERE pu.commission_value<>0 AND po.posapp_id = '". $this->session->data['posapp_id'] ."'";
		$sql .=" WHERE  o.posapp_id = '". $this->session->data['posapp_id'] ."' ";

		if (isset($data['filter_payment_method'])) { 
			$sql .=" and o.payment_method = '".$this->db->escape($data['filter_payment_method'])."'";
		}

		if (isset($data['filter_username'])) {
			$sql .=" and po.user_id = '".$this->db->escape($data['filter_username'])."'";
		}

		if (isset($data['filter_order_id'])) {
			$sql .=" and po.order_id = '".$this->db->escape($data['filter_order_id'])."'";
		}

		if (isset($data['filter_payment_method'])) {
			$sql .=" and o.payment_method like '".$this->db->escape($data['filter_payment_method'])."%'";
		}		

		$query = $this->db->query($sql);
		return $query->row['total'];
	}


	public function getUserName($user_id){		
		$sql="select * from " . DB_PREFIX . "pos_user where user_id='".$user_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getAmount($order_id){		
		$sql="select * from " . DB_PREFIX . "order where order_id='".$order_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	} 

	public function getCommission($order_id){		
		$sql="select * from " . DB_PREFIX . "order where order_id='".$order_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	} 

	public function getCompletedOrders($completed_status){
		
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "pos_commission_order` WHERE `order_status_id` IN (".implode(',',$completed_status).") AND posapp_id = '". (int)$this->session->data['posapp_id'] ."' ");
		
		return $query->rows;
	}

	public function getProcessingOrders($processing_status){
		// foreach($processing_status as $processing){
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "pos_commission_order` WHERE `order_status_id` IN (".implode(',',$processing_status).")  AND posapp_id = '". (int)$this->session->data['posapp_id'] ."' ");
		// }
		return $query->rows;
	}

	public function getPendingOrders($payment_pending){
		// foreach($payment_pending as $pending){
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "pos_commission_order` WHERE `order_status_id` IN (".implode(',',$payment_pending).") AND posapp_id = '". (int)$this->session->data['posapp_id'] ."' ");
		// }
		return $query->rows;
	}

	public function getUnsuccessOrders($unsuccessfull_status){
		// foreach($unsuccessfull_status as $unsuccessfull){
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "pos_commission_order` WHERE `order_status_id` NOT IN (".implode(',',$unsuccessfull_status).") AND posapp_id = '". (int)$this->session->data['posapp_id'] ."' ");
		
		return $query->rows;
		// }
	}
	
		
	



}