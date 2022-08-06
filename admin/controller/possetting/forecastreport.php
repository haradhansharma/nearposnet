<?php
class ControllerPossettingForecastReport extends Controller {
 	private $error = array();
	public function index() {
			
		$this->load->language('possetting/forecastreport');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/forecastreport');
		
		$this->getList();
	}

 	public function getList() {
		$this->load->language('possetting/forecastreport');
 		
	 	if (isset($this->request->get['filter_from'])) {
			$filter_from = $this->request->get['filter_from'];
		} else {
			$filter_from = '';
		}

		if (isset($this->request->get['filter_to'])) {
			$filter_to = $this->request->get['filter_to'];

		} else {
			$filter_to = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'order_id';
		}
	 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
		 	$order = 'DESC';
		}
	 
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';

		if (isset($this->request->get['filter_from'])) {
			$url .= '&filter_from=' . $this->request->get['filter_from'];
		}

		if (isset($this->request->get['filter_to'])) {
			$url .= '&filter_to=' . $this->request->get['filter_to'];
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
			'href' => $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
	
		$data['forecastreports'] = array();

		$filter_data = array(
			'filter_from'   => $filter_from,
			'filter_to'   => $filter_to,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		

		$commission_total = $this->model_possetting_forecastreport->getTotalReport($filter_data);
		
		$results = $this->model_possetting_forecastreport->getReports($filter_data);
		
		$this->load->model('possetting/forecastreport');
		$this->load->model('catalog/product');
		$this->load->model('pos/posproduct');
		$this->load->model('tool/image');

		foreach ($results as $result) {
		/* Product Data information */	
			$proinfo = $this->model_catalog_product->getProduct($result['product_id']);
			if(isset($proinfo['image'])) {
			if(isset($proinfo['quantity'])){
				$quantity = $proinfo['quantity'];
			} else{
				$quantity = '';
			}

			if(!empty($result['product_id'])){
				if (is_file(DIR_IMAGE . $proinfo['image'])) {
					$image = $this->model_tool_image->resize($proinfo['image'], 40, 40);
				} else {
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$salestock = $this->model_possetting_forecastreport->getproductqty($result['product_id']);

			if($salestock > $quantity){
				$futurestk = $salestock - $quantity;
			} else {
				$futurestk = 0;
			}
		/* Product Data information */	
			
		/* POS Product Data information */	
			$posproinfo = $this->model_pos_posproduct->getProduct($result['cproduct_id']);
			if(isset($posproinfo['quantity'])){
				$posproquantity = $posproinfo['quantity'];
			} else{
				$posproquantity = '';
			}

			if(isset($posproinfo['image'])){
				if (is_file(DIR_IMAGE . $posproinfo['image'])) {
					$stockimage = $this->model_tool_image->resize($posproinfo['image'], 40, 40);
				} else {
					$stockimage = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
			} else {
				$stockimage = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
		
			$possalestock = $this->model_possetting_forecastreport->getposproductqty($result['cproduct_id']);

			if($possalestock > $posproquantity){
				$posfuturestk = (int)$possalestock - (int)$posproquantity;
			} else {
				$posfuturestk = 0;
			}	

		/* POS Product Data information */
			
				$this->load->model('sale/order');
				$orderinfo = $this->model_sale_order->getOrder($result['order_id']);
				$orderstatus = $orderinfo['order_status_id'];

					$data['forecastreports'][] = array(
						'order_id'	=> $result['order_id'],
						'product_id'	=> $result['product_id'],
						'cproduct_id'	=> $result['cproduct_id'],
						'proname'	     => $result['name'],
						'image'	     => $image,
						'posimage'	=> $stockimage,
						'quantity'	=> $quantity,
						'proquantity'	=> $futurestk,
						'posproquantity'=> $posfuturestk,
						'posquantity'  => $posproquantity,
						'salestock'  => $salestock,
						'orderstatus'  => $orderstatus,
					);
				
			}
		}	
		
		$data['heading_title']       = $this->language->get('heading_title');

		$data['text_list']             = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_none'] 		 	 = $this->language->get('text_none');
		$data['text_print'] 		 = $this->language->get('text_print');
		$data['text_day'] 		      = $this->language->get('text_day');
		$data['text_month'] 		 = $this->language->get('text_month');
		$data['text_year'] 		      = $this->language->get('text_year');
		$data['text_from'] 		      = $this->language->get('text_from');
		$data['text_to'] 		      = $this->language->get('text_to');
		$data['text_instock'] 		 = $this->language->get('text_instock');

		$data['column_image']	      = $this->language->get('column_image');
		$data['column_product']	      = $this->language->get('column_product');
		$data['column_salestock']	 = $this->language->get('column_salestock');
		$data['column_currentstock']	 = $this->language->get('column_currentstock');
		$data['column_futurestock']	 = $this->language->get('column_futurestock');
		$data['column_date']	      = $this->language->get('column_date');

		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['button_save'] 		 = $this->language->get('button_save');
		$data['user_token']                 = $this->session->data['user_token'];
		
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

		if (isset($this->request->get['filter_from'])) {
			$url .= '&filter_from=' . $this->request->get['filter_from'];
		}

		if (isset($this->request->get['filter_to'])) {
			$url .= '&filter_to=' . $this->request->get['filter_to'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_product'] 	  = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . '&sort=product' . $url, true);
		$data['sort_currentstock'] = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . '&sort=currentstock' . $url, true);
		$data['sort_salestock'] 	  = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . '&sort=salestock' . $url, true);
		$data['sort_futurestock']  = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . '&sort=futurestock' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_from'])) {
			$url .= '&filter_from=' . $this->request->get['filter_from'];
		}

		if (isset($this->request->get['filter_to'])) {
			$url .= '&filter_to=' . $this->request->get['filter_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination 	   = new Pagination();
		$pagination->total = $commission_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($commission_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($commission_total - $this->config->get('config_limit_admin'))) ? $commission_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $commission_total, ceil($commission_total / $this->config->get('config_limit_admin')));

		$data['filter_from'] = $filter_from;
		$data['filter_to'] = $filter_to;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['forecastprint'] = $this->url->link('possetting/forecastreport/forecastprint', 'user_token=' . $this->session->data['user_token'].$url, true);

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/forecastreport', $data));
	}

	public function forecastprint() {
		$this->load->language('possetting/forecastreport');

		$this->load->model('possetting/forecastreport');
 		
	 	if (isset($this->request->get['filter_from'])) {
			$filter_from = $this->request->get['filter_from'];
		} else {
			$filter_from = '';
		}

		if (isset($this->request->get['filter_to'])) {
			$filter_to = $this->request->get['filter_to'];

		} else {
			$filter_to = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'order_id';
		}
	 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
		 	$order = 'DESC';
		}
	 
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';

		if (isset($this->request->get['filter_from'])) {
			$url .= '&filter_from=' . $this->request->get['filter_from'];
		}

		if (isset($this->request->get['filter_to'])) {
			$url .= '&filter_to=' . $this->request->get['filter_to'];
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
			'href' => $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
	
		$data['forecastreports'] = array();

		$filter_data = array(
			'filter_from'   => $filter_from,
			'filter_to'   => $filter_to,
			'sort'  => $sort,
			'order' => $order,
			'start' => '0',
			'limit' => '999999999999999999'
		);
		
		$commission_total = $this->model_possetting_forecastreport->getTotalReport($filter_data);
		
		$results = $this->model_possetting_forecastreport->getReports($filter_data);
		
		$this->load->model('possetting/forecastreport');
		$this->load->model('catalog/product');
		$this->load->model('pos/posproduct');
		$this->load->model('tool/image');

		foreach ($results as $result) {
		/* Product Data information */	
			$proinfo = $this->model_catalog_product->getProduct($result['product_id']);
			
			if(isset($proinfo['quantity'])){
				$quantity = $proinfo['quantity'];
			} else{
				$quantity = '';
			}

			if(!empty($result['product_id'])){
				if (is_file(DIR_IMAGE . $proinfo['image'])) {
					$image = $this->model_tool_image->resize($proinfo['image'], 40, 40);
				} else {
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$salestock = $this->model_possetting_forecastreport->getproductqty($result['product_id']);

			if($salestock > $quantity){
				$futurestk = $salestock - $quantity;
			} else {
				$futurestk = 0;
			}
		/* Product Data information */	
			
		/* POS Product Data information */	
			$posproinfo = $this->model_pos_posproduct->getProduct($result['cproduct_id']);
			if(isset($posproinfo['quantity'])){
				$posproquantity = $posproinfo['quantity'];
			} else{
				$posproquantity = '';
			}

			if(!empty($result['cproduct_id'])){
				if (is_file(DIR_IMAGE . $posproinfo['image'])) {
					$posimage = $this->model_tool_image->resize($posproinfo['image'], 40, 40);
				} else {
					$posimage = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
			} else {
				$posimage = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
		
			$possalestock = $this->model_possetting_forecastreport->getposproductqty($result['cproduct_id']);

			if($possalestock > $posproquantity){
				$posfuturestk = $possalestock - $posproquantity;
			} else {
				$posfuturestk = 0;
			}	

		/* POS Product Data information */
			
				$this->load->model('sale/order');
				$orderinfo = $this->model_sale_order->getOrder($result['order_id']);
				$orderstatus = $orderinfo['order_status_id'];

					$data['forecastreports'][] = array(
						'order_id'	=> $result['order_id'],
						'product_id'	=> $result['product_id'],
						'cproduct_id'	=> $result['cproduct_id'],
						'proname'	     => $result['name'],
						'image'	     => $image,
						'posimage'	=> $posimage,
						'quantity'	=> $quantity,
						'proquantity'	=> $futurestk,
						'posproquantity'=> $posfuturestk,
						'posquantity'  => $posproquantity,
						'salestock'  => $salestock,
						'orderstatus'  => $orderstatus,
					);
				
			}	
		
		$data['heading_title']       = $this->language->get('heading_title');

		$data['text_list']             = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_none'] 		 	 = $this->language->get('text_none');
		$data['text_print'] 		 = $this->language->get('text_print');
		$data['text_day'] 		      = $this->language->get('text_day');
		$data['text_month'] 		 = $this->language->get('text_month');
		$data['text_year'] 		      = $this->language->get('text_year');
		$data['text_from'] 		      = $this->language->get('text_from');
		$data['text_to'] 		      = $this->language->get('text_to');
		$data['text_instock'] 		 = $this->language->get('text_instock');

		$data['column_image']	      = $this->language->get('column_image');
		$data['column_product']	      = $this->language->get('column_product');
		$data['column_salestock']	 = $this->language->get('column_salestock');
		$data['column_currentstock']	 = $this->language->get('column_currentstock');
		$data['column_futurestock']	 = $this->language->get('column_futurestock');
		$data['column_date']	      = $this->language->get('column_date');

		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['button_save'] 		 = $this->language->get('button_save');
		$data['user_token']                 = $this->session->data['user_token'];
		
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

		if (isset($this->request->get['filter_from'])) {
			$url .= '&filter_from=' . $this->request->get['filter_from'];
		}

		if (isset($this->request->get['filter_to'])) {
			$url .= '&filter_to=' . $this->request->get['filter_to'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_product'] = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . '&sort=product' . $url, true);
		$data['sort_currentstock'] = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . '&sort=currentstock' . $url, true);
		$data['sort_salestock'] = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . '&sort=salestock' . $url, true);
		$data['sort_futurestock'] = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . '&sort=futurestock' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_from'])) {
			$url .= '&filter_from=' . $this->request->get['filter_from'];
		}

		if (isset($this->request->get['filter_to'])) {
			$url .= '&filter_to=' . $this->request->get['filter_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination 	   = new Pagination();
		$pagination->total = $commission_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($commission_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($commission_total - $this->config->get('config_limit_admin'))) ? $commission_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $commission_total, ceil($commission_total / $this->config->get('config_limit_admin')));

		$data['filter_from'] = $filter_from;
		$data['filter_to'] = $filter_to;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/forecastreport_print', $data));
	}

}