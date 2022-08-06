<?php
class ControllerCheckoutShippingMethod extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');
		unset($this->session->data['pos_distance']);

		if (isset($this->session->data['shipping_address'])) {
			// Shipping Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);

					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$this->session->data['shipping_methods'] = $method_data;
		}

		////sharma for pos

		if(isset($this->session->data['pos_distance'])){
			if (empty($this->session->data['shipping_methods'])) {
				$data['error_warning'] = sprintf($this->language->get('error_distance'), $this->session->data['pos_distance'], $this->cart->getPosname((int)$this->session->data['checkout_posapp_id']), $this->cart->getAlloweddistance((int)$this->session->data['checkout_posapp_id']) . ' km', $this->url->link('product/allpos'));
			} else {
				$data['error_warning'] = '';
			}

		}else{////sharma for pos additional

			if (empty($this->session->data['shipping_methods'])) {
				$data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
			} else {
				$data['error_warning'] = '';
			}

	  }////sharma for pos aditional

	  if (isset($this->session->data['shipping_methods'])) {
	  	$data['shipping_methods'] = $this->session->data['shipping_methods'];
	  } else {
	  	$data['shipping_methods'] = array();
	  }

	  if (isset($this->session->data['shipping_method']['code'])) {
	  	$data['code'] = $this->session->data['shipping_method']['code'];
	  } else {
	  	$data['code'] = '';
	  }

	  if (isset($this->session->data['comment'])) {
	  	$data['comment'] = $this->session->data['comment'];
	  } else {
	  	$data['comment'] = '';
	  }


        ////sharma for pos

	  if(isset($this->session->data['pos_distance'])){
	  	$this->load->model('tool/image'); 
	  	$this->load->model('extension/shipping/posship'); 
	  	$apikey = $this->config->get('module_posapp_apikey');


	  	$address = array(
	  		'address_1' => $this->session->data['shipping_address']['address_1'],
	  		'city'      => $this->session->data['shipping_address']['city'],
	  		'zone'      => $this->session->data['shipping_address']['zone'],
	  		'country'   => $this->session->data['shipping_address']['country']
	  	); 


	  	if ($address) {
	  		$coords = $this->model_extension_shipping_posship->getCoordinatesCustomer($address['address_1'] . ' ' . $address['city'] . ' ' . $address['zone'] . ' ' . $address['country'], $apikey);
	  	} else {
	  		$coords = array('lng' => 0, 'lat' => 0);
	  	}

	  	$pos_units= array(
	  		'k' => array('km', 1.60934),
	  		'm' => array('miles', 1)
	  	);

	  	if ($coords) {
	  		$units = (array)$pos_units;
	  		$unit = $units['k'];
	  	}
	  	
	  	$limit = 6;
	  	$filter_data = array(
	  		'longitude' => $coords['lng'],
	  		'latitude'  => $coords['lat'],			
	  		'limit'     => $limit
	  	);

	  	
	  	$data['nearest_poses'] = array();        
	  	$nearest_poses = $this->model_extension_shipping_posship->getNearestPos($filter_data );
	  	foreach($nearest_poses as $nearest_pose){
	  		$data['nearest_poses'][] = array(
	  			'posapp_id' => $nearest_pose['posapp_id'],
	  			'posname' => $nearest_pose['posname'],
	  			'frontimage' => $this->model_tool_image->resize($nearest_pose['frontimage'], 150, 150),
	  			'address' => $nearest_pose['address'],
	  			'city' => $nearest_pose['city'],							
	  			'zone' => $nearest_pose['zone'],
	  			'alloweddistance' => sprintf($this->language->get('text_alloweddistance'), round($nearest_pose['alloweddistance']), $unit[0]),		
	  			'country' => $nearest_pose['country'],
	  			'latitude' => $nearest_pose['latitude'],
	  			'longitude' => $nearest_pose['longitude'],
	  			'distance' => sprintf($this->language->get('text_distance'), round($nearest_pose['distance'] * $unit[1], 2), $unit[0]),				
	  			'href' => $this->url->link('product/allpos/info', 'posapp_id=' . $nearest_pose['posapp_id'])

	  		);
	  	}
	  }

        ////sharma for pos


	  
	  $this->response->setOutput($this->load->view('checkout/shipping_method', $data));
	}

	public function save() {
		$this->load->language('checkout/checkout');

		$json = array();

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate if shipping address has been set.
		if (!isset($this->session->data['shipping_address'])) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!isset($this->request->post['shipping_method'])) {
			$json['error']['warning'] = $this->language->get('error_shipping');			
		} else {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			}
		}

		if (!$json) {
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}