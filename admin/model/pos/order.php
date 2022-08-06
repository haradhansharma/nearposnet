<?php
class ModelPosOrder extends Model {
	public function addOrder($data,$order_status_id) {

		//sharma for pos edited to add posapp
		if(isset($this->request->get['posapp_id'])) {
			$checkout_posapp_id = $this->request->get['posapp_id'];
		} elseif(isset($this->session->data['posapp_id'])){
			$checkout_posapp_id = $this->session->data['posapp_id'];
		} else{
			$checkout_posapp_id = 0; 
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', posapp_id = '" . (int)$checkout_posapp_id . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");

		$order_id = $this->db->getLastId();

		// Products
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', cproduct_id = '" . (int)$product['cproduct_id'] . "',  name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

				$order_product_id = $this->db->getLastId();
				$this->load->model('possetting/cost');
				$cost_info= $this->model_possetting_cost->getProductCost($product['product_id'],false);
				if (!empty($cost_info)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_product_cost SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "',order_product_id = '" . (int)$order_product_id. "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "',cost = '" . (float)$cost_info['cost'] . "'");
				}

				foreach ($product['option'] as $option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				}
			}
		}

		// Gift Voucher
		
		// Vouchers
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		// Totals
		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}
		
		// Stock subtraction
		$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_product_query->rows as $order_product) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

			$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
			}
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '".$order_status_id."', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "',order_status_id = '".$order_status_id."', notify = '0', comment = '" . $this->db->escape($data['comment']) . "', date_added = NOW()");

		// store user id and store id
		$store_id=0;
		if(isset($this->session->data['store_id']))
		{
			$store_id=$this->session->data['store_id'];
		}

		if (!empty($data['split_payments']) && $data['payment_method']=='split') {
			foreach ($data['split_payments'] as $split_payment) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "pos_split_payment SET order_id = '" . (int)$order_id . "',order_status_id = '" . (int)$order_status_id . "',method='".$split_payment['method']."',amount='".(float)$split_payment['amount']."',date_added=now()");
			}
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "pos_order SET order_id = '" . (int)$order_id . "', posapp_id = '" . (int)$checkout_posapp_id . "', user_id='".$this->user->getId()."',store_id='".$store_id."'");
		
		$this->orderemail($order_id);
		
		if(!empty($data['partial_amount'])){
			$this->db->query("INSERT INTO " . DB_PREFIX . "partial_payment SET order_id = '" . (int)$order_id . "', partial_amount='".(float)$data['partial_amount']."', date_added = NOW()");
		}
		return $order_id;
	}

	public function editOrder($data,$order_id) {

		//sharma for pos edited to add posapp
		if(isset($this->request->get['posapp_id'])) {
			$checkout_posapp_id = $this->request->get['posapp_id'];
		} elseif(isset($this->session->data['posapp_id'])){
			$checkout_posapp_id = $this->session->data['posapp_id'];
		} else{
			$checkout_posapp_id = 0; 
		}
		
		// Restock
		$this->db->query("DELETE FROM " . DB_PREFIX . "partial_payment WHERE order_id = '" . (int)$order_id . "'");
		if(!empty($data['partial_amount'])){
			$this->db->query("INSERT INTO " . DB_PREFIX . "partial_payment SET order_id = '" . (int)$order_id . "', partial_amount='".(float)$data['partial_amount']."', date_added = NOW()");
		}
		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		foreach($product_query->rows as $product) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");

			$option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

			foreach ($option_query->rows as $option) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		
		

		// Products
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "',cproduct_id = '" . (int)$product['cproduct_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

				$order_product_id = $this->db->getLastId();
				$this->load->model('possetting/cost');
				$cost_info= $this->model_possetting_cost->getProductCost($product['product_id'],false);
				if (!empty($cost_info)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_product_cost SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "',order_product_id = '" . (int)$order_product_id. "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "',cost = '" . (float)$cost_info['cost'] . "'");
				}

				foreach ($product['option'] as $option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				}
			}
		}

		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}
		
			// Stock subtraction
		
		$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_product_query->rows as $order_product) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

			$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
			}
		}
		

		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$data['total'] . "', payment_method = '" .$this->db->escape($data['payment_method']) . "', payment_code = '" .$this->db->escape($data['payment_code']) . "', order_status_id = '". (int)$data['order_status_id'] ."', comment = '" . $this->db->escape($data['comment']) . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
		
		// store user id and store id
		$store_id=0;
		if(isset($this->session->data['store_id']))
		{
			$store_id=$this->session->data['store_id'];
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "pos_split_payment WHERE order_id = '" . (int)$order_id . "'");
		if (!empty($data['split_payments']) && $data['payment_method']=='split') {
			foreach ($data['split_payments'] as $split_payment) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "pos_split_payment SET order_id = '" . (int)$order_id . "',order_status_id = '" . (int)$data['order_status_id'] . "',method='".$split_payment['method']."',amount='".(float)$split_payment['amount']."',date_added=now()");
			}
		}
		$this->db->query("update " . DB_PREFIX . "pos_order SET user_id='".$this->user->getId()."',store_id='".$store_id."' where order_id = '" . (int)$order_id . "'");



	}

	////sharma for pos

	public function posData($posapp_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_application` WHERE `posapp_id` = '". (int)$posapp_id ."' ");
		return $query->row;

	}

	
	public function getPosUser($order_id) {
		//sharma for pos edited to add posapp
		if(isset($this->request->get['posapp_id'])) {
			$checkout_posapp_id = $this->request->get['posapp_id'];
		} elseif(isset($this->session->data['posapp_id'])){
			$checkout_posapp_id = $this->session->data['posapp_id'];
		} else{
			$checkout_posapp_id = 0; 
		}
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pos_order po LEFT JOIN " . DB_PREFIX . "pos_user pu ON (po.user_id = pu.user_id) WHERE po.order_id='".$order_id."' AND po.posapp_id = '" . (int)$checkout_posapp_id . "'");

		return $query->row;
	}

	
	public function getOrder($order_id) {
		//sharma for pos edited to add posapp
		if(isset($this->request->get['posapp_id'])) {
			$checkout_posapp_id = $this->request->get['posapp_id'];
		} elseif(isset($this->session->data['posapp_id'])){
			$checkout_posapp_id = $this->session->data['posapp_id'];
		} else{
			$checkout_posapp_id = 0; 
		}
		$order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "' AND o.posapp_id = '" . (int)$checkout_posapp_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}


			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'posapp_id'                => $order_query->row['posapp_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'custom_field'            => json_decode($order_query->row['custom_field'], true),
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => json_decode($order_query->row['payment_custom_field'], true),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => json_decode($order_query->row['shipping_custom_field'], true),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'order_status'            => $order_query->row['order_status'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified']
			);
} else {
	return false;
}
}

public function getAddress($address_id) {
	$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

	if ($address_query->num_rows) {
		$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

		if ($country_query->num_rows) {
			$country = $country_query->row['name'];
			$iso_code_2 = $country_query->row['iso_code_2'];
			$iso_code_3 = $country_query->row['iso_code_3'];
			$address_format = $country_query->row['address_format'];
		} else {
			$country = '';
			$iso_code_2 = '';
			$iso_code_3 = '';
			$address_format = '';
		}

		$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

		if ($zone_query->num_rows) {
			$zone = $zone_query->row['name'];
			$zone_code = $zone_query->row['code'];
		} else {
			$zone = '';
			$zone_code = '';
		}

		$address_data = array(
			'address_id'     => $address_query->row['address_id'],
			'firstname'      => $address_query->row['firstname'],
			'lastname'       => $address_query->row['lastname'],
			'company'        => $address_query->row['company'],
			'address_1'      => $address_query->row['address_1'],
			'address_2'      => $address_query->row['address_2'],
			'postcode'       => $address_query->row['postcode'],
			'city'           => $address_query->row['city'],
			'zone_id'        => $address_query->row['zone_id'],
			'zone'           => $zone,
			'zone_code'      => $zone_code,
			'country_id'     => $address_query->row['country_id'],
			'country'        => $country,
			'iso_code_2'     => $iso_code_2,
			'iso_code_3'     => $iso_code_3,
			'address_format' => $address_format,
			'custom_field'   => json_decode($address_query->row['custom_field'], true)
		);

		return $address_data;
	} else {
		return false;
	}
}

public function getCustomer($customer_id) {
	$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

	return $query->row;
}

public function loadinCart($order_id) {
	$this->pos->clear();
	unset($this->session->data['shipping_method']);
	unset($this->session->data['voucher']);
	unset($this->session->data['voucherdiscount']);
	unset($this->session->data['coupondiscount']);
	unset($this->session->data['coupon']);
	unset($this->session->data['order_id']);
	unset($this->session->data['mdiscount']);
	$this->session->data['order_id']=$order_id;

	$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");		
	if(isset($query->row['order_id'])) {
		foreach($query->rows as $product) {
			$option_data = array();
			$order_options = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' and order_product_id='".$product['order_product_id']."'")->rows;

			foreach ($order_options as $order_option) {
				if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
					$option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
				} elseif ($order_option['type'] == 'checkbox') {
					$option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
				} elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
					$option_data[$order_option['product_option_id']] = $order_option['value'];
				} elseif ($order_option['type'] == 'file') {
					$option_data[$order_option['product_option_id']] = $this->encryption->encrypt($this->config->get('config_encryption'), $order_option['value']);
				}
			}
			$product_id=$product['product_id'];
			$cproduct_id=$product['cproduct_id'];
			$qty=$product['quantity'];				
			$this->pos->add($product_id, $qty, $option_data,'',$cproduct_id);			
		}
	}

	$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
	if(isset($query->row['order_id'])) {
		foreach($query->rows as $order_total) {

			if($order_total['code']=='coupon') {
				$this->session->data['coupondiscount']=str_replace('-','',$order_total['value']);
				$this->session->data['coupon']=$order_total['title'];
			}
			if($order_total['code']=='mdiscount')
			{
				$this->session->data['mdiscount']=str_replace('-','',$order_total['value']);
			}
		}			
	}
}

public function getOrderProducts($order_id) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

	return $query->rows;
}

public function getOrderOptions($order_id, $order_product_id) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

	return $query->rows;
}
public function getOrderVouchers($order_id) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

	return $query->rows;
}	
public function getOrderTotals($order_id) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

	return $query->rows;
}
public function getOrderAmount($data)
{
	$implode = array();

	foreach ($this->config->get('config_complete_status') as $order_status_id) {
		$implode[] = "'" . (int)$order_status_id . "'";
	}

	$sql ="SELECT sum(total) as total FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND DATE(date_added) = DATE(NOW())";

	if(isset($data['payment_method']) && $data['payment_method']==1)
	{
		$sql .=" and payment_method='cash'";
	}
	else if(isset($data['payment_method']) && $data['payment_method']==2)
	{
		$sql .=" and payment_method='card'";
	}


	$query = $this->db->query($sql);
	if(isset($query->row['total']))
	{
		return $query->row['total'];
	}
	else
	{
		return 0;
	}


}

/// 12 05 2018 /////

public function getDashboardOrderAmount($orderstatus_data,$payment_method_data,$daytype,$custom_figure){

		//sharma for pos edited to add posapp
	if(isset($this->request->get['posapp_id'])) {
		$checkout_posapp_id = $this->request->get['posapp_id'];
	} elseif(isset($this->session->data['posapp_id'])){
		$checkout_posapp_id = $this->session->data['posapp_id'];
	} else{
		$checkout_posapp_id = 0; 
	}
	////sharma for pos

	$implode = array();
	if (isset($payment_method_data)) {
		foreach ($payment_method_data as $payment_method) {
			$implode[] = " payment_code='" . $payment_method . "'";
		}
	}

	$implode1 = array();
	if (isset($orderstatus_data)) {
		foreach ($orderstatus_data as $order_status_id) {
			$implode1[] = " order_status_id='" . (int)$order_status_id . "'";
		}
	}
	////sharma for pos

	$implode2 = array();	
	if (isset($custom_figure)) {
		foreach ($custom_figure as $custom_figure_value) {
			if(($custom_figure_value == 'online_sale') && ($custom_figure_value == 'pos_sale')){
				$implode2[] = " order_id > 0";
			}else{
				if($custom_figure_value == 'online_sale'){
					$implode2[] = " order_id NOT IN (SELECT order_id FROM " . DB_PREFIX . "pos_order WHERE posapp_id = '". (int)$checkout_posapp_id ."')";
				}elseif($custom_figure_value == 'pos_sale'){
					$implode2[] = " order_id IN (SELECT order_id FROM " . DB_PREFIX . "pos_order WHERE posapp_id = '". (int)$checkout_posapp_id ."')";
				}
			}
		}
	}
	////sharma for pos

	$sql ="SELECT sum(total) as total FROM `" . DB_PREFIX . "order` WHERE order_status_id<>0 AND posapp_id = '" . (int)$checkout_posapp_id . "'";

	if ($implode) {
		$sql .= " and  (" . implode(" OR ", $implode) . ")";
	}


	if ($implode1) {
		$sql .= " and (" . implode(" OR ", $implode1) . ")";
	}
	////sharma for pos

	if ($implode2) {
		$sql .= " and (" . implode(" OR ", $implode2) . ")";
	}
	////sharma for pos

	

	switch($daytype) {
		case '1Day';
		$sql .= " and date(date_added)='".date('Y-m-d')."'";
		break;
		case '1Month':
		$sql .= " GROUP BY   MONTH(date_added)";
		break;
		case '1Year':
		$sql .= " GROUP BY YEAR(date_added)";
		break;
	}

	$query = $this->db->query($sql);
	if(isset($query->row['total'])){
		return $query->row['total'];
	} else {
		return 0;
	}

}

	///sharma for web
public function getWebOrders($posapp_id){  

	
	$sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "order` WHERE `order_id` NOT IN (SELECT `order_id` FROM `" . DB_PREFIX . "pos_order` WHERE `posapp_id` = '". (int)$posapp_id ."') AND `order_status_id` <> 0 AND `posapp_id` = '". (int)$posapp_id ."'";
	
	$query = $this->db->query($sql);
	return $query->row['total'];

}
public function getDueOrders($data = array()) {		
	if(isset($this->request->get['posapp_id'])) {
		$checkout_posapp_id = $this->request->get['posapp_id'];
	} elseif(isset($this->session->data['posapp_id'])){
		$checkout_posapp_id = $this->session->data['posapp_id'];
	} else{
		$checkout_posapp_id = 0; 
	}

	$sql = "SELECT DISTINCT(o.order_id), o.order_status_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified, sum(pp.partial_amount) as partial_amount, o.telephone FROM `" . DB_PREFIX . "partial_payment` pp LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = pp.order_id) ";


	if (!empty($data['filter_order_status'])) {
		$implode = array();

		$order_statuses = explode(',', $data['filter_order_status']);

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$sql .= " WHERE o.posapp_id = '" . (int)$checkout_posapp_id . "' AND (" . implode(" OR ", $implode) . ")";
		}
	} else {
		$sql .= " WHERE o.posapp_id = '" . (int)$checkout_posapp_id . "' AND o.order_status_id > '0'";
	}
	

	if (!empty($data['filter_order_id'])) {
		$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
	}

	if (!empty($data['filter_customer'])) {
		$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
	}

	if (!empty($data['filter_order_status'])) {
		$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status'] . "'";
	}

	if (!empty($data['filter_date_from'])) {
		$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
	}

	if (!empty($data['filter_date_to'])) {
		$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
	}


	if (!empty($data['filter_total'])) {
		$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
	}
	$sql .= " GROUP BY o.order_id";

	$sort_data = array(
		'o.order_id',
		'customer',
		'order_status',
		'o.date_added',
		'o.date_modified',
		'o.total'
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
public function getTotalDueOrders($data = array()) {		
	if(isset($this->request->get['posapp_id'])) {
		$checkout_posapp_id = $this->request->get['posapp_id'];
	} elseif(isset($this->session->data['posapp_id'])){
		$checkout_posapp_id = $this->session->data['posapp_id'];
	} else{
		$checkout_posapp_id = 0; 
	}

	$sql = "SELECT count(DISTINCT(o.order_id)) as total FROM `" . DB_PREFIX . "partial_payment` pp LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = pp.order_id) ";


	if (!empty($data['filter_order_status'])) {
		$implode = array();

		$order_statuses = explode(',', $data['filter_order_status']);

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$sql .= " WHERE o.posapp_id = '" . (int)$checkout_posapp_id . "' AND (" . implode(" OR ", $implode) . ")";
		}
	} else {
		$sql .= " WHERE o.posapp_id = '" . (int)$checkout_posapp_id . "' AND o.order_status_id > '0'";
	}
	

	if (!empty($data['filter_order_id'])) {
		$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
	}

	if (!empty($data['filter_customer'])) {
		$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
	}

	if (!empty($data['filter_order_status'])) {
		$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status'] . "'";
	}

	if (!empty($data['filter_date_from'])) {
		$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
	}

	if (!empty($data['filter_date_to'])) {
		$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
	}


	if (!empty($data['filter_total'])) {
		$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
	}
	// $sql .= " GROUP BY o.order_id";

	$sort_data = array(
		'o.order_id',
		'customer',
		'order_status',
		'o.date_added',
		'o.date_modified',
		'o.total'
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

	return $query->row['total'];
}


	///sharma for web




public function getOrders($data = array()) {

		//sharma for pos edited to add posapp
	if(isset($this->request->get['posapp_id'])) {
		$checkout_posapp_id = $this->request->get['posapp_id'];
	} elseif(isset($this->session->data['posapp_id'])){
		$checkout_posapp_id = $this->session->data['posapp_id'];
	} else{
		$checkout_posapp_id = 0; 
	}

	$sql = "SELECT o.order_id,o.order_status_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";


	if(isset($this->session->data['store_id']) ||  isset($data['filter_user']) ||  isset($data['filter_store']))
	{
		$sql .=" left join `" . DB_PREFIX . "pos_order` po on po.order_id=o.order_id";
	}

	if (!empty($data['filter_order_status'])) {
		$implode = array();

		$order_statuses = explode(',', $data['filter_order_status']);

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$sql .= " WHERE o.posapp_id = '" . (int)$checkout_posapp_id . "' AND (" . implode(" OR ", $implode) . ")";
		}
	} else {
		$sql .= " WHERE o.posapp_id = '" . (int)$checkout_posapp_id . "' AND o.order_status_id > '0'";
	}


	if(isset($this->session->data['store_id']))
	{
		$sql .=" and po.user_id='".$this->user->getId()."'";
	}
	if(isset($data['filter_user']))
	{
		$sql .=" and po.user_id='".$data['filter_user']."'";
	}
	if(isset($data['filter_store']))
	{
		$sql .=" and po.store_id='".$data['filter_store']."'";
	}


	if (!empty($data['filter_order_id'])) {
		$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
	}

	if (!empty($data['filter_customer'])) {
		$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
	}

	if (!empty($data['filter_order_status'])) {
		$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status'] . "'";
	}

	if (!empty($data['filter_date_from'])) {
		$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
	}

	if (!empty($data['filter_date_to'])) {
		$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
	}


		/*if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}*/

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'customer',
			'order_status',
			'o.date_added',
			'o.date_modified',
			'o.total'
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

	public function getTotalOrders($data = array()) {

		//sharma for pos edited to add posapp
		if(isset($this->request->get['posapp_id'])) {
			$checkout_posapp_id = $this->request->get['posapp_id'];
		} elseif(isset($this->session->data['posapp_id'])){
			$checkout_posapp_id = $this->session->data['posapp_id'];
		} else{
			$checkout_posapp_id = 0; 
		}


		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o";
		
		

		if(isset($this->session->data['store_id']) ||  isset($data['filter_user']) ||  isset($data['filter_store']))
		{
			$sql .=" left join `" . DB_PREFIX . "pos_order` po on po.order_id=o.order_id";
		}
		

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE o.posapp_id = '" . (int)$checkout_posapp_id . "' AND (" . implode(" OR ", $implode) . ")";
			}
		} else {
			$sql .= " WHERE o.posapp_id = '" . (int)$checkout_posapp_id . "' AND o.order_status_id > '0'";
		}
		

		
		
		if(isset($this->session->data['store_id']))
		{
			$sql .=" and po.user_id='".$this->user->getId()."'";
		}
		if(isset($data['filter_user']))
		{
			$sql .=" and po.user_id='".$data['filter_user']."'";
		}
		if(isset($data['filter_store']))
		{
			$sql .=" and po.store_id='".$data['filter_store']."'";
		}
		
		
		
		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false, $override = false) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '".$order_status_id."', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "',order_status_id = '".$order_status_id."', notify = '0', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}

	public function getOrderStatus($order_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' and language_id='".(int)$this->config->get('config_language_id')."'");

		return $query->row;
	}
	
	public function orderemail($order_id) {
		$this->load->language('possetting/posordermail');

		$mailstatus = $this->config->get('setting_ordermail');
		if(!empty($mailstatus)) {
			$order_info = $this->getOrder($order_id);
			
			if(!empty($order_info['email'])) {

				$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_info['order_status_id']. "' AND language_id = '" . (int)$order_info['language_id'] . "'");

				if ($order_status_query->num_rows) {
					$order_status = $order_status_query->row['name'];
				} else {
					$order_status = '';
				}

				$subject = '';

				// HTML Mail
				$data = array();

				

				$order_id = $order_id;
				$date_added = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
				$payment_method = $order_info['payment_method'];
				$shipping_method = $order_info['shipping_method'];
				$email = $order_info['email'];
				$telephone = $order_info['telephone'];
				$ip = $order_info['ip'];
				$order_status = $order_status;

				if (isset($order_info['comment'])) {
					$comment = nl2br($order_info['comment']);
				} else {
					$comment = '';
				}

				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
				);

				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);

				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$this->load->model('tool/upload');
				$this->load->model('tool/image');
				// Products
				$data['products'] = array();
				$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
				foreach ($order_product_query->rows as $product) {

					$productquery = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product['product_id'] . "'");

					if (isset($productquery->row['image'])) {
						$image = $this->model_tool_image->resize($productquery->row['image'],100,100);
					} else {
						$image = '';
					}
					
					
					$option_data = array();

					$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

					foreach ($order_option_query->rows as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

							if ($upload_info) {
								$value = $upload_info['name'];
							} else {
								$value = '';
							}
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
						);
					}

					$data['products'][] = array(
						'name'     => $product['name'],
						'image'     => $image,
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				// Vouchers
				$data['vouchers'] = array();

				$order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

				foreach ($order_voucher_query->rows as $voucher) {
					$data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
					);
				}

				// Order Totals
				$data['totals'] = array();
				
				$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

				foreach ($order_total_query->rows as $total) {
					$data['totals'][] = array( 
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
					);
				}
				
				$data['text_product']=$this->language->get('text_product');
				$data['text_model']=$this->language->get('text_model');
				$data['text_quantity']=$this->language->get('text_quantity');
				$data['text_price']=$this->language->get('text_price');
				$data['text_total']=$this->language->get('text_total');
				$data['text_image']=$this->language->get('text_image');
				
				$productdata	= $this->load->view('possetting/posordermail', $data);
				$sub_info 		= $this->config->get('setting_orders_subject');
				$msg_info 		= $this->config->get('setting_orders_message');
			/*	
				$find = array(
					'{order_id}',
					'{date_added}',
					'{payment_method}',
					'{shipping_method}',
					'{payment_address}',
					'{shipping_address}',
					'{email}',
					'{telephone}',
					'{ip}',
					'{order_status}',
					'{productdata}'
				);

				$replace = array(
					'order_id' 		=> $order_id,
					'date_added' 	=> $date_added,
					'payment_method'=> $payment_method,
					'shipping_method'=> $shipping_method,
					'payment_address'=> $payment_address,
					'shipping_address'=> $shipping_address,
					'email' 		=> $email,
					'telephone' 	=> $telephone,
					'ip' 			=> $ip,
					'order_status' 	=> $order_status,
					'productdata,' 	=> $productdata
				);
*/
				$find = array(
					'{order_id}',
					'{firstname}',
					'{lastname}',
					'{email}',
					'{telephone}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{fax}',
					'{country}',
					'{productdata}',
					'{order_status}',
					'{payment_method}',
					'{date_added}',
				);

				$replace = array(
					'order_id' => $order_info['order_id'],
					'firstname' => $order_info['firstname'],
					'lastname'  => $order_info['lastname'],
					'email' 	=> $order_info['email'],
					'telephone' => $telephone,
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'fax' 		=> $order_info['fax'],
					'country'   => $order_info['shipping_country'],
					'productdata'   => $productdata,
					'order_status' 	=> $order_status,
					'payment_method' 	=> $payment_method,
					'date_added' 	=> $date_added,
				);

				$subject 		= str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $sub_info))));

				$ordermessage 	= str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $msg_info))));

				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($order_info['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setHtml(html_entity_decode($ordermessage));
				
				$mail->send();


				$sub_info 		= $this->config->get('setting_orders_subject_admin');
				$msg_info 		= $this->config->get('setting_orders_message_admin');
				$subject 		= str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $sub_info))));

				$ordermessage 	= str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $msg_info))));

				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($this->config->get('config_email'));
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setHtml(html_entity_decode($ordermessage));
				
				$mail->send();

			}	
		}
	}

}