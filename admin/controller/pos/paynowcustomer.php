<?php
class ControllerPosPaynowcustomer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('customer/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/customer');
		
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
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

	
		$data['add'] = $this->load->controller('pos/customerlist/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['customers'] = array();

		$filter_data = array(
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		

		$results = $this->model_customer_customer->getCustomers($filter_data);

		foreach ($results as $result) {

			$login_info = $this->model_customer_customer->getTotalLoginAttempts($result['email']);

			if ($login_info && $login_info['total'] >= $this->config->get('config_login_attempts')) {
				$unlock = $this->url->link('pos/customerlist/unlock', 'user_token=' . $this->session->data['user_token'] . '&email=' . $result['email'] . $url, true);
			} else {
				$unlock = '';
			}

			$data['customers'][] = array(
				'customer_id'    => $result['customer_id'],
				'name'           => $result['name'],
				'email'          => $result['email'],
				'customer_group' => $result['customer_group'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'ip'             => $result['ip'],
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['heading_title'] 		= $this->language->get('heading_title');

		$data['text_list'] 			= $this->language->get('text_list');
		$data['text_enabled'] 		= $this->language->get('text_enabled');
		$data['text_disabled'] 		= $this->language->get('text_disabled');
		$data['text_yes'] 			= $this->language->get('text_yes');
		$data['text_no'] 			= $this->language->get('text_no');
		$data['text_default'] 		= $this->language->get('text_default');
		$data['text_no_results'] 	= $this->language->get('text_no_results');
		$data['text_confirm'] 		= $this->language->get('text_confirm');

		$data['column_name'] 		= $this->language->get('column_name');
		$data['column_email'] 		= $this->language->get('column_email');
		$data['column_customer_group'] = $this->language->get('column_customer_group');
		$data['column_status'] 		= $this->language->get('column_status');
		$data['column_approved'] 	= $this->language->get('column_approved');
		$data['column_ip'] 			= $this->language->get('column_ip');
		$data['column_date_added'] 	= $this->language->get('column_date_added');
		$data['column_action'] 		= $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['button_add'] = $this->language->get('button_add');
		
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
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

		$data['sort_name'] = $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_email'] = $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . '&sort=c.email' . $url, true);
		$data['sort_customer_group'] = $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . '&sort=customer_group' . $url, true);
		$data['sort_status'] = $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
		$data['sort_ip'] = $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . '&sort=c.ip' . $url, true);
		$data['sort_date_added'] = $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['customer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_add_ban_ip'] = $this->language->get('text_add_ban_ip');
		$data['text_remove_ban_ip'] = $this->language->get('text_remove_ban_ip');

// Custom Fields Start ///
		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_fax'] = $this->language->get('entry_fax');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_newsletter'] = $this->language->get('entry_newsletter');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_confirm'] = $this->language->get('entry_confirm');
// Custom Fields End ///		
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['customer_id'])) {
			$data['customer_id'] = $this->request->get['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);


		$data['cancel'] = $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$customer_info = $this->model_customer_customer->getCustomer($this->request->get['customer_id']);
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} elseif (!empty($customer_info)) {
			$data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} else {
			$data['firstname'] = $this->config->get('setting_firstname');
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else {
			$data['lastname'] = $this->config->get('setting_lastname');
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = $this->config->get('setting_email');
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else {
			$data['telephone'] = $this->config->get('setting_telephone');
		}

		if (isset($this->request->post['fax'])) {
			$data['fax'] = $this->request->post['fax'];
		} else {
			$data['fax'] = $this->config->get('setting_fax');
		}

		if (isset($this->request->post['company'])) {
			$data['company'] = $this->request->post['company'];
		} else {
			$data['company'] = $this->config->get('setting_company');
		}

		if (isset($this->request->post['address_1'])) {
			$data['address_1'] = $this->request->post['address_1'];
		} else {
			$data['address_1'] = $this->config->get('setting_address1');
		}

		if (isset($this->request->post['address_2'])) {
			$data['address_2'] = $this->request->post['address_2'];
		} else {
			$data['address_2'] = $this->config->get('setting_address2');
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} else {
			$data['city'] = $this->config->get('setting_city');
		}

		if (isset($this->request->post['postcode'])) {
			$data['postcode'] = $this->request->post['postcode'];
		} else {
			$data['postcode'] = $this->config->get('setting_postcode');
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} else {
			$data['country_id'] = $this->config->get('setting_country_id');
		}

// Custom Fields Start ///
		$modulestatus = $this->config->get('setting_status');
		if(!empty($modulestatus)){

			// label

			$allalbles = $this->config->get('setting_settings');

			if(!empty($allalbles['firstnamelabel'][$this->config->get('config_language_id')])) {
				$data['entry_firstname']	= $allalbles['firstnamelabel'][$this->config->get('config_language_id')];
			}

			if(!empty($allalbles['lastnamelabel'][$this->config->get('config_language_id')])) {
				$data['entry_lastname'] 	= $allalbles['lastnamelabel'][$this->config->get('config_language_id')];
			}		

			if(!empty($allalbles['emaillabel'][$this->config->get('config_language_id')])) {
				$data['entry_email'] 		= $allalbles['emaillabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['phonelabel'][$this->config->get('config_language_id')])) {
				$data['entry_telephone']	= $allalbles['phonelabel'][$this->config->get('config_language_id')];
			}

			if(!empty($allalbles['faxlabel'][$this->config->get('config_language_id')])) {
				$data['entry_fax']			= $allalbles['faxlabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['companylabel'][$this->config->get('config_language_id')])) {
			$data['entry_company']			= $allalbles['companylabel'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['add1label'][$this->config->get('config_language_id')])) {
				$data['entry_address_1'] 	= $allalbles['add1label'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['add2label'][$this->config->get('config_language_id')])) {
				$data['entry_address_2'] 	= $allalbles['add2label'][$this->config->get('config_language_id')];
			}
			
			if(!empty($allalbles['citylabel'][$this->config->get('config_language_id')])) {
				$data['entry_city']			= $allalbles['citylabel'][$this->config->get('config_language_id')];
			}

			if(!empty($allalbles['postcodelabel'][$this->config->get('config_language_id')])) {
				$data['entry_postcode']		= $allalbles['postcodelabel'][$this->config->get('config_language_id')];
			}

			if(!empty($allalbles['countrylabel'][$this->config->get('config_language_id')])) {
				$data['entry_country']		= $allalbles['countrylabel'][$this->config->get('config_language_id')];
			}

			if(!empty($allalbles['zonelabel'][$this->config->get('config_language_id')])) {
				$data['entry_zone']		    = $allalbles['zonelabel'][$this->config->get('config_language_id')];
			}

			if(!empty($allalbles['pwdlabel'][$this->config->get('config_language_id')])) {
				$data['entry_password']		= $allalbles['pwdlabel'][$this->config->get('config_language_id')];
			}

			if(!empty($allalbles['cpwdlabel'][$this->config->get('config_language_id')])) {
				$data['entry_confirm']		= $allalbles['cpwdlabel'][$this->config->get('config_language_id')];
			}


			// Require status
			$data['firstnamerequiredstatus'] = $this->config->get('setting_fnamerequired');
			$data['lastnamerequiredstatus']  = $this->config->get('setting_lastnamerequired');
			$data['emailrequiredstatus'] 	 = $this->config->get('setting_emailrequired');
			$data['phonerequiredstatus'] 	 = $this->config->get('setting_phonerequired');
			$data['faxrequiredstatus'] 		 = $this->config->get('setting_faxrequired');
			$data['companyrequiredstatus'] 	 = $this->config->get('setting_compquired');
			$data['add1requiredstatus'] 	 = $this->config->get('setting_add1required');
			$data['add2requiredstatus'] 	 = $this->config->get('setting_add2required');
			$data['cityrequiredstatus'] 	 = $this->config->get('setting_cityrequired');
			$data['postcoderequiredstatus']  = $this->config->get('setting_postcodrequired');
			$data['countryrequiredstatus']   = $this->config->get('setting_countryrequired');
			$data['zonerequiredstatus']      = $this->config->get('setting_zonerequired');
			$data['pwdrequiredstatus']       = $this->config->get('setting_pwdrequired');
			$data['cpwdrequiredstatus']      = $this->config->get('setting_cpwdrequired');
			
						

			// Status
			$data['firstnamestatus'] 	= $this->config->get('setting_fnamestatus');
			$data['lastnamestatus'] 	= $this->config->get('setting_lastnamestatus');
			$data['emailstatus'] 		= $this->config->get('setting_emailstatus');
			$data['phonestatus'] 		= $this->config->get('setting_phonestatus');
			$data['faxstatus'] 		    = $this->config->get('setting_faxstatus');
			$data['companystatus'] 		= $this->config->get('setting_compstatus');
			$data['add1status'] 		= $this->config->get('setting_add1status');
			$data['add2status'] 		= $this->config->get('setting_add2status');
			$data['citystatus'] 		= $this->config->get('setting_citystatus');
			$data['postcodestatus'] 	= $this->config->get('setting_postcodstatus');
			$data['countrystatus'] 	    = $this->config->get('setting_countrystatus');
			$data['zonestatus'] 	    = $this->config->get('setting_zonestatus');
			$data['pwdstatus'] 	        = $this->config->get('setting_pwdstatus');
			$data['cpwdstatus'] 	    = $this->config->get('setting_cpwdstatus');
			
						

			// Sort Order
			$data['firstnamesort_order']= $this->config->get('setting_fnamesortorder');
			$data['lastnamesort_order'] = $this->config->get('setting_lastnamesortorder');
			$data['emailsort_order'] 	= $this->config->get('setting_emailsortorder');
			$data['phonesort_order'] 	= $this->config->get('setting_phonesortorder');
			$data['faxsort_order'] 		= $this->config->get('setting_faxsortorder');
			$data['companysort_order'] 	= $this->config->get('setting_compsortorder');
			$data['add1sort_order'] 	= $this->config->get('setting_add1sortorder');
			$data['add2sort_order'] 	= $this->config->get('setting_add2sortorder');
			$data['citysort_order'] 	= $this->config->get('setting_citysortorder');
			$data['postcodesort_order'] = $this->config->get('setting_postcodsortorder');
			$data['countrysort_order']  = $this->config->get('setting_countrysortorder');
			$data['zonesort_order']     = $this->config->get('setting_zonesortorder');
			$data['pwdsort_order']      = $this->config->get('setting_pwdsortorder');
			$data['cpwdsort_order']     = $this->config->get('setting_cpwdsortorder');
			
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = (int)$this->request->post['country_id'];
		} elseif (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->request->post['zone_id'])) {
			$data['zone_id'] = (int)$this->request->post['zone_id'];
		} elseif (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();
// Custom Fields End ///		
        $this->response->setOutput($this->load->view('pos/paynowcustomer', $data));
        //return $this->load->view('pos/paynowcustomer', $data);

	}
	
	public function addpaycustomer() {	
		$this->load->language('pos/pos');		
		$this->load->model('pos/pos');		
		
		$modulestatus	    = $this->config->get('setting_status');
		
		$fnamerequired	    = $this->config->get('setting_fnamerequired');
		$lnamerequired	    = $this->config->get('setting_lastnamerequired');
		$emailrequired	    = $this->config->get('setting_emailrequired');
		$phonerequired	    = $this->config->get('setting_phonerequired');
		$companyrequired    = $this->config->get('setting_compquired');
		$add1required		= $this->config->get('setting_add1required');
		$cityrequired		= $this->config->get('setting_cityrequired');
		$postcoderequired	= $this->config->get('setting_postcodrequired');
		$countryrequired	= $this->config->get('setting_countryrequired');
		$zonerequired		= $this->config->get('setting_zonerequired');
		$pwdrequired		= $this->config->get('setting_pwdrequired');
		$cpwdrequired		= $this->config->get('setting_cpwdrequired');

		$allalbles 		= $this->config->get('setting_settings');

		$json = array();
		if (!$json) {
			$this->load->model('pos/pos');
			if(!empty($fnamerequired)) {
				if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
					$json['error'][] = $allalbles['firstnamerror'][$this->config->get('config_language_id')];
				}
			}

			if(!empty($lnamerequired)) {
				if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
					$json['error'][] = $allalbles['lastnamerror'][$this->config->get('config_language_id')];
				}
			}
			if(!empty($emailrequired)) {
				if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
					$json['error'][] = $allalbles['emailerror'][$this->config->get('config_language_id')];
				}
			}
			if(!empty($emailrequired)) {
			if ($this->model_pos_pos->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error'][] = $this->language->get('error_exists');
			}
			}
			if(!empty($phonerequired)) {
				if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
					$json['error'][] = $allalbles['phonerror'][$this->config->get('config_language_id')];
				}
			}

			if(!empty($faxrequired)) {
				if ((utf8_strlen(trim($this->request->post['fax'])) < 3) || (utf8_strlen(trim($this->request->post['fax'])) > 32)) {
					$json['error'][] = $allalbles['faxerror'][$this->config->get('config_language_id')];
				}
			}
			if(!empty($companyrequired)) {
				if ((utf8_strlen(trim($this->request->post['company'])) < 3) || (utf8_strlen(trim($this->request->post['company'])) > 128)) {
					$json['error'][] = $allalbles['companyerror'][$this->config->get('config_language_id')];
				}
			}
			
			if(!empty($add1required)) {
				if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
					$json['error'][] = $allalbles['add1error'][$this->config->get('config_language_id')];
				}
			}
			if(!empty($cityrequired)) {
				if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
					$json['error'][] = $allalbles['cityerror'][$this->config->get('config_language_id')];
				}
			}

			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
			if(!empty($postcoderequired)) {
				if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
					$json['error'][] = $allalbles['postcoderror'][$this->config->get('config_language_id')];
				}
			}
			if(!empty($countryrequired)) {
				if ($this->request->post['country_id'] == '') {
					$json['error'][] = $allalbles['countryerror'][$this->config->get('config_language_id')];
				}
			}
			if(!empty($zonerequired)) {
				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
					$json['error'][] = $allalbles['zonerror'][$this->config->get('config_language_id')];
				}
			}
			if(!empty($pwdrequired)) {
				if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
					$json['error'][] = $allalbles['pwderror'][$this->config->get('config_language_id')];
				}
			}
			if(!empty($cpwdrequired)) {
				if ($this->request->post['confirm'] != $this->request->post['password']) {
					$json['error'][] = $allalbles['cpwderror'][$this->config->get('config_language_id')];
				}
			}
		}
		
		if(!$json){
			$json['customer']=$this->model_pos_pos->addCustomer($this->request->post);
			$json['success']='Success!';
		}
		$this->response->setOutput(json_encode($json));
	}
	
// Custom Fields End ///
	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
// Custom Fields End ///	
}
