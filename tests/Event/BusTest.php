<?php
namespace Rafi\Event;

use StdClass;

class BusTest extends \PHPUnit_Framework_TestCase {

	protected $bus;

	public function setUp()
	{
		$this->bus = Bus::instance();
	}

	public function testCanSubscribeAndTrigger()
	{
		$data = [ 'a', 'b', NULL ];

		$dummy = $this->getMockBuilder('\Rafi\Event\Dummy')
			->getMock();

		$dummy
			->expects($this->once())
			->method('bar')
			->with($data[0], $data[1], $data[2])
			->willReturn('foo');

		$this->bus->on('foo', [ $dummy, 'bar' ]);
		$this->bus->trigger('foo', $data);
	}

	public function testCanAttachObservers()
	{
		$dummy = $this->getMockBuilder('\Rafi\Event\Dummy')
			->getMock();

		$dummy
			->expects($this->once())
			->method('execute')
			->willReturn(TRUE);

		$observer = $this->getMockBuilder('\Rafi\Core\Observer\Comment\Features')
			->disableOriginalConstructor()
			->getMock();

		$observer
			->expects($this->once())
			->method('get_subscribers')
			->willReturn([ 'baz' => [ $dummy, 'execute' ] ]);

		$this->bus->attach($observer);
		$this->bus->trigger('baz', [ 'bar' ]);
	}

}

class Dummy {
	public function bar() {}
	public function execute() {}
}
