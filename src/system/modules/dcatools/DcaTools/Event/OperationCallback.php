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

namespace DcaTools\Event;

use Symfony\Component\EventDispatcher\GenericEvent;


/**
 * Class ContaoCallback
 * @package DcaTools\Button
 */
class OperationCallback
{

	/**
	 * @var array
	 */
	protected $arrCallback;


	/**
	 * @param array $arrCallback
	 */
	public function __construct(array $arrCallback)
	{
		$this->arrCallback = $arrCallback;
	}


	/**
	 * @param GenericEvent $objEvent
	 *
	 * @return mixed|void
	 */
	public function execute(GenericEvent $objEvent)
	{
		$objCallback = new $this->arrCallback[0]();

		/** @var DcaTools\Component\Operation $objOperation */
		$objOperation = $objEvent->getSubject();

		/** @var DcaTools\Definition\DataContainer $objDataContainer */
		$objDataContainer = $objOperation->getDefinition()->getDataContainer();

		$strBuffer = $objCallback->{$this->arrCallback[1]}
		(
			$objOperation->hasModel() ? $objOperation->getModel()->getPropertiesAsArray() : array(),
			$objOperation->getHref(),
			$objOperation->getLabel(),
			$objOperation->getTitle(),
			$objOperation->getIcon(),
			$objOperation->getAttributes(),
			$objDataContainer->getName(),
			null,
			null,
			false,
			null,
			null,
			null
		);

		if($strBuffer == '')
		{
			$objOperation->hide();
		}
		else {
			$objEvent->setArgument('buffer', $strBuffer);
		}
	}

}