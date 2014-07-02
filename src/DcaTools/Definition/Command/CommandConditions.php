<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command;


use Traversable;

class CommandConditions implements \IteratorAggregate
{
	/**
	 * @var array
	 */
	private $conditions = array();


	/**
	 * @param CommandCondition $condition
	 * @return $this
	 */
	public function addCondition(CommandCondition $condition)
	{
		$hash = spl_object_hash($condition);
		$this->conditions[$hash] = $condition;

		return $this;
	}


	/**
	 * @param CommandCondition $condition
	 * @return $this
	 */
	public function removeCondition(CommandCondition $condition)
	{
		$hash = spl_object_hash($condition);
		unset($this->conditions[$hash]);

		return $this;
	}


	/**
	 * @param array $conditions
	 * @return $this
	 */
	public function addConditions(array $conditions = array())
	{
		foreach($conditions as $condition) {
			$this->addCondition($condition);
		}

		return $this;
	}


	/**
	 * @return array
	 */
	public function getConditions()
	{
		return $this->conditions;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable|CommandCondition[] An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->conditions);
	}

} 