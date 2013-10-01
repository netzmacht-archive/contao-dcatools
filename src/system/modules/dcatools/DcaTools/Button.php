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

use Netzmacht\DcaTools\DataContainer;
use Netzmacht\DcaTools\Node\Child;
use Symfony\Component\EventDispatcher\EventDispatcher;


/**
 * Class Button provides a generic class which allows to define multiple events being triggered when a button component
 * of Contao DCA is generated. The button will be loaded using DcaTools::buttonCallback
 *
 * Following events are supported:
 *  - initialize:   called in the constructor
 *  - validate:     Can set the button as disabled or hidden.
 *  - generate:     Called when button is generated, use this for influencing the output.
 *
 * @package Netzmacht\DcaTools\Button
 */
class Button extends Child
{

	/**
	 * Button Template
	 * @var string
	 */
	protected $strTemplate = 'be_button';


	/**
	 * Rendered button
	 *
	 * @var string
	 */
	protected $strBuffer;


	/**
	 * @var string
	 */
	protected $strScope;


	/**
	 * Constructor
	 * @param $strName
	 * @param DataContainer $strScope
	 * @param DataContainer $objDataContainer
	 */
	public function __construct($strName, $strScope, DataContainer $objDataContainer)
	{
		$definition =& $objDataContainer->getDefinition();
		$strConfig = $strScope == 'global' ? 'global_operations' : 'operations';

		parent::__construct($strName, $objDataContainer, $definition['list'][$strConfig][$strName]);

		// load events from definition
		if(isset($this->definition['events']) && is_array($this->definition['events']))
		{
			foreach($this->definition['events'] as $strEvent => $arrListeners)
			{
				foreach($arrListeners as $listener)
				{
					if (is_array($listener) && count($listener) === 2 && is_int($listener[1])) {
						list($listener, $priority) = $listener;
					}
					else {
						$priority = 0;
					}

					$this->addListener($strEvent, $listener, $priority);
				}
			}
		}

		$this->strScope = $strScope;
		$this->dispatch('initialize');
	}


	/**
	 * Magic Contao style
	 *
	 * @param $strKey
	 * @param $value
	 */
	public function __set($strKey, $value)
	{
		$this->definition[$strKey] = $value;
	}


	/**
	 * Magic Contao style
	 *
	 * @param $strKey
	 *
	 * @return mixed
	 */
	public function __get($strKey)
	{
		if(isset($this->definition[$strKey]))
		{
			return $this->definition[$strKey];
		}
	}


	/**
	 * Validate if button can be shown
	 *
	 * @return bool
	 */
	public function validate()
	{
		$this->dispatch('validate');

		return !$this->hidden;
	}


	/**
	 * @return string
	 */
	public function getTemplateName()
	{
		return $this->strTemplate;
	}


	/**
	 * Change used template
	 *
	 * @param string $strName
	 * @event template
	 */
	public function setTemplateName($strName)
	{
		$this->strTemplate = $strName;
	}


	/**
	 * @param $strBuffer
	 */
	public function setBuffer($strBuffer)
	{
		$this->strBuffer = $strBuffer;
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
		$this->strScoppe = $strScope;
	}


	/**
	 * @return string
	 */
	public function generate($arrRow, $href, $label, $title, $icon, $attributes, $strTable)
	{
		$this->row = $arrRow;
		$this->href = $href;
		$this->label = $label;
		$this->title = $title;
		$this->icon = $icon;
		$this->attributes = $attributes;
		$this->table = $strTable;

		if(!$this->validate())
		{
			return '';
		}

		// default generate event
		$this->dispatch('generate');

		// used for contao native callback
		$this->dispatch('native');

		// everything which shall be called after contao native callback
		$this->dispatch('render');

		// This is for Contao style button callbacks which can be wrapped in an event
		if($this->strBuffer != '')
		{
			return $this->strBuffer;
		}

		$objTemplate = new \BackendTemplate($this->strTemplate);
		$objTemplate->setData($this->definition);

		return $objTemplate->parse();
	}


	/**
	 * Remove child from parent
	 * @return mixed
	 */
	public function remove()
	{
		$this->getDataContainer()->removeButton($this);

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function toString()
	{
		return '';
	}


	/**
	 * @return mixed
	 */
	public function toArray()
	{
		return $this->definition;
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param Button
	 *
	 * @return $this
	 */
	public function extend($node)
	{
		$definition = $node->getDefinition();
		$this->definition = $definition;

		return $this;
	}


}