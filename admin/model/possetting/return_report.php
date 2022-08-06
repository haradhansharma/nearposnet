<?php

class ModelPossettingReturnReport extends Model {

	public function getReports($data) {

		//sharma for pos edited to add posapp
		    if(isset($this->request->get['posapp_id'])) {
				$checkout_posapp_id = $this->request->get['posapp_id'];
			} elseif(isset($this->session->data['posapp_id'])){
				$checkout_posapp_id = $this->session->data['posapp_id'];
			} else{
				$checkout_posapp_id = 0; 
			}

		$sql="SELECT * FROM `" . DB_PREFIX . "pos_order_return` WHERE return_id<>0 AND posapp_id = '". (int)$checkout_posapp_id ."'";
				
		if (!empty($data['filter_product'])) {

			$sql .= " AND name LIKE '%" .$data['filter_product'] . "%'";
		}

		if (!empty($data['filter_product_id_form'])) {

			$sql .= " AND product_id >='" .$data['filter_product_id_form'] . "'";
		}

		if (!empty($data['filter_product_id_to'])) {

			$sql .= " AND product_id <='" .$data['filter_product_id_to'] . "'";
		}

		if (!empty($data['filter_price_form'])) {

			$sql .= " AND price >='" .$data['filter_price_form'] . "'";
		}

		if (!empty($data['filter_model'])) { 

			$sql .= " AND model LIKE '%" .$data['filter_model'] . "%'";
		}

		if (!empty($data['filter_price_to'])) {

			$sql .= " AND price <='" .$data['filter_price_to'] . "'";
		}

		if (!empty($data['filter_quantity_form'])) {

			$sql .= " AND quantity >='" .$data['filter_quantity_form'] . "'";
		}

		if (!empty($data['filter_quantity_to'])) {

			$sql .= " AND quantity <='" .$data['filter_quantity_to'] . "'";
		}

		if (!empty($data['filter_date_form'])) {

			$sql .= " AND date(date_added) >='" .$data['filter_date_form'] . "'";
		}

		if (!empty($data['filter_date_to'])) {

			$sql .= " AND date(date_added) <='" .$data['filter_date_to'] . "'";
		}

		if (!empty($data['filter_order_id'])) {

			$sql .= " AND order_id ='" .$data['filter_order_id'] . "'";
		}


		if (isset($data['filter_return_id']) && $data['filter_return_id'] !== '') {
			$sql .= " AND return_id = '" . (int)$data['filter_return_id'] . "'";
		}

		$sort_data = array(

			'product_id',
			'order_id',
			'name',
			'model',
			'price',
			'quantity',
			'date_added',
			'reason',

		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {

			$sql .= " ORDER BY " . $data['sort'];

		} else {

			$sql .= " ORDER BY product_id";

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

	public function deleteReturn($return_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pos_order_return` WHERE return_id='".$return_id."'");
	}

	public function getTotalReport($data=array()) {
		//sharma for pos edited to add posapp
		    if(isset($this->request->get['posapp_id'])) {
				$checkout_posapp_id = $this->request->get['posapp_id'];
			} elseif(isset($this->session->data['posapp_id'])){
				$checkout_posapp_id = $this->session->data['posapp_id'];
			} else{
				$checkout_posapp_id = 0; 
			}
			
		$sql="SELECT COUNT(*) as total FROM `" . DB_PREFIX . "pos_order_return` WHERE return_id<>0 AND posapp_id = '". (int)$checkout_posapp_id ."'";

		if (!empty($data['filter_product'])) {

			$sql .= " AND name LIKE '%" .$data['filter_product'] . "%'";
		}

		if (!empty($data['filter_product_id_form'])) {

			$sql .= " AND product_id >='" .$data['filter_product_id_form'] . "'";
		}

		if (!empty($data['filter_product_id_to'])) {

			$sql .= " AND product_id <='" .$data['filter_product_id_to'] . "'";
		}

		if (!empty($data['filter_price_form'])) {

			$sql .= " AND price >='" .$data['filter_price_form'] . "'";
		}

		if (!empty($data['filter_model'])) {

			$sql .= " AND model LIKE '%" .$data['filter_model'] . "%'";
		}

		if (!empty($data['filter_price_to'])) {

			$sql .= " AND price <='" .$data['filter_price_to'] . "'";
		}

		if (!empty($data['filter_quantity_form'])) {

			$sql .= " AND quantity >='" .$data['filter_quantity_form'] . "'";
		}

		if (!empty($data['filter_quantity_to'])) {

			$sql .= " AND quantity <='" .$data['filter_quantity_to'] . "'";
		}

		if (!empty($data['filter_date_form'])) {

			$sql .= " AND date(date_added) >='" .$data['filter_date_form'] . "'";
		}

		if (!empty($data['filter_date_to'])) {

			$sql .= " AND date(date_added) <='" .$data['filter_date_to'] . "'";
		}

		if (!empty($data['filter_order_id'])) {

			$sql .= " AND order_id ='" .$data['filter_order_id'] . "'";
		}


		if (isset($data['filter_return_id']) && $data['filter_return_id'] !== '') {
			$sql .= " AND return_id = '" . (int)$data['filter_return_id'] . "'";
		}

		$query = $this->db->query($sql);
		if (!empty($query->num_rows)) {
			return $query->row['total'];
		}else{
			return 0;
		}

	}
}