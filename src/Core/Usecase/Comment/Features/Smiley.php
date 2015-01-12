<?php
namespace Rafi\Core\Usecase\Comment\Features;

class Smiley {

	const PARTIAL = '{{smiley}}';

	public function __construct()
	{
	}

	public function set()
	{
	}

	public function execute($comment)
	{
		$comment->body = str_replace(':)', self::PARTIAL, $comment->body);
	}

}
