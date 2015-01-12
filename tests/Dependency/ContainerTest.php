<?php
namespace Rafi\Dependency;

class ContainerTest extends \PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->container = $this->getMockForTrait('Rafi\Dependency\Container');
	}

	public function testRegistry()
	{
		foreach ([ 'bar', NULL, [0] ] as $value)
		{
			$this->container->set('foo', $value);
			$this->assertEquals($value, $this->container->get('foo'));
		}
	}

	public function testBuild()
	{
		$class_name = 'Rafi\Dependency\Dummy';
		$dummy = $this->container->build($class_name);

		$this->assertInstanceOf($class_name, $dummy);
	}

	public function testBuildUnknownClass()
	{
		$this->setExpectedException('Exception');

		$incorrect_class_name = 'Does\Not\Exist';
		$this->container->build($incorrect_class_name);
	}

	public function testBuildWithDependencies()
	{
		$class_name = 'Rafi\Dependency\Dummy_Deps';
		$dummy_deps = $this->container->build($class_name);

		$this->assertInstanceOf($class_name, $dummy_deps);
	}

	public function testBuildWithStoredDependencies()
	{
		$class_name = 'Rafi\Dependency\Dummy_Deps2';

		$this->container->set('foo', 'bar');
		$dummy_deps = $this->container->build($class_name);

		$this->assertInstanceOf($class_name, $dummy_deps);
		$this->assertEquals('bar', $dummy_deps->foo);
	}

	public function testBuildDepsWithDefaultValue()
	{
		$class_name = 'Rafi\Dependency\Dummy_Deps_DefaultValue';

		$dummy_deps = $this->container->build($class_name);

		$this->assertInstanceOf($class_name, $dummy_deps);
		$this->assertEquals('bar', $dummy_deps->foo);
	}

}

class Dummy {}

class Dummy_Deps {
	public function __construct(Dummy $dummy) {}
}

class Dummy_Deps2 {
	public $foo;
	public function __construct(Dummy $dummy, $foo) {
		$this->foo = $foo;
	}
}

class Dummy_Deps_DefaultValue {
	public $foo;
	public function __construct($foo = 'bar') {
		$this->foo = $foo;
	}
}
