<?php
namespace Rafi\Core\Repository;

use StdClass;
use Rafi\Core\Data;

class FeatureTest extends \PHPUnit_Framework_TestCase {

	public function testCanFindAllFeatures()
	{
		$db = $this->getMockBuilder('Rafi\Storage\MySQL\Database')
			->disableOriginalConstructor()
			->getMock();

		$db->expects($this->once())
			->method('quote')
			->willReturn(TRUE);

		$db->expects($this->once())
			->method('query')
			->willReturn(TRUE);

		$repo = new Feature($db);
		$comments = $repo->find_all([], new StdClass);

		$this->assertTrue($comments);
	}

	public function testCanGet()
	{
		$this->setExpectedException('\Exception');

		$db = $this->getMockBuilder('Rafi\Storage\MySQL\Database')
			->disableOriginalConstructor()
			->getMock();

		$repo = new Feature($db);
		$repo->get([]);
	}

	public function testCanCreate()
	{
		$this->setExpectedException('\Exception');

		$db = $this->getMockBuilder('Rafi\Storage\MySQL\Database')
			->disableOriginalConstructor()
			->getMock();

		$repo = new Feature($db);
		$repo->create([]);
	}

	public function testCanDelete()
	{
		$this->setExpectedException('\Exception');

		$db = $this->getMockBuilder('Rafi\Storage\MySQL\Database')
			->disableOriginalConstructor()
			->getMock();

		$repo = new Feature($db);
		$repo->delete([]);
	}
}
