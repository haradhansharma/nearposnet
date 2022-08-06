<?php
class ModelPosCustomerProduct extends Model {

	public function getProducts($data = array()) {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {

			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sql="SELECT * FROM `" . DB_PREFIX . "order_product` op LEFT JOIN " .DB_PREFIX. "order o ON (op.order_id = o.order_id) WHERE customer_id='".$this->session->data['pos_customer_id']."' AND o.order_status_id IN(" . implode(",", $implode) . ") GROUP BY op.product_id";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);
		$this->load->model('pos/pos');
		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->model_pos_pos->getProduct($result['product_id']);
		}

		return $product_data;
	}
}
