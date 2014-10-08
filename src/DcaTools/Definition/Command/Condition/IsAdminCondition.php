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
use DcaTools\Dca\Button;
use DcaTools\User\User;

class IsAdminCondition extends AbstractStateCondition
{

    /**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
    public function execute(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

}
