<?php
class ControllerProductAllpos extends Controller {

	public function findlocation(){

		if (isset($this->request->post['lat'])) {
			$lat = $this->request->post['lat'];
		}else{
			$lat = 0;

		}
		if (isset($this->request->post['lng'])) {
			$lng = $this->request->post['lng'];
		}else{
			$lng = 0;
		}

		if (isset($this->request->post['address'])) {
			$address = $this->request->post['address'];
		}else{
			$address = '';
		}

		$this->session->data['livelat'] = $lat;
		$this->session->data['livelng'] = $lng;	
		$this->session->data['formated_address'] = $address;

	}




	public function index() {           

		$this->load->language('product/category');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');		
		$apikey = $this->config->get('module_posapp_apikey');
		$data['apikey'] = $this->config->get('module_posapp_apikey');
		$this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $apikey . '&libraries=places') ;

		if(isset($this->session->data['formated_address'])){
			$data['formated_address'] = $this->session->data['formated_address'];
		}else{
			$data['formated_address'] = 'Type Location';
		}
		

		
		$this->load->language('product/allpos');
		$this->load->model('catalog/allpos');
		$this->load->model('account/posapp');
		$this->load->model('tool/image');

		$this->document->setTitle( $this->language->get('heading_title'));

		$data['thumb_number_width'] = 60;
		$data['thumb_number_height'] = 33;
		

