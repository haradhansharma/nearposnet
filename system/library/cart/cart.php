<?php
namespace Cart;
class Cart {
	private $data = array();

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');

		// Remove all the expired carts with no customer ID
		$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE (api_id > '0' OR customer_id = '0') AND date_added < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

		if ($this->customer->getId()) {
			// We want to change the session ID on all the old items in the customers cart
			$this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE api_id = '0' AND customer_id = '" . (int)$this->customer->getId() . "'");

			// Once the customer is logged in we want to update the customers cart
			$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '0' AND customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

			foreach ($cart_query->rows as $cart) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart['cart_id'] . "'");

				// The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
				$this->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
			}
		}
	}

	////sharma for pos

	public function duePayment($posapp_id){
		$query = $this->db->query("SELECT sum(o.total) as total, sum(pp.partial_amount) as partial_amount FROM `" . DB_PREFIX . "partial_payment` pp LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = pp.order_id) WHERE o.posapp_id = '". (int)$posapp_id ."' ");
		if($query->num_rows){
			$total = round($query->row['total'], 2);
			$partial_amount = round($query->row['partial_amount'], 2);
			$due_amount = $total - $partial_amount;
		}else{
			$due_amount = 0;
		}
		return $due_amount;
	}
	////sharma for pos


	public function getProducts() {
		$product_data = array();

		$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

		foreach ($cart_query->rows as $cart) {
			$stock = true;

			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");

			if ($product_query->num_rows && ($cart['quantity'] > 0)) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;

				$option_data = array();

				foreach (json_decode($cart['option']) as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
									$stock = false;
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}

				$price = $product_query->row['price'];

				// Product Discounts
				$discount_quantity = 0;

				foreach ($cart_query->rows as $cart_2) {
					if ($cart_2['product_id'] == $cart['product_id']) {
						$discount_quantity += $cart_2['quantity'];
					}
				}

				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}

				// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}

				// Reward Points
				$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($product_reward_query->num_rows) {
					$reward = $product_reward_query->row['points'];
				} else {
					$reward = 0;
				}

				// Downloads
				$download_data = array();

				$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$cart['product_id'] . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

				foreach ($download_query->rows as $download) {
					$download_data[] = array(
						'download_id' => $download['download_id'],
						'name'        => $download['name'],
						'filename'    => $download['filename'],
						'mask'        => $download['mask']
					);
				}

				// Stock
				if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $cart['quantity'])) {
					$stock = false;
				}

				$recurring_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r LEFT JOIN " . DB_PREFIX . "product_recurring pr ON (r.recurring_id = pr.recurring_id) LEFT JOIN " . DB_PREFIX . "recurring_description rd ON (r.recurring_id = rd.recurring_id) WHERE r.recurring_id = '" . (int)$cart['recurring_id'] . "' AND pr.product_id = '" . (int)$cart['product_id'] . "' AND rd.language_id = " . (int)$this->config->get('config_language_id') . " AND r.status = 1 AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($recurring_query->num_rows) {
					$recurring = array(
						'recurring_id'    => $cart['recurring_id'],
						'name'            => $recurring_query->row['name'],
						'frequency'       => $recurring_query->row['frequency'],
						'price'           => $recurring_query->row['price'],
						'cycle'           => $recurring_query->row['cycle'],
						'duration'        => $recurring_query->row['duration'],
						'trial'           => $recurring_query->row['trial_status'],
						'trial_frequency' => $recurring_query->row['trial_frequency'],
						'trial_price'     => $recurring_query->row['trial_price'],
						'trial_cycle'     => $recurring_query->row['trial_cycle'],
						'trial_duration'  => $recurring_query->row['trial_duration']
					);
				} else {
					$recurring = false;
				}

				$product_data[] = array(
					'cart_id'         => $cart['cart_id'],
					'product_id'      => $product_query->row['product_id'],
					////sharma for pos
					'posapp_id'      => $this->getProductPosapp($product_query->row['product_id']),
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'shipping'        => $product_query->row['shipping'],
					'image'           => $product_query->row['image'],
					'option'          => $option_data,
					'download'        => $download_data,
					'quantity'        => $cart['quantity'],
					'minimum'         => $product_query->row['minimum'],
					'subtract'        => $product_query->row['subtract'],
					'stock'           => $stock,
					'price'           => ($price + $option_price),
					'total'           => ($price + $option_price) * $cart['quantity'],
					'reward'          => $reward * $cart['quantity'],
					'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $cart['quantity'] : 0),
					'tax_class_id'    => $product_query->row['tax_class_id'],
					'weight'          => ($product_query->row['weight'] + $option_weight) * $cart['quantity'],
					'weight_class_id' => $product_query->row['weight_class_id'],
					'length'          => $product_query->row['length'],
					'width'           => $product_query->row['width'],
					'height'          => $product_query->row['height'],
					'length_class_id' => $product_query->row['length_class_id'],
					'recurring'       => $recurring
				);
			} else {
				$this->remove($cart['cart_id']);
			}
		}

		return $product_data;
	}

	public function add($product_id, $quantity = 1, $option = array(), $recurring_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND recurring_id = '" . (int)$recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");

		if (!$query->row['total']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "cart SET api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "', customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session->getId()) . "', product_id = '" . (int)$product_id . "', recurring_id = '" . (int)$recurring_id . "', `option` = '" . $this->db->escape(json_encode($option)) . "', quantity = '" . (int)$quantity . "', date_added = NOW()");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = (quantity + " . (int)$quantity . ") WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND recurring_id = '" . (int)$recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
		}
	}

	public function update($cart_id, $quantity) {
		$this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function remove($cart_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function clear() {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	// public function getRecurringProducts() {
	// 	$product_data = array();

	// 	foreach ($this->getProducts() as $value) {
	// 		if ($value['recurring']) {
	// 			$product_data[] = $value;
	// 		}
	// 	}

	// 	return $product_data;
	// }

	public function getRecurringProducts() {
		$product_data = array();

		if(isset($this->session->data['checkout_posapp_id'])){
			$posapp_id = $this->session->data['checkout_posapp_id']; 

			foreach ($this->getProducts2($posapp_id ) as $value) {
				if ($value['recurring']) {
					$product_data[] = $value;
				}
			}
		}else{
			$posapp_id = ''; 
			foreach ($this->getProducts() as $value) {
				if ($value['recurring']) {
					$product_data[] = $value;
				}
			}

		}

		return $product_data;
	}

	// public function getWeight() {
	// 	$weight = 0;

	// 	foreach ($this->getProducts() as $product) {
	// 		if ($product['shipping']) {
	// 			$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
	// 		}
	// 	}

	// 	return $weight;
	// }

	public function getWeight() {
		$weight = 0;
		if(isset($this->session->data['checkout_posapp_id'])){
			$posapp_id = $this->session->data['checkout_posapp_id']; 
			foreach ($this->getProducts2($posapp_id) as $product) {
				if ($product['shipping']) {
					$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
				}
			}
		}else{
			$posapp_id = '';
			foreach ($this->getProducts() as $product) {
				if ($product['shipping']) {
					$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
				}
			}
		}

		return $weight;
	}

	// public function getSubTotal() {
	// 	$total = 0;

	// 	foreach ($this->getProducts() as $product) {
	// 		$total += $product['total'];
	// 	}

	// 	return $total;
	// }
	public function getSubTotal() {
		$total = 0;

		if(isset($this->session->data['checkout_posapp_id'])){
			$posapp_id = $this->session->data['checkout_posapp_id']; 			
			foreach ($this->getProducts2( $posapp_id) as $product) {
				$total += $product['total'];
			}
		}elseif(isset($this->session->data['selected_order_id'])){					
			foreach ($this->getOrderData() as $selected_order_id) {
				$total += $selected_order_id['payable'];
			}
		}else{
			$posapp_id = ''; 
			foreach ($this->getProducts() as $product) {	
				$total += $product['total'];
			}
		}

		return $total;
	}

	public function getTaxes() {
		$tax_data = array();

		if(isset($this->session->data['selected_order_id'])){

			foreach ($this->getOrderData() as $selected_order) {
				if ($this->config->get('module_posapp_tax_class_id')) {
					$tax_rates = $this->tax->getRates($selected_order['payable'], $this->config->get('module_posapp_tax_class_id'));

					foreach ($tax_rates as $tax_rate) {
						if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
							$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * 1);
						} else {
							$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * 1);
						}
					}
				}
			}

		}else{

			foreach ($this->getProducts() as $product) {
				if ($product['tax_class_id']) {
					$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

					foreach ($tax_rates as $tax_rate) {
						if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
							$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
						} else {
							$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
						}
					}
				}
			}
		}

		return $tax_data;
	}

	// public function getTotal() {
	// 	$total = 0;

	// 	foreach ($this->getProducts() as $product) {
	// 		$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
	// 	}

	// 	return $total;
	// }

	public function getTotal() {
		$total = 0;
		if(isset($this->session->data['checkout_posapp_id'])){
			$posapp_id = $this->session->data['checkout_posapp_id']; 
			foreach ($this->getProducts2($posapp_id) as $product) {
				$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
			}
		}elseif(isset($this->session->data['selected_order_id'])){			
			foreach ($this->getOrderData() as $selected_order_id) {
				$total += $this->tax->calculate($selected_order_id['payable'], $this->config->get('module_posapp_tax_class_id'), $this->config->get('config_tax')) * 1;
			}
		}else{
			$posapp_id = ''; 
			foreach ($this->getProducts() as $product) {
				$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
			}
		}

		return $total;
	}

	public function countProducts() {
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts() {
		return count($this->getProducts());
	}

	public function hasRecurringProducts() {
		return count($this->getRecurringProducts());
	}

	public function hasStock() {
		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
				return false;
			}
		}

		return true;
	}

	// public function hasShipping() {
	// 	foreach ($this->getProducts() as $product) {
	// 		if ($product['shipping']) {
	// 			return true;
	// 		}
	// 	}

	// 	return false;
	// }

	public function hasShipping() {
		if(isset($this->session->data['checkout_posapp_id'])){
			$posapp_id = $this->session->data['checkout_posapp_id'];
			foreach ($this->getProducts2($posapp_id) as $product) {
				if ($product['shipping']) {
					return true;
				}
			}
		}else{
			$posapp_id = '';
			foreach ($this->getProducts() as $product) {
				if ($product['shipping']) {
					return true;
				}
			}
		}
		return false;
	}

	public function hasDownload() {
		foreach ($this->getProducts() as $product) {
			if ($product['download']) {
				return true;
			}
		}

		return false;
	}

	public function getCommissionPending(){	
		
		$deduct_status = $this->config->get('config_complete_status');	
		$total = 0;
		$sql="SELECT o.order_id, o.posapp_id, o.total, pcd.commission_value FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "pos_order` po ON (po.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "pos_user` pu ON (pu.user_id = po.user_id) LEFT JOIN `" . DB_PREFIX . "pos_commission_details` pcd ON (pcd.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "pos_commission_order` pco ON (pco.commission_order_id = pcd.commission_order_id) ";
		$sql .=" WHERE  o.posapp_id = '". $this->session->data['posapp_id'] ."'  AND pco.order_status_id IS NULL ";
		$payable = 0;		
		$query = $this->db->query($sql);
		foreach($query->rows as $result){
			if($result['commission_value']){
				$commission_value = $result['commission_value'];
			}else{
				$commission_value = $this->config->get('module_posapp_commission_value');
			}
			$payable += ((int)$result['total'] *  (int)$commission_value )/100 ;		
		}

		$sql2="SELECT o.order_id, o.posapp_id, o.total, pcd.commission_value FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "pos_order` po ON (po.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "pos_user` pu ON (pu.user_id = po.user_id) LEFT JOIN `" . DB_PREFIX . "pos_commission_details` pcd ON (pcd.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "pos_commission_order` pco ON (pco.commission_order_id = pcd.commission_order_id) ";
		$sql2 .=" WHERE  o.posapp_id = '". $this->session->data['posapp_id'] ."'  AND pco.order_status_id NOT IN (".implode(',',$deduct_status).") ";
		$payable2 = 0;		
		$query2 = $this->db->query($sql2);
		foreach($query2->rows as $result2){
			if($result2['commission_value']){
				$commission_value2 = $result2['commission_value'];
			}else{
				$commission_value2 = $this->config->get('module_posapp_commission_value');
			}
			$payable2 += ((int)$result['total'] *  (int)$commission_value )/100 ;			
		}
		return $total += $payable + $payable2;
	}

	public function getRegdiff(){

		$query = $this->db->query("SELECT `date_added` FROM `vp_pos_user` WHERE `posapp_id` = '". $this->session->data['posapp_id'] ."' ");	
		
		$approved = date_create($query->row['date_added']);
		$approved->format("Y-m-d\TH:i:sP");
		$current = date_create(date("Y-m-d"));
		$current->format("Y-m-d\TH:i:sP");
		$interval = date_diff($approved, $current);
		$diff =$interval->format('%d');

		return $diff;	

 //        return $interval->format('%Y years %M months and %D days %H hours %I minutes and %S seconds.');

	}

	public function getOrderData(){

		$selected_orders = array();

		foreach($this->session->data['selected_order_id'] as $selected_order_id){

			$query = $this->db->query(" SELECT pa.posname, o.order_id, o.total as total, po.posapp_id, pu.commission_value, pu.commission FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "pos_order po ON (o.order_id = po.order_id) LEFT JOIN " . DB_PREFIX . "pos_application pa ON (pa.posapp_id = po.posapp_id) LEFT JOIN " . DB_PREFIX . "pos_user pu ON (pu.user_id = po.user_id) WHERE o.order_status_id <> 0 AND o.total >0 AND o.order_id = '". (int)$selected_order_id ."' ");

			foreach($query->rows as $selected_order){
				if($selected_order['commission_value'] != null){
					$commission_value = $selected_order['commission_value'];
				}else{
					$commission_value = $this->config->get('module_posapp_commission_value');
				}
				if($commission_value){
					$payable = ($commission_value * $selected_order['total'])/100;
				}else{
					$payable = 0;
				}
				if($payable){
					$tax = $this->tax->getTax($payable, $this->config->get('module_posapp_tax_class_id'));
				}else{
					$tax = 0;
				}
				if($selected_order['posapp_id'] > 0 ){
					$posname = $this->getPosname($selected_order['posapp_id']);
				}else{
					$posname = $this->config->get('config_name');

				}
				$selected_orders[] = array(
					'order_id' => $selected_order['order_id'],
					'posname' => $posname,
					'total' => $selected_order['total'],
					'posapp_id' => $selected_order['posapp_id'],
					'commission_value' => $commission_value,
					'commission' => $selected_order['commission'],
					'payable' => $payable,
					'tax' => $tax,
					'order_id' => $selected_order['order_id'],
					'order_id' => $selected_order['order_id']
				);

			}

		}
		return $selected_orders;
	}

	// public function getOrderSubTotal() {
	// 	$total = 0;

	// 	foreach ($this->getOrderData() as $selected_order) {
	// 		$total += $selected_order['payable'];
	// 	}		

	// 	return $total;
	// }

	// public function getOrderTaxes() {
	// 	$tax_data = array();

	// 	foreach ($this->getOrderData() as $selected_order) {
	// 		if ($this->config->get('module_posapp_tax_class_id')) {
	// 			$tax_rates = $this->tax->getRates($selected_order['payable'], $this->config->get('module_posapp_tax_class_id'));

	// 			foreach ($tax_rates as $tax_rate) {
	// 				if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
	// 					$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * 1);
	// 				} else {
	// 					$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * 1);
	// 				}
	// 			}
	// 		}
	// 	}

	// 	return $tax_data;
	// }

}
