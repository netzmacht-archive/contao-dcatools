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


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Definition\Permission\Condition\AbstractCondition;
use DcaTools\Definition\Permission\Context;
use DcaTools\User\User;

class HasAccessCondition extends AbstractCondition
{

	/**
	 * @return array
	 */
	protected function getDefaultConfig()
	{
		return array(
			'domain' => null,
			'role'   => null,
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
		return $user->hasRole($this->config['role'], $this->config['domain']);
	}

} 