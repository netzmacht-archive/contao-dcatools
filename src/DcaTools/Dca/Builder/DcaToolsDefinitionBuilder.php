<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Builder;

use ContaoCommunityAlliance\DcGeneral\Contao\Dca\Builder\Legacy\DcaReadingDataDefinitionBuilder;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\ContainerInterface;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;
use DcaTools\Condition\Command\CommandConditionFactory;
use DcaTools\Condition\Permission\PermissionConditionFactory;
use DcaTools\Definition\Command\Condition;
use DcaTools\Definition\Command\CommandConditions;
use DcaTools\Definition\DcaToolsDefinition;
use DcaTools\Definition\Permission\PermissionConditions;

class DcaToolsDefinitionBuilder extends DcaReadingDataDefinitionBuilder
{

	/**
	 * @var CommandConditionFactory
	 */
	private $commandConditionFactory;

	/**
	 * @var PermissionConditionFactory
	 */
	private $permissionConditionFactory;


	/**
	 * @param CommandConditionFactory $commandConditionFactory
	 * @param PermissionConditionFactory $permissionConditionFactory
	 */
	function __construct(CommandConditionFactory $commandConditionFactory, PermissionConditionFactory $permissionConditionFactory)
	{
		$this->commandConditionFactory = $commandConditionFactory;
		$this->permissionConditionFactory = $permissionConditionFactory;
	}


	/**
	 * Build a data definition and store it into the environments container.
	 *
	 * @param ContainerInterface $container The data definition container to populate.
	 *
	 * @param BuildDataDefinitionEvent $event The event that has been triggered.
	 *
	 * @return void
	 */
	public function build(ContainerInterface $container, BuildDataDefinitionEvent $event)
	{
		$definition = new DcaToolsDefinition();
		$this->loadDca($container->getName(), $event->getDispatcher());

		$definition->setLegacyMode($this->getFromDca('dcatools/legacy'));
		$definition->setCallbacks((array)$this->getFromDca('dcatools/callbacks'));

		$this->buildCommandConditions(
			$definition->getCommandConditions(),
			(array) $this->getFromDca('dcatools/command_conditions')
		);

		$this->buildPermissionConditions(
			$definition->getPermissionConditions(),
			(array) $this->getFromDca('dcatools/permission_conditions')
		);

		$container->setDefinition($definition::NAME, $definition);
	}


	/**
	 * @param \DcaTools\Definition\Command\CommandConditions $conditions
	 * @param $definitions
	 */
	private function buildCommandConditions(CommandConditions $conditions, $definitions)
	{
		foreach($definitions as $definition) {
			$condition = $this->commandConditionFactory->createFromConfig($definition);

			$conditions->addCondition($condition);
		}
	}


	/**
	 * @param \DcaTools\Definition\Permission\PermissionConditions $conditions
	 * @param $definitions
	 */
	private function buildPermissionConditions(PermissionConditions $conditions, $definitions)
	{
		foreach($definitions as $definition) {
			$condition = $condition = $this->permissionConditionFactory->createFromConfig($definition);

			$conditions->addCondition($condition);
		}
	}

} 