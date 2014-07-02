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
use DcaTools\Definition\Permission\Context;
use DcaTools\User\User;


/**
 * Class IsAdminCondition
 * @package DcaTools\Condition\Permission
 */
final class IsAdminCondition extends AbstractCondition
{

	/**
	 * Execute the condition
	 *
	 * @param EnvironmentInterface $environment
	 * @param \DcaTools\User\User $user
	 * @param \DcaTools\Definition\Permission\Context $context
	 * @return bool
	 */
	public function execute(EnvironmentInterface $environment, User $user, Context $context)
	{
		$isAdmin =  $user->hasRole(User::ROLE_ADMIN);

		return $this->applyConfigInverse($isAdmin);
	}


} 