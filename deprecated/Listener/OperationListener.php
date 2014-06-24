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

namespace deprecated\DcaTools\Listener;

use \deprecated\DcaTools\DcaTools;
use \deprecated\DcaTools\Definition;
use \deprecated\DcaTools\Event;
use \deprecated\DcaTools\Helper\Permissions;


/**
 * Class OperationListeners stores listener which can be used for the operation generate event
 *
 * @package DcaTools\Event
 */
class OperationListener
{

	/**
	 * Test if User is an admin.
	 *
	 * @param Event\GenerateEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool
	 */
	public static function isAdmin(Event\GenerateEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if(Permissions::isAdmin()) {
			return true;
		}

		if($blnStop) {
			$objEvent->stopPropagation();
			$objEvent->getView()->isVisible(false);
		}

		return false;
	}


	/**
	 * @param Event\GenerateEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool
	 */
	public static function hasAccess(Event\GenerateEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		$tableName = $objEvent->getModel()->getProviderName();

		if(Permissions::hasAccess($tableName, $arrConfig)) {
			return true;
		}

		if($blnStop) {
			$objEvent->stopPropagation();
		}

		return false;
	}


	/**
	 * rule checks if user is allowed to run action
	 *
	 * @param Event\GenerateEvent $objEvent
	 * @param array $arrConfig option data row of operation buttons
	 * 		- table string 		optional if want to check for another table
	 * 		- closed bool 		optional if want to check if table is closed
	 * 		- ptable string 	optional if want to check isAllowed for another table than data from $arrRow
	 * 		- property string  	optional column of current row for WHERE id=? statement, default pid
	 * 		- where string		optional customized where, default id=?
	 * 		- value string		optional value if not want to check against a value of arrRow, default $arrRow[$pid]
	 * 		- operation int 	operation integer for BackendUser::isAllowed
	 * @param bool $blnStop if true event will be stopped
	 *
	 * @return bool true if rule is passed
	 */
	public static function isAllowed(Event\GenerateEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		$strTable = (isset($arrConfig['table'])) ? $arrConfig['table'] : $objEvent->getModel()->getProviderName();

		if(!isset($arrConfig['closed']) || !$GLOBALS['TL_DCA'][$strTable]['config']['closed'])
		{
			if(Permissions::isAdmin() || Permissions::isAllowed($objEvent->getModel(), $arrConfig))
			{
				return true;
			}

			if($blnStop) {
				$objEvent->stopPropagation();
			}
		}

		return false;
	}


	/**
	 * Disable icon depending on a callback or a diven value
	 *
	 * @param Event\GenerateEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function disableIcon(Event\GenerateEvent $objEvent, array $arrConfig=array(), $blnStop=true)
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
			/** @var \deprecated\DcaTools\Component\Operation\View $view */
			$view = $objEvent->getView();

			if(isset($arrConfig['icon']))
			{
				$view->setIcon($arrConfig['icon']);
			}
			else {
				// TODO: extract to disabled icon mapping config
				if($view->getIcon() == 'visible.gif')
				{
					$view->setIcon('invisible.gif');
				}
				else {
					$view->setIcon(str_replace('.', '_.', $view->getIcon()));
				}

			}

			$view->setDisabled(true);

			if($blnStop)
			{
				$objEvent->stopPropagation();
			}
		}

		return true;
	}


	/**
	 * Create a referer link
	 *
	 * @param Event\GenerateEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function referer(Event\GenerateEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		/** @var \deprecated\DcaTools\Component\Operation\View $view */
		$view = $objEvent->getView();
		$view->setHref(\Controller::getReferer(true));
		$objEvent->setOutput('');
		$objEvent->setConfigAttribute('plain', true);

		return true;
	}


	/**
	 * @param Event\GenerateEvent $objEvent
	 * @param array $arrConfig
	 * @param bool $blnStop
	 *
	 * @return bool
	 */
	public static function toggleIcon(Event\GenerateEvent $objEvent, array $arrConfig=array(), $blnStop=true)
	{
		if (strlen(\Input::get('tid')))
		{
			static::toggleState($objEvent, $arrConfig, \Input::get('tid'), (\Input::get('state') == 1));
			\Controller::redirect(\Controller::getReferer());
		}

		$objEvent->setOutput(null);

		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();
		/** @var \deprecated\DcaTools\Component\Operation\View $objView */
		$objView = $objEvent->getView();

		/** @var \deprecated\DcaTools\Definition\DataContainer $objController */
		$objDataContainer = Definition::getDataContainer($objEvent->getModel()->getProviderName());

		$arrRow = $objEvent->getModel()->getPropertiesAsArray();

		$strTable    = (isset($arrConfig['table'])) ? $arrConfig['table'] : $objDataContainer->getName();
		$strProperty = (isset($arrConfig['property'])) ? $arrConfig['property'] : 'published';
		$blnVisible  = (isset($arrConfig['inverted']) ? $arrRow[$strProperty] : !$arrRow[$strProperty]);

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if ($objUser->isAdmin && !$objUser->hasAccess($strTable . '::' . $strProperty , 'alexf'))
		{
			if($blnStop)
			{
				$objEvent->stopPropagation();
			}

			return false;
		}

		$strHref = $objView->getHref();
		$strHref .= '&amp;id='.$arrRow['pid'].'&amp;tid='.$arrRow['id'].'&amp;state='.($blnVisible ? 1 : '');

		$objView->setHref($strHref);
		$objEvent->setConfigAttribute('id', false);

		if ($blnVisible)
		{
			$objView->setIcon(isset($arrConfig['icon']) ? $arrConfig['icon'] : 'invisible.gif');
		}

		return true;
	}


	/**
	 * Toggle state of a value
	 *
	 * @param Event\GenerateEvent $objEvent
	 * @param array $arrConfig
	 * @param $intId
	 *
	 * @param $blnVisible
	 */
	protected static function toggleState(Event\GenerateEvent $objEvent, array $arrConfig, $intId, $blnVisible)
	{
		// Check permissions to edit
		\Input::setGet('id', $intId);
		\Input::setGet('act', 'toggle');

		// trigger permission checking
		$name = $objEvent->getModel()->getProviderName();
		$objEvent->getDispatcher()->dispatch(sprintf('dcatools.%s.check-permission', $name));

		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();

		if(isset($arrConfig['inverted']))
		{
			$blnVisible = !$blnVisible;
		}

		$strTable = (isset($arrConfig['table'])) ? $arrConfig['table'] : $name;
		$strProperty = (isset($arrConfig['property'])) ? $arrConfig['property'] : 'published';

		// Check permissions to publish
		if (!$objUser->isAdmin && !$objUser->hasAccess($strTable . '::' . $strProperty, 'alexf'))
		{
			$strError = 'Not enough permissions to toggle state of item ID "'.$intId.'"';
			$strError = Permissions::prepareErrorMessage($arrConfig, $strError);

			DcaTools::error($strError);
		}

		$objVersions = new \Versions($strTable, $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA'][$strTable]['propertys'][$strProperty]['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA'][$strTable]['propertys'][$strProperty]['save_callback'] as $callback)
			{
				$objCallback = new $callback[0];
				$blnVisible = $objCallback->$callback[1]($blnVisible, $objEvent);
			}
		}

		// Update the database
		\Database::getInstance()
			->prepare("UPDATE $strTable SET tstamp=". time() .", $strProperty ='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
			->execute($intId);


		$objVersions->create();
	}

}
