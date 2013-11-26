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

namespace DcaTools\Event;

use DcaTools\DcaTools;

/**
 * Class GetDynamicParentEvent
 * @package DcaTools\Event
 */
class GetDynamicParentEvent extends DcaToolsEvent
{

	/**
	 * @var string
	 */
	protected $moduleName;

	/**
	 * @var \DcaTools\DcaTools
	 */
	protected $controller;

	/**
	 * @var string
	 */
	protected $parentName;


	/**
	 * @param DcaTools $controller
	 * @param $moduleName
	 */
	public function __construct(DcaTools $controller, $moduleName)
	{
		parent::__construct($controller);

		$this->moduleName = $moduleName;
	}


	/**
	 * @return mixed
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}


	/**
	 * @param string $parentName
	 */
	public function setParentName($parentName)
	{
		$this->parentName = $parentName;
	}


	/**
	 * @return string
	 */
	public function getParentName()
	{
		return $this->parentName;
	}

}
