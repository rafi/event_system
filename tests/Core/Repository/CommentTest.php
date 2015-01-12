<?php
namespace Rafi\Core\Repository;

use StdClass;
use Rafi\Core\Data;

class CommentTest extends \PHPUnit_Framework_TestCase {

	public function testCanFindAllComments()
	{
		$db = $this->getMockBuilder('Rafi\Storage\MySQL\Database')
			->disableOriginalConstructor()
			->getMock();

		$db->expects($this->once())
			->method('query')
			->willReturn(TRUE);

		$repo = new Comment($db);
		$comments = $repo->find_all([], new StdClass);

		$this->assertTrue($comments);
	}

	public function testCanCreateComment()
	{
		$db = $this->getMockBuilder('Rafi\Storage\MySQL\Database')
			->disableOriginalConstructor()
			->getMock();

		$db->expects($this->once())
			->method('query')
			->willReturn(TRUE);

		$db->expects($this->any())
			->method('quote')
			->willReturn('x');

		$db->expects($this->once())
			->method('query')
			->willReturn(TRUE);

		$comment = new StdClass;
		$comment->parent_id = 1;
		$comment->email = 'foo@bar.com';
		$comment->body = 'baz';

		$repo = new Comment($db);
		$result = $repo->create($comment);

		$this->assertTrue($result);
	}

}
