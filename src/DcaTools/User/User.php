<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\User;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;

interface User
{
	const ROLE_ADMIN = 'admin';


	/**
	 * @param $role
	 * @param $domain
	 * @return bool
	 */
	public function hasRole($role, $domain=null);


	/**
	 * @param $action
	 * @param ModelInterface $model
	 * @return mixed
	 */
	public function isAllowed($action, ModelInterface $model);


	/**
	 * Get Current user id
	 *
	 * @return int
	 */
	public function getId();

} 