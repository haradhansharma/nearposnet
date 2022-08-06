<?php
class ControllerPossettingCommissionReport extends Controller {
	private $error = array();
	public function index() {
///sharma for pos	

		unset($this->session->data['selected_order_id']);

		$this->load->language('possetting/commission_report');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/commission_report');
		
		$this->getList();
	}

	public function getList() { 
		////sharma for pos
		unset($this->session->data['selected_order_id']);
		$this->load->language('possetting/commission_report');

		if (isset($this->request->get['filter_username'])) {
			$filter_username = $this->request->get['filter_username'];
		} else {
			$filter_username = null;
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$filter_payment_method = $this->request->get['filter_payment_method'];
		} else {
			$filter_payment_method = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'order_id';
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


		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . $this->request->get['filter_username'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$url .= '&filter_payment_method=' . $this->request->get['filter_payment_method'];
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
			'href' => $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['users'] = array(); 

		$filter_data = array(
			'filter_username'   => $filter_username,
			'filter_order_id'   => $filter_order_id,
			'filter_payment_method'   => $filter_payment_method,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

        //Checkout Completed Order
		$completed_status = $this->config->get('config_complete_status');
		$data['completed_orders']=array();
		$completed_orders = $this->model_possetting_commission_report->getCompletedOrders($completed_status);
		foreach($completed_orders as $completed_order){
			$data['completed_orders'][]=array(
				'commission_order_id' => $completed_order['commission_order_id'],				
				'invoice' => $completed_order['invoice_prefix'].$completed_order['commission_order_id'],
				'href' => $this->url->link('possetting/confirm', 'user_token=' . $this->session->data['user_token']. '&commission_order_id=' . $completed_order['commission_order_id'], true),
				'payable' => $this->currency->format($completed_order['payable'], $this->config->get('config_currency'), $this->config->get('currency_value'))
			);
		}

		//checkout processing order
		$processing_status = $this->config->get('config_processing_status');
		$data['processing_orders']=array();
		$processing_orders = $this->model_possetting_commission_report->getProcessingOrders($processing_status);
		foreach($processing_orders as $processing_order){
			$data['processing_orders'][]=array(
				'commission_order_id' => $processing_order['commission_order_id'],				
				'invoice' => $processing_order['invoice_prefix'].$processing_order['commission_order_id'],
				'href' => $this->url->link('possetting/confirm', 'user_token=' . $this->session->data['user_token']. '&commission_order_id=' . $processing_order['commission_order_id'], true),
				'payable' => $this->currency->format($processing_order['payable'], $this->config->get('config_currency'), $this->config->get('currency_value'))
			);
		}

		//payment pending order
		$payment_pending = array(0);
		$data['pending_orders']=array();
		$pending_orders = $this->model_possetting_commission_report->getPendingOrders($payment_pending);
		foreach($pending_orders as $pending_order){
			$data['pending_orders'][]=array(
				'commission_order_id' => $pending_order['commission_order_id'],				
				'invoice' => $pending_order['invoice_prefix'].$pending_order['commission_order_id'],
				'href' => $this->url->link('possetting/confirm', 'user_token=' . $this->session->data['user_token']. '&commission_order_id=' . $pending_order['commission_order_id'], true),
				'payable' => $this->currency->format($pending_order['payable'], $this->config->get('config_currency'), $this->config->get('currency_value'))
			);
		}

		//unsuccessfull payment
		$unsuccessfull_status = array_merge($completed_status, $processing_status, $payment_pending);		
		$data['unsuccess_orders']=array();
		$unsuccess_orders = $this->model_possetting_commission_report->getUnsuccessOrders($unsuccessfull_status);
		foreach($unsuccess_orders as $unsuccess_order){
			$data['unsuccess_orders'][]=array(
				'commission_order_id' => $unsuccess_order['commission_order_id'],				
				'invoice' => $unsuccess_order['invoice_prefix'].$unsuccess_order['commission_order_id'],
				'href' => $this->url->link('possetting/confirm', 'user_token=' . $this->session->data['user_token']. '&commission_order_id=' . $unsuccess_order['commission_order_id'], true),
				'payable' => $this->currency->format($unsuccess_order['payable'], $this->config->get('config_currency'), $this->config->get('currency_value'))
			);
		}

		
		$this->load->model('possetting/commission_report');
		$commission_total = $this->model_possetting_commission_report->getTotalReport($filter_data);
		$results = $this->model_possetting_commission_report->getReports($filter_data);
		$grandtotale=0;
		$data['grandtotale']=0;

		foreach ($results as $result) {

			if(isset($result['username'])){
				$username=$result['username'];
			} else {
				$username='eCommerce';
			}

			if(isset($result['commission'])){
				$commissiontype=$result['commission'];
			} else {
				$commissiontype='';
			}

			if(isset($result['commission_value'])){
				$commission_value=$result['commission_value'];         
			} else {
				$commission_value=$this->config->get('module_posapp_commission_value');
			}

			$amount_info = $this->model_possetting_commission_report->getAmount($result['order_id']);

			if(isset($amount_info['payment_method'])){
				$payment_method=$amount_info['payment_method'];         
			} else {
				$payment_method='';
			}

			if(isset($amount_info['total'])){
				$amount=$amount_info['total'];         
			} else {
				$amount=0;
			}	

			if($commissiontype=='Fixed') {
				$commission=$commission_value;
			} else {
				$commission=$amount/100 * $commission_value;
			}

			
			$amount_info = $this->model_possetting_commission_report->getAmount($result['order_id']);
			if(isset($amount_info['total'])){
				$totals=$amount_info['total']; 
			} else {
				$totals='';
			}

			///sharma for pos

			$existing_status_id = $this->model_possetting_commission_report->getExistingOrderHistory($result['order_id']);

			if($existing_status_id){
				$ddd = $existing_status_id['order_status_id'];
			}else{
				$ddd = '' ;
			}


			if( $ddd  > 0){
				$status_name = $this->model_possetting_commission_report->getStatusName($ddd);
			}elseif($ddd == ''){
				$status_name = 'Need to genarate';
			}else{
				$status_name = 'Checkout Pending';
			}

			$grandtotale+=$totals;

			$data['users'][] = array(
				'user_id'    => isset($result['user_id'])?$result['user_id']:0,
				'username'	=> $username,
				'payment_method'	=> $payment_method,
				'order_id'	=> $result['order_id'],
				'amount'	=> $this->currency->format($amount, $this->config->get('config_currency'), $this->config->get('currency_value')),
				'commission'=>  $this->currency->format($commission,  $this->config->get('config_currency'), $this->config->get('currency_value')),
				'status'=> $status_name
			);
			
		}
		
		$data['grandtotale'] = $this->currency->format($grandtotale, $this->config->get('config_currency'), $this->config->get('currency_value'));
 ///sharma for pos    
		$data['checkout_pending'] = (int)$this->model_possetting_commission_report->checkoutPending();	
		$data['checkoutInitializing'] = (int)$this->model_possetting_commission_report->checkoutInitializing();
		$data['non_genarated'] = (int)$grandtotale - ((int)$data['checkout_pending'] + (int)$data['checkoutInitializing']);	

		$data['paybalance'] = sprintf($this->language->get('text_due'), ($this->currency->format($this->cart->getCommissionPending(),  $this->config->get('config_currency'), $this->config->get('currency_value'))));

		$data['heading_title']       = $this->language->get('heading_title');

		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_none'] 		 	 = $this->language->get('text_none');
		$data['text_print'] 		 = $this->language->get('text_print');

		$data['column_username']	 = $this->language->get('column_username');
		$data['column_order_id']	 = $this->language->get('column_order_id');
		$data['column_amount']	     = $this->language->get('column_amount');
		$data['column_commission']	 = $this->language->get('column_commission');

		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['button_save'] 		 = $this->language->get('button_save');
		$data['user_token']               = $this->session->data['user_token'];
		
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
		////sharma for pos

		if (isset($this->session->data['error'])) {
			$data['error'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . $this->request->get['filter_username'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$url .= '&filter_payment_method=' . $this->request->get['filter_payment_method'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order_id']	= $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=order_id' . $url, true);
		$data['sort_username']	= $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url, true);
		$data['sort_commission'] = $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=commission' . $url, true);
		$data['sort_amount']  	= $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=amount' . $url, true);
		$data['sort_status']  	= $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . $this->request->get['filter_username'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$url .= '&filter_payment_method=' . $this->request->get['filter_payment_method'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->post['setting_paymentmethod'])) {
			$setting_paymentmethods = $this->request->post['setting_paymentmethod'];
		} else {
			$setting_paymentmethods = $this->config->get('setting_paymentmethod');
		}
		
		$data['setting_paymentmethods'] = array();
		if(is_array($setting_paymentmethods)) {
			foreach ($setting_paymentmethods as $setting_paymentmethod) {
				$data['setting_paymentmethods'][] = array(
					'name' 	=> $setting_paymentmethod['name']
				);
			}
		}

		$pagination 	   = new Pagination();
		$pagination->total = $commission_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($commission_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($commission_total - $this->config->get('config_limit_admin'))) ? $commission_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $commission_total, ceil($commission_total / $this->config->get('config_limit_admin')));

		$data['filter_username'] = $filter_username;
		$data['filter_order_id'] = $filter_order_id;
		$data['filter_payment_method'] = $filter_payment_method;
		$this->load->model('possetting/user');

		$user_info = $this->model_possetting_user->getUser($filter_username);
		if (isset($user_info['username'])) {
			$data['username'] = $user_info['username'];
		} else {
			$data['username'] = '';
		}

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['commissionprint'] = $this->url->link('possetting/commission_report/comissionreport', 'user_token=' . $this->session->data['user_token'].$url, true);
		////sharma for pos
		$data['genarate'] = $this->url->link('possetting/commission_report/generate', 'user_token=' . $this->session->data['user_token'] . $url, true);
		////sharma for pos

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/commission_report', $data));
	}

	////sharma for pos

	public function generate() {
		$this->load->language('possetting/commission_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/commission_report');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->request->post['selected'])){
			unset($this->session->data['selected_order_id']);
			$selected_order_id = $this->request->post['selected']; 
			$this->session->data['selected_order_id'] = $selected_order_id;

			foreach($this->session->data['selected_order_id'] as $have_order){
				$entry_info = $this->model_possetting_commission_report->getExistingOrderDetails($have_order);				
			}
		}		

		if (isset($this->session->data['selected_order_id']) && $this->validateGenarate() && !$entry_info) {			

			$order_data = array();

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);


			require_once  DIR_CATALOG . "/model/setting/extension.php";
			$exs = new ModelSettingExtension($this->registry);

			$sort_order = array();	
			$results = $exs->getExtensionsForPosService('total');				

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results); 

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {						
					require_once  DIR_CATALOG . "/model/extension/total/" . $result['code'] . ".php";
					$a = "ModelExtensionTotal" . str_replace("_", "", $result['code']);
					$ttl = new $a($this->registry);	
					// We have to put the totals in an array so that they pass by reference.
					$ttl->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);

			$order_data['totals'] = $totals;

			$order_data['invoice_prefix']  = 'CO-'. date('Y') .'-00';			
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');
			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_SERVER;
				} else {
					$order_data['store_url'] = HTTP_SERVER; 
				}
			}

