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

use DcaTools\Definition;
use DcaTools\Event\CheckPermissionEvent;
use DcaTools\Event\Contao;
use DcaTools\Event\ControllerEvent;
use DcaTools\Event\GetDynamicParentEvent;
use DcaTools\Event\Priority;
use DcaTools\Event\RestrictedDataAccessEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class DataContainer
 * @package DcaTools\Component
 */
class Controller
{

	/**
	 * @var array
	 */
	protected $enabledOperations = array();

	/**
	 * @var array
	 */
	protected $enabledGlobalOperations = array();

	/**
	 * @var array
	 */
	protected static $arrInstances = array();

	/**
	 * @var \DcaTools\Definition\DataContainer
	 */
	protected $objDefinition;

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	protected $objDispatcher;


	/**
	 * @param $strName
	 *
	 * @return Controller
	 */
	public static function getInstance($strName)
	{
		global $container;

		if(!isset(static::$arrInstances[$strName]))
		{
			static::$arrInstances[$strName] = new static($strName, $container['event-dispatcher']);
		}

		return static::$arrInstances[$strName];
	}


	/**
	 * @param string $strName
	 * @param $objDispatcher
	 */
	protected function __construct($strName, $objDispatcher)
	{
		$this->objDefinition = Definition::getDataContainer($strName);
		$this->objDispatcher = $objDispatcher;
	}


	/**
	 * Initialize DataContainer
	 */
	public function initialize()
	{
		$this->triggerInitializeEvent();
		$this->triggerCheckPermissionEvent();

		$this->initializeEventListeners();
	}


	/**
	 * @return Definition\DataContainer
	 */
	public function getDefinition()
	{
		return $this->objDefinition;
	}


	/**
	 * Trigger initialize event
	 */
	protected function triggerInitializeEvent()
	{
		$event     = new ControllerEvent($this);
		$eventName = sprintf('dcatools.%s.initialize', $this->objDefinition->getName());
		$this->objDispatcher->dispatch($eventName, $event);
	}


	/**
	 * Trigger check permission event
	 */
	protected function triggerCheckPermissionEvent()
	{
		$event     = new CheckPermissionEvent($this);
		$eventName = sprintf('dcatools.%s.check-permission', $this->objDefinition->getName());

		$this->objDispatcher->dispatch($eventName, $event);

		if(!$event->isAccessGranted())
		{
			$message = $event->hasErrors() ? implode(', ', $event->getErrors()) : $this->getDefaultError();
			static::error($message);
		}
	}


	/**
	 * Initialize all provides event listeners as replacement for Contaos usual callbacks
	 */
	protected function initializeEventListeners()
	{
		$this->initializeOperationListeners();
		$this->initializeGlobalOperationListeners();
	}


	/**
	 * Initialize events for all registered operations events
	 */
	protected function initializeOperationListeners()
	{
		foreach($this->objDefinition->getOperationNames() as $operation)
		{
			$eventName = sprintf('dcatools.%s.global_operations.%s', $this->objDefinition->getName(), $operation);
			if(isset($GLOBALS['TL_EVENTS'][$eventName]))
			{
				$this->enableOperationEvents($operation);
			}
		}
	}


	/**
	 * initialize events for all global operations events
	 */
	protected function initializeGlobalOperationListeners()
	{
		foreach($this->objDefinition->getGlobalOperationNames() as $operation)
		{
			$eventName = sprintf('dcatools.%s.global_operations.%s', $this->objDefinition->getName(), $operation);
			if(isset($GLOBALS['TL_EVENTS'][$eventName]))
			{
				$this->enableGlobalOperationEvents($operation);
			}
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
		$objEvent = new RestrictedDataAccessEvent($this, $strParent);
		$this->objDispatcher->dispatch('getAllowedIds', $objEvent);

		return $objEvent->getEntries();
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
			throw new \RuntimeException("DataContainer '{$this->objDefinition->getName()}' does not have dynamic ptables");
		}

		$objEvent = new RestrictedDataAccessEvent($this);
		$this->objDispatcher->dispatch('dcatools.tl_content.getAllowedDynamicParents', $objEvent);

		return $objEvent->getEntries();
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
		$objEvent = new RestrictedDataAccessEvent($this, $strParent, $arrFields);
		$this->objDispatcher->dispatch('getAllowedEntries', $objEvent);

		return $objEvent->getEntries();
	}


