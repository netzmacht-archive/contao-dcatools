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
 * Class Container is an abstract class as base for SubPalettes and Legends which contains fields
 * @package Netzmacht\DcaTools\Palette
 */
abstract class FieldContainer extends Child implements FieldAccess, Exportable
{

	/**
	 * @var Field[]
	 */
	protected $arrFields = array();


	/**
	 * Clone all fields as well
	 */
	public function __clone()
	{
		parent::__clone();

		foreach($this->arrFields as $strName => $objField)
		{
			$this->arrFields[$strName] = clone $objField;
			$this->arrFields[$strName]->setParent($this);
		}
	}


	/**
	 * Add a field
	 *
	 * @param Field|string $field
	 * @param string|Field|null $reference field reference
	 * @param int $intPosition Position where to insert
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addField($field, $reference=null, $intPosition=Node::POS_LAST)
	{
		if(is_string($field))
		{
			// get base field of DataContainer
			if($this->getDataContainer()->hasField($field))
			{
				$field = clone $this->getDataContainer()->getField($field);
			}
			else
			{
				$field = clone $this->getDataContainer()->createField($field);
			}
		}
		elseif($this->hasField($field))
		{
			throw new \RuntimeException("Field '{$field->getName()}' already exists.");
		}

		// register FieldContainer to Field events
		$field->addListener('move',   array($this, 'fieldListener'));
		$field->addListener('change', array($this, 'fieldListener'));
		$field->addListener('remove', array($this, 'fieldListener'));

		// register DataContainer to delete event
		$field->addListener('delete', array($this->getDataContainer()), 'fieldListener');

		$this->addAtPosition($this->arrFields, $field, $reference, $intPosition);
		$field->dispatch('move');

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
	 * @param string|Field $field
	 *
	 * @return bool
	 */
	public function hasField($field)
	{
		return isset($this->arrFields[(string) $field]);
	}


	/**
	 * Create a new field
	 *
	 * @param $strName
	 *
	 * @return Field
	 *
	 * @throws \RuntimeException
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
	 * Remove a field from the container
	 *
	 * @param Field|string $field
	 * @param bool $blnFromDataContainer
	 *
	 * @return $this
	 */
	public function removeField($field, $blnFromDataContainer=false)
	{
		$strName = (string) $field;

		if($this->hasField($strName))
		{
			$strEvent = $blnFromDataContainer ? 'delete' : 'remove';
			$this->arrFields[$strName]->dispatch($strEvent);

			unset($this->arrFields[$strName]);
			$this->dispatch('change');
		}

		return $this;
	}


	/**
	 * Move field to new position
	 *
	 * @param Field|string $field
	 * @param null $reference
	 * @param $intPosition
	 * @return $this
	 */
	public function moveField($field, $reference=null, $intPosition=FieldContainer::POS_LAST)
	{
		if(is_string($field))
		{
			$strName = $field;
			$objField = $this->getField($strName);
		}
		else
		{
			$objField = $field;
			$strName = $objField->getName();
		}

		if($this->hasField($strName))
		{
			unset($this->arrFields[$strName]);

			$this->addAtPosition($this->arrFields, $objField, $reference, $intPosition);
			$objField->dispatch('move');
		}
		else
		{
			$this->addField($objField, $reference, $intPosition);
		}

		return $this;
	}


	/**
	 * Get all fields and also include activated fields in SubPalettes
	 *
	 * @return array
	 */
	public function getActiveFields()
	{
		$arrFields = array();

		foreach($this->getFields() as $objField)
		{
			$arrFields[$objField->getName()] = $objField;

			if($objField->isSelector() && $objField->hasActiveSubPalette())
			{
				$arrFields = array_merge($arrFields, $objField->getActiveSubPalette()->getActiveFields());
			}
		}

		return $arrFields;
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

		foreach($this->getFields() as $strName => $objField)
		{
			if($objField->isSelector())
			{
				$arrSelectors[$strName] = $objField;
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
			case 'rename':
				/** @var $objEvent \Netzmacht\DcaTools\Event\Event */
				$objConfig = $objEvent->getConfig();
				$objDispatcher = $objEvent->getDispatcher();

				/** @var $objDispatcher Field */
				$this->arrFields[$objDispatcher->getName()] = $this->arrFields[$objConfig->get('origin')];
				unset($this->arrFields[$objConfig->get('origin')]);

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
