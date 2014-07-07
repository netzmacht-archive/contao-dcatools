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
use DcaTools\User\User;


/**
 * Class IsAdminCondition
 * @package DcaTools\Condition\Permission
 */
final class IsAdminCondition extends AbstractCondition
{

	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return string
	 */
	public function describe(EnvironmentInterface $environment, User $user, Context $context)
	{
		return sprintf('User "%s" has to be admin', $user->getId());
	}


	/**
	 * Execute the condition
	 *
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
	public function execute(EnvironmentInterface $environment, User $user, Context $context)
	{
		return $user->hasRole(User::ROLE_ADMIN);
	}

} 