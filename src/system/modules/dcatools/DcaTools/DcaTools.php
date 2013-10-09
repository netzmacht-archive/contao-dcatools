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

		// wrap callback in a closure if configuration is passed
		if(isset($arrConfig[2]))
		{
			$arrConfig = function($objEvent) use($arrConfig)
			{
				$arrConfig[0]::$arrConfig[1]($objEvent, $arrConfig[2]);
			};
		}

		$objTarget->addListener($strName, $arrConfig, $intPriority);
	}
}