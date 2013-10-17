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
use DcaTools\Structure\PropertyContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;


/**
 * Class Container is an abstract class as base for SubPalettes and Legends which contains properties
 * @package DcaTools\Palette
 */
abstract class PropertyContainer extends Node implements PropertyContainerInterface
{

	/**
	 * @var Property[]
	 */
	protected $arrProperties = array();


	/**
	 * Clone all properties as well
	 */
	public function __clone()
	{
		parent::__clone();

		foreach($this->arrProperties as $strName => $objProperty)
		{
			$this->arrProperties[$strName] = clone $objProperty;
			$this->arrProperties[$strName]->setParent($this);
		}
	}


	/**
	 * Add a property
	 *
	 * @param Property|string $property
	 * @param string|Property|null $reference property reference
	 * @param int $intPosition Position where to insert
	 *
	 * @return Property
	 *
	 * @throws \RuntimeException
	 */
	public function addProperty($property, $reference=null, $intPosition=Definition::LAST)
	{
		/** @var Property $objProperty */
		list($strName, $objProperty) = Property::argument($this, $property);

		if($this->hasProperty($strName))
		{
			throw new \RuntimeException("Property '{$strName}' already exists in {$this->getName()}.");
		}

		if($objProperty !== null)
		{
			$this->requireSameDataContainer($objProperty);
			$blnInDataContainer = $objProperty->getParent() instanceof DataContainer;

			if($objProperty->getParent() != $this && !$blnInDataContainer)
			{
				$objProperty->getParent()->remove($strName);
			}
			elseif($blnInDataContainer)
			{
				$objProperty = clone $objProperty;
			}
		}
		else {
			$objProperty = clone $this->getDataContainer()->getProperty($strName);
		}

		$objProperty->setParent($this);

		$this->addAtPosition($this->arrProperties, $objProperty, $reference, $intPosition);
		$this->updateDefinition();

		return $objProperty;
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
		if($this->hasProperty($strName))
		{
			return $this->arrProperties[$strName];
		}

		throw new \RuntimeException("Property '$strName' does not exists.");
	}


	/**
	 * Get Properties
	 *
	 * @return Property[]
	 */
	public function getProperties()
	{
		return $this->arrProperties;
	}


	/**
	 * Check if property exists in container
	 *
	 * @param string|Property $property
	 *
	 * @return bool
	 */
	public function hasProperty($property)
	{
		return isset($this->arrProperties[(string) $property]);
	}


	/**
	 * Create a new property
	 *
	 * @param $strName
	 *
	 * @return Property
	 *
	 * @throws \RuntimeException
	 */
	public function createProperty($strName)
	{
		if($this->hasProperty($strName))
		{
			throw new \RuntimeException("Property '$strName' already exists");
		}

		if(!$this->getDataContainer()->hasProperty($strName))
		{
			$this->getDataContainer()->createProperty($strName);
		}

		$objProperty = clone $this->getDataContainer()->getProperty($strName);
		$this->arrProperties[$strName] = $objProperty;

		$this->updateDefinition();

		return $objProperty;
	}


	/**
	 * Remove a property from the container
	 *
	 * @param Property|string $property
	 * @param bool $blnFromDataContainer
	 *
	 * @return $this
	 */
	public function removeProperty($property, $blnFromDataContainer=false)
	{
		list($strName, $objProperty) = Property::argument($this, $property);

		if($this->hasProperty($strName))
		{
			if($blnFromDataContainer && $this != $this->getDataContainer())
			{
				$this->getDataContainer()->removeProperty($strName);
			}

			unset($this->arrProperties[$strName]);
			$this->updateDefinition();
		}

		return $this;
	}


	/**
	 * Move property to new position
	 *
	 * @param Property|string $property
	 * @param null $reference
	 * @param $intPosition
	 * @return $this
	 */
	public function moveProperty($property, $reference=null, $intPosition=Definition::LAST)
	{
		/** @var Property $objProperty */
		list($strName, $objProperty) = Property::argument($this, $property, false);

		$this->requireSameDataContainer($objProperty);

		if($objProperty->getParent() instanceof DataContainer)
		{
			if($this != $objProperty->getParent())
			{
				$objProperty = clone $objProperty;
				$objProperty->setParent($this);
			}
		}
		elseif($this !== $objProperty->getParent())
		{
			$objProperty->getParent()->remove();
		}

		if($this->hasProperty($strName))
		{
			unset($this->arrProperties[$strName]);

			$this->addAtPosition($this->arrProperties, $objProperty, $reference, $intPosition);
			$this->updateDefinition();
		}
		else
		{
			$this->addProperty($objProperty, $reference, $intPosition);
		}

		return $this;
	}


	/**
	 * Retrieve the names of all defined properties.
	 *
	 * @return string[]
	 */
	public function getPropertyNames()
	{
		return array_keys($this->arrProperties);
	}



	/**
	 * Get iterator for accessing properties
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->getProperties());
	}


	/**
	 * Check if container has selector properties
	 *
	 * @return bool
	 */
	public function hasSelectors()
	{
		foreach($this->getProperties() as $objProperty)
		{
			if($objProperty->isSelector())
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

		foreach($this->getProperties() as $strName => $objProperty)
		{
			if($objProperty->isSelector())
			{
				$arrSelectors[$strName] = $objProperty;
			}
		}

		return $arrSelectors;
	}


	/**
	 * Export property list
	 *
	 * @return string
	 */
	public function asString()
	{
		$strReturn = '';

		foreach($this->getProperties() as $strName => $objProperty)
		{
			if($strReturn != '')
			{
				$strReturn .= ',';
			}

			$strReturn .= $strName;
		}

		return $strReturn;
	}


	/**
	 * Export property names as array
	 *
	 * @return mixed
	 */
	public function asArray()
	{
		$arrReturn = array();

		foreach($this->getProperties() as $strName => $objProperty)
		{
			$arrReturn[] = $strName;
		}

		return $arrReturn;
	}


	/**
	 * Update defintion
	 *
	 * @param bool $blnPropagation
	 *
	 * @return $this
	 */
	public function updateDefinition($blnPropagation=true)
	{
		$this->definition = $this->asString();

		return $this;
	}

}
