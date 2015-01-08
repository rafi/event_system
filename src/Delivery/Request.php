<?php
namespace Rafi\Delivery;

use ReflectionClass;

class Request {

	/**
	 * @var  string $method GET, POST, PUT, DELETE, HEAD, etc
	 */
	protected $method = 'GET';

	/**
	 * @var  string $protocol HTTP/1.1, FTP, CLI, etc
	 */
	protected $protocol = 'HTTP/1.1';

	/**
	 * @var  string $body
	 */
	protected $body;

	/**
	 * @var array   $get query parameters
	 */
	protected $get = [];

	/**
	 * @var array   $post post parameters
	 */
	protected $post = [];

	/**
		* @var array  $parameters app dependencies
	 */
	protected $parameters = [];

	/**
	 * @var  string  $prefix  namespace prefix
	 */
	protected $prefix;

	/**
	 * @var  string  $controller  controller to be executed
	 */
	protected $controller;

	/**
	 * @var  string  $action  action to be executed in the controller
	 */
	protected $action;

	/**
	 * @var  string  $uri  the URI of the request
	 */
	protected $uri;

	protected $routes = [];

	// Matches a URI group and captures the contents
	const REGEX_GROUP   = '\(((?:(?>[^()]+)|(?R))*)\)';

	// Defines the pattern of a <segment>
	const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';

	// What can be part of a <segment> value
	const REGEX_SEGMENT = '[^/.,;?\n]++';

	// What must be escaped in the route regex
	const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';

	/**
	 * Creates a new request instance
	 */
	public function __construct(array $parameters = [])
 	{
		$this->parameters = $parameters;

		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			$this->protocol = strtoupper($_SERVER['SERVER_PROTOCOL']);
		}

		if (isset($_SERVER['REQUEST_METHOD']))
		{
			$this->method = strtoupper($_SERVER['REQUEST_METHOD']);
		}

		if ($this->method !== 'GET')
		{
			// Ensure the raw body is saved for future use
			$this->body = file_get_contents('php://input');
		}

		// Query and post parameters
		$this->get = $_GET;
		$this->post = $_POST;

		$this->uri = $this->detect_uri();

		if (isset($parameters['routes']))
		{
			$this->routes = $parameters['routes'];
		}

