<?php
namespace Rafi\Delivery\Controller;

use Rafi\Delivery\Request;
use Rafi\Delivery\Response;
use Rafi\Dependency\Container;
use ReflectionClass;

class BaseTest extends \PHPUnit_Framework_Testcase {

	public function testExecuteAction()
	{
		$app = new App;

		$request = new Request;
		$request->action('bar');

		$controller = new Foo($request, new Response, [ 'app' => $app ]);
		$response = $controller->execute(new ReflectionClass($controller));

		$this->assertEquals('foobar', $response->body());
	}

}

class App {
	use Container;
}

class Foo extends Base {

	protected $view = '';

	public function after()
	{
		$this->response->body($this->view);
	}

	public function action_bar()
	{
		$this->view = 'foobar';
	}

}
