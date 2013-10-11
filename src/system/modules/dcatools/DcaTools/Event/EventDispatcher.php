<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 10.10.13
 * Time: 16:21
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class EventDispatcher
 * @package Netzmacht\DcaTools\Event
 */
class EventDispatcher extends \Symfony\Component\EventDispatcher\EventDispatcher
{

	/**
	 * Dispatch the event, will create an DcaTools event if none given
	 *
	 * @param string $strEvent
	 * @param \Symfony\Component\EventDispatcher\Event|null $objEvent
	 *
	 * @return GenericEvent
	 */
	public function dispatch($strEvent, \Symfony\Component\EventDispatcher\Event $objEvent=null)
	{
		if($objEvent === null)
		{
			$objEvent = new GenericEvent($this);
		}

		return parent::dispatch($strEvent, $objEvent);
	}


	/**
	 * Add listener to event. Allow configuration as 3rd param of listener and priority being included in listener
	 *
	 * @see https://github.com/bit3/contao-event-dispatcher/blob/master/contao/config/services.php
	 *
	 * @param string $strName
	 * @param callable $listener
	 * @param int $intPriority
	 */
	public function addListener($strName, $listener, $intPriority=0)
	{
		if (is_array($listener) && count($listener) === 2 && is_int($listener[1]))
		{
			list($listener, $intPriority) = $listener;
		}

		// wrap callback in a closure if configuration is passed
		if(is_array($listener) && isset($listener[2]))
		{
			$listener = function($objEvent) use($listener)
			{
				$listener[0]::$listener[1]($objEvent, $listener[2]);
			};
		}

		parent::addListener($strName, $listener, $intPriority);
	}


	/**
	 * Add multiple listeners to an event
	 *
	 * @param $strEvent
	 * @param $arrListeners
	 */
	public function addListeners($strEvent, $arrListeners)
	{
		foreach($arrListeners as $listener)
		{
			$this->addListener($strEvent, $listener);
		}
	}

}