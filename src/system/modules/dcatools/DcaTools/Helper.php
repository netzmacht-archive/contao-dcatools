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

use DcaTools\Component\DataContainer;
use DcaTools\Component\Operation;
use DcaTools\Event\OperationCallback;

/**
 * Class Helper provides hooks and callbacks for getting connected with Contao
 *
 * @package DcaTools
 */
class Helper
{
	const DataContainer = 'DcaTools\Component\DataContainer';

	protected static $arrComponents = array();

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

		return null;
	}


	/**
	 * @param $strName
	 */
	public function hookLoadDataContainer($strName)
	{
		array_insert($GLOBALS['TL_DCA'][$strName]['config']['onload_callback'], 0, array(
			array('DcaTools\Helper', 'callbackInitializeDataContainer')
		));
	}


	/**
	 * Initialize the DataContainer
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
				$objDataContainer = new DataContainer($dc->table);
				$objDataContainer->initialize();
			}
		}

		$this->scanOperationEvents($dc->table, 'operations');
		$this->scanOperationEvents($dc->table, 'global_operations');
	}


	/**
	 * @param string $strTable
	 * @param string $strKey
	 */
	protected function scanOperationEvents($strTable, $strKey)
	{
		$strCallback = ($strKey == 'operations' ? 'operationCallback' : 'globalOperationCallback');

		if(!isset($GLOBALS['TL_DCA'][$strTable]['list'][$strKey]))
		{
			return;
		}

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
				'DcaTools\Helper', $strCallback . $strOperation
			);
		}
	}


	/**
	 * @param $strName
	 * @param string $strType
	 *
	 * @return mixed
	 */
	public static function getComponent($strName, $strType=Helper::DataContainer)
	{
		if(!isset(static::$arrComponents[$strType][$strName]))
		{
			static::$arrComponents[$strType][$strName] = new $strType($strName);
		}

		return static::$arrComponents[$strType][$strName];
	}


	/**
	 * Callback for getting entries of a data container
	 *
	 * @param null $dc
	 *
	 * @return array
	 */
	public static function getAllowedIds($strName)
	{
		if(TL_MODE != 'BE')
		{
			return array();
		}

		/** @var \DcaTools\Component\DataContainer $objComponent */
		$objComponent = static::getComponent($strName);

		return $objComponent->getAllowedIds();
	}
}