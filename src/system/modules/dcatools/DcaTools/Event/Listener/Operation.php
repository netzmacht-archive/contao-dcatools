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

use DcaTools\Event;


/**
 * Class OperationListeners stores listener which can be used for the operation generate event
 *
 * @package DcaTools\Event
 */
class Operation extends Permissions
{

	/**
	 * Test if User is an admin.
	 *
	 * @param Event\Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool
	 */
	public static function isAdmin(Event\Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(parent::isAdmin($objEvent, $arrConfig, $blnStop))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->setArgument('buffer', '');
			$objEvent->stopPropagation();
			$objEvent->getSubject()->hide();
		}

		return false;
	}


	/**
	 * @param Event\Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool
	 */
	public static function hasAccess(Event\Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(\BackendUser::getInstance()->isAdmin || parent::hasAccess($objEvent, $arrConfig))
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
	 * rule checks if user is allowed to run action
	 *
	 * @param Event\Permission $objEvent
	 * @param array $arrConfig option data row of operation buttons
	 * 		- table string 		optional if want to check for another table
	 * 		- closed bool 		optional if want to check if table is closed
	 * 		- ptable string 	optioinal if want to check isAllowed for another table than data from $arrRow
	 * 		- property string  	optional column of current row for WHERE id=? statement, default pid
	 * 		- where string		optional customized where, default id=?
	 * 		- value string		optional value if not want to check against a value of arrRow, default $arrRow[$pid]
	 * 		- operation int 	operation integer for BackendUser::isAllowed
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool true if rule is passed
	 */
	public static function isAllowed(Event\Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		/** @var \DcaTools\Definition\DataContainer $objDataContainer */
		$objDataContainer = $objEvent->getSubject()->getDataContainer();
		
		$strTable = (isset($arrConfig['table'])) ? $arrConfig['table'] : $objDataContainer->getName();

		if(!isset($arrConfig['closed']) || !$GLOBALS['TL_DCA'][$strTable]['config']['closed'])
		{
			if(\BackendUser::getInstance()->isAdmin || parent::isAllowed($objEvent, $arrConfig))
			{
				return true;
			}
		}

		if($blnStop)
		{
			$objEvent->denyAccess();
		}

		return false;
	}


	/**
	 * Disable icon depending on a callback or a diven value
	 *
	 * @param Event\Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function disableIcon(Event\Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(isset($arrConfig['callback']))
		{
			$blnDisable = call_user_func($arrConfig['callback'], $objEvent, $arrConfig, false);
		}
		else
		{
			$blnDisable = (bool) $arrConfig['value'];
		}

		if($blnDisable)
		{
			/** @var \DcaTools\Component\Operation $objButton */
			$objButton = $objEvent->getSubject();

			$strIcon = isset($arrConfig['icon']) ? $arrConfig['icon'] : str_replace('.', '_.', $objButton->getIcon());
			$objButton->setIcon($strIcon);
			$objButton->disable();

			$objEvent->setOutput('');
			$objEvent->stopPropagation();
		}

		return true;
	}


	/**
	 * Create a referer link
	 *
	 * @param Event\Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function referer(Event\Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		$objEvent->getSubject()->setHref(\Controller::getReferer(true));
		$objEvent->setOutput('');
		$objEvent->setArgument('plain', true);

		return true;
	}


	/**
	 * @param Event\Permission $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function toggleIcon(Event\Permission $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if (strlen(\Input::get('tid')))
		{
			static::toggleState($objEvent, $arrConfig, \Input::get('tid'), (\Input::get('state') == 1));
			\Controller::redirect(\Controller::getReferer());
		}

		$objEvent->setArgument('buffer', '');

		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();
		$objOperation = $objEvent->getSubject();

		/** @var \DcaTools\Definition\DataContainer $objController */
		$objController = $objOperation->getDataContainer();

		$arrRow = $objEvent->getModel()->getPropertiesAsArray();

		$strTable = (isset($arrConfig['table'])) ? $arrConfig['table'] : $objController->getName();
		$strProperty = (isset($arrConfig['property'])) ? $arrConfig['property'] : 'published';
		$blnVisible = (isset($arrConfig['inverted']) ? $arrRow[$strProperty] : !$arrRow[$strProperty]);

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if ($objUser->isAdmin && !$objUser->hasAccess($strTable . '::' . $strProperty , 'alexf'))
		{
			if($blnStop)
			{
				$objEvent->stopPropagation();
			}

			return false;
		}

		$strHref = $objEvent->getSubject()->getHref();
		$strHref .= '&amp;id='.$arrRow['pid'].'&amp;tid='.$arrRow['id'].'&amp;state='.($blnVisible ? 1 : '');

		$objOperation->setHref($strHref);
		$objEvent->setArgument('noId', true);

		if ($blnVisible)
		{
			$objOperation->setIcon(isset($arrConfig['icon']) ? $arrConfig['icon'] : 'invisible.gif');
		}

		return true;
	}


	/**
	 * Toggle state of a value
	 *
	 * @param Event\Permission $objEvent
	 * @param array $arrConfig
	 * @param $intId
	 *
	 * @param $blnVisible
	 */
	protected static function toggleState(Event\Permission $objEvent, array $arrConfig, $intId, $blnVisible)
	{
		// Check permissions to edit
		\Input::setGet('id', $intId);
		\Input::setGet('act', 'toggle');

		// trigger permission checking
		/** @var \DcaTools\Controller $objController */
		$objController = $objEvent->getSubject()->getDataContainer();
		$objController->dispatch('permissions');

		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();


		if(isset($arrConfig['inverted']))
		{
			$blnVisible = !$blnVisible;
		}

		$strTable = (isset($arrConfig['table'])) ? $arrConfig['table'] : $objController->getName();
		$strProperty = (isset($arrConfig['property'])) ? $arrConfig['property'] : 'published';

		// Check permissions to publish
		if (!$objUser->isAdmin && !$objUser->hasAccess($strTable . '::' . $strProperty, 'alexf'))
		{
			$strError = 'Not enough permissions to toggle state of item ID "'.$intId.'"';
			$strError = static::prepareErrorMessage($arrConfig, $strError);

			\Controller::log($strError, get_called_class() . ' toggleState', TL_ERROR);
			\Controller::redirect('contao/main.php?act=error');
		}

		$objVersions = new \Versions($strTable, $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA'][$strTable]['propertys'][$strProperty]['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA'][$strTable]['propertys'][$strProperty]['save_callback'] as $callback)
			{
				$objCallback = new $callback[0];
				$blnVisible = $objCallback->$callback[1]($blnVisible, $objController);
			}
		}

		// Update the database
		\Database::getInstance()
			->prepare("UPDATE $strTable SET tstamp=". time() .", $strProperty ='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
			->execute($intId);


		$objVersions->create();
	}

}
