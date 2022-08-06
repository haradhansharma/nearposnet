<?php
class ControllerPossettingBarcode extends Controller {
 	private $error = array();
	public function index() {
		////sharma for pos
		if(($this->cart->getCommissionPending() > $this->config->get('module_posapp_payment_limit')) &&  ($this->cart->getRegdiff() > $this->config->get('module_posapp_payment_trail_allowed_day') ) ){
			$this->load->language('extension/module/posapp');
			$this->session->data['success'] = sprintf($this->language->get('text_limit_over'), '<b>Limit is: ' . $this->currency->format($this->config->get('module_posapp_payment_limit'), $this->config->get('config_currency')).'</b>', 'But current due is: '.'<b>'.$this->currency->format($this->cart->getCommissionPending(), $this->config->get('config_currency')) . '</b>' ) ;
			$this->response->redirect($this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'], true));
		}
		$this->load->language('possetting/barcode');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/barcode');
		
		$this->getList();
	}

	public function editqty() {
		$this->load->language('possetting/barcode');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/barcode');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_possetting_barcode->updateOpionQTY($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
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
			
			$this->response->redirect($this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

 	public function getList() {
	 	if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
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
	 
	 	if (isset($this->request->get['filter_store'])) {
		$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
	 
	 	if (isset($this->request->get['filter_name'])) {
		$url .= '&filter_name=' . $this->request->get['filter_name'];
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
			'href' => $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'], true)
		);
		
	
		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_quantity' => $filter_quantity,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
			
		$this->load->model('tool/image');	
		$this->load->model('catalog/option');	

		$product_total = $this->model_possetting_barcode->getTotalBarcodes($filter_data);
		$results=$this->model_possetting_barcode->getProductBarcodes($filter_data);

		foreach($results as $result) {
			$barcodes=array();
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
						
			$option_data = array();

			$product_options = $this->model_possetting_barcode->getProductOptions($result['product_id']);
			
			if(!empty($product_options)) {

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
										
					if ($option_info) {

						$product_option_value_data = array();
						
						foreach ($product_option['product_option_value'] as $product_option_value) {
							
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
							
							$optionvalue_upc = $this->model_possetting_barcode->getProductOptionUpc($product_option_value['product_option_value_id']);
							if(isset($optionvalue_upc['upc'])){
								$upc= $optionvalue_upc['upc'];
							} else {
								$upc='';
							}

							if ($option_value_info) {
								
								$product_option_value_data[] = array(
									'name'                    => $option_value_info['name'],
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $option_value_info['option_value_id'],
									'quantity'                => $product_option_value['quantity'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix'],
									'upc'            		  => $upc,
								);
							}
							if(!empty($upc)) {
								$filename=$result['product_id'];

								if(!empty($product_option_value['product_option_value_id'])) {
									$filename.='-'.$product_option_value['product_option_value_id'];
								}
								$filename.='.png';
								$fullpath=DIR_IMAGE.'catalog/barcode/'.$filename;
								$httpath=HTTP_CATALOG.'image/catalog/barcode/'.$filename;
								if(file_exists($fullpath)) {
									$barcodes[]=array('url'=>$httpath,'upc'=>$upc);
								}
							}
								
						}
					}
					$option_data[] = array(
						'product_option_value' => $product_option_value_data,
						'name'                 => $option_info['name'],
						'product_id'           => $product_option['product_id'],
						'type'                 => $option_info['type']
					);
					
				}
			} else {
				$barcodes[]=array(
					'product_id' =>$result['product_id'],
					'upc'        =>$result['upc'],
				);
			}

			if(!empty($result['upc'])) {
				$filename=$result['product_id'];
				$filename.='.png';
				$fullpath=DIR_IMAGE.'catalog/barcode/'.$filename;
				$httpath=HTTP_CATALOG.'image/catalog/barcode/'.$filename;
				if(file_exists($fullpath)){
					$barcodes[]=array('url'=>$httpath,'upc'=>$result['upc']);
				}
				
			}

			/* 06 11 2019 */
			$qrcodes = array();
			if(!empty($result['upc'])) {
				$filename=$result['product_id'];
				$filename.='.png';
				$fullpath=DIR_IMAGE.'catalog/qrcode/'.$filename;
				$httpath=HTTP_CATALOG.'image/catalog/qrcode/'.$filename;
				if(file_exists($fullpath)){
					$qrcodes[]=array('url'=>$httpath,'upc'=>$result['upc']);
				}

			}
/* 06 11 2019 */

			$barcodeheight = $this->config->get('setting_barcodeheight');
			$barcodewidth = $this->config->get('setting_barcodewidth');
			if(isset($barcodeheight)) {
				$barcodeheight = $barcodeheight;
			} else {
				$barcodeheight =40;
			}

			if(isset($barcodewidth)) {
				$barcodewidth = $barcodewidth;
			} else {
				$barcodewidth =135;
			}

			$usecode = $this->config->get('setting_usecode');
			if($usecode=='name') {
				$result['upc'] = $result['name'];
			} elseif ($usecode=='model') {
				$result['upc'] = $result['model'];
			} elseif ($usecode=='isbn') {
				$result['upc'] = $result['isbn'];
			} elseif ($usecode=='upc') {
				$result['upc'] = $result['upc'];
			} else {
				$result['upc'] = substr(strip_tags(html_entity_decode($result['description'])),0,50);
			}

			$data['products'][]=array(
				'product_id' =>$result['product_id'],
				'image'      => $image,
				'name'       =>$result['name'],
				'upc'        =>$result['upc'],
				'model'      =>$result['model'],
				'quantity'   =>$result['quantity'],
				'barcodes'	 =>$barcodes,
				'qrcodes'	 =>$qrcodes,
				'barcodeheight'	 =>$barcodeheight,
				'barcodewidth'	 =>$barcodewidth,
				'option_data'=>$option_data,
				'generate'   =>$this->url->link('possetting/barcode/generate', 'user_token=' . $this->session->data['user_token'] .'&product_id=' . $result['product_id'], true)
			);

		}
   		
   	/* 24 09 2019 */
		$data['button_coppy']      	= $this->language->get('button_coppy');
		$data['button_qr']      	= $this->language->get('button_qr');
   	/* 24 09 2019 */
		$data['heading_title']       = $this->language->get('heading_title');
		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_nooption'] 		 = $this->language->get('text_nooption');
		$data['text_upc'] 		     = $this->language->get('text_upc');
		$data['column_id'] 		 	 = $this->language->get('column_id');
		$data['column_image'] 		 = $this->language->get('column_image');
		$data['column_product_name'] = $this->language->get('column_product_name');
		$data['column_model'] 		 = $this->language->get('column_model');
		$data['column_upc'] 		 = $this->language->get('column_upc');
		$data['column_barcode'] 	 = $this->language->get('column_barcode');
		$data['column_product_option']= $this->language->get('column_product_option');
		$data['column_quantity'] 	 = $this->language->get('column_quantity');
		$data['column_images'] 		 = $this->language->get('column_images');
		$data['column_action'] 		 = $this->language->get('column_action');
		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_generate'] 	 = $this->language->get('button_generate');
		$data['button_save'] 		 = $this->language->get('button_save');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['user_token']               = $this->session->data['user_token'];
		
		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}
	$url = '';

			 if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
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
		$data['action'] = $this->url->link('possetting/barcode/editqty', 'user_token=' . $this->session->data['user_token'].$url, true);
		$data['generatelabel'] = $this->url->link('possetting/barcode/labelgenerate', 'user_token=' . $this->session->data['user_token'].$url, true);
		
		$data['generatelabelqr'] = $this->url->link('possetting/barcode/labelgenerateqr', 'user_token=' . $this->session->data['user_token'].$url, true);
		
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_id'] 			= $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . '&sort=id' . $url, true);
		$data['sort_product_name'] = $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . '&sort=product_name' . $url, true);
		$data['sort_model']		   = $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . '&sort=model' . $url, true);
		$data['sort_barcode']  	   = $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . '&sort=barcode' . $url, true);
		$data['sort_image']   	   = $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . '&sort=image' . $url, true);
		$data['sort_quantity']     = $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . '&sort=quantity' . $url, true);
		$data['sort_product_option']= $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . '&sort=product_option' . $url, true);
		
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['sort'])) {
		$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
		$url .= '&order=' . $this->request->get['order'];
		}
        
		$pagination 	   = new Pagination();
		$pagination->total = $product_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_quantity'] = $filter_quantity;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/barcode', $data));
	}
 
	public function generate() {
		$this->load->language('possetting/barcode');
		$this->load->model('possetting/barcode');
		$this->load->model('catalog/option');
		$this->load->model('catalog/product');
							
		$this->document->setTitle($this->language->get('heading_generate'));
		$data['heading_generate']  	= $this->language->get('heading_generate');
		$data['text_glist']         = $this->language->get('text_glist');
				
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
		 	$product_id = 0;
		}

		$product = $this->model_possetting_barcode->getProductUpc($product_id);
		$barcodes= array();

		$special = false;

		$product_specials = $this->model_catalog_product->getProductSpecials($product_id);

		foreach ($product_specials  as $product_special) {
			if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
				$special = $product_special['price'];
				break;
			}
		}

		$price=0;
		$option_price=0;
		$totalprice=0;
		if ((float)$special) {
			$price = $special;
		} else {
			$price = $product['price'];
		}

		$data['option_data'] = array();
		$product_options = $this->model_possetting_barcode->getProductOptions($product_id);
		
		if(!empty($product_options)) {
			foreach ($product_options as $product_option) {
				$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
				
				if ($option_info) {

					$barcode = array();

					foreach ($product_option['product_option_value'] as $product_option_value) {
						if ($product_option_value['price_prefix'] == '+') {
							$option_price += $product_option_value['price'];
						} elseif ($product_option_value['price_prefix'] == '-') {
							$option_price -= $product_option_value['price'];
						}
						$totalprice=$this->currency->format($price+$option_price, $this->config->get('config_currency'));

						$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
						
						$optionvalue_upc = $this->model_possetting_barcode->getProductOptionUpc($product_option_value['product_option_value_id']);
						if(isset($optionvalue_upc['upc'])){
							$upc= $optionvalue_upc['upc'];
						
						if ($option_value_info) {
							$barcodes[] = array(
								'product_id' => $product_id,
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'upc'            		  => $upc,
								'price'            		  => $totalprice
							);
							
						}
						}
					}
					
				}
			}
		}
		if (!empty($product) && empty($barcodes)) {
			$barcodes[] = array(
				'product_id' => $product['product_id'],
				'product_option_value_id' => '',
				'price'               => $this->currency->format($product['price'], $this->config->get('config_currency')),
				'upc'            	=> $product['upc']
			);
		}
		require_once(DIR_SYSTEM.'/library/barcode/BarcodeGenerator.php');
		require_once(DIR_SYSTEM.'/library/barcode/BarcodeGeneratorPNG.php');
		$pageheight 	= $this->config->get('setting_pageheight');
		$pagewidth 		= $this->config->get('setting_pagewidth');
		if(isset($pageheight)) {
			$pageheight = $pageheight;
		} else {
			$pageheight =160;
		}

		if(isset($pagewidth)) {
			$pagewidth = $pagewidth;
		} else {
			$pagewidth =600;
		}
		echo "<div style='height:$pageheight;width:$pagewidth'>";
		foreach($barcodes as $barcode) {
			if(!empty($barcode['upc'])) {
				$filename=$barcode['product_id'];
				if(!empty($barcode['product_option_value_id'])) {
					$filename.='-'.$barcode['product_option_value_id'];
				}
				$filename.='.png';
				$fullpath=DIR_IMAGE.'catalog/barcode/'.$filename;
				$httpath=HTTP_CATALOG.'image/catalog/barcode/'.$filename;
				$generator = new Picqer\Barcode\BarcodeGeneratorPNG(); 
				$codeimage=$generator->getBarcode($barcode['upc'], $generator::TYPE_CODE_128);
				file_put_contents($fullpath,$codeimage);

				$barcodeheight 	= $this->config->get('setting_barcodeheight');
				$barcodewidth 	= $this->config->get('setting_barcodewidth');
				$showname 		= $this->config->get('setting_showname');
				$showprice 		= $this->config->get('setting_showprice');
				
				if(isset($barcodeheight)) {
					$barcodeheight = $barcodeheight;
				} else {
					$barcodeheight =40;
				}

				if(isset($barcodewidth)) {
					$barcodewidth = $barcodewidth;
				} else {
					$barcodewidth =135;
				}

				if($showname==1) {
					$barcode['upc'] = $barcode['upc'];
				} else {
					$barcode['upc'] ='';
				}

				if($showprice==1) {
					$barcode['price'] = $barcode['price'];
				} else {
					$barcode['price'] ='';
				}

				$barlabelqty = $this->config->get('setting_labelqty');
				$labelqtymin = 1;
				if(isset($barlabelqty)){
					$labelqty = $barlabelqty;
				} else {
					$labelqty=1;
				}

				if(!empty($barlabelqty)) {
					while ($labelqtymin <= $labelqty) {
						echo "<div style='text-align:center;float:left;margin-right:10px;'><img height='".$barcodeheight."' width='".$barcodewidth."' src='".$httpath."' /> </br> ".$barcode['upc'].' '.$barcode['price'].'</div>';
						$labelqtymin++;
					}
				} else {
					echo "<div style='text-align:center;float:left;margin-right:10px;'><img height='".$barcodeheight."' width='".$barcodewidth."' src='".$httpath."' /> </br> ".$barcode['upc'].' '.$barcode['price'].'</div>';
				}

			}
		}
		echo "</div>";

	}

	public function labelgenerate() {
	    $this->load->language('possetting/barcode');
	    $this->load->model('possetting/barcode');
	    $this->load->model('catalog/option');
	    $this->load->model('catalog/product');
	    
	    $this->document->setTitle($this->language->get('heading_generate'));	        
	    $data['user_token'] = $this->session->data['user_token'];

	    $labelgenerates = array();

		if (isset($this->request->post['selected'])) {
			$labelgenerates = $this->request->post['selected'];
		} elseif (isset($this->request->get['product_id'])) {
			$labelgenerates[] = $this->request->get['product_id'];
		}

		if(empty($labelgenerates)) {
			$this->session->data['warning'] = 'Request Error: Product Id Missing ';
			$this->response->redirect($this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . $url, true));
		} else {
			
			foreach($labelgenerates as $product_id) {
			    $product = $this->model_possetting_barcode->getProductUpc($product_id);
			    $barcodes= array();

			    $special = false;

				$product_specials = $this->model_catalog_product->getProductSpecials($product_id);

				foreach ($product_specials  as $product_special) {
					if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
						$special = $product_special['price'];
						break;
					}
				}

				$price=0;
				$option_price=0;
				if ((float)$special) {
					$price = $special;
				} else {
					$price = $product['price'];
				}

			    $data['option_data'] = array();
			    $product_options = $this->model_possetting_barcode->getProductOptions($product_id);
			    
			    if(!empty($product_options)) {
			      foreach ($product_options as $product_option) {
			        $option_info = $this->model_catalog_option->getOption($product_option['option_id']);
			        
			        if ($option_info) {

			          $barcode = array();
			          foreach ($product_option['product_option_value'] as $product_option_value) {
			          	if ($product_option_value['price_prefix'] == '+') {
							$option_price += $product_option_value['price'];
						} elseif ($product_option_value['price_prefix'] == '-') {
							$option_price -= $product_option_value['price'];
						}
						$totalprice=$this->currency->format($price+$option_price, $this->config->get('config_currency'));
			          	
			            $option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
			            
			            $optionvalue_upc = $this->model_possetting_barcode->getProductOptionUpc($product_option_value['product_option_value_id']);
			            if(isset($optionvalue_upc['upc'])){
			              $upc= $optionvalue_upc['upc'];
			            
			            if ($option_value_info) {
			              $barcodes[] = array(
			                'product_id' => $product_id,
			                'product_option_value_id' => $product_option_value['product_option_value_id'],
			                'upc'                 	=> $upc,
			                'price'          		=> $totalprice
			              );
			              
			            }
			            }
			          }
			          
			        }
			      }
			    }
				if (!empty($product) && empty($barcodes)) {
					$barcodes[] = array(
						'product_id' => $product['product_id'],
						'product_option_value_id' => '',
						'price'               => $this->currency->format($product['price'], $this->config->get('config_currency')),
						'upc'            	=> $product['upc']
					);
				}
			    require_once(DIR_SYSTEM.'/library/barcode/BarcodeGenerator.php');
			    require_once(DIR_SYSTEM.'/library/barcode/BarcodeGeneratorPNG.php');
			    $pageheight 	= $this->config->get('setting_pageheight');
				$pagewidth 		= $this->config->get('setting_pagewidth');
				if(isset($pageheight)) {
					$pageheight = $pageheight;
				} else {
					$pageheight =150;
				}
				

				if(isset($pagewidth)) {
					$pagewidth = $pagewidth;
				} else {
					$pagewidth =600;
				}
				$barcodeheight 	= $this->config->get('setting_barcodeheight');
				$barcodewidth 	= $this->config->get('setting_barcodewidth');
				$showname 		= $this->config->get('setting_showname');
				$showprice 		= $this->config->get('setting_showprice');
						
				$pageheight=$pageheight."px";
				$pagewidth=$pagewidth."px";
				$divholder=($barcodewidth+10)."px";
				
			    echo "<div style='height:$pageheight;width:$pagewidth'>";
			    foreach($barcodes as $barcode) {
			      	if(!empty($barcode['upc'])) {
						$filename=$barcode['product_id'];
						if(!empty($barcode['product_option_value_id'])) {
							$filename.='-'.$barcode['product_option_value_id'];
						}
						$filename.='.png';
						$fullpath=DIR_IMAGE.'catalog/barcode/'.$filename;
						$httpath=HTTP_CATALOG.'image/catalog/barcode/'.$filename;
						$generator = new Picqer\Barcode\BarcodeGeneratorPNG(); 
						$codeimage=$generator->getBarcode($barcode['upc'], $generator::TYPE_CODE_128);
						file_put_contents($fullpath,$codeimage);

						
						
						if(isset($barcodeheight)) {
							$barcodeheight = $barcodeheight;
						} else {
							$barcodeheight =40;
						}

						if(isset($barcodewidth)) {
							$barcodewidth = $barcodewidth;
						} else {
							$barcodewidth =135;
						}

						if($showname==1) {
							$barcode['upc'] = $barcode['upc'];
						} else {
							$barcode['upc'] ='';
						}

						if($showprice==1) {
							$barcode['price'] = $barcode['price'];
						} else {
							$barcode['price'] ='';
						}

						$barlabelqty = $this->config->get('setting_labelqty');
						$labelqtymin = 1;
						if(isset($barlabelqty)){
							$labelqty = $barlabelqty;
						} else {
							$labelqty=1;
						}

						if(!empty($barlabelqty)) {
							while ($labelqtymin <= $labelqty) {
								echo "<div style='float:left;width:$divholder' ><img height='".$barcodeheight."' width='".$barcodewidth."' src='".$httpath."' /> </br><div style='text-align:center'> ".$barcode['upc'].' '.$barcode['price'].'</div></div>';
								$labelqtymin++;
							}
						} else {
							echo "<div style='float:left;width:$divholder'><img height='".$barcodeheight."' width='".$barcodewidth."' src='".$httpath."' /> </br><div style='text-align:center'> ".$barcode['upc'].' '.$barcode['price'].'</div></div>';
						}
			      	}
			    }
			    echo "</div>";
			}
		}
  	}

	public function labelgenerateqr() {
	    $this->load->language('possetting/barcode');
	    $this->load->model('possetting/barcode');
	    $this->load->model('catalog/option');
	    $this->load->model('catalog/product');
	    
	    $this->document->setTitle($this->language->get('heading_generate'));	        
	    $data['user_token'] = $this->session->data['user_token'];

	    $labelgenerates = array();

		if (isset($this->request->post['selected'])) {
			$labelgenerates = $this->request->post['selected'];
		} elseif (isset($this->request->get['product_id'])) {
			$labelgenerates[] = $this->request->get['product_id'];
		}

		if(empty($labelgenerates)) {
			$this->session->data['warning'] = 'Request Error: Product Id Missing ';
			$this->response->redirect($this->url->link('possetting/barcode', 'user_token=' . $this->session->data['user_token'] . $url, true));
		} else {
			
			foreach($labelgenerates as $product_id) {
			    $product = $this->model_possetting_barcode->getProductUpc($product_id);
			    $qrcodes= array();

			    $special = false;

				$product_specials = $this->model_catalog_product->getProductSpecials($product_id);

				foreach ($product_specials  as $product_special) {
					if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
						$special = $product_special['price'];
						break;
					}
				}

				$price=0;
				$option_price=0;
				if ((float)$special) {
					$price = $special;
				} else {
					$price = $product['price'];
				}

			    $data['option_data'] = array();
			    $product_options = $this->model_possetting_barcode->getProductOptions($product_id);
			    
			    if(!empty($product_options)) {
			      foreach ($product_options as $product_option) {
			        $option_info = $this->model_catalog_option->getOption($product_option['option_id']);
			        
			        if ($option_info) {

			          $barcode = array();
			          foreach ($product_option['product_option_value'] as $product_option_value) {
			          	if ($product_option_value['price_prefix'] == '+') {
							$option_price += $product_option_value['price'];
						} elseif ($product_option_value['price_prefix'] == '-') {
							$option_price -= $product_option_value['price'];
						}
						$totalprice=$this->currency->format($price+$option_price, $this->config->get('config_currency'));
			          	
			            $option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
			            
			            $optionvalue_upc = $this->model_possetting_barcode->getProductOptionUpc($product_option_value['product_option_value_id']);
			            if(isset($optionvalue_upc['upc'])){
			              $upc= $optionvalue_upc['upc'];
			            
			            if ($option_value_info) {
			              $qrcodes[] = array(
			                'product_id' => $product_id,
			                'product_option_value_id' => $product_option_value['product_option_value_id'],
			                'upc'                 	=> $upc,
			                'price'          		=> $totalprice
			              );
			              
			            }
			            }
			          }
			          
			        }
			      }
			    } else {
			      
			      if (isset($product)) {
			        $qrcodes[] = array(
			          'product_id' => $product['product_id'],
			          'product_option_value_id' => '',
			          'price'               => $this->currency->format($product['price'], $this->config->get('config_currency')),
			          'upc'                 => $product['upc']
			        );
			      }
			    }

					
				require_once(DIR_SYSTEM.'/library/barcode/phpqrcode/qrlib.php');
				$pageheight 	= $this->config->get('setting_pageheight');
				$pagewidth 		= $this->config->get('setting_pagewidth');
				if(isset($pageheight)) {
					$pageheight = $pageheight;
				} else {
					$pageheight =150;
				}
				

				if(isset($pagewidth)) {
					$pagewidth = $pagewidth;
				} else {
					$pagewidth =600;
				}

				$barcodeheight 	= $this->config->get('setting_barcodeheight');
				$barcodewidth 	= $this->config->get('setting_barcodewidth');
				$showname 		= $this->config->get('setting_showname');
				$showprice 		= $this->config->get('setting_showprice');

				$pageheight=$pageheight."px";
				$pagewidth=$pagewidth."px";
				$divholder=($barcodewidth+10)."px";
			
				echo "<div style='height:$pageheight;width:$pagewidth'>";
			    foreach($qrcodes as $qrcode) {
			      	if(!empty($qrcode['upc'])) {
			      		$filename=$qrcode['product_id'];
						if(!empty($qrcode['product_option_value_id'])) {
							$filename.='-'.$qrcode['product_option_value_id'];
						}
			      		$filename .= '.png';
						$qrroot = DIR_IMAGE.'catalog/qrcode';
						if (!file_exists($qrroot)) {
							mkdir($qrroot);
						}
						$file_img= $qrroot.'/'.$filename;
						$httpath=HTTP_CATALOG.'image/catalog/qrcode/'.$filename;
						$qrvalue = $qrcode['upc'];
						QRcode::png($qrvalue,$file_img);
						
						if(isset($barcodeheight)) {
							$barcodeheight = $barcodeheight;
						} else {
							$barcodeheight =40;
						}

						if(isset($barcodewidth)) {
							$barcodewidth = $barcodewidth;
						} else {
							$barcodewidth =135;
						}

						if($showname==1) {
							$qrcode['upc'] = $qrcode['upc'];
						} else {
							$qrcode['upc'] ='';
						}

						if($showprice==1) {
							$qrcode['price'] = $qrcode['price'];
						} else {
							$qrcode['price'] ='';
						}

						$barlabelqty = $this->config->get('setting_labelqty');
						$labelqtymin = 1;
						if(isset($barlabelqty)){
							$labelqty = $barlabelqty;
						} else {
							$labelqty=1;
						}

						if(!empty($barlabelqty)) {
							while ($labelqtymin <= $labelqty) {
								echo "<div style='float:left;width:$divholder' ><img height='".$barcodeheight."' width='".$barcodewidth."' src='".$httpath."' /> </br><div style='text-align:center'>".$qrcode['upc'].' '.$qrcode['price'].'</div></div>';
								$labelqtymin++;
							}
						} else {
							echo "<div style='float:left;width:$divholder' ><img height='".$barcodeheight."' width='".$barcodewidth."' src='".$httpath."' /> </br><div style='text-align:center'> ".$qrcode['upc'].' '.$qrcode['price'].'</div></div>';
						}
			      	}
			    }
			    echo "</div>";
			}
		}
  	}
}