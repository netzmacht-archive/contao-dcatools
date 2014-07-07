<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command\Filter;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Dca\Button;
use DcaTools\User\User;


class AllFilter extends AbstractChildrenFilter
{

	/**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 *
	 * @return bool
	 */
	public function match(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null)
	{
		foreach($this->children as $child) {
			if(!$child->match($button, $environment, $user, $model)) {
				return false;
			}
		}

		return true;
	}

} 