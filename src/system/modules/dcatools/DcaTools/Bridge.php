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

use DcaTools\Component\Operation;
use DcaTools\Component\GlobalOperation;
use DcaTools\Data\ModelFactory;


/**
 * Class Bridge connects DcaTools to the default Contao callbacks
 *
 * @package DcaTools
 */
class Bridge
{
	protected static $enabledOperationEvents = array();

	/**
	 * Use magic stuff for generating operations
	 *
	 * @param $method
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		if (strncmp($method, 'operationCallback', 17) === 0)
		{
			$operationName = substr($method, 17);

			$definition = Definition::getOperation($arguments[6], $operationName);
			$dispatcher = $GLOBALS['container']['event-dispatcher'];
			$controller = new Operation\Controller($definition, $dispatcher);

			$row   = array_shift($arguments);
			$model = ModelFactory::create($arguments[5], $row['id'], $row);
			$controller->setModel($model);

			$view = new Operation\View();
			$view->setHref($arguments[0]);
			$view->setLabel($arguments[1]);
			$view->setTitle($arguments[2]);
			$view->setIcon($arguments[3]);
			$view->setAttributes($arguments[4]);
			$controller->setView($view);

			$config = array();
			$config['rootIds']           = $arguments[6];
			$config['childRecordIds']    = $arguments[7];
			$config['circularReference'] = $arguments[8];
			$config['previous']          = $arguments[9];
			$config['next']              = $arguments[10];
			$controller->setConfig($config);

			return $controller->generate();
		}
		elseif (strncmp($method, 'globalOperationCallback', 23) === 0)
		{
			$operationName = substr($method, 23);

			$definition = Definition::getGlobalOperation($arguments[5], $operationName);
			$dispatcher = $GLOBALS['container']['event-dispatcher'];
			$controller = new GlobalOperation\Controller($definition, $dispatcher);

			$view = new GlobalOperation\View();
			$view->setHref($arguments[0]);
			$view->setLabel($arguments[1]);
			$view->setTitle($arguments[2]);
			$view->setIcon($arguments[3]);
			$view->setAttributes($arguments[4]);
			$controller->setView($view);

			$config = array();
			$config['rootIds']           = $arguments[6];
			$config['childRecordIds']    = $arguments[7];
			$config['circularReference'] = $arguments[8];
			$config['previous']          = $arguments[9];
			$config['next']              = $arguments[10];
			$controller->setConfig($config);

			return $controller->generate();
		}

		return null;
	}


	/**
	 * Register button_callback if events are set
	 *
	 * @param string $strTable
	 */
	protected function registerOperationEvents($strTable)
	{
		if(!isset($GLOBALS['TL_EVENTS']))
		{
			return;
		}

		/** @var \DcaTools\Definition\DataContainer $definition */
		$controller = Controller::getInstance($strTable);
		$definition = $controller->getDefinition();

		foreach($definition->getOperationNames() as $operationName)
		{
			$eventName = sprintf('dcatools.%s.operation.%s', $strTable, $operationName);
			if(isset($GLOBALS['TL_EVENTS'][$eventName]))
			{
				$controller->enableOperationEvents($operationName);
			}
		}
	}

	/**
	 * Register button_callback if events are set
	 *
	 * @param string $strTable
	 */
	protected function registerGlobalOperationEvents($strTable)
	{
		if(!isset($GLOBALS['TL_EVENTS']))
		{
			return;
		}

		/** @var \DcaTools\Definition\DataContainer $definition */
		$controller = Controller::getInstance($strTable);
		$definition = $controller->getDefinition();

		foreach($definition->getOperationNames() as $operationName)
		{
			$eventName = sprintf('dcatools.%s.global_operation.%s', $strTable, $operationName);
			if(isset($GLOBALS['TL_EVENTS'][$eventName]))
			{
				$controller->enableGlobalOperationEvents($operationName);
			}
		}
	}

	/**
	 * @param $name
	 */
	public function hookLoadDataContainer($name)
	{
		$controller = $controller = Controller::getInstance($name);
		$controller->initialize();
	}

}