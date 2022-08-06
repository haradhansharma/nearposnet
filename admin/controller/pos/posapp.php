<?php
class ControllerPosPosapp extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('pos/posapp');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pos/posapp');

		$this->getList();
	}

	// public function add() {
	// 	$status = $this->config->get('module_posapp_status');
	// 	$apikey = $this->config->get('module_posapp_apikey');		

	// 	$this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $apikey );

	// 	$this->load->language('pos/posapp');

	// 	$this->document->setTitle($this->language->get('heading_title'));

	// 	$this->load->model('pos/posapp');

	// 	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
	// 		$this->model_pos_posapp->addposapp($this->request->post);

	// 		$this->session->data['success'] = $this->language->get('text_success');

	// 		$url = '';

	// 		if (isset($this->request->get['sort'])) {
	// 			$url .= '&sort=' . $this->request->get['sort'];
	// 		}

	// 		if (isset($this->request->get['order'])) {
	// 			$url .= '&order=' . $this->request->get['order'];
	// 		}

	// 		if (isset($this->request->get['page'])) {
	// 			$url .= '&page=' . $this->request->get['page'];
	// 		} 

	// 		$this->response->redirect($this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . $url, true));
	// 	}

	// 	$this->getForm(); 
	// }

	public function edit() {

		$this->load->language('pos/posapp');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pos/posapp');

		if (isset($this->request->get['posapp_id']) ) {
			$posapp_info = $this->model_pos_posapp->getPosappById($this->request->get['posapp_id']);
		}	

		if($posapp_info['frontimage']){
			$data['f_image'] = $posapp_info['frontimage'];
		}else{
			$data['f_image'] = '';

		}

		if($posapp_info['tradelicenceimage']){
			$data['tl_image'] = $posapp_info['tradelicenceimage'];
		}else{
			$data['tl_image'] = '';
        }

        if($posapp_info['salt']){
			$data['salt'] = $posapp_info['salt'];
		}else{
			$data['salt'] = '';
        }
		
		if($posapp_info['password']){
			$data['password'] = $posapp_info['password'];
		}else{
			$data['password'] = '';
        }
		
			

		if (isset($this->request->files['frontimage'])) {                    
			$data['frontimage'] =$this->request->files['frontimage']['name']; 
			$data['frontimage_tmp'] =$this->request->files['frontimage']['tmp_name'];
			$pathinfo = pathinfo($this->request->files['frontimage']['name']);
			if($this->request->files['frontimage']['name']){
				$data['frontimage_extension']= $pathinfo['extension'];
			}
		}else{
			$data['frontimage'] = $posapp_info['frontimage'];
			$data['frontimage_tmp'] =''; 
			$pathinfo = '';
			$data['frontimage_extension']  = '';                         
		}

        
		if (isset($this->request->files['tradelicenceimage'])) {                    
			$data['tradelicenceimage'] =$this->request->files['tradelicenceimage']['name']; 
			$data['tradelicenceimage_tmp'] =$this->request->files['tradelicenceimage']['tmp_name'];
			$pathinfo = pathinfo($this->request->files['tradelicenceimage']['name']);
			if($this->request->files['tradelicenceimage']['name']){
				$data['tradelicenceimage_extension']= $pathinfo['extension']; 
			}
		}else{
			$data['tradelicenceimage'] =$posapp_info['tradelicenceimage'];;
			$data['tradelicenceimage_tmp'] =''; 
			$pathinfo = '';
			$data['tradelicenceimage_extension']  = '';                         
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->load->language('pos/posapp');

			$posapp_id = $this->request->get['posapp_id'];
			$this->model_pos_posapp->editPosapp($posapp_id, $this->request->post, $data);		


	        $approved_app = $this->model_pos_posapp->getUser($posapp_id);
			if(!$approved_app ){			  	
             $this->model_pos_posapp->addUser($posapp_id, $this->request->post, $data);             
			}

			if ($approved_app) {				
				$this->model_pos_posapp->editUser($posapp_id, $this->request->post, $data); 
			}

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

			$this->response->redirect($this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('pos/posapp');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pos/posapp');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $posapp_id) {
				$this->model_pos_posapp->deletePosapp($posapp_id);
				$this->model_pos_posapp->deleteUser($posapp_id);
			}

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

			$this->response->redirect($this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		
		if (isset($this->request->get['filter_store'])) {
			
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = false;
		}
		
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

		$url = '';
		
		if ($order == 'ASC') {
		$url .= '&order=DESC';
		} else {
		$url .= '&order=ASC';
		}
		
		if (isset($this->request->get['filter_store'])) {
		$url .= '&filter_store=' . $this->request->get['filter_store'];
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
			'href' => $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		// $data['add'] = $this->url->link('pos/posapp/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('pos/posapp/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['posapps'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'filter_store'    => $filter_store,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$posapp_total = $this->model_pos_posapp->getTotalPosapps($filter_data);
		$results = $this->model_pos_posapp->getPosapps($filter_data);
		$this->load->model('possetting/store');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		
		foreach ($results as $result) {
			$store_info = $this->model_possetting_store->getStore($result['store_id']);
			if(isset($store_info['name']))
			{
				$store=$store_info['name'];         
			}
			else
			{
				$store='';
			}

			if($result['country_id']){
					$country_result = $this->model_localisation_country->getCountry($result['country_id']);
					$country = $country_result['name'];
				}
				if($result['zone_id']){
					$zone_result = $this->model_localisation_zone->getZone($result['zone_id']);
					$zone = $zone_result['name'];
				}

				if($result['status'] == 1){
					$app_status = $this->language->get('text_enabled');
				}elseif($result['status'] == 2){
                    $app_status = $this->language->get('text_hold');
				}else{
					$app_status = $this->language->get('text_disabled');

				}	

			$data['posapps'][] = array(
				'posapp_id'    => $result['posapp_id'],
				'username'   => $result['username'],
				'posname'   => $result['posname'].'('.$result['posapp_id'].')',
				'address'   => $result['address'].', '.$result['city'].', '.$zone.', '.$country,
				'store'   	 => $store,
				'status'     => $app_status,				
				'commission' => $result['commission'],
				'commission_value' => $result['commission_value'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'       => $this->url->link('pos/posapp/edit', 'user_token=' . $this->session->data['user_token'] . '&posapp_id=' . $result['posapp_id'] . $url, true)
			);
			//print_r($data['posapps']);
		}

		$data['heading_title'] 		= $this->language->get('heading_title');
		$data['text_list'] 			= $this->language->get('text_list');
		$data['text_no_results'] 	= $this->language->get('text_no_results');
		$data['text_confirm'] 		= $this->language->get('text_confirm');
		$data['text_none'] 			= $this->language->get('text_none');
		$data['text_select'] 		= $this->language->get('text_select');
		$data['text_percent'] 		= $this->language->get('text_percent');
		$data['text_fixed'] 		= $this->language->get('text_fixed');
		$data['column_username'] 	= $this->language->get('column_username');
		$data['column_store'] 		= $this->language->get('column_store');
		$data['column_commission'] 	= $this->language->get('column_commission');
		$data['entry_store'] 		= $this->language->get('entry_store');
		$data['column_status'] 		= $this->language->get('column_status');
		$data['column_date_added'] 	= $this->language->get('column_date_added');
		$data['column_action'] 		= $this->language->get('column_action');
		$data['button_add'] 		= $this->language->get('button_add');
		$data['button_edit'] 		= $this->language->get('button_edit');
		$data['button_delete'] 		= $this->language->get('button_delete');
		$data['button_filter']		= $this->language->get('button_filter');
		$data['user_token']         		= $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['filter_store']    = $filter_store;
		
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

		$data['sort_username'] = $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url, true);
		$data['sort_store'] = $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . '&sort=store' . $url, true);
		$data['sort_status'] = $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_commission'] = $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . '&sort=commission' . $url, true);
		$data['sort_date_added'] = $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $posapp_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($posapp_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($posapp_total - $this->config->get('config_limit_admin'))) ? $posapp_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $posapp_total, ceil($posapp_total / $this->config->get('config_limit_admin')));

		$data['sort'] 		  = $sort;
		$data['order'] 		  = $order;
		$data['filter_store'] = $filter_store;
		
		
		$data['header'] 	 = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] 	 = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pos/posapp_list', $data));
	}

	protected function getForm() {	

	$status = $this->config->get('module_posapp_status');
		$apikey = $this->config->get('module_posapp_apikey');
		$this->load->language('pos/posapp');		

		$this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $apikey );
		// $this->document->addScript('admin/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		// $this->document->addScript('admin/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		// $this->document->addScript('admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		// $this->document->addStyle('admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');	


		$data['heading_title']		= $this->language->get('heading_title');
		$data['text_form'] 			= !isset($this->request->get['posapp_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] 		= $this->language->get('text_enabled');
		$data['text_disabled'] 		= $this->language->get('text_disabled');
		$data['entry_username'] 	= $this->language->get('entry_username');
		$data['entry_store'] 		= $this->language->get('entry_store');
		$data['entry_password'] 	= $this->language->get('entry_password');
		$data['entry_confirm'] 		= $this->language->get('entry_confirm');
		$data['entry_firstname'] 	= $this->language->get('entry_firstname');
		$data['entry_lastname'] 	= $this->language->get('entry_lastname');
		$data['entry_email'] 		= $this->language->get('entry_email');
		$data['entry_image'] 		= $this->language->get('entry_image');
		$data['entry_status'] 		= $this->language->get('entry_status');
		$data['entry_commission'] 	= $this->language->get('entry_commission');
		$data['entry_value'] 		= $this->language->get('entry_value');
		$data['button_save'] 		= $this->language->get('button_save');
		$data['button_cancel'] 		= $this->language->get('button_cancel');
		$data['text_select'] 		= $this->language->get('text_select');
		$data['text_percent']		= $this->language->get('text_percent');
		$data['text_fixed'] 		= $this->language->get('text_fixed');
		$data['text_none'] 			= $this->language->get('text_none');		

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['posapp_id']) ) {
			$posapp_info = $this->model_pos_posapp->getPosappById($this->request->get['posapp_id']);
		}

        $this->load->model('tool/image');
		if (is_file(DIR_IMAGE . $posapp_info['frontimage'])) {
				$image_frontimage = $this->model_tool_image->resize($posapp_info['frontimage'], 200, 200);
			} else {
				$image_frontimage = $this->model_tool_image->resize('no_image.png', 200, 200);
			}
		if (is_file(DIR_IMAGE . $posapp_info['tradelicenceimage'])) {
				$image_tradelicenceimage = $this->model_tool_image->resize($posapp_info['tradelicenceimage'], 200, 200);
			} else {
				$image_tradelicenceimage = $this->model_tool_image->resize('no_image.png', 200, 200);
			}	


		if ($posapp_info['frontimage']) {
			$data['frontimage_pre'] = $image_frontimage;
		}else{
            $data['frontimage_pre'] = '';
		}
		

		if ($posapp_info['tradelicenceimage']) {
			$data['tradelicenceimage_pre'] = $image_tradelicenceimage;
		}else{
            $data['tradelicenceimage_pre'] = '';
		}


		

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['frontimage'])) {
			$data['error_frontimage'] = $this->error['frontimage'];
		} else {
			$data['error_frontimage'] = '';
		}
		if (isset($this->error['tradelicenceimage'])) {
			$data['error_tradelicenceimage'] = $this->error['tradelicenceimage'];
		} else {
			$data['error_tradelicenceimage'] = '';
		}

		if (isset($this->error['size'])) {
			$data['error_size'] = $this->error['size'];
		} else {
			$data['error_size'] = '';
		}
		if (isset($this->error['extension'])) {
			$data['error_extension'] = $this->error['extension'];
		} else {
			$data['error_extension'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}
		if (isset($this->error['posname'])) {
			$data['error_posname'] = $this->error['posname'];
		} else {
			$data['error_posname'] = '';
		}
		if (isset($this->error['city'])) {
			$data['error_city'] = $this->error['city'];
		} else {
			$data['error_city'] = '';
		}
		if (isset($this->error['zone'])) {
			$data['error_zone'] = $this->error['zone'];
		} else {
			$data['error_zone'] = '';
		}
		if (isset($this->error['cost'])) {
			$data['error_cost'] = $this->error['cost'];
		} else {
			$data['error_cost'] = '';
		}
		if (isset($this->error['alloweddistance'])) {
			$data['error_alloweddistance'] = $this->error['alloweddistance'];
		} else {
			$data['error_alloweddistance'] = '';
		}
		if (isset($this->error['licence_validaty'])) {
			$data['error_licence_validaty'] = $this->error['licence_validaty'];
		} else {
			$data['error_licence_validaty'] = '';
		}
		if (isset($this->error['coordinate'])) {
			$data['error_coordinate'] = $this->error['coordinate'];
		} else {
			$data['error_coordinate'] = '';
		}
		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		// Categories
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		if (isset($this->request->post['posapp_category'])) {
			$categories = $this->request->post['posapp_category'];
		} elseif (isset($this->request->get['posapp_id'])) {
			$categories = $this->model_pos_posapp->getPosappCategories($this->request->get['posapp_id']);
		} else {
			$categories = array();
		}

		$data['posapp_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['posapp_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);


		if (!isset($this->request->get['posapp_id'])) {
			$data['action'] = $this->url->link('pos/posapp/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('pos/posapp/edit', 'user_token=' . $this->session->data['user_token'] . '&posapp_id=' . $this->request->get['posapp_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('pos/posapp', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['posapp_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$posapp_info = $this->model_pos_posapp->getPosappById($this->request->get['posapp_id']);
		}


		$data['commissions'] = array();				
		$data['commissions'][] = array(
			'commission'    => $this->language->get('text_percent'),
			'value' 		=> 'Pecentage'
		);
		$data['commissions'][] = array(
			'commission'    => $this->language->get('text_fixed'),
			'value' 		=> 'Fixed'
		);
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($posapp_info)) {
			$data['status'] = $posapp_info['status'];
		} else {
			$data['status'] = 0;
		}

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


		
		if (isset($this->request->post['store_id'])) {
			$data['store_id'] = $this->request->post['store_id'];

		} elseif (!empty($posapp_info)) {
			$data['store_id'] = $posapp_info['store_id'];
		} else {
			$data['store_id'] = 0;
		}		

		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];

		} elseif (!empty($posapp_info)) {
			$data['username'] = $posapp_info['username'];
		} else {
			$data['username'] = '';
		}

		if (isset($this->request->post['posname'])) {
			$data['posname'] = $this->request->post['posname'];
		}elseif (!empty($posapp_info)) {
			$data['posname'] = $posapp_info['posname'];
		} else {
			$data['posname'] = '';
		}		
		
		// $this->load->model('possetting/store');
		// $data['stores'] = $this->model_possetting_store->getStores($data);

		// if (isset($this->request->post['posapp_store_id'])) {
		// 	$data['posapp_store_id'] = $this->request->post['posapp_store_id'];
		// } elseif (!empty($posapp_info['posapp_store_id'])) {
		// 	$data['posapp_store_id'] = $posapp_info['posapp_store_id'];
		// } else {
		// 	$data['posapp_store_id'] = '';
		// }

		
		

		if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
		}elseif (!empty($posapp_info)) {
			$data['address'] = $posapp_info['address'];
		}  else {
			$data['address'] = '';
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		}elseif (!empty($posapp_info)) {
			$data['city'] = $posapp_info['city'];
		}  else {
			$data['city'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		}elseif (!empty($posapp_info)) {
			$data['country_id'] = (int)$posapp_info['country_id'];
		}  else {
			$data['country_id'] = '';
		}
		if (isset($this->request->post['zone_id'])) {
			$data['zone_id'] = $this->request->post['zone_id'];
		}elseif (!empty($posapp_info)) {
			$data['zone_id'] = (int)$posapp_info['zone_id'];
		}  else {
			$data['zone_id'] = '';
		}
		if (isset($this->request->post['cost'])) {
			$data['cost'] = $this->request->post['cost'];
		}elseif (!empty($posapp_info)) {
			$data['cost'] = (int)$posapp_info['cost'];
		} else {
			$data['cost'] = 0;
		}
		if (isset($this->request->post['alloweddistance'])) {
			$data['alloweddistance'] = $this->request->post['alloweddistance'];
		}elseif (!empty($posapp_info)) {
			$data['alloweddistance'] = (int)$posapp_info['alloweddistance'];
		} else {
			$data['alloweddistance'] = 1;
		}
		if (isset($this->request->post['licence_validaty'])) {
			$data['licence_validaty'] = $this->request->post['licence_validaty'];
		}elseif (!empty($posapp_info)) {
			$data['licence_validaty'] = date($this->language->get('date_format_short'), strtotime($posapp_info['licence_validaty']));
		} else {
			$data['licence_validaty'] = '';
		}
		if (isset($this->request->post['latitude'])) {
			$data['latitude'] = $this->request->post['latitude'];
		}elseif (!empty($posapp_info)) {
			$data['latitude'] = $posapp_info['latitude'];
		} else {
			$data['latitude'] = '';
		}

		if (isset($this->request->post['longitude'])) {
			$data['longitude'] = $this->request->post['longitude'];
		}elseif (!empty($posapp_info)) {
			$data['longitude'] = $posapp_info['longitude'];
		} else {
			$data['longitude'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($posapp_info)) {
			$data['firstname'] = $posapp_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($posapp_info)) {
			$data['lastname'] = $posapp_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($posapp_info)) {
			$data['email'] = $posapp_info['email'];
		} else {
			$data['email'] = '';
		}
		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		}elseif (!empty($posapp_info)) {
			$data['telephone'] = $posapp_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		}elseif (!empty($posapp_info)) {
			$data['code'] = $posapp_info['code'];
		} else {
			$data['code'] = '';
		}

		

		
		if (isset($this->request->post['commission'])) {
			$data['commission'] = $this->request->post['commission'];
		} elseif (isset($posapp_info['commission'])) {
			$data['commission'] = $posapp_info['commission'];
		} else {
			$data['commission'] = '';
		}

		if (isset($this->request->post['commission_value'])) {
			$data['commission_value'] = $this->request->post['commission_value'];
		} elseif (isset($posapp_info['commission_value'])) {
			$data['commission_value'] = $posapp_info['commission_value'];
		} else {
			$data['commission_value'] = '';
		}



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pos/posapp_form', $data));
	}

	public function country() {
		$json = array();

		if (isset($this->request->get['country_id'])) {
			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

			if ($country_info) {
				$this->load->model('localisation/zone');

				foreach ($this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']) as $zone) {
					$json['zone'][] = array(
						'zone_id' => $zone['zone_id'],
						'name'    => $zone['name']
					);					
				}
			}

		}


 
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_posname']) || isset($this->request->get['filter_email'])) {
			if (isset($this->request->get['filter_posname'])) {
				$filter_name = $this->request->get['filter_posname'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}
			
			$this->load->model('pos/posapp');

			$filter_data = array(
				'filter_posname'      => $filter_posname,
				'filter_email'     => $filter_email,				
				'start'            => 0,
				'limit'            => 5
			);

			$results = $this->model_pos_posapp->getPosapps($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'posapp_id'       => $result['posapp_id'],					
					'posname'              => strip_tags(html_entity_decode($result['posname'], ENT_QUOTES, 'UTF-8')),					
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email'],
					'telephone'         => $result['telephone'],					
					'address'           => $result['address']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['posname'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'pos/posapp')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['username']) < 3) || (utf8_strlen($this->request->post['username']) > 20)) {
			$this->error['username'] = $this->language->get('error_username');
		}

		$posusername_info = $this->model_pos_posapp->getUserNameByUsername($this->request->post['username']);
		$posusername_info2 = $this->model_pos_posapp->getUserNameByUsername2($this->request->post['username']);

		if (!isset($this->request->get['posapp_id'])) {
			if ($posusername_info || $posusername_info2 ) {
				$this->error['warning'] = $this->language->get('error_exists_username');
			}
		} else {
			if (($posusername_info || $posusername_info2) && (($this->request->get['posapp_id'] != $posusername_info2['posapp_id']) )) {
				$this->error['warning'] = $this->language->get('error_exists_username');
			}
		}
		if (($this->request->post['latitude']) == '' || ($this->request->post['longitude'] == '')) {
			$this->error['coordinate'] = $this->language->get('error_coordinate');
		}

		if ((utf8_strlen($this->request->post['posname']) < 3) || (utf8_strlen($this->request->post['posname']) > 20)) {
			$this->error['posname'] = $this->language->get('error_posname');
		}
		if ((utf8_strlen($this->request->post['city']) < 3) || (utf8_strlen($this->request->post['city']) > 20)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		if ($this->request->post['zone_id'] < 1 || $this->request->post['zone_id'] == ''  ) {
			$this->error['zone'] = $this->language->get('error_zone');
		}
		if(!$this->request->get['posapp_id']){
		if ((utf8_strlen($this->request->files['tradelicenceimage']['name']) < 3)) {
			$this->error['tradelicenceimage'] = $this->language->get('error_tradelicenceimage');
		}
		if ((utf8_strlen($this->request->files['frontimage']['name']) < 3)) {
			$this->error['frontimage'] = $this->language->get('error_frontimage');
		}
	   }
		$current_date = date('Y-m-d');
		$postdate = $this->request->post['licence_validaty'];


		if ($postdate < $current_date) {
			$this->error['licence_validaty'] = $this->language->get('error_licence_validaty');
		}

		if ((!preg_match ("/^[0-9]*$/", $this->request->post['cost'])) || ($this->request->post['cost'] > 30)) {
			$this->error['cost'] = $this->language->get('error_cost');
		}
		if ((!preg_match ("/^[0-9]*$/", $this->request->post['alloweddistance'])) || ($this->request->post['alloweddistance'] > 10) || ($this->request->post['alloweddistance'] < 1)) {
			$this->error['alloweddistance'] = $this->language->get('error_alloweddistance');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		

		if ( isset($this->request->files['tradelicenceimage']) && !empty($this->request->files['tradelicenceimage']['name'])) {
			$allowed = array('image/jpg','image/jpeg','image/png');
			if (!in_array($this->request->files['tradelicenceimage']['type'], $allowed)) {
				$this->error['extension'] = $this->language->get('error_extension');
			}
			if ($this->request->files['tradelicenceimage']['size']> 2097152) {
				$this->error['size'] = $this->language->get('error_size');
			}
		}
		if ( isset($this->request->files['frontimage']) && !empty($this->request->files['frontimage']['name'])) {
			$allowed = array('image/jpg','image/jpeg','image/png');
			if (!in_array($this->request->files['frontimage']['type'], $allowed)) {
				$this->error['extension'] = $this->language->get('error_extension');
			}
			if ($this->request->files['frontimage']['size']> 2097152) {
				$this->error['size'] = $this->language->get('error_size');
			}
		}

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$posemail_info = $this->model_pos_posapp->getTotalPossappsByEmail($this->request->post['email']);

		if (!isset($this->request->get['posapp_id'])) {
			if ($posemail_info) {
				$this->error['warning'] = $this->language->get('error_exists_email');
			}
		} else {
			if ($posemail_info && ($this->request->get['posapp_id'] != $posemail_info['posapp_id'])) {
				$this->error['warning'] = $this->language->get('error_exists_email');
			}
		}

		if ($this->request->post['password'] || (!isset($this->request->get['posapp_id']))) {
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'pos/posapp')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}		

		return !$this->error;
	}
}