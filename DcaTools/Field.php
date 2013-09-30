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

namespace Netzmacht\DcaTools;

use Netzmacht\DcaTools\Node\Child;
use Netzmacht\DcaTools\Node\FieldContainer;
use Netzmacht\DcaTools\Node\Node;

/**
 * Class Field
 * @package Netzmacht\DcaTools\Palette
 */
class Field extends Child
{
	/**
	 * @var Container
	 */
	protected $objParent;


	/**
	 * @param string $strName
	 * @param DataContainer $objDataContainer
	 * @param FieldContainer $objParent
	 */
	public function __construct($strName, DataContainer $objDataContainer, FieldContainer $objParent=null)
	{
		$arrDefinition = $objDataContainer->getDefinition();

		parent::__construct($strName, $objDataContainer, $arrDefinition['fields'][$strName]);

		$this->objParent = $objParent === null ? $objDataContainer : $objParent;
	}


	/**
	 * @return Container|FieldContainer
	 */
	public function getParent()
	{
		return $this->objParent;
	}


	/**
	 * Test if field is an selector or update value
	 *
	 * return $this if value is updated
	 *
	 * @return bool|$this
	 */
	public function isSelector()
	{
		return $this->getDataContainer()->hasSelector($this);
	}


	/**
	 * @return bool
	 */
	public function hasActiveSubPalette()
	{
		$objRecord = $this->getDataContainer()->getRecord();

		if($objRecord === null)
		{
			return false;
		}

		if($objRecord->{$this->getName()} == 1)
		{
			return $this->getDataContainer()->hasSubPalette($this->getName());
		}

		return $this->getDataContainer()->hasSubPalette($this->strName . '_' . $objRecord->{$this->strName});
	}


	/**
	 * Get activated sub palette. Method does not check if element is an selector and if subpalette exists.
	 * Methods has to be called before
	 *
	 * @return SubPalette|null
	 */
	public function getActiveSubPalette()
	{
		$objRecord = $this->getDataContainer()->getRecord();

		if($objRecord === null)
		{
			return null;
		}

		$varValue = $objRecord->{$this->strName};

		if($varValue == 1)
		{
			if($this->getDataContainer()->hasSubPalette($this->strName))
			{
				return $this->getDataContainer()->getSubPalette($this->strName);
			}
		}
		else
		{
			return $this->getDataContainer()->getSubPalette($this->strName . '_' . $objRecord->{$this->strName});
		}

		return null;
	}


	/**
	 * get a defined subpalette
	 *
	 * @param null $mixedValue
	 * @param int $intFlag
	 * @return SubPalette
	 * @throws \RuntimeException
	 */
	public function getSubPalette($mixedValue=null, $intFlag=null)
	{
		$strName = $this->strName;

		if($mixedValue === null)
		{
			return $this->getDataContainer()->getSubPalette($strName, $intFlag);
		}

		$strName .= '_' . $mixedValue;
		return $this->getDataContainer()->getSubPalette($strName, $intFlag);
	}


	/**
	 * Append field to legend or supalette
	 *
	 * @param FieldContainer $objContainer
	 * @param null $strReferenceField
	 * @param null $intPosition
	 *
	 * @return $this
	 */
	public function appendTo(FieldContainer $objContainer, $reference=null, $intPosition=Field::POS_LAST)
	{
		if($this->getParent() != $objContainer)
		{
			$this->getParent()->removeField($this);
		}
		else {
			$this->objParent = $objContainer;
		}

		$this->getParent()->moveField($this, $reference, $intPosition);

		return $this;
	}


	/**
	 * Move Palette to new place
	 *
	 * @param string|FieldContainer $reference
	 *
	 * @return $this
	 */
	public function appendAfter($reference)
	{
		$this->getParent()->moveField($this, $reference, static::POS_AFTER);

		return $this;
	}


	/**
	 * Move Palette to new place
	 *
	 * @param string|FieldContainer $reference
	 *
	 * @return $this
	 */
	public function appendBefore($reference)
	{
		$this->getParent()->moveField($this, $reference, static::POS_BEFORE);

		return $this;
	}


	/**
	 * @param bool $blnUpdateParent
	 *
	 * @return $this
	 */
	public function remove($blnRemoveFromDataContainer=false)
	{
		$this->objParent->removeField($this, $blnRemoveFromDataContainer);

		return $this;
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param Node $objNode
	 * @return $this
	 */
	public function extend($objNode)
	{
		if(is_string($objNode))
		{
			$objNode = $this->getDataContainer()->getField($objNode);
		}
		elseif(!$objNode instanceof Field)
		{
			throw new \RuntimeException("Node '{$objNode}' is not a field");
		}

		$this->definition = $objNode->getDefinition();
		$this->dispatch('change');

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function toString()
	{
		return $this->getName();
	}


	/**
	 * @return mixed
	 */
	public function toArray()
	{
		return $this->getName();
	}


}