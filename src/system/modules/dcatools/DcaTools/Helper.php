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
use Netzmacht\DcaTools\Component\DataContainer;
use Netzmacht\DcaTools\Component\Operation;
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
			$strOperation = substr($strMethod, 17);
			$objOperation = new Operation($arrArguments[6], $strOperation, 'local');

			$objOperation->setModel(array_shift($arrArguments));
		}
		elseif (strncmp($strMethod, 'globalOperationCallback', 23) === 0)
		{
			$strOperation = substr($strMethod, 23);
			$objOperation = new Operation($arrArguments[5], $strOperation, 'global');
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

		$arrConfig =& $GLOBALS['TL_DCA'][$dc->table]['dcatools'];

		// initialize and check permissions
		if(isset($arrConfig['events']))
		{
			$objDataContainer = new DataContainer($dc->table);
			$objDataContainer->initialize();
		}

		$this->scanOperationEvents($dc->table, $arrConfig, 'operations');
		$this->scanOperationEvents($dc->table, $arrConfig, 'global_operations');
	}


	/**
	 * @param string $strTable
	 * @param array $arrConfig
	 * @param string $strKey
	 */
	protected function scanOperationEvents($strTable, array $arrConfig, $strKey)
	{
		if(!isset($arrConfig['scan'][$strKey]) && $arrConfig['scan'][$strKey])
		{
			return;
		}

		$strCallback = ($strKey == 'operations' ? 'operationCallback' : 'globalOperationCallback');

		foreach($GLOBALS['TL_DCA'][$strTable]['list'][$strKey] as $strOperation => $arrOperation)
		{
			if(!isset($arrOperation['events']))
			{
				continue;
			}

			if(isset($arrOperation['button_callback']))
			{
				$GLOBALS['TL_DCA'][$strTable]['list'][$strKey][$strOperation]['events']['generate'][] = array(
					function($objEvent) use($arrOperation) {
						$objCallback = new OperationCallback($arrOperation['button_callback']);
						$objCallback->execute($objEvent);
					}, 99
				);
			}

			$GLOBALS['TL_DCA'][$strTable]['list'][$strKey][$strOperation]['button_callback'] = array
			(
				'Netzmacht\DcaTools\Helper', $strCallback . $strOperation
			);
		}
	}
}