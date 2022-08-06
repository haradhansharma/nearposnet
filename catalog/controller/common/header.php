<?php
class ControllerCommonHeader extends Controller {
///sharma for POS
	public function getLivelocation(){
		if (isset($this->request->post['lat'])) {
			$lat = $this->request->post['lat'];
		}else{
			$lat = 0;

		}
		if (isset($this->request->post['lng'])) {
			$lng = $this->request->post['lng'];
		}else{
			$lng = 0;

		}

		$this->session->data['livelat'] = $lat;
		$this->session->data['livelng'] = $lng;	

		if (isset($this->request->post['formated_address'])) {
			$formated_address = $this->request->post['formated_address'];
		}else{
			$formated_address = '';
		}

		$this->session->data['formated_address'] = $formated_address;

		if (isset($this->request->post['map_components'])) {
			$map_components = $this->request->post['map_components'];
		}else{
			$map_components = '';
		}		

		if($map_components){
			foreach($map_components as $map_component ){
				$types = $map_component['types'];


				if (in_array("country", $types) ) {
					$map_country = $map_component["long_name"];
					$this->session->data['map_country'] = $map_country;
				}

				if (in_array("administrative_area_level_1", $types) ) {
					$map_division = $map_component["long_name"];
					$this->session->data['map_division'] = $map_division;
				}

				if (in_array("administrative_area_level_2", $types) ) {
					$map_district = $map_component["long_name"];
					$this->session->data['map_district'] = $map_district;
				}

				if (in_array("locality", $types) && in_array("political", $types) ) {
					$map_city = $map_component["long_name"];
					$this->session->data['map_city'] = $map_city;
				}

				if (in_array("sublocality_level_1", $types) ) {
					$map_area = $map_component["long_name"];
					$this->session->data['map_area'] = $map_area;
				}

				if (in_array("route", $types) ) {
					$map_route = $map_component["long_name"];
					$this->session->data['map_route'] = $map_route;
				}
			}	
		}
	}

	///sharma for POS  
	public function index() {  
////sharma for Theme
	

		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
		///sharma for POS
		if(isset($this->session->data['formated_address'])){
		$data['formated_address'] = $this->session->data['formated_address'];
		$data['map_area'] = $this->session->data['map_area'];
	    }
	    if(isset($this->session->data['livelat'])){
		$data['livelat'] = $this->session->data['livelat'];
	    }
		$data['new_app'] = $this->url->link('account/posapp', '', true);
		$data['apikey'] = $this->config->get('module_posapp_apikey');
		$this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $data['apikey'] );

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		$data['text_compare'] = sprintf($this->language->get('text_compare')); 
		$data['compare_total'] =  isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0 ; 
	    $data['compare'] = $this->url->link('product/compare');	

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = $this->language->get('text_wishlist');
			$data['wishlist_total'] = $this->model_account_wishlist->getTotalWishlist();
			$data['account_name'] = $this->customer->getFirstName();

		} else {
			$data['text_wishlist'] = $this->language->get('text_wishlist');
			$data['wishlist_total'] = isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0;
			$data['account_name'] = $this->language->get('text_account');
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true); 
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');

		$data['account_menu'] = $this->load->controller('extension/module/account');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		return $this->load->view('common/header', $data);
	}
}
