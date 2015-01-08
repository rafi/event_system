<?php
namespace App;

use Rafi\Dependency\Container;
use Rafi\Event;
use Rafi\Storage\MySQL;

class Manager {
	use Container;

	public function __construct(array $config)
	{
		// Create a singleton event bus and inject it to container
		$this->set('event', Event\Bus::instance());

		// Create a singleton database and inject it to container
		$this->set('database', MySQL\Database::instance($config));
	}

	/**
	 * @var  array  Types of errors to display at shutdown
	 */
	public static $shutdown_errors = [ E_PARSE, E_ERROR, E_USER_ERROR ];

	/**
	 * PHP error handler, converts all errors into ErrorExceptions. This handler
	 * respects error_reporting settings.
	 *
	 * @throws  ErrorException
	 * @return  TRUE
	 */
	public static function error_handler($code, $error, $file = NULL, $line = NULL)
	{
		if (error_reporting() & $code)
		{
			// This error is not suppressed by current error reporting settings
			// Convert the error into an ErrorException
			throw new \ErrorException($error, $code, 0, $file, $line);
		}

		// Do not execute the PHP error handler
		return TRUE;
	}

	/**
	 * Catches errors that are not caught by the error handler, such as E_PARSE.
	 *
	 * @return  void
	 */
	public static function shutdown_handler()
	{
		if ($error = error_get_last() AND in_array($error['type'], self::$shutdown_errors))
		{
			// Clean the output buffer
			ob_get_level() AND ob_clean();

			// Fake an exception for nice debugging
			Exception::handler(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));

			// Shutdown now to avoid a "death loop"
			exit(1);
		}
	}
}
