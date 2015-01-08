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
		$result = [];
		foreach ($this->comments as $comment)
		{
			$result[] = get_object_vars($comment);
		}

		return $result;
	}

}
