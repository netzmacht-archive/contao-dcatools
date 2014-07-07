<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission;


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Condition\Permission\Context;
use DcaTools\Condition\Permission\PermissionConditionFactory;
use DcaTools\User\User;


/**
 * Interface PermissionCondition
 * @package DcaTools\Definition\Permission
 */
interface PermissionCondition
{
	/**
	 * @param array $config
	 * @param PermissionFilter $filter
	 * @param PermissionConditionFactory $factory
	 * @return PermissionCondition
	 */
	public static function fromConfig(array $config, PermissionFilter $filter=null, PermissionConditionFactory $factory);


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
	public function match(EnvironmentInterface $environment, User $user, Context $context);


	/**
	 * @return string
	 */
	public function getError();


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return string
	 */
	public function describe(EnvironmentInterface $environment, User $user, Context $context);

} 