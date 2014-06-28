<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Contao;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use DcaTools\Assertion;
use DcaTools\User\User;

class BackendUserDecorator implements User
{
	/**
	 * @var \BackendUser
	 */
	private $user;


	/**
	 * @param \BackendUser $user
	 */
	function __construct(\BackendUser $user)
	{
		$this->user = $user;
	}


	/**
	 * @param $role
	 * @param $domain
	 * @return bool
	 */
	public function hasRole($role, $domain=null)
	{
		if($domain === null && $role == static::ROLE_ADMIN) {
			return $this->user->isAdmin;
		}

		return $this->user->hasAccess($role, $domain);
	}


	/**
	 * @param $action
	 * @param ModelInterface $model
	 * @return mixed
	 */
	public function isAllowed($action, ModelInterface $model)
	{
		Assertion::eq('tl_page', $model->getProviderName(), 'Contao only supports User::isAllowed for tl_page');

		return $this->user->isAllowed($action, $model->getPropertiesAsArray());
	}

} 