<?php
session_start();
date_default_timezone_set('Europe/Moscow');

define('PROTO', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://');
define('SITE_PATH', __DIR__ . '/');
define('CFG', SITE_PATH . 'config/');
define('CLS', SITE_PATH . 'cls/');
define('FUNCTIONS', SITE_PATH . 'functions/');


$cfg = [];

$server_configs = [
	'config.server.' . gethostname() . '.php',
	'config.server.php'
];

foreach ($server_configs as $server_config) {
	if (file_exists(CFG . $server_config)) {
		require_once CFG . $server_config;
		break;
	}
}


require_once FUNCTIONS . 'engine.php';
require_once FUNCTIONS . 'methods.php';
require_once CLS . 'auth.class.php';
require_once CLS . 'safemysql.class.php';
require_once CFG . 'config.inc.php';


$db = new SafeMySQL($cfg['db_settings']);

$auth = new auth(varStr('hash'));
