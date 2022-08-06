<?php

class ControllerPossettingCostReport extends Controller {

 	private $error = array();

	public function index() {

		$this->load->language('possetting/cost_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/cost_report');

		$this->getList();

	}



 	public function getList() {

		$this->load->language('possetting/cost_report');



		if (isset($this->request->get['filter_upc'])) {

			$filter_upc = $this->request->get['filter_upc'];

		} else {

			$filter_upc = '';

		}


		if (isset($this->request->get['filter_product'])) {

			$filter_product = $this->request->get['filter_product'];

		} else {

			$filter_product = '';

		}


		if (isset($this->request->get['filter_product_id_form'])) {

			$filter_product_id_form = $this->request->get['filter_product_id_form'];

		} else {

			$filter_product_id_form = '';

		}


		if (isset($this->request->get['filter_product_id_to'])) {

			$filter_product_id_to = $this->request->get['filter_product_id_to'];

		} else {

			$filter_product_id_to = '';

		}


		if (isset($this->request->get['filter_sell_quantity_form'])) {

			$filter_sell_quantity_form = $this->request->get['filter_sell_quantity_form'];

		} else {

			$filter_sell_quantity_form = '';

		}


		if (isset($this->request->get['filter_sell_quantity_to'])) {

			$filter_sell_quantity_to = $this->request->get['filter_sell_quantity_to'];

		} else {

			$filter_sell_quantity_to = '';

		}


		if (isset($this->request->get['filter_price_form'])) {

			$filter_price_form = $this->request->get['filter_price_form'];

		} else {

			$filter_price_form = '';

		}


		if (isset($this->request->get['filter_price_to'])) {

			$filter_price_to = $this->request->get['filter_price_to'];

		} else {

			$filter_price_to = '';

		}


		if (isset($this->request->get['filter_avi_quantity_form'])) {

			$filter_avi_quantity_form = $this->request->get['filter_avi_quantity_form'];

		} else {

			$filter_avi_quantity_form = '';

		}


		if (isset($this->request->get['filter_avi_quantity_to'])) {

			$filter_avi_quantity_to = $this->request->get['filter_avi_quantity_to'];

		} else {

			$filter_avi_quantity_to = '';

		}


		if (isset($this->request->get['filter_date_form'])) {

			$filter_date_form = $this->request->get['filter_date_form'];

		} else {

			$filter_date_form = '';

		}


		if (isset($this->request->get['filter_date_to'])) {

			$filter_date_to = $this->request->get['filter_date_to'];

		} else {

			$filter_date_to = '';

		}

	 

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

		

		if (isset($this->request->get['filter_upc'])) {

			$url .= '&filter_upc=' . $this->request->get['filter_upc'];

		}


		if (isset($this->request->get['filter_product'])) {

			$url .= '&filter_product=' . $this->request->get['filter_product'];

		}


		if (isset($this->request->get['filter_product_id_form'])) {

			$url .= '&filter_product_id_form=' . $this->request->get['filter_product_id_form'];

		}


		if (isset($this->request->get['filter_product_id_to'])) {

			$url .= '&filter_product_id_to=' . $this->request->get['filter_product_id_to'];

		}


		if (isset($this->request->get['filter_sell_quantity_form'])) {

			$url .= '&filter_sell_quantity_form=' . $this->request->get['filter_sell_quantity_form'];

		}


		if (isset($this->request->get['filter_sell_quantity_to'])) {

			$url .= '&filter_sell_quantity_to=' . $this->request->get['filter_sell_quantity_to'];

		}


		if (isset($this->request->get['filter_price_form'])) {

			$url .= '&filter_price_form=' . $this->request->get['filter_price_form'];

		}


		if (isset($this->request->get['filter_price_to'])) {

			$url .= '&filter_price_to=' . $this->request->get['filter_price_to'];

		}


		if (isset($this->request->get['filter_avi_quantity_form'])) {

			$url .= '&filter_avi_quantity_form=' . $this->request->get['filter_avi_quantity_form'];

		}


		if (isset($this->request->get['filter_avi_quantity_to'])) {

			$url .= '&filter_avi_quantity_to=' . $this->request->get['filter_avi_quantity_to'];

		}


		if (isset($this->request->get['filter_date_form'])) {

			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];

		}


