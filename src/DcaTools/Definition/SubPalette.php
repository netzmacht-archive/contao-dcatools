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

namespace DcaTools\Definition;

/**
 * Class SubPalette
 *
 * @package Prototype\Palette
 */
class SubPalette extends PropertyContainer
{

	/**
	 * @param string $strName
	 * @param DataContainer $objDataContainer
	 */
	public function __construct($strName, DataContainer $objDataContainer)
	{
		$arrDefinition =& $objDataContainer->getDefinition();

		parent::__construct($strName, $objDataContainer, $arrDefinition['subpalettes'][$strName]);

		$this->loadFromDefinition();
	}


	/**
	 * Remove child from parent
	 *
	 * @return $this
	 */
	public function remove()
	{
		$this->getDataContainer()->removeSubPalette($this);
		unset($this->objDataContainer);

		return $this;
	}

	/**
	 * Extend an existing node of the same type
	 *
	 * @param SubPalette $node
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function extend($node)
	{
		if(is_string($node))
		{
			$node = $this->getDataContainer()->getSubPalette($node);
		}
		elseif(get_class($node) != get_class($this))
		{
			throw new \RuntimeException("Node '{$node->getName()}' is not the same Node type");
		}

		/** @var $objNode PropertyContainer */

		$this->arrProperties = array_merge($this->arrProperties, $node->getProperties());
		$this->updateDefinition();

		return $this;
	}


	/**
	 * Get Selector of the subpalette
	 * @return Property[]|void
	 */
	public function getSelector()
	{
		$arrSelectors = array();

		foreach(parent::getSelectors() as $strName => $objProperty)
		{
			if(strpos($this->getName(), $strName) === 0)
			{
				$arrSelectors[$strName] = $objProperty;
			}
		}
	}


	/**
	 * Load propertys from definition
	 */
	protected function loadFromDefinition()
	{
		$arrProperties = explode(',', $this->getDefinition());

		foreach($arrProperties as $strProperty)
		{
			if($strProperty != '' && $this->getDataContainer()->hasProperty($strProperty))
			{
				$this->addProperty($strProperty);
			}
		}
	}


	/**
	 * Prepare argument so that an array of name and the object is passed
	 *
	 * @param DataContainer $objReference
	 * @param Legend|string $node
	 * @param bool $blnNull return null if property does not exists
	 *
	 * @return array[string|Legend|null]
	 */
	public static function argument(DataContainer $objReference, $node, $blnNull=true)
	{
		return static::prepareArgument($objReference, $node, $blnNull, 'SubPalette');
	}


	/**
	 * @param null $strKey
	 * @return mixed
	 */
	public function get($strKey=null)
	{
		return $this->definition;
	}
}