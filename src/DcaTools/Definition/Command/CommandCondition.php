<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Condition\Command\CommandConditionFactory;
use DcaTools\Dca\Button;
use DcaTools\User\User;

/**
 * Class Condition
 *
 * @package DcaTools\Dca\Command
 */
interface CommandCondition
{
    const DISABLE = 'disable';
    const HIDE    = 'hide';

    /**
	 * @param array $config
	 * @param CommandFilter $filter
	 * @param CommandConditionFactory $factory
	 *
	 * @return static
	 */
    public static function fromConfig(array $config, CommandFilter $filter=null, CommandConditionFactory $factory);

    /**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
    public function match(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null);

}
