<?php
class ModelExtensionShippingPosship extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/posship');
		$apikey = $this->config->get('module_posapp_apikey');
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_posship_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_posship_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		}   else {
			$status = false;
		}


		if ($address) {
			$coords = $this->getCoordinatesCustomer($address['address_1'] . ' ' . $address['city'] . ' ' . $address['zone'] . ' ' . $address['country'], $apikey);
		} else {
			$coords = array('lng' => 0, 'lat' => 0);
		}

		$pos_units= array(
			'k' => array('km', 1.60934),
			'm' => array('miles', 1)
		);

		if ($coords) {
			$units = (array)$pos_units;
			$unit = $units['k'];
		}
				// $data['posapp'] = array();
		$limit = 5;

		if(isset($this->session->data['checkout_posapp_id'])){
			$checkout_posapp_id = $this->session->data['checkout_posapp_id'];
		} else{
			$checkout_posapp_id = 0;
		}

		$filter_data = array(
			'longitude' => $coords['lng'],
			'latitude'  => $coords['lat'],
			'checkout_posapp_id' => $checkout_posapp_id ,
			'limit'     => $limit
		);
		
		if($coords){
			$posapp = $this->getPosapp($filter_data);
			$dis = round($posapp['distance'] * $unit[1], 2);
			$distance = sprintf($this->language->get('text_distance'), $dis, $unit[0]);
			$this->session->data['pos_distance'] = $distance;
			$allowed_distance = (int)$posapp['alloweddistance'];
			$title_quote = $posapp['posname'] . ' Shipping' . $distance;
			$cost_quote = (int)$posapp['cost'];
		}
		
		if( $allowed_distance < $dis ){
			$status = false;			    
		}
		$method_data = array();
		
		if ($status) {
			$quote_data = array();

			$quote_data['posship'] = array(
				'code'         => 'posship.posship',
				'title'        => $title_quote,
				'cost'         => $cost_quote,
				'tax_class_id' => $this->config->get('shipping_posship_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate((int)$cost_quote, $this->config->get('shipping_posship_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'posship',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_posship_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}

	public function getNearestPos($data = array()){

		$sql = "SELECT pa.*, (SELECT name FROM " . DB_PREFIX . "country c WHERE c.country_id = pa.country_id) AS country, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = pa.zone_id) AS zone, (3959 * ACOS(COS(RADIANS(" . $this->db->escape($data['latitude']) . ")) * COS(RADIANS(pa.latitude)) * COS(RADIANS(pa.longitude) - RADIANS(" . $this->db->escape($data['longitude']) . ")) + SIN(RADIANS(" . $this->db->escape($data['latitude']) . ")) * SIN(RADIANS(pa.latitude)))) AS distance FROM " . DB_PREFIX . "pos_application pa WHERE pa.status = '1' ORDER BY distance ASC LIMIT ". $this->db->escape($data['limit']) ."";		

		$query = $this->db->query($sql);

		return $query->rows;      

	}

	public function getCoordinatesCustomer($address, $key) {
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $key;

		if (extension_loaded('curl')) {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0");
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = curl_exec($ch);

			curl_close($ch);
		} elseif (ini_get('allow_url_fopen')) {
			$result = file_get_contents($url);
		}

		if (isset($result)) {
			$response = json_decode($result, true);

			if (isset($response['status']) && $response['status'] == 'OK') {
				$geog = (isset($response['results'][0])) ? $response['results'][0]['geometry']['location'] : $response['results']['geometry']['location'];

				return array('lng' => $geog['lng'], 'lat' => $geog['lat']);
			} elseif (isset($response['error_message'])) {
				$this->log->write('Google Maps STATUS: ' . $response['status'] . ' - ' . $response['error_message']);
			} else {
				$this->log->write('Google Maps STATUS: Unspecified Error. Customer Address: ' . $address);
			}
		}

		return array();
	}
	public function getPosapp($data = array()) {

		$config_geo_parts = explode(' ', (string)$this->config->get('config_geocode'));	
		$config_latitude = $config_geo_parts[0];
		$config_longitude = $config_geo_parts[1];

		if($data['checkout_posapp_id'] > 0){
			$sql = "SELECT pa.*, (SELECT name FROM " . DB_PREFIX . "country c WHERE c.country_id = pa.country_id) AS country, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = pa.zone_id) AS zone, (3959 * ACOS(COS(RADIANS(" . $this->db->escape($data['latitude']) . ")) * COS(RADIANS(pa.latitude)) * COS(RADIANS(pa.longitude) - RADIANS(" . $this->db->escape($data['longitude']) . ")) + SIN(RADIANS(" . $this->db->escape($data['latitude']) . ")) * SIN(RADIANS(pa.latitude)))) AS distance FROM " . DB_PREFIX . "pos_application pa WHERE pa.posapp_id = '". (int)$this->db->escape($data['checkout_posapp_id']) ."' AND pa.status = '1'";
			$query = $this->db->query($sql);
			return $query->row;
		}else{
			$sql = array(
				'distance' => (3959 * ACOS(COS(deg2rad((int)$data['latitude'])) * COS(deg2rad((int)$config_latitude)) * COS(deg2rad((int)$config_longitude) - deg2rad((int)$data['longitude'])) + SIN(deg2rad((int)$data['latitude'])) * SIN(deg2rad((int)$config_latitude)))),
				'posname' =>  $this->config->get('config_name'),
				'cost'    => (int)$this->config->get('shipping_posship_cost'),
				'alloweddistance' => (int)$this->config->get('shipping_posship_alloweddistance')

			);
			return (array)$sql; 

		}		

		
	}
}


