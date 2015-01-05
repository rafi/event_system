<?php
namespace App\Controller;

use Rafi\Core\Usecase;
use Rafi\Delivery;
use App\View;

/**
 * Browser endpoint for HTML rendering
 */
class Comment extends Delivery\Controller\HTML {

	// Render HTML from PHP templates
	use Delivery\Template\Render\Php;

	/**
	 * Comment list
	 */
	public function action_index(
		Usecase\Comment\Read $usecase,
		View\Comment\Read $view
	)
	{
		$output = $usecase->execute();

		$this->template = 'comment/read';
		$this->view = $view->set($output);
	}

	/**
	 * Comment creation
	 */
	public function action_create(
		Usecase\Comment\Create $usecase,
		View\Comment\Create $view
	)
	{
		$comment = $this->request->post();
		$output = $usecase
			->set($comment)
			->execute();

		$this->template = 'comment/create';
		$this->view = $view->set($output);
	}

}
