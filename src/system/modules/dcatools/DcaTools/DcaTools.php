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
use Netzmacht\DcaTools\Event\ButtonCallback;
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

		// handle button events
		foreach($GLOBALS['TL_DCA'][$dc->table]['list']['operations'] as $strButton => $arrButton)
		{

			if(!(isset($arrConfig['buttonEvents']) && $arrConfig['buttonEvents']) && !isset($arrButton['events']))
			{
				continue;
			}

			if(isset($arrButton['button_callback']))
			{
				$GLOBALS['TL_DCA'][$dc->table]['list']['operations'][$strButton]['events']['native'][] = array(
					function($objEvent) use($arrButton) {
						$objCallback = new ContaoCallback($objEvent, $arrButton['button_callback']);
						$objCallback->execute();
					}, -1
				);
			}

			$GLOBALS['TL_DCA'][$dc->table]['list']['operations'][$strButton]['button_callback'] = array
			(
				'Netzmacht\DcaTools\DcaTools', 'buttonCallback' . $strButton
			);
		}

		// handle button events
		foreach($GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'] as $strButton => $arrButton)
		{
			if(!(isset($arrConfig['buttonEvents']) && $arrConfig['buttonEvents']) && !isset($arrButton['events']))
			{
				continue;
			}

			if(isset($arrButton['button_callback']))
			{
				$GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'][$strButton]['events']['native'][] = array
				(
					'Netzmacht\DcaTools\Event\ButtonCallback', 'execute', array
					(
						'callback' =>  $arrButton['button_callback']
					)
				);
			}

			$GLOBALS['TL_DCA'][$dc->table]['list']['global_operations'][$strButton]['button_callback'] = array
			(
				'Netzmacht\DcaTools\DcaTools', 'globalButtonCallback' . $strButton
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
	protected function registerEvent(EventDispatcher $objTarget, $strName, $arrConfig)
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
	 * Use magic stuff for generating buttons
	 *
	 * @param $strMethod
	 * @param $arrArguments
	 *
	 * @return mixed
	 */
	public function __call($strMethod, $arrArguments)
	{
		$objDataContainer = DcaTools::getDataContainer($arrArguments[6]);

		if (strncmp($strMethod, 'buttonCallback', 14) === 0)
		{
			$strButton = substr($strMethod, 14);
			$objButton = $objDataContainer->getButton($strButton, 'local');

			return call_user_func_array(array($objButton, 'generate'), $arrArguments);
		}
		elseif (strncmp($strMethod, 'globalButtonCallback', 20) === 0)
		{
			$strButton = substr($strMethod, 20);
			$objButton = $objDataContainer->getButton($strButton, 'global');

			return call_user_func_array(array($objButton, 'generate'), $arrArguments);
		}
	}
}