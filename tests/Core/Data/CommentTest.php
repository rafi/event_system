<?php
namespace Rafi\Core\Data;

class CommentTest extends \PHPUnit_Framework_TestCase {

	protected $comment;

	public function setUp()
	{
		$this->comment = new Comment;
	}

	public function tearDown()
	{
		unset($this->comment);
	}

	public function testCanBeInstantiated()
	{
		$this->assertInstanceOf('Rafi\Core\Data\Comment', $this->comment);
	}

	public function testHasProperFields()
	{
		$this->assertNull($this->comment->id);
		$this->assertNull($this->comment->parent_id);
		$this->assertNull($this->comment->email);
		$this->assertNull($this->comment->body);
	}

}
