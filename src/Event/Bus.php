<?php
namespace Rafi\Event;

/**
 * Event bus, singleton pattern
 */
class Bus {

	protected static $instance = null;

	/**
	 * Returns singleton instance statically
	 */
	public static function instance()
	{
		if ( ! isset(static::$instance))
		{
			static::$instance = new static;
		}
		return static::$instance;
	}

	/**
	 * @var array $events Event container for listeners
	 */
	protected $events = [];

	/**
	 * @var array $observers Observer container for subscribers
	 */
	protected $observers = [];

	/**
	 * Prevent from being instantiated
	 */
	protected function __construct()
	{
	}

	/**
	 * Prevent from being cloned
	 */
	protected function __clone()
	{
	}

	/**
	 * Prevent from being unserialized
	 */
	protected function __wakeup()
	{
	}

	/**
	 * Trigger an event and execute subscribed listeners
	 */
	public function trigger($event_name, array $arguments = [])
	{
		if (isset($this->events[$event_name]))
		{
			foreach ($this->events[$event_name] as & $subscriber)
			{
				// Observers are stored as strings for lazy-loading
				// This instantiates a new instance and stores it
				// back into the events container (by reference).
				if (is_string($subscriber))
				{
					$subscriber = [ new $subscriber, 'execute' ];
				}

				// Execute the subscribers' event
				call_user_func_array($subscriber, $arguments);
			}
			unset($observer);
		}
	}

	/**
	 * Attach a subscriber to an event
	 */
	public function on($event_name, callable $subscriber)
	{
		isset($this->events[$event_name])
			OR $this->events[$event_name] = [];

		$this->events[$event_name][] = $subscriber;
	}

	/**
	 * Attach an observer with subscribers
	 */
	public function attach(ObserverInterface $observer)
	{
		$key = get_class($observer);

		if (empty($this->observers[$key]))
		{
			$this->observers[$key] = $observer;

			// Collect observer's subscribers for lazy-loading
			// once an event is triggered.
			$subscribers = $observer->get_subscribers();
			foreach ($subscribers as $event_name => $class_name)
			{
				$this->events[$event_name][] = $class_name;
			}
		}
	}

}
