<?php
namespace App\View\Comment;

use App\View\Layout;

class Read extends Layout {

	protected $comments;

	public function set(\ArrayAccess $comments)
	{
		$this->comments = $comments;

		return $this;
	}

	public function comments()
	{
		// TODO Should be implemented as partials in a template rendering engine.
		$partials = [
			'{{smiley}}' => '<img src="../../app/media/img/smiley.png" />'
		];

		$result = [];
		foreach ($this->comments as $comment)
		{
			$comment->body = strtr($comment->body, $partials);
			$result[] = get_object_vars($comment);
		}

		return $result;
	}

}
