<?php
namespace Rafi\Core\Usecase\Comment;

use Rafi\Core\Data;

class ReadTest extends \PHPUnit_Framework_TestCase {

	public function testCanBeExecuted()
	{
		$comment = new Data\Comment();

		$event = $this->getMockBuilder('Rafi\Event\Bus')
			->disableOriginalConstructor()
			->getMock();

		$event->expects($this->once())
			->method('trigger')
			->with(
				$this->equalTo('read'),
				$this->equalTo([ [ $comment ] ])
			);

		$repo = $this->getMockBuilder('Rafi\Core\Repository\Comment')
			->disableOriginalConstructor()
			->getMock();

		$repo->expects($this->once())
			->method('find_all')
			->with(
				$this->equalTo([]),
				$this->equalTo($comment)
			)
			->willReturn([ $comment ]);

		$output = (new Read($event, $comment, $repo))
			->set([])
			->execute();

		$this->assertEquals($output, [ $comment ]);
	}

}
