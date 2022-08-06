<?php

class ControllerExtensionModulePosapp extends Controller {
	private $error = array();	


	public function install() {
		$this->load->model('extension/module/posapp');
		$this->model_extension_module_posapp->install();

	}

	public function uninstall() {
		$this->load->model('extension/module/posapp');
		$this->model_extension_module_posapp->uninstall();

	}

	public function index() {

		$this->load->language('extension/module/posapp');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_posapp', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_apikey'] = $this->language->get('entry_apikey');
		$data['help_apikey'] = $this->language->get('help_apikey');
		$data['help_apikey'] = $this->language->get('help_apikey');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array(); 

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/posapp', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/posapp', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);



		if (isset($this->request->post['module_posapp_status'])) {
			$data['module_posapp_status'] = $this->request->post['module_posapp_status'];
		} else {
			$data['module_posapp_status'] = $this->config->get('module_posapp_status');
		}

		if (isset($this->request->post['module_posapp_apikey'])) {
			$data['module_posapp_apikey'] = $this->request->post['module_posapp_apikey'];
		} else {
			$data['module_posapp_apikey'] = $this->config->get('module_posapp_apikey');
		}
		if (isset($this->request->post['module_posapp_commission_value'])) {
			$data['module_posapp_commission_value'] = $this->request->post['module_posapp_commission_value'];
		} else {
			$data['module_posapp_commission_value'] = $this->config->get('module_posapp_commission_value');
		}

		if (isset($this->request->post['module_posapp_agent_commission'])) {
			$data['module_posapp_agent_commission'] = $this->request->post['module_posapp_agent_commission'];
		} else {
			$data['module_posapp_agent_commission'] = $this->config->get('module_posapp_agent_commission');
		}

		if (isset($this->request->post['module_posapp_target_product_to_pay'])) {
			$data['module_posapp_target_product_to_pay'] = $this->request->post['module_posapp_target_product_to_pay'];
		} else {
			$data['module_posapp_target_product_to_pay'] = $this->config->get('module_posapp_target_product_to_pay');
		}



		$method_data = array();
		$address =array(
			'country_id' => $this->config->get('config_country_id'),
			'zone_id' => $this->config->get('config_zone_id')
		);

		require_once  DIR_CATALOG . "/model/setting/extension.php";
		$exs = new ModelSettingExtension($this->registry);
		$results = $exs->getExtensions('payment');
		foreach ($results as $result) {
			if ($this->config->get('payment_' . $result['code'] . '_status')) {
				require_once  DIR_CATALOG . "/model/extension/payment/" . $result['code'] . ".php";

				$a = "ModelExtensionPayment" . $result['code'];
				$pym = new $a($this->registry);					

				$method = $pym->getMethod($address, 10000000000000);
				if ($method) {
					$method_data[$result['code']] = $method;						
				}
			}
		}

		$sort_order = array();

		foreach ($method_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $method_data);

		$data['paymentmethods'] = $method_data;      


		if (isset($this->request->post['module_posapp_payment_method_code'])) {
			$data['module_posapp_payment_method_code'] = $this->request->post['module_posapp_payment_method_code'];
		} else {
			$data['module_posapp_payment_method_code'] = $this->config->get('module_posapp_payment_method_code');
		}

		if (isset($this->request->post['module_posapp_payment_limit'])) {
			$data['module_posapp_payment_limit'] = $this->request->post['module_posapp_payment_limit'];
		} else {
			$data['module_posapp_payment_limit'] = $this->config->get('module_posapp_payment_limit');
		}

		if (isset($this->request->post['module_posapp_payment_trail_allowed_day'])) {
			$data['module_posapp_payment_trail_allowed_day'] = $this->request->post['module_posapp_payment_trail_allowed_day'];
		} else {
			$data['module_posapp_payment_trail_allowed_day'] = $this->config->get('module_posapp_payment_trail_allowed_day');
		}

		

		$this->load->model('localisation/tax_class'); 

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['module_posapp_tax_class_id'])) {
			$data['module_posapp_tax_class_id'] = $this->request->post['module_posapp_tax_class_id'];
		} else {
			$data['module_posapp_tax_class_id'] = $this->config->get('module_posapp_tax_class_id');
		}
		
		$data['customer_groups'] = array();		
		$this->load->model('customer/customer_group');		
		$customer_groups = $this->model_customer_customer_group->getCustomerGroups();
		if ($customer_groups) {
			foreach ($customer_groups as $customer_group) {				
				$data['customer_groups'][] = array(
					'customer_group_id' => $customer_group['customer_group_id'],
					'name'        => $customer_group['name']
				);					
			}
		}

		if (isset($this->request->post['module_posapp_customer_group'])) { 
			$data['module_posapp_customer_group'] = $this->request->post['module_posapp_customer_group'];
		}else{
			$data['module_posapp_customer_group'] =  $this->config->get('module_posapp_customer_group');
		}

		if($this->config->get('module_posapp_customer_group'))  {
			$con_customer_group = $this->model_customer_customer_group->getCustomerGroup($this->config->get('module_posapp_customer_group'));       	
			$data['defined_customer_group_id'] = $con_customer_group['customer_group_id'];
			$data['defined_customer_group_name'] = $con_customer_group['name'];
		}else{
			$data['defined_customer_group_id'] = 0;
			$data['defined_customer_group_name'] = '';

		}





		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/posapp', $data));


	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/posapp')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}


}
