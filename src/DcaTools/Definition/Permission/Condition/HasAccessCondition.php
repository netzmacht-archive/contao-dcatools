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
use DcaTools\Definition\Permission\Context;
use DcaTools\User\User;


/**
 * Class HasAccessCondition is a legacy wrapper condition. It's just here using Contao's language for
 * BackendUser::hasAccess
 *
 * @package DcaTools\Condition\Permission
 */
final class HasAccessCondition extends AbstractCondition
{

	/**
	 * @return array
	 */
	protected function getDefaultConfig()
	{
		return array(
			'action' => null,
			'domain' => null,
		);
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param \DcaTools\User\User $user
	 * @param \DcaTools\Definition\Permission\Context $context
	 * @return bool
	 */
	public function execute(EnvironmentInterface $environment, User $user, Context $context)
	{
		$hasAccess = $user->hasRole($this->config['action'], $this->config['domain']);

		return $this->applyConfigInverse($hasAccess);
	}

}