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

use Symfony\Component\EventDispatcher;


/**
 * Class Event
 * @package Netzmacht\DcaTools\Event
 */
class Event extends EventDispatcher\Event
{

	/**
	 * @var Config
	 */
	protected $objConfig;


	/**
	 * @param Config|array $objConfig
	 */
	public function __construct($objConfig=null)
	{
		if(is_array($objConfig))
		{
			$objConfig = new Config($objConfig);
		}

		$this->objConfig = $objConfig;
	}


	/**
	 * @return Config
	 */
	public function getConfig()
	{
		return $this->objConfig;
	}


	/**
	 * @param Config $objConfig
	 */
	public function setConfig(Config $objConfig)
	{
		$this->objConfig = $objConfig;
	}

}
