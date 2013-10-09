<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   MPL/2.0
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace Netzmacht\DcaTools\Event;

use Netzmacht\DcaTools\Event\Config;
use Netzmacht\DcaTools\Event\Event;


/**
 * Class ButtonEvent is base class for a button event. You do not have to use it, You can register every event as you
 * want.
 *
 * @package Netzmacht\DcaTools\Button
 */
abstract class DataContainerEvent extends Event
{

	/**
	 * Constructor
	 *
	 * @param Config $objConfig
	 */
	public function __construct(Config $objConfig=null)
	{
		parent::__construct($objConfig);
	}


	/**
	 * @return \Netzmacht\DcaTools\Operation
	 */
	public function getDataContainer()
	{
		return $this->getDispatcher();
	}

}