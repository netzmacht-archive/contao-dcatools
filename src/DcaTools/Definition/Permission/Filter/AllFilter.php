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
use DcaTools\Condition\Permission\Context;
use DcaTools\User\User;

class AllFilter extends AbstractChildrenFilter
{
    /**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 *
	 * @return bool
	 */
    public function match(EnvironmentInterface $environment, User $user, Context $context)
    {
        foreach ($this->children as $child) {
            if (!$child->match($environment, $user, $context)) {
                return false;
            }
        }

        return true;
    }

}
