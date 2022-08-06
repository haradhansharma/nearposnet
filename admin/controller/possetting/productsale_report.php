<?php
class ControllerPossettingProductsalereport extends Controller {
 	private $error = array();
	public function index() {
			
		$this->load->language('possetting/productsale_report');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/productsale_report');
		$this->load->model('catalog/product');
		
		$this->getList();
	}

 	public function getList() {
 		
	 	if (isset($this->request->get['filter_productid'])) {
			$filter_productid = $this->request->get['filter_productid'];
		} else {
			$filter_productid = null;
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_date_form'])) {
			$filter_date_form = $this->request->get['filter_date_form'];
		} else {
			$filter_date_form = null;
		}

		if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
		} else {
			$filter_date_to = null;
		}
	 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'order_product_id';
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
	 
	 	if (isset($this->request->get['filter_productid'])) {
			$url .= '&filter_productid=' . $this->request->get['filter_productid'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . $this->request->get['filter_model'];
		}

		if (isset($this->request->get['filter_date_form'])) {
			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
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
		 $data['print'] = $this->url->link('possetting/productsale_report/productsalereport', 'user_token=' . $this->session->data['user_token']. $url, true);
						  
	
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
			'href' => $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['productsells'] = array();

		$filter_data = array(
			'filter_productid'  => $filter_productid,
			'filter_name'   	=> $filter_name,
			'filter_model'   	=> $filter_model,
			'filter_date_form' => $filter_date_form,
			'filter_date_to' => $filter_date_to,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
	
		$productsell_total = $this->model_possetting_productsale_report->getTotalProductSales($filter_data);
		
		$productsells = $this->model_possetting_productsale_report->getProductSales($filter_data);
		
		foreach($productsells as $productsell) {
			
			$totalsale=0;
			$totalsale = $this->model_possetting_productsale_report->getTotalSales($filter_data,$productsell['product_id']);
			
			$data['productsells'][]=array(
				'product_id' 	=> $productsell['product_id'],
				'name' 			=> $productsell['name'],
				'model' 		=> $productsell['model'],
				'price' 		=> $productsell['price'],
				'date_added' 	=> $productsell['date_added'],
				'totalsale' 	=> $totalsale,
			);
		}

		$data['heading_title']       = $this->language->get('heading_title');
		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_invoice'] 		 = $this->language->get('text_invoice');

		$data['column_productid']	 = $this->language->get('column_productid');
		$data['column_name']	 	 = $this->language->get('column_name');
		$data['column_model']	     = $this->language->get('column_model');
		$data['column_totalsell']	 = $this->language->get('column_totalsell');
		$data['column_totalamount']	 = $this->language->get('column_totalamount');
		$data['column_date']	 	 = $this->language->get('column_date');
		$data['column_action']	 	 = $this->language->get('column_action');

		$data['entry_productid']	 = $this->language->get('entry_productid');
		$data['entry_name']	 	 	 = $this->language->get('entry_name');
		$data['entry_model']	 	 = $this->language->get('entry_model');
		$data['entry_date']	 	 	 = $this->language->get('entry_date');

		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['user_token']               = $this->session->data['user_token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_productid'])) {
			$url .= '&filter_productid=' . $this->request->get['filter_productid'];
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . $this->request->get['filter_model'];
		}

		if (isset($this->request->get['filter_date_form'])) {
			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}


		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_productid'] 	= $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=productid' . $url, true);
		$data['sort_name'] 			= $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_model']		   	= $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=model' . $url, true);
		$data['sort_totalsell']		= $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=totalsale' . $url, true);
		$data['sort_totalamount']	= $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=totalamount' . $url, true);
		$data['sort_date_added']	= $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		
		$url='';
		
		if (isset($this->request->get['filter_productid'])) {
			$url .= '&filter_productid=' . $this->request->get['filter_productid'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . $this->request->get['filter_model'];
		}

		if (isset($this->request->get['filter_date_form'])) {
			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
				   
        
		$pagination 	   = new Pagination();
		$pagination->total = $productsell_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($productsell_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($productsell_total - $this->config->get('config_limit_admin'))) ? $productsell_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $productsell_total, ceil($productsell_total / $this->config->get('config_limit_admin')));

		
		$data['filter_productid'] 	= $filter_productid;
		$data['filter_name'] 		= $filter_name;
		$data['filter_model'] 		= $filter_model;
		$data['filter_date_form'] 	= $filter_date_form;
		$data['filter_date_to'] 	= $filter_date_to;
		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/productsale_report', $data));
	}

	public function productsalereport() {
		$this->load->language('possetting/productsale_report');

		$data['title'] = $this->language->get('text_invoice');
		
		$data['heading_title']       = $this->language->get('heading_title');
		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_invoice'] 		 = $this->language->get('text_invoice');

		$data['text_invoice'] 		 = $this->language->get('text_invoice');
		$data['column_productid']	 = $this->language->get('column_productid');
		$data['column_name']	 	 = $this->language->get('column_name');
		$data['column_model']	     = $this->language->get('column_model');
		$data['column_totalsell']	 = $this->language->get('column_totalsell');
		$data['column_totalamount']	 = $this->language->get('column_totalamount');
		$data['column_date']	 	 = $this->language->get('column_date');

		$this->load->model('possetting/productsale_report');
		$data['productsells'] = array();
		if (isset($this->request->get['filter_productid'])) {
			$filter_productid = $this->request->get['filter_productid'];
		} else {
			$filter_productid = null;
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_date_form'])) {
			$filter_date_form = $this->request->get['filter_date_form'];
		} else {
			$filter_date_form = null;
		}

		if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
		} else {
			$filter_date_to = null;
		}
	 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'order_product_id';
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
					
		$data['productsells'] = array();

		$filter_data = array(
			'filter_productid'  => $filter_productid,
			'filter_name'   	=> $filter_name,
			'filter_model'   	=> $filter_model,
			'filter_date_form' 	=> $filter_date_form,
			'filter_date_to' 	=> $filter_date_to,
			'sort'  => $sort,
			'order' => $order,
			'start' => '0',
			'limit' => '999999999999999999'
		);

		$productsells = $this->model_possetting_productsale_report->getProductSales($filter_data);
		
		foreach($productsells as $productsell) {
			$totalsale = $this->model_possetting_productsale_report->getTotalSales($filter_data,$productsell['product_id']);
			
			$data['productsells'][]=array(
				'product_id' 	=> $productsell['product_id'],
				'name' 			=> $productsell['name'],
				'model' 		=> $productsell['model'],
				'price' 		=> $productsell['price'],
				'date_added' 	=> $productsell['date_added'],
				'totalsale' 	=> $totalsale
			);
		}

		
		$this->response->setOutput($this->load->view('possetting/productsale_print', $data));
	}
 
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'possetting/productsale_report')) {
		 $this->error['warning'] = $this->language->get('error_permission');
		}
		 return !$this->error;
	}


}