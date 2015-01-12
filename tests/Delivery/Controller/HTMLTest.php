<?php
namespace Rafi\Delivery\Controller;

use Rafi\Delivery\Request;
use Rafi\Delivery\Response;
use Rafi\Dependency\Container;
use ReflectionClass;

class HTMLTest extends \PHPUnit_Framework_Testcase {

	public function testPlainTextView()
	{
		$app = new HTMLApp;

		$request = new Request;
		$request->action('foo');

		$controller = new HTMLFoo($request, new Response, [ 'app' => $app ]);
		$response = $controller->execute(new ReflectionClass($controller));

		$this->assertEquals('foobar', $response->body());
	}
}

class HTMLApp {
	use Container;
}

class HTMLFoo extends HTML {

	public function action_foo()
	{
		$this->view = 'foobar';
	}

	/**
	 * Mocking the abstract render method
	 */
	public function render($template)
	{
		return $template;
	}

}
