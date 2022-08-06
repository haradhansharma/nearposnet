<?php
class ControllerPosPartialpayment extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('pos/printinvoice');
		$this->load->model('pos/pos');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'partial_payment_id';
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
	
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}
		$data['order_id']=$order_id;


		$data['partial_payments'] = array();

		$filter_data = array(
			'order_id'              => $order_id,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);
		$order_info=$this->model_pos_pos->getOrderTotal($order_id);
		$partial_total = $this->model_pos_pos->getTotalPartialpayments($filter_data);

		$results = $this->model_pos_pos->getPartialpayments($filter_data);
	
		foreach ($results as $result) {
			$data['partial_payments'][] = array(
				'partial_amount'  => $this->currency->format($result['partial_amount'], $order_info['currency_code'], $order_info['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['text_form'] = !isset($this->request->get['customer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		
		$data['column_amount'] = $this->language->get('column_amount');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['text_payhistory'] = $this->language->get('text_payhistory');
		$data['text_amount'] = $this->language->get('text_amount');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['column_action'] = $this->language->get('column_action');
		$data['text_paynow'] = $this->language->get('text_paynow');
		$data['text_due_amount'] = $this->language->get('text_due_amount');

		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['user_token'] = $this->session->data['user_token'];

		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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

		$data['sort_partial_amount'] = $this->url->link('pos/partialpayment', 'user_token=' . $this->session->data['user_token'] . '&sort=partial_amount' . $url, true);
		$data['sort_date_added'] = $this->url->link('pos/partialpayment', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $partial_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('pos/partialpayment', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}'. '&order_id='.$order_id, true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($partial_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($partial_total - $this->config->get('config_limit_admin'))) ? $partial_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $partial_total, ceil($partial_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		

		if(!empty($order_info['total'])){
			$order_amount=$order_info['total'];
		}else{
			$order_amount=0;
		}

		$partial_sum=$this->model_pos_pos->getSumPartialpayments($order_id);
		$data['partial_sum']=$partial_sum;
		$data['order_amount']=$order_amount;

		$data['partial_pay']=$order_amount-$partial_sum;



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pos/partialpayment_form', $data));

	}
	
	public function addPartial() {	
		$this->load->language('pos/pos');		
		$this->load->model('pos/pos');		
		$json = array();
		if (empty($this->request->post['partial_amount'])) {
			$json['error'] ='Enter Amount!';
		}
		elseif ($this->request->post['partial_amount'] > $this->request->post['partial_pay']) {
			$json['error'] ='Partial Amount must be less than Order amount!';
		}

		if(!$json){
			$this->model_pos_pos->addPartialAmount($this->request->post);
			$json['success']='Payment Success!';
		}
		$this->response->setOutput(json_encode($json));
	}
	
}
