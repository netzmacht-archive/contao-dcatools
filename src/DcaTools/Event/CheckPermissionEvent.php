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
	 * @param DcaTools $controller
	 */
	public function __construct(DcaTools $controller)
	{
		parent::__construct($controller);
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