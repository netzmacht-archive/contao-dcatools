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

use DcaTools\Structure\PropertyContainerInterface;
use Symfony\Component\EventDispatcher\Event;


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
	 * @param Property $objProperty
	 * @param string $strLegend
	 * @param Property|string|null $reference
	 * @param $intPosition
	 *
	 * @return $this
	 */
	public function addProperty(Property $objProperty, $strLegend='default', $reference=null, $intPosition=Palette::POS_LAST)
	{
		if(!$this->hasLegend($strLegend))
		{
			$this->createLegend($strLegend);
		}

		$this->getLegend($strLegend)->addProperty($objProperty, $reference, $intPosition);

		return $this;
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
	 * @return \RecursiveIteratorIterator|Property[]
	 */
	public function getProperties()
	{
		return new \RecursiveIteratorIterator($this->getLegends());
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
	public function moveProperty($property, $strLegend='default', $reference=null, $intPosition=PropertyContainer::POS_LAST)
	{
		$this->getLegend($strLegend)->moveProperty($property, $reference, $intPosition);

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
		return $this->getLegend($strLegend)->createProperty($strName);
	}


	/**
	 * Check if container has selector propertys
	 *
	 * @return bool
	 */
	public function hasSelectors()
	{
		foreach($this->getLegends() as $objLegend)
		{
			if($objLegend->hasSelectors())
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * Get all selectors containing to the
	 *
	 * @return \ArrayIterator|Property[]
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

		return new \ArrayIterator($arrSelectors);
	}


	/**
	 * Get all SubPalettes, same as DataContainer->getSubPalettes
	 *
	 * @return SubPalette[]
	 */
	public function getSubPalettes()
	{
		return $this->getDataContainer()->getSubPalettes();
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
	 * @return \ArrayIterator|Legend[]
	 */
	public function getLegends()
	{
		return new \ArrayIterator($this->arrLegends);
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
	public function createLegend($strName, $reference=null, $intPosition=Palette::POS_LAST)
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
			$objLegend = $this->arrLegends[$strName];
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
	public function moveLegend($objLegend, $reference=null, $intPosition=Palette::POS_LAST)
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

			if(isset($matches[2]))
			{
				$objLegend->addModifier('hide');
			}

			// create each property
			foreach($arrProperties as $strProperty)
			{
				if($strProperty == '')
				{
					continue;
				}

				$objProperty = clone $this->getDataContainer()->getProperty($strProperty);

				// prevent faulty dca breaks loading
				try {
					$objLegend->addProperty($objProperty);
				}
				catch(\RuntimeException $e){}
			}

			// prevent faulty dca breaks loading
			$this->arrLegends[$objLegend->getName()] = $objLegend;
		}

		return $this;
	}


	/**
	 * Export as string
	 *
	 * @return string
	 */
	public function asString()
	{
		return static::convertToString($this);
	}


	/**
	 * Convert list of properties to an array
	 *
	 * @param \Traversable $objIterator
	 *
	 * @return array
	 */
	public static function convertToString(\Traversable $objIterator)
	{
		$strExport = '';

		foreach($objIterator as $objLegend)
		{
			/** @var Legend $objLegend */
			$strProperties = $objLegend->asString();

			if($strProperties)
			{
				$strExport .= $strProperties . ';';
			}
		}

		return $strExport;
	}


	/**
	 * Export to array
	 *
	 * @return array
	 */
	public function asArray()
	{
		return static::convertToArray($this);
	}


	/**
	 * Convert list of properties to an array
	 *
	 * @param \Traversable $objIterator
	 *
	 * @return array
	 */
	public static function convertToArray(\Traversable $objIterator)
	{
		$arrExport = array();

		foreach($objIterator as $strLegend => $objLegend)
		{
			/** @var Legend $objLegend */
			$arrExport[$strLegend] = $objLegend->asArray();
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

		return $this;
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param Node $objNode
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function extend($objNode)
	{
		if(is_string($objNode))
		{
			$objNode = $this->getDataContainer()->getPalette($objNode);
		}
		elseif(!$objNode instanceof Palette)
		{
			throw new \RuntimeException("Node '{$objNode->getName()}' is not a Palette");
		}

		foreach($objNode->getLegends() as $strName => $objLegend)
		{
			$this->arrLegends[$strName] = clone $objLegend;
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