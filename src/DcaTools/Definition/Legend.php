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

use DcaTools\Definition;

/**
 * Class Legend represents legends of a palette
 * @package DcaTools\Palette
 */
class Legend extends PropertyContainer
{

	/**
	 * Modifiers like hide
	 * @var array
	 */
	protected $arrModifiers = array();


	/**
	 * @var Palette
	 */
	protected $objPalette;


	/**
	 * Constructor
	 *
	 * @param $strName
	 * @param DataContainer $objDataContainer
	 * @param Palette $objPalette
	 */
	public function __construct($strName, DataContainer $objDataContainer, Palette $objPalette)
	{
		// no properties stored so far
		$definition = '';

		parent::__construct($strName, $objDataContainer, $definition);

		$this->objPalette = $objPalette;
	}


	/**
	 * Prepare for cloning
	 */
	public function __clone()
	{
		unset($this->objPalette);
	}


	/**
	 * Get Definition
	 *
	 * @param $strKey
	 * @return mixed
	 */
	public function get($strKey=null)
	{
		return $this->asString();
	}


	/**
	 * @return Palette
	 */
	public function getPalette()
	{
		return $this->objPalette;
	}


	/**
	 * @param Palette $objPalette
	 */
	public function setPalette(Palette $objPalette)
	{
		$this->objPalette = $objPalette;
	}


	/**
	 * Add an modifer
	 * @param string $strName
	 *
	 * @return $this
	 */
	public function addModifier($strName)
	{
		if(!in_array($strName, $this->arrModifiers))
		{
			$this->arrModifiers[] = $strName;
		}

		return $this;
	}


	/**
	 * Check if an modifier exists
	 *
	 * @param $strName
	 *
	 * @return bool
	 */
	public function hasModifier($strName)
	{
		return in_array($strName, $this->arrModifiers);
	}


	/**
	 * Get all modifiers
	 *
	 * @return array
	 */
	public function getModifiers()
	{
		return $this->arrModifiers;
	}


	/**
	 * Append legend to a Palette
	 *
	 * @param Palette $objPalette
	 * @param null|Legend $reference
	 * @param null|int $intPosition
	 *
	 * @return $this
	 */
	public function appendTo(Palette $objPalette, $reference=null, $intPosition=Definition::LAST)
	{
		if($this->getPalette() != $objPalette)
		{
			$this->getPalette()->removeLegend($this);
			$this->objPalette = $objPalette;
		}

		$this->getPalette()->moveLegend($this, $reference, $intPosition);

		return $this;
	}


	/**
	 * Remove legend
	 *
	 * @return $this;
	 */
	public function remove()
	{
		$this->getPalette()->removeLegend($this);
		unset($this->objPalette);

		return $this;
	}


	/**
	 * Move Palette to new place
	 *
	 * @param Legend|string $reference
	 *
	 * @return $this
	 */
	public function appendAfter($reference)
	{
		$this->getPalette()->moveLegend($this, $reference, Definition::AFTER);

		return $this;
	}


	/**
	 * Move Palette to new place
	 *
	 * @param Legend|string $reference
	 *
	 * @return $this
	 */
	public function appendBefore($reference)
	{
		$this->getPalette()->moveLegend($this, $reference, Definition::BEFORE);

		return $this;
	}


	/**
	 * Export as string
	 *
	 * @return string
	 */
	public function asString()
	{
		/** @var Legend $objIterator */
		$strExport = parent::asString();

		if($strExport == '')
		{
			return $strExport;
		}

		$strModifier = implode(':', $this->getModifiers());
		$strModifier = $strModifier == '' ? '' : (':'. $strModifier);

		return sprintf('{%s_legend%s},%s', $this->getName(), $strModifier, $strExport);
	}


	/**
	 * Export as array
	 *
	 * @param bool $blnIncludeModifiers
	 *
	 * @return string
	 */
	public function asArray($blnIncludeModifiers=false)
	{
		/** @var Legend $objIterator */
		$arrProperties = parent::asArray();

		if(!$blnIncludeModifiers)
		{
			return $arrProperties;
		}

		$arrModifiers = array_map(
			function($item) {
				return ':' . $item;
			},
			$this->getModifiers()
		);

		return array_merge($arrModifiers, $arrProperties);
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param Legend $node
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function extend($node)
	{
		if(is_string($node))
		{
			$node = $this->getPalette()->getLegend($node);
		}
		elseif(get_class($node) != get_class($this))
		{
			throw new \RuntimeException("Node '{$node->getName()}' is not the same Node type");
		}

		foreach($node->getProperties() as $objProperty)
		{
			$this->arrProperties[$objProperty->getName()] = clone $objProperty;
			$this->arrProperties[$objProperty->getName()]->setParent($this);
		}

		$this->updateDefinition();

		return $this;
	}


	/**
	 * Prepare argument so that an array of name and the object is passed
	 *
	 * @param Palette $objReference
	 * @param Legend|string $node
	 * @param bool $blnNull return null if property does not exists
	 *
	 * @return array[string|Legend|null]
	 */
	public static function argument(Palette $objReference, $node, $blnNull=true)
	{
		return static::prepareArgument($objReference, $node, $blnNull, 'Legend');
	}


	/**
	 * Update data definition
	 */
	public function updateDefinition($blnPropagation=true)
	{
		if($blnPropagation)
		{
			$this->getPalette()->updateDefinition();
		}

		return $this;
	}

}
