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
use Netzmacht\DcaTools\Field;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class Container is an abstract class as base for subpalettes and legends which contains fields
 * @package Netzmacht\DcaTools\Palette
 */
abstract class FieldContainer extends Child implements \IteratorAggregate, Exportable
{

	/**
	 * @var Field[]
	 */
	protected $arrFields = array();


	/**
	 * Add a field
	 *
	 * @param Field $objField
	 * @param string|Field|null $reference field reference
	 * @param int $intPosition Position where to insert
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addField($objField, $reference=null, $intPosition=Node::POS_LAST)
	{
		if(is_string($objField))
		{
			$strName = $objField;
			if($this->getDataContainer()->hasField($objField))
			{
				$objField = clone $this->getDataContainer()->getField($objField);
			}
			else
			{
				$objField = clone $this->getDataContainer()->createField($objField);
			}
		}
		elseif($this->hasField($objField))
		{
			throw new \RuntimeException("Field '{$objField->getName()}' already exists.");
		}

		$objField->addListener('change', array($this, 'fieldListener'));
		$objField->addListener('remove', array($this, 'fieldListener'));

		$this->addAtPosition($this->arrFields, $objField, $reference, $intPosition);
		$objField->dispatch('change');

		return $this;
	}


	/**
	 * Get an Field
	 *
	 * @param string $strName
	 *
	 * @return Field
	 *
	 * @throws \RuntimeException if Field does not exists
	 */
	public function getField($strName)
	{
		if($this->hasField($strName))
		{
			return $this->arrFields[$strName];
		}

		throw new \RuntimeException("Field '$strName' does not exists.");
	}


	/**
	 * @return array|Field[]
	 */
	public function getFields()
	{
		return $this->arrFields;
	}


	/**
	 * Check if field exists in container
	 *
	 * @param string $strName
	 *
	 * @return bool
	 */
	public function hasField($strName)
	{
		if($strName instanceof Field)
		{
			$strName = $strName->getName();
		}

		return isset($this->arrFields[$strName]);
	}


	/**
	 * @param $strName
	 *
	 * @return Field
	 */
	public function createField($strName)
	{
		if($this->hasField($strName))
		{
			throw new \RuntimeException("Field '$strName' already exists");
		}

		if(!$this->getDataContainer()->hasField($strName))
		{
			$this->getDataContainer()->createField($strName);
		}

		$this->addField($strName);

		return $this->getField($strName);
	}


	/**
	 * Get iterator for accessing fields
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->arrFields);
	}


	/**
	 * Remove a field from the container
	 *
	 * @param string $strName
	 * @param bool $blnFromDataContainer
	 *
	 * @return $this
	 */
	public function removeField($strName, $blnFromDataContainer=false)
	{
		if($this->hasField($strName))
		{
			if($strName instanceof Field)
			{
				$strName = $strName->getName();
			}

			if($blnFromDataContainer)
			{
				$this->arrFields[$strName]->dispatch('removeFromDataContainer');
			}
			else {
				$this->arrFields[$strName]->dispatch('remove');
			}

			unset($this->arrFields[$strName]);
		}

		$this->dispatch('change');

		return $this;
	}


	/**
	 * Move field to new position
	 *
	 * @param Field $objField
	 * @param null $reference
	 * @param $intPosition
	 * @return $this
	 */
	public function moveField(Field $objField, $reference=null, $intPosition=FieldContainer::POS_LAST)
	{
		if($this->hasField($objField))
		{
			unset($this->arrFields[$objField->getName()]);

			$this->addAtPosition($this->arrFields, $objField, $reference, $intPosition);
			$objField->dispatch('change');
		}
		else
		{
			$this->addField($objField, $reference, $intPosition);
		}

		return $this;
	}


	/**
	 * Check if container has selector fields
	 *
	 * @return bool
	 */
	public function hasSelectors()
	{
		foreach($this->getFields() as $objField)
		{
			if($objField->isSelector())
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * Get all selectors containing to the
	 *
	 * @return Field[]
	 */
	public function getSelectors()
	{
		$arrSelectors = array();

		foreach($this->getFields() as $objField)
		{
			if($objField->isSelector())
			{
				$arrSelectors[$objField->getName()] = $objField;
			}
		}

		return $arrSelectors;
	}


	/**
	 * Export field list
	 *
	 * @return string
	 */
	public function toString()
	{
		return implode(',', array_keys($this->arrFields));
	}


	/**
	 * Export field names as array
	 *
	 * @return mixed
	 */
	public function toArray()
	{
		return array_keys($this->arrFields);
	}


	/**
	 * @param Event $objEvent
	 *
	 * @return mixed
	 */
	public function fieldListener(Event $objEvent)
	{
		switch($objEvent->getName())
		{
			case 'change':
			case 'remove':
				if(DcaTools::doAutoUpdate())
				{
					$this->updateDefinition();
				}

				$this->dispatch('change');
				break;
		}
	}

}