<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 09.10.13
 * Time: 11:04
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools;

use DcGeneral\Data\DefaultModel;
use Netzmacht\DcaTools\Event\OperationCallback;

/**
 * Class Helper provides hooks and callbacks for getting connected with Contao
 *
 * @package Netzmacht\DcaTools
 */
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
		if (strncmp($strMethod, 'operationCallback', 17) === 0)
		{
			$objDataContainer = Definition::getDataContainer($arrArguments[6]);

			$strOperation = substr($strMethod, 17);
			$objOperation = $objDataContainer->getOperation($strOperation, 'local');

			$objModel = new DefaultModel();
			$objModel->setPropertiesAsArray(array_shift($arrArguments));

			$objDataContainer->setModel($objModel);
		}
		elseif (strncmp($strMethod, 'globalOperationCallback', 23) === 0)
		{
			$objDataContainer = Definition::getDataContainer($arrArguments[5]);

			$strOperation = substr($strMethod, 23);
			$objOperation = $objDataContainer->getOperation($strOperation, 'global');
		}

		if(isset($objOperation))
		{
			$objOperation->setHref($arrArguments[0]);
			$objOperation->setLabel(array($arrArguments[1], $arrArguments[2]));
			$objOperation->setAttributes($arrArguments[4]);

			return $objOperation->generate();
		}
	}


	/**
	 * @param $strName
	 */
	public function hookLoadDataContainer($strName)
	{
		array_insert($GLOBALS['TL_DCA'][$strName]['config']['onload_callback'], 0, array(
			array('Netzmacht\DcaTools\Helper', 'callbackInitializeDataContainer')
		));
	}


	/**
	 * Initialize the DataContainer
	 * @param $dc
	 */
	public function callbackInitializeDataContainer($dc)
	{
		if(!isset($GLOBALS['TL_DCA'][$dc->table]['dcatools']))
		{
			return;
		}

		$objDataContainer = Definition::getDataContainer($dc->table);

		$arrConfig =& $GLOBALS['TL_DCA'][$dc->table]['dcatools'];


		if(isset($arrConfig['initialize']) && is_array($arrConfig['initialize']))
		{
			foreach($arrConfig['initialize'] as $listener)
			{
				Definition::registerListener($objDataContainer, 'initialize', $listener);
			}
		}

		if(isset($arrConfig['permissions']) && is_array($arrConfig['permissions']))
		{
			foreach($arrConfig['permissions'] as $listener)
			{
				Definition::registerListener($objDataContainer, 'permissions', $listener);
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
		}

		// initialize and check permissions
		$objDataContainer->initialize();
	}
}