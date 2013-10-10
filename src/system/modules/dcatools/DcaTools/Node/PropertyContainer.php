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
 * Class Container is an abstract class as base for SubPalettes and Legends which contains propertys
 * @package Netzmacht\DcaTools\Palette
 */
abstract class PropertyContainer extends Child implements PropertyAccess, Exportable
{

	/**
	 * @var Property[]
	 */
	protected $arrPropertys = array();


	/**
	 * Clone all propertys as well
	 */
	public function __clone()
	{
		parent::__clone();

		foreach($this->arrPropertys as $strName => $objProperty)
		{
			$this->arrPropertys[$strName] = clone $objProperty;
			$this->arrPropertys[$strName]->setParent($this);
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
		if(is_string($property))
		{
			// get base property of DataContainer
			if($this->getDataContainer()->hasProperty($property))
			{
				$property = clone $this->getDataContainer()->getProperty($property);
			}
			else
			{
				$property = clone $this->getDataContainer()->createProperty($property);
			}
		}
		elseif($this->hasProperty($property))
		{
			throw new \RuntimeException("Property '{$property->getName()}' already exists.");
		}

		// register PropertyContainer to Property events
		$property->addListener('move',   array($this, 'propertyListener'));
		$property->addListener('change', array($this, 'propertyListener'));
		$property->addListener('remove', array($this, 'propertyListener'));

		// register DataContainer to delete event
		$property->addListener('delete', array($this->getDataContainer()), 'propertyListener');

		$this->addAtPosition($this->arrPropertys, $property, $reference, $intPosition);
		$property->dispatch('move');

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
			return $this->arrPropertys[$strName];
		}

		throw new \RuntimeException("Property '$strName' does not exists.");
	}


	/**
	 * @return array|Property[]
	 */
	public function getPropertys()
	{
		return $this->arrPropertys;
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
		return isset($this->arrPropertys[(string) $property]);
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

		$this->addProperty($strName);

		return $this->getProperty($strName);
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
			$this->arrPropertys[$strName]->dispatch($strEvent);

			unset($this->arrPropertys[$strName]);
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
			unset($this->arrPropertys[$strName]);

			$this->addAtPosition($this->arrPropertys, $objProperty, $reference, $intPosition);
			$objProperty->dispatch('move');
		}
		else
		{
			$this->addProperty($objProperty, $reference, $intPosition);
		}

		return $this;
	}


	/**
	 * Get all propertys and also include activated propertys in SubPalettes
	 *
	 * @return array
	 */
	public function getActivePropertys()
	{
		$arrPropertys = array();

		foreach($this->getPropertys() as $objProperty)
		{
			$arrPropertys[$objProperty->getName()] = $objProperty;

			if($objProperty->isSelector() && $objProperty->hasActiveSubPalette())
			{
				$arrPropertys = array_merge($arrPropertys, $objProperty->getActiveSubPalette()->getActivePropertys());
			}
		}

		return $arrPropertys;
	}


	/**
	 * Get iterator for accessing propertys
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->arrPropertys);
	}


	/**
	 * Check if container has selector propertys
	 *
	 * @return bool
	 */
	public function hasSelectors()
	{
		foreach($this->getPropertys() as $objProperty)
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

		foreach($this->getPropertys() as $strName => $objProperty)
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
		return implode(',', array_keys($this->arrPropertys));
	}


	/**
	 * Export property names as array
	 *
	 * @return mixed
	 */
	public function asArray()
	{
		return array_keys($this->arrPropertys);
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

				$this->arrPropertys[$objProperty->getName()] = $this->arrPropertys[$objEvent->getArgument('origin')];
				unset($this->arrPropertys[$objEvent->getArgument('origin')]);

				// no break

			case 'move':
			case 'change':
			case 'remove':
				if(DcaTools::doAutoUpdate())
				{
					$this->updateDefinition();
				}

				// propagate changes
				$this->dispatch('change');
				break;
		}
	}

}
