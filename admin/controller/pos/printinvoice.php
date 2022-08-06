<?php
class ControllerPosPrintInvoice extends Controller {
	public function index() {
		
		$this->load->language('pos/printinvoice');
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$data['title'] = $this->language->get('text_invoice');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$data['text_invoice'] = $this->language->get('text_invoice');
		$data['text_order_detail'] = $this->language->get('text_order_detail');
		$data['text_order_id'] = $this->language->get('text_order_id');
		$data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$data['text_invoice_date'] = $this->language->get('text_invoice_date');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_website'] = $this->language->get('text_website');
		$data['text_payment_address'] = $this->language->get('text_payment_address');
		$data['text_shipping_address'] = $this->language->get('text_shipping_address');
		$data['text_payment_method'] = $this->language->get('text_payment_method');
		$data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$data['text_comment'] = $this->language->get('text_comment');
		$data['text_cashier'] = $this->language->get('text_cashier');

		$data['column_product'] = $this->language->get('column_product');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');
		
		$store_url = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
		
		
		
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['storelogo'] = $store_url .'image/'.$this->config->get('config_logo');			
		} else {
			$data['storelogo'] = '';
		}

		$storelogo = $this->config->get('setting_image');
		$logowidth = $this->config->get('setting_logowidth');
		$logoheight = $this->config->get('setting_logoheight');
		$this->load->model('tool/image');
		if (!empty($storelogo)) {
			$data['storelogo'] = $this->model_tool_image->resize($storelogo, $logoheight, $logowidth);
		}
		
		$this->load->model('pos/order');

