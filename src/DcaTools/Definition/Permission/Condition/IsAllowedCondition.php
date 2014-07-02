<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission\Condition;


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Assertion;
use DcaTools\Definition\Permission\Context;
use DcaTools\User\User;


final class IsAllowedCondition extends AbstractCondition
{
	/**
	 * @return array
	 */
	protected function getDefaultConfig()
	{
		return array(
			'context' => null,
			'action'  => null,
		);
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param \DcaTools\User\User $user
	 * @param \DcaTools\Definition\Permission\Context $context
	 * @return bool
	 */
	public function execute(EnvironmentInterface $environment, User $user, Context $context)
	{
		if($this->config['context'] == Context::COLLECTION) {
			Assertion::true($context->isListView(), 'You are not in list view. Access to the collection is not allowed there.');

			$allowed    = true;
			$collection = $context->getCollection();

			foreach($collection as $model) {
				$allowed = $user->isAllowed($this->config['action'], $model);
				$allowed = $this->applyConfigInverse($allowed);

				if(!$allowed) {
					break;
				}
			}

			return $allowed;
		}

		$model   = $this->getContextModel($context);
		$allowed = $user->isAllowed($this->config['action'], $model);

		return $this->applyConfigInverse($allowed);
	}

} 