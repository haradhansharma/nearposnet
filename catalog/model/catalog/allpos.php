<?php
class ModelCatalogAllpos extends Model {
	public function getAllpos($posapp_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_application  WHERE posapp_id = '" . (int)$posapp_id . "' AND status = '1'");

		return $query->row;
	} 

	public function getPosCategories($posapp_id) {	 

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "posapp_to_category` ptc LEFT JOIN `" . DB_PREFIX . "category_path` cp ON (ptc.category_id = cp.category_id) LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (ptc.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category c ON (c.category_id = ptc.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE ptc.posapp_id = '".$posapp_id."' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");		

		return $query->rows;
	}
	public function getTotalPoss($data = array()) {
		$sql = "SELECT COUNT(DISTINCT pa.posapp_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM `" . DB_PREFIX . "category_path` cp  LEFT JOIN `" . DB_PREFIX . "posapp_to_category` p2c ON (cp.category_id = p2c.category_id) LEFT JOIN `" . DB_PREFIX . "pos_application` pa ON (pa.posapp_id = p2c.posapp_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "posapp_to_category p2c LEFT JOIN `" . DB_PREFIX . "pos_application` pa ON (pa.posapp_id = p2c.posapp_id)";
			}

		} else {
			$sql .= " FROM " . DB_PREFIX . "pos_application pa";
		}

		$sql .= " WHERE  pa.status = '1'  AND pa.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}			
		}

		
		if (!empty($data['filter_country'])) {
			$sql .= " AND pa.country_id = '" . (int)$data['filter_country'] . "' ";
		}

		if (!empty($data['filter_zone'])) {
			$sql .= " AND pa.zone_id = '" . (int)$data['filter_zone'] . "' ";
		}

		if (!empty($data['filter_city'])) {
			$sql .= " AND LOWER(pa.city) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_city'])) . "%' ";
		}

		if (!empty($data['filter_posapp_id'])) {
			$sql .= " AND p.posapp_id = '" . (int)$data['filter_posapp_id'] . "'";
		}

		



		if (!empty($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		}	
		

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getAllposs($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "pos_application WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' status = '1'";

			$sort_data = array(
				'posname',
				'city',
				'zone'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY posname";
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
		} else {
			$allpos_data = $this->cache->get('allpos.' . (int)$this->config->get('config_store_id'));

			if (!$allpos_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_application  WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY posname");

				$allpos_data = $query->rows;

				$this->cache->set('allpos.' . (int)$this->config->get('config_store_id'), $allpos_data);
			}

			return $allpos_data;
		}
	}
	public function getPoss($data = array()) {

		$sql = "SELECT *, (SELECT name FROM " . DB_PREFIX . "country c WHERE c.country_id = pa.country_id) AS country, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = pa.zone_id) AS zone, (3959 * ACOS(COS(RADIANS(" . $this->db->escape($data['latitude']) . ")) * COS(RADIANS(pa.latitude)) * COS(RADIANS(pa.longitude) - RADIANS(" . $this->db->escape($data['longitude']) . ")) + SIN(RADIANS(" . $this->db->escape($data['latitude']) . ")) * SIN(RADIANS(pa.latitude)))) AS distance ";		

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM `" . DB_PREFIX . "category_path` cp  LEFT JOIN `" . DB_PREFIX . "posapp_to_category` p2c ON (cp.category_id = p2c.category_id) LEFT JOIN `" . DB_PREFIX . "pos_application` pa ON (pa.posapp_id = p2c.posapp_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "posapp_to_category p2c LEFT JOIN `" . DB_PREFIX . "pos_application` pa ON (pa.posapp_id = p2c.posapp_id)";
			}

		} else {
			$sql .= " FROM " . DB_PREFIX . "pos_application pa";
		}

		$sql .= " WHERE  pa.status = '1'  AND pa.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if($this->request->get['route'] <> 'product/product'){
			if(isset($this->request->get['posapp_id'])){
				$sql .= "  AND pa.posapp_id = '" . (int)$this->request->get['posapp_id'] . "'";
			}
		}

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}			
		}
		
		if (!empty($data['filter_country'])) {
			$sql .= " AND pa.country_id = '" . (int)$data['filter_country'] . "' ";
		}

		if (!empty($data['filter_zone'])) {
			$sql .= " AND pa.zone_id = '" . (int)$data['filter_zone'] . "' ";
		}

		if (!empty($data['filter_city'])) {
			$sql .= " AND LOWER(pa.city) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_city'])) . "%' ";
		}

		if (!empty($data['filter_posapp_id'])) {
			$sql .= " AND p.posapp_id = '" . (int)$data['filter_posapp_id'] . "'";
		}

		$sql .= " GROUP BY pa.posapp_id";		

		$sort_data = array(
			'pa.posname',
			'pa.firstname',		
			'pa.posapp_id',
			'pa.date_added',
			'distance'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pa.posname' || $data['sort'] == 'pa.firstname' || $data['sort'] == 'distance') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			}  else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY " . ($this->config->get('module_posapp_status') ? 'distance' : 'pa.posapp_id') . " ";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pa.posname) DESC";
		} else {
			$sql .= " ASC, LCASE(pa.posname) ASC";
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
}