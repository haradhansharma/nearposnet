<?php
class ControllerPosHoldonReport extends Controller {
	private $error = array();

	public function index() {
		
		$this->load->language('pos/holdon');
		$this->load->model('pos/holdon');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pos/order');

		if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
		} else {
			$filter_date_to = false;
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['export'] = $this->url->link('sale/order/export', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['add'] = $this->url->link('sale/order/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['holdreports'] = array();

		$filter_data = array(
			'filter_date_to' 	   => $filter_date_to,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);
		$this->load->model('customer/customer');

		$order_total = $this->model_pos_holdon->getTotalHoldOn($filter_data);

		$results = $this->model_pos_holdon->getholdoreport($filter_data);
		foreach ($results as $result) {
			$customer_info = $this->model_customer_customer->getCustomer($result['customer_id']);
			$data['holdreports'][] = array(
				'holdon_id'     	=> $result['holdon_id'],
				'customername'     	=> !empty($customer_info['firstname'])?$customer_info['firstname'].' '.$customer_info['lastname']:'NA',
				'holdon_no'     	=> $result['holdon_no'],
				'date_added'    	=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'delete'            => $this->url->link('pos/pos/delete', 'user_token=' . $this->session->data['user_token'] . '&holdon_id=' . $result['holdon_id'] . $url, true),
				'print'            	=> $this->url->link('pos/holdonreport/holdonPrint', 'user_token=' . $this->session->data['user_token'] . '&holdon_id=' . $result['holdon_id'] . $url, true),
			);

		}
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_missing'] = $this->language->get('text_missing');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_select'] = $this->language->get('text_select');

		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_holdon'] = $this->language->get('column_holdon');
		$data['column_proname'] = $this->language->get('column_proname');
		$data['column_prooption'] = $this->language->get('column_prooption');
		$data['column_dateadded'] = $this->language->get('column_dateadded');
		$data['column_action'] = $this->language->get('column_action');
		
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

		$data['sort_order'] = $this->url->link('pos/holdonreport', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('pos/holdonreport', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_status'] = $this->url->link('pos/holdonreport', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total'] = $this->url->link('pos/holdonreport', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('pos/holdonreport', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('pos/holdonreport', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('pos/holdonreport', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		if (!empty($this->request->get['filter_data'])) {
			$data['tmdurl'] = true;
		}else{
			$data['tmdurl'] = false;
		}

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pos/holdonreport', $data));
	}

	public function holdaddCart() {
		$this->load->language('pos/holdonreport');

		$json = array();

		if (isset($this->request->get['holdon_id'])) {
			$holdon_id = (int)$this->request->get['holdon_id'];
		} else {
			$holdon_id = 0;
		}

		$this->load->model('pos/holdon');

		$h1product_infos = $this->model_pos_holdon->getholdProductId($holdon_id);
		if(isset($h1product_infos['hold_option'])) {
			$products = unserialize($h1product_infos['hold_option']);
		} else {
			$products =array();
		}

		foreach($products as $product) {
			
			if (!empty($product['product_id'])) {
				$product_id = $product['product_id'];
				// Options
				if (!empty($product['option'])) {
					$options = $product['option'];
				} else {
					$options = array();
				}

				///echo "<pre>";print_r($options);die();
				
				$option=array();
				foreach($options as $product_option_id => $value) {
					$option[$product_option_id]=$value;
				}
				$recurring_id=0;
				$h1 = $this->pos->add($product_id, $product['quantity'], $option, $recurring_id);
				
				if($this->session->data['cart']) {
					$h1product_infos = $this->model_pos_holdon->deleteHoldOn($holdon_id);
				}
			} else {
				if (isset($product['cproduct_id'])) {
					$cproduct_id = (int)$product['cproduct_id'];
				} else {
					$cproduct_id = 0;
				}

				if (isset($product['quantity'])) {
					$quantity = $product['quantity'];
				} else {
					$quantity = 0;
				}
				$option=array();
				
				$recurring_id=0;
				$this->pos->add(0,$quantity, '','',$cproduct_id);
				$json['success'] ='success';
				$json['total'] = '';
				
				/*if($this->session->data['cart']) {
					$h1product_infos = $this->model_pos_holdon->deleteHoldOn($holdon_id);
				}*/
			}

		}
		$json['success'] ='success';
		$json['total'] = '';
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function holdonPrint() {
		$this->load->model('customer/customer');
		$this->load->model('pos/holdon');
		$this->load->language('pos/holdon');
		$this->load->model('tool/upload');

		$this->document->setTitle($this->language->get('text_holdon'));
		
		$data['storelogo'] ='';
		$storelogo = $this->config->get('setting_image');
		$logowidth = $this->config->get('setting_logowidth');
		$logoheight = $this->config->get('setting_logoheight');
		$this->load->model('tool/image');
		if (!empty($storelogo)) {
			$data['storelogo'] = $this->model_tool_image->resize($storelogo, $logowidth, $logoheight);
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

		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_holdon'] = $this->language->get('text_holdon');
		$data['text_holdon_id'] = $this->language->get('text_holdon_id');
		$data['column_proname'] = $this->language->get('column_proname');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');

		if (isset($this->request->get['holdon_id'])) {
			$holdon_id = $this->request->get['holdon_id'];
		} else {
			$holdon_id = 0;
		}
		$data['products'] = array();
		$holdon_info = $this->model_pos_holdon->getholdProductId($holdon_id);
		$products = !empty($holdon_info['hold_option'])?unserialize($holdon_info['hold_option']):array();
		foreach ($products as $key => $result) {
			$option_data = array();
			foreach ($result['option'] as $option) {
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
			$data['products'][] = array(
				'product_id' 	=> $result['product_id'],
				'name' 			=> $result['name'],
				'option'    	=> $option_data,
				'model' 		=> $result['model'],
				'price' 		=> $this->currency->format($result['price'], $this->config->get('config_currency')),
				'total' 		=> $this->currency->format($result['total'], $this->config->get('config_currency')),
				'quantity' 		=> $result['quantity'],
			);
		}
		$customer_id = !empty($holdon_info['customer_id'])?$holdon_info['customer_id']:'0';
		$customer_info = $this->model_customer_customer->getCustomer($customer_id);
		$data['customername']     	= !empty($customer_info['firstname'])?$customer_info['firstname'].' '.$customer_info['lastname']:'NA';
		$data['holdon_id'] 			= !empty($holdon_info['holdon_id'])?$holdon_info['holdon_id']:'0';
		$data['holdon_no'] 			= !empty($holdon_info['holdon_no'])?$holdon_info['holdon_no']:'';
		$data['date_added'] 		= !empty($holdon_info['date_added'])? date('Y-m-d',strtotime($holdon_info['date_added'])):'';
		if(!empty($this->config->get('setting_format'))) {
			$this->response->setOutput($this->load->view('pos/holdonprintinvoice2', $data));
		} else {
			$this->response->setOutput($this->load->view('pos/holdonprintinvoice', $data));
		}
	}

}
