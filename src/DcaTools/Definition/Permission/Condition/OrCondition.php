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
use DcaTools\Assertion;
use DcaTools\Condition\Permission\Context;
use DcaTools\Definition\Permission\PermissionCondition;
use DcaTools\User\User;

/**
 * Class OrCondition
 * @package DcaTools\Definition\Permission\Condition
 */
class OrCondition extends AbstractCondition
{
    /**
	 * @var PermissionCondition[]
	 */
    private $conditions = array();

    /**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
    protected function execute(EnvironmentInterface $environment, User $user, Context $context)
    {
        if (empty($this->conditions)) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if ($condition->match($environment, $user, $context)) {
                return true;
            }
        }

        return false;
    }

    /**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return string
	 */
    public function describe(EnvironmentInterface $environment, User $user, Context $context)
    {
        $descriptions = array();

        foreach ($this->conditions as $condition) {
            $descriptions[] = $condition->describe($environment, $user, $context);
        }

        return 'At least one of these conditions match: '. implode(' OR ', $descriptions);
    }

    /**
	 * @param PermissionCondition[] $conditions
	 * @return $this
	 */
    public function setConditions($conditions)
    {
        Assertion::allIsInstanceOf($conditions, 'DcaTools\Definition\Permission\PermissionCondition');

        $this->conditions = $conditions;

        return $this;
    }

    /**
	 * @return PermissionCondition[]
	 */
    public function getConditions()
    {
        return $this->conditions;
    }

}
