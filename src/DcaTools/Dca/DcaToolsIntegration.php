<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca;


use ContaoCommunityAlliance\DcGeneral\DC_General;
use ContaoCommunityAlliance\DcGeneral\DcGeneral;
use ContaoCommunityAlliance\DcGeneral\Factory\DcGeneralFactory;
use DcaTools\Assertion;
use DcaTools\Dca\Callback\CallbackDispatcher;
use DcaTools\Dca\Callback\CallbackManager;
use DcaTools\Event\InitializeCallbackManagerEvent;
use DcaTools\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
		if(!isset($GLOBALS['TL_DCA'][$name]['dcatools']) || !isset($GLOBALS['TL_DCA'][$name]['dcatools']['enabled'])) {
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
		if(!$dataContainer instanceof DC_General) {
			$dcGeneral = $this->createDcGeneral($dataContainer->name);

			$this->initializeCallbackManager($dcGeneral);
		}
	}


	/**
	 * @param $name
	 * @return \ContaoCommunityAlliance\DcGeneral\DcGeneral
	 */
	private function createDcGeneral($name)
	{
		/** @var \Pimple $container */
		$container = $GLOBALS['container'];
		$factory   = new DcGeneralFactory();
		$dcGeneral = $factory
			->setEventPropagator($container['dcatools.event-propagator'])
			->setTranslator($container['dcatools.translator'])
			->setContainerName($name)
			->createDcGeneral();

		return $dcGeneral;
	}

	/**
	 * @param DcGeneral $dataContainer
	 */
	private function initializeCallbackManager(DcGeneral $dataContainer)
	{
		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher    = $GLOBALS['container']['event-dispatcher'];
		$callbackDispatcher = new CallbackDispatcher($dataContainer);
		$callbackManager    = new CallbackManager($callbackDispatcher, get_called_class());

		$event = new InitializeCallbackManagerEvent($callbackManager);
		$eventDispatcher->dispatch($event::NAME, $event);

		$this->callbackManager    = $event->getCallbackManager();
		$this->callbackDispatcher = $callbackDispatcher;
	}


	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 * @throws \DcaTools\Exception\InvalidArgumentException
	 */
	public function __call($method, $arguments)
	{
		if(!method_exists($this->callbackDispatcher, $method)) {
			throw new InvalidArgumentException('Method does not exists', $method);
		}

		return call_user_func_array(array($this, $method), $arguments);
	}

} 