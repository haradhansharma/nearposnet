<?php
class ControllerPosPos extends Controller {
	public function index() {
////sharma for pos

		$this->load->model('user/user');
		$this->load->model('tool/image');
		$user_info = $this->model_user_user->getUser($this->user->getId());

		if(!empty($user_info['image'])){
			$user_image = $user_info['image'];
		}else{
			$user_image = '';

		}

		if (is_file(DIR_IMAGE . $user_image)) {
			$data['image'] = $this->model_tool_image->resize($user_info['image'], 45, 45);
		} else {
			$data['image'] = $this->model_tool_image->resize('profile.png', 45, 45);
		}

		$data['home'] = $this->url->link('pos/login', '', true);






		if(($this->cart->getCommissionPending() > $this->config->get('module_posapp_payment_limit')) &&  ($this->cart->getRegdiff() > $this->config->get('module_posapp_payment_trail_allowed_day') ) ){
			$this->load->language('extension/module/posapp');
			$this->session->data['success'] = sprintf($this->language->get('text_limit_over'), '<b>Limit is: ' . $this->currency->format($this->config->get('module_posapp_payment_limit'), $this->config->get('config_currency')).'</b>', 'But current due is: '.'<b>'.$this->currency->format($this->cart->getCommissionPending(), $this->config->get('config_currency')) . '</b>' ) ;
			$this->response->redirect($this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->load->language('pos/pos');

		$this->document->setTitle($this->language->get('heading_title'));



		$data['heading_title'] = $this->language->get('heading_title');

		$data['breadcrumbs'] = array();
		// SET TAX
		$this->tax->setShippingAddress($this->config->get('config_country_id'),$this->config->get('config_zone_id'));
		$this->tax->setPaymentAddress($this->config->get('config_country_id'),$this->config->get('config_zone_id'));
		// SET TAX
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('pos/pos', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('pos/pos', 'user_token=' . $this->session->data['user_token'], true)
		);

		

		//popuplinks
		$data['qrcode'] = $this->url->link('pos/readqr', 'user_token=' . $this->session->data['user_token'], true);
		$data['productlist'] = $this->url->link('pos/posproductlist', 'user_token=' . $this->session->data['user_token'], true);
		$data['noncatalog'] = $this->url->link('pos/posproduct', 'user_token=' . $this->session->data['user_token'], true);
		$data['holdon'] = $this->url->link('pos/holdon', 'user_token=' . $this->session->data['user_token'] , true);
		$data['paypopup'] = $this->url->link('pos/paynow', 'user_token=' . $this->session->data['user_token'] , true);


		$data['sidebars'] = array(); 

		$data['sidebars'][] = array(
			'fa' => 'fa fa-user',
			'name' => $this->language->get('button_selectuser'),
			'href' => $this->url->link('pos/selectcustomer', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['sidebars'][] = array(
			'fa' => 'fa fa-shopping-cart',
			'name' => $this->language->get('button_orderlist'),
			'href' => $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['sidebars'][] = array(
			'fa' => 'fa fa-undo',
			'name' => $this->language->get('button_return'),
			'href' => $this->url->link('pos/returnorder', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['sidebars'][] = array(
			'fa' => 'fa fa-users',
			'name' => $this->language->get('button_customerlist'),
			'href' => $this->url->link('pos/customerlist', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['sidebars'][] = array(
			'fa' => 'fa fa-th-list',
			'name' => $this->language->get('button_holdreport'),
			'href' => $this->url->link('pos/holdonreport', 'user_token=' . $this->session->data['user_token'], true)
		); 
		$data['sidebars'][] = array(
			'fa' => 'fa fa-battery-quarter',
			'name' => 'Due Report',
			'href' => $this->url->link('pos/due', 'user_token=' . $this->session->data['user_token'], true)
		);
		////sharma for pos

		$data['sidebars2'] = array(); 
		
		$data['sidebars2'][] = array(
			'fa' => 'fa fa-sliders',
			'name' => 'Product Sale',
			'href' => $this->url->link('possetting/productsale_report', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['sidebars2'][] = array(
			'fa' => 'fa fa-pie-chart',
			'name' => 'Commission',
			'href' => $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['sidebars2'][] = array(
			'fa' => 'fa fa-life-ring',
			'name' => 'Forecast',
			'href' => $this->url->link('possetting/forecastreport', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['sidebars2'][] = array(
			'fa' => 'fa fa-lemon-o',
			'name' => 'Cost',
			'href' => $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['sidebars2'][] = array(
			'fa' => 'fa fa-cube',
			'name' => 'Return',
			'href' => $this->url->link('possetting/return_report', 'user_token=' . $this->session->data['user_token'], true)
		);
		////sharma for pos






		$this->document->addScript('view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$data['order_id']=0;
		if(isset($this->session->data['order_id'])) {
			$data['order_id']=$this->session->data['order_id'];
		}
		$data['user_token']=$this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['totalsale'] = $this->load->controller('pos/totalsale');
		$data['totalcash'] = $this->load->controller('pos/totalcash');
		$data['dashboard'] = $this->load->controller('pos/dashboardload');


		$data['dashmenu'] = $this->load->controller('tmdblog/dashmenu');
		//$data['customerlist'] = $this->load->controller('pos/customerlist');
		$data['categorysearch'] = $this->load->controller('pos/categorysearch');
		$data['cart'] = $this->load->controller('pos/cart');
		$data['allcategory'] = $this->load->controller('pos/allcategory');
		$data['productinfo'] = $this->load->controller('pos/productinfo');

		$data['footer'] = $this->load->controller('common/footer');

		//cart code
		$data['text_loading'] = $this->language->get('text_loading');
		$data['entry_coupon'] = $this->language->get('entry_coupon');
		$data['entry_voucher'] = $this->language->get('entry_voucher');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['text_item'] = $this->language->get('text_item');
		$data['text_name'] = $this->language->get('text_name');
		$data['text_price'] = $this->language->get('text_price');
		$data['text_qty'] = $this->language->get('text_qty');
		$data['text_total'] = $this->language->get('text_total');
		$data['text_action'] = $this->language->get('text_action');
		$data['text_fixed'] = $this->language->get('text_fixed');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_subtotal'] = $this->language->get('text_subtotal');
		$data['text_grandtotal'] = $this->language->get('text_grandtotal');
		$data['text_todaysale'] = $this->language->get('text_todaysale');
		$data['text_todaycash'] = $this->language->get('text_todaycash');
		$data['entry_orderid'] = $this->language->get('entry_orderid');
		/* 24 09 2019 */
		$data['button_addproduct'] = $this->language->get('button_addproduct');
		/* 24 09 2019 */
		$data['button_submit'] = $this->language->get('button_submit');
		$data['button_product'] = $this->language->get('button_product');
		$data['button_coupon'] = $this->language->get('button_coupon');
		$data['button_voucher'] = $this->language->get('button_voucher');
		$data['button_discount'] = $this->language->get('button_discount');
		$data['button_print'] = $this->language->get('button_print');
		$data['button_paynow'] = $this->language->get('button_paynow');
		$data['button_customerlist'] = $this->language->get('button_customerlist');
		$data['button_orderlist'] = $this->language->get('button_orderlist');
		$data['text_splitpayment'] = $this->language->get('text_splitpayment');
		$data['text_split'] = $this->language->get('text_split');
		if (isset($this->request->post['setting_paymentmethod'])) {
			$setting_paymentmethods = $this->request->post['setting_paymentmethod'];
		} else {
			$setting_paymentmethods = $this->config->get('setting_paymentmethod');
		}
		$data['setting_paymentmethods'] = array();
		if(is_array($setting_paymentmethods)) {
			foreach ($setting_paymentmethods as $setting_paymentmethod) {
				$data['setting_paymentmethods'][] = array(
					'name' 	=> $setting_paymentmethod['name'],
					'order_status_id'	=> $setting_paymentmethod['order_status_id'],
				);
			}
		}

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
				////sharma for pos

				$custom_figure=array();
				if (isset($dashboards['dashboard_custom_figure'])) {
					foreach ($dashboards['dashboard_custom_figure'] as $dashboard_custom_figure) {
						$custom_figure[]=$dashboard_custom_figure['figures'];
					}
				}
				////sharma for pos
				

				$totalamount = $this->model_pos_order->getDashboardOrderAmount($orderstatus_data,$payment_method_data,$dashboards['daytype'],$custom_figure);
				//print_r($totalamount);die();
				$data['setting_dashboards'][] = array(
					'name' 	 			=> $dashboards['name'],
					'sort_order' 	 	=> $dashboards['sort_order'],
					'daytype' 	 		=> $dashboards['daytype'],
					'dashboard_status' 	=> $dashboards['dashboard_status'],
					'text_color' 	 	=> $dashboards['text_color'],
					'bg_color' 	 		=> $dashboards['bg_color'],
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

		$data['subtotal']= $this->currency->format($this->pos->getSubTotal(), $this->config->get('config_currency'));
		$data['total']=$this->currency->format($this->pos->getTotal(), $this->config->get('config_currency'));
		if (isset($this->session->data['coupon'])) {
			$data['coupon'] = $this->session->data['coupon'];
		} else {
			$data['coupon'] = '';
		}

		if (isset($this->session->data['voucher'])) {
			$data['voucher'] = $this->session->data['voucher'];
		} else {
			$data['voucher'] = '';
		}
		if (!empty($this->session->data['pos_customer_id'])) {
			$customer_info = $this->model_pos_order->getCustomer($this->session->data['pos_customer_id']);

			if (!empty($customer_info)) {

				$find = array(
					'{email}',
					'{telephone}'
				);

				$replace = array(
					'email' => $customer_info['email'],
					'telephone'  => $customer_info['telephone']
				);
				$finddisplay = $this->config->get('setting_display_customer');
				$format = $this->config->get('setting_display_format');
				if (!in_array('3', $finddisplay)) {
					$format = str_replace('{email}', '', $format);
				}
				if (!in_array('4', $finddisplay)) {
					$format = str_replace('{telephone}', '', $format);
				}
				$display_info = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
				$display_info = str_replace('<br />','', $display_info);

				$data['customer_name'] = $customer_info['firstname'].' '.$customer_info['lastname'].' '.$display_info;
			}
		}
		/* 01 11 2019 */
		$data['procart'] = $this->session->data['cart'];

		$data['button_hold'] = $this->language->get('button_hold');
		$data['button_holdreport'] = $this->language->get('button_holdreport');
		/* 01 11 2019 */

		/*  $data['customerform'] = $this->load->controller('pos/customerform');*/
		$json['themechoose'] = $this->config->get('setting_themechoose');

		$poslayout	= $this->config->get('setting_themechoose');

		if($poslayout=='layout1'){
			$this->response->setOutput($this->load->view('pos/pos', $data));
		} elseif($poslayout=='layout2'){
			$this->response->setOutput($this->load->view('pos/pos1', $data));
		} elseif($poslayout=='layout3'){
			$this->response->setOutput($this->load->view('pos/pos2', $data));
		} elseif($poslayout=='layout4'){
			$this->response->setOutput($this->load->view('pos/pos3', $data));
		} else{
			$this->response->setOutput($this->load->view('pos/pos', $data));
		}


	}

	public function ajaxloaddata() {

		$this->load->model('pos/pos');
		$this->load->model('tool/image');
		$json=array();
		$json['breadcrumbs']=array();
		$json['categories']=array();
		$json['products']=array();

		$json['themechoose'] = $this->config->get('setting_themechoose');

		if(isset($this->request->get['category_id']))
		{
			$category_id=$this->request->get['category_id'];

			if (isset($this->request->get['path'])) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$path = '';

				$parts = explode('_', (string)$this->request->get['path']);

				$category_id = (int)array_pop($parts);

				foreach ($parts as $path_id) {
					if (!$path) {
						$path = (int)$path_id;
					} else {
						$path .= '_' . (int)$path_id;
					}

					$category_info = $this->model_pos_pos->getCategory($path_id);

					if ($category_info) {
						$json['breadcrumbs'][] = array(
							'text' => $category_info['name'],
							'category_id'=>$category_info['category_id'],
							'path'=>$category_info['name']
						);

					}

				}
				$category_info = $this->model_pos_pos->getCategory($category_id);

				if ($category_info) {
					$json['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'category_id'=>$category_info['category_id'],
						'path'=>$category_info['name']
					);


				}
			}

			$path=$this->request->get['path'];

			$json['products']=array();
			$categories = $this->model_pos_pos->getCategories($this->request->get['category_id']);
			foreach ($categories as $category) {
				$json['categories'][] = array(
					'category_id' => $category['category_id'],
					'name'        => $category['name'],
					'path'        => $path.'_'.$category['category_id'],

				);
			}

			if (isset($this->request->get['filter'])) {
				$filter = $this->request->get['filter'];
			} else {
				$filter = '';
			}

			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
			} else {
				$sort = 'p.sort_order';
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

			/* 24 09 2019 */
			$prolimit = $this->config->get('setting_pagelimit');
			if($prolimit) {
				$limits = $prolimit;
			} else {
				$limits = 10;
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = $limits;
			}
			/* 24 09 2019 */

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}


			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);


			$totalproduct = $this->model_pos_pos->getTotalProducts($filter_data);
			$results = $this->model_pos_pos->getProducts($filter_data);
			if(($page - 1) * $limit<$totalproduct)
			{
				$json['loadmore']=1;
			}
			if($page==2)
			{
				$json['categories']=array();
			}
			foreach ($results as $result) {
				/* 24 09 2019 */
				$imageheight = $this->config->get('setting_imageheight');
				$imagewidth = $this->config->get('setting_imagewidth');
				if($imageheight) {
					$heights = $imageheight;
				} else {
					$heights = 200;
				}

				if($imagewidth) {
					$widths = $imagewidth;
				} else {
					$widths = 200;
				}
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'],$heights,$widths);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',$heights,$widths);
				}
				/* 24 09 2019 */


				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
				} else {
					$special = false;
				}



				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->config->get('config_currency'));
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$options ='';

				foreach ($this->model_pos_pos->getProductOptions($result['product_id']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {

						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($option_value['price'], $this->config->get('config_currency'));
						} else {
							$price = false;
						}

						$options.=$option_value['name'].'-'.$option_value['quantity'].', ';


					}

				}

				$json['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'stock'         => $result['quantity'],
					'options'         => $options,
					//'percentage'         => $percentage,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'href'     => $this->url->link('pos/productinfo','&product_id=' . $result['product_id'] . '&user_token=' . $this->session->data['user_token'] .  $url, true)

				);
			}

			$json['addquick']=false;
		}
		else if(isset($this->request->post['filter_product']))
		{
			$json['addquick']=true;
			$filter_data = array(
				'filter_name'         => $this->request->post['filter_product'],
				'filter_tag'          => $this->request->post['filter_product'],
				'filter_description'  => true,
			);
			$results = $this->model_pos_pos->getProducts($filter_data);

			$json['themechoose'] = $this->config->get('setting_themechoose');

			$countproduct=count($results);
			if($countproduct>1)
			{
				$json['addquick']=false;
			}
		// Extra code for option match
			if($countproduct==0)
			{
				$json['addquick']=false;
			}
		// Extra code for option match
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'],150,150);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png',150,150);
				}



				if(isset($result['price'])){
					$price = $this->currency->format($result['price'], $this->config->get('config_currency'));
				} else{
					$price =  false;
				}
				$percentage=0;

				if ((float)$result['special']) {
					$percentage=round(100-(($result['special']/$result['price'])*100),2);
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->config->get('config_currency'));
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$options ='';

				foreach ($this->model_pos_pos->getProductOptions($result['product_id']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {

						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($option_value['price'], $this->config->get('config_currency'));
						} else {
							$price = false;
						}

						$options.=$option_value['name'].'-'.$option_value['quantity'].',';


					}

				}

				$json['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'options'         => $options,
					'stock'         => $result['quantity'],
					'percentage'         => $percentage,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'href'     => $this->url->link('pos/productinfo', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'], true)

				);
			}

			// Extra code for option match
			if(empty($json['products']))
			{
				$filter_data = array(
					'filter_search'         => $this->request->post['filter_product'],
				);
				$this->load->model('possetting/stockin');
				$results = $this->model_possetting_stockin->getStockinOptionUpcs($filter_data);
				foreach($results as $result)
				{
					$option_infos = $this->model_possetting_stockin->getProductOptionValue($result['product_option_value_id']);
					$product_option_id=$option_infos['product_option_id'];
					$quantity='1';
					$option=array();
					$option[$product_option_id]=$result['product_option_value_id'];
					$this->pos->add($result['product_id'], $quantity, $option, '');
					$json['cartload']=true;
				}
			}
			// Extra code for option match

		}
		else
		{

			$categories = $this->model_pos_pos->getCategories(0);
			foreach ($categories as $category) {
				$json['categories'][] = array(
					'category_id' => $category['category_id'],
					'name'        => $category['name'],
					'path'        => $category['category_id'],
				);
			}
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function ajaxloadproductdata() {
		$this->load->model('pos/pos');
		$this->load->model('tool/image');
		$json=array();

		$this->load->model('pos/pos');
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$product_info = $this->model_pos_pos->getProduct($product_id);

		$this->response->setOutput($this->load->view('pos/productinfo', $data));

	}
	public function delete() {
		$this->load->language('pos/pos');

		$this->document->setTitle($this->language->get('heading_title'));


		$this->load->model('pos/holdon');
		$json = array();
		if (!empty($this->request->get['holdon_id'])){
			$this->model_pos_holdon->deleteHoldOn($this->request->get['holdon_id']);
			$json['success'] = 'Holdon Deleted Successfully!';
			$tmdurl = $this->url->link('pos/holdonreport','user_token='.$this->session->data['user_token'].'&filter_data=true',true);
			$json['tmdurl'] = str_replace('amp;', '', $tmdurl);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
