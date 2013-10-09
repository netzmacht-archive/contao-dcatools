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
use Netzmacht\DcaTools\Node\FieldAccess;
use Netzmacht\DcaTools\Node\FieldContainer;
use Netzmacht\DcaTools\Node\Node;
use Netzmacht\DcaTools\Palette\SubPalette;

/**
 * Class Field
 * @package Netzmacht\DcaTools\Palette
 */
class Field extends Child
{
	/**
	 * @var FieldContainer
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
	 * @return FieldContainer
	 */
	public function getParent()
	{
		return $this->objParent;
	}


	/**
	 * @param FieldContainer $objParent
	 */
	public function setParent(FieldContainer $objParent)
	{
		$this->objParent = $objParent;
	}


	/**
	 * Test if field is an selector or update value
	 *
	 * @param bool $blnSelector add as selector
	 *
	 * @return bool|$this
	 */
	public function isSelector($blnSelector=null)
	{
		if($blnSelector)
		{
			$this->getDataContainer()->addSelector($this);
		}

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
	 * Get activated sub palette. Method does not check if element is an selector and if SubPalette exists.
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
	 * Append field to legend or SubPalette
	 *
	 * @param FieldContainer $objContainer
	 * @param null $reference
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
	 * @param bool $blnRemoveFromDataContainer
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
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
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
	 * Export as string
	 * @return mixed
	 */
	public function toString()
	{
		return $this->getName();
	}


	/**
	 * Export to array wil return string because fields are handled as string in meta palettes
	 *
	 * @return mixed
	 */
	public function toArray()
	{
		return $this->getName();
	}


	/**
	 * Update definition
	 */
	public function updateDefinition()
	{
		// TODO not supported so far, will update the global field settings in a future version
	}


}