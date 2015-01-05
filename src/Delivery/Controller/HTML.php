<?php
namespace Rafi\Delivery\Controller;

use Rafi\Delivery\Exception;

abstract class HTML extends Base {

	/**
	 * @var  mixed  $view The content View object
	 */
	protected $view;

	/**
	 * @var  string $content Formatted content
	 */
	protected $content = '';

	/**
	 * @var  string  $template Content's template
	 */
	protected $template = NULL;

	/**
	 * Child controllers must use render traits
	 */
	abstract protected function render($template, $content);

	public function after()
	{
		// If content is NULL, no View to render
		if ($this->view === NULL)
		{
			// Verify if response was handled manually
			if ( ! $this->response->body())
				throw new Exception('An empty view can\'t fulfill request');
		}
		elseif (is_object($this->view))
		{
			$this->response->body(
				$this->render($this->template, $this->view)
			);
		}
	}

	public function assets()
	{
		$escaped = 'window.pass = '.json_encode($this->environment()).';';
		$assets = '<script type="text/javascript">'.$escaped.'</script>'.PHP_EOL;

		return $assets;
	}

	public function environment()
	{
		return [
			'route' => [
				'name'       => $this->request->route(),
				'directory'  => $this->request->directory(),
				'controller' => strtolower($this->request->controller()),
				'action'     => $this->request->action()
			],
			'url' => [
				'full'   => URL::base('http'),
				'base'   => URL::base(),
				'media'  => URL::base().Media::uri('/').'/',
			],
			'lang' => [
				'current' => 'en-US'
			],
			'environment' => ENVNAME,
		];
	}

}
