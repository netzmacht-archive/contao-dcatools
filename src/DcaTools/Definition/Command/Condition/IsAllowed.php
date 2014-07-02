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


use Assert\Assertion;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use DcaTools\Dca\Button;
use DcaTools\User\User;

class IsAllowed extends AbstractCondition
{
	/**
	 * @return array
	 */
	protected function getDefaultConfig()
	{
		return array(
			'action' => null,
		);
	}


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function execute(Button $button, InputProviderInterface $input, User $user, ModelInterface $model = null)
	{
		Assertion::notNull($model, 'isAllowed rule can only be applied to model commands');

		return $user->isAllowed($this->config['action'], $model);
	}

} 