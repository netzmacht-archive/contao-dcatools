<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command\Condition;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use DcaTools\Dca\Button;
use DcaTools\Definition\Command\Condition;
use DcaTools\User\User;


class DisableCondition extends AbstractStateCondition
{

	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param \DcaTools\User\User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function execute(Button $button, InputProviderInterface $input, User $user, ModelInterface $model=null)
	{
		$disabled = $this->getState($button, $input, $user, $model);
		$button->setDisabled($disabled);

		return !$disabled;
	}

} 