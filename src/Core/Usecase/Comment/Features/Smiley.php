<?php
namespace Rafi\Core\Usecase\Comment\Features;

class Smiley {

	public function __construct()
	{
	}

	public function set()
	{
	}

	public function execute($comment)
	{
		$comment->body = str_replace(':)', '{{smiley}}', $comment->body);
	}

}
