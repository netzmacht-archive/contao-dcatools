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

use DcaTools\Component\Component;
use DcaTools\Definition;
use DcaTools\Event;

/**
 * Class DataContainer
 * @package DcaTools\Component
 */
class Controller extends Component
{
	protected static $arrInstances = array();


	/**
	 * @param $strName
	 *
	 * @return Controller
	 */
	public static function getInstance($strName)
	{
		if(!isset(static::$arrInstances[$strName]))
		{
			static::$arrInstances[$strName] = new Controller($strName);
		}

		return static::$arrInstances[$strName];
	}


	/**
	 * @param Definition\Node $strName
	 */
	protected function __construct($strName)
	{
		parent::__construct(Definition::getDataContainer($strName));

		$arrConfig = $this->objDefinition->get('dcatools');

		if(isset($arrConfig['events']) && is_array($arrConfig['events']))
		{
			foreach($arrConfig['events'] as $strName => $arrListeners)
			{
				$this->addListeners($strName, $arrListeners);
			}
		}
	}


	/**
	 * Initialize DataContainer
	 */
	public function initialize()
	{
		$this->dispatch('initialize');

		if(\Input::get('act') != '')
		{
			$strErrorDefault = sprintf(
				'User "%s" has not enough permission to run action "%s" for DataContainer "%s"',
				\BackendUser::getInstance()->username,
				\Input::get('act'),
				$this->getName()
			);

			if(\Input::get('id') != '')
			{
				$strErrorDefault .= ' on item with ID "' .\Input::get('id') . '"';
			}
		}
		else
		{
			$strErrorDefault = sprintf(
				'User "%s" has not enough permission to access module "%s"',
				\BackendUser::getInstance()->username,
				\Input::get('do')
			);
		}

		$objEvent = new Event\Permission($this);
		$objEvent->setError($strErrorDefault);

		/** @var Event\Permission $objEvent */
		$objEvent = $this->dispatch('permissions', $objEvent);

		if(!$objEvent->isAccessGranted())
		{
			\Controller::log($objEvent->getError(), 'DataContainer initialize', TL_ERROR);
			\Controller::redirect('contao/main.php?act=error');
			return;
		}
	}


	/**
	 * Get all entries of an element filtered for user access. Events need to be registered to get it works
	 *
	 * This is useful for getting all content elements where each ptable can register its getter
	 *
	 * @param string $strParent
	 *
	 * @return array
	 */
	public function getAllowedIds($strParent=null)
	{
		$objEvent = new Event\Event($this);
		$objEvent['ids'] = array();

		if($strParent !== null)
		{
			$objEvent['parentDataContainer'] = $strParent;
		}

		$objEvent = $this->dispatch('getAllowedIds', $objEvent);
		return $objEvent['ids'];
	}


	/**
	 * Get all allowed ptables for a user.
	 *
	 * It is nessecary to register getAllowedDynamicParents events for the data container to set the ptables
	 *
	 * @return array
	 *
	 * @throws \RuntimeException
	 */
	public function getAllowedDynamicParents()
	{
		if(!$this->objDefinition->getFromDefinition('config/dynamicPtable'))
		{
			throw new \RuntimeException("DataContainer '{$this->getName()}' does not have dynamic ptables");
		}

		$obEvent = new Event\Event($this);
		$obEvent['ptables'] = array();

		$objEvent = $this->dispatch('getAllowedDynamicParents', $obEvent);
		return $objEvent['ptables'];
	}


	/**
	 * Get all allowed entries grouped by ptable and pid
	 *
	 * @param string $strParent
	 * @param array $arrFields
	 *
	 * @return array
	 */
	public function getAllowedEntries($strParent=null, array $arrFields=array())
	{
		$objEvent = new Event\Event($this);
		$objEvent['entries'] = array();
		$objEvent['fields'] = $arrFields;

		if($strParent !== null)
		{
			$objEvent['parentDataContainer'] = $strParent;
		}

		$objEvent = $this->dispatch('getAllowedEntries', $objEvent);
		return $objEvent['entries'];
	}
}