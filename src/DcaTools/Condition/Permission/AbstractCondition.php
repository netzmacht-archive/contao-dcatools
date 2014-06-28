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


use DcaTools\User\User;

abstract class AbstractCondition implements PermissionCondition
{
	/**
	 * @var array
	 */
	protected $config = array();


	/**
	 * @var User
	 */
	protected $user;


	/**
	 * @param User $user
	 * @param array $config
	 */
	function __construct(User $user, array $config=array())
	{
		$this->config = array_merge($this->config, $config);
		$this->user   = $user;
	}


	/**
	 * @return string
	 */
	public function getError()
	{
		if(!isset($this->config['error'])) {
			$class = get_called_class();
			$class = substr($class, strrpos($class, '\\')+1);

			return 'Permission denied: ' . $class  . ' failed';
		}

		return $this->config['error'];
	}


} 