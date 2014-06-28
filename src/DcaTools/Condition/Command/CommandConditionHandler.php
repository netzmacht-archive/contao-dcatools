<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Command;


use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetOperationButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;
use DcaTools\Dca\Button;
use DcaTools\View\ButtonRenderer;
use DcaTools\Condition\Command;
use DcaTools\Definition\DcaToolsDefinition;


class CommandConditionHandler
{
	const PRIORITY = 10000;

	/**
	 * @var string
	 */
	private static $eventNamePattern = '%s[%s][%s]';

	/**
	 * @var ButtonRenderer
	 */
	private $renderer;


	/**
	 * @param ButtonRenderer $renderer
	 */
	function __construct(ButtonRenderer $renderer)
	{
		$this->renderer = $renderer;
	}


	/**
	 * @param BuildDataDefinitionEvent $event
	 */
	public function setUp(BuildDataDefinitionEvent $event)
	{
		/** @var Contao2BackendViewDefinitionInterface $definition */
		$definition = $event->getContainer()->getDefinition(Contao2BackendViewDefinitionInterface::NAME);
		$dispatcher = $event->getDispatcher();
		$listener   = array($this, 'execute');

		foreach($definition->getModelCommands()->getCommands() as $command) {
			$eventName = sprintf(
				static::$eventNamePattern,
				GetOperationButtonEvent::NAME,
				$event->getContainer()->getName(),
				$command->getName()
			);

			$dispatcher->addListener($eventName, $listener, static::PRIORITY);
		}
	}


	/**
	 * @param GetOperationButtonEvent $event
	 */
	public function execute(GetOperationButtonEvent $event)
	{
		$definition = $event->getEnvironment()->getDataDefinition();

		if(!$definition->hasDefinition(DcaToolsDefinition::NAME)) {
			return;
		}

		/** @var DcaToolsDefinition $dcaTools */
		$dcaTools   = $definition->getDefinition(DcaToolsDefinition::NAME);
		$conditions = $dcaTools->getCommandConditions()->getConditions($event->getCommand()->getName());

		if(!$conditions) {
			return;
		}

		$this->render($event, $conditions);
	}


	/**
	 * @param GetOperationButtonEvent $event
	 * @param CommandCondition[] $conditions
	 */
	private function render(GetOperationButtonEvent $event, array $conditions)
	{
		$button = new Button($event);
		$model  = $event->getModel();
		$input  = $event->getEnvironment()->getInputProvider();

		foreach($conditions as $condition) {
			if($condition->__invoke($button, $input, $model)) {
				break;
			}
		}

		$html = $this->renderer->render($button);

		if($html !== false) {
			$event->setHtml($html);
			$event->stopPropagation();
		}
	}
}