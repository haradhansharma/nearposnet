<?php
class ModelSettingExtension extends Model {
	public function getExtensions($type) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");

		return $query->rows;
	}
	////sharma for pos
	function getExtensionsForPosService($type) {
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE type = '" . $this->db->escape($type) . "' AND code IN ('total', 'sub_total', 'tax') ");

		return $query->rows;
	}
}