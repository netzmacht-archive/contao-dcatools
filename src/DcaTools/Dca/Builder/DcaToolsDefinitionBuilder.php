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
use DcaTools\Assertion;
use DcaTools\Definition\Command\CommandCondition;
use DcaTools\Definition\Command\Condition;
use DcaTools\Definition\Command\CommandConditions;
use DcaTools\Definition\DcaToolsDefinition;
use DcaTools\Definition\Permission\PermissionConditions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class DcaToolsDefinitionBuilder extends DcaReadingDataDefinitionBuilder
{

	/**
	 * @var array
	 */
	private $commandConditions;

	/**
	 * @var array
	 */
	private $permissionConditions;


	/**
	 * @param array $commandConditions
	 * @param array $permissionConditions
	 * @internal param \DcaTools\User\User $user
	 */
	function __construct(array $commandConditions, array $permissionConditions)
	{
		$this->commandConditions    = $commandConditions;
		$this->permissionConditions = $permissionConditions;
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
			$condition = $this->createCommandCondition(
				$definition['condition'],
				(array) $definition['config'],
				(array) $definition['filter']
			);

			$conditions->addCondition($condition);
		}
	}


	/**
	 * @param $condition
	 * @param array $config
	 * @param array $filter
	 * @return CommandCondition
	 */
	private function createCommandCondition($condition, array $config, array $filter)
	{
		if(isset($this->commandConditions[$condition])) {
			$condition = $this->commandConditions[$condition];

			if(isset($config['condition'])) {
				$config['condition'] = $this->createCommandCondition(
					$config['condition'],
					(array) $config['config'],
					(array) $config['filter']
				);
			}

			if(is_callable($condition)) {
				return call_user_func($condition, $config, $filter);
			}
		}

		Assertion::classExists($condition, 'Condition class does not exists');
		return new $condition($config, $filter);
	}


	/**
	 * @param \DcaTools\Definition\Permission\PermissionConditions $conditions
	 * @param $definitions
	 */
	private function buildPermissionConditions(PermissionConditions $conditions, $definitions)
	{
		foreach($definitions as $definition) {
			$condition = $this->createPermissionCondition(
				$definition['condition'],
				(array) $definition['config'],
				(array) $definition['filter']
			);

			$conditions->addCondition($condition);
		}
	}


	/**
	 * @param $condition
	 * @param array $config
	 * @param array $filter
	 * @return mixed
	 */
	private function createPermissionCondition($condition, array $config, array $filter)
	{
		if(isset($this->permissionConditions[$condition])) {
			$condition = $this->permissionConditions[$condition];

			if(isset($config['conditions'])) {
				$children = array();

				foreach($config['conditions'] as $child) {
					$children[] = $this->createPermissionCondition(
						$child['condition'],
						(array) $child['config'],
						(array) $child['filter']
					);
				}

				$config['conditions'] = $children;
			}

			if(is_callable($condition)) {
				return call_user_func($condition, $config, $filter);
			}
		}

		Assertion::classExists($condition, 'Condition class does not exists');
		return new $condition($config, $filter);
	}

} 