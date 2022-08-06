<?php
class ControllerPossettingProduct extends Controller {
 private $error = array();
		public function index() {
			////sharma for pos
		if(($this->cart->getCommissionPending() > $this->config->get('module_posapp_payment_limit')) &&  ($this->cart->getRegdiff() > $this->config->get('module_posapp_payment_trail_allowed_day') ) ){
			$this->load->language('extension/module/posapp');
			$this->session->data['success'] = sprintf($this->language->get('text_limit_over'), '<b>Limit is: ' . $this->currency->format($this->config->get('module_posapp_payment_limit'), $this->config->get('config_currency')).'</b>', 'But current due is: '.'<b>'.$this->currency->format($this->cart->getCommissionPending(), $this->config->get('config_currency')) . '</b>' ) ;
			$this->response->redirect($this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'], true));
		}
			
		$this->load->language('possetting/product');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/product');
		
		$this->getList();
}
 public function add() {
		$this->load->language('possetting/product');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/product');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
		//database//
		$this->model_possetting_product->addProduct($this->request->post);
		
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
		$this->response->redirect($this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
}
 public function edit(){
		$this->load->language('possetting/product');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

		$this->model_possetting_product->editProduct($this->request->get['product_id'],$this->request->post);
		
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
		$this->response->redirect($this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
}
 public function delete() {
	
		$this->load->language('possetting/product');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/product');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
		foreach ($this->request->post['selected'] as $product_id)
		{
			$this->model_possetting_product->deleteProducts($product_id);
		}

		$this->session->data['success'] = $this->language->get('text_success');
		$url = '';

		$this->response->redirect($this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getList();
}
 public function getList() {
		
	 	if (isset($this->request->get['filter_store'])) {
			
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = false;
		}
	 
	 	if (isset($this->request->get['filter_name'])) {
			
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = false;
		}
	 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 $sort = 'name';
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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
	 	
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
	 
	 	if (isset($this->request->get['filter_store'])) {
		$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
	 
	 	if (isset($this->request->get['filter_name'])) {
		$url .= '&filter_name=' . $this->request->get['filter_name'];
		}
		
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
		'href' => $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('possetting/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('possetting/product/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
		'sort'  => $sort,
		'order' => $order,
		'filter_store'    => $filter_store,
		'filter_name'     => $filter_name,
		'start' => ($page - 1) * $this->config->get('config_limit_admin'),
		'limit' => $this->config->get('config_limit_admin')
		);
				
		$product_total = $this->model_possetting_product->getTotalProductss($filter_data);
		
		$results=$this->model_possetting_product->getProducts($filter_data);
		
		$this->load->model('tool/image');
		$this->load->model('possetting/store');				
		foreach($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 65, 65);
			} else {
			 	$image = $this->model_tool_image->resize('no_image.png', 65, 65);
			}
			
			$store_info = $this->model_possetting_store->getStore($result['store_id']);
			if(isset($store_info['name'])) {
				$store=$store_info['name'];         
			} else {
				$store='';
			}
			
			$data['products'][]=array(
				'product_id' => $result['product_id'],
				'name'       => $result['name'],
				'image'		 => $image,
				'store'		 => $store,
				'model'      => $result['model'],
				'price'      => $result['price'],
				'quantity'   => $result['quantity'],
				'status'     => ($result['status'] ? $this->language->get('text_enable') : $this->language->get('text_disable')),
				'edit'       => $this->url->link('possetting/product/edit', 'user_token=' . $this->session->data['user_token'] .'&product_id=' . $result['product_id'] . $url, true)
			);
		}
   
		$data['heading_title']          = $this->language->get('heading_title');
		$data['text_list']           	= $this->language->get('text_list');
		$data['text_no_results'] 		= $this->language->get('text_no_results');
		$data['text_confirm']			= $this->language->get('text_confirm');
		$data['text_male']				= $this->language->get('Male');
		$data['text_female'] 			= $this->language->get('Female');
		$data['text_none'] 				= $this->language->get('text_none');
	 	$data['text_select'] 			= $this->language->get('text_select');
		$data['column_name']		    = $this->language->get('column_name');
		$data['entry_store']		    = $this->language->get('entry_store');
		$data['entry_name']		    	= $this->language->get('entry_name');
		$data['entry_model']		    = $this->language->get('entry_model');
		$data['column_description']		= $this->language->get('column_description');
		$data['column_image']			= $this->language->get('column_image');
		$data['column_price']			= $this->language->get('column_price');
		$data['column_qty']				= $this->language->get('column_qty');
		$data['column_store']			= $this->language->get('column_store');
		$data['column_status']			= $this->language->get('column_status');
		$data['column_action']			= $this->language->get('column_action');
		$data['button_remove']          = $this->language->get('button_remove');
		$data['button_edit']            = $this->language->get('button_edit');
		$data['button_add']             = $this->language->get('button_add');
		$data['button_filter']          = $this->language->get('button_filter');
		$data['button_delete']          = $this->language->get('button_delete');
		$data['text_confirm']           = $this->language->get('text_confirm');
		$data['name']                   = $this->language->get('name');
		$data['user_token']                  = $this->session->data['user_token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['sort']  = $sort;
		$data['order'] = $order;
	 	$data['filter_store']      = $filter_store;
	 	$data['filter_name']       = $filter_name;
	  
		$data['sort_name']  = $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_model']  = $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . '&sort=model' . $url, true);
		$data['sort_store'] = $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . '&sort=store' . $url, true);
		$data['sort_price']	= $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . '&sort=price' . $url, true);
		$data['sort_qty']	= $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . '&sort=qty' . $url, true);
		$data['sort_status']= $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_image'] = $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . '&sort=image' . $url, true);	
		    /// session
				
		if (isset($this->session->data['success'])) {
		 $data['success'] = $this->session->data['success'];
		unset($this->session->data['success']);
		} else {
		$data['success'] = '';
		}

		$data['sort']  = $sort;
		$data['order'] = $order;
	 	$data['filter_store']   = $filter_store;
	 	$data['filter_name']    = $filter_name;
		$data['packages']=array();
		
		$data['sort']=$sort;
		$data['order']=$order;
		$data['pagination']='';
		$data['results']='';
		        //action button
		
		$data['add']=$this->url->link('possetting/product/add','&user_token='.$this->session->data['user_token'].$url,true);
		
		$data['delete']=$this->url->link('possetting/product/delete','&user_token='.$this->session->data['user_token'].$url,true);
		   
		$url = '';
			///// pagination //////  
		if (isset($this->request->get['sort'])) {
		$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
		$url .= '&order=' . $this->request->get['order'];
		}
        
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/product_list', $data));
	}
                 
 	protected function getForm() {
		$data['heading_title']          = $this->language->get('heading_title');
		$data['text_form']              = !isset($this->request->get['information_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default']           = $this->language->get('text_default');
		$data['text_enable']            = $this->language->get('text_enable');
		$data['text_disable']           = $this->language->get('text_disable');
		$data['text_select']            = $this->language->get('text_select');
		$data['entry_name']             = $this->language->get('entry_name');
		$data['entry_model']            = $this->language->get('entry_model');
		$data['entry_store']            = $this->language->get('entry_store');
		$data['entry_quantity']         = $this->language->get('entry_quantity');
		$data['entry_status']			= $this->language->get('entry_status');
		$data['entry_description']      = $this->language->get('entry_description');
		$data['entry_image']          	= $this->language->get('entry_image');
		$data['entry_price']          	= $this->language->get('entry_price');
		$data['entry_shipping']         = $this->language->get('entry_shipping');
		$data['entry_taxclass']         = $this->language->get('entry_taxclass');
		$data['button_save']            = $this->language->get('button_save');
		$data['button_add']             = $this->language->get('button_add');
		$data['button_remove']          = $this->language->get('button_remove');
		$data['button_cancel']          = $this->language->get('button_cancel');
		$data['text_none'] 				= $this->language->get('text_none');

		/////image show//////
				
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
	 	if (isset($this->error['name'])) {
		 	$data['error_name'] = $this->error['name'];
		} else {
		 	$data['error_name'] = '';
		}
	 		
	 	if (isset($this->error['description'])) {
		 	$data['error_description'] = $this->error['description'];
		} else {
		 	$data['error_description'] = '';
		}
	 
			///  language //////
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
	 
	 	$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
	 
		$url = '';
     
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
		'text' => $this->language->get('text_home'),
		'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
		'text' => $this->language->get('heading_title'),
		'href' => $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		if (!isset($this->request->get['product_id'])) {
		$data['action'] = $this->url->link('possetting/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
    	}else {
		$data['action'] = $this->url->link('possetting/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
    	}
		$data['cancel'] = $this->url->link('possetting/product', 'user_token=' . $this->session->data['user_token'] . $url, true);
																/////edit qouery /////
		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
		$product_info = $this->model_possetting_product->getProduct($this->request->get['product_id']);
			}
		$data['user_token'] = $this->session->data['user_token'];
		
		//////// editform /////////
		 
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (isset($product_info['name'])) {
			$data['name'] = $product_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (isset($product_info['model'])) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = '';
		}
	 
	 	if (isset($this->request->post['store_id'])) {
			$data['store_id'] = $this->request->post['store_id'];
		} elseif (!empty($product_info['store_id'])) {
			$data['store_id'] = $product_info['store_id'];
		} else {
			$data['store_id'] = '';
		}
	 
	 	if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (isset($product_info['description'])) {
			$data['description'] = $product_info['description'];
		} else {
			$data['description'] = '';
		}
	 
	 	if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (isset($product_info['image'])) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}
	 
	 	if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (isset($product_info['quantity'])) {
			$data['quantity'] = $product_info['quantity'];
		} else {
			$data['quantity'] = '';
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (isset($product_info['price'])) {
			$data['price'] = $product_info['price'];
		} else {
			$data['price'] = '';
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (isset($product_info['shipping'])) {
			$data['shipping'] = $product_info['shipping'];
		} else {
			$data['shipping'] = '';
		}

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (isset($product_info['tax_class_id'])) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = '';
		}

		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
	 
	 	if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (isset($product_info['status'])) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = '';
		}
	 
	 	$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($product_info) && $product_info['image'] && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/product_form', $data));
}
protected function validateForm() {
		
		if (!$this->user->hasPermission('modify','possetting/product')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
		
		
		if ((utf8_strlen($this->request->post['name'])< 3)||(utf8_strlen($this->request->post['name']) > 255)) {
		$this->error['name']= $this->language->get('error_name');
		}
	
		return !$this->error;
}
          
protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'possetting/product')) {
		 $this->error['warning'] = $this->language->get('error_permission');
		}
		 return !$this->error;
	}
	
	public function autocomplete() {
			
    $this->load->model('possetting/product');
		
	  if (isset($this->request->get['filter_name'])) {
			
		if (isset($this->request->get['sort'])) {
		 $sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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
			
		$filter_data = array(
		'filter_name'=> $this->request->get['filter_name'],
		'order' => $order,
		'start' => ($page - 1) * $this->config->get('config_limit_admin'),
		'limit' => $this->config->get('config_limit_admin')
		);
		
		$results=$this->model_possetting_product->getProducts($filter_data);
		foreach ($results as $result) {
			$json[] = array(
				'product_id' => $result['product_id'],
				'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
		}
		}
		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}
		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		}
	
	
	
		
	}
?>