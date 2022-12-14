<?php

class ModelExtensionPaymentSSLCommerce extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('extension/payment/SSLCommerce');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_SSLCommerce_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('payment_SSLCommerce_total') > $total && !isset($this->session->data['user_token'])) {
			$status = false;
		} elseif (!$this->config->get('payment_SSLCommerce_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows && isset($this->session->data['user_token'])) {
			$status = true; 
		} else {
			$status = false;
		}	
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'SSLCommerce',
				'terms'      => '',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('payment_SSLCommerce_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>
