<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   MPL/2.0
 * @copyright 2013 netzmacht creative David Molineus
 */
namespace Netzmacht\DcaTools\Event;

use Symfony\Component\EventDispatcher\GenericEvent;


/**
 * Class OperationListeners stores listener which can be used for the operation generate event
 *
 * @package Netzmacht\DcaTools\Event
 */
class OperationListeners extends Permissions
{

	/**
	 * Test if User is an admin.
	 *
	 * @param GenericEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool
	 */
	public static function isAdmin(GenericEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(parent::isAdmin($objEvent, $arrConfig, $blnStop))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->stopPropagation();
			$objEvent->getSubject()->hide();
		}

		return false;
	}


	/**
	 * @param GenericEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool
	 */
	public static function hasAccess(GenericEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(\BackendUser::getInstance()->isAdmin || parent::hasAccess($objEvent, $arrConfig))
		{
			return true;
		}

		if($blnStop)
		{
			$objEvent->getSubject()->hide();
			$objEvent->stopPropagation();
		}

		return false;
	}


	/**
	 * rule checks if user is allowed to run action
	 *
	 * @param GenericEvent $objEvent
	 * @param array $arrConfig option data row of operation buttons
	 * 		- table string 		optional if want to check for another table
	 * 		- closed bool 		optional if want to check if table is closed
	 * 		- ptable string 	optioinal if want to check isAllowed for another table than data from $arrRow
	 * 		- field string  	optional column of current row for WHERE id=? statement, default pid
	 * 		- where string		optional customized where, default id=?
	 * 		- value string		optional value if not want to check against a value of arrRow, default $arrRow[$pid]
	 * 		- operation int 	operation integer for BackendUser::isAllowed
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool true if rule is passed
	 */
	public static function isAllowed(GenericEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		/** @var \Netzmacht\DcaTools\DataContainer $objDataContainer */
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
			$objEvent->getSubject()->hide();
			$objEvent->stopPropagation();
		}

		return false;
	}


	/**
	 * Disable icon depending on a callback or a diven value
	 *
	 * @param GenericEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function disableIcon(GenericEvent $objEvent, array $arrConfig=array(), $blnStop=true)
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
			$objButton = $objEvent->getSubject();

			$strIcon = isset($arrConfig['icon']) ? $arrConfig['icon'] : str_replace('.', '_.', $objButton->getIcon());
			$objButton->setIcon($strIcon);
			$objButton->disable();
		}

		return true;
	}


	/**
	 * Create a referer link
	 *
	 * @param GenericEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function referer(GenericEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		$objEvent->getSubject()->setHref(\Controller::getReferer(true));
		$objEvent->setArgument('plain', true);

		return true;
	}


	/**
	 * @param GenericEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function toggleIcon(GenericEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if (strlen(\Input::get('tid')))
		{
			static::toggleState($objEvent, $arrConfig, \Input::get('tid'), (\Input::get('state') == 1));
			\Controller::redirect(\Controller::getReferer());
		}

		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();
		$objOperation = $objEvent->getSubject();

		/** @var \Netzmacht\DcaTools\DataContainer $objDataContainer */
		$objDataContainer = $objOperation->getDataContainer();

		$arrRow = $objDataContainer->getRecord()->row();

		$strTable = (isset($arrConfig['table'])) ? $arrConfig['table'] : $objDataContainer->getName();
		$strField = (isset($arrConfig['field'])) ? $arrConfig['field'] : 'published';
		$blnVisible = (isset($arrConfig['inverted']) ? $arrRow[$strField] : !$arrRow[$strField]);

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if ($objUser->isAdmin && !$objUser->hasAccess($strTable . '::' . $strField , 'alexf'))
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
	 * @param GenericEvent $objEvent
	 * @param array $arrConfig
	 * @param $intId
	 *
	 * @param $blnVisible
	 */
	protected static function toggleState(GenericEvent $objEvent, array $arrConfig, $intId, $blnVisible)
	{
		// Check permissions to edit
		\Input::setGet('id', $intId);
		\Input::setGet('act', 'toggle');

		// trigger permission checking
		/** @var \Netzmacht\DcaTools\DataContainer $objDataContainer */
		$objDataContainer = $objEvent->getSubject()->getDataContainer();
		$objDataContainer->dispatch('permissions');

		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();


		if(isset($arrConfig['inverted']))
		{
			$blnVisible = !$blnVisible;
		}

		$strTable = (isset($arrConfig['table'])) ? $arrConfig['table'] : $objDataContainer->getName();
		$strField = (isset($arrConfig['field'])) ? $arrConfig['field'] : 'published';

		// Check permissions to publish
		if (!$objUser->isAdmin && !$objUser->hasAccess($strTable . '::' . $strField, 'alexf'))
		{
			$strError = 'Not enough permissions to toggle state of item ID "'.$intId.'"';
			$strError = static::prepareErrorMessage($arrConfig, $strError);

			\Controller::log($strError, get_called_class() . ' toggleState', TL_ERROR);
			\Controller::redirect('contao/main.php?act=error');
		}

		$objVersions = new \Versions($strTable, $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA'][$strTable]['fields'][$strField]['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA'][$strTable]['fields'][$strField]['save_callback'] as $callback)
			{
				$objCallback = new $callback[0];
				$blnVisible = $objCallback->$callback[1]($blnVisible, $objDataContainer);
			}
		}

		// Update the database
		\Database::getInstance()
			->prepare("UPDATE $strTable SET tstamp=". time() .", $strField ='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
			->execute($intId);


		$objVersions->create();
	}

}
