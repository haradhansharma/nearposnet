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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
	 	
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
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
		
		$taxs_total = $this->model_possetting_sale_report->getTotalTaxReport($filter_data);
		$results = $this->model_possetting_sale_report->getTaxReports($filter_data);
		
		$grandtotale=0;
		$data['grandtotale']=0;

		$grandpaid=0;
		$granddue=0;

		$texttotal=0;
		$gtaxs=0;
		$data['gtaxs']=0;
		$data['texttotal']=array();
		$tax_infos = $this->model_possetting_sale_report->getSellTaxs($data);
		foreach ($tax_infos as $tax) {
			$data['texttotal'][$tax['name']]=0;
		}
		foreach ($results as $result) {
			if(isset($result['total'])){
				$amount=$result['total'];         
			} else {
				$amount='';
			}	
			$grandtotale+=$amount;

			$products=array();
			$orderproducts = $this->model_possetting_sale_report->getSellProducts($result['order_id']);
			foreach($orderproducts as $product) {
				$proview = HTTP_CATALOG.'index.php?route=product/product'.'&product_id=' . $product['product_id'];
				$products[]=array(
					'proview' 	=> $proview,
					'name'		=> $product['name'],
				);
			}

			$ordertaxs=array();
		
		foreach ($tax_infos as $tax) {
			$taxvalue = $this->model_possetting_sale_report->getTotalTaxs($result['order_id'],$tax['name']);
			if(!isset($taxvalue['value']))
			{
				$taxvalue['value']=0;
			}
			$ordertaxs[]=array(
					'value' => $this->currency->format($taxvalue['value'], $this->config->get('config_currency'), $this->config->get('currency_value')),
				);	
			$data['texttotal'][$tax['name']]=$data['texttotal'][$tax['name']]+$taxvalue['value'];
		}

		////sharma for pos

		$partial_paid =  $this->model_possetting_sale_report->getPartialPaid($result['order_id']);

		if( $this->model_possetting_sale_report->getPartialPaid($result['order_id'])){
			$partial_paid =  $this->model_possetting_sale_report->getPartialPaid($result['order_id']);
		}else{
			$partial_paid = $result['total'];
		}

		$due_partial = round($result['total'], 2) - round($partial_paid, 2);


		$grandpaid+=$partial_paid;
		$granddue+=$due_partial;
			
			
			$data['sellreports'][] = array(
				'order_id'    		=> $result['order_id'],
				'product'    		=> $products,
				'tax'    			=> $ordertaxs,
				'payment_method'	=> $result['payment_method'],
				'total'             => $this->currency->format($result['total'], $this->config->get('config_currency'), $this->config->get('currency_value')),
				'partial_paid'             => $this->currency->format($partial_paid, $this->config->get('config_currency'), $this->config->get('currency_value')),
				'due_partial'             => $this->currency->format($due_partial, $this->config->get('config_currency'), $this->config->get('currency_value')),
				'date_added' 		=> date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);	
		}

		$data['grandtotale'] = $this->currency->format($grandtotale, $this->config->get('config_currency'), $this->config->get('currency_value'));


		$data['grandpaid'] = $this->currency->format($grandpaid, $this->config->get('config_currency'), $this->config->get('currency_value'));
		$data['granddue'] = $this->currency->format($granddue, $this->config->get('config_currency'), $this->config->get('currency_value'));



		$data['gtaxs'] = $this->currency->format($gtaxs, $this->config->get('config_currency'), $this->config->get('currency_value'));
		$data['texttotals']=array();
		foreach($data['texttotal'] as $key=>$value) {
			$data['texttotals'][]=array('value'=>$this->currency->format($value, $this->config->get('config_currency'), $this->config->get('currency_value')));;
		}

		$data['saletaxs']= array();
		$tax_infos = $this->model_possetting_sale_report->getSellTaxs($data);
		
		foreach ($tax_infos as $tax) {
			$data['saletaxs'][] = array(
				'name'    => $tax['name'],
			);			
		}
   
		$data['heading_title']        = $this->language->get('heading_title');

		$data['text_list']            = $this->language->get('text_list');
		$data['text_no_results'] 	  = $this->language->get('text_no_results');
		$data['text_confirm'] 		  = $this->language->get('text_confirm');
		$data['text_nooption'] 		  = $this->language->get('text_nooption');
		$data['text_select'] 		  = $this->language->get('text_select');
		$data['text_print'] 		  = $this->language->get('text_print');

		$data['column_order_id']	  = $this->language->get('column_order_id');
		$data['column_product']	      = $this->language->get('column_product');
		$data['column_date']	      = $this->language->get('column_date');
		$data['column_payment_method']= $this->language->get('column_payment_method');
		$data['column_total']  		  =	$this->language->get('column_total');
		$data['column_form']	      = $this->language->get('column_form');
		$data['column_to']	          = $this->language->get('column_to');
		$data['column_grand']	      = $this->language->get('column_grand');

		$data['button_delete'] 		  = $this->language->get('button_delete');
		$data['button_filter'] 		  = $this->language->get('button_filter');
		$data['button_save'] 		  = $this->language->get('button_save');
		$data['user_token']           = $this->session->data['user_token'];
		
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
		$data['salereports'] = $this->url->link('possetting/sale_report/filterreport', 'user_token=' . $this->session->data['user_token']. $url, true);
	  
		$data['sort_order_id']		= $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=order_id' . $url, true);
		$data['sort_date'] 			= $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=date' . $url, true);
		$data['sort_payment_method']  = $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=payment_method' . $url, true);
		$data['sort_product']   		= $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=product' . $url, true);
		$data['sort_total']   		= $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . '&sort=total' . $url, true);
		
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
					'name' 	=> $setting_paymentmethod['name']
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
		$pagination->total = $taxs_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/sale_report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($taxs_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($taxs_total - $this->config->get('config_limit_admin'))) ? $taxs_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $taxs_total, ceil($taxs_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
		$data['filter_payment_method'] = $filter_payment_method;
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		if(isset($filter_date_form) && isset($filter_date_to)) {
			$data['filter_date_form'] = $filter_date_form;
			$data['filter_date_to'] = $filter_date_to;
		} else {
			$data['filter_date_form'] 	= date('Y-m-d');;
			$data['filter_date_to'] 	= date('Y-m-d');;
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/sale_report', $data));
	}

	public function filterreport() {
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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
	 	
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
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
			'limit' => '999999999999999',
		);

		$taxs_total = $this->model_possetting_sale_report->getTotalTaxReport($filter_data);
		$results = $this->model_possetting_sale_report->getTaxReports($filter_data);
		
		$grandtotale=0;
		$grandpaid=0;
		$granddue=0;
		$data['grandtotale']=0;
		$texttotal=0;
		$gtaxs=0;
		$data['gtaxs']=0;
		$data['texttotal']=array();
		$tax_infos = $this->model_possetting_sale_report->getSellTaxs($data);
		foreach ($tax_infos as $tax) {
			$data['texttotal'][$tax['name']]=0;
		}
		foreach ($results as $result) {
			if(isset($result['total'])){
				$amount=$result['total'];         
			} else {
				$amount='';
			}	
			$grandtotale+=$amount;

			$products=array();
			$orderproducts = $this->model_possetting_sale_report->getSellProducts($result['order_id']);
			foreach($orderproducts as $product) {
				$proview = HTTP_CATALOG.'index.php?route=product/product'.'&product_id=' . $product['product_id'];
				$products[]=array(
					'proview' 	=> $proview,
					'name'		=> $product['name'],
				);
			}

			$ordertaxs=array();
		
		foreach ($tax_infos as $tax) {
			$taxvalue = $this->model_possetting_sale_report->getTotalTaxs($result['order_id'],$tax['name']);
			if(!isset($taxvalue['value']))
			{
				$taxvalue['value']=0;
			}
			$ordertaxs[]=array(
					'value' => $this->currency->format($taxvalue['value'], $this->config->get('config_currency'), $this->config->get('currency_value')),
				);	
			$data['texttotal'][$tax['name']]=$data['texttotal'][$tax['name']]+$taxvalue['value'];
		}



		$partial_paid =  $this->model_possetting_sale_report->getPartialPaid($result['order_id']);
		if( $this->model_possetting_sale_report->getPartialPaid($result['order_id'])){
			$partial_paid =  $this->model_possetting_sale_report->getPartialPaid($result['order_id']);
		}else{
			$partial_paid = $result['total'];
		}
		$due_partial = round($result['total'], 2) - round($partial_paid, 2);

		$grandpaid+=$partial_paid;
		$granddue+=$due_partial;
			
			
			$data['sellreports'][] = array(
				'order_id'    		=> $result['order_id'],
				'product'    		=> $products,
				'tax'    			=> $ordertaxs,
				'payment_method'	=> $result['payment_method'],
				'total'             => $this->currency->format($result['total'], $this->config->get('config_currency'), $this->config->get('currency_value')),
				'partial_paid'             => $this->currency->format($partial_paid, $this->config->get('config_currency'), $this->config->get('currency_value')),
				'due_partial'             => $this->currency->format($due_partial, $this->config->get('config_currency'), $this->config->get('currency_value')),
				'date_added' 		=> date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);	
		}

		$data['grandtotale'] = $this->currency->format($grandtotale, $this->config->get('config_currency'), $this->config->get('currency_value'));


		$data['grandpaid'] = $this->currency->format($grandpaid, $this->config->get('config_currency'), $this->config->get('currency_value'));
		$data['granddue'] = $this->currency->format($granddue, $this->config->get('config_currency'), $this->config->get('currency_value'));



		$data['gtaxs'] = $this->currency->format($gtaxs, $this->config->get('config_currency'), $this->config->get('currency_value'));
		$data['texttotals']=array();
		foreach($data['texttotal'] as $key=>$value)
		{
			$data['texttotals'][]=array('value'=>$this->currency->format($value, $this->config->get('config_currency'), $this->config->get('currency_value')));;
		}

		$data['saletaxs']= array();
		$tax_infos = $this->model_possetting_sale_report->getSellTaxs($data);
		
		foreach ($tax_infos as $tax) {
			$data['saletaxs'][] = array(
				'name'    => $tax['name'],
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/salereport_print', $data));
	}
}