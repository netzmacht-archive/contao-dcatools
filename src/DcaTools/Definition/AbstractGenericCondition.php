<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;

class AbstractGenericCondition
{
	const METHOD_GET     = 'get';
	const METHOD_POST    = 'post';
	const METHOD_SESSION = 'session';


	/**
	 * @var array
	 */
	protected $config = array();


	/**
	 * @var array
	 */
	protected $filter = array(
		'always'         => false,
		'action'         => null,
		'value'          => null,
		'param'          => null,
		'param_operator' => '=',
		'param_callback' => null,
		'method'         => AbstractGenericCondition::METHOD_GET,
		'inverse'        => false,
	);


	/**
	 * @param array $config
	 * @param array $filter
	 */
	function __construct(array $config=array(), array $filter=array())
	{
		$this->config = array_merge($this->config, $this->getDefaultConfig(), $config);
		$this->filter = array_merge($this->filter, $this->getDefaultFilter(), $filter);
	}


	/**
	 * @return array
	 */
	protected function getDefaultFilter()
	{
		return array();
	}


	/**
	 * @return array
	 */
	protected function getDefaultConfig()
	{
		return array();
	}


	/**
	 * @param InputProviderInterface $input
	 * @param $method
	 * @param $name
	 * @return mixed
	 */
	protected function getInputValue(InputProviderInterface $input, $method, $name)
	{
		switch($method) {
			case static::METHOD_GET:
				return $input->getParameter($name);
				break;

			case static::METHOD_POST:
				return $input->getValue($name);
				break;

			case static::METHOD_SESSION:
				return $input->getPersistentValue($name);
				break;
		}

		return null;
	}


	/**
	 * @param $state
	 * @return bool
	 */
	protected function applyConfigInverse($state)
	{
		if($this->config['inverse']) {
			return !$state;
		}

		return $state;
	}


	/**
	 * @param $state
	 * @return bool
	 */
	protected function applyFilterInverse($state)
	{
		if($this->filter['inverse']) {
			return !$state;
		}

		return $state;
	}


	/**
	 * @param ModelInterface $model
	 * @return bool|mixed
	 */
	protected function matchPropertyFilter(ModelInterface $model)
	{
		if(!$this->filter['property']) {
			return true;
		}

		if($this->filter['callback']) {
			return call_user_func($this->filter['callback'], $model, $this->filter['property']);
		}

		$value = $model->getProperty($this->filter['property']);
		$state = $this->compare($value, $this->filter['operator'], $this->filter['value']);

		return $state;
	}


	/**
	 * @param $property
	 * @param $operator
	 * @param $value
	 * @return bool
	 */
	protected function compare($property, $operator, $value)
	{
		switch($operator) {
			case '=':
				return ($property == $value);
				break;

			case '!=':
				return ($property != $value);
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
		}

		return false;
	}

} 