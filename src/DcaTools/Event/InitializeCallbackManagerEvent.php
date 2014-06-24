<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Event;


use DcaTools\Dca\Callback\CallbackManager;
use Symfony\Component\EventDispatcher\Event;

class InitializeCallbackManagerEvent extends Event
{
	const NAME = 'dcatools.initialize-callback-manager';

	/**
	 * @var CallbackManager
	 */
	private $callbackManager;


	/**
	 * @param CallbackManager $callbackManager
	 */
	function __construct(CallbackManager $callbackManager)
	{
		$this->callbackManager = $callbackManager;
	}


	/**
	 * @param \DcaTools\Dca\Callback\CallbackManager $callbackManager
	 */
	public function setCallbackManager($callbackManager)
	{
		$this->callbackManager = $callbackManager;
	}


	/**
	 * @return \DcaTools\Dca\Callback\CallbackManager
	 */
	public function getCallbackManager()
	{
		return $this->callbackManager;
	}



} 