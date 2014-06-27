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


class Permissions
{
	/**
	 * Test if User is an admin.
	 *
	 * @return bool
	 */
	public function isAdmin()
	{
		return \BackendUser::getInstance()->isAdmin;
	}

} 