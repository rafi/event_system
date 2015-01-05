<?php
namespace Rafi\Delivery\Controller;

use ReflectionClass;
use Rafi\Delivery\Exception;
use Rafi\Delivery\Request;
use Rafi\Delivery\Response;

class Base {

	/**
	 * @var  Request   $request  Request that created the controller
	 */
	public $request;

	/**
	 * @var  Response  $response  Controller's response object
	 */
	public $response;

	/**
	 * @var  mixed  $app  App container
	 */
	public $app;

	/**
	 * @var array $action_map Map HTTP methods to controller actions
	 */
	protected $action_map = [];

	/**
	 * Creates a new controller instance. Each controller must be constructed
	 * with the request object that created it.
	 *
	 * @param   Request   $request     Request that created the controller
	 * @param   Response  $response    The request's response
	 * @param   array     $parameters  Extra sub-class dependencies
	 */
	public function __construct(Request $request, Response $response, array $parameters)
	{
		// Assign the request to the controller
		$this->request = $request;

		// Assign a response to the controller
		$this->response = $response;

		// Assigns the app dependencies through the request flow
		foreach ($parameters as $key => $value)
		{
			if (property_exists($this, $key))
			{
				$this->$key = $value;
			}
		}
	}

	/**
	 * Execute controller action:
	 * - Determine action verbs by map
	 * - Inject action dependencies
	 *
	 * @param   ReflectionClass $reflection
	 * @return  Response
	 */
	public function execute(ReflectionClass $reflection)
	{
		// Execute the "before action" method
		$this->before();

		// Determine the action to use
		if (empty($this->action_map))
		{
			$action = 'action_';
		}
		else
		{
			// Get the basic verb based action
			$action = $this->action_map[$this->request->method()];
		}
		$action .= $this->request->action();

		// If the action doesn't exist, it's a 404
		if ( ! method_exists($this, $action))
		{
			throw new Exception(
				'The requested URL :uri was not found on this server.',
				[ ':uri' => $this->request->uri() ]
			);
		}

		// Resolve all dependencies for this context
		$deps = $this->app->get_dependencies($reflection->getMethod($action));

		// Execute the action itself and supply arguments
		// TODO: With PHP 5.6, this can be replaced with:
		//
		//   $this->{$action}(...$deps);
		//
		call_user_func_array([ $this, $action ], $deps);

		// Execute the "after action" method
		$this->after();

		return $this->response;
	}

	/**
	 * Automatically executed before the controller action. Can be used to set
	 * class properties, do authorization checks, and execute other custom code.
	 *
	 * @return  void
	 */
	public function before()
	{
		// Nothing by default
	}

	/**
	 * Automatically executed after the controller action. Can be used to apply
	 * transformation to the response, add extra output, and execute
	 * other custom code.
	 *
	 * @return  void
	 */
	public function after()
	{
		// Nothing by default
	}

}
