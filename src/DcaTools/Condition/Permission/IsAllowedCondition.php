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
use DcaTools\User\User;

final class IsAllowedCondition extends AbstractStateCondition
{

	/**
	 * @param User $user
	 * @param array $config
	 */
	function __construct(User $user, array $config = array())
	{
		$this->config['action'] = null;

		parent::__construct($user, $config);
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param ModelInterface $model
	 * @return bool
	 */
	protected function getState(EnvironmentInterface $environment, ModelInterface $model)
	{
		return $this->user->isAllowed($this->config['action'], $model);
	}

} 