<?php
require realpath(dirname(__FILE__).'/..').'/env.php';
require DOCROOT.'vendor/autoload.php';

define('APP_START_TIME', microtime(TRUE));
define('APP_START_MEMORY', memory_get_usage());

ob_start();
set_exception_handler([ 'App\Exception', 'handler' ]);
set_error_handler([ 'App\Exception', 'error_handler' ]);
register_shutdown_function([ 'App\Exception', 'shutdown_handler' ]);

$config = include APPPATH.'config/database.php';
$app = new App\Manager($config);

// Load app-specific routes
$routes = include APPPATH.'config/routes.php';

echo (new Rafi\Delivery\Request([
		'app' => $app,
		'base_url' => '/event_system/srv/http',
		'routes' => $routes,
		'template_dir' => APPPATH.'media/template'
	]))
	->execute()
	->body();
