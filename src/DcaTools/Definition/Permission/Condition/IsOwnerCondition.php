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


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Assertion;
use DcaTools\Definition\Permission\Context;
use DcaTools\User\User;


/**
 * Class IsOwnerCondition
 * @package DcaTools\Definition\Permission\Condition
 */
class IsOwnerCondition extends AbstractCondition
{
	const BY_ID       = 'id';
	const BY_USERNAME = 'username';


	/**
	 * @return array
	 */
	protected function getDefaultConfig()
	{
		return array(
			'by' 	   => static::BY_ID,
			'property' => 'userid',
			'context'  => Context::MODEL
		);
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param \DcaTools\Definition\Permission\Context $context
	 * @return bool
	 */
	public function execute(EnvironmentInterface $environment, User $user, Context $context)
	{
		if($this->config['context'] == Context::MODEL) {
			Assertion::true($context->isListView(), 'You are not in list view. Access to the collection is not allowed there.');

			$owner      = false;
			$collection = $context->getCollection();

			foreach($collection as $model) {
				$owner = $this->isOwner($model, $user);
				$owner = $this->applyConfigInverse($owner);

				if(!$owner) {
					break;
				}
			}

			return $owner;
		}

		$model = $this->getContextModel($context);
		$owner = $this->isOwner($model, $user);

		return $this->applyConfigInverse($owner);
	}


	/**
	 * @param ModelInterface $model
	 * @param User $user
	 * @return bool
	 */
	private function isOwner(ModelInterface $model, User $user)
	{
		switch($this->config['by']) {
			case static::BY_ID:
				return $user->getId() == $model->getProperty($this->config['property']);
				break;

			case static::BY_USERNAME:
				return $user->getUsername() == $model->getProperty($this->config['property']);
				break;

			default:
				return false;
		}
	}

} 