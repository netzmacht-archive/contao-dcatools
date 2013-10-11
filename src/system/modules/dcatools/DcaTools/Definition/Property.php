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

namespace Netzmacht\DcaTools\Definition;

use DcGeneral\DataDefinition\PropertyInterface;

/**
 * Class Property
 * @package Netzmacht\DcaTools\Palette
 */
class Property extends Node implements PropertyInterface
{
	/**
	 * @var PropertyContainer
	 */
	protected $objParent;


	/**
	 * @param string $strName
	 * @param DataContainer $objDataContainer
	 * @param PropertyContainer $objParent
	 */
	public function __construct($strName, DataContainer $objDataContainer, PropertyContainer $objParent=null)
	{
		$arrDefinition = $objDataContainer->getDefinition();

		parent::__construct($strName, $objDataContainer, $arrDefinition['propertys'][$strName]);

		$this->objParent = $objParent === null ? $objDataContainer : $objParent;
	}


	/**
	 * Return the label of the property.
	 *
	 * @return array
	 */
	public function getLabel()
	{
		return $this->definition['label'];
	}


	/**
	 * Set label
	 *
	 * @param array $arrLabel
	 */
	public function setLabel(array $arrLabel)
	{
		$this->set('label', $arrLabel);
	}


	/**
	 * @param array $arrLabel
	 */
	public function setLabelByRef(array &$arrLabel)
	{
		$this->definition['label'] =& $arrLabel;
	}


	/**
	 * Retrieve information about a property.
	 *
	 * @return PropertyInterface
	 */
	public function getWidgetType()
	{
		return $this->get('inputType');
	}


	/**
	 * @param $strType
	 */
	public function setWidgetType($strType)
	{
		$this->definition['inputType'] = $strType;
	}


	/**
	 * Fetch the evaluation information from the property.
	 *
	 * @return array
	 */
	public function getEvaluation()
	{
		return $this->get('eval');
	}


	/**
	 * @param $strKey
	 * @param $value
	 */
	public function setEvaluationAttribute($strKey, $value)
	{
		$this->definition['eval'][$strKey] = $value;
	}


	/**
	 * @param $strKey
	 * @return null
	 */
	public function getEvaluationAttribute($strKey)
	{
		if(isset($this->definition['eval'][$strKey]))
		{
			return $this->definition['eval'][$strKey];
		}

		return null;
	}


	/**
	 * Determinator if search is enabled on this property.
	 *
	 * @return bool
	 */
	public function isSearchable()
	{
		return (bool) $this->get('search');
	}


	/**
	 * @param $blnValue
	 */
	public function setSearchable($blnValue)
	{
		$this->set('search', $blnValue);
	}


	/**
	 * Determinator if filtering may be performed on this property.
	 *
	 * @return bool
	 */
	public function isFilterable()
	{
		return (bool) $this->get('filter');
	}


	/**
	 * @param $blnValue
	 */
	public function setFilterable($blnValue)
	{
		$this->set('filter', $blnValue);
	}


	/**
	 * Determinator if sorting may be performed on this property.
	 *
	 * @return bool
	 */
	public function isSortable()
	{
		return (bool) $this->get('sorting');
	}


	/**
	 * @param $blnValue
	 */
	public function setSortable($blnValue)
	{
		$this->set('filter', $blnValue);
	}

	/**
	 * Fetch some arbitrary information.
	 *
	 * @param $strKey
	 *
	 * @return mixed
	 */
	public function get($strKey)
	{
		if(isset($this->definition[$strKey]))
		{
			return $this->definition[$strKey];
		}

		return null;
	}


	/**
	 * @param $strKey
	 * @param $value
	 */
	public function set($strKey, $value)
	{
		$this->definition[$strKey] = $value;
	}


	/**
	 * @return PropertyContainer
	 */
	public function getParent()
	{
		return $this->objParent;
	}


	/**
	 * @param PropertyContainer $objParent
	 */
	public function setParent(PropertyContainer $objParent)
	{
		$this->objParent = $objParent;
	}


	/**
	 * Test if property is an selector or update value
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

		return $this->getDataContainer()->hasSelector($this->getName());
	}


	/**
	 * @return bool
	 */
	public function hasActiveSubPalette()
	{
		$objModel = $this->getDataContainer()->getModel();

		if($objModel === null)
		{
			return false;
		}

		if($objModel->getProperty($this->getName()) == 1)
		{
			return $this->getDataContainer()->hasSubPalette($this->getName());
		}

		return $this->getDataContainer()->hasSubPalette($this->strName . '_' . $objModel->getProperty($this->getName()));
	}


	/**
	 * Get activated sub palette. Method does not check if element is an selector and if SubPalette exists.
	 * Methods has to be called before
	 *
	 * @return SubPalette|null
	 */
	public function getActiveSubPalette()
	{
		$objModel = $this->getDataContainer()->getModel();

		if($objModel === null)
		{
			return null;
		}

		$varValue = $objModel->getProperty($this->strName);

		if($varValue == 1)
		{
			if($this->getDataContainer()->hasSubPalette($this->strName))
			{
				return $this->getDataContainer()->getSubPalette($this->strName);
			}
		}
		else
		{
			return $this->getDataContainer()->getSubPalette($this->strName . '_' . $objModel->getProperty($this->strName));
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
	 * Append property to legend or SubPalette
	 *
	 * @param PropertyContainer $objContainer
	 * @param null $reference
	 * @param null $intPosition
	 *
	 * @return $this
	 */
	public function appendTo(PropertyContainer $objContainer, $reference=null, $intPosition=Property::POS_LAST)
	{
		if($this->getParent() != $objContainer)
		{
			$this->getParent()->removeProperty($this);
		}
		else {
			$this->objParent = $objContainer;
		}

		$this->getParent()->moveProperty($this, $reference, $intPosition);

		return $this;
	}


	/**
	 * Move Palette to new place
	 *
	 * @param string|PropertyContainer $reference
	 *
	 * @return $this
	 */
	public function appendAfter($reference)
	{
		$this->getParent()->moveProperty($this, $reference, static::POS_AFTER);

		return $this;
	}


	/**
	 * Move Palette to new place
	 *
	 * @param string|PropertyContainer $reference
	 *
	 * @return $this
	 */
	public function appendBefore($reference)
	{
		$this->getParent()->moveProperty($this, $reference, static::POS_BEFORE);

		return $this;
	}


	/**
	 * @param bool $blnRemoveFromDataContainer
	 *
	 * @return $this
	 */
	public function remove($blnRemoveFromDataContainer=false)
	{
		$this->objParent->removeProperty($this, $blnRemoveFromDataContainer);

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
			$objNode = $this->getDataContainer()->getProperty($objNode);
		}
		elseif(!$objNode instanceof Property)
		{
			throw new \RuntimeException("Node '{$objNode}' is not a property");
		}

		$this->definition = $objNode->getDefinition();
		$this->dispatch('change');

		return $this;
	}


	/**
	 * Export as string
	 * @return mixed
	 */
	public function asString()
	{
		return $this->getName();
	}


	/**
	 * Export to array wil return string because propertys are handled as string in meta palettes
	 *
	 * @return mixed
	 */
	public function asArray()
	{
		return $this->getName();
	}

}