<?php
class ModelLocalisationCountry extends Model {


	////sharma for pos
	public function getCountryId($country_mame) {
		$query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "country WHERE name = '" . (string)$country_mame . "' AND status = '1'");
		return $query->row['country_id'];
	}
	////sharma for pos


	public function getCountry($country_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");

		return $query->row;
	}

	public function getCountries() {
		$country_data = $this->cache->get('country.catalog');

		if (!$country_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' ORDER BY name ASC");

			$country_data = $query->rows;

			$this->cache->set('country.catalog', $country_data);
		}

		return $country_data;
	}
}