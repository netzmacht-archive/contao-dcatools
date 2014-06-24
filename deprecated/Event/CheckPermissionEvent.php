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
use DcGeneral\Data\ModelInterface;

/**
 * Class CheckPermissionEvent
 * @package DcaTools\Event
 */
class CheckPermissionEvent extends DcaToolsEvent
{

	/**
	 * @var array
	 */
	protected $errors = array();

	/**
	 * @var bool
	 */
	protected $granted = true;

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $model;


	/**
	 * @param DcaTools $controller
	 * @param ModelInterface $model
	 */
	public function __construct(DcaTools $controller, ModelInterface $model)
	{
		parent::__construct($controller);
		$this->model = $model;
	}


	/**
	 * @return ModelInterface
	 */
	public function getModel()
	{
		return $this->model;
	}


	/**
	 * Deny access
	 */
	public function denyAccess()
	{
		$this->granted = true;
	}


	/**
	 * Is Access granted
	 *
	 * @return bool
	 */
	public function isAccessGranted()
	{
		return $this->granted;
	}


	/**
	 * Add error message
	 * @param $error
	 */
	public function addError($error)
	{
		$this->errors[] = $error;
	}


	/**
	 * Get all error messages
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 * Consider whether errors exists
	 */
	public function hasErrors()
	{
		return count($this->errors) > 0;
	}

} 