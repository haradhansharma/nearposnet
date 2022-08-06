<?php
class ControllerPossettingCost extends Controller {
 	private $error = array();
	public function index() {
		////sharma for pos
		if($this->cart->getCommissionPending() > $this->config->get('module_posapp_payment_limit')){
			$this->load->language('extension/module/posapp');
			$this->session->data['success'] = sprintf($this->language->get('text_limit_over'), '<b>Limit is: ' . $this->currency->format($this->config->get('module_posapp_payment_limit'), $this->config->get('config_currency')).'</b>', 'But current due is: '.'<b>'.$this->currency->format($this->cart->getCommissionPending(), $this->config->get('config_currency')) . '</b>' ) ;
			$this->response->redirect($this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'], true));
		}
		$this->load->language('possetting/cost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/cost');

		$this->getList();
	}

	public function editqty() {
		$this->load->language('possetting/cost');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/cost');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_possetting_cost->updateOpionQTY($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
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
			

			$this->response->redirect($this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

 	public function getList() {
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

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'product_id';
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
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
			'href' => $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'], true)
		);


		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_quantity' => $filter_quantity,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');
		$this->load->model('catalog/option');

		$product_total = $this->model_possetting_cost->getTotalProducts($filter_data);
		$results=$this->model_possetting_cost->getProducts($filter_data);

		foreach($results as $result) 
		{
			if (is_file(DIR_IMAGE . $result['image'])) 
			{
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} 
			else 
			{
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$option_data = array();

			$product_options = $this->model_possetting_cost->getProductOptions($result['product_id']);

			if(!empty($product_options)) 
			{

				foreach ($product_options as $product_option) 
				{
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) 
					{

						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) 
						{

							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
							
							$cost_info1 = $this->model_possetting_cost->getProductCost(false,$product_option_value['product_option_value_id']);

							if ($option_value_info) 
							{

								$product_option_value_data[] = array(
									'cost'                    => !empty($cost_info1['cost'])?$cost_info1['cost']:'',
									'name'                    => $option_value_info['name'],
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $option_value_info['option_value_id'],
									'quantity'                => $product_option_value['quantity'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix'],
								);
							}
						}
					}
					$option_data[] = array(
						'product_option_value' => $product_option_value_data,
						'name'                 => $option_info['name'],
						'product_id'           => $product_option['product_id'],
						'type'                 => $option_info['type']
					);

				}
			}
			$cost_info = $this->model_possetting_cost->getProductCost($result['product_id'],false);

			$data['products'][]=array(
				'cost' 			=>!empty($cost_info['cost'])?$cost_info['cost']:'',
				'product_id' 	=>$result['product_id'],
				'image'      	=> $image,
				'name'       	=>$result['name'],
				'upc'        	=>$result['upc'],
				'model'      	=>$result['model'],
				'quantity'   	=>$result['quantity'],
				'option_data' 	=>$option_data,
			);
		}
		//echo "<pre>";print_r($data['products']);die();
		$data['column_cost']       = $this->language->get('column_cost');
		$data['heading_title']       = $this->language->get('heading_title');
		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_nooption'] 		 = $this->language->get('text_nooption');
		$data['column_id'] 		 	 = $this->language->get('column_id');
		$data['column_image'] 		 = $this->language->get('column_image');
		$data['column_upc'] 		 = $this->language->get('column_upc');
		$data['column_product_name'] = $this->language->get('column_product_name');
		$data['column_model'] 		 = $this->language->get('column_model');
		$data['column_cost'] 	 = $this->language->get('column_cost');
		$data['column_product_option']= $this->language->get('column_product_option');
		$data['column_quantity'] 	 = $this->language->get('column_quantity');
		$data['column_images'] 		 = $this->language->get('column_images');
		$data['column_action'] 		 = $this->language->get('column_action');
		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_save'] 		 = $this->language->get('button_save');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['button_reset'] 		 = $this->language->get('button_reset');
		$data['user_token']               = $this->session->data['user_token'];

		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}
		$url = '';

			 if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
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
		$data['action'] = $this->url->link('possetting/cost/editqty', 'user_token=' . $this->session->data['user_token'].$url, true);
		$data['reset'] = $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'], true);
		
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_product_name'] = $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'] . '&sort=product_name' . $url, true);
		$data['sort_model']		   = $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'] . '&sort=model' . $url, true);
		$data['sort_cost']  	   = $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'] . '&sort=cost' . $url, true);
		$data['sort_image']   	   = $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'] . '&sort=image' . $url, true);
		$data['sort_quantity']     = $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'] . '&sort=quantity' . $url, true);
		$data['sort_product_option']= $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'] . '&sort=product_option' . $url, true);

		if (isset($this->session->data['success'])) {
		 $data['success'] = $this->session->data['success'];
		unset($this->session->data['success']);
		} else {
		$data['success'] = '';
		}

		$data['sort']  = $sort;
		$data['order'] = $order;
		$data['packages']=array();

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['sort'])) {
		$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
		$url .= '&order=' . $this->request->get['order'];
		}

		$pagination 	   = new Pagination();
		$pagination->total = $product_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/cost', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_quantity'] = $filter_quantity;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/cost', $data));
	}

}
