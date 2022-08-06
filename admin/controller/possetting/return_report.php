<?php

class ControllerPossettingReturnReport extends Controller {

	private $error = array();

	public function index() {

		$this->load->language('possetting/return_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/return_report');

		$this->getList();
	}

	public function delete() {
		$this->load->language('possetting/return_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/return_report');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $return_id) {
				$this->model_possetting_return_report->deleteReturn($return_id);
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

			$this->response->redirect($this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}



	public function getList() {

		$this->load->language('possetting/return_report');

		if (isset($this->request->get['filter_product'])) {

			$filter_product = $this->request->get['filter_product'];

		} else {

			$filter_product = '';

		}


		if (isset($this->request->get['filter_product_id_form'])) {

			$filter_product_id_form = $this->request->get['filter_product_id_form'];

		} else {

			$filter_product_id_form = '';

		}


		if (isset($this->request->get['filter_product_id_to'])) {

			$filter_product_id_to = $this->request->get['filter_product_id_to'];

		} else {

			$filter_product_id_to = '';

		}

		if (isset($this->request->get['filter_price_form'])) {

			$filter_price_form = $this->request->get['filter_price_form'];

		} else {

			$filter_price_form = '';

		}

		if (isset($this->request->get['filter_model'])) {

			$filter_model = $this->request->get['filter_model'];

		} else {

			$filter_model = '';

		}


		if (isset($this->request->get['filter_price_to'])) {

			$filter_price_to = $this->request->get['filter_price_to'];

		} else {

			$filter_price_to = '';

		}


		if (isset($this->request->get['filter_quantity_form'])) {

			$filter_quantity_form = $this->request->get['filter_quantity_form'];

		} else {

			$filter_quantity_form = '';

		}


		if (isset($this->request->get['filter_quantity_to'])) {

			$filter_quantity_to = $this->request->get['filter_quantity_to'];

		} else {

			$filter_quantity_to = '';

		}


		if (isset($this->request->get['filter_date_form'])) {

			$filter_date_form = $this->request->get['filter_date_form'];

		} else {

			$filter_date_form = '';

		}


		if (isset($this->request->get['filter_date_to'])) {

			$filter_date_to = $this->request->get['filter_date_to'];

		} else {

			$filter_date_to = '';

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

		if (isset($this->request->get['filter_product'])) {

			$url .= '&filter_product=' . $this->request->get['filter_product'];

		}


		if (isset($this->request->get['filter_product_id_form'])) {

			$url .= '&filter_product_id_form=' . $this->request->get['filter_product_id_form'];

		}


		if (isset($this->request->get['filter_product_id_to'])) {

			$url .= '&filter_product_id_to=' . $this->request->get['filter_product_id_to'];

		}



		if (isset($this->request->get['filter_price_form'])) {

			$url .= '&filter_price_form=' . $this->request->get['filter_price_form'];

		}



		if (isset($this->request->get['filter_model'])) {

			$url .= '&filter_model=' . $this->request->get['filter_model'];

		}


		if (isset($this->request->get['filter_price_to'])) {

			$url .= '&filter_price_to=' . $this->request->get['filter_price_to'];

		}


		if (isset($this->request->get['filter_quantity_form'])) {

			$url .= '&filter_quantity_form=' . $this->request->get['filter_quantity_form'];

		}


		if (isset($this->request->get['filter_quantity_to'])) {

			$url .= '&filter_quantity_to=' . $this->request->get['filter_quantity_to'];

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

			'href' => $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . $url, true)

		);

		$data['reset'] = $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '', true);
		$data['delete'] = $this->url->link('possetting/return_report/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['return_reports'] = array();



		$filter_data = array(
			'filter_product'	  		=> $filter_product,
			'filter_product_id_form'	=> $filter_product_id_form,
			'filter_product_id_to'	  	=> $filter_product_id_to,
			'filter_price_form'	  		=> $filter_price_form,
			'filter_model'	  			=> $filter_model,
			'filter_price_to'	  		=> $filter_price_to,
			'filter_quantity_form'		=> $filter_quantity_form,
			'filter_quantity_to'		=> $filter_quantity_to,
			'filter_date_form'	  		=> $filter_date_form,
			'filter_date_to'	  		=> $filter_date_to,
			'sort'  					=> $sort,
			'order' 					=> $order,
			'start' 					=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 					=> $this->config->get('config_limit_admin')

		);

		$this->load->model('tool/image');

		$result_total 	= $this->model_possetting_return_report->getTotalReport($filter_data);

		$results 			= $this->model_possetting_return_report->getReports($filter_data);
		foreach ($results as $result) {

			$this->load->model('catalog/product');
			$product_info=$this->model_catalog_product->getProduct($result['product_id']);

			if(isset($product_info['image'])){
				if (is_file(DIR_IMAGE . $product_info['image'])) {
					$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
				}else{
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
			}else{
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}			
			
			
			$data['return_reports'][] = array(
				'return_id'			=> $result['return_id'],
				'product_id'		=> $result['product_id'],
				'order_id'			=> $result['order_id'],
				'name' 				=> $result['name'],
				'model' 			=> $result['model'],
				'reason' 			=> $result['reason'],
				'image'     		=> $image,
				'quantity'  		=> $result['quantity'],
				'date_added'  		=> $result['date_added'],
				'price'  			=> $this->currency->format($result['price'], $this->config->get('config_currency')),
				'print'  			=> $this->url->link('possetting/return_report/returnPrint', 'user_token=' . $this->session->data['user_token'] .$url.'&return_id='.$result['return_id'].'&order_id='.$result['order_id'], true),
			);
		}

		$data['heading_title']       			= $this->language->get('heading_title');
		$data['text_list']           			= $this->language->get('text_list');
		$data['text_no_results'] 	 			= $this->language->get('text_no_results');
		$data['text_confirm'] 		 			= $this->language->get('text_confirm');
		$data['text_select'] 		 			= $this->language->get('text_select');

		$data['entry_product_id']      			= $this->language->get('entry_product_id');
		$data['entry_image']           			= $this->language->get('entry_image');
		$data['entry_order_id']           		= $this->language->get('entry_order_id');
		$data['entry_name']            			= $this->language->get('entry_name');
		$data['entry_price']           			= $this->language->get('entry_price');
		$data['entry_quantity']        			= $this->language->get('entry_quantity');
		$data['entry_model']   					= $this->language->get('entry_model');
		$data['entry_reason']   				= $this->language->get('entry_reason');
		$data['entry_form']             		= $this->language->get('entry_form');
		$data['entry_to']             			= $this->language->get('entry_to');
		$data['entry_date']             		= $this->language->get('entry_date');
		$data['entry_date_added']             	= $this->language->get('entry_date_added');
		$data['button_delete'] 		  			= $this->language->get('button_delete');
		$data['button_filter'] 		  			= $this->language->get('button_filter');
		$data['button_reset'] 		  			= $this->language->get('button_reset');
		$data['button_save'] 		  			= $this->language->get('button_save');
		$data['text_none'] 		  				= $this->language->get('text_none');
		$data['column_action'] 		  				= $this->language->get('column_action');
		$data['button_print'] 		  				= $this->language->get('button_print');

		$data['user_token']                			= $this->session->data['user_token'];

		

		if (isset($this->error['warning'])) {

			$data['error_warning'] = $this->error['warning'];

		} else {

			$data['error_warning'] = '';

		}



		$url = '';


		if (isset($this->request->get['filter_product'])) {

			$url .= '&filter_product=' . $this->request->get['filter_product'];

		}


		if (isset($this->request->get['filter_product_id_form'])) {

			$url .= '&filter_product_id_form=' . $this->request->get['filter_product_id_form'];

		}


		if (isset($this->request->get['filter_product_id_to'])) {

			$url .= '&filter_product_id_to=' . $this->request->get['filter_product_id_to'];

		}


		if (isset($this->request->get['filter_price_form'])) {

			$url .= '&filter_price_form=' . $this->request->get['filter_price_form'];

		}


		if (isset($this->request->get['filter_model'])) {

			$url .= '&filter_model=' . $this->request->get['filter_model'];

		}


		if (isset($this->request->get['filter_price_to'])) {

			$url .= '&filter_price_to=' . $this->request->get['filter_price_to'];

		}


		if (isset($this->request->get['filter_quantity_form'])) {

			$url .= '&filter_quantity_form=' . $this->request->get['filter_quantity_form'];

		}


		if (isset($this->request->get['filter_quantity_to'])) {

			$url .= '&filter_quantity_to=' . $this->request->get['filter_quantity_to'];

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

		

		$data['sort']  = $sort;

		$data['order'] = $order;

		$data['sort_product_id'] 	= $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '&sort=product_id' . $url, true);
		$data['sort_order_id'] 		= $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '&sort=order_id' . $url, true);
		$data['sort_name'] 			= $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_model'] 		= $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '&sort=model' . $url, true);
		$data['sort_reason'] 		= $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '&sort=reason' . $url, true);
		$data['sort_price'] 		= $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '&sort=price' . $url, true);
		$data['sort_quantity'] 		= $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '&sort=quantity' . $url, true);
		$data['sort_date_added'] 	= $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);


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


		if (isset($this->request->get['filter_product'])) {

			$url .= '&filter_product=' . $this->request->get['filter_product'];

		}


		if (isset($this->request->get['filter_product_id_form'])) {

			$url .= '&filter_product_id_form=' . $this->request->get['filter_product_id_form'];

		}


		if (isset($this->request->get['filter_product_id_to'])) {

			$url .= '&filter_product_id_to=' . $this->request->get['filter_product_id_to'];

		}


		if (isset($this->request->get['filter_price_form'])) {

			$url .= '&filter_price_form=' . $this->request->get['filter_price_form'];

		}


		if (isset($this->request->get['filter_model'])) {

			$url .= '&filter_model=' . $this->request->get['filter_model'];

		}


		if (isset($this->request->get['filter_price_to'])) {

			$url .= '&filter_price_to=' . $this->request->get['filter_price_to'];

		}


		if (isset($this->request->get['filter_quantity_form'])) {

			$url .= '&filter_quantity_form=' . $this->request->get['filter_quantity_form'];

		}


		if (isset($this->request->get['filter_quantity_to'])) {

			$url .= '&filter_quantity_to=' . $this->request->get['filter_quantity_to'];

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

		$pagination->total = $result_total;

		$pagination->page  = $page;

		$pagination->limit = $this->config->get('config_limit_admin');

		$pagination->url   = $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination']= $pagination->render();



		$data['results'] = sprintf($this->language->get('text_pagination'), ($result_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($result_total - $this->config->get('config_limit_admin'))) ? $result_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $result_total, ceil($result_total / $this->config->get('config_limit_admin')));


		$data['sort'] = $sort;

		$data['order'] = $order;
		$data['filter_product'] = $filter_product;
		$data['filter_product_id_form'] = $filter_product_id_form;
		$data['filter_product_id_to'] = $filter_product_id_to;
		$data['filter_model'] = $filter_model;
		$data['filter_price_form'] = $filter_price_form;
		$data['filter_price_to'] = $filter_price_to;
		$data['filter_quantity_form'] = $filter_quantity_form;
		$data['filter_quantity_to'] = $filter_quantity_to;
		$data['filter_date_form'] = $filter_date_form;
		$data['filter_date_to'] = $filter_date_to;



		$data['header']      = $this->load->controller('common/header');

		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/return_report', $data));

	}
	
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'possetting/return_report')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}


	public function returnPrint() {
		$this->load->model('sale/order');
		$this->load->model('possetting/return_report');
		$this->load->language('possetting/return_report');
		$this->load->model('tool/upload');

		$this->document->setTitle($this->language->get('text_return'));
		
		$data['storelogo'] ='';
		$storelogo = $this->config->get('setting_image');
		$logowidth = $this->config->get('setting_logowidth');
		$logoheight = $this->config->get('setting_logoheight');
		$this->load->model('tool/image');
		if (!empty($storelogo)) {
			$data['storelogo'] = $this->model_tool_image->resize($storelogo, $logoheight, $logowidth);
		}

		
		$data['config_name'] = $this->config->get('config_name');
		$data['config_address'] = $this->config->get('config_address');
		$data['config_telephone'] = $this->config->get('config_telephone');
		$data['config_email'] = $this->config->get('config_email');

		$this->load->model('setting/store');
		$this->load->model('setting/setting');
		if (!empty($this->session->data['store_id'])) {
			$store_info = $this->model_setting_setting->getSetting('config', $this->session->data['store_id']);
			if (!empty($store_info)) {
				$data['config_name'] 	=$store_info['config_name'];
				$data['config_address'] 	=$store_info['config_address'];
				$data['config_telephone'] 	=$store_info['config_telephone'];
				$data['config_email'] 	=$store_info['config_email'];

				if (!empty($store_info['config_image']) && is_file(DIR_IMAGE . $store_info['config_image'])) {
					$data['storelogo'] = $this->model_tool_image->resize($store_info['config_image'], $logowidth, $logoheight);
				}
			}
		}

		$data['entry_order_date'] = $this->language->get('entry_order_date');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_return_id'] = $this->language->get('text_return_id');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_email'] = $this->language->get('text_email');
		$data['entry_reason'] = $this->language->get('entry_reason');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_order_id'] = $this->language->get('entry_order_id');

		$data['text_grand_total'] = $this->language->get('text_grand_total');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		if (isset($this->request->get['return_id'])) {
			$return_id = $this->request->get['return_id'];
		} else {
			$return_id = null;
		}
		$filter_data = array(
			'filter_return_id' => $return_id,
			'filter_order_id' => $order_id,
		);
		$total = 0;
		$data['products'] = array();
		$return_infos = $this->model_possetting_return_report->getReports($filter_data);
		foreach ($return_infos as $key => $result) {
			$option_data = array();
			$options = $this->model_sale_order->getOrderOptions($result['order_id'], $result['order_product_id']);
			foreach ($options as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				);
			}
			$total += $result['quantity']*$result['price'];
			$data['products'][] = array(
				'option'    		=> $option_data,
				'product_id'		=> $result['product_id'],
				'order_id'			=> $result['order_id'],
				'name' 				=> $result['name'],
				'model' 			=> $result['model'],
				'reason' 			=> $result['reason'],
				'quantity'  		=> $result['quantity'],
				'date_added'  		=> $result['date_added'],
				'price' 			=> $this->currency->format($result['price'], $this->config->get('config_currency')),
				'total' 			=> $this->currency->format($result['quantity']*$result['price'], $this->config->get('config_currency')),
			);
		}
		$data['grand_total'] = $this->currency->format($total, $this->config->get('config_currency'));
		$customer_id 				= !empty($order_id)?$order_id:'0';
		$order_info 				= $this->model_sale_order->getOrder($order_id);
		$data['customername']     	= !empty($order_info['firstname'])?$order_info['firstname'].' '.$order_info['lastname']:'NA';
		$data['order_id'] 			= !empty($order_id)?$order_id:'0';
		$data['invoice_no'] 		= !empty($order_info['invoice_no'])?$order_info['invoice_no']:'';
		$data['date_added'] 		= !empty($order_info['date_added'])? date('Y-m-d',strtotime($order_info['date_added'])):'NA';
		if(!empty($this->config->get('setting_format'))) {
			$this->response->setOutput($this->load->view('possetting/returnprintinvoice2', $data));
		} else {
			$this->response->setOutput($this->load->view('possetting/returnprintinvoice', $data));
		}
	}


}