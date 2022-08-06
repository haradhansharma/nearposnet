<?php
class ControllerPossettingStore extends Controller {
 private $error = array();
		public function index() {
			
		$this->load->language('possetting/store');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/store');
		
		$this->getList();
}
 public function add() {
		$this->load->language('possetting/store');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/store');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
		//database//
		$this->model_possetting_store->addStore($this->request->post);
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
		$this->response->redirect($this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
}
 public function edit(){
		$this->load->language('possetting/store');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/store');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

		$this->model_possetting_store->editStore($this->request->get['store_id'],$this->request->post);
		
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
		$this->response->redirect($this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getForm();
}
 public function delete() {
	
		$this->load->language('possetting/store');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/store');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
		foreach ($this->request->post['selected'] as $store_id)
		{
			$this->model_possetting_store->deleteStores($store_id);
		}

		$this->session->data['success'] = $this->language->get('text_success');
		$url = '';

		$this->response->redirect($this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		$this->getList();
}
 public function getList() {
		
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
		'href' => $this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('possetting/store/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('possetting/store/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['stores'] = array();

		$filter_data = array(
		'sort'  => $sort,
		'order' => $order,
		'start' => ($page - 1) * $this->config->get('config_limit_admin'),
		'limit' => $this->config->get('config_limit_admin')
		);
				
		$store_total = $this->model_possetting_store->getTotalStoress();
		
		$results=$this->model_possetting_store->getStores($filter_data);
						
		foreach($results as $result)
		{
			$data['stores'][]=array(
				'store_id'   =>$result['store_id'],
				'name'       =>$result['name'],
				'location'   =>$result['location'],
				'phone'      =>$result['phone'],
				'status'     => ($result['status'] ? $this->language->get('text_enable') : $this->language->get('text_disable')),
				'edit'       => $this->url->link('possetting/store/edit', 'user_token=' . $this->session->data['user_token'] .'&store_id=' . $result['store_id'] . $url, true)
			);
		}
   
		$data['heading_title']          = $this->language->get('heading_title');
		$data['text_list']           	= $this->language->get('text_list');
		$data['text_no_results'] 		= $this->language->get('text_no_results');
		$data['text_confirm']			= $this->language->get('text_confirm');
		$data['text_male']				= $this->language->get('Male');
		$data['text_female'] 			= $this->language->get('Female');
		$data['text_none'] 				= $this->language->get('text_none');
		$data['column_name']		    = $this->language->get('column_name');
		$data['column_location']		= $this->language->get('column_location');
		$data['column_phone']			= $this->language->get('column_phone');
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
	  
		$data['sort_name']  = $this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

		$data['sort_location']  = $this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . '&sort=location' . $url, true);
		
		$data['sort_phone']  = $this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . '&sort=phone' . $url, true);

		$data['sort_status']  = $this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
			
		    /// session
				
		if (isset($this->session->data['success'])) {
		 $data['success'] = $this->session->data['success'];
		unset($this->session->data['success']);
		} else {
		$data['success'] = '';
		}

		$data['sort']  = $sort;
		$data['order'] = $order;
		$data['packages']=array();
		
		$data['sort']=$sort;
		$data['order']=$order;
		$data['pagination']='';
		$data['results']='';
		
		$data['add']=$this->url->link('possetting/store/add','&user_token='.$this->session->data['user_token'].$url,true);
		
		$data['delete']=$this->url->link('possetting/store/delete','&user_token='.$this->session->data['user_token'].$url,true);
		   
		$url = '';
			///// pagination //////  
		if (isset($this->request->get['sort'])) {
		$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
		$url .= '&order=' . $this->request->get['order'];
		}
        
		$pagination = new Pagination();
		$pagination->total = $store_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($store_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($store_total - $this->config->get('config_limit_admin'))) ? $store_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $store_total, ceil($store_total / $this->config->get('config_limit_admin')));

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/store_list', $data));
}
                 
 protected function getForm() {
		$data['heading_title']          = $this->language->get('heading_title');
		$data['text_form']              = !isset($this->request->get['information_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default']           = $this->language->get('text_default');
		$data['text_enable']            = $this->language->get('text_enable');
		$data['text_disable']           = $this->language->get('text_disable');
		$data['text_select']            = $this->language->get('text_select');
		$data['entry_name']             = $this->language->get('entry_name');
		$data['entry_status']			= $this->language->get('entry_status');
		$data['entry_location']         = $this->language->get('entry_location');
		$data['entry_phone']          	= $this->language->get('entry_phone');
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
	 
	 	if (isset($this->error['phone'])) {
		 	$data['error_phone'] = $this->error['phone'];
		} else {
		 	$data['error_phone'] = '';
		}
		
	 	if (isset($this->error['name'])) {
		 	$data['error_name'] = $this->error['name'];
		} else {
		 	$data['error_name'] = '';
		}
	 
	 	if (isset($this->error['location'])) {
			$data['error_location'] = $this->error['location'];
		} else {
			$data['error_location'] = '';
		}
	 
			///  language //////
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$url = '';
     
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
		'text' => $this->language->get('text_home'),
		'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
		'text' => $this->language->get('heading_title'),
		'href' => $this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		if (!isset($this->request->get['store_id'])) {
		$data['action'] = $this->url->link('possetting/store/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
    	}else {
		$data['action'] = $this->url->link('possetting/store/edit', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'] . $url, true);
    	}
		$data['cancel'] = $this->url->link('possetting/store', 'user_token=' . $this->session->data['user_token'] . $url, true);
																/////edit qouery /////
		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
		$store_info = $this->model_possetting_store->getStore($this->request->get['store_id']);
			}
		$data['user_token'] = $this->session->data['user_token'];
		
		//////// editform /////////
		 
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (isset($store_info['name'])) {
			$data['name'] = $store_info['name'];
		} else {
			$data['name'] = '';
		}
	 
	 	if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (isset($store_info['location'])) {
			$data['location'] = $store_info['location'];
		} else {
			$data['location'] = '';
		}
	 
	 	if (isset($this->request->post['phone'])) {
			$data['phone'] = $this->request->post['phone'];
		} elseif (isset($store_info['phone'])) {
			$data['phone'] = $store_info['phone'];
		} else {
			$data['phone'] = '';
		}
	 
	 	if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (isset($store_info['status'])) {
			$data['status'] = $store_info['status'];
		} else {
			$data['status'] = '';
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

		$this->response->setOutput($this->load->view('possetting/store_form', $data));
}
protected function validateForm() {
		
		if (!$this->user->hasPermission('modify','possetting/store')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
		
		if ((utf8_strlen($this->request->post['name'])< 3)||(utf8_strlen($this->request->post['name']) > 255)) {
		$this->error['name']= $this->language->get('error_name');
		}
	
		if ((utf8_strlen($this->request->post['phone'])< 3)||(utf8_strlen($this->request->post['phone']) > 255)) {
		$this->error['phone']= $this->language->get('error_phone');
		}
	
		if ((utf8_strlen($this->request->post['location'])< 3)||(utf8_strlen($this->request->post['location']) > 255)) {
		$this->error['location']= $this->language->get('error_location');
		}
	
		return !$this->error;
}
          
 protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'possetting/store')) {
		 $this->error['warning'] = $this->language->get('error_permission');
		}
		 return !$this->error;
	}
		
	public function autocomplete() {
			
    $this->load->model('possetting/store');
		
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
		
		$results=$this->model_possetting_store->getStores($filter_data);
		foreach ($results as $result) {
			$json[] = array(
				'store_id' => $result['store_id'],
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