<?php
namespace App\View;

class LayoutTest extends \PHPUnit_Framework_Testcase {

	public function testCanBeInstantiated()
	{
		$view = new Layout;
		$this->assertInstanceOf('App\View\Layout', $view);
	}

}
