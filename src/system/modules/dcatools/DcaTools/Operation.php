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

use DcGeneral\DataDefinition\OperationInterface;
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
class Operation extends Child implements OperationInterface
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
					DcaTools::registerListener($this, $strEvent, $listener);
				}
			}
		}

		$this->strScope = $strScope;
		$this->dispatch('initialize');
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
	 * @param string $strTitle
	 */
	public function setTitle($strTitle)
	{
		$arrLabel = $this->get('label');
		$arrLabel[0] = $strTitle;

		$this->set('label', $arrLabel);
	}


	/**
	 * @param bool $blnRaw
	 *
	 * @return string
	 */
	public function getTitle($blnRaw=true)
	{
		if(!$blnRaw && $this->getDataContainer()->hasRecord())
		{
			return sprintf($this->strTitle, $this->getDataContainer()->getRecord()->id);
		}

		return $this->strTitle;
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
		// Trigger generate event
		/** @var \Symfony\Component\EventDispatcher\GenericEvent $objEvent */
		$objEvent = $this->dispatch('generate');

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
		$objTemplate->config = $objEvent->getArguments();
		$objTemplate->operation = $this;

		return $objTemplate->parse();
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
	 * @param array $arrConfig
	 *
	 * @return mixed
	 */
	public function asString(array $arrConfig = array('table' => true, 'id' => true))
	{
		return $this->generate($arrConfig);
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


}