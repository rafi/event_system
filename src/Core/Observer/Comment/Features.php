<?php
namespace Rafi\Core\Observer\Comment;

use Rafi\Event\ObserverInterface;
use Rafi\Core\Repository;

class Features implements ObserverInterface {

	public function __construct(Repository\Observer $repo)
	{
	}

	public function get_subscribers()
	{
		$namespace = 'Rafi\\Core\\Usecase\\Comment\\Features\\';

		return [ 'submit' => $namespace.'Smiley' ];
	}

}
