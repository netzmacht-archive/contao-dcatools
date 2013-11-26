<?php

/**
 * DcaTools - Toolset for datacontainers in Contao
 * Copyright (C) 2013 David Molineus
 *
 * @package   netzmacht-dcatools
 * @author    David Molineus <molineus@netzmacht.de>
 * @license   LGPL-3.0+
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace DcaTools\Event;

use DcaTools\Controller;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetDynamicParentEvent
 * @package DcaTools\Event
 */
class GetDynamicParentEvent extends Event
{

	/**
	 * @var string
	 */
	protected $moduleName;

	/**
	 * @var \DcaTools\Controller
	 */
	protected $controller;

	/**
	 * @var string
	 */
	protected $parentName;


	/**
	 * @param Controller $controller
	 * @param $moduleName
	 */
	public function __construct(Controller $controller, $moduleName)
	{
		$this->controller = $controller;
		$this->moduleName = $moduleName;
	}


	/**
	 * @return \DcaTools\Controller
	 */
	public function getController()
	{
		return $this->controller;
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
