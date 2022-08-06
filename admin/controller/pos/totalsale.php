<?php
class ControllerPosTotalSale extends Controller {
	public function index() {
		$this->load->language('pos/pos');
		$this->load->model('pos/order');

		$data['user_token'] = $this->session->data['user_token'];
		
		$data['text_todaysale'] = $this->language->get('text_todaysale');
		
		$data['amount'] = $this->currency->format($this->model_pos_order->getOrderAmount(array()), $this->config->get('config_currency'));
		
		return $this->load->view('pos/totalsale', $data);
	}
}