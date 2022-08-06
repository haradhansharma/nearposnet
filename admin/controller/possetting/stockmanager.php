<?php
class ControllerPossettingStockmanager extends Controller {
 private $error = array();
		public function index() {
			
		$this->load->language('possetting/stockmanager');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/stockmanager');
		
		$this->getList();
}
 public function add() {
		$this->load->language('possetting/stockmanager');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/stockmanager');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
		//database//
		$this->model_possetting_stockmanager->addStock($this->request->post);
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
		$this->response->redirect($this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
}
 public function edit(){
		$this->load->language('possetting/stockmanager');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/stockmanager');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

		$this->model_possetting_stockmanager->editStock($this->request->get['stock_id'],$this->request->post);
		
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
		$this->response->redirect($this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
}
 public function delete() {
	
		$this->load->language('possetting/stockmanager');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/stockmanager');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
		foreach ($this->request->post['selected'] as $stock_id)
		{
			$this->model_possetting_stockmanager->deleteStocks($stock_id);
		}

		$this->session->data['success'] = $this->language->get('text_success');
		$url = '';

		$this->response->redirect($this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getList();
}
 public function getList() {
		
	 	if (isset($this->request->get['filter_store'])) {
			
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = false;
		}
	 
	 	if (isset($this->request->get['filter_product'])) {
			
			$filter_product = $this->request->get['filter_product'];
		} else {
			$filter_product = false;
		}
	 	 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 $sort = 'store_id';
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
	 
	 	if (isset($this->request->get['filter_product'])) {
		$url .= '&filter_product=' . $this->request->get['filter_product'];
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
		'href' => $this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('possetting/stockmanager/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('possetting/stockmanager/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['stockmanagers'] = array();

		$filter_data = array(
		'sort'  => $sort,
		'order' => $order,
		'filter_store'    	 => $filter_store,
		'filter_product'     => $filter_product,
		'start' => ($page - 1) * $this->config->get('config_limit_admin'),
		'limit' => $this->config->get('config_limit_admin')
		);
				
		$stockmanager_total = $this->model_possetting_stockmanager->getTotalStockss($filter_data);
		
		$results=$this->model_possetting_stockmanager->getStocks($filter_data);
		
		$this->load->model('possetting/store');				
		$this->load->model('catalog/product');				
		foreach($results as $result)
		{
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);
				if(isset($product_info['name']))
				{
					$product=$product_info['name'];         
				}
				else
				{
					$product='';
				}
				
				$store_info = $this->model_possetting_store->getStore($result['store_id']);
				if(isset($store_info['name']))
				{
					$store=$store_info['name'];         
				}
				else
				{
					$store='';
				}
			
			$data['stockmanagers'][]=array(
				'stock_id' =>$result['stock_id'],
				'quantity'=>$result['quantity'],
				'store'		 =>$store,
				'product'		 =>$product,
				'edit'       => $this->url->link('possetting/stockmanager/edit', 'user_token=' . $this->session->data['user_token'] .'&stock_id=' . $result['stock_id'] . $url, true)
			);
		}
   
		$data['heading_title']          = $this->language->get('heading_title');
		$data['text_list']           	= $this->language->get('text_list');
		$data['text_no_results'] 		= $this->language->get('text_no_results');
		$data['text_confirm']			= $this->language->get('text_confirm');
		$data['text_none'] 				= $this->language->get('text_none');
	 	$data['text_select'] 			= $this->language->get('text_select');
		$data['column_product']		    = $this->language->get('column_product');
		$data['entry_store']		    = $this->language->get('entry_store');
		$data['entry_name']		    	= $this->language->get('entry_name');
		$data['entry_product']		    = $this->language->get('entry_product');
		$data['column_quantity']		= $this->language->get('column_quantity');
		$data['column_store']			= $this->language->get('column_store');
		$data['column_action']			= $this->language->get('column_action');
		$data['button_remove']          = $this->language->get('button_remove');
		$data['button_edit']            = $this->language->get('button_edit');
		$data['button_add']             = $this->language->get('button_add');
		$data['button_filter']          = $this->language->get('button_filter');
		$data['button_delete']          = $this->language->get('button_delete');
		$data['text_confirm']           = $this->language->get('text_confirm');
		$data['user_token']                  = $this->session->data['user_token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		 } else {
			$data['error_warning'] = '';
		 }
		$data['sort']  = $sort;
		$data['order'] = $order;
	 	$data['filter_store']      = $filter_store;
	 	$data['filter_product']       = $filter_product;
	  
		$data['sort_product']  = $this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . '&sort=product' . $url, true);
		$data['sort_store'] = $this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . '&sort=store' . $url, true);
		$data['sort_quantity']  = $this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . '&sort=quantity' . $url, true);
		
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
	 	$data['filter_product']    = $filter_product;
		$data['packages']=array();
		
		$data['sort']=$sort;
		$data['order']=$order;
		$data['pagination']='';
		$data['results']='';
		       		
		$data['add']=$this->url->link('possetting/stockmanager/add','&user_token='.$this->session->data['user_token'].$url,true);
		
		$data['delete']=$this->url->link('possetting/stockmanager/delete','&user_token='.$this->session->data['user_token'].$url,true);
		   
		$url = '';
			
		if (isset($this->request->get['sort'])) {
		$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
		$url .= '&order=' . $this->request->get['order'];
		}
        
		$pagination = new Pagination();
		$pagination->total = $stockmanager_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($stockmanager_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($stockmanager_total - $this->config->get('config_limit_admin'))) ? $stockmanager_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $stockmanager_total, ceil($stockmanager_total / $this->config->get('config_limit_admin')));

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/stockmanager_list', $data));
}
                 
