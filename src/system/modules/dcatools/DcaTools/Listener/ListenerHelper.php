<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 04.11.13
 * Time: 11:03
 */

namespace DcaTools\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ListenerHelper
{

	/**
	 * Add multiple listeners to the event dispatcher
	 *
	 * @param EventDispatcher $dispatcher
	 * @param $eventName
	 * @param array $listeners
	 */
	public static function addListeners(EventDispatcher $dispatcher, $eventName, array $listeners)
	{
		foreach($listeners as $listener)
		{
			$dispatcher->addListener($eventName, $listener);
		}
	}


	/**
	 * Get Listener which supports that a config parameter is passed by
	 *
	 * @param $callback
	 * @param null $config
	 * @param int $priority
	 * @return array|callable
	 */
	public static function createConfigurableListener($callback, $config=null, $priority=0)
	{
		if($config === null)
		{
			return array($callback, $priority);
		}

		return function (Event $event) use($callback, $config)
		{
			return call_user_func($callback, $event, $config);
		};
	}

}
