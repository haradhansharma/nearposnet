<?php
class ControllerPosPosProductlist extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('pos/posproductlist');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pos/posproduct');

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'product_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
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

		$data['productlist'] = array();

		$filter_data = array(
			'search'	=> $search,
			'sort'      => $sort,
			'order'     => $order,
		);

		$this->load->model('tool/image');
		$product_total = $this->model_pos_posproduct->getTotalProductss($filter_data);
		$results = $this->model_pos_posproduct->getProducts($filter_data);
		foreach($results as $result) {
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
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], $heights, $widths);
			} else {
			 	$image = $this->model_tool_image->resize('no_image.png', $heights, $widths);
			}

			if ((float)$result['price']) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
			} else {
				$price = false;
			}

			if ((float)$result['price']) {
				$tax = $this->currency->format($result['price'], $this->config->get('config_currency'));
			} else {
				$tax = false;
			}

			$data['productlist'][]= array(
				'product_id'=> $result['product_id'],
				'name' 		=> $result['name'],
				'model' 	=> $result['model'],
				'price' 	=> $price,
				'quantity' 	=> $result['quantity'],
				'image' 	=> $image,
				'tax' 		=> $tax,
			);
		}

		$url = '';

		if (isset($this->request->get['search'])) {
		 	$url .= '&search=' . $this->request->get['search'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])) {
		 	$url .= '&limit=' . $this->request->get['limit'];
		}

		$data['product_total'] = $product_total;
		$data['page'] = $page;
		$data['limits'] = $this->config->get('setting_pagelimit');

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('setting_pagelimit');
		$pagination->url   = $this->url->link('pos/posproductlist', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('setting_pagelimit')) + 1 : 0, ((($page - 1) * $this->config->get('setting_pagelimit')) > ($product_total - $this->config->get('setting_pagelimit'))) ? $product_total : ((($page - 1) * $this->config->get('setting_pagelimit')) + $this->config->get('setting_pagelimit')), $product_total, ceil($product_total / $this->config->get('setting_pagelimit')));
		$data['search'] = $search;
		$data['heading_title'] 		= $this->language->get('heading_title');
		$data['text_no_results'] 	= $this->language->get('text_no_results');
		$data['text_list'] 			= $this->language->get('text_list');
		$data['column_id'] 			= $this->language->get('column_id');
		$data['column_image'] 		= $this->language->get('column_image');
		$data['column_name'] 		= $this->language->get('column_name');
		$data['column_model'] 		= $this->language->get('column_model');
		$data['column_price'] 		= $this->language->get('column_price');
		$data['column_qty'] 		= $this->language->get('column_qty');
		$data['column_action'] 		= $this->language->get('column_action');
		$data['button_addtocart'] 	= $this->language->get('button_addtocart');
		$data['user_token'] 		= $this->session->data['user_token'];

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->response->setOutput($this->load->view('pos/posproductlist', $data));
	}

	public function addCart() {
		$this->load->language('pos/productinfo');

		$json = array();

		if (isset($this->request->post['cproduct_id'])) {
			$cproduct_id = (int)$this->request->post['cproduct_id'];
		} else {
			$cproduct_id = 0;
		}

		$this->load->model('pos/posproduct');

		$product_info = $this->model_pos_posproduct->getProduct($cproduct_id);

		if ($product_info) {

			if (!$json) {
				$this->pos->add(0,$quantity=1, '','',$cproduct_id);
				$json['success'] ='success';
				$json['total'] = '';
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}
}
