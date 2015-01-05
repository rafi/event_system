<?php
require realpath(dirname(__FILE__).'/..').'/env.php';
require DOCROOT.'vendor/autoload.php';

define('APP_START_TIME', microtime(TRUE));
define('APP_START_MEMORY', memory_get_usage());

ob_start();
set_exception_handler([ 'App\Exception', 'handler' ]);
set_error_handler([ 'App\Manager', 'error_handler' ]);
register_shutdown_function([ 'App\Manager', 'shutdown_handler' ]);

// Load app-specific routes
require APPPATH.'routes.php';

$app = new App\Manager;

echo (new Rafi\Delivery\Request([
		'app' => $app,
		'base_url' => '/srv/http',
		'template_dir' => APPPATH.'media/template'
	]))
	->execute()
//	->send_headers()
	->body();
