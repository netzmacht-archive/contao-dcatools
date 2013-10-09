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

namespace Netzmacht\DcaTools\Event;

use Symfony\Component\EventDispatcher\GenericEvent;


/**
 * Class ContaoCallback
 * @package Netzmacht\DcaTools\Button
 */
class OperationCallback
{

	/**
	 * @var array
	 */
	protected $arrCallback;


	/**
	 * @param Config $objConfig
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

		$objOperation = $objEvent->getSubject();

		/** @var \Netzmacht\DcaTools\DataContainer $objDataContainer */
		$objDataContainer = $objOperation->getDataContainer();

		$objOperation->setBuffer
		(
			$objCallback->{$this->arrCallback[1]}
			(
				$objDataContainer->hasRecord() ? $objDataContainer->getRecord()->row() : array(),
				$objOperation->getHref(),
				$objOperation->getLabel(),
				$objOperation->getTitle(),
				$objOperation->getIcon(),
				$objOperation->getAttributes(),
				$objOperation->getDataContainer()->getName(),
				null,
				null,
				false,
				null,
				null,
				null
			)
		);
	}

}