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
use Netzmacht\DcaTools\Property;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;


/**
 * Class Container is an abstract class as base for SubPalettes and Legends which contains properties
 * @package Netzmacht\DcaTools\Palette
 */
abstract class PropertyContainer extends Child implements PropertyAccess, Exportable
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
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addProperty($property, $reference=null, $intPosition=Node::POS_LAST)
	{
		if($property instanceof Property)
		{
			$strName = $property->getName();
		}
		else {
			$strName = $property;
			$property = clone $this->getDataContainer()->getProperty($strName);
		}

		if($this->hasProperty($strName))
		{
			throw new \RuntimeException("Property '{$strName}' already exists in {$this->getName()}.");
		}

		$objProperty = clone $this->getDataContainer()->getProperty($strName);

			// register PropertyContainer to Property events
		$objProperty->addListener('move',   array($this, 'propertyListener'));
		$objProperty->addListener('change', array($this, 'propertyListener'));
		$objProperty->addListener('remove', array($this, 'propertyListener'));

		// register DataContainer to delete event
		$objProperty->addListener('delete', array($this->getDataContainer()), 'propertyListener');

		$this->addAtPosition($this->arrProperties, $objProperty, $reference, $intPosition);
		$objProperty->dispatch('move');

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
		if($this->hasProperty($strName))
		{
			return $this->arrProperties[$strName];
		}

		throw new \RuntimeException("Property '$strName' does not exists.");
	}


	/**
	 * @return array|Property[]
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

		// register PropertyContainer to Property events
		$objProperty->addListener('move',   array($this, 'propertyListener'));
		$objProperty->addListener('change', array($this, 'propertyListener'));
		$objProperty->addListener('remove', array($this, 'propertyListener'));

		// register DataContainer to delete event
		$objProperty->addListener('delete', array($this->getDataContainer()), 'propertyListener');
		$this->arrProperties[$strName] = $objProperty;
		$objProperty->dispatch('move');

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
		$strName = (string) $property;

		if($this->hasProperty($strName))
		{
			$strEvent = $blnFromDataContainer ? 'delete' : 'remove';
			$this->arrProperties[$strName]->dispatch($strEvent);

			unset($this->arrProperties[$strName]);
			$this->dispatch('change');
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
	public function moveProperty($property, $reference=null, $intPosition=PropertyContainer::POS_LAST)
	{
		if(is_string($property))
		{
			$strName = $property;
			$objProperty = $this->getProperty($strName);
		}
		else
		{
			$objProperty = $property;
			$strName = $objProperty->getName();
		}

		if($this->hasProperty($strName))
		{
			unset($this->arrProperties[$strName]);

			$this->addAtPosition($this->arrProperties, $objProperty, $reference, $intPosition);
			$objProperty->dispatch('move');
		}
		else
		{
			$this->addProperty($objProperty, $reference, $intPosition);
		}

		return $this;
	}


	/**
	 * Get all properties and also include activated properties in SubPalettes
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
				$arrProperties = array_merge($arrProperties, $objProperty->getActiveSubPalette()->getActiveProperties());
			}
		}

		return $arrProperties;
	}


	/**
	 * Get iterator for accessing properties
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->arrProperties);
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
		return implode(',', array_keys($this->arrProperties));
	}


	/**
	 * Export property names as array
	 *
	 * @return mixed
	 */
	public function asArray()
	{
		return array_keys($this->arrProperties);
	}


	/**
	 * @param Event $objEvent
	 *
	 * @return mixed
	 */
	public function propertyListener(Event $objEvent)
	{
		switch($objEvent->getName())
		{
			case 'rename':
				/** @var GenericEvent $objEvent */
				/** @var Property $objProperty */
				$objProperty = $objEvent->getSubject();

				$this->arrProperties[$objProperty->getName()] = $this->arrProperties[$objEvent->getArgument('origin')];
				unset($this->arrProperties[$objEvent->getArgument('origin')]);

				// no break

			case 'move':
			case 'change':
			case 'remove':
				$this->updateDefinition();

				// propagate changes
				$this->dispatch('change');
				break;
		}
	}

}
