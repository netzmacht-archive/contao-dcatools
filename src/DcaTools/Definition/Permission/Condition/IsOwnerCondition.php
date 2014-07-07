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
use DcaTools\Condition\Permission\Context;
use DcaTools\Condition\Permission\PermissionConditionFactory;
use DcaTools\Definition\Permission\PermissionCondition;
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
	 * @var string
	 */
	private $by = IsOwnerCondition::BY_ID;

	/**
	 * @var string
	 */
	private $property = 'userid';

	/**
	 * @var string
	 */
	private $context = Context::MODEL;


	/**
	 * @param array $config
	 * @param PermissionConditionFactory $factory
	 * @return PermissionCondition
	 */
	public static function fromConfig(array $config, PermissionConditionFactory $factory)
	{
		/** @var IsOwnerCondition $condition */
		$condition = parent::fromConfig($config, $factory);

		if(isset($config['by'])) {
			Assertion::inArray($config['by'], array(static::BY_ID, static::BY_USERNAME), 'By has to be by id or by username');

			$condition->setBy($config['by']);
		}

		if(isset($config['property'])) {
			$condition->setProperty($config['property']);
		}

		if(isset($config['context'])) {
			Assertion::inArray(
				$config['context'],
				array(Context::MODEL, Context::PARENT, Context::COLLECTION),
				'By has to be by id or by username'
			);

			$condition->setContext($config['context']);
		}

		return $condition;
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param \DcaTools\Condition\Permission\Context $context
	 * @return bool
	 */
	public function execute(EnvironmentInterface $environment, User $user, Context $context)
	{
		if($this->context == Context::COLLECTION) {
			Assertion::true($context->isListView(), 'You are not in list view. Access to the collection is not allowed there.');

			$owner      = false;
			$collection = $context->getCollection();

			foreach($collection as $model) {
				$owner = $this->isOwner($model, $user);

				if(!$owner || $this->isInverse()) {
					break;
				}
			}

			return $owner;
		}

		if($this->context == Context::PARENT) {
			$model = $context->getParent();
		}
		else {
			$model = $context->getModel();
		}

		return $this->isOwner($model, $user);
	}


	/**
	 * @param ModelInterface $model
	 * @param User $user
	 * @return bool	 */
	private function isOwner(ModelInterface $model, User $user)
	{
		$value = $model->getProperty($this->property);

		if($this->by == static::BY_USERNAME) {
			return $user->getUsername() == $value;
		}

		return $user->getId() == $value;
	}


	/**
	 * @param string $by
	 *
	 * @return $this
	 */
	public function setBy($by)
	{
		$this->by = $by;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getBy()
	{
		return $this->by;
	}


	/**
	 * @param string $context
	 *
	 * @return $this
	 */
	public function setContext($context)
	{
		$this->context = $context;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getContext()
	{
		return $this->context;
	}


	/**
	 * @param string $property
	 *
	 * @return $this
	 */
	public function setProperty($property)
	{
		$this->property = $property;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getProperty()
	{
		return $this->property;
	}

} 