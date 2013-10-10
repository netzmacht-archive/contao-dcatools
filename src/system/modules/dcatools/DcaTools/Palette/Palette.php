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

namespace Netzmacht\DcaTools\Palette;

use Netzmacht\DcaTools\DataContainer;
use Netzmacht\DcaTools\DcaTools;
use Netzmacht\DcaTools\Property;
use Netzmacht\DcaTools\Node\Child;
use Netzmacht\DcaTools\Node\PropertyAccess;
use Netzmacht\DcaTools\Node\PropertyContainer;
use Netzmacht\DcaTools\Node\Node;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class Palette provides methods for manipulating palette
 * @package Netzmacht\Prototype\Palette
 */
class Palette extends Child implements PropertyAccess
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

		$this->addListener('change', array($this, 'updateDefinition'));

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
	 * @return $this
	 */
	public function addProperty($property, $strLegend='default', $reference=null, $intPosition=Palette::POS_LAST)
	{
		$this->getLegend($strLegend)->addProperty($property, $reference, $intPosition);

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
	 * Get all propertys and also include activated propertys in supalettes
	 *
	 * @return array
	 */
	public function getActiveProperties()
	{
		$arrProperties = array();

		foreach($this->getProperties() as $objProperty)
		{
			$arrProperties[$objProperty->getName()] = $objProperty;

			if($objProperty->isSelector() && $objProperty->hasActiveSubPalette())
			{
				$arrProperties = array_merge($arrProperties, $objProperty->getActiveSubPalette()->getProperties());
			}
		}

		return $arrProperties;
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
	 * Get all SubPalettes, same as DataContainer->getSubPalettes
	 *
	 * @return SubPalette[]
	 */
	public function getSubPalettes()
	{
		return $this->getDataContainer()->getSubPalettes();
	}


	/**
	 * Get all active SubPalettes
	 *
	 * @return SubPalette[]
	 */
	public function getActiveSubPalettes()
	{
		$arrSubPalettes = array();

		foreach($this->getDataContainer()->getSelectors() as $objProperty)
		{
			if($objProperty->hasActiveSubPalette())
			{
				$objSubPalette = $objProperty->getActiveSubPalette();
				$arrSubPalettes[$objSubPalette->getName()] = $objSubPalette;
			}
		}

		return $arrSubPalettes;
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
	 * @param Legend|string $objLegend
	 * @param string|Legend|null $reference
	 * @param int $intPosition
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addLegend($objLegend, $reference=null, $intPosition=Palette::POS_LAST)
	{
		if(is_string($objLegend))
		{
			$objLegend = new Legend($objLegend, $this->getDataContainer(), $this);
		}

		if($this->hasLegend($objLegend))
		{
			throw new \RuntimeException("Legend '{$objLegend->getName()}' is already added.");
		}

		$objLegend->addListener('move',   array($this, 'legendListener'));
		$objLegend->addListener('remove', array($this, 'legendListener'));
		$objLegend->addListener('change', array($this, 'legendListener'));

		$this->addAtPosition($this->arrLegends, $objLegend, $reference, $intPosition);
		$objLegend->dispatch('move');

		return $this;
	}


	/**
	 * Create a new legend
	 *
	 * @param $strName
	 *
	 * @return Legend
	 *
	 * @throws \RuntimeException
	 */
	public function createLegend($strName)
	{
		$this->addLegend($strName);

		return $this->getLegend($strName);
	}


	/**
	 * Remove legend of palette
	 *
	 * @param Legend|string $legend
	 * @param bool $blnFromAllPalettes
	 *
	 * @return $this
	 */
	public function removeLegend($legend, $blnFromAllPalettes=false)
	{
		$strName = is_object($legend) ? $legend->getName() : $legend;

		if($this->hasLegend($strName))
		{
			$objLegend = $this->arrLegends[$strName];
			unset($this->arrLegends[$strName]);

			$strEvent = $blnFromAllPalettes ? 'delete' : 'remove';
			$objLegend->dispatch($strEvent);
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
		$objLegend->dispatch('move');

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
			try {
				$this->addLegend($objLegend);
			}
			catch(\RuntimeException $e){}

		}

		return $this;
	}


	/**
	 * Export to string
	 *
	 * @param bool $blnActive
	 *
	 * @return mixed|string
	 */
	public function asString($blnActive=false)
	{
		$strExport = '';

		foreach($this->getLegends() as $objLegend)
		{
			$strProperties = $objLegend->asString($blnActive);

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
	 * @param bool $blnActive
	 * @return array|mixed
	 */
	public function asArray($blnActive=false)
	{
		$arrExport = array();

		foreach($this->getLegends() as $objLegend)
		{
			$arrExport = array_merge($arrExport, $objLegend->asArray($blnActive));
		}

		return $arrExport;
	}


	/**
	 * Append Palette to a DataContainer
	 *
	 * @param DataContainer $objDataContainer
	 * @param null $strReference
	 * @param $intPosition
	 *
	 * @return Palette
	 */
	public function appendTo(DataContainer $objDataContainer, $strReference=null, $intPosition=Palette::POS_LAST)
	{
		if(!$objDataContainer->hasPalette($this))
		{
			$this->objDataContainer = $objDataContainer;
			$objDataContainer->addPalette($this, $strReference, $intPosition);
		}

		return $this;
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
	 * Listen to property changes
	 *
	 * @param Event $objEvent
	 *
	 * @return void
	 */
	public function propertyListener(Event $objEvent)
	{
		switch($objEvent->getName())
		{
			case 'change':
				$this->dispatch('change');
				break;
		}
	}


	/**
	 * @param Event $objEvent
	 */
	public function legendListener(Event $objEvent)
	{
		switch($objEvent->getName())
		{
			case 'rename':
				/** @var $objEvent \Netzmacht\DcaTools\Event\Event */
				$objConfig = $objEvent->getConfig();
				$objDispatcher = $objEvent->getDispatcher();

				/** @var $objDispatcher Legend */
				$this->arrLegends[$objDispatcher->getName()] = $this->arrLegends[$objConfig->get('origin')];
				unset($this->arrLegends[$objConfig->get('origin')]);

				// no break here

			case 'move':
			case 'remove':
			case 'change':
				if(DcaTools::doAutoUpdate())
				{
					$this->updateDefinition();
				}

				$this->dispatch('change');
				break;
		}
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

		$this->dispatch('change');

		return $this;
	}

}