<?php
class ModelLocalisationZone extends Model {	

	////sharma for pos
	public function getZoneId($zone_mame, $map_country_id) {
		$implode = array();	
		$words = explode(' ', trim(preg_replace('/\s+/', ' ', $zone_mame)));
		foreach ($words as $word) {
			$query = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE name  LIKE '%" . (string)$word . "%' AND status = '1'");
			if($query->num_rows){
				$implode[] = $query->row['zone_id'];
			}
		}		

		$zone_id = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE zone_id IN (" . implode(',', $implode) . ") AND country_id = '". (int)$map_country_id ."' ");
		
		return $zone_id->row['zone_id'];

	}
	////sharma for pos


	public function getZone($zone_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");

		return $query->row;
	}

	public function getZonesByCountryId($country_id) {
		$zone_data = $this->cache->get('zone.' . (int)$country_id);

		if (!$zone_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");

			$zone_data = $query->rows;

			$this->cache->set('zone.' . (int)$country_id, $zone_data);
		}

		return $zone_data;
	}
}