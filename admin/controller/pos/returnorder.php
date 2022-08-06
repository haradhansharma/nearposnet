<?php
class ControllerPosReturnOrder extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('pos/returnorder');
		$data['entry_order_id'] = $this->language->get('entry_order_id');
		$data['button_search'] = $this->language->get('button_search');
		$data['user_token'] = $this->session->data['user_token'];
		$this->document->setTitle($this->language->get('heading_title'));
		$this->response->setOutput($this->load->view('pos/returnorder', $data));


		if(isset($this->request->get['tmd_order_id']))
		{
			$order_id= $this->request->get['tmd_order_id'];
		}
		else
		{
			$order_id= 0;
		}

		if(isset($this->request->get['filter_data']))
		{
			$data['filter_data']= $this->request->get['filter_data'];
		}
		else
		{
			$data['filter_data']= false;
		}

		$this->load->model('pos/returnorder');
		$this->load->model('tool/image');
		$order_info = $this->model_pos_returnorder->getOrder($order_id);
		


		$data['heading_title'] = $this->language->get('text_order');

		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['text_order_detail'] = $this->language->get('text_order_detail');
		$data['column_image'] = $this->language->get('column_image');
		$data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$data['text_order_id'] = $this->language->get('text_order_id');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$data['text_shipping_address'] = $this->language->get('text_shipping_address');
		$data['text_payment_method'] = $this->language->get('text_payment_method');
		$data['text_payment_address'] = $this->language->get('text_payment_address');
		$data['text_history'] = $this->language->get('text_history');
		$data['text_comment'] = $this->language->get('text_comment');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_order_not_found'] = $this->language->get('text_order_not_found');
		$data['error_quantity'] = $this->language->get('error_quantity');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_reason'] = $this->language->get('column_reason');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_comment'] = $this->language->get('column_comment');

		$data['button_update'] = $this->language->get('button_update');
		$data['button_return'] = $this->language->get('button_return');


		$data['action'] = $this->url->link('pos/returnorder/update', 'user_token=' . $this->session->data['user_token'], true);
		if ($order_info) 
		{
			
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
			

			$data['payment_address'] = $order_info['payment_firstname'].' '.$order_info['payment_lastname'].','.$order_info['payment_zone'].','.$order_info['payment_country'];

			$data['payment_method'] = $order_info['payment_method'];
			$data['shipping_method'] = $order_info['shipping_method'];

//////////sharma for pos
			$this->load->model('localisation/return_reason');
		    $data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons(); 



////sharma for pos

			// $setting_returns = $this->config->get('setting_return');

			// $data['settingreturns'] = array();

			// if(is_array($setting_returns)) {
			// 	foreach ($setting_returns as $reason) {
			// 		$data['settingreturns'][] = array(
			// 			'reason' 		=> $reason['setting_return_description'][$this->config->get('config_language_id')]['reason'],
			// 		); 
			// 	}
			// }

			////sahrma for pos

			$this->load->model('localisation/order_status');

			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

			$this->load->model('catalog/product');
			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();

			$products = $this->model_pos_returnorder->getOrderProducts($order_id);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_pos_returnorder->getOrderOptions($order_id, $product['order_product_id']);

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
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$product_info = $this->model_catalog_product->getProduct($product['product_id']);

				if ($product_info['image']) 
				{
					$image = $this->model_tool_image->resize($product_info['image'], 50, 50);
				} 
				else 
				{
					$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
				}

				$data['products'][] = array(
					'image'     => $image,
					'order_product_id'	=> $product['order_product_id'],
					'product_id'	=> $product['product_id'],
					'order_id'	=> $product['order_id'],
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			// Totals
			$data['totals'] = array();

			$totals = $this->model_pos_returnorder->getOrderTotals($order_id);

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}
		}
		$this->response->setOutput($this->load->view('pos/returnorder', $data));
	}
		
	public function update() 
	{	
		$json = '';
		$this->load->language('pos/returnorder'); 
		$this->load->model('pos/returnorder');
		

		$order_id = $this->model_pos_returnorder->addReturn($this->request->post);
		if (!empty($order_id)) {
			$json['success'] 	= $this->language->get('text_success2');
			$json['order_id'] 	= $order_id;
			$json['link'] 		= $this->url->link('possetting/return_report/returnPrint', 'user_token=' . $this->session->data['user_token'].'&order_id='.$order_id, true);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
