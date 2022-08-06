<?php

class ModelPossettingCostReport extends Model {

	public function getReports($data) {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {

			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sql="SELECT * FROM `" . DB_PREFIX . "order_product` op LEFT JOIN " .DB_PREFIX. "order o ON (op.order_id = o.order_id) ";
				

		if (!empty($data['filter_upc']) || !empty($data['filter_avi_quantity_form']) || !empty($data['filter_avi_quantity_to']) || !empty($data['filter_price_form']) || !empty($data['filter_price_to'])) {
			$sql .= "  LEFT JOIN " .DB_PREFIX. "product p ON (p.product_id = op.product_id)";
		}

		$sql .=" WHERE o.order_status_id IN(" . implode(",", $implode) . ")";	
		
		if (!empty($data['filter_upc'])) {

			$sql .= " AND p.upc='" .$data['filter_upc'] . "'";
		}

		if (!empty($data['filter_product'])) {

			$sql .= " AND op.name='" .$data['filter_product'] . "'";
		}

		if (!empty($data['filter_product_id_form'])) {

			$sql .= " AND op.product_id >='" .$data['filter_product_id_form'] . "'";
		}

		if (!empty($data['filter_product_id_to'])) {

			$sql .= " AND op.product_id <='" .$data['filter_product_id_to'] . "'";
		}

		if (!empty($data['filter_sell_quantity_form'])) {

			$sql .= " AND op.quantity >='" .$data['filter_sell_quantity_form'] . "'";
		}

		if (!empty($data['filter_sell_quantity_to'])) {

			$sql .= " AND op.quantity <='" .$data['filter_sell_quantity_to'] . "'";
		}

		if (!empty($data['filter_price_form'])) {

			$sql .= " AND p.price >='" .$data['filter_price_form'] . "'";
		}

		if (!empty($data['filter_price_to'])) {

			$sql .= " AND p.price <='" .$data['filter_price_to'] . "'";
		}

		if (!empty($data['filter_avi_quantity_form'])) {

			$sql .= " AND p.quantity >='" .$data['filter_avi_quantity_form'] . "'";
		}

		if (!empty($data['filter_avi_quantity_to'])) {

			$sql .= " AND p.quantity <='" .$data['filter_avi_quantity_to'] . "'";
		}

		if (!empty($data['filter_date_form'])) {

			$sql .= " AND date(o.date_added) >='" .$data['filter_date_form'] . "'";
		}

		if (!empty($data['filter_date_to'])) {

			$sql .= " AND date(o.date_added) <='" .$data['filter_date_to'] . "'";
		}

		$sql .= " GROUP BY op.product_id";

		$sort_data = array(

			'op.product_id',
			'op.name',
			'op.price',
			'op.quantity',

		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {

			$sql .= " ORDER BY " . $data['sort'];

		} else {

			$sql .= " ORDER BY op.product_id";

		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {

			$sql .= " DESC";

		} else {

			$sql .= " ASC";

		}

		/*if (isset($data['start']) || isset($data['limit'])) {

			if ($data['start'] < 0) {

				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {

				$data['limit'] = 20;

			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}*/
//print_r($sql);die();
		
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCost($product_id,$order_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order_product_cost where product_id='".$product_id."' AND order_id='".$order_id."'";
		$query = $this->db->query($sql);
//echo "<pre>";print_r($query->row);die();
		if(!empty($query->row['cost']))
		{
			return  $query->row['cost'];
		}
	}


	public function getTotalSellReport($data=array()) {

		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {

			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sql="SELECT COUNT(*) as total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" .DB_PREFIX. "order` o ON (op.order_id = o.order_id) ";
				
		if (!empty($data['filter_upc']) || !empty($data['filter_avi_quantity_form']) || !empty($data['filter_avi_quantity_to'])|| !empty($data['filter_price_form']) || !empty($data['filter_price_to'])) {
			$sql .= "  LEFT JOIN " .DB_PREFIX. "product p ON (p.product_id = op.product_id)";
		}

		$sql .=" WHERE o.order_status_id IN(" . implode(",", $implode) . ")";	
		
		if (!empty($data['filter_upc'])) {

			$sql .= " AND p.upc='" .$data['filter_upc'] . "'";
		}

		if (!empty($data['filter_product'])) {

			$sql .= " AND op.name='" .$data['filter_product'] . "'";
		}

		if (!empty($data['filter_product_id_form'])) {

			$sql .= " AND op.product_id >='" .$data['filter_product_id_form'] . "'";
		}

		if (!empty($data['filter_product_id_to'])) {

			$sql .= " AND op.product_id <='" .$data['filter_product_id_to'] . "'";
		}

		if (!empty($data['filter_sell_quantity_form'])) {

			$sql .= " AND op.quantity >='" .$data['filter_sell_quantity_form'] . "'";
		}

		if (!empty($data['filter_sell_quantity_to'])) {

			$sql .= " AND op.quantity <='" .$data['filter_sell_quantity_to'] . "'";
		}

		if (!empty($data['filter_price_form'])) {

			$sql .= " AND p.price >='" .$data['filter_price_form'] . "'";
		}

		if (!empty($data['filter_price_to'])) {

			$sql .= " AND p.price <='" .$data['filter_price_to'] . "'";
		}

		if (!empty($data['filter_avi_quantity_form'])) {

			$sql .= " AND p.quantity >='" .$data['filter_avi_quantity_form'] . "'";
		}

		if (!empty($data['filter_avi_quantity_to'])) {

			$sql .= " AND p.quantity <='" .$data['filter_avi_quantity_to'] . "'";
		}

		if (!empty($data['filter_date_form'])) {

			$sql .= " AND date(o.date_added) >='" .$data['filter_date_form'] . "'";
		}

		if (!empty($data['filter_date_to'])) {

			$sql .= " AND date(o.date_added) <='" .$data['filter_date_to'] . "'";
		}

		$sql .= " GROUP BY op.product_id";
		$query = $this->db->query($sql);
		//print_r($query);die();
		if (!empty($query->num_rows)) {
			return $query->num_rows;
		}else{
			return 0;
		}

	}

	public function getTmdSales($product_id,$data=array()) {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sql="SELECT SUM(op.quantity) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" .DB_PREFIX. "order` o ON (op.order_id = o.order_id) WHERE o.order_status_id IN(" . implode(",", $implode) . ") and op.product_id='".$product_id."'";
		

		if (!empty($data['filter_product'])) {

			$sql .= " AND op.name='" .$data['filter_product'] . "'";
		}

		if (!empty($data['filter_product_id_form'])) {

			$sql .= " AND op.product_id >='" .$data['filter_product_id_form'] . "'";
		}

		if (!empty($data['filter_product_id_to'])) {

			$sql .= " AND op.product_id <='" .$data['filter_product_id_to'] . "'";
		}

		if (!empty($data['filter_sell_quantity_form'])) {

			$sql .= " AND op.quantity >='" .$data['filter_sell_quantity_form'] . "'";
		}

		if (!empty($data['filter_sell_quantity_to'])) {

			$sql .= " AND op.quantity <='" .$data['filter_sell_quantity_to'] . "'";
		}

		if (!empty($data['filter_date_form'])) {

			$sql .= " AND date(o.date_added) >='" .$data['filter_date_form'] . "'";
		}

		if (!empty($data['filter_date_to'])) {

			$sql .= " AND date(o.date_added) <='" .$data['filter_date_to'] . "'";
		}	
		$query = $this->db->query($sql);
		if(isset($query->row['total'])){
			return $query->row['total'];
		} else {
			return 0;
		}
	}


}