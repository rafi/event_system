<?php
namespace Rafi\Core\Usecase\Comment\Features;

use StdClass;

class SmileyTest extends \PHPUnit_Framework_TestCase {

	public function testCanConvertSmileys()
	{
		$comment = new StdClass;
		$comment->body = 'Hey :) there';

		$feature = new Smiley;
		$feature->execute($comment);

		$this->assertEquals('Hey '.Smiley::PARTIAL.' there', $comment->body);
	}

}
