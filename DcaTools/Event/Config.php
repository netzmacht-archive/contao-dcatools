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

/**
 * Class Config provides configuration for events
 * @package Netzmacht\DcaTools\Event
 */
class Config
{

	/**
	 * @var array
	 */
	protected  $arrConfiguration = array();


	/**
	 * @param array $arrConfiguration
	 */
	public function __construct(array $arrConfiguration = array())
	{
		$this->arrConfiguration = $arrConfiguration;
	}


	/**
	 * @param $strKey
	 * @param $value
	 */
	public function set($strKey, $value)
	{
		$this->arrConfiguration[$strKey] = $value;
	}


	/**
	 * @param $strKey
	 * @return null
	 */
	public function get($strKey)
	{
		if($this->has($strKey))
		{
			return $this->arrConfiguration[$strKey];
		}

		return null;
	}


	/**
	 * @param $strKey
	 * @return bool
	 */
	public function has($strKey)
	{
		return isset($this->arrConfiguration[$strKey]);
	}

}