<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;

interface PermissionCondition
{
	/**
	 * @param EnvironmentInterface $environment
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function __invoke(EnvironmentInterface $environment, ModelInterface $model);


	/**
	 * @return string
	 */
	public function getError();
} 