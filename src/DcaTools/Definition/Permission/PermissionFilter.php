<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission;

use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Condition\Permission\FilterFactory;
use DcaTools\Condition\Permission\Context;
use DcaTools\User\User;

/**
 * Interface Filter
 * @package spec\DcaTools\Definition\Command\Filter
 */
interface PermissionFilter
{

    /**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return PermissionFilter
	 */
    public static function fromConfig(array $config, FilterFactory $factory);

    /**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 *
	 * @return bool
	 */
    public function match(EnvironmentInterface $environment, User $user, Context $context);

}
