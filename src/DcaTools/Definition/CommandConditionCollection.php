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


use Assert\Assertion;
use DcaTools\Condition\Command\CommandCondition;


/**
 * Class ConditionCollection
 * @package DcaTools\Definition\Command
 */
class CommandConditionCollection
{
	const FILTER_ANY = '*';

	/**
	 * @var array
	 */
	private $conditions = array();

	/**
	 * @var array
	 */
	private $filters = array();


	/**
	 * @param CommandCondition $condition
	 * @param array $filter
	 * @return $this
	 */
	public function addCondition(CommandCondition $condition, $filter=array())
	{
		$this->assertValidFilter($filter);

		$this->conditions[] = $condition;
		$this->filters[]	= $filter;

		return $this;
	}


	/**
	 * @param CommandCondition $condition
	 * @return $this
	 */
	public function removeCondition(CommandCondition $condition)
	{
		$index = array_search($condition, $this->conditions);

		if($index !== false) {
			unset($this->conditions[$index]);
			unset($this->filters[$index]);
		}

		return $this;
	}


	/**
	 * @param CommandCondition $condition
	 * @return mixed
	 */
	public function getConditionFilter(CommandCondition $condition)
	{
		$index = array_search($condition, $this->conditions);
		Assertion::integer($index, 'Condition is not in collection');

		return $this->filters[$index];
	}


	/**
	 * @param CommandCondition $condition
	 * @param array $filter
	 */
	public function changeConditionFilter(CommandCondition $condition, $filter=array())
	{
		$index = array_search($condition, $this->conditions);
		Assertion::integer($index, 'Condition is not in collection');

		$this->filters[$index] = $filter;
	}


	/**
	 * @param array $conditions
	 * @param array $filter
	 * @return $this
	 */
	public function addConditions(array $conditions, $filter=array())
	{
		Assertion::allIsInstanceOf($conditions, 'DcaTools\Dca\Button\Condition');

		foreach($conditions as $condition) {
			$this->addCondition($condition, $filter);
		}

		return $this;
	}


	/**
	 * @param string $commandName
	 * @return array
	 */
	public function getConditions($commandName=null)
	{
		if($commandName === null) {
			return $this->conditions;
		}

		$filtered = array();

		foreach($this->conditions as $index => $condition) {
			$filter = $this->filters[$index];

			if($filter == static::FILTER_ANY) {
				$filtered[] = $condition;
			}
			elseif(in_array($commandName, (array) $filter)) {
				$filtered[] = $condition;
			}
		}

		return $filtered;
	}


	/**
	 * @param $filter
	 */
	private function assertValidFilter($filter)
	{
		if($filter == static::FILTER_ANY || is_string($filter)) {
			return;
		}

		Assertion::allString($filter);
	}

}