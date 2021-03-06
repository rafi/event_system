<?php
namespace Rafi\Core\Usecase\Comment;

use Rafi\Event;
use Rafi\Core\Data;
use Rafi\Core\Repository;
use Rafi\Core\Observer;
use Exception;

class Create {

	protected $data;
	protected $event;
	protected $comment;
	protected $repo;

	public function __construct(
		Event\Bus $event,
		Data\Comment $comment,
		Repository\Comment $repo,
		Observer\Comment\Features $observer
	)
	{
		// Attach the comment feature subscribers to the event-bus
		$event->attach($observer);

		$this->event = $event;
		$this->comment = $comment;
		$this->repo = $repo;
	}

	public function set(array $data)
	{
		$this->data = $data;

		return $this;
	}

	public function execute()
	{
		$this->repo->hydrate($this->comment, $this->data);

		// Validation
		if (empty($this->comment->email))
			throw new Exception('Email is mandatory');

		// Trigger event
		$this->event->trigger('submit', [ $this->comment ]);

		// Write to persistency layer
		$this->repo->create($this->comment);

		// Return simple data fields
		return get_object_vars($this->comment);
	}

}
