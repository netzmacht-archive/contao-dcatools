<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command\Condition;


use Assert\Assertion;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Condition\Command\CommandConditionFactory;
use DcaTools\Dca\Button;
use DcaTools\Definition\Command\CommandFilter;
use DcaTools\User\User;


class IsAllowedCondition extends AbstractCondition
{
	/**
	 * @var string
	 */
	private $action;


	/**
	 * @param array $config
	 * @param \DcaTools\Definition\Command\CommandFilter $filter
	 * @param CommandConditionFactory $factory
	 *
	 * @return static
	 */
	public static function fromConfig(array $config, CommandFilter $filter=null, CommandConditionFactory $factory)
	{
		Assertion::keyExists($config, 'action', 'Action has to be defined');

		/** @var IsAllowedCondition $condition */
		$condition = new static($filter);
		$condition->setAction($config['action']);

		return $condition;
	}


	/**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function execute(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null)
	{
		Assertion::notNull($model, 'isAllowed rule can only be applied to model commands');

		return $user->isAllowed($this->action, $model);
	}


	/**
	 * @param string $action
	 *
	 * @return $this
	 */
	public function setAction($action)
	{
		$this->action = $action;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

} 