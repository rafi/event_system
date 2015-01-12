<?php
namespace Rafi\Core\Data;

class Comment {

	public $id;
	public $parent_id;
	public $email;
	public $body;

	public function __construct($body = NULL, $email = NULL)
	{
		$this->body = $body;
		$this->email = $email;
	}

}
