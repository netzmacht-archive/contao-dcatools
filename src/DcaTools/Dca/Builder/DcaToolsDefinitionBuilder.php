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
use DcaTools\Condition\Command\CommandCondition;
use DcaTools\Config\Map;
use DcaTools\Condition\Command;
use DcaTools\Definition\CommandConditionCollection;
use DcaTools\Definition\DcaToolsDefinition;
use DcaTools\Definition\PermissionConditionCollection;
use DcaTools\User\User;
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
	 * @var User
	 */
	private $user;


	/**
	 * @param User $user
	 * @param array $commandConditions
	 * @param array $permissionConditions
	 */
	function __construct(User $user, array $commandConditions, array $permissionConditions)
	{
		$this->user				    = $user;
		$this->commandConditions    = $commandConditions;
		$this->permissionConditions = $permissionConditions;
	}


	/**
	 * @param BuildDataDefinitionEvent $event
	 * @throws NotImplementedException
	 */
	public static function process(BuildDataDefinitionEvent $event)
	{
		//throw new NotImplementedException();
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
	 * @param \DcaTools\Definition\CommandConditionCollection $conditions
	 * @param $definitions
	 */
	private function buildCommandConditions(CommandConditionCollection $conditions, $definitions)
	{
		foreach($definitions as $definition) {
			$condition = $this->createCommandCondition($definition['condition'], $definition['config']);
			$conditions->addCondition($condition, $definition['filter']);
		}
	}


	/**
	 * @param $condition
	 * @param $config
	 * @return CommandCondition
	 */
	private function createCommandCondition($condition, $config)
	{
		if(isset($this->commandConditions[$condition])) {
			$condition = $this->commandConditions[$condition];

			if(isset($config['condition'])) {
				$config['condition'] = $this->createCommandCondition($config['condition'], (array) $config['config']);
			}

			if(is_callable($condition)) {
				return call_user_func($condition, $config);
			}
		}

		Assertion::classExists($condition, 'Condition class does not exists');
		return new $condition($config);
	}


	/**
	 * @param \DcaTools\Definition\PermissionConditionCollection $conditions
	 * @param $definitions
	 */
	private function buildPermissionConditions(PermissionConditionCollection $conditions, $definitions)
	{
		foreach($definitions as $definition) {
			$condition = $this->createPermissionCondition($definition['condition'], $definition['config']);
			$conditions->addCondition($condition);
		}
	}


	/**
	 * @param $condition
	 * @param $config
	 * @return mixed
	 */
	private function createPermissionCondition($condition, $config)
	{
		if(isset($this->permissionConditions[$condition])) {
			$condition = $this->permissionConditions[$condition];

			if(isset($config['conditions'])) {
				$children = array();

				foreach($config['conditions'] as $condition) {
					$children[] = $this->createPermissionCondition($condition['condition'], (array) $condition['config']);
				}

				$config['conditions'] = $children;
			}

			if(is_callable($condition)) {
				return call_user_func($condition, $this->user, $config);
			}
		}

		Assertion::classExists($condition, 'Condition class does not exists');
		return new $condition($config);
	}

} 