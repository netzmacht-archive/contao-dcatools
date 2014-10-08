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
use DcaTools\Definition\Command\CommandCondition;
use DcaTools\User\User;
use DcaTools\Definition\Command\Condition;
use DcaTools\Definition\DcaToolsDefinition;

class CommandConditionHandler
{
    const PRIORITY = 1000;

    /**
	 * @var string
	 */
    private static $eventNamePattern = '%s[%s][%s]';

    /**
	 * @var User
	 */
    private $user;

    /**
	 * @param \DcaTools\User\User $user
	 */
    public function __construct(User $user)
    {
        $this->user = $user;
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

        foreach ($definition->getModelCommands()->getCommands() as $command) {
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

        if (!$definition->hasDefinition(DcaToolsDefinition::NAME)) {
            return;
        }

        /** @var DcaToolsDefinition $dcaTools */
        $dcaTools   = $definition->getDefinition(DcaToolsDefinition::NAME);
        $conditions = $dcaTools->getCommandConditions()->getConditions();

        if (!$conditions) {
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
        $button      = new Button($event);
        $model       = $event->getModel();
        $environment = $event->getEnvironment();

        foreach ($conditions as $condition) {
            if (!$condition->match($button, $environment, $this->user, $model)) {
                break;
            }
        }

        if (!$button->isVisible()) {
            $event->setHtml('');
            $event->stopPropagation();
        }
    }
}
