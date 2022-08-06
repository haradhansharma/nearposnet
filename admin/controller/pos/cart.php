<?php
class ControllerPosCart extends Controller {
	public function index() {
		$this->load->language('pos/pos');

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('pos/cart', $data);
	}

	public function ajaxloadaddtocart() {

		$this->load->language('pos/productinfo');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		}
		else if (isset($this->request->post['cproduct_id'])) {
			$cproduct_id = (int)$this->request->post['cproduct_id'];
		}
		else {
			$product_id = 0;
		}

		$this->load->model('pos/pos');
		if(empty($cproduct_id))
		{
			$product_info = $this->model_pos_pos->getProduct($product_id);

			if ($product_info) {
				if (isset($this->request->post['quantity']) && ((int)$this->request->post['quantity'] >= $product_info['minimum'])) {
					$quantity = (int)$this->request->post['quantity'];
				} else {
					$quantity = $product_info['minimum'] ? $product_info['minimum'] : 1;
				}

				if (isset($this->request->post['option'])) {
					$option = array_filter($this->request->post['option']);
				} else {
					$option = array();
				}

				$product_options = $this->model_pos_pos->getProductOptions($this->request->post['product_id']);

				foreach ($product_options as $product_option) {
					if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
						$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
					}
				}

				if (isset($this->request->post['recurring_id'])) {
					$recurring_id = $this->request->post['recurring_id'];
				} else {
					$recurring_id = 0;
				}

				$recurrings = $this->model_pos_pos->getProfiles($product_info['product_id']);

				if ($recurrings) {
					$recurring_ids = array();

					foreach ($recurrings as $recurring) {
						$recurring_ids[] = $recurring['recurring_id'];
					}

					if (!in_array($recurring_id, $recurring_ids)) {
						$json['error']['recurring'] = $this->language->get('error_recurring_required');
					}
				}


				if (!$json) {

					$addcart = $this->pos->add($this->request->post['product_id'], $quantity, $option, $recurring_id);

					$json['success'] ='success';


					$json['total'] = '';
				} else {
					$json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
				}
			}
		}
		else
		{
					$quantity = (int)$this->request->post['quantity'];
					$addcart = $this->pos->add(0,$quantity, '','',$cproduct_id);
					$json['success'] ='success';

					$json['total'] = '';

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	function loadcart() {
		$this->load->language('pos/pos');
		$data['user_token'] = $this->session->data['user_token'];
		$data['text_loading'] = $this->language->get('text_loading');
		$data['entry_coupon'] = $this->language->get('entry_coupon');
		$data['entry_voucher'] = $this->language->get('entry_voucher');

		$data['button_coupon'] = $this->language->get('button_coupon');
		$data['button_voucher'] = $this->language->get('button_voucher');
		$data['button_discount'] = $this->language->get('button_discount');

		$data['coupon'] = '';

		$data['voucher'] = '';

		$this->load->model('tool/image');
		$data['products'] = array();

			$products = $this->pos->getProducts();

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}

				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], 70, 70);
				} else {
					$image = '';
				}

				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				// Display prices

					$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));

					$total = $this->currency->format($this->tax->calculate($product['price']* $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));

				// Display prices

				$data['products'][] = array(
					'product_id'       => $product['product_id'],
					'key'       => $product['key'],
					'thumb'     => $image,
					'name'      => $product['name'],
					'prices'    => $product['price'],
					'model'     => $product['model'],
					'option'    => $option_data,
					'quantity'  => $product['quantity'],
					'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'reward'    => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
					'price'     => $price,
					'total'     => $total,

				);
			}
			$data['subtotal']= $this->currency->format($this->pos->getSubTotal(), $this->config->get('config_currency'));
			$data['total']=$this->currency->format($this->pos->getTotal(), $this->config->get('config_currency'));
			//$data['tax']=$this->currency->format($this->pos->getTax(), $this->config->get('config_currency'));
			// Gift Voucher
			$data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$data['vouchers'][] = array(
						'key'         => $key,
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount']),
						'remove'      => $this->url->link('checkout/cart', 'remove=' . $key)
					);
				}
			}

			$data['poslay']	= $this->config->get('setting_themechoose');
			$poslayout	= $this->config->get('setting_themechoose');

			if($poslayout=='layout1'){
				$this->response->setOutput($this->load->view('pos/cart', $data));
			} elseif($poslayout=='layout2'){
				$this->response->setOutput($this->load->view('pos/cart1', $data));
			} elseif($poslayout=='layout3'){
				$this->response->setOutput($this->load->view('pos/cart1', $data));
			} elseif($poslayout=='layout4'){
				$this->response->setOutput($this->load->view('pos/cart1', $data));
			} else{
				$this->response->setOutput($this->load->view('pos/cart', $data));
			}
	}

		/// clear cart start
	function clearcart() {
			$json = array();
		$this->pos->clear();
		$this->load->language('pos/pos');
		$data['user_token'] = $this->session->data['user_token'];
		unset($this->session->data['shipping_method']);
		unset($this->session->data['voucher']);
		unset($this->session->data['voucherdiscount']);
		unset($this->session->data['coupondiscount']);
		unset($this->session->data['coupon']);
		unset($this->session->data['mdiscount']);
		if(isset($this->session->data['order_id']))
		{
			unset( $this->session->data['order_id']);

		}
		$json['success']='';
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
		/// clear cart end

	public function remove() {

		$this->load->language('pos/pos');
		$data['user_token'] = $this->session->data['user_token'];
		$json = array();

		// Remove
		if (isset($this->request->post['key'])) {
			$this->pos->remove($this->request->post['key']);

			unset($this->session->data['vouchers'][$this->request->post['key']]);

			$json['success']= $this->language->get('text_remove');

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		$this->load->language('pos/pos');

		$json = array();

		// Update
		if (!empty($this->request->post['quantity'])) {

				$this->pos->update($this->request->post['key'], $this->request->post['quantity'],$this->request->post['price']);


			$json['success'] = $this->language->get('text_update');

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);


		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function manualdiscount()
	{
		$this->load->language('pos/total');

		$json['themechoose'] = $this->config->get('setting_themechoose');

		$json=array();
		if(!empty($this->request->post['discount']) && !empty($this->request->post['discount_type']))
		{
			$total=$this->pos->getTotal();
			if($this->request->post['discount_type']=='F')
			{
				$discount=$this->request->post['discount'];
				$this->session->data['mdiscount']=$this->request->post['discount'];
			}
			else
			{
				$discount=($total*$this->request->post['discount']/100);
				$this->session->data['mdiscount']=$discount;
			}

			$json['success'] =sprintf($this->language->get('text_manualsuccess'),$this->currency->format($discount,$this->config->get('config_currency')));
		}
		else
		{
			if(isset($this->session->data['mdiscount']))
			{
				unset($this->session->data['mdiscount']);
			}
				$json['error'] = $this->language->get('error_manualsuccess');

		}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
	}

	public function applydiscount() {

		$this->load->language('pos/total');

		if(!empty($this->request->post['coupon']))
		{
			$this->load->model('pos/coupon');
			$coupon_info = $this->model_pos_coupon->getCoupon($this->request->post['coupon']);

			if ($coupon_info) {
				$this->session->data['coupon'] = $this->request->post['coupon'];
				$taxes = $this->pos->getTaxes();
				$total['taxes']=$taxes;
				 $amount = $this->model_pos_coupon->getTotal($total);

				$this->session->data['coupondiscount'] = $amount;

				$json['success'] =sprintf($this->language->get('text_manualsuccess'),$this->currency->format($amount,$this->config->get('config_currency')));

			} else {
				if(isset($this->session->data['coupondiscount'])){
				unset($this->session->data['coupondiscount']);}
				$json['error'] = $this->language->get('error_coupon');
			}


		}
		else {
				$json['error'] = $this->language->get('error_empty');

				unset($this->session->data['coupon']);
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));

	}

	public function applyvoucher()
	{
		$this->load->model('pos/voucher');

		if (isset($this->request->post['voucher'])) {
		$voucher_info = $this->model_pos_voucher->getVoucher($this->request->post['voucher']);

		if ($voucher_info) {
			$this->session->data['voucher'] = $this->request->post['voucher'];
				$this->session->data['voucherdiscount'] = $voucher_info['amount'];
				$json['success'] = $this->language->get('text_success');

		} else {
			$json['error'] = $this->language->get('error_voucher');
		}


		}
		else
		{
			if(isset($this->session->data['voucherdiscount']))
			{
				unset($this->session->data['voucherdiscount']);
			}
			$json['error'] = $this->language->get('error_empty');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function loadtotal() {
		$json['totals']=array();

		$this->load->language('pos/total');

		$sub_total = $this->pos->getSubTotal();
		$total =$sub_total;


		$json['totals'][] = array(
			'code'       => 'sub_total',
			'title'      => $this->language->get('text_sub_total'),
			'text'      => $this->currency->format($sub_total, $this->config->get('config_currency')),
			'value'      => $sub_total,
			'sort_order' => '0'

		);



		$taxes = $this->pos->getTaxes();
		foreach ($taxes as $key => $value) {
			if ($value > 0) {
				$json['totals'][] = array(
					'code'       => 'tax',
					'title'      => $this->tax->getRateName($key),
					'text'      => $this->currency->format($value,$this->config->get('config_currency')),
					'value'      => $value,
					'sort_order' => '1'

				);
				$total +=$value;

			}
		}

			if(isset($this->session->data['mdiscount']))
		{
			$discount_total=$this->session->data['mdiscount'];
		if ($discount_total > 0) {
					$json['totals'][] = array(
						'code'       => 'mdiscount',
						'title'      => $this->language->get('text_mdiscount'),
						'text'      => '-' .$this->currency->format($discount_total,$this->config->get('config_currency')),
						'value'      =>-$discount_total,
						'sort_order' => '2'

					);
				$total -=$discount_total;
		}
		}

		if(isset($this->session->data['coupondiscount']))
		{
			$this->load->language('extension/total/coupon	');
			$discount_total=$this->session->data['coupondiscount'];
			if ($discount_total > 0) {
					$json['totals'][] = array(
						'code'       => 'coupon',
						'title'      => sprintf($this->language->get('text_coupon'), $this->session->data['coupon']),
						'text'      => '-'.$this->currency->format($discount_total,$this->config->get('config_currency')),
						'value'      => -$discount_total,
						'sort_order' => '3'

					);
				$total -=$discount_total;
			}
		}
		if(isset($this->session->data['voucherdiscount']))
		{

			$amount=$this->session->data['voucherdiscount'];
		if ($amount > 0) {
					$json['totals'][] = array(
						'code'       => 'voucher',
						'title'      => 'Voucher',
						'text'      => '-'.$this->currency->format($amount,$this->config->get('config_currency')),
						'value'      => -$amount,
						'sort_order' => '4'


					);
					$total -=$amount;


				}
		}


		$json['totals'][] = array(
			'code'       => 'total',
			'title'      => $this->language->get('text_total'),
			'text'      =>$this->currency->format($total,$this->config->get('config_currency')),
			'value'      => max(0, $total),
			'sort_order' => '5'

		);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

}
