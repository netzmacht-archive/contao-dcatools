<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission\Condition;


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Condition\Permission\Context;
use DcaTools\Condition\Permission\PermissionConditionFactory;
use DcaTools\Definition\Permission\Condition\Filter\PermissionFilter;
use DcaTools\Definition\Permission\PermissionCondition;
use DcaTools\User\User;


/**
 * Class AbstractCondition
 * @package DcaTools\Definition\Permission\Condition
 */
abstract class AbstractCondition implements PermissionCondition
{

	/**
	 * @var PermissionFilter
	 */
	private $filter;

	/**
	 * @var bool
	 */
	private $inverse = false;

	/**
	 * @var string
	 */
	protected $error;


	/**
	 * @param PermissionFilter $filter
	 */
	function __construct(PermissionFilter $filter = null)
	{
		$this->filter = $filter;
	}


	/**
	 * @param array $config
	 * @param PermissionConditionFactory $factory
	 * @return PermissionCondition
	 */
	public static function fromConfig(array $config, PermissionConditionFactory $factory)
	{
		/** @var AbstractCondition $condition */
		$condition = new static();

		if(isset($config['inverse'])) {
			$condition->setInverse($config['inverse']);
		}

		return $condition;
	}


	/**
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
	public function match(EnvironmentInterface $environment, User $user, Context $context)
	{
		$match = true;

		$this->error = 'Permission denied: ' . $this->describe($environment, $user, $context)  . ' failed';

		if($this->filter($environment, $user, $context)) {
			$match = $this->execute($environment, $user, $context);

			if($this->inverse) {
				return !$match;
			}
		}

		return $match;
	}


	/**
	 * @param boolean $inverse
	 */
	public function setInverse($inverse)
	{
		$this->inverse = (bool) $inverse;
	}


	/**
	 * @return boolean
	 */
	public function isInverse()
	{
		return $this->inverse;
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
	abstract protected function execute(EnvironmentInterface $environment, User $user, Context $context);


	/**
	 * Match the filter
	 *
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return mixed
	 */
	protected  function filter(EnvironmentInterface $environment, User $user, Context $context)
	{
		if($this->filter) {
			return $this->filter->match($environment, $user, $context);
		}

		return false;
	}

} 