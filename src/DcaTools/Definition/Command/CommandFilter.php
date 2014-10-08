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
use DcaTools\Condition\Command\FilterFactory;
use DcaTools\Dca\Button;
use DcaTools\User\User;

/**
 * Interface Filter
 * @package spec\DcaTools\Definition\Command\Filter
 */
interface CommandFilter
{

    /**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return CommandFilter
	 */
    public static function fromConfig(array $config, FilterFactory $factory);

    /**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 *
	 * @return bool
	 */
    public function match(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null);

}
