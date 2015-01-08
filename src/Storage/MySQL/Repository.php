<?php
namespace Rafi\Storage\MySQL;

use Rafi\Storage\DataMapper;

class Repository {
	use DataMapper;

	public function __construct(Database $database)
	{
		$this->database = $database;
	}

}