	/**
	 * Get name of parent table
	 *
	 * @param $module
	 *
	 * @return string|false
	 */
	public function getParentName($module=null)
	{
		if($this->objDefinition->get('config/dynamicPtable'))
		{
			$eventName = sprintf('dcatools.%s.getDynamicParent', $this->$this->objDefinition->getName());
			$event = new GetDynamicParentEvent($this, $module);

			$this->objDispatcher->dispatch($eventName, $event);
			return $event->getParentName();
		}
		elseif($this->objDefinition->get('config/ptable'))
		{
			return $this->objDefinition->get('config/ptable');
		}

		return false;
	}


	/**
	 * @return EventDispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->objDispatcher;
	}


	/**
	 * @param $operation
	 * @param $listener
	 * @param int $priority
	 */
	public function addOperationListener($operation, $listener, $priority=0)
	{
		$this->enableOperationEvents($operation);

		$eventName = sprintf('dcatools.%s.operation.%s', $this->objDefinition->getName(), $operation);
		$this->objDispatcher->addListener($eventName, $listener, $priority);
	}


	/**
	 * @param $operation
	 * @param $listener
	 * @param int $priority
	 */
	public function addGlobalOperationListener($operation, $listener, $priority=0)
	{
		$this->enableGlobalOperationEvents($operation);

		$eventName = sprintf('dcatools.%s.global_operation.%s', $this->objDefinition->getName(), $operation);
		$this->objDispatcher->addListener($eventName, $listener, $priority);
	}


	/**
	 * @param $operation
	 */
	public function enableOperationEvents($operation)
	{
		if(!isset($this->enabledOperations[$operation]))
		{
			$config = $this->objDefinition->get('list/operations/' . $operation);

			$this->enabledOperations[$operation] = true;

			if(isset($config['button_callback']))
			{
				$this->getEventDispatcher()->addListener(
					'dcatools.operation.' . $operation,
					function($objEvent) use($config)
					{
						Contao::generateOperation($objEvent, $config['button_callback']);
					},
					Priority::CALLBACK
				);
			}

			$this->objDefinition->set(sprintf('list/operations/%s/button_callback', $operation), array
			(
				'DcaTools\Bridge', 'operationCallback' . $operation
			));
		}
	}


	/**
	 * @param $operation
	 */
	public function enableGlobalOperationEvents($operation)
	{
		if(!isset($this->enabledGlobalOperations[$operation]))
		{
			$config = $this->objDefinition->get('list/global_operations/' . $operation);

			$this->enabledGlobalOperations[$operation] = true;

			if(isset($config['button_callback']))
			{
				$this->getEventDispatcher()->addListener(
					'dcatools.operation.' . $operation,
					function($objEvent) use($config)
					{
						Contao::generateOperation($objEvent, $config['button_callback']);
					},
					Priority::CALLBACK
				);
			}

			$this->objDefinition->set(sprintf('list/global_operations/%s/button_callback', $operation), array
			(
				'DcaTools\Bridge', 'globalOperationCallback' . $operation
			));
		}
	}


	/**
	 * Trigger an error
	 *
	 * @param $message
	 * @param bool $redirect
	 */
	public static function error($message, $redirect=true)
	{
		$arrDebug = debug_backtrace();
		$strCall = $arrDebug[1]['class'] . ' ' .$arrDebug[1]['function'];

		\Controller::log($message, $strCall, 'TL_ERROR');

		if($redirect)
		{
			\Controller::redirect('contao/main.php?act=error');
		}
	}


	/**
	 * @return string
	 */
	protected function getDefaultError()
	{
		if(\Input::get('act') != '')
		{
			$defaultError = sprintf(
				'User "%s" has not enough permission to run action "%s" for DataContainer "%s"',
				\BackendUser::getInstance()->username,
				\Input::get('act'),
				$this->objDefinition->getName()
			);

			if(\Input::get('id') != '')
			{
				$defaultError .= ' on item with ID "' .\Input::get('id') . '"';
			}
		}
		else
		{
			$defaultError = sprintf(
				'User "%s" has not enough permission to access module "%s"',
				\BackendUser::getInstance()->username,
				\Input::get('do')
			);
		}

		return $defaultError;
	}

}
