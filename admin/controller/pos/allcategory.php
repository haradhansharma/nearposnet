<?php
class ControllerPosAllCategory extends Controller {
	public function index() {
		$this->load->language('pos/pos');
		$this->load->model('pos/pos');

		$data['user_token'] = $this->session->data['user_token'];
		$url = '';


		$data['categories'] = array();

		$categories = $this->model_pos_pos->getCategories(0);

		foreach ($categories as $category) {

			if ($category['image']) {
				$image = $this->model_tool_image->resize($category['image'], $this->config->get('theme_' .$this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' .$this->config->get('config_theme') . '_image_category_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' .$this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' .$this->config->get('config_theme') . '_image_category_height'));
			}

			$data['categories'][] = array(
				'category_id' => $category['category_id'],
				'thumb'   	 => $image,
				'name'        => $category['name'],
				'path'        => $category['category_id'],

			);
		}

		$data['poslay']	= $this->config->get('setting_themechoose');

		$poslayout	= $this->config->get('setting_themechoose');

		if($poslayout=='layout1'){
			return $this->load->view('pos/allcategory', $data);
		} elseif($poslayout=='layout2'){
			return $this->load->view('pos/allcategory1', $data);
		} elseif($poslayout=='layout3'){
			return $this->load->view('pos/allcategory1', $data);
		} elseif($poslayout=='layout4'){
			return $this->load->view('pos/allcategory1', $data);
		} else{
			return $this->load->view('pos/allcategory', $data);
		}


	}


}
