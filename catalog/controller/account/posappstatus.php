<?php
class ControllerAccountPosappstatus extends Controller {
	public function index() {
		$apikey = $this->config->get('module_posapp_apikey');
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/posappstatus', '', true);

			$this->response->redirect($this->url->link('account/posapp', '', true));
		}

		$this->load->language('account/posapp');

		$this->document->setTitle($this->language->get('heading_title_status'));
		$this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $apikey );
		$data['new_app'] = $this->url->link('account/posapp', '', true);
		

		if ($this->request->server['HTTPS']) {
			$server = HTTPS_SERVER;
		} else {
			$server = HTTP_SERVER;
		}
		$data['pos_login'] = $server . 'admin/index.php?route=pos/login' ;

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_posappstatus'),
			'href' => $this->url->link('account/posappstatus', '', true)
		);

		$this->load->model('account/posapp');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('tool/image');
		
		
		// $data['column_amount'] = sprintf($this->language->get('column_amount'), $this->config->get('config_currency'));

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['posappstatus'] = array();

		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$posapplication_total = $this->model_account_posapp->getTotalPosapps();

		$results = $this->model_account_posapp->getPosapps($filter_data);

		if($results){
		foreach ($results as $result) {

			if ($result['frontimage']) {
					$image_frontimage = $this->model_tool_image->resize($result['frontimage'], 228, 114);
				} else {
					$image_frontimage = $this->model_tool_image->resize('placeholder.png', 228, 114);
				}
			if ($result['tradelicenceimage']) {
					$image_tradelicenceimage = $this->model_tool_image->resize($result['tradelicenceimage'], 228, 114);
				} else {
					$image_tradelicenceimage = $this->model_tool_image->resize('placeholder.png', 228, 114);
				}
			
				if($result['country_id']){
					$country_result = $this->model_localisation_country->getCountry($result['country_id']);
					$country = $country_result['name'];
				}
				if($result['zone_id']){
					$zone_result = $this->model_localisation_zone->getZone($result['zone_id']);
					$zone = $zone_result['name'];
				}
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$cost = $this->currency->format($this->tax->calculate($result['cost'],  $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$cost = false;
				}
				if($result['status'] == 0 ){
					$status = 'Disabled';
				}elseif($result['status'] == 2){
					$status = 'Hold';
				}else{
					$status = 'Enabled';
				}
				

			$data['posappstatus'][] = array(
				'posapp_id'      => $result['posapp_id'],
				'customer_id' => $result['customer_id'],
				'customer_group_id' => $result['customer_group_id'],
				'username' => $result['username'],
				'store_id' => $result['store_id'],
				'posname' => $result['posname'],
				'firstname' => $result['firstname'],
				'lastname' => $result['lastname'],
				'email' => $result['email'],
				'frontimage' => $image_frontimage,
				'tradelicenceimage' => $image_tradelicenceimage,
				'licence_validaty' => date($this->language->get('date_format_short'), strtotime($result['licence_validaty'])),
				'telephone' => $result['telephone'],
				'address' => $result['address'],
				'city' => $result['city'],
				'country' => $country,
				'zone' => $zone ,
				'latitude' => $result['latitude'],
				'longitude' => $result['longitude'],
				'shipping_cost' => $cost,
				'code' => $result['code'],
				'ip' => $result['ip'],
				'status_number' => $result['status'],
				'status' => $status,
				'commission' => $result['commission'],
				'commission_value' => $result['commission_value'],				
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'        => $this->url->link('account/posapp',  'posapp_id=' . $result['posapp_id'] , true),
				'edit'        => $this->url->link('account/posappedit', 'posapp_id=' . $result['posapp_id'], true)
			);
		}

		$pagination = new Pagination();
		$pagination->total = $posapplication_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('account/posappstatus', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($posapplication_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($posapplication_total - 10)) ? $posapplication_total : ((($page - 1) * 10) + 10), $posapplication_total, ceil($posapplication_total / 10));

		$data['total'] = $this->currency->format($this->customer->getBalance(), $this->session->data['currency']);
	}

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/posappstatus', $data));
	}
}