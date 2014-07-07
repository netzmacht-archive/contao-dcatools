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


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Assertion;
use DcaTools\Condition\Permission\Context;
use DcaTools\Condition\Permission\FilterFactory;
use DcaTools\Definition\Permission\Condition\Filter\PermissionFilter;
use DcaTools\User\User;

class ContextFilter extends AbstractFilter
{
	/**
	 * @var string
	 */
	private $context;

	/**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return PermissionFilter|static
	 */
	public static function fromConfig(array $config, FilterFactory $factory)
	{
		Assertion::keyExists($config, 'context', 'Context has to be defined');

		/** @var ContextFilter $filter */
		$filter = parent::fromConfig($config, $factory);
		$filter->setContext($config['context']);

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
		return $context->match($this->context);
	}


	/**
	 * @param string $context
	 *
	 * @return $this
	 */
	public function setContext($context)
	{
		$this->context = $context;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getContext()
	{
		return $this->context;
	}

} 