			$posapp_info = $this->model_possetting_commission_report->getPosapp($this->session->data['posapp_id']);	

			$this->load->model('localisation/zone');
			$this->load->model('localisation/country');							
			$order_data['firstname'] = $posapp_info['firstname'];
			$order_data['lastname'] = $posapp_info['lastname'];
			$order_data['email'] = $posapp_info['email'];
			$order_data['telephone'] = $posapp_info['telephone'];
			$order_data['commission_value'] = $posapp_info['commission_value'];
			$order_data['commission'] = $posapp_info['commission'];
			$order_data['posapp_id'] = $this->session->data['posapp_id'];
			if(isset($this->session->data['posapp_id']) > 0 ){
				$order_data['posname'] = $this->cart->getPosname($this->session->data['posapp_id']);
			}else{
				$order_data['posname'] = $this->config->get('config_name');
			}

			if ($order_data['posapp_id']) {
				if ($this->request->server['HTTPS']) {
					$order_data['pos_url'] = HTTPS_CATALOG . 'index.php?route=product/allpos/info&posapp_id=' . $order_data['posapp_id'];
				} else {
					$order_data['pos_url'] = HTTP_CATALOG . 'index.php?route=product/allpos/info&posapp_id=' . $order_data['posapp_id'];
				}					
			}