		$this->load->model('setting/setting');

		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}

		foreach ($orders as $order_id) {
			$order_info = $this->model_pos_order->getOrder($order_id);
			
			if ($order_info) {
				$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

				////sharma for pos
				$posapp_query = $this->model_pos_order->posData($order_info['posapp_id'] );	

				if((int)$order_info['posapp_id'] > 0 ){
					if($posapp_query){
						$store_address = $posapp_query['address'];
						$store_email = $posapp_query['email'];
						$store_telephone = $posapp_query['telephone'];
						$store_fax = '';
						$data['name'] = $posapp_query['posname'];
						$data['posapp_id'] = '=='.$order_info['posapp_id'].'==';
					} 

				}else{
					if ($store_info) {
						$store_address = $store_info['config_address'];
						$store_email = $store_info['config_email'];
						$store_telephone = $store_info['config_telephone'];
						$store_fax = $store_info['config_fax'];
						$data['name'] = $this->config->get('config_name');
					} else {
						$store_address = $this->config->get('config_address');
						$store_email = $this->config->get('config_email');
						$store_telephone = $this->config->get('config_telephone');
						$store_fax = $this->config->get('config_fax');
						$data['name'] = $this->config->get('config_name');
					}
				}



				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
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

				$product_data = array();

				$products = $this->model_pos_order->getOrderProducts($order_id);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->model_pos_order->getOrderOptions($order_id, $product['order_product_id']);

					foreach ($options as $option) {
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
							'value' => $value
						);
					}

					$product_data[] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$voucher_data = array();

				$vouchers = $this->model_pos_order->getOrderVouchers($order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$total_data = array();

				$totals = $this->model_pos_order->getOrderTotals($order_id);

				foreach ($totals as $total) {
					$total_data[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}
				////sharma for pos
				$this->load->model('possetting/sale_report');
				$partial_paid =  $this->model_possetting_sale_report->getPartialPaid($order_id);

				if( $this->model_possetting_sale_report->getPartialPaid($order_id)){
					$partial_paid =  $this->model_possetting_sale_report->getPartialPaid($order_id);
				}else{
					$partial_paid = $order_info['total'];
				}

				$due_partial = round($order_info['total'], 2) - round($partial_paid, 2);
				$total_data[] = array(
					'title' => 'Paid',
					'text'  => $this->currency->format($partial_paid, $order_info['currency_code'], $order_info['currency_value'])
				);

				$total_data[] = array(
					'title' => 'Due',
					'text'  => $this->currency->format($due_partial, $order_info['currency_code'], $order_info['currency_value'])
				);

				
				$ordercomment=explode(':',$order_info['comment']);
				
				$cardnumber='';
				
				if(isset($ordercomment[1]))
				{
					$cardnumber=' ('.$ordercomment[1].') ';
				}
				$this->load->model('possetting/user');

				$users_info = $this->model_pos_order->getPosUser($order_id);
				if(isset($users_info['username'])) {
					$usernames= $users_info['username'];
				} else {
					$usernames='';
				}

				$this->load->model('pos/pos');
				$p_payments = array();
				$partial_sum = $this->model_pos_pos->getSumPartialpayments($order_id);
				$partial_results = $this->model_pos_pos->getPartialinvoices($order_id);
				if($partial_sum){
					foreach ($partial_results as $partial_result) {
						$p_payments[] = array(
							'partial_amount'    =>  $this->currency->format($partial_result['partial_amount'], $order_info['currency_code'], $order_info['currency_value']),
							'date_added'    => date($this->language->get('date_format_short'), strtotime($partial_result['date_added'])),
						);
					}
				}
				
				$data['orders'][] = array(
					'order_id'	       => $order_id,
					'p_payments'   => $p_payments,
					'invoice_no'       => $invoice_no,
					'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'       => $order_info['store_name'],
					'store_url'        => rtrim($order_info['store_url'], '/'),
					'store_address'    => nl2br($store_address),
					'store_email'      => $store_email,
					'store_telephone'  => $store_telephone,
					'store_fax'        => $store_fax,
					'usernames'        => $usernames,
					'email'            => $order_info['email'],
					'telephone'        => $order_info['telephone'],
					'shipping_address' => $shipping_address,
					'shipping_method'  => $order_info['shipping_method'],
					'payment_address'  => $payment_address,
					'payment_method'   => $order_info['payment_method'].$cardnumber,
					'product'          => $product_data,
					'voucher'          => $voucher_data,
					'total'            => $total_data,
					'comment'          => nl2br($ordercomment[0])
				);
				//print_r($data['orders']);
			}
		}


/// Invoice Print 3mm Start ////


		$this->load->model('customer/customer');
		
		$data['text_invoice'] 			= $this->language->get('text_invoice');
		$data['text_invoice_no'] 		= $this->language->get('text_invoice_no');
		$data['text_date_added'] 		= $this->language->get('text_date_added');
		$data['text_code'] 				= $this->language->get('text_code');
		$data['text_qty'] 				= $this->language->get('text_qty');
		$data['text_description'] 		= $this->language->get('text_description');
		$data['text_price'] 			= $this->language->get('text_price');
		$data['text_due_date'] 			= $this->language->get('text_due_date');
		$data['text_apaid'] 			= $this->language->get('text_apaid');
		$data['text_adue'] 				= $this->language->get('text_adue');
		$data['text_customer'] 			= $this->language->get('text_customer');

		$data['column_product'] 		= $this->language->get('column_product');
		$data['column_model'] 			= $this->language->get('column_model');
		$data['column_quantity'] 		= $this->language->get('column_quantity');
		$data['column_price'] 			= $this->language->get('column_price');
		$data['column_total'] 			= $this->language->get('column_total');

/// Invoice Setting Start ///				
		$data['store_logo'] 		= $this->config->get('setting_store_logo');
		$data['store_name'] 		= $this->config->get('setting_store_name');
		$data['store_address'] 		= $this->config->get('setting_store_address');
		$data['store_telephone']	= $this->config->get('setting_store_telephone');
		$data['store_order_date'] 	= $this->config->get('setting_order_date');
		$data['store_order_time'] 	= $this->config->get('setting_order_time');
		$data['invoice_number'] 	= $this->config->get('setting_invoice_number');
		$data['cashier_name'] 		= $this->config->get('setting_cashier_name');
		$data['payment_mode'] 		= $this->config->get('setting_payment_mode');
		$data['shipping_mode'] 		= $this->config->get('setting_shipping_mode');
/// Invoice Setting End ///	


		////sharma for pos
		$posapp_query = $this->model_pos_order->posData($order_info['posapp_id'] );	

		if((int)$order_info['posapp_id'] > 0 ){
			if($posapp_query){
				$data['config_name'] = $posapp_query['posname'];
				$data['config_address'] = $posapp_query['address'];
				$data['config_telephone'] = $posapp_query['telephone'];
			}
		}else{
			$data['config_name'] = $this->config->get('config_name');
			$data['config_address'] = $this->config->get('config_address');
			$data['config_telephone'] = $this->config->get('config_telephone');
		}

		$data['setting_invoice'] 	= !empty($this->config->get('setting_invoice'))?nl2br($this->config->get('setting_invoice')):'';
		$data['setting_extra'] 	= !empty($this->config->get('setting_extra'))?nl2br($this->config->get('setting_extra')):'';
		if (isset($this->request->get['order_id'])) {
			$order_id = (int)$this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		
		$order_info = $this->model_pos_order->getOrder($order_id);

		$this->load->model('possetting/user');

		$users_info = $this->model_pos_order->getPosUser($order_id);
		if(isset($users_info['username'])) {
			$usernames= $users_info['username'];
		} else {
			$usernames='';
		}

		if ($order_info['invoice_no']) {
			$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
		} else {
			$invoice_no = '';
		}

		$data['usernames']= $usernames;
		$data['invoice_no']  = $invoice_no;
		$data['date_added']  = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
		$data['time']  		 = date('H:i A', strtotime($order_info['date_added']));
		$data['order_id'] 	 = $order_info['order_id'];

		$data['products'] = array();

		$products = $this->model_pos_order->getOrderProducts($order_id);

		foreach ($products as $product) {
			$option_data2 = array();

			$options = $this->model_pos_order->getOrderOptions($order_id, $product['order_product_id']);

			foreach ($options as $option) {
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

				$option_data2[] = array(
					'name'  => $option['name'],
					'value' => $value
				);
			}
			
			$data['products'][] = array(
				'name'    => $product['name'],
				'model'    => $product['model'],
				'option'   => $option_data2,
				'quantity' => $product['quantity'],
				'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
			);
		}

		$data['total_data'] = array();

		$totals = $this->model_pos_order->getOrderTotals($order_id);

		foreach ($totals as $total) {
			$data['total_data'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
			);
		}

		////sharma for pos
		$this->load->model('possetting/sale_report');
		$partial_paid =  $this->model_possetting_sale_report->getPartialPaid($order_id);

		if( $this->model_possetting_sale_report->getPartialPaid($order_id)){
			$partial_paid =  $this->model_possetting_sale_report->getPartialPaid($order_id);
		}else{
			$partial_paid = $order_info['total'];
		}

		$due_partial = round($order_info['total'], 2) - round($partial_paid, 2);
		$data['total_data'][] = array(
			'title' => 'Paid',
			'text'  => $this->currency->format($partial_paid, $order_info['currency_code'], $order_info['currency_value'])
		);

		$data['total_data'][] = array(
			'title' => 'Due',
			'text'  => $this->currency->format($due_partial, $order_info['currency_code'], $order_info['currency_value'])
		);



/// Invoice Print 3mm End ////	

		if(!empty($this->config->get('setting_format'))) {
			$this->response->setOutput($this->load->view('pos/printinvoice1', $data));
		} else {
			$this->response->setOutput($this->load->view('pos/printinvoice', $data));
		}
		
	}
}