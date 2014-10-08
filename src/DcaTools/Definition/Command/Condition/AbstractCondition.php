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
use DcaTools\Condition\Command\CommandConditionFactory;
use DcaTools\Dca\Button;
use DcaTools\Definition\Command\CommandCondition;
use DcaTools\Definition\Command\Condition;
use DcaTools\Definition\Command\CommandFilter;
use DcaTools\User\User;

abstract class AbstractCondition implements CommandCondition
{
    /**
	 * @var \DcaTools\Definition\Command\CommandFilter
	 */
    private $filter;

    /**
	 * @var bool
	 */
    private $inverse = false;

    /**
	 * @param CommandFilter $filter
	 */
    public function __construct(CommandFilter $filter=null)
    {
        $this->filter = $filter;
    }

    /**
	 * @param array $config
	 * @param CommandFilter $filter
	 * @param CommandConditionFactory $factory
	 *
	 * @return static
	 */
    public static function fromConfig(array $config, CommandFilter $filter = null, CommandConditionFactory $factory)
    {
        /** @var AbstractCondition $condition */
        $condition = new static($filter);

        if (isset($config['inverse'])) {
            $condition->setInverse($config['inverse']);
        }

        return $condition;
    }

    /**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param \DcaTools\User\User $user
	 * @param ModelInterface $model
	 *
	 * @return bool
	 */
    public function match(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null)
    {
        $match = true;

        if ($this->filter($button, $environment, $user, $model)) {
            $match = $this->execute($button, $environment, $user, $model);

            if ($this->inverse) {
                return !$match;
            }
        }

        return $match;
    }

    /**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 *
	 * @return bool
	 */
    abstract protected function execute(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null);

    /**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
    protected function filter(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null)
    {
        if (!$this->filter) {
            return true;
        }

        return $this->filter->match($button, $environment, $user, $model);
    }

    /**
	 * @param boolean $inverse
	 *
	 * @return $this
	 */
    public function setInverse($inverse)
    {
        $this->inverse = (bool) $inverse;

        return $this;
    }

    /**
	 * @return boolean
	 */
    public function isInverse()
    {
        return $this->inverse;
    }

}
