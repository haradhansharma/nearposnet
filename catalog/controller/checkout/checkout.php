<?php
class ControllerCheckoutCheckout extends Controller {
	public function index() {
		// Validate cart has products and has stock.
		// if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) || !isset($this->request->get['posapp_id']) ) {
			$this->response->redirect($this->url->link('checkout/cart'));
		}

		// Validate minimum quantity requirements.
		// $products = $this->cart->getProducts();
////sharma for pos
		unset($this->session->data['pos_distance']);

		if(isset($this->request->get['posapp_id'])) {
			$checkout_posapp_id = $this->request->get['posapp_id'];
		} elseif(isset($this->session->data['checkout_posapp_id'])){
			$checkout_posapp_id = $this->session->data['checkout_posapp_id'];
		} else{
			$checkout_posapp_id = 0;
		}
		unset($this->session->data['checkout_posapp_id']);
		$this->session->data['checkout_posapp_id'] = $checkout_posapp_id;

		if(isset($this->session->data['checkout_posapp_id'])) {		
			$products = $this->cart->getProducts2($checkout_posapp_id);
		}else{	   	
			$products = $this->cart->getProducts();
		}
		

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->response->redirect($this->url->link('checkout/cart'));
			}
		}

		$this->load->language('checkout/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		// Required by klarna
		if ($this->config->get('payment_klarna_account') || $this->config->get('payment_klarna_invoice')) {
			$this->document->addScript('http://cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'), 1);
		$data['text_checkout_account'] = sprintf($this->language->get('text_checkout_account'), 2);
		$data['text_checkout_payment_address'] = sprintf($this->language->get('text_checkout_payment_address'), 2);
		$data['text_checkout_shipping_address'] = sprintf($this->language->get('text_checkout_shipping_address'), 3);
		$data['text_checkout_shipping_method'] = sprintf($this->language->get('text_checkout_shipping_method'), 4);
		
		if ($this->cart->hasShipping()) {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
		} else {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);	
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		$data['logged'] = $this->customer->isLogged();

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = '';
		}

		$data['shipping_required'] = $this->cart->hasShipping();

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('checkout/checkout', $data));
	}

	public function con(){

		$this->load->language('checkout/checkout');


		//shipping address
		$data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
		$data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
		$data['shipping_company'] = $this->session->data['shipping_address']['company'];
		$data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
		$data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
		$data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
		$data['shipping_city'] = $this->session->data['shipping_address']['city'];		
		$data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
		$data['shipping_country'] = $this->session->data['shipping_address']['country'];

		//payment address
		$data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
		$data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
		$data['payment_company'] = $this->session->data['payment_address']['company'];
		$data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
		$data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
		$data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
		$data['payment_city'] = $this->session->data['payment_address']['city'];		
		$data['payment_zone'] = $this->session->data['payment_address']['zone'];
		$data['payment_country'] = $this->session->data['payment_address']['country'];


		//Shipping Method
		$data['shipping_method'] = $this->session->data['shipping_method']['title'];

		//Payment Method
		$data['payment_method'] = $this->session->data['payment_method']['title'];




		$this->response->setOutput($this->load->view('checkout/confirmview', $data));


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

	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}