<?php
class ControllerPosOrderList extends Controller {
	private $error = array();

	public function index() {
			$this->load->language('sale/order');
		$this->load->language('pos/pos');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pos/order');

		if (!empty($this->request->get['filter_data'])) {
			$data['tmdurl'] = true;
		}else{
			$data['tmdurl'] = false;
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = false;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = false;
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = false;
		}
		
		if (isset($this->request->get['filter_date_from'])) {
			$filter_date_from = $this->request->get['filter_date_from'];
		} else {
			$filter_date_from = false;
		}

		if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
		} else {
			$filter_date_to = false;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		
		if (isset($this->request->get['filter_date_from'])) {
			$url .= '&filter_date_from=' . $this->request->get['filter_date_from'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['export'] = $this->url->link('sale/order/export', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['add'] = $this->url->link('sale/order/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['orders'] = array();

		$filter_data = array(
			'filter_order_id'      => $filter_order_id,
			'filter_customer'	   => $filter_customer,
			'filter_order_status'  => $filter_order_status,
			'filter_date_from'     => $filter_date_from,
			'filter_date_to' 	   => $filter_date_to,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_pos_order->getTotalOrders($filter_data);

		$results = $this->model_pos_order->getOrders($filter_data);
	
		foreach ($results as $result) {
			$data['orders'][] = array(
				'order_id'      => $result['order_id'],
				'partl_payment'     => $this->url->link('pos/partialpayment', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
				'customer'      => $result['customer'],
				'order_status'  => $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'shipping_code' => $result['shipping_code'],
	/* 6 11 2019 */'viewprint'     => $this->url->link('pos/printinvoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),/* 06 11 2019 */
				'edit'          => $this->url->link('pos/orderlist/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_missing'] = $this->language->get('text_missing');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_select'] = $this->language->get('text_select');

		$data['column_order_id'] = $this->language->get('column_order_id');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_date'] = $this->language->get('column_date');

		$data['entry_order_id'] = $this->language->get('entry_order_id');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');
		$data['entry_from'] = $this->language->get('entry_from');
		$data['entry_to'] = $this->language->get('entry_to');
	
		$data['entry_date'] = $this->language->get('entry_date');

		$data['button_invoice_print'] = $this->language->get('button_invoice_print');
		$data['button_shipping_print'] = $this->language->get('button_shipping_print');
		
		$data['button_export'] = $this->language->get('button_export');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_view'] = $this->language->get('button_view');
		$data['button_ip_add'] = $this->language->get('button_ip_add');

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_from'])) {
			$url .= '&filter_date_from=' . $this->request->get['filter_date_from'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_status'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_from'])) {
			$url .= '&filter_date_from=' . $this->request->get['filter_date_from'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] 		= $filter_order_id;
		$data['filter_customer'] 		= $filter_customer;
		$data['filter_order_status'] 	= $filter_order_status;
		$data['filter_date_from'] 		= $filter_date_from;
		$data['filter_date_to'] 	= $filter_date_to;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pos/orderlist', $data));
	}


	
	
	public function edit() {
		$json=array();
		if(isset($this->request->get['order_id']))
		{
			$this->load->model('pos/order');
			$order_id=$this->request->get['order_id'];
			
			// Load All Product in session //
			$this->model_pos_order->loadinCart($order_id);
			// Load All Product in session //
			$json['success']='Load in cart';
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
		
	}
	
	public function editsave() {
		$this->load->model('pos/order');
			$json = array();
			if($this->request->post){

			if($this->request->post['payment_method']!='Card' && empty($this->request->post['order_status_id']))
			{
				
				$json['error']='Please Select Order Status';
				
			}
			
			else if($this->request->post['payment_method']=='Card') {
			
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

			if ($this->request->post['payment_method']=='split') {
				$split_total = 0;
				$split_payments = array();
				if(!empty($this->request->post['split_payment'])) {
					$split_payment 	= $this->request->post['split_payment'];
					foreach($split_payment as $split_payment) {
						$split_total += $split_payment['amount'];
						$split_payments[] = array(
							'amount' 	 => $split_payment['amount'],
							'method' 	 => $split_payment['method'],
						);
					}
				}
				$this->load->language('pos/paynow');
				if($split_total < $this->pos->getTotal()) {
					$json['error']=$this->language->get('error_amount');
				}
			}
			
		
		if(empty($json['error'])) {		
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
			$order_data['total'] = $total;
			
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

			$order_data['split_payments'] = array();
			if(!empty($this->request->post['split_payment']) && $this->request->post['payment_method'] =='split') {
			$order_data['split_payment'] 	= $this->request->post['split_payment'];
				foreach($order_data['split_payment'] as $split_payment) {
					$order_data['split_payments'][] = array(
						'amount' 	 => $split_payment['amount'],
						'method' 	 => $split_payment['method'],
					);
				}
			}
			
			if(!empty($this->request->post['partial_amount']))
			{
				$order_data['partial_amount'] =$this->request->post['partial_amount'];
			}else{
				$order_data['partial_amount']='';
			}
			
			$order_data['order_status_id'] = $this->request->post['order_status_id'];
			$order_data['comment'] = $this->request->post['comment'];
			$order_data['payment_method'] = $this->request->post['payment_method'];
			$order_data['payment_code'] = $this->request->post['payment_method'];
	
		 	$order_id=$this->session->data['order_id'];
			$this->model_pos_order->editOrder($order_data,$order_id);
			
			// Unset All
			$this->pos->clear();
			unset($this->session->data['shipping_method']);
			unset($this->session->data['voucher']);
			unset($this->session->data['voucherdiscount']);
			unset($this->session->data['coupondiscount']);
			unset($this->session->data['coupon']);
			unset($this->session->data['order_id']);
			unset($this->session->data['mdiscount']);
			
			// Unset All
			$json['success']='Order Placed Sucessfully want print invoice click on print button ';
			$json['link']=$this->url->link('pos/printinvoice', '&order_id='.$order_id.'&user_token=' . $this->session->data['user_token'], true);
			}
		
		}	
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
