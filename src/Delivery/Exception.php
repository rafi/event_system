<?php
namespace Rafi\Delivery;

class Exception extends \Exception {

	/**
	 * @var  array  $php_errors  PHP error codes as human readable names
	 */
	public static $php_errors = [
		E_ERROR              => 'Fatal Error',
		E_USER_ERROR         => 'User Error',
		E_PARSE              => 'Parse Error',
		E_WARNING            => 'Warning',
		E_USER_WARNING       => 'User Warning',
		E_STRICT             => 'Strict',
		E_NOTICE             => 'Notice',
		E_RECOVERABLE_ERROR  => 'Recoverable Error',
		E_DEPRECATED         => 'Deprecated',
	];

	/**
	 * @var  string  $error_view  Error rendering view
	 */
	public static $error_view = 'app/error';

	/**
	 * @var  string  $content_type  Error view content type
	 */
	public static $content_type = 'text/html';

	/**
	 * Creates a new exception.
	 *
	 * @param   string          $message    Error message
	 * @param   array           $variables  Parameter variables
	 * @param   integer|string  $code       The exception code
	 * @param   Exception       $previous   Previous exception
	 * @return  void
	 */
	public function __construct($message = "", array $variables = NULL, $code = 0, \Exception $previous = NULL)
	{
		// Set the message
		$message = strtr($message, $variables ?: []);

		// Pass the message and integer code to the parent
		parent::__construct($message, (int) $code, $previous);

		// Save the unmodified code
		// @link http://bugs.php.net/39615
		$this->code = $code;
	}

	/**
	 * Magic object-to-string method
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return self::text($this);
	}

	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * @param   Exception  $e
	 * @return  void
	 */
	public static function handler(\Exception $e)
	{
		echo '<pre>'.$e.'</pre>';
		echo '<pre>'.$e->getTraceAsString().'</pre>';

//		$response = self::_handler($e);
//
//		// Send the response to the browser
//		echo $response->send_headers()->body();

		exit(1);
	}

	/**
	 * Exception handler, logs the exception and generates a Response object
	 * for display.
	 *
	 * @param   Exception  $e
	 * @return  Response
	 */
	public static function _handler(\Exception $e)
	{
		try
		{
			// Log the exception
			self::log($e);

			// Generate the response
			$response = self::response($e);

			return $response;
		}
		catch (\Exception $e)
		{
			/**
			 * Things are going *really* badly for us, We now have no choice
			 * but to bail. Hard.
			 */
			// Clean the output buffer if one exists
			ob_get_level() AND ob_clean();

			// Set the Status code to 500, and Content-Type to text/plain.
			header('Content-Type: text/plain; charset=utf-8', TRUE, 500);

			echo $e->getMessage();

			exit(1);
		}
	}

	/**
	 * Logs an exception.
	 *
	 * @param   Exception  $e
	 * @param   int        $level
	 * @return  void
	 */
	public static function log(\Exception $e, $level = Log::EMERGENCY)
	{
		if (is_object(App::$log))
		{
			// Create a text version of the exception
			$error = self::text($e);

			// Add this exception to the log
			App::$log->add($level, $error, NULL, [ 'exception' => $e ]);

			// Make sure the logs are written
			App::$log->write();
		}
	}

	/**
	 * Get a single line of text representing the exception:
	 *
	 * Error [ Code ]: Message ~ File [ Line ]
	 *
	 * @param   Exception  $e
	 * @return  string
	 */
	public static function text(\Exception $e)
	{
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
			get_class($e), $e->getCode(), strip_tags($e->getMessage()), $e->getFile(), $e->getLine());
	}

	/**
	 * Get a Response object representing the exception
	 *
	 * @param   Exception  $e
	 * @return  Response
	 */
	public static function response(\Exception $e)
	{
		try
		{
			// Get the exception information
			$class   = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();
			$trace   = $e->getTrace();

			if ($e instanceof \ErrorException)
			{
				/**
				 * If XDebug is installed, and this is a fatal error,
				 * use XDebug to generate the stack trace
				 */
				if (function_exists('xdebug_get_function_stack') AND $code == E_ERROR)
				{
					$trace = array_slice(array_reverse(xdebug_get_function_stack()), 4);

					foreach ($trace as & $frame)
					{
						/**
						 * XDebug pre 2.1.1 doesn't currently set the call type key
						 * http://bugs.xdebug.org/view.php?id=695
						 */
						if ( ! isset($frame['type']))
						{
							$frame['type'] = '??';
						}

						// Xdebug returns the words 'dynamic' and 'static' instead of using '->' and '::' symbols
						if ('dynamic' === $frame['type'])
						{
							$frame['type'] = '->';
						}
						elseif ('static' === $frame['type'])
						{
							$frame['type'] = '::';
						}

						// XDebug also has a different name for the parameters array
						if (isset($frame['params']) AND ! isset($frame['args']))
						{
							$frame['args'] = $frame['params'];
						}
					}
				}

				if (isset(self::$php_errors[$code]))
				{
					// Use the human-readable error name
					$code = self::$php_errors[$code];
				}
			}

			/**
			 * The stack trace becomes unmanageable inside PHPUnit.
			 *
			 * The error view ends up several GB in size, taking
			 * serveral minutes to render.
			 */
			if (defined('PHPUnit_MAIN_METHOD'))
			{
				$trace = array_slice($trace, 0, 2);
			}

			// Instantiate the error view.
			$view = View::factory(self::$error_view, get_defined_vars());

			// Prepare the response object.
			$response = (new Response)
				->body($view->render());
		}
		catch (\Exception $e)
		{
			/**
			 * Things are going badly for us, Lets try to keep things under control by
			 * generating a simpler response object.
			 */
			$response = (new Response)
				->body(self::text($e));
		}

		return $response;
	}

}
