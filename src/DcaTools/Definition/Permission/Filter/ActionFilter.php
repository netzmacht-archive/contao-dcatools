<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission\Filter;


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Assertion;
use DcaTools\Condition\Permission\Context;
use DcaTools\Condition\Permission\FilterFactory;
use DcaTools\Definition\Permission\PermissionFilter;
use DcaTools\User\User;

class ActionFilter extends AbstractFilter
{
	/**
	 * @var array
	 */
	private $action = array();


	/**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return PermissionFilter|static
	 */
	public static function fromConfig(array $config, FilterFactory $factory)
	{
		Assertion::keyExists($config, 'action', 'Action is not defined');

		/** @var ActionFilter $filter */
		$filter = parent::fromConfig($config, $factory);
		$filter->setAction((array) $config['action']);

		return $filter;
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param Context $context
	 *
	 * @return bool
	 */
	public function match(EnvironmentInterface $environment, User $user, Context $context)
	{
		$action = $this->getActionFromEnvironment($environment);

		return in_array($action, $this->action);
	}


	/**
	 * @param array $action
	 * @return $this
	 */
	public function setAction($action)
	{
		$this->action = $action;

		return $this;
	}


	/**
	 * @return array
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @return mixed
	 */
	protected function getActionFromEnvironment(EnvironmentInterface $environment)
	{
		$input = $environment->getInputProvider();

		if($input->hasParameter('key')) {
			return $input->getParameter('key');
		}

		return $input->getParameter('act');
	}

}
