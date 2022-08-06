<?php
class ModelPosReturnOrder extends Model {
	public function getOrder($order_id) {
		//sharma for pos edited to add posapp
		    if(isset($this->request->get['posapp_id'])) {
				$checkout_posapp_id = $this->request->get['posapp_id'];
			} elseif(isset($this->session->data['posapp_id'])){
				$checkout_posapp_id = $this->session->data['posapp_id'];
			} else{
				$checkout_posapp_id = 0; 
			}

		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND order_status_id > '0' AND posapp_id = '" . (int)$checkout_posapp_id . "'");

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
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
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
				'payment_method'          => $order_query->row['payment_method'],
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
				'shipping_method'         => $order_query->row['shipping_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
		} else {
			return false;
		}
	}

	public function getOrders($start = 0, $limit = 20) {

		//sharma for pos edited to add posapp
		    if(isset($this->request->get['posapp_id'])) {
				$checkout_posapp_id = $this->request->get['posapp_id'];
			} elseif(isset($this->session->data['posapp_id'])){
				$checkout_posapp_id = $this->session->data['posapp_id'];
			} else{
				$checkout_posapp_id = 0; 
			}
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.order_status_id > '0' AND o.posapp_id = '" . (int)$checkout_posapp_id . "' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getOrderProduct($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getTmdTaxes($order_id,$order_status_id) {
		$tax_data = array();

		$this->load->model('catalog/product');

		$pro_infos = $this->getOrderProducts($order_id);
		$total =0;
		foreach ($pro_infos as $product) {
			$product2 = $this->model_catalog_product->getProduct($product['product_id']);
			if ($product2['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['price'], $product2['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
			$total +=$product['total'];
		}
		
		$totals = array();
		$totals[] = array(
			'code'       => 'sub_total',
			'title'      => 'Sub Total',
			'value'      => $total,
			'sort_order' => '0'
		);

		foreach ($tax_data as $key => $value) {
			if ($value > 0) {
				$totals[] = array(
					'code'       => 'tax',
					'title'      => $this->tax->getRateName($key),
					'value'      => $value,
					'sort_order' => '4'
				);
				$total +=$value;
			}
		}
		$totals[] = array(
			'code'       => 'total',
			'title'      => 'Grand Total',
			'value'      => $total,
			'sort_order' => '5'
		);

		if (!empty($totals)) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
			foreach ($totals as $total2) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total2['code']) . "', title = '" . $this->db->escape($total2['title']) . "', `value` = '" . (float)$total2['value'] . "', sort_order = '" . (int)$total2['sort_order'] . "'");
			}
			$this->db->query("UPDATE " . DB_PREFIX . "order SET total = '" . (float)$total . "',order_status_id = '" . (int)$order_status_id . "' WHERE order_id = '" . (int)$order_id . "'");
		}
	}

	////sharma for pos
	public function getPosappForOrder($order_id)
	{
		$query = $this->db->query("SELECT posapp_id FROM " . DB_PREFIX . "pos_order WHERE order_id = '" . (int)$order_id . "'");
		return $query->row['posapp_id'];
	}
	////sharma for pos

	public function addReturn($data=array()) 
	{	
		$returnStatus = false;
		foreach ($data['product_return'] as $order_product_id => $result) {
			if (!empty($result['quantity'])) {
				$this->load->model('catalog/product');
				$pro_info = $this->getOrderProduct($result['order_id'],$order_product_id);
				if (!empty($pro_info) && $result['quantity'] >= $result['quantity']) {
					$this->db->query("UPDATE " . DB_PREFIX . "order_product SET `total` = (total - " . (float)$pro_info['price']*$result['quantity'] . "),`quantity` = (quantity - " . (int)$result['quantity'] . ")  WHERE order_product_id = '" . (int)$order_product_id . "'");
					$this->db->query("UPDATE " . DB_PREFIX . "product SET `quantity` = (quantity + " . (int)$result['quantity'] . ")  WHERE product_id = '" . (int)$pro_info['product_id'] . "'");

					//$query = $this->db->query("DELETE FROM " . DB_PREFIX . "pos_order_return WHERE order_id='".$result['order_id']."' AND order_product_id='".$order_product_id."' AND product_id='".$pro_info['product_id']."'");


					$query = $this->db->query("INSERT INTO " . DB_PREFIX . "pos_order_return SET order_id='".$result['order_id']."', posapp_id='".$this->getPosappForOrder($result['order_id'])."', order_product_id='".$order_product_id."',product_id='".$pro_info['product_id']."',name='".$pro_info['name']."',model='".$pro_info['model']."',price='".$pro_info['price']."',reason='".$result['reason']."',quantity='".$result['quantity']."',order_status_id='".$data['order_status_id']."',date_added=now()");
					$return_id = $this->db->getLastId();
					$opt_pro_infos = $this->getOrderOptions($result['order_id'],$order_product_id);
					$returnStatus = true;
					$order_id = $result['order_id'];
					if(!empty($opt_pro_infos)){
						foreach ($opt_pro_infos as $value) {
							$query = $this->db->query("INSERT INTO " . DB_PREFIX . "pos_return_option SET return_id='".$return_id."',order_id='".$result['order_id']."',order_product_id='".$order_product_id."',product_option_id='".$value['product_option_id']."',product_option_value_id='".$value['product_option_value_id']."',name='".$value['name']."',value='".$value['value']."',type='".$value['type']."'");
						}
					}
				}
			}
		}

		if ($returnStatus) {
			$this->getTmdTaxes($order_id,$data['order_status_id']);
		}

		return $order_id;
	}
}