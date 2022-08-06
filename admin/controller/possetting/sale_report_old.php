<?php
class ControllerPossettingSaleReport extends Controller {
 	private $error = array();
	public function index() {
			
		$this->load->language('possetting/sale_report');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/sale_report');
		$this->load->model('possetting/commission_report');
		
		$this->getList();
	}

 	public function getList() {
		$this->load->language('possetting/sale_report');

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$filter_payment_method = $this->request->get['filter_payment_method'];
		} else {
			$filter_payment_method = null;
		}

		if (isset($this->request->get['filter_date_form'])) {
			$filter_date_form = $this->request->get['filter_date_form'];
		} else {
			$filter_date_form = date('Y-m-d');
		}

		if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
		} else {
			$filter_date_to = date('Y-m-d');
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
			'href' => $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['sellreports'] = array();

		$filter_data = array(
			'filter_order_id'   => $filter_order_id,
			'filter_payment_method' => $filter_payment_method,
			'filter_date_form' => $filter_date_form,
			'filter_date_to' => $filter_date_to,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$commission_total = $this->model_possetting_sale_report->getTotalSellReport($filter_data);
		$results = $this->model_possetting_sale_report->getSellReports($filter_data);
				
		$grandtotale=0;
		$data['grandtotale']=0;

		foreach ($results as $result) {
			$amount_info = $this->model_possetting_commission_report->getAmount($result['order_id']);
			if(isset($amount_info['total'])){
				$amount=$amount_info['total'];         
			} else {
				$amount='';
			}	
			$grandtotale+=$amount;

			$data['sellreports'][] = array(
				'order_id'    		=> $result['order_id'],
				'payment_method'	=> $result['payment_method'],
				'total'             => $this->currency->format($result['total'], $this->config->get('config_currency'), $this->config->get('currency_value')),
				'date_added' 		=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			);
			
		}

		$data['grandtotale'] = $this->currency->format($grandtotale, $this->config->get('config_currency'), $this->config->get('currency_value'));
   
		$data['heading_title']        = $this->language->get('heading_title');

		$data['text_list']            = $this->language->get('text_list');
		$data['text_no_results'] 	  = $this->language->get('text_no_results');
		$data['text_confirm'] 		  = $this->language->get('text_confirm');
		$data['text_nooption'] 		  = $this->language->get('text_nooption');
		$data['text_select'] 		  = $this->language->get('text_select');
		$data['text_print'] 		  = $this->language->get('text_print');

		$data['column_order_id']	  = $this->language->get('column_order_id');
		$data['column_date']	      = $this->language->get('column_date');
		$data['column_payment_method']= $this->language->get('column_payment_method');
		$data['column_total_amount']  = $this->language->get('column_total_amount');
		$data['column_total']	      = $this->language->get('column_total');
		$data['column_form']	      = $this->language->get('column_form');
		$data['column_to']	          = $this->language->get('column_to');

		$data['button_delete'] 		  = $this->language->get('button_delete');
		$data['button_filter'] 		  = $this->language->get('button_filter');
		$data['button_save'] 		  = $this->language->get('button_save');
		$data['user_token']                = $this->session->data['user_token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$url .= '&filter_payment_method=' . $this->request->get['filter_payment_method'];
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
		
		$data['print'] = $this->url->link('possetting/sale_report/print', 'user_token=' . $this->session->data['user_token']. $url, true);
		
		
		$data['sort']  = $sort;
		$data['order'] = $order;
	  
		$data['sort_order_id']		= $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=order_id' . $url, true);
		$data['sort_date'] 			= $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=date' . $url, true);
		$data['sort_payment_method']= $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=payment_method' . $url, true);
		$data['sort_total']   	    = $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=total' . $url, true);
		
		if (isset($this->session->data['success'])) {
		 $data['success'] = $this->session->data['success'];
		unset($this->session->data['success']);
		} else {
		$data['success'] = '';
		}

		if (isset($this->request->post['setting_paymentmethod'])) {
			$setting_paymentmethods = $this->request->post['setting_paymentmethod'];
		} else {
			$setting_paymentmethods = $this->config->get('setting_paymentmethod');
		}
		
		$data['setting_paymentmethods'] = array();
		if(is_array($setting_paymentmethods)) {
			foreach ($setting_paymentmethods as $setting_paymentmethod) {
				$data['setting_paymentmethods'][] = array(
					'name' 	=> $setting_paymentmethod['method_name']
				);
			}
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$url .= '&filter_payment_method=' . $this->request->get['filter_payment_method'];
		}

		if (isset($this->request->get['filter_date_form'])) {
			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}


		$data['sort']  = $sort;
		$data['order'] = $order;
		$data['packages']=array();
		   
		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$url .= '&filter_payment_method=' . $this->request->get['filter_payment_method'];
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
		$pagination->total = $commission_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($commission_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($commission_total - $this->config->get('config_limit_admin'))) ? $commission_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $commission_total, ceil($commission_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
		$data['filter_payment_method'] = $filter_payment_method;
		$data['filter_date_form'] = $filter_date_form;
		$data['filter_date_to'] = $filter_date_to;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/sale_report', $data));
	}

	public function print() {
		$this->load->language('possetting/sale_report');
		$this->load->model('possetting/sale_report');
		$this->load->model('possetting/commission_report');

		$data['sellreports'] = array();
		
		
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_payment_method'])) {
			$filter_payment_method = $this->request->get['filter_payment_method'];
		} else {
			$filter_payment_method = null;
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
		
		$data['sellreports'] = array();

		$filter_data = array(
			'filter_order_id'   => $filter_order_id,
			'filter_payment_method' => $filter_payment_method,
			'filter_date_form' => $filter_date_form,
			'filter_date_to' => $filter_date_to,
			'sort'  => $sort,
			'order' => $order,
			'start' => '0',
			'limit' => '999999999999999999'
		);

		$results = $this->model_possetting_sale_report->getSellReports($filter_data);
				
		foreach ($results as $result) {
			$amount_info = $this->model_possetting_commission_report->getAmount($result['order_id']);
			if(isset($amount_info['total'])){
				$amount=$amount_info['total'];         
			} else {
				$amount='';
			}	

			$data['sellreports'][] = array(
				'order_id'    		=> $result['order_id'],
				'payment_method'	=> $result['payment_method'],
				'total'             => $this->currency->format($result['total'], $this->config->get('config_currency'), $this->config->get('currency_value')),
				'date_added' 		=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			);
			
		}
   
		$data['heading_title']        = $this->language->get('heading_title');

		$data['text_list']            = $this->language->get('text_list');
		$data['text_no_results'] 	  = $this->language->get('text_no_results');
		$data['text_confirm'] 		  = $this->language->get('text_confirm');
		$data['text_nooption'] 		  = $this->language->get('text_nooption');
		$data['text_select'] 		  = $this->language->get('text_select');

		$data['column_order_id']	  = $this->language->get('column_order_id');
		$data['column_date']	      = $this->language->get('column_date');
		$data['column_payment_method']= $this->language->get('column_payment_method');
		$data['column_total_amount']  = $this->language->get('column_total_amount');
		$data['column_total']	      = $this->language->get('column_total');
		$data['column_form']	      = $this->language->get('column_form');
		$data['column_to']	          = $this->language->get('column_to');

		$data['button_delete'] 		  = $this->language->get('button_delete');
		$data['button_filter'] 		  = $this->language->get('button_filter');
		$data['button_save'] 		  = $this->language->get('button_save');
		$data['user_token']                = $this->session->data['user_token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';

		
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/salereport_print', $data));
	}

}