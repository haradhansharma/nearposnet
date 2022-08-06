<?php
class ControllerPosTotalCash extends Controller {
	public function index() {
		$this->load->language('pos/pos');

		$data['user_token'] = $this->session->data['user_token'];
		
		$data['text_todaycash'] = $this->language->get('text_todaycash');

		$filter=array('payment_method'=>'cash');
		$data['amount']=$this->currency->format($this->model_pos_order->getOrderAmount($filter), $this->config->get('config_currency'));
		return $this->load->view('pos/totalcash', $data);
	}
}