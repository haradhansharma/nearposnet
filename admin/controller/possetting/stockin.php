<?php
class ControllerPossettingStockin extends Controller {
	private $error = array();

	public function index() {
		////sharma for pos
		if(($this->cart->getCommissionPending() > $this->config->get('module_posapp_payment_limit')) &&  ($this->cart->getRegdiff() > $this->config->get('module_posapp_payment_trail_allowed_day') ) ){
			$this->load->language('extension/module/posapp');
			$this->session->data['success'] = sprintf($this->language->get('text_limit_over'), '<b>Limit is: ' . $this->currency->format($this->config->get('module_posapp_payment_limit'), $this->config->get('config_currency')).'</b>', 'But current due is: '.'<b>'.$this->currency->format($this->cart->getCommissionPending(), $this->config->get('config_currency')) . '</b>' ) ;
			$this->response->redirect($this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'], true));
		}
		$this->load->language('possetting/stockin');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/stockin');

		$this->getList();
	}

	public function editqty() {
		$this->load->language('possetting/stockin');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/stockin');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_possetting_stockin->updateOpionQty($this->request->post);
			//echo"<pre>";
			//print_r($this->request->post);die();
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('possetting/stockin', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		
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

		if (isset($this->request->get['filter_search'])) {
			$data['filter_search'] = $this->request->get['filter_search'];
		} else {
			$data['filter_search'] = '';
		}

		$url = '';
		
		if ($order == 'ASC') {
		$url .= '&order=DESC';
		} else {
		$url .= '&order=ASC';
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
			'href' => $this->url->link('possetting/stockin', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['stockins'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$data['action'] = $this->url->link('possetting/stockin/editqty', 'user_token=' . $this->session->data['user_token'].$url, true);


		$data['heading_title'] 		= $this->language->get('heading_title');
		$data['text_list'] 			= $this->language->get('text_list');
		$data['text_form'] 			= $this->language->get('text_form');
		$data['text_no_results'] 	= $this->language->get('text_no_results');
		$data['text_confirm'] 		= $this->language->get('text_confirm');
		$data['text_none'] 			= $this->language->get('text_none');
		$data['text_select'] 		= $this->language->get('text_select');
		$data['text_fixed'] 		= $this->language->get('text_fixed');
		$data['entry_name'] 		= $this->language->get('entry_name');
		$data['entry_option'] 		= $this->language->get('entry_option');
		$data['entry_action'] 	    = $this->language->get('entry_action');
		$data['entry_qty'] 			= $this->language->get('entry_qty');
		$data['entry_search'] 		= $this->language->get('entry_search');
		$data['button_update']		= $this->language->get('button_update');
		$data['button_delete']		= $this->language->get('button_delete');
		$data['button_save']		= $this->language->get('button_save');
		$data['button_cancel']		= $this->language->get('button_cancel');
		$data['user_token']         		= $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['cancel'] = $this->url->link('possetting/stockin', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['sort_name'] 	= $this->url->link('possetting/stockin', 'user_token=' . $this->session->data['user_token'] . '&sort=name', true);
		$data['sort_option']= $this->url->link('possetting/stockin', 'user_token=' . $this->session->data['user_token'] . '&sort=option', true);
		$data['sort_qty'] 	= $this->url->link('possetting/stockin', 'user_token=' . $this->session->data['user_token'] . '&sort=qty', true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['sort'] 		  = $sort;
		$data['order'] 		  = $order;
		
		
		$data['header'] 	 = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] 	 = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/stockin', $data));
	}

	public function search() {
	$json['products']=array();
		$this->load->language('possetting/stockin');
		$this->load->model('possetting/stockin');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		
		if (isset($this->request->get['filter_search'])) {
			$filter_search = $this->request->get['filter_search'];
		} else {
			$filter_search = false;
		}
		
		$json_filter=array(
			'filter_search'=> $filter_search,
		);

		
		$upc_infos = $this->model_possetting_stockin->getStockinOptionUpcs($json_filter);
		if(!empty($upc_infos)){
		foreach($upc_infos as $result){
			if(isset($result['product_option_value_id'])) {
				$option_infos = $this->model_possetting_stockin->getProductOptionValue($result['product_option_value_id']);
			}
						
			if (isset($option_infos['name'])) {
				$valuename = $option_infos['name'];
			} else {
				$valuename = '';
			}

			if (isset($option_infos['option_id'])) {
				$option_id = $option_infos['option_id'];
			} else {
				$option_id = '';
			}

			$options_data=array();
			$options = $this->model_possetting_stockin->getStockinProductOption($option_infos['option_id']);
			foreach($options as $option) {
				$options_data[]= $option['name'];
			}

			$products_data=array();
			$product_info = $this->model_possetting_stockin->getStockinProduct($result['product_id']);
			foreach($product_info as $product) {
				$products_data[]= $product['name'];
			}

			$json['products'][]=array(
				'product_option_value_id' 	=> $result['product_option_value_id'],
				'product_id' 				=> $result['product_id'],
				'valuename' 				=> $valuename,
				'option' 					=> $options_data,
				'productname' 				=> $products_data,
				'option_id' 				=> $option_id,
				'upc' 						=> $result['upc'],
			);
			//print_r($products_data);die();
		}
		}
		if(empty($json['products']))
		{
			$results = $this->model_possetting_stockin->getStockinUpcs($json_filter);
			if(!empty($results)){
			foreach($results as $result){
			$products_data=array();
			$product_info = $this->model_possetting_stockin->getStockinProduct($result['product_id']);
			foreach($product_info as $product) {
				$products_data[]= $product['name'];
			}
			$json['products'][]=array(
				'product_option_value_id' 	=> '',
				'product_id' 				=> $result['product_id'],
				'valuename' 				=> '',
				'option' 					=> '',
				'productname' 				=> $products_data,
				'option_id' 				=> '',
				'upc' 						=> $result['upc'],
			);
			}
		}
		}
										
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}