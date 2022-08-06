<?php
class ControllerAccountTransaction extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/transaction', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}



		$this->load->language('account/transaction');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_transaction'),
			'href' => $this->url->link('account/transaction', '', true)
		);

		$this->load->model('account/transaction');
		
		$data['column_amount'] = sprintf($this->language->get('column_amount'), $this->config->get('config_currency'));

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['transactions'] = array();

		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$transaction_total = $this->model_account_transaction->getTotalTransactions();

		$results = $this->model_account_transaction->getTransactions($filter_data);

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

        	////sharma for pos
		$agent_info = $this->model_account_transaction->getAgent($this->customer->getId());
		if(!$agent_info){
			$data['notapplied'] = true;
		}
			////sharma for pos

		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('account/transaction', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($transaction_total - 10)) ? $transaction_total : ((($page - 1) * 10) + 10), $transaction_total, ceil($transaction_total / 10));

		$data['total'] = $this->currency->format($this->customer->getBalance(), $this->session->data['currency']);


		if ($this->config->get('config_agent_term_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_agent_term_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_agent_term_id'), true), $information_info['title']);
				$data['error_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			} else {
				$data['text_agree'] = ''; 
				$data['error_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}		


		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/transaction', $data));
	}

	////sharma for pos

	public function agentCheck(){

		$this->load->model('account/transaction');
		$this->load->language('account/transaction');

		$json['logged'] = $this->customer->isLogged();

		$agent_info = $this->model_account_transaction->getAgent($this->customer->getId());
		if($agent_info){

			if($agent_info['status'] == 0){
				$json['agent_code_info'] = sprintf($this->language->get('text_agent_code'), $agent_info['code_prefix'] . $agent_info['service_agent_id']);
				$json['status'] = false;	
			}else{
				$json['agent_code_info'] = sprintf($this->language->get('text_approved_agent_code'), $agent_info['code_prefix'] . $agent_info['service_agent_id']);
				$json['status'] = true;	
			}

		}else{
			$json['error']['warning'] = 'Not Found';
			$json['agent_code_info'] = $this->language->get('button_beagent');
			$json['notapplied'] = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}	

	public function beagent(){
		$this->load->model('account/transaction');
		$this->load->language('account/transaction');		

		$data['customer_id'] = $this->customer->getId();
		$data['code_prefix'] = 'NPC';

		$check_info = $this->model_account_transaction->getAgent($this->customer->getId());
		$json['error_agree'] = $this->language->get('error_agree');

		if(!$check_info){		
			$agent_info = $this->model_account_transaction->getAgentInfo($data);
			if($agent_info){
				$json['success'] = sprintf($this->language->get('text_agent_app_success'), $this->customer->getFirstName() . ' '.  $this->customer->getLastName());
				$json['agent_code'] = $agent_info['code_prefix'] . $agent_info['service_agent_id'];	
				$json['agent_code_info'] = sprintf($this->language->get('text_agent_code'), $agent_info['code_prefix'] . $agent_info['service_agent_id']);
				$json['service_agent_id'] = $agent_info['service_agent_id'];			
			} else {
				$json['error']['warning'] = 'Not Found!';
			}
		}else{
			$json['error']['warning'] = 'Already Applied!';

		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function updatetearm(){
		$this->load->model('account/transaction');

		$agent_id = (int)$this->request->post['agent_id'];
		$checked = (int)$this->request->post['checked'];

		print_r($agent_id)	;	

		$this->model_account_transaction->agentTearmUpdate($agent_id, $checked);	

	}

		////sharma for pos
}