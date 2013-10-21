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

namespace DcaTools;

use DcaTools\Controller;
use DcaTools\Component\Operation;
use DcaTools\Event\Contao;
use DcaTools\Event\Event;
use DcaTools\Event\Permission;
use DcaTools\Event\Priority;
use DcaTools\Model\ContaoModel;
use DcGeneral\Data\DefaultModel;


/**
 * Class Bridge connects DcaTools to the default Contao callbacks
 *
 * @package DcaTools
 */
class Bridge
{

	/**
	 * Use magic stuff for generating operations
	 *
	 * @param $strMethod
	 * @param $arrArguments
	 *
	 * @return mixed
	 */
	public function __call($strMethod, $arrArguments)
	{
		if (strncmp($strMethod, 'operationCallback', 17) === 0)
		{
			$strOperation = substr($strMethod, 17);
			$objOperation = new Operation($arrArguments[6], $strOperation, 'local');

			$objOperation->setModel(array_shift($arrArguments));
		}
		elseif (strncmp($strMethod, 'globalOperationCallback', 23) === 0)
		{
			$strOperation = substr($strMethod, 23);
			$objOperation =  new Operation($arrArguments[5], $strOperation, 'global');
		}

		if(isset($objOperation))
		{
			$objOperation->setHref($arrArguments[0]);
			$objOperation->setLabel($arrArguments[1]);
			$objOperation->setTitle($arrArguments[2]);
			$objOperation->setIcon($arrArguments[3]);
			$objOperation->setAttributes($arrArguments[4]);

			$objEvent = new Permission($objOperation);
			$objEvent['table'] = $arrArguments[5];
			$objEvent['rootIds'] = $arrArguments[6];
			$objEvent['childRecordIds'] = $arrArguments[7];
			$objEvent['circularReference'] = $arrArguments[8];
			$objEvent['previous'] = $arrArguments[9];
			$objEvent['next'] = $arrArguments[10];

			return $objOperation->generate($objEvent);
		}

		return null;
	}


	/**
	 * Hook will dynamically assign an onload callback
	 *
	 * @param $strName
	 */
	public function hookLoadDataContainer($strName)
	{
		array_insert($GLOBALS['TL_DCA'][$strName]['config']['onload_callback'], 0, array(
			array('DcaTools\Bridge', 'callbackInitializeDataContainer')
		));
	}


	/**
	 * Initialize the DataContainer
	 *
	 * @param $dc
	 */
	public function callbackInitializeDataContainer($dc)
	{
		if(isset($GLOBALS['TL_DCA'][$dc->table]['dcatools']))
		{
			$arrConfig =& $GLOBALS['TL_DCA'][$dc->table]['dcatools'];

			$objController = Controller::getInstance($dc->table);

			// initialize and check permissions
			if(isset($arrConfig['events']))
			{
				$objController->initialize();

				$this->registerGenerateChildRecord($objController);
				$this->registerPasteOperation($objController);
			}

			$this->registerOperationEvents($dc->table, 'operations');
			$this->registerOperationEvents($dc->table, 'global_operations');
		}
	}


	/**
	 * Callback for child_record_callback
	 *
	 * @param $arrRow
	 *
	 * @return mixed
	 */
	public function callbackChildRecord($arrRow)
	{
		$objController = Controller::getInstance(\Input::get('table'));

		$objModel = new DefaultModel();
		$objModel->setPropertiesAsArray($arrRow);
		$objModel->setID($arrRow['id']);

		$objEvent = new Event($objController);
		$objEvent->setModel($objModel);

		$objEvent = $objController->dispatch('generateChildRecord', $objEvent);

		return $objEvent->getOutput();
	}


	/**
	 * Callback for button_callback
	 *
	 * @param $arrRow
	 *
	 * @return mixed
	 */
	public function callbackOperation($arrRow=null)
	{
		$objController = Controller::getInstance(\Input::get('table'));

		$objEvent = new Event($objController);

		if($arrRow !== null)
		{
			$objEvent->setModel(new ContaoModel($arrRow));
		}

		$objEvent = $objController->dispatch('generateChildRecord', $objEvent);

		return $objEvent->getOutput();
	}


