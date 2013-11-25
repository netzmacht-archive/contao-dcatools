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
abstract class AbstractOperation extends Node implements OperationInterface
{

	/**
	 * @param string $strAttributes
	 *
	 * @return $this
	 */
	public function setAttributes($strAttributes)
	{
		$this->set('attributes', $strAttributes);

		return $this;
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
	 *
	 * @return $this
	 */
	public function setHref($strHref)
	{
		$this->set('href', $strHref);

		return $this;
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
	 *
	 * @return $this
	 */
	public function setIcon($strIcon)
	{
		$this->set('icon', $strIcon);

		return $this;
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
	 *
	 * @return $this
	 */
	public function setLabel(array $arrLabel=null)
	{
		$this->set('label', $arrLabel);

		return $this;
	}


	/**
	 * @param array $arrLabel
	 *
	 * @return $this
	 */
	public function setLabelByRef(array &$arrLabel=null)
	{
		$this->definition['label'] =& $arrLabel;

		return $this;
	}


	/**
	 * @return array
	 */
	public function getLabel()
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
	 * Register a callback (Contao 3.2 supports callable as well)
	 *
	 * @param array|callable $callback
	 */
	public function registerCallback($callback)
	{
		$this->definition['button_callback'] = $callback;
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
	 * @param bool $blnPropagation
	 * @return $this|void
	 */
	public function updateDefinition($blnPropagation=true)
	{
		return $this;
	}


}