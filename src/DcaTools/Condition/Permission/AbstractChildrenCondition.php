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


use Assert\Assertion;
use DcaTools\User\User;

abstract class AbstractChildrenCondition implements PermissionCondition
{
	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var PermissionCondition[]
	 */
	protected $conditions;


	/**
	 * @param $user
	 * @param array $config
	 */
	function __construct($user, array $config=array())
	{
		Assertion::keyExists($config, 'conditions');
		Assertion::isArray($config['conditions']);
		Assertion::allIsInstanceOf($config['conditions'], 'DcaTools\Condition\Permission\PermissionCondition');

		$this->user       = $user;
		$this->conditions = $config['conditions'];
	}
} 