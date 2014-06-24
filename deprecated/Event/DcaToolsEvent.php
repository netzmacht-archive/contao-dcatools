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

namespace deprecated\DcaTools\Event;

use \deprecated\DcaTools\DcaTools;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class ControllerEvent
 * @package DcaTools\Event
 */
class DcaToolsEvent extends Event
{

	/**
	 * @var DcaTools
	 */
	protected $controller;


	/**
	 * @param DcaTools $controller
	 */
	public function __construct(DcaTools $controller)
	{
		$this->controller = $controller;
	}


	/**
	 * @return DcaTools
	 */
	public function getDcaTools()
	{
		return $this->controller;
	}

}
