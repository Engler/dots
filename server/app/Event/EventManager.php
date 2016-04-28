<?php
namespace App\Event;

use App\Event\Event;

/*
	- socket.open
	- socket.message
	- socket.close
	- socket.error

	- game.squareFinished
	- game.squareFillEdge
	- game.turnChanged
	- game.finished
*/
class EventManager
{
	private static $listeners;

	public static function on($eventName, $callback)
	{
		if (!isset(self::$listeners[$eventName])) {
			self::$listeners[$eventName] = [];
		}

		self::$listeners[$eventName][] = $callback;
	}

	public static function fire($eventName, $params = [])
	{
		if (isset(self::$listeners[$eventName])) {
			$event = new Event($eventName, $params);
			foreach (self::$listeners[$eventName] as $listener) {
				$listener($event);
			}
		}
	}
}