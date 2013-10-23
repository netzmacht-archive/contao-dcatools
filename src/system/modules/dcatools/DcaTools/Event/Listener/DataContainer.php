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

namespace DcaTools\Event\Listener;

use DcaTools\Event\Permission;

/**
 * Class DataContainerListeners
 * @package DcaTools\Event
 */
class DataContainer extends Permissions
{

	/**
	 * @param Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool|void
	 */
	public static function hasAccess(Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(!static::hasGenericPermission($objEvent, $arrConfig) || parent::hasAccess($objEvent, $arrConfig))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->denyAccess();
		}

		return false;
	}


	/**
	 * @param Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool|void
	 */
	public static function isAllowed(Permission $objEvent, array $arrConfig=array(), $blnStop=true)
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
				$objEvent->denyAccess();
			}
		}

		return false;
	}


	/**
	 * @param Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool|void
	 */
	public static function isAdmin(Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(!static::hasGenericPermission($objEvent, $arrConfig) || parent::isAdmin($objEvent, $arrConfig))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->denyAccess();
		}

		return false;
	}


	/**
	 * @param Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool|void
	 */
	public static function forbidden(Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(!static::hasGenericPermission($objEvent, $arrConfig))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->denyAccess();
		}

		return false;
	}



	/**
	 * @param Permission $objEvent
	 * @param array $arrConfig
	 *
	 * @return bool
	 */
	public static function hasGenericPermission(Permission $objEvent, array $arrConfig=array())
	{
		$objEvent->setArgument('error', static::prepareErrorMessage($arrConfig, $objEvent->getArgument('error')));
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