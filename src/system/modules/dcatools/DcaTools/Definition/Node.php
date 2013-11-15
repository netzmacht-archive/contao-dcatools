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

namespace DcaTools\Definition;

use DcaTools\Definition;
use DcaTools\Structure\ExportInterface;

/**
 * Class Node
 * @package DcaTools\Node
 */
abstract class Node  implements ExportInterface
{

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
	protected function __construct($strName, DataContainer $objDataContainer, &$definition)
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
	 * @return string
	 */
	public function getName()
	{
		return $this->strName;
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
	 * Require same datacontainer throws an exception if DataContainer is not the same
	 *
	 * @param Node $node
	 *
	 * @throws \RuntimeException
	 */
	protected function requireSameDataContainer(node $node)
	{
		if(!$this->hasSameDataContainer($node))
		{
			$arrDebug = (debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2));
			throw new \RuntimeException("{$arrDebug[1]['class']}::{$arrDebug[1]['method']} not allowed for different DataContainers");
		}
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
	protected function addAtPosition(array &$arrTarget, Node $objElement, $strReference=null, $intPosition=Definition::LAST)
	{
		$this->requireSameDataContainer($objElement);

		if($strReference === Definition::FIRST || $strReference === Definition::LAST)
		{
			$intPosition = $strReference;
		}

		if($strReference === null || $intPosition === Definition::LAST)
		{
			$arrTarget[$objElement->getName()] = $objElement;
		}
		elseif($intPosition === Definition::FIRST)
		{
			$arrTarget = array_merge(array($objElement->getName() => $objElement), $arrTarget);
		}
		else
		{
			$intPos = array_search($strReference, array_keys($arrTarget));
			($intPosition === Definition::BEFORE) ?: ++$intPos;

			if($intPos == 0)
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
	 * @return mixed
	 */
	public function getFromDefinition($strKey)
	{
		if(!is_array($this->definition))
		{
			return $this->definition;
		}

		$chunks = explode('/', $strKey);
		$arrDca = $this->definition;

		while (($chunk = array_shift($chunks)) !== null)
		{
			if ($chunk == '' || !is_array($arrDca) || !array_key_exists($chunk, $arrDca))
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
	 * @return static
	 */
	public function copy($strName)
	{
		$strOld = $this->getName();

		$this->strName = $strName;
		$objCopy = clone $this;

		$this->strName = $strOld;

		return $objCopy;
	}


	/**
	 * Prepare for cloning
	 */
	public function __clone()
	{
		//unset($this->definition);
	}


	/**
	 * Prepare argument so that an array of name and the object is passed
	 *
	 * @param Node $objReference
	 * @param Node|string $node
	 * @param bool $blnNull return null if property does not exists
	 * @param $strClass
	 *
	 * @return array[string|Property|null]
	 */
	protected static function prepareArgument(Node $objReference, $node, $blnNull, $strClass)
	{
		$node = is_object($node) ? $node->getName() : $node;

		if(($strClass != 'DataContainer') && !call_user_func(array($objReference , 'has' . $strClass), $node) && $blnNull)
		{
			return array($node, null);
		}

		return array($node, call_user_func(array($objReference , 'get' . $strClass), $node));
	}


	/**
	 * Update the definition of current element
	 *
	 * @param bool $blnPropagation
	 *
	 * @return $this
	 */
	abstract public function updateDefinition($blnPropagation=true);

}
