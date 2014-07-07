<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission\Filter;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Assertion;
use DcaTools\Condition\Permission\Context;
use DcaTools\Condition\Permission\FilterFactory;
use DcaTools\Dca\Button;
use DcaTools\Definition\Permission\Condition\Filter\PermissionFilter;
use DcaTools\User\User;
use DcaTools\Util\Comparison;
use DcaTools\Util\Input;

class ParamFilter extends AbstractFilter
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $method = Input::METHOD_GET;

	/**
	 * @var string
	 */
	private $operator = Comparison::EQUAL;

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var callable
	 */
	private $callback;


	/**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return PermissionFilter|static
	 */
	public static function fromConfig(array $config, FilterFactory $factory)
	{
		Assertion::keyExists($config, 'name', 'Param name has to be set');

		/** @var ParamFilter $filter */
		$filter = parent::fromConfig($config, $factory);
		$filter->setName($config['name']);

		if(isset($config['method'])) {
			$filter->setMethod($config['method']);
		}

		if(isset($config['value'])) {
			$filter->setValue($config['value']);
		}

		if(isset($config['operator'])) {
			$filter->setValue($config['operator']);
		}

		if(isset($config['callback'])) {
			$filter->setValue($config['callback']);
		}

		return $filter;
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 *
	 * @return bool
	 */
	public function match(EnvironmentInterface $environment, User $user, Context $context)
	{
		if($this->callback) {
			return call_user_func($this->callback, $this, $environment, $user, $context);
		}

		$param = Input::getValue($environment->getInputProvider(), $this->method, $this->name);

		return Comparison::compare($this->operator, $this->value, $param);
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
	 * @param string $method
	 *
	 * @return $this
	 */
	public function setMethod($method)
	{
		$this->method = $method;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}


	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @param string $operator
	 *
	 * @return $this
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getOperator()
	{
		return $this->operator;
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

} 