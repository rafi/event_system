<?php
namespace App\View\Comment;

use StdClass;

class ReadTest extends \PHPUnit_Framework_Testcase {

	protected $fixture;
	protected $comment_a;
	protected $comment_b;
	protected $comment_c;

	public function setUp()
	{
		$this->comment_a = new StdClass;
		$this->comment_a->body = 'What\'s up?';

		$this->comment_b = new StdClass;
		$this->comment_b->body = 'Hey there';

		$this->comment_c = new StdClass;
		$this->comment_c->body = '{{smiley}}';

		$this->fixture = [ $this->comment_a, $this->comment_b, $this->comment_c ];
	}

	public function tearDown()
	{
		unset($this->fixture, $this->comment_a, $this->comment_b, $this->comment_c);
	}

	public function testComments()
	{
		$view = (new Read)
			->set($this->fixture);

		$comments = $view->comments();

		$should_match = [
			(array) $this->comment_a,
			(array) $this->comment_b,
			[ 'body' => $view::PARTIALS[$this->comment_c->body] ]
		];

		$this->assertEquals($should_match, $comments);
	}

}
