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
use DcGeneral\DataDefinition\PropertyInterface;

/**
 * Class Property
 * @package DcaTools\Palette
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
		$arrDefinition =& $objDataContainer->getDefinition();

		parent::__construct($strName, $objDataContainer, $arrDefinition['fields'][$strName]);

		$this->objParent = $objParent === null ? $objDataContainer : $objParent;
	}


	/**
	 * Prepare for cloning
	 */
	public function __clone()
	{
		unset($this->objParent);
	}


	/**
	 * Copy Propety to a new one
	 *
	 * @param string $strName new name
	 *
	 * @return static
	 */
	public function copy($strName)
	{
		$strOld = $this->getName();

		$this->strName = $strName;
		$objCopy = clone $this;

		$arrDefinition =& $this->objDataContainer->getDefinition();
		$arrDefinition['fields'][$strName] = $this->definition;

		$this->strName = $strOld;

		return $objCopy;
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
	 *
	 * @return $this;
	 */
	public function setLabel(array $arrLabel)
	{
		$this->set('label', $arrLabel);

		return $this;
	}


	/**
	 * @param array $arrLabel
	 *
	 * @return $this
	 */
	public function setLabelByRef(array &$arrLabel)
	{
		$this->definition['label'] =& $arrLabel;

		return $this;
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
	 *
	 * @return $this
	 */
	public function setWidgetType($strType)
	{
		$this->definition['inputType'] = $strType;

		return $this;
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
	 *
	 * @return $this
	 */
	public function setEvaluationAttribute($strKey, $value)
	{
		$this->definition['eval'][$strKey] = $value;

		return $this;
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
	 *
	 * @return $this
	 */
	public function setSearchable($blnValue)
	{
		$this->set('search', (bool) $blnValue);

		return $this;
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
	 *
	 * @return $this
	 */
	public function setFilterable($blnValue)
	{
		$this->set('filter', (bool) $blnValue);

		return $this;
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
	 *
	 * @return $this
	 */
	public function setSortable($blnValue)
	{
		$this->set('sorting', $blnValue);

		return $this;
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
	 *
	 * @return $this
	 */
	public function set($strKey, $value)
	{
		$this->definition[$strKey] = $value;

		return $this;
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
	 *
	 * @return $this;
	 */
	public function setParent(PropertyContainer $objParent)
	{
		$this->objParent = $objParent;

		return $this;
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
		if($blnSelector !== null)
		{
			if($blnSelector)
			{
				$this->getDataContainer()->addSelector($this);
			}
			else {
				$this->getDataContainer()->removeSelector($this);
			}

		}

		return $this->getDataContainer()->hasSelector($this->getName());
	}


	/**
	 * get a defined subpalette
	 *
	 * @param null $mixedValue
	 * @return SubPalette
	 * @throws \RuntimeException
	 */
	public function getSubPalette($mixedValue=null)
	{
		$strName = $this->strName;

		if($mixedValue === null || $mixedValue === '1' || $mixedValue === true)
		{
			return $this->getDataContainer()->getSubPalette($strName);
		}

		$strName .= '_' . $mixedValue;
		return $this->getDataContainer()->getSubPalette($strName);
	}


	/**
	 * get a defined subpalette
	 *
	 * @param null $mixedValue
	 * @return SubPalette
	 * @throws \RuntimeException
	 */
	public function hasSubPalette($mixedValue=null)
	{
		if(!$this->isSelector())
		{
			return false;
		}

		if($mixedValue === null || $mixedValue === '1' || $mixedValue === true)
		{
			return $this->getDataContainer()->hasSubPalette($this->strName);
		}

		return $this->getDataContainer()->hasSubPalette($this->strName . '_' . $mixedValue);
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
	public function appendTo(PropertyContainer $objContainer, $reference=null, $intPosition=Definition::LAST)
	{
		if($this->getParent() != $objContainer)
		{
			$this->getParent()->removeProperty($this);
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
		$this->getParent()->moveProperty($this, $reference, Definition::AFTER);

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
		$this->getParent()->moveProperty($this, $reference, Definition::BEFORE);

		return $this;
	}


	/**
	 * Remove property from parent and datacontainer if set too true
	 *
	 * You should not use the object anymore after removing it!
	 *
	 * @param bool $blnRemoveFromDataContainer
	 *
	 * @return $this
	 */
	public function remove($blnRemoveFromDataContainer=false)
	{
		$this->objParent->removeProperty($this, $blnRemoveFromDataContainer);

		if($this->objParent == $this->objDataContainer)
		{
			unset($this->objDataContainer);
		}

		unset($this->objParent);

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
		return $this->getDefinition();
	}


	/**
	 * Prepare argument so that an array of name and the object is passed
	 *
	 * @param PropertyContainerInterface $objReference
	 * @param Node|string $node
	 * @param bool $blnNull return null if property does not exists
	 *
	 * @return array[string|Property|null]
	 */
	public static function argument(PropertyContainerInterface $objReference, $node, $blnNull=true)
	{
		return Node::prepareArgument($objReference, $node, $blnNull, 'Property');
	}


	/**
	 *  Update the definition of current element
	 *
	 * @param bool $blnPropagation
	 * @return $this
	 */
	public function updateDefinition($blnPropagation = true)
	{
		if($blnPropagation)
		{
			$this->getParent()->updateDefinition();
			$this->getDataContainer()->updateDefinition();
		}

		return $this;
	}


}