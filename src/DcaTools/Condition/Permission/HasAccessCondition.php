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
use DcaTools\Condition\Permission\Context\Context;
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
	 * @param Context $context
	 * @return bool|mixed
	 */
	public function __invoke(EnvironmentInterface $environment, Context $context)
	{
		return $this->condition->__invoke($environment, $context);
	}


	/**
	 * @return string
	 */
	public function getError()
	{
		return 'Permission denied: No access';
	}


} 