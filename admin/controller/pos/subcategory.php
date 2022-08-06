<?php
class ControllerPosSubcategory extends Controller {
	public function index() {
		$this->load->language('pos/pos');
		$this->load->model('pos/pos');
		
		$data['user_token'] = $this->session->data['user_token'];
		$url = '';
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}
		
		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}
		
		$data['categories'] = array();

		$categories = $this->model_pos_pos->getCategories(0);
		
		foreach ($categories as $category) {
			$children_data = array();

			if ($category['category_id'] == $data['category_id']) {
				$children = $this->model_pos_pos->getCategories($category['category_id']);

				foreach($children as $child) {
					$filter_data = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);

					$children_data[] = array(
						'category_id' => $child['category_id'],
						'name' => $child['name'],
						'href' => $this->url->link('pos/pos', 'user_token=' . $this->session->data['user_token'] . '&path=' . $category['category_id'] . '_' . $child['category_id'].$url, true)
					);
				}
			}

			$filter_data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			$data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'],
				'children'    => $children_data,
				'href'        => $this->url->link('pos/pos','user_token=' . $this->session->data['user_token'] . '&path=' . $category['category_id'].$url, true)
			);
		}	
		
		$this->response->setOutput($this->load->view('pos/subcategory', $data));	
		
	}
}