<?php
class ControllerExtensionAdminpayCheque extends Controller {
	public function index() {
		$this->load->language('extension/adminpay/cheque');

		$data['payable'] = $this->config->get('payment_cheque_payable');
		$data['address'] = nl2br($this->config->get('config_address'));

		$data['user_token'] = $this->session->data['user_token'];

		$this->session->data['commission_order_id'] = $this->request->get['commission_order_id'];
		

		return $this->load->view('extension/adminpay/cheque', $data);
	}

	public function confirm() {	

		$json = array();
				
		if ($this->session->data['user_token']) {
			$this->load->language('extension/adminpay/cheque');

			// $this->load->model('checkout/order');
			$this->load->model('possetting/commission_report');

			$comment  = $this->language->get('text_payable') . "\n";
			$comment .= $this->config->get('payment_cheque_payable') . "\n\n";
			$comment .= $this->language->get('text_address') . "\n";
			$comment .= $this->config->get('config_address') . "\n\n";
			$comment .= $this->language->get('text_payment') . "\n";			
			
			$this->model_possetting_commission_report->addOrderHistory($this->session->data['commission_order_id'], $this->config->get('payment_cheque_order_status_id'), $comment, true);

			$this->session->data['success'] = $this->language->get('text_success');
			unset($this->session->data['commission_order_id']);
			
			
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'], true));
			
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}