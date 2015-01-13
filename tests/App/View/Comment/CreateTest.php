<?php
namespace App\View\Comment;

class CreateTest extends \PHPUnit_Framework_Testcase {

	public function testCanSet()
	{
		$view = new Create;

		$this->assertEquals($view, $view->set([]));
	}

}
