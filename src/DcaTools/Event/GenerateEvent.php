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

use DcaTools\Component\ControllerInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class GenerateEvent
 * @package DcaTools\Component\Operation
 */
class GenerateEvent extends Event
{

	/**
	 * @var ControllerInterface
	 */
	protected $controller;


	/**
	 * @var string
	 */
	protected $output;


	/**
	 * @param ControllerInterface $controller
	 */
	public function __construct(ControllerInterface $controller)
	{
		$this->controller = $controller;
	}


	/**
	 * @return ControllerInterface
	 */
	public function getController()
	{
		return $this->controller;
	}


	/**
	 * Get current model
	 *
	 * @return \DcGeneral\Data\ModelInterface
	 */
	public function getModel()
	{
		return $this->controller->getModel();
	}


	/**
	 * Shortcut to get the view
	 *
	 * @return \DcaTools\Component\ViewInterface
	 */
	public function getView()
	{
		return $this->controller->getView();
	}


	/**
	 * @param $output
	 */
	public function setOutput($output)
	{
		$this->output = $output;
	}


	/**
	 * @return string|null
	 */
	public function getOutput()
	{
		return $this->output;
	}

}