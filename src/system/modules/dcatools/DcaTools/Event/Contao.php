<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 21.10.13
 * Time: 07:43
 * To change this template use File | Settings | File Templates.
 */

namespace DcaTools\Event;


class Contao
{

	/**
	 * @param Event $objEvent
	 * @param array $arrCallback
	 */
	public static function generateOperation(Event $objEvent, array $arrCallback)
	{
		$objCallback = new $arrCallback[0]();

		/** @var \DcaTools\Component\Operation $objOperation */
		$objOperation = $objEvent->getSubject();

		/** @var \DcaTools\Definition\DataContainer $objDataContainer */
		$objDataContainer = $objOperation->getDefinition()->getDataContainer();

		// $arrRow, $v['href'], $label, $title, $v['icon'], $attributes, $strTable, $arrRootIds, $arrChildRecordIds, $blnCircularReference, $strPrevious, $strNext
		$strBuffer = $objCallback->{$arrCallback[1]}
		(
			$objOperation->hasModel() ? $objOperation->getModel()->getPropertiesAsArray() : array(),
			$objOperation->getHref(),
			$objOperation->getLabel(),
			$objOperation->getTitle(),
			$objOperation->getIcon(),
			$objOperation->getAttributes(),
			isset($objEvent['table']) ? $objEvent['table'] : $objDataContainer->getName(),
			isset($objEvent['rootIds']) ? $objEvent['rootIds'] : array(),
			isset($objEvent['childRecordIds']) ? $objEvent['childRecordIds'] : array(),
			isset($objEvent['circularReference']) ? $objEvent['circularReference'] : false,
			isset($objEvent['previous']) ? $objEvent['previous'] : null,
			isset($objEvent['next']) ? $objEvent['next'] : null
		);

		if($strBuffer == '')
		{
			$objOperation->hide();
		}
		else {
			$objEvent->setOutput($strBuffer);
		}
	}


	/**
	 * @param Event $objEvent
	 * @param array $arrCallback
	 */
	public static function generateChildRecord(Event $objEvent, array $arrCallback)
	{
		$arrRow = $objEvent->getModel()->getPropertiesAsArray();

		$strBuffer = \Controller::importStatic($arrCallback[0])->$arrCallback[1]($arrRow);
		$objEvent->setOutput($strBuffer);
	}

}