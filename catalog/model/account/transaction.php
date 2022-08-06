<?php
class ModelAccountTransaction extends Model {
	public function getTransactions($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$this->customer->getId() . "'";

		$sort_data = array(
			'amount',
			'description',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added"; 
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

	public function getTotalTransactions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}

	public function getTotalAmount() {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$this->customer->getId() . "' GROUP BY customer_id");

		if ($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	////sharma for pos

	public function getAgent($customer_id){
		$query = $this->db->query(" SELECT * FROM  " . DB_PREFIX . "service_agent WHERE customer_id = '". (int)$customer_id ."' ");
		return $query->row;		

	}

	public function getAgentInfo($data){		

		$this->db->query("INSERT INTO " . DB_PREFIX . "service_agent SET customer_id = '" . $this->db->escape($data['customer_id']) . "', code_prefix = '" . $this->db->escape($data['code_prefix']) . "', creat_date = NOW()");
		$agent_id = $this->db->getLastId();

		$query = $this->db->query(" SELECT * FROM  " . DB_PREFIX . "service_agent WHERE service_agent_id = '". (int)$agent_id ."' ");

		return $query->row;		

	}
	public function agentTearmUpdate($agent_id, $checked){
		$this->db->query("UPDATE " . DB_PREFIX . "service_agent SET tearm_agree = '". (int)$checked ."' WHERE  service_agent_id = '". (int)$agent_id ."' ");
	}
	////sharma for pos
}