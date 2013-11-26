<?php

/**
 * DcaTools - Toolkit for data containers in Contao
 * Copyright (C) 2013 David Molineus
 *
 * @package   netzmacht-dcatools
 * @author    David Molineus <molineus@netzmacht.de>
 * @license   LGPL-3.0+
 * @copyright 2013 netzmacht creative David Molineus
 */


namespace DcaTools\Listener;

use DcaTools\Definition;
use DcaTools\Event;

/**
 * Class Permissions provides listeners for operations and permission events
 *
 * @package DcaTools\Event
 */
class PermissionsListener
{

	/**
	 * Test if User is an admin.
	 *
	 * @param Event\PermissionEvent $objEvent
	 * @param array $arrConfig
	 *
	 * @return bool
	 */
	public static function isAdmin(Event\PermissionEvent $objEvent, array $arrConfig=array())
	{
		if(!\BackendUser::getInstance()->isAdmin)
		{
			return false;
		}

		return true;
	}


	/**
	 * generic is allowed rule
	 *
	 * @param Event\PermissionEvent $objEvent
	 * @param array $arrConfig supports
	 * 		- ptable string 	optional if want to check isAllowed for another table than data from $arrRow
	 * 		- property string  	optional column of current row for WHERE id=? statement, default pid
	 * 		- value string		optional value if not want to check against a value of arrRow, default $arrRow[$pid]
	 * 		- operation int 	operation integer for BackendUser::isAllowed
	 *      - pid string        optional parent id column
	 *
	 * @return bool
	 */
	public static function isAllowed(Event\PermissionEvent $objEvent, array $arrConfig)
	{
		/** @var \DcaTools\Controller $objController */
		$objController = $objEvent->getSubject()->getDataContainer();

		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();

		$arrRow = array();

		if($objController->hasModel())
		{
			$arrRow = $objController->getModel()->getPropertiesAsArray();
		}

		if(!isset($arrConfig['ptable']))
		{
			return $objUser->isAllowed($arrConfig['operation'], $arrRow);
		}


		if(isset($arrConfig['ptable']) && $arrConfig['ptable'] !== true)
		{
			$strPTable = $arrConfig['ptable'];
		}
		else {
			$arrDefinition = $objController->getDefinition();
			$strPTable = $arrDefinition['config']['ptable'];
		}

		$strPId   = isset($arrConfig['pid']) ? $arrConfig['pid'] : 'pid';
		$strValue = isset($arrConfig['value']) ? $arrConfig['value'] :  $arrRow[$strPId];

		$objParent = \Database::getInstance()
			->prepare("SELECT * FROM $strPTable WHERE id=?")
			->limit(1)
			->execute($strValue);

		return $objUser->isAllowed($arrConfig['operation'], $objParent->row());
	}


	/**
	 * prepare a error message will try to replace wildcards in an error message
	 *
	 * @param array $arrConfig, supportet error and params
	 * @param string $strError error message
	 *
	 * @return string
	 */
	protected static function prepareErrorMessage(array $arrConfig, $strError)
	{
		if(isset($arrConfig['error']))
		{
			if(isset($arrConfig['params']))
			{
				if(!is_array($arrConfig['params']))
				{
					$arrConfig['params'] = array($arrConfig['params']);
				}

				$arrParams = array($arrConfig['error']);

				foreach ($arrConfig['params']  as $strParam)
				{
					$arrParams[] = ($strParam == '%user') ? \BackendUser::getInstance()->username : \Input::get($strParam);
				}

				$strError = call_user_func_array('sprintf', $arrParams);
			}
		}

		return $strError;
	}


	protected static function checkAccess($tableName, array $arrConfig)
	{
		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();

		if($objUser->isAdmin)
		{
			return true;
		}

		// Has access to an module
		if(isset($arrConfig['module']))
		{
			return $objUser->hasAccess($arrConfig['module'], 'modules');
		}

		// Get table
		if($arrConfig['ptable'])
		{
			$definition = Definition::getDataContainer($tableName);
			$tableName  = $definition->get('config/ptable');
		}
		else
		{
			$tableName  = isset($arrConfig['table']) ? $arrConfig['table'] : $tableName;
		}

		// Check access for an action
		if(isset($arrConfig['permission']) && isset($arrConfig['action']))
		{
			if($arrConfig['action'] == 'alexf')
			{
				$arrConfig['action'] = $tableName . '::' . $arrConfig['action'];
			}

			return $objUser->hasAccess($arrConfig['action'], $arrConfig['permission']);
		}
		elseif(isset($arrConfig['alexf']))
		{
			return $objUser->hasAccess($tableName . '::' . $arrConfig['alexf'], 'alexf');
		}
		elseif(isset($arrConfig['fop']))
		{
			return $objUser->hasAccess($arrConfig['fop'], 'fop');
		}

		return false;
	}

}