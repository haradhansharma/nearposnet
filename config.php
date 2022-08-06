<?php
// HTTP
define('HTTP_SERVER', 'http://nearpos.net/');
define('HTTP_ADMIN', 'http://nearpos.net/admin');

// HTTPS
define('HTTPS_SERVER', 'https://nearpos.net/');
define('HTTPS_ADMIN', 'https://nearpos.net/admin');

// DIR
define('DIR_APPLICATION', '/home/sincehence/nearpos.net/catalog/');
define('DIR_SYSTEM', '/home/sincehence/nearpos.net/system/');
define('DIR_IMAGE', '/home/sincehence/nearpos.net/image/');
define('DIR_STORAGE', '/home/sincehence/nearpos_data/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');



// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'sincehence_nearpos');
define('DB_PASSWORD', 'Sharma@270815');
define('DB_DATABASE', 'sincehence_nearpos');
define('DB_PORT', '3306');
define('DB_PREFIX', 'vp_');