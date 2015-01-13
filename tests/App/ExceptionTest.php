<?php
namespace App;

class ExceptionTest extends \PHPUnit_Framework_Testcase {

	public function testCanSet()
	{
		$e = new Exception;
		$this->assertInstanceOf('App\Exception', $e);
	}

}
