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
use DcaTools\Event\Priority;
use DcaTools\Model\ContaoModel;


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
			$objOperation = Operation::getInstance($arrArguments[6], $strOperation, 'local');

			$objOperation->setModel(array_shift($arrArguments));
		}
		elseif (strncmp($strMethod, 'globalOperationCallback', 23) === 0)
		{
			$strOperation = substr($strMethod, 23);
			$objOperation =  Operation::getInstance($arrArguments[5], $strOperation, 'global');
		}

		if(isset($objOperation))
		{
			$objOperation->setHref($arrArguments[0]);
			$objOperation->setLabel($arrArguments[1]);
			$objOperation->setTitle($arrArguments[2]);
			$objOperation->setIcon($arrArguments[3]);
			$objOperation->setAttributes($arrArguments[4]);

			return $objOperation->generate();
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
	 * Callback for child_record_callback
	 *
	 * @param $arrRow
	 *
	 * @return mixed
	 */
	public function callbackChildRecord($arrRow)
	{
		$objController = Controller::getInstance(\Input::get('table'));

		$objEvent = new Event($objController);
		$objEvent->setModel(new ContaoModel($arrRow));

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
	 * Initialize the DataContainer
	 *
	 * @param $dc
	 */
	public function callbackInitializeDataContainer($dc)
	{
		if(isset($GLOBALS['TL_DCA'][$dc->table]['dcatools']))
		{
			$arrConfig =& $GLOBALS['TL_DCA'][$dc->table]['dcatools'];

			// initialize and check permissions
			if(isset($arrConfig['events']))
			{
				$this->registerGenerateChildRecord($dc->table);

				$objController = Controller::getInstance($dc->table);
				$objController->initialize();
			}

			$this->registerOperationEvents($dc->table, 'operations');
			$this->registerOperationEvents($dc->table, 'global_operations');
		}
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
				$GLOBALS['TL_DCA'][$strTable]['dcatools'][$strKey][] = array
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
	 * @param $strTable
	 */
	protected function registerGenerateChildRecord($strTable)
	{
		$arrConfig =& $GLOBALS['TL_DCA'][$strTable]['dcatools'];

		if(isset($arrConfig['events']['generateChildRecord']))
		{
			if(isset($GLOBALS['TL_DCA'][$strTable]['list']['sorting']['child_record_callback']))
			{
				$arrCallback = $GLOBALS['TL_DCA'][$strTable]['list']['sorting']['child_record_callback'];

				$arrConfig['events']['generateChildRecord'][] = array
				(
					function(Event $objEvent) use($arrCallback)
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

}