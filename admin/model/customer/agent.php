<?php
class ModelCustomerAgent extends Model {

	public function getAgent($service_agent_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "service_agent WHERE service_agent_id = '" . (int)$service_agent_id . "'");

		return $query->row;
	}
	public function editAgent($service_agent_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "service_agent SET  status = '" . (int)$data['status'] . "' WHERE service_agent_id = '" . (int)$service_agent_id . "'");
	}

	public function getCustomerByAgentId($service_agent_id){
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "service_agent sa LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = sa.customer_id) WHERE sa.service_agent_id = '" . (int)$service_agent_id . "'");

		return $query->row;
	}

	public function deleteAgent($service_agent_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "service_agent WHERE service_agent_id = '" . (int)$service_agent_id . "'");
	}

	public function getAgents($data = array()) {

		$sql = "SELECT *, sa.status AS status, CONCAT(sa.code_prefix, '', sa.service_agent_id) AS agent_code, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "service_agent sa LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = sa.customer_id ) LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)";

		if(isset($this->request->get['route'])){
			if($this->request->get['route'] == 'customer/agent_approval'){
				$approval_location = true;
			}else{
				$approval_location = false;
			}
		}

		if($approval_location){
			$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND sa.status = 0 ";
		}else{
			$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND sa.status = 1 ";

		}			
		
		// $sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		$implode = array();

		if (!empty($data['filter_agent_code'])) {
			$implode[] = "CONCAT(sa.code_prefix, '', sa.service_agent_id) LIKE '%" . $this->db->escape($data['filter_agent_code']) . "%'";
		}

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		
		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}
		
		
		if (!empty($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "sa.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(sa.creat_date) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'agent_code',
			'name',
			'c.email',
			'customer_group',
			'status',
			'c.ip',
			'sa.creat_date'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY agent_code";
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


	public function getTotalAgents($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "service_agent sa LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = sa.customer_id )";

		if(isset($this->request->get['route'])){
			if($this->request->get['route'] == 'customer/agent_approval'){
				$approval_location = true;
			}else{
				$approval_location = false;
			}
		}

		if($approval_location){
			$sql .= " WHERE  sa.status = 0 ";
		}else{
			$sql .= " WHERE  sa.status = 1 ";

		}

		$implode = array();
		if (!empty($data['filter_agent_code'])) {
			$implode[] = "CONCAT(sa.code_prefix, '', sa.service_agent_id) LIKE '%" . $this->db->escape($data['filter_agent_code']) . "%'";
		}

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}


		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "sa.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(sa.creat_date) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	public function approveAgent($service_agent_id){

		$status = 1;

		$this->db->query("UPDATE " . DB_PREFIX . "service_agent SET  status = '" . (int)$status . "' WHERE service_agent_id = '" . (int)$service_agent_id . "'");

	}

	public function denyAgent($service_agent_id){

		$status = 0;

		$this->db->query("UPDATE " . DB_PREFIX . "service_agent SET  status = '" . (int)$status . "' WHERE service_agent_id = '" . (int)$service_agent_id . "'");

	}
}