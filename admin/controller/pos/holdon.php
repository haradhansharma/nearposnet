<?php
class ControllerPosHoldOn extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('pos/holdon');
		$this->load->model('pos/holdon');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['warning']='';
		$products = $this->pos->getProducts();
		if(empty($products))
		{
			$data['warning']=$this->language->get('text_warning');
		}

		$data['button_submit'] 		= $this->language->get('button_submit');
		$data['text_holdon'] 		= $this->language->get('text_holdon');
		$data['user_token'] 				= $this->session->data['user_token'];	
		
		$this->response->setOutput($this->load->view('pos/holdon', $data));

	}	

	public function submitholdon(){
		$this->load->model('pos/holdon');
		$this->load->language('pos/holdon');
		$json = array();
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if($this->request->post['holdon_no'] == ''){
				$json['error'] = $this->language->get('text_holdno');
			}
			if(!$json){
				$this->model_pos_holdon->addholdon($this->request->post);
				$json['success'] = $this->language->get('text_success');
				unset($this->session->data['cart']);
			}
		}	
					
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
