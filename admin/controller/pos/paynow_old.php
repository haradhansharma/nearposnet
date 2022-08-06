<?php
class ControllerPosPaynow extends Controller {
	public function index() {
			$this->load->model('pos/pos');
			$this->load->language('pos/paynow');
		$this->load->model('pos/order');
		$data['warning']='';
		$products = $this->pos->getProducts();
		if(empty($products))
		{
			$data['warning']=$this->language->get('text_warning');
		}
				
		
		$data['user_token'] = $this->session->data['user_token'];
		$url = '';
		
		
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_place'] = $this->language->get('text_place');
		$data['text_editplace'] = $this->language->get('text_editplace');
		$data['text_customer'] = $this->language->get('text_customer');
		$data['text_gcustomer'] = $this->language->get('text_gcustomer');
		$data['text_or'] = $this->language->get('text_or');
		$data['text_pay_method'] = $this->language->get('text_pay_method');
		$data['text_cash'] = $this->language->get('text_cash');
		$data['text_card'] = $this->language->get('text_card');
		$data['text_cardlast'] = $this->language->get('text_cardlast');
		$data['text_comment'] = $this->language->get('text_comment');
		$data['text_order'] = $this->language->get('text_order');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_clearcart'] = $this->language->get('text_clearcart');	
		 
		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		// Customer Add

		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['text_invoice'] 		= $this->language->get('text_invoice');
		$data['text_bill'] 			= $this->language->get('text_bill');
		$data['text_card'] 			= $this->language->get('text_card');
		$data['text_cash'] 			= $this->language->get('text_cash');
		$data['text_offlinecard'] 	= $this->language->get('text_offlinecard');
		$data['text_banktransfer'] 	= $this->language->get('text_banktransfer');
		$data['text_cheque'] 		= $this->language->get('text_cheque');
		$data['entry_document'] 	= $this->language->get('entry_document');
		$data['entry_email'] 		= $this->language->get('entry_email');
		$data['entry_contact'] 		= $this->language->get('entry_contact');
		$data['entry_company'] 		= $this->language->get('entry_company');
		$data['entry_company'] 		= $this->language->get('entry_company');
		$data['entry_rut'] 			= $this->language->get('entry_rut');
		$data['entry_turn'] 		= $this->language->get('entry_turn');
		
		$data['paynow_guest']  = $this->config->get('setting_paynow_guest');
		$data['default_guest'] = $this->config->get('setting_defult_guest');

		if (isset($this->request->post['setting_paymentmethod'])) {
			$setting_paymentmethods = $this->request->post['setting_paymentmethod'];
		} else {
			$setting_paymentmethods = $this->config->get('setting_paymentmethod');
		}
		$data['setting_paymentmethods'] = array();
		if(is_array($setting_paymentmethods)) {
			foreach ($setting_paymentmethods as $setting_paymentmethod) {
				$data['setting_paymentmethods'][] = array(
					'name' 	=> $setting_paymentmethod['method_name'],
					'order_status_id'	=> $setting_paymentmethod['order_status_id'],
				);
			}
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		
		
		$data['order_id']=0;
		if(isset($this->session->data['order_id']))
		{
		$data['order_id']=$this->session->data['order_id'];
		$orderinfo=$this->model_pos_order->getOrder($this->session->data['order_id']);
		//print_r($orderinfo);die();
		}
		
		if (isset($orderinfo['customer_id'])) {
			$data['guestcustomer'] = $orderinfo['customer_id'];
		} else {
			$data['guestcustomer'] = 0;
		}	
		
		if (isset($orderinfo['firstname'])) {
			$data['customer_name'] = $orderinfo['firstname'].' '.$orderinfo['lastname'];
		} else {
			$data['customer_name'] = '';
		}	
	
		if (isset($orderinfo['customer_id'])) {
			$data['customer_id'] = $orderinfo['customer_id'];
		} else {
			$data['customer_id'] = '';
		}	
	
		if (isset($orderinfo['payment_method'])) {
			$data['payment_method'] =$orderinfo['payment_method'];
		} else {
			$data['payment_method'] ='';
		}
	
		
		if (isset($this->request->post['cachange'])) {
			$data['cachange'] = $this->request->post['cachange'];
		} else {
			$data['cachange'] = '';
		}
		if (isset($orderinfo['comment'])) {
			$data['comment'] = $orderinfo['comment'];
		} else {
			$data['comment'] = '';
		}	
		if (isset($orderinfo['order_status_id'])) {
			$data['order_status_id'] = $orderinfo['order_status_id'];
		} else {
			$data['order_status_id'] = '';
		}	
		
		
		if (isset($orderinfo['firstname'])) {
			$data['firstname'] = $orderinfo['firstname'];
		} else {
			$data['firstname'] = '';
		}
		
		if (isset($orderinfo['lastname'])) {
			$data['lastname'] = $orderinfo['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($orderinfo['email'])) {
			$data['email'] = $orderinfo['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($orderinfo['telephone'])) {
			$data['telephone'] = $orderinfo['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($orderinfo['payment_address_1'])) {
			$data['address_1'] = $orderinfo['payment_address_1'];
		} else {
			$data['address_1'] = '';
		}
		
		
		$data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		} 
		
		
		// Order Status
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if(isset($this->session->data['order_id']))
		{
		$this->response->setOutput($this->load->view('pos/paynowedit', $data));
		}
		else
		{
			$this->response->setOutput($this->load->view('pos/paynow', $data));	
		}
		
	//	$this->response->setOutput($this->load->view('pos/paynow', $data));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['customer_name'])) {
			if (isset($this->request->get['customer_name'])) {
				$customer_name = $this->request->get['customer_name'];
			} else {
				$customer_name = '';
			}

			$this->load->model('pos/pos');

			$filter_data = array(
				'filter_name'  => $customer_name,
				'start'        => 0,
				'limit'        => 15
			);

			$results = $this->model_pos_pos->getCustomers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'customer_id'       => $result['customer_id'],
					'customer_group_id' => $result['customer_group_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'customer_group'    => $result['customer_group'],
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email'],
					'telephone'         => $result['telephone'],
					'fax'               => $result['fax'],
					);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addorder() {
		
		$json = array();
		if($this->request->post){
			if(empty($this->request->post['customer_id']) && empty($this->request->post['guestcustomer']))
			{
				$json['error']='Please Select Customer Type';				
			}
		
			else if($this->request->post['payment_method']=='cash' && empty($this->request->post['order_status_id']))
			{
				
				$json['error']='Please Select Order Status';
				
			}
			
			else if($this->request->post['payment_method']=='card') {
			
				if(empty($this->request->post['cc_owner'])) {
					
					$json['error']='Please add Card Owner Value';
					
				}
				else if(empty($this->request->post['cc_number'])) {
					
					$json['error']='Please add Card Number';
					
				}
			
				else if(empty($this->request->post['cc_expire_date_month'])) {
					
					$json['error']='Please add Card Expiry Date';
					
				}
				else if(empty($this->request->post['cc_expire_date_year'])) {
					
					$json['error']='Please add Card Expiry Year';
					
				}
				
				else if(empty($this->request->post['cc_cvv2'])) {
					
					$json['error']='Please add Card Security Code';
					
				}
				else if(($this->request->post['cc_cvv2'] < 4)) {
					
					$json['error']='Add three digits for security Card';
					
				}
				
				
			
			}
			if(empty($json['error']))
			{			
					// Add Order Functioon 
			/// For Total Collection
			$this->load->language('pos/total');

		$sub_total = $this->pos->getSubTotal();
		$total =$sub_total;
		

		$totals[] = array(
			'code'       => 'sub_total',
			'title'      => $this->language->get('text_sub_total'),
			'text'      => $this->currency->format($sub_total, $this->config->get('config_currency')),
			'value'      => $sub_total,
			'sort_order' => '0'
			
		);
		
		
	
		$taxes = $this->pos->getTaxes();
			foreach ($taxes as $key => $value) {
				if ($value > 0) {
					$totals[] = array(
						'code'       => 'tax',
						'title'      => $this->tax->getRateName($key),
						'text'      => $this->currency->format($value,$this->config->get('config_currency')),
						'value'      => $value,
						'sort_order' => '1'
						
					);
					$total +=$value;
					
				}
			}
		
			if(isset($this->session->data['mdiscount']))
			{
				$discount_total=$this->session->data['mdiscount'];
				if ($discount_total > 0) {
							$totals[] = array(
								'code'       => 'mdiscount',
								'title'      => $this->language->get('text_mdiscount'),
								'text'      => '-' .$this->currency->format($discount_total,$this->config->get('config_currency')),
								'value'      =>-$discount_total,
								'sort_order' => '2'
								
							);
						$total -=$discount_total;
				}
			}
		
			if(isset($this->session->data['coupondiscount']))
			{
				$this->load->language('extension/total/coupon	');
				$discount_total=$this->session->data['coupondiscount'];
				if ($discount_total > 0) {
						$totals[] = array(
							'code'       => 'coupon',
							'title'      => sprintf($this->language->get('text_coupon'), $this->session->data['coupon']),
							'text'      => '-'.$this->currency->format($discount_total,$this->config->get('config_currency')),
							'value'      => -$discount_total,
							'sort_order' => '3'
							
						);
					$total -=$discount_total;
				}
			}
			if(isset($this->session->data['voucherdiscount']))
			{
			
			$amount=$this->session->data['voucherdiscount'];
			if ($amount > 0) {
						$totals[] = array(
							'code'       => 'voucher',
							'title'      => sprintf($this->language->get('text_voucher'), $this->session->data['voucher']),
							'text'      => '-'.$this->currency->format($amount,$this->config->get('config_currency')),
							'value'      => -$amount,
							'sort_order' => '4'
							
							
						);
						$total -=$amount;

						
					} 
			}
		

			$totals[] = array(
				'code'       => 'total',
				'title'      => $this->language->get('text_total'),
				'text'      =>$this->currency->format($total,$this->config->get('config_currency')),
				'value'      => max(0, $total),
				'sort_order' => '5'
				
			);
			
			/// Total end
			$order_data['totals'] = $totals;

			$this->load->language('pos/checkout');

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_CATALOG;
				} else {
					$order_data['store_url'] = HTTP_CATALOG;
				}
			}

			if (!empty($this->request->post['customer_id'])) {
				$this->load->model('pos/order');

				$customer_info = $this->model_pos_order->getCustomer($this->request->post['customer_id']);
				$address = $this->model_pos_order->getAddress($customer_info['address_id']);

				$order_data['customer_id'] = $this->request->post['customer_id'];
				$order_data['customer_group_id'] = $customer_info['customer_group_id'];
				$order_data['firstname'] = $customer_info['firstname'];
				$order_data['lastname'] = $customer_info['lastname'];
				$order_data['email'] = $customer_info['email'];
				$order_data['telephone'] = $customer_info['telephone'];
				$order_data['fax'] = $customer_info['fax'];
				
				$order_data['zone_id'] = $address['zone_id'];
				$order_data['city'] = $address['city'];
				$order_data['country_id'] = $address['country_id'];
				$order_data['postcode'] = $address['postcode'];
				$order_data['zone'] = $address['zone'];
				$order_data['country'] = $address['country'];
				$order_data['address_format'] = $address['address_format'];
				$order_data['company'] = $address['company'];
				$order_data['address_1'] = $address['address_1'];
				$order_data['address_2'] = $address['address_2'];
				
				
				$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);
				
			} elseif (!empty($this->request->post['guestcustomer'])) {
				$order_data['customer_id'] = 0;
				$order_data['customer_group_id'] = '';
				$order_data['firstname'] = 'Guest';
				$order_data['lastname'] = 'Guest';
				$order_data['email'] = '';
				$order_data['telephone'] = '';
				$order_data['fax'] ='';
				$order_data['custom_field'] = '';
				
				$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$this->config->get('config_country_id') . "'");

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

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$this->config->get('config_zone_id') . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}
			
				$order_data['zone_id'] =  $this->config->get('config_zone_id');
				$order_data['city'] = '';
				$order_data['country_id'] = $this->config->get('config_country_id');
				$order_data['postcode'] = '';
				$order_data['zone'] = $zone;
				$order_data['country'] = $country;
				$order_data['address_format'] = '';
				$order_data['company'] = '';
				$order_data['address_1'] = '';
				$order_data['address_2'] = '';
				
			}
			
			$order_data['payment_firstname'] = $order_data['firstname'];
			$order_data['payment_lastname'] = $order_data['lastname'];
			$order_data['payment_company'] = $order_data['company'];
			$order_data['payment_address_1'] = $order_data['address_1'];
			$order_data['payment_address_2'] = $order_data['address_2'];
			$order_data['payment_city'] = $order_data['city'];
			$order_data['payment_postcode'] = $order_data['postcode'];
			$order_data['payment_zone'] = $order_data['zone'];
			$order_data['payment_zone_id'] = $order_data['zone_id'];
			$order_data['payment_country'] = $order_data['country'];
			$order_data['payment_country_id'] = $order_data['country_id'];
			$order_data['payment_address_format'] = $order_data['address_format'];
			$order_data['payment_custom_field'] =  array();
			
			$order_data['payment_method'] = $this->request->post['payment_method'];
			$order_data['payment_code'] = $this->request->post['payment_method'];
	

			if ($this->pos->hasShipping()) {
				$order_data['shipping_firstname'] = $order_data['firstname'];
				$order_data['shipping_lastname'] = $order_data['lastname'];
				$order_data['shipping_company'] = $order_data['company'];
				$order_data['shipping_address_1'] = $order_data['address_1'];
				$order_data['shipping_address_2'] = $order_data['address_2'];
				$order_data['shipping_city'] = $order_data['city'];
				$order_data['shipping_postcode'] = $order_data['postcode'];
				$order_data['shipping_zone'] = $order_data['zone'];
				$order_data['shipping_zone_id'] = $order_data['zone_id'];
				$order_data['shipping_country'] = $order_data['country'];
				$order_data['shipping_country_id'] = $order_data['country_id'];
				$order_data['shipping_address_format'] = $order_data['address_format'];
				$order_data['shipping_custom_field'] = array();

				
				$order_data['shipping_method'] = 'Free';
				
				$order_data['shipping_code'] = 'free';
				
			} else {
				$order_data['shipping_firstname'] = '';
				$order_data['shipping_lastname'] = '';
				$order_data['shipping_company'] = '';
				$order_data['shipping_address_1'] = '';
				$order_data['shipping_address_2'] = '';
				$order_data['shipping_city'] = '';
				$order_data['shipping_postcode'] = '';
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
				$order_data['shipping_address_format'] = '';
				$order_data['shipping_custom_field'] = array();
				$order_data['shipping_method'] = '';
				$order_data['shipping_code'] = '';
			}

			$order_data['products'] = array();

			foreach ($this->pos->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					);
				}

				$order_data['products'][] = array(
					'product_id' => $product['product_id'],
					'cproduct_id' => $product['cproduct_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}

			// Gift Voucher
			$order_data['vouchers'] = array();		

			$order_data['comment'] = $this->request->post['comment'];
			if(!empty($this->request->post['cc_number']))
			{
				$order_data['comment'] .=':' .substr($this->request->post['cc_number'],-4);
			}
			$order_data['total'] = $total;

			
			$order_data['affiliate_id'] = 0;
			$order_data['commission'] = 0;
			$order_data['marketing_id'] = 0;
			$order_data['tracking'] = '';
			

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->config->get('config_currency'));
			$order_data['currency_code'] = $this->config->get('config_currency');
			$order_data['currency_value'] = $this->currency->getValue($this->config->get('config_currency'));
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}

			$this->load->model('pos/order');
			
			if($this->request->post['payment_method']=='card') {
			$order_id= $this->model_pos_order->addOrder($order_data,0);
			$paymentinfo=array(
			'order_id'=>$order_id,
			'cc_number'=>$this->request->post['cc_number'],
			'cc_expire_date_month'=>$this->request->post['cc_expire_date_month'],
			'cc_expire_date_year'=>$this->request->post['cc_expire_date_year'],
			'cc_cvv2'=>$this->request->post['cc_cvv2']
			);
			$json=$this->paymetaim($paymentinfo);
			if(!empty($json['redirect']))
			{
					// Unset All
				$this->pos->clear();
				unset($this->session->data['shipping_method']);
				unset($this->session->data['voucher']);
				unset($this->session->data['voucherdiscount']);
				unset($this->session->data['coupondiscount']);
				unset($this->session->data['coupon']);
				unset($this->session->data['mdiscount']);
				
				// Unset All
				$json['success']='Order Placed Sucessfully want print invoice click on print button ';
				$json['link']=$this->url->link('pos/printinvoice', '&order_id='.$order_id.'&user_token=' . $this->session->data['user_token'], true);
			}
			} else {
			$order_id= $this->model_pos_order->addOrder($order_data,$this->request->post['order_status_id']);
			
			// Unset All
			$this->pos->clear();
			unset($this->session->data['shipping_method']);
			unset($this->session->data['voucher']);
			unset($this->session->data['voucherdiscount']);
			unset($this->session->data['coupondiscount']);
			unset($this->session->data['coupon']);
			unset($this->session->data['mdiscount']);
			
			// Unset All
			$json['success']='Order Placed Sucessfully want print invoice click on print button ';
			$json['link']=$this->url->link('pos/printinvoice', '&order_id='.$order_id.'&user_token=' . $this->session->data['user_token'], true);
			}
			}
		
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function paymetaim($request) {
		if ($this->config->get('authorizenet_aim_server') == 'live') {
			$url = 'https://secure.authorize.net/gateway/transact.dll';
		} elseif ($this->config->get('authorizenet_aim_server') == 'test') {
			$url = 'https://test.authorize.net/gateway/transact.dll';
		}

		//$url = 'https://secure.networkmerchants.com/gateway/transact.dll';
		
		$this->load->model('pos/order');

		$order_info = $this->model_pos_order->getOrder($request['order_id']);

		$data = array();

		$data['x_login'] = $this->config->get('authorizenet_aim_login');
		$data['x_tran_key'] = $this->config->get('authorizenet_aim_key');
		$data['x_version'] = '3.1';
		$data['x_delim_data'] = 'true';
		$data['x_delim_char'] = '|';
		$data['x_encap_char'] = '"';
		$data['x_relay_response'] = 'false';
		$data['x_first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$data['x_last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['x_company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
		$data['x_address'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$data['x_city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		$data['x_state'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
		$data['x_zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$data['x_country'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
		$data['x_phone'] = $order_info['telephone'];
		$data['x_customer_ip'] = $this->request->server['REMOTE_ADDR'];
		$data['x_email'] = $order_info['email'];
		$data['x_description'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$data['x_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false);
		$data['x_currency_code'] = $order_info['currency_code'];
		$data['x_method'] = 'CC';
		$data['x_type'] = ($this->config->get('authorizenet_aim_method') == 'capture') ? 'AUTH_CAPTURE' : 'AUTH_ONLY';
		$data['x_card_num'] = str_replace(' ', '', $request['cc_number']);
		$data['x_exp_date'] = $request['cc_expire_date_month'] . $request['cc_expire_date_year'];
		$data['x_card_code'] = $request['cc_cvv2'];
		$data['x_invoice_num'] = $request['order_id'];
		$data['x_solution_id'] = 'A1000015';

		/* Customer Shipping Address Fields */
		if ($order_info['shipping_method']) {
			$data['x_ship_to_first_name'] = html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_last_name'] = html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_company'] = html_entity_decode($order_info['shipping_company'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_address'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_city'] = html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_state'] = html_entity_decode($order_info['shipping_zone'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_zip'] = html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_country'] = html_entity_decode($order_info['shipping_country'], ENT_QUOTES, 'UTF-8');
		} else {
			$data['x_ship_to_first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_address'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_state'] = html_entity_decode($order_info['payment_zone'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$data['x_ship_to_country'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
		}

		if ($this->config->get('authorizenet_aim_mode') == 'test') {
			$data['x_test_request'] = 'true';
		}

		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

		$response = curl_exec($curl);

		$json = array();

		if (curl_error($curl)) {
			$json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);

			$this->log->write('AUTHNET AIM CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {
			$i = 1;

			$response_info = array();

			$results = explode('|', $response);

			foreach ($results as $result) {
				$response_info[$i] = trim($result, '"');

				$i++;
			}

			if ($response_info[1] == '1') {
				$message = '';

				if (isset($response_info['5'])) {
					$message .= 'Authorization Code: ' . $response_info['5'] . "\n";
				}

				if (isset($response_info['6'])) {
					$message .= 'AVS Response: ' . $response_info['6'] . "\n";
				}

				if (isset($response_info['7'])) {
					$message .= 'Transaction ID: ' . $response_info['7'] . "\n";
				}

				if (isset($response_info['39'])) {
					$message .= 'Card Code Response: ' . $response_info['39'] . "\n";
				}

				if (isset($response_info['40'])) {
					$message .= 'Cardholder Authentication Verification Response: ' . $response_info['40'] . "\n";
				}

				if (!$this->config->get('authorizenet_aim_hash') || (strtoupper($response_info[38]) == strtoupper(md5($this->config->get('authorizenet_aim_hash') . $this->config->get('authorizenet_aim_login') . $response_info[7] . $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false))))) {
					$this->model_pos_order->addOrderHistory($order_info['order_id'], $this->config->get('authorizenet_aim_order_status_id'), $message, false);
				} else {
					$this->model_pos_order->addOrderHistory($this->session->data['order_id'], $this->config->get('config_order_status_id'));
				}

				$json['redirect'] = $this->url->link('checkout/success', '', true);
			} else {
				$json['error'] = $response_info[4];
			}
		} else {
			$json['error'] = 'Empty Gateway Response';

			$this->log->write('AUTHNET AIM CURL ERROR: Empty Gateway Response');
		}

		return $json; 
	}
	
}