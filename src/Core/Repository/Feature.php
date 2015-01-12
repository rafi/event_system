<?php
namespace Rafi\Core\Repository;

use Rafi\Storage\MySQL\Repository;
use Rafi\Storage\Database;

class Feature extends Repository {

	public function get($data)
	{
		throw new \Exception('Not implemented.');
	}

	public function find_all($entity, $object)
	{
		$sql = 'SELECT * FROM features WHERE entity = '.$this->database->quote($entity);

		return $this->database->query(
			Database::SELECT,
			$sql,
			get_class($object)
		);
	}

	public function create($data)
	{
		throw new \Exception('Not implemented.');
	}

	public function delete($data)
	{
		throw new \Exception('Not implemented.');
	}

}
