<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 27.09.13
 * Time: 18:26
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools;


use DcGeneral\Callbacks\ContaoStyleCallbacks;
use Netzmacht\DcaTools\Event\OperationCallback;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DcaTools
{

	/**
	 * @var DataContainer[]
	 */
	protected static $arrDataContainers = array();


	/**
	 * @param $strName
	 * @param null $objRecord
	 * @return DataContainer
	 */
	protected static $blnAutoUpdate = false;


	/**
	 * @param $strName
	 * @param null $objRecord
	 * @return DataContainer
	 */
	public static function getDataContainer($strName, $objRecord=null)
	{
		if(!isset(static::$arrDataContainers[$strName]))
		{
			static::$arrDataContainers[$strName] = new DataContainer($strName, $objRecord);
		}

		return static::$arrDataContainers[$strName];
	}


	/**
	 * @param null $blnValue
	 * @return bool
	 */
	public static function doAutoUpdate($blnValue=null)
	{
		if($blnValue !== null)
		{
			static::$blnAutoUpdate = (bool) $blnValue;
		}

		return static::$blnAutoUpdate;
	}


	/**
	 * @param $strName
	 */
	public function hookLoadDataContainer($strName)
	{
		if(isset($GLOBALS['TL_DCA'][$strName]['dcatools']))
		{
			$GLOBALS['TL_DCA'][$strName]['config']['onload_callback'][] = array('Netzmacht\DcaTools\DcaTools', 'initializeDataContainer');
		}
	}


	/**
	 * Initialize the DataContainer
	 * @param $dc
	 */
	public function initializeDataContainer($dc)
	{
		$objDataContainer = static::getDataContainer($dc->table);

		$arrConfig =& $GLOBALS['TL_DCA'][$dc->table]['dcatools'];


		if(isset($arrConfig['events']) && is_array($arrConfig['events']))
		{
			foreach($arrConfig['events'] as $strEvent => $listener)
			{
				$this->registerEvent($objDataContainer, $strEvent, $listener);
			}
		}

		// handle operation events
		foreach($GLOBALS['TL_DCA'][$dc->table]['list']['operations'] as $strOperation => $arrOperation)
		{

			if(!(isset($arrConfig['operationEvents']) && $arrConfig['operationEvents']) && !isset($arrOperation['events']))
			{
				continue;
			}

			if(isset($arrOperation['button_callback']))
			{
				$GLOBALS['TL_DCA'][$dc->table]['list']['operations'][$strOperation]['events']['native'][] = array(
					function($objEvent) use($arrOperation) {
						$objCallback = new OperationCallback($arrOperation['button_callback']);
						$objCallback->execute($objEvent);
					}, -1
				);
			}

			$GLOBALS['TL_DCA'][$dc->table]['list']['operations'][$strOperation]['button_callback'] = array
			(
				'Netzmacht\DcaTools\DcaTools', 'operationCallback' . $strOperation
			);
		}

		// handle operation events
		foreach($GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'] as $strOperation => $arrOperation)
		{
			if(!(isset($arrConfig['operationEvents']) && $arrConfig['operationEvents']) && !isset($arrOperation['events']))
			{
				continue;
			}

			if(isset($arrOperation['button_callback']))
			{
				$GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'][$strOperation]['events']['native'][] = array
				(
					'Netzmacht\DcaTools\Event\OperationCallback', 'execute', array
					(
						'callback' =>  $arrOperation['button_callback']
					)
				);
			}

			$GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'][$strOperation]['button_callback'] = array
			(
				'Netzmacht\DcaTools\DcaTools', 'globalOperationCallback' . $strOperation
			);
		}
	}


	/**
	 * Register an event to a dispatcher
	 *
	 * @param EventDispatcher $objTarget
	 * @param $strName
	 * @param $arrConfig
	 */
	public static function registerListener(EventDispatcher $objTarget, $strName, $arrConfig)
	{
		// @see https://github.com/bit3/contao-event-dispatcher/blob/master/contao/config/services.php
		if (is_array($arrConfig) && count($arrConfig) === 2 && is_int($arrConfig[1]))
		{
			list($arrConfig, $intPriority) = $arrConfig;
		}
		else
		{
			$intPriority = 0;
		}

		$objTarget->addListener($strName, $arrConfig, $intPriority);
	}



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
}