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
use DcaTools\Structure\PropertyContainerInterface;


/**
 * Class Palette provides methods for manipulating palette
 * @package Prototype\Palette
 */
class Palette extends Node implements PropertyContainerInterface
{

	/**
	 * Legends of the palette
	 * @var Legend[]
	 */
	protected $arrLegends = array();


	/**
	 * Constructor
	 * @param $strName
	 * @param DataContainer $objDataContainer
	 */
	public function __construct($strName, DataContainer $objDataContainer)
	{
		$definition =& $objDataContainer->getDefinition();

		if(!isset($definition['palettes'][$strName]))
		{
			$definition['palettes'][$strName] = '';
		}

		parent::__construct($strName, $objDataContainer, $definition['palettes'][$strName]);

		$this->loadFromDefinition();
	}


	public function __clone()
	{
		parent::__clone();

		foreach($this->arrLegends as $strName => $objLegend)
		{
			$this->arrLegends[$strName] = clone $objLegend;
			$this->arrLegends[$strName]->setPalette($this);
		}
	}


	/**
	 * Get Iterator for all legend
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->arrLegends);
	}


	/**
	 * Add Property
	 *
	 * @param Property|string $property
	 * @param string $strLegend
	 * @param Property|string|null $reference
	 * @param $intPosition
	 *
	 * @return Property
	 */
	public function addProperty($property, $strLegend='default', $reference=null, $intPosition=Definition::LAST)
	{
		if(!$this->hasLegend($strLegend))
		{
			$this->createLegend($strLegend);
		}

		return $this->getLegend($strLegend)->addProperty($property, $reference, $intPosition);
	}


	/**
	 * Get an Property
	 *
	 * @param string $strName
	 *
	 * @return Property
	 *
	 * @throws \RuntimeException if Property does not exists
	 */
	public function getProperty($strName)
	{
		foreach($this->arrLegends as $objLegend)
		{
			if($objLegend->hasProperty($strName))
			{
				return $objLegend->getProperty($strName);
			}
		}

		throw new \RuntimeException("Property '$strName' does not exists.");
	}


	/**
	 * @return array|Property[]
	 */
	public function getProperties()
	{
		$arrProperties = array();

		foreach($this->arrLegends as $objLegend)
		{
			$arrProperties = array_merge($arrProperties, $objLegend->getProperties());
		}

		return $arrProperties;
	}


	/**
	 * Get all property names
	 *
	 * @return array
	 */
	public function getPropertyNames()
	{
		return array_keys($this->getProperties());
	}


