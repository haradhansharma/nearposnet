<?php
class ControllerPossettingConfirm extends Controller {

	public function index() {
		$this->load->language('possetting/commission_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/commission_report');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('possetting/confirm', 'user_token=' . $this->session->data['user_token'], true)
		);	


		if (isset($this->request->get['commission_order_id'])) {
			$commission_order_id = $this->request->get['commission_order_id'];
		} else {
			$commission_order_id = 0;
		}


		$data = array();
		$order_info = $this->model_possetting_commission_report->getCommissionOrder($commission_order_id);
		if($order_info){

			$data['commission_order_id'] = $order_info['commission_order_id'];
			$data['invoice_prefix'] = $order_info['invoice_prefix'];
			$data['posapp_id'] = $order_info['posapp_id'];
			$data['posname'] = $order_info['posname'];
			$data['pos_url'] = $order_info['pos_url'];
			$data['store_id'] = $order_info['store_id'];
			$data['store_name'] = $order_info['store_name'];
			$data['store_url'] = $order_info['store_url'];
			$data['firstname'] = $order_info['firstname'];
			$data['lastname'] = $order_info['lastname'];
			$data['email'] = $order_info['email'];
			$data['telephone'] = $order_info['telephone'];
			$data['payment_firstname'] = $order_info['payment_firstname'];
			$data['payment_lastname'] = $order_info['payment_lastname'];
			$data['payment_address_1'] = $order_info['payment_address_1'];
			$data['payment_address_2'] = $order_info['payment_address_2'];
			$data['payment_city'] = $order_info['payment_city'];
			$data['payment_postcode'] = $order_info['payment_postcode'];
			$data['payment_country'] = $order_info['payment_country'];
			$data['payment_zone'] = $order_info['payment_zone'];
			$data['payment_method'] = $order_info['payment_method'];
			$data['total'] = $order_info['total'];
			$data['payable'] = $order_info['payable'];
			$data['order_status_id'] = $order_info['order_status_id']; 
			$data['commission_value'] = $order_info['commission_value'];
			$data['commission'] = $order_info['commission'];
			$data['currency_code'] = $order_info['currency_code'];
			$data['ip'] = $order_info['ip'];
			$data['date_added'] = $order_info['date_added'];
			$data['payment_address'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'] . '</br>' . $order_info['payment_address_1'] . '</br>' . $order_info['payment_address_2'] . '</br>' . $order_info['payment_city'] . '</br>' . $order_info['payment_zone'] . '</br>' . $order_info['payment_country'] . '</br>';


			$data['commission_order_details'] = array();
			$details_info = $this->model_possetting_commission_report->getCommissionOrderDetails($commission_order_id);
			foreach($details_info as $commission_order){
				$data['commission_order_details'][] = array(
					'pos_commission_details_id' => $commission_order['pos_commission_details_id'],
					'commission_order_id' => $commission_order['commission_order_id'],
					'order_id' => $commission_order['order_id'],
					'posapp_id' => $commission_order['posapp_id'],
					'posname' => $commission_order['posname'],
					'commission_value' => $commission_order['commission_value'],
					'commission' => $commission_order['commission'],
					'total' => $commission_order['total'],
					'payable' => $commission_order['payable'],
					'tax' => $commission_order['tax'],
					'creat_date' => $commission_order['creat_date']

				);

			}

			$data['commission_order_total'] = array();
			$total_info = $this->model_possetting_commission_report->getCommissionOrderTotal($commission_order_id);
			foreach($total_info as $commission_order_total){
				$data['commission_order_total'][] = array(
					'pos_commission_order_total_id' => $commission_order_total['pos_commission_order_total_id'],
					'commission_order_id' => $commission_order_total['commission_order_id'],
					'code' => $commission_order_total['code'],
					'title' => $commission_order_total['title'],
					'value' => $commission_order_total['value'],
					'sort_order' => $commission_order_total['sort_order']
				);

			}

		}

		$completed_status = $this->config->get('config_complete_status');
		$processing_status = $this->config->get('config_processing_status');
		$payment_pending = array(0);
		$unsuccessfull_status = array_merge($completed_status, $processing_status, $payment_pending);	
        
        if(in_array($order_info['order_status_id'],  $completed_status) ){
        	$data['payment'] = '<div class="alert alert-success">' . $this->language->get('message_payment_success') . '</div>';        	      	
        }elseif(in_array($order_info['order_status_id'],  $processing_status) ){ 
        	$data['payment'] = '<div class="alert alert-success">' . $this->language->get('message_payment_under_processing') . '</div>';                  	        	
        }elseif(in_array($order_info['order_status_id'],  $payment_pending) ){  
            $data['payment'] = '<div class="alert alert-success">' . $this->language->get('message_payment_pending') . '</div>'   . '</br>';                   	
        	$data['payment'] .= $this->load->controller('extension/adminpay/' . $this->config->get('module_posapp_payment_method_code'));        	
        }elseif(!in_array($order_info['order_status_id'],  $unsuccessfull_status) ){        
            $data['payment'] = '<div class="alert alert-danger">' . $this->language->get('message_payment_unsuccessfull') . '</div>'   . '</br>';          	
        	$data['payment'] .= $this->load->controller('extension/adminpay/' . $this->config->get('module_posapp_payment_method_code'));        	
        }
		



		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/confirm', $data));

	}
}