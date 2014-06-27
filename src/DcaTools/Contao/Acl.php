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


class Acl implements \DcaTools\Acl
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
	 * @return bool
	 */
	public function isAdmin()
	{
		return $this->user->isAdmin;
	}


} 