	/**
	 * Check if property exists in container
	 *
	 * @param string $strName
	 *
	 * @return bool
	 */
	public function hasProperty($strName)
	{
		foreach($this->arrLegends as $objLegend)
		{
			if($objLegend->hasProperty($strName))
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * Remove a property from the container
	 *
	 * @param string $strName
	 * @param bool $blnFromDataContainer
	 *
	 * @return $this
	 */
	public function removeProperty($strName, $blnFromDataContainer=false)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		foreach($this->getLegends() as $objLegend)
		{
			if($objLegend->hasProperty($strName))
			{
				$objLegend->removeProperty($strName, $blnFromDataContainer);
			}
		}

		return $this;
	}


	/**
	 * Move property to new position
	 *
	 * @param Property|string $property
	 * @param string $strLegend
	 * @param null $reference
	 * @param int $intPosition
	 *
	 * @return $this
	 */
	public function moveProperty($property, $strLegend='default', $reference=null, $intPosition=Definition::LAST)
	{
		list($strName, $objProperty) = Property::argument($this, $property);

		if(!$this->hasLegend($strLegend))
		{
			$this->createLegend($strLegend);
		}

		/** @var \DcaTools\Definition\Property $objProperty */
		if($objProperty === null)
		{
			if($this->hasProperty($strName))
			{
				$objProperty = $this->getProperty($strName);

				if($objProperty->getParent() != $this)
				{
					$objProperty->getParent()->removeProperty($objProperty);
					$this->getLegend($strLegend)->addProperty($objProperty);
				}
			}
			else {
				$objProperty = $this->getDataContainer()->getProperty($this);
				$this->getLegend($strLegend)->addProperty($objProperty);
			}
		}
		elseif($objProperty->getParent() != $this && !$this->getLegend($strLegend)->hasProperty($strName))
		{
			$objProperty->getParent()->removeProperty($objProperty);
			$this->getLegend($strLegend)->addProperty($objProperty);
		}

		$this->getLegend($strLegend)->moveProperty($objProperty, $reference, $intPosition);

		return $this;
	}


	/**
	 * Create a new property
	 *
	 * @param string $strName
	 * @param string $strLegend legend name
	 *
	 * @return Property
	 */
	public function createProperty($strName, $strLegend='default')
	{
		if(!$this->hasLegend($strLegend))
		{
			$this->createLegend($strLegend);
		}

		return $this->getLegend($strLegend)->createProperty($strName);
	}


	/**
	 * Check if container has selector propertys
	 *
	 * @return bool
	 */
	public function hasSelectors()
	{
		foreach($this->getDataContainer()->getSelectors() as $strName => $objProperty)
		{
			if($this->hasProperty($strName))
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * Get all selectors containing to the
	 *
	 * @return Property[]
	 */
	public function getSelectors()
	{
		$arrSelectors = array();

		foreach($this->getLegends() as $objLegend)
		{
			if($objLegend->hasSelectors())
			{
				$arrSelectors = array_merge($arrSelectors, $objLegend->getSelectors());
			}
		}

		return $arrSelectors;
	}


	/**
	 * Get a legend
	 *
	 * @param $strName
	 *
	 * @return Legend
	 *
	 * @throws \RuntimeException
	 */
	public function getLegend($strName)
	{
		if($this->hasLegend($strName))
		{
			return $this->arrLegends[$strName];
		}

		throw new \RuntimeException("Legend '$strName' does not exists");
	}


	/**
	 * Get all legends
	 *
	 * @return Legend[]
	 */
	public function getLegends()
	{
		return $this->arrLegends;
	}


	/**
	 * Test is legend exists
	 *
	 * @param $strName
	 *
	 * @return bool
	 */
	public function hasLegend($strName)
	{
		if($strName instanceof Legend)
		{
			$strName = $strName->getName();
		}

		return isset($this->arrLegends[$strName]);
	}


	/**
	 * add existing legend to palette
	 *
	 * @param string $strName
	 * @param string|Legend|null $reference
	 * @param int $intPosition
	 *
	 * @return Legend
	 *
	 * @throws \RuntimeException
	 */
	public function createLegend($strName, $reference=null, $intPosition=Definition::LAST)
	{
		if($this->hasLegend($strName))
		{
			throw new \RuntimeException("Legend '{$strName}' already exists.");
		}

		$objLegend = new Legend($strName, $this->getDataContainer(), $this);

		$this->addAtPosition($this->arrLegends, $objLegend, $reference, $intPosition);
		$this->updateDefinition();

		return $objLegend;
	}


	/**
	 * Remove legend of palette
	 *
	 * @param Legend|string $legend
	 *
	 * @return $this
	 */
	public function removeLegend($legend)
	{
		$strName = is_object($legend) ? $legend->getName() : $legend;

		if($this->hasLegend($strName))
		{
			unset($this->arrLegends[$strName]);

			$this->updateDefinition();
		}

		return $this;
	}


	/**
	 * @param $objLegend
	 * @param null $reference
	 * @param $intPosition
	 * @return $this
	 */
	public function moveLegend($objLegend, $reference=null, $intPosition=Definition::LAST)
	{
		$objLegend = is_string($objLegend) ? $this->getLegend($objLegend) : $objLegend;

		if($this->hasLegend($objLegend))
		{
			unset($this->arrLegends[$objLegend->getName()]);
		}

		$this->addAtPosition($this->arrLegends, $objLegend, $reference, $intPosition);
		$this->updateDefinition();

		return $this;
	}


	/**
	 * Load definition from the dca
	 *
	 * @return $this;
	 */
	protected function loadFromDefinition()
	{
		$arrDefinition = explode(';', $this->getDefinition());

		// go throw each legend
		foreach($arrDefinition as $strLegend)
		{
			if($strLegend == '')
			{
				continue;
			}

			$arrProperties = explode(',', $strLegend);

			// extract legend title and modifier
			preg_match('/\{(.*)_legend(:hide)?\}/', $arrProperties[0], $matches);
			array_shift($arrProperties);

			$objLegend = new Legend($matches[1], $this->getDataContainer(), $this);
			$this->arrLegends[$objLegend->getName()] = $objLegend;

			if(isset($matches[2]))
			{
				$objLegend->addModifier('hide');
			}

			// create each property
			foreach($arrProperties as $strProperty)
			{
				if($strProperty != '' && $this->getDataContainer()->hasProperty($strProperty))
				{
					$objLegend->addProperty($strProperty);
				}
			}
		}

		return $this;
	}


	/**
	 * Export
	 *
	 * @return array|mixed
	 */
	public function asString()
	{
		$strExport = '';

		foreach($this->getLegends() as $objLegend)
		{
			$strProperties = $objLegend->asString();

			if($strProperties)
			{
				if($strExport != '')
				{
					$strExport .= ';';
				}

				$strExport .= $strProperties;
			}

		}

		return $strExport;
	}


	/**
	 * Export to array
	 *
	 * @param bool $blnIncludeModifiers
	 *
	 * @return array|mixed
	 */
	public function asArray($blnIncludeModifiers=false)
	{
		$arrExport = array();

		foreach($this->getLegends() as $strLegend => $objLegend)
		{
			$arrExport[$strLegend] = $objLegend->asArray($blnIncludeModifiers);
		}

		return $arrExport;
	}


	/**
	 * remove Palette
	 * @return Palette
	 */
	public function remove()
	{
		$this->getDataContainer()->removePalette($this);
		unset($this->objDataContainer);

		return $this;
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param Palette|string $palette
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function extend($palette)
	{
		list($strName, $objPalette) = Palette::argument($this->getDataContainer(), $palette);

		/** @var Palette $objPalette */
		foreach($objPalette->getLegends() as $strName => $objLegend)
		{
			if(!$this->hasLegend($strName))
			{
				$this->createLegend($strName);
			}

			$legend = $this->getLegend($strName);

			/** @var Property $property */
			foreach($objLegend as $property)
			{
				if(!$legend->hasProperty($property))
				{
					$legend->addProperty($property);
				}
			}
		}

		$this->updateDefinition();
		return $this;
	}


	/**
	 * @param $strKey
	 * @return mixed|void
	 */
	public function get($strKey=null)
	{
		return $this->definition;
	}


	/**
	 * Prepare argument so that an array of name and the object is passed
	 *
	 * @param DataContainer $objReference
	 * @param Palette|string $node
	 * @param bool $blnNull return null if not exists for object
	 *
	 * @return array(string, Palette)
	 */
	public static function argument(DataContainer $objReference, $node, $blnNull=true)
	{
		return static::prepareArgument($objReference, $node, $blnNull, 'Palette');
	}


	/**
	 * Update the definition of current element
	 *
	 * @param bool $blnPropagation
	 *
	 * @return $this
	 */
	public function updateDefinition($blnPropagation = true)
	{
		$this->definition = $this->asString();
	}

}
