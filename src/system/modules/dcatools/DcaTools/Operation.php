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
 * Class Operation provides a generic class which allows to define multiple events being triggered when a button component
 * of Contao DCA is generated. The button will be loaded using DcaTools::buttonCallback
 *
 * Following events are supported:
 *  - initialize:   called in the constructor
 *  - validate:     Can set the button as disabled or hidden.
 *  - generate:     Called when button is generated, use this for influencing the output.
 *
 * @package Netzmacht\DcaTools\Operation
 */
class Operation extends Child
{

	/**
	 * Operation Template
	 * @var string
	 */
	protected $strTemplate = 'be_operation';


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
	 * @var string
	 */
	protected $strAttributes;


	/**
	 * @var string
	 */
	protected $strLabel;


	/**
	 * @var string
	 */
	protected $strTitle;


	/**
	 * @var string
	 */
	protected $strHref;


	/**
	 * @var string
	 */
	protected $strIcon;


	/**
	 * @var bool
	 */
	protected $blnDisabled;


	/**
	 * @var bool
	 */
	protected $blnHidden;


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

		// load event listeners from definition
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

		// initiate button from definition
		$this->setLabel(isset($definition['label'][0]) ? $definition['label'][0] : $strName);
		$this->setTitle(isset($definition['label'][1]) ? $definition['label'][1] : $strName);
		$this->setIcon($definition['icon']);
		$this->setHref($definition['href']);
		$this->setAttributes($definition['attributes']);

		$this->strScope = $strScope;
		$this->dispatch('initialize');
	}


	/**
	 * @param string $strAttributes
	 */
	public function setAttributes($strAttributes)
	{
		$this->strAttributes = $strAttributes;
	}


	/**
	 * @return string
	 */
	public function getAttributes()
	{
		return $this->strAttributes;
	}


	/**
	 * @param string $strHref
	 */
	public function setHref($strHref)
	{
		$this->strHref = \Controller::addToUrl($strHref);
	}


	/**
	 * @return string
	 */
	public function getHref()
	{
		return $this->strHref;
	}


	/**
	 * @param string $strIcon
	 */
	public function setIcon($strIcon)
	{
		$this->strIcon = $strIcon;
	}


	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->strIcon;
	}


	/**
	 * @param string $strLabel
	 */
	public function setLabel($strLabel)
	{
		$this->strLabel = $strLabel;
	}


	/**
	 * @return string
	 */
	public function getLabel()
	{
		if($this->getDataContainer()->hasRecord())
		{
			return sprintf($this->strLabel, $this->getDataContainer()->getRecord()->id);
		}

		return $this->strLabel;
	}


	/**
	 * @param string $strTitle
	 */
	public function setTitle($strTitle)
	{
		$this->strTitle = $strTitle;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		if($this->getDataContainer()->hasRecord())
		{
			return sprintf($this->strTitle, $this->getDataContainer()->getRecord()->id);
		}

		return $this->strTitle;
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
		$this->strScope = $strScope;
	}


	/**
	 * Hide Operation
	 */
	public function hide()
	{
		$this->blnHidden = true;
	}


	/**
	 * @return bool
	 */
	public function isHidden()
	{
		return $this->blnHidden;
	}


	/**
	 * Disable operation. Operation will be displayed as disabled
	 */
	public function disable()
	{
		$this->blnDisabled = true;
	}



	/**
	 * @return bool
	 */
	public function isDisabled()
	{
		return $this->blnDisabled;
	}


	/**
	 *
	 * @param array $arrConfig
	 *
	 * @return string
	 */
	public function generate(array $arrConfig = array('table' => true, 'id' => true))
	{
		// default generate event
		$this->dispatch('generate');

		if($this->isHidden())
		{
			return '';
		}

		// This is for Contao style button callbacks which are wrapped in an event
		if($this->strBuffer != '')
		{
			return $this->strBuffer;
		}

		$objTemplate = new \BackendTemplate($this->strTemplate);
		$objTemplate->button = $this;

		return $objTemplate->parse();
	}


	/**
	 * Remove child from parent
	 * @return $this
	 */
	public function remove()
	{
		$this->getDataContainer()->removeOperation($this);

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function toString()
	{
		return $this->generate();
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


}