<?php
namespace Rafi\Core\Usecase\Comment;

use Rafi\Core\Data;

class CreateTest extends \PHPUnit_Framework_TestCase {

	public function testCanBeExecuted()
	{
		$data = [ 'body' => 'Foobar', 'email' => 'foo@bar.com' ];
		$comment = new Data\Comment($data['body'], $data['email']);

		$event = $this->getMockBuilder('Rafi\Event\Bus')
			->disableOriginalConstructor()
			->getMock();

		$event->expects($this->once())
			->method('trigger')
			->with(
				$this->equalTo('submit'),
				$this->equalTo([ $comment ])
			);

		$repo = $this->getMockBuilder('Rafi\Core\Repository\Comment')
			->disableOriginalConstructor()
			->getMock();

		$repo->expects($this->once())
			->method('hydrate')
			->with(
				$this->equalTo($comment),
				$this->equalTo($data)
			);

		$repo->expects($this->once())
			->method('create')
			->with($this->equalTo($comment));

		$observer = $this->getMockBuilder('Rafi\Core\Observer\Comment\Features')
			->disableOriginalConstructor()
			->getMock();

		$output = (new Create($event, $comment, $repo, $observer))
			->set($data)
			->execute();

		$this->assertEquals($output['body'], $data['body']);
		$this->assertEquals($output['email'], $data['email']);
	}

}
