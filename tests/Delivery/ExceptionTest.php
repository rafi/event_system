<?php
namespace Rafi\Delivery;

class ExceptionTest extends \PHPUnit_Framework_Testcase {

	public function testTextOutput()
	{
		$exception = new Exception('Hey', [ 'a', 'b', 'c', 100 ]);
		$e_str = (string) $exception;

		$this->assertContains(__NAMESPACE__, (string) $exception);
	}

	public function testHandlingResponse()
	{
		$exception = new Exception('Hey');
		$response = Exception::_handler($exception);

		$this->assertInstanceOf('Rafi\Delivery\Response', $response);
		$this->assertContains((string) $exception, $response->body());
	}

}
