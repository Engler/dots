<?php
namespace App\Event;

class Event
{
	protected $name;
	protected $time;
	protected $params;

	public function __construct($name, $params = [])
	{
		$this->name = $name;
		$this->time = time();
		$this->params = $params;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getTime()
	{
		return $this->time;
	}

	public function __get($name)
	{
		if (isset($this->params[$name])) {
			return $this->params[$name];
		}
		return null;
	}
}