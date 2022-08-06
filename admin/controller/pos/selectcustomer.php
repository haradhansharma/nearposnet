<?php
class ControllerPosSelectCustomer extends Controller {
	private $error = array();

	public function index() {
		$this->load->model('pos/order');
		$this->load->language('pos/pos');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['button_save'] = $this->language->get('button_save');
		$data['user_token'] = $this->session->data['user_token'];
		$this->document->setTitle($this->language->get('heading_title'));

		$data['newadd'] = $this->url->link('pos/paynowcustomer', 'user_token=' . $this->session->data['user_token'] , true);

		$this->response->setOutput($this->load->view('pos/selectcustomer', $data));


	}

	public function edit()
	{
		$json=array();
		$filter_data = array();
		$this->load->model('pos/customerproduct');
		$this->load->model('tool/image');
		if(isset($this->request->post))
		{	unset($this->session->data['pos_customer_id']);
			$this->session->data['pos_customer_id']=$this->request->post['customer_id'];

			$results = $this->model_pos_customerproduct->getProducts($filter_data);
			$display_customer_pro = $this->config->get('setting_display_customer_pro');
			if ($display_customer_pro ==1)
			{
				$json['products'] = array();
				foreach ($results as $result)
				{
					if ($result['image'])
					{
						$image = $this->model_tool_image->resize($result['image'], 200, 200);
					}
					else
					{
						$image = $this->model_tool_image->resize('placeholder.png', 200, 200);
					}

					if(isset($result['price']))
					{
						$price = $this->currency->format($result['price'], $this->config->get('config_currency'));
					}
					else
					{
						$price =  false;
					}

					$percentage=0;

					if ((float)$result['special'])
					{
						$percentage=round(100-(($result['special']/$result['price'])*100),2);
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
					}
					else
					{
						$special = false;
					}

					if ($this->config->get('config_tax'))
					{
						$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->config->get('config_currency'));
					}
					else
					{
						$tax = false;
					}

					if ($this->config->get('config_review_status'))
					{
						$rating = (int)$result['rating'];
					}
					else
					{
						$rating = false;
					}

					$options ='';

					foreach ($this->model_pos_pos->getProductOptions($result['product_id']) as $option)
					{
						$product_option_value_data = array();

						foreach ($option['product_option_value'] as $option_value)
						{

							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price'])
							{
								$price = $this->currency->format($option_value['price'], $this->config->get('config_currency'));
							}
							else
							{
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
					);
				}
			}
			$json['success']=$this->language->get('button_save');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
