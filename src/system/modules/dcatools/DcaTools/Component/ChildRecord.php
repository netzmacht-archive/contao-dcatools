<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   LGPL/3.0
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace DcaTools\Component;

use DcaTools\Event\Event;


/**
 * Class ChildRecord
 *
 * @package DcaTools\Component
 */
class ChildRecord extends Visual
{

	/**
	 * @var
	 */
	protected $objModel;


	/**
	 * @param Event $objEvent
	 *
	 * @return mixed|void
	 */
	protected function compile(Event $objEvent)
	{
		$this->objModel = $objEvent->getModel();
	}

}