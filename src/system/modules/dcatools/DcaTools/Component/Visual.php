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

namespace Netzmacht\DcaTools\Component;

use Netzmacht\DcaTools\Definition\Node;
use Symfony\Component\EventDispatcher\GenericEvent;

abstract class Visual extends Component
{

	/**
	 * Template name
	 * @var string
	 */
	protected $strTemplate;


	/**
	 * Template Format
	 *
	 * @var string
	 */
	protected $strFormat = 'html5';


	/**
	 * @var bool
	 */
	protected $blnHidden;


	/**
	 * @param Node $objDefinition
	 */
	public function __construct(Node $objDefinition)
	{
		parent::__construct($objDefinition);

		$arrEvents = $objDefinition->get('events');

		if(is_array($arrEvents))
		{
			foreach($arrEvents as $strEvent => $arrListeners)
			{
				$this->addListeners($strEvent, $arrListeners);
			}
		}
	}


	/**
	 * Get Template name
	 *
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
	 * @return mixed
	 */
	abstract protected function compile(GenericEvent $objEvent);


	/**
	 * @return string
	 */
	public function generate()
	{
		if($this->isHidden())
		{
			return '';
		}

		$objEvent = new GenericEvent($this);
		$objEvent->setArgument('render', true);

		$objEvent = $this->dispatch('generate', $objEvent);

		// check if rendering is denied
		if(!$objEvent->getArgument('render'))
		{
			return '';
		}

		// check for generated component
		if($objEvent->hasArgument('buffer') && $objEvent->getArgument('buffer') != '')
		{
			return $objEvent->getArgument('buffer');
		}

		$this->compile($objEvent);

		ob_start();
		include \Controller::getTemplate($this->strTemplate);
		$strBuffer = ob_get_contents();
		ob_end_clean();

		return $strBuffer;
	}
}