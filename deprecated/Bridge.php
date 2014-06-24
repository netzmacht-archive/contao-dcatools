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

use deprecated\DcaTools\Component\Operation;
use deprecated\DcaTools\Component\GlobalOperation;
use deprecated\DcaTools\Data\ModelFactory;
use DcGeneral\DC_General;


/**
 * Class Bridge connects DcaTools to the default Contao callbacks
 *
 * @package DcaTools
 */
class Bridge
{

	/**
	 * @param $name
	 */
	public function hookLoadDataContainer($name)
	{
		if(isset($GLOBALS['TL_DCA'][$name]['dcatools'])) {
			$definition = Definition::getDataContainer($name);
			$definition->registerCallback('onload', array('\DcaTools\Bridge', 'callbackInitialize'));

			if($definition->get('config/dataContainer') == 'General') {
				$definition->registerCallback('onload', array('\DcaTools\Bridge', 'callbackDcGeneralOnLoad'));
			}
		}
	}


	/**
	 * @param $dc
	 */
	public function callbackInitialize($dc)
	{
		$table = get_class($dc) == 'DcGeneral\DC_General' ? $dc->getTable() : $dc->table;
		$model = ModelFactory::byDc($dc, true);

		$controller = $controller = DcaTools::getInstance($table);
		$controller->initialize($model);
	}


	/**
	 * Do not use built compatibility driver manager, use DC_General instead
	 *
	 * @param DC_General $dc
	 */
	public function callbackDcGeneralOnLoad(DC_General $dc)
	{
		/** @var \Pimple $container */
		global $container;

		// no need for own driver manager, use Dc_General
		$GLOBALS['container']['dcatools.driver-manager'] = $container->share(function() use($dc) {
			return $dc;
		});
	}


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
		if (strncmp($method, 'operationCallback', 17) === 0) {
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
		elseif (strncmp($method, 'globalOperationCallback', 23) === 0) {
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

}
