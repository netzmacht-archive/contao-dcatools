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


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Assertion;
use DcaTools\Condition\Command\CommandConditionFactory;
use DcaTools\Dca\Button;
use DcaTools\Definition\Command\CommandFilter;
use DcaTools\User\User;


class HasAccessCondition extends AbstractCondition
{
	/**
	 * @var string
	 */
	private $action;

	/**
	 * @var string
	 */
	private $domain;


	/**
	 * @param array $config
	 * @param CommandFilter $filter
	 * @param CommandConditionFactory $factory
	 *
	 * @return static
	 */
	public static function fromConfig(array $config, CommandFilter $filter=null, CommandConditionFactory $factory)
	{
		$condition = parent::fromConfig($config, $filter, $factory);

		Assertion::keyExists($config, 'action', 'No action defined');
		Assertion::keyExists($config, 'domain', 'No domain defined');

		/** @var HasAccessCondition $condition */
		$condition
			->setAction($config['action'])
			->setDomain($config['domain']);

		return $condition;
	}


	/**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function execute(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model=null)
	{
		return $user->hasRole($this->action, $this->domain);
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


	/**
	 * @param string $domain
	 *
	 * @return $this
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getDomain()
	{
		return $this->domain;
	}

} 