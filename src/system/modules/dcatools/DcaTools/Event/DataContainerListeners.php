<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 09.10.13
 * Time: 10:14
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools\Event;

/**
 * Class DataContainerListeners
 * @package Netzmacht\DcaTools\Event
 */
class DataContainerListeners extends Permissions
{

	/**
	 * @param DataContainerEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool|void
	 */
	public static function hasAccess(DataContainerEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(!static::hasGenericPermission($objEvent, $arrConfig) || parent::hasAccess($objEvent, $arrConfig))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->stopPropagation();
		}

		return false;
	}


	/**
	 * @param DataContainerEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool|void
	 */
	public static function isAllowed(DataContainerEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(static::hasGenericPermission($objEvent, $arrConfig))
		{
			if(!isset($arrConfig['value']))
			{
				$arrConfig['value'] = \Input::get('id');
			}

			if(parent::isAllowed($objEvent, $arrConfig))
			{
				return true;
			}

			if($blnStop)
			{
				$objEvent->stopPropagation();
			}
		}

		return false;
	}


	/**
	 * @param DataContainerEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool|void
	 */
	public static function isAdmin(DataContainerEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(!static::hasGenericPermission($objEvent, $arrConfig) || parent::isAdmin($objEvent, $arrConfig))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->stopPropagation();
		}

		return false;
	}


	/**
	 * @param DataContainerEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool|void
	 */
	public static function forbidden(DataContainerEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(!static::hasGenericPermission($objEvent, $arrConfig))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->stopPropagation();
		}

		return false;
	}



	/**
	 * @param DataContainerEvent $objEvent
	 * @param array $arrConfig
	 *
	 * @return bool
	 */
	protected static function hasGenericPermission(DataContainerEvent $objEvent, array $arrConfig=array())
	{
		$objEvent->getConfig()->set('error', static::prepareErrorMessage($arrConfig, $objEvent->getConfig()->get('error')));
		$blnAccess = true;

		if(isset($arrConfig['act']))
		{
			if($arrConfig['act'] == '*' && \Input::get('act') != '')
			{
				return true;
			}

			if(!is_array($arrConfig['act']))
			{
				$arrConfig['act'] = array($arrConfig['act']);
			}

			if($arrConfig['act'][0] == '*')
			{
				if(\Input::get('act') != '*' && !in_array(\Input::get('act'), $arrConfig['act']))
				{
					return true;
				}
			}
			elseif(in_array(\Input::get('act'), $arrConfig['act']))
			{
				return true;
			}

			$blnAccess = false;
		}

		if(isset($arrConfig['key']))
		{
			if($arrConfig['key'] == '*' && \Input::get('act') != '')
			{
				return true;
			}

			if(!is_array($arrConfig['key']))
			{
				$arrConfig['key'] = array($arrConfig['key']);
			}

			if($arrConfig['key'][0] == '*')
			{
				if(\Input::get('key') != '*' && !in_array(\Input::get('key'), $arrConfig['key']))
				{
					return true;
				}
			}
			elseif(in_array(\Input::get('key'), $arrConfig['key']))
			{
				return true;
			}

			$blnAccess = false;
		}

		return $blnAccess;
	}

}