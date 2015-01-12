<?php
namespace Rafi\Core\Observer\Comment;

use Rafi\Event\ObserverInterface;
use Rafi\Core\Data;
use Rafi\Core\Repository;

class Features implements ObserverInterface {

	const FEATURE_NAMESPACE = 'Rafi\\Core\\Usecase\\Comment\\Features\\';

	protected $repo;

	public function __construct(
		Repository\Feature $repo,
		Data\Feature $feature
	)
	{
		$this->repo = $repo;
		$this->feature = $feature;
	}

	public function get_subscribers()
	{
		$features = $this->repo->find_all('comment', $this->feature);

		$events = [];
		foreach ($features as $feature)
		{
			$events[$feature->event] = self::FEATURE_NAMESPACE.$feature->name;
		}

		return $events;
	}

}
