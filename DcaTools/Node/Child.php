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

use Netzmacht\DcaTools\DataContainer;


/**
 * Class Child
 * @package Netzmacht\DcaTools\Node
 */
abstract class Child extends Node
{


	/**
	 * @var DataContainer
	 */
	protected $objDataContainer;


	/**
	 * Constructor
	 *
	 * @param $strName
	 * @param DataContainer $objDataContainer
	 * @param $definition
	 */
	public function __construct($strName, $objDataContainer, &$definition)
	{
		parent::__construct($strName, $definition);

		$this->objDataContainer = $objDataContainer;
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
		// nothing changed
		if($this->getDataContainer() == $objDataContainer)
		{
			return $this;
		}

		$this->dispatch('removeFromDataContainer');

		$this->objDataContainer = $objDataContainer;
		$this->objDataContainer->dispatch('change');

		return $this;
	}


	/**
	 * Test if element has same DataContainer as root
	 *
	 * @param Child $node
	 *
	 * @return bool
	 */
	public function hasSameDataContainer(Child $node)
	{
		return ($this->getDataContainer() == $node->getDataContainer());
	}


	/**
	 * Remove child from parent
	 * @return mixed
	 */
	public abstract function remove();


	/**
	 * Add a node at a specified position
	 *
	 * @param array $arrTarget
	 * @param Child $objElement
	 * @param null $strReference
	 * @param $intPosition
	 *
	 * @throws \RuntimeException
	 */
	protected function addAtPosition(array &$arrTarget, Child $objElement, $strReference=null, $intPosition=Child::POS_LAST)
	{
		if($objElement->getDataContainer() != $this->getDataContainer())
		{
			throw new \RuntimeException("Tables are not identical");
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

}