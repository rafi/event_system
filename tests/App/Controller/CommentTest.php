<?php
namespace App\Controller;

class CommentTest extends \PHPUnit_Framework_Testcase {

	protected $request;
	protected $controller;

	public function setUp()
	{
		$this->request = $this->getMockBuilder('Rafi\Delivery\Request')
			->getMock();

		$this->response = $this->getMockBuilder('Rafi\Delivery\Response')
			->getMock();

		$this->controller = new Comment($this->request, $this->response, []);
	}

	public function tearDown()
	{
		unset($this->controller, $this->request, $this->response);
	}

	public function testExecuteIndex()
	{
		$fixture = [ 'a' => '11', 'b' => '22' ];

		$usecase = $this->getMockBuilder('Rafi\Core\Usecase\Comment\Read')
			->disableOriginalConstructor()
			->getMock();

		$usecase->expects($this->once())
			->method('execute')
			->willReturn($fixture);

		$view = $this->getMockBuilder('App\View\Comment\Read')
			->disableOriginalConstructor()
			->getMock();

		$view->expects($this->once())
			->method('set')
			->with($this->equalTo($fixture));

		$this->controller->action_index($usecase, $view);
	}

	public function testExecuteCreate()
	{
		$fixture = [ 'a' => '11', 'b' => '22' ];

		$this->request->expects($this->once())
			->method('post')
			->willReturn($fixture);

		$usecase = $this->getMockBuilder('Rafi\Core\Usecase\Comment\Create')
			->disableOriginalConstructor()
			->getMock();

		$usecase->expects($this->once())
			->method('set')
			->willReturn($usecase);

		$usecase->expects($this->once())
			->method('execute')
			->willReturn($fixture);

		$view = $this->getMockBuilder('App\View\Comment\Create')
			->disableOriginalConstructor()
			->getMock();

		$view->expects($this->once())
			->method('set')
			->with($this->equalTo($fixture));

		$this->controller->action_create($usecase, $view);
	}
}
