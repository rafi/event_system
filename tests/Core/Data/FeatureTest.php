<?php
namespace Rafi\Core\Data;

class FeatureTest extends \PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->feature = new Feature;
	}

	public function tearDown()
	{
		unset($this->feature);
	}

	public function testCanBeInstantiated()
	{
		$this->assertInstanceOf('Rafi\Core\Data\Feature', $this->feature);
	}

	public function testHasProperFields()
	{
		$this->assertNull($this->feature->id);
		$this->assertNull($this->feature->entity);
		$this->assertNull($this->feature->name);
		$this->assertNull($this->feature->event);
		$this->assertNull($this->feature->title);
	}

}
