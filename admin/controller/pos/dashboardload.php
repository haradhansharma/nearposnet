<?php
class ControllerPosDashboardload extends Controller {
	public function index() {
		$this->load->language('pos/pos');
		$this->load->model('pos/order');
		$this->load->model('tool/image');

		$data['user_token'] = $this->session->data['user_token'];
		
		$data['text_todaysale'] = $this->language->get('text_todaysale');


		if (isset($this->request->post['setting_dashboard'])) {
			$setting_dashboards = $this->request->post['setting_dashboard'];
		} else {
			$setting_dashboards = $this->config->get('setting_dashboard');
		}
		
		$data['setting_dashboards'] = array();
		
		if(isset($setting_dashboards)) {
			foreach ($setting_dashboards as $dashboards) {
				

				$orderstatus_data=array();
				if (isset($dashboards['dashboard_orderstatus'])) {
					foreach ($dashboards['dashboard_orderstatus'] as $dashboard_orderstatus) {
						$orderstatus_data[]=$dashboard_orderstatus['order_status_id'];
					}
				}

				$payment_method_data=array();
				if (isset($dashboards['dashboard_paymentmethod'])) {
					foreach ($dashboards['dashboard_paymentmethod'] as $dashboard_paymentmethod) {
						$payment_method_data[]=$dashboard_paymentmethod['method'];
					}
				}

				$custom_figure=array();
				if (isset($dashboards['dashboard_custom_figure'])) {
					foreach ($dashboards['dashboard_custom_figure'] as $dashboard_custom_figure) {
						$custom_figure[]=$dashboard_custom_figure['figures'];
					}
				}
				
				
				$totalamount = $this->model_pos_order->getDashboardOrderAmount($orderstatus_data,$payment_method_data,$dashboards['daytype'],$custom_figure);
			
				$data['setting_dashboards'][] = array(
					'name' 	 			=> $dashboards['name'],
					'sort_order' 	 	=> $dashboards['sort_order'],
					'daytype' 	 		=> $dashboards['daytype'],
					'dashboard_status' 	=> $dashboards['dashboard_status'],
					'text_color' 	 	=> $dashboards['text_color'],
					'bg_color' 	 		=> $dashboards['bg_color'],
					'text_color' 	 	=> $dashboards['text_color'],
					'icon'      		=> $dashboards['icon'],
					////sharma for pos
					'dashboard_custom_figure'=> $custom_figure,
					////sharma for pos
					'dashboard_paymentmethod'=> $payment_method_data,
					'dashboard_orderstatus'=> $orderstatus_data,
					'totalamount'		=> $this->currency->format($totalamount, $this->config->get('config_currency')),
					
				);
			
			}
		}
		////sharma for pos 
		$data['setting_dashboards'][] = array(
					'name' 	 			=> 'Pending Com.',
					'sort_order' 	 	=> 998,
					'daytype' 	 		=> '',
					'dashboard_status' 	=> '',
					'text_color' 	 	=> '#ffffff',
					'bg_color' 	 		=> '#7f0d1d',					
					'icon'      		=> '',
					// 
					'totalamount'		=> $this->currency->format($this->cart->getCommissionPending(),  $this->config->get('config_currency'))
					
				);
		$data['setting_dashboards'][] = array(
					'name' 	 			=> 'Due',
					'sort_order' 	 	=> 999,
					'daytype' 	 		=> '',
					'dashboard_status' 	=> '',
					'text_color' 	 	=> '#ffffff',
					'bg_color' 	 		=> '#dba10e',					
					'icon'      		=> '',
					// 
					'totalamount'		=> $this->currency->format($this->cart->duePayment($this->session->data['posapp_id']),  $this->config->get('config_currency'))
					
				);

		$data['setting_dashboards'][] = array(
					'name' 	 			=> 'Web',
					'sort_order' 	 	=> 1000,
					'daytype' 	 		=> '',
					'dashboard_status' 	=> '',
					'text_color' 	 	=> '#ffffff',
					'bg_color' 	 		=> '#990e8f',					
					'icon'      		=> '',
					// 
					'totalamount'		=> $this->model_pos_order->getWebOrders($this->session->data['posapp_id'])
					
				);
		
		

		////sharma for pos
		
		
		

		
		
	
		
		if(isset($this->request->get['loaddirect']))
		{
				$this->response->setOutput($this->load->view('pos/dashboardload', $data));
		}
		else
		{	
			return $this->load->view('pos/dashboardload', $data);
		}
	}
}