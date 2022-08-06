<?php
class ControllerAccountPosappedit extends Controller {
	private $error = array();
	public function index() {
		$status = $this->config->get('module_posapp_status');
		$apikey = $this->config->get('module_posapp_apikey');
		$data['logged'] = $this->customer->isLogged();	
		

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/posappedit', '', true);

			$this->response->redirect($this->url->link('account/posapp', '', true));
		}
		$this->load->language('account/posapp');

		$this->document->setTitle($this->language->get('heading_title_edit'));
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $apikey );

		$this->load->model('account/posapp'); 
		$this->load->model('account/customer'); 

		$posapp_info = $this->model_account_posapp->getPosappById($this->request->get['posapp_id']);

		if($posapp_info['frontimage']){
			$data['f_image'] = $posapp_info['frontimage'];
		}else{
			$data['f_image'] = '';

		}

		if($posapp_info['tradelicenceimage']){
			$data['tl_image'] = $posapp_info['tradelicenceimage'];
		}else{
			$data['tl_image'] = '';
		}
		

		$data['status'] = 2;
		$data['commission_value'] = $this->config->get('module_posapp_commission_value');

		if (isset($this->request->files['frontimage'])) {                    
			$data['frontimage'] =$this->request->files['frontimage']['name']; 
			$data['frontimage_tmp'] =$this->request->files['frontimage']['tmp_name'];
			$pathinfo = pathinfo($this->request->files['frontimage']['name']);
			if($this->request->files['frontimage']['name']){
				$data['frontimage_extension']= $pathinfo['extension'];
			}
		}else{
			$data['frontimage'] = $posapp_info['frontimage'];
			$data['frontimage_tmp'] =''; 
			$pathinfo = '';
			$data['frontimage_extension']  = '';                         
		}
		

		
		if (isset($this->request->files['tradelicenceimage'])) {                    
			$data['tradelicenceimage'] =$this->request->files['tradelicenceimage']['name']; 
			$data['tradelicenceimage_tmp'] =$this->request->files['tradelicenceimage']['tmp_name'];
			$pathinfo = pathinfo($this->request->files['tradelicenceimage']['name']);
			if($this->request->files['tradelicenceimage']['name']){
				$data['tradelicenceimage_extension']= $pathinfo['extension']; 
			}
		}else{
			$data['tradelicenceimage'] =$posapp_info['tradelicenceimage'];;
			$data['tradelicenceimage_tmp'] =''; 
			$pathinfo = '';
			$data['tradelicenceimage_extension']  = '';                         
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {			
			
			$posapp_id = $this->request->get['posapp_id'];

			$this->model_account_posapp->editPosapp($posapp_id, $this->request->post, $data);

			// $approved_app = $this->model_account_posapp->getUser($posapp_id);
			// if($approved_app){
			$this->model_account_posapp->editUser($posapp_id, $this->request->post, $data);
            // }
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/posappstatus', '', true));
		}

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
			'text' => $this->language->get('text_application'),
			'href' => $this->url->link('account/posappedit', '', true)
		);		

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['frontimage'])) {
			$data['error_frontimage'] = $this->error['frontimage'];
		} else {
			$data['error_frontimage'] = '';
		}
		if (isset($this->error['tradelicenceimage'])) {
			$data['error_tradelicenceimage'] = $this->error['tradelicenceimage'];
		} else {
			$data['error_tradelicenceimage'] = '';
		}

		if (isset($this->error['size'])) {
			$data['error_size'] = $this->error['size'];
		} else {
			$data['error_size'] = '';
		}
		if (isset($this->error['extension'])) {
			$data['error_extension'] = $this->error['extension'];
		} else {
			$data['error_extension'] = '';
		}		

		$data['action'] = $this->url->link('account/posappedit', 'posapp_id=' . $this->request->get['posapp_id'], true)	;	

		
		
		if($posapp_info){

			$this->load->model('tool/image');
			if (is_file(DIR_IMAGE . $posapp_info['frontimage'])) {
				$image_frontimage = $this->model_tool_image->resize($posapp_info['frontimage'], 200, 200);
			} else {
				$image_frontimage = $this->model_tool_image->resize('no_image.png', 200, 200);
			}
			if (is_file(DIR_IMAGE . $posapp_info['tradelicenceimage'])) {
				$image_tradelicenceimage = $this->model_tool_image->resize($posapp_info['tradelicenceimage'], 200, 200);
			} else {
				$image_tradelicenceimage = $this->model_tool_image->resize('no_image.png', 200, 200);
			}	


			if ($posapp_info['frontimage']) {
				$data['frontimage_pre'] = $image_frontimage;
			}else{
				$data['frontimage_pre'] = '';
			}
			

			if ($posapp_info['tradelicenceimage']) {
				$data['tradelicenceimage_pre'] = $image_tradelicenceimage;
			}else{
				$data['tradelicenceimage_pre'] = '';
			}

		// Categories
			$this->load->model('catalog/category');

			if (isset($this->request->post['posapp_category'])) {
				$categories = $this->request->post['posapp_category'];
			} elseif (isset($this->request->get['posapp_id'])) {
				$categories = $this->model_account_posapp->getPosappCategories($this->request->get['posapp_id']);
			} else {
				$categories = array();
			}

			$data['posapp_categories'] = array();

			foreach ($categories as $category_id) {
				$category_info = $this->model_catalog_category->getCategory2($category_id);

				if ($category_info) { 
					$data['posapp_categories'][] = array(
						'category_id' => $category_info['category_id'],
						'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
					);
				}
			}
			
			if(isset($this->request->post['store_id'])){
				$data['store_id'] = $this->request->post['store_id'];		
			}elseif (!empty($posapp_info)) {
				$data['store_id'] = (int)$posapp_info['store_id'];
			}else{
				$data['store_id'] = $this->config->get('store_id');
			}

			if (isset($this->request->post['username'])) {
				$data['username'] = $this->request->post['username'];
			}elseif (!empty($posapp_info)) {
				$data['username'] = $posapp_info['username'];
			} else {
				$data['username'] = '';
			}

			if (isset($this->error['username'])) {
				$data['error_username'] = $this->error['username'];
			} else {
				$data['error_username'] = '';
			}
			if (isset($this->request->post['posname'])) {
				$data['posname'] = $this->request->post['posname'];
			}elseif (!empty($posapp_info)) {
				$data['posname'] = $posapp_info['posname'];
			} else {
				$data['posname'] = '';
			}
			if (isset($this->error['posname'])) {
				$data['error_posname'] = $this->error['posname'];
			} else {
				$data['error_posname'] = '';
			}

			if (isset($this->request->post['code'])) {
				$data['code'] = $this->request->post['code'];
			}elseif (!empty($posapp_info)) {
				$data['code'] = $posapp_info['code'];
			} else {
				$data['code'] = '';
			}


			if (isset($this->request->post['address'])) {
				$data['address'] = $this->request->post['address'];
			}elseif (!empty($posapp_info)) {
				$data['address'] = $posapp_info['address'];
			}  else {
				$data['address'] = '';
			}

			if (isset($this->request->post['city'])) {
				$data['city'] = $this->request->post['city'];
			}elseif (!empty($posapp_info)) {
				$data['city'] = $posapp_info['city'];
			}  else {
				$data['city'] = '';
			}
			if (isset($this->error['city'])) {
				$data['error_city'] = $this->error['city'];
			} else {
				$data['error_city'] = '';
			}

			if (isset($this->request->post['country_id'])) {
				$data['country_id'] = $this->request->post['country_id'];
			}elseif (!empty($posapp_info)) {
				$data['country_id'] = (int)$posapp_info['country_id'];
			}  else {
				$data['country_id'] = '';
			}

			

			if (isset($this->request->post['zone_id'])) {
				$data['zone_id'] = $this->request->post['zone_id'];
			}elseif (!empty($posapp_info)) {
				$data['zone_id'] = (int)$posapp_info['zone_id'];
			}  else {
				$data['zone_id'] = '';
			}

			if (isset($this->error['zone'])) {
				$data['error_zone'] = $this->error['zone'];
			} else {
				$data['error_zone'] = '';
			}		

			if (isset($this->request->post['cost'])) {
				$data['cost'] = $this->request->post['cost'];
			}elseif (!empty($posapp_info)) {
				$data['cost'] = (int)$posapp_info['cost'];
			} else {
				$data['cost'] = 0;
			}		

			if (isset($this->error['cost'])) {
				$data['error_cost'] = $this->error['cost'];
			} else {
				$data['error_cost'] = '';
			}

			if (isset($this->request->post['alloweddistance'])) {
				$data['alloweddistance'] = $this->request->post['alloweddistance'];
			}elseif (!empty($posapp_info)) {
				$data['alloweddistance'] = (int)$posapp_info['alloweddistance'];
			} else {
				$data['alloweddistance'] = 1;
			}		

			if (isset($this->error['alloweddistance'])) {
				$data['error_alloweddistance'] = $this->error['alloweddistance'];
			} else {
				$data['error_alloweddistance'] = '';
			}
			

			

			if (isset($this->request->post['licence_validaty'])) {
				$data['licence_validaty'] = $this->request->post['licence_validaty'];
			}elseif (!empty($posapp_info)) {
				$data['licence_validaty'] = date($this->language->get('date_format_short'), strtotime($posapp_info['licence_validaty']));
			} else {
				$data['licence_validaty'] = '';
			}
			if (isset($this->error['licence_validaty'])) {
				$data['error_licence_validaty'] = $this->error['licence_validaty'];
			} else {
				$data['error_licence_validaty'] = '';
			}
			

			
			
			$data['commissions'] = array();				
			$data['commissions'][] = array(
				'commission'    => $this->language->get('text_percent'),
				'value' 		=> 'Pecentage'
			);
			
			if (isset($this->request->post['commission'])) {
				$data['commission'] = $this->request->post['commission'];
			}elseif (!empty($posapp_info)) {
				$data['commission'] = $posapp_info['commission'];
			}  else {
				$data['commission'] = '';
			}

			
			

			if (isset($this->request->post['latitude'])) {
				$data['latitude'] = $this->request->post['latitude'];
			}elseif (!empty($posapp_info)) {
				$data['latitude'] = $posapp_info['latitude'];
			} else {
				$data['latitude'] = '';
			}

			if (isset($this->request->post['longitude'])) {
				$data['longitude'] = $this->request->post['longitude'];
			}elseif (!empty($posapp_info)) {
				$data['longitude'] = $posapp_info['longitude'];
			} else {
				$data['longitude'] = '';
			}

			if (isset($this->error['coordinate'])) {
				$data['error_coordinate'] = $this->error['coordinate'];
			} else {
				$data['error_coordinate'] = '';
			}


			$this->load->model('localisation/country');

			$data['countries'] = $this->model_localisation_country->getCountries();

			

			if (isset($this->request->post['firstname'])) {
				$data['firstname'] = $this->request->post['firstname'];
			} elseif($this->customer->isLogged()) {
				$data['firstname'] = $this->customer->getFirstName();
			}else {
				$data['firstname'] = '';
			}

			if (isset($this->error['firstname'])) {
				$data['error_firstname'] = $this->error['firstname'];
			} else {
				$data['error_firstname'] = '';
			}
			

			if (isset($this->request->post['lastname'])) {
				$data['lastname'] = $this->request->post['lastname'];
			} elseif($this->customer->isLogged()) {
				$data['lastname'] = $this->customer->getLastName();
			}else {
				$data['lastname'] = '';
			}

			if (isset($this->error['lastname'])) {
				$data['error_lastname'] = $this->error['lastname'];
			} else {
				$data['error_lastname'] = '';
			}

			if (isset($this->request->post['email'])) {
				$data['email'] = $this->request->post['email'];
			}elseif (!empty($posapp_info)) {
				$data['email'] = $posapp_info['email'];
			} else {
				$data['email'] = '';
			}

			if (isset($this->error['email'])) {
				$data['error_email'] = $this->error['email'];
			} else {
				$data['error_email'] = '';
			}

			if (isset($this->request->post['telephone'])) {
				$data['telephone'] = $this->request->post['telephone'];
			}elseif (!empty($posapp_info)) {
				$data['telephone'] = $posapp_info['telephone'];
			} else {
				$data['telephone'] = '';
			}

			if (isset($this->error['telephone'])) {
				$data['error_telephone'] = $this->error['telephone'];
			} else {
				$data['error_telephone'] = '';
			}


			if (isset($this->request->post['password'])) {
				$data['password'] = $this->request->post['password'];
			} else {
				$data['password'] = '';
			}

			if (isset($this->error['password'])) {
				$data['error_password'] = $this->error['password'];
			} else {
				$data['error_password'] = '';
			}

			if (isset($this->request->post['confirm'])) {
				$data['confirm'] = $this->request->post['confirm'];
			} else {
				$data['confirm'] = '';
			}

			if (isset($this->error['confirm'])) {
				$data['error_confirm'] = $this->error['confirm'];
			} else {
				$data['error_confirm'] = '';
			}

		}
		

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/posappedit', $data));
	}

	public function country() {
		$json = array();

		if (isset($this->request->get['country_id'])) {
			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

			if ($country_info) {
				$this->load->model('localisation/zone');

				foreach ($this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']) as $zone) {
					$json['zone'][] = array(
						'zone_id' => $zone['zone_id'],
						'name'    => $zone['name']
					);					
				}
			}

		}



		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validate() {
		if ((utf8_strlen($this->request->post['username']) < 3) || (utf8_strlen($this->request->post['username']) > 20)) {
			$this->error['username'] = $this->language->get('error_username');
		}


		$posusername_info = $this->model_account_posapp->getUserNameByUsername($this->request->post['username']);
		$posusername_info2 = $this->model_account_posapp->getUserNameByUsername2($this->request->post['username']);

		if ($posusername_info) {
			$posuser_posapp_id = $posusername_info['posapp_id'];
		}elseif($posusername_info2){
			$posuser_posapp_id = $posusername_info2['posapp_id'];
		}else{
			$posuser_posapp_id = 0;

		}

		if (!isset($this->request->get['posapp_id'])) {
			if ($posusername_info || $posusername_info2) {
				$this->error['warning'] = $this->language->get('error_exists_username');
			}
		} else {
			if (($posusername_info || $posusername_info2) && ($this->request->get['posapp_id'] != $posuser_posapp_id)) {
				$this->error['warning'] = $this->language->get('error_exists_username');
			}
		}



		if (($this->request->post['latitude']) == '' || ($this->request->post['longitude'] == '')) {
			$this->error['coordinate'] = $this->language->get('error_coordinate');
		}

		if ((utf8_strlen($this->request->post['posname']) < 3) || (utf8_strlen($this->request->post['posname']) > 20)) {
			$this->error['posname'] = $this->language->get('error_posname');
		}
		if ((utf8_strlen($this->request->post['city']) < 3) || (utf8_strlen($this->request->post['city']) > 20)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		if ($this->request->post['zone_id'] < 1 || $this->request->post['zone_id'] == ''  ) {
			$this->error['zone'] = $this->language->get('error_zone');
		}
		if(!$this->request->get['posapp_id']){
			if ((utf8_strlen($this->request->files['tradelicenceimage']['name']) < 3)) {
				$this->error['tradelicenceimage'] = $this->language->get('error_tradelicenceimage');
			}
			if ((utf8_strlen($this->request->files['frontimage']['name']) < 3)) {
				$this->error['frontimage'] = $this->language->get('error_frontimage');
			}
		}
		$current_date = date('Y-m-d');
		$postdate = $this->request->post['licence_validaty'];


		if ($postdate < $current_date) {
			$this->error['licence_validaty'] = $this->language->get('error_licence_validaty');
		}

		if ((!preg_match ("/^[0-9]*$/", $this->request->post['cost'])) || ($this->request->post['cost'] > 30)  ) {
			$this->error['cost'] = $this->language->get('error_cost');
		}

		if ((!preg_match ("/^[0-9]*$/", $this->request->post['alloweddistance'])) || ($this->request->post['alloweddistance'] > 10) || ($this->request->post['alloweddistance'] < 1)) {
			$this->error['alloweddistance'] = $this->language->get('error_alloweddistance');
		}


		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		

		if ( isset($this->request->files['tradelicenceimage']) && !empty($this->request->files['tradelicenceimage']['name'])) {
			$allowed = array('image/jpg','image/jpeg','image/png');
			if (!in_array($this->request->files['tradelicenceimage']['type'], $allowed)) {
				$this->error['extension'] = $this->language->get('error_extension');
			}
			if ($this->request->files['tradelicenceimage']['size']> 2097152) {
				$this->error['size'] = $this->language->get('error_size');
			}
		}
		if ( isset($this->request->files['frontimage']) && !empty($this->request->files['frontimage']['name'])) {
			$allowed = array('image/jpg','image/jpeg','image/png');
			if (!in_array($this->request->files['frontimage']['type'], $allowed)) {
				$this->error['extension'] = $this->language->get('error_extension');
			}
			if ($this->request->files['frontimage']['size']> 2097152) {
				$this->error['size'] = $this->language->get('error_size');
			}
		}
		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}
		
		$cusemail_info = $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email']);
		$posemail_info = $this->model_account_posapp->getTotalPossappsByEmail($this->request->post['email']);

		if (!isset($this->request->get['posapp_id'])) {
			if ($posemail_info || $cusemail_info ) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($posemail_info && ($this->request->get['posapp_id'] != $posemail_info['posapp_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}
		

		if(!$this->request->get['posapp_id']){			
			if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
				$this->error['firstname'] = $this->language->get('error_firstname');
			}

			if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
				$this->error['lastname'] = $this->language->get('error_lastname');
			}
			
		}

		
		
		if($this->request->post['password']){
			if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['confirm'] != $this->request->post['password']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}
		
		
		return !$this->error;
	}

	
}