<?php
class ControllerPosReadqr extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('pos/readqr');

		$this->document->setTitle($this->language->get('heading_title'));

		
		$data['heading_title'] 		= $this->language->get('heading_title');
		$data['user_token'] 				= $this->session->data['user_token'];	
		
		$this->response->setOutput($this->load->view('pos/readqr', $data));
	}	
}
