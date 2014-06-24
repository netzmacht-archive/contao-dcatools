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

namespace deprecated\DcaTools;

use \deprecated\DcaTools\Definition;
use \deprecated\DcaTools\Event\CheckPermissionEvent;
use \deprecated\DcaTools\Event\DcaToolsEvent;
use \deprecated\DcaTools\Helper\Formatter;
use \deprecated\DcaTools\Listener\ContaoListener;
use \deprecated\DcaTools\Event\GetDynamicParentEvent;
use \deprecated\DcaTools\Event\RestrictedDataAccessEvent;
use DcGeneral\Data\ModelInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;


/**
 * Class DataContainer
 * @package DcaTools\Component
 */
class DcaTools
{

	/**
	 * event priorities
	 */
	const PRIORITY_HIGH  = 1;

	const PRIORITY_NORMAL  = 0;

	const PRIORITY_LOW     = -1;


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
	protected static $instances = array();

	/**
	 * @var \deprecated\DcaTools\Definition\DataContainer
	 */
	protected $definition;

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	protected $dispatcher;

	/**
	 * @var bool
	 */
	protected $initialized;


	/**
	 * @param $name
	 *
	 * @return DcaTools
	 */
	public static function getInstance($name)
	{
		global $container;

		if(!isset(static::$instances[$name]))
		{
			static::$instances[$name] = new static($name, $container['event-dispatcher']);
		}

		return static::$instances[$name];
	}


	/**
	 * @param string $name
	 * @param $dispatcher
	 */
	protected function __construct($name, $dispatcher)
	{
		$this->definition = Definition::getDataContainer($name);
		$this->dispatcher = $dispatcher;
	}


	/**
	 * Initialize DataContainer
	 *
	 * @param ModelInterface $model
	 */
	public function initialize(ModelInterface $model)
	{
		if(!$this->initialized)
		{
			$this->initializeEventListeners();

			$this->triggerInitializeEvent();
			$this->triggerCheckPermissionEvent($model);

			$this->initialized = true;
		}
	}


	/**
	 * @return Definition\DataContainer
	 */
	public function getDefinition()
	{
		return $this->definition;
	}


	/**
	 * @return Formatter
	 */
	public function getFormatter()
	{
		return Formatter::create($this->definition->getName());
	}


	/**
	 * Trigger initialize event
	 */
	protected function triggerInitializeEvent()
	{
		$event     = new DcaToolsEvent($this);
		$eventName = sprintf('dcatools.%s.initialize', $this->definition->getName());
		$this->dispatcher->dispatch($eventName, $event);
	}


	/**
	 * Trigger check permission event
	 *
	 * @param ModelInterface $model
	 */
	protected function triggerCheckPermissionEvent(ModelInterface $model)
	{
		$event     = new CheckPermissionEvent($this, $model);
		$eventName = sprintf('dcatools.%s.check-permission', $this->definition->getName());

		$this->dispatcher->dispatch($eventName, $event);

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
		foreach($this->definition->get('dcatools') as $event => $listeners) {
			foreach($listeners as $listener) {
				$config   = null;
				$priority = 0;

				// detect config and priority
				if(is_array($listener)) {
					// listener, config and priority given
					if(count($listener) == 3 && is_array($listener[1])) {
						list($listener, $config, $priority) = $listener;
					}
					elseif(count($listener) == 2) {
						// listener and config given
						if(is_array($listener[1])) {
							list($listener, $config) = $listener;
						}
						// listener and priority given
						elseif(is_callable($listener[0]) && is_int($listener[1])) {
							list($listener, $priority) = $listener;
						}
					}
				}

				// create configurable listener
				if($config) {
					$listener = Helper\EventListener::createConfigurableListener($listener, $config);
				}

				$this->dispatcher->addListener($event, $listener, $priority);
				$chunks = explode('.', $event);

				if(isset($chunks[2])) {
					if($chunks[2] == 'operation') {
						$this->enableOperationEvents($chunks[3]);
					}

					if($chunks[2] == 'global_operation') {
						$this->enableGlobalOperationEvents($chunks[3]);
					}
				}
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
		$this->dispatcher->dispatch('getAllowedIds', $objEvent);

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
		if(!$this->definition->getFromDefinition('config/dynamicPtable'))
		{
			throw new \RuntimeException("DataContainer '{$this->definition->getName()}' does not have dynamic ptables");
		}

		$objEvent = new RestrictedDataAccessEvent($this);
		$this->dispatcher->dispatch('dcatools.tl_content.getAllowedDynamicParents', $objEvent);

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
		$this->dispatcher->dispatch('getAllowedEntries', $objEvent);

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
		if($this->definition->get('config/dynamicPtable'))
		{
			$eventName = sprintf('dcatools.%s.getDynamicParent', $this->$this->definition->getName());
			$event = new GetDynamicParentEvent($this, $module);

			$this->dispatcher->dispatch($eventName, $event);
			return $event->getParentName();
		}
		elseif($this->definition->get('config/ptable'))
		{
			return $this->definition->get('config/ptable');
		}

		return false;
	}


	/**
	 * @return EventDispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->dispatcher;
	}


	/**
	 * @param $operation
	 * @param $listener
	 * @param int $priority
	 */
	public function addOperationListener($operation, $listener, $priority=0)
	{
		$this->enableOperationEvents($operation);

		$eventName = sprintf('dcatools.%s.operation.%s', $this->definition->getName(), $operation);
		$this->dispatcher->addListener($eventName, $listener, $priority);
	}


	/**
	 * @param $operation
	 * @param $listener
	 * @param int $priority
	 */
	public function addGlobalOperationListener($operation, $listener, $priority=0)
	{
		$this->enableGlobalOperationEvents($operation);

		$eventName = sprintf('dcatools.%s.global_operation.%s', $this->definition->getName(), $operation);
		$this->dispatcher->addListener($eventName, $listener, $priority);
	}


	/**
	 * @param $operation
	 */
	public function enableOperationEvents($operation)
	{
		if(!isset($this->enabledOperations[$operation]))
		{
			$config = $this->definition->get('list/operations/' . $operation);

			$this->enabledOperations[$operation] = true;

			if(isset($config['button_callback']))
			{
				$this->addOperationListener($operation,
					function ($objEvent) use($config) {
						ContaoListener::generateOperation($objEvent, $config['button_callback']);
					},
					static::PRIORITY_LOW
				);
			}

			$this->definition->set(sprintf('list/operations/%s/button_callback', $operation), array
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
			$config = $this->definition->get('list/global_operations/' . $operation);

			$this->enabledGlobalOperations[$operation] = true;

			if(isset($config['button_callback']))
			{
				$this->addGlobalOperationListener($operation, function ($objEvent) use($config) {
						ContaoListener::generateOperation($objEvent, $config['button_callback']);
					},
					static::PRIORITY_LOW
				);
			}

			$this->definition->set(sprintf('list/global_operations/%s/button_callback', $operation), array
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
				$this->definition->getName()
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