			$order_data['payment_firstname'] = $posapp_info['firstname'];
			$order_data['payment_lastname'] = $posapp_info['lastname'];
			$order_data['payment_company'] = $posapp_info['posname'];
			$order_data['payment_address_1'] = $posapp_info['address'];
			$order_data['payment_address_2'] = '';
			$order_data['payment_city'] = $posapp_info['city'];
			$order_data['payment_postcode'] = '';
			$zone_result = $this->model_localisation_zone->getZone($posapp_info['zone_id']);
			$order_data['payment_zone'] = $zone_result['name'];
			$order_data['payment_zone_id'] = $posapp_info['zone_id'];
			$country_result = $this->model_localisation_country->getCountry($posapp_info['country_id']);
			$order_data['payment_country'] = $country_result['name'];
			$order_data['payment_country_id'] = $posapp_info['country_id'];
			$order_data['payment_address_format'] = '';

			if ($this->config->get('module_posapp_payment_method_code')){
				$order_data['payment_method'] = $this->config->get('module_posapp_payment_method_code');
			} else {
				$order_data['payment_method'] = '';
			}

			if ($this->config->get('module_posapp_payment_method_code')) {
				$order_data['payment_code'] = $this->config->get('module_posapp_payment_method_code');
			} else {
				$order_data['payment_code'] = '';
			}

