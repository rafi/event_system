<?php
namespace Rafi\Core\Usecase\Comment;

use Rafi\Event;
use Rafi\Core\Data;
use Rafi\Core\Repository;

class Read {

	protected $data;
	protected $event;
	protected $comment;
	protected $repo;

	public function __construct(
		Event\Bus $event,
		Data\Comment $comment,
		Repository\Comment $repo
	)
	{
		$this->event = $event;
		$this->comment = $comment;
		$this->repo = $repo;
	}

	public function set(array $data)
	{
		$this->data = $data;
	}

	public function execute()
	{
		$collection = $this->repo->find_all($this->comment);

		$this->event->trigger('read', [ $collection ]);

		return $collection;
	}

}
