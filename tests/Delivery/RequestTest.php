<?php
namespace Rafi\Delivery;

use Rafi\Dependency\Container;

class RequestTest extends \PHPUnit_Framework_Testcase {

	public function testCanReadEnvironment()
	{
		$_GET['foo'] = 'bar';
		$_POST['foo'] = 'bar';

		$request = new Request;

		$this->assertEquals('bar', $request->post('foo'));
		$this->assertEquals('bar', $request->query('foo'));
	}

	public function testCanDetectURI()
	{
		$_SERVER['PATH_INFO'] = '/root/dir/foo';
		$request = new Request([ 'base_url' => '/root' ]);
		unset($_SERVER['PATH_INFO']);

		$this->assertEquals('/root/dir/foo', $request->uri());

		$_SERVER['REQUEST_URI'] = '/root/dir/foo';
		$request = new Request([ 'base_url' => '/root' ]);
		unset($_SERVER['REQUEST_URI']);

		$this->assertEquals('/dir/foo', $request->uri());

		$prev = $_SERVER['PHP_SELF'];
		$_SERVER['PHP_SELF'] = '/root/dir/foo';
		$request = new Request([ 'base_url' => '/root' ]);
		unset($_SERVER['PHP_SELF']);

		$this->assertEquals('/dir/foo', $request->uri());
	}

	public function testCanExecuteRoute()
	{
		$app = new App;

		$_SERVER['PATH_INFO'] = '/foo/bar';
		$routes = [ '<controller>/<action>' => [ 'prefix' => 'Rafi\Delivery' ] ];
		$request = new Request([ 'app' => $app, 'routes' => $routes ]);
		$response = $request->execute();

		$this->assertEquals('foobar', $response->body());
		unset($_SERVER['PATH_INFO']);
	}

}

class App {
	use Container;
}

class Foo extends Controller\Base {

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
