<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;

abstract class AbstractStateCondition extends AbstractCondition
{
	const PARAM_GET     = 'get';
	const PARAM_POST    = 'post';
	const PARAM_SESSION = 'session';


	/**
	 * @var array
	 */
	protected $config = array(
		'action'   => null,
		'property' => null,
		'param'    => AbstractStateCondition::PARAM_GET,
		'name'	   => null,
		'value'	   => null,
		'operator' => '=',
		'callback' => null,
		'inverse'  => false,
	);


	/**
	 * @param EnvironmentInterface $environment
	 * @param ModelInterface $model
	 * @return bool|mixed
	 */
	public function __invoke(EnvironmentInterface $environment, ModelInterface $model)
	{
		$state = true;

		if($this->config['action']) {
			if($this->getAction($environment) == $this->config['action']) {
				$state = $this->getState($environment, $model);
			}
		}
		elseif($this->config['property']) {
			$state = $this->getStateByProperty($model);
		}
		elseif($this->config['param']) {
			$state = $this->getStateByParam($environment);
		}

		if($this->config['inverse']) {
			$state = !$state;
		}

		return $state;
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param ModelInterface $model
	 * @return mixed
	 */
	abstract protected function getState(EnvironmentInterface $environment, ModelInterface $model);


	/**
	 * @param EnvironmentInterface $environment
	 * @return mixed
	 */
	private function getAction(EnvironmentInterface $environment)
	{
		$input = $environment->getInputProvider();

		if($input->hasParameter('key')) {
			return $input->getParameter('key');
		}

		return $input->getParameter('act');
	}


	/**
	 * @param $property
	 * @param $operator
	 * @param $value
	 * @return bool
	 */
	private function compare($property, $operator, $value)
	{
		switch($operator) {
			case '=':
				return ($property === $value);
				break;

			case '!=':
				return ($property === $value);
				break;

			case '<':
				return ($property < $value);
				break;

			case '>':
				return ($property > $value);
				break;

			case '>=':
				return ($property >= $value);
				break;

			case '<=':
				return ($property <= $value);
				break;

			case '==':
				return ($property == $value);
				break;
		}

		return false;
	}


	/**
	 * @param InputProviderInterface $input
	 * @param $param
	 * @param $name
	 * @return mixed
	 */
	private function getInputValue(InputProviderInterface $input, $param, $name)
	{
		switch($param) {
			case static::PARAM_GET:
				return $input->getParameter($name);
				break;

			case static::PARAM_POST:
				return $input->getValue($name);
				break;

			case static::PARAM_SESSION:
				return $input->getPersistentValue($name);
				break;
		}

		return null;
	}


	/**
	 * @param ModelInterface $model
	 * @return bool|mixed
	 */
	private function getStateByProperty(ModelInterface $model)
	{
		if($this->config['callback']) {
			return call_user_func($this->config['callback'], $model, $this->config['property']);
		}

		$value = $model->getProperty($this->config['property']);
		$state = $this->compare($value, $this->config['operator'], $this->config['value']);

		return $state;
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @return bool|mixed
	 */
	private function getStateByParam(EnvironmentInterface $environment)
	{
		$input = $environment->getInputProvider();

		if($this->config['callback']) {
			return call_user_func($this->config['callback'], $input, $this->config['param'], $this->config['name']);
		}

		$value = $this->getInputValue($input, $this->config['param'], $this->config['name']);
		$state = $this->compare($value, $this->config['operator'], $this->config['value']);

		return $state;
	}

} 