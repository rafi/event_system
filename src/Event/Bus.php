<?php
namespace Rafi\Event;

class Bus {

	protected $events = [];

	public function trigger($event_name, array $arguments = [])
	{
		if (isset($this->events[$event_name]))
		{
			foreach ($this->events[$event_name] as $subscriber)
			{
				call_user_func_array($subscriber, $arguments);
			}
		}
	}

	public function on($event_name, callable $subscriber)
	{
		isset($this->events[$event_name])
			OR $this->events[$event_name] = [];

		$this->events[$event_name][] = $subscriber;
	}

}
