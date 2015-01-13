<?php
namespace App;

use Rafi\Dependency\Container;
use Rafi\Event;
use Rafi\Storage\MySQL;

class Manager {
	use Container;

	public function __construct(array $config)
	{
		// Create a singleton event bus and inject it to container
		$this->set('event', Event\Bus::instance());

		// Create a singleton database and inject it to container
		$this->set('database', MySQL\Database::instance($config));
	}

}
