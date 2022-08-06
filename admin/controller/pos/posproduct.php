<?php
class ControllerPosPosProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('pos/posproduct');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pos/posproduct');
		
		
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

	
		$data['add'] = $this->load->controller('pos/posproduct/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['customers'] = array();

		$filter_data = array(
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		

		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_reqshipping'] = $this->language->get('entry_reqshipping');
		
		
		$data['button_addproduct'] = $this->language->get('button_addproduct');
		$data['button_add'] = $this->language->get('button_add');
		
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

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

		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

	
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['user_token'] = $this->session->data['user_token'];

		

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'href' => $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);


		$data['cancel'] = $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} else {
			$data['model'] = '';
		}
		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} else {
			$data['price'] = '';
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} else {
			$data['quantity'] = '';
		}
		

		if (isset($this->request->post['rshipping'])) {
			$data['rshipping'] = $this->request->post['rshipping'];
		} else {
			$data['rshipping'] = 0;
		}

	/* 24 09 2019 */
		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} else {
			$data['tax_class_id'] = '';
		}

		$data['entry_taxclass'] = $this->language->get('entry_taxclass');
		$data['text_select'] 	= $this->language->get('text_select');

		$this->load->model('localisation/tax_class');
    	$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
	/* 24 09 2019 */

		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pos/posproduct', $data));
	

	}
	
	public function addPosproduct() {			
		$json=array();
		
		if($this->request->post){
		$this->load->model('possetting/product');			
		if(empty($this->request->post['name']))
		{
			$json['error']='Name must be enter';
		} elseif(empty($this->request->post['model'])){
			$json['error']='Model must be enter';
			
		} elseif(empty($this->request->post['price'])){
			$json['error']='Price must be enter';
			
		} elseif(empty($this->request->post['quantity'])){
			$json['error']='Quantity must be enter';
			
		} else {			
			$json['cproduct_id']=$this->model_possetting_product->addProduct($this->request->post);
			$json['quantity']=$this->request->post['quantity'];
			$json['success']='Success!';
		}
		} 
		$this->response->setOutput(json_encode($json));
	}
}
