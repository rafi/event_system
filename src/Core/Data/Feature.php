<?php
namespace Rafi\Core\Data;

class Feature {

	public $id;
	public $entity;
	public $name;
	public $event;
	public $title;

	public function __construct($entity = NULL, $name = NULL, $event = NULL)
	{
		$this->entity = $entity;
		$this->name = $name;
		$this->event = $event;
	}

}
