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
use DcaTools\Component\ViewInterface;
use DcGeneral\Data\ModelInterface;
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
	protected $config;

	/**
	 * @var \DcaTools\Component\ViewInterface
	 */
	protected $view;

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $model;

	/**
	 * @var string
	 */
	protected $output;


	/**
	 * @param ModelInterface $model
	 * @param ViewInterface $view
	 * @param array $config
	 */
	public function __construct(ModelInterface $model, ViewInterface $view, array $config=array())
	{
		$this->model  = $model;
		$this->view   = $view;
		$this->config = $config;
	}


	/**
	 * Get current model
	 *
	 * @return \DcGeneral\Data\ModelInterface
	 */
	public function getModel()
	{
		return $this->model;
	}


	/**
	 * Shortcut to get the view
	 *
	 * @return \DcaTools\Component\ViewInterface
	 */
	public function getView()
	{
		return $this->view;
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


	/**
	 * @param $name
	 * @param $value
	 */
	public function setConfigAttribute($name, $value)
	{
		$this->config[$name] = $value;
	}


	/**
	 * @param $name
	 * @param $default=null
	 * @return mixed
	 */
	public function getConfigAttribute($name, $default=null)
	{
		if(isset($this->config[$name]))
		{
			return $this->config[$name];
		}

		return $default;
	}


	/**
	 * @param array $config
	 * @return mixed|void
	 */
	public function setConfig(array $config)
	{
		$this->config = array_merge($this->config, $config);
	}


	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}

}
