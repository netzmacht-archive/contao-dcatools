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
use DcaTools\Definition\Command\CommandCondition;
use DcaTools\Definition\Command\Condition;
use DcaTools\Definition\Command\CommandFilter;
use DcaTools\User\User;
use DcaTools\Util\Comparison;

/**
 * Class AbstractStateCondition
 * @package DcaTools\Dca\Button\Condition
 */
abstract class AbstractStateCondition extends AbstractCondition
{
	/**
	 * @var CommandCondition
	 */
	private $condition;

	/**
	 * @var string
	 */
	private $property;

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var string
	 */
	private $operator = Comparison::EQUAL;

	/**
	 * @var callable
	 */
	private $callback;


	/**
	 * @param array $config
	 * @param CommandFilter $filter
	 * @param CommandConditionFactory $factory
	 *
	 * @return static
	 */
	public static function fromConfig(array $config, CommandFilter $filter=null, CommandConditionFactory $factory)
	{
		/** @var AbstractStateCondition $condition */
		$condition = parent::fromConfig($config, $filter, $factory);

		if(isset($config['condition'])) {
			if(is_array($config['condition'])) {
				$child = $factory->createFromConfig($config['condition']);
			}
			else {
				$child = $factory->createByName($config['condition']);
			}

			$condition->setCondition($child);
		}

		if(isset($config['property'])) {
			$condition->setProperty($config['property']);
		}

		if(isset($config['operator'])) {
			$condition->setOperator($config['operator']);
		}

		if(isset($config['value'])) {
			$condition->setValue($config['value']);
		}

		if(isset($config['callback'])) {
			$condition->setCallback($config['callback']);
		}

		return $condition;
	}


	/**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 *
	 * @return bool
	 */
	protected function getState(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model=null)
	{
		$state = false;

		if($this->condition) {
			$state = $this->condition->match($button, $environment, $user, $model);
		}
		elseif($this->callback) {
			$state = call_user_func($this->callback, $this, $button, $environment, $user, $model);
		}
		elseif($this->property) {
			$state = Comparison::compare($this->operator, $this->property, $this->value);
		}

		if($this->isInverse()) {
			return !$state;
		}

		return $state;
	}


	/**
	 * @param CommandCondition $condition
	 *
	 * @return $this
	 */
	public function setCondition(CommandCondition $condition)
	{
		$this->condition = $condition;

		return $this;
	}


	/**
	 * @param $property
	 *
	 * @return $this
	 */
	public function setProperty($property)
	{
		$this->property = $property;

		return $this;
	}


	/**
	 * @param $operator
	 * @return $this
	 */
	public function setOperator($operator)
	{
		Assertion::true(Comparison::supportsOperator($operator), sprintf('Comparison operator "%s" is not supported', $operator));

		$this->operator = $operator;

		return $this;
	}


	/**
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * @param callable $callback
	 *
	 * @return $this
	 */
	public function setCallback($callback)
	{
		$this->callback = $callback;

		return $this;
	}

	/**
	 * @return callable
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * @return \DcaTools\Definition\Command\CommandCondition
	 */
	public function getCondition()
	{
		return $this->condition;
	}


	/**
	 * @return string
	 */
	public function getOperator()
	{
		return $this->operator;
	}


	/**
	 * @return string
	 */
	public function getProperty()
	{
		return $this->property;
	}

}