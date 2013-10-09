<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 09.10.13
 * Time: 11:04
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools;


use Netzmacht\DcaTools\Event\OperationCallback;

class Helper
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
		$objDataContainer = DcaTools::getDataContainer($arrArguments[6]);

		if (strncmp($strMethod, 'operationCallback', 14) === 0)
		{
			$strOperation = substr($strMethod, 14);
			$objOperation = $objDataContainer->getOperation($strOperation, 'local');
		}
		elseif (strncmp($strMethod, 'globalOperationCallback', 20) === 0)
		{
			$strOperation = substr($strMethod, 20);
			$objOperation = $objDataContainer->getOperation($strOperation, 'global');

			$strClass = \Controller::getModelClassFromTable($arrArguments[7]);

			/** @var \Model $objModel */
			$objModel = new $strClass;
			$objModel->setRow(array_shift($arrArguments));

			$objDataContainer->setRecord($objModel);
		}

		if(isset($objOperation))
		{
			$objOperation->setHref($arrArguments[1]);
			$objOperation->setLabel($arrArguments[1]);
			$objOperation->setTitle($arrArguments[1]);
			$objOperation->setIcon($arrArguments[1]);
			$objOperation->setAttributes($arrArguments[1]);

			return $objOperation->generate();
		}
	}


	/**
	 * @param $strName
	 */
	public function hookLoadDataContainer($strName)
	{
		if(isset($GLOBALS['TL_DCA'][$strName]['dcatools']))
		{
			array_insert($GLOBALS['TL_DCA'][$strName]['config']['onload_callback'], 0, array(
				array('Netzmacht\DcaTools\Helper', 'callbackInitializeDataContainer')
			));
		}
	}


	/**
	 * Initialize the DataContainer
	 * @param $dc
	 */
	public function callbackInitializeDataContainer($dc)
	{
		$objDataContainer = DcaTools::getDataContainer($dc->table);

		$arrConfig =& $GLOBALS['TL_DCA'][$dc->table]['dcatools'];


		if(isset($arrConfig['initialize']) && is_array($arrConfig['initialize']))
		{
			foreach($arrConfig['initialize'] as $listener)
			{
				DcaTools::registerListener($objDataContainer, 'initialize', $listener);
			}
		}

		if(isset($arrConfig['permissions']) && is_array($arrConfig['permissions']))
		{
			foreach($arrConfig['permissions'] as $listener)
			{
				DcaTools::registerListener($objDataContainer, 'permissions', $listener);
			}
		}


		if(isset($arrConfig['operationListeners']) && $arrConfig['operationListeners'])
		{
			// handle operation events
			foreach($GLOBALS['TL_DCA'][$dc->table]['list']['operations'] as $strOperation => $arrOperation)
			{
				if(!isset($arrOperation['events']))
				{
					continue;
				}

				if(isset($arrOperation['button_callback']))
				{
					$GLOBALS['TL_DCA'][$dc->table]['list']['operations'][$strOperation]['events']['generate'][] = array(
						function($objEvent) use($arrOperation) {
							$objCallback = new OperationCallback($arrOperation['button_callback']);
							$objCallback->execute($objEvent);
						}, -1
					);
				}

				$GLOBALS['TL_DCA'][$dc->table]['list']['operations'][$strOperation]['button_callback'] = array
				(
					'Netzmacht\DcaTools\Helper', 'operationCallback' . $strOperation
				);
			}

			// handle global operation events
			foreach($GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'] as $strOperation => $arrOperation)
			{
				if(!isset($arrOperation['events']))
				{
					continue;
				}

				if(isset($arrOperation['button_callback']))
				{
					$GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'][$strOperation]['events']['generate'][] = array(
						function($objEvent) use($arrOperation) {
							$objCallback = new OperationCallback($arrOperation['button_callback']);
							$objCallback->execute($objEvent);
						}, -1
					);
				}

				$GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'][$strOperation]['button_callback'] = array
				(
					'Netzmacht\DcaTools\Helper', 'globalOperationCallback' . $strOperation
				);
			}

			// initialize and check permissions
			$objDataContainer->initialize();
		}
	}
}