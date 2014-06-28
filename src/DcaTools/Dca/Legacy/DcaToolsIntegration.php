<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Legacy;


use ContaoCommunityAlliance\DcGeneral\DC_General;
use ContaoCommunityAlliance\DcGeneral\DcGeneral;
use ContaoCommunityAlliance\DcGeneral\Factory\DcGeneralFactory;
use DcaTools\Definition\DcaToolsDefinition;
use DcaTools\Exception\InvalidArgumentException;
use DcaTools\View\ViewHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DcaToolsIntegration works as connector between the dca callbacks and the tools DcaTools provides
 *
 * @package DcaTools\Dca
 */
class DcaToolsIntegration
{

	/**
	 * @var DcaToolsIntegration
	 */
	private static $instance;


	/**
	 * Singleton to make sure every callback interacts with this instance
	 *
	 * @return DcaToolsIntegration
	 */
	public static function getInstance()
	{
		if(!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @var CallbackManager
	 */
	private $callbackManager;


	/**
	 * @var CallbackDispatcher
	 */
	private $callbackDispatcher;


	/**
	 * @param $name
	 */
	public function onLoadDataContainer($name)
	{
		// dcatools is not enabled
		if(!isset($GLOBALS['TL_DCA'][$name]['dcatools']) || !isset($GLOBALS['TL_DCA'][$name]['dcatools']['legacy']) ||
			!$GLOBALS['TL_DCA'][$name]['dcatools']['legacy']) {
			return;
		}

		// no not activate for dc general
		if($GLOBALS['TL_DCA'][$name]['config']['dataContainer'] == 'DC_General') {
			return;
		}

		// set initilialize instance
		$GLOBALS['TL_DCA'][$name]['config']['onload_callback']['dcatools'] = array(get_called_class(), 'initialize');
	}


	/**
	 * @param \DataContainer $dataContainer
	 */
	public function initialize(\DataContainer $dataContainer)
	{
		// check again against dc general to avoid that a subclass would be used
		if(!$dataContainer instanceof DC_General) {
			$dcGeneral = $this->createDcGeneral($dataContainer->table);

			/** @var \Pimple $container */
			global $container;

			/** @var ViewHelper $viewHelper */
			$viewHelper = $container['dcatools.view-helper'];
			$viewHelper->setEnvironment($dcGeneral->getEnvironment());

			$this->callbackDispatcher = new CallbackDispatcher($dcGeneral, $viewHelper);
			$this->initializeCallbackManager($dcGeneral);

			$this->callbackDispatcher->containerOnLoad($dataContainer);
		}
	}


	/**
	 * Create instance of DcGeneral.
	 *
	 * Not only environment is required because ContainerOnLoadEvent requires the DcGeneral
	 * (in fact it just stores the environment)
	 *
	 * @param $name
	 * @return \ContaoCommunityAlliance\DcGeneral\DcGeneral
	 */
	private function createDcGeneral($name)
	{
		/** @var \Pimple $container */
		global $container;

		$factory   = new DcGeneralFactory();
		$dcGeneral = $factory
			->setEventPropagator($container['dcatools.event-propagator'])
			->setTranslator($container['dcatools.translator'])
			->setContainerName($name)
			->createDcGeneral();

		return $dcGeneral;
	}


	/**
	 * @param DcGeneral $dcGeneral
	 */
	private function initializeCallbackManager(DcGeneral $dcGeneral)
	{
		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher    	  = $GLOBALS['container']['event-dispatcher'];
		$this->callbackManager    = new CallbackManager(get_called_class());

		/** @var DcaToolsDefinition $definition */
		$definition = $dcGeneral->getEnvironment()->getDataDefinition()->getDefinition(DcaToolsDefinition::NAME);
		$name       = $dcGeneral->getEnvironment()->getDataDefinition()->getName();

		foreach($definition->getCallbacks() as $callback => $for) {
			if(!$for) {
				$this->callbackManager->enableCallback($callback, $name);
				continue;
			}

			foreach((array)$for as $value) {
				$this->callbackManager->enableCallback($callback, $name, $value);
			}
		}
	}


	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 * @throws \DcaTools\Exception\InvalidArgumentException
	 */
	public function __call($method, $arguments)
	{
		// extract button name
		if(strncmp($method, 'modelOperationButton', 20) === 0 || strncmp($method, 'containerGlobalButton', 21) === 0) {
			list($method, $button) = explode('_', $method, 2);
			array_unshift($arguments, $button);
		}

		if(!method_exists($this->callbackDispatcher, $method)) {
			throw new InvalidArgumentException('Method does not exists', 0, null, $method);
		}

		return call_user_func_array(array($this->callbackDispatcher, $method), $arguments);
	}

} 