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
use DcaTools\Condition\Permission\PermissionConditionFactory;
use DcaTools\Definition\Permission\PermissionCondition;
use DcaTools\User\User;

class HasRoleCondition extends AbstractCondition
{
    /**
	 * @var
	 */
    private $role;

    /**
	 * @var
	 */
    private $domain;

    /**
	 * @param array $config
	 * @param PermissionConditionFactory $factory
	 *
	 * @return PermissionCondition
	 */
    public static function fromConfig(array $config, PermissionConditionFactory $factory)
    {
        Assertion::keyExists($config, 'role', 'Role is required');
        Assertion::keyExists($config, 'domain', 'Domain is required');

        /** @var HasRoleCondition $condition */
        $condition = parent::fromConfig($config, $factory);
        $condition->setRole($config['role']);
        $condition->setDomain($config['domain']);

        return $condition;
    }

    /**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return string
	 */
    public function describe(EnvironmentInterface $environment, User $user, Context $context)
    {
        return sprintf('Role %s.%s required for user "%s"', $this->domain, $this->role, $user->getId());
    }

    /**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
    public function execute(EnvironmentInterface $environment, User $user, Context $context)
    {
        return $user->hasRole($this->role, $this->domain);
    }

    /**
	 * @param mixed $domain
	 *
	 * @return $this
	 */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
	 * @return mixed
	 */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
	 * @param mixed $action
	 *
	 * @return $this
	 */
    public function setRole($action)
    {
        $this->role = $action;

        return $this;
    }

    /**
	 * @return mixed
	 */
    public function getRole()
    {
        return $this->role;
    }

}
