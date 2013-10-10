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

use DcGeneral\DataDefinition\PropertyInterface;
use Netzmacht\DcaTools\Node\Child;
use Netzmacht\DcaTools\Node\PropertyContainer;
use Netzmacht\DcaTools\Node\Node;
use Netzmacht\DcaTools\Palette\SubPalette;

/**
 * Class Property
 * @package Netzmacht\DcaTools\Palette
 */
class Property extends Child implements \ArrayAccess, PropertyInterface
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
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->definition);
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 */
	public function &offsetGet($offset)
	{
		return $this->definition[$offset];
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->definition[$offset] = $value;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->definition[$offset]);
	}


	/**
	 * Return the name of the property.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->definition->getName();
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


	/**
	 * Update definition
	 */
	public function updateDefinition()
	{
		// TODO not supported so far, will update the global property settings in a future version
	}


}