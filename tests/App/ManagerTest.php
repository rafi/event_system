<?php
namespace App;

class ManagerTest extends \PHPUnit_Framework_Testcase {

	public function testProvideDependencies()
	{
		$app = new Manager([ 'default' => [] ]);

		$this->assertInstanceOf('Rafi\Event\Bus', $app->get('event'));
		$this->assertInstanceOf('Rafi\Storage\Database', $app->get('database'));
	}

}
