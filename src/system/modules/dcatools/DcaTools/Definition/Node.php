<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   LGPL/3.0
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace Netzmacht\DcaTools\Definition;

use Netzmacht\DcaTools\Event\EventDispatcher;
use Netzmacht\DcaTools\Structure\ExportInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Node
 * @package Netzmacht\DcaTools\Node
 */
abstract class Node extends EventDispatcher implements ExportInterface
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
	 * @var DataContainer
	 */
	protected $objDataContainer;


	/**
	 * Constructor
	 *
	 * @param string $strName
	 * @param DataContainer $objDataContainer
	 * @param mixed $definition
	 */
	public function __construct($strName, DataContainer $objDataContainer, &$definition)
	{
		$this->strName = $strName;
		$this->definition =& $definition;
		$this->objDataContainer = $objDataContainer;
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
		$objEvent = new GenericEvent();
		$objEvent->setArgument('origin', $this->strName);

		$strEvent = ($this->strName != '') ? 'rename' : 'change';

		$this->dispatch($strEvent, $objEvent);
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
	 * Get root DataContainer object
	 *
	 * @return DataContainer
	 */
	public function getDataContainer()
	{
		return $this->objDataContainer;
	}


	/**
	 * Change DataContainer of Child
	 *
	 * @param DataContainer $objDataContainer
	 * @return $this
	 */
	public function setDataContainer(DataContainer $objDataContainer)
	{
		if($this->getDataContainer() != $objDataContainer)
		{
			// data container is changed, so element is deleted from origin one,
			$this->dispatch('delete');

			$this->objDataContainer = $objDataContainer;
			$this->objDataContainer->dispatch('change');
		}

		return $this;
	}


	/**
	 * Test if element has same DataContainer as root
	 *
	 * @param Node $node
	 *
	 * @return bool
	 */
	public function hasSameDataContainer(Node $node)
	{
		return ($this->getDataContainer() == $node->getDataContainer());
	}


	/**
	 * Remove child from parent
	 * @return mixed
	 */
	public abstract function remove();


	/**
	 * Get from definition
	 *
	 * @param $strKey
	 * @return mixed
	 */
	abstract public function get($strKey);


	/**
	 * Add a node at a specified position
	 *
	 * @param array $arrTarget
	 * @param Node $objElement
	 * @param null $strReference
	 * @param $intPosition
	 *
	 * @throws \RuntimeException
	 */
	protected function addAtPosition(array &$arrTarget, Node $objElement, $strReference=null, $intPosition=Node::POS_LAST)
	{
		if($objElement->getDataContainer() != $this->getDataContainer())
		{
			throw new \RuntimeException("DataContainers are not identical");
		}

		if($strReference === null || $strReference === static::POS_LAST)
		{
			$arrTarget[$objElement->getName()] = $objElement;
		}
		else
		{
			$intPos = array_search($strReference, array_keys($arrTarget));
			($intPosition === static::POS_BEFORE) ?: ++$intPos;

			if($intPos == 0 || $strReference === static::POS_FIRST)
			{
				$arrTarget = array_merge(array($objElement->getName() => $objElement), $arrTarget);
			}
			else
			{
				$arrTarget = array_slice($arrTarget, 0, $intPos, true) +
					array($objElement->getName() => $objElement) +
					array_slice($arrTarget, $intPos, count($arrTarget) - 1, true) ;
			}
		}
	}


	/**
	 * @param $strKey
	 * @return null
	 */
	protected  function getFromDefinition($strKey)
	{
		if(!is_array($this->definition))
		{
			return $this->definition;
		}

		$chunks = explode('/', $strKey);
		$arrDca = $this->definition;

		while (($chunk = array_shift($chunks)) !== null)
		{
			if (!array_key_exists($chunk, $arrDca))
			{
				return null;
			}

			$arrDca = $arrDca[$chunk];
		}

		return $arrDca;
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
	 * @param string $strName new name
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
		$this->definition = $this->asString();
	}

}