		if (isset($this->request->get['filter_date_to'])) {

			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];

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

		if ($order == 'ASC') {

			$url .= '&order=DESC';

		} else {

			$url .= '&order=ASC';

		}

	 

		if (isset($this->request->post['selected'])) {

			$data['selected'] = (array)$this->request->post['selected'];

		} else {

			$data['selected'] = array();

		}



		$data['breadcrumbs'] = array();



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_home'),

			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)

		);



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('heading_title'),

			'href' => $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . $url, true)

		);

		$data['reset'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '', true);

		$data['cost_reports'] = array();



		$filter_data = array(
			'filter_upc'	  			=> $filter_upc,
			'filter_product'	  		=> $filter_product,
			'filter_product_id_form'	=> $filter_product_id_form,
			'filter_product_id_to'	  	=> $filter_product_id_to,
			'filter_sell_quantity_form'	=> $filter_sell_quantity_form,
			'filter_sell_quantity_to'	=> $filter_sell_quantity_to,
			'filter_price_form'	  		=> $filter_price_form,
			'filter_price_to'	  		=> $filter_price_to,
			'filter_avi_quantity_form'	=> $filter_avi_quantity_form,
			'filter_avi_quantity_to'	=> $filter_avi_quantity_to,
			'filter_date_form'	  		=> $filter_date_form,
			'filter_date_to'	  		=> $filter_date_to,

			'sort'  => $sort,

			'order' => $order,

			'start' => ($page - 1) * $this->config->get('config_limit_admin'),

			'limit' => $this->config->get('config_limit_admin')

		);



		$this->load->model('tool/image');

		$this->load->model('catalog/product');

		

		$result_total 	= $this->model_possetting_cost_report->getTotalSellReport($filter_data);

		$results 			= $this->model_possetting_cost_report->getReports($filter_data);



		$tmdtotalcost 			= 0;
		$data['totalstock'] 		= 0;
		$tmdtotalprofit 		= 0;
		$tmdactsalemade 		= 0;
		$tmdactprofitmade 		= 0;
		$tmdtotalitemscost 	= 0;
		$tmdtotalest_total_sale= 0;

		$data['totalcost'] 			= 0;
		$data['totalprofit'] 		= 0;
		$data['actsalemade'] 		= 0;
		$data['actprofitmade'] 		= 0;
		$data['totalitemscost'] 		= 0;
		$data['totalest_total_sale']	= 0;
		$data['totalstock'] 		= 0;
		$data['totalsale_quantity'] = 0;
		$data['totalsale_quantity'] = 0;

		foreach ($results as $result) 
		{
			$cost=$this->model_possetting_cost_report->getCost($result['product_id'],$result['order_id']);

			$this->load->model('catalog/product');
			$product_info=$this->model_catalog_product->getProduct($result['product_id']);
			if (!empty($product_info['image']) && is_file(DIR_IMAGE . $product_info['image'])) 
			{
				$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
			}
			else 
			{
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			if (!empty($product_info['name'])) 
			{
				$pro_name = $product_info['name'];
			} 
			else 
			{
				$pro_name = '';
			}

			if (!empty($product_info['price'])) 
			{
				$pro_price = $product_info['price'];
			} else 
			{
				$pro_price = '';
			}

			if (!empty($product_info['upc'])) 
			{
				$pro_upc = $product_info['upc'];
			} 
			else 
			{
				$pro_upc = '';
			}

			$sale_quantity=$this->model_possetting_cost_report->getTmdSales($result['product_id'],$filter_data);
			
			if(!empty($cost) && $cost!='0.00'){
				$amount 	= $product_info['quantity']*$cost;
				$amount2 	= $product_info['quantity']*$product_info['price'];
				$profit 	= $amount2-$amount;
				$tmdtotalprofit 	+=$profit;
				$data['totalprofit'] 	=$this->currency->format($tmdtotalprofit, $this->config->get('config_currency'));

				$actsale 				= $sale_quantity*$result['price'];
				$tmdactsalemade 	+=$actsale;
				$data['actsalemade'] 	=$this->currency->format($tmdactsalemade, $this->config->get('config_currency'));

				$actprofit 				= ($sale_quantity*$result['price'])-($sale_quantity*$cost);
				$tmdactprofitmade 	+=$actprofit;
				$data['actprofitmade'] 	=$this->currency->format($tmdactprofitmade, $this->config->get('config_currency'));

				$tmdtotalcost		+= $cost;
				$data['totalcost'] 		=$this->currency->format($tmdtotalcost, $this->config->get('config_currency'));

				$data['totalstock'] 	+= $product_info['quantity'];

				$totalitemscost			=$product_info['quantity']*$cost;
				$tmdtotalitemscost 		+=$totalitemscost;
				$data['totalitemscost'] 	=$this->currency->format($tmdtotalitemscost, $this->config->get('config_currency'));

				$totalest_total_sale	=$product_info['quantity']*$pro_price;
				$tmdtotalest_total_sale 	+=$totalest_total_sale;
				$data['totalest_total_sale'] 	=$this->currency->format($tmdtotalest_total_sale, $this->config->get('config_currency'));


				$sale_quantity2				=$sale_quantity;
				$data['totalsale_quantity'] +=$sale_quantity2;
			
				$data['cost_reports'][] = array(
					'product_id'			=> $result['product_id'],
					'name' 					=> $pro_name,
					'image'     			=> $image,
					'inventory'     		=> $this->currency->format($product_info['quantity']*$cost, $this->config->get('config_currency')),
					'est_total_sale'     	=> $this->currency->format($product_info['quantity']*$pro_price, $this->config->get('config_currency')),
					'sale_quantity'     	=> $sale_quantity,
					'quantity'  			=> $product_info['quantity'],
					'date_added'  			=> $result['date_added'],
					'cost'  				=> $this->currency->format($cost, $this->config->get('config_currency')),
					'upc'  	    			=> $pro_upc,
					'price' 				=> $this->currency->format($pro_price, $this->config->get('config_currency')),
					//'profit' 				=> $this->currency->format($profit, $this->config->get('config_currency')),
					'profit' 				=> $this->currency->format(($product_info['quantity']*$pro_price)-($product_info['quantity']*$cost), $this->config->get('config_currency')),
					'actsalemade' 			=> $this->currency->format($sale_quantity*$result['price'], $this->config->get('config_currency')),
					'actprofitmade' 		=> $this->currency->format(($sale_quantity*$result['price'])-($sale_quantity*$cost), $this->config->get('config_currency')),
				);
			}	
		}
		//echo"<pre>";print_r($totalitemscost);die();


		$data['heading_title']       			= $this->language->get('heading_title');
		$data['text_stock_total']    			= $this->language->get('text_stock_total');
		$data['text_cost_total']     			= $this->language->get('text_cost_total');
		$data['text_profit_total']   			= $this->language->get('text_profit_total');
		$data['text_sale_total']     			= $this->language->get('text_sale_total');
		$data['text_list']           			= $this->language->get('text_list');
		$data['text_no_results'] 	 			= $this->language->get('text_no_results');
		$data['text_confirm'] 		 			= $this->language->get('text_confirm');
		$data['text_nooption'] 		 			= $this->language->get('text_nooption');
		$data['text_select'] 		 			= $this->language->get('text_select');

		$data['entry_product_id']      			= $this->language->get('entry_product_id');
		$data['entry_image']           			= $this->language->get('entry_image');
		$data['entry_name']            			= $this->language->get('entry_name');
		$data['entry_price']           			= $this->language->get('entry_price');
		$data['entry_quantity']        			= $this->language->get('entry_quantity');
		$data['entry_sale_quantity']   			= $this->language->get('entry_sale_quantity');
		$data['entry_inventory']   				= $this->language->get('entry_inventory');
		$data['entry_inventory2']   			= $this->language->get('entry_inventory2');
		$data['text_stock_totalinventory']   	= $this->language->get('text_stock_totalinventory');
		$data['text_stock_totalinventory2']   	= $this->language->get('text_stock_totalinventory2');
		$data['text_total_sell_qty']   			= $this->language->get('text_total_sell_qty');
		$data['entry_cost']            			= $this->language->get('entry_cost');
		$data['entry_profit']          			= $this->language->get('entry_profit');
		$data['entry_actsalemade']          	= $this->language->get('entry_actsalemade');
		$data['entry_actprofitmade']          	= $this->language->get('entry_actprofitmade');
		$data['entry_upc']             			= $this->language->get('entry_upc');
		$data['entry_form']             		= $this->language->get('entry_form');
		$data['entry_to']             			= $this->language->get('entry_to');
		$data['entry_date']             		= $this->language->get('entry_date');
		$data['entry_date_added']             	= $this->language->get('entry_date_added');
		$data['text_salemade_total']           	= $this->language->get('text_salemade_total');
		$data['text_profitmade_total']        	= $this->language->get('text_profitmade_total');
		$data['button_delete'] 		  			= $this->language->get('button_delete');
		$data['button_filter'] 		  			= $this->language->get('button_filter');
		$data['button_reset'] 		  			= $this->language->get('button_reset');
		$data['button_save'] 		  			= $this->language->get('button_save');

		$data['user_token']                			= $this->session->data['user_token'];

		

		if (isset($this->error['warning'])) {

			$data['error_warning'] = $this->error['warning'];

		} else {

			$data['error_warning'] = '';

		}



		$url = '';



		if (isset($this->request->get['filter_upc'])) {

			$url .= '&filter_upc=' . $this->request->get['filter_upc'];

		}
		if (isset($this->request->get['filter_product'])) {

			$url .= '&filter_product=' . $this->request->get['filter_product'];

		}


		if (isset($this->request->get['filter_product_id_form'])) {

			$url .= '&filter_product_id_form=' . $this->request->get['filter_product_id_form'];

		}


		if (isset($this->request->get['filter_product_id_to'])) {

			$url .= '&filter_product_id_to=' . $this->request->get['filter_product_id_to'];

		}


		if (isset($this->request->get['filter_sell_quantity_form'])) {

			$url .= '&filter_sell_quantity_form=' . $this->request->get['filter_sell_quantity_form'];

		}


		if (isset($this->request->get['filter_sell_quantity_to'])) {

			$url .= '&filter_sell_quantity_to=' . $this->request->get['filter_sell_quantity_to'];

		}


		if (isset($this->request->get['filter_price_form'])) {

			$url .= '&filter_price_form=' . $this->request->get['filter_price_form'];

		}


		if (isset($this->request->get['filter_price_to'])) {

			$url .= '&filter_price_to=' . $this->request->get['filter_price_to'];

		}


		if (isset($this->request->get['filter_avi_quantity_form'])) {

			$url .= '&filter_avi_quantity_form=' . $this->request->get['filter_avi_quantity_form'];

		}


		if (isset($this->request->get['filter_avi_quantity_to'])) {

			$url .= '&filter_avi_quantity_to=' . $this->request->get['filter_avi_quantity_to'];

		}


		if (isset($this->request->get['filter_date_form'])) {

			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];

		}


		if (isset($this->request->get['filter_date_to'])) {

			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];

		}


		if ($order == 'ASC') {

			$url .= '&order=DESC';

		} else {

			$url .= '&order=ASC';

		}



		if (isset($this->request->get['page'])) {

			$url .= '&page=' . $this->request->get['page'];

		}

		

		$data['sort']  = $sort;

		$data['order'] = $order;

		$data['sort_product_id'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '&sort=product_id' . $url, true);

		$data['sort_image'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '&sort=image' . $url, true);

		$data['sort_name'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

		$data['sort_quantity'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '&sort=quantity' . $url, true);

		$data['sort_price'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '&sort=price' . $url, true);

		$data['sort_cost'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '&sort=cost' . $url, true);

		$data['sort_profit'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '&sort=profit' . $url, true);

		$data['sort_upc'] = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . '&sort=upc' . $url, true);

		if (isset($this->session->data['success'])) {

			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);

		} else {

			$data['success'] = '';

		}



		$data['sort']  = $sort;

		$data['order'] = $order;

		$data['packages']=array();

		   

		$url = '';



		if (isset($this->request->get['filter_upc'])) {

			$url .= '&filter_upc=' . $this->request->get['filter_upc'];

		}

		if (isset($this->request->get['filter_product'])) {

			$url .= '&filter_product=' . $this->request->get['filter_product'];

		}


		if (isset($this->request->get['filter_product_id_form'])) {

			$url .= '&filter_product_id_form=' . $this->request->get['filter_product_id_form'];

		}


		if (isset($this->request->get['filter_product_id_to'])) {

			$url .= '&filter_product_id_to=' . $this->request->get['filter_product_id_to'];

		}


		if (isset($this->request->get['filter_sell_quantity_form'])) {

			$url .= '&filter_sell_quantity_form=' . $this->request->get['filter_sell_quantity_form'];

		}


		if (isset($this->request->get['filter_sell_quantity_to'])) {

			$url .= '&filter_sell_quantity_to=' . $this->request->get['filter_sell_quantity_to'];

		}


		if (isset($this->request->get['filter_price_form'])) {

			$url .= '&filter_price_form=' . $this->request->get['filter_price_form'];

		}


		if (isset($this->request->get['filter_price_to'])) {

			$url .= '&filter_price_to=' . $this->request->get['filter_price_to'];

		}


		if (isset($this->request->get['filter_avi_quantity_form'])) {

			$url .= '&filter_avi_quantity_form=' . $this->request->get['filter_avi_quantity_form'];

		}


		if (isset($this->request->get['filter_avi_quantity_to'])) {

			$url .= '&filter_avi_quantity_to=' . $this->request->get['filter_avi_quantity_to'];

		}


		if (isset($this->request->get['filter_date_form'])) {

			$url .= '&filter_date_form=' . $this->request->get['filter_date_form'];

		}


		if (isset($this->request->get['filter_date_to'])) {

			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];

		}

		if (isset($this->request->get['sort'])) {

			$url .= '&sort=' . $this->request->get['sort'];

		}



		if (isset($this->request->get['order'])) {

			$url .= '&order=' . $this->request->get['order'];

		}

        

		/*$pagination 	   = new Pagination();

		$pagination->total = $result_total;

		$pagination->page  = $page;

		$pagination->limit = $this->config->get('config_limit_admin');

		$pagination->url   = $this->url->link('possetting/cost_report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination']= $pagination->render();



		$data['results'] = sprintf($this->language->get('text_pagination'), ($result_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($result_total - $this->config->get('config_limit_admin'))) ? $result_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $result_total, ceil($result_total / $this->config->get('config_limit_admin')));
*/
		$data['filter_upc'] = $filter_upc;



		$data['sort'] = $sort;

		$data['order'] = $order;
		$data['filter_product'] = $filter_product;
		$data['filter_product_id_form'] = $filter_product_id_form;
		$data['filter_product_id_to'] = $filter_product_id_to;
		$data['filter_sell_quantity_form'] = $filter_sell_quantity_form;
		$data['filter_sell_quantity_to'] = $filter_sell_quantity_to;
		$data['filter_price_form'] = $filter_price_form;
		$data['filter_price_to'] = $filter_price_to;
		$data['filter_avi_quantity_form'] = $filter_avi_quantity_form;
		$data['filter_avi_quantity_to'] = $filter_avi_quantity_to;
		$data['filter_date_form'] = $filter_date_form;
		$data['filter_date_to'] = $filter_date_to;



		$data['header']      = $this->load->controller('common/header');

		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/cost_report', $data));

	}



}