			$order_data['commissions'] =array();
			$total_sale_value = 0;	

			foreach($this->cart->getOrderData() as $commission_result){

				$order_data['commissions'][] =array(
					'order_id' => $commission_result['order_id'],
					'posapp_id' => $commission_result['posapp_id'],
					'posname' => $commission_result['posname'],
					'commission' => $commission_result['commission'],
					'commission_value' =>  $commission_result['commission_value'],
					'total' => $commission_result['total'],						
					'payable' => $commission_result['payable'],
					'tax' => $this->tax->getTax($commission_result['payable'], $this->config->get('module_posapp_tax_class_id')),						
				);

				$total_sale_value += $commission_result['total'];			
			}

			$order_data['total'] = $total_sale_value;
			$order_data['payable'] = $total_data['total'];	

			if(!isset($this->session->data['currency'])){
				$this->session->data['currency'] = $this->config->get('config_currency');
			}

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
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

			$commission_order_id = $this->model_possetting_commission_report->genarateComissionProduct($order_data);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('possetting/confirm', 'user_token=' . $this->session->data['user_token']. '&commission_order_id=' . $commission_order_id, true));

		}else{
			
			$this->error['warning'];

			$url = '';

			if (isset($this->request->get['filter_username'])) {
				$url .= '&filter_username=' . $this->request->get['filter_username'];
			}

			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}

			if (isset($this->request->get['filter_payment_method'])) {
				$url .= '&filter_payment_method=' . $this->request->get['filter_payment_method'];
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

			if (isset($this->request->post['setting_paymentmethod'])) {
				$setting_paymentmethods = $this->request->post['setting_paymentmethod'];
			} else {
				$setting_paymentmethods = $this->config->get('setting_paymentmethod');
			}

			$data['setting_paymentmethods'] = array();
			if(is_array($setting_paymentmethods)) {
				foreach ($setting_paymentmethods as $setting_paymentmethod) {
					$data['setting_paymentmethods'][] = array(
						'name' 	=> $setting_paymentmethod['name']
					);
				}
			}

			$existing_orders = array();
			foreach($this->session->data['selected_order_id'] as $has){				
				$entry_info = $this->model_possetting_commission_report->getExistingOrderDetails($has);
				foreach($entry_info as $entry){
					$existing_orders[] = 	$entry['order_id'];				
				}							
			}

			foreach($existing_orders as $key => $value){
				$ex_orders .= $value . ' ';
				$this->session->data['error'] = sprintf($this->language->get('text_exists_first'), $ex_orders, $this->language->get('text_exists_last'));
			}		
			
			$this->response->redirect($this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->index();


	}

	protected function validateGenarate() {
		if (!$this->user->hasPermission('modify', 'possetting/commission_report')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	///sharma for pos

	public function comissionreport() {
		$this->load->language('possetting/commission_report');
		$this->load->model('possetting/commission_report');	

		if (isset($this->request->get['filter_username'])) {
			$filter_username = $this->request->get['filter_username'];
		} else {
			$filter_username = null;
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$filter_payment_method = $this->request->get['filter_payment_method'];
		} else {
			$filter_payment_method = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'order_id';
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

		$url ='';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['users'] = array();

		$filter_data = array(
			'filter_username'   => $filter_username,
			'filter_order_id'   => $filter_order_id,
			'filter_payment_method'   => $filter_payment_method,
			'sort'  => $sort,
			'order' => $order,
			
		);

		$commission_total = $this->model_possetting_commission_report->getTotalReport($filter_data);
		$results = $this->model_possetting_commission_report->getReports($filter_data);

		foreach ($results as $result) {
			$user_info = $this->model_possetting_commission_report->getUserName($result['user_id']);
			if(isset($user_info)){

				if(isset($user_info['username'])){
					$username=$user_info['username'];         
				} else {
					$username='';
				}

				if(isset($user_info['commission'])){
					$commissiontype=$user_info['commission'];         
				} else {
					$commissiontype='';
				}

				if(isset($user_info['commission_value'])){
					$commission_value=$user_info['commission_value'];         
				} else {
					$commission_value='';
				}

				$amount_info = $this->model_possetting_commission_report->getAmount($result['order_id']);
				if(isset($amount_info['total'])){
					$amount=$amount_info['total'];         
				} else {
					$amount='';
				}

				if(isset($amount_info['payment_method'])){
					$payment_method=$amount_info['payment_method'];         
				} else {
					$payment_method='';
				}	
				
				if($commissiontype=='Fixed')
				{
					$commission=$commission_value;
				}
				else
				{
					$commission=$amount*$commission_value/100;
				}

			}
			if($commission) {
				$data['users'][] = array(
					'user_id'    => isset($result['user_id'])?$result['user_id']:0,
					'username'	=> $username,
					'payment_method'	=> $payment_method,
					'order_id'	=> $result['order_id'],
					'amount'	=> $this->currency->format($amount, $this->config->get('config_currency'), $this->config->get('currency_value')),
					'commission'=>  $this->currency->format($commission,  $this->config->get('config_currency'), $this->config->get('currency_value')),
				);
			}
		}
		$data['heading_title']       = $this->language->get('heading_title');
		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_none'] 		 	 = $this->language->get('text_none');
		$data['text_print'] 		 = $this->language->get('text_print');

		$data['column_username']	 = $this->language->get('column_username');
		$data['column_order_id']	 = $this->language->get('column_order_id');
		$data['column_amount']	     = $this->language->get('column_amount');
		$data['column_commission']	 = $this->language->get('column_commission');

		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['button_save'] 		 = $this->language->get('button_save');
		$data['user_token']               = $this->session->data['user_token'];
		
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/commissionreport_print', $data));
	}

	public function autocomplete(){
		$json = array();
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'username';
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
		$this->load->model('possetting/user');

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$accounts = $this->model_possetting_user->getUsers($filter_data);
		foreach ($accounts as $account) {

			$json[] = array(
				'user_id'    => isset($result['user_id'])?$result['user_id']:0,
				'username'   => strip_tags(html_entity_decode($account['username'], ENT_QUOTES, 'UTF-8'))
			);
		}
		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['username'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


}