		if (isset($parameters['base_ur']))
		{
			$parameters['base_ur'] = rtrim($parameters['base_url'], '/').'/';
		}
	}

	/**
	 * Execute request controller
	 */
	public function execute()
	{
		$response = new Response([ 'protocol' => $this->protocol ]);

		$prefix = '';
		$controller = '';
		foreach ($this->routes as $route => $route_params)
		{
			$route_regex = $this->compile($route);
			if ($match = $this->matches($route_regex, $route_params))
			{
				$prefix = '\\'.$match['prefix'].'\\';
				$controller = $match['controller'];
				$action = isset($match['action']) ? $match['action'] : 'index';
			}
		}

		if (empty($controller) || ! class_exists($prefix.$controller))
		{
			throw new Exception(
				'The requested URL :uri was not found on this server.',
				[ ':uri' => $this->uri() ]
			);
		}

		$this->prefix = $prefix;
		$this->controller = $controller;
		$this->action = $action;

		// Load the controller using reflection
		$class = new ReflectionClass($prefix.$controller);

		if ($class->isAbstract())
		{
			throw new Exception(
				'Cannot create instances of abstract :controller',
				[ ':controller' => $prefix.$controller ]
			);
		}

		// Create a new instance of the controller
		$controller = $class->newInstance($this, $response, $this->parameters);

		// Run the controller's execute() method
		$response = $class->getMethod('execute')->invokeArgs($controller, [ $class ]);

		if ( ! $response instanceof Response)
		{
			// Controller failed to return a Response.
			throw new Exception('Controller failed to return a Response');
		}

		return $response;
	}

	/**
	 * Sets and gets the uri from the request.
	 *
	 * @param   string $uri
	 * @return  mixed
	 */
	public function uri($uri = NULL)
	{
		if ($uri === NULL)
		{
			// Act as a getter
			return empty($this->uri) ? '/' : $this->uri;
		}

		// Act as a setter
		$this->uri = $uri;

		return $this;
	}

	/**
	 * Sets and gets the action for the controller.
	 *
	 * @param   string   $action  Action to execute the controller from
	 * @return  mixed
	 */
	public function action($action = NULL)
	{
		if ($action === NULL)
		{
			// Act as a getter
			return $this->action;
		}

		// Act as a setter
		$this->action = (string) $action;

		return $this;
	}

	/**
	 * Automatically detects the URI of the main request using PATH_INFO,
	 * REQUEST_URI, PHP_SELF or REDIRECT_URL.
	 *
	 * @return  string
	 */
	public function detect_uri()
	{
		if ( ! empty($_SERVER['PATH_INFO']))
		{
			// PATH_INFO does not contain the docroot or index
			$uri = $_SERVER['PATH_INFO'];
		}
		else
		{
			// REQUEST_URI and PHP_SELF include the docroot and index

			if (isset($_SERVER['REQUEST_URI']))
			{
				/**
				 * We use REQUEST_URI as the fallback value. The reason
				 * for this is we might have a malformed URL such as:
				 *
				 *  http://localhost/http://example.com/judge.php
				 *
				 * which parse_url can't handle. So rather than leave empty
				 * handed, we'll use this.
				 */
				$uri = $_SERVER['REQUEST_URI'];

				if ($request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
				{
					// Valid URL path found, set it.
					$uri = $request_uri;
				}

				// Decode the request URI
				$uri = rawurldecode($uri);
			}
			elseif (isset($_SERVER['PHP_SELF']))
			{
				$uri = $_SERVER['PHP_SELF'];
			}
			elseif (isset($_SERVER['REDIRECT_URL']))
			{
				$uri = $_SERVER['REDIRECT_URL'];
			}
			else
			{
				throw new Exception('Unable to detect the URI using PATH_INFO, REQUEST_URI, PHP_SELF or REDIRECT_URL');
			}

			// Get the path from the base URL, including the index file
			$base_url = isset($this->parameters['base_url'])
				? parse_url($this->parameters['base_url'], PHP_URL_PATH)
				: '/';

			if (strpos($uri, $base_url) === 0)
			{
				// Remove the base URL from the URI
				$uri = (string) substr($uri, strlen($base_url));
			}

			$index_file = isset($this->parameters['index_file'])
				? $this->parameters['index_file']
				: FALSE;

			if ($index_file && strpos($uri, $index_file) === 0)
			{
				// Remove the index file from the URI
				$uri = (string) substr($uri, strlen($index_file));
			}
		}

		return $uri;
	}

	/**
	 * Gets or sets HTTP query string.
	 *
	 * @param   mixed   $key    Key or key value pairs to set
	 * @param   string  $value  Value to set to a key
	 * @return  mixed
	 */
	public function query($key = NULL, $value = NULL)
	{
		if (is_array($key))
		{
			// Act as a setter, replace all query strings
			$this->get = $key;

			return $this;
		}

		if ($key === NULL)
		{
			// Act as a getter, all query strings
			return $this->get;
		}
		elseif ($value === NULL)
		{
			// Act as a getter, single query string
			return $this->get[$key];
		}

		// Act as a setter, single query string
		$this->get[$key] = $value;

		return $this;
	}

	/**
	 * Gets or sets HTTP POST parameters to the request.
	 *
	 * @param   mixed  $key    Key or key value pairs to set
	 * @param   string $value  Value to set to a key
	 * @return  mixed
	 */
	public function post($key = NULL, $value = NULL)
	{
		if (is_array($key))
		{
			// Act as a setter, replace all fields
			$this->post = $key;

			return $this;
		}

		if ($key === NULL)
		{
			// Act as a getter, all fields
			return $this->post;
		}
		elseif ($value === NULL)
		{
			// Act as a getter, single field
			return $this->post[$key];
		}

		// Act as a setter, single field
		$this->post[$key] = $value;

		return $this;
	}

	/**
	 * Returns the compiled regular expression for the route. This translates
	 * keys and optional groups to a proper PCRE regular expression.
	 *
	 * @return  string
	 */
	public static function compile($uri, array $regex = NULL)
	{
		// The URI should be considered literal except for keys and optional parts
		// Escape everything preg_quote would escape except for : ( ) < >
		$expression = preg_replace('#'.self::REGEX_ESCAPE.'#', '\\\\$0', $uri);

		if (strpos($expression, '(') !== FALSE)
		{
			// Make optional parts of the URI non-capturing and optional
			$expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
		}

		// Insert default regex for keys
		$expression = str_replace(array('<', '>'), array('(?P<', '>'.self::REGEX_SEGMENT.')'), $expression);

		if ($regex)
		{
			$search = $replace = array();
			foreach ($regex as $key => $value)
			{
				$search[]  = "<$key>".self::REGEX_SEGMENT;
				$replace[] = "<$key>$value";
			}

			// Replace the default regex with the user-specified regex
			$expression = str_replace($search, $replace, $expression);
		}

		return '#^'.$expression.'$#uD';
	}

	/**
	 * Tests if the route matches a given Request. A successful match will return
	 * all of the routed parameters as an array. A failed match will return
	 * boolean FALSE.
	 *
	 * @return  array|boolean
	 */
	public function matches($route_regex, array $defaults = [])
	{
		$uri = trim($this->uri(), '/');

		if ( ! preg_match($route_regex, $uri, $matches))
			return FALSE;

		$params = array();
		foreach ($matches as $key => $value)
		{
			if (is_int($key))
			{
				// Skip all unnamed keys
				continue;
			}

			// Set the value for all matched keys
			$params[$key] = $value;
		}

		foreach ($defaults as $key => $value)
		{
			if ( ! isset($params[$key]) OR $params[$key] === '')
			{
				// Set default values for any key that was not matched
				$params[$key] = $value;
			}
		}

		if ( ! empty($params['controller']))
		{
			// PSR-0: Replace underscores with spaces, run ucwords, then replace underscore
			$params['controller'] = str_replace(' ', '_', ucwords(str_replace('_', ' ', $params['controller'])));
		}

		return $params;
	}
}
