<?php

/**
 * @package    contao-dcatools
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Button\Condition;


use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BaseButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGlobalButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetOperationButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use DcaTools\Assertion;
use DcaTools\Config\Map;
use DcaTools\Dca\Button;
use DcaTools\Dca\Button\ButtonRenderer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * Class ConditionManager
 * @package DcaTools\Dca\Command\Condition
 */
class ConditionManager
{
	/**
	 * @var EventDispatcherInterface
	 */
	private $eventDispatcher;

	/**
	 * @var InputProviderInterface
	 */
	private $inputProvider;

	/**
	 * @var string
	 */
	private $eventNamePattern = '%s[%s][%s]';

	/**
	 * @var Map
	 */
	private $map;

	/**
	 * @var bool[]
	 */
	private $conditions = array();

	/**
	 * @var
	 */
	private $renderer;

	/**
	 * @var int
	 */
	private $eventPriority;


	/**
	 * @param EventDispatcherInterface $eventDispatcher
	 * @param \DcaTools\Config\Map $map
	 * @param \DcaTools\Dca\Button\ButtonRenderer $renderer
	 * @param InputProviderInterface $inputProvider
	 * @param $eventNamePattern
	 * @param int $eventPriority
	 */
	function __construct(
		EventDispatcherInterface $eventDispatcher,
		Map $map,
		ButtonRenderer $renderer,
		InputProviderInterface $inputProvider,
		$eventNamePattern=null,
		$eventPriority=100)
	{
		$this->eventDispatcher  = $eventDispatcher;
		$this->map              = $map;
		$this->renderer		    = $renderer;
		$this->eventPriority	= $eventPriority;
		$this->inputProvider    = $inputProvider;

		if($eventNamePattern) {
			$this->eventNamePattern = $eventNamePattern;
		}
	}


	/**
	 * @param $dataContainerName
	 * @param $commandType
	 * @param $commandName
	 * @param $conditionName
	 * @param array $arguments
	 * @return $this
	 */
	public function addCondition($dataContainerName, $commandType, $commandName, $conditionName, $arguments=array())
	{
		Assertion::keyExists($this->map, $conditionName, 'Invalid command condition. "' . $conditionName . '" is not registered.');

		if(!isset($this->conditions[$dataContainerName][$commandType][$commandName])) {
			$this->createEvent($dataContainerName, $commandType, $commandName);
		}

		$this->conditions[$dataContainerName][$commandType][$commandName][] = array($conditionName, $arguments);

		return $this;
	}


	/**
	 * @param $dataContainerName
	 * @param $commandType
	 * @param array $conditions
	 * @return $this
	 */
	public function addConditions($dataContainerName, $commandType, array $conditions)
	{
		foreach($conditions as $condition) {
			$arguments = isset($condition[2]) ? $condition[2] : array();
			$this->addCondition($dataContainerName, $commandType, $condition[0], $condition[1], $arguments);
		}

		return $this;
	}


	/**
	 * @param $dataContainerName
	 * @param $commandType
	 * @param $commandName
	 * @param \DcaTools\Dca\Button $button
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function executeConditions($dataContainerName, $commandType, $commandName, Button $button, ModelInterface $model=null)
	{
		$state = true;

		if(isset($this->conditions[$dataContainerName][$commandType][$commandName])) {
			foreach($this->conditions[$dataContainerName][$commandType][$commandName] as $condition) {
				if(!$this->handleCondition($condition, $button, $model)) {
					$state = false;
					break;
				}
			}
		}

		return $state;
	}


	/**
	 * @param $dataContainerName
	 * @return bool
	 */
	public function hasConditions($dataContainerName)
	{
		if(isset($this->conditions[$dataContainerName])) {
			return $this->conditions[$dataContainerName];
		}

		return false;
	}


	/**
	 * @param $condition
	 * @param \DcaTools\Dca\Button $button
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function handleCondition($condition, Button $button, ModelInterface $model=null)
	{
		list($name, $config) = $condition;
		$condition = $this->map[$name];
		$config    = (array) $config;

		if(is_callable($condition)) {
			$condition = call_user_func($condition, $this, $config);
		}
		else {
			$condition = new $condition($this, $config);
		}

		Assertion::isCallable($condition, 'Condition is not callable');

		return call_user_func($condition, $button, $this->inputProvider, $model);
	}


	/**
	 * @param $dataContainerName
	 * @param $commandType
	 * @param $commandName
	 */
	private function createEvent($dataContainerName, $commandType, $commandName)
	{
		$type      = $commandType == Button::OPERATION ? GetOperationButtonEvent::NAME : GetGlobalButtonEvent::NAME;
		$eventName = sprintf($this->eventNamePattern, $type, $dataContainerName, $commandName);
		$handler   = function(BaseButtonEvent $event) use($dataContainerName, $commandType, $commandName) {
			$button = new Button($event);
			$model  = null;

			if($event instanceof GetOperationButtonEvent) {
				$model = $event->getModel();
			}

			$this->executeConditions($dataContainerName, $commandType, $commandName, $button, $model);
			$html = $this->renderer->render($button);

			if($html !== false) {
				$event->setHtml($html);
				$event->stopPropagation();
			}
		};

		$this->eventDispatcher->addListener($eventName, $handler, $this->eventPriority);
	}

} 