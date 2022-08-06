<?php
class ControllerPossettingSetting extends Controller {
	private $error = array();

	public function index() {
///sharma for pos

		

		if(($this->cart->getCommissionPending() > $this->config->get('module_posapp_payment_limit')) &&  ($this->cart->getRegdiff() > $this->config->get('module_posapp_payment_trail_allowed_day') ) ){
			$this->load->language('extension/module/posapp');
			$this->session->data['success'] = sprintf($this->language->get('text_limit_over'), '<b>Limit is: ' . $this->currency->format($this->config->get('module_posapp_payment_limit'), $this->config->get('config_currency')).'</b>', 'But current due is: '.'<b>'.$this->currency->format($this->cart->getCommissionPending(), $this->config->get('config_currency')) . '</b>' ) ;
			$this->response->redirect($this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'], true));
		}
		$this->load->language('possetting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting'); 

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('setting', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('possetting/setting', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] 	= $this->language->get('heading_title');

		$data['text_edit'] 			= $this->language->get('text_edit');
		$data['text_enabled'] 		= $this->language->get('text_enabled');
		$data['text_disabled'] 		= $this->language->get('text_disabled');
		$data['text_select'] 		= $this->language->get('text_select');
		$data['text_yes'] 			= $this->language->get('text_yes');
		$data['text_no'] 			= $this->language->get('text_no');
		$data['text_horizontal'] 	= $this->language->get('text_horizontal');
		$data['text_vertical'] 		= $this->language->get('text_vertical');
		$data['text_none'] 			= $this->language->get('text_none');
		$data['text_upc'] 			= $this->language->get('text_upc');
		$data['text_sku'] 			= $this->language->get('text_sku');
		$data['text_shortformat'] 	= $this->language->get('text_shortformat');
		$data['text_longformat'] 	= $this->language->get('text_longformat');
		$data['text_guestnot'] 	    = $this->language->get('text_guestnot');
		$data['text_alwaysguest'] 	= $this->language->get('text_alwaysguest');

		$data['entry_status'] 			= $this->language->get('entry_status');
		$data['entry_fieldname'] 		= $this->language->get('entry_fieldname');
		$data['entry_label'] 			= $this->language->get('entry_label');
		$data['entry_error'] 			= $this->language->get('entry_error');
		$data['entry_required'] 		= $this->language->get('entry_required');
		$data['entry_sort'] 			= $this->language->get('entry_sort');
		$data['entry_firstname'] 		= $this->language->get('entry_firstname');
		$data['entry_firstname_missing']= $this->language->get('entry_firstname_missing');
		$data['entry_lastname'] 		= $this->language->get('entry_lastname');
		$data['entry_lastname_missing'] = $this->language->get('entry_lastname_missing');
		$data['entry_email'] 			= $this->language->get('entry_email');
		$data['entry_email_missing'] 	= $this->language->get('entry_email_missing');
		$data['entry_telephone'] 		= $this->language->get('entry_telephone');
		$data['entry_fax'] 				= $this->language->get('entry_fax');
		$data['entry_company'] 			= $this->language->get('entry_company');
		$data['entry_address1'] 		= $this->language->get('entry_address1');
		$data['entry_address2'] 		= $this->language->get('entry_address2');
		$data['entry_city'] 			= $this->language->get('entry_city');
		$data['entry_postcode'] 		= $this->language->get('entry_postcode');
		$data['entry_country'] 			= $this->language->get('entry_country');
		$data['entry_zone'] 			= $this->language->get('entry_zone');
		$data['entry_password'] 		= $this->language->get('entry_password');
		$data['entry_password_error'] 	= $this->language->get('entry_password_error');
		$data['entry_confirm_password'] = $this->language->get('entry_confirm_password');
		$data['entry_privacy'] 			= $this->language->get('entry_privacy');
		//$data['entry_title'] 			= $this->language->get('entry_title');
		$data['entry_submit_button'] 	= $this->language->get('entry_submit_button');
		$data['entry_email_warning'] 	= $this->language->get('entry_email_warning');
		//$data['entry_email_exists'] 	= $this->language->get('entry_email_exists');
		$data['entry_privacyautochk'] 	= $this->language->get('entry_privacyautochk');
		$data['entry_sucess'] 			= $this->language->get('entry_sucess');
		$data['entry_emailtemplate'] 	= $this->language->get('entry_emailtemplate');
		$data['entry_already'] 			= $this->language->get('entry_already');
		$data['entry_personal'] 		= $this->language->get('entry_personal');
		$data['entry_addtitle'] 		= $this->language->get('entry_addtitle');
		$data['entry_passtitle'] 		= $this->language->get('entry_passtitle');
		$data['entry_newsletter'] 		= $this->language->get('entry_newsletter');
		$data['entry_subscribe'] 		= $this->language->get('entry_subscribe');
		$data['entry_subject'] 			= $this->language->get('entry_subject');
		$data['entry_title'] 			= $this->language->get('entry_title');
		$data['entry_approvedcustomer'] = $this->language->get('entry_approvedcustomer');
		$data['entry_unapprovesuccess'] = $this->language->get('entry_unapprovesuccess');
		$data['entry_order_status'] 	= $this->language->get('entry_order_status');
		$data['entry_sortorder'] 		= $this->language->get('entry_sortorder');
		$data['entry_customer_group'] 	= $this->language->get('entry_customer_group');
		$data['entry_default_password'] = $this->language->get('entry_default_password');
		$data['entry_barcode'] 			= $this->language->get('entry_barcode');
		$data['entry_barcodetype'] 		= $this->language->get('entry_barcodetype');
		$data['entry_productbarcode'] 	= $this->language->get('entry_productbarcode');
		$data['entry_barcodeimage'] 	= $this->language->get('entry_barcodeimage');
		$data['entry_name'] 			= $this->language->get('entry_name');
		$data['entry_format'] 			= $this->language->get('entry_format');
		$data['entry_logo'] 			= $this->language->get('entry_logo');
		$data['entry_sname'] 			= $this->language->get('entry_sname');
		$data['entry_saddress'] 		= $this->language->get('entry_saddress');
		$data['entry_odate'] 			= $this->language->get('entry_odate');
		$data['entry_otime'] 			= $this->language->get('entry_otime');
		$data['entry_number'] 			= $this->language->get('entry_number');
		$data['entry_oid'] 	 			= $this->language->get('entry_oid');
		$data['entry_cname'] 			= $this->language->get('entry_cname');
		$data['entry_smode'] 			= $this->language->get('entry_smode');
		$data['entry_pmode'] 			= $this->language->get('entry_pmode');
		$data['entry_onote'] 			= $this->language->get('entry_onote');
		$data['entry_extra'] 			= $this->language->get('entry_extra');
		$data['entry_subject'] 			= $this->language->get('entry_subject');
		$data['entry_message'] 			= $this->language->get('entry_message');
		$data['entry_invoice'] 			= $this->language->get('entry_invoice');
		$data['entry_stelephone'] 		= $this->language->get('entry_stelephone');
		$data['entry_paysetting'] 	    = $this->language->get('entry_paysetting');
		$data['entry_defaultguest'] 	= $this->language->get('entry_defaultguest');
		$data['tab_diaplaysetting'] 			= $this->language->get('tab_diaplaysetting');

		$data['entry_text'] 			= $this->language->get('entry_text');
		$data['entry_textcolor']		= $this->language->get('entry_textcolor');
		$data['entry_bgcolor'] 			= $this->language->get('entry_bgcolor');
		$data['entry_method'] 			= $this->language->get('entry_method');
		$data['entry_icon'] 			= $this->language->get('entry_icon');
		$data['text_day'] 				= $this->language->get('text_day');
		$data['text_year'] 				= $this->language->get('text_year');
		$data['text_month'] 			= $this->language->get('text_month');
		$data['text_all'] 				= $this->language->get('text_all');
		$data['entry_daytype'] 			= $this->language->get('entry_daytype');
		$data['text_fullsize'] 			= $this->language->get('text_fullsize');
		$data['text_3mm'] 				= $this->language->get('text_3mm');
		$data['entry_invoicestatus'] 	= $this->language->get('entry_invoicestatus');
		$data['entry_customermail'] 	= $this->language->get('entry_customermail');
		$data['entry_ordermail'] 		= $this->language->get('entry_ordermail');

		$data['help_customer_group'] 	= $this->language->get('help_customer_group');
		$data['help_newsletter'] 		= $this->language->get('help_newsletter');
		$data['help_default_password'] 	= $this->language->get('help_default_password');
		$data['help_barcode'] 			= $this->language->get('help_barcode');

		$data['tab_customsetting']   	= $this->language->get('tab_customsetting');
		$data['tab_language'] 			= $this->language->get('tab_language');
		$data['tab_customersetting'] 	= $this->language->get('tab_customersetting');
		$data['tab_barcode'] 			= $this->language->get('tab_barcode');
		$data['tab_paymentmethod'] 		= $this->language->get('tab_paymentmethod');
		$data['tab_invoice'] 			= $this->language->get('tab_invoice');
		$data['tab_dashboard'] 			= $this->language->get('tab_dashboard');
		$data['tab_cmail'] 				= $this->language->get('tab_cmail');
		$data['tab_omail'] 				= $this->language->get('tab_omail');
		$data['tab_paysetting'] 		= $this->language->get('tab_paysetting');
		$data['button_add'] 			= $this->language->get('button_add');

		$data['button_save'] 			= $this->language->get('button_save');
		$data['button_cancel'] 			= $this->language->get('button_cancel');
		$data['button_discount_add'] 	= $this->language->get('button_discount_add');
		$data['button_remove'] 			= $this->language->get('button_remove');
		$data['user_token'] 					= $this->session->data['user_token'];

		$data['tab_generalsetting'] 			= $this->language->get('tab_generalsetting');
		$data['entry_logos'] 			= $this->language->get('entry_logos');
		$data['entry_logos_size'] 			= $this->language->get('entry_logos_size');
		
		$data['text_toadmin'] 			= $this->language->get('text_toadmin');
		$data['text_tocustomer'] 			= $this->language->get('text_tocustomer');


		if(isset($this->request->post['setting_logowidth'])){
			$data['setting_logowidth'] = $this->request->post['setting_logowidth'];
		}else{
			$data['setting_logowidth'] = $this->config->get('setting_logowidth');
		}

		if(isset($this->request->post['setting_logoheight'])){
			$data['setting_logoheight'] = $this->request->post['setting_logoheight'];
		}else{
			$data['setting_logoheight'] = $this->config->get('setting_logoheight');
		}

		if(isset($this->request->post['setting_display_customer'])){
			$data['setting_display_customer'] = $this->request->post['setting_display_customer'];
		}else{
			$data['setting_display_customer'] = $this->config->get('setting_display_customer');
		}

		if(isset($this->request->post['setting_filter_customer'])){
			$data['setting_filter_customer'] = $this->request->post['setting_filter_customer'];
		}else{
			$data['setting_filter_customer'] = $this->config->get('setting_filter_customer');
		}

		if (isset($this->request->post['setting_partial_payment'])) {
			$data['setting_partial_payment'] = $this->request->post['setting_partial_payment'];
		} else {
			$data['setting_partial_payment'] = $this->config->get('setting_partial_payment');
		}

		if(isset($this->request->post['setting_display_format'])){
			$data['setting_display_format'] = $this->request->post['setting_display_format'];
		}else{
			$data['setting_display_format'] = $this->config->get('setting_display_format');
		}

		if(isset($this->request->post['setting_display_customer_pro'])){
			$data['setting_display_customer_pro'] = $this->request->post['setting_display_customer_pro'];
		}else{
			$data['setting_display_customer_pro'] = $this->config->get('setting_display_customer_pro');
		}

		if(isset($this->request->post['setting_image'])){
			$data['setting_image'] = $this->request->post['setting_image'];
		}else{
			$data['setting_image'] = $this->config->get('setting_image');
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['setting_image']) && is_file(DIR_IMAGE . $this->request->post['setting_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['setting_image'], 100, 100);
		} elseif (!empty($this->config->get('setting_image')) && is_file(DIR_IMAGE . $this->config->get('setting_image'))) {
			$data['thumb'] = $this->model_tool_image->resize($this->config->get('setting_image'), 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['entry_splitpayment'] = $this->language->get('entry_splitpayment');
		if (isset($this->request->post['setting_splitpayment'])) {
			$data['setting_splitpayment'] = $this->request->post['setting_splitpayment'];
		} else {
			$data['setting_splitpayment'] = $this->config->get('setting_splitpayment');
		}

		$setting_return = $this->config->get('setting_return');		 
		if (isset($this->request->post['setting_return'])) {
			$data['setting_returns'] = $this->request->post['setting_return'];
		} elseif ($setting_return) {
			$data['setting_returns'] = $this->config->get('setting_return');
		} else {
			$data['setting_returns'] = array();
		} 

		
	///new code 13 june 2020 //

	//17 june 2020
		if (isset($this->request->post['setting_themechoose'])) {
			$data['setting_themechoose'] = $this->request->post['setting_themechoose'];
		} else {
			$data['setting_themechoose'] = $this->config->get('setting_themechoose');
		}

		if(isset($this->request->post['setting_bodycolor'])){
			$data['setting_bodycolor'] = $this->request->post['setting_bodycolor'];
		}else{
			$data['setting_bodycolor'] = $this->config->get('setting_bodycolor');
		}

		if(isset($this->request->post['setting_maincolor'])){
			$data['setting_maincolor'] = $this->request->post['setting_maincolor'];
		}else{
			$data['setting_maincolor'] = $this->config->get('setting_maincolor');
		}

		if(isset($this->request->post['setting_maincolortxt'])){
			$data['setting_maincolortxt'] = $this->request->post['setting_maincolortxt'];
		}else{
			$data['setting_maincolortxt'] = $this->config->get('setting_maincolortxt');
		}

		if(isset($this->request->post['setting_topbar'])){
			$data['setting_topbar'] = $this->request->post['setting_topbar'];
		}else{
			$data['setting_topbar'] = $this->config->get('setting_topbar');
		}

		if(isset($this->request->post['setting_topbartext'])){
			$data['setting_topbartext'] = $this->request->post['setting_topbartext'];
		}else{
			$data['setting_topbartext'] = $this->config->get('setting_topbartext');
		}

		if(isset($this->request->post['setting_boxbgcolor'])){
			$data['setting_boxbgcolor'] = $this->request->post['setting_boxbgcolor'];
		}else{
			$data['setting_boxbgcolor'] = $this->config->get('setting_boxbgcolor');
		}

		if(isset($this->request->post['setting_tablebgcolor'])){
			$data['setting_tablebgcolor'] = $this->request->post['setting_tablebgcolor'];
		}else{
			$data['setting_tablebgcolor'] = $this->config->get('setting_tablebgcolor');
		}

		if(isset($this->request->post['setting_tabletxtcolor'])){
			$data['setting_tabletxtcolor'] = $this->request->post['setting_tabletxtcolor'];
		}else{
			$data['setting_tabletxtcolor'] = $this->config->get('setting_tabletxtcolor');
		}

		if(isset($this->request->post['setting_probgcolor'])){
			$data['setting_probgcolor'] = $this->request->post['setting_probgcolor'];
		}else{
			$data['setting_probgcolor'] = $this->config->get('setting_probgcolor');
		}

		if(isset($this->request->post['setting_clearcartbg'])){
			$data['setting_clearcartbg'] = $this->request->post['setting_clearcartbg'];
		}else{
			$data['setting_clearcartbg'] = $this->config->get('setting_clearcartbg');
		}

		if(isset($this->request->post['setting_clearcart'])){
			$data['setting_clearcart'] = $this->request->post['setting_clearcart'];
		}else{
			$data['setting_clearcart'] = $this->config->get('setting_clearcart');
		}

		if(isset($this->request->post['setting_btnscolorbg'])){
			$data['setting_btnscolorbg'] = $this->request->post['setting_btnscolorbg'];
		}else{
			$data['setting_btnscolorbg'] = $this->config->get('setting_btnscolorbg');
		}

		if(isset($this->request->post['setting_btnscolor'])){
			$data['setting_btnscolor'] = $this->request->post['setting_btnscolor'];
		}else{
			$data['setting_btnscolor'] = $this->config->get('setting_btnscolor');
		}

		if(isset($this->request->post['setting_topbartext'])){
			$data['setting_topbartext'] = $this->request->post['setting_topbartext'];
		}else{
			$data['setting_topbartext'] = $this->config->get('setting_topbartext');
		}

		if(isset($this->request->post['setting_topbartext'])){
			$data['setting_topbartext'] = $this->request->post['setting_topbartext'];
		}else{
			$data['setting_topbartext'] = $this->config->get('setting_topbartext');
		}

		if(isset($this->request->post['setting_topbartext'])){
			$data['setting_topbartext'] = $this->request->post['setting_topbartext'];
		}else{
			$data['setting_topbartext'] = $this->config->get('setting_topbartext');
		}

		if(isset($this->request->post['setting_topbartext'])){
			$data['setting_topbartext'] = $this->request->post['setting_topbartext'];
		}else{
			$data['setting_topbartext'] = $this->config->get('setting_topbartext');
		}

		if(isset($this->request->post['setting_topbartext'])){
			$data['setting_topbartext'] = $this->request->post['setting_topbartext'];
		}else{
			$data['setting_topbartext'] = $this->config->get('setting_topbartext');
		}

		if(isset($this->request->post['setting_topbartext'])){
			$data['setting_topbartext'] = $this->request->post['setting_topbartext'];
		}else{
			$data['setting_topbartext'] = $this->config->get('setting_topbartext');
		}

		if(isset($this->request->post['setting_topbartext'])){
			$data['setting_topbartext'] = $this->request->post['setting_topbartext'];
		}else{
			$data['setting_topbartext'] = $this->config->get('setting_topbartext');
		}
	//17 june 2020

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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title1'),
			'href' => $this->url->link('possetting/setting', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['barcodes'] = array();

		$data['barcodes'][] = array(
			'type'     		=> $this->language->get('text_upc'),
			'value' 		=> 'UPC'
		);

		$data['barcodes'][] = array(
			'type'     		=> $this->language->get('text_sku'),
			'value' 		=> 'Sku'
		);

		$data['formats'] = array();

		$data['formats'][] = array(
			'format'     		=> $this->language->get('text_shortformat'),
			'value' 		=> 'Short Format'
		);

		$data['formats'][] = array(
			'format'     		=> $this->language->get('text_longformat'),
			'value' 		=> 'Long Format'
		);

		$data['daytypes'] = array();

		$data['daytypes'][] = array(
			'daytype'     	=> $this->language->get('text_day'),
			'value' 		=> '1 Day'
		);

		$data['daytypes'][] = array(
			'daytype'     	=> $this->language->get('text_month'),
			'value' 		=> '1 Month'
		);

		$data['daytypes'][] = array(
			'daytype'     	=> $this->language->get('text_year'),
			'value' 		=> '1 Year'
		);

		$data['daytypes'][] = array(
			'daytype'     	=> $this->language->get('text_all'),
			'value' 		=> 'All'
		);

		$data['action'] = $this->url->link('possetting/setting', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('localisation/order_status');
		$data['order_statuss'] = $this->model_localisation_order_status->getOrderStatuses($data);

		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

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

		/*if (isset($this->request->post['setting_zone_id'])) {
			$data['setting_zone_id'] = (int)$this->request->post['setting_zone_id'];
		} elseif (isset($this->session->data['shipping_address']['setting_zone_id'])) {
			$data['setting_zone_id'] = $this->session->data['shipping_address']['setting_zone_id'];
		} else {
			$data['setting_zone_id'] = '';
		}*/

		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

// Register manager Tab Starts ///
		if(isset($this->request->post['setting_firstname'])){
			$data['setting_firstname'] = $this->request->post['setting_firstname'];
		}else{
			$data['setting_firstname'] = $this->config->get('setting_firstname');
		}

		if(isset($this->request->post['setting_settings'])){
			$data['setting_settings'] = $this->request->post['setting_settings'];
		}else{
			$data['setting_settings'] = $this->config->get('setting_settings');
		}

		if(isset($this->request->post['setting_status'])){
			$data['setting_status'] = $this->request->post['setting_status'];
		}else{
			$data['setting_status'] = $this->config->get('setting_status');
		}

		//Setting Firstname

		if(isset($this->request->post['setting_fnamerequired'])){
			$data['setting_fnamerequired'] = $this->request->post['setting_fnamerequired'];
		}else{
			$data['setting_fnamerequired'] = $this->config->get('setting_fnamerequired');
		}

		if(isset($this->request->post['setting_fnamesortorder'])){
			$data['setting_fnamesortorder'] = $this->request->post['setting_fnamesortorder'];
		}else{
			$data['setting_fnamesortorder'] = $this->config->get('setting_fnamesortorder');
		}

		if(isset($this->request->post['setting_fnamestatus'])){
			$data['setting_fnamestatus'] = $this->request->post['setting_fnamestatus'];
		}else{
			$data['setting_fnamestatus'] = $this->config->get('setting_fnamestatus');
		}

		//Setting LastName

		if(isset($this->request->post['setting_lastnamerequired'])){
			$data['setting_lastnamerequired'] = $this->request->post['setting_lastnamerequired'];
		}else{
			$data['setting_lastnamerequired'] = $this->config->get('setting_lastnamerequired');
		}

		if(isset($this->request->post['setting_lastnamesortorder'])){
			$data['setting_lastnamesortorder'] = $this->request->post['setting_lastnamesortorder'];
		}else{
			$data['setting_lastnamesortorder'] = $this->config->get('setting_lastnamesortorder');
		}

		if(isset($this->request->post['setting_lastnamestatus'])){
			$data['setting_lastnamestatus'] = $this->request->post['setting_lastnamestatus'];
		}else{
			$data['setting_lastnamestatus'] = $this->config->get('setting_lastnamestatus');
		}

		//Setting E-mail Address

		if(isset($this->request->post['setting_emailrequired'])){
			$data['setting_emailrequired'] = $this->request->post['setting_emailrequired'];
		}else{
			$data['setting_emailrequired'] = $this->config->get('setting_emailrequired');
		}

		if(isset($this->request->post['setting_emailsortorder'])){
			$data['setting_emailsortorder'] = $this->request->post['setting_emailsortorder'];
		}else{
			$data['setting_emailsortorder'] = $this->config->get('setting_emailsortorder');
		}

		if(isset($this->request->post['setting_emailstatus'])){
			$data['setting_emailstatus'] = $this->request->post['setting_emailstatus'];
		}else{
			$data['setting_emailstatus'] = $this->config->get('setting_emailstatus');
		}

		//Setting Telephone

		if(isset($this->request->post['setting_phonerequired'])){
			$data['setting_phonerequired'] = $this->request->post['setting_phonerequired'];
		}else{
			$data['setting_phonerequired'] = $this->config->get('setting_phonerequired');
		}

		if(isset($this->request->post['setting_phonesortorder'])){
			$data['setting_phonesortorder'] = $this->request->post['setting_phonesortorder'];
		}else{
			$data['setting_phonesortorder'] = $this->config->get('setting_phonesortorder');
		}

		if(isset($this->request->post['setting_phonestatus'])){
			$data['setting_phonestatus'] = $this->request->post['setting_phonestatus'];
		}else{
			$data['setting_phonestatus'] = $this->config->get('setting_phonestatus');
		}

		//Setting Fax

		if(isset($this->request->post['setting_faxrequired'])){
			$data['setting_faxrequired'] = $this->request->post['setting_faxrequired'];
		}else{
			$data['setting_faxrequired'] = $this->config->get('setting_faxrequired');
		}

		if(isset($this->request->post['setting_faxsortorder'])){
			$data['setting_faxsortorder'] = $this->request->post['setting_faxsortorder'];
		}else{
			$data['setting_faxsortorder'] = $this->config->get('setting_faxsortorder');
		}

		if(isset($this->request->post['setting_faxstatus'])){
			$data['setting_faxstatus'] = $this->request->post['setting_faxstatus'];
		}else{
			$data['setting_faxstatus'] = $this->config->get('setting_faxstatus');
		}

		//Setting Company

		if(isset($this->request->post['setting_compquired'])){
			$data['setting_compquired'] = $this->request->post['setting_compquired'];
		}else{
			$data['setting_compquired'] = $this->config->get('setting_compquired');
		}

		if(isset($this->request->post['setting_compsortorder'])){
			$data['setting_compsortorder'] = $this->request->post['setting_compsortorder'];
		}else{
			$data['setting_compsortorder'] = $this->config->get('setting_compsortorder');
		}

		if(isset($this->request->post['setting_compstatus'])){
			$data['setting_compstatus'] = $this->request->post['setting_compstatus'];
		}else{
			$data['setting_compstatus'] = $this->config->get('setting_compstatus');
		}

		//Setting Address 1

		if(isset($this->request->post['setting_add1required'])){
			$data['setting_add1required'] = $this->request->post['setting_add1required'];
		}else{
			$data['setting_add1required'] = $this->config->get('setting_add1required');
		}

		if(isset($this->request->post['setting_add1sortorder'])){
			$data['setting_add1sortorder'] = $this->request->post['setting_add1sortorder'];
		}else{
			$data['setting_add1sortorder'] = $this->config->get('setting_add1sortorder');
		}

		if(isset($this->request->post['setting_add1status'])){
			$data['setting_add1status'] = $this->request->post['setting_add1status'];
		}else{
			$data['setting_add1status'] = $this->config->get('setting_add1status');
		}

		//Setting Address 2

		if(isset($this->request->post['setting_add2required'])){
			$data['setting_add2required'] = $this->request->post['setting_add2required'];
		}else{
			$data['setting_add2required'] = $this->config->get('setting_add2required');
		}

		if(isset($this->request->post['setting_add2sortorder'])){
			$data['setting_add2sortorder'] = $this->request->post['setting_add2sortorder'];
		}else{
			$data['setting_add2sortorder'] = $this->config->get('setting_add2sortorder');
		}

		if(isset($this->request->post['setting_add2status'])){
			$data['setting_add2status'] = $this->request->post['setting_add2status'];
		}else{
			$data['setting_add2status'] = $this->config->get('setting_add2status');
		}

		//Setting City

		if(isset($this->request->post['setting_cityrequired'])){
			$data['setting_cityrequired'] = $this->request->post['setting_cityrequired'];
		}else{
			$data['setting_cityrequired'] = $this->config->get('setting_cityrequired');
		}

		if(isset($this->request->post['setting_citysortorder'])){
			$data['setting_citysortorder'] = $this->request->post['setting_citysortorder'];
		}else{
			$data['setting_citysortorder'] = $this->config->get('setting_citysortorder');
		}

		if(isset($this->request->post['setting_citystatus'])){
			$data['setting_citystatus'] = $this->request->post['setting_citystatus'];
		}else{
			$data['setting_citystatus'] = $this->config->get('setting_citystatus');
		}

		//Setting Postcode

		if(isset($this->request->post['setting_postcodrequired'])){
			$data['setting_postcodrequired'] = $this->request->post['setting_postcodrequired'];
		}else{
			$data['setting_postcodrequired'] = $this->config->get('setting_postcodrequired');
		}

		if(isset($this->request->post['setting_postcodsortorder'])){
			$data['setting_postcodsortorder'] = $this->request->post['setting_postcodsortorder'];
		}else{
			$data['setting_postcodsortorder'] = $this->config->get('setting_postcodsortorder');
		}

		if(isset($this->request->post['setting_postcodstatus'])){
			$data['setting_postcodstatus'] = $this->request->post['setting_postcodstatus'];
		}else{
			$data['setting_postcodstatus'] = $this->config->get('setting_postcodstatus');
		}

		//Setting Country

		if(isset($this->request->post['setting_countryrequired'])){
			$data['setting_countryrequired'] = $this->request->post['setting_countryrequired'];
		}else{
			$data['setting_countryrequired'] = $this->config->get('setting_countryrequired');
		}

		if(isset($this->request->post['setting_countrysortorder'])){
			$data['setting_countrysortorder'] = $this->request->post['setting_countrysortorder'];
		}else{
			$data['setting_countrysortorder'] = $this->config->get('setting_countrysortorder');
		}

		if(isset($this->request->post['setting_countrystatus'])){
			$data['setting_countrystatus'] = $this->request->post['setting_countrystatus'];
		}else{
			$data['setting_countrystatus'] = $this->config->get('setting_countrystatus');
		}

		//Setting Zone

		if(isset($this->request->post['setting_zonerequired'])){
			$data['setting_zonerequired'] = $this->request->post['setting_zonerequired'];
		}else{
			$data['setting_zonerequired'] = $this->config->get('setting_zonerequired');
		}

		if(isset($this->request->post['setting_zonesortorder'])){
			$data['setting_zonesortorder'] = $this->request->post['setting_zonesortorder'];
		}else{
			$data['setting_zonesortorder'] = $this->config->get('setting_zonesortorder');
		}

		if(isset($this->request->post['setting_zonestatus'])){
			$data['setting_zonestatus'] = $this->request->post['setting_zonestatus'];
		}else{
			$data['setting_zonestatus'] = $this->config->get('setting_zonestatus');
		}

		//Setting Password

		if(isset($this->request->post['setting_pwdrequired'])){
			$data['setting_pwdrequired'] = $this->request->post['setting_pwdrequired'];
		}else{
			$data['setting_pwdrequired'] = $this->config->get('setting_pwdrequired');
		}

		if(isset($this->request->post['setting_pwdsortorder'])){
			$data['setting_pwdsortorder'] = $this->request->post['setting_pwdsortorder'];
		}else{
			$data['setting_pwdsortorder'] = $this->config->get('setting_pwdsortorder');
		}

		if(isset($this->request->post['setting_pwdstatus'])){
			$data['setting_pwdstatus'] = $this->request->post['setting_pwdstatus'];
		}else{
			$data['setting_pwdstatus'] = $this->config->get('setting_pwdstatus');
		}

		//Setting Confirm Password

		if(isset($this->request->post['setting_cpwdrequired'])){
			$data['setting_cpwdrequired'] = $this->request->post['setting_cpwdrequired'];
		}else{
			$data['setting_cpwdrequired'] = $this->config->get('setting_cpwdrequired');
		}

		if(isset($this->request->post['setting_cpwdsortorder'])){
			$data['setting_cpwdsortorder'] = $this->request->post['setting_cpwdsortorder'];
		}else{
			$data['setting_cpwdsortorder'] = $this->config->get('setting_cpwdsortorder');
		}

		if(isset($this->request->post['setting_cpwdstatus'])){
			$data['setting_cpwdstatus'] = $this->request->post['setting_cpwdstatus'];
		}else{
			$data['setting_cpwdstatus'] = $this->config->get('setting_cpwdstatus');
		}

// Register manager Tab Ends ///

//Customer Set Tab Start //
		if (isset($this->request->post['setting_customer_group_id'])) {
			$data['setting_customer_group_id'] = $this->request->post['setting_customer_group_id'];
		} else {
			$data['setting_customer_group_id'] = $this->config->get('setting_customer_group_id');
		}

		if (isset($this->request->post['setting_newsletter'])) {
			$data['setting_newsletter'] = $this->request->post['setting_newsletter'];
		} else {
			$data['setting_newsletter'] = $this->config->get('setting_newsletter');
		}

		if (isset($this->request->post['setting_customer_group'])) {
			$data['setting_customer_group'] = $this->request->post['setting_customer_group'];
		} else {
			$data['setting_customer_group'] = $this->config->get('setting_customer_group');
		}

		if (isset($this->request->post['setting_default_password'])) {
			$data['setting_default_password'] = $this->request->post['setting_default_password'];
		} else {
			$data['setting_default_password'] = $this->config->get('setting_default_password');
		}

		if (isset($this->request->post['setting_lastname'])) {
			$data['setting_lastname'] = $this->request->post['setting_lastname'];
		} else {
			$data['setting_lastname'] = $this->config->get('setting_lastname');
		}

		if (isset($this->request->post['setting_email'])) {
			$data['setting_email'] = $this->request->post['setting_email'];
		} else {
			$data['setting_email'] = $this->config->get('setting_email');
		}

		if (isset($this->request->post['setting_telephone'])) {
			$data['setting_telephone'] = $this->request->post['setting_telephone'];
		} else {
			$data['setting_telephone'] = $this->config->get('setting_telephone');
		}

		if (isset($this->request->post['setting_fax'])) {
			$data['setting_fax'] = $this->request->post['setting_fax'];
		} else {
			$data['setting_fax'] = $this->config->get('setting_fax');
		}

		if (isset($this->request->post['setting_company'])) {
			$data['setting_company'] = $this->request->post['setting_company'];
		} else {
			$data['setting_company'] = $this->config->get('setting_company');
		}

		if (isset($this->request->post['setting_address1'])) {
			$data['setting_address1'] = $this->request->post['setting_address1'];
		} else {
			$data['setting_address1'] = $this->config->get('setting_address1');
		}

		if (isset($this->request->post['setting_address2'])) {
			$data['setting_address2'] = $this->request->post['setting_address2'];
		} else {
			$data['setting_address2'] = $this->config->get('setting_address2');
		}

		if (isset($this->request->post['setting_city'])) {
			$data['setting_city'] = $this->request->post['setting_city'];
		} else {
			$data['setting_city'] = $this->config->get('setting_city');
		}

		if (isset($this->request->post['setting_type'])) {
			$data['setting_type'] = $this->request->post['setting_type'];
		} else {
			$data['setting_type'] = $this->config->get('setting_type');
		}

		if (isset($this->request->post['setting_postcode'])) {
			$data['setting_postcode'] = $this->request->post['setting_postcode'];
		} else {
			$data['setting_postcode'] = $this->config->get('setting_postcode');
		}

		if (isset($this->request->post['setting_country_id'])) {
			$data['setting_country_id'] = $this->request->post['setting_country_id'];
		} else {
			$data['setting_country_id'] = $this->config->get('setting_country_id');
		}

		if (isset($this->request->post['setting_zone_id'])) {
			$data['setting_zone_id'] = $this->request->post['setting_zone_id'];
		} else {
			$data['setting_zone_id'] = $this->config->get('setting_zone_id');
		}
		//print_r($data['setting_zone_id']);die();

//Customer Set Tab End //

//Barcode Set Tab Starts //

		if (isset($this->request->post['setting_barcode'])) {
			$data['setting_barcode'] = $this->request->post['setting_barcode'];
		} else {
			$data['setting_barcode'] = $this->config->get('setting_barcode');
		}

		if (isset($this->request->post['setting_barcode_product'])) {
			$data['setting_barcode_product'] = $this->request->post['setting_barcode_product'];
		} else {
			$data['setting_barcode_product'] = $this->config->get('setting_barcode_product');
		}

		if (isset($this->request->post['setting_barcodeimage'])) {
			$data['setting_barcodeimage'] = $this->request->post['setting_barcodeimage'];
		} else {
			$data['setting_barcodeimage'] = $this->config->get('setting_barcodeimage');
		}
//Barcode Set Tab End //

//Payment Method Tab Start //

		if (isset($this->request->post['setting_paymentmethod'])) {
			$setting_paymentmethods = $this->request->post['setting_paymentmethod'];
		} else {
			$setting_paymentmethods = $this->config->get('setting_paymentmethod');
		}
		//echo "<pre>";
		//print_r($setting_paymentmethods);die();
		$data['setting_paymentmethods'] = array();

		if(is_array($setting_paymentmethods)) {
			foreach ($setting_paymentmethods as $setting_paymentmethod) {
				$data['setting_paymentmethods'][] = array(
					'name' 	 			=> $setting_paymentmethod['name'],
					'order_status_id'	=> $setting_paymentmethod['order_status_id'],
					'sortorder'      	=> $setting_paymentmethod['sortorder'],
				);

			}
		}
//Payment Method Tab End //

//Invoice Tab Start //

		if (isset($this->request->post['setting_format'])) {
			$data['setting_format'] = $this->request->post['setting_format'];
		} else {
			$data['setting_format'] = $this->config->get('setting_format');
		}

		if (isset($this->request->post['setting_store_logo'])) {
			$data['setting_store_logo'] = $this->request->post['setting_store_logo'];
		} else {
			$data['setting_store_logo'] = $this->config->get('setting_store_logo');
		}

		if (isset($this->request->post['setting_store_name'])) {
			$data['setting_store_name'] = $this->request->post['setting_store_name'];
		} else {
			$data['setting_store_name'] = $this->config->get('setting_store_name');
		}

		if (isset($this->request->post['setting_store_address'])) {
			$data['setting_store_address'] = $this->request->post['setting_store_address'];
		} else {
			$data['setting_store_address'] = $this->config->get('setting_store_address');
		}

		if (isset($this->request->post['setting_store_telephone'])) {
			$data['setting_store_telephone'] = $this->request->post['setting_store_telephone'];
		} else {
			$data['setting_store_telephone'] = $this->config->get('setting_store_telephone');
		}

		if (isset($this->request->post['setting_order_date'])) {
			$data['setting_order_date'] = $this->request->post['setting_order_date'];
		} else {
			$data['setting_order_date'] = $this->config->get('setting_order_date');
		}

		if (isset($this->request->post['setting_order_time'])) {
			$data['setting_order_time'] = $this->request->post['setting_order_time'];
		} else {
			$data['setting_order_time'] = $this->config->get('setting_order_time');
		}

		if (isset($this->request->post['setting_invoice_number'])) {
			$data['setting_invoice_number'] = $this->request->post['setting_invoice_number'];
		} else {
			$data['setting_invoice_number'] = $this->config->get('setting_invoice_number');
		}

		if (isset($this->request->post['setting_cashier_name'])) {
			$data['setting_cashier_name'] = $this->request->post['setting_cashier_name'];
		} else {
			$data['setting_cashier_name'] = $this->config->get('setting_cashier_name');
		}

		if (isset($this->request->post['setting_shipping_mode'])) {
			$data['setting_shipping_mode'] = $this->request->post['setting_shipping_mode'];
		} else {
			$data['setting_shipping_mode'] = $this->config->get('setting_shipping_mode');
		}

		if (isset($this->request->post['setting_payment_mode'])) {
			$data['setting_payment_mode'] = $this->request->post['setting_payment_mode'];
		} else {
			$data['setting_payment_mode'] = $this->config->get('setting_payment_mode');
		}

		if (isset($this->request->post['setting_order_note'])) {
			$data['setting_order_note'] = $this->request->post['setting_order_note'];
		} else {
			$data['setting_order_note'] = $this->config->get('setting_order_note');
		}

		if (isset($this->request->post['setting_extra'])) {
			$data['setting_extra'] = $this->request->post['setting_extra'];
		} else {
			$data['setting_extra'] = $this->config->get('setting_extra');
		}

		if (isset($this->request->post['setting_invoice'])) {
			$data['setting_invoice'] = $this->request->post['setting_invoice'];
		} else {
			$data['setting_invoice'] = $this->config->get('setting_invoice');
		}


//Invoice Tab End //

//Dashboard Tab End //

		if (isset($this->request->post['setting_dashboard'])) {
			$setting_dashboards = $this->request->post['setting_dashboard'];
		} else {
			$setting_dashboards = $this->config->get('setting_dashboard');
		}

		$this->load->model('tool/image');
		$data['setting_dashboards'] = array();
		if(is_array($setting_dashboards)) {
			foreach($setting_dashboards as $setting_dashboard){
				/*if (is_file(DIR_IMAGE . $setting_dashboard['icon'])) {
					$icon = $setting_dashboard['icon'];
					$thumb_icon = $setting_dashboard['icon'];
				} else {
					$icon = '';
					$thumb_icon = 'no_image.png';
				}*/

				$orderstatus_data=array();
				if (isset($setting_dashboard['dashboard_orderstatus'])) {
					foreach ($setting_dashboard['dashboard_orderstatus'] as $dashboard_orderstatus) {
						$orderstatus_data[]=$dashboard_orderstatus['order_status_id'];
					}
				}

				$payment_method_data=array();
				if (isset($setting_dashboard['dashboard_paymentmethod'])) {
					foreach ($setting_dashboard['dashboard_paymentmethod'] as $dashboard_paymentmethod) {
						$payment_method_data[]=$dashboard_paymentmethod['method'];
					}
				}
				////sharma for pos
				$custom_figure=array();
				if (isset($setting_dashboard['dashboard_custom_figure'])) {
					foreach ($setting_dashboard['dashboard_custom_figure'] as $dashboard_custom_figure) {
						$custom_figure[]=$dashboard_custom_figure['figures'];
					}
				}
				////sharma for pos
				$data['setting_dashboards'][] = array(
					'name' 			=> $setting_dashboard['name'],
					'sort_order' 	=> $setting_dashboard['sort_order'],
					'daytype' 		=> $setting_dashboard['daytype'],
					'dashboard_status' => $setting_dashboard['dashboard_status'],
					'text_color' 	=> $setting_dashboard['text_color'],
					'bg_color' 		=> $setting_dashboard['bg_color'],
					'icon' 			=> $setting_dashboard['icon'],
					//'icon'      	=> $icon,
					////sharma for pos
					'dashboard_custom_figure'=> $custom_figure,
					////sharma for pos
					'dashboard_paymentmethod'=> $payment_method_data,
					'dashboard_orderstatus'=> $orderstatus_data,
					//'thumb_icon'     => $this->model_tool_image->resize($thumb_icon, 100, 100),
				); 
			}
		}
		////sharma for pos
		$data['custom_figuresdata'] = array();
		
		$data['custom_figuresdata'][] = array(
			        'name' 				=> 'Online Sale',
					'value' 				=> 'online_sale',					
					'sortorder' 		=> 1,
				);
		$data['custom_figuresdata'][] = array(
					'name' 				=> 'POS Sale',
					'value' 				=> 'pos_sale',					
					'sortorder' 		=> 2,
				);
		
		////sharma for pos
		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('localisation/order_status');
		$data['orderstatuss'] = $this->model_localisation_order_status->getOrderStatuses($data);

		if (isset($this->request->post['setting_paymentmethod'])) {
			$paymentmethods = $this->request->post['setting_paymentmethod'];
		} else {
			$paymentmethods = $this->config->get('setting_paymentmethod');
		}
		$data['paymentmethods'] = array();
		if(is_array($paymentmethods)) {
			foreach ($paymentmethods as $paymentmethod) {
				$data['paymentmethods'][] = array(
					'name' 				=> $paymentmethod['name'],
					'order_status_id' 	=> $setting_paymentmethod['order_status_id'],
					'sortorder' 		=> $setting_paymentmethod['sortorder'],
				);
			}
		}
//Dashboard Tab End //

//Customer Mail Tab Start //

		if (isset($this->request->post['setting_customers_subject'])) {
			$data['setting_customers_subject'] = $this->request->post['setting_customers_subject'];
		} else {
			$data['setting_customers_subject'] = $this->config->get('setting_customers_subject');
		}

		if (isset($this->request->post['setting_customers_message'])) {
			$data['setting_customers_message'] = $this->request->post['setting_customers_message'];
		} else {
			$data['setting_customers_message'] = $this->config->get('setting_customers_message');
		}

		if (isset($this->request->post['setting_customers_subject_admin'])) {
			$data['setting_customers_subject_admin'] = $this->request->post['setting_customers_subject_admin'];
		} else {
			$data['setting_customers_subject_admin'] = $this->config->get('setting_customers_subject_admin');
		}

		if (isset($this->request->post['setting_customers_message_admin'])) {
			$data['setting_customers_message_admin'] = $this->request->post['setting_customers_message_admin'];
		} else {
			$data['setting_customers_message_admin'] = $this->config->get('setting_customers_message_admin');
		}

//Customer Mail Tab End //

//Order Mail Tab Start //

		if (isset($this->request->post['setting_orders_subject'])) {
			$data['setting_orders_subject'] = $this->request->post['setting_orders_subject'];
		} else {
			$data['setting_orders_subject'] = $this->config->get('setting_orders_subject');
		}

		if (isset($this->request->post['setting_orders_message'])) {
			$data['setting_orders_message'] = $this->request->post['setting_orders_message'];
		} else {
			$data['setting_orders_message'] = $this->config->get('setting_orders_message');
		}

		if (isset($this->request->post['setting_orders_subject_admin'])) {
			$data['setting_orders_subject_admin'] = $this->request->post['setting_orders_subject_admin'];
		} else {
			$data['setting_orders_subject_admin'] = $this->config->get('setting_orders_subject_admin');
		}

		if (isset($this->request->post['setting_orders_message_admin'])) {
			$data['setting_orders_message_admin'] = $this->request->post['setting_orders_message_admin'];
		} else {
			$data['setting_orders_message_admin'] = $this->config->get('setting_orders_message_admin');
		}


//Order Mail Tab End //


//Pay Now Setting Tab Start //

		if (isset($this->request->post['setting_defult_guest'])) {
			$data['setting_defult_guest'] = $this->request->post['setting_defult_guest'];
		} else {
			$data['setting_defult_guest'] = $this->config->get('setting_defult_guest');
		}

		if (isset($this->request->post['setting_default_paymentmethod'])) {
			$data['setting_default_paymentmethod'] = $this->request->post['setting_default_paymentmethod'];
		} else {
			$data['setting_default_paymentmethod'] = $this->config->get('setting_default_paymentmethod');
		}

		if (isset($this->request->post['setting_customermail'])) {
			$data['setting_customermail'] = $this->request->post['setting_customermail'];
		} else {
			$data['setting_customermail'] = $this->config->get('setting_customermail');
		}

		if (isset($this->request->post['setting_ordermail'])) {
			$data['setting_ordermail'] = $this->request->post['setting_ordermail'];
		} else {
			$data['setting_ordermail'] = $this->config->get('setting_ordermail');
		}

		$data['entry_splitpayment'] = $this->language->get('entry_splitpayment');
		if (isset($this->request->post['setting_splitpayment'])) {
			$data['setting_splitpayment'] = $this->request->post['setting_splitpayment'];
		} else {
			$data['setting_splitpayment'] = $this->config->get('setting_splitpayment');
		}
//Pay Now setting Mail Tab End //

		/* 24 09 2019 */
		$data['tab_barcode'] 			= $this->language->get('tab_barcode');
		$data['text_upc'] 				= $this->language->get('text_upc');
		$data['text_isbn'] 				= $this->language->get('text_isbn');
		$data['text_proname'] 			= $this->language->get('text_proname');

		$data['entry_size'] 			= $this->language->get('entry_size');
		$data['entry_height'] 			= $this->language->get('entry_height');
		$data['entry_width'] 			= $this->language->get('entry_width');
		$data['entry_usecode'] 			= $this->language->get('entry_usecode');
		$data['entry_showname'] 		= $this->language->get('entry_showname');
		$data['entry_showprice'] 		= $this->language->get('entry_showprice');
		$data['entry_pagesize'] 		= $this->language->get('entry_pagesize');
		$data['entry_labelsize'] 		= $this->language->get('entry_labelsize');
		$data['entry_labelqty'] 		= $this->language->get('entry_labelqty');
		$data['entry_pagelimit'] 		= $this->language->get('entry_pagelimit');
		$data['entry_imagesize'] 		= $this->language->get('entry_imagesize');

		$data['barcodes'] = array();

		$data['barcodes'][] = array(
			'text'     		=> $this->language->get('text_upc'),
			'value' 		=> 'upc'
		);
		$data['barcodes'][] = array(
			'text'     		=> $this->language->get('text_isbn'),
			'value' 		=> 'sku'
		);
		$data['barcodes'][] = array(
			'text'     		=> $this->language->get('text_proname'),
			'value' 		=> 'name'
		);
		$data['barcodes'][] = array(
			'text'     		=> $this->language->get('text_model'),
			'value' 		=> 'model'
		);
		$data['barcodes'][] = array(
			'text'     		=> $this->language->get('text_description'),
			'value' 		=> 'description'
		);

		if (isset($this->request->post['setting_barcodeheight'])) {
			$data['setting_barcodeheight'] = $this->request->post['setting_barcodeheight'];
		} else {
			$data['setting_barcodeheight'] = $this->config->get('setting_barcodeheight');
		}

		if (isset($this->request->post['setting_barcodewidth'])) {
			$data['setting_barcodewidth'] = $this->request->post['setting_barcodewidth'];
		} else {
			$data['setting_barcodewidth'] = $this->config->get('setting_barcodewidth');
		}

		if (isset($this->request->post['setting_usecode'])) {
			$data['setting_usecode'] = $this->request->post['setting_usecode'];
		} else {
			$data['setting_usecode'] = $this->config->get('setting_usecode');
		}

		if (isset($this->request->post['setting_showname'])) {
			$data['setting_showname'] = $this->request->post['setting_showname'];
		} else {
			$data['setting_showname'] = $this->config->get('setting_showname');
		}

		if (isset($this->request->post['setting_showprice'])) {
			$data['setting_showprice'] = $this->request->post['setting_showprice'];
		} else {
			$data['setting_showprice'] = $this->config->get('setting_showprice');
		}

		if (isset($this->request->post['setting_pageheight'])) {
			$data['setting_pageheight'] = $this->request->post['setting_pageheight'];
		} else {
			$data['setting_pageheight'] = $this->config->get('setting_pageheight');
		}

		if (isset($this->request->post['setting_pagewidth'])) {
			$data['setting_pagewidth'] = $this->request->post['setting_pagewidth'];
		} else {
			$data['setting_pagewidth'] = $this->config->get('setting_pagewidth');
		}

		if (isset($this->request->post['setting_labelqty'])) {
			$data['setting_labelqty'] = $this->request->post['setting_labelqty'];
		} else {
			$data['setting_labelqty'] = $this->config->get('setting_labelqty');
		}

		if (isset($this->request->post['setting_pagelimit'])) {
			$data['setting_pagelimit'] = $this->request->post['setting_pagelimit'];
		} else {
			$data['setting_pagelimit'] = $this->config->get('setting_pagelimit');
		}

		if (isset($this->request->post['setting_imageheight'])) {
			$data['setting_imageheight'] = $this->request->post['setting_imageheight'];
		} else {
			$data['setting_imageheight'] = $this->config->get('setting_imageheight');
		}

		if (isset($this->request->post['setting_imagewidth'])) {
			$data['setting_imagewidth'] = $this->request->post['setting_imagewidth'];
		} else {
			$data['setting_imagewidth'] = $this->config->get('setting_imagewidth');
		}

		/* 24 09 2019 */


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'possetting/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

	public function method() { 
		$json = array();

		$setting_paymentmethods = $this->config->get('setting_paymentmethod');

		$json['setting_paymentmethods'] = array();

		if(is_array($setting_paymentmethods)) {
			foreach ($setting_paymentmethods as $setting_paymentmethod) {
				$json['setting_paymentmethods'][$setting_paymentmethod['name']] = $setting_paymentmethod['order_status_id'];

			}
		}
		$json1=array(); 
		if (isset($json['setting_paymentmethods'][$this->request->get['payment_method']])) {
			$json['order_status_id']=$json['setting_paymentmethods'][$this->request->get['payment_method']];
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json1));
	}

}
