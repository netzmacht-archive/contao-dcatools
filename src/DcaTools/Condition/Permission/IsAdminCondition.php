<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\User\User;


/**
 * Class IsAdminCondition
 * @package DcaTools\Condition\Permission
 */
final class IsAdminCondition extends AbstractStateCondition
{

	/**
	 * @param EnvironmentInterface $environment
	 * @param ModelInterface $model
	 * @return mixed
	 */
	protected function getState(EnvironmentInterface $environment, ModelInterface $model)
	{
		return $this->user->hasRole(User::ROLE_ADMIN);
	}

} 