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

use DcGeneral\DataDefinition\OperationInterface;
use DcaTools\Definition;


/**
 * Class Operation provides a generic class which allows to define multiple events being triggered when a button component
 * of Contao DCA is generated. The button will be loaded using Definition::buttonCallback
 *
 * Following events are supported:
 *  - initialize:   called in the constructor
 *  - validate:     Can set the button as disabled or hidden.
 *  - generate:     Called when button is generated, use this for influencing the output.
 *
 * @package DcaTools\Operation
 */
class Operation extends Node implements OperationInterface
{
	/**
	 * @var string
	 */
	protected $strScope;


	/**
	 * Constructor
	 * @param string $strName
	 * @param string $strScope
	 * @param DataContainer $objDataContainer
	 */
	public function __construct($strName, $strScope, DataContainer $objDataContainer)
	{
		$definition =& $objDataContainer->getDefinition();
		$strConfig = $strScope == 'global' ? 'global_operations' : 'operations';

		parent::__construct($strName, $objDataContainer, $definition['list'][$strConfig][$strName]);

		$this->strScope = $strScope;
	}


	/**
	 * @param string $strAttributes
	 */
	public function setAttributes($strAttributes)
	{
		$this->set('attributes', $strAttributes);
	}


	/**
	 * @return string
	 */
	public function getAttributes()
	{
		return $this->get('attributes');
	}


	/**
	 * @param string $strHref
	 */
	public function setHref($strHref)
	{
		$this->set('href', $strHref);
	}


	/**
	 * @return string
	 */
	public function getHref()
	{
		return $this->get('href');
	}


	/**
	 * @param string $strIcon
	 */
	public function setIcon($strIcon)
	{
		$this->set('icon', $strIcon);
	}


	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->get('icon');
	}


	/**
	 * @param array $arrLabel
	 */
	public function setLabel(array $arrLabel)
	{
		$this->set('label', $arrLabel);
	}


	/**
	 * @param bool $blnRaw
	 *
	 * @return array
	 */
	public function getLabel($blnRaw=true)
	{
		return $this->get('label');
	}


	/**
	 * Return the callback to use.
	 *
	 * @return array
	 */
	public function getCallback()
	{
		return $this->get('button_callback');
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
	 * Set information
	 *
	 * @param $strKey
	 * @param $value
	 */
	public function set($strKey, $value)
	{
		$this->definition[$strKey] = $value;
	}


	/**
	 * @return string
	 */
	public function getScope()
	{
		return $this->strScope;
	}


	/**
	 * @param $strScope
	 */
	public function setScope($strScope)
	{
		$this->strScope = $strScope;
	}


	/**
	 * Remove child from parent
	 *
	 * @return $this
	 */
	public function remove()
	{
		$this->getDataContainer()->removeOperation($this);

		return $this;
	}


	/**
	 *
	 * @return mixed
	 */
	public function asString()
	{
		return $this->getName();
	}


	/**
	 * @return mixed
	 */
	public function asArray()
	{
		return $this->definition;
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param Operation $node
	 *
	 * @return $this
	 */
	public function extend($node)
	{
		$definition = $node->getDefinition();
		$this->definition = $definition;

		return $this;
	}


	/**
	 * Prepare argument so that an array of name and the object is passed
	 *
	 * @param DataContainer $objReference
	 * @param Legend|string $node
	 * @param bool $blnNull return null if property does not exists
	 *
	 * @return array[string|Legend|null]
	 */
	public static function argument(DataContainer $objReference, $node, $blnNull=true)
	{
		return static::prepareArgument($objReference, $node, $blnNull, 'Operation');
	}


	/**
	 * @param bool $blnPropagation
	 * @return $this|void
	 */
	public function updateDefinition($blnPropagation=true)
	{
		return $this;
	}


}