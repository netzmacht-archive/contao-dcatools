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

namespace Netzmacht\DcaTools\Node;

use Netzmacht\DcaTools\DcaTools;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Node
 * @package Netzmacht\DcaTools\Node
 */
abstract class Node extends EventDispatcher implements Exportable
{
	/**
	 * @var int use for injecting element before other one
	 */
	const POS_BEFORE = 1;

	/**
	 * @var int use for injecting element before after one
	 */
	const POS_AFTER  = 2;

	/**
	 * @var int use for injecting element at first place
	 */
	const POS_FIRST  = 4;

	/**
	 * @var int use for injecting element at last place
	 */
	const POS_LAST   = 8;


	/**
	 * Name of element
	 *
	 * @var string
	 */
	protected $strName;


	/**
	 * DCA definition
	 *
	 * @var mixed
	 */
	protected $definition;


	/**
	 * Constrcutor
	 *
	 * @param $strName
	 * @param $definition
	 */
	public function __construct($strName, &$definition)
	{
		$this->strName = $strName;
		$this->definition =& $definition;
	}


	/**
	 * Stringify Node will return the name
	 *
	 * @return string
	 */
	function __toString()
	{
		return $this->getName();
	}


	/**
	 * Get Name
	 *
	 * @return mixed
	 */
	public function getName()
	{
		return $this->strName;
	}


	/**
	 * Set Name. This method is not supposed being called. Used for internal stuff
	 *
	 * @param string $strName
	 */
	public function setName($strName)
	{
		if($this->strName !== null)
		{
			throw new \RuntimeException('Node can not be renamed');
		}
		$this->strName = $strName;
	}


	/**
	 * @return mixed
	 */
	public function &getDefinition()
	{
		return $this->definition;
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param string|Node
	 *
	 * @return $this
	 */
	abstract public function extend($node);



	/**
	 * Copy node to a new one
	 *
	 * @param $strName
	 *
	 * @return mixed
	 */
	public function copy($strName)
	{
		$objCopy = clone $this;

		return $objCopy;
	}


	/**
	 * Prepare for cloning
	 */
	public function __clone()
	{
		unset($this->definition);
	}



	/**
	 * @param Event $objEvent
	 */
	public function updateDefinition()
	{
		$this->definition = $this->toString();
	}
}
