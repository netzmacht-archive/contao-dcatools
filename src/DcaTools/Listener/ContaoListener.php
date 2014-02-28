<?php

/**
 * DcaTools - Toolkit for data containers in Contao
 * Copyright (C) 2013 David Molineus
 *
 * @package   netzmacht-dcatools
 * @author    David Molineus <molineus@netzmacht.de>
 * @license   LGPL-3.0+
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace DcaTools\Listener;

use DcaTools\Definition;
use DcaTools\Event\GenerateEvent;


class ContaoListener
{

	/**
	 * @param GenerateEvent $objEvent
	 * @param array $arrCallback
	 */
	public static function generateOperation(GenerateEvent $objEvent, array $arrCallback)
	{
		$objCallback = new $arrCallback[0]();

		/** @var \DcaTools\Component\Operation\View $objView */
		$objView    = $objEvent->getView();

		// $arrRow, $v['href'], $label, $title, $v['icon'], $attributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext
		$strBuffer = $objCallback->{$arrCallback[1]}
		(
			$objEvent->getModel() ? $objEvent->getModel()->getPropertiesAsArray() : array(),
			$objView->getHref(),
			$objView->getLabel(),
			$objView->getTitle(),
			$objView->getIcon(),
			$objView->getAttributes(),
			$objEvent->getConfigAttribute('table', $objEvent->getModel() ? $objEvent->getModel()->getProviderName() : null),
			$objEvent->getConfigAttribute('rootIds', array()),
			$objEvent->getConfigAttribute('childRecordIds', array()),
			$objEvent->getConfigAttribute('circularReference', false),
			$objEvent->getConfigAttribute('previous'),
			$objEvent->getConfigAttribute('next')
		);

		if($strBuffer == '') {
			$objView->setVisible(false);
		}
		else {
			$objEvent->setOutput($strBuffer);
		}
	}

}