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
use Netzmacht\DcaTools\Event\Event;
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
		$arrConfig = array('origin' => $this->strName);
		$strEvent = ($this->strName != '') ? 'move' : 'change';

		$this->strName = $strName;
		$this->dispatch($strEvent, new Event($arrConfig));
	}


	/**
	 * Get Definition as reference
	 *
	 * @return mixed
	 */
	public function &getDefinition()
	{
		return $this->definition;
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param Node|string node or name of the node
	 *
	 * @return $this
	 */
	abstract public function extend($node);



	/**
	 * Copy node to a new one
	 *
	 * @param $strName new name
	 *
	 * @return mixed
	 */
	public function copy($strName=null)
	{
		$objCopy = clone $this;

		if($strName)
		{
			$objCopy->setName($strName);
		}

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
	 * Update the definition of current element
	 */
	public function updateDefinition()
	{
		$this->definition = $this->toString();
	}


	/**
	 * Dispatch the event, will create an DcaTools event if none given
	 */
	public function dispatch($strEvent, \Symfony\Component\EventDispatcher\Event $objEvent=null)
	{
		if($objEvent === null)
		{
			$objEvent = new Event();
		}

		parent::dispatch($strEvent, $objEvent);
	}
}