 protected function getForm() {
		$data['heading_title']          = $this->language->get('heading_title');
		$data['text_form']              = !isset($this->request->get['information_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default']           = $this->language->get('text_default');
		$data['text_enable']            = $this->language->get('text_enable');
		$data['text_disable']           = $this->language->get('text_disable');
		$data['text_select']            = $this->language->get('text_select');
		$data['entry_product']          = $this->language->get('entry_product');
		$data['entry_store']            = $this->language->get('entry_store');
		$data['entry_quantity']         = $this->language->get('entry_quantity');
		$data['entry_status']			= $this->language->get('entry_status');
		$data['entry_model']      		= $this->language->get('entry_model');
		$data['entry_image']          	= $this->language->get('entry_image');
		$data['button_save']            = $this->language->get('button_save');
		$data['button_add']             = $this->language->get('button_add');
		$data['button_remove']          = $this->language->get('button_remove');
		$data['button_cancel']          = $this->language->get('button_cancel');
		$data['text_none'] 				= $this->language->get('text_none');
	 	$data['user_token']                  = $this->session->data['user_token'];
				
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
			///  language //////
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
	 
	 	//$this->load->model('catalog/option');
		//$data['options'] = $this->model_catalog_option->getOptions($data);
	 
		$url = '';
     
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
		'text' => $this->language->get('text_home'),
		'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
		'text' => $this->language->get('heading_title'),
		'href' => $this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		if (!isset($this->request->get['stock_id'])) {
		$data['action'] = $this->url->link('possetting/stockmanager/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
    	}else {
		$data['action'] = $this->url->link('possetting/stockmanager/edit', 'user_token=' . $this->session->data['user_token'] . '&stock_id=' . $this->request->get['stock_id'] . $url, true);
    	}
		$data['cancel'] = $this->url->link('possetting/stockmanager', 'user_token=' . $this->session->data['user_token'] . $url, true);
																/////edit qouery /////
		if (isset($this->request->get['stock_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
		$stockmanager_info = $this->model_possetting_stockmanager->getStock($this->request->get['stock_id']);
			}
		$data['user_token'] = $this->session->data['user_token'];
		
		//////// editform /////////
		 
		if (isset($this->request->post['product'])) {
			$data['product'] = $this->request->post['product'];
		} elseif (isset($stockmanager_info['product'])) {
			$data['product'] = $stockmanager_info['product'];
		} else {
			$data['product'] = '';
		}
	 
	 	if (isset($this->request->post['store'])) {
			$data['store'] = $this->request->post['store'];
		} elseif (isset($stockmanager_info['store'])) {
			$data['store'] = $stockmanager_info['store'];
		} else {
			$data['store'] = '';
		}
	 
	 	if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (isset($stockmanager_info['quantity'])) {
			$data['quantity'] = $stockmanager_info['quantity'];
		} else {
			$data['quantity'] = '';
		}
	 
	 	if (isset($this->request->post['product_id'])){
			$data['product_id'] = $this->request->post['product_id'];
		}elseif(isset($stockmanager_info['product_id'])){
			$data['product_id'] = $stockmanager_info['product_id'];
		}else {
			$data['product_id'] = '';		
		}
		
		if(!empty($data['product_id']))
		{	
			$this->load->model('catalog/product');
			$products_info=$this->model_catalog_product->getProduct($data['product_id']);
			$data['product']=$products_info['name'];
		}
		else
		{
			$data['product']='';
		}
	 
	 	if (isset($this->request->post['store_id'])){
			$data['store_id'] = $this->request->post['store_id'];
		}elseif(isset($stockmanager_info['store_id'])){
			$data['store_id'] = $stockmanager_info['store_id'];
		}else {
			$data['store_id'] = '';		
		}
		
		if(!empty($data['store_id']))
		{	
			$this->load->model('possetting/store');
			$stores_info=$this->model_possetting_store->getStore($data['store_id']);
			$data['store']=$stores_info['name'];
		}
		else
		{
			$data['store']='';
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
		
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/stockmanager_form', $data));
}
protected function validateForm() {
		
		if (!$this->user->hasPermission('modify','possetting/stockmanager')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			
		return !$this->error;
}
          
protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'possetting/stockmanager')) {
		 $this->error['warning'] = $this->language->get('error_permission');
		}
		 return !$this->error;
	}
		
	}
?>