<?php
class ControllerAccountAccount extends Controller {
	public function index() {


		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}


        ///sharma for pos
		$this->load->model('account/address');
		if($this->customer->isLogged() && !$this->model_account_address->getAddressesById((int)$this->customer->getId())){
			$this->response->redirect($this->url->link('account/address/add', '', true));

		}

		

		$this->load->language('account/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 
		
		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['password'] = $this->url->link('account/password', '', true);
		$data['address'] = $this->url->link('account/address', '', true);
		////sharma for pos
		if($this->cart->getPoappByCustomer()){
			$data['pos_status'] = $this->url->link('account/posappstatus', '', true);
		}
		$data['new_pos_application'] = $this->url->link('account/posapp', '', true);

		
		$data['credit_cards'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/extension/credit_card/*.php');
		
		foreach ($files as $file) {
			$code = basename($file, '.php');
			
			if ($this->config->get('payment_' . $code . '_status') && $this->config->get('payment_' . $code . '_card')) {
				$this->load->language('extension/credit_card/' . $code, 'extension');

				$data['credit_cards'][] = array(
					'name' => $this->language->get('extension')->get('heading_title'),
					'href' => $this->url->link('extension/credit_card/' . $code, '', true)
				);
			}
		}
		
		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		
		if ($this->config->get('total_reward_status')) {
			$data['reward'] = $this->url->link('account/reward', '', true);
		} else {
			$data['reward'] = '';
		}		
		
		$data['return'] = $this->url->link('account/return', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['recurring'] = $this->url->link('account/recurring', '', true);



		$data['acount_dash'] = array();

		$this->load->model('account/order');
		if($this->model_account_order->getTotalOrdersAmount()){
			$data['order_total_amount'] = round($this->model_account_order->getTotalOrdersAmount(), 2);
		}else{
			$data['order_total_amount'] = 0;
		}
		if($this->model_account_order->getTotalOrders()){
			$data['order_total'] = (int)$this->model_account_order->getTotalOrders();
		}else{
			$data['order_total'] = 0;
		}

		if($this->cart->getPoappByCustomer() > 0){

			$this->load->model('account/posapp');
			if($this->model_account_posapp->getNotApproved()){
				$data['pos_not_approved'] = (int)$this->model_account_posapp->getNotApproved();
			}else{
				$data['pos_not_approved'] = 0;
			}

			if($this->model_account_posapp->getHold()){
				$data['pos_hold'] = (int)$this->model_account_posapp->getHold();
			}else{
				$data['pos_hold'] = 0;
			}

			if($this->model_account_posapp->getApproved()){
				$data['pos_approved'] = (int)$this->model_account_posapp->getApproved();
			}else{
				$data['pos_approved'] = 0;
			}

			if($this->model_account_posapp->getTotalSaleOfPos()){
				$data['total_sale'] = (int)$this->model_account_posapp->getTotalSaleOfPos();
			}else{
				$data['total_sale'] = 0;
			}

			if($this->model_account_posapp->getSaleOrdersNumber()){
				$data['total_sale_count'] = (int)$this->model_account_posapp->getSaleOrdersNumber();
			}else{
				$data['total_sale_count'] = 0;
			}
		}
		

		
		$data['acount_dash'][] = array(			
			'title' => '# of Order',
			'result' => $data['order_total']
		);
		$data['acount_dash'][] = array(			
			'title' => 'total of Order',
			'result' => $data['order_total_amount']
		);
		if($this->cart->getPoappByCustomer() > 0){

			$data['acount_dash'][] = array(			
				'title' => 'Approved POS',
				'result' => $data['pos_approved']
			);

			$data['acount_dash'][] = array(			
				'title' => 'Hold POS',
				'result' => $data['pos_hold']
			);

			$data['acount_dash'][] = array(			
				'title' => 'Not Approved POS',
				'result' => $data['pos_not_approved']
			);
			$data['acount_dash'][] = array(			
				'title' => 'Total Sale',
				'result' => $data['total_sale']
			);
			$data['acount_dash'][] = array(			
				'title' => 'Total Sale Count',
				'result' => $data['total_sale_count'] 
			);
		}

		


		

		
		$this->load->model('account/customer');	
		
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('account/account', $data));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