		if ($this->config->get('config_logo')) {
			$site_logo =   $this->model_tool_image->resize($this->config->get('config_logo'), $data['thumb_number_width'], $data['thumb_number_height']);
		} else {
			$site_logo = '';
		}   



		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter']; 
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pa.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['path'])) {
			$data['path'] = $this->request->get['path'];
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

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/allpos', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {
			$this->document->setTitle($category_info['meta_title']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			$data['heading_title'] = $category_info['name'];

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/allpos', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
			$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
			$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');

			static $rs = 0; 

			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true

				);

				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_allpos->getTotalPoss($filter_data) . ')' : ''),
					'href' => $this->url->link('product/allpos', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
				);
			}
			$data['rss'] = $rs++;

			$data['posapps'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,				
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit,
				'longitude' => $this->session->data['livelng'],
				'latitude'  => $this->session->data['livelat']
			);

			$posapp_total = $this->model_catalog_allpos->getTotalPoss($filter_data);

			$results = $this->model_catalog_allpos->getPoss($filter_data);			 
			$units = (array)$this->language->get('pos_units');
			$unit = $units['k'];
			foreach ($results as $result) {
				$distance = '';
				if($result){
					$re_posapp_id = $result['posapp_id'];
				}else{
					$re_posapp_id = 0;
				}	 


				$pos_categories = array();
				$categories = $this->model_catalog_category->getCategories3(0, $re_posapp_id);
				foreach ($categories as $category) {
					$children_data = array();
					$children = $this->model_catalog_category->getCategories3($category['category_id'], $re_posapp_id);
					foreach($children as $child) {
						$filter_data = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);
						$children_data[] = array(
							'category_id' => $child['category_id'],
							'name' => $child['name'],
							// 'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_allpos->getTotalPoss($filter_data) . ')' : ''),
							'href' => $this->url->link('product/allpos', 'path=' . $category['category_id'] . '_' . $child['category_id'])
						);
					}

					$filter_data = array(
						'filter_category_id'  => $category['category_id'],
						'filter_sub_category' => true
					);

					$pos_categories[] = array(
						'category_id' => $category['category_id'],
						'name'        => $category['name'] ,
						// 'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_allpos->getTotalPoss($filter_data) . ')' : ''),
						'children'    => $children_data,
						'href'        => $this->url->link('product/allpos', 'path=' . $category['category_id'])
					);
				}					

				$distance = sprintf($this->language->get('text_distance'), round($result['distance'] * $unit[1], 2), $unit[0]);
				$data['posapps'][] = array(
					'posapp_id' => $result['posapp_id'],
					'site_logo' => $site_logo,
					'posname' => $result['posname'],
					'frontimage' => $this->model_tool_image->resize($result['frontimage'], 360, 180),
					'address' => $result['address'],
					'city' => $result['city'],	
					'pos_categories' => $pos_categories,				
					'zone' => $result['zone'],
					'country' => $result['country'],
					'latitude' => $result['latitude'],
					'longitude' => $result['longitude'],
					'distance' => $distance,
					'alloweddistance' => sprintf($this->language->get('text_alloweddistance'), $result['alloweddistance'], $unit[0]),					
					'href' => $this->url->link('product/allpos/info', 'path=' . $this->request->get['path'] . '&posapp_id=' . $result['posapp_id'])
				);
			}	 	
			

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'pa.sort_order-ASC',
				'href'  => $this->url->link('product/allpos', 'path=' . $this->request->get['path'] . '&sort=pa.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pa.posname-ASC',
				'href'  => $this->url->link('product/allpos', 'path=' . $this->request->get['path'] . '&sort=pa.posname&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pa.posname-DESC',
				'href'  => $this->url->link('product/allpos', 'path=' . $this->request->get['path'] . '&sort=pa.posname&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_distance_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/allpos', 'path=' . $this->request->get['path'] . '&sort=distance&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_distance_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/allpos', 'path=' . $this->request->get['path'] . '&sort=distance&order=DESC' . $url)
			);

			

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/allpos', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $posapp_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/allpos', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($posapp_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($posapp_total - $limit)) ? $posapp_total : ((($page - 1) * $limit) + $limit), $posapp_total, ceil($posapp_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
				$this->document->addLink($this->url->link('product/allpos', 'path=' . $category_info['category_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('product/allpos', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
			}
			
			if ($page > 1) {
				$this->document->addLink($this->url->link('product/allpos', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($posapp_total / $limit) > $page) {
				$this->document->addLink($this->url->link('product/allpos', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit; 

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');


			$this->response->setOutput($this->load->view('product/allpos_list', $data));
		} else {
			$this->load->language('product/category');
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');		
			// $apikey = $this->config->get('module_posapp_apikey');
			// $this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $apikey );
			$this->load->language('product/allpos');
			$this->load->model('catalog/allpos');
			$this->load->model('account/posapp');
			$this->load->model('tool/image');  


			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			

			$this->document->setTitle( $this->language->get('heading_title'));			

			$data['posapps'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,				
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => 20,
				'longitude' => isset($this->session->data['livelng'])?$this->session->data['livelng']:0,
				'latitude'  => isset($this->session->data['livelat'])?$this->session->data['livelat']:0
			);

			$posapp_total = $this->model_catalog_allpos->getTotalPoss($filter_data);

			$results = $this->model_catalog_allpos->getPoss($filter_data);			 
			$units = (array)$this->language->get('pos_units');
			$unit = $units['k'];
			foreach ($results as $result) {          

				$distance = ''; 

				if($result){
					$re_posapp_id = $result['posapp_id'];
				}else{
					$re_posapp_id = 0;

				}	 

				$pos_categories = array();
				$categories = $this->model_catalog_category->getCategories3(0, $re_posapp_id);
				foreach ($categories as $category) {
					$children_data = array();

					$children = $this->model_catalog_category->getCategories3($category['category_id'], $re_posapp_id);
					foreach($children as $child) {
						$filter_data = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);

						$children_data[] = array(
							'category_id' => $child['category_id'],
							'name' => $child['name'] ,
							// 'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_allpos->getTotalPoss($filter_data) . ')' : ''),
							'href' => $this->url->link('product/allpos', 'path=' . $category['category_id'] . '_' . $child['category_id'])
						);
					}


					$filter_data = array(
						'filter_category_id'  => $category['category_id'],
						'filter_sub_category' => true
					);

					$pos_categories[] = array(
						'category_id' => $category['category_id'],
						'name'        => $category['name'],
						// 'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_allpos->getTotalPoss($filter_data) . ')' : ''),
						'children'    => $children_data,
						'href'        => $this->url->link('product/allpos', 'path=' . $category['category_id'])
					);
				}





				$distance = sprintf($this->language->get('text_distance'), round($result['distance'] * $unit[1], 2), $unit[0]);
				$data['posapps'][] = array(
					'posapp_id' => $result['posapp_id'],
					'site_logo' => $site_logo,
					'posname' => $result['posname'],
					'frontimage' => $this->model_tool_image->resize($result['frontimage'], 360, 180),
					'address' => $result['address'],
					'city' => $result['city'],	
					'pos_categories' => $pos_categories,				
					'zone' => $result['zone'],
					'country' => $result['country'],
					'latitude' => $result['latitude'],
					'longitude' => $result['longitude'],
					'distance' => $distance,
					'alloweddistance' => sprintf($this->language->get('text_alloweddistance'), $result['alloweddistance'], $unit[0]),					
					'href' => $this->url->link('product/allpos/info', 'posapp_id=' . $result['posapp_id'])
				);
			}	

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/allpos_list', $data));
		}
	}


	public function info() {
		$apikey = $this->config->get('module_posapp_apikey');
		$this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $apikey );
		$this->load->language('product/allpos');

		$this->load->model('catalog/allpos');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['posapp_id'])) {
			$posapp_id = (int)$this->request->get['posapp_id'];
		} else {
			$posapp_id = 0;
		}
		$data['posapps'] = array();

		$filter_data = array(				
			'posapp_id'          => $posapp_id,				
			'longitude' => isset($this->session->data['livelng'])?$this->session->data['livelng']:0,
			'latitude'  => isset($this->session->data['livelat'])?$this->session->data['livelat']:0
		);

		$results = $this->model_catalog_allpos->getPoss($filter_data);			 
		$units = (array)$this->language->get('pos_units');
		$unit = $units['k'];

		foreach ($results as $result) {          

			$distance = ''; 
			$distance = sprintf($this->language->get('text_distance'), round($result['distance'] * $unit[1], 2), $unit[0]);
			$data['posapps'][] = array(
				'posapp_id' => $result['posapp_id'],
				'posname' => $result['posname'],
				'frontimage' => $this->model_tool_image->resize($result['frontimage'], 360, 180),
				'address' => $result['address'],
				'city' => $result['city'],								
				'zone' => $result['zone'],
				'country' => $result['country'],
				'latitude' => $result['latitude'],
				'longitude' => $result['longitude'],
				'distance' => $distance,
				'alloweddistance' => sprintf($this->language->get('text_alloweddistance'), $result['alloweddistance'], $unit[0]),					
				'href' => $this->url->link('product/allpos/info', 'posapp_id=' . $result['posapp_id'])
			);
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
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_pos'),
			'href' => $this->url->link('product/allpos')
		);

		$allpos_info = $this->model_catalog_allpos->getAllpos($posapp_id);

		if ($allpos_info) {
			$this->document->setTitle($allpos_info['posname']);

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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $allpos_info['posname'],
				'href' => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . $url)
			);


			$data['heading_title'] = $allpos_info['posname'];

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			$data['compare'] = $this->url->link('product/compare');

			$data['products'] = array();

			$filter_data = array(
				'filter_posapp_id' => $posapp_id,
				'sort'                   => $sort,
				'order'                  => $order,
				'start'                  => ($page - 1) * $limit,
				'limit'                  => $limit
			);

			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['options'] = array();
				$data['config_option_type'] = $this->config->get('config_thumboption');

				foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$option_price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
							} else {
								$option_price = false;
							}

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $this->model_tool_image->resize($option_value['image'], 25, 25),
								'price'                   => $option_price,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					$data['options'][] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'required'             => $option['required']
					);
				}

				$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $result['minimum']);

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'posapp_id'  => $result['posapp_id'],
					'posname'  => $this->cart->getPosname($result['posapp_id'])?$this->cart->getPosname($result['posapp_id']):'',
					'phref' => $this->url->link('product/allpos/info',  'posapp_id=' . $result['posapp_id']),
					'thumb'       => $image,
					'name'        => strtoupper(utf8_substr(trim(strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))), 0, 25 ). '..'),
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'options'     => $data['options'],
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'posapp_id=' . $result['posapp_id'] . '&product_id=' . $result['product_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . $url . '&limit=' . $value)
				);
			}

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

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] .  $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
				$this->document->addLink($this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&page=' . $page), 'canonical');
			}
			
			if ($page > 1) {
				$this->document->addLink($this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . (($page - 2) ? '&page=' . ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
				$this->document->addLink($this->url->link('product/allpos/info', 'posapp_id=' . $this->request->get['posapp_id'] . '&page=' . ($page + 1)), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/allpos_info', $data));
		} else {
			$url = '';

			if (isset($this->request->get['posapp_id'])) {
				$url .= '&posapp_id=' . $this->request->get['posapp_id'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/allpos/info', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}
