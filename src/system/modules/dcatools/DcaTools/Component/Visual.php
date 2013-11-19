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

namespace DcaTools\Component;

use DcaTools\Event\GenerateEvent;

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
	 * @var
	 */
	protected $eventName;


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
	 * Compile visual component
	 *
	 * @param GenerateEvent $objEvent
	 */
	abstract protected function compile(GenerateEvent $objEvent);


	/**
	 * @param GenerateEvent $objEvent
	 * @return string
	 */
	public function generate(GenerateEvent $objEvent=null)
	{
		if($this->isHidden())
		{
			return '';
		}

		if($objEvent === null)
		{
			$objEvent = new GenerateEvent($this);
		}

		$this->objDispatcher->dispatch($this->eventName, $objEvent);

		// check if rendering is denied
		if($this->isHidden())
		{
			return '';
		}

		// check for generated component
		if($objEvent->hasOutput())
		{
			return $objEvent->getOutput();
		}

		$this->compile($objEvent);

		ob_start();
		include \Controller::getTemplate($this->strTemplate);
		$strBuffer = ob_get_contents();
		ob_end_clean();

		return $strBuffer;
	}
}