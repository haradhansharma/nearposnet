<?php
class ControllerPosSettingSplitPaymentReport extends Controller {
 	private $error = array();
	public function index() {
			
		$this->load->language('possetting/splitpayment_report');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/splitpayment_report');
		$this->load->model('catalog/product');
		
		$this->getList();
	}
 	public function getList() {
 		
	 	if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = null;
		}

		if (isset($this->request->get['filter_date_form'])) {
			$filter_date_form = $this->request->get['filter_date_form'];
		} else {
			$filter_date_form = null;
		}

		if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
		} else {
			$filter_date_to = null;
		}
	 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'order_product_id';
		}
	 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
		 	$order = 'ASC';
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

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['filter_date_form'])) {
			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
		 $data['print'] = $this->url->link('possetting/splitpayment_report/splitsalereportprint', 'user_token=' . $this->session->data['user_token']. $url, true);
						  
	
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('possetting/splitpayment_report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['reports'] = array();

		$filter_data = array(
			'filter_order_id'  => $filter_order_id,
			'filter_order_status_id'   	=> $filter_order_status_id,
			'filter_date_form' => $filter_date_form,
			'filter_date_to' => $filter_date_to,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
	
		$this->load->model('localisation/order_status');
		
		$split_total = $this->model_possetting_splitpayment_report->getTotalSplitReports($filter_data);
		
		$results = $this->model_possetting_splitpayment_report->getSplitReports($filter_data);
		
		foreach($results as $result) {
			$payments = array();
			$payment_infos = $this->model_possetting_splitpayment_report->getSplitPaymentMethod($result['order_id']);
			foreach ($payment_infos as $payment_info) {
				$payments[]=array(
					'method' 			=> $payment_info['method'],
					'amount' 			=> $this->currency->format($payment_info['amount'], $this->config->get('config_currency'), $this->config->get('currency_value')),
				);
			}

			$totalamount = $this->model_possetting_splitpayment_report->getTotalAmount($result['order_id']);
			$order_status = $this->model_localisation_order_status->getOrderStatus($result['order_status_id']);
			if (isset($order_status['name'])) {
				$order_status_name = $order_status['name'];
			}else{
				$order_status_name ='';
			}
			$data['reports'][]=array(
				'order_id' 			=> $result['order_id'],
				'date_added' 		=> $result['date_added'],
				'totalamount'		=> $this->currency->format($totalamount, $this->config->get('config_currency'), $this->config->get('currency_value')),
				'payments' 			=> $payments,
				'order_status_name' => $order_status_name,
			);
		}
		$data['heading_title']       = $this->language->get('heading_title');
		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_invoice'] 		 = $this->language->get('text_invoice');

		$data['column_totalsell']	 = $this->language->get('column_totalsell');
		$data['column_totalamount']	 = $this->language->get('column_totalamount');
		$data['column_date']	 	 = $this->language->get('column_date');
		$data['column_action']	 	 = $this->language->get('column_action');

		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['user_token']          = $this->session->data['user_token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$url = '';

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_date_form'])) {
			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];
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

		$data['sort_order_id'] 			= $this->url->link('possetting/splitpayment_report', 'user_token=' . $this->session->data['user_token'] . '&sort=order_id' . $url, true);
		$data['sort_order_status_id'] 	= $this->url->link('possetting/splitpayment_report', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status_id' . $url, true);
		$data['sort_method'] 			= $this->url->link('possetting/splitpayment_report', 'user_token=' . $this->session->data['user_token'] . '&sort=method' . $url, true);
		$data['sort_date_added']		= $this->url->link('possetting/splitpayment_report', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		
		$url='';
		
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['filter_date_form'])) {
			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];
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
		$data['order_statuss'] = $this->model_localisation_order_status->getOrderStatuses();
				   
        
		$pagination 	   = new Pagination();
		$pagination->total = $split_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/splitpayment_report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($split_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($split_total - $this->config->get('config_limit_admin'))) ? $split_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $split_total, ceil($split_total / $this->config->get('config_limit_admin')));

		
		$data['filter_order_id'] 	= $filter_order_id;
		$data['filter_order_status_id'] 		= $filter_order_status_id;
		$data['filter_date_form'] 	= $filter_date_form;
		$data['filter_date_to'] 	= $filter_date_to;
		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/splitpayment_report', $data));
	}

	public function splitsalereportprint() {
		$this->load->language('possetting/splitpayment_report');

		$data['title'] = $this->language->get('text_invoice');
		
		$data['heading_title']       = $this->language->get('heading_title');
		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_invoice'] 		 = $this->language->get('text_invoice');

		$data['text_invoice'] 		 = $this->language->get('text_invoice');
		$data['column_productid']	 = $this->language->get('column_productid');
		$data['column_name']	 	 = $this->language->get('column_name');
		$data['column_model']	     = $this->language->get('column_model');
		$data['column_totalsell']	 = $this->language->get('column_totalsell');
		$data['column_totalamount']	 = $this->language->get('column_totalamount');
		$data['column_date']	 	 = $this->language->get('column_date');

		$this->load->model('possetting/splitpayment_report');
		$data['productsells'] = array();
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = null;
		}

		if (isset($this->request->get['filter_date_form'])) {
			$filter_date_form = $this->request->get['filter_date_form'];
		} else {
			$filter_date_form = null;
		}

		if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
		} else {
			$filter_date_to = null;
		}
	 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'order_product_id';
		}
	 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
		 	$order = 'ASC';
		}
	 
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
					
		$data['productsells'] = array();

		$filter_data = array(
			'filter_order_id'  			=> $filter_order_id,
			'filter_order_status_id'   	=> $filter_order_status_id,
			'filter_date_form' 			=> $filter_date_form,
			'filter_date_to' 			=> $filter_date_to,
			'sort'  					=> $sort,
			'order' 					=> $order,
			'start' 					=> '0',
			'limit' 					=> '9999999999999999'
		);
	
		$this->load->model('localisation/order_status');
		
		$split_total = $this->model_possetting_splitpayment_report->getTotalSplitReports($filter_data);
		
		$results = $this->model_possetting_splitpayment_report->getSplitReports($filter_data);
		
		foreach($results as $result) {
			$payments = array();
			$payment_infos = $this->model_possetting_splitpayment_report->getSplitPaymentMethod($result['order_id']);
			foreach ($payment_infos as $payment_info) {
				$payments[]=array(
					'method' 			=> $payment_info['method'],
					'amount' 			=> $this->currency->format($payment_info['amount'], $this->config->get('config_currency'), $this->config->get('currency_value')),
				);
			}

			$totalamount = $this->model_possetting_splitpayment_report->getTotalAmount($result['order_id']);
			$order_status = $this->model_localisation_order_status->getOrderStatus($result['order_status_id']);
			if (isset($order_status['name'])) {
				$order_status_name = $order_status['name'];
			}else{
				$order_status_name ='';
			}
			$data['reports'][]=array(
				'order_id' 			=> $result['order_id'],
				'date_added' 		=> $result['date_added'],
				'totalamount'		=> $this->currency->format($totalamount, $this->config->get('config_currency'), $this->config->get('currency_value')),
				'payments' 			=> $payments,
				'order_status_name' => $order_status_name,
			);
		}

		
		$this->response->setOutput($this->load->view('possetting/splitpayment_report_print', $data));
	}
 
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'possetting/splitpayment_report')) {
		 $this->error['warning'] = $this->language->get('error_permission');
		}
		 return !$this->error;
	}


}