	/**
	 * @param $objDc
	 * @param $arrRow
	 * @param $strTable
	 * @param $blnCircularReference
	 * @param $arrClipboard
	 * @param $arrChildren
	 * @param $intPrevious
	 * @param $intNext
	 *
	 * @return string
	 */
	public function callbackPasteOperation($objDc, $arrRow, $strTable, $blnCircularReference, $arrClipboard, $arrChildren, $intPrevious, $intNext)
	{
		$objModel = new DefaultModel();
		$objModel->setPropertiesAsArray($arrRow);
		$objModel->setID($arrRow['id']);

		$objOperation = new Operation($objDc->table, 'paste-after');
		$objOperation->setModel($objModel);

		$objEvent = new Event($objOperation);
		$objEvent->setArgument('circularReference', $blnCircularReference);
		$objEvent->setArgument('table', $strTable);
		$objEvent->setArgument('clipboard', $arrClipboard);
		$objEvent->setArgument('children', $arrChildren);
		$objEvent->setArgument('previous', $intPrevious);
		$objEvent->setArgument('next', $intNext);

		// TODO implement this
		//$strBuffer = $objOperation->generate($objEvent);
	}


	/**
	 * Register button_callback if events are set
	 *
	 * @param string $strTable
	 * @param string $strKey
	 */
	protected function registerOperationEvents($strTable, $strKey)
	{
		$strCallback = ($strKey == 'operations' ? 'operationCallback' : 'globalOperationCallback');

		if(!isset($GLOBALS['TL_DCA'][$strTable]['dcatools'][$strKey]))
		{
			return;
		}

		foreach($GLOBALS['TL_DCA'][$strTable]['dcatools'][$strKey] as $strOperation => $arrListeners)
		{
			// operation does not exists only event listeners
			if(!isset($GLOBALS['TL_DCA'][$strTable]['list'][$strKey][$strOperation]))
			{
				continue;
			}

			$arrOperation = $GLOBALS['TL_DCA'][$strTable]['list'][$strKey][$strOperation];

			if(isset($arrOperation['button_callback']))
			{
				$GLOBALS['TL_DCA'][$strTable]['dcatools'][$strKey][$strOperation][] = array
				(
					function($objEvent) use($arrOperation)
					{
						Contao::generateOperation($objEvent, $arrOperation['button_callback']);
					},
					Priority::CALLBACK
				);
			}

			$GLOBALS['TL_DCA'][$strTable]['list'][$strKey][$strOperation]['button_callback'] = array
			(
				'DcaTools\Bridge', $strCallback . $strOperation
			);
		}
	}


	/**
	 * Register an generate child record if any events are set
	 *
	 * @param Controller $objController
	 */
	protected function registerGenerateChildRecord(Controller $objController)
	{
		$strTable  = $objController->getName();
		$arrConfig = $objController->getDefinition()->getFromDefinition('dcatools/events');

		if(isset($arrConfig['generateChildRecord']))
		{
			if(isset($GLOBALS['TL_DCA'][$strTable]['list']['sorting']['child_record_callback']))
			{
				$arrCallback = $GLOBALS['TL_DCA'][$strTable]['list']['sorting']['child_record_callback'];

				$objController->addListener('generateChildRecord', function(Event $objEvent) use($arrCallback)
					{
						Contao::generateChildRecord($objEvent, $arrCallback);
					},
					Priority::CALLBACK
				);
			}

			$GLOBALS['TL_DCA'][$strTable]['list']['sorting']['child_record_callback'] = array(
				'DcaTools\Bridge', 'callbackChildRecord'
			);
		}
	}


	/**
	 * @param Controller $objController
	 */
	protected function registerPasteOperation(Controller $objController)
	{
		$strTable = $objController->getName();
		$arrConfig = $objController->getDefinition()->getFromDefinition('dcatools/events');

		if(isset($arrConfig['pasteOperation']))
		{
			if(isset($GLOBALS['TL_DCA'][$strTable]['list']['sorting']['paste_button_callback']))
			{
				$arrCallback = $GLOBALS['TL_DCA'][$strTable]['list']['sorting']['paste_button_callback'];

				$objController->addListener('pasteOperation', function(Event $objEvent) use($arrCallback)
					{
						Contao::generateOperation($objEvent, $arrCallback);
					},
					Priority::CALLBACK
				);
			}

			$GLOBALS['TL_DCA'][$strTable]['list']['sorting']['paste_button_callback'] = array(
				'DcaTools\Bridge', 'callbackPasteOperation'
			);
		}
	}

}