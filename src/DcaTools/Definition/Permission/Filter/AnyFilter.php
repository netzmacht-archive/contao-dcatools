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

/**
 * Class AnyFilter
 * @package DcaTools\Definition\Command\Condition\Filter
 */
class AnyFilter extends AbstractChildrenFilter
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
        if (empty($this->children)) {
            return true;
        }

        foreach ($this->children as $child) {
            if ($child->match($environment, $user, $context)) {
                return true;
            }
        }

        return false;
    }

}
