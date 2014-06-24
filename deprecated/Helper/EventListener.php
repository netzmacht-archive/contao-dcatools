<?php

/**
 * DcaTools - Toolkit for data containers in Contao
 * Copyright (C) 2013 David Molineus
 *
 * @package   netzmacht-dcatools
 * @author    David Molineus <molineus@netzmacht.de>
 * @license   LGPL-3.0+
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace deprecated\DcaTools\Helper;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Helper class for event handling
 * @package DcaTools\Event
 */
class EventListener
{

	/**
	 * Add multiple listeners to an dispatcher
	 *
	 * @param EventDispatcherInterface $dispatcher
	 * @param $eventName
	 * @param array $listeners
	 */
	public static function addListeners(EventDispatcherInterface $dispatcher, $eventName, array $listeners)
	{
		foreach($listeners as $listener)
		{
			$dispatcher->addListener($eventName, $listener);
		}
	}


	/**
	 * Create a configurable listener
	 *
	 * @param callable $callback
	 * @param null $config
	 * @return array|callable
	 */
	public static function createConfigurableListener($callback, $config=null)
	{
		if($config === null)
		{
			return $callback;
		}

		return function (Event $event) use($callback, $config)
		{
			return call_user_func($callback, $event, $config);
		};
	}

}
