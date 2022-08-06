<?php
class ControllerCommonMenu extends Controller {
	public function index() {
		$this->load->language('common/menu');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();
		////sharma for Pos

		if (is_file(DIR_IMAGE . 'icons-01.png')) {
			$data['npos'] = $this->model_tool_image->resize('icons-01.png', 22, 22);;
		} else {
			$data['npos'] = '';
		}


		if(isset($this->request->get['route'])){
			$data['route'] = (string)$this->request->get['route'];
		}else{
			$data['route'] = '';
		}	

		$data['pos_route'] = 'product/allpos';
		$data['pos_route_info'] = 'product/allpos/info';
		if(isset( $this->request->get['posapp_id'])){		
			$data['posapp_id'] = $this->request->get['posapp_id'];
		}else{
			$data['posapp_id'] = '';
		}

		$this->load->model('setting/module');
		$data['modules'] = array();
		$setting_info = $this->model_setting_module->getModule('category');
		if ($this->config->get('module_category_status')) {
			$module_data = $this->load->controller('extension/module/category');
			if ($module_data) {
				$data['modules'][] = $module_data;
			}
		}

		$data['nearposs'][] = array(
			'name'     => $this->language->get('text_nearest_pos'),					
			'column'   =>  1,
			'href'     => $this->url->link('product/allpos','', true)
		);
        ////sharma for pos
		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}

				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		return $this->load->view('common/menu', $data);
	}
}
