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
use DcaTools\Definition\Permission\Context;
use DcaTools\User\User;


/**
 * Interface PermissionCondition
 * @package DcaTools\Definition\Permission
 */
interface PermissionCondition
{
	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
	public function match(EnvironmentInterface $environment, User $user, Context $context);


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
	public function execute(EnvironmentInterface $environment, User $user, Context $context);


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
	public function filter(EnvironmentInterface $environment, User $user, Context $context);


	/**
	 * @return string
	 */
	public function getError();

} 