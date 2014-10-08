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

final class IsAllowedCondition extends AbstractCondition
{

    /**
	 * @var string
	 */
    private $action;

    /**
	 * @var string
	 */
    private $context = Context::MODEL;

    /**
	 * @param array $config
	 * @param PermissionConditionFactory $factory
	 *
	 * @return PermissionCondition
	 */
    public static function fromConfig(array $config, PermissionConditionFactory $factory)
    {
        Assertion::keyExists($config, 'action', 'Action is required');

        /** @var IsAllowedCondition $condition */
        $condition = parent::fromConfig($config, $factory);
        $condition->setAction($config['action']);

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
        return sprintf(
            'Action %s is allowed for user %s in context %s',
            $this->action,
            $user->getId(),
            $this->context
        );
    }

    /**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 * @return bool
	 */
    public function execute(EnvironmentInterface $environment, User $user, Context $context)
    {
        if ($this->context == Context::PARENT) {
            $model = $context->getParent();

            return $user->isAllowed($this->action, $model);
        }

        if ($context->isListView()) {
            Assertion::eq($this->context, Context::COLLECTION, 'Context has to be set to collection for list view');

            $collection = $context->getCollection();

            if (!count($collection)) {
                return true;
            }

            foreach ($collection as $model) {
                if (!$user->isAllowed($this->action, $model)) {
                    return false;
                }
            }

            return true;
        }

        $model = $context->getModel();

        return $user->isAllowed($this->action, $model);
    }

    /**
	 * @param mixed $action
	 *
	 * @return $this
	 */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
	 * @return mixed
	 */
    public function getAction()
    {
        return $this->action;
    }

    /**
	 * @param mixed $context
	 */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
	 * @return mixed
	 */
    public function getContext()
    {
        return $this->context;
    }

}
