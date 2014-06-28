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


/**
 * Class HasAccessCondition is a legacy wrapper condition. It's just here using Contao's language for
 * BackendUser::hasAccess
 *
 * @package DcaTools\Condition\Permission
 */
final class HasAccessCondition implements PermissionCondition
{
	/**
	 * @var RoleCondition
	 */
	private $condition;


	/**
	 * @param User $user
	 * @param array $config
	 */
	function __construct(User $user, array $config=array())
	{
		$config['role']   = $config['field'];
		$config['domain'] = $config['array'];

		$this->condition = new RoleCondition($user, $config);
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param ModelInterface $model
	 * @return bool|mixed
	 */
	public function __invoke(EnvironmentInterface $environment, ModelInterface $model)
	{
		return $this->condition->__invoke($environment, $model);
	}

} 