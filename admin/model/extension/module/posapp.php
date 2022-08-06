<?php
class ModelExtensionModulePosapp extends Model {

	public function install() {
		$check_in_user = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "pos_user` LIKE 'posapp_id'");
		if (!$check_in_user->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "pos_user` ADD  `posapp_id` int(11) NOT NULL ");
		}
		$check_in_product = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'posapp_id'");
		if (!$check_in_product->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD  `posapp_id` int(11) NOT NULL ");
		} 
		$check_in_order = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'posapp_id'");
		if (!$check_in_order->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD  `posapp_id` int(11) NOT NULL ");
		} 
		$check_in_pos_order = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "pos_order` LIKE 'posapp_id'");
		if (!$check_in_pos_order->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "pos_order` ADD  `posapp_id` int(11) NOT NULL ");
		} 
		$check_in_pos_customer = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "pos_customer` LIKE 'posapp_id'");
		if (!$check_in_pos_customer->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "pos_customer` ADD  `posapp_id` int(11) NOT NULL ");
		}
		$check_in_pos_product = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "pos_product` LIKE 'posapp_id'");
		if (!$check_in_pos_product->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "pos_product` ADD  `posapp_id` int(11) NOT NULL ");
		} 
		
		$check_in_cart = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "cart` LIKE 'posapp_id'");
		if (!$check_in_cart->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "cart` ADD  `posapp_id` int(11) NOT NULL ");
		} 

		$check_in_pos_order_return = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "pos_order_return` LIKE 'posapp_id'");
		if (!$check_in_pos_order_return->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "pos_order_return` ADD  `posapp_id` int(11) NOT NULL ");
		} 

		$check_in_return = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "return` LIKE 'posapp_id'");
		if (!$check_in_return->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "return` ADD  `posapp_id` int(11) NOT NULL ");
		} 

		$check_in_search = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer_search` LIKE 'posname'");
		if (!$check_in_search->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_search` ADD  `posname` varchar(128) NOT NULL ");
		} 

		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."service_agent (
			`service_agent_id` int(11) NOT NULL AUTO_INCREMENT,
			`customer_id` int(11) NOT NULL,
			`code_prefix` varchar(9) NOT NULL,
			`status` tinyint(1) NOT NULL DEFAULT '0',
			`tearm_agree` tinyint(1) NOT NULL DEFAULT '0',						
			`creat_date` datetime NOT NULL,
			PRIMARY KEY (`service_agent_id`),
			KEY `customer_id` (`customer_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;");  



		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."pos_application (
			`posapp_id` int(11) NOT NULL AUTO_INCREMENT,
			`customer_id` int(11) NOT NULL,
			`customer_group_id` int(11) NOT NULL,
			`username` varchar(20) NOT NULL,
			`password` varchar(40) NOT NULL,
			`salt` varchar(9) NOT NULL,
			`store_id` int(10) NOT NULL,
			`posname` varchar(128) NOT NULL,
			`agent_code` varchar(128) NOT NULL,
			`firstname` varchar(32) NOT NULL,
			`lastname` varchar(32) NOT NULL,
			`email` varchar(96) NOT NULL,
			`frontimage` varchar(255) NOT NULL,
			`tradelicenceimage` varchar(255) NOT NULL,
			`licence_validaty` datetime NOT NULL,
			`telephone` varchar(25) NOT NULL,
			`address` varchar(255) NOT NULL,
			`city` varchar(128) NOT NULL,
			`country_id` int(11) NOT NULL,
			`zone_id` int(11) NOT NULL,
			`latitude` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`longitude` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`cost` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`code` varchar(40) NOT NULL,
			`ip` varchar(40) NOT NULL,
			`status` tinyint(1) NOT NULL DEFAULT '0',			
			`commission` text NOT NULL,
			`commission_value` int(100) NOT NULL,
			`date_added` datetime NOT NULL,
			`alloweddistance` int(11) NOT NULL,				
			PRIMARY KEY (`posapp_id`),
			KEY `customer_id` (`customer_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;");   

		
		
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."pos_commission_details (
			`pos_commission_details_id` int(11) NOT NULL AUTO_INCREMENT,
			`commission_order_id` int(11) NOT NULL,
			`order_id` int(11) NOT NULL,
			`posapp_id` int(11) NOT NULL,			
			`posname` varchar(128) NOT NULL,
			`commission_value` int(100) NOT NULL,
			`commission` varchar(128) NOT NULL,			
			`total` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`payable` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`tax` decimal(15,4) NOT NULL DEFAULT '0.0000',			
			`creat_date` datetime NOT NULL,
			PRIMARY KEY (`pos_commission_details_id`),
			KEY `commission_order_id` (`commission_order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;");   



		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."pos_commission_order_total (
			`pos_commission_order_total_id` int(10) NOT NULL AUTO_INCREMENT,
			`commission_order_id` int(11) NOT NULL,
			`code` varchar(32) NOT NULL,
			`title` varchar(255) NOT NULL,
			`value` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`sort_order` int(3) NOT NULL,
			PRIMARY KEY (`pos_commission_order_total_id`),
			KEY `commission_order_id` (`commission_order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1; ") ;       
		

		

		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."pos_commission_order_history (			
			`commission_order_history_id` int(11) NOT NULL AUTO_INCREMENT,
			`commission_order_id` int(11) NOT NULL,
			`order_status_id` int(11) NOT NULL,
			`notify` tinyint(1) NOT NULL DEFAULT '0',
			`comment` text NOT NULL,
			`date_added` datetime NOT NULL,
			PRIMARY KEY (`commission_order_history_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;");

		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."posapp_to_category (
			`posapp_id` int(11) NOT NULL,
			`category_id` int(11) NOT NULL,
			PRIMARY KEY (`posapp_id`,`category_id`),
			KEY `category_id` (`category_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."pos_commission_order (
			`commission_order_id` int(11) NOT NULL AUTO_INCREMENT,  
			`invoice_prefix` varchar(26) NOT NULL,
			`posapp_id` int(11) NOT NULL DEFAULT '0',
			`posname` varchar(64) NOT NULL,
			`pos_url` varchar(255) NOT NULL,
			`store_id` int(11) NOT NULL DEFAULT '0',
			`store_name` varchar(64) NOT NULL,
			`store_url` varchar(255) NOT NULL,  
			`firstname` varchar(32) NOT NULL,
			`lastname` varchar(32) NOT NULL,
			`email` varchar(96) NOT NULL,
			`telephone` varchar(32) NOT NULL,		 
			`payment_firstname` varchar(32) NOT NULL,
			`payment_lastname` varchar(32) NOT NULL,  
			`payment_address_1` varchar(128) NOT NULL,
			`payment_address_2` varchar(128) NOT NULL,
			`payment_city` varchar(128) NOT NULL,
			`payment_postcode` varchar(10) NOT NULL,
			`payment_country` varchar(128) NOT NULL,
			`payment_country_id` int(11) NOT NULL,
			`payment_zone` varchar(128) NOT NULL,
			`payment_zone_id` int(11) NOT NULL,
			`payment_address_format` text NOT NULL,  
			`payment_method` varchar(128) NOT NULL,
			`payment_code` varchar(128) NOT NULL, 		
			`total` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`payable` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`order_status_id` int(11) NOT NULL,  
			`commission_value` decimal(15,4) NOT NULL, 
			`commission` varchar(255) NOT NULL,   
			`language_id` int(11) NOT NULL,
			`currency_id` int(11) NOT NULL,
			`currency_code` varchar(3) NOT NULL,
			`currency_value` decimal(15,8) NOT NULL DEFAULT '1.00000000',
			`ip` varchar(40) NOT NULL,
			`forwarded_ip` varchar(40) NOT NULL,
			`user_agent` varchar(255) NOT NULL,
			`accept_language` varchar(255) NOT NULL,
			`date_added` datetime NOT NULL,	
			`date_modified` datetime NOT NULL,		
			PRIMARY KEY (`commission_order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
		

	}
	function getExtensions($type) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");

		return $query->rows;
	}
	
	public function uninstall() {
		// $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."pos_application`");
		// $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."posapp_to_category`");

